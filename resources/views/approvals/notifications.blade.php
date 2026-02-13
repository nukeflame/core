@extends('layouts.app', [
    'pageTitle' => 'Approvals - ' . $company->company_name,
])

@section('styles')
    <style>
        .summary-card {
            background: linear-gradient(135deg, #aaaaaab5 0%, rgba(255, 255, 255, 0) 100%);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 24px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            /* color: white; */
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .summary-card .number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .summary-card .label {
            font-size: 14px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }


        /* Priority Badges */
        .priority-critical {
            background: #dc3545;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-high {
            background: #ff9800;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-medium {
            background: #9c27b0;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-low {
            background: #4caf50;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Status Badges */
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Expandable Row Details */
        .child-row {
            background: #f8f9fa;
            padding: 20px;
            border-left: 4px solid #667eea;
        }

        .detail-section {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 14px;
            color: #212529;
            font-weight: 500;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-approve:hover {
            background: #218838;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-reject:hover {
            background: #c82333;
        }

        .btn-review {
            background: #007bff;
            color: white;
        }

        .btn-review:hover {
            background: #0056b3;
        }

        /* Review Modal */
        .review-modal .modal-dialog {
            max-width: 1200px;
        }

        .review-header {
            background: linear-gradient(135deg, #6c757d 0%, #333 100%);
            color: white;
            padding: 24px;
            border-radius: 8px 8px 0 0;
        }

        .review-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .review-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #dee2e6;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }

        .info-item {
            background: white;
            padding: 12px;
            border-radius: 6px;
            border-left: 3px solid #667eea;
        }

        .info-item .label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-item .value {
            font-size: 14px;
            color: #212529;
            font-weight: 600;
        }

        /* Timeline */
        .approval-timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: -22px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #667eea;
        }

        .timeline-item:after {
            content: '';
            position: absolute;
            left: -17px;
            top: 12px;
            width: 2px;
            height: calc(100% - 12px);
            background: #dee2e6;
        }

        .timeline-item:last-child:after {
            display: none;
        }

        .amount-display {
            background: linear-gradient(135deg, #6c757d 0%, #333 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .amount-display .label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .amount-display .value {
            font-size: 32px;
            font-weight: bold;
        }

        .dataTables_wrapper .dataTables_scroll {
            overflow-x: auto;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
            background-color: #667eea;
            border: 2px solid #667eea;
            box-shadow: 0 0 0 2px white;
        }

        .filter-panel {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .filter-panel .form-select,
        .filter-panel .form-control {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            font-size: 14px;
        }

        /* Custom Scrollbar */
        .child-row::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .child-row::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .child-row::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .child-row::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Hover Effects */
        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa !important;
        }

        /* Icons */
        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f8f9fa;
            margin-right: 8px;
        }

        .icon-wrapper i {
            font-size: 16px;
            color: #667eea;
        }

        /* Comments Section */
        .comment-box {
            background: white;
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #dee2e6;
            margin-top: 12px;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 12px;
        }

        .comment-meta {
            flex: 1;
        }

        .comment-author {
            font-weight: 600;
            font-size: 14px;
            color: #212529;
        }

        .comment-date {
            font-size: 12px;
            color: #6c757d;
        }

        .comment-text {
            font-size: 14px;
            line-height: 1.6;
            color: #495057;
        }

        /* Approval Decision Panel */
        .decision-panel {
            background: white;
            border-radius: 8px;
            padding: 20px;
            border: 2px solid #dee2e6;
            margin-top: 20px;
        }

        .decision-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #212529;
        }

        .decision-buttons {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        .decision-buttons button {
            flex: 1;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        /* Risk Indicator */
        .risk-indicator {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .risk-high {
            background: #fee;
            color: #c00;
        }

        .risk-medium {
            background: #fff3cd;
            color: #856404;
        }

        .risk-low {
            background: #d4edda;
            color: #155724;
        }
    </style>
@endsection

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Approval Management</h1>
            <p class="text-muted mb-0">Review and process pending approvals</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Approvals</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md">
            <div class="summary-card all-card">
                <div class="number">{{ $counts['all'] }}</div>
                <div class="label">Total Approvals</div>
            </div>
        </div>
        <div class="col-md">
            <div class="summary-card claim-card">
                <div class="number">{{ $counts['claim'] }}</div>
                <div class="label">Claims</div>
            </div>
        </div>
        <div class="col-md">
            <div class="summary-card fac-card">
                <div class="number">{{ $counts['fac'] }}</div>
                <div class="label">Facultative</div>
            </div>
        </div>
        <div class="col-md">
            <div class="summary-card treaty-card">
                <div class="number">{{ $counts['treaty'] }}</div>
                <div class="label">Treaties</div>
            </div>
        </div>
        <div class="col-md">
            <div class="summary-card pending-card">
                <div class="number">{{ $counts['pending'] }}</div>
                <div class="label">Pending Review</div>
            </div>
        </div>
    </div>

    <form method="POST" id="form_view_review">
        {{ csrf_field() }}
        <input type="hidden" name="cover_no" id="app_cover_no">
        <input type="hidden" name="endorsement_no" id="app_endorse_no">
        <input type="hidden" name="customer_id" id="app_customer_id">
    </form>

    <div class="filter-panel">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" id="status-filter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Priority</label>
                <select class="form-select" id="priority-filter">
                    <option value="">All Priorities</option>
                    <option value="critical">Critical</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" class="form-control" id="date-from">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" class="form-control" id="date-to">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary w-100" id="apply-filters">
                    <i class="bx bx-filter-alt"></i> Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs and DataTable -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <ul class="nav nav-pills" role="tablist" id="approvalTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#all-tabs" data-type="all">
                                <i class="bx bx-list-ul me-2"></i>All Approvals
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#fac-tab" data-type="fac">
                                <i class="bx bx-shield me-2"></i>Facultative
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#claims-tab" data-type="claim">
                                <i class="bx bx-receipt me-2"></i>Claims
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#treaty-tab"
                                data-type="treaty">
                                <i class="bx bx-file me-2"></i>Treaties
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane show active border-0" id="all-tabs" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover approved-table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th class="dtr-control" style="width: 30px;"></th>
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th>Client/Insured</th>
                                            <th>Amount</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Submitted</th>
                                            <th>Age</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper review-modal" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h4 class="modal-title text-white mb-1" id="modal-title">Approval Review</h4>
                        <p class="mb-0 opacity-75" id="modal-subtitle">Review details before making decision</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="review-content">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading approval details...</p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-danger" id="modal-reject-btn">
                        <i class="bx bx-x"></i> Reject
                    </button>
                    <button type="button" class="btn btn-success" id="modal-approve-btn">
                        <i class="bx bx-check"></i> Approve
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let currentApprovalId = null;
            let currentApprovalData = null;

            var approvalTable = $('.approved-table').DataTable({
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                processing: true,
                serverSide: true,
                responsive: {
                    details: {
                        type: 'column',
                        target: 0,
                        renderer: function(api, rowIdx, columns) {
                            return renderChildRow(api.row(rowIdx).data(), columns);
                        }
                    }
                },
                searchDelay: 500,
                order: [
                    [7, 'desc']
                ],
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"></div></div>',
                    emptyTable: "No approval records found",
                    info: "Showing _START_ to _END_ of _TOTAL_ approvals",
                    infoEmpty: "No approvals available",
                    search: "Search:",
                    paginate: {
                        first: '<i class="bx bx-chevrons-left"></i>',
                        last: '<i class="bx bx-chevrons-right"></i>',
                        next: '<i class="bx bx-chevron-right"></i>',
                        previous: '<i class="bx bx-chevron-left"></i>'
                    }
                },
                ajax: {
                    url: "{{ route('admin.approvals.data') }}",
                    data: function(d) {
                        d.type = $('.nav-link.active').data('type');
                        d.status_filter = $('#status-filter').val();
                        d.priority_filter = $('#priority-filter').val();
                        d.date_from = $('#date-from').val();
                        d.date_to = $('#date-to').val();
                        d._token = $('meta[name="csrf-token"]').attr('content');
                    },
                    error: function(xhr, error, code) {
                        toastr.error('Failed to load data. Please refresh.');
                    }
                },
                columns: [{
                        className: 'dtr-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                        width: '30px'
                    },
                    {
                        data: 'title',
                        name: 'title',
                        render: function(data, type, row) {
                            return `<div class="fw-semibold">${data || 'N/A'}</div>`;
                        }
                    },
                    {
                        data: 'type_badge',
                        name: 'type',
                        orderable: false
                    },
                    {
                        data: 'client',
                        name: 'client',
                        render: function(data, type, row) {
                            return `<div class="text-truncate" style="max-width: 200px;" title="${data || 'N/A'}">${data || 'N/A'}</div>`;
                        }
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        className: 'text-start',
                        render: function(data, type, row) {
                            return `<span class="text-success fw-semibold">${data || '0.00'}</span>`;
                        }
                    },
                    {
                        data: 'priority_badge',
                        name: 'priority',
                        className: 'text-start'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-start'
                    },
                    {
                        data: 'date',
                        name: 'date',
                        render: function(data, type, row) {
                            return data ? moment(data).format('MMM DD, YYYY HH:mm') : 'N/A';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const createdDate = moment(row.date);
                            const now = moment();
                            const days = now.diff(createdDate, 'days');
                            const hours = now.diff(createdDate, 'hours') % 24;

                            let ageClass = days > 7 ? 'text-danger' : days > 3 ? 'text-warning' :
                                'text-success';
                            let ageText = days > 0 ? `${days}d ${hours}h` : `${hours}h`;

                            return `<span class="${ageClass} fw-semibold">${ageText}</span>`;
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-start',
                        width: '150px'
                    }
                ],
                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            function renderChildRow(rowData, columns) {
                let data = {};

                try {
                    data = JSON.parse(rowData.data || '{}');
                } catch (e) {
                    console.error('Failed to parse row data:', e);
                    data = {};
                }

                // Merge data into rowData for easier access
                const mergedData = {
                    ...rowData,
                    ...data
                };

                return `
                    <div class="child-row">
                        <div class="row g-3">
                            ${createBasicInfoSection(mergedData)}
                            ${createRequestDetailsSection(rowData)}
                            ${createCoverDetailsSection(data)}
                            ${createClaimDetailsSection(data)}
                            ${createActionButtons(rowData)}
                        </div>
                    </div>
                `;
            }

            function createActionButtons(rowData) {
                const id = parseInt(rowData.id) || 0;
                const isPending = rowData.status_raw === 'pending';

                const approveRejectButtons = isPending ? `
                    <button class="action-btn btn-approve approve-btn" data-id="${id}">
                        <i class="bx bx-check me-1"></i> Approve
                    </button>
                    <button class="action-btn btn-reject decline-btn" data-id="${id}">
                        <i class="bx bx-x me-1"></i> Reject
                    </button>
                ` : '';

                return `
                    <div class="col-md-12">
                        <div class="quick-actions justify-content-end">
                            <button class="action-btn btn-review" data-id="${id}" onclick="openReviewModal(${id})">
                                <i class="bx bx-show-alt me-1"></i> Full Review
                            </button>
                            ${approveRejectButtons}
                        </div>
                    </div>
                `;
            }

            function sanitizeHtml(str) {
                if (!str) return '';
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            function createClaimDetailsSection(data) {
                if (!data.intimation_no) return '';

                const items = [];

                if (data.intimation_no) {
                    items.push(createInfoItem('Intimation No', data.intimation_no));
                }
                if (data.loss_date) {
                    items.push(createInfoItem('Loss Date', formatDate(data.loss_date, 'MMM DD, YYYY')));
                }

                return `
                    <div class="col-md-12">
                        <div class="detail-section">
                            <h6 class="review-section-title">
                                <i class="bx bx-receipt me-2"></i>Claim Details
                            </h6>
                            <div class="info-grid">
                                ${items.join('')}
                            </div>
                        </div>
                    </div>
                `;
            }

            function createRequestDetailsSection(rowData) {
                const initials = getInitials(rowData.created_by);
                const author = sanitizeHtml(rowData.created_by || 'Unknown');
                const date = formatDate(rowData.date);
                const comment = sanitizeHtml(rowData.comment || 'No comment provided');

                return `
                    <div class="col-md-6">
                        <div class="detail-section">
                            <h6 class="review-section-title">
                                <i class="bx bx-user me-2"></i>Request Details
                            </h6>
                            <div class="comment-box">
                                <div class="comment-header">
                                    <div class="comment-avatar">${initials}</div>
                                    <div class="comment-meta">
                                        <div class="comment-author">${author}</div>
                                        <div class="comment-date">${date}</div>
                                    </div>
                                </div>
                                <div class="comment-text">${comment}</div>
                            </div>
                        </div>
                    </div>
                `;
            }

            function createCoverDetailsSection(data) {
                if (!data.cover_no) return '';

                const items = [];

                if (data.cover_no) {
                    items.push(createInfoItem('Cover No', data.cover_no));
                }
                if (data.endorsement_no) {
                    items.push(createInfoItem('Endorsement No', data.endorsement_no));
                }
                if (data.business_type) {
                    items.push(createInfoItem('Business Type', data.business_type));
                }
                if (data.no_of_instalments) {
                    items.push(createInfoItem('Installments', data.no_of_instalments));
                }

                return `
                    <div class="col-md-12">
                        <div class="detail-section">
                            <h6 class="review-section-title">
                                <i class="bx bx-detail me-2"></i>Cover Details
                            </h6>
                            <div class="info-grid">
                                ${items.join('')}
                            </div>
                        </div>
                    </div>
                `;
            }

            function createInfoItem(label, value) {
                return `
                    <div class="info-item">
                        <div class="label">${sanitizeHtml(label)}</div>
                        <div class="value">${sanitizeHtml(value)}</div>
                    </div>
                `;
            }

            function createBasicInfoSection(rowData) {
                return `
                    <div class="col-md-6">
                        <div class="detail-section">
                            <h6 class="review-section-title">
                                <i class="bx bx-info-circle me-2"></i>Basic Information
                            </h6>
                            <div class="info-grid">
                                ${createInfoItem('Reference', rowData.title || 'N/A')}
                                ${createInfoItem('Type', rowData.type || 'N/A')}
                                ${createInfoItem('Client', rowData.client || 'N/A')}
                                ${createInfoItem('Amount', formatCurrency(rowData.amount))}
                            </div>
                        </div>
                    </div>
                `;
            }

            // function renderChildRow(rowData, columns) {
            //     const data = JSON.parse(rowData.data || '{}');

            //     return `
        //         <div class="child-row">
        //             <div class="row g-3">
        //                 <!-- Basic Information -->
        //                 <div class="col-md-6">
        //                     <div class="detail-section">
        //                         <h6 class="review-section-title">
        //                             <i class="bx bx-info-circle me-2"></i>Basic Information
        //                         </h6>
        //                         <div class="info-grid">
        //                             <div class="info-item">
        //                                 <div class="label">Reference</div>
        //                                 <div class="value">${rowData.title || 'N/A'}</div>
        //                             </div>
        //                             <div class="info-item">
        //                                 <div class="label">Type</div>
        //                                 <div class="value">${data.type || 'N/A'}</div>
        //                             </div>
        //                             <div class="info-item">
        //                                 <div class="label">Client</div>
        //                                 <div class="value">${rowData.client || 'N/A'}</div>
        //                             </div>
        //                             <div class="info-item">
        //                                 <div class="label">Amount</div>
        //                                 <div class="value">KES ${rowData.amount || '0.00'}</div>
        //                             </div>
        //                         </div>
        //                     </div>
        //                 </div>

        //                 <!-- Request Details -->
        //                 <div class="col-md-6">
        //                     <div class="detail-section">
        //                         <h6 class="review-section-title">
        //                             <i class="bx bx-user me-2"></i>Request Details
        //                         </h6>
        //                         <div class="comment-box">
        //                             <div class="comment-header">
        //                                 <div class="comment-avatar">
        //                                     ${rowData.created_by ? rowData.created_by.charAt(0).toUpperCase() : 'U'}
        //                                 </div>
        //                                 <div class="comment-meta">
        //                                     <div class="comment-author">${rowData.created_by || 'Unknown'}</div>
        //                                     <div class="comment-date">${rowData.date ? moment(rowData.date).format('MMM DD, YYYY HH:mm') : 'N/A'}</div>
        //                                 </div>
        //                             </div>
        //                             <div class="comment-text">${rowData.comment || 'No comment provided'}</div>
        //                         </div>
        //                     </div>
        //                 </div>

        //                 <!-- Additional Details (if available) -->
        //                 ${data.cover_no ? `
            //                                                                                                                 <div class="col-md-12">
            //                                                                                                                     <div class="detail-section">
            //                                                                                                                         <h6 class="review-section-title">
            //                                                                                                                             <i class="bx bx-detail me-2"></i>Cover Details
            //                                                                                                                         </h6>
            //                                                                                                                         <div class="info-grid">
            //                                                                                                                             ${data.cover_no ? `<div class="info-item">
        //                                 <div class="label">Cover No</div>
        //                                 <div class="value">${data.cover_no}</div>
        //                             </div>` : ''}
            //                                                                                                                             ${data.endorsement_no ? `<div class="info-item">
        //                                 <div class="label">Endorsement No</div>
        //                                 <div class="value">${data.endorsement_no}</div>
        //                             </div>` : ''}
            //                                                                                                                             ${data.business_type ? `<div class="info-item">
        //                                 <div class="label">Business Type</div>
        //                                 <div class="value">${data.business_type}</div>
        //                             </div>` : ''}
            //                                                                                                                             ${data.no_of_instalments ? `<div class="info-item">
        //                                 <div class="label">Installments</div>
        //                                 <div class="value">${data.no_of_instalments}</div>
        //                             </div>` : ''}
            //                                                                                                                         </div>
            //                                                                                                                     </div>
            //                                                                                                                 </div>` : ''}

        //                 ${data.intimation_no ? `
            //                                                                                                                 <div class="col-md-12">
            //                                                                                                                     <div class="detail-section">
            //                                                                                                                         <h6 class="review-section-title">
            //                                                                                                                             <i class="bx bx-receipt me-2"></i>Claim Details
            //                                                                                                                         </h6>
            //                                                                                                                         <div class="info-grid">
            //                                                                                                                             ${data.intimation_no ? `<div class="info-item">
        //                                 <div class="label">Intimation No</div>
        //                                 <div class="value">${data.intimation_no}</div>
        //                             </div>` : ''}
            //                                                                                                                             ${data.loss_date ? `<div class="info-item">
        //                                 <div class="label">Loss Date</div>
        //                                 <div class="value">${moment(data.loss_date).format('MMM DD, YYYY')}</div>
        //                             </div>` : ''}
            //                                                                                                                         </div>
            //                                                                                                                     </div>
            //                                                                                                                 </div>` : ''}

        //                 <!-- Quick Actions -->
        //                 <div class="col-md-12">
        //                     <div class="quick-actions justify-content-end">
        //                         <button class="action-btn btn-review" data-id="${rowData.id}" onclick="openReviewModal(${rowData.id})">
        //                             <i class="bx bx-show-alt me-1"></i> Full Review
        //                         </button>
        //                         ${rowData.status_raw === 'pending' ? `
            //                                                                                                                         <button class="action-btn btn-approve approve-btn" data-id="${rowData.id}">
            //                                                                                                                             <i class="bx bx-check me-1"></i> Approve
            //                                                                                                                         </button>
            //                                                                                                                         <button class="action-btn btn-reject decline-btn" data-id="${rowData.id}">
            //                                                                                                                             <i class="bx bx-x me-1"></i> Reject
            //                                                                                                                         </button>` : ''}
        //                     </div>
        //                 </div>
        //             </div>
        //         </div>
        //     `;
            // }

            $('#approvalTabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
                approvalTable.ajax.reload();
            });

            $('#apply-filters').on('click', function() {
                approvalTable.ajax.reload();
            });

            $('.filter-panel input, .filter-panel select').on('keypress', function(e) {
                if (e.which === 13) {
                    approvalTable.ajax.reload();
                }
            });

            window.openReviewModal = function(approvalId) {
                currentApprovalId = approvalId;
                $('#reviewModal').modal('show');
                loadFullReviewData(approvalId);
            };

            function loadFullReviewData(approvalId) {
                $('#review-content').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Loading approval details...</p>
                    </div>
                `);

                $.ajax({
                    url: `/approvals/${approvalId}/details`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            currentApprovalData = response.data;
                            renderFullReview(response.data);
                        } else {
                            showReviewError('Failed to load approval details');
                        }
                    },
                    error: function(xhr) {
                        console.error('Review load error:', xhr);
                        showReviewError('An error occurred while loading details');
                    }
                });
            }

            function renderFullReview(approval) {
                const data = approval.data;

                //     <!-- Core Information -->
                //     <div class="review-section">
                //         <h5 class="review-section-title">Core Information</h5>
                //         <div class="info-grid">
                //             <div class="info-item">
                //                 <div class="label">Reference Number</div>
                //                 <div class="value">${approval.title || 'N/A'}</div>
                //             </div>
                //             <div class="info-item">
                //                 <div class="label">Type</div>
                //                 <div class="value">${data.type || 'N/A'}</div>
                //             </div>
                //             <div class="info-item">
                //                 <div class="label">Priority</div>
                //                 <div class="value">${approval.priority_badge}</div>
                //             </div>
                //             <div class="info-item">
                //                 <div class="label">Status</div>
                //                 <div class="value">${approval.status}</div>
                //             </div>
                //             <div class="info-item">
                //                 <div class="label">Client/Insured</div>
                //                 <div class="value">${approval.client || 'N/A'}</div>
                //             </div>
                //             <div class="info-item">
                //                 <div class="label">Submitted Date</div>
                //                 <div class="value">${moment(approval.date).format('MMM DD, YYYY HH:mm')}</div>
                //             </div>
                //         </div>
                //     </div>

                //     <!-- Request Details -->
                //     <div class="review-section">
                //         <h5 class="review-section-title">Request Information</h5>
                //         <div class="comment-box">
                //             <div class="comment-header">
                //                 <div class="comment-avatar">
                //                     ${approval.created_by ? approval.created_by.charAt(0).toUpperCase() : 'U'}
                //                 </div>
                //                 <div class="comment-meta">
                //                     <div class="comment-author">${approval.created_by || 'Unknown User'}</div>
                //                     <div class="comment-date">${moment(approval.date).format('MMMM DD, YYYY at HH:mm')}</div>
                //                 </div>
                //             </div>
                //             <div class="comment-text mt-3">${approval.comment || 'No comment provided'}</div>
                //         </div>
                //     </div>

                //     <!-- Type-Specific Details -->
                //     ${data.type === 'facultative' && data.cover_no ? `
            //                                                                                                                                                                             <div class="review-section">
            //                                                                                                                                                                                 <h5 class="review-section-title">Facultative Cover Details</h5>
            //                                                                                                                                                                                 <div class="info-grid">
            //<div class="info-item">
            //    <div class="label">Cover Number</div>
            //    <div class="value">${data.cover_no}</div>
            //</div>
            //<div class="info-item">
            //    <div class="label">Endorsement Number</div>
            //    <div class="value">${data.endorsement_no || 'N/A'}</div>
            //</div>
            //<div class="info-item">
            //    <div class="label">Business Type</div>
            //    <div class="value">${data.business_type || 'N/A'}</div>
            //</div>
            //<div class="info-item">
            //    <div class="label">Sum Insured</div>
            //    <div class="value">KES ${approval.amount || '0.00'}</div>
            //</div>
            //<div class="info-item">
            //    <div class="label">Number of Installments</div>
            //    <div class="value">${data.no_of_instalments || 'N/A'}</div>
            //</div>
            //<div class="info-item">
            //    <div class="label">Customer ID</div>
            //    <div class="value">${data.customer_id || 'N/A'}</div>
            //</div>
            //                                                                                                                                                                                 </div>
            //                                                                                                                                                                             </div>` : ''}

                //     ${data.type === 'claim' && data.intimation_no ? `
            //                                                                                                                                                                             <div class="review-section">
            //                                                                                                                                                                                 <h5 class="review-section-title">Claim Details</h5>
            //                                                                                                                                                                                 <div class="info-grid">
            //<div class="info-item">
            //    <div class="label">Intimation Number</div>
            //    <div class="value">${data.intimation_no}</div>
            //</div>
            //<div class="info-item">
            //    <div class="label">Cover Number</div>
            //    <div class="value">${data.cover_no || 'N/A'}</div>
            //</div>
            //<div class="info-item">
            //    <div class="label">Date of Loss</div>
            //    <div class="value">${data.loss_date ? moment(data.loss_date).format('MMM DD, YYYY') : 'N/A'}</div>
            //</div>
            //<div class="info-item">
            //    <div class="label">Claim Amount</div>
            //    <div class="value">KES ${approval.amount || '0.00'}</div>
            //</div>
            //<div class="info-item">
            //    <div class="label">Business Type</div>
            //    <div class="value">${data.business_type || 'N/A'}</div>
            //</div>
            //                                                                                                                                                                                 </div>
            //                                                                                                                                                                             </div>` : ''}

                //     <!-- Decision Panel (only for pending) -->
                //     ${approval.status_raw === 'pending' ? `
            //<div class="decision-panel">
            //    <h5 class="decision-title">Make Your Decision</h5>
            //    <div class="form-group">
            //        <label class="form-label">Your Comment (Required)</label>
            //        <textarea class="form-control" id="decision-comment" rows="4"
            //            placeholder="Please provide your reason for approval or rejection..."></textarea>
            //    </div>
            //    <div class="decision-buttons">
            //        <button type="button" class="btn btn-lg btn-danger" onclick="makeDecision('R')">
            //            <i class="bx bx-x me-2"></i> Reject Approval
            //        </button>
            //        <button type="button" class="btn btn-lg btn-success" onclick="makeDecision('A')">
            //            <i class="bx bx-check me-2"></i> Approve Request
            //        </button>
            //    </div>
            //</div>` : ''}
                //${approval.approver_comment ? `
            //<div class="review-section">
            //    <h5 class="review-section-title">Approver Comment</h5>
            //    <div class="comment-box">
            //        <div class="comment-text">${approval.approver_comment}</div>
            //</div>
            //                                                                                                                                                                             </div>` : ''}

                const html = `
                    <div class="review-content-wrapper">
                        <!-- Amount Display -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="amount-display">
                                    <div class="label">Approval Amount</div>
                                    <div class="value">KES ${approval.amount || '0.00'}</div>
                                </div>
                            </div>
                        </div>
                    </div>                `;

                $('#review-content').html(html);

                $('#modal-title').text(`Review: ${approval.title}`);
                $('#modal-subtitle').text(
                    `Submitted by ${approval.created_by} on ${moment(approval.date).format('MMM DD, YYYY')}`);

                if (approval.status_raw === 'pending') {
                    $('#modal-approve-btn, #modal-reject-btn').show();
                } else {
                    $('#modal-approve-btn, #modal-reject-btn').hide();
                }
            }

            function showReviewError(message) {
                $('#review-content').html(`
                    <div class="alert alert-danger">
                        <i class="bx bx-error me-2"></i>${message}
                    </div>
                `);
            }

            window.makeDecision = function(action) {
                const comment = $('#decision-comment').val().trim();

                if (!comment) {
                    toastr.error('Please provide a comment for your decision');
                    $('#decision-comment').focus();
                    return;
                }

                if (comment.length < 10) {
                    toastr.error('Comment must be at least 10 characters long');
                    return;
                }

                const data = {
                    id: currentApprovalId,
                    action: action,
                    comment: comment
                };

                processApproval(data);
            };

            $('#modal-approve-btn').on('click', function() {
                Swal.fire({
                    title: 'Approve Request',
                    input: 'textarea',
                    inputLabel: 'Your Comment (Required)',
                    inputPlaceholder: 'Provide reason for approval...',
                    showCancelButton: true,
                    confirmButtonText: 'Approve',
                    confirmButtonColor: '#28a745',
                }).then((result) => {
                    if (result.isConfirmed) {
                        processApproval({
                            id: currentApprovalId,
                            action: 'A',
                            comment: result.value ?? 'Approved'
                        });
                    }
                });
            });

            $('#modal-reject-btn').on('click', function() {
                Swal.fire({
                    title: 'Reject Request',
                    input: 'textarea',
                    inputLabel: 'Reason for Rejection (Required)',
                    inputPlaceholder: 'Provide detailed reason for rejection...',
                    inputAttributes: {
                        'aria-label': 'Type your reason here',
                        'minlength': '10'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Reject',
                    confirmButtonColor: '#dc3545',
                    preConfirm: (comment) => {
                        if (!comment || comment.trim().length < 10) {
                            Swal.showValidationMessage('Reason must be at least 10 characters');
                            return false;
                        }
                        return comment;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        processApproval({
                            id: currentApprovalId,
                            action: 'R',
                            comment: result.value ?? 'Rejected'
                        });
                    }
                });
            });

            $(document).on('click', '.approve-btn', function(e) {
                e.preventDefault();
                const approvalId = $(this).data('id');
                currentApprovalId = approvalId;

                Swal.fire({
                    title: 'Approve Request',
                    input: 'textarea',
                    inputLabel: 'Your Comment (Required)',
                    inputPlaceholder: 'Provide reason for approval...',
                    showCancelButton: true,
                    confirmButtonText: 'Approve',
                    confirmButtonColor: '#28a745',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const comment = result.value == '' ? 'Approved' : result.value;
                        processApproval({
                            id: approvalId,
                            action: 'A',
                            comment: comment
                        });
                    }
                });
            });

            $(document).on('click', '.decline-btn', function(e) {
                e.preventDefault();
                const approvalId = $(this).data('id');
                currentApprovalId = approvalId;

                Swal.fire({
                    title: 'Reject Request',
                    input: 'textarea',
                    inputLabel: 'Reason for Rejection (Required)',
                    inputPlaceholder: 'Provide detailed reason...',
                    showCancelButton: true,
                    confirmButtonText: 'Reject',
                    confirmButtonColor: '#dc3545',
                }).then((result) => {
                    if (result.isConfirmed) {
                        processApproval({
                            id: approvalId,
                            action: 'R',
                            comment: result.value ?? 'Declined'
                        });
                    }
                });
            });

            function processApproval(data) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your decision',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetchWithCsrf("{{ route('admin.approvals.action') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data),
                    })
                    .then(response => response.json())
                    .then(result => {
                        Swal.close();

                        if (result.status === 200 || result.status === 201) {
                            const actionText = data.action === 'A' ? 'approved' : 'rejected';

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: `Approval has been ${actionText} successfully`,
                                timer: 3000,
                                showConfirmButton: false
                            });

                            $('#reviewModal').modal('hide');
                            approvalTable.ajax.reload();
                        } else if (result.status === 422) {
                            showValidationErrors(result.errors);
                        } else {
                            throw new Error(result.message || 'Processing failed');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'An unexpected error occurred'
                        });
                    });
            }

            function showValidationErrors(errors) {
                let errorHtml = '<ul class="text-start">';
                $.each(errors, function(field, messages) {
                    messages.forEach(function(message) {
                        errorHtml += `<li>${message}</li>`;
                    });
                });
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorHtml
                });
            }

            $(document).on('click', '.review-btn', function(e) {
                e.preventDefault();
                const endorsementNo = $(this).data('endorsement_no');
                const type = $(this).data('type');
                const coverNo = $(this).data('cover_no');
                const customerId = $(this).data('customer_id');
                const intimationNo = $(this).data('intimation_no');

                let url = '';
                switch (type) {
                    case 'facultative':
                        url = "{{ route('cover.CoverHome') }}";
                        url += `?endorsement_no=${encodeURIComponent(endorsementNo)}`;
                        break;
                    case 'claim':
                        url = "{{ route('claim.notification.claim.detail') }}";
                        url +=
                            `?intimation_no=${encodeURIComponent(intimationNo)}&process_type=claim`;
                        break;
                }

                if (url) {
                    window.open(url, '_blank');
                }
            });
        });
    </script>
@endpush
