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

        .fs-12 {
            font-size: 0.75rem;
        }

        #customer-table {
            font-size: 0.875rem;
        }

        #customer-table thead th {
            font-weight: 600;
            text-transform: capitalize;
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

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        .table-stats {
            font-size: 0.9rem;
        }

        .table-stats .value {
            font-size: 1.25rem;
            font-weight: 600;
        }

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
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Customers Management</h1>
            <p class="text-muted mb-0 mt-1">View and manage all customer records</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i
                                class="bx bx-home-alt me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">List</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body table-stats">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                <i class="bi bi-people fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0">Total Customers</p>
                            <h4 class="fw-semibold mt-1 value" id="stat-total-customers">0</h4>
                            <span class="text-muted fs-12">All records</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body table-stats">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-success-transparent">
                                <i class="bi bi-funnel fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0">Filtered Results</p>
                            <h4 class="fw-semibold mt-1 value" id="stat-filtered-customers">0</h4>
                            <span class="text-muted fs-12">Current search</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body table-stats">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-info-transparent">
                                <i class="bi bi-diagram-3 fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0">Customer Types</p>
                            <h4 class="fw-semibold mt-1 value" id="stat-customer-types">0</h4>
                            <span class="text-muted fs-12" id="stat-type-summary">No data loaded</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body table-stats">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-warning-transparent">
                                <i class="bi bi-clock-history fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0">Last Refresh</p>
                            <h4 class="fw-semibold mt-1 value" id="stat-last-refresh">--:--</h4>
                            <span class="text-muted fs-12">DataTable sync time</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Customer Directory</h5>
                        <small class="text-muted">Browse and maintain registered customers</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="add_customer">
                        <i class="bx bx-plus me-1"></i> Add Customer
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="customer-table" role="grid"
                            aria-label="Customers table">
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
            const $customerTbl = $('#customer-table').DataTable({
                pageLength: 50,
                lengthMenu: [
                    [50, 100, 200],
                    [50, 100, 200]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: {
                    url: '{{ route('customer.data') }}',
                    error: function(xhr, error, code) {
                        console.error('DataTables AJAX error:', error);
                        setStatsError();
                    }
                },
                columns: [{
                        data: 'customer_id',
                        name: 'customer_id',
                        defaultContent: '<span class="text-muted">—</span>',
                        className: 'fw-bold text-primary'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        defaultContent: '<span class="text-muted">—</span>',
                        className: 'fw-semibold'
                    },
                    {
                        data: 'customer_type_name',
                        name: 'customer_type_name',
                        title: 'Customer Type',
                        defaultContent: '<span class="text-muted">—</span>',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return `<span class="badge ${getBadgeClassForType(data)}">${escapeHtml(data)}</span>`;
                            }
                            return data || '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        data: 'tax_no',
                        name: 'tax_no',
                        defaultContent: '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'registration_no',
                        name: 'registration_no',
                        defaultContent: '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'email',
                        name: 'email',
                        defaultContent: '<span class="text-muted">—</span>',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return `<a href="mailto:${escapeHtml(data)}"><i class="bi bi-envelope me-1"></i>${escapeHtml(data)}</a>`;
                            }
                            return data || '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        data: 'website',
                        name: 'website',
                        defaultContent: '<span class="text-muted">—</span>',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                const cleaned = String(data).trim().replace(/^\/+/, '');
                                const url = cleaned.startsWith('http') ? cleaned : `https://${cleaned}`;
                                return `<a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer"><i class="bi bi-globe me-1"></i>${escapeHtml(cleaned)}</a>`;
                            }
                            return data || '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        data: 'debited_covers',
                        name: 'debited_covers',
                        defaultContent: '<span class="text-muted">—</span>',
                        className: 'text-center',
                        render: function(data, type, row) {
                            const count = parseInt(data, 10) || 0;
                            if (type === 'display') {
                                const badgeClass = count > 0 ? 'bg-info' : 'bg-secondary';
                                return `<span class="badge ${badgeClass}">${count}</span>`;
                            }
                            return count;
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        title: 'Actions',
                        searchable: false,
                        orderable: false,
                        defaultContent: '<span class="text-muted">—</span>',
                        className: 'text-center'
                    }
                ],
                language: {
                    processing: "Processing...",
                    search: "Search customers:",
                    searchPlaceholder: "Enter search term...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    emptyTable: "No customers available",
                    zeroRecords: "No matching customers found"
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            $customerTbl.on('click', '.edit_customer', function(e) {
                e.preventDefault();
                const customerId = $(this).data('id');
                if (customerId) {
                    window.location.href = `/customer/${customerId}/edit`;
                }
            });

            $customerTbl.on('click', '.remove_process_customer', function(e) {
                e.preventDefault();

                const customerId = $(this).data('cedant-id') || $(this).data('customer-id') || $(this).data('id');
                const customerName = $(this).data('name') || 'this customer';

                if (!customerId) {
                    toastr.error('Invalid customer ID');
                    return;
                }

                Swal.fire({
                    title: 'Delete Customer?',
                    html: `
                        <div class="text-start">
                            <p class="mb-2">You are about to permanently delete <strong>${escapeHtml(customerName)}</strong> and all related data.</p>
                            <p class="mb-0 text-danger"><strong>This action cannot be undone.</strong></p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        clearCustomerData(customerId);
                    }
                });
            });

            $customerTbl.on('xhr.dt', function(e, settings, json, xhr) {
                updateStatistics(json);
            });

            $("#add_customer").on('click', function() {
                window.location.href = '{{ route('customer.form') }}';
            });

            function updateStatistics(payload) {
                if (!payload) {
                    setStatsError();
                    return;
                }

                const totalCustomers = Number(payload.recordsTotal || 0);
                const filteredCustomers = Number(payload.recordsFiltered || 0);
                const dataRows = Array.isArray(payload.data) ? payload.data : [];

                const types = {};
                dataRows.forEach(row => {
                    const key = row.customer_type_name || 'Unknown';
                    types[key] = (types[key] || 0) + 1;
                });

                const uniqueTypes = Object.keys(types).length;
                const typeSummary = Object.entries(types)
                    .sort((a, b) => b[1] - a[1])
                    .slice(0, 2)
                    .map(([name, count]) => `${name}: ${count}`)
                    .join(', ');

                $('#stat-total-customers').text(formatNumber(totalCustomers));
                $('#stat-filtered-customers').text(formatNumber(filteredCustomers));
                $('#stat-customer-types').text(formatNumber(uniqueTypes));
                $('#stat-type-summary').text(typeSummary || 'No type breakdown yet');
                $('#stat-last-refresh').text(new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                }));
            }

            function setStatsError() {
                $('#stat-total-customers').html('<span class="text-danger">—</span>');
                $('#stat-filtered-customers').html('<span class="text-danger">—</span>');
                $('#stat-customer-types').html('<span class="text-danger">—</span>');
                $('#stat-type-summary').text('Error loading data');
                $('#stat-last-refresh').text('--:--');
            }

            function formatNumber(num) {
                return Number(num || 0).toLocaleString();
            }

            function clearCustomerData(customerId) {
                const loadingToast = toastr.info('Processing request...', 'Please wait', {
                    timeOut: 0,
                    extendedTimeOut: 0,
                    closeButton: false
                });

                $.ajax({
                    url: "{{ route('customer.delete_customer_data') }}",
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        id: customerId
                    }),
                    success: function(data) {
                        toastr.clear(loadingToast);

                        if (data.status === 202 || data.status === 201) {
                            toastr.success(data.message || 'Customer and related data deleted successfully', 'Success');
                            $customerTbl.ajax.reload(null, false);
                        } else if (data.status === 422 && data.errors) {
                            const firstError = getFirstValidationError(data.errors);
                            toastr.error(firstError || 'Validation failed', 'Error');
                            $customerTbl.ajax.reload(null, false);
                        } else {
                            toastr.error(data.message || 'Failed to delete customer', 'Error');
                            $customerTbl.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        toastr.clear(loadingToast);

                        if (xhr.status === 404) {
                            toastr.error('Customer not found or already deleted', 'Error');
                            $customerTbl.ajax.reload(null, false);
                            return;
                        }

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const firstError = getFirstValidationError(xhr.responseJSON.errors);
                            toastr.error(firstError || 'Validation failed', 'Error');
                            $customerTbl.ajax.reload(null, false);
                            return;
                        }

                        toastr.error('An internal error occurred. Please try again.', 'Error');
                        $customerTbl.ajax.reload(null, false);
                    }
                });
            }

            function getFirstValidationError(errors) {
                if (!errors || typeof errors !== 'object') {
                    return null;
                }

                const firstKey = Object.keys(errors)[0];
                if (!firstKey || !Array.isArray(errors[firstKey])) {
                    return null;
                }

                return errors[firstKey][0] || null;
            }

            function getBadgeClassForType(type) {
                const typeLower = String(type).toLowerCase();
                const typeMap = {
                    corporate: 'bg-primary',
                    individual: 'bg-success',
                    broker: 'bg-info',
                    agent: 'bg-warning'
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
                return String(text).replace(/[&<>"']/g, m => map[m]);
            }
        });
    </script>
@endpush
