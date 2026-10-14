<!DOCTYPE html>
<html lang="en">

<head>
    <title>WEMU | Delete User Account</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="canonical" href="{{ asset('assets/logo/custom-2.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet" />

    <!-- FontAwesome & Plugins -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/css/cdn/toastr.css') }}" />

    <script src="{{ asset('assets/js/custom_js/cdn/jquery.min.js') }}"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body#kt_body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #060608;
            color: #F8FAFC;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
        }

        /* Ambient glowing background mesh */
        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 15% 18%, rgba(225, 29, 72, 0.28) 0%, transparent 45%),
                radial-gradient(circle at 85% 25%, rgba(99, 102, 241, 0.32) 0%, transparent 50%),
                radial-gradient(circle at 50% 80%, rgba(168, 85, 247, 0.24) 0%, transparent 55%);
            background-color: #060608;
            animation: bgBreathe 18s ease-in-out infinite alternate;
        }

        @keyframes bgBreathe {
            0% {
                opacity: 0.85;
                transform: scale(1);
            }
            100% {
                opacity: 1;
                transform: scale(1.05);
            }
        }

        /* Ambient grid pattern overlay */
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 32px 32px;
            z-index: 0;
            pointer-events: none;
            opacity: 0.35;
        }

        /* Glassmorphic Central Card */
        .delete-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 44px 36px;
            width: 100%;
            max-width: 520px;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255, 255, 255, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.12);
            position: relative;
            z-index: 10;
            transition: all 0.3s ease;
        }

        /* Header & Branding */
        .brand-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.25);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .brand-logo-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(225, 29, 72, 0.25), transparent);
            pointer-events: none;
        }

        .brand-logo-wrap img {
            width: 44px;
            height: auto;
            position: relative;
            z-index: 2;
        }

        .badge-portal {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 9999px;
            background: rgba(225, 29, 72, 0.12);
            border: 1px solid rgba(225, 29, 72, 0.3);
            color: #fda4af;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .brand-title {
            font-family: 'Outfit', sans-serif;
            font-size: 30px;
            font-weight: 700;
            margin: 0 0 10px;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #FFFFFF 30%, #E2E8F0 60%, #FDA4AF 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-subtitle {
            font-size: 14px;
            color: #94A3B8;
            line-height: 1.55;
            margin: 0 auto;
            max-width: 380px;
        }

        /* Form Inputs */
        .form-label-custom {
            font-size: 13px;
            font-weight: 600;
            color: #CBD5E1;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 22px;
        }

        .input-icon-wrap {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .input-group-custom input {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 16px 20px 16px 48px;
            color: #F8FAFC;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group-custom input:focus {
            background: rgba(15, 23, 42, 0.85);
            border-color: #E11D48;
            box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.18);
        }

        .input-group-custom input:focus + .input-icon-wrap {
            color: #FDA4AF;
        }

        .input-group-custom input::placeholder {
            color: #64748B;
            font-size: 14px;
        }

        /* Action Button */
        .btn-delete-submit {
            width: 100%;
            background: linear-gradient(135deg, #E11D48 0%, #BE123C 50%, #9F1239 100%);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            padding: 16px 24px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.4);
        }

        .btn-delete-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(225, 29, 72, 0.55);
            border-color: rgba(255, 255, 255, 0.3);
            color: #FFFFFF;
        }

        .btn-delete-submit:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-delete-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-delete-submit .spinner-border {
            width: 1.1rem;
            height: 1.1rem;
            border-width: 2px;
            display: none;
        }

        .btn-delete-submit.loading .spinner-border {
            display: inline-block;
        }

        .btn-delete-submit.loading .btn-text-content {
            display: none;
        }

        /* Dynamic Result Card Container */
        .result-panel {
            margin-top: 24px;
            border-radius: 16px;
            padding: 20px;
            display: none;
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
            overflow: hidden;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Status Variant: User Just Deleted (Success) */
        .result-panel.variant-success {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.3);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15);
        }

        .result-panel.variant-success .result-icon {
            color: #34D399;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .result-panel.variant-success .result-title {
            color: #6EE7B7;
        }

        /* Status Variant: User Already Deleted (Warning / Info) */
        .result-panel.variant-warning {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.3);
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.15);
        }

        .result-panel.variant-warning .result-icon {
            color: #FBBF24;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .result-panel.variant-warning .result-title {
            color: #FCD34D;
        }

        /* Status Variant: User Not Found / Error (Danger) */
        .result-panel.variant-danger {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.3);
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.15);
        }

        .result-panel.variant-danger .result-icon {
            color: #F87171;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .result-panel.variant-danger .result-title {
            color: #FCA5A5;
        }

        .result-content-wrap {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .result-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .result-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .result-desc {
            font-size: 13px;
            color: #94A3B8;
            margin: 0 0 10px;
            line-height: 1.45;
        }

        .result-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 6px;
        }

        .result-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            color: #E2E8F0;
        }

        .result-pill.badge-status {
            font-weight: 600;
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.35);
            color: #FDA4AF;
        }

        /* Footer Links */
        .card-footer-links {
            margin-top: 30px;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .card-footer-links a {
            color: #94A3B8;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .card-footer-links a:hover {
            color: #FFFFFF;
        }

        .info-notice {
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 22px;
            display: flex;
            gap: 10px;
            font-size: 12px;
            color: #94A3B8;
            line-height: 1.45;
        }

        .info-notice i {
            color: #818CF8;
            margin-top: 2px;
        }
    </style>
</head>

<body id="kt_body">
    <!-- Ambient Background FX -->
    <div class="bg-mesh"></div>
    <div class="bg-grid"></div>

    <div class="delete-card">
        <!-- Brand Header -->
        <div class="brand-header">
            <div class="brand-logo-wrap">
                <img alt="WEMU Logo" src="{{ asset('assets/media/logos/logo.png') }}" />
            </div>
            <div>
                <span class="badge-portal">
                    <i class="fa-solid fa-user-shield fs-8"></i> Account Administration
                </span>
            </div>
            <h1 class="brand-title">Delete User</h1>
            <p class="brand-subtitle">
                Enter the user's registered email address to check existence and deactivate the account.
            </p>
        </div>

        <!-- Guidance Note -->
        <div class="info-notice">
            <i class="fa-solid fa-circle-info"></i>
            <div>
                Active accounts will have their status updated to inactive (<strong>is_active = 0</strong>). Already deleted accounts will be verified and reported.
            </div>
        </div>

        <!-- Submission Form -->
        <form id="deleteUserForm" method="POST" action="{{ route('admin.delete.account') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label-custom">
                    <i class="fa-regular fa-envelope text-muted"></i> User Email Address
                </label>
                <div class="input-group-custom">
                    <input type="email" name="email" id="email" required autocomplete="off"
                        placeholder="e.g. user@example.com" value="{{ old('email') }}" />
                    <span class="input-icon-wrap">
                        <i class="fa-solid fa-at"></i>
                    </span>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="btnSubmitDelete" class="btn-delete-submit">
                <span class="btn-text-content">
                    <i class="fa-solid fa-user-xmark me-1"></i> Check & Delete Account
                </span>
                <span class="spinner-border text-light" role="status" aria-hidden="true"></span>
            </button>
        </form>

        <!-- Dynamic Result Card -->
        <div id="resultPanel" class="result-panel">
            <div class="result-content-wrap">
                <div class="result-icon" id="resultIcon">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="flex-grow-1">
                    <h3 class="result-title" id="resultTitle">User deleted</h3>
                </div>
            </div>
        </div>

        <!-- Server Flash Fallbacks (for non-AJAX or standard redirects) -->
        @if (session('success_msg'))
            <div class="result-panel variant-success d-block mt-4">
                <div class="result-content-wrap">
                    <div class="result-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <h3 class="result-title">User deleted</h3>
                    </div>
                </div>
            </div>
        @elseif (session('warning_msg'))
            <div class="result-panel variant-warning d-block mt-4">
                <div class="result-content-wrap">
                    <div class="result-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <h3 class="result-title">Already deleted</h3>
                    </div>
                </div>
            </div>
        @elseif (session('error_msg'))
            <div class="result-panel variant-danger d-block mt-4">
                <div class="result-content-wrap">
                    <div class="result-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                    <div>
                        <h3 class="result-title">{{ session('error_msg') }}</h3>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Footer Links -->
        
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/toastr.js') }}"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            // Configure toastr
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "4000"
                };
            }

            $('#deleteUserForm').on('submit', function(e) {
                e.preventDefault();

                var $form = $(this);
                var $btn = $('#btnSubmitDelete');
                var $panel = $('#resultPanel');
                var $icon = $('#resultIcon');
                var $title = $('#resultTitle');
                var emailVal = $.trim($('#email').val());

                if (!emailVal) {
                    if (typeof toastr !== 'undefined') toastr.warning('Please enter an email address.');
                    return;
                }

                $btn.addClass('loading').prop('disabled', true);
                $panel.hide().removeClass('variant-success variant-warning variant-danger');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        email: emailVal
                    },
                    dataType: 'json',
                    success: function(response) {
                        $btn.removeClass('loading').prop('disabled', false);

                        if (response.type === 'just_deleted' || (response.status && response.is_deleted)) {
                            // User was active -> now deleted
                            $panel.addClass('variant-success').fadeIn();
                            $icon.html('<i class="fa-solid fa-circle-check"></i>');
                            $title.text('User deleted');
                            if (typeof toastr !== 'undefined') toastr.success('User deleted');

                        } else if (response.type === 'already_deleted' || response.is_deleted || response.message === 'Already deleted') {
                            // User already deleted
                            $panel.addClass('variant-warning').fadeIn();
                            $icon.html('<i class="fa-solid fa-triangle-exclamation"></i>');
                            $title.text('Already deleted');
                            if (typeof toastr !== 'undefined') toastr.warning('Already deleted');

                        } else {
                            // User not found or other message
                            $panel.addClass('variant-danger').fadeIn();
                            $icon.html('<i class="fa-solid fa-circle-xmark"></i>');
                            var msg = response.message || 'User not found';
                            $title.text(msg);
                            if (typeof toastr !== 'undefined') toastr.error(msg);
                        }
                    },
                    error: function(xhr) {
                        $btn.removeClass('loading').prop('disabled', false);
                        var errMsg = 'User not found';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }

                        $panel.addClass('variant-danger').fadeIn();
                        $icon.html('<i class="fa-solid fa-triangle-exclamation"></i>');
                        $title.text(errMsg);

                        if (typeof toastr !== 'undefined') toastr.error(errMsg);
                    }
                });
            });
        });
    </script>
</body>

</html>

