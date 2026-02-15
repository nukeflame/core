@extends('layouts.app')

@section('content')
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class='bx bx-check-circle me-2'></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class='bx bx-error-circle me-2'></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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

    {{-- Main Content --}}
    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-title mb-0">Schedule Headers List</div>
                    <button type="button" class="btn btn-primary btn-sm" id="addScheduleHeaderBtn"
                        aria-label="Add new schedule header">
                        <i class='bx bx-plus me-1'></i>
                        Add Schedule Header
                    </button>
                </div>
                <div class="card-body">
                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover" id="scheduleHeaderTable"
                            aria-label="Schedule headers table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Business Type</th>
                                    <th scope="col">Position</th>
                                    <th scope="col">Amount Field</th>
                                    <th scope="col">Sum Insured Type</th>
                                    <th scope="col">Data Determinant</th>
                                    <th scope="col">Class</th>
                                    <th scope="col">Class Group</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables will populate this --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden form for navigation --}}
    {{ html()->form('get', route('schedule.header.form'))->id('scheduleHeaderForm')->class('d-none')->open() }}
    <input type="hidden" name="id" id="scheduleHeaderId">
    {{ html()->form()->close() }}
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
            justify-content: center;
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
    {{-- Include SweetAlert2 for better modals (optional, remove if not available) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

    <script>
        (function() {
            'use strict';

            // Configuration
            const CONFIG = {
                routes: {
                    data: @json(route('bd.schedule.header.data')),
                    form: @json(route('schedule.header.form')),
                    delete: @json(route('delete.schedule.header'))
                },
                csrfToken: @json(csrf_token()),
                datatable: null
            };

            /**
             * Initialize DataTable with configuration
             */
            function initializeDataTable() {
                CONFIG.datatable = $('#scheduleHeaderTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: CONFIG.routes.data,
                        error: function(xhr, error, code) {
                            console.error('DataTable Ajax Error:', error);
                            toastr.error('Failed to load schedule headers. Please refresh the page.');
                        }
                    },
                    order: [
                        [0, 'asc']
                    ],
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    language: {
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                        emptyTable: '<div class="text-center py-4"><i class="bx bx-folder-open fs-1 text-muted"></i><p class="mt-2 text-muted">No schedule headers found. Create your first one to get started.</p></div>',
                        zeroRecords: '<div class="text-center py-4"><i class="bx bx-search fs-1 text-muted"></i><p class="mt-2 text-muted">No matching records found. Try adjusting your search.</p></div>'
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            width: '60px'
                        },
                        {
                            data: 'name',
                            name: 'name',
                            render: function(data, type, row) {
                                return `<strong>${data || '-'}</strong>`;
                            }
                        },
                        {
                            data: 'bus_type',
                            name: 'bus_type',
                            defaultContent: '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'position',
                            name: 'position',
                            defaultContent: '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'amount_field',
                            name: 'amount_field',
                            defaultContent: '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'sum_insured_type',
                            name: 'sum_insured_type',
                            defaultContent: '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'data_determinant',
                            name: 'data_determinant',
                            defaultContent: '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'class',
                            name: 'class',
                            defaultContent: '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'class_group',
                            name: 'class_group',
                            defaultContent: '<span class="text-muted">-</span>'
                        },
                        {
                            data: null,
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `
                                    <div class="action-buttons">
                                        <button type="button"
                                                class="btn btn-sm btn-info action-btn edit-btn"
                                                data-id="${row.id}"
                                                title="Edit schedule header"
                                                aria-label="Edit schedule header ${row.name}">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-danger action-btn delete-btn"
                                                data-id="${row.id}"
                                                data-name="${row.name}"
                                                title="Delete schedule header"
                                                aria-label="Delete schedule header ${row.name}">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    responsive: true,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
                });
            }

            /**
             * Navigate to schedule header form
             * @param {number|null} id - Schedule header ID (null for new)
             */
            function navigateToForm(id = null) {
                const url = id ? `${CONFIG.routes.form}?id=${id}` : CONFIG.routes.form;
                window.location.href = url;
            }

            /**
             * Delete schedule header
             * @param {number} id - Schedule header ID
             * @param {string} name - Schedule header name
             */
            function deleteScheduleHeader(id, name) {
                // Use SweetAlert2 if available, otherwise use native confirm
                const confirmMessage = `Are you sure you want to delete "${name}"? This action cannot be undone.`;

                // Check if SweetAlert2 is available
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Delete Schedule Header?',
                        text: confirmMessage,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performDelete(id, name);
                        }
                    });
                } else {
                    // Fallback to native confirm
                    if (confirm(confirmMessage)) {
                        performDelete(id, name);
                    }
                }
            }

            /**
             * Perform the delete operation
             * @param {number} id - Schedule header ID
             * @param {string} name - Schedule header name
             */
            function performDelete(id, name) {
                $.ajax({
                    url: CONFIG.routes.delete,
                    method: 'POST',
                    data: {
                        id: id,
                        _token: CONFIG.csrfToken
                    },
                    beforeSend: function() {
                        // Optional: Show loading indicator
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        }
                    },
                    success: function(response) {
                        if (typeof Swal !== 'undefined') {
                            Swal.close();
                        }

                        toastr.success(`Schedule header "${name}" deleted successfully`);
                        CONFIG.datatable.ajax.reload(null, false); // Stay on current page
                    },
                    error: function(xhr, status, error) {
                        if (typeof Swal !== 'undefined') {
                            Swal.close();
                        }

                        const errorMessage = xhr.responseJSON?.message ||
                        'Failed to delete schedule header';
                        toastr.error(errorMessage);
                        console.error('Delete Error:', error);

                        // Reload table to ensure data consistency
                        CONFIG.datatable.ajax.reload(null, false);
                    }
                });
            }

            /**
             * Initialize event listeners
             */
            function initializeEventListeners() {
                // Add new schedule header button
                $('#addScheduleHeaderBtn').on('click', function() {
                    navigateToForm();
                });

                // Edit button click handler (delegated)
                $('#scheduleHeaderTable').on('click', '.edit-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = $(this).data('id');
                    navigateToForm(id);
                });

                // Delete button click handler (delegated)
                $('#scheduleHeaderTable').on('click', '.delete-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    deleteScheduleHeader(id, name);
                });

                // Optional: Row click to edit (excluding action buttons)
                $('#scheduleHeaderTable').on('click', 'tbody tr', function(e) {
                    // Only trigger if not clicking on action buttons
                    if (!$(e.target).closest('.action-buttons').length) {
                        const data = CONFIG.datatable.row(this).data();
                        if (data && data.id) {
                            navigateToForm(data.id);
                        }
                    }
                });
            }

            /**
             * Initialize the module
             */
            function init() {
                $(document).ready(function() {
                    initializeDataTable();
                    initializeEventListeners();

                    // Auto-dismiss alerts after 5 seconds
                    setTimeout(function() {
                        $('.alert').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 5000);
                });
            }

            // Start the application
            init();
        })();
    </script>
@endpush
