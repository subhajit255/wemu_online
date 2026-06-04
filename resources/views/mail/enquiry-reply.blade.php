<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response to your Query - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #374151;
            -webkit-font-smoothing: antialiased;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 0;
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
            background-color: #0d3d36;
            padding: 32px 40px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .body-content {
            padding: 40px;
            background-color: #ffffff;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 24px;
            margin-top: 0;
        }

        .intro-text {
            font-size: 15px;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 32px;
        }

        .query-box {
            background-color: #f9fafb;
            border-left: 4px solid #9ca3af;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 32px;
        }

        .query-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            color: #6b7280;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .query-text {
            font-size: 15px;
            color: #374151;
            line-height: 1.5;
            margin: 0;
            font-style: italic;
        }

        .reply-box {
            background-color: #f0fdfa;
            border: 1px solid #ccfbf1;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 32px;
        }

        .reply-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            color: #0d3d36;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .reply-text {
            font-size: 16px;
            color: #111827;
            line-height: 1.6;
            margin: 0;
        }
        
        .reply-text p {
            margin-top: 0;
        }

        .footer {
            background-color: #f9fafb;
            padding: 32px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer-text {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
            margin: 0;
        }

        .footer-brand {
            font-weight: 700;
            color: #374151;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .header, .body-content, .footer {
                padding: 24px !important;
            }
        }
    </style>
</head>
<body>
    <table class="email-wrapper" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="email-container" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <h1>{{ config('app.name') }}</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="body-content">
                            <p class="greeting">Hello,</p>
                            
                            <p class="intro-text">
                                Thank you for contacting the {{ config('app.name') }} support team. We have received your inquiry and our team has prepared a response for you below.
                            </p>
                            
                            <!-- Original Query -->
                            <div class="query-box">
                                <div class="query-label">Your Original Message</div>
                                <p class="query-text">"{!! nl2br(e($enquiry ?? '')) !!}"</p>
                            </div>
                            
                            <!-- Admin Reply -->
                            <div class="reply-box">
                                <div class="reply-label">Our Response</div>
                                <div class="reply-text">
                                    {!! nl2br(e($reply ?? '')) !!}
                                </div>
                            </div>
                            
                            <p class="intro-text" style="margin-bottom: 0;">
                                If you have any further questions or need additional assistance, please don't hesitate to reach out to us again.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p class="footer-text">
                                Best regards,<br>
                                <span class="footer-brand">The {{ config('app.name') }} Team</span>
                            </p>
                            <p class="footer-text" style="margin-top: 16px; font-size: 11px;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
