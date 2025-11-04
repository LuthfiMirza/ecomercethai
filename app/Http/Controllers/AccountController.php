<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Display the account page for the current visitor.
     */
    public function __invoke(Request $request): View
    {
        $orders = collect();

        if (Auth::check()) {
            $orders = Order::where('user_id', Auth::id())
                ->with('orderItems.product')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('pages.account', [
            'orders' => $orders
        ]);
    }
}
