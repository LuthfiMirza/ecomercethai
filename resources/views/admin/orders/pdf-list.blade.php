<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
        .brand { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .brand h2 { margin: 0; font-size: 18px; }
        .badge { display:inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-indigo { background: #e0e7ff; color: #3730a3; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #e5e7eb; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; }
        th { background: #f8fafc; text-align: left; }
    </style>
    </head>
<body>
    <div class="brand">
        <h2>{{ config('app.name', 'Lungpaeit') }} — Orders</h2>
        <span style="font-size: 12px; color:#64748b;">Generated: {{ now()->format('d M Y H:i') }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name ?? '-' }}</td>
                    <td>{{ format_price($order->total_amount ?? 0) }}</td>
                    <td>
                        @php($s = $order->status)
                        <span class="badge {{ $s==='completed'?'badge-green':($s==='processing'?'badge-blue':($s==='pending'?'badge-yellow':($s==='shipped'?'badge-indigo':($s==='cancelled'?'badge-red':'badge-gray')))) }}">
                            {{ ucfirst($s) }}
                        </span>
                    </td>
                    <td>{{ $order->created_at?->format('d M Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
