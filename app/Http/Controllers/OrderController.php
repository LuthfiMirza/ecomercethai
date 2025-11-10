<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Retrieve an order that the authenticated user is allowed to access.
     */
    private function findAuthorizedOrder($orderId, array $with = []): Order
    {
        $user = Auth::user();

        if (! $user) {
            abort(404);
        }

        $query = Order::query();

        if (! empty($with)) {
            $query->with($with);
        }

        $query->where('id', $orderId);

        $isAdmin = (bool) ($user->is_admin ?? false);
        if (! $isAdmin && method_exists($user, 'hasRole')) {
            $isAdmin = $user->hasRole('admin');
        }

        if (! $isAdmin) {
            $query->where('user_id', $user->getKey());
        }

        return $query->firstOrFail();
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

    /**
     * Show a single order that belongs to the authenticated user.
     */
    public function show(string $locale, $order)
    {
        $orderData = $this->findAuthorizedOrder($order, ['orderItems.product']);

        return view('pages.order-detail', [
            'order' => $orderData,
        ]);
    }

    /**
     * Provide lightweight order information for polling.
     */
    public function status(string $locale, $order)
    {
        $orderData = $this->findAuthorizedOrder($order);

        return response()->json([
            'id' => $orderData->id,
            'status' => $orderData->status,
            'status_label' => ucfirst((string) $orderData->status),
            'payment_status' => $orderData->payment_status,
            'payment_status_label' => ucfirst((string) $orderData->payment_status),
            'payment_status_variant' => $this->paymentBadgeVariant($orderData->payment_status),
            'payment_verified_at' => $orderData->payment_verified_at?->toIso8601String(),
            'updated_at' => $orderData->updated_at?->toIso8601String(),
        ]);
    }
}
