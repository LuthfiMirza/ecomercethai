<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Show a single order that belongs to the authenticated user.
     */
    public function show(string $locale, $order)
    {
        $user = Auth::user();

        if (! $user) {
            abort(404);
        }

        $query = Order::with('orderItems.product')->where('id', $order);

        $isAdmin = (bool) ($user->is_admin ?? false);
        if (! $isAdmin && method_exists($user, 'hasRole')) {
            $isAdmin = $user->hasRole('admin');
        }

        if (! $isAdmin) {
            $query->where('user_id', $user->getKey());
        }

        $orderData = $query->firstOrFail();

        return view('pages.order-detail', [
            'order' => $orderData,
        ]);
    }
}
