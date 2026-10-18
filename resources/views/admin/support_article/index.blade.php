@extends('layout.app')

@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6" style="border-bottom: 1px solid #f3f4f6;">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack flex-wrap gap-4">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bolder fs-2 flex-column justify-content-center my-0">
                Support Articles
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Help & Support</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-dark">Support Articles</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3 flex-wrap">
            <button type="button" id="btnToggleAllAccordions" class="btn btn-sm btn-light d-flex align-items-center px-4 py-3" style="border-radius: 8px; font-weight: 600;">
                <i class="fa-solid fa-arrows-up-down fs-7 me-2"></i>
                <span id="toggleAllText">Expand All</span>
            </button>
            <a href="#" class="btn btn-sm wemu-btn-primary d-flex align-items-center px-4 py-3"
                data-bs-toggle="modal" data-bs-target="#kt_modal_add_article"
                style="border-radius: 8px; font-weight: 600;">
                <i class="fa-solid fa-plus fs-6 me-2"></i> Add Support Article
            </a>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid pt-8">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Stat Summary Cards -->
        {{-- <div class="row g-5 mb-8">
            <div class="col-xl-3 col-sm-6">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 16px; background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);">
                    <div class="card-body p-6 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-semibold fs-7 d-block mb-1">Total Articles</span>
                            <span class="fs-2hx fw-bolder text-gray-900 lh-1">{{ $totalCount }}</span>
                            <span class="d-block fs-8 text-primary fw-bold mt-2">
                                <i class="fa-solid fa-book-open me-1"></i> Across all categories
                            </span>
                        </div>
                        <div class="w-55px h-55px rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center text-primary fs-2">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 16px; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                    <div class="card-body p-6 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-semibold fs-7 d-block mb-1">Active Articles</span>
                            <span class="fs-2hx fw-bolder text-gray-900 lh-1">{{ $activeCount }}</span>
                            <span class="d-block fs-8 text-success fw-bold mt-2">
                                <i class="fa-solid fa-circle-check me-1"></i> Published & visible
                            </span>
                        </div>
                        <div class="w-55px h-55px rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center text-success fs-2">
                            <i class="fa-solid fa-toggle-on"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 16px; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
                    <div class="card-body p-6 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-semibold fs-7 d-block mb-1">Inactive / Drafts</span>
                            <span class="fs-2hx fw-bolder text-gray-900 lh-1">{{ $inactiveCount }}</span>
                            <span class="d-block fs-8 text-warning fw-bold mt-2">
                                <i class="fa-solid fa-eye-slash me-1"></i> Hidden from users
                            </span>
                        </div>
                        <div class="w-55px h-55px rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center text-warning fs-2">
                            <i class="fa-solid fa-toggle-off"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 16px; background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);">
                    <div class="card-body p-6 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-semibold fs-7 d-block mb-1">Categories</span>
                            <span class="fs-2hx fw-bolder text-gray-900 lh-1">{{ count($allCategories) }}</span>
                            <span class="d-block fs-8 text-info fw-bold mt-2">
                                <i class="fa-solid fa-folder me-1"></i> Knowledge domains
                            </span>
                        </div>
                        <div class="w-55px h-55px rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center text-info fs-2">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Filter Card -->
        <div class="card wemu-filter-card mb-8">
            <div class="card-body p-5">
                <form action="{{ route('admin.support-article.list') }}" method="GET" class="d-flex align-items-center justify-content-between flex-wrap gap-4 w-100">
                    <div class="d-flex align-items-center gap-4 flex-grow-1 flex-wrap">
                        <div class="position-relative w-100 max-w-400px" style="flex: 1; max-width: 380px;">
                            <i class="fa-solid fa-magnifying-glass fs-5 text-gray-500 position-absolute top-50 translate-middle-y ms-4"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid wemu-search-input ps-12"
                                placeholder="Search articles or answers..." style="height: 44px;" />
                        </div>
                        <select name="category" class="form-select form-select-solid wemu-select w-100 max-w-250px"
                            style="flex: 1; max-width: 260px; height: 44px;">
                            <option value="">All Categories</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="form-select form-select-solid wemu-select w-100 max-w-200px"
                            style="flex: 1; max-width: 200px; height: 44px;">
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
                        <a href="{{ route('admin.support-article.list') }}" class="btn btn-light px-6 text-gray-600 fw-bold hover-scale d-flex align-items-center"
                            style="height: 44px; border-radius: 8px; background-color: #f9f9fb; border: 1px solid #f1f1f4;">
                            Reset
                        </a>
                    </div>
                </form>

                <!-- Quick Category Pill Tags -->
                <div class="d-flex align-items-center gap-2 flex-wrap mt-4 pt-3" style="border-top: 1px dashed #e5e7eb;">
                    <span class="fs-8 fw-bold text-muted text-uppercase me-2">Quick Filter:</span>
                    <a href="{{ route('admin.support-article.list') }}"
                        class="badge {{ empty(request('category')) ? 'bg-primary text-white' : 'badge-light text-gray-700' }} px-3 py-2 fs-7 fw-semibold rounded-pill text-decoration-none">
                        All ({{ $totalCount }})
                    </a>
                    @foreach($allCategories as $cat)
                        @php
                            $catCount = \App\Models\SupportArticle::where('category', $cat)->count();
                        @endphp
                        <a href="{{ route('admin.support-article.list', array_merge(request()->except('category'), ['category' => $cat])) }}"
                            class="badge {{ request('category') === $cat ? 'bg-primary text-white' : 'badge-light text-gray-700' }} px-3 py-2 fs-7 fw-semibold rounded-pill text-decoration-none hover-scale">
                            {{ $cat }} ({{ $catCount }})
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Accordion Cards Grouped by Category -->
        @if($articlesByCategory->count() > 0)
            @foreach($articlesByCategory as $categoryName => $catArticles)
                @php
                    $catSlug = Str::slug($categoryName);
                    // Determine an iconic icon for common categories
                    $catIcon = 'fa-solid fa-folder-open';
                    $catColor = '#6366f1';
                    if (stripos($categoryName, 'Payment') !== false || stripos($categoryName, 'Billing') !== false) {
                        $catIcon = 'fa-solid fa-credit-card';
                        $catColor = '#0ea5e9';
                    } elseif (stripos($categoryName, 'Account') !== false || stripos($categoryName, 'Profile') !== false) {
                        $catIcon = 'fa-solid fa-user-gear';
                        $catColor = '#8b5cf6';
                    } elseif (stripos($categoryName, 'Security') !== false || stripos($categoryName, 'Privacy') !== false) {
                        $catIcon = 'fa-solid fa-shield-halved';
                        $catColor = '#10b981';
                    }
                @endphp

                <div class="wemu-glass-card p-0 mb-8 shadow-sm">
                    <!-- Category Card Header -->
                    <div class="d-flex align-items-center justify-content-between px-7 py-5 flex-wrap gap-3"
                        style="border-bottom: 1px solid rgba(0,0,0,0.05); background: linear-gradient(90deg, rgba(249,250,251,0.8) 0%, rgba(255,255,255,1) 100%);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="w-40px h-40px rounded-3 d-flex align-items-center justify-content-center text-white"
                                style="background: {{ $catColor }}; box-shadow: 0 4px 12px {{ $catColor }}33;">
                                <i class="{{ $catIcon }} fs-5"></i>
                            </div>
                            <div>
                                <h3 class="fs-5 fw-bolder text-gray-900 mb-0">{{ $categoryName }}</h3>
                                <span class="fs-8 text-muted fw-semibold">{{ $catArticles->count() }} {{ Str::plural('Question', $catArticles->count()) }} in this section</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <span class="badge badge-light-primary fw-bold px-3 py-2 fs-7">
                                {{ $catArticles->where('is_active', 1)->count() }} Active / {{ $catArticles->where('is_active', 0)->count() }} Inactive
                            </span>
                            <button type="button" class="btn btn-sm btn-light-primary fw-bold d-flex align-items-center px-3 py-2 btn-quick-add"
                                data-category="{{ $categoryName }}" data-bs-toggle="modal" data-bs-target="#kt_modal_add_article"
                                style="border-radius: 8px;">
                                <i class="fa-solid fa-plus fs-7 me-1"></i> Add Question
                            </button>
                        </div>
                    </div>

                    <!-- Accordion Body for Questions & Answers in this Card -->
                    <div class="p-6">
                        <div class="accordion support-accordion" id="accordion_cat_{{ $catSlug }}">
                            @foreach($catArticles as $idx => $article)
                                @php
                                    $itemCollapseId = 'collapse_art_' . $article->id;
                                    $isFirst = ($loop->first && empty(request('search')));
                                @endphp
                                <div class="accordion-item mb-4 border rounded-3 overflow-hidden shadow-none"
                                    style="border-color: #f1f2f6 !important; background: #ffffff;">
                                    
                                    <!-- Accordion Item Header -->
                                    <h2 class="accordion-header" id="heading_art_{{ $article->id }}">
                                        <div class="d-flex align-items-center justify-content-between p-4 px-5 {{ $isFirst ? '' : 'collapsed' }}"
                                            style="cursor: pointer; user-select: none; transition: background-color 0.2s;"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#{{ $itemCollapseId }}"
                                            aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                            aria-controls="{{ $itemCollapseId }}">

                                            <!-- Left: Index + Question Title -->
                                            <div class="d-flex align-items-center gap-3 flex-grow-1 pe-4">
                                                <span class="badge badge-light-dark fw-bolder px-2 py-1 fs-8" style="min-width: 32px; text-align: center;">
                                                    #{{ sprintf('%02d', $article->sort_order ?: ($idx + 1)) }}
                                                </span>
                                                <span class="fs-6 fw-bold text-gray-900 question-title text-hover-primary">
                                                    {{ $article->question }}
                                                </span>
                                            </div>

                                            <!-- Right: Status Toggle + Action Buttons + Chevron -->
                                            <div class="d-flex align-items-center gap-4 flex-shrink-0" onclick="event.stopPropagation();">
                                                <!-- Status Switch -->
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                                        <input class="form-check-input h-20px w-35px isVerified" type="checkbox"
                                                            value="{{ $article->is_active }}"
                                                            {{ $article->is_active ? 'checked="checked"' : '' }}
                                                            data-uuid="{{ $article->uuid }}"
                                                            data-table="support_articles"
                                                            title="Toggle active status" />
                                                    </div>
                                                    <span class="fs-8 fw-bold d-none d-sm-inline" style="color: {{ $article->is_active ? '#10b981' : '#9ca3af' }}; width: 50px;">
                                                        {{ $article->is_active ? 'Active' : 'Draft' }}
                                                    </span>
                                                </div>

                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-sm btn-icon btn-light-primary rounded-circle edit-article-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#kt_modal_edit_article"
                                                    data-id="{{ $article->id }}"
                                                    data-uuid="{{ $article->uuid }}"
                                                    data-category="{{ $article->category }}"
                                                    data-question="{{ $article->question }}"
                                                    data-answer="{{ htmlspecialchars($article->answer, ENT_QUOTES, 'UTF-8') }}"
                                                    data-sort_order="{{ $article->sort_order }}"
                                                    data-is_active="{{ $article->is_active }}"
                                                    title="Edit question & answer">
                                                    <i class="fa-solid fa-pen-to-square fs-7"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <button type="button" class="btn btn-sm btn-icon btn-light-danger rounded-circle deleteData"
                                                    data-table="support_articles"
                                                    data-uuid="{{ $article->uuid }}"
                                                    title="Delete this article">
                                                    <i class="fa-solid fa-trash fs-7"></i>
                                                </button>

                                                <!-- Accordion Expand Indicator Arrow -->
                                                <div class="accordion-arrow-btn text-muted ms-1" style="transition: transform 0.3s ease;">
                                                    <i class="fa-solid fa-chevron-down fs-7"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </h2>

                                    <!-- Accordion Item Content (Answer) -->
                                    <div id="{{ $itemCollapseId }}" class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                                        aria-labelledby="heading_art_{{ $article->id }}"
                                        data-bs-parent="#accordion_cat_{{ $catSlug }}">
                                        <div class="accordion-body px-6 py-5 pt-0">
                                            <div class="p-5 rounded-3" style="background-color: #fbfcfe; border: 1px solid #edf2f7;">
                                                <div class="d-flex align-items-center gap-2 mb-3 text-muted fs-8 fw-semibold text-uppercase">
                                                    <i class="fa-solid fa-comment-dots text-primary me-1"></i> Answer content
                                                </div>
                                                <div class="text-gray-800 fs-6 lh-base article-answer-content">
                                                    {!! $article->answer !!}
                                                </div>
                                                
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-4 mt-4" style="border-top: 1px solid #e9ecef;">
                                                    <div class="d-flex align-items-center gap-3 text-muted fs-8">
                                                        <span><i class="fa-regular fa-clock me-1"></i> Added {{ $article->created_at ? $article->created_at->format('M d, Y') : 'N/A' }}</span>
                                                        @if($article->updated_at && $article->updated_at != $article->created_at)
                                                            <span>• Updated {{ $article->updated_at->diffForHumans() }}</span>
                                                        @endif
                                                    </div>
                                                    <button type="button" class="btn btn-link btn-color-primary btn-active-color-primary fs-8 fw-bold p-0 edit-article-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#kt_modal_edit_article"
                                                        data-id="{{ $article->id }}"
                                                        data-uuid="{{ $article->uuid }}"
                                                        data-category="{{ $article->category }}"
                                                        data-question="{{ $article->question }}"
                                                        data-answer="{{ htmlspecialchars($article->answer, ENT_QUOTES, 'UTF-8') }}"
                                                        data-sort_order="{{ $article->sort_order }}"
                                                        data-is_active="{{ $article->is_active }}">
                                                        <i class="fa-solid fa-pen fs-8 me-1"></i> Quick Edit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="wemu-glass-card text-center py-15 mb-8">
                <div class="w-70px h-70px rounded-circle bg-light-primary text-primary mx-auto mb-4 d-flex align-items-center justify-content-center fs-1">
                    <i class="fa-solid fa-circle-question"></i>
                </div>
                <h3 class="fs-4 fw-bolder text-gray-800 mb-2">No Support Articles Found</h3>
                <p class="text-muted fs-6 mb-6">
                    @if(request('search') || request('category') || request('status') !== null)
                        No questions match your current search or filters. Try adjusting your search query.
                    @else
                        No support articles have been added yet. Click below to create your first article!
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-3">
                    @if(request('search') || request('category') || request('status') !== null)
                        <a href="{{ route('admin.support-article.list') }}" class="btn btn-light px-6 fw-bold">
                            Reset Filters
                        </a>
                    @endif
                    <button type="button" class="btn wemu-btn-primary px-6 fw-bold" data-bs-toggle="modal" data-bs-target="#kt_modal_add_article">
                        <i class="fa-solid fa-plus me-2"></i> Add First Article
                    </button>
                </div>
            </div>
        @endif

    </div>
