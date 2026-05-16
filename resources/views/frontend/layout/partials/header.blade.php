<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

<header class="header">
    <div class="container-fluid">
        <div class="header-wrap">
            <div class="logo">
                <a href="{{ route('landing.page') }}">
                    <img src="{{ asset('assets/frontend') }}/images/logo.png" alt="">
                </a>
            </div>
            <div class="rt-side">
                <div class="navigation">
                    <div class="stellarnav">
                        <ul>
                            <li><a href="{{ route('landing.page') }}">Home</a></li>
                            <li><a href="#feature-sec">Features</a></li>
                            <li><a href="#howItWork-sec">How it works</a></li>
                            <li><a href="#goals-sec">Achieve Goal</a></li>
                            <li><a href="#faq-sec">FAQ</a></li>
                            <li><a target="_blank" href="{{ route('coming.soon') }}">Download App</a></li>
                        </ul>
                    </div>
                </div>
                <div class="hd-right-side">
                    <a href="javascript:void(0);" class="book-btn" data-bs-toggle="modal"
                        data-bs-target="#exampleModal">Contact</a>
                </div>
            </div>
        </div>
    </div>
</header>
