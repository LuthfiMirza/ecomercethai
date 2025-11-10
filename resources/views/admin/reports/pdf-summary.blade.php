<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reports Summary</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
        h2 { margin: 0 0 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; }
        th { background: #f8fafc; text-align: left; }
        .brand { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .muted { color: #64748b; font-size: 12px; }
    </style>
    </head>
<body>
    <div class="brand">
        <h2>{{ config('app.name', 'Lungpaeit') }} — Reports</h2>
        <span class="muted">Generated: {{ now()->format('d M Y H:i') }}</span>
    </div>
    <h3>Monthly Sales</h3>
    <table>
        <thead><tr><th>Month</th><th>Revenue</th><th>Orders</th></tr></thead>
        <tbody>
            @foreach(($months ?? []) as $i=>$m)
            <tr>
                <td>{{ $m }}</td>
                <td>Rp {{ number_format(($revenue[$i] ?? 0),0,',','.') }}</td>
                <td>{{ $orders[$i] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Top Products</h3>
    <table>
        <thead><tr><th>#</th><th>Product</th><th>Sold</th><th>Price</th></tr></thead>
        <tbody>
            @foreach(($topProducts ?? []) as $i=>$row)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $row->product->name ?? 'Product' }}</td>
                <td>{{ $row->sold }}</td>
                <td>Rp {{ number_format($row->product->price ?? 0,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Revenue by Category</h3>
    <table>
        <thead><tr><th>Category</th><th>Revenue</th></tr></thead>
        <tbody>
            @foreach(($categoryLabels ?? []) as $i=>$label)
            <tr>
                <td>{{ $label }}</td>
                <td>Rp {{ number_format(($categoryData[$i] ?? 0),0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
