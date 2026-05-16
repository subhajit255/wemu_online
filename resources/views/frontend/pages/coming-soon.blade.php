<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets/frontend') }}/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets/frontend') }}/css/style.css">
<link rel="stylesheet" href="{{ asset('assets/frontend') }}/css/responsive.css">
<link rel="stylesheet" href="{{ asset('assets/css/cdn/toastr.css') }}" />
<link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.png') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css"
    integrity="sha512-gOQQLjHRpD3/SEOtalVq50iDn4opLVup2TF8c4QPI3/NmUPNZOk2FG0ihi8oCU/qYEsw4P6nuEZT2lAG0UNYaw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<title>Coming Soon | WEMU</title>

<body class="commingSoonBody">
    <div class="commingSoon">
        <div class="wrap">
            <div class="logo">
                <img src="{{ asset('assets/frontend') }}/images/logo.png" alt="">
            </div>
            <div class="countwrap">
                <div class="ttl">
                    <h2>Launch Countdown</h2>
                </div>
                <div class="countarea">
                    <div class="timebox">
                        <div class="timeboxwrap">
                            <h3 id="days">--</h3>
                            <span>Days</span>
                        </div>
                    </div>
                    <div class="timebox">
                        <div class="timeboxwrap">
                            <h3 id="hours">--</h3>
                            <span>Hours</span>
                        </div>
                    </div>
                    <div class="timebox">
                        <div class="timeboxwrap">
                            <h3 id="minutes">--</h3>
                            <span>Minutes</span>
                        </div>
                    </div>
                    <div class="timebox">
                        <div class="timeboxwrap">
                            <h3 id="seconds">--</h3>
                            <span>Seconds</span>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('notify.me') }}" class="formSubmit" id="notifyForm" method="POST">
                @csrf
                <div class="notify" style="padding-top: 30px;">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email address"
                            aria-label="Enter Your Email address" aria-describedby="basic-addon2" name="email"
                            id="email">
                        <button class="btn btn-primary" type="submit" id="notifyBtn">Notify Me</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <div class="duckImg">
        <img src="{{ asset('assets/frontend') }}/images/bg2.png" alt="">
    </div>
    <div class="bgtop">
        <img src="{{ asset('assets/frontend') }}/images/bg-top.jpg" alt="">
    </div>
    <div class="bg-bottom">
        <img src="{{ asset('assets/frontend') }}/images/bg-bottom.png" alt="">
    </div>
    <footer class="cmfooter">
        <div class="stay">
            <span><i class="fa-solid fa-angle-right"></i></span>
            <p>Stay Tuned</p>
        </div>
        <a href="WWW.KEEPYOURDUCKSINAROW.COM"> <i class="fa-solid fa-globe"></i> WWW.KEEPYOURDUCKSINAROW.COM</a>
    </footer>
</body>

<script src="{{ asset('assets/frontend') }}/js/jquery-3.7.1.min.js"></script>
<script src="{{ asset('assets/frontend') }}/js/bootstrap.min.js"></script>
<script src="{{ asset('assets/frontend') }}/js/stellarnav.min.js"></script>
<script src="{{ asset('assets/frontend') }}/js/custom.js"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script src="{{ asset('assets/js/operations.js') }}?v=1.1"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const Swal = window.Swal;
</script>
<script>
    const countDownDate = new Date("{{ $settings->launching_date }}").getTime();

    const countdownFunction = setInterval(function() {
        const now = new Date().getTime();
        const distance = countDownDate - now;

        const daysElement = document.getElementById("days");
        const hoursElement = document.getElementById("hours");
        const minutesElement = document.getElementById("minutes");
        const secondsElement = document.getElementById("seconds");

        if (distance > 0) {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            if (daysElement) daysElement.innerHTML = String(days).padStart(2, '0');
            if (hoursElement) hoursElement.innerHTML = String(hours).padStart(2, '0');
            if (minutesElement) minutesElement.innerHTML = String(minutes).padStart(2, '0');
            if (secondsElement) secondsElement.innerHTML = String(seconds).padStart(2, '0');
        } else {
            clearInterval(countdownFunction);

            // Show 00:00:00:00 on expiry
            if (daysElement) daysElement.innerHTML = "00";
            if (hoursElement) hoursElement.innerHTML = "00";
            if (minutesElement) minutesElement.innerHTML = "00";
            if (secondsElement) secondsElement.innerHTML = "00";

            // Optional: Alert or show a message
            const wrapper = document.querySelector(".countwrap");
            if (wrapper) {
                wrapper.innerHTML = `<div class="ttl">
                                        <h2>We Have Launched!</h2>
                                    </div>`;
            }
        }
    }, 1000);
</script>
