@extends('layouts.app')


@push('styles')
    <style>
        .card.custom-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card.custom-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .avatar {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
        }

        .avatar-rounded {
            border-radius: 0.5rem !important;
        }

        .bg-primary-transparent {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .bg-success-transparent {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .bg-info-transparent {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .bg-warning-transparent {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .bg-danger-transparent {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .bg-secondary {
            background-color: #6c757d !important;
        }

        .fs-12 {
            font-size: 0.75rem;
        }

        /* Sparkline Styles */
        .sparkline-container {
            display: flex;
            align-items: flex-end;
            height: 30px;
            gap: 2px;
        }

        .sparkline-bar {
            width: 3px;
            min-height: 2px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .sparkline-bar:hover {
            opacity: 0.7;
        }

        /* Modern Table Enhancements */
        #customer-table {
            font-size: 0.875rem;
        }

        #customer-table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }

        #customer-table tbody tr {
            transition: all 0.2s ease;
        }

        #customer-table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
            transform: translateX(2px);
        }

        .clickable-cell {
            cursor: pointer;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .page-header-breadcrumb {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .page-header-breadcrumb .ms-md-1 {
                margin-top: 0.5rem;
            }

            .avatar {
                width: 2.5rem;
                height: 2.5rem;
            }

            .card-body h4 {
                font-size: 1.25rem;
            }
        }


        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .col-xl-3 .card {
            animation: fadeInUp 0.5s ease-out;
        }

        .col-xl-3:nth-child(1) .card {
            animation-delay: 0.1s;
        }

        .col-xl-3:nth-child(2) .card {
            animation-delay: 0.2s;
        }

        .col-xl-3:nth-child(3) .card {
            animation-delay: 0.3s;
        }

        .col-xl-3:nth-child(4) .card {
            animation-delay: 0.4s;
        }

        #customer-table {
            font-size: 0.875rem;
        }

        #customer-table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }

        #customer-table tbody tr {
            transition: all 0.2s ease;
        }

        #customer-table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
            transform: translateX(2px);
        }

        .clickable-cell {
            cursor: pointer;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
        }

        @media (max-width: 768px) {
            .page-header-breadcrumb {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .page-header-breadcrumb .ms-md-1 {
                margin-top: 0.5rem;
            }
        }

        .dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 2rem;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        #customer-table tbody tr {
            cursor: pointer;
        }

        #customer-table tbody tr.table-active {
            background-color: rgba(13, 110, 253, 0.1);
        }
    </style>
@endpush


