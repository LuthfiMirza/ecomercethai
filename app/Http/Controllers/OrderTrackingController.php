<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderTrackingController extends Controller
{
    public function show(string $token)
    {
        $order = Order::with('orderItems.product')
            ->where('track_token', $token)
            ->firstOrFail();

        return view('pages.track-order-public', [
            'order' => $order
        ]);
    }
}
