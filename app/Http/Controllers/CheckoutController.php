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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
            'shippingCost' => $shippingCost
        ]);
    }

    public function process(Request $request)
    {
        \Log::info('Checkout process called', [
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
            'request_headers' => $request->headers->all(),
        ]);

        $validated = $request->validate([
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
            'payment_method' => 'required|in:bank_transfer',
            'coupon_code' => 'nullable|string',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        \Log::info('Checkout validation passed', ['validated' => $validated]);

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

            // Create order (conditionally include optional columns)
            $orderData = [
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
                    'country' => $shippingAddress->country
                ]),
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,
                'payment_proof_path' => $paymentProofPath
            ];

            if (Schema::hasColumn('orders', 'track_token')) {
                $orderData['track_token'] = Str::random(40);
            }

            $order = Order::create($orderData);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price
                ]);
            }

            // Clear cart
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            // Redirect: if payment proof submitted go straight to order detail
            // Otherwise show the checkout success page with next steps
            $redirectUrl = $paymentProofPath
                ? route('orders.show', [
                    'locale' => app()->getLocale(),
                    'order' => $order->id
                ])
                : route('checkout.success', [
                    'locale' => app()->getLocale(),
                    'order' => $order->id
                ]);
            $successMessage = $paymentProofPath
                ? __('payment.proof_uploaded')
                : __('Pesanan berhasil dibuat.');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'redirect' => $redirectUrl,
                    'order_number' => sprintf('#%s', $order->id),
                    'message' => $successMessage
                ]);
            }

            return redirect()->to($redirectUrl)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($paymentProofPath) {
                Storage::disk('public')->delete($paymentProofPath);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('checkout.error') . ': ' . $e->getMessage()
                ], 500);
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
            'is_default' => ['sometimes', 'boolean']
        ]);

        $userId = Auth::id();
        $address = null;

        DB::transaction(function () use ($data, $userId, &$address) {
            $data['user_id'] = $userId;
            $data['is_default'] = isset($data['is_default']) ? (bool) $data['is_default'] : false;

            $address = ShippingAddress::create($data);

            if ($address->is_default) {
                ShippingAddress::where('user_id', $userId)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });

        if ($request->expectsJson()) {
            $lines = array_values(array_filter([
                $address->address_line1,
                $address->address_line2,
                trim(implode(' ', array_filter([$address->city, $address->state, $address->postal_code]))),
                $address->country
            ]));

            return response()->json([
                'success' => true,
                'message' => __('checkout.address_added'),
                'address' => [
                    'id' => (string) $address->id,
                    'name' => $address->name,
                    'phone' => $address->phone,
                    'is_default' => (bool) $address->is_default,
                    'lines' => $lines
                ]
            ], 201);
        }

        return redirect()
            ->route('checkout')
            ->with('success', __('checkout.address_added'));
    }

    public function success(Order|string $order)
    {
        if (! $order instanceof Order) {
            $order = Order::with('orderItems.product')->findOrFail($order);
        } else {
            $order->loadMissing('orderItems.product');
        }

        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        $shipping = collect(json_decode($order->shipping_address ?? '[]', true));
        $items = $order->orderItems ?? collect();
        $itemsTotal = $items->sum(fn ($item) => ($item->price ?? 0) * ($item->quantity ?? 0));
        $discountAmount = $order->discount_amount ?? 0;
        $shippingTotal = max($order->total_amount - $itemsTotal + $discountAmount, 0);

        return view('pages.checkout-success', [
            'order' => $order,
            'shipping' => $shipping,
            'items' => $items,
            'itemsTotal' => $itemsTotal,
            'discountAmount' => $discountAmount,
            'shippingTotal' => $shippingTotal
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => __('checkout.invalid_coupon')
            ]);
        }

        $cartItems = Cart::where('user_id', Auth::id())->get();
        $subtotal = $cartItems->sum('subtotal');

        if ($subtotal < $coupon->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => __('checkout.min_purchase_not_met', ['amount' => $coupon->min_purchase])
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
            'message' => __('checkout.coupon_applied')
        ]);
    }
}
