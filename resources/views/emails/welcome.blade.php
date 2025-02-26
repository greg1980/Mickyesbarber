<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9fafb;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.8em;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to MickyesBarbers!</h1>
        </div>

        <div class="content">
            <h2>Hi {{ $user->name }},</h2>

            <p>Welcome to MickyesBarbers! We're thrilled to have you as a member of our community.</p>

            <p>With your new account, you can:</p>
            <ul>
                <li>Book appointments with our skilled barbers</li>
                <li>View your booking history</li>
                <li>Share your transformation photos</li>
                <li>Leave reviews for our services</li>
            </ul>

            <p>Ready to book your first appointment?</p>

            <a href="{{ url('/dashboard') }}" class="button">Visit Your Dashboard</a>

            <p>If you have any questions or need assistance, don't hesitate to contact us.</p>

            <p>Best regards,<br>The MickyesBarbers Team</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} MickyesBarbers. All rights reserved.</p>
            <p>You're receiving this email because you recently created a new account.</p>
        </div>
    </div>
</body>
</html>
