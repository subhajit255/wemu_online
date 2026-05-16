<!DOCTYPE html>
<html lang="en">

<head>
    <title>WEMU | Delete Account</title>
    <meta charset="utf-8" />
    <link rel="canonical" href="{{ asset('assets/logo/custom-2.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cdn/toastr.css') }}" />
    <script src="{{ asset('assets/js/custom_js/cdn/jquery.min.js') }}"></script>
    <style>
        .w-md-600px {
            width: 100% !important;
        }
    </style>
</head>

<body id="kt_body" class="app-blank bgi-size-cover bgi-position-center">
    <script>
        document.documentElement.setAttribute("data-bs-theme", 'light');
    </script>
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <style>
            body {
                background-image: url({{ asset('assets/media/auth/bg10.jpeg') }});
            }
        </style>
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="d-flex flex-lg-row-fluid login_left">
                <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                    <img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20"
                        src="{{ asset('assets/media/auth/login_banner.png') }}" alt="" />
                    <div class="login-leftcon">
                        <h1 class="fs-2qx fw-bold text-center mb-7">Welcome to WEMU</h1>
                        <div class="fs-base text-center fw-semibold">Don't miss the opportunity set reminders for service
                            warranty and expenses all in one</div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
                <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10">
                    <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
                        <div class="d-flex flex-center flex-column-fluid pb-15 pb-lg-20">
                            <form class="form w-100 formSubmit" novalidate="novalidate" id="kt_sign_in_form"
                                action="{{ route('admin.delete.account') }}" method="POST">
                                @csrf
                                <div class="login-right">
                                    <div class="text-center pt-4 mb-11">
                                        <img alt="Logo" src="{{ asset('assets/media/logos/logo.png') }}"
                                            class="h-40px app-sidebar-logo-default" />
                                    </div>
                                    <div class="text-center mb-11">
                                        <h1 class="text-dark fw-bolder mb-3">Delete Account</h1>
                                    </div>
                                    <div class="fv-row mb-8">
                                        <input type="text" placeholder="Mobile Number" name="mobile_number" id="mobile_number"
                                            autocomplete="off" class="form-control bg-transparent number-only" maxlength="10"/>
                                    </div>
                                    <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold ">
                                        <div></div>
                                    </div>
                                    <div class="d-grid mb-10">
                                        <button type="submit" id="kt_sign_in_submit" class="btn btn-dark">
                                            <span class="indicator-label">Delete</span>
                                            <span class="indicator-progress">Please wait...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="preloader">
        <span class="ripple-effect"></span>
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
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <script src="{{ asset('assets/js/operations.js') }}"></script>
    <script>
        $(window).on('load', function() {
            $("#preloader").fadeOut(0);
        });
    </script>
</body>

</html>
