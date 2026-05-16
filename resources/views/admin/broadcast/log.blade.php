@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card">
                        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                    <!--begin::Title-->
                                    <h1
                                        class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                        Broadcast List</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.broadcast.list') }}">Broadcast</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">Broadcast</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div class="card-body pt-0">
                            <div class="mb-5">
                                <h3 class="fw-bold">Broadcast Details</h3>
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Title:</strong> {{ $details['title'] }}</li>
                                    <li class="list-group-item"><strong>Body:</strong> {{ $details['body'] }}</li>
                                    <li class="list-group-item"><strong>File Path:</strong> {{ $details['file_path'] ?? 'N/A' }}</li>
                                </ul>
                            </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th>Sl No</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            {{-- <th>Created At</th>
                                            <th>Updated At</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        @forelse ($details->broadcastLogs ?? [] as $log)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ \Carbon\Carbon::parse($log['date'])->format('F j, Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($log['time'])->format('g:i A') }}</td>
                                                {{-- <td>{{ $log['created_at'] }}</td>
                                                <td>{{ $log['updated_at'] }}</td> --}}
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No logs available</td>
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
    </div>

    @push('script')
        <script></script>
    @endpush
@endsection
