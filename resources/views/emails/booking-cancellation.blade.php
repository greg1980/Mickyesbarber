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
        .booking-details {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
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
            <h1>Booking Cancellation</h1>
        </div>

        <div class="content">
            <h2>Hi {{ $booking->user->name }},</h2>

            <p>Your booking has been cancelled as requested. Here are the cancelled booking details:</p>

            <div class="booking-details">
                <p><strong>Booking Reference:</strong> #{{ $booking->id }}</p>
                <p><strong>Date:</strong> {{ $booking->booking_date->format('l, F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $booking->booking_time->format('g:i A') }}</p>
                <p><strong>Barber:</strong> {{ $booking->barber->name }}</p>
            </div>

            @if($booking->deposit_amount > 0)
            <p><strong>Refund Information:</strong></p>
            <p>Your deposit of £{{ number_format($booking->deposit_amount, 2) }} will be refunded to your original payment method within 5-7 business days.</p>
            @endif

            <p>Would you like to book another appointment?</p>

            <a href="{{ url('/dashboard') }}" class="button">Book New Appointment</a>

            <p>If you have any questions about the cancellation or refund process, please don't hesitate to contact us.</p>

            <p>Best regards,<br>The MickyesBarbers Team</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} MickyesBarbers. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>
