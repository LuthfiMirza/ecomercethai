<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact Form Submission</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ff7043;">New Contact Form Submission</h2>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Name:</strong> {{ $data['name'] }}</p>
            <p><strong>Email:</strong> {{ $data['email'] }}</p>
            <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
        </div>
        
        <div style="margin: 20px 0;">
            <p><strong>Message:</strong></p>
            <p style="background: #fff; padding: 15px; border-left: 4px solid #ff7043;">
                {{ $data['message'] }}
            </p>
        </div>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="color: #999; font-size: 12px;">
            This email was sent from the contact form on {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
