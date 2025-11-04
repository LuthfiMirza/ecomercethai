<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ff7043;">Order Confirmation</h2>
        
        <p>Dear {{ $order->user->name }},</p>
        
        <p>Thank you for your order! Your order has been received and is being processed.</p>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0;">Order Details</h3>
            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y') }}</p>
            <p><strong>Total Amount:</strong> {{ format_price($order->total_amount) }}</p>
            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
        </div>
        
        <h3>Order Items</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Product</th>
                    <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Quantity</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->product->name }}</td>
                    <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">{{ $item->quantity }}</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">{{ format_price($item->price * $item->quantity) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px; text-align: right;">
            <p><strong>Total:</strong> {{ format_price($order->total_amount) }}</p>
        </div>
        
        @if($order->track_url)
        <p style="margin-top: 20px;">
            You can track your order anytime using this link:
            <br/>
            <a href="{{ $order->track_url }}" style="color: #ff7043;">{{ $order->track_url }}</a>
        </p>
        @endif
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p>You can track your order status by visiting your account page.</p>
        
        <p style="color: #999; font-size: 12px;">
            If you have any questions, please contact us at {{ config('mail.from.address') }}
        </p>
    </div>
</body>
</html>
