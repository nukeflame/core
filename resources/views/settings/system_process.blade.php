@extends('layouts.app', [
    'pageTitle' => 'System Process - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">System Processes</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">System Processes</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card mt-4">
                <div class="card-body">
                    <div class="contact-header">
                        <div class="d-flex d-block align-items-center justify-content-between">
                            <div class="h6 fw-semibold mb-0">Manage system processes</div>
                            <div class="d-flex mt-sm-0 mt-0 align-items-center">
                                <button class="btn btn-sm btn-dark btn-fs-13" data-bs-toggle="modal"
                                    id="systemProcessModalBtn" data-bs-target="#systemProcessModal"><i class="ri-add-line"
                                        style="vertical-align: -2px;"></i>
                                    <span>New Process</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Process list</div>
                </div>
                <div class="card-body">
                    <table id="process-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Initiated By</th>
                                <th>Started At</th>
                                <th>Completed At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-super-scaled md-wrapper" id="systemProcessModal" tabindex="-1"
        aria-labelledby="systemProcessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="systemProcessForm" action="{{ route('admin.system_processes.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title dc-modal-title" id="staticBackdropLabel"><i class="bx bx-chip"></i> Create
                            System Process
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="processName" class="form-label">Process Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-inputs" id="processName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="processCategory" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="processCategory" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="system_maintenance">System Maintenance</option>
                                        <option value="user_backup">Sytem Backup</option>
                                        <option value="cover_registration">Cover Registration</option>
                                        <option value="gl_batch_process">GL Batch Process</option>
                                        <option value="claim_registration">Claim Registration</option>
                                        <option value="claim_intimation_process">Claim Intimation Process</option>
                                        <option value="user_management">User Management</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="executionType" class="form-label">Execution Type <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="executionType" name="execution_type" required>
                                        <option value="">Select Execution Type</option>
                                        <option value="scheduled">Scheduled</option>
                                        <option value="manual">Manual</option>
                                        <option value="triggered">Triggered</option>
                                        <option value="continuous">Continuous</option>
                                        <option value="on_demand">On Demand</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="processStatus" class="form-label">Initial Status <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="processStatus" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="draft">Draft</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="permissionSelect" class="form-label">Associated Permissions <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2 select2-multiple" id="permissionSelect"
                                        name="permissions[]" multiple="multiple" required>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="processPriority" class="form-label">Priority</label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="processPriority" name="priority">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="processDescription" class="form-label">Description</label>
                            <textarea class="form-inputs" id="processDescription" name="description" rows="3"
                                placeholder="Provide a detailed description of the system process"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light border-0 btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark btn-sm" id="saveSystemProcess">Save Process</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const processTable = $('#process-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('settings.system_processes_datatable') !!}",
                },
                columns: [{
                        data: 'id',
                        searchable: false,
                        class: 'highlight-idx'
                    },
                    {
                        data: 'name',
                        searchable: true,
                        class: 'highlight-view2-more'

                    },
                    {
                        data: 'description',
                        searchable: true,
                        class: 'highlight-view-more'
                    },
                    {
                        data: 'priority',
                        searchable: true
                    },
                    {
                        data: 'status',
                        searchable: true
                    },
                    {
                        data: 'initiated_by',
                        searchable: true
                    },
                    {
                        data: 'started_at',
                        searchable: true
                    },
                    {
                        data: 'completed_at',
                        searchable: true
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false
                    },
                ]
            });

            $('#systemProcessModal').on('shown.bs.modal', function() {
                $('.form-inputs.select2').select2({
                    dropdownParent: $('#systemProcessModal'),
                    width: '100%'
                });

                $('#permissionSelect').select2({
                    dropdownParent: $('#systemProcessModal'),
                    placeholder: "Select permissions",
                    allowClear: true,
                    width: '100%'
                });
            });


            $('#systemProcessModalBtn').on('click', function() {
                $("#systemProcessForm")[0].reset()
            });

            $('#addParameterBtn').on('click', function() {
                const currentParameterCount = $('#parametersContainer .input-group').length;
                if (currentParameterCount < 5) {
                    const newParameterGroup = `
                        <div class="input-group mb-2">
                            <input type="text" class="form-control color-blk" name="parameters[keys][]" placeholder="Parameter Key">
                            <input type="text" class="form-control color-blk" name="parameters[values][]" placeholder="Parameter Value">
                            <button type="button" class="btn btn-danger remove-parameter">-</button>
                        </div>
                    `;
                    $('#parametersContainer').append(newParameterGroup);
                    if (currentParameterCount + 1 === 5) {
                        $('#addParameterBtn').prop('disabled', true);
                    }
                }
            });

            $('#processCategory').on('change', function() {
                const selectedCategory = $(this).val();
                const $permissionSelect = $('#permissionSelect');
                $permissionSelect.prop('disabled', true);
                $permissionSelect.empty();
                if (selectedCategory) {
                    $.ajax({
                        url: `/admin/get-permissions/${selectedCategory}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                response.permissions.forEach(function(permission) {
                                    const newOption = new Option(
                                        permission.name,
                                        permission.id,
                                        false,
                                        false
                                    );
                                    $permissionSelect.append(newOption);
                                });
                                $permissionSelect.trigger('change');
                            } else {
                                toastr.error('Failed to fetch permissions');
                            }
                        },
                        error: function(xhr) {
                            toastr.error('An error occurred while fetching permissions');
                        },
                        complete: function() {
                            $permissionSelect.prop('disabled', false);
                        }
                    });

                }
            });

            $('#parametersContainer').on('click', '.remove-parameter', function() {
                $(this).closest('.input-group').remove();
                $('#addParameterBtn').prop('disabled', false);
            });

            $("#systemProcessForm").validate({
                errorClass: "errorClass",
                rules: {
                    name: {
                        required: true
                    },
                    category: {
                        required: true
                    },
                    execution_type: {
                        required: true
                    },
                    status: {
                        required: true
                    },
                    'permissions[]': {
                        required: true
                    }
                },
                submitHandler: function(form) {
                    $("#saveSystemProcess").html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                    );
                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            $('#systemProcessModal').modal('hide');
                            toastr.success('Process Created Successfully');
                            processTable.ajax.reload();
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON?.message ||
                                'Error creating system process';
                            toastr.error(errorMessage, '', {
                                messageClass: 'text-center'
                            });
                        },
                        complete: function() {
                            $("#saveSystemProcess").prop('disabled', false);
                            $("#saveSystemProcess").html('Save Process');
                        }
                    });
                }
            });

            $('#systemProcessForm input, #systemProcessForm select, #systemProcessForm textarea').on('change',
                function() {
                    if ($(this).val()) {
                        $(this).removeClass('is-invalid');
                    } else if ($(this).prop('required')) {
                        $(this).addClass('is-invalid');
                    }
                });
        });
    </script>
@endpush
