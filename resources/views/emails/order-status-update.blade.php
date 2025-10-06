<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ff7043;">Order Status Update</h2>
        
        <p>Dear {{ $order->user->name }},</p>
        
        <p>Your order status has been updated.</p>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
            <p><strong>New Status:</strong> <span style="color: #ff7043; font-weight: bold;">{{ ucfirst($status) }}</span></p>
            <p><strong>Updated At:</strong> {{ now()->format('F d, Y H:i') }}</p>
        </div>
        
        @if($status === 'shipped')
        <p>Your order has been shipped and is on its way to you!</p>
        @elseif($status === 'delivered')
        <p>Your order has been delivered. We hope you enjoy your purchase!</p>
        @elseif($status === 'cancelled')
        <p>Your order has been cancelled. If you have any questions, please contact us.</p>
        @endif
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p>You can view your order details by visiting your account page.</p>
        
        <p style="color: #999; font-size: 12px;">
            If you have any questions, please contact us at {{ config('mail.from.address') }}
        </p>
    </div>
</body>
</html>