</div>

<!-- Modal - Add Support Article -->
<div class="modal fade" id="kt_modal_add_article" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content rounded-4">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-3"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form id="kt_modal_add_article_form" class="form formSubmit" action="{{ route('admin.support-article.add') }}" method="POST">
                    @csrf
                    <div class="mb-10 text-center">
                        <h1 class="mb-2 text-dark fw-bolder">Add Support Article</h1>
                        <div class="text-muted fw-semibold fs-6">Create a new support question & answer card.</div>
                    </div>

                    <!-- Category Field -->
                    <div class="d-flex flex-column mb-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Category</span>
                        </label>
                        <select class="form-select form-select-solid" name="category" id="add_category_select" required>
                            <option value="">Select a Category</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                            <option value="__custom__">+ Enter Custom Category...</option>
                        </select>
                        <div id="add_custom_category_div" class="mt-3" style="display: none;">
                            <input type="text" class="form-control form-control-solid" id="add_custom_category_input" placeholder="Type new category name..." />
                        </div>
                    </div>

                    <!-- Question Field -->
                    <div class="d-flex flex-column mb-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Question / Title</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="e.g. How to change payment method details" name="question" required />
                    </div>

                    <!-- Answer Field -->
                    <div class="d-flex flex-column mb-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Answer</span>
                        </label>
                        <textarea class="form-control form-control-solid" id="add_article_answer_ckeditor" rows="6" placeholder="Provide a detailed, helpful answer..." name="answer"></textarea>
                    </div>

                    <!-- Additional Options: Sort order and Active toggle -->
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold mb-2">Sort Order</label>
                            <input type="number" class="form-control form-control-solid" name="sort_order" value="1" min="0" placeholder="Display order (e.g. 1)" />
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch form-check-custom form-check-solid mb-3">
                                <input class="form-check-input h-25px w-45px" type="checkbox" name="is_active" value="1" id="add_is_active" checked />
                                <label class="form-check-label fs-6 fw-bold ms-3" for="add_is_active">
                                    Publish Immediately
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-10">
                        <button type="reset" class="btn btn-light me-3 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn wemu-btn-primary fw-bold px-8">
                            <span class="indicator-label">Save Article</span>
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

