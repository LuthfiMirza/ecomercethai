<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function bankTransfer($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pages.payment.bank-transfer', compact('order'));
    }

    public function uploadProof(Request $request, $orderId)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->payment_status !== 'pending') {
            return back()->with('error', __('payment.already_processed'));
        }

        // Delete old proof if exists
        if ($order->payment_proof_path) {
            Storage::disk('public')->delete($order->payment_proof_path);
        }

        // Store new proof
        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof_path' => $path,
            'payment_status' => 'verifying',
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', __('payment.proof_uploaded'));
    }

    // Midtrans integration placeholder
    public function midtrans($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // TODO: Implement Midtrans Snap integration
        // 1. Set your Merchant Server Key
        // 2. Create transaction details
        // 3. Get Snap Token
        // 4. Redirect to Snap payment page

        return view('pages.payment.midtrans', compact('order'));
    }

    // Xendit integration placeholder
    public function xendit($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // TODO: Implement Xendit integration
        // 1. Create invoice
        // 2. Redirect to payment page

        return view('pages.payment.xendit', compact('order'));
    }

    // Stripe integration placeholder
    public function stripe($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // TODO: Implement Stripe Checkout integration
        // 1. Create checkout session
        // 2. Redirect to Stripe checkout

        return view('pages.payment.stripe', compact('order'));
    }

    public function callback(Request $request)
    {
        // Handle payment gateway callbacks
        // This will be different for each gateway
        
        return response()->json(['success' => true]);
    }
}
