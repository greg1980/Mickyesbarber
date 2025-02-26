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
            <h1>Welcome to the MickyesBarbers Team!</h1>
        </div>

        <div class="content">
            <h2>Hi {{ $barber->name }},</h2>

            <p>Congratulations! Your registration as a barber at MickyesBarbers has been approved.</p>

            <p>Your profile details:</p>
            <ul>
                <li>Name: {{ $barber->name }}</li>
                <li>Experience: {{ $barber->years_of_experience }} years</li>
                <li>Specialties: {{ implode(', ', $barber->specialties) }}</li>
            </ul>

            <p>As a MickyesBarbers team member, you can now:</p>
            <ul>
                <li>Manage your availability</li>
                <li>View your upcoming appointments</li>
                <li>Share transformation photos</li>
                <li>Interact with clients</li>
            </ul>

            <p>Ready to start?</p>

            <a href="{{ url('/dashboard') }}" class="button">Access Your Dashboard</a>

            <p>If you have any questions or need assistance, please contact our management team.</p>

            <p>Best regards,<br>The MickyesBarbers Management Team</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} MickyesBarbers. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
