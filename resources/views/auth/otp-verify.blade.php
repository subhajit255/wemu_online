<!DOCTYPE html>
<html lang="en">

<head>
    <title>WEMU - Verify Account</title>
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
        .w-md-600px { width: 100% !important; }

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
            background: rgba(255,255,255,0.022);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 22px;
            padding: 40px 30px;
            width: 100%;
            max-width: 480px;
            margin: 20px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.55), inset 0 1px 0 rgba(255,255,255,0.08);
            position: relative;
            z-index: 10;
            text-align: center;
        }

        .step-heading {
            font-size: 26px;
            font-weight: 800;
            margin: 0 0 10px;
            letter-spacing: 1px;
            color: #fff;
        }

        .step-sub {
            font-size: 14px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 24px;
            line-height: 1.5;
        }

        /* OTP INPUTS */
        .otp-wrapper {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 24px auto 10px;
            width: 100%;
            max-width: 380px;
        }

        .otp-digit {
            flex: 1;
            min-width: 0;
            height: 58px;
            background: rgba(255,255,255,0.07);
            border: 1.5px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            font-family: inherit;
            outline: none;
            transition: all 0.25s ease;
            caret-color: #a855f7;
        }

        .otp-digit:focus {
            border-color: #a855f7;
            background: rgba(168,85,247,0.12);
            box-shadow: 0 0 0 3px rgba(168,85,247,0.22), 0 0 16px rgba(168,85,247,0.25);
        }

        .otp-digit.has-val {
            border-color: #6366f1;
            background: rgba(99,102,241,0.1);
            color: #fff;
        }

        .timer-row {
            text-align: center;
            font-size: 13px;
            color: rgba(255,255,255,0.45);
            margin-top: 14px;
        }

        #resendBtn {
            color: #a5b4fc;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            margin-left: 5px;
            transition: color 0.2s;
        }
        #resendBtn:hover { color: #c4b5fd; }
        #resendBtn.disabled {
            color: rgba(255,255,255,0.28);
            pointer-events: none;
            cursor: not-allowed;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            background-size: 200% 200%;
            color: #fff;
            border: none;
            border-radius: 13px;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 24px;
            animation: gradientShift 5s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(168,85,247,0.5);
            color: #fff;
        }

        .btn-submit[data-kt-indicator="on"] .indicator-label { display: none; }
        .btn-submit[data-kt-indicator="on"] .indicator-progress { display: inline-block; }
        .btn-submit .indicator-progress { display: none; }

        .action-links {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }
        .action-links a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.3s;
        }
        .action-links a:hover { color: #fff; }

        .shield-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(168,85,247,0.12);
            border: 2px solid rgba(168,85,247,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .shield-icon i {
            font-size: 26px;
            color: #a855f7;
        }
    </style>
</head>

<body id="kt_body" class="app-blank">
    <div class="bg-mesh"></div>
    <div class="d-flex flex-column flex-root align-items-center justify-content-center w-100 min-vh-100" id="kt_app_root">
        <div class="login-card">
            <form class="form w-100 formSubmit" novalidate="novalidate" id="kt_otp_form" action="{{ route('artist.otp.verify') }}" method="POST">
                @csrf

                <div class="shield-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2 class="step-heading">Verify your account</h2>
                <p class="step-sub">We've sent a 6-digit code to your mobile/email. Enter it below to continue.</p>

                <div class="otp-wrapper" id="otpBoxes">
                    <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                    <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                    <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                    <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                    <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                    <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                </div>
                <input type="hidden" name="otp" id="otp">

                <div class="timer-row">
                    <span id="timerText">Resend code in <strong id="timerCount">00:30</strong></span>
                    <a id="resendBtn" class="disabled" onclick="resendOtp()">Resend</a>
                </div>

                <button type="submit" id="kt_otp_submit" class="btn-submit">
                    <span class="indicator-label">Verify Account</span>
                    <span class="indicator-progress">
                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>

                <div class="action-links d-flex justify-content-center">
                    <a href="{{ route('artist.login') }}">
                        <i class="fas fa-arrow-left me-2"></i> Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div id="preloader" style="background-color: #0a0a0a;">
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
            startTimer();
        });

        /* ---- OTP input handling ---- */
        var otpTimer = null;

        $(document).on('keyup', '.otp-digit', function(e) {
            var $this = $(this);
            var key   = e.which || e.keyCode;
            var val   = $this.val().replace(/\D/g, '');
            $this.val(val);

            if (val) {
                $this.addClass('has-val');
                $this.nextAll('.otp-digit').first().focus();
            } else {
                $this.removeClass('has-val');
            }
            if (key === 8 && !val) {
                $this.prevAll('.otp-digit').first().focus();
            }

            // Concatenate all into hidden input
            var otp = '';
            $('.otp-digit').each(function() { otp += $(this).val(); });
            $('#otp').val(otp);
        });

        $(document).on('paste', '.otp-digit', function(e) {
            e.preventDefault();
            var pasted = (e.originalEvent.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
            var boxes  = $('.otp-digit');
            for (var i = 0; i < pasted.length && i < 6; i++) {
                boxes.eq(i).val(pasted[i]).addClass('has-val');
            }
            boxes.eq(Math.min(pasted.length, 5)).focus();
            var otp = '';
            boxes.each(function() { otp += $(this).val(); });
            $('#otp').val(otp);
        });

        /* ---- OTP timer ---- */
        function startTimer() {
            clearInterval(otpTimer);
            var secs = 30;
            $('#timerText').show();
            $('#resendBtn').addClass('disabled');
            tick();
            otpTimer = setInterval(function() {
                secs--;
                tick();
                if (secs <= 0) {
                    clearInterval(otpTimer);
                    $('#timerText').hide();
                    $('#resendBtn').removeClass('disabled');
                }
            }, 1000);

            function tick() {
                var m = Math.floor(secs / 60), s = secs % 60;
                $('#timerCount').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
            }
        }

        function resendOtp() {
            if ($('#resendBtn').hasClass('disabled')) return;
            
            $.ajax({
                type: 'POST',
                url: '{{ route("artist.otp.verify") }}',
                data: { action: 'resend', _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(r) {
                    if (r.status) {
                        toastr.success(r.message || 'A new verification code has been sent!');
                        if (r.otp) {
                            console.log('OTP:', r.otp);
                            setTimeout(function() { alert('OTP: ' + r.otp); }, 500);
                        }
                        startTimer();
                    } else {
                        toastr.error(r.message || 'Failed to resend code.');
                    }
                },
                error: function() { toastr.error('Failed to resend code.'); }
            });
        }
    </script>
    @if(isset($verificationCode))
    <script>
        console.log("Verification Code:", "{{ $verificationCode }}");
        $(window).on('load', function() {
            setTimeout(function() {
                alert("Verification Code: {{ $verificationCode }}");
            }, 600);
        });
    </script>
    @endif
</body>

</html>