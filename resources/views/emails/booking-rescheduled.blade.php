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
            <h1>Booking Rescheduled</h1>
        </div>

        <div class="content">
            <h2>Hi {{ $booking->user->name }},</h2>

            <p>Your booking has been successfully rescheduled. Here are your updated booking details:</p>

            <div class="booking-details">
                <p><strong>Booking Reference:</strong> #{{ $booking->id }}</p>
                <h3>Previous Time:</h3>
                <p>{{ \Carbon\Carbon::parse($oldDate)->format('l, F j, Y') }} at {{ $oldTime }}</p>

                <h3>New Time:</h3>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $booking->booking_time }}</p>
                <p><strong>Barber:</strong> {{ $booking->barber->name }}</p>
            </div>

            <p><strong>Important Notes:</strong></p>
            <ul>
                <li>Please arrive 5-10 minutes before your appointment time</li>
                <li>Your existing deposit has been transferred to this new booking</li>
                <li>If you need to make any further changes, please do so at least 24 hours in advance</li>
            </ul>

            <p>Need to view or manage your booking?</p>

            <a href="{{ url('/dashboard') }}" class="button">View Booking Details</a>

            <p>If you have any questions about your rescheduled appointment, please don't hesitate to contact us.</p>

            <p>Best regards,<br>The MickyesBarbers Team</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} MickyesBarbers. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>
