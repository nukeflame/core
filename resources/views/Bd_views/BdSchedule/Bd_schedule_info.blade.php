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
                    <button type="button" class="btn btn-primary btn-sm" id="addScheduleHeaderBtn"
                        data-bs-toggle="modal" data-bs-target="#addScheduleHeaderModal"
                        aria-label="Add new schedule header">
                        <i class='bx bx-plus me-1'></i>
                        Add Schedule Header
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover" id="scheduleHeaderTable"
                            aria-label="Schedule headers table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addScheduleHeaderModal" tabindex="-1" aria-labelledby="addScheduleHeaderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('bd.schedule.header.store') }}" id="addScheduleHeaderForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addScheduleHeaderModalLabel">Add Schedule Header</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="sh-name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sh-name" name="name" maxlength="100"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="sh-business-type" class="form-label">Business Type</label>
                                <select class="form-select" id="sh-business-type" name="business_type">
                                    <option value="">Select type</option>
                                    <option value="FAC">Facultative</option>
                                    <option value="TRT">Treaty</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sh-position" class="form-label">Position <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="sh-position" name="position" required>
                            </div>
                            <div class="col-md-6">
                                <label for="sh-amount-field" class="form-label">Amount Field <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="sh-amount-field" name="amount_field" required>
                                    <option value="">Select amount field</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="col-md-6 amount-field-dependent" style="display:none;">
                                <label for="sh-sum-insured-type" class="form-label">Type of Sum Insured</label>
                                <select class="form-select" id="sh-sum-insured-type" name="sum_insured_type">
                                    <option value="">--select sum insured type--</option>
                                    @foreach ($type_of_sum_insured as $sumType)
                                        <option value="{{ $sumType->sum_insured_code }}">
                                            {{ $sumType->sum_insured_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 amount-field-dependent" style="display:none;">
                                <label for="sh-data-determinant" class="form-label">Data Determinant</label>
                                <select class="form-select" id="sh-data-determinant" name="data_determinant">
                                    <option value="">--Select data determinant--</option>
                                    <option value="COM">Commission</option>
                                    <option value="PREM">Premium</option>
                                    <option value="SI">Sum Insured</option>
                                </select>
                            </div>
                            <div class="col-md-6 fac-dependent" style="display:none;">
                                <label for="sh-class-group" class="form-label">Class Group</label>
                                <select class="form-select" id="sh-class-group" name="class_group">
                                    <option value="">-- Select Class Group --</option>
                                    @foreach ($classGroups as $classGroup)
                                        <option value="{{ $classGroup->group_code }}">
                                            {{ $classGroup->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 fac-dependent" style="display:none;">
                                <label for="sh-class" class="form-label">Class</label>
                                <select class="form-select" id="sh-class" name="class">
                                    <option value="">----select---</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
    </style>
@endpush

@push('script')
    <script>
        (function() {
            'use strict';

            const tableSelector = '#scheduleHeaderTable';
            const dataUrl = @json(route('bd.schedule.header.data'));
            const editUrl = @json(route('schedule.header.form'));
            const deleteUrl = @json(route('delete.schedule.header'));
            const csrfToken = @json(csrf_token());
            const addModalSelector = '#addScheduleHeaderModal';
            const addFormSelector = '#addScheduleHeaderForm';
            const businessTypeSelector = '#sh-business-type';
            const amountFieldSelector = '#sh-amount-field';
            const classSelector = '#sh-class';
            const classGroupSelector = '#sh-class-group';
            const getClassUrl = @json(route('get_class'));

            function resolveName(row) {
                return row.name || row.header_name || row.clause_title || '-';
            }

            $(function() {
                if (!$.fn.DataTable) {
                    return;
                }

                const datatable = $(tableSelector).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: dataUrl,
                        type: 'GET',
                        error: function(xhr, error) {
                            toastr.error('Failed to load table data.');
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            defaultContent: '-'
                        },
                        {
                            data: null,
                            name: 'name',
                            render: function(data, type, row) {
                                const name = resolveName(row);
                                if (type === 'display') {
                                    return $('<div>').text(name).html();
                                }
                                return name;
                            }
                        },
                        {
                            data: null,
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-start',
                            render: function(data, type, row) {
                                if (type !== 'display') {
                                    return '';
                                }

                                const id = row.id;
                                if (!id) {
                                    return '<span class="text-muted">N/A</span>';
                                }

                                return `
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-info edit-btn" data-id="${id}">
                                            <i class='bx bx-edit'></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${id}">
                                            <i class='bx bx-trash'></i> Delete
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25
                });

                $(tableSelector).on('click', '.edit-btn', function() {
                    const id = $(this).data('id');
                    window.location.href = `${editUrl}?id=${id}`;
                });

                $(tableSelector).on('click', '.delete-btn', function() {
                    const id = $(this).data('id');
                    if (!id) {
                        return;
                    }

                    if (!window.confirm('Are you sure you want to delete this record?')) {
                        return;
                    }

                    $.ajax({
                        url: deleteUrl,
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            id: id
                        },
                        success: function(response) {
                            if (response && response.success) {
                                toastr.success(response.message || 'Deleted successfully.');
                                datatable.ajax.reload(null, false);
                                return;
                            }
                            toastr.error((response && response.message) || 'Delete failed.');
                        },
                        error: function() {
                            toastr.error('Delete failed.');
                        }
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
                }

                function loadClassesByGroup() {
                    const classGroup = ($(classGroupSelector).val() || '').trim();
                    const $class = $(classSelector);
                    $class.empty().append('<option value="">----select---</option>');
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
                                const label = item.class_name ? `${item.class_code} - ${item.class_name}` :
                                    item.class_code;
                                $class.append(`<option value="${item.class_code}">${label}</option>`);
                            });
                        }
                    });
                }

                $(businessTypeSelector).on('change', syncFacFieldRequirements);
                $(amountFieldSelector).on('change', syncAmountDependentFields);
                $(classGroupSelector).on('change', loadClassesByGroup);
                $(addModalSelector).on('shown.bs.modal', function() {
                    $(addFormSelector)[0].reset();
                    syncFacFieldRequirements();
                    syncAmountDependentFields();
                    loadClassesByGroup();
                });
            });
        })();
    </script>
@endpush
