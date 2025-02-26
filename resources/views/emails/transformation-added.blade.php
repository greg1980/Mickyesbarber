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
        .transformation-details {
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
        .rating {
            color: #fbbf24;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Transformation Added</h1>
        </div>

        <div class="content">
            <h2>Hi {{ $transformation->user->name }},</h2>

            <p>Thank you for sharing your transformation with us! Here are the details:</p>

            <div class="transformation-details">
                <p><strong>Date:</strong> {{ $transformation->created_at->format('l, F j, Y') }}</p>
                <p><strong>Barber:</strong> {{ $transformation->barber->name }}</p>
                <p><strong>Style:</strong> {{ $transformation->haircut_style }}</p>
                @if($transformation->rating)
                <p><strong>Your Rating:</strong> <span class="rating">{{ str_repeat('★', $transformation->rating) }}{{ str_repeat('☆', 5 - $transformation->rating) }}</span></p>
                @endif
                @if($transformation->review)
                <p><strong>Your Review:</strong> {{ $transformation->review }}</p>
                @endif
            </div>

            <p>Your transformation photos have been successfully uploaded and are now visible on your profile.</p>

            <p>Want to view your transformation?</p>

            <a href="{{ url('/transformations') }}" class="button">View Transformation</a>

            <p>Thank you for choosing MickyesBarbers for your grooming needs!</p>

            <p>Best regards,<br>The MickyesBarbers Team</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} MickyesBarbers. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>