<!-- Modal - Edit Support Article -->
<div class="modal fade" id="kt_modal_edit_article" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content rounded-4">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-3"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form id="edit_article_form" class="form formSubmit" action="{{ route('admin.support-article.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="edit_article_id" value="">
                    
                    <div class="mb-10 text-center">
                        <h1 class="mb-2 text-dark fw-bolder">Edit Support Article</h1>
                        <div class="text-muted fw-semibold fs-6">Update question, category, or answer content.</div>
                    </div>

                    <!-- Category Field -->
                    <div class="d-flex flex-column mb-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Category</span>
                        </label>
                        <select class="form-select form-select-solid" name="category" id="edit_category_select" required>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                            <option value="__custom__">+ Enter Custom Category...</option>
                        </select>
                        <div id="edit_custom_category_div" class="mt-3" style="display: none;">
                            <input type="text" class="form-control form-control-solid" id="edit_custom_category_input" placeholder="Type custom category name..." />
                        </div>
                    </div>

                    <!-- Question Field -->
                    <div class="d-flex flex-column mb-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Question / Title</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" name="question" id="edit_article_question" required />
                    </div>

                    <!-- Answer Field -->
                    <div class="d-flex flex-column mb-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Answer</span>
                        </label>
                        <textarea class="form-control form-control-solid" id="edit_article_answer_ckeditor" rows="6" name="answer"></textarea>
                    </div>

                    <!-- Additional Options -->
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold mb-2">Sort Order</label>
                            <input type="number" class="form-control form-control-solid" name="sort_order" id="edit_article_sort_order" min="0" />
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch form-check-custom form-check-solid mb-3">
                                <input class="form-check-input h-25px w-45px" type="checkbox" name="is_active" value="1" id="edit_article_is_active" />
                                <label class="form-check-label fs-6 fw-bold ms-3" for="edit_article_is_active">
                                    Active / Visible
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-10">
                        <button type="button" class="btn btn-light me-3 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn wemu-btn-primary fw-bold px-8">
                            <span class="indicator-label">Update Article</span>
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
            if (!document.getElementById(elementId)) return;
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
                    console.error('CKEditor init error:', err);
                });
        }

        initCKEditor('add_article_answer_ckeditor', (editor) => addEditor = editor);
        initCKEditor('edit_article_answer_ckeditor', (editor) => editEditor = editor);

        // Toggle custom category input in Add Modal
        const addCategorySelect = document.getElementById('add_category_select');
        const addCustomDiv = document.getElementById('add_custom_category_div');
        const addCustomInput = document.getElementById('add_custom_category_input');

        if (addCategorySelect && addCustomDiv) {
            addCategorySelect.addEventListener('change', function () {
                if (this.value === '__custom__') {
                    addCustomDiv.style.display = 'block';
                    addCustomInput.setAttribute('required', 'required');
                    addCustomInput.focus();
                } else {
                    addCustomDiv.style.display = 'none';
                    addCustomInput.removeAttribute('required');
                }
            });

            addCustomInput.addEventListener('input', function () {
                if (addCategorySelect.value === '__custom__') {
                    // Update hidden or dynamically set
                    addCategorySelect.querySelector('option[value="__custom__"]').value = this.value;
                }
            });
        }

        // Quick Add from Category Header button
        const quickAddBtns = document.querySelectorAll('.btn-quick-add');
        quickAddBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const cat = this.getAttribute('data-category');
                if (addCategorySelect && cat) {
                    addCategorySelect.value = cat;
                    if (addCustomDiv) addCustomDiv.style.display = 'none';
                }
            });
        });

        // Edit button handler
        const editButtons = document.querySelectorAll('.edit-article-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const category = this.getAttribute('data-category');
                const question = this.getAttribute('data-question');
                const rawAnswer = this.getAttribute('data-answer');
                const sortOrder = this.getAttribute('data-sort_order');
                const isActive = this.getAttribute('data-is_active');

                // Decode HTML entities
                const doc = new DOMParser().parseFromString(rawAnswer, 'text/html');
                const answer = doc.body.textContent || rawAnswer;

                document.getElementById('edit_article_id').value = id;
                document.getElementById('edit_article_question').value = question;
                document.getElementById('edit_article_sort_order').value = sortOrder || 0;
                document.getElementById('edit_article_is_active').checked = (isActive == 1 || isActive === '1');

                // Set category
                const editSelect = document.getElementById('edit_category_select');
                let foundOption = false;
                for (let i = 0; i < editSelect.options.length; i++) {
                    if (editSelect.options[i].value === category) {
                        editSelect.selectedIndex = i;
                        foundOption = true;
                        break;
                    }
                }
                if (!foundOption) {
                    const newOpt = new Option(category, category, true, true);
                    editSelect.add(newOpt, editSelect.options[0]);
                }

                document.getElementById('edit_article_answer_ckeditor').value = answer;
                if (editEditor) {
                    editEditor.setData(answer);
                }
            });
        });

        // Expand All / Collapse All Accordions Button
        const toggleAllBtn = document.getElementById('btnToggleAllAccordions');
        const toggleAllText = document.getElementById('toggleAllText');
        let allExpanded = false;

        if (toggleAllBtn) {
            toggleAllBtn.addEventListener('click', function () {
                const collapseElements = document.querySelectorAll('.support-accordion .accordion-collapse');
                const headerButtons = document.querySelectorAll('.support-accordion .accordion-header > div');

                allExpanded = !allExpanded;

                collapseElements.forEach(el => {
                    const bsCollapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                    if (allExpanded) {
                        bsCollapse.show();
                    } else {
                        bsCollapse.hide();
                    }
                });

                headerButtons.forEach(btn => {
                    if (allExpanded) {
                        btn.classList.remove('collapsed');
                        btn.setAttribute('aria-expanded', 'true');
                    } else {
                        btn.classList.add('collapsed');
                        btn.setAttribute('aria-expanded', 'false');
                    }
                });

                toggleAllText.textContent = allExpanded ? 'Collapse All' : 'Expand All';
            });
        }

        // Chevron rotation animation on collapse toggle
        document.querySelectorAll('.support-accordion .accordion-collapse').forEach(el => {
            el.addEventListener('show.bs.collapse', function () {
                const header = this.previousElementSibling.querySelector('.accordion-arrow-btn i');
                if (header) {
                    header.style.transform = 'rotate(180deg)';
                }
            });
            el.addEventListener('hide.bs.collapse', function () {
                const header = this.previousElementSibling.querySelector('.accordion-arrow-btn i');
                if (header) {
                    header.style.transform = 'rotate(0deg)';
                }
            });
        });
    });
</script>
<style>
    .support-accordion .accordion-header > div:hover {
        background-color: #f8fafc !important;
    }
    .support-accordion .accordion-item {
        transition: all 0.2s ease-in-out;
    }
    .support-accordion .accordion-item:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .article-answer-content p:last-child {
        margin-bottom: 0;
    }
    .article-answer-content ul, .article-answer-content ol {
        margin-bottom: 0.5rem;
        padding-left: 1.5rem;
    }
    .article-answer-content strong {
        color: #1e293b;
    }
</style>
@endpush
@endsection
