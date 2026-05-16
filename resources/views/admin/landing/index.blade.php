@extends('layout.app')
@section('content')
    <style>
        .remove-faq {
            height: 36px;
        }
    </style>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card">
                        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                    <h1
                                        class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                        Landing Page {{ !empty($details) ? 'Update' : 'Add' }}</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.dashboard') }}"
                                                class="text-muted text-hover-primary">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.landing.page.update') }}"
                                                class="text-muted text-hover-primary">Landing Page</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div class="card-body pt-6">
                                <div class="container">
                                    <form id="landingForm" action="{{ route('admin.landing.page.update') }}" method="POST"
                                        class="formSubmit fileUpload" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="{{ $details->id ?? null }}">

                                        <h4 class="pt-4">Hero Section</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="hero_title_one" class="label-style">Hero Title One</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter hero title one" name="hero_title_one"
                                                        id="hero_title_one" value="{{ $details->hero_title_one ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="hero_title_two" class="label-style">Hero Title Two</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter hero title two" name="hero_title_two"
                                                        id="hero_title_two" value="{{ $details->hero_title_two ?? null }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="hero_content" class="label-style">Hero Content</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter hero content" name="hero_content"
                                                        id="hero_content" value="{{ $details->hero_content ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="app_url" class="label-style">App URL</label>
                                                    <input type="text" class="form-control" placeholder="Enter app url"
                                                        name="app_url" id="app_url"
                                                        value="{{ $details->app_url ?? null }}">
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <br>
                                        <hr>
                                        <h4 class="pt-4">Features</h4>
                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="feature_title" class="label-style">Feature Title</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature title" name="feature_title"
                                                        id="feature_title" value="{{ $details->feature_title ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="feature_description" class="label-style">Feature
                                                        Description</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature description" name="feature_description"
                                                        id="feature_description"
                                                        value="{{ $details->feature_description ?? null }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="feature_sub_title_one" class="label-style">Feature Sub
                                                        Title One</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature sub title one"
                                                        name="feature_sub_title_one" id="feature_sub_title_one"
                                                        value="{{ $details->feature_sub_title_one ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="feature_sub_desc_one" class="label-style">Feature Sub Desc
                                                        One</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature sub desc one"
                                                        name="feature_sub_desc_one" id="feature_sub_desc_one"
                                                        value="{{ $details->feature_sub_desc_one ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="feature_sub_title_two" class="label-style">Feature Sub
                                                        Title Two</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature sub title two"
                                                        name="feature_sub_title_two" id="feature_sub_title_two"
                                                        value="{{ $details->feature_sub_title_two ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="feature_sub_desc_two" class="label-style">Feature Sub Desc
                                                        Two</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature sub desc two"
                                                        name="feature_sub_desc_two" id="feature_sub_desc_two"
                                                        value="{{ $details->feature_sub_desc_two ?? null }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="feature_sub_title_three" class="label-style">Feature Sub
                                                        Title Three</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature sub title three"
                                                        name="feature_sub_title_three" id="feature_sub_title_three"
                                                        value="{{ $details->feature_sub_title_three ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="feature_sub_desc_three" class="label-style">Feature Sub
                                                        Desc Three</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature sub desc three"
                                                        name="feature_sub_desc_three" id="feature_sub_desc_three"
                                                        value="{{ $details->feature_sub_desc_three ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="feature_sub_title_four" class="label-style">Feature Sub
                                                        Title Four</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature sub title four"
                                                        name="feature_sub_title_four" id="feature_sub_title_four"
                                                        value="{{ $details->feature_sub_title_four ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="feature_sub_desc_four" class="label-style">Feature Sub
                                                        Desc Four</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter feature sub desc four"
                                                        name="feature_sub_desc_four" id="feature_sub_desc_four"
                                                        value="{{ $details->feature_sub_desc_four ?? null }}">
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <br>
                                        <hr>
                                        <h4 class="pt-4">How It Works</h4>
                                        <div class="row pt-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="hiw_title_one" class="label-style">HIW Title One</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter HIW title one" name="hiw_title_one"
                                                        id="hiw_title_one" value="{{ $details->hiw_title_one ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="hiw_desc_one" class="label-style">HIW Desc One</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter HIW desc one" name="hiw_desc_one"
                                                        id="hiw_desc_one" value="{{ $details->hiw_desc_one ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="hiw_title_two" class="label-style">HIW Title Two</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter HIW title two" name="hiw_title_two"
                                                        id="hiw_title_two" value="{{ $details->hiw_title_two ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="hiw_desc_two" class="label-style">HIW Desc Two</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter HIW desc two" name="hiw_desc_two"
                                                        id="hiw_desc_two" value="{{ $details->hiw_desc_two ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="hiw_title_three" class="label-style">HIW Title
                                                        Three</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter HIW title three" name="hiw_title_three"
                                                        id="hiw_title_three"
                                                        value="{{ $details->hiw_title_three ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="hiw_desc_three" class="label-style">HIW Desc Three</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter HIW desc three" name="hiw_desc_three"
                                                        id="hiw_desc_three"
                                                        value="{{ $details->hiw_desc_three ?? null }}">
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <br>
                                        <hr>
                                        <h4 class="pt-4">Goal</h4>
                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="goal_title" class="label-style">Goal Title</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter goal title" name="goal_title" id="goal_title"
                                                        value="{{ $details->goal_title ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="goal_content" class="label-style">Goal Content</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter goal content" name="goal_content"
                                                        id="goal_content" value="{{ $details->goal_content ?? null }}">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <br>
                                        <br>
                                        <hr>
                                        <h4 class="pt-4">FAQs</h4>
                                        <div class="row pt-3">
                                            <div class="col-md-12">
                                                <label class="label-style">Question - Answer</label>
                                                <div id="faqs-wrapper">
                                                    @php
                                                        $faqs = [];
                                                        if (!empty($details->faqs)) {
                                                            $faqs = json_decode($details->faqs, true) ?? [];
                                                        }
                                                    @endphp
                                                    @if (!empty($faqs))
                                                        @foreach ($faqs as $index => $faq)
                                                            <div class="faq-item mb-3 d-flex align-items-start gap-2">
                                                                <input type="text"
                                                                    name="faqs[{{ $index }}][question]"
                                                                    class="form-control me-2" placeholder="Question"
                                                                    value="{{ $faq['question'] ?? '' }}"
                                                                    style="width:45%">
                                                                <input type="text"
                                                                    name="faqs[{{ $index }}][answer]"
                                                                    class="form-control" placeholder="Answer"
                                                                    value="{{ $faq['answer'] ?? '' }}" style="width:45%">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm remove-faq">Remove</button>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="faq-item mb-3 d-flex align-items-start gap-2">
                                                            <input type="text" name="faqs[0][question]"
                                                                class="form-control me-2" placeholder="Question"
                                                                style="width:45%">
                                                            <input type="text" name="faqs[0][answer]"
                                                                class="form-control" placeholder="Answer"
                                                                style="width:45%">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-faq">Remove</button>
                                                        </div>
                                                    @endif
                                                </div>
                                                <button type="button" id="add-faq"
                                                    class="btn btn-primary btn-sm mt-2">Add FAQ</button>
                                            </div>
                                        </div>
                                        @push('script')
                                            <script>
                                                $(document).ready(function() {
                                                    let faqIndex = $('#faqs-wrapper .faq-item').length;
                                                    $('#add-faq').click(function() {
                                                        let html = `
                                                        <div class="faq-item mb-3 d-flex align-items-start gap-2">
                                                            <input type="text" name="faqs[${faqIndex}][question]" class="form-control me-2" placeholder="Question" style="width:45%">
                                                            <input type="text" name="faqs[${faqIndex}][answer]" class="form-control" placeholder="Answer" style="width:45%">
                                                            <button type="button" class="btn btn-danger btn-sm remove-faq">Remove</button>
                                                        </div>
                                                    `;
                                                        $('#faqs-wrapper').append(html);
                                                        faqIndex++;
                                                    });
                                                    $(document).on('click', '.remove-faq', function() {
                                                        $(this).closest('.faq-item').remove();
                                                    });
                                                });
                                            </script>
                                        @endpush --}}

                                        <br>
                                        <br>
                                        <hr>
                                        <h4 class="pt-4">Testimonials</h4>
                                        <div id="testimonials-wrapper">
                                            @php
                                                $testimonials = [];
                                                if (!empty($details->testimonials)) {
                                                    $testimonials = json_decode($details->testimonials, true) ?? [];
                                                }
                                            @endphp
                                            @if (!empty($testimonials))
                                                @foreach ($testimonials as $index => $testimonial)
                                                    <div class="row pt-3 testimonial-item align-items-end mb-2">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="label-style">Testimonial Title One</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Enter testimonial title one"
                                                                    name="testimonials[{{ $index }}][title_one]"
                                                                    value="{{ $testimonial['title_one'] ?? '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="label-style">Testimonial Title Two</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Enter testimonial title two"
                                                                    name="testimonials[{{ $index }}][title_two]"
                                                                    value="{{ $testimonial['title_two'] ?? '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="label-style">Testimonial Title Content</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Enter testimonial title content"
                                                                    name="testimonials[{{ $index }}][title_content]"
                                                                    value="{{ $testimonial['title_content'] ?? '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label class="label-style">Image</label>
                                                                <input type="file" class="form-control testimonial-image-input"
                                                                    name="testimonials[{{ $index }}][image]"
                                                                    accept="image/*"
                                                                    onchange="previewTestimonialImage(this, 'testimonial-image-preview-{{ $index }}')">
                                                                <img id="testimonial-image-preview-{{ $index }}"
                                                                    src="{{ !empty($testimonial['image']) ? asset('uploads/testimonials/' . $testimonial['image']) : '' }}"
                                                                    alt="Image Preview"
                                                                    style="max-width: 80px; max-height: 80px; margin-top: 5px; {{ empty($testimonial['image']) ? 'display:none;' : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-testimonial mb-2">Remove</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="row pt-3 testimonial-item align-items-end mb-2">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="label-style">Testimonial Title One</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter testimonial title one"
                                                                name="testimonials[0][title_one]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="label-style">Testimonial Title Two</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter testimonial title two"
                                                                name="testimonials[0][title_two]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="label-style">Testimonial Title Content</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter testimonial title content"
                                                                name="testimonials[0][title_content]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="label-style">Image</label>
                                                            <input type="file" class="form-control testimonial-image-input"
                                                                name="testimonials[0][image]"
                                                                accept="image/*"
                                                                onchange="previewTestimonialImage(this, 'testimonial-image-preview-0')">
                                                            <img id="testimonial-image-preview-0"
                                                                src=""
                                                                alt="Image Preview"
                                                                style="max-width: 80px; max-height: 80px; margin-top: 5px; display:none;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-testimonial mb-2">Remove</button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" id="add-testimonial"
                                            class="btn btn-primary btn-sm mt-2">Add Testimonial</button>

                                        @push('script')
                                        <script>
                                            function previewTestimonialImage(input, previewId) {
                                                const preview = document.getElementById(previewId);
                                                if (input.files && input.files[0]) {
                                                    const reader = new FileReader();
                                                    reader.onload = function(e) {
                                                        preview.src = e.target.result;
                                                        preview.style.display = 'block';
                                                    }
                                                    reader.readAsDataURL(input.files[0]);
                                                } else {
                                                    preview.src = '';
                                                    preview.style.display = 'none';
                                                }
                                            }

                                            $(document).ready(function() {
                                                let testimonialIndex = $('#testimonials-wrapper .testimonial-item').length;
                                                $('#add-testimonial').click(function() {
                                                    let html = `
                                                    <div class="row pt-3 testimonial-item align-items-end mb-2">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="label-style">Testimonial Title One</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Enter testimonial title one"
                                                                    name="testimonials[${testimonialIndex}][title_one]">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="label-style">Testimonial Title Two</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Enter testimonial title two"
                                                                    name="testimonials[${testimonialIndex}][title_two]">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="label-style">Testimonial Title Content</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Enter testimonial title content"
                                                                    name="testimonials[${testimonialIndex}][title_content]">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label class="label-style">Image</label>
                                                                <input type="file" class="form-control testimonial-image-input"
                                                                    name="testimonials[${testimonialIndex}][image]"
                                                                    accept="image/*"
                                                                    onchange="previewTestimonialImage(this, 'testimonial-image-preview-${testimonialIndex}')">
                                                                <img id="testimonial-image-preview-${testimonialIndex}"
                                                                    src=""
                                                                    alt="Image Preview"
                                                                    style="max-width: 80px; max-height: 80px; margin-top: 5px; display:none;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button" class="btn btn-danger btn-sm remove-testimonial mb-2">Remove</button>
                                                        </div>
                                                    </div>
                                                    `;
                                                    $('#testimonials-wrapper').append(html);
                                                    testimonialIndex++;
                                                });
                                                $(document).on('click', '.remove-testimonial', function() {
                                                    $(this).closest('.testimonial-item').remove();
                                                });
                                            });
                                        </script>
                                        @endpush

                                        <br>
                                        <br>
                                        <hr>
                                        <h4 class="pt-4">Footer Section</h4>

                                        <div class="row pt-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="testimonial_title_one" class="label-style">Footer
                                                        Title One</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter testimonial title one"
                                                        name="testimonial_title_one" id="testimonial_title_one"
                                                        value="{{ $details->testimonial_title_one ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="testimonial_title_two" class="label-style">Footer
                                                        Title Two</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter testimonial title two"
                                                        name="testimonial_title_two" id="testimonial_title_two"
                                                        value="{{ $details->testimonial_title_two ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="testimonial_title_content" class="label-style">Footer
                                                        Title Content</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter testimonial title content"
                                                        name="testimonial_title_content" id="testimonial_title_content"
                                                        value="{{ $details->testimonial_title_content ?? null }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="testimonial_ios_app_link" class="label-style">Testimonial
                                                        iOS App Link</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter iOS app link" name="testimonial_ios_app_link"
                                                        id="testimonial_ios_app_link"
                                                        value="{{ $details->testimonial_ios_app_link ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="testimonial_android_app_link"
                                                        class="label-style">Testimonial Android App Link</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Android app link"
                                                        name="testimonial_android_app_link"
                                                        id="testimonial_android_app_link"
                                                        value="{{ $details->testimonial_android_app_link ?? null }}">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <br>
                                        <br>
                                        <hr>
                                        <h4 class="pt-4">Footer</h4> --}}
                                        {{-- <div class="row pt-3">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="footer_hours_desc" class="label-style">Footer Hours
                                                        Description</label>
                                                    <textarea class="form-control" placeholder="Enter footer hours description" name="footer_hours_desc"
                                                        id="footer_hours_desc" rows="4">{{ $details->footer_hours_desc ?? null }}</textarea>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <div class="button add-btn-div-save-style pt-4">
                                            <button type="submit" id="submitBtn" class="btn btn-dark">
                                                <span
                                                    class="indicator-label">{{ !empty($details) ? 'Update' : 'Save' }}</span>
                                                <span class="indicator-progress">Please wait...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script src="{{ asset('assets/js/custom_js/cdn/ckeditor.js') }}"></script>
        <script>
            function previewImage(input, previewId) {
                const preview = document.getElementById(previewId);
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    preview.src = '';
                    preview.style.display = 'none';
                }
            }
            $(document).ready(function() {
                let footerHoursDesc;
                ClassicEditor
                    .create(document.querySelector('#footer_hours_desc'))
                    .then(editor => {
                        footerHoursDesc = editor;
                        console.log('Editor initialized', editor);
                    })
                    .catch(error => {
                        console.error('Error initializing CKEditor', error);
                    });

                $(document).on("click", "#submitBtn", function() {
                    document.getElementById('footer_hours_desc').value = footerHoursDesc.getData();
                });
            });
        </script>
    @endpush
@endsection
