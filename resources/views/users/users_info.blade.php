@extends('layouts.app', [
    'pageTitle' => 'Users - ' . $company->company_name,
])

@section('content')
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

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card mt-4">
                <div class="card-body">
                    <div class="contact-header">
                        <div class="d-flex d-block align-items-center justify-content-between">
                            <div class="h6 fw-semibold mb-0">Manage users</div>
                            <div class="d-flex mt-sm-0 mt-0 align-items-center">
                                <button class="btn btn-sm btn-dark btn-fs-13" id="createUserBtn" class="ri-add-line"
                                    style="vertical-align: -2px;"></i>
                                    <span>Create
                                        User
                                    </span></button>

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

                        <div class="row mb-3 mt-2">
                            <div class="col-12">
                                <h6 class="text-dark font-weight-bold pb-0 mb-0">Basic Information</h6>
                                <hr class="mb-2 p-0 mt-2" />
                            </div>
                        </div>
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
                        <div class="row my-3">
                            <div class="col-12">
                                <h6 class="text-dark font-weight-bold mb-0 pb-0">System Access</h6>
                                <hr class="mb-2 p-0 mt-2" />
                            </div>
                        </div>
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

    <!--New User Modal -->
    {{-- <div class="modal effect-super-scaled md-wrapper" id="newUserModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="staticBackdropLabel"><i
                            class="bx bx-user me-2 fs-15"></i>Create User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="p-3">
                    <form id="addUserForm" method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        <div class="row mb-3 mt-2">
                            <div class="col-12">
                                <h6 class="text-dark font-weight-bold pb-0 mb-0">Basic Information</h6>
                                <hr class="mb-2 p-0 mt-2" />
                            </div>
                        </div>
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
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label fs-14">Phone Number</label>
                                <input type="text"
                                    class="form-control color-blk @error('phone_number') is-invalid @enderror"
                                    id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                                    placeholder="e.g., +254 700 123456">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="checked2" class="form-label fs-14">&nbsp;</label>
                                <div class="form-check form-check-lg d-flex align-items-center">
                                    <input class="form-check-input form-checked-dark" type="checkbox" value=""
                                        id="checkebox-lg" checked="">
                                    <label class="form-check-label" for="checkebox-lg">
                                        Is Staff?
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-12">
                                <h6 class="text-dark font-weight-bold mb-0 pb-0">System Access</h6>
                                <hr class="mb-2 p-0 mt-2" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="department_id" class="form-label fs-14">Department <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select
                                        class="form-inputs select2 color-blk @error('department_id') is-invalid @enderror"
                                        id="department_id" name="department_id">
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->department_code }}"
                                                {{ old('department_id') == $department->department_code ? 'selected' : '' }}>
                                                {{ $department->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
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

    <div class="modal effect-super-scaled md-wrapper" id="editUserModal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="editUserModalLabel"><i
                            class="bx bx-user-circle me-2 fs-15"></i>Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="p-3">
                    <form id="editUserForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_user_id" name="user_id">

                        <div class="row mb-3 mt-2">
                            <div class="col-12">
                                <h6 class="text-dark font-weight-bold pb-0 mb-0">Basic Information</h6>
                                <hr class="mb-2 p-0 mt-2" />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="edit_username" class="form-label fs-14">Username <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control color-blk" id="edit_username" name="username"
                                    placeholder="e.g., pknuek" required>
                                <small id="edit_usernameHelp" class="form-text text-muted">
                                    3-20 characters, alphanumeric only
                                </small>
                                <div class="invalid-feedback" id="username_error"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_email" class="form-label fs-14">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control color-blk" id="edit_email" name="email"
                                    placeholder="e.g., pknuek@acentriagroup.com" required>
                                <div class="invalid-feedback" id="email_error"></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_first_name" class="form-label fs-14">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control color-blk" id="edit_first_name"
                                    name="first_name" required>
                                <div class="invalid-feedback" id="first_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_last_name" class="form-label fs-14">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control color-blk" id="edit_last_name"
                                    name="last_name" required>
                                <div class="invalid-feedback" id="last_name_error"></div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="edit_phone_number" class="form-label fs-14">Phone Number</label>
                                <input type="text" class="form-control color-blk" id="edit_phone_number"
                                    name="phone_number" placeholder="e.g., +254 700 123456">
                                <div class="invalid-feedback" id="phone_number_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_is_staff" class="form-label fs-14">&nbsp;</label>
                                <div class="form-check form-check-lg d-flex align-items-center">
                                    <input class="form-check-input form-checked-dark" type="checkbox" value="1"
                                        id="edit_is_staff" name="is_staff">
                                    <label class="form-check-label" for="edit_is_staff">
                                        Is Staff?
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-12">
                                <h6 class="text-dark font-weight-bold mb-0 pb-0">System Access</h6>
                                <hr class="mb-2 p-0 mt-2" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_department_id" class="form-label fs-14">Department <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2 color-blk" id="edit_department_id"
                                        name="department_id">
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->department_code }}">
                                                {{ $department->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback" id="department_id_error"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_role_id" class="form-label fs-14">Role <span
                                        class="text-danger">*</span></label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="edit_role_id" name="role_id">
                                        <option value="">Select Role</option>
                                    </select>
                                </div>
                                <div class="invalid-feedback" id="role_id_error"></div>
                            </div>
                        </div>

                        <div class="modal-footer mt-4 p-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="userUpdateBtn"
                                class="btn btn-dark btn-wave waves-effect waves-light">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <!--Assign Role Modal -->
    <div class="modal effect-super-scaled md-wrapper" id="userRoleModal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="staticBackdropLabel"><i class="bx bx-shield-plus"></i>
                        Assign
                        Role
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
                                    {{-- @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach --}}
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
            let selectedRows = [];

            const $userTable = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 12,
                lengthMenu: [12, 24, 50, 100],
                order: [
                    [0, 'asc']
                ],
                ajax: '{!! route('admin.users.data') !!}',
                columns: [{
                        data: 'id',
                        defaultContent: "<b class='dashes' style=''>_</b>",
                        className: 'highlight-idx'
                    },
                    {
                        data: 'user_name',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'name',
                        defaultContent: "<b class='dashes' style=''>_</b>",
                        className: 'highlight-view-point'
                    },
                    {
                        data: 'department',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'role',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'phone_number',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'is_employee',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'status',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'last_login',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        sortable: false,
                        className: 'highlight-desc-3 highlight-overflow'
                    },
                ]
            });

            $('.form-inputs').select2({
                dropdownParent: $('#userModal')
            });

            // $('#newUserModal').on('shown.bs.modal', function() {
            //     $(this).find('.select2').select2({
            //         width: '100%',
            //         dropdownParent: $(this)
            //     });
            // });

            // $('#newUserModal').on('hidden.bs.modal', function() {
            //     $("#addUserForm")[0].reset();
            //     $(this).find('.select2').select2({
            //         width: '100%',
            //         dropdownParent: $(this)
            //     });
            // });

            $("#first_name, #last_name").on('blur', function() {
                const firstName = $("#first_name").val().trim();
                const lastName = $("#last_name").val().trim();

                if (firstName && lastName && !$("#username").val()) {
                    const suggestedUsername = (firstName.charAt(0) + lastName).toLowerCase()
                        .replace(/[^a-z0-9]/g, '');

                    $.ajax({
                        url: "{{ route('admin.username.check') }}",
                        method: "POST",
                        data: {
                            username: suggestedUsername,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $("#username").removeClass('is-invalid').addClass('is-valid');
                            if (!response.exists) {
                                $("#username").val(suggestedUsername);
                            } else {
                                $("#username").val(suggestedUsername + Math.floor(Math
                                    .random() * 100));
                            }
                        }
                    });
                }
            });

            $("#department_id").change(function() {
                $(this).val();
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

            $("#username").on('blur', function() {
                const username = $(this).val().trim();
                if (username) {
                    $(".invalid-feedback").empty();
                    if (!/^[a-z0-9]{3,20}$/.test(username)) {
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        $(this).after(
                            '<div class="invalid-feedback">Username must be 3-20 alphanumeric characters</div>'
                        );
                        return;
                    }

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
                                $("#usernameHelp").html('Username is available');
                            }
                        }
                    });
                }
            });

            $("#email").on('blur', function() {
                const email = $(this).val().trim();
                if (email) {
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

            $("#addUserForm").submit(function(e) {
                e.preventDefault();
                $("#userSaveBtn").prop('disabled', true);

                $("#userSaveBtn").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#newUserModal').modal('hide');
                        $('#users-table').DataTable().ajax.reload();
                        Swal.fire({
                            title: 'Success!',
                            text: 'User created successfully',
                            icon: 'success'
                        });
                        $("#addUserForm")[0].reset();
                    },
                    error: function(xhr) {
                        $('.invalid-feedback').empty();
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}`).after(
                                `<div class="invalid-feedback">${value}</div>`);
                        });
                    },
                    complete: function() {
                        resetForm()
                        $('#usernameHelp').remove();
                        $("#userSaveBtn").prop('disabled', false);
                        $("#userSaveBtn").html('Submit');
                    }
                });
            });

            $('#createUserBtn').on('click', function() {
                resetForm();
                $('#userForm').attr('action', '{{ route('admin.users.store') }}');
                $('#userModal').modal('show');
            });

            function editUser(userId) {
                resetForm();

                $('#modalTitle').text('Edit User');
                $('#modalIcon').removeClass('bx-user').addClass('bx-user-circle');
                $('#form_method').val('PUT');
                $('#create_only_options').hide();
                $('#saveUserBtn').text('Update');

                $.ajax({
                    url: '/admin/users/' + userId + '/edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data)
                        // $('#user_id').val(data.id);
                        // $('#username').val(data.username);
                        // $('#email').val(data.email);
                        // $('#first_name').val(data.first_name);
                        // $('#last_name').val(data.last_name);
                        // $('#phone_number').val(data.phone_number);
                        // $('#is_staff').prop('checked', data.is_staff == 1);

                        // // Store the role ID to be set after department change loads roles
                        // $('#role_id').data('selected-role', data.role_id);

                        // // Set department and trigger change to load roles
                        // $('#department_id').val(data.department_id).trigger('change');

                        // // Set form action
                        // $('#userForm').attr('action', '/admin/users/' + userId);

                        // // Show the modal
                        // $('#userModal').modal('show');
                    }
                });
            }

            window.editUser = editUser;

            $('#userForm').on('submit', function(e) {
                e.preventDefault();

                $(this).find('.is-invalid').removeClass('is-invalid');
                $("#saveUserBtn").prop('disabled', true);
                $("#saveUserBtn").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#userModal').modal('hide');
                        toastr.success(response.message || 'User saved successfully');
                        if (typeof userTable !== 'undefined') {
                            userTable.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                if (key === 'role_id' || key === 'department_id') {
                                    // console.log('#error_' + key)
                                    $('#error_' + key).text(value[0]);
                                } else {
                                    $('#' + key).addClass('is-invalid');
                                    $('#' + key + '_error').text(value[0]);
                                }
                            });
                        } else {
                            toastr.error('Error saving user');
                        }
                    },
                    complete: function() {
                        // $('#usernameHelp').remove();
                        $("#saveUserBtn").prop('disabled', false);
                        $("#saveUserBtn").html('Submit');
                    }
                });
            });

            $('#userModal').on('hidden.bs.modal', function() {
                resetForm();
            });

            $('#userRoleModal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#userRoleModal')
                });
            });

            $userTable.on('click', '.user-list-btns .user-assign-role', function(e) {
                e.preventDefault()

                // let selectedRoleIds = selectedRows.map(function(row) {
                //     return row.id;
                // });

                // $('#saveRoleBtn').prop('disabled', selectedRows.length === 0);
                // $('#roleModal').find('input[name="selected_role_ids"]').val(JSON.stringify(
                //     selectedRoleIds));
                // $('#totalPermissionSelected').text('Selected Permissions: ' + selectedRows.length);

                // const selectedPermissions = selectedRows?.map((x) => x.name);
                // $('#permission_ids').val(selectedPermissions);

                $('#userRoleModal').modal('show');
            })

            $userTable.on('click', '.remove-user', function(e) {
                e.preventDefault();
                const $button = $(this);
                const email = $button.data('email');

                if (!email) {
                    toastr.error('Invalid role data');
                    return;
                }

                Swal.fire({
                    title: 'Remove User',
                    text: `Are you sure you want to delete this user?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $button.prop('disabled', true).html(
                            '<i class="bx bx-loader-alt bx-spin"></i>');

                        $.ajax({
                            url: "{!! route('admin.user.destroy') !!}",
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                email: email
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success('User deleted successfully');
                                    $userTable.ajax.reload(null,
                                        false);
                                } else {
                                    toastr.error(response.message ||
                                        'Failed to delete user');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 422 && xhr.responseJSON && xhr
                                    .responseJSON.errors) {
                                    const errors = xhr.responseJSON.errors;
                                    Object.values(errors).forEach(error => {
                                        toastr.error(error[0]);
                                    });
                                } else {
                                    toastr.error(
                                        'An error occurred while deleting the user');
                                    console.error('User deletion error:', xhr);
                                }
                            },
                            complete: function() {
                                $button.prop('disabled', false).html(
                                    '<i class="bx bx-trash"></i> Delete');
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#userForm')[0].reset();
                $('#userForm').find('.is-invalid').removeClass('is-invalid');
                $('#user_id').val('');
                $('#form_method').val('POST');
                $('#department_id').val('').trigger('change');
                $('#role_id').empty().append('<option value="">Select Role</option>');
                $('#error_department_id').empty();
                $('#error_role_id').empty();
                $('#create_only_options').show();
                $('#saveUserBtn').text('Submit');
                $('#modalTitle').text('Create User');
                $('#modalIcon').removeClass('bx-user-circle').addClass('bx-user');
            }
        });
    </script>
@endpush
