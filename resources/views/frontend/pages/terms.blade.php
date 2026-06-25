@extends('frontend.layout.app')
@section('content')
    <div class="container my-5 py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card border-0 rounded-4" style="background-color: #1e1e2d; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4 text-center" style="color: #ffffff;">{{ $cms->title ?? 'Terms & Conditions' }}</h2>
                        <hr class="mb-5" style="border-color: #2b2b40;">
                        @if(!empty($sections) && is_array($sections))
                            @foreach($sections as $section)
                                <div class="mb-5">
                                    <h4 class="fw-bold mb-3" style="color: #f9f9fb;">{{ $section['title'] ?? '' }}</h4>
                                    <div class="fs-6" style="line-height: 1.8; color: #a1a5b7;">
                                        {!! $section['content'] ?? '' !!}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center" style="color: #a1a5b7;">Terms and conditions content not available at the moment.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        body { background-color: #151521 !important; }
        .header { background-color: #1e1e2d !important; border-bottom: 1px solid #2b2b40 !important; }
        .header .stellarnav ul li a { color: #ffffff !important; }
        .footer { background-color: #101828 !important; }
        .footer p, .footer h3, .footer ul li a { color: #a1a5b7 !important; }
        .footer h3 { color: #ffffff !important; }
    </style>
@endsection
