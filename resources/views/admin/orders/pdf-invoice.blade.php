<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #ORD{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
        h2 { margin: 0; }
        .brand { display:flex; align-items:center; justify-content:space-between; margin-bottom: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #e5e7eb; padding:6px; }
        th { background:#f8fafc; text-align:left; }
        .right { text-align:right; }
        .muted { color:#64748b; font-size:11px; }
        .badge { display:inline-block; padding:2px 8px; border-radius:9999px; font-size:10px; font-weight:600; }
        .badge-success { background:#d1fae5; color:#047857; }
        .badge-info { background:#e0f2fe; color:#0369a1; }
        .badge-warn { background:#fef3c7; color:#b45309; }
        .badge-neutral { background:#e2e8f0; color:#475569; }
    </style>
</head>
<body>
    @php
        $subtotal = $order->orderItems->sum(fn($i) => ($i->price ?? 0) * ($i->quantity ?? 0));
        $discount = (float)($order->discount_amount ?? ($order->discount ?? 0));
        $shipping = (float)($order->shipping_cost ?? 0);
        $total = max(0, $subtotal + $shipping - $discount);
        $paymentStatus = $order->payment_status ?? 'pending';
        $paymentBadge = match($paymentStatus) {
            'paid' => 'badge-success',
            'processing' => 'badge-info',
            'pending' => 'badge-warn',
            default => 'badge-neutral',
        };
        $paymentLabel = ucfirst(str_replace('_', ' ', $paymentStatus));
        $paymentMethodLabel = $order->payment_method ? ucwords(str_replace(['_', '-'], ' ', $order->payment_method)) : 'N/A';
        $shippingAddress = collect(json_decode($order->shipping_address ?? '[]', true));
        if ($shippingAddress->isEmpty() && filled($order->shipping_address)) {
            $shippingAddress = collect(['address_line1' => $order->shipping_address]);
        }
        $shippingRecipient = $shippingAddress->get('name');
        $shippingPhone = $shippingAddress->get('phone');
        $shippingLines = collect([
            $shippingAddress->get('address_line1'),
            $shippingAddress->get('address_line2'),
            collect([$shippingAddress->get('city'), $shippingAddress->get('state'), $shippingAddress->get('postal_code')])->filter()->implode(', '),
            $shippingAddress->get('country'),
        ])->filter()->implode("\n");
    @endphp

    <div class="brand">
        <div>
            <h2>{{ config('app.name', 'Lungpaeit') }} - Invoice</h2>
            <div class="muted">Issued {{ $order->created_at?->format('d M Y') }}</div>
        </div>
        <div>
            <div><strong>Invoice:</strong> #ORD{{ $order->id }}</div>
            <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
        </div>
    </div>

    <table>
        <tr>
            <td style="width:40%; vertical-align:top;">
                <strong>Bill To</strong><br>
                {{ $order->user->name ?? 'Customer' }}<br>
                {{ $order->user->email ?? '-' }}<br>
                @if($shippingRecipient)
                    {{ $shippingRecipient }}<br>
                @endif
                @if($shippingPhone)
                    {{ $shippingPhone }}<br>
                @endif
                @if(!empty($shippingLines))
                    {!! nl2br(e($shippingLines)) !!}<br>
                @endif
            </td>
            <td style="width:30%; vertical-align:top;">
                <strong>Payment Method</strong><br>
                {{ $paymentMethodLabel }}<br>
                <span class="badge {{ $paymentBadge }}">{{ $paymentLabel }}</span><br>
                <span class="muted">Verified: {{ $order->payment_verified_at?->format('d M Y H:i') ?? '-' }}</span>
            </td>
            <td class="right" style="vertical-align:top;">
                <strong>Created At:</strong><br>
                {{ $order->created_at?->format('d M Y H:i') }}<br>
                <strong>Order Total:</strong><br>
                Rp {{ number_format($total,0,',','.') }}
            </td>
        </tr>
    </table>

    <h3 style="margin-top:18px;">Items</h3>
    <table>
        <thead><tr><th>Product</th><th class="right">Qty</th><th class="right">Price</th><th class="right">Subtotal</th></tr></thead>
        <tbody>
            @foreach($order->orderItems as $item)
            <tr>
                <td>{{ $item->product->name ?? 'Product' }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">Rp {{ number_format($item->price,0,',','.') }}</td>
                <td class="right">Rp {{ number_format(($item->price ?? 0) * ($item->quantity ?? 0),0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top:10px;">
        <tr>
            <td class="right"><strong>Subtotal</strong></td>
            <td class="right" style="width:160px;">Rp {{ number_format($subtotal,0,',','.') }}</td>
        </tr>
        @if($shipping>0)
        <tr>
            <td class="right"><strong>Shipping</strong></td>
            <td class="right">Rp {{ number_format($shipping,0,',','.') }}</td>
        </tr>
        @endif
        @if($discount>0)
        <tr>
            <td class="right"><strong>Discount</strong></td>
            <td class="right">- Rp {{ number_format($discount,0,',','.') }}</td>
        </tr>
        @endif
        <tr>
            <td class="right"><strong>Total</strong></td>
            <td class="right"><strong>Rp {{ number_format($total,0,',','.') }}</strong></td>
        </tr>
    </table>
</body>
</html>
