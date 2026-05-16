@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar whitee-bg  " class="app-toolbar py-3 py-lg-6" style="background-color:#fff">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            User Details</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.user.list') }}">User</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                Details
                            </li>
                        </ul>
                    </div>

                    {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <button type="button" class="btn btn-dark addDetail" data-bs-toggle="modal"
                                data-bs-target="#addIncomeMdl">Add
                                Income</button>
                        </div>
                    </div> --}}

                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card mt-4 p-4 with-bg">
                        <h5>User Details -></h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Name : </label>
                                <b>{{ $detail->name ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Email : </label>
                                <b>{{ $detail->email ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Mobile Number : </label>
                                <b>{{ getPhoneCode($detail->id) }}{{ $detail->mobile_number ?? 'N/A' }}</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Total Item Expense Till Date : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemExpense->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Previous Month's Item Expense : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemPreviousMonthExpense->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">This Month's Item Expense : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemCurrentMonthExpense->sum('price') ?? 'N/A' }}</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Total Item Till Date : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItem->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Previous Month's Item : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemPreviousMonth->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">This Month's Item : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemCurrentMonth->sum('price') ?? 'N/A' }}</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Total Item Count Till Date : </label>
                                <b>{{ $detail->userItem->count() ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Previous Month's Item Count : </label>
                                <b>{{ $detail->userItemPreviousMonth->count() ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">This Month's Item Count : </label>
                                <b>{{ $detail->userItemCurrentMonth->count() ?? 'N/A' }}</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Total Expense Till Date : </label>
                                <b>{{ getCurrency() }}{{ $detail->userExpense->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Previous Month's Expense : </label>
                                <b>{{ getCurrency() }}{{ $detail->userExpensePreviousMonth->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">This Month's Expense : </label>
                                <b>{{ getCurrency() }}{{ $detail->userExpenseCurrentMonth->sum('price') ?? 'N/A' }}</b>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4 p-4 with-bg">
                        <h5>Subscription -></h5>
                        <hr>
                        <div class="row">
                            @php
                                $userSubscription = userSubscription($detail->id);
                            @endphp
                            @if($userSubscription)
                                <div class="col-md-3">
                                    <label for="">Plan Name : </label>
                                    <b>{{ $userSubscription->subscription?->title ?? 'N/A' }}</b>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Price : </label>
                                    <b>{{ getCurrency() }}{{ $userSubscription->subscription?->price ?? 'N/A' }}</b>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Start Date : </label>
                                    <b>{{ $userSubscription->start_date ?? 'N/A' }}</b>
                                </div>
                                <div class="col-md-3">
                                    <label for="">End Date : </label>
                                    <b>{{ $userSubscription->end_date ?? 'N/A' }}</b>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Total Activity : </label>
                                    <b>{{ $userSubscription->total_activity_count ?? 'N/A' }}</b>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Used Activity  : </label>
                                    <b>{{ $userSubscription->used_activity_count ?? 'N/A' }}</b>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Remaining Activity  : </label>
                                    <b>{{ $userSubscription->remaining_activity_count ?? 'N/A' }}</b>
                                </div>
                            @else
                                <div class="col-md-12">
                                    <label for="">No Subscription Found</label>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card mt-4 p-4 with-bg">
                        <h5>User Budget of {{ \Carbon\Carbon::now()->subMonth()->format('F') }},
                            {{ \Carbon\Carbon::now()->subMonth()->year }} -></h5>
                        <hr>
                        <div class="row">
                            @forelse($userPreviousMonthBudget as $budgetPrev)
                                <div class="col-md-6">
                                    <label for="">Category : </label>
                                    <b>{{ $budgetPrev['title'] ?? 'N/A' }}</b>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Budget : </label>
                                    <b>{{ getCurrency() }}{{ $budgetPrev['total_budget'] ?? 'N/A' }}</b>
                                </div>
                            @empty
                                <div class="col-md-12">
                                    <label for="">No Budget Found</label>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card mt-4 p-4 with-bg">
                        <h5>User Budget of {{ \Carbon\Carbon::now()->format('F') }}, {{ date('Y') }} -></h5>
                        <hr>
                        <div class="row">
                            @forelse($userCurrentMonthBudget as $budget)
                                <div class="col-md-6">
                                    <label for="">Category : </label>
                                    <b>{{ $budget['title'] ?? 'N/A' }}</b>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Budget : </label>
                                    <b>{{ getCurrency() }}{{ $budget['total_budget'] ?? 'N/A' }}</b>
                                </div>
                            @empty
                                <div class="col-md-12">
                                    <label for="">No Budget Found</label>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card mt-4 p-4 with-bg">
                        <h5>User Goals -></h5>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card mt-4 p-4">
                                    <h5 class="margin-left">{{ $detail->name ?? 'N/A' }}'s Goal Details -></h5>
                                    <hr>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable"
                                                id="kt_customers_table">
                                                <thead>
                                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                        <th>Sl No</th>
                                                        <th>Name</th>
                                                        <th>Service Frequency</th>
                                                        <th>Image</th>
                                                        {{-- <th>Link</th> --}}
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Is Completed</th>
                                                        <th class="text-end min-w-70px">Task</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="fw-semibold text-gray-600">
                                                    @forelse ($detail->userGoals ?? [] as $goal)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $goal->name ?? 'N/A' }}</td>
                                                            <td>{{ $goal->serviceFrequency->name ?? 'N/A' }}</td>
                                                            <td><img src="{{ $goal->image_path ?? 'N/A' }}" alt="Image"
                                                                    width="50"></td>
                                                            {{-- <td><a href="{{ $goal->link ?? '#' }}"
                                                                    target="_blank">{{ $goal->link ?? 'N/A' }}</a></td> --}}
                                                            <td>{{ \Carbon\Carbon::parse($goal->start_date)->format('jS M, Y') ?? 'N/A' }}
                                                            </td>
                                                            <td>{{ \Carbon\Carbon::parse($goal->end_date)->format('jS M, Y') ?? 'N/A' }}
                                                            </td>
                                                            <td>{{ $goal->is_completed ? 'Yes' : 'No' }}</td>
                                                            <td><a class="viewTask" href="Javascript:void(0)"
                                                                    data-bs-toggle="modal" data-bs-target="#viewtask"
                                                                    data-detail="{{ json_encode($goal) }}" data-subtask="{{ json_encode($goal->tasks) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9">No Data Found</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            {{-- {!! $users->withQueryString()->links('pagination::bootstrap-5') !!} --}}
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
        <script>
            $(document).on("click", ".viewTask", function() {
                const detail = $(this).data('detail');
                $('#task-image').attr('src', detail.image_path ?? 'N/A');
                $('#task-name').html(detail.name ?? 'N/A');
                $('#task-link').attr('href', detail.link ?? '#').html(detail.link ?? 'N/A');
                $('#task-start-date').html(detail.start_date ? moment(detail.start_date).format('Do MMM, YYYY') : 'N/A');
                $('#task-end-date').html(detail.end_date ? moment(detail.end_date).format('Do MMM, YYYY') : 'N/A');
                $('#task-is-completed').html(detail.is_completed ? 'Yes' : 'No');
                $('#task-is-active').html(detail.is_active ? 'Yes' : 'No');
                const task = $(this).data('subtask');
                let taskCards = '';
                task.forEach(t => {
                    taskCards += `
                        <div class="col-md-6 card mt-4 p-4 shadow">
                            <h5>${t.name}</h5>
                            <p><strong>Task Type:</strong> ${t.task_type == 2 ? 'Repeated Task' : 'One Time Task'}</p>
                            <p><strong>Start Date:</strong> ${moment(t.start_date).format('Do MMM, YYYY')}</p>
                            <p><strong>End Date:</strong> ${moment(t.end_date).format('Do MMM, YYYY')}</p>
                            <p><strong>Is Completed:</strong> ${t.is_completed ? 'Yes' : 'No'}</p>
                            <p><strong>Is Active:</strong> ${t.is_active ? 'Yes' : 'No'}</p>
                            <p><strong>Comments:</strong> ${t.comments ?? 'N/A'}</p>
                            <p><strong>URL:</strong>
                                ${t.url ? `<a href="${t.url}" target="_blank">${t.url}</a>` : 'N/A'}
                            </p>
                        </div>
                    `;
                });
                $('#task-cards-container').html(taskCards);
            });
        </script>
    @endpush
@endsection

@section('pageModal')
    <!-- Modal -->
    <div class="modal fade modal-lg" id="viewtask" tabindex="-1" aria-labelledby="viewtaskLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewtaskLabel">Goal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4 text-center">
                        <h5 class="text-primary">Goal Details -></h5>
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <img id="task-image" src="" alt="Image" class="rounded-circle me-3" width="50">
                            <div>
                                <h6 id="task-name" class="mb-0"></h6>
                                <small id="task-service-frequency-id" class="text-muted"></small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <strong>Start Date:</strong> <span id="task-start-date"></span>
                        </div>
                        <div class="mb-2">
                            <strong>End Date:</strong> <span id="task-end-date"></span>
                        </div>
                        <div class="mb-2">
                            <strong>Is Completed:</strong> <span id="task-is-completed"></span>
                        </div>
                    </div>
                    <h5 class="text-primary">Task List -></h5>
                    <div id="task-cards-container" class="row g-3"></div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
@endsection
