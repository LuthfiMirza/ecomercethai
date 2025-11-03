<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    private function shippingCost(): float
    {
        return 50.0; // Flat rate in THB for now
    }

    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', __('checkout.empty_cart'));
        }

        $shippingAddresses = ShippingAddress::where('user_id', Auth::id())->get();
        $subtotal = $cartItems->sum('subtotal');
        $shippingCost = $this->shippingCost();

        return view('pages.checkout', [
            'cartItems' => $cartItems,
            'shippingAddresses' => $shippingAddresses,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
            'payment_method' => 'required|in:bank_transfer',
            'coupon_code' => 'nullable|string',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Verify shipping address belongs to user
        $shippingAddress = ShippingAddress::where('id', $request->shipping_address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Get cart items
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', __('checkout.empty_cart'));
        }

        // Calculate totals
        $subtotal = $cartItems->sum('subtotal');
        $discountAmount = 0;
        $couponCode = null;

        // Apply coupon if provided
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('is_active', true)
                ->where('valid_from', '<=', now())
                ->where('valid_until', '>=', now())
                ->first();

            if ($coupon && $subtotal >= $coupon->min_purchase) {
                if ($coupon->discount_type === 'percentage') {
                    $discountAmount = ($subtotal * $coupon->discount_value) / 100;
                    if ($coupon->max_discount) {
                        $discountAmount = min($discountAmount, $coupon->max_discount);
                    }
                } else {
                    $discountAmount = $coupon->discount_value;
                }
                $couponCode = $coupon->code;
            }
        }

        // Calculate shipping (simple flat rate for now)
        $shippingCost = $this->shippingCost();
        $totalAmount = $subtotal - $discountAmount + $shippingCost;

        $paymentProofPath = null;

        try {
            DB::beginTransaction();

            if ($request->payment_method === 'bank_transfer' && $request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => $paymentProofPath ? 'verifying' : 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => json_encode([
                    'name' => $shippingAddress->name,
                    'phone' => $shippingAddress->phone,
                    'address_line1' => $shippingAddress->address_line1,
                    'address_line2' => $shippingAddress->address_line2,
                    'city' => $shippingAddress->city,
                    'state' => $shippingAddress->state,
                    'postal_code' => $shippingAddress->postal_code,
                    'country' => $shippingAddress->country,
                ]),
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,
                'payment_proof_path' => $paymentProofPath,
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                ]);
            }

            // Clear cart
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            // Redirect based on payment method
            if ($paymentProofPath) {
                return redirect()->route('orders.show', $order->id)
                    ->with('success', __('payment.proof_uploaded'));
            }

            return redirect()->route('payment.bank-transfer', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($paymentProofPath) {
                Storage::disk('public')->delete($paymentProofPath);
            }
            return back()->with('error', __('checkout.error') . ': ' . $e->getMessage());
        }
    }

    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:120'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($data, $userId) {
            $data['user_id'] = $userId;
            $data['is_default'] = isset($data['is_default']) ? (bool) $data['is_default'] : false;

            $address = ShippingAddress::create($data);

            if ($address->is_default) {
                ShippingAddress::where('user_id', $userId)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });

        return redirect()
            ->route('checkout')
            ->with('success', __('checkout.address_added'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => __('checkout.invalid_coupon'),
            ]);
        }

        $cartItems = Cart::where('user_id', Auth::id())->get();
        $subtotal = $cartItems->sum('subtotal');

        if ($subtotal < $coupon->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => __('checkout.min_purchase_not_met', ['amount' => $coupon->min_purchase]),
            ]);
        }

        $discountAmount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discountAmount = ($subtotal * $coupon->discount_value) / 100;
            if ($coupon->max_discount) {
                $discountAmount = min($discountAmount, $coupon->max_discount);
            }
        } else {
            $discountAmount = $coupon->discount_value;
        }

        return response()->json([
            'success' => true,
            'discount_amount' => $discountAmount,
            'message' => __('checkout.coupon_applied'),
        ]);
    }
}
