@extends('layouts.app', [
    'pageTitle' => 'Integration & APIs - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Integration & APIs</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Integration & APIs</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <ul class="nav nav-pills justify-content-start nav-style-3 mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page"
                                href="#running-processes" aria-selected="true"><i class="bx bx-chip"></i> Running
                                Processes</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#apis-tab"
                                aria-selected="true"><i class="bx bx-link"></i> APIs</a>
                        </li> --}}
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="running-processes" role="tabpanel">
                            <div class="card custom-card">
                                <div class="card-header d-flex justify-content-between align-items-center mb-2">
                                    <h2 class="card-title mb-0">
                                        <i class="bx bx-server me-2 text-dark"></i>System Process Management Console
                                    </h2>
                                    <div class="text-muted">
                                        Last Refreshed: {{ now()->format('Y-m-d H:i:s') }}
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <table id="processTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Process Name</th>
                                                <th>Status</th>
                                                <th>Process ID</th>
                                                <th>Started</th>
                                                <th>Memory Usage</th>
                                                <th>CPU Usage</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    @if ($stoppedProcessCount > 0)
                                        <div class="alert alert-warning" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            {{ $stoppedProcessCount }} {{ Str::plural('process', $stoppedProcessCount) }} in
                                            stopped state. Immediate investigation is recommended.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane text-muted" id="apis-tab" role="tabpanel">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#processTable').DataTable({
                responsive: true,
                pageLength: 10,
                processing: false,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                ajax: {
                    url: "{!! route('admin.processes.data') !!}",
                },
                columns: [{
                    data: 'process_name',
                    searchable: false,
                }, {
                    data: 'status',
                    searchable: true,
                }, {
                    data: 'process_id',
                    searchable: true,
                }, {
                    data: 'started_at',
                    searchable: true
                }, {
                    data: 'memory_usage',
                    searchable: true
                }, {
                    data: 'cpu_usage',
                    searchable: true
                }, {
                    data: 'action',
                    searchable: true,
                    sortable: false
                }],
                columnDefs: [{
                    orderable: false,
                }]
            });

            $(document).on('click', '.restart-process', function() {
                const processId = $(this).data('id');
                const processCommand = $(this).data('command');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to restart this process",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, restart it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.processes.restart') }}',
                            method: 'POST',
                            data: {
                                id: processId,
                                command: processCommand,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success('Process restarted successfully');
                                $('#processTable').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error('Failed to restart process');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.stop-process', function() {
                const processId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to stop this process",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, stop it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.processes.stop') }}',
                            method: 'POST',
                            data: {
                                id: processId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                console.log(response)
                                toastr.success(response.message);
                                $('#processTable').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                console.log(xhr.responseJSON)
                                toastr.error('Failed to stop process');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
