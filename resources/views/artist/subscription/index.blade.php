@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                My Subscriptions
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">View your subscription history and details</span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('artist.subscription.plans') }}" class="btn btn-sm fw-bold btn-primary">Take New Subscription</a>
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

        <div class="card clean-metric-card mb-5 mb-xl-10">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4 song-table mb-0">
                        <thead>
                            <tr class="fw-bold text-muted bg-transparent">
                                <th class="ps-6 min-w-200px rounded-start">Plan</th>
                                <th class="min-w-100px">Price</th>
                                <th class="min-w-100px">Started On</th>
                                <th class="min-w-100px">Ends At</th>
                                <th class="min-w-100px">Transaction ID</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-100px text-end pe-6 rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $sub)
                            <tr>
                                <td class="ps-6">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bold mb-1 fs-6">{{ $sub->subscription ? $sub->subscription->name : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold d-block fs-7">
                                        @if($sub->subscription)
                                            @if($sub->subscription->price > 0)
                                                ${{ number_format($sub->subscription->price, 2) }} / {{ $sub->subscription->interval }}
                                            @else
                                                Free
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold d-block fs-7">{{ $sub->started_on ? \Carbon\Carbon::parse($sub->started_on)->format('M d, Y') : 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold d-block fs-7">{{ $sub->ends_at ? \Carbon\Carbon::parse($sub->ends_at)->format('M d, Y') : ($sub->ended_at ? \Carbon\Carbon::parse($sub->ended_at)->format('M d, Y') : 'N/A') }}</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold d-block fs-7">{{ $sub->transaction_id ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($sub->stripe_status === 'pending_cancel')
                                        <span class="badge badge-light-warning fw-bold px-4 py-2">Cancels at period end</span>
                                    @elseif($sub->status == 1)
                                        <span class="badge badge-light-success fw-bold px-4 py-2">Active</span>
                                    @elseif($sub->status == 0)
                                        <span class="badge badge-light-warning fw-bold px-4 py-2">In-active</span>
                                    @elseif($sub->status == 2)
                                        <span class="badge badge-light-danger fw-bold px-4 py-2">Expired</span>
                                    @elseif($sub->status == 3)
                                        <span class="badge badge-light-danger fw-bold px-4 py-2">Cancelled</span>
                                    @else
                                        <span class="badge badge-light-secondary fw-bold px-4 py-2">Unknown</span>
                                    @endif
                                </td>
                                <td class="text-end pe-6">
                                    @if($sub->status == 1 && $sub->stripe_status !== 'pending_cancel')
                                        <form action="{{ route('artist.subscription.cancel', $sub->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this subscription? Your access will continue until the end of your billing period.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light-danger fw-bold">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-10">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                                        <i class="fa-solid fa-credit-card text-muted mb-4" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <h4 class="fw-bold text-dark mb-1">No Subscriptions Found</h4>
                                        <p class="text-muted fs-7 mb-4">You haven't purchased any subscriptions yet.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end py-6 pe-6">
                    <div class="custom-pagination-wrapper">
                        {!! $subscriptions->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
            </div>
        </div>

    </div>
    @push('style')
    <style>
        .custom-pagination-wrapper .pagination {
            display: flex;
            gap: 8px;
            border: none;
        }

        .custom-pagination-wrapper .page-item {
            margin: 0;
        }

        .custom-pagination-wrapper .page-item .page-link {
            border: 1px solid #e5e7eb;
            border-radius: 12px !important;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .custom-pagination-wrapper .page-item.active .page-link {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border-color: transparent;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.35);
            transform: translateY(-2px);
        }

        .custom-pagination-wrapper .page-item .page-link:hover:not(.active) {
            background-color: #f5f3ff;
            border-color: #c4b5fd;
            color: #7c3aed;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .custom-pagination-wrapper .page-item.disabled .page-link {
            background-color: #f9fafb;
            border-color: #f3f4f6;
            color: #d1d5db;
            opacity: 0.7;
            pointer-events: none;
            box-shadow: none;
        }

        .custom-pagination-wrapper .page-link:focus {
            box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25) !important;
            outline: 0;
        }
    </style>
    @endpush
@endsection
