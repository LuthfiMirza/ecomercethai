<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Banner;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        // High-level metrics
        $totalRevenue = (float) Order::sum('total_amount');
        $totalOrders = (int) Order::count();
        $totalCustomers = (int) User::count();
        $totalProducts = (int) Product::count();
        $totalPayments = (int) Payment::count();
        $activeBanners = (int) Banner::active()->count();
        $productsSold = (int) OrderItem::sum('quantity');

        // Sales last 7 days for chart
        $start = now()->subDays(6)->startOfDay();
        $end = now()->endOfDay();
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $daily = Order::selectRaw('DATE(created_at) as d, SUM(total_amount) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('total', 'd');

        $labels = [];
        $data = [];
        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $data[] = (float) ($daily[$key] ?? 0);
        }

        // Top products by quantity sold
        $topProducts = OrderItem::selectRaw('product_id, SUM(quantity) as sold')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        // Revenue by category for dashboard
        $categoryRows = OrderItem::selectRaw('products.category_id, SUM(order_items.quantity * order_items.price) as total')
            ->join('products','products.id','=','order_items.product_id')
            ->groupBy('products.category_id')
            ->with('product.category')
            ->get();
        $catTotals = [];
        foreach ($categoryRows as $row) {
            $name = optional(optional($row->product)->category)->name ?? 'Uncategorized';
            if (!isset($catTotals[$name])) $catTotals[$name] = 0;
            $catTotals[$name] += (float) $row->total;
        }
        $categoryLabels = array_keys($catTotals);
        $categoryData = array_values($catTotals);

        // Top customers by revenue
        $topCustomers = Order::selectRaw('user_id, SUM(total_amount) as spent, COUNT(*) as orders')
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('spent')
            ->take(5)
            ->get();

        // Recent orders for table
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Growth metrics (last 30 days vs previous 30 days)
        $now = now();
        $customersLast30 = User::where('created_at', '>=', $now->copy()->subDays(30))->count();
        $customersPrev30 = User::whereBetween('created_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])->count();
        $ordersLast30 = Order::where('created_at', '>=', $now->copy()->subDays(30))->count();
        $ordersPrev30 = Order::whereBetween('created_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])->count();
        $customersGrowth = $customersPrev30 > 0 ? round((($customersLast30 - $customersPrev30) / $customersPrev30) * 100, 2) : 100;
        $ordersGrowth = $ordersPrev30 > 0 ? round((($ordersLast30 - $ordersPrev30) / $ordersPrev30) * 100, 2) : 100;

        // Monthly target gauge
        $currentMonthRevenue = (float) Order::whereBetween('created_at', [$now->copy()->startOfMonth(), $now])->sum('total_amount');
        $lastMonthRevenue = (float) Order::whereBetween('created_at', [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()])->sum('total_amount');
        $target = max($lastMonthRevenue, 1.0);
        $gaugePercent = (float) min(100, round(($currentMonthRevenue / $target) * 100, 2));
        $todayRevenue = (float) Order::whereDate('created_at', $now->toDateString())->sum('total_amount');

        // Monthly sales for bar/line charts (Jan..Dec current year)
        $yStart = now()->copy()->startOfYear();
        $months = [];
        $monthlyRevenue = [];
        $monthlyOrders = [];
        for ($i=0; $i<12; $i++) {
            $mStart = $yStart->copy()->addMonths($i);
            $mEnd = $mStart->copy()->endOfMonth();
            $months[] = $mStart->format('M');
            $monthlyRevenue[] = (float) Order::whereBetween('created_at', [$mStart, $mEnd])->sum('total_amount');
            $monthlyOrders[] = (int) Order::whereBetween('created_at', [$mStart, $mEnd])->count();
        }

        $revenueLabels = $labels;
        $revenueData = $data;

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalOrders', 'totalCustomers', 'productsSold',
            'labels', 'data', 'revenueLabels', 'revenueData', 'topProducts', 'recentOrders', 'categoryLabels', 'categoryData', 'topCustomers',
            'customersGrowth', 'ordersGrowth', 'gaugePercent', 'target', 'currentMonthRevenue', 'todayRevenue',
            'months', 'monthlyRevenue', 'monthlyOrders', 'totalProducts', 'totalPayments', 'activeBanners'
        ))->with('totalUsers', $totalCustomers);
    }

    public function metrics()
    {
        // Aggregate metrics with optional date range
        $from = request('from');
        $to = request('to');
        $start = $from ? \Carbon\Carbon::parse($from)->startOfDay() : now()->copy()->startOfMonth();
        $end = $to ? \Carbon\Carbon::parse($to)->endOfDay() : now()->copy()->endOfDay();

        // Totals
        $totalRevenue = (float) Order::whereBetween('created_at', [$start, $end])->sum('total_amount');
        $totalOrders = (int) Order::whereBetween('created_at', [$start, $end])->count();
        $totalCustomers = (int) User::count();
        $productsSold = (int) OrderItem::whereHas('order', fn($q)=>$q->whereBetween('created_at', [$start,$end]))->sum('quantity');

        // Category totals (range)
        $categoryRows = OrderItem::selectRaw('products.category_id, SUM(order_items.quantity * order_items.price) as total')
            ->join('products','products.id','=','order_items.product_id')
            ->join('orders','orders.id','=','order_items.order_id')
            ->whereBetween('orders.created_at', [$start,$end])
            ->groupBy('products.category_id')->with('product.category')->get();
        $catTotals=[]; foreach ($categoryRows as $row){ $name=optional(optional($row->product)->category)->name ?? 'Uncategorized'; $catTotals[$name]=($catTotals[$name]??0)+(float)$row->total; }
        $categoryLabels=array_keys($catTotals); $categoryData=array_values($catTotals);

        // Time-series: adapt bucket by range length
        $days = $end->diffInDays($start) + 1;
        $months=[]; $revenue=[]; $orders=[];
        if ($days <= 31) {
            $raw = Order::selectRaw('DATE(created_at) as bucket, SUM(total_amount) as total, COUNT(*) as cnt')
                ->whereBetween('created_at', [$start,$end])->groupBy('bucket')->orderBy('bucket')->get();
            $mapR = $raw->pluck('total','bucket'); $mapO = $raw->pluck('cnt','bucket');
            $cursor = $start->copy()->startOfDay();
            while ($cursor->lte($end)) { $k=$cursor->format('Y-m-d'); $months[]=$cursor->format('d M'); $revenue[]=(float)($mapR[$k]??0); $orders[]=(int)($mapO[$k]??0); $cursor->addDay(); }
        } else {
            $raw = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bucket, SUM(total_amount) as total, COUNT(*) as cnt")
                ->whereBetween('created_at', [$start,$end])->groupBy('bucket')->orderBy('bucket')->get();
            $mapR=$raw->pluck('total','bucket'); $mapO=$raw->pluck('cnt','bucket');
            $cursor = $start->copy()->startOfMonth();
            while ($cursor->lte($end)) { $k=$cursor->format('Y-m'); $months[]=$cursor->format('M Y'); $revenue[]=(float)($mapR[$k]??0); $orders[]=(int)($mapO[$k]??0); $cursor->addMonth(); }
        }

        return response()->json(compact('totalRevenue','totalOrders','totalCustomers','productsSold','categoryLabels','categoryData','months','revenue','orders'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
