<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Order::with('user')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Customer', 'Total', 'Status', 'Created At'];
    }

    public function map($order): array
    {
        return [
            $order->id,
            optional($order->user)->name,
            $order->total_amount,
            $order->status,
            optional($order->created_at)->toDateTimeString(),
        ];
    }
}

