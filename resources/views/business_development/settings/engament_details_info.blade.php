@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Engament Details</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage nature-of-engagement options used across onboarding forms.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('bd.engament-details.index') }}">Business Development</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Engament Details</li>
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
                                <i class="bi bi-list-check fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0">Total Options</p>
                            <h4 class="fw-semibold mt-1" id="stat-total">0</h4>
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
                                <i class="bi bi-check-circle fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0">Active</p>
                            <h4 class="fw-semibold mt-1" id="stat-active">0</h4>
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
                            <span class="avatar avatar-md avatar-rounded bg-danger-transparent">
                                <i class="bi bi-slash-circle fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0">Inactive</p>
                            <h4 class="fw-semibold mt-1" id="stat-inactive">0</h4>
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
                            <p class="text-muted mb-0">Last Refresh</p>
                            <h4 class="fw-semibold mt-1" id="stat-last-refresh">-</h4>
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
                        <h5 class="card-title mb-0">Engament Details List</h5>
                        <small class="text-muted">Options shown in the "Nature of engagement" dropdown.</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="addEngamentBtn" data-bs-toggle="modal"
                        data-bs-target="#engamentDetailModal">
                        <i class='bx bx-plus me-1'></i>
                        Add Engament Detail
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover" id="engamentDetailsTable"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 4%;">#</th>
                                    <th>Engagement Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Sort Order</th>
                                    <th>Created At</th>
                                    <th style="width: 12%;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade effect-scale" id="engamentDetailModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="engamentDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="engamentDetailForm">
                    @csrf
                    <input type="hidden" name="id" id="ed-id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="engamentDetailModalLabel">Add Engament Detail</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="ed-name" class="form-label fw-semibold">Engagement Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ed-name" name="name" maxlength="100"
                                placeholder="e.g. Underwriter Tender" required>
                        </div>

                        <div class="mb-3">
                            <label for="ed-description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="ed-description" name="description" rows="3" maxlength="500"
                                placeholder="Optional description"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ed-status" class="form-label fw-semibold">Status</label>
                                <select class="form-select" id="ed-status" name="status">
                                    <option value="A">Active</option>
                                    <option value="I">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ed-sort-order" class="form-label fw-semibold">Sort Order</label>
                                <input type="number" class="form-control" id="ed-sort-order" name="sort_order"
                                    min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="engamentSubmitBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #engamentDetailsTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .badge-status {
            font-size: 11px;
            font-weight: 600;
            padding: 0.35rem 0.5rem;
            border-radius: 999px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function() {
            'use strict';

            const tableSelector = '#engamentDetailsTable';
            const tableDataUrl = @json(route('bd.engament-details.data'));
            const storeUrl = @json(route('bd.engament-details.store'));
            const deleteUrl = @json(route('bd.engament-details.delete'));

            const modalSelector = '#engamentDetailModal';
            const formSelector = '#engamentDetailForm';

            let dataTable = null;

            function nowTimeLabel() {
                return new Date().toLocaleTimeString();
            }

            function updateStatsFromTable(json) {
                const rows = (json && json.data) ? json.data : [];
                const total = Number((json && json.recordsTotal) || rows.length || 0);
                const active = rows.filter(r => (r.status || '').toUpperCase() === 'A').length;
                const inactive = rows.filter(r => (r.status || '').toUpperCase() !== 'A').length;

                $('#stat-total').text(total);
                $('#stat-active').text(active);
                $('#stat-inactive').text(inactive);
                $('#stat-last-refresh').text(nowTimeLabel());
            }

            function resetModal() {
                $(formSelector).trigger('reset');
                $('#ed-id').val('');
                $('#ed-status').val('A');
                $('#ed-sort-order').val(0);
                $('#engamentDetailModalLabel').text('Add Engament Detail');
                $('#engamentSubmitBtn').text('Save');
            }

            function showToast(type, message) {
                if (typeof toastr !== 'undefined') {
                    toastr[type](message);
                    return;
                }
                alert(message);
            }

            $(function() {
                if (!$.fn.DataTable) {
                    return;
                }

                dataTable = $(tableSelector).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: tableDataUrl,
                        data: function(d) {
                            d.filter_status = $('#filter-status').val();
                        },
                        dataSrc: function(json) {
                            updateStatsFromTable(json);
                            return json.data || [];
                        }
                    },
                    order: [
                        [4, 'asc'],
                        [1, 'asc']
                    ],
                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'name',
                            defaultContent: '-'
                        },
                        {
                            data: 'description',
                            defaultContent: '-',
                            render: function(data) {
                                const value = (data || '').trim();
                                return value ? value : '-';
                            }
                        },
                        {
                            data: 'status',
                            render: function(data) {
                                const status = (data || '').toUpperCase() === 'A' ? 'Active' :
                                    'Inactive';
                                const cls = status === 'Active' ?
                                    'bg-success-transparent text-success' :
                                    'bg-danger-transparent text-danger';
                                return `<span class="badge ${cls} badge-status">${status}</span>`;
                            }
                        },
                        {
                            data: 'sort_order',
                            defaultContent: 0
                        },
                        {
                            data: 'created_at',
                            defaultContent: '-'
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(row) {
                                const encoded = encodeURIComponent(JSON.stringify(row));
                                return `<div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-engament" data-row="${encoded}">Edit</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-engament" data-id="${row.id}">Delete</button>
                                </div>`;
                            }
                        }
                    ]
                });

                $('#filter-status').on('change', function() {
                    dataTable.ajax.reload();
                });

                $('#resetFiltersBtn').on('click', function() {
                    $('#filter-status').val('');
                    dataTable.ajax.reload();
                });

                $('#addEngamentBtn').on('click', resetModal);

                $(document).on('click', '.edit-engament', function() {
                    const raw = $(this).attr('data-row') || '';
                    const row = JSON.parse(decodeURIComponent(raw));

                    $('#ed-id').val(row.id || '');
                    $('#ed-name').val(row.name || '');
                    $('#ed-description').val(row.description || '');
                    $('#ed-status').val((row.status || 'A').toUpperCase() === 'I' ? 'I' : 'A');
                    $('#ed-sort-order').val(Number(row.sort_order || 0));

                    $('#engamentDetailModalLabel').text('Edit Engament Detail');
                    $('#engamentSubmitBtn').text('Update');
                    $(modalSelector).modal('show');
                });

                $(formSelector).on('submit', function(e) {
                    e.preventDefault();

                    const formData = $(this).serialize();
                    $.ajax({
                        url: storeUrl,
                        method: 'POST',
                        data: formData,
                        success: function(resp) {
                            $(modalSelector).modal('hide');
                            dataTable.ajax.reload(null, false);
                            showToast('success', resp.message || 'Saved successfully.');
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON && xhr.responseJSON.message ?
                                xhr.responseJSON
                                .message : 'Failed to save engament detail.';
                            showToast('error', message);
                        }
                    });
                });

                $(document).on('click', '.delete-engament', function() {
                    const id = $(this).data('id');
                    if (!id) return;

                    const runDelete = function() {
                        $.ajax({
                            url: deleteUrl,
                            method: 'POST',
                            data: {
                                _token: @json(csrf_token()),
                                id: id
                            },
                            success: function(resp) {
                                dataTable.ajax.reload(null, false);
                                showToast('success', resp.message || 'Deleted successfully.');
                            },
                            error: function(xhr) {
                                const message = xhr.responseJSON && xhr.responseJSON.message ?
                                    xhr.responseJSON
                                    .message : 'Failed to delete engament detail.';
                                showToast('error', message);
                            }
                        });
                    };

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Delete this engament detail?',
                            text: 'This action cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#d33'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                runDelete();
                            }
                        });
                        return;
                    }

                    if (confirm('Delete this engament detail?')) {
                        runDelete();
                    }
                });

                $(modalSelector).on('hidden.bs.modal', resetModal);
            });
        })();
    </script>
@endpush