@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Cedants Management</h1>
            <p class="text-muted mb-0 mt-1">Manage and view all cedant companies</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"> <i
                                class="bx bx-home-alt me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Cedants</a></li>
                    <li class="breadcrumb-item active" aria-current="page">List</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                <i class="bi bi-building fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Cedants</p>
                                    <h4 class="fw-semibold mt-1" id="stat-total-cedants">
                                        <span class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </span>
                                    </h4>
                                </div>
                                <div id="total-cedants-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="badge bg-primary-transparent" id="stat-total-change">
                                        <i class="ri-arrow-up-s-line align-middle"></i>0%
                                    </span>
                                    <span class="text-muted ms-2 fs-12">This month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-success-transparent">
                                <i class="bi bi-shield-check fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Active Covers</p>
                                    <h4 class="fw-semibold mt-1" id="stat-active-covers">
                                        <span class="spinner-border spinner-border-sm text-success" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </span>
                                    </h4>
                                </div>
                                <div id="active-covers-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="badge bg-success-transparent" id="stat-covers-change">
                                        <i class="ri-arrow-up-s-line align-middle"></i>0%
                                    </span>
                                    <span class="text-muted ms-2 fs-12">This month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-info-transparent">
                                <i class="bi bi-diagram-3 fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Cedant Types</p>
                                    <h4 class="fw-semibold mt-1" id="stat-cedant-types">
                                        <span class="spinner-border spinner-border-sm text-info" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </span>
                                    </h4>
                                </div>
                                <div id="cedant-types-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="text-muted fs-12" id="stat-types-breakdown">
                                        Loading...
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-warning-transparent">
                                <i class="bi bi-clock-history fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Recent Activity</p>
                                    <h4 class="fw-semibold mt-1" id="stat-recent-activity">
                                        <span class="spinner-border spinner-border-sm text-warning" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </span>
                                    </h4>
                                </div>
                                <div id="recent-activity-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="text-muted fs-12" id="stat-last-update">
                                        Loading...
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Cedant Directory</h5>
                        <small class="text-muted">View and manage all registered cedants</small>
                    </div>
                    <button type="button" class="btn btn-primary" id="add_customer" aria-label="Add new cedant">
                        <i class="bi bi-plus-circle me-1"></i> Add Cedant
                    </button>
                </div>
                <div class="card-body">
                    {{ html()->form('POST', route('customer.dtl'))->id('form_customer_datatable')->open() }}
                    <input type="hidden" id="customer_id" name="customer_id" aria-hidden="true" />
                    {{ csrf_field() }}
                    {{ html()->form()->close() }}

                    <div class="table-responsivef">
                        <table class="table table-striped table-hover align-middle" id="customer-table" role="grid"
                            aria-label="Cedants table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-nowrap">ID</th>
                                    <th scope="col" class="text-nowrap">Name</th>
                                    <th scope="col" class="text-nowrap">Type</th>
                                    <th scope="col" class="text-nowrap">Tax No</th>
                                    <th scope="col" class="text-nowrap">Registration No</th>
                                    <th scope="col" class="text-nowrap">Email</th>
                                    <th scope="col" class="text-nowrap">Website</th>
                                    <th scope="col" class="text-nowrap">Debited Covers</th>
                                    <th scope="col" class="text-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            loadStatistics();

            const table = $('#customer-table').DataTable({
                pageLength: 13,
                lengthMenu: [
                    [13, 25, 50, 100, 200],
                    [13, 25, 50, 100, 200]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: {
                    url: '{{ route('cedant.data') }}',
                    type: 'GET',
                    error: function(xhr, error, code) {
                        toastr.error('Failed to load cedants data. Please refresh the page.');
                    }
                },
                columns: [{
                        data: 'customer_id',
                        name: 'customer_id',
                        title: 'ID',
                        className: 'fw-bold text-primary highlight-index',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        title: 'Name',
                        className: 'fw-semibold clickable-cell',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data || '<span class="text-muted">—</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'customer_type_name',
                        name: 'customer_type_name',
                        title: 'Type',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                const badgeClass = getBadgeClassForType(data);
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                            return data || '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        data: 'tax_no',
                        name: 'tax_no',
                        title: 'Tax No',
                        render: function(data, type, row) {
                            return data || '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        data: 'registration_no',
                        name: 'registration_no',
                        title: 'Reg No',
                        render: function(data, type, row) {
                            return data || '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
                        title: 'Email',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return `<i class="bi bi-envelope me-1"></i>${escapeHtml(data)}`;
                            }
                            return data || '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        data: 'website',
                        name: 'website',
                        title: 'Website',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                const url = data.startsWith('http') ? data : `https://${data}`;
                                return `<a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                    <i class="bi bi-globe me-1"></i>${escapeHtml(data)}
                                </a>`;
                            }
                            return data || '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        data: 'debited_covers',
                        name: 'debited_covers',
                        title: 'Covers',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const count = parseInt(data) || 0;
                                const badgeClass = count > 0 ? 'bg-info' : 'bg-secondary';
                                return `<span class="badge ${badgeClass}">${count}</span>`;
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'process',
                        name: 'process',
                        title: 'Actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Cedant actions">
                                        <button type="button"
                                                class="btn btn-outline-primary process_customer"
                                                data-customer-id="${row.customer_id}"
                                                title="View details"
                                                aria-label="View details for ${escapeHtml(row.name)}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-danger remove_process_customer"
                                                data-cedant-id="${row.customer_id}"
                                                data-name="${escapeHtml(row.name)}"
                                                title="Clear all covers"
                                                aria-label="Clear all covers for ${escapeHtml(row.name)}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                `;
                            }
                            return data;
                        }
                    }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "Search cedants:",
                    searchPlaceholder: "Enter search term...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ cedants",
                    infoEmpty: "No cedants available",
                    infoFiltered: "(filtered from _MAX_ total cedants)",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>'
                    },
                    emptyTable: "No cedants registered yet",
                    zeroRecords: "No matching cedants found",
                    loadingRecords: "Loading cedants..."
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });

            $('#customer-table').on('click', '.process_customer', function(e) {
                e.preventDefault();
                const customerId = $(this).data('customer-id');

                if (!customerId) {
                    toastr.warning('Invalid customer ID');
                    return;
                }

                $("#customer_id").val(customerId);
                $("#form_customer_datatable").submit();
            });

            $('#customer-table tbody').on('dblclick', 'tr', function() {
                const rowData = table.row(this).data();

                if (!rowData || !rowData.customer_id) {
                    toastr.warning('Invalid customer data');
                    return;
                }

                $("#customer_id").val(rowData.customer_id);
                $("#form_customer_datatable").submit();
            });

            $('#customer-table tbody').on('click', 'tr', function() {
                $('#customer-table tbody tr').removeClass('table-active');
                $(this).addClass('table-active');
            });

            $(document).on('click', '.remove_process_customer', function(e) {
                e.preventDefault();

                const cedantId = $(this).data('cedant-id');
                const cedantName = $(this).data('name');

                if (!cedantId) {
                    toastr.error('Invalid cedant ID');
                    return;
                }

                Swal.fire({
                    title: 'Clear All Covers?',
                    html: `
                        <div class="text-start">
                            <p class="mb-2"><strong>Warning:</strong> This is a destructive action!</p>
                            <p class="mb-2">You are about to permanently delete:</p>
                            <ul class="text-danger">
                                <li>All insurance covers for <strong>${escapeHtml(cedantName)}</strong></li>
                                <li>All related data and records</li>
                            </ul>
                            <p class="mb-0"><strong>This action cannot be undone.</strong></p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, delete everything',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        clearCedantData(cedantId, cedantName);
                    }
                });
            });

            $("#add_customer").click(function(e) {
                e.preventDefault()
                // window.location.href = ''; //"{{-- route('customer.create') --}}";
            });

            function clearCedantData(cedantId, cedantName) {
                const loadingToast = toastr.info('Processing request...', 'Please wait', {
                    timeOut: 0,
                    extendedTimeOut: 0,
                    closeButton: false
                });

                $.ajax({
                    url: "{{ route('customer.clear_cedant_data') }}",
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        id: cedantId
                    }),
                    success: function(data) {
                        toastr.clear(loadingToast);

                        if (data.status === 202 || data.status === 201) {
                            toastr.success(data.message || 'All covers cleared successfully',
                                'Success');
                            setTimeout(() => {
                                table.ajax.reload(null, false);
                            }, 1500);
                        } else if (data.status === 422) {
                            showServerSideValidationErrors(data.errors);
                        } else {
                            toastr.error(data.message || 'Failed to clear covers', 'Error');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.clear(loadingToast);

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            showServerSideValidationErrors(xhr.responseJSON.errors);
                        } else if (xhr.status === 404) {
                            toastr.error('Cedant not found or already deleted', 'Error');
                        } else if (xhr.status === 500) {
                            toastr.error('Server error occurred. Please try again or contact support.',
                                'Error');
                        } else {
                            toastr.error('An internal error occurred. Please try again.', 'Error');
                        }
                    }
                });
            }

            function getBadgeClassForType(type) {
                const typeLower = type.toLowerCase();
                const typeMap = {
                    'corporate': 'bg-primary',
                    'individual': 'bg-success',
                    'broker': 'bg-info',
                    'agent': 'bg-warning'
                };
                return typeMap[typeLower] || 'bg-secondary';
            }

            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }

            function loadStatistics() {
                $.ajax({
                    url: "{{ route('cedant.statistics') }}",
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        updateStatistics(data);
                    },
                    error: function(xhr, status, error) {
                        updateStatisticsError();

                        if (xhr.status === 404) {
                            toastr.warning(
                                'Statistics endpoint not found. Please check route configuration.');
                        } else if (xhr.status === 500) {
                            toastr.error('Server error loading statistics. Please check logs.');
                        } else if (xhr.status === 0) {
                            toastr.error('Network error. Please check your connection.');
                        }
                    }
                });
            }

            function updateStatistics(data) {
                $('#stat-total-cedants').html(formatNumber(data.total_cedants || 0));

                const totalChange = data.total_change || 0;
                const totalChangeHtml = totalChange >= 0 ?
                    `<i class="ri-arrow-up-s-line align-middle"></i>${Math.abs(totalChange)}%` :
                    `<i class="ri-arrow-down-s-line align-middle"></i>${Math.abs(totalChange)}%`;
                const totalChangeBadge = totalChange >= 0 ? 'bg-primary-transparent' : 'bg-danger-transparent';
                $('#stat-total-change').html(totalChangeHtml).removeClass().addClass(`badge ${totalChangeBadge}`);

                $('#stat-active-covers').html(formatNumber(data.active_covers || 0));

                const coversChange = data.covers_change || 0;
                const coversChangeHtml = coversChange >= 0 ?
                    `<i class="ri-arrow-up-s-line align-middle"></i>${Math.abs(coversChange)}%` :
                    `<i class="ri-arrow-down-s-line align-middle"></i>${Math.abs(coversChange)}%`;
                const coversChangeBadge = coversChange >= 0 ? 'bg-success-transparent' : 'bg-danger-transparent';
                $('#stat-covers-change').html(coversChangeHtml).removeClass().addClass(
                    `badge ${coversChangeBadge}`);

                $('#stat-cedant-types').html(data.unique_types || 0);

                let typesBreakdown = '';
                if (data.types_breakdown && data.types_breakdown.length > 0) {
                    const topTypes = data.types_breakdown.slice(0, 2);
                    typesBreakdown = topTypes.map(t => `${t.name}: ${t.count}`).join(', ');
                } else {
                    typesBreakdown = 'No types available';
                }
                $('#stat-types-breakdown').text(typesBreakdown);

                $('#stat-recent-activity').html(formatNumber(data.recent_activity || 0));

                const lastUpdate = data.last_update ? formatRelativeTime(data.last_update) : 'No recent activity';
                $('#stat-last-update').text(lastUpdate);

                if (data.sparkline_data) {
                    renderSparklines(data.sparkline_data);
                }
            }

            function updateStatisticsError() {
                $('#stat-total-cedants').html('<span class="text-danger">—</span>');
                $('#stat-active-covers').html('<span class="text-danger">—</span>');
                $('#stat-cedant-types').html('<span class="text-danger">—</span>');
                $('#stat-recent-activity').html('<span class="text-danger">—</span>');
                $('#stat-total-change').html('N/A').removeClass().addClass('badge bg-secondary');
                $('#stat-covers-change').html('N/A').removeClass().addClass('badge bg-secondary');
                $('#stat-types-breakdown').text('Error loading data');
                $('#stat-last-update').text('Unable to fetch');
            }

            function formatNumber(num) {
                if (num >= 1000000) {
                    return (num / 1000000).toFixed(1) + 'M';
                } else if (num >= 1000) {
                    return (num / 1000).toFixed(1) + 'K';
                }
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            function formatRelativeTime(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMs / 3600000);
                const diffDays = Math.floor(diffMs / 86400000);

                if (diffMins < 1) return 'Just now';
                if (diffMins < 60) return `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;
                if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
                if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
                if (diffDays < 30)
                    return `${Math.floor(diffDays / 7)} week${Math.floor(diffDays / 7) > 1 ? 's' : ''} ago`;
                return date.toLocaleDateString();
            }

            function renderSparklines(sparklineData) {

                if (sparklineData.total_cedants && sparklineData.total_cedants.length > 0) {
                    const sparkHtml = generateSparkline(sparklineData.total_cedants, 'primary');
                    $('#total-cedants-spark').html(sparkHtml);
                }

                if (sparklineData.active_covers && sparklineData.active_covers.length > 0) {
                    const sparkHtml = generateSparkline(sparklineData.active_covers, 'success');
                    $('#active-covers-spark').html(sparkHtml);
                }
            }

            function generateSparkline(data, color) {
                const max = Math.max(...data);
                const bars = data.map(val => {
                    const height = max > 0 ? Math.round((val / max) * 20) : 0;
                    return `<span class="sparkline-bar bg-${color}" style="height: ${height}px;" title="${val}"></span>`;
                }).join('');

                return `<div class="sparkline-container">${bars}</div>`;
            }

            table.on('draw', function() {
                loadStatistics();
            });
        });
    </script>
@endpush
