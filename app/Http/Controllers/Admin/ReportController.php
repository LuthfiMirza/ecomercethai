<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        // Filters
        $period = request('period', 'month'); // week|month|quarter|year|custom
        $from = request('from');
        $to = request('to');
        $categoryId = request('category_id');
        $productId = request('product_id');
        $segment = request('segment', 'all'); // all|new|returning

        // Resolve date range
        $now = now();
        if ($period === 'week') { $rangeStart = $now->copy()->startOfWeek(); $rangeEnd = $now->copy()->endOfWeek(); }
        elseif ($period === 'quarter') { $rangeStart = $now->copy()->firstOfQuarter(); $rangeEnd = $now->copy()->lastOfQuarter(); }
        elseif ($period === 'year') { $rangeStart = $now->copy()->startOfYear(); $rangeEnd = $now->copy()->endOfYear(); }
        elseif ($period === 'custom' && $from && $to) { $rangeStart = \Carbon\Carbon::parse($from)->startOfDay(); $rangeEnd = \Carbon\Carbon::parse($to)->endOfDay(); }
        else { $rangeStart = $now->copy()->startOfMonth(); $rangeEnd = $now->copy()->endOfMonth(); $period = 'month'; }

        // Time-series for overview charts (adaptive granularity)
        $days = $rangeEnd->diffInDays($rangeStart) + 1;
        $seriesLabels = collect(); $revenue = collect(); $orders = collect();
        // Base filtered orders query (do NOT mutate this; clone before adding selects)
        $baseSeries = Order::query()->whereBetween('created_at', [$rangeStart, $rangeEnd]);
        if ($categoryId || $productId) {
            $baseSeries->whereIn('orders.id', function($sub) use ($categoryId, $productId, $rangeStart, $rangeEnd){
                $sub->select('order_items.order_id')->from('order_items')
                    ->join('orders','orders.id','=','order_items.order_id')
                    ->when($categoryId, fn($q)=>$q->join('products','products.id','=','order_items.product_id')->where('products.category_id',$categoryId))
                    ->when($productId, fn($q)=>$q->where('order_items.product_id',$productId))
                    ->whereBetween('orders.created_at', [$rangeStart, $rangeEnd])
                    ->groupBy('order_items.order_id');
            });
        }
        $driver = (new Order())->getConnection()->getDriverName();
        $weekExpr = $driver === 'sqlite'
            ? "strftime('%Y-%W', created_at)"
            : "DATE_FORMAT(created_at, '%x-%v')";
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        if ($days <= 31) {
            $raw = (clone $baseSeries)->selectRaw('DATE(created_at) as bucket, SUM(total_amount) as total')
                ->groupBy('bucket')->orderBy('bucket')->pluck('total','bucket');
            $rawOrders = (clone $baseSeries)->selectRaw('DATE(created_at) as bucket, COUNT(*) as cnt')
                ->groupBy('bucket')->pluck('cnt','bucket');
            $cursor = $rangeStart->copy()->startOfDay();
            while ($cursor->lte($rangeEnd)) { $k=$cursor->format('Y-m-d'); $seriesLabels->push($cursor->format('d M')); $revenue->push((float)($raw[$k]??0)); $orders->push((int)($rawOrders[$k]??0)); $cursor->addDay(); }
        } elseif ($days <= 180) {
            $raw = (clone $baseSeries)->selectRaw("$weekExpr as bucket, SUM(total_amount) as total")
                ->groupBy('bucket')->orderBy('bucket')->pluck('total','bucket');
            $rawOrders = (clone $baseSeries)->selectRaw("$weekExpr as bucket, COUNT(*) as cnt")
                ->groupBy('bucket')->pluck('cnt','bucket');
            $cursor = $rangeStart->copy()->startOfWeek();
            while ($cursor->lte($rangeEnd)) { $k=$cursor->format('o-W'); $seriesLabels->push('W'.$cursor->format('W').' '.$cursor->format('o')); $revenue->push((float)($raw[$k]??0)); $orders->push((int)($rawOrders[$k]??0)); $cursor->addWeek(); }
        } else {
            $raw = (clone $baseSeries)->selectRaw("$monthExpr as bucket, SUM(total_amount) as total")
                ->groupBy('bucket')->orderBy('bucket')->pluck('total','bucket');
            $rawOrders = (clone $baseSeries)->selectRaw("$monthExpr as bucket, COUNT(*) as cnt")
                ->groupBy('bucket')->pluck('cnt','bucket');
            $cursor = $rangeStart->copy()->startOfMonth();
            while ($cursor->lte($rangeEnd)) { $k=$cursor->format('Y-m'); $seriesLabels->push($cursor->format('M Y')); $revenue->push((float)($raw[$k]??0)); $orders->push((int)($rawOrders[$k]??0)); $cursor->addMonth(); }
        }

        // Top products (respect filters if provided)
        $topItemsQuery = OrderItem::selectRaw('product_id, SUM(quantity) as sold')
            ->join('orders','orders.id','=','order_items.order_id');
        $topItemsQuery->when($categoryId, fn($q)=>$q->join('products','products.id','=','order_items.product_id')->where('products.category_id',$categoryId));
        $topItemsQuery->when($productId, fn($q)=>$q->where('order_items.product_id',$productId));
        $topItemsQuery->whereBetween('orders.created_at', [$rangeStart, $rangeEnd])->groupBy('product_id')->orderByDesc('sold');
        $topProducts = $topItemsQuery->with('product')->take(10)->get();

        // Revenue by category (within range)
        $categoryRows = OrderItem::selectRaw('products.category_id, SUM(order_items.quantity * order_items.price) as total')
            ->join('products','products.id','=','order_items.product_id')
            ->join('orders','orders.id','=','order_items.order_id')
            ->whereBetween('orders.created_at', [$rangeStart, $rangeEnd])
            ->when($productId, fn($q)=>$q->where('order_items.product_id',$productId))
            ->groupBy('products.category_id')
            ->with('product.category')
            ->get();

        $categoryLabels = [];
        $categoryTotals = [];
        foreach ($categoryRows as $row) {
            $name = optional(optional($row->product)->category)->name ?? 'Uncategorized';
            if (!isset($categoryTotals[$name])) $categoryTotals[$name] = 0;
            $categoryTotals[$name] += (float) $row->total;
        }
        $categoryLabels = array_keys($categoryTotals);
        $categoryData = array_values($categoryTotals);

        // KPI for current period range
        $ordersInRange = Order::whereBetween('created_at', [$rangeStart, $rangeEnd]);
        if ($categoryId || $productId) {
            $ordersInRange->whereIn('orders.id', function($sub) use ($categoryId, $productId, $rangeStart, $rangeEnd) {
                $sub->select('order_items.order_id')->from('order_items')
                    ->join('orders','orders.id','=','order_items.order_id')
                    ->when($categoryId, fn($q)=>$q->join('products','products.id','=','order_items.product_id')->where('products.category_id',$categoryId))
                    ->when($productId, fn($q)=>$q->where('order_items.product_id',$productId))
                    ->whereBetween('orders.created_at', [$rangeStart, $rangeEnd])
                    ->groupBy('order_items.order_id');
            });
        }

        // Segment filter (new vs returning)
        if ($segment !== 'all') {
            $ordersInRange->whereIn('user_id', function($sub) use ($segment, $rangeStart, $rangeEnd) {
                $sub->select('user_id')->from('orders as o2')
                    ->selectRaw('MIN(o2.created_at) as first_order_at')
                    ->groupBy('user_id');
                if ($segment === 'new') {
                    $sub->havingRaw('MIN(o2.created_at) BETWEEN ? AND ?', [$rangeStart, $rangeEnd]);
                } else { // returning
                    $sub->havingRaw('MIN(o2.created_at) < ?', [$rangeStart]);
                }
            });
        }

        $totalRevenuePeriod = (float) $ordersInRange->sum('total_amount');
        $totalOrdersPeriod = (int) $ordersInRange->count();
        $distinctCustomersPeriod = (int) (clone $ordersInRange)->distinct('user_id')->count('user_id');
        $aov = $totalOrdersPeriod > 0 ? round($totalRevenuePeriod / $totalOrdersPeriod, 2) : 0;
        $clv = $distinctCustomersPeriod > 0 ? round($totalRevenuePeriod / $distinctCustomersPeriod, 2) : 0;

        // MoM change (revenue)
        $lastStart = $rangeStart->copy()->subMonths(1)->startOfDay();
        $lastEnd = $rangeStart->copy()->subDay()->endOfDay();
        $lastRevenue = (float) Order::whereBetween('created_at', [$lastStart, $lastEnd])->sum('total_amount');
        $momRevenueChange = $lastRevenue > 0 ? round((($totalRevenuePeriod - $lastRevenue) / $lastRevenue) * 100, 2) : 100;

        // Monthly target like dashboard
        $currentMonthRevenue = (float) Order::whereBetween('created_at', [$now->copy()->startOfMonth(), $now])->sum('total_amount');
        $lastMonthRevenue = (float) Order::whereBetween('created_at', [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()])->sum('total_amount');
        $target = max($lastMonthRevenue, 1.0);
        $gaugePercent = (float) min(100, round(($currentMonthRevenue / $target) * 100, 2));
        $todayRevenue = (float) Order::whereDate('created_at', $now->toDateString())->sum('total_amount');

        // Location analytics (top by shipping city/region parsed from address)
        $ordersForLoc = Order::whereBetween('created_at', [$rangeStart, $rangeEnd]);
        if ($categoryId || $productId) {
            $ordersForLoc->whereIn('orders.id', function($sub) use ($categoryId, $productId, $rangeStart, $rangeEnd) {
                $sub->select('order_items.order_id')->from('order_items')
                    ->join('orders','orders.id','=','order_items.order_id')
                    ->when($categoryId, fn($q)=>$q->join('products','products.id','=','order_items.product_id')->where('products.category_id',$categoryId))
                    ->when($productId, fn($q)=>$q->where('order_items.product_id',$productId))
                    ->whereBetween('orders.created_at', [$rangeStart, $rangeEnd])
                    ->groupBy('order_items.order_id');
            });
        }
        $locations = [];
        foreach ($ordersForLoc->get(['shipping_address']) as $o) {
            $addr = (string)$o->shipping_address;
            $part = trim((string) preg_replace('/^.*[,\n]\s*/', '', $addr)); // take last part after comma/newline
            if ($part === '') $part = 'Unknown';
            $locations[$part] = ($locations[$part] ?? 0) + 1;
        }
        arsort($locations);
        $locationLabels = array_slice(array_keys($locations), 0, 8);
        $locationData = array_slice(array_values($locations), 0, 8);

        // Finance: Revenue vs Cost (optional COGS%)
        $cogsPercent = (float) (env('COGS_PERCENT', null));
        $finance = [ 'revenue' => $totalRevenuePeriod, 'cost' => null, 'profit' => null, 'margin' => null, 'cogs_percent' => $cogsPercent ?: null ];
        if ($cogsPercent) {
            $cost = round($totalRevenuePeriod * ($cogsPercent/100), 2);
            $profit = round($totalRevenuePeriod - $cost, 2);
            $finance['cost'] = $cost; $finance['profit'] = $profit; $finance['margin'] = $totalRevenuePeriod>0 ? round(($profit/$totalRevenuePeriod)*100,2) : 0;
        }

        // Refunds & Returns (approx: cancelled/refunded orders)
        $refundStatuses = ['cancelled','canceled','refunded'];
        $refundsQuery = Order::whereBetween('created_at', [$rangeStart, $rangeEnd])->whereIn('status', $refundStatuses);
        $refundAmount = (float) $refundsQuery->sum('total_amount');
        $refundCount = (int) $refundsQuery->count();

        // Dropdown sources
        $categories = Category::orderBy('name')->get(['id','name']);
        $products = Product::orderBy('name')->get(['id','name']);

        return view('admin.reports.index', [
            'months' => $seriesLabels,
            'revenue' => $revenue,
            'orders' => $orders,
            'topProducts' => $topProducts,
            'categoryLabels' => $categoryLabels,
            'categoryData' => $categoryData,
            // KPI (period)
            'period' => $period,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'totalRevenue' => $totalRevenuePeriod,
            'totalOrders' => $totalOrdersPeriod,
            'aov' => $aov,
            'clv' => $clv,
            'momRevenueChange' => $momRevenueChange,
            'gaugePercent' => $gaugePercent,
            'currentMonthRevenue' => $currentMonthRevenue,
            'target' => $target,
            'todayRevenue' => $todayRevenue,
            // Extra analytics
            'locationLabels' => $locationLabels,
            'locationData' => $locationData,
            'finance' => $finance,
            'refundAmount' => $refundAmount,
            'refundCount' => $refundCount,
            // Filters
            'categories' => $categories,
            'products' => $products,
            'selectedCategory' => $categoryId,
            'selectedProduct' => $productId,
            'segment' => $segment,
        ]);
    }

    public function metrics(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $start = $from ? \Carbon\Carbon::parse($from)->startOfMonth() : now()->startOfMonth()->subMonths(11);
        $end = $to ? \Carbon\Carbon::parse($to)->endOfMonth() : now()->endOfMonth();

        $driver = (new Order())->getConnection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlyRaw = Order::selectRaw("$monthExpr as ym, SUM(total_amount) as total, COUNT(*) as orders")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();
        $months = [];
        $revenue = [];
        $orders = [];
        $cursor = $start->copy()->startOfMonth();
        while ($cursor->lte($end)) {
            $ym = $cursor->format('Y-m');
            $row = $monthlyRaw->firstWhere('ym',$ym);
            $months[] = $cursor->format('M Y');
            $revenue[] = (float) ($row->total ?? 0);
            $orders[] = (int) ($row->orders ?? 0);
            $cursor->addMonth();
        }

        // Category totals in range
        $categoryRows = OrderItem::selectRaw('products.category_id, SUM(order_items.quantity * order_items.price) as total')
            ->join('products','products.id','=','order_items.product_id')
            ->join('orders','orders.id','=','order_items.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('products.category_id')
            ->with('product.category')
            ->get();
        $categoryTotals = [];
        foreach ($categoryRows as $row) {
            $name = optional(optional($row->product)->category)->name ?? 'Uncategorized';
            $categoryTotals[$name] = ($categoryTotals[$name] ?? 0) + (float) $row->total;
        }
        $categoryLabels = array_keys($categoryTotals);
        $categoryData = array_values($categoryTotals);

        return response()->json(compact('months','revenue','orders','categoryLabels','categoryData'));
    }

    public function exportPdf()
    {
        // Build same data as index
        $start = now()->startOfMonth()->subMonths(11);
        $driver = Order::getConnection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlyRaw = Order::selectRaw("$monthExpr as ym, SUM(total_amount) as total, COUNT(*) as orders")
            ->where('created_at','>=',$start)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();
        $months = collect(); $revenue = collect(); $orders = collect();
        for ($i=0; $i<12; $i++) {
            $m = $start->copy()->addMonths($i); $ym = $m->format('Y-m');
            $row = $monthlyRaw->firstWhere('ym',$ym);
            $months->push($m->format('M Y'));
            $revenue->push((float) ($row->total ?? 0));
            $orders->push((int) ($row->orders ?? 0));
        }
        $topProducts = OrderItem::selectRaw('product_id, SUM(quantity) as sold')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('sold')
            ->take(10)
            ->get();
        $categoryRows = OrderItem::selectRaw('products.category_id, SUM(order_items.quantity * order_items.price) as total')
            ->join('products','products.id','=','order_items.product_id')
            ->groupBy('products.category_id')
            ->with('product.category')
            ->get();
        $categoryTotals = [];
        foreach ($categoryRows as $row) {
            $name = optional(optional($row->product)->category)->name ?? 'Uncategorized';
            if (!isset($categoryTotals[$name])) $categoryTotals[$name] = 0;
            $categoryTotals[$name] += (float) $row->total;
        }
        $categoryLabels = array_keys($categoryTotals);
        $categoryData = array_values($categoryTotals);

        $pdf = Pdf::loadView('admin.reports.pdf-summary', compact('months','revenue','orders','topProducts','categoryLabels','categoryData'));
        return $pdf->download('reports_'.now()->format('Ymd_His').'.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new ReportsExport, 'reports_'.now()->format('Ymd_His').'.xlsx');
    }
}
