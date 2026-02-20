@extends('layouts.app')

@push('styles')
    <style>
        .pay-method-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .pay-method-table tbody td {
            vertical-align: middle;
        }

        .pay-method-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .pay-method-table .btn:last-child {
            margin-right: 0;
        }

        .pay-method-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .pay-method-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .pay-method-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .pay-method-modal .modal-body {
            padding: 1.25rem;
        }

        .pay-method-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .pay-method-modal .form-control,
        .pay-method-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .pay-method-modal .form-control:focus,
        .pay-method-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Payment Methods</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage payment method setup for cover operations.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Payment Methods</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Methods</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-pay-methods">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-pay-methods">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Visible Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-visible-pay-methods">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Codes (Visible)</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-visible-pay-codes">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Payment Method List</h5>
                        <small class="text-muted">View and manage payment methods</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#payMethod">
                        <i class='bx bx-plus me-1'></i>Add Payment Method
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover pay-method-table" id="payMethod-table"
                            aria-label="Payment method table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pay Method Code</th>
                                    <th>Pay Method Name</th>
                                    <th>Short Description</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade pay-method-modal" id="payMethod" tabindex="-1" aria-labelledby="payMethodLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="payMethodLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Payment Method
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_payMethod" action="{{ route('payMethod.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Pay Method Code</label>
                                <input type="text" class="form-control" placeholder="Enter pay method code"
                                    aria-label="Pay Method Code" id="pay_method_code" name="pay_method_code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Pay Method Name</label>
                                <input type="text" class="form-control" placeholder="Enter pay method name"
                                    aria-label="Pay Method Name" id="pay_method_name" name="pay_method_name">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Short Description</label>
                                <input type="text" class="form-control" placeholder="Enter short description"
                                    aria-label="Short Description" id="short_description" name="short_description">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_payMethod">
                            <i class="fas fa-check me-1"></i> Save Payment Method
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade pay-method-modal" id="edit_payMethodModal" tabindex="-1"
        aria-labelledby="editPayMethodLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editPayMethodLabel">
                        <i class="bx bx-edit me-2"></i>Edit Payment Method
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit_payMethod" action="{{ route('payMethod.edit') }}" method="post">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <input type="hidden" id="ed_pay_method_code" name="ed_pay_method_code">
                            <div class="col-md-12">
                                <label class="form-label">Pay Method Name</label>
                                <input type="text" class="form-control" placeholder="Enter pay method name"
                                    aria-label="Pay Method Name" id="ed_pay_method_name" name="ed_pay_method_name">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Short Description</label>
                                <input type="text" class="form-control" placeholder="Enter short description"
                                    aria-label="Short Description" id="ed_short_description" name="ed_short_description">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="edit_payMethod_submit">
                            <i class="fas fa-check me-1"></i> Update Payment Method
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#payMethod-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('payMethod.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'pay_method_code'
                    },
                    {
                        data: 'pay_method_name'
                    },
                    {
                        data: 'short_description'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                drawCallback: function(settings) {
                    const json = settings.json || {};
                    const rows = this.api().rows({
                        page: 'current'
                    }).data().toArray();
                    const codeCount = rows
                        .map(row => (row.pay_method_code || '').toString().trim())
                        .filter(code => code.length > 0)
                        .length;

                    $('#stat-total-pay-methods').text(json.recordsTotal || 0);
                    $('#stat-filtered-pay-methods').text(json.recordsFiltered || 0);
                    $('#stat-visible-pay-methods').text(rows.length || 0);
                    $('#stat-visible-pay-codes').text(codeCount || 0);
                }
            });

            $('#payMethod-table').on('click', '#edit_payMethod', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const code = (rowData.pay_method_code || '').toString().trim();
                const name = (rowData.pay_method_name || '').toString().trim();
                const desc = (rowData.short_description || '').toString().trim();
                $('#ed_pay_method_code').val(code);
                $('#ed_pay_method_name').val(name);
                $('#ed_short_description').val(desc);
                $('#edit_payMethodModal').modal('show');
            });

            $('#store_payMethod').validate({
                rules: {
                    pay_method_code: {
                        required: true,
                        maxlength: 20
                    },
                    pay_method_name: {
                        required: true,
                        maxlength: 80
                    },
                    short_description: {
                        required: true,
                        maxlength: 5
                    }
                },
                messages: {
                    pay_method_code: {
                        required: 'Pay method code is required',
                        maxlength: 'Pay method code must be at most 20 characters'
                    },
                    pay_method_name: {
                        required: 'Pay method name is required',
                        maxlength: 'Pay method name must be at most 80 characters'
                    },
                    short_description: {
                        required: 'Short description is required',
                        maxlength: 'Short description must be at most 5 characters'
                    }
                },
                errorPlacement: function(error, element) {
                    error.addClass('text-danger');
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('error').removeClass('valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error').addClass('valid');
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Save payment method?',
                            text: 'Do you want to submit these changes?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Save',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                        return false;
                    }
                    form.submit();
                }
            });

            $('#edit_payMethod').validate({
                rules: {
                    ed_pay_method_name: {
                        required: true,
                        maxlength: 80
                    },
                    ed_short_description: {
                        required: true,
                        maxlength: 5
                    }
                },
                messages: {
                    ed_pay_method_name: {
                        required: 'Pay method name is required',
                        maxlength: 'Pay method name must be at most 80 characters'
                    },
                    ed_short_description: {
                        required: 'Short description is required',
                        maxlength: 'Short description must be at most 5 characters'
                    }
                },
                errorPlacement: function(error, element) {
                    error.addClass('text-danger');
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('error').removeClass('valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error').addClass('valid');
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Update payment method?',
                            text: 'Do you want to save these changes?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Update',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                        return false;
                    }
                    form.submit();
                }
            });
        });
    </script>
@endpush
