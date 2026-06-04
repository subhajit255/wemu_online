<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Team Invitation</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-wrapper {
            width: 100%;
            padding: 40px 0;
            background-color: #f3f4f6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background-color: #3e63dd;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            font-size: 20px;
            color: #111827;
            margin-top: 0;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #4b5563;
        }
        .credentials-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .credentials-box p {
            margin: 5px 0;
            font-size: 15px;
        }
        .credentials-box strong {
            color: #111827;
        }
        .button-container {
            text-align: center;
            margin: 35px 0 15px;
        }
        .btn {
            display: inline-block;
            background-color: #3e63dd;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="header">
                <h1>Welcome to the WEMU Team!</h1>
            </div>
            <div class="content">
                <h2>Hello {{ $user->name }},</h2>
                <p>You have been invited to join the team on WEMU by your administrator. We are excited to have you on board!</p>
                <p>An account has been automatically created for you. You can use the following credentials to log in to your dashboard and get started:</p>
                
                <div class="credentials-box">
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Password:</strong> {{ $password }}</p>
                </div>

                <p>We highly recommend changing your password after your first login for security purposes.</p>

                <div class="button-container">
                    <a href="{{ url('/') }}" class="btn">Log In to Dashboard</a>
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} WEMU. All rights reserved.</p>
                <p>If you did not expect this invitation, please ignore this email.</p>
            </div>
        </div>
    </div>
</body>
</html>
