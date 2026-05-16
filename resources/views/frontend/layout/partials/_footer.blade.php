<script src="{{ asset('assets/frontend') }}/js/jquery-3.7.1.min.js"></script>

<script src="{{ asset('assets/frontend') }}/js/bootstrap.min.js"></script>
<script src="{{ asset('assets/frontend') }}/js/stellarnav.min.js"></script>
<!-- <script src="{{ asset('assets/frontend') }}/js/aos.js"></script> -->
<script src="{{ asset('assets/frontend') }}/js/custom.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
    $('.owl-carousel').each(function() {
        $(this).owlCarousel({
            loop: true,
            margin: 10,
            nav: false,
            autoplay: true,
            autoplayTimeout: 3000,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        })
    })
</script>

@stack('script')
