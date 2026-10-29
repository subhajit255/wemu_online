<!DOCTYPE html>
<html lang="en">

<head>
    <title>WEMU - {{ Route::is('admin.*') ? 'Admin Portal' : 'Artist Portal' }} - Set New Password</title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
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

        body#kt_body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #060608;
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 12% 20%, rgba(76,29,149,0.42) 0%, transparent 45%),
                radial-gradient(circle at 88% 80%, rgba(225,29,72,0.36) 0%, transparent 45%),
                radial-gradient(circle at 55% 50%, rgba(37,99,235,0.28) 0%, transparent 50%);
            background-color: #060608;
            animation: bgBreathe 16s ease-in-out infinite alternate;
        }

        @keyframes bgBreathe {
            0%   { opacity: 0.85; transform: scale(1); }
            100% { opacity: 1;    transform: scale(1.07); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px 30px;
            width: 100%;
            max-width: 460px;
            margin: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 10;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .brand-title {
            font-size: 38px;
            font-weight: 800;
            margin: 0 0 8px;
            letter-spacing: 4px;
            background: linear-gradient(to right, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
        }

        .shield-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(168,85,247,0.12);
            border: 2px solid rgba(168,85,247,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .shield-icon i {
            font-size: 24px;
            color: #a855f7;
        }

        .step-heading {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #fff;
            text-align: center;
        }

        .step-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
            margin-bottom: 24px;
            line-height: 1.5;
            text-align: center;
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
            padding: 14px 45px 14px 18px;
            color: #fff;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group-custom input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #6366f1;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
        }

        .input-group-custom input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .toggle-pwd {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-pwd:hover {
            color: #fff;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            background-size: 200% 200%;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            animation: gradientShift 5s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(168, 85, 247, 0.4);
            color: #fff;
        }

        .btn-submit[data-kt-indicator="on"] .indicator-label { display: none; }
        .btn-submit[data-kt-indicator="on"] .indicator-progress { display: inline-block; }
        .btn-submit .indicator-progress { display: none; }

        .action-links {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
        }

        .action-links a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.3s;
        }

        .action-links a:hover {
            color: #fff;
        }
    </style>
</head>

<body id="kt_body" class="app-blank">
    <div class="bg-mesh"></div>
    <div class="d-flex flex-column flex-root align-items-center justify-content-center w-100 min-vh-100" id="kt_app_root">
        <div class="login-card">
            <form class="form w-100 formSubmit" novalidate="novalidate" id="kt_reset_password_form"
                action="{{ Route::is('admin.*') ? route('admin.reset.password') : route('artist.reset.password') }}" method="POST">
                @csrf

                <div class="brand-header">
                    <h1 class="brand-title">WEMU</h1>
                    <p class="brand-subtitle">{{ Route::is('admin.*') ? 'Admin Portal' : 'Artist Portal' }}</p>
                </div>

                <div class="shield-icon">
                    <i class="fas fa-lock"></i>
                </div>

                <h2 class="step-heading">Set New Password</h2>
                <p class="step-sub">Enter your new password and confirm it below to securely update your account.</p>

                <div class="input-group-custom">
                    <input id="new_password" type="password" placeholder="New Password (min 6 characters)" name="password" autocomplete="new-password" required />
                    <button type="button" class="toggle-pwd" id="togglePassword">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>

                <div class="input-group-custom">
                    <input id="confirm_password" type="password" placeholder="Confirm New Password" name="password_confirmation" autocomplete="new-password" required />
                    <button type="button" class="toggle-pwd" id="toggleConfirmPassword">
                        <i class="fas fa-eye" id="eyeConfirmIcon"></i>
                    </button>
                </div>

                <button type="submit" id="kt_reset_submit" class="btn-submit">
                    <span class="indicator-label">Update Password</span>
                    <span class="indicator-progress">
                        Updating password... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>

                <div class="action-links d-flex justify-content-center">
                    <a href="{{ Route::is('admin.*') ? route('admin.login') : route('artist.login') }}">
                        <i class="fas fa-arrow-left me-2"></i> Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div id="preloader" style="background-color: #0a0a0a;"></div>

    <script>
        var baseUrl = "{{ url('/') }}";
        var APP_URL = "{{ json_encode(url('/')) }}";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <script src="{{ asset('assets/js/toastr.js') }}"></script>
    <script src="{{ asset('assets/js/operations.js') }}"></script>
    <script>
        $(window).on('load', function() {
            $("#preloader").fadeOut(500);
        });

        // Toggle Password Visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const pwdInput = document.getElementById('new_password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });

        // Toggle Confirm Password Visibility
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const pwdInput = document.getElementById('confirm_password');
            const eyeIcon = document.getElementById('eyeConfirmIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>

