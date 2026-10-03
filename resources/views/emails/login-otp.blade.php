<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Verification Code for WEMU</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #121212;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #e5e7eb;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #1a1a1a;
        }
        .header {
            background: linear-gradient(to bottom, #234d31, #1a3924);
            padding: 40px 20px;
            text-align: center;
            border-bottom: 2px solid #2d613f;
        }
        .header-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            letter-spacing: 3px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .header p {
            margin: 10px 0 0 0;
            color: #a7c0b0;
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .content h2 {
            margin-top: 0;
            color: #ffffff;
            font-size: 22px;
            font-weight: normal;
            font-family: 'Georgia', serif;
        }
        .content p {
            color: #a1a1aa;
            font-size: 15px;
            margin-bottom: 20px;
        }
        .otp-box {
            background-color: #222222;
            border: 1px dashed #34714d;
            border-radius: 8px;
            padding: 30px;
            margin: 30px auto;
            max-width: 300px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: 700;
            color: #5eead4;
            letter-spacing: 5px;
            margin: 0;
        }
        .help-text {
            font-size: 13px;
            color: #71717a;
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #333333;
            padding-top: 30px;
        }
        .footer {
            background-color: #141414;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #222222;
        }
        .footer h4 {
            margin: 0 0 10px 0;
            color: #ffffff;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
            color: #71717a;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <img src="{{ asset('assets/media/logos/logo.png') }}" alt="WEMU Logo" style="height: 40px; vertical-align: middle; margin-right: 10px;" />
                WEMU
            </h1>
            <p>LOGIN VERIFICATION</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Hello, {{ $name ?? 'Artist' }} 👋</h2>
            <p>You requested to securely log in to your WEMU Artist account. Please use the verification code below to complete your login.</p>

            <!-- OTP Box -->
            <div class="otp-box">
                <p style="margin-bottom: 10px; font-size: 12px; text-transform: uppercase; color: #a7c0b0; letter-spacing: 1px;">Your Verification Code</p>
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <p style="font-size: 13px; margin-top: 20px;">If you did not request this login, please safely ignore this email.</p>

            <!-- Help Text -->
            <div class="help-text">
                Need help or experiencing issues? Reply directly to this email or visit our support center.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <h4>
                <img src="{{ asset('assets/media/logos/logo.png') }}" alt="WEMU Logo" style="height: 20px; vertical-align: middle; margin-right: 5px;" />
                WEMU
            </h4>
            <p>Your ultimate destination for endless music streaming and artist discovery.</p>
            <br>
            <p>&copy; {{ date('Y') }} WEMU. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
