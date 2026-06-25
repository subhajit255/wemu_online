@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6" style="border-bottom: 1px solid #f3f4f6;">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bolder fs-2 flex-column justify-content-center my-0">
                Team Directory
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">Dashboard</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Team</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="#" class="btn btn-sm wemu-btn-primary d-flex align-items-center px-4 py-3"
                data-bs-toggle="modal" data-bs-target="#add_team_member_modal"
                style="border-radius: 8px; font-weight: 600;">
                <i class="fa-solid fa-user-plus fs-6 me-2"></i> Add Team Member
            </a>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid pt-8">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Filter Card -->
        <div class="card wemu-filter-card mb-8">
            <div class="card-body p-5">
                <form action="{{ route('artist.team.index') }}" method="GET" class="d-flex align-items-center justify-content-between flex-wrap gap-4 w-100">
                    <div class="d-flex align-items-center gap-4 flex-grow-1 flex-wrap">
                        <div class="position-relative w-100 max-w-400px" style="flex: 1; max-width: 400px;">
                            <i
                                class="fa-solid fa-magnifying-glass fs-5 text-gray-500 position-absolute top-50 translate-middle-y ms-4"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid wemu-search-input ps-12"
                                placeholder="Search users by name, email or mobile..." style="height: 44px;" />
                        </div>
                        <select name="status" class="form-select form-select-solid wemu-select w-100 max-w-200px"
                            style="flex: 1; max-width: 250px; height: 44px;">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn wemu-btn-primary px-6 d-flex align-items-center"
                            style="height: 44px; font-weight: 600; border-radius: 8px;">
                            <i class="fa-solid fa-sliders fs-6 me-2"></i> Apply
                        </button>
                        <a href="{{ route('artist.team.index') }}" class="btn btn-light px-6 text-gray-600 fw-bold hover-scale d-flex align-items-center"
                            style="height: 44px; border-radius: 8px; background-color: #f9f9fb; border: 1px solid #f1f1f4;">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>


        <div class="wemu-glass-card p-0">
            {{-- Table header bar --}}
            <div class="d-flex align-items-center justify-content-between px-7 py-5" style="border-bottom: 1px solid rgba(0,0,0,0.04);">
                <div class="d-flex align-items-center gap-3">
                    <span class="fs-6 fw-bold text-dark">User Directory</span>
                    <span class="badge badge-light-primary fw-semibold">{{ $users->total() }} users</span>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted fs-8" style="cursor: pointer;" onclick="window.location.reload()">
                    <i class="fa-solid fa-arrow-rotate-right"></i> Live data
                </div>
            </div>

            <div class="table-responsive">
                <table class="table wemu-listener-table m-0">
                    <thead>
                        <tr>
                            <th class="ps-7">#</th>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end pe-7">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td class="ps-7 text-muted fw-semibold">{{ str_pad($users->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="wemu-avatar-wrapper me-4">
                                        <div class="symbol symbol-50px me-3">
                                            <img src="{{ $user->image_path }}"
                                                alt="Avatar" class="rounded-3"
                                                style="border-radius: 12px !important;">
                                        </div>
                                        <div class="wemu-avatar-status"></div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="#"
                                            class="text-dark text-hover-primary fw-bold fs-6 mb-1">{{ $user->name }}</a>
                                        <span class="text-muted fw-semibold fs-7">Joined {{ $user->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-muted fs-7 mb-1"><i
                                            class="fa-solid fa-envelope text-muted me-2"></i>
                                        {{ $user->email }}</span>
                                    @if($user->mobile_number)
                                    <span class="text-muted fs-7"><i
                                            class="fa-solid fa-phone text-muted me-2"></i> {{ $user->mobile_number }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="wemu-role-badge d-inline-flex align-items-center">
                                    <i class="fa-solid fa-user-pen me-2" style="color: #3e63dd;"></i> Sub-Artist
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-start">
                                    <div
                                        class="form-check form-switch form-check-custom form-check-solid wemu-status-toggle">
                                        <input class="form-check-input h-20px w-35px" type="checkbox"
                                            value="" id="status_{{ $user->id }}" {{ $user->is_active ? 'checked="checked"' : '' }} />
                                    </div>
                                    <span class="wemu-status-text ms-1 text-dark">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                </div>
                            </td>
                            <td class="text-end pe-7">
                                <button
                                    class="btn btn-sm btn-icon btn-light btn-active-light-primary rounded-circle"
                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                                </button>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-175px py-4"
                                    data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 manage-permissions"
                                            data-id="{{ $user->id }}"
                                            data-permissions="{{ $user->permissions }}">Manage Permission</a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 edit-team-member"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-phone="{{ $user->mobile_number }}"
                                            data-permissions="{{ $user->permissions }}">Edit</a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 text-danger delete-team-member" data-id="{{ $user->id }}">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No team members found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="py-6 px-6">
                    <div class="custom-pagination-wrapper w-100">
                        {!! $users->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal - Add Team Member -->
<div class="modal fade" id="add_team_member_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded-4">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-3"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form id="kt_modal_new_target_form" class="form" action="#">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <div class="mb-13 text-center">
                        <h1 class="mb-3 text-dark fw-bolder">Add Team Member</h1>
                        <div class="text-muted fw-semibold fs-5">Invite a new member to join your team.</div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-12 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Full Name</span>
                            </label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Enter full name" name="name" />
                        </div>
                        <!-- <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Role</span>
                            </label>
                            <select class="form-select form-select-solid" data-control="select2"
                                data-hide-search="true" data-placeholder="Select a Role" name="role">
                                <option value="">Select a Role...</option>
                                <option value="1">Sub-Artist</option>
                                <option value="2">Manager</option>
                            </select>
                        </div> -->
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Email Address</span>
                        </label>
                        <input type="email" class="form-control form-control-solid"
                            placeholder="Enter email address" name="email" />
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span>Phone Number (Optional)</span>
                        </label>
                        <input type="text" class="form-control form-control-solid"
                            placeholder="Enter phone number" name="phone" />
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span>Sidebar Permissions</span>
                        </label>
                        <div class="d-flex flex-wrap gap-5 mt-3">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="songs" id="perm_songs"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="perm_songs">Songs (Music)</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="albums" id="perm_albums"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="perm_albums">Albums</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="analytics" id="perm_analytics"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="perm_analytics">Analytics</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="audience" id="perm_audience"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="perm_audience">Audience</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="releases" id="perm_releases"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="perm_releases">Releases</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-10">
                        <button type="reset" class="btn btn-light me-3 fw-bold"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn wemu-btn-primary fw-bold" id="kt_modal_new_target_submit">
                            <span class="indicator-label">Save Team Member</span>
                            <span class="indicator-progress" style="display: none;">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal - Manage Permissions -->
<div class="modal fade" id="manage_permissions_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content rounded-4">
            <div class="modal-header pb-0 border-0">
                <h2 class="fw-bold mb-0">Manage Permissions</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-3"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 pt-5 pb-10">
                <form id="kt_modal_manage_permissions_form" class="form" action="#">
                    @csrf
                    <input type="hidden" name="id" value="" />
                    <div class="text-muted fw-semibold fs-5 mb-8">
                        Update sidebar access for this team member.
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span>Sidebar Permissions</span>
                        </label>
                        <div class="d-flex flex-wrap gap-5 mt-3">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input manage-permission-checkbox" type="checkbox" name="permissions[]" value="songs" id="m_perm_songs"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="m_perm_songs">Songs (Music)</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input manage-permission-checkbox" type="checkbox" name="permissions[]" value="albums" id="m_perm_albums"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="m_perm_albums">Albums</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input manage-permission-checkbox" type="checkbox" name="permissions[]" value="analytics" id="m_perm_analytics"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="m_perm_analytics">Analytics</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input manage-permission-checkbox" type="checkbox" name="permissions[]" value="audience" id="m_perm_audience"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="m_perm_audience">Audience</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input manage-permission-checkbox" type="checkbox" name="permissions[]" value="releases" id="m_perm_releases"/>
                                <label class="form-check-label fw-semibold text-gray-700" for="m_perm_releases">Releases</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-10">
                        <button type="reset" class="btn btn-light me-3 fw-bold"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn wemu-btn-primary fw-bold" id="kt_modal_manage_permissions_submit">
                            <span class="indicator-label">Save Permissions</span>
                            <span class="indicator-progress" style="display: none;">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
<script>
    $('#kt_modal_new_target_form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitButton = $('#kt_modal_new_target_submit');
        submitButton.find('.indicator-label').hide();
        submitButton.find('.indicator-progress').show();
        submitButton.attr('disabled', true);

        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('artist.team.storeOrUpdate') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status == 200) {
                    location.reload();
                } else {
                    Swal.fire({
                        text: response.message || 'An error occurred.',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    submitButton.find('.indicator-label').show();
                    submitButton.find('.indicator-progress').hide();
                    submitButton.attr('disabled', false);
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if(errors) {
                    var errorMsg = '';
                    $.each(errors, function(key, value) {
                        errorMsg += value[0] + '\n';
                    });
                    Swal.fire({
                        text: errorMsg,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                } else {
                    Swal.fire({
                        text: 'An error occurred.',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
                submitButton.find('.indicator-label').show();
                submitButton.find('.indicator-progress').hide();
                submitButton.attr('disabled', false);
            }
        });
    });

    $('.edit-team-member').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var phone = $(this).data('phone');
        var permissions = $(this).data('permissions');

        $('#kt_modal_new_target_form input[name="id"]').val(id);
        $('#kt_modal_new_target_form input[name="name"]').val(name);
        $('#kt_modal_new_target_form input[name="email"]').val(email);
        $('#kt_modal_new_target_form input[name="phone"]').val(phone);
        
        // Reset permissions
        $('.permission-checkbox').prop('checked', false);
        if (permissions) {
            var permsArray = typeof permissions === 'string' ? JSON.parse(permissions) : permissions;
            $.each(permsArray, function(index, value) {
                $('.permission-checkbox[value="' + value + '"]').prop('checked', true);
            });
        }

        $('#kt_modal_new_target_form h1').text('Edit Team Member');
        $('#kt_modal_new_target_form .indicator-label').text('Save Changes');

        $('#add_team_member_modal').modal('show');
    });

    $('#add_team_member_modal').on('hidden.bs.modal', function () {
        $('#kt_modal_new_target_form')[0].reset();
        $('#kt_modal_new_target_form input[name="id"]').val('');
        $('.permission-checkbox').prop('checked', false);
        $('#kt_modal_new_target_form h1').text('Add Team Member');
        $('#kt_modal_new_target_form .indicator-label').text('Send Invitation');
    });

    $('.delete-team-member').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            text: "Are you sure you want to delete this team member?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "No, cancel",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ url('artist/team/delete') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if(response.status == 200) {
                            location.reload();
                        } else {
                            Swal.fire({
                                text: response.message || 'An error occurred.',
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            text: 'An error occurred.',
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            }
        });
    });

    // Manage Permissions Logic
    $('.manage-permissions').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var permissions = $(this).data('permissions');

        $('#kt_modal_manage_permissions_form input[name="id"]').val(id);
        
        $('.manage-permission-checkbox').prop('checked', false);
        if (permissions) {
            var permsArray = typeof permissions === 'string' ? JSON.parse(permissions) : permissions;
            $.each(permsArray, function(index, value) {
                $('.manage-permission-checkbox[value="' + value + '"]').prop('checked', true);
            });
        }

        $('#manage_permissions_modal').modal('show');
    });

    $('#kt_modal_manage_permissions_form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitButton = $('#kt_modal_manage_permissions_submit');
        submitButton.find('.indicator-label').hide();
        submitButton.find('.indicator-progress').show();
        submitButton.attr('disabled', true);

        var id = form.find('input[name="id"]').val();
        var formData = new FormData(this);
        
        $.ajax({
            url: "{{ url('artist/team/update-permissions') }}/" + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status == 200) {
                    location.reload();
                } else {
                    Swal.fire({
                        text: response.message || 'An error occurred.',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    submitButton.find('.indicator-label').show();
                    submitButton.find('.indicator-progress').hide();
                    submitButton.attr('disabled', false);
                }
            },
            error: function() {
                Swal.fire({
                    text: 'An error occurred.',
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                submitButton.find('.indicator-label').show();
                submitButton.find('.indicator-progress').hide();
                submitButton.attr('disabled', false);
            }
        });
    });
</script>
@endpush