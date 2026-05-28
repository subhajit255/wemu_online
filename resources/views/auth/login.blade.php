<!DOCTYPE html>
<html lang="en">

<head>
    <title>WEMU - {{ Route::currentRouteName() == 'admin.login' ? 'Admin Portal' : 'Artist Login' }}</title>
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

        /* Artistic Login Styles */
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

        /* Abstract glowing background */
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

        .input-group-custom .toggle-pwd {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: color 0.3s;
            padding: 5px;
        }

        .input-group-custom .toggle-pwd:hover {
            color: #fff;
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

        /* Modal Styling */
        .modal-content {
            background: #18181b;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .modal-content .text-dark,
        .modal-content h1 {
            color: #fff !important;
        }

        .modal-content .text-muted {
            color: #a1a5b7 !important;
        }

        .modal-content .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 12px;
        }

        .modal-content .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .modal-content .btn-dark {
            background: #6366f1;
            color: #fff;
            border: none;
            border-radius: 10px;
        }

        .modal-content .btn-dark:hover {
            background: #4f46e5;
        }

        .modal-content .btn-light {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 10px;
        }

        .modal-content .btn-light:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .modal-header .btn-icon {
            color: rgba(255, 255, 255, 0.5);
        }

        .modal-header .btn-icon:hover {
            color: #fff;
        }
    </style>
</head>

<body id="kt_body" class="app-blank">
    <div class="bg-mesh"></div>
    <div class="d-flex flex-column flex-root align-items-center justify-content-center w-100 h-100" id="kt_app_root">
        <div class="login-card">
            <form class="form w-100 formSubmit" novalidate="novalidate" id="kt_sign_in_form"
                action="{{ Route::currentRouteName() == 'admin.login' ? route('admin.login') : route('artist.login') }}" method="POST">
                @csrf

                <div class="brand-header">
                    <h1 class="brand-title">WEMU</h1>
                    <p class="brand-subtitle">{{ Route::currentRouteName() == 'admin.login' ? 'Admin Portal' : 'Artist Portal' }}</p>
                </div>

                <div class="input-group-custom">
                    <input type="text" placeholder="Email Address" name="email" id="email" autocomplete="off" />
                </div>

                <div class="input-group-custom">
                    <input id="password" type="password" placeholder="Password" name="password" autocomplete="off" />
                    <button type="button" class="toggle-pwd" id="togglePassword">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>

                <button type="submit" id="kt_sign_in_submit" class="btn-submit">
                    <span class="indicator-label">Sign In</span>
                    <span class="indicator-progress">
                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>

                <div class="action-links d-flex flex-column gap-3 mt-4">
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#forgot_password_form">
                        Forgot Password?
                    </a>
                    @if(Route::currentRouteName() !== 'admin.login')
                    <span style="color: rgba(255,255,255,0.4);">
                        Don't have an account?
                        <a href="{{ route('artist.register') }}?reset=1" style="color: #a5b4fc; font-weight: 600;">
                            Register now
                        </a>
                    </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div id="preloader" style="background-color: #0a0a0a;">
        <!-- <img src="{{ asset('assets/media/logos/loading.gif') }}" alt="" style="width:10%"> -->
    </div>

    {{-- register modal start --}}
    <div class="modal fade" id="register_form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <form id="artist_register_form" class="form formSubmit"
                        action="{{ route('artist.register') }}" method="POST">
                        @csrf
                        <div class="mb-13 text-center">
                            <h1 class="mb-3">Create an Account</h1>
                            <div class="text-muted fw-semibold fs-5">Fill in the details to register
                            </div>
                        </div>

                        <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Name</span>
                            </label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Enter Name" name="name" id="reg_name" />
                        </div>

                        <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Email</span>
                            </label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Enter Email Address" name="email" id="reg_email" />
                        </div>

                        <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Mobile</span>
                            </label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Enter Mobile Number" name="mobile" id="reg_mobile" />
                        </div>

                        <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Password</span>
                            </label>
                            <input type="password" class="form-control form-control-solid"
                                placeholder="Enter Password" name="password" id="reg_password" />
                        </div>

                        <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Confirm Password</span>
                            </label>
                            <input type="password" class="form-control form-control-solid"
                                placeholder="Confirm Password" name="password_confirmation" id="reg_password_confirmation" />
                        </div>

                        <div class="text-center pt-4">
                            <button type="reset" class="btn btn-light me-3"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-dark">
                                <span class="indicator-label">Register</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- register modal end --}}

    {{-- forgot modal start --}}
    <div class="modal fade" id="forgot_password_form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <form id="admin_forgot_password_form" class="form formSubmit"
                        action="{{ route('admin.forgot.password') }}" enctype='multipart/form-data'>
                        @csrf
                        <div class="mb-13 text-center">
                            <h1 class="mb-3">Forgot Password</h1>
                            <div class="text-muted fw-semibold fs-5">Enter your email address Here
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Email</span>
                            </label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Enter Email Address" name="email" id="email" />
                        </div>

                        <div class="text-center pt-4">
                            <button type="reset" id="admin_password_form_cancel" class="btn btn-light me-3"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="admin_password_form_submit" class="btn btn-dark">
                                <span class="indicator-label">Send</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reset_password_form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <form id="admin_reset_password_form" class="form formSubmit"
                        action="{{ route('admin.reset.password') }}" enctype='multipart/form-data'>
                        @csrf
                        <input type="hidden" name="reset_token" id="reset_token">
                        <div class="mb-13 text-center">
                            <h1 class="mb-3">Reset Your Password</h1>
                            <div class="text-muted fw-semibold fs-5">Enter your new password
                                <a href="javascript:void(0)" class="fw-bold link-primary">Here</a>.
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">New Password</span>
                            </label>
                            <input type="password" class="form-control form-control-solid"
                                placeholder="Enter New Password" name="new_password" id="new_password" />
                        </div>

                        <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Confirm Password</span>
                            </label>
                            <input type="password" class="form-control form-control-solid"
                                placeholder="Enter Confirm Password" name="password_confirmation"
                                id="password_confirmation" />
                        </div>

                        <div class="text-center pt-4">
                            <button type="reset" id="admin_reset_password_form_cancel" class="btn btn-light me-3"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="admin_reset_password_form_submit" class="btn btn-dark">
                                <span class="indicator-label">Reset</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- forgot modal end --}}

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
        $(document).ready(function() {
            const linkExpire = "{{ $linkExpire ?? '' }}";
            const token = "{{ $token ?? '' }}";
            $('#reset_token').val(token);
            if (linkExpire == true) {
                swal.fire({
                    text: "Awww !!! Link Expired, Please try again",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, Got it!",
                    customClass: {
                        confirmButton: "btn btn-dark"
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(location).attr('href', "{{ route('artist.login') }}");
                    }
                })
            } else {
                if (token != '') {
                    $('#reset_password_form').modal('show');
                }
            }
        });

        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fas', 'fa-eye');
                eyeIcon.classList.add('fas', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fas', 'fa-eye-slash');
                eyeIcon.classList.add('fas', 'fa-eye');
            }
        });

        $(window).on('load', function() {
            $("#preloader").fadeOut(500);
        });
    </script>
</body>

</html>