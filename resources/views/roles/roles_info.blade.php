@extends('layouts.app', [
    'pageTitle' => 'Roles - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Roles</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Roles</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="col-xl-12">
        <div class="card custom-card mt-4">
            <div class="card-body">
                <div class="contact-header">
                    <div class="d-flex d-block align-items-center justify-content-between">
                        <div class="h6 fw-semibold mb-0">Manage roles</div>
                        <div class="d-flex mt-sm-0 mt-0 align-items-center">
                            <button class="btn btn-sm btn-dark btn-fs-13" id="add_user" data-bs-toggle="modal"
                                data-bs-target="#newRoleModal"><i class="ri-add-line" style="vertical-align: -2px;"></i>
                                <span>Create
                                    Role
                                </span></button>
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
                    <div class="card-title">Role list</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="roles-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Role name</th>
                                    <th>Description</th>
                                    <th>Department(s)</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Last Modified</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--New Role Modal -->
    <div class="modal effect-super-scaled md-wrapper" id="newRoleModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="staticBackdropLabel"><i class="bx bx-lock-alt"></i> Create
                        Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="roleForm" method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <p class="text-dark py-2 text-italic mb-0">Roles let you group permissions and
                                    assign them to principals in your organization. You can manually select
                                    permissions or import permissions from another role</p>
                                <hr class="mb-2 p-0 mt-2" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label fs-14">Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control color-blk @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label fs-14">Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control color-blk resize-none @error('description') is-invalid @enderror" rows="3"
                                    id="description" name="description" value="{{ old('description') }}" required></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="permission_level" class="form-label fs-14">Permission Level <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="permission_level" name="permission_level">
                                        @foreach ($permissionLevels as $name => $level)
                                            <option value="{{ $level }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback" id="permission_level-error"></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="role_launch_stage" class="form-label fs-14">Role visibility<span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2 @error('role_launch_stage') is-invalid @enderror"
                                        id="role_launch_stage" name="role_launch_stage">
                                        <option value="general_availability">General Availabilty</option>
                                        <option value="disabled">Disabled</option>
                                    </select>
                                </div>
                                @error('role_launch_stage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <label for="department_id" class="form-label fs-14">Departments </label>
                                <div class="card-md">
                                    <div class="card-md">
                                        <select class="form-inputs select2" name="department_ids[]" id="department_id"
                                            multiple>
                                            <option value="" disabled>Select Department</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveRoleBtn" class="btn btn-dark">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Assign Permission Modal -->
    <div class="modal effect-super-scaled md-wrapper" id="addPermissionModal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="assignPermissionForm" method="POST" action="{{ route('admin.permissions.assign') }}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title dc-modal-title" id="staticBackdropLabel"><i class="bx bx-shield"></i> Add
                            Permission
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="p-3 pb-0">
                        @csrf
                        <input type="hidden" name="selected_role_ids" id="selected_role_ids">
                        <input type="hidden" name="selected_permission_ids" id="selected_permission_ids">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="filter_role" class="form-label font-weight-bold fs-14">Filter permissions by
                                    role</label>
                                <div class="card-md">
                                    <select class="select2 form-inputs" id="filter_role">
                                        <option selected>-- Select --</option>
                                        @if ($roles && count($roles) > 0)
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach`
                                        @else
                                            <option value="">No roles available</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-dm-12">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="filter-role-btn"><i
                                            class="bx bx-filter"></i></span>
                                    <input type="text" class="form-control color-blk" aria-label="filterRole"
                                        aria-describedby="filter-role-btn" placeholder="Filter" id="filterRole" />
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-nowrap" id="permissionsTable">
                                <thead class="table-secondary">
                                    <tr>
                                        <th scope="col">
                                            <div class="form-check form-check-md d-flex align-items-center">
                                                <input class="form-check-input form-checked-dark" type="checkbox"
                                                    value="" id="selectAllPermissions">
                                                <label class="form-check-label" for="selectAllPermissions">
                                                </label>
                                            </div>
                                        </th>
                                        <th scope="col" class="fs-13">Permission <span><i
                                                    class="bi bi-arrow-up"></i></span></th>
                                        <th scope="col" class="fs-13">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="permissionsTableBody">
                                    @if ($permissions && count($permissions) > 0)
                                        @foreach ($permissions as $permission)
                                            <tr data-role="{{ $permission->id }}">
                                                <td class="fs-12" style="padding: 4px 9px">
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <input class="form-check-input form-checked-dark" type="checkbox"
                                                            value="" id="checkbox-{{ $permission->id }}"
                                                            data-permission="{{ $permission->name }}" />
                                                        <label class="form-check-label"
                                                            for="checkbox-{{ $permission->id }}"></label>
                                                    </div>
                                                </td>
                                                <td class="fs-12" style="padding: 4px 9px">{{ $permission->name }}</td>
                                                <td class="fs-12" style="padding: 4px 9px">
                                                    <span class="text-dark">
                                                        {{ $permission->status ? 'Supported' : 'Non-applicable' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">No permissions available</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div></div>
                            <div width="100%" class="d-flex justify-content-between align-items-center">
                                <div class="text-muted" id="paginationInfo">1 - 10 of 10</div>
                                <nav>
                                    <ul class="pagination mb-0">
                                        <li class="page-item">
                                            <button type="button" class="page-link p-0 bg-white border-0 mr-3"
                                                style="font-size: 30px; margin: 0px; margin-right: 7px;" id="prevPage"
                                                aria-label="Previous">
                                                <span class="bx bx-chevron-left"></span>
                                            </button>
                                        </li>
                                        <li class="page-item">
                                            <button type="button" class="page-link p-0 bg-white border-0 mr-3"
                                                style="font-size: 30px; margin: 0px;" id="nextPage" aria-label="Next">
                                                <span class="bx bx-chevron-right"></span>
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 mb-0 pb-0">
                        <button type="button" class="btn btn-light" style="font-size: 13px; padding: 4px 24px;"
                            id="cancelPermissionBtn" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="savePermissionBtn" style="font-size: 13px; padding: 4px 24px;"
                            disabled class="btn btn-dark btn-wave waves-effect waves-light">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Department Assignment Modal -->
    <div class="modal effect-super-scaled md-wrapper" id="departmentModal" tabindex="-1"
        aria-labelledby="departmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="departmentModalLabel"><i class="bx bx-buildings me-2"></i>
                        Assign Departments</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="departmentAssignForm" action="{{ route('admin.departments.assign') }}" method="POST">
                        @csrf
                        <input type="hidden" id="userId" name="userId">
                        <input type="hidden" id="roleId" name="roleId">
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Select Department</label>
                            <div class="card-md">
                                <select class="form-inputs select2" id="department_id" name="department_ids[]" required
                                    multiple>
                                    <option value="" disabled>Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->department_code }}">
                                            {{ $department->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-inputs form-control-disabled bg-light" id="roleName"
                                name="roleName" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer mt-4 mb-0 pb-0">
                    <button type="button" class="btn btn-light" style="font-size: 13px; padding: 4px 24px;"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveDepartment" style="font-size: 13px; padding: 4px 24px;"
                        class="btn btn-dark btn-wave waves-effect waves-light">Add</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let permissions = @json($permissions);
            let selectedRows = [];
            const itemsPerPage = 10;
            let currentPage = 1;
            const totalPermissions = permissions.length;
            const totalPages = Math.ceil(totalPermissions / itemsPerPage);
            let rolePermissions = []

            const $roleTable = $('#roles-table').DataTable({
                order: [
                    [1, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: true,
                lengthChange: false,
                pageLength: 17,
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                ajax: {
                    url: "{!! route('settings.roles_datatable') !!}",
                },
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    className: 'select-checkbox',
                    render: function(data, type, row, meta) {
                        let isDisabled = row.slug == 'super_admin' ? 'disabled' : '';
                        return '<div class="form-check form-check-md d-flex align-items-center">' +
                            '<input class="form-check-input form-checked-dark checkbox-md" type="checkbox" value="' +
                            row.id + '" id="checkbox-md-' + row.id + '" ' + isDisabled + '>' +
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
                        data: 'departments',
                        searchable: false,
                    },
                    {
                        data: 'status',
                        searchable: false,
                        // class: 'highlight-view-point',
                    },
                    {
                        data: 'created_at',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'updated_at',
                        searchable: true
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false,
                        class: 'highlight-view-point',
                    },
                ],
                dom: 'Bfrtip',
                buttons: [
                    // {
                    //     text: '<i class="bx bx-user-plus me-1" style="vertical-align: -2px; font-size:18px;"></i>Assign Permission',
                    //     className: 'btn btn-ouline-dark shadow-primary btn-wave waves-effect waves-light add-permission-btn hidden',
                    //     action: function(e, dt, node, config) {
                    //         $('#addPermissionModal').modal('show');
                    //     }
                    // },
                    {
                        text: '<i class="bx bx-buildings me-1" style="vertical-align: -2px; font-size:18px;"></i>Assign Department',
                        className: 'btn btn-ouline-dark shadow-primary btn-wave waves-effect waves-light add-department-btn hidden',
                        action: function(e, dt, node, config) {
                            $('#departmentModal').modal('show');
                        }
                    }
                ]
            });

            $('#addPermissionModal').on('shown.bs.modal', function() {
                $('.select2').select2({
                    dropdownParent: $('#addPermissionModal')
                });
                $('#filter_role').val('-- Select --').trigger('change');
                $('#filterRole').val('');
                updateSaveButtonState();
                renderPage();
            });


            $('#addPermissionModal').on('hidden.bs.modal', function() {
                //
            });

            $('#departmentModal').on('hidden.bs.modal', function() {
                $('#departmentAssignForm')[0].reset();
                $('#department_id').val(null).trigger('change');
                $('#roleName').val('');
                $('#roleId').val('');
                $('#userId').val('');
                $('#saveDepartment').prop('disabled', false).html('Add');
            });

            $("#addPermissionModal #first_name, #last_name").on('blur', function() {
                const firstName = $("#first_name").val().trim();
                const lastName = $("#last_name").val().trim();

                if (firstName && lastName && !$("#username").val()) {
                    const suggestedRolename = (firstName.charAt(0) + lastName).toLowerCase()
                        .replace(/[^a-z0-9]/g, '');
                    $.ajax({
                        url: "{{ route('admin.username.check') }}",
                        method: "POST",
                        data: {
                            username: suggestedRolename,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $("#username").removeClass('is-invalid').addClass('is-valid');
                            if (!response.exists) {
                                $("#username").val(suggestedRolename);
                            } else {
                                $("#username").val(suggestedRolename + Math.floor(Math
                                    .random() * 100));
                            }
                        }
                    });
                }
            });

            $("#addPermissionModal #department_id").change(function() {
                const departmentId = $(this).val();
                if (departmentId) {
                    $.ajax({
                        url: "{{ route('admin.roles.by.department') }}",
                        method: "GET",
                        data: {
                            department_id: departmentId
                        },
                        success: function(data) {
                            let options = '<option value="">Select Role</option>';

                            $.each(data, function(key, role) {
                                options +=
                                    `<option value="${role.id}">${role.name}</option>`;
                            });

                            $("#role_id").html(options);
                        }
                    });
                } else {
                    $("#role_id").html('<option value="">Select Role</option>');
                }
            });

            $("#addPermissionModal #username").on('blur', function() {
                const username = $(this).val().trim();
                if (username) {
                    // Check username format
                    $(".invalid-feedback").empty();
                    if (!/^[a-z0-9]{3,20}$/.test(username)) {
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        $(this).after(
                            '<div class="invalid-feedback">Rolename must be 3-20 alphanumeric characters</div>'
                        );
                        return;
                    }

                    // Check if username exists
                    $("#username").removeClass('is-valid');
                    $.ajax({
                        url: "{{ route('admin.username.check') }}",
                        method: "POST",
                        data: {
                            username: username,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.exists) {
                                $("#username").removeClass('is-valid').addClass('is-invalid');
                                $("#usernameHelp").html(
                                    '<span class="text-danger">This username is already taken</span>'
                                );
                            } else {
                                $("#username").removeClass('is-invalid').addClass('is-valid');
                                $("#usernameHelp").html('Rolename is available');
                            }
                        }
                    });
                }
            });

            $("#addPermissionModal #email").on('blur', function() {
                const email = $(this).val().trim();
                if (email) {
                    // Check email format
                    $(".invalid-feedback").empty();
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        $(this).after(
                            '<div class="invalid-feedback">Please enter a valid email address</div>');
                        return;
                    }

                    const domain = email.split('@')[1];
                    $.ajax({
                        url: "{{ route('admin.email.domain.check') }}",
                        method: "POST",
                        data: {
                            domain: domain,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (!response.allowed) {
                                $("#email").removeClass('is-valid').addClass('is-invalid');
                                $("#email").after(
                                    '<div class="invalid-feedback">This email domain is not allowed</div>'
                                );
                            } else {
                                $("#email").removeClass('is-invalid').addClass('is-valid');
                            }
                        }
                    });
                }
            });

            function renderPage() {
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const pagePermissions = permissions.slice(startIndex, endIndex);

                $('#permissionsTableBody').empty();

                pagePermissions.forEach(permission => {
                    const isChecked = selectedRows.length > 0 && selectedRows.some(row => {
                        const perms = JSON.parse(row.permissions) ?? [];
                        return perms.some(perm => perm.name === permission.name);
                    })

                    $('#permissionsTableBody').append(`
                        <tr data-role="${permission.id}">
                            <td class="fs-12" style="padding: 4px 9px">
                                <div class="form-check form-check-md d-flex align-items-center">
                                    <input class="form-check-input form-checked-dark" type="checkbox"
                                        value="" id="checkbox-${permission.id}"
                                        data-permission="${permission.name}"
                                        ${permission.status !== 'A' ? 'disabled' : ''}
                                        ${isChecked ? 'checked' : ''} />
                                    <label class="form-check-label" for="checkbox-${permission.id}"></label>
                                </div>
                            </td>
                            <td class="fs-12" style="padding: 4px 9px">${permission.name}</td>
                            <td class="fs-12" style="padding: 4px 9px">
                                <span class="text-dark">
                                    ${permission.status === 'A' ? 'Supported' : 'Non-applicable'}
                                </span>
                            </td>
                        </tr>
                    `);
                });

                const startItem = startIndex + 1;
                const endItem = Math.min(endIndex, totalPermissions);
                $('#paginationInfo').text(`${startItem} - ${endItem} of ${totalPermissions}`);

                if (currentPage === 1) {
                    $('#prevPage').prop('disabled', true);
                    $('#prevPage').addClass('text-muted');
                } else {
                    $('#prevPage').prop('disabled', false);
                    $('#prevPage').removeClass('text-muted');
                }

                if (currentPage === totalPages) {
                    $('#nextPage').prop('disabled', true);
                    $('#nextPage').addClass('text-muted');
                } else {
                    $('#nextPage').prop('disabled', false);
                    $('#nextPage').removeClass('text-muted');
                }
            }

            $('#prevPage').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderPage();
                }
            });

            $('#nextPage').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderPage();
                }
            });

            $('#roleForm').validate({
                rules: {
                    errorClass: "errorClass",
                    title: {
                        required: true,
                        minlength: 3
                    },
                    description: {
                        required: true,
                        minlength: 5
                    },
                    role_launch_stage: {
                        required: true
                    }
                },
                messages: {
                    title: {
                        required: "Please enter a title",
                        minlength: "Title must be at least 3 characters long"
                    },
                    description: {
                        required: "Please enter a description",
                        minlength: "Description must be at least 5 characters long"
                    },
                    role_launch_stage: {
                        required: "Please select role visibility"
                    }
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $("#saveRoleBtn").prop('disabled', true);
                    $("#saveRoleBtn").html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                    );
                    $.ajax({
                        url: $(form).attr('action'),
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $('#newRoleModal').modal('hide');
                            $('#roles-table').DataTable().ajax.reload();
                            Swal.fire({
                                title: 'Success!',
                                text: 'Role created successfully',
                                icon: 'success'
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $('#' + key).addClass('is-invalid');
                                    $('#' + key + '-error').text(value[0]);
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: xhr.responseJSON.message ||
                                        'Something went wrong. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        complete: function() {
                            $("#roleForm")[0].reset();
                            $("#saveRoleBtn").prop('disabled', false);
                            $("#saveRoleBtn").html('Submit');
                        }
                    });
                    return false;
                }
            });

            $('#roles-table').on('change', 'input[type="checkbox"]', function() {
                const $row = $(this).closest('tr');
                const rowData = $roleTable.row($row).data();
                if (this.checked) {
                    selectedRows.push(rowData);
                } else {
                    selectedRows = selectedRows.filter(row => row.id !== rowData.id);
                }
                if (selectedRows.length > 0) {
                    const selectedDepartments = typeof selectedRows[0]?.departments !== "undefined" ? JSON
                        .parse(selectedRows[0]?.departments) : []

                    const v = selectedDepartments?.map((x) => x.department_code);
                    $('#departmentModal #department_id').val(v);
                    $('#departmentModal #department_id').trigger('change');
                }

                $('.add-permission-btn').toggleClass('hidden', selectedRows.length === 0);
                $('.add-department-btn').toggleClass('hidden', selectedRows.length > 1);
            });

            $('.add-permission-btn').on('click', function() {
                let selectedRoleIds = selectedRows.map(function(row) {
                    return row.id;
                });
                $('#addPermissionModal').find('input[name="selected_role_ids"]').val(JSON.stringify(
                    selectedRoleIds));
                $('#addPermissionModal').modal('show');
            });

            $('.add-department-btn').on('click', function() {
                let selectedRole = selectedRows.map(function(row) {
                    return {
                        id: row.id,
                        name: row.name
                    };
                });
                $('#departmentModal').find('#roleId').val(selectedRole[0].id);
                $('#departmentModal').find('#userId').val("{{ auth()->id() ?? '' }}");
                $('#departmentModal').find('#roleName').val(selectedRole[0].name);
            });

            $('#selectAllPermissions').on('change', function() {
                const checkboxes = $('#permissionsTableBody input[type="checkbox"]:not([disabled])');
                checkboxes.prop('checked', $(this).prop('checked'));
                updateSaveButtonState();
            });

            function updateSaveButtonState() {
                const hasCheckedPermissions = $('#permissionsTableBody input[type="checkbox"]:checked').length > 0;
                const perms = $('#permissionsTableBody input[type="checkbox"]:checked');
                let permNames = [];
                $(perms).each(function() {
                    const permName = $(this).data('permission')
                    permNames.push(permName)
                })
                $('#addPermissionModal').find('#selected_permission_ids').val(JSON.stringify(permNames));
                $('#savePermissionBtn').prop('disabled', !hasCheckedPermissions);
            }

            $('#filter_role').on('change', function() {
                const selectedRole = $(this).val();
                if (selectedRole === '-- Select --') {
                    $('#permissionsTableBody tr').show();
                    return;
                }

                //{{--  $.ajax({
                //     url: '/admin/roles/' + selectedRole + '/permissions',
                //     method: 'GET',
                //     success: function(response) {
                //         const rolePermissions = response.permissions || [];
                //         $('#permissionsTableBody tr').each(function() {
                //             const permissionName = $(this).find('td:nth-child(2)').text().trim();
                //             if (rolePermissions.includes(permissionName)) {
                //                 $(this).show();
                //                 $(this).find('input[type="checkbox"]').prop('checked', true);
                //             } else {
                //                 $(this).hide();
                //                 $(this).find('input[type="checkbox"]').prop('checked', false);
                //             }
                //         });
                //         updateSaveButtonState();
                //     },
                //     error: function(xhr) {
                //         console.error('Error fetching role permissions:', xhr);
                //         $('#permissionsTableBody tr').show();
                //     }
                // }); --}}
            });

            $('#filterRole').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                const permissionName = permissions.map((x) => x.name.toLowerCase())

                // $('#permissionsTableBody tr').each(function() {
                //     permissionName.forEach((v) => {
                //         if (v.includes(searchText)) {
                //             // $('#permissionsTableBody').empty();
                //             const perm = permissions.find((c) => c.name === v)
                //             $('#permissionsTableBody').append(`
            //                 <tr data-role="${perm.id}">
            //                     <td class="fs-12" style="padding: 4px 9px">
            //                         <div class="form-check form-check-md d-flex align-items-center">
            //                             <input class="form-check-input form-checked-dark" type="checkbox"
            //                                 value="" id="checkbox-${perm.id}"
            //                                 data-perm="${perm.name}"
            //                                 ${perm.status !== 'A' ? 'disabled' : ''} />
            //                             <label class="form-check-label" for="checkbox-${perm.id}"></label>
            //                         </div>
            //                     </td>
            //                     <td class="fs-12" style="padding: 4px 9px">${perm.name}</td>
            //                     <td class="fs-12" style="padding: 4px 9px">
            //                         <span class="text-dark">
            //                             ${perm.status === 'A' ? 'Supported' : 'Non-applicable'}
            //                         </span>
            //                     </td>
            //                 </tr>
            //             `);
                //         } else {
                //             $('#permissionsTableBody').empty();
                //         }
                //     })
                // });
            });

            function permissionBelongsToRole(permissionName, roleName) {
                rolePermissions.push(permissionName)
                return true;
            }

            $('#assignPermissionForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        $('#addPermissionModal').modal('hide');
                        toastr.success('Permissions assigned successfully!');
                        $roleTable.ajax.reload();
                        selectedRows = [];
                        $('.add-permission-btn').addClass('hidden');
                    },
                    error: function(xhr) {
                        toastr.error('Error assigning permissions. Please try again.');
                    }
                });
            });

            $('#saveDepartment').on('click', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const formData = $('#departmentAssignForm').serialize();
                $btn.prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                    );
                $.ajax({
                    url: $("#departmentAssignForm").attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#departmentModal').modal('hide');
                            toastr.success('Department assigned successfully!');
                            $roleTable.ajax.reload();
                            selectedRows = [];
                            $('.add-department-btn').addClass('hidden');
                        } else {
                            toastr.error(response.message || 'An error occurred');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.values(errors).forEach(error => {
                                toastr.error(error[0]);
                            });
                        } else {
                            toastr.error(
                                'An error occurred while assigning departments. Please try again.'
                            );
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            'Save <i class="bi bi-save ml-2"></i>');
                    }
                });
            });

            $('#departmentModal').on('hidden.bs.modal', function() {
                $('#departmentAssignForm')[0].reset();
                selectedRows = [];
                $roleTable.ajax.reload();

                $('.add-department-btn').addClass('hidden');
                $('#departmentModal #department_id').val([]);
                $('#departmentModal #department_id').trigger('change');
                $('#roles-table input[type="checkbox"]').prop('checked', false);
            });

            $roleTable.on('click', '.remove-role', function(e) {
                e.preventDefault();
                // const $button = $(this);
                // const roleData = $button.data('role');

                // if (!roleData || !roleData.id) {
                //     toastr.error('Invalid role data');
                //     return;
                // }

                Swal.fire({
                    title: 'Remove Role',
                    text: `Are you sure you want to delete this role?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545',
                    focusCancel: true
                }).then((result) => {
                    // if (result.isConfirmed) {
                    //     $button.prop('disabled', true).html(
                    //         '<i class="bx bx-loader-alt bx-spin"></i>');

                    //     $.ajax({
                    //         url: "{!! route('admin.roles.destroy') !!}",
                    //         method: 'POST',
                    //         data: {
                    //             _token: "{{ csrf_token() }}",
                    //             role_id: roleData.id
                    //         },
                    //         success: function(response) {
                    //             if (response.success) {
                    //                 toastr.success('Role deleted successfully');
                    //                 $roleTable.DataTable().ajax.reload(null,
                    //                     false); // Maintain pagination position
                    //             } else {
                    //                 toastr.error(response.message ||
                    //                     'Failed to delete role');
                    //             }
                    //         },
                    //         error: function(xhr) {
                    //             if (xhr.status === 422 && xhr.responseJSON && xhr
                    //                 .responseJSON.errors) {
                    //                 const errors = xhr.responseJSON.errors;
                    //                 Object.values(errors).forEach(error => {
                    //                     toastr.error(error[0]);
                    //                 });
                    //             } else {
                    //                 toastr.error(
                    //                     'An error occurred while deleting the role');
                    //                 console.error('Role deletion error:', xhr);
                    //             }
                    //         },
                    //         complete: function() {
                    //             // Restore button state
                    //             $button.prop('disabled', false).html(
                    //                 '<i class="fas fa-trash"></i>');
                    //         }
                    //     });
                    // }
                });
            });

            renderPage();
        });
    </script>
@endpush
