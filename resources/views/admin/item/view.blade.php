@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Item Details</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.item.list') }}" class="text-muted text-hover-primary">
                                    Item</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                Item Details
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card">
                        <div class="card-body pt-6">
                            <div class="container">
                                <div class="row pt-2">

                                    <div class="col-md-12 pt-4 pb-4 themeGardant">
                                        <h3>Item Details --></h3>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">User</h5>
                                                <p class="card-text">{{ $details->user?->name ?? 'N/A' }}
                                                    ({{ getPhoneCode($details->user?->id) }}{{ $details->user?->mobile_number ?? 'N/A' }})</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Category</h5>
                                                <p class="card-text">{{ $details->category?->title ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Name</h5>
                                                <p class="card-text">{{ $details->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Price</h5>
                                                <p class="card-text">{{ getCurrency() }}{{ $details->price ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Purchase Date</h5>
                                                <p class="card-text">{{ $details->date ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Brand Name</h5>
                                                <p class="card-text">{{ $details->brand_name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Model No</h5>
                                                <p class="card-text">{{ $details->model_no ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Serial No</h5>
                                                <p class="card-text">{{ $details->serial_no ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 pt-4 pb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Is Expense</h5>
                                                <p class="card-text">{{ $details->is_expense ? 'Yes' : 'No' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 pt-4 pb-4 themeGardant">
                                        <h3>Service Details --></h3>
                                    </div>

                                    <div class="col-md-4 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Service Frequency</h5>
                                                <p class="card-text">
                                                    {{ $details->serviceDetail?->serviceFrequency?->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Last Service Date</h5>
                                                <p class="card-text">
                                                    {{ $details->serviceDetail?->last_service_date ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Set Remainder</h5>
                                                <p class="card-text">
                                                    {{ $details->serviceDetail?->set_remainder ? 'Yes' : 'No' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 pt-4 pb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Comments</h5>
                                                <p class="card-text">{{ $details->serviceDetail?->comments ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 pt-4 pb-4 themeGardant">
                                        <h3>Warranty Details --></h3>
                                    </div>

                                    <div class="col-md-6 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Expiry Date</h5>
                                                <p class="card-text">{{ $details->warrantyDetail?->expiry_date ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Include</h5>
                                                <p class="card-text">
                                                    @switch($details->warrantyDetail?->include)
                                                        @case(1)
                                                            Repair
                                                        @break

                                                        @case(2)
                                                            Replace
                                                        @break

                                                        @case(3)
                                                            Both
                                                        @break

                                                        @case(4)
                                                            None
                                                        @break

                                                        @default
                                                            N/A
                                                    @endswitch
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Customer Care Number</h5>
                                                <p class="card-text">
                                                    {{ $details->warrantyDetail?->customer_care_number ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pt-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Set Remainder</h5>
                                                <p class="card-text">
                                                    {{ $details->warrantyDetail?->set_remainder ? 'Yes' : 'No' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 pt-4 pb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Comments</h5>
                                                <p class="card-text">{{ $details->warrantyDetail?->comments ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 pt-4 pb-4 themeGardant">
                                        <h3>Item Images --></h3>
                                    </div>

                                    <div class="multipleImageShowing pt-4 pb-4">
                                        <div id="previewImages">
                                            @if (!empty($details->images))
                                                @foreach ($details->images as $image)
                                                    <div class="col-md-4">
                                                        <div id="imgCls_{{ $loop->iteration }}">
                                                            <img style="width:100px"
                                                                src="{{ asset('storage/item') . '/' . $image->image }}"
                                                                alt="">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
    @endpush
@endsection
