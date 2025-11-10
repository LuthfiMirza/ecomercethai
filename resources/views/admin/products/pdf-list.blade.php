<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
        .brand { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .brand h2 { margin: 0; font-size: 18px; }
        .badge { display:inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-gray { background: #e5e7eb; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; }
        th { background: #f8fafc; text-align: left; }
    </style>
</head>
<body>
    <div class="brand">
        <h2>{{ config('app.name', 'Lungpaeit') }} — Products</h2>
        <span style="font-size: 12px; color:#64748b;">Generated: {{ now()->format('d M Y H:i') }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:50px;">ID</th>
                <th>Name</th>
                <th>Category</th>
                <th style="width:120px;">Price</th>
                <th style="width:70px;">Stock</th>
                <th style="width:90px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category->name ?? '-' }}</td>
                <td>Rp {{ number_format($p->price,0,',','.') }}</td>
                <td>{{ $p->stock }}</td>
                <td>
                    @if(($p->status ?? 'active') === 'active')
                        <span class="badge badge-green">Active</span>
                    @else
                        <span class="badge badge-gray">Inactive</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
