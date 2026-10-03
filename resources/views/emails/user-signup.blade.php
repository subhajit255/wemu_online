<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Sacred Bloom</title>
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
            font-size: 22px;
            font-weight: normal;
            font-family: 'Georgia', serif;
        }
        .content p {
            color: #a1a1aa;
            font-size: 15px;
            margin-bottom: 20px;
        }
        .summary-box {
            background-color: #222222;
            border: 1px solid #333333;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .summary-box-title {
            color: #d4a373;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 0;
            font-size: 14px;
        }
        .summary-label {
            color: #a1a1aa;
            width: 40%;
        }
        .summary-value {
            color: #ffffff;
            font-weight: 500;
        }
        .summary-value a {
            color: #93c5fd;
            text-decoration: none;
        }
        .badge {
            background-color: #134e4a;
            color: #5eead4;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
        }
        .journey-section h3 {
            font-family: 'Georgia', serif;
            font-size: 18px;
            color: #ffffff;
            margin-bottom: 20px;
            font-weight: normal;
        }
        .journey-item {
            margin-bottom: 20px;
        }
        .journey-item table {
            width: 100%;
        }
        .journey-icon {
            width: 40px;
            vertical-align: top;
            font-size: 20px;
        }
        .journey-text h4 {
            margin: 0 0 5px 0;
            color: #ffffff;
            font-size: 15px;
        }
        .journey-text p {
            margin: 0;
            font-size: 13px;
            color: #a1a1aa;
        }
        .actions {
            margin: 40px 0 20px 0;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 15px 0;
            text-align: center;
            border-radius: 25px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 15px;
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
            <p>YOUR ULTIMATE MUSIC EXPERIENCE</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Welcome, {{ $name ?? 'Noelle Stanley' }} 🎵</h2>
            <p>Welcome to WEMU! We are thrilled to welcome you into our community of music lovers, artists, and creators.</p>
            <p>Your account is now active. Whether you're discovering new genres, following your favorite artists, or curating the perfect playlist, we are dedicated to providing you with an uninterrupted streaming experience.</p>

            <!-- Account Summary -->
            <div class="summary-box">
                <div class="summary-box-title">✦ YOUR ACCOUNT SUMMARY</div>
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Full Name:</td>
                        <td class="summary-value">{{ $name ?? 'Noelle Stanley' }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Email Address:</td>
                        <td class="summary-value"><a href="mailto:{{ $email ?? 'mankur430@gmail.com' }}">{{ $email ?? 'mankur430@gmail.com' }}</a></td>
                    </tr>
                    <tr>
                        <td class="summary-label">Member Status:</td>
                        <td class="summary-value"><span class="badge">Active Listener</span></td>
                    </tr>
                    <tr>
                        <td class="summary-label">Joined On:</td>
                        <td class="summary-value">{{ $joined_date ?? 'Sep 29, 2026' }}</td>
                    </tr>
                </table>
            </div>

            <!-- Journey Section -->
            <div class="journey-section">
                <h3>How to begin your musical journey:</h3>
                
                <div class="journey-item">
                    <table>
                        <tr>
                            <td class="journey-icon">🎶</td>
                            <td class="journey-text">
                                <h4>Discover New Releases</h4>
                                <p>Browse through the latest tracks, trending albums, and exclusive hits tailored to your taste.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="journey-item">
                    <table>
                        <tr>
                            <td class="journey-icon">📝</td>
                            <td class="journey-text">
                                <h4>Create Custom Playlists</h4>
                                <p>Curate your own personalized playlists for every mood and seamlessly add your favorite songs.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="journey-item">
                    <table>
                        <tr>
                            <td class="journey-icon">✨</td>
                            <td class="journey-text">
                                <h4>Follow Your Artists</h4>
                                <p>Stay updated with your favorite creators, never miss a beat, and get notified about new drops.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            {{-- <div class="actions">
                <a href="#" class="btn btn-primary">Start Listening &rarr;</a>
                <a href="#" class="btn btn-secondary">View My Profile</a>
            </div> --}}

            <!-- Help Text -->
            <div class="help-text">
                Need help navigating the app or finding your first track? Feel free to reply directly to this email or visit our support center.
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
            <p>You are receiving this confirmation because you signed up for WEMU.</p>
        </div>
    </div>
</body>
</html>
