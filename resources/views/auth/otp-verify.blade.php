<!DOCTYPE html>
<html lang="en">

<head>
    <title>WEMU - OTP Verification</title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cdn/toastr.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('assets/js/custom_js/cdn/jquery.min.js') }}"></script>
    <style>
        .w-md-600px {
            width: 100% !important;
        }

        /* Artistic Login Styles */
        body#kt_body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #000;
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Abstract glowing background */
        .bg-mesh {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 15% 50%, rgba(76, 29, 149, 0.4), transparent 50%),
                radial-gradient(circle at 85% 30%, rgba(225, 29, 72, 0.4), transparent 50%),
                radial-gradient(circle at 50% 80%, rgba(37, 99, 235, 0.4), transparent 50%);
            background-color: #0a0a0a;
            z-index: -1;
            animation: breathe 10s ease-in-out infinite alternate;
        }

        @keyframes breathe {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }

            100% {
                transform: scale(1.1);
                opacity: 1;
            }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px 30px;
            width: 100%;
            max-width: 450px;
            margin: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 10;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-title {
            font-size: 42px;
            font-weight: 800;
            margin: 0 0 10px;
            letter-spacing: 4px;
            background: linear-gradient(to right, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
        }

        .input-group-custom {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group-custom input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px 20px;
            color: #fff;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group-custom input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(167, 139, 250, 0.5);
            box-shadow: 0 0 0 4px rgba(167, 139, 250, 0.1);
        }

        .input-group-custom input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899);
            background-size: 200% 200%;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            animation: gradientShift 5s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(168, 85, 247, 0.4);
            color: #fff;
        }

        .btn-submit[data-kt-indicator="on"] .indicator-label {
            display: none;
        }

        .btn-submit[data-kt-indicator="on"] .indicator-progress {
            display: inline-block;
        }

        .btn-submit .indicator-progress {
            display: none;
        }

        .action-links {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }

        .action-links a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: color 0.3s;
        }

        .action-links a:hover {
            color: #fff;
        }

        /* OTP Specific Styles */
        .otp-input-container {
            position: relative;
        }

        .otp-input {
            text-align: center;
            letter-spacing: 20px;
            font-size: 24px !important;
            font-weight: 600;
            padding-left: 40px !important;
            /* Offset for letter spacing to center text */
        }

        .otp-input::placeholder {
            letter-spacing: 20px;
        }
    </style>
</head>

<body id="kt_body" class="app-blank">
    <div class="bg-mesh"></div>
    <div class="d-flex flex-column flex-root align-items-center justify-content-center w-100 min-vh-100" id="kt_app_root">
        <div class="login-card">
            <form class="form w-100 formSubmit" novalidate="novalidate" id="kt_otp_form"
                action="{{ route('artist.otp.verify') }}" method="POST">
                @csrf

                <div class="brand-header">
                    <h1 class="brand-title">WEMU</h1>
                    <p class="brand-subtitle">Verification Required</p>
                    <p style="color: rgba(255,255,255,0.6); font-size: 14px; margin-top: 15px;">
                        Enter the 6-digit OTP sent to your mobile number to continue.
                    </p>
                </div>

                <div class="input-group-custom otp-input-container">
                    <input type="text" placeholder="------" name="otp" id="otp" autocomplete="off" maxlength="6" class="otp-input" />
                </div>

                <button type="submit" id="kt_otp_submit" class="btn-submit mt-4">
                    <span class="indicator-label">Verify Account</span>
                    <span class="indicator-progress">
                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>

                <div class="action-links d-flex justify-content-center mt-4">
                    <a href="{{ route('artist.login') }}">
                        <i class="fas fa-arrow-left me-2"></i> Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div id="preloader" style="background-color: #0a0a0a;">
        <!-- <img src="{{ asset('assets/media/logos/loading.gif') }}" alt="" style="width:10%"> -->
    </div>

    <script>
        var baseUrl = "{{ url('/') }}";
        var APP_URL = "{{ json_encode(url('/')) }}";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/toastr.js') }}"></script>
    <script src="{{ asset('assets/js/operations.js') }}"></script>
    <script>
        $(window).on('load', function() {
            $("#preloader").fadeOut(500);
        });
    </script>
</body>

</html>