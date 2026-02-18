@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Schedule Headers</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage Business Development schedule header definitions.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('bd.schedule-headers.index') }}">Business Development</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Schedule Headers</li>
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
                                    <p class="text-muted mb-0">Total Headers</p>
                                    <h4 class="fw-semibold mt-1" id="stat-total-headers">0</h4>
                                </div>
                                <div id="total-cedants-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="badge bg-primary-transparent" id="stat-total-change">All records</span>
                                    <span class="text-muted ms-2 fs-12">Server total</span>
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
                                    <p class="text-muted mb-0">Visible Rows</p>
                                    <h4 class="fw-semibold mt-1" id="stat-visible-rows">0</h4>
                                </div>
                                <div id="active-covers-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="badge bg-success-transparent" id="stat-covers-change">Current page</span>
                                    <span class="text-muted ms-2 fs-12">After filters</span>
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
                                    <p class="text-muted mb-0">Business Types</p>
                                    <h4 class="fw-semibold mt-1" id="stat-business-types">0</h4>
                                </div>
                                <div id="cedant-types-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="text-muted fs-12" id="stat-types-breakdown">-</span>
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
                                    <p class="text-muted mb-0">Last Refresh</p>
                                    <h4 class="fw-semibold mt-1" id="stat-last-refresh">-</h4>
                                </div>
                                <div id="recent-activity-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="text-muted fs-12" id="stat-last-update">Waiting for data...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Schedule Headers List</h5>
                        <small class="text-muted">View and manage schedule headers</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="addScheduleHeaderBtn" data-bs-toggle="modal"
                        data-bs-target="#addScheduleHeaderModal" aria-label="Add new schedule header">
                        <i class='bx bx-plus me-1'></i>
                        Add Schedule Header
                    </button>
                </div>
                <div class="card-body">
                    {{-- Filters --}}
                    <div class="row g-2 mb-3 align-items-end">
                        <div class="col-md-3">
                            <label for="filter-business-type" class="form-label fw-semibold fs-12 mb-1">Business
                                Type</label>
                            <select class="form-select form-select-sm" id="filter-business-type">
                                <option value="">All</option>
                                <option value="FAC">Facultative</option>
                                <option value="TRT">Treaty</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter-class-group" class="form-label fw-semibold fs-12 mb-1">Class Group</label>
                            <select class="form-select form-select-sm" id="filter-class-group">
                                <option value="">All</option>
                                @foreach ($classGroups as $cg)
                                    <option value="{{ $cg->group_code }}">{{ $cg->group_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter-class-name" class="form-label fw-semibold fs-12 mb-1">Class Name</label>
                            <select class="form-select form-select-sm" id="filter-class-name">
                                <option value="">All</option>
                                @foreach ($classes as $cls)
                                    <option value="{{ $cls->class_code }}"
                                        data-group="{{ $cls->class_group_code ?? '' }}">{{ $cls->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="resetFiltersBtn">
                                <i class='bx bx-reset me-1'></i> Reset
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover" id="scheduleHeaderTable"
                            aria-label="Schedule headers table" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">ID</th>
                                    <th>Schedule Title</th>
                                    <th>Business Type</th>
                                    <th>Class Group</th>
                                    <th>Class Name</th>
                                    <th>Position</th>
                                    <th>Amount Field</th>
                                    <th>Sum Insured Type</th>
                                    <th>Data Determinant</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade effect-scale" id="addScheduleHeaderModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="addScheduleHeaderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="max-width: 65%;">
            <div class="modal-content">
                <form method="POST" action="{{ route('bd.schedule.header.store') }}" id="addScheduleHeaderForm">
                    @csrf
                    <input type="hidden" name="id" id="sh-id" value="" />

                    <div class="modal-header">
                        <h5 class="modal-title" id="addScheduleHeaderModalLabel">Add Schedule Header</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            {{-- Left Column: Header Information --}}
                            <div class="col-md-7">
                                <div class="card shadow-sm mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">Header Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label for="sh-name" class="form-label fw-semibold">
                                                    Name <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="sh-name" name="name"
                                                    maxlength="100" placeholder="Enter schedule header name" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="sh-position" class="form-label fw-semibold">
                                                    Position <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control" id="sh-position"
                                                    name="position" placeholder="0" min="0" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sh-business-type" class="form-label fw-semibold">
                                                    Business Type
                                                </label>
                                                <select class="form-select" id="sh-business-type" name="business_type">
                                                    <option value="">-- Select Type --</option>
                                                    <option value="FAC">Facultative</option>
                                                    <option value="TRT">Treaty</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sh-amount-field" class="form-label fw-semibold">
                                                    Amount Field <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="sh-amount-field" name="amount_field"
                                                    required>
                                                    <option value="">-- Select --</option>
                                                    <option value="Y">Yes</option>
                                                    <option value="N">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Classification Section (FAC-dependent) --}}
                                <div class="card shadow-sm mb-3 fac-dependent" style="display:none;">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">Classification</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="sh-class-group" class="form-label fw-semibold">
                                                    Class Group
                                                </label>
                                                <select class="form-select" id="sh-class-group" name="class_group">
                                                    <option value="">-- Select Class Group --</option>
                                                    @foreach ($classGroups as $classGroup)
                                                        <option value="{{ $classGroup->group_code }}">
                                                            {{ $classGroup->group_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sh-class" class="form-label fw-semibold">
                                                    Class
                                                </label>
                                                <select class="form-select" id="sh-class" name="class">
                                                    <option value="">-- Select Class --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Field Configuration --}}
                            <div class="col-md-5">
                                <div class="card shadow-sm mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">Field Configuration</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3 amount-field-dependent" style="display:none;">
                                            <label for="sh-sum-insured-type" class="form-label fw-semibold">
                                                Type of Sum Insured
                                            </label>
                                            <select class="form-select" id="sh-sum-insured-type" name="sum_insured_type">
                                                <option value="">-- Select Sum Insured Type --</option>
                                                @foreach ($type_of_sum_insured as $sumType)
                                                    <option value="{{ $sumType->sum_insured_code }}">
                                                        {{ $sumType->sum_insured_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-0 amount-field-dependent" style="display:none;">
                                            <label for="sh-data-determinant" class="form-label fw-semibold">
                                                Data Determinant
                                            </label>
                                            <select class="form-select" id="sh-data-determinant" name="data_determinant">
                                                <option value="">-- Select Data Determinant --</option>
                                                <option value="COM">Commission</option>
                                                <option value="PREM">Premium</option>
                                                <option value="SI">Sum Insured</option>
                                            </select>
                                        </div>
                                        <div class="amount-field-placeholder text-center text-muted py-4">
                                            <i class="bi bi-info-circle fs-3 d-block mb-2 opacity-50"></i>
                                            <small>Select <strong>"Yes"</strong> for Amount Field to configure
                                                additional options.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-check me-1"></i> Save Schedule Header
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Loading overlay */
        .dataTables_processing {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Action buttons styling */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-start;
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Empty state styling */
        .dataTables_empty {
            padding: 3rem 1rem !important;
            text-align: center;
            color: #6c757d;
        }

        /* Table styling improvements */
        #scheduleHeaderTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        #scheduleHeaderTable tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }
        }

        /* Add Schedule Header Modal */
        #addScheduleHeaderModal .modal-body {
            max-height: 75vh;
            overflow-y: auto;
        }

        #addScheduleHeaderModal .form-label {
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 13px;
        }

        #addScheduleHeaderModal .card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        #addScheduleHeaderModal .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
@endpush

@push('script')
    <script>
        (function() {
            'use strict';

            const tableSelector = '#scheduleHeaderTable';
            const dataUrl = @json(route('bd.quote.schedule.header.data'));
            const addModalSelector = '#addScheduleHeaderModal';
            const addFormSelector = '#addScheduleHeaderForm';
            const businessTypeSelector = '#sh-business-type';
            const amountFieldSelector = '#sh-amount-field';
            const classSelector = '#sh-class';
            const classGroupSelector = '#sh-class-group';
            const getClassUrl = @json(route('get_class'));
            const scheduleHeaderFormUrl = @json(route('schedule.header.form'));
            const deleteScheduleHeaderUrl = @json(route('delete.schedule.header'));
            const csrfToken = @json(csrf_token());
            const classGroupMap = @json($classGroups->pluck('group_name', 'group_code'));
            const classNameMap = @json($classes->pluck('class_name', 'class_code'));

            $(function() {
                if (!$.fn.DataTable) {
                    return;
                }

                const datatableColumns = [{
                        data: 'id',
                        name: 'id',
                        defaultContent: '-'
                    },
                    {
                        data: null,
                        name: 'schedule_title',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            return row.schedule_title || row.name || row.header_name || row
                                .clause_title ||
                                '-';
                        }
                    },
                    {
                        data: null,
                        name: 'business_type',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            const busType = (row.business_type || row.bus_type || '').toString().trim()
                                .toUpperCase();
                            if (busType === 'FAC')
                                return '<span class="badge bg-info-transparent">Facultative</span>';
                            if (busType === 'TRT')
                                return '<span class="badge bg-primary-transparent">Treaty</span>';
                            return busType || '-';
                        }
                    },
                    {
                        data: null,
                        name: 'class_group',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            const classGroupValue = (row.class_group || '').toString().trim();
                            const classGroupCode = (row.class_group_code || '').toString().trim();
                            const normalizedGroupValue = classGroupValue.toUpperCase();
                            const normalizedGroupCode = classGroupCode.toUpperCase();

                            return row.class_group_name ||
                                classGroupMap[classGroupValue] ||
                                classGroupMap[normalizedGroupValue] ||
                                classGroupMap[classGroupCode] ||
                                classGroupMap[normalizedGroupCode] ||
                                classGroupValue ||
                                classGroupCode ||
                                '-';
                        }
                    },
                    {
                        data: null,
                        name: 'class_name',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            const classCode = (row.class_code || row.class || '').toString().trim();
                            const className = (row.class_name || '').toString().trim() ||
                                classNameMap[classCode] ||
                                classNameMap[classCode.toUpperCase()] ||
                                '';

                            if (classCode && className && classCode !== className) {
                                return `${classCode} - ${className}`;
                            }

                            return className || classCode || '-';
                        }
                    },
                    {
                        data: 'position',
                        name: 'position',
                        defaultContent: '-'
                    },
                    {
                        data: null,
                        name: 'amount_field',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            const val = (row.amount_field || '').toString().trim().toUpperCase();
                            if (val === 'Y')
                                return '<span class="badge bg-success-transparent">Yes</span>';
                            if (val === 'N')
                                return '<span class="badge bg-secondary-transparent">No</span>';
                            return val || '-';
                        }
                    },
                    {
                        data: 'sum_insured_type',
                        name: 'sum_insured_type',
                        defaultContent: '-'
                    },
                    {
                        data: null,
                        name: 'data_determinant',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            const val = (row.data_determinant || '').toString().trim().toUpperCase();
                            if (val === 'COM') return 'Commission';
                            if (val === 'PREM') return 'Premium';
                            if (val === 'SI') return 'Sum Insured';
                            return val || '-';
                        }
                    },
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        defaultContent: '-',
                        render: function(data, type, row) {
                            if (row.action) {
                                return row.action;
                            }

                            const rowId = (row.id || '').toString().trim();
                            if (!rowId) {
                                return '-';
                            }

                            const rowJson = encodeURIComponent(JSON.stringify(row));
                            return `
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-outline-dark btn-sm action-btn edit-schedule-header" data-row="${rowJson}"><i class="fas fa-edit me-1"></i>Edit</button>
                                    <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-schedule-header" data-id="${rowId}" data-title="${(row.schedule_title || row.name || '').toString().trim()}"><i class="fas fa-trash-alt me-1"></i>Remove</button>
                                </div>
                            `;
                        }
                    }
                ];

                const dataTable = $(tableSelector).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: dataUrl,
                        type: 'GET',
                        data: function(d) {
                            d.filter_business_type = $('#filter-business-type').val();
                            d.filter_class_group = $('#filter-class-group').val();
                            d.filter_class_name = $('#filter-class-name').val();
                        },
                        error: function(xhr, error) {
                            toastr.error('Failed to load table data.');
                        }
                    },
                    columns: datatableColumns,
                    order: [
                        [0, 'asc']
                    ],
                    pageLength: 25
                });

                // Filter handlers
                $('#filter-business-type, #filter-class-group, #filter-class-name').on('change', function() {
                    dataTable.ajax.reload(null, true);
                });

                // Cascade: when class group changes, filter class name options
                $('#filter-class-group').on('change', function() {
                    const selectedGroup = $(this).val();
                    const $className = $('#filter-class-name');
                    $className.val('');
                    $className.find('option').each(function() {
                        const optGroup = $(this).data('group') || '';
                        if (!$(this).val() || !selectedGroup) {
                            $(this).show();
                        } else {
                            $(this).toggle(optGroup.toString().toUpperCase() === selectedGroup
                                .toUpperCase());
                        }
                    });
                });

                // Reset all filters
                $('#resetFiltersBtn').on('click', function() {
                    $('#filter-business-type').val('');
                    $('#filter-class-group').val('').trigger('change');
                    $('#filter-class-name').val('');
                    dataTable.ajax.reload(null, true);
                });

                // Edit: populate modal with row data and open it
                $(document).on('click', '.edit-schedule-header', function() {
                    let row;
                    try {
                        const rawData = $(this).data('row');
                        // jQuery auto-parses data attributes; handle both string and object
                        if (typeof rawData === 'string') {
                            row = JSON.parse(decodeURIComponent(rawData));
                        } else {
                            row = rawData;
                        }
                    } catch (e) {
                        toastr.error('Failed to load schedule header data.');
                        return;
                    }
                    if (!row || !row.id) return;

                    // Reset form first
                    $(addFormSelector)[0].reset();
                    scheduleHeaderValidator.resetForm();
                    $(addFormSelector).find('.is-invalid').removeClass('is-invalid');

                    // Populate fields
                    $('#sh-id').val(row.id);
                    $('#sh-name').val(row.name || row.schedule_title || '');
                    $('#sh-position').val(row.position != null ? row.position : '');

                    const busType = (row.business_type || row.bus_type || '').toString().trim()
                        .toUpperCase();
                    $('#sh-business-type').val(busType);

                    const amtField = (row.amount_field != null ? row.amount_field : '').toString()
                        .trim().toUpperCase();
                    $('#sh-amount-field').val(amtField);

                    $('#sh-sum-insured-type').val(
                        row.sum_insured_type || row.type_of_sum_insured || ''
                    );

                    const dataDet = (row.data_determinant != null ? row.data_determinant : '')
                        .toString().trim().toUpperCase();
                    $('#sh-data-determinant').val(dataDet);

                    const classGroupVal = (row.class_group || row.class_group_code || '').toString()
                        .trim();
                    $('#sh-class-group').val(classGroupVal);

                    // Update dependent visibility
                    syncFacFieldRequirements();
                    syncAmountDependentFields();

                    // Update modal title
                    $('#addScheduleHeaderModalLabel').text('Edit Schedule Header');

                    // Load classes by group, then set selected class
                    const classValue = (row.class || row.class_code || '').toString().trim();
                    if (classGroupVal) {
                        $.ajax({
                            url: getClassUrl,
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                class_group: classGroupVal
                            },
                            success: function(resp) {
                                const $cls = $(classSelector);
                                $cls.empty().append(
                                    '<option value="">-- Select Class --</option>');
                                if (Array.isArray(resp)) {
                                    resp.forEach(function(item) {
                                        if (!item || !item.class_code) return;
                                        const label = item.class_name ?
                                            `${item.class_code} - ${item.class_name}` :
                                            item.class_code;
                                        $cls.append(
                                            `<option value="${item.class_code}">${label}</option>`
                                        );
                                    });
                                }
                                $cls.val(classValue);
                            }
                        });
                    }

                    // Open the modal
                    isEditMode = true;
                    const modal = bootstrap.Modal.getOrCreateInstance(document.querySelector(
                        addModalSelector));
                    modal.show();
                });

                // Remove: SweetAlert confirmation
                $(document).on('click', '.remove-schedule-header', function() {
                    const id = ($(this).data('id') || '').toString().trim();
                    const title = ($(this).data('title') || '').toString().trim();
                    if (!id) return;

                    const label = title ? `"${title}"` : 'this schedule header';

                    Swal.fire({
                        title: 'Are you sure?',
                        html: `You are about to delete <strong>${label}</strong>. This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Yes, delete it',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then(function(result) {
                        if (!result.isConfirmed) return;

                        $.ajax({
                            url: deleteScheduleHeaderUrl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                _token: csrfToken,
                                id: id
                            },
                            success: function(resp) {
                                if (resp && resp.success) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: resp.message ||
                                            'Schedule header deleted successfully.',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    dataTable.ajax.reload(null, false);
                                    return;
                                }
                                Swal.fire('Error', (resp && resp.message) ||
                                    'Failed to delete schedule header.', 'error'
                                );
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Failed to delete schedule header.', 'error'
                                );
                            }
                        });
                    });
                });

                function syncFacFieldRequirements() {
                    const isFac = ($(businessTypeSelector).val() || '').toUpperCase() === 'FAC';
                    $('.fac-dependent').toggle(isFac);
                    $(classSelector).prop('required', isFac);
                    $(classGroupSelector).prop('required', isFac);
                }

                function syncAmountDependentFields() {
                    const hasAmountField = ($(amountFieldSelector).val() || '') === 'Y';
                    $('.amount-field-dependent').toggle(hasAmountField);
                    $('.amount-field-placeholder').toggle(!hasAmountField);
                }

                function loadClassesByGroup() {
                    const classGroup = ($(classGroupSelector).val() || '').trim();
                    const $class = $(classSelector);
                    $class.empty().append('<option value="">-- Select Class --</option>');
                    if (!classGroup) {
                        return;
                    }

                    $.ajax({
                        url: getClassUrl,
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            class_group: classGroup
                        },
                        success: function(resp) {
                            if (!Array.isArray(resp)) {
                                return;
                            }
                            resp.forEach(function(item) {
                                if (!item || !item.class_code) {
                                    return;
                                }
                                const label = item.class_name ?
                                    `${item.class_code} - ${item.class_name}` :
                                    item.class_code;
                                $class.append(
                                    `<option value="${item.class_code}">${label}</option>`
                                );
                            });
                        }
                    });
                }

                // jQuery Validate for the Add Schedule Header form
                const scheduleHeaderValidator = $(addFormSelector).validate({
                    rules: {
                        name: {
                            required: true,
                            maxlength: 100
                        },
                        position: {
                            required: true,
                            digits: true,
                            min: 0
                        },
                        business_type: {
                            required: true
                        },
                        amount_field: {
                            required: true
                        },
                        class_group: {
                            required: {
                                depends: function() {
                                    return ($(businessTypeSelector).val() || '').toUpperCase() ===
                                        'FAC';
                                }
                            }
                        },
                        class: {
                            required: {
                                depends: function() {
                                    return ($(businessTypeSelector).val() || '').toUpperCase() ===
                                        'FAC';
                                }
                            }
                        },
                        sum_insured_type: {
                            required: {
                                depends: function() {
                                    return ($(amountFieldSelector).val() || '') === 'Y';
                                }
                            }
                        },
                        data_determinant: {
                            required: {
                                depends: function() {
                                    return ($(amountFieldSelector).val() || '') === 'Y';
                                }
                            }
                        }
                    },
                    messages: {
                        name: {
                            required: 'Please enter a schedule header name',
                            maxlength: 'Name cannot exceed 100 characters'
                        },
                        position: {
                            required: 'Please enter a position',
                            digits: 'Position must be a whole number',
                            min: 'Position must be 0 or greater'
                        },
                        business_type: {
                            required: 'Please select a business type'
                        },
                        amount_field: {
                            required: 'Please select an amount field option'
                        },
                        class_group: {
                            required: 'Please select a class group'
                        },
                        class: {
                            required: 'Please select a class'
                        },
                        sum_insured_type: {
                            required: 'Please select a sum insured type'
                        },
                        data_determinant: {
                            required: 'Please select a data determinant'
                        }
                    },
                    errorElement: 'span',
                    errorClass: 'text-danger small',
                    errorPlacement: function(error, element) {
                        error.insertAfter(element);
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    }
                });

                let isEditMode = false;

                $(businessTypeSelector).on('change', syncFacFieldRequirements);
                $(amountFieldSelector).on('change', syncAmountDependentFields);
                $(classGroupSelector).on('change', loadClassesByGroup);
                $(addModalSelector).on('shown.bs.modal', function() {
                    if (!isEditMode) {
                        $(addFormSelector)[0].reset();
                        $('#sh-id').val('');
                        scheduleHeaderValidator.resetForm();
                        $(addFormSelector).find('.is-invalid').removeClass('is-invalid');
                        $('#addScheduleHeaderModalLabel').text('Add Schedule Header');
                        syncFacFieldRequirements();
                        syncAmountDependentFields();
                        loadClassesByGroup();
                    }
                    isEditMode = false;
                });
            });
        })();
    </script>
@endpush
