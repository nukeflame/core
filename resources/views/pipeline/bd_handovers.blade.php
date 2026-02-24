@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Business Development Handovers</h1>
            <p class="text-muted fs-13 mb-0 mt-1">Manage and track insurance opportunities from BD to underwriting</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pipeline.bd_handovers') }}">Pipeline</a></li>
                    <li class="breadcrumb-item active" aria-current="page">BD Handovers</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="row mb-4">
        <!-- Total Handovers Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div class="flex-fill">
                            <p class="mb-1 text-muted fs-13">Total Handovers</p>
                            <h3 class="fw-semibold mb-0" id="total-handovers">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </h3>
                            <span class="text-muted fs-12" id="total-handovers-subtitle">All opportunities</span>
                        </div>
                        <div class="text-end">
                            <span class="avatar avatar-md bg-primary-transparent">
                                <i class="ri-briefcase-line fs-20"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approval Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div class="flex-fill">
                            <p class="mb-1 text-muted fs-13">Pending Approval</p>
                            <h3 class="fw-semibold mb-0 text-warning" id="pending-approval">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </h3>
                            <span class="text-muted fs-12" id="pending-approval-subtitle">Awaiting review</span>
                        </div>
                        <div class="text-end">
                            <span class="avatar avatar-md bg-warning-transparent">
                                <i class="ri-time-line fs-20"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div class="flex-fill">
                            <p class="mb-1 text-muted fs-13">Approved</p>
                            <h3 class="fw-semibold mb-0 text-success" id="approved-count">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </h3>
                            <span class="text-muted fs-12" id="approved-subtitle">Ready for processing</span>
                        </div>
                        <div class="text-end">
                            <span class="avatar avatar-md bg-success-transparent">
                                <i class="ri-checkbox-circle-line fs-20"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Value Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div class="flex-fill">
                            <p class="mb-1 text-muted fs-13">Total Premium Value</p>
                            <h3 class="fw-semibold mb-0 text-primary" id="total-premium">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </h3>
                            <span class="text-muted fs-12" id="premium-subtitle">Across all handovers</span>
                        </div>
                        <div class="text-end">
                            <span class="avatar avatar-md bg-primary-transparent">
                                <i class="ri-money-dollar-circle-line fs-20"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats & Charts Row -->
    <div class="row mb-4 d-none">
        <!-- Division Breakdown -->
        <div class="col-xl-4 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Division Breakdown</div>
                </div>
                <div class="card-body">
                    <div id="division-chart" style="min-height: 200px;">
                        <div class="text-center py-5">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            <p class="text-muted mt-2">Loading division data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-xl-4 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Status Distribution</div>
                </div>
                <div class="card-body">
                    <div id="status-breakdown" style="min-height: 200px;">
                        <div class="text-center py-5">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            <p class="text-muted mt-2">Loading status data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Cedants -->
        <div class="col-xl-4 col-lg-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Top Cedants</div>
                </div>
                <div class="card-body" style="max-height: 340px; overflow-y: auto;">
                    <div id="top-cedants-list" class="customScrollBar">
                        <div class="text-center py-5" style="min-height: 200px;">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            <p class="text-muted mt-2">Loading cedant data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Data Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="card-title mb-2 mb-md-0">BD Handovers List</div>
                    <div class="d-flex gap-2 flex-wrap">
                        <!-- Quick Filters -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary filter-status"
                                data-status="all">
                                All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning filter-status"
                                data-status="pending">
                                Pending
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success filter-status"
                                data-status="approved">
                                Approved
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger filter-status"
                                data-status="rejected">
                                Rejected
                            </button>
                        </div>

                        <!-- Export Options -->
                        {{-- <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="exportDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-download-line me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="#" id="export-excel">
                                        <i class="ri-file-excel-line me-2"></i>Export to Excel
                                    </a></li>
                                <li><a class="dropdown-item" href="#" id="export-pdf">
                                        <i class="ri-file-pdf-line me-2"></i>Export to PDF
                                    </a></li>
                                <li><a class="dropdown-item" href="#" id="export-csv">
                                        <i class="ri-file-text-line me-2"></i>Export to CSV
                                    </a></li>
                            </ul>
                        </div> --}}

                        <!-- Refresh Button -->
                        <button type="button" class="btn btn-sm btn-primary" id="refresh-table">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover" id="bd_handovers_table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Opportunity ID</th>
                                    <th>Cedant</th>
                                    <th>Insured Name </th>
                                    <th>Division</th>
                                    <th>Business Class</th>
                                    <th>Currency</th>
                                    <th>Sum Insured</th>
                                    <th>Premium</th>
                                    <th>Effective Date</th>
                                    <th>Closing Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-end">Current Page Totals:</th>
                                    <th id="footer-sum-insured">-</th>
                                    <th id="footer-premium">-</th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Comment Modal -->
    <div class="modal fade" id="rejectedCommentModal" tabindex="-1" aria-labelledby="rejectedCommentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger-transparent">
                    <h5 class="modal-title text-danger" id="rejectedCommentModalLabel">
                        <i class="ri-error-warning-line me-2"></i>Rejection Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <strong>Reason for Rejection:</strong>
                    </div>
                    <div id="rejection_message" class="p-3 border rounded bg-light"
                        style="min-height: 120px; max-height: 400px; overflow-y: auto; white-space: pre-wrap;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-body .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        .filter-status.active {
            background-color: var(--bs-primary);
            color: white;
            border-color: var(--bs-primary);
        }

        .filter-status.active.btn-outline-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .filter-status.active.btn-outline-success {
            background-color: #198754;
            border-color: #198754;
        }

        .filter-status.active.btn-outline-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .highlight-index {
            font-weight: 600;
            color: #495057;
        }

        .highlight-view-point {
            color: #0d6efd;
            cursor: pointer;
        }

        .highlight-view-point:hover {
            text-decoration: underline;
        }

        .highlight-action {
            font-weight: 500;
        }

        .highlight-overflow {
            max-width: 200px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #842029;
        }

        .status-processing {
            background-color: #cfe2ff;
            color: #084298;
        }

        .cedant-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .cedant-item:last-child {
            border-bottom: none;
        }

        .cedant-name {
            font-weight: 500;
            color: #495057;
        }

        .cedant-count {
            background-color: #e7f3ff;
            color: #0d6efd;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        #division-chart,
        #status-breakdown {
            position: relative;
        }

        #bd_handovers_table tfoot th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            border-top: 2px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .page-header-breadcrumb {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .btn-group {
                width: 100%;
            }

            .btn-group .btn {
                flex: 1;
                font-size: 11px;
                padding: 0.25rem 0.5rem;
            }

            .card-header {
                padding: 1rem;
            }
        }

        .avatar {
            transition: transform 0.3s ease;
        }

        .card:hover .avatar {
            transform: scale(1.1);
        }
    </style>
@endpush

@push('script')
    <script>
        const ROUTES = {
            bdHandoversDatatable: "{!! route('pipeline.bd_handovers_datatable') !!}",
            bdHandoversStats: "{!! route('pipeline.bd_handovers_stats') !!}",
            clearCedantData: "{!! route('customer.clear_cedant_data') !!}",
            createCover: "{!! route('pipeline.create_cover') !!}",
            bdApprovalAction: "{!! route('admin.approvals.bd-action') !!}",
            exportData: "{!! route('pipeline.bd_handovers_export') !!}",
        };
    </script>
    <script src="{{ asset('js/bd-handovers.js') }}"></script>
@endpush
