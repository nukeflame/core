@extends('layouts.app', [
    'pageTitle' => 'System Process Actions - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">System Process Actions</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">System Processes Actions</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="col-xl-12">
        <div class="card custom-card mt-4">
            <div class="card-body">
                <div class="contact-header">
                    <div class="d-flex d-block align-items-center justify-content-between">
                        <div class="h6 fw-semibold mb-0">Manage system process actions</div>
                        <div class="d-flex mt-sm-0 mt-0 align-items-center">
                            <button class="btn btn-sm btn-dark btn-fs-13" id="action_modal" data-bs-toggle="modal"
                                data-bs-target="#systemActionModal"><i class="ri-add-line"
                                    style="vertical-align: -2px;"></i>
                                <span>New Process Action</span></button>
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
                    <div class="card-title">Process Action list</div>
                </div>
                <div class="card-body">
                    <table id="action-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Process</th>
                                <th>Status</th>
                                <th>Action Type</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-super-scaled md-wrapper" id="systemActionModal" tabindex="-1"
        aria-labelledby="systemActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="systemActionForm" action="{{ route('admin.system_actions.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title dc-modal-title" id="systemActionModalLabel">
                            <i class="bi bi-lightning"></i> Create System Action
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="actionName" class="form-label">Action Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-inputs" id="actionName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="associatedModule" class="form-label">Associated Module <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="associatedModule" name="module">
                                        <option value="">Select Module</option>
                                        <option value="dashboard">Dashboard Module</option>
                                        <option value="approvals">Approvals Module</option>
                                        <option value="business_development">Business Development Module</option>
                                        <option value="cover_administration">Cover Administration Module</option>
                                        <option value="claims_administration">Claims Administration Module</option>
                                        <option value="reports">Reports Module</option>
                                        <option value="settings">Settings Module</option>
                                        <option value="reinsurance">Reinsurance Settings Module</option>
                                        <option value="user_management">User Management Module</option>
                                        <option value="integration_apis">Integration & APIs Module</option>
                                        <option value="all_modules">All Modules</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="actionType" class="form-label">Action Type <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="actionType" name="action_type" required>
                                        <option value="">Select Action Type</option>
                                        <option value="create">Create</option>
                                        <option value="update">Update</option>
                                        <option value="delete">Delete</option>
                                        <option value="read">Read</option>
                                        <option value="export">Export</option>
                                        <option value="import">Import</option>
                                        <option value="verify">Verify</option>
                                        <option value="approve">Approve</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="actionStatus" class="form-label">Initial Status <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="actionStatus" name="status" required>
                                        <option value="pending">Pending</option>
                                        <option value="running">Running</option>
                                        <option value="completed">Completed</option>
                                        <option value="failed">Failed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="actionPriority" class="form-label">Priority</label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="actionPriority" name="priority">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="associatedProcess" class="form-label">Associated Process <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="associatedProcess" name="system_process_id">
                                        <option value="">Select Process</option>
                                        @if ($processes)
                                            @foreach ($processes as $proc)
                                                <option value="{{ $proc->id }}">{{ $proc->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="scheduleDateTime" class="form-label">Schedule Date and Time</label>
                                <input type="datetime-local" class="form-control color-blk" id="scheduleDateTime"
                                    name="scheduled_at">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="actionDescription" class="form-label">Description</label>
                            <textarea class="form-inputs" id="actionDescription" name="description" rows="3"
                                placeholder="Provide a detailed description of the system action"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light border-0 btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark btn-sm" id="saveSystemAction">
                            <i class="bi bi-save"></i> Save Action
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
            const actionTable = $('#action-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                actioning: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('settings.system_process_action_datatable') !!}",
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
                        data: 'process.name',
                        searchable: true
                    },
                    {
                        data: 'action_type',
                        searchable: true
                    },
                    {
                        data: 'created_by',
                        searchable: true
                    },
                    {
                        data: 'updated_by',
                        searchable: true
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false,
                        orderable: false
                    },
                ]
            });

            $('#systemActionModal').on('shown.bs.modal', function() {
                $('.form-inputs.select2').select2({
                    dropdownParent: $('#systemActionModal')
                });
            })

            $("#systemActionForm").validate({
                errorClass: "errorClass",
                highlight: function(element, errorClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass) {
                    $(element).removeClass('is-invalid');
                },
                rules: {
                    name: {
                        required: true
                    },
                    module: {
                        required: true
                    },
                    action_type: {
                        required: true
                    },
                    status: {
                        required: true
                    },
                    system_process_id: {
                        required: true
                    }
                },
                submitHandler: function(form) {
                    $('#saveSystemAction').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                    );
                    let url = $(form).attr('action');
                    let method = $(form).attr('method');
                    let formData = new FormData(form);
                    $.ajax({
                        url: url,
                        method: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status == 201) {
                                toastr.success(response.message);
                                $('#systemActionModal').modal('hide');
                                actionTable.ajax.reload();
                            } else {
                                toastr.error("Failed to save Action");
                            }
                            $('#saveSystemAction').prop('disabled', false).text(
                                'Save Action');
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(field => {
                                    const input = $(`[name="${field}"]`);
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').remove();
                                    input.after(
                                        `<div class="invalid-feedback fs-13">${errors[field][0]}</div>`
                                    );
                                });
                            } else {
                                toastr.error('An error occurred while saving the action');
                            }
                            $('#saveSystemAction').prop('disabled', false).html(
                                '<i class="bi bi-save"></i> Save Action');
                        }
                    });
                }
            })
        });
    </script>
@endpush
