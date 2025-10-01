<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalPayments = Payment::count();
        $activeBanners = Banner::active()->count();

        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));
        $revenueByDay = $days->mapWithKeys(fn ($date) => [
            $date => (float) Payment::whereDate('created_at', $date)
                ->where('status', 'paid')
                ->sum('amount'),
        ]);

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalProducts' => $totalProducts,
            'totalPayments' => $totalPayments,
            'activeBanners' => $activeBanners,
            'revenueLabels' => $revenueByDay->keys()->values(),
            'revenueData' => $revenueByDay->values(),
        ]);
    }
}
