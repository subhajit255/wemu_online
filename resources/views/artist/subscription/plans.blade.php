@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Subscription Plans
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Choose a subscription plan that fits your needs</span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('artist.subscription.index') }}" class="btn btn-sm fw-bold btn-secondary">Back to My Subscriptions</a>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-10">
            @forelse($subscriptions as $plan)
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 15px;">
                        <div class="card-body p-8">
                            <div class="mb-5 text-center">
                                <h2 class="fw-bold text-dark mb-2">{{ $plan->name }}</h2>
                                @if($plan->price > 0)
                                    <div class="fs-1 fw-bold text-primary mb-2">${{ number_format($plan->price, 2) }}</div>
                                    <span class="text-muted fs-5 fw-semibold">/ {{ $plan->interval }}</span>
                                @else
                                    <div class="fs-1 fw-bold text-success mb-2">Free</div>
                                    <span class="text-muted fs-5 fw-semibold">Forever</span>
                                @endif
                            </div>
                            
                            <div class="fs-6 text-muted mb-8 text-center">
                                {{ $plan->description ?? 'Get access to premium features and analytics to grow your audience.' }}
                            </div>

                            @if($plan->features)
                            <div class="mb-8">
                                <ul class="list-unstyled">
                                    @php
                                        $features = explode("\n", $plan->features);
                                    @endphp
                                    @foreach($features as $feature)
                                        @if(trim($feature) !== '')
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="fa-solid fa-check text-success me-3"></i>
                                            <span class="text-gray-800">{{ trim($feature) }}</span>
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <div class="text-center mt-auto">
                                <form action="{{ route('artist.subscription.checkout') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    @if(auth()->user()->subscription_type == $plan->id)
                                        <button type="button" class="btn btn-light-success fw-bold w-100 py-3" disabled>Current Plan</button>
                                    @else
                                        <button type="submit" class="btn btn-primary fw-bold w-100 py-3">Subscribe Now</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-10">
                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                        <i class="fa-solid fa-box-open text-muted mb-4" style="font-size: 3rem; opacity: 0.2;"></i>
                        <h4 class="fw-bold text-dark mb-1">No Plans Available</h4>
                        <p class="text-muted fs-7 mb-4">There are currently no subscription plans available to choose from.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
