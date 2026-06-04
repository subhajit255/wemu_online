@extends('layout.app')
@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <!-- Page Header -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Help & Support Queries
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Help & Support</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid pt-0">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Filter Section -->
                <div class="wemu-glass-card mb-6">
                    <form>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <div class="wemu-search-bar">
                                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                    <input type="text" class="wemu-search-input" placeholder="Search Queries...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select form-select-solid wemu-select">
                                    <option value="all">All Types</option>
                                    <option value="help">Help Center</option>
                                    <option value="contact">Contact Us</option>
                                    <option value="report">Report Problem</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex justify-content-md-end gap-2">
                                <button class="btn wemu-btn-filter searchBtn" type="button">
                                    <i class="fa-solid fa-sliders me-2"></i> Apply
                                </button>
                                <button type="button" class="btn btn-light px-5">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Main Table Card -->
                <div class="wemu-glass-card p-0">
                    <div class="d-flex align-items-center justify-content-between px-7 py-5" style="border-bottom: 1px solid rgba(0,0,0,0.04);">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-6 fw-bold text-gray-800">Queries</span>
                            <span class="badge badge-light-primary fw-semibold">{{ $queries->count() }} queries</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted fs-8" style="cursor: pointer;" onclick="window.location.reload()">
                            <i class="fa-solid fa-arrow-rotate-right"></i> Live data
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table wemu-listener-table m-0">
                            <thead>
                                <tr>
                                    <th class="ps-7">ID</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Subject / Message</th>
                                    <th>Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-7">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($queries as $item)
                                <tr class="wemu-listener-row">
                                    <td class="ps-7 text-muted fw-bold fs-8">#{{ $item->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="wemu-avatar">
                                                @if($item->user && $item->user->image)
                                                    <img src="{{ asset('storage/users/' . $item->user->image) }}" style="width: 45px; height: 45px; border-radius: 8px;" alt="">
                                                @else
                                                    <div class="symbol-label bg-light-primary text-primary fw-bolder fs-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                                        {{ strtoupper(substr($item->user->name ?? 'U', 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="#" class="fw-bold text-gray-900 text-hover-primary fs-6 d-block">{{ $item->user->name ?? 'Unknown' }}</a>
                                                <span class="text-muted fs-8">{{ $item->user->email ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="wemu-tier-pill wemu-tier-individual">Support Request</span>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" class="viewQuery text-decoration-none" data-subject="{{ $item->subject }}" data-enquiry="{{ $item->query }}">
                                            <div class="fw-bold text-gray-900 mb-1 text-hover-primary">{{ Str::limit($item->subject ?? 'No Subject', 30) }}</div>
                                            <div class="text-muted fs-8 text-truncate" style="max-width: 250px;">{{ Str::limit($item->query, 50) }}</div>
                                        </a>
                                    </td>
                                    <td class="text-muted fs-8 fw-semibold">{{ $item->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="text-center">
                                        @if($item->status == 0)
                                            <span class="badge badge-light-warning fw-bold px-3 py-2">Pending</span>
                                        @elseif($item->status == 1)
                                            <span class="badge badge-light-success fw-bold px-3 py-2">Replied</span>
                                        @else
                                            <span class="badge badge-light-secondary fw-bold px-3 py-2">Closed</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-7">
                                        @if($item->status == 0)
                                            <button class="btn btn-sm btn-light-primary fw-bold btn-reply" data-id="{{ $item->id }}" data-user="{{ $item->user->name ?? 'Unknown' }}" data-email="{{ $item->user->email ?? 'N/A' }}">Reply</button>
                                        @else
                                            <button class="btn btn-sm btn-light fw-bold" disabled>Replied</button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10 text-muted">No queries found.</td>
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

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center fs-3 fw-bold text-dark">
                    <div class="d-flex align-items-center justify-content-center bg-light-primary rounded-circle me-3" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-reply text-primary fs-6"></i>
                    </div>
                    Reply to Query <span id="replyQueryId" class="text-muted ms-2 fs-5 fw-semibold"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-5">
                <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                    <div class="d-flex flex-column">
                        <span class="text-muted fw-semibold fs-7 mb-1">Replying to:</span>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-dark fs-5 me-2" id="replyUserName">John Doe</span>
                            <span class="badge badge-light-dark fs-8" id="replyUserEmail">johndoe@example.com</span>
                        </div>
                    </div>
                </div>

                <form id="replyForm">
                    <input type="hidden" id="reply_id">
                    <div class="mb-5">
                        <label class="form-label fw-bold text-gray-800 fs-6">Your Message</label>
                        <textarea class="form-control form-control-solid" id="reply_message" rows="6" placeholder="Type your reply here. This will be sent to the user's email." required></textarea>
                        <div class="form-text text-muted mt-2 fs-8">A copy of this reply will be stored in the system.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-end gap-2">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary fw-bold" id="sendReplyBtn">
                    <i class="fa-solid fa-paper-plane me-1"></i> Send Reply
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center fs-3 fw-bold text-dark">
                    <div class="d-flex align-items-center justify-content-center bg-light-info rounded-circle me-3" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-eye text-info fs-6"></i>
                    </div>
                    Query Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-5">
                <div class="mb-5">
                    <label class="form-label fw-bold text-gray-800 fs-6">Subject</label>
                    <div id="viewSubject" class="form-control form-control-solid bg-light text-dark fw-semibold" style="height: auto;" readonly></div>
                </div>
                <div class="mb-5">
                    <label class="form-label fw-bold text-gray-800 fs-6">Message / Enquiry</label>
                    <div id="viewEnquiry" class="form-control form-control-solid bg-light text-dark" style="min-height: 120px; height: auto; white-space: pre-wrap;" readonly></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-end gap-2">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const replyModal = new bootstrap.Modal(document.getElementById('replyModal'));
        
        $('.btn-reply').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('user');
            const email = $(this).data('email');
            
            $('#replyQueryId').text('#' + id);
            $('#reply_id').val(id);
            $('#reply_message').val('');
            $('#replyUserName').text(name);
            $('#replyUserEmail').text(email);
            
            replyModal.show();
        });

        const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
        
        $('.viewQuery').on('click', function(e) {
            e.preventDefault();
            const subject = $(this).data('subject');
            const enquiry = $(this).data('enquiry');
            
            $('#viewSubject').text(subject);
            $('#viewEnquiry').text(enquiry);
            
            viewModal.show();
        });

        $('#sendReplyBtn').on('click', function() {
            const btn = $(this);
            const id = $('#reply_id').val();
            const reply = $('#reply_message').val();

            if(!reply) {
                toastr.warning('Please enter a reply message.');
                return;
            }

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Sending...');
            
            $.ajax({
                url: "{{ route('admin.help.support.reply') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    reply: reply
                },
                success: function(response) {
                    if(response.status) {
                        replyModal.hide();
                        Swal.fire({
                            text: "Reply sent successfully!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        toastr.error(response.message || 'Something went wrong');
                        btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane me-1"></i> Send Reply');
                    }
                },
                error: function(err) {
                    toastr.error('Failed to send reply');
                    btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane me-1"></i> Send Reply');
                }
            });
        });
    });
</script>
@endpush
@endsection
