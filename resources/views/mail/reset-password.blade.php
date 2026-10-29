<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Link for WEMU</title>
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
        .btn-reset {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin: 25px auto;
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
            <p>PASSWORD RESET</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Reset Your Password</h2>
            <p>You are receiving this email because we received a password reset request for your account. Please click the button below to reset your password:</p>

            <div>
                <a href="{{ $link ?? '#' }}" class="btn-reset">Reset Password</a>
            </div>

            <p style="font-size: 13px; margin-top: 20px;">If you did not request a password reset, no further action is required.</p>

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
