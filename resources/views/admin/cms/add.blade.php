@extends('layout.app')
@section('content')

@php
    $blocks = [];
    if (!empty($details) && !empty($details->description)) {
        $decoded = json_decode($details->description, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $blocks = $decoded;
        } else {
            // Legacy fallback
            $blocks = [
                ['title' => '', 'content' => $details->description]
            ];
        }
    }
    
    // Always provide at least one empty block for new pages
    if (empty($blocks)) {
        $blocks = [
            ['title' => '', 'content' => '']
        ];
    }
@endphp

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ !empty($details) ? 'Edit CMS Page' : 'Add New CMS Page' }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.cms.list') }}" class="text-muted text-hover-primary">CMS Management</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">{{ !empty($details) ? 'Edit' : 'Add' }}</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.cms.list') }}" class="btn btn-sm btn-light fw-bold btn-active-light-primary rounded-pill">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <form id="cmsForm" action="{{ route('admin.cms.add') }}" method="POST" class="formSubmit fileUpload" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $details->id ?? null }}">

                    <!-- Main Info Card -->
                    <div class="card shadow-sm border-0 mb-8">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-4 text-dark">Page Details</span>
                            </h3>
                        </div>
                        <div class="card-body py-4">
                            <div class="row mb-8">
                                <div class="col-lg-4 mb-8 mb-lg-0">
                                    <label class="d-block fw-semibold fs-6 mb-4">
                                        <span class="required">Featured Image</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Allowed file types: png, jpg, jpeg."></i>
                                    </label>
                                    
                                    <style>
                                        .image-input-wrapper {
                                            border-radius: 12px;
                                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                                        }
                                        .image-input-placeholder {
                                            background-image: url('{{ !empty($details->file) ? $details->image_path : asset('assets/media/svg/files/blank-image.svg') }}');
                                            background-size: cover;
                                            background-position: center;
                                        }
                                    </style>
                                    
                                    <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
                                        <div class="image-input-wrapper w-200px h-200px"></div>
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Image">
                                            <i class="fa-solid fa-camera fs-6"></i>
                                            <input type="file" name="file" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="avatar_remove" />
                                        </label>
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel Image">
                                            <i class="fa-solid fa-xmark fs-6"></i>
                                        </span>
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove Image">
                                            <i class="fa-solid fa-trash fs-6"></i>
                                        </span>
                                    </div>
                                    <div class="form-text mt-3 text-muted">Upload a high-quality image representing this page.</div>
                                </div>
                                
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-md-12 mb-6">
                                            <label class="required form-label fw-bold fs-6 text-gray-800">Page Title</label>
                                            <input type="text" class="form-control form-control-lg form-control-solid rounded-3" placeholder="e.g. Terms and Conditions" name="title" value="{{ $details->title ?? null }}" required />
                                            <div class="form-text">This will be used as the main heading.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12 mb-6">
                                            <label class="form-label fw-bold fs-6 text-gray-800">Page Settings</label>
                                            <div class="d-flex align-items-center mt-2 p-4 bg-light rounded-3 border border-dashed border-gray-300">
                                                <div class="form-check form-switch form-check-custom form-check-solid form-check-success me-5">
                                                    <input class="form-check-input" type="checkbox" name="for_home" id="for_home" value="1" {{ !empty($details->for_home) && $details->for_home == 1 ? 'checked' : '' }} />
                                                    <label class="form-check-label fw-bold text-gray-700" for="for_home">
                                                        Show on Home Page
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Blocks -->
                    <div class="card shadow-sm border-0 mb-8">
                        <div class="card-header border-0 pt-6 d-flex align-items-center justify-content-between">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-4 text-dark">Content Blocks</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Build the page content dynamically section by section.</span>
                            </h3>
                            <button type="button" class="btn btn-sm btn-light-primary fw-bold rounded-pill" id="addBlockBtn">
                                <i class="fa-solid fa-plus me-1"></i> Add Block
                            </button>
                        </div>
                        <div class="card-body py-4" id="blocksContainer">
                            @foreach($blocks as $index => $block)
                            <div class="content-block bg-light rounded-3 p-5 mb-5 position-relative border border-gray-200">
                                @if(count($blocks) > 1)
                                <button type="button" class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 mt-3 me-3 remove-block-btn">
                                    <i class="fa-solid fa-xmark fs-4"></i>
                                </button>
                                @endif
                                <h4 class="text-gray-700 fw-bold mb-4">Block #<span class="block-number">{{ $index + 1 }}</span></h4>
                                
                                <div class="mb-5">
                                    <label class="form-label fw-semibold text-gray-700">Section Title (Optional)</label>
                                    <input type="text" class="form-control form-control-solid rounded-3" name="blocks[{{ $index }}][title]" value="{{ $block['title'] ?? '' }}" placeholder="e.g. Introduction or Intellectual Property" />
                                </div>
                                
                                <div class="mb-2">
                                    <label class="required form-label fw-semibold text-gray-700">Section Content</label>
                                    <textarea class="form-control form-control-solid ckeditor-block" name="blocks[{{ $index }}][content]">{{ $block['content'] ?? '' }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="d-flex justify-content-end pt-5">
                        <a href="{{ route('admin.cms.list') }}" class="btn btn-light rounded-pill me-3">Cancel</a>
                        <button type="submit" id="submitBtn" class="btn btn-primary fw-bold rounded-pill">
                            <span class="indicator-label">
                                <i class="fa-solid fa-save me-1"></i> {{ !empty($details) ? 'Save Changes' : 'Create Page' }}
                            </span>
                            <span class="indicator-progress">
                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
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
    document.addEventListener("DOMContentLoaded", function() {
        let blockIndex = {{ count($blocks) }};

        // Initialize CKEditor for existing blocks
        function initCKEditor(element) {
            ClassicEditor
                .create(element, {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
                })
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        element.value = editor.getData();
                    });
                })
                .catch(err => {
                    console.error(err.stack);
                });
        }

        document.querySelectorAll('.ckeditor-block').forEach(textarea => {
            initCKEditor(textarea);
        });

        // Add New Block
        document.getElementById('addBlockBtn').addEventListener('click', function() {
            const container = document.getElementById('blocksContainer');
            
            const newBlock = document.createElement('div');
            newBlock.className = 'content-block bg-light rounded-3 p-5 mb-5 position-relative border border-gray-200';
            
            newBlock.innerHTML = `
                <button type="button" class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 mt-3 me-3 remove-block-btn">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </button>
                <h4 class="text-gray-700 fw-bold mb-4">Block #<span class="block-number">${blockIndex + 1}</span></h4>
                
                <div class="mb-5">
                    <label class="form-label fw-semibold text-gray-700">Section Title (Optional)</label>
                    <input type="text" class="form-control form-control-solid rounded-3" name="blocks[${blockIndex}][title]" placeholder="e.g. Introduction or Intellectual Property" />
                </div>
                
                <div class="mb-2">
                    <label class="required form-label fw-semibold text-gray-700">Section Content</label>
                    <textarea class="form-control form-control-solid new-ckeditor-block" name="blocks[${blockIndex}][content]"></textarea>
                </div>
            `;
            
            container.appendChild(newBlock);
            
            // Initialize CKEditor for the new block
            const newTextarea = newBlock.querySelector('.new-ckeditor-block');
            initCKEditor(newTextarea);
            newTextarea.classList.remove('new-ckeditor-block'); // Prevent re-init
            
            blockIndex++;
            updateBlockNumbers();
            updateRemoveButtons();
        });

        // Remove Block (Event Delegation)
        document.getElementById('blocksContainer').addEventListener('click', function(e) {
            if (e.target.closest('.remove-block-btn')) {
                const block = e.target.closest('.content-block');
                // Ensure at least one block remains
                if (document.querySelectorAll('.content-block').length > 1) {
                    block.remove();
                    updateBlockNumbers();
                    updateRemoveButtons();
                } else {
                    Swal.fire({
                        text: "You must have at least one content block.",
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            }
        });

        function updateBlockNumbers() {
            document.querySelectorAll('.content-block').forEach((block, idx) => {
                block.querySelector('.block-number').textContent = idx + 1;
            });
        }
        
        function updateRemoveButtons() {
            const blocks = document.querySelectorAll('.content-block');
            if (blocks.length === 1) {
                const removeBtn = blocks[0].querySelector('.remove-block-btn');
                if (removeBtn) removeBtn.remove();
            } else {
                blocks.forEach((block, idx) => {
                    if (!block.querySelector('.remove-block-btn')) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 mt-3 me-3 remove-block-btn';
                        btn.innerHTML = '<i class="fa-solid fa-xmark fs-4"></i>';
                        block.prepend(btn);
                    }
                });
            }
        }
    });
</script>
@endpush
@endsection
