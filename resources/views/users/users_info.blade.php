@extends('layouts.app', [
    'pageTitle' => 'Users - ' . $company->company_name,
])

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Users</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Action Header Card -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card mt-4">
                <div class="card-body">
                    <div class="contact-header">
                        <div class="d-flex d-block align-items-center justify-content-between">
                            <div class="h6 fw-semibold mb-0">Manage users</div>
                            <div class="d-flex mt-sm-0 mt-0 align-items-center">
                                <button class="btn btn-sm btn-dark btn-fs-13" id="createUserBtn">
                                    <i class="ri-add-line" style="vertical-align: -2px;"></i>
                                    <span>Create User</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User List Card -->
    <div class="row mt-1">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">User list</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Department</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Is Employee?</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Create/Edit Modal -->
    <div class="modal effect-super-scaled md-wrapper" id="userModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="userModalLabel">
                        <i class="bx bx-user me-2 fs-15" id="modalIcon"></i>
                        <span id="modalTitle">Create User</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="p-3">
                    <form id="userForm" method="POST">
                        @csrf
                        <input type="hidden" id="user_id" name="user_id">
                        <input type="hidden" id="form_method" name="_method" value="POST">

                        <!-- Basic Information Section -->
                        <div class="row mb-3 mt-2">
                            <div class="col-12">
                                <h6 class="text-dark font-weight-bold pb-0 mb-0">Basic Information</h6>
                                <hr class="mb-2 p-0 mt-2" />
                            </div>
                        </div>

                        <!-- Username and Email Row -->
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="username" class="form-label fs-14">Username <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control color-blk @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username') }}" placeholder="e.g., pknuek"
                                    required>
                                <small id="usernameHelp" class="form-text text-muted">
                                    3-20 characters, alphanumeric only
                                </small>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fs-14">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control color-blk @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="e.g., pknuek@acentriagroup.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- First Name and Last Name Row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fs-14">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control color-blk @error('first_name') is-invalid @enderror" id="first_name"
                                    name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label fs-14">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control color-blk @error('last_name') is-invalid @enderror" id="last_name"
                                    name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone and Staff Checkbox Row -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label fs-14">Phone Number</label>
                                <input type="text" class="form-control color-blk" id="phone_number"
                                    name="phone_number" placeholder="e.g., +254 700 123456">
                                <div class="invalid-feedback" id="phone_number_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="is_staff" class="form-label fs-14">&nbsp;</label>
                                <div class="form-check form-check-lg d-flex align-items-center">
                                    <input class="form-check-input form-checked-dark" type="checkbox" value="1"
                                        id="is_staff" name="is_staff">
                                    <label class="form-check-label" for="is_staff">
                                        Is Staff?
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- System Access Section -->
                        <div class="row my-3">
                            <div class="col-12">
                                <h6 class="text-dark font-weight-bold mb-0 pb-0">System Access</h6>
                                <hr class="mb-2 p-0 mt-2" />
                            </div>
                        </div>

                        <!-- Department and Role Row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="department_id" class="form-label fs-14">Department <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select
                                        class="form-inputs select2 color-blk @error('department_id') is-invalid @enderror"
                                        id="department_id" name="department_id">
                                        <option value="">Select Department</option>
                                        @if ($departments)
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->department_code }}"
                                                    {{ old('department_id') == $department->department_code ? 'selected' : '' }}>
                                                    {{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div id="error_department_id" class="pt-1 text-danger"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="role_id" class="form-label fs-14">Role <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2 @error('role_id') is-invalid @enderror"
                                        id="role_id" name="role_id">
                                        <option value="">Select Role</option>
                                    </select>
                                </div>
                                <div id="error_role_id" class="pt-1 text-danger"></div>
                            </div>
                        </div>

                        <!-- Create-only Options -->
                        <div class="row mb-3" id="create_only_options">
                            <div class="col-md-6">
                                <div class="form-check form-check-md d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="send_welcome_email" name="send_welcome_email" checked>
                                    <label class="form-check-label" for="send_welcome_email">
                                        Send welcome email with login instructions
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-md d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="require_password_change" name="require_password_change" checked>
                                    <label class="form-check-label" for="require_password_change">
                                        Require password change on first login
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer mt-4 p-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="saveUserBtn"
                                class="btn btn-dark btn-wave waves-effect waves-light">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Assignment Modal -->
    <div class="modal effect-super-scaled md-wrapper" id="userRoleModal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="staticBackdropLabel">
                        <i class="bx bx-shield-plus"></i>
                        Assign Role
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
                                <label for="role_ids" class="form-label fs-14">Assign to Roles:</label>
                                <select name="role_ids[]" id="role_ids" multiple class="form-control form-inputs">
                                    {{-- Role options will be populated dynamically --}}
                                </select>
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
            const ROUTES = {
                userData: '{!! route('admin.users.data') !!}',
                userStore: '{{ route('admin.users.store') }}',
                userDestroy: '{{ route('admin.user.destroy') }}',
                usernameCheck: '{{ route('admin.username.check') }}',
                emailDomainCheck: '{{ route('admin.email.domain.check') }}',
                rolesByDepartment: '{{ route('admin.roles.by.department') }}',
                rolesAssign: '{{ route('admin.roles.assign') }}'
            };

            const CSRF_TOKEN = '{{ csrf_token() }}';

            let selectedRows = [];
            let currentUserId = null;

            const $userTable = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 12,
                lengthMenu: [12, 24, 50, 100],
                order: [
                    [0, 'asc']
                ],
                ajax: ROUTES.userData,
                columns: [{
                        data: 'id',
                        name: 'id',
                        defaultContent: "<b class='dashes'>_</b>",
                        className: 'highlight-idx'
                    },
                    {
                        data: 'username',
                        name: 'username',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'full_name',
                        name: 'full_name',
                        defaultContent: "<b class='dashes'>_</b>",
                        className: 'highlight-view-point'
                    },
                    {
                        data: 'department_name',
                        name: 'department.department_name',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'role_name',
                        name: 'roles.name',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'is_employee',
                        name: 'is_employee',
                        defaultContent: "<b class='dashes'>_</b>",
                        render: function(data) {
                            return data == 1 ?
                                '<span class="badge bg-success">Yes</span>' :
                                '<span class="badge bg-secondary">No</span>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'last_login',
                        name: 'last_login',
                        defaultContent: "<b class='dashes'>_</b>",
                        render: function(data) {
                            return data ? moment(data).format('MMM DD, YYYY HH:mm') : 'Never';
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'highlight-desc-3 highlight-overflow',
                    }
                ],
                drawCallback: function(settings) {
                    $('[data-bs-toggle="dropdown"]').dropdown();
                }
            });

            initializeSelect2();
            bindFormEvents();
            bindTableEvents();
            bindModalEvents();

            function initializeSelect2() {
                $('.form-inputs').select2({
                    dropdownParent: $('#userModal'),
                    width: '100%'
                });
            }

            function bindFormEvents() {
                $("#first_name, #last_name").on('blur', handleNameBlur);
                $("#department_id").on('change', handleDepartmentChange);
                $("#username").on('blur', validateUsername);
                $("#email").on('blur', validateEmail);
                $('#createUserBtn').on('click', showCreateUserModal);
                $('#userForm').on('submit', handleFormSubmission);
            }

            function bindTableEvents() {
                $userTable.on('click', '.edit-user', function(e) {
                    e.preventDefault();
                    const userId = $(this).data('user-id');

                    if (userId) {
                        editUser(userId);
                    } else {
                        toastr.error('User ID not found');
                    }
                });

                $userTable.on('click', '.user-assign-role', function(e) {
                    e.preventDefault();
                    const userId = $(this).data('user-id') || $(this).closest('tr').find('.edit-user').data(
                        'user-id');

                    if (userId) {
                        handleRoleAssignment(userId);
                    } else {
                        toastr.error('User ID not found');
                    }
                });

                $userTable.on('click', '.remove-user', function(e) {
                    e.preventDefault();
                    const email = $(this).data('email');
                    const userId = $(this).data('user-id');

                    if (email) {
                        handleUserRemoval(email, $(this));
                    } else {
                        toastr.error('User email not found');
                    }
                });

                $(document).on('click', '[onclick*="changeStatus"]', function(e) {
                    console.log('Change status clicked');
                    // Your existing changeStatus function will handle this
                });

                $(document).on('click', '[onclick*="resetPassword"]', function(e) {
                    console.log('Reset password clicked');
                    // Your existing resetPassword function will handle this
                });

                $(document).on('click', '[onclick*="changeDepartment"]', function(e) {
                    console.log('Change department clicked');
                    // Your existing changeDepartment function will handle this
                });
            }

            function bindModalEvents() {
                $('#userModal').on('hidden.bs.modal', resetUserForm);

                $('#userRoleModal').on('shown.bs.modal', function() {
                    $('.form-inputs').select2({
                        dropdownParent: $('#userRoleModal'),
                        width: '100%'
                    });
                });

                $('#saveRoleBtn').on('click', handleRoleAssignmentSave);
            }

            function handleNameBlur() {
                const firstName = $("#first_name").val().trim();
                const lastName = $("#last_name").val().trim();

                if (firstName && lastName && !$("#username").val()) {
                    const suggestedUsername = generateUsername(firstName, lastName);
                    checkUsernameAvailability(suggestedUsername, true);
                }
            }

            function generateUsername(firstName, lastName) {
                return (firstName.charAt(0) + lastName).toLowerCase().replace(/[^a-z0-9]/g, '');
            }

            function handleDepartmentChange() {
                const departmentId = $(this).val();

                if (departmentId) {
                    loadRolesByDepartment(departmentId);
                } else {
                    resetRoleSelect();
                }
            }

            function loadRolesByDepartment(departmentId) {
                $.ajax({
                    url: ROUTES.rolesByDepartment,
                    method: "GET",
                    data: {
                        department_id: departmentId
                    },
                    success: function(data) {
                        populateRoleOptions(data);

                        const selectedRole = $('#role_id').data('selected-role');
                        if (selectedRole) {
                            setTimeout(() => {
                                $('#role_id').val(selectedRole).trigger('change');
                                $('#role_id').removeData('selected-role');
                            }, 100);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading roles:', xhr);
                        resetRoleSelect();
                    }
                });
            }

            function populateRoleOptions(roles) {
                let options = '<option value="">Select Role</option>';
                $.each(roles, function(key, role) {
                    options += `<option value="${role.id}">${role.name}</option>`;
                });
                $("#role_id").html(options);
            }

            function resetRoleSelect() {
                $("#role_id").html('<option value="">Select Role</option>');
            }

            function validateUsername() {
                const username = $(this).val().trim();
                const $field = $(this);

                if (!username) return;

                clearFieldErrors($field);

                if (!isValidUsernameFormat(username)) {
                    showUsernameFormatError($field);
                    return;
                }

                checkUsernameAvailability(username, false);
            }

            function isValidUsernameFormat(username) {
                return /^[a-z0-9]{3,20}$/.test(username);
            }

            function showUsernameFormatError($field) {
                $field.removeClass('is-valid').addClass('is-invalid');
                updateUsernameHelp('Username must be 3-20 alphanumeric characters', 'danger');
            }

            function checkUsernameAvailability(username, isAutoGenerated) {
                $.ajax({
                    url: ROUTES.usernameCheck,
                    method: "POST",
                    data: {
                        username: username,
                        _token: CSRF_TOKEN
                    },
                    success: function(response) {
                        handleUsernameCheckResponse(response, username, isAutoGenerated);
                    },
                    error: function(xhr) {
                        console.error('Username check error:', xhr);
                    }
                });
            }

            function handleUsernameCheckResponse(response, username, isAutoGenerated) {
                const $usernameField = $("#username");

                if (response.exists) {
                    if (isAutoGenerated) {
                        const newUsername = username + Math.floor(Math.random() * 100);
                        $usernameField.val(newUsername);
                    } else {
                        $usernameField.removeClass('is-valid').addClass('is-invalid');
                        updateUsernameHelp('This username is already taken', 'danger');
                    }
                } else {
                    if (isAutoGenerated) {
                        $usernameField.val(username);
                    } else {
                        $usernameField.removeClass('is-invalid').addClass('is-valid');
                        updateUsernameHelp('Username is available', 'success');
                    }
                }
            }

            function updateUsernameHelp(message, type) {
                const $help = $("#usernameHelp");
                $help.removeClass('text-muted text-danger text-success');

                switch (type) {
                    case 'danger':
                        $help.addClass('text-danger');
                        break;
                    case 'success':
                        $help.addClass('text-success');
                        break;
                    default:
                        $help.addClass('text-muted');
                }

                $help.text(message);
            }

            function validateEmail() {
                const email = $(this).val().trim();
                const $field = $(this);

                if (!email) return;

                clearFieldErrors($field);

                if (!isValidEmailFormat(email)) {
                    showEmailFormatError($field);
                    return;
                }

                const domain = email.split('@')[1];
                checkEmailDomain(domain, $field);
            }

            function isValidEmailFormat(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            function showEmailFormatError($field) {
                $field.removeClass('is-valid').addClass('is-invalid');
                $field.after('<div class="invalid-feedback">Please enter a valid email address</div>');
            }

            function checkEmailDomain(domain, $field) {
                $.ajax({
                    url: ROUTES.emailDomainCheck,
                    method: "POST",
                    data: {
                        domain: domain,
                        _token: CSRF_TOKEN
                    },
                    success: function(response) {
                        handleEmailDomainResponse(response, $field);
                    },
                    error: function(xhr) {
                        console.error('Email domain check error:', xhr);
                    }
                });
            }

            function handleEmailDomainResponse(response, $field) {
                if (!response.allowed) {
                    $field.removeClass('is-valid').addClass('is-invalid');
                    $field.after('<div class="invalid-feedback">This email domain is not allowed</div>');
                } else {
                    $field.removeClass('is-invalid').addClass('is-valid');
                }
            }

            function clearFieldErrors($field) {
                $field.removeClass('is-invalid is-valid');
                $field.siblings('.invalid-feedback').remove();
            }

            function showCreateUserModal() {
                resetUserForm();
                $('#userForm').attr('action', ROUTES.userStore);
                $('#userModal').modal('show');
            }

            function handleFormSubmission(e) {
                e.preventDefault();

                clearFormErrors();
                setSubmitButtonLoading(true);

                const formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: handleFormSuccess,
                    error: handleFormError,
                    complete: function() {
                        setSubmitButtonLoading(false);
                    }
                });
            }

            function handleFormSuccess(response) {
                console.log('Form submission successful:', response);
                $('#userModal').modal('hide');
                $userTable.ajax.reload();

                Swal.fire({
                    title: 'Success!',
                    text: response.message || 'User saved successfully',
                    icon: 'success'
                });
            }

            function handleFormError(xhr) {

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    displayValidationErrors(xhr.responseJSON.errors);
                } else {
                    toastr.error('Error saving user');
                }
            }

            function displayValidationErrors(errors) {
                $.each(errors, function(key, value) {
                    if (key === 'role_id' || key === 'department_id') {
                        $(`#error_${key}`).text(value[0]);
                    } else {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}`).after(`<div class="invalid-feedback">${value[0]}</div>`);
                    }
                });
            }

            function clearFormErrors() {
                $('#userForm').find('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('#error_department_id, #error_role_id').empty();
            }

            function setSubmitButtonLoading(isLoading) {
                const $btn = $("#saveUserBtn");

                if (isLoading) {
                    $btn.prop('disabled', true);
                    $btn.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                    );
                } else {
                    $btn.prop('disabled', false);
                    const buttonText = $('#form_method').val() === 'PUT' ? 'Update' : 'Submit';
                    $btn.html(buttonText);
                }
            }

            function editUser(userId) {
                currentUserId = userId;
                resetUserForm();
                setModalToEditMode();
                loadUserData(userId);
            }

            function setModalToEditMode() {
                $('#modalTitle').text('Edit User');
                $('#modalIcon').removeClass('bx-user').addClass('bx-user-circle');
                $('#form_method').val('PUT');
                $('#create_only_options').hide();
                $('#saveUserBtn').text('Update');
            }

            function loadUserData(userId) {
                $.ajax({
                    url: `/admin/users/${userId}/edit`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log('User data loaded:', data);
                        // populateEditForm(data, userId);
                    },
                    error: function(xhr) {
                        console.error('Error loading user data:', xhr);
                        toastr.error('Error loading user data');
                    }
                });
            }

            function populateEditForm(data, userId) {
                console.log('Populating edit form with data:', data);

                $('#user_id').val(data.id);
                $('#username').val(data.username);
                $('#email').val(data.email);
                $('#first_name').val(data.first_name);
                $('#last_name').val(data.last_name);
                $('#phone_number').val(data.phone_number || '');
                $('#is_staff').prop('checked', data.is_staff == 1);

                if (data.role_id) {
                    $('#role_id').data('selected-role', data.role_id);
                }

                if (data.department_id) {
                    $('#department_id').val(data.department_id).trigger('change');
                }

                $('#userForm').attr('action', `/admin/users/${userId}`);

                $('#userModal').modal('show');
            }

            function handleRoleAssignment(userId) {
                console.log('Role assignment for user ID:', userId);
                // Store user ID for role assignment
                $('#userRoleModal').data('user-id', userId);

                // Load available roles
                loadAvailableRoles();

                // Show the modal
                $('#userRoleModal').modal('show');
            }

            /**
             * Load available roles for assignment
             */
            function loadAvailableRoles() {
                // This would typically load roles from your backend
                // For now, we'll assume roles are loaded or handle this based on your backend logic
                console.log('Loading available roles...');
            }

            function handleRoleAssignmentSave() {
                const userId = $('#userRoleModal').data('user-id');
                const selectedRoles = $('#role_ids').val();

                if (!selectedRoles || selectedRoles.length === 0) {
                    toastr.error('Please select at least one role');
                    return;
                }

                const $saveBtn = $('#saveRoleBtn');
                setButtonLoading($saveBtn, true, 'Assigning...');

                $.ajax({
                    url: ROUTES.rolesAssign,
                    method: 'POST',
                    data: {
                        user_id: userId,
                        role_ids: selectedRoles,
                        _token: CSRF_TOKEN
                    },
                    success: function(response) {
                        $('#userRoleModal').modal('hide');
                        toastr.success(response.message || 'Roles assigned successfully');
                        $userTable.ajax.reload();
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, 'Role assignment');
                    },
                    complete: function() {
                        setButtonLoading($saveBtn, false, '', 'Add');
                    }
                });
            }

            function handleUserRemoval(email, $button) {
                if (!email) {
                    toastr.error('Invalid user data');
                    return;
                }
                showDeleteConfirmation(email, $button);
            }

            function showDeleteConfirmation(email, $button) {
                Swal.fire({
                    title: 'Remove User',
                    text: 'Are you sure you want to delete this user?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeUserDeletion(email, $button);
                    }
                });
            }

            function executeUserDeletion(email, $button) {
                setDeleteButtonLoading($button, true);

                $.ajax({
                    url: ROUTES.userDestroy,
                    method: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        email: email
                    },
                    success: function(response) {
                        handleDeleteSuccess(response);
                    },
                    error: function(xhr) {
                        handleDeleteError(xhr);
                    },
                    complete: function() {
                        setDeleteButtonLoading($button, false);
                    }
                });
            }

            function handleDeleteSuccess(response) {
                if (response.success) {
                    toastr.success('User deleted successfully');
                    $userTable.ajax.reload(null, false);
                } else {
                    toastr.error(response.message || 'Failed to delete user');
                }
            }

            function handleDeleteError(xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    Object.values(xhr.responseJSON.errors).forEach(error => {
                        const errorMessage = Array.isArray(error) ? error[0] : error;
                        toastr.error(errorMessage);
                    });
                    return;
                }

                if (xhr.status === 403) {
                    let forbiddenMessage = 'Access denied';
                    if (xhr.responseJSON?.message) {
                        forbiddenMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            forbiddenMessage = response.message || forbiddenMessage;
                        } catch (e) {
                            console.warn('Could not parse 403 response text:', xhr.responseText);
                        }
                    }

                    toastr.error(forbiddenMessage);
                    return;
                }

                if (xhr.status === 401) {
                    toastr.error('You are not authenticated. Please log in again.');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                    return;
                }

                if (xhr.status === 404) {
                    toastr.error('User not found or has already been deleted');

                    if (typeof $userTable !== 'undefined' && $userTable.ajax) {
                        $userTable.ajax.reload();
                    }
                    return;
                }

                if (xhr.status === 500) {
                    let serverErrorMessage = 'Server error occurred. Please try again later.';
                    if (xhr.responseJSON?.message) {
                        serverErrorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(serverErrorMessage);
                    return;
                }

                if (xhr.status === 0 || xhr.statusText === 'timeout') {
                    toastr.error('Request timed out. Please check your connection and try again.');
                    return;
                }

                let genericErrorMessage = 'An error occurred while deleting the user';

                if (xhr.responseJSON?.message) {
                    genericErrorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            genericErrorMessage = response.message;
                        }
                    } catch (e) {
                        console.warn('Could not parse error response:', xhr.responseText);
                    }
                }

                toastr.error(genericErrorMessage);
            }

            function setDeleteButtonLoading($button, isLoading) {
                if (isLoading) {
                    $button.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i>');
                } else {
                    $button.prop('disabled', false).html('<i class="bx bx-trash"></i>');
                }
            }

            function resetUserForm() {
                const $form = $('#userForm');

                $form[0].reset();
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.is-valid').removeClass('is-valid');
                $('.invalid-feedback').remove();

                $('#user_id').val('');
                $('#form_method').val('POST');

                $('#department_id').val('').trigger('change');
                resetRoleSelect();

                $('#error_department_id, #error_role_id').empty();

                $('#create_only_options').show();
                $('#saveUserBtn').text('Submit');
                $('#modalTitle').text('Create User');
                $('#modalIcon').removeClass('bx-user-circle').addClass('bx-user');

                updateUsernameHelp('3-20 characters, alphanumeric only', 'muted');

                currentUserId = null;
            }

            function setButtonLoading($button, isLoading, loadingText = 'Loading...', originalText = null) {
                if (isLoading) {
                    $button.data('original-text', $button.html());
                    $button.prop('disabled', true);
                    $button.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${loadingText}`
                    );
                } else {
                    $button.prop('disabled', false);
                    const restoreText = originalText || $button.data('original-text') || 'Submit';
                    $button.html(restoreText);
                }
            }

            function handleAjaxError(xhr, context = 'Operation') {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    Object.values(errors).forEach(error => {
                        toastr.error(Array.isArray(error) ? error[0] : error);
                    });
                } else if (xhr.responseJSON?.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error(`${context} failed. Please try again.`);
                }
            }
        });
    </script>
@endpush
