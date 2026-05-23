@extends('layout.app')
@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl" style="padding-top: 30px;">

                <!-- Header -->
                <div class="d-flex flex-stack mb-8">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Subscription Directory
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">Dashboard</li>
                            <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                            <li class="breadcrumb-item text-muted">Subscriptions</li>
                        </ul>
                    </div>
                    @can('add-subscription')
                    <button type="button" class="btn-purple-glow goTo" data-action="{{ route('admin.subscription.add') }}">
                        <i class="fa-solid fa-plus me-2"></i> Add Subscription
                    </button>
                    @endcan
                </div>

                <!-- Search/Filter Bar -->
                <div class="search-card">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-5">
                            <div class="search-wrapper">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" class="modern-input" placeholder="Search subscriptions by name...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="modern-input">
                                <option>All Statuses</option>
                                <option>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-end gap-4">
                            <button class="btn-purple-glow px-5"><i class="fa-solid fa-sliders me-2"></i> Apply</button>
                            <a href="#" class="text-muted fw-semibold text-hover-primary">Reset</a>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="table-card">
                    <div class="table-card-header">
                        <h2 class="table-card-title">
                            Subscription Directory
                            <span class="count-pill">{{ count($details) }} plans</span>
                        </h2>
                        <div class="live-data">
                            <i class="fa-solid fa-rotate-right"></i> Live data
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Plan</th>
                                    <th>Price</th>
                                    <th>Audience / Interval</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($details as $detail)
                                <tr>
                                    <td class="text-muted fw-semibold">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="name-block">
                                            <div>
                                                <span class="name-text">{{ $detail->name ?? 'N/A' }}</span>
                                                <span class="sub-text">Added {{ $detail->created_at->format('M Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 fw-bolder fs-6">{{ $detail->currency ?? '$' }} {{ $detail->price ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            @if($detail->available_for == 1)
                                            <span class="pill-blue"><i class="fa-solid fa-user"></i> User</span>
                                            @else
                                            <span class="pill-purple"><i class="fa-solid fa-music"></i> Artist</span>
                                            @endif

                                            <span class="text-muted fw-bold">/</span>

                                            @if($detail->interval == 1)
                                            <span class="pill-blue"><i class="fa-solid fa-calendar-days"></i> Monthly</span>
                                            @else
                                            <span class="pill-purple"><i class="fa-solid fa-calendar-days"></i> Yearly</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column align-items-center" style="width: 50px;">
                                            <label class="custom-switch">
                                                <input type="checkbox" data-uuid="{{ $detail->uuid }}"
                                                    data-table="subscriptions" class="form-check-input isVerified"
                                                    id="status_{{ $detail->id }}"
                                                    value="{{ $detail->status ?? 0 }}" {{ $detail->status == 1 ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <span class="status-label {{ $detail->status == 1 ? '' : 'inactive' }}" id="label_status_{{ $detail->id }}">{{ $detail->status == 1 ? 'Active' : 'Inactive' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn-action-dots" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 mt-2">
                                            @can('edit-subscription')
                                            <li><a class="dropdown-item py-2" href="{{ route('admin.subscription.edit', $detail->uuid) }}"><i class="fa-solid fa-pen text-muted me-2"></i> Edit</a></li>
                                            @endcan
                                            @can('delete-subscription')
                                            @if($detail->is_default != 1)
                                            <li>
                                                @if (isActiveSubscription($detail->id))
                                                <a class="dropdown-item text-danger py-2 deleteData custom-data-table" href="javascript:void(0)" data-table="subscriptions" data-uuid="{{ $detail->uuid }}"><i class="fa-solid fa-trash text-danger me-2"></i> Delete</a>
                                                @else
                                                <a class="dropdown-item text-danger py-2 deleteNotActive custom-data-table" href="javascript:void(0)" title="Since this subscription is active in some customer profiles, it cannot be deleted !"><i class="fa-solid fa-trash text-danger me-2"></i> Delete</a>
                                                @endif
                                            </li>
                                            @endif
                                            @endcan
                                        </ul>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No subscriptions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    $(document).on("click", ".deleteNotActive", function() {
        Swal.fire({
            text: "Since this subscription is active in some customer profiles, it cannot be deleted !",
            icon: "error",
            showCancelButton: true,
            showConfirmButton: false,
            buttonsStyling: false,
            cancelButtonText: "Ok got it !!",
            customClass: {
                cancelButton: "btn fw-bold btn-danger",
            },
        })
    });

    // Update custom switch label visually when toggled
    $(document).on('change', '.isVerified', function() {
        const isChecked = $(this).is(':checked');
        const labelId = '#label_' + $(this).attr('id');
        if (isChecked) {
            $(labelId).text('Active').removeClass('inactive');
        } else {
            $(labelId).text('Inactive').addClass('inactive');
        }
    });
</script>
@endpush
@endsection