@extends('layouts.app', [
    'pageTitle' => 'Permissions - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Permissions</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Permissions</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="col-xl-12">
        <div class="card custom-card mt-4">
            <div class="card-body">
                <div class="contact-header">
                    <div class="d-flex d-block align-items-center justify-content-between">
                        <div class="h6 fw-semibold mb-0">Manage permissions</div>
                        <div class="d-flex mt-sm-0 mt-0 align-items-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Permission list</div>
                </div>
                <div class="card-body">
                    <table id="permission-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Names</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Roles</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!--Assign Role Modal -->
    <div class="modal effect-super-scaled md-wrapper" id="roleModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="staticBackdropLabel"><i class="bx bx-shield"></i> Assign Role
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="roleForm" method="POST" action="{{ route('admin.roles.assign') }}">
                        @csrf
                        <input type="hidden" id="permission_ids" name="permission_ids[]">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <p id="totalPermissionSelected"></p>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="username" class="form-label fs-14">Assign to Roles:</label>
                                <select name="role_ids[]" id="role_ids" multiple class="form-control form-inputs">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>

                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer mt-4 mb-0 pb-0">
                    <button type="button" class="btn btn-light" style="font-size: 13px; padding: 4px 24px;"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveRoleBtn" style="font-size: 13px; padding: 4px 24px;"
                        class="btn btn-dark btn-wave waves-effect waves-light">Add</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let selectedRows = [];

            const $table = $('#permission-table').DataTable({
                order: [
                    [1, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                pageLength: 17,
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                ajax: {
                    url: "{!! route('settings.permissions_datatable') !!}",
                },
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    className: 'select-checkbox',
                    render: function(data, type, row, meta) {
                        return '<div class="form-check form-check-md d-flex align-items-center">' +
                            '<input class="form-check-input form-checked-dark checkbox-md" type="checkbox" value="' +
                            row.id + '" id="checkbox-md-' + row.id + '">' +
                            '<label class="form-check-label" for="checkbox-md-' + row.id +
                            '"></label>' +
                            '</div>';
                    }
                }],
                columns: [{
                        data: null,
                        defaultContent: '',
                        searchable: false,
                        orderable: false,
                        class: 'highlight-zero'
                    },
                    {
                        data: 'name',
                        searchable: true,
                        class: 'highlight-2view-point'
                    },
                    {
                        data: 'description',
                        searchable: true,
                        class: 'highlight-description',
                        render: function(data) {
                            return data || '--';
                        }
                    },
                    {
                        data: 'status',
                        searchable: false,
                    },
                    {
                        data: 'roles_count',
                        searchable: false,
                        class: 'highlight-index',
                    },
                    {
                        data: 'created_at',
                        searchable: false,
                        sortable: false
                    },
                ],
                dom: 'Bfrtip',
                buttons: [{
                    text: '<i class="bx bx-shield-plus me-1" style="vertical-align: -3px; font-size:18px;"></i>Assign Role',
                    className: 'btn btn-ouline-dark shadow-primary btn-wave waves-effect waves-light add-role-btn hidden',
                    action: function(e, dt, node, config) {
                        $('#roleModal').modal('show');
                    }
                }]
            });

            $('#roleModal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#roleModal')
                });
            });

            $('#permission-table').on('change', 'input[type="checkbox"]', function() {
                const $row = $(this).closest('tr');
                const rowData = $table.row($row).data();
                if (this.checked) {
                    selectedRows.push(rowData);
                } else {
                    selectedRows = selectedRows.filter(row => row.id !== rowData.id);
                }

                if (selectedRows.length > 0) {
                    const selectedRoles = typeof selectedRows[0]?.roles !== "undefined" ? JSON
                        .parse(selectedRows[0]?.roles) : []

                    const v = selectedRoles?.map((x) => x.id);

                    $('#roleModal #role_ids').val(v);
                    $('#roleModal #role_ids').trigger('change');
                }

                $('.add-role-btn').toggleClass('hidden', selectedRows.length === 0);
            });

            $('.add-role-btn').on('click', function() {
                let selectedRoleIds = selectedRows.map(function(row) {
                    return row.id;
                });
                $('#saveRoleBtn').prop('disabled', selectedRows.length === 0);
                $('#roleModal').find('input[name="selected_role_ids"]').val(JSON.stringify(
                    selectedRoleIds));
                $('#totalPermissionSelected').text('Selected Permissions: ' + selectedRows.length);

                const selectedPermissions = selectedRows?.map((x) => x.name);
                $('#permission_ids').val(selectedPermissions);

                $('#roleModal').modal('show');
            });

            $('#roleModal').on('hidden.bs.modal', function() {
                $('#roleForm')[0].reset();
                selectedRows = [];
                $table.ajax.reload();

                $('.add-role-btn').addClass('hidden');
                $('#roleModal #role_id').val([]);
                $('#roleModal #role_id').trigger('change');
                $('#permission-table input[type="checkbox"]').prop('checked', false);
                $('#totalPermissionSelected').text('');
                $('#roleModal #permission_ids').val([]);
                $('#roleModal #permission_ids').trigger('change');
                $('#roleModal #role_ids').val([]);
                $('#roleModal #role_ids').trigger('change');
            });

            $('#roleForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        $('#roleModal').modal('hide');
                        toastr.success(response.message);
                        $('#roleForm')[0].reset();
                        $('#roleModal #role_id').val([]);
                        $('#roleModal #role_id').trigger('change');
                        $('#totalPermissionSelected').text('');
                        $('#permission-table input[type="checkbox"]').prop('checked', false);
                        $('#roleModal #permission_ids').val([]);
                        $('#roleModal #permission_ids').trigger('change');
                        $('#roleModal #role_ids').val([]);
                        $('#roleModal #role_ids').trigger('change');
                        $table.ajax.reload();
                        selectedRows = [];
                        $('.add-role-btn').addClass('hidden');
                    },
                    error: function(xhr) {
                        toastr.error('Error assigning permissions. Please try again.');
                    }
                });
            });

            $('#roleModal #saveRoleBtn').on('click', function() {
                $('#roleForm').submit();
            });
        });
    </script>
@endpush
