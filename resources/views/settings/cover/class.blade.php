@extends('layouts.app')

@push('styles')
    <style>
        .class-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .class-table tbody td {
            vertical-align: middle;
        }

        .class-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .class-table .btn:last-child {
            margin-right: 0;
        }

        .class-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .class-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .class-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .class-modal .modal-body {
            padding: 1.25rem;
        }

        .class-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .class-modal .form-control,
        .class-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .class-modal .form-control:focus,
        .class-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Cover Classes</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage class setup, grouping and combined status for cover operations.
            </p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Classes</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Classes</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-classes">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Combined (Yes)</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-combined-yes">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Combined (No)</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-combined-no">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-classes">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Class List</h5>
                        <small class="text-muted">View and manage cover classes</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#add_class">
                        <i class='bx bx-plus me-1'></i>Add Class
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover class-table" id="class-table"
                            aria-label="Class table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Class Code</th>
                                    <th>Class Name</th>
                                    <th>Combined</th>
                                    <th>Class Group Code</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade class-modal" id="add_class" tabindex="-1" aria-labelledby="addClassLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addClassLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Class
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_class" action="{{ route('class.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Class Group Code</label>
                                <select name="class_group_code" id="class_group_code" class="form-select text-capitalize">
                                    <option value="">--Select class group--</option>
                                    @foreach ($clGrps as $clgr)
                                        <option value="{{ $clgr['group_code'] }}">{{ $clgr['group_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Class Code</label>
                                <input type="text" class="form-control" placeholder="Enter class code"
                                    aria-label="class Code" id="class_code" name="class_code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Class Name</label>
                                <input type="text" class="form-control" placeholder="Enter class name"
                                    aria-label="class Name" id="class_name" name="class_name">
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_class_submit">
                            <i class="fas fa-check me-1"></i> Save Class
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade class-modal" id="edit_classModal" tabindex="-1" aria-labelledby="editClassLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editClassLabel">
                        <i class="bx bx-edit me-2"></i>Edit Class
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_class_form" action="{{ route('class.edit') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Class Group Code</label>
                                <input type="hidden" class="form-control" placeholder="class" aria-label="class"
                                    id="ed_class_code" name="ed_class_code" required>
                                <select name="ed_class_group_code" id="ed_class_group_code"
                                    class="form-select text-capitalize">
                                    <option value="">--Select class group--</option>
                                    @foreach ($clGrps as $clgr)
                                        <option value="{{ $clgr['group_code'] }}">{{ $clgr['group_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Class Name</label>
                                <input type="text" class="form-control" placeholder="Enter class name"
                                    aria-label="class Name" id="ed_class_name" name="ed_class_name" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Combined</label>
                                <select name="ed_combined" id="ed_combined" class="form-select text-capitalize" required>
                                    <option value="">--Select combined--</option>
                                    <option value="N">NO</option>
                                    <option value="Y">YES</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm" id="edit_class_submit">
                                    <i class="fas fa-check me-1"></i> Update Class
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('class.delete') }}" method="post" id="del_class_form">
        {{ csrf_field() }}
        <input type="hidden" name="del_class_code" id="del_class_code">
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#class-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('class.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'class_code',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'class_name',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'combined',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'class_group_code',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'action',
                        defaultContent: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                drawCallback: function(settings) {
                    const json = settings.json || {};
                    const rows = this.api().rows({
                        page: 'current'
                    }).data().toArray();
                    const combinedYes = rows.filter(row => (row.combined || '').toString()
                        .toUpperCase() ===
                        'Y').length;
                    const totalVisible = rows.length;

                    $('#stat-total-classes').text(json.recordsTotal || 0);
                    $('#stat-filtered-classes').text(json.recordsFiltered || 0);
                    $('#stat-combined-yes').text(combinedYes);
                    $('#stat-combined-no').text(Math.max(totalVisible - combinedYes, 0));
                }
            });

            $('#class-table').on('click', '#edit_class', function() {
                var class_code = $(this).closest('tr').find('td:eq(1)').text();
                var class_name = $(this).closest('tr').find('td:eq(2)').text();
                var class_group_code = $(this).closest('tr').find('td:eq(4)').text();
                var combined = $(this).closest('tr').find('td:eq(3)').text();
                $("#ed_class_code").val(class_code);
                $("#ed_class_name").val(class_name);
                $("#ed_class_group_code").val(class_group_code.trim());
                $("#ed_combined").val(combined.trim().toUpperCase());
                $('#edit_classModal').modal('show');
            });

            $('#class-table').on('click', '#activate_class', function() {
                var class_code = $(this).closest('tr').find('td:eq(1)').text();
                var status = $(this).val();
                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to ' + status + ' the class',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#del_class_code").val(class_code);
                        $('#del_class_form').submit();
                    }
                });
            });

            $("#store_class").validate({
                rules: {
                    class_group_code: {
                        required: true
                    },
                    class_code: {
                        required: true,
                        maxlength: 5
                    },
                    class_name: {
                        required: true,
                        maxlength: 100
                    }
                },
                messages: {
                    class_group_code: {
                        required: "class group code is required"
                    },
                    class_code: {
                        required: "class ISO is required",
                        maxlength: "class ISO must be at most 5 characters"
                    },
                    class_name: {
                        required: "class name is required",
                        maxlength: "class name must be at most 100 characters"
                    }
                },
                errorPlacement: function(error, element) {
                    error.addClass("text-danger");
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
                            title: 'Save class?',
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
        });
    </script>
@endpush
