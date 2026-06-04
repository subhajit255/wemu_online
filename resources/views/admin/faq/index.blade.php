@extends('layout.app')

@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6" style="border-bottom: 1px solid #f3f4f6;">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bolder fs-2 flex-column justify-content-center my-0">
                FAQ Directory
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">FAQs</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('add-faq')
            <a href="#" class="btn btn-sm wemu-btn-primary d-flex align-items-center px-4 py-3"
                data-bs-toggle="modal" data-bs-target="#kt_modal_add_faq"
                style="border-radius: 8px; font-weight: 600;">
                <i class="fa-solid fa-plus fs-6 me-2"></i> Add FAQ
            </a>
            @endcan
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid pt-8">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Filter Card -->
        <div class="card wemu-filter-card mb-8">
            <div class="card-body p-5">
                <form action="{{ route('admin.faq.list') }}" method="GET" class="d-flex align-items-center justify-content-between flex-wrap gap-4 w-100">
                    <div class="d-flex align-items-center gap-4 flex-grow-1 flex-wrap">
                        <div class="position-relative w-100 max-w-400px" style="flex: 1; max-width: 400px;">
                            <i class="fa-solid fa-magnifying-glass fs-5 text-gray-500 position-absolute top-50 translate-middle-y ms-4"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid wemu-search-input ps-12"
                                placeholder="Search FAQs by question..." style="height: 44px;" />
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
                        <a href="{{ route('admin.faq.list') }}" class="btn btn-light px-6 text-gray-600 fw-bold hover-scale d-flex align-items-center"
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
                    <span class="fs-6 fw-bold text-gray-800">FAQ Directory</span>
                    <span class="badge badge-light-primary fw-semibold">{{ collect($details)->count() }} FAQs</span>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted fs-8" style="cursor: pointer;" onclick="window.location.reload()">
                    <i class="fa-solid fa-arrow-rotate-right"></i> Live data
                </div>
            </div>

            <div class="table-responsive">
                <table class="table wemu-listener-table m-0">
                    <thead>
                        <tr>
                            <th class="ps-7 w-80px">#</th>
                            <th class="min-w-250px">Question</th>
                            <th class="min-w-300px">Answer</th>
                            <th>Status</th>
                            <th class="text-end pe-7">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $index => $faq)
                        <tr>
                            <td class="ps-7 text-muted fw-semibold">{{ sprintf('%02d', $index + 1) }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-900 fw-bold fs-6 mb-1">{{ $faq->question }}</span>
                                    <span class="text-muted fw-semibold fs-7">Added {{ $faq->created_at->format('M Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-light-primary fw-bold edit-faq-btn"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#kt_modal_edit_faq"
                                    data-id="{{ $faq->id }}"
                                    data-question="{{ $faq->question }}"
                                    data-answer="{{ $faq->answer }}"
                                    data-is_active="{{ $faq->is_active }}">
                                    <i class="fa-solid fa-eye me-1"></i> View / Edit
                                </button>
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-start">
                                    <div class="form-check form-switch form-check-custom form-check-solid wemu-status-toggle">
                                        <input class="form-check-input h-20px w-35px isVerified" type="checkbox"
                                            value="{{ $faq->is_active }}" {{ $faq->is_active ? 'checked="checked"' : '' }} data-uuid="{{ $faq->uuid }}" data-table="faqs" />
                                    </div>
                                    <span class="wemu-status-text ms-1 mt-1" style="color: {{ $faq->is_active ? '#10b981' : '#ef4444' }}; font-size: 10px; font-weight: 700;">
                                        {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                    </span>
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
                                    @can('edit-faq')
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 edit-faq-btn"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#kt_modal_edit_faq"
                                            data-id="{{ $faq->id }}"
                                            data-question="{{ $faq->question }}"
                                            data-answer="{{ $faq->answer }}"
                                            data-is_active="{{ $faq->is_active }}">Edit</a>
                                    </div>
                                    @endcan
                                    @can('delete-faq')
                                    <div class="menu-item px-3">
                                        <a href="javascript:void(0)" data-table="faqs" data-uuid="{{ $faq->uuid }}" class="menu-link px-3 text-danger deleteData">Delete</a>
                                    </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No FAQs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal - Add FAQ -->
<div class="modal fade" id="kt_modal_add_faq" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded-4">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-3"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form id="kt_modal_add_faq_form" class="form formSubmit" action="{{ route('admin.faq.add') }}" method="POST">
                    @csrf
                    <div class="mb-13 text-center">
                        <h1 class="mb-3 text-dark fw-bolder">Add FAQ</h1>
                        <div class="text-muted fw-semibold fs-5">Create a new frequently asked question.</div>
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Question</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Enter FAQ question" name="question" required />
                    </div>
                    
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Answer</span>
                        </label>
                        <textarea class="form-control form-control-solid" id="add_answer_ckeditor" rows="5" placeholder="Enter full answer" name="answer" required></textarea>
                    </div>

                    <div class="text-center mt-10">
                        <button type="reset" class="btn btn-light me-3 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn wemu-btn-primary fw-bold">
                            <span class="indicator-label">Submit</span>
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

<!-- Modal - Edit FAQ -->
<div class="modal fade" id="kt_modal_edit_faq" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded-4">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-3"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form id="edit_faq_form" class="form formSubmit" action="{{ route('admin.faq.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="edit_id" value="">
                    
                    <div class="mb-13 text-center">
                        <h1 class="mb-3 text-dark fw-bolder">Edit FAQ</h1>
                        <div class="text-muted fw-semibold fs-5">Update question and answer details.</div>
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Question</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Enter FAQ question" name="question" id="edit_question" required />
                    </div>
                    
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Answer</span>
                        </label>
                        <textarea class="form-control form-control-solid" id="edit_answer_ckeditor" rows="5" placeholder="Enter full answer" name="answer" required></textarea>
                    </div>

                    <div class="text-center mt-10">
                        <button type="button" class="btn btn-light me-3 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn wemu-btn-primary fw-bold">
                            <span class="indicator-label">Save Changes</span>
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

@push('script')
<script src="{{ asset('assets/js/custom_js/cdn/ckeditor.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let addEditor, editEditor;

        function initCKEditor(elementId, editorInstanceCallback) {
            ClassicEditor
                .create(document.getElementById(elementId), {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
                })
                .then(editor => {
                    editorInstanceCallback(editor);
                    editor.model.document.on('change:data', () => {
                        document.getElementById(elementId).value = editor.getData();
                    });
                })
                .catch(err => {
                    console.error(err.stack);
                });
        }

        initCKEditor('add_answer_ckeditor', (editor) => addEditor = editor);
        initCKEditor('edit_answer_ckeditor', (editor) => editEditor = editor);

        const editButtons = document.querySelectorAll('.edit-faq-btn');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const question = this.getAttribute('data-question');
                const answer = this.getAttribute('data-answer');
                
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_question').value = question;
                document.getElementById('edit_answer_ckeditor').value = answer;
                
                // Update CKEditor content
                if (editEditor) {
                    editEditor.setData(answer);
                }
            });
        });
    });
</script>
@endpush
@endsection
