<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderTrackingController extends Controller
{
    /**
     * Resolve an order by its tracking token.
     */
    private function findOrderByToken(string $token, array $with = []): Order
    {
        $query = Order::query();

        if (! empty($with)) {
            $query->with($with);
        }

        return $query
            ->where('track_token', $token)
            ->firstOrFail();
    }

    /**
     * Determine the badge variant for a payment status.
     */
    private function paymentBadgeVariant(?string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'paid', 'completed' => 'success',
            'failed', 'canceled', 'cancelled', 'refunded', 'expired' => 'danger',
            default => 'warning',
        };
    }

    public function show(string $locale, string $token)
    {
        $order = $this->findOrderByToken($token, ['orderItems.product']);

        return view('pages.track-order-public', [
            'order' => $order
        ]);
    }

    public function status(string $locale, string $token)
    {
        $order = $this->findOrderByToken($token);

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'status_label' => ucfirst((string) $order->status),
            'payment_status' => $order->payment_status,
            'payment_status_label' => ucfirst((string) $order->payment_status),
            'payment_status_variant' => $this->paymentBadgeVariant($order->payment_status),
            'payment_verified_at' => $order->payment_verified_at?->toIso8601String(),
            'updated_at' => $order->updated_at?->toIso8601String(),
        ]);
    }
}
