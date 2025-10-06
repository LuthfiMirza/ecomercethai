<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportsExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Monthly Sales' => new class implements FromCollection, WithHeadings {
                public function collection()
                {
                    $start = now()->startOfMonth()->subMonths(11);
                    $rows = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, SUM(total_amount) as total, COUNT(*) as orders')
                        ->where('created_at','>=',$start)
                        ->groupBy('ym')
                        ->orderBy('ym')
                        ->get();
                    return $rows->map(fn($r)=>[ $r->ym, (float)$r->total, (int)$r->orders ]);
                }
                public function headings(): array { return ['Month','Revenue','Orders']; }
            },
            'Top Products' => new class implements FromCollection, WithHeadings {
                public function collection()
                {
                    $rows = OrderItem::selectRaw('product_id, SUM(quantity) as sold')
                        ->with('product')
                        ->groupBy('product_id')
                        ->orderByDesc('sold')
                        ->take(10)
                        ->get();
                    return $rows->map(fn($r)=>[ optional($r->product)->name, (int)$r->sold ]);
                }
                public function headings(): array { return ['Product','Sold']; }
            },
            'Top Categories' => new class implements FromCollection, WithHeadings {
                public function collection()
                {
                    $rows = OrderItem::selectRaw('products.category_id, SUM(order_items.quantity * order_items.price) as total')
                        ->join('products','products.id','=','order_items.product_id')
                        ->groupBy('products.category_id')
                        ->with('product.category')
                        ->get();
                    $totals = [];
                    foreach ($rows as $row) {
                        $name = optional(optional($row->product)->category)->name ?? 'Uncategorized';
                        if (!isset($totals[$name])) $totals[$name] = 0;
                        $totals[$name] += (float) $row->total;
                    }
                    return collect($totals)->map(function ($v, $k) { return [$k, (float) $v]; });
                }
                public function headings(): array { return ['Category','Revenue']; }
            },
            'Top Customers' => new class implements FromCollection, WithHeadings {
                public function collection()
                {
                    $rows = Order::selectRaw('user_id, SUM(total_amount) as spent, COUNT(*) as orders')
                        ->with('user')
                        ->groupBy('user_id')
                        ->orderByDesc('spent')
                        ->take(20)
                        ->get();
                    return $rows->map(fn($r)=>[ optional($r->user)->name, (float) $r->spent, (int) $r->orders ]);
                }
                public function headings(): array { return ['Customer','Spent','Orders']; }
            },
        ];
    }
}
