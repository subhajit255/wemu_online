<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan Purchase & Order Confirmation</title>
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
        }
        .content h2 {
            margin-top: 0;
            color: #ffffff;
            font-size: 20px;
            font-weight: normal;
            font-family: 'Georgia', serif;
        }
        .content > p {
            color: #a1a1aa;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .summary-box {
            background-color: #222222;
            border: 1px solid #333333;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .summary-box-title {
            color: #d4a373;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .plan-title {
            color: #ffffff;
            font-size: 18px;
            font-weight: 500;
            margin: 0 0 15px 0;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 0;
            font-size: 13px;
            vertical-align: top;
        }
        .summary-label {
            color: #a1a1aa;
            width: 35%;
        }
        .summary-value {
            color: #ffffff;
        }
        .highlight-green {
            color: #4ade80;
            font-weight: 500;
        }
        .info-box {
            border: 1px solid #404040;
            border-radius: 8px;
            padding: 15px 20px;
            font-size: 13px;
            color: #a1a1aa;
            margin: 20px 0;
        }
        .included-section {
            margin: 25px 0;
        }
        .included-title {
            font-size: 12px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .included-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .included-list li {
            font-size: 13px;
            color: #a1a1aa;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }
        .included-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #e5e7eb;
        }
        .booking-box {
            background-color: transparent;
            border: 1px solid #3f3f46;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .booking-box h4 {
            margin: 0 0 10px 0;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
        }
        .booking-box p {
            margin: 0;
            color: #a1a1aa;
            font-size: 13px;
            line-height: 1.5;
        }
        .actions {
            margin: 30px 0 20px 0;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 15px 0;
            text-align: center;
            border-radius: 25px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 12px;
            box-sizing: border-box;
        }
        .btn-primary {
            background-color: #34714d;
            color: #ffffff;
            border: 1px solid #34714d;
        }
        .btn-secondary {
            background-color: transparent;
            color: #e5e7eb;
            border: 1px solid #404040;
        }
        .help-text {
            font-size: 12px;
            color: #71717a;
            line-height: 1.5;
            margin-top: 30px;
            border-top: 1px solid #333333;
            padding-top: 30px;
        }
        .help-text a {
            color: #a7c0b0;
            text-decoration: underline;
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
            font-size: 13px;
        }
        .footer p {
            margin: 5px 0;
            color: #71717a;
            font-size: 11px;
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
            <p>SUBSCRIPTION & ORDER CONFIRMATION</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Welcome, {{ $name ?? 'Noelle Stanley' }} 🎵</h2>
            <p>Thank you for joining the premium experience! Your subscription to <strong>{{ $plan_name ?? 'WEMU Premium' }}</strong> has been completed successfully and your access is active.</p>

            <!-- Plan Details Box -->
            <div class="summary-box">
                <div class="summary-box-title">✦ ACTIVE PLAN & DETAILS</div>
                <h3 class="plan-title">{{ $plan_name ?? 'WEMU Premium' }}</h3>
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Plan Benefits:</td>
                        <td class="summary-value"><span style="color:#f87171">🎶</span> Ad-Free Music & Unlimited Skips</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Current Status:</td>
                        <td class="summary-value" style="font-weight: 500;">Active Premium Membership</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Valid Through:</td>
                        <td class="summary-value">{{ $valid_through ?? 'November 24, 2026' }}</td>
                    </tr>
                </table>
            </div>

            <!-- Receipt Box -->
            <div class="summary-box">
                <div class="summary-box-title">✦ PAYMENT RECEIPT</div>
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Order Reference:</td>
                        <td class="summary-value" style="word-break: break-all; font-size: 11px;">{{ $order_ref ?? 'wemu_sub_a1LzmLGQQzUmZ08rD8jqMBHeJ4dNuYQC15jp5r7K3wo1zyCg3qhyEAiWi8' }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Date:</td>
                        <td class="summary-value">{{ $order_date ?? 'Sep 29, 2026 - 12:18 PM' }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Plan Price:</td>
                        <td class="summary-value">{{ $plan_price ?? '$12.99/mo' }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label" style="font-weight: 600; color: #ffffff; padding-top: 15px;">Total Paid:</td>
                        <td class="summary-value" style="font-weight: 600; font-size: 15px; padding-top: 15px;">{{ $total_paid ?? '$12.99' }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Payment Status:</td>
                        <td class="summary-value highlight-green">✓ Paid / Active</td>
                    </tr>
                </table>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                {{ $plan_description ?? 'Unlimited ad-free music, valid for 1 month.' }}
            </div>

            <!-- Included Section -->
            <div class="included-section">
                <div class="included-title">WHAT'S INCLUDED:</div>
                <ul class="included-list">
                    <li>Ad-free listening experience</li>
                    <li>Unlimited skips and replays</li>
                    <li>High-quality audio streaming</li>
                    <li>Offline downloads</li>
                </ul>
            </div>

            <!-- Booking Box -->
            <div class="booking-box">
                <h4>How to start listening:</h4>
                <p><strong>Your unlimited access is applied automatically when logged into your account.</strong> Visit the app, search for your favorite tracks, and hit <strong>Play</strong>.</p>
            </div>

            <!-- Actions -->
            <div class="actions">
                <a href="#" class="btn btn-primary">Start Listening &rarr;</a>
                <a href="#" class="btn btn-secondary">Manage Subscription</a>
            </div>

            <!-- Help Text -->
            <div class="help-text">
                Need assistance with your subscription or have questions about billing? Feel free to reply directly to this email (<a href="mailto:support@wemu.com">support@wemu.com</a>) or visit our help center.
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
            <p>You received this purchase confirmation because you subscribed to WEMU.</p>
        </div>
    </div>
</body>
</html>
