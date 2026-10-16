<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="dark light" />
    <meta name="supported-color-schemes" content="dark light" />
    <title>The New {{ $appName ?? 'WEMU' }} is Here!</title>
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            min-width: 100% !important;
            background-color: #07090e;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #e2e8f0;
        }
        @media only screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .content-padding {
                padding: 22px 18px !important;
            }
            .feature-column {
                display: block !important;
                width: 100% !important;
                padding-bottom: 12px !important;
            }
            .header-padding {
                padding: 30px 18px 20px 18px !important;
            }
            .hero-title {
                font-size: 24px !important;
                line-height: 32px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #07090e;">
    @php
        $logoSrc = isset($message) 
            ? $message->embed(public_path('assets/media/logos/wemu-mail-logo.png')) 
            : (file_exists(public_path('assets/media/logos/wemu-mail-logo.png')) 
                ? 'assets/media/logos/wemu-mail-logo.png' 
                : asset('assets/media/logos/logo.png'));
    @endphp

    <!-- Preheader Text (Hidden Preview in Inbox) -->
    <div style="display: none; font-size: 1px; color: #07090e; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">
        A brand new version of {{ $appName ?? 'WEMU' }} is launched! Update your app and sign in with {{ $email ?? 'your email' }} to start streaming.
    </div>

    <!-- Main Wrapper Table -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #07090e; padding: 30px 0;">
        <tr>
            <td align="center">
                <!-- Inner Container -->
                <table role="presentation" class="email-container" border="0" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; width: 100%; background-color: #10141e; border-radius: 18px; overflow: hidden; border: 1px solid #1c2333; box-shadow: 0 16px 50px rgba(0, 0, 0, 0.7);">
                    
                    <!-- Top Glowing Ambient Accent -->
                    <tr>
                        <td height="4" style="background: linear-gradient(90deg, #0ca68f 0%, #00f2fe 50%, #7928ca 100%);"></td>
                    </tr>

                    <!-- Header with WEMU Logo on Top -->
                    <tr>
                        <td align="center" class="header-padding" style="padding: 38px 30px 24px 30px; background: radial-gradient(circle at 50% 20%, #1a2233 0%, #10141e 75%);">
                            <!-- Official WEMU Logo -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 16px;">
                                        <div style="display: inline-block; padding: 6px; background: linear-gradient(135deg, rgba(12, 166, 143, 0.4), rgba(0, 242, 254, 0.2), rgba(121, 40, 202, 0.3)); border-radius: 22px; box-shadow: 0 0 30px rgba(0, 242, 254, 0.2);">
                                            <img src="{{ $logoSrc }}" alt="WEMU" width="96" height="96" style="display: block; border-radius: 18px; width: 96px; height: 96px;" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; padding: 6px 16px; background: rgba(0, 242, 254, 0.08); border: 1px solid rgba(0, 242, 254, 0.3); border-radius: 30px; margin-bottom: 12px;">
                                            <span style="font-size: 11px; font-weight: 700; letter-spacing: 2px; color: #00f2fe; text-transform: uppercase;">
                                                🚀 MAJOR UPDATE • VERSION 2.0
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="margin: 0; font-size: 30px; font-weight: 800; letter-spacing: 2px; color: #ffffff; text-transform: uppercase;">
                                            WEMU
                                        </h1>
                                        <p style="margin: 4px 0 0 0; font-size: 12px; letter-spacing: 3px; color: #64748b; text-transform: uppercase;">
                                            The Sound of Tomorrow
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Hero Section -->
                    <tr>
                        <td class="content-padding" style="padding: 10px 36px 16px 36px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <h2 class="hero-title" style="margin: 0; font-size: 26px; line-height: 34px; font-weight: 700; color: #ffffff; text-align: center;">
                                            The Brand New <span style="background: linear-gradient(90deg, #00f2fe, #38bdf8); -webkit-background-clip: text; -webkit-text-fill-color: #00f2fe; color: #00f2fe;">WEMU</span> is Here! 🎧
                                        </h2>
                                        <p style="margin: 12px 0 0 0; font-size: 15px; line-height: 24px; color: #94a3b8; text-align: center;">
                                            We've completely rebuilt and supercharged your music streaming experience. Faster, smoother, and packed with studio-grade sound.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Instruction Card (3 Simple Steps) -->
                    <tr>
                        <td class="content-padding" style="padding: 8px 36px 20px 36px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #151a26; border: 1px solid #222b3d; border-radius: 14px; padding: 22px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 12px; font-weight: 700; letter-spacing: 1.5px; color: #38bdf8; text-transform: uppercase; margin-bottom: 16px;">
                                            ⚡ QUICK 3-STEP GUIDE TO GET STARTED
                                        </div>

                                        <!-- Step 1 -->
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 14px;">
                                            <tr>
                                                <td width="34" valign="top" style="padding-right: 12px;">
                                                    <div style="width: 30px; height: 30px; border-radius: 50%; background: rgba(56, 189, 248, 0.15); color: #38bdf8; text-align: center; line-height: 30px; font-weight: 700; font-size: 13px;">1</div>
                                                </td>
                                                <td valign="top">
                                                    <strong style="color: #ffffff; font-size: 14px;">Update or Install the Latest App</strong>
                                                    <p style="margin: 3px 0 0 0; color: #94a3b8; font-size: 13px; line-height: 19px;">
                                                        Head over to your app store (iOS / Android) or visit our website to install the newest update.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Step 2 -->
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 14px;">
                                            <tr>
                                                <td width="34" valign="top" style="padding-right: 12px;">
                                                    <div style="width: 30px; height: 30px; border-radius: 50%; background: rgba(168, 85, 247, 0.15); color: #c084fc; text-align: center; line-height: 30px; font-weight: 700; font-size: 13px;">2</div>
                                                </td>
                                                <td valign="top">
                                                    <strong style="color: #ffffff; font-size: 14px;">Register With Your Existing Email</strong>
                                                    <p style="margin: 3px 0 0 0; color: #94a3b8; font-size: 13px; line-height: 19px;">
                                                        Sign up or log in using your registered email address:
                                                        <br/>
                                                        <span style="display: inline-block; padding: 4px 10px; margin-top: 6px; background: rgba(0, 242, 254, 0.08); border: 1px solid rgba(0, 242, 254, 0.25); border-radius: 6px; color: #00f2fe; font-family: 'SF Mono', Consolas, Menlo, Monaco, monospace; font-size: 13px; font-weight: 600;">{{ $email }}</span>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Step 3 -->
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="34" valign="top" style="padding-right: 12px;">
                                                    <div style="width: 30px; height: 30px; border-radius: 50%; background: rgba(52, 211, 153, 0.15); color: #34d399; text-align: center; line-height: 30px; font-weight: 700; font-size: 13px;">3</div>
                                                </td>
                                                <td valign="top">
                                                    <strong style="color: #ffffff; font-size: 14px;">Enjoy Unlimited Songs & Playlists</strong>
                                                    <p style="margin: 3px 0 0 0; color: #94a3b8; font-size: 13px; line-height: 19px;">
                                                        Stream your favorite tracks, explore trending artists, and enjoy music without limits!
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- What's New Section (Focused 2 Features) -->
                    <tr>
                        <td class="content-padding" style="padding: 6px 36px 20px 36px;">
                            <div style="font-size: 12px; font-weight: 700; letter-spacing: 1.5px; color: #64748b; text-transform: uppercase; margin-bottom: 12px; text-align: center;">
                                WHAT'S NEW IN WEMU 2.0
                            </div>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td class="feature-column" width="48%" valign="top" style="background-color: #141824; border: 1px solid #1f273a; border-radius: 12px; padding: 18px;">
                                        <div style="font-size: 24px; margin-bottom: 8px;">🔊</div>
                                        <strong style="color: #ffffff; font-size: 15px;">Studio Hi-Fi Audio</strong>
                                        <p style="margin: 6px 0 0 0; color: #818cf8; font-size: 13px; line-height: 19px;">
                                            Rich low-end bass and ultra-clear acoustic fidelity with enhanced bitrate streaming.
                                        </p>
                                    </td>
                                    <td width="4%" style="font-size: 0; line-height: 0;">&nbsp;</td>
                                    <td class="feature-column" width="48%" valign="top" style="background-color: #141824; border: 1px solid #1f273a; border-radius: 12px; padding: 18px;">
                                        <div style="font-size: 24px; margin-bottom: 8px;">⚡</div>
                                        <strong style="color: #ffffff; font-size: 15px;">Instant Playback</strong>
                                        <p style="margin: 6px 0 0 0; color: #38bdf8; font-size: 13px; line-height: 19px;">
                                            Zero buffer latency with ultra-fast song switching and offline listening support.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Notice: If already registered -->
                    <tr>
                        <td class="content-padding" style="padding: 6px 36px 30px 36px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: rgba(148, 163, 184, 0.05); border: 1px dashed rgba(148, 163, 184, 0.25); border-radius: 12px; padding: 16px 20px;">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0; font-size: 13px; line-height: 20px; color: #94a3b8; text-align: center;">
                                            ✨ <strong style="color: #e2e8f0;">Already registered with this email?</strong> You're already set to go! Feel free to disregard this email and continue enjoying your music.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td height="1" style="background-color: #1c2333; font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" class="content-padding" style="padding: 28px 30px; background-color: #0b0e15;">
                            <p style="margin: 0 0 10px 0; font-size: 13px; color: #64748b; line-height: 18px;">
                                Need help or have questions? Contact us anytime at <a href="mailto:support@wemu.online" style="color: #38bdf8; text-decoration: none;">support@wemu.online</a>
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #475569; line-height: 16px;">
                                &copy; {{ date('Y') }} {{ $appName ?? 'WEMU' }}. All rights reserved.<br/>
                                You received this email because your email address was registered on {{ $appName ?? 'WEMU' }}.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Inner Container -->
            </td>
        </tr>
    </table>
</body>
</html>
