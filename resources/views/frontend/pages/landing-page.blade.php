@extends('frontend.layout.app')
<style>
    .finances-sec .owl-dots {
        justify-content: center;
        padding-bottom: 20px;
    }

    #preloader {
        position: fixed;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        z-index: 9999;
        color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>


@section('content')
    <section class="banner">
        <div class="bannerWrap">
            <div class="bannerImg">
                <img src="{{ asset('assets/frontend/images/banner-img.png') }}" alt="">
            </div>
            <div class="bannerText">
                <h6>What is WEMU?</h6>
                <h1>{{ $details['hero_title_one'] ?? '' }}</h1>
                <h4>{{ $details['hero_title_two'] ?? '' }}</h4>
                <p>{{ $details['hero_content'] ?? '' }}</p>
                <a href="{{ $details['app_url'] ?? route('coming.soon') }}" class="sitebtn">Download App <span class="btnArrow"><i
                            class="fa-solid fa-arrow-right-long"></i></span></a>
            </div>
        </div>
    </section>

    <section class="feature-sec" id="feature-sec">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="featureInfo">
                        <h6>Our Features</h6>
                        <h2>{{ $details['feature_title'] ?? '' }}</h2>
                        <p>{{ $details['feature_description'] ?? '' }}</p>
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">Contact</a>
                    </div>
                </div>
                <div class="col-lg-7 col-md-12 col-sm-12">
                    <div class="row gy-4">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="featureListBox" id="equalfeatureBox">
                                <div class="ttl">
                                    <div class="icon">
                                        <img src="{{ asset('assets/frontend/images/features-image-1.png') }}"
                                            alt="">
                                    </div>
                                    <div class="text">
                                        <h3>{{ $details['feature_sub_title_one'] ?? '' }}</h3>
                                    </div>
                                </div>
                                <div class="para">
                                    <p>{{ $details['feature_sub_desc_one'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="featureListBox">
                                <div class="ttl">
                                    <div class="icon">
                                        <img src="{{ asset('assets/frontend/images/features-image-2.png') }}"
                                            alt="">
                                    </div>
                                    <div class="text">
                                        <h3>{{ $details['feature_sub_title_two'] ?? '' }}</h3>
                                    </div>
                                </div>
                                <div class="para">
                                    <p>{{ $details['feature_sub_desc_two'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="featureListBox">
                                <div class="ttl">
                                    <div class="icon">
                                        <img src="{{ asset('assets/frontend/images/features-image-3.png') }}"
                                            alt="">
                                    </div>
                                    <div class="text">
                                        <h3>{{ $details['feature_sub_title_three'] ?? '' }}</h3>
                                    </div>
                                </div>
                                <div class="para">
                                    <p>{{ $details['feature_sub_desc_three'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="featureListBox">
                                <div class="ttl">
                                    <div class="icon">
                                        <img src="{{ asset('assets/frontend/images/features-image-4.png') }}"
                                            alt="">
                                    </div>
                                    <div class="text">
                                        <h3>{{ $details['feature_sub_title_four'] ?? '' }}</h3>
                                    </div>
                                </div>
                                <div class="para">
                                    <p>{{ $details['feature_sub_desc_four'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="howItWork-sec" id="howItWork-sec">
        <div class="container">
            <h2>How it Works?</h2>
            <div class="row justify-center">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="howItWorkBox">
                        <div class="tham fstImg">
                            <img src="{{ asset('assets/frontend/images/work-img1.png') }}" alt="">
                        </div>
                        <div class="text">
                            <h4>{{ $details['hiw_title_one'] ?? '' }}</h4>
                            <p>{{ $details['hiw_desc_one'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="howItWorkBox">
                        <div class="tham">
                            <img src="{{ asset('assets/frontend/images/work-img2.png') }}" alt="">
                        </div>
                        <div class="text">
                            <h4>{{ $details['hiw_title_two'] ?? '' }}</h4>
                            <p>{{ $details['hiw_desc_two'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="howItWorkBox">
                        <div class="tham">
                            <img src="{{ asset('assets/frontend/images/work-img3.png') }}" alt="">
                        </div>
                        <div class="text">
                            <h4>{{ $details['hiw_title_three'] ?? '' }}</h4>
                            <p>{{ $details['hiw_desc_three'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="goals-sec" id="goals-sec">
        <div class="goalsImg">
            <img src="{{ asset('assets/frontend/images/become-driver1.png') }}" alt="">
        </div>
        <div class="goalsWrap">
            <h6>MY GOALS</h6>
            <h2>{{ $details['goal_title'] ?? '' }}</h2>
            <p>{{ $details['goal_content'] ?? '' }}</p>
            <a href="#" class="sitebtn">Setup Your First Goal <span class="btnArrow"><i
                        class="fa-solid fa-arrow-right-long"></i></span></a>
        </div>
        <div class="goalsImg">
            <img src="{{ asset('assets/frontend/images/become-driver2.png') }}" alt="">
        </div>
    </section>

    @if (!empty($faqs) && $faqs->isNotEmpty())
        <section class="faq-sec" id="faq-sec">
            <div class="container">
                <h2>FAQs</h2>
                <div class="faqWrap">
                    <div class="d-flex align-items-center">
                        {{-- @php
                        $faqs = json_decode($details['faqs'] ?? '[]', true);
                    @endphp --}}
                        <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            @foreach ($faqs as $index => $faq)
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                    id="v-pills-{{ $index }}-tab" data-bs-toggle="pill"
                                    data-bs-target="#v-pills-{{ $index }}" type="button" role="tab"
                                    aria-controls="v-pills-{{ $index }}"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">{{ $faq['question'] ?? '' }}</button>
                            @endforeach
                        </div>
                        <div class="tab-content" id="v-pills-tabContent">
                            @foreach ($faqs as $index => $faq)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                    id="v-pills-{{ $index }}" role="tabpanel"
                                    aria-labelledby="v-pills-{{ $index }}-tab" tabindex="0">
                                    <div class="faqText">
                                        <h4>{{ $faq['question'] ?? '' }}</h4>
                                        <p>{!! nl2br(e($faq['answer'] ?? '')) !!}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="testimonial-sec">
        <div class="container">
            <h2>Testimonials</h2>
            <div class="testimonialWrap">
                <div id="testimonialSlider" class="owl-carousel owl-theme">
                    @php
                        $testimonials = json_decode($details['testimonials'] ?? '[]', true);
                        // dd($testimonials);
                    @endphp
                    @foreach ($testimonials as $testimonial)
                        <div class="item">
                            <div class="testimonialBox">
                                <div class="author">
                                    <div class="icon">
                                        @if (!empty($testimonial['image']))
                                            <img src="{{ asset('uploads/testimonials/' . $testimonial['image']) }}"
                                                alt="">
                                        @else
                                            <img src="{{ asset('assets/frontend/images/default-user.png') }}"
                                                alt="">
                                        @endif
                                    </div>
                                    <h4>{{ $testimonial['title_one'] ?? '' }}</h4>
                                    <p>{{ $testimonial['title_two'] ?? '' }}</p>
                                </div>
                                <div class="text">
                                    <p>{{ $testimonial['title_content'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
    </section>

    <section class="finances-sec">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="finances-text">
                        <h2>{{ $details['testimonial_title_one'] ?? '' }}</h2>
                        <h4>{{ $details['testimonial_title_two'] ?? '' }}</h4>
                        <p>{{ $details['testimonial_title_content'] ?? '' }}</p>
                        <div class="downloadBtn">
                            <a href="{{ $details['testimonial_ios_app_link'] ?? '#' }}">
                                <img src="{{ asset('assets/frontend/images/apple.png') }}" alt="">
                            </a>
                            <a href="{{ $details['testimonial_android_app_link'] ?? '#' }}">
                                <img src="{{ asset('assets/frontend/images/playstore.png') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="finances-img">
                        <img src="{{ asset('assets/frontend/images/device-screens-img.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="preloader">
        <img src="{{ asset('assets/media/logos/loading.gif') }}" alt="" style="width:10%">
    </div>
@endsection

@section('pageModal')
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Contact Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="contact-info">
                        <p>
                            <strong>Contact Number : </strong>
                        </p>
                        <p>
                            <strong>Contact Email : </strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(window).on('load', function() {
            $("#preloader").fadeOut(500);
        });
    </script>
@endpush
