@extends('layouts.app', [
    'pageTitle' => 'Departments - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Departments</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Departments</li>
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
                            <div class="h6 fw-semibold mb-0">Manage departments</div>
                            <div class="d-flex mt-sm-0 mt-0 align-items-center">
                                <button class="btn btn-sm btn-dark btn-fs-13" id="add_department_btn" data-bs-toggle="modal"
                                    data-bs-target="#createDepartmentModal"><i class="ri-add-line"
                                        style="vertical-align: -2px;"></i>
                                    <span>Add Department</span></button>
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
                    <div class="card-title">Department list</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="departments-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    {{-- <th>Cost Center</th> --}}
                                    {{-- <th>Annual Budget ({{ $defaultCurrency }})</th> --}}
                                    <th>Parent Department</th>
                                    <th>Primary Location</th>
                                    <th>Start Date</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal md-wrapper effect-super-scaled" id="createDepartmentModal" tabindex="-1"
        aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDepartmentModalLabel"><i class="bx bx-building me-1"></i> Create
                        Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="departmentForm" action="{{ route('settings.department.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="company" class="form-label">Company <span class="text-danger">*</span></label>
                            <div class="card-md">
                                <select id="company" name="company_id" class="form-inputs select2 mb-1" required>
                                    <option value="" selected disabled>Select company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->company_id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departmentCode" class="form-label">Department Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-inputs" id="departmentCode" name="code"
                                        placeholder="e.g. HR-001, IT-001" required>
                                    <div class="form-text">Unique identifier for this department</div>
                                    <div class="invalid-feedback">Please enter a department code.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departmentName" class="form-label">Department Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-inputs" id="departmentName" name="name"
                                        placeholder="e.g. Human Resources, IT" required>
                                    <div class="invalid-feedback">Please enter a department name.</div>
                                </div>
                            </div>
                        </div>
                        <h6 class="mt-0 mb-2 border-bottom pb-2 fs-15">Department Details</h6>
                        <div class="mb-2">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-inputs" rows="3"
                                placeholder="Brief description of department functions and responsibilities"></textarea>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="costCenter" class="form-label">Cost Center</label>
                                    <input type="text" class="form-inputs" id="costCenter" name="cost_center"
                                        placeholder="e.g. CC-0042">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="budget" class="form-label">Annual Budget</label>
                                    <input type="text" class="form-inputs" id="budget" name="budget"
                                        placeholder="e.g. 500,000" onkeyup="this.value=numberWithCommas(this.value)">
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parentDepartment" class="form-label">Parent Department</label>
                                    <div class="card-md">
                                        <select id="parentDepartment" name="parent_id" class="form-inputs select2">
                                            <option value="" selected>None (Top Level)</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Primary Location</label>
                                    <div class="card-md">
                                        <select id="location" name="location_id" class="form-inputs select2">
                                            <option value="" selected disabled>Select location</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Management -->
                            <h6 class="mt-0 mb-3 border-bottom pb-2 fs-15">Management</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="departmentHead" class="form-label">Department Head</label>
                                        <div class="card-md">
                                            <select id="departmentHead" name="department_head_id"
                                                class="form-inputs select2">
                                                <option value="" selected disabled>Select employee</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="startDate" class="form-label">Start Date</label>
                                        <input type="date" class="form-inputs" id="startDate" name="start_date">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="contactEmail" class="form-label">Department Email</label>
                                <input type="email" class="form-inputs" id="contactEmail" name="email" />
                            </div>

                            <h6 class="mt-0 mb-3 border-bottom pb-2 fs-15">Management</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span
                                                class="text-danger">*</span></label>
                                        <div class="card-md">
                                            <select id="status" name="status" class="form-inputs select2" required>
                                                <option value="active" selected>Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="planned">Planned</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-light btn-sm"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-dark btn-sm" id="saveDepartmentBtn">Save
                                Department</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#departments-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 12,
                responsive: true,
                lengthMenu: [12, 24, 50, 100],
                order: [
                    [0, 'asc']
                ],
                ajax: '{{ route('settings.departments.data') }}',
                columns: [{
                        data: 'id',
                        class: 'highlight-idx'
                    },
                    {
                        data: 'department_name',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'department_code',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'description',
                        defaultContent: "<b class='dashes' style=''>_</b>",
                        class: 'highlight-description'
                    },
                    // {
                    //     data: 'cost_center',
                    //     defaultContent: "<b class='dashes' style=''>_</b>"
                    // },
                    // {
                    //     data: 'annual_budget',
                    //     defaultContent: "<b class='dashes' style=''>_</b>"
                    // },
                    {
                        data: 'parent_department',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'location',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'start_date',
                        defaultContent: "<b class='dashes' style=''>_</b>",
                        class: 'highlight-index'
                    },
                    {
                        data: 'email',
                        defaultContent: "<b class='dashes' style=''>_</b>",
                        class: 'highlight-action'
                    },
                    {
                        data: 'status',
                        defaultContent: "<b class='dashes' style=''>_</b>",
                        class: 'highlight-index'
                    }, {
                        data: 'action',
                        searchable: false,
                        sortable: false,
                        defaultContent: "<b style=''>_</b>"
                    },
                ]
            });

            $('.dataTable').on('click', 'tbody td #edit_cDepartments', function() {
                var ed_company_id = $(this).closest('tr').find('td:eq(0)').text();
                $("#ed_company_id").val(ed_company_id);
                var ed_department_code = $(this).closest('tr').find('td:eq(1)').text();
                $("#ed_department_code").val(ed_department_code);
                var ed_department_name = $(this).closest('tr').find('td:eq(2)').text();
                $("#ed_department_name").val(ed_department_name);
                $('#edit_cDepartmentsModal').modal('show');
            });

            $("#departmentForm").validate({
                // errorClass: "errorClass",
                rules: {
                    company_id: "required",
                    code: {
                        required: true,
                        minlength: 3
                    },
                    name: {
                        required: true,
                        minlength: 3
                    },
                    status: "required"
                },
                messages: {
                    company_id: "Please select a company",
                    code: {
                        required: "Please enter a department code",
                        minlength: "Code must be at least 3 characters long"
                    },
                    name: {
                        required: "Please enter a department name",
                        minlength: "Name must be at least 3 characters long"
                    },
                    status: "Please select a status"
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
                submitHandler: function(form) {
                    $('#saveDepartmentBtn')
                        .prop('disabled', true)
                        .html(`<span class="me-2">Saving...</span><div class="loading"></div>`);
                    var formData = new FormData(form);
                    $.ajax({
                        url: $(form).attr('action'),
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#createDepartmentModal').modal('hide');
                            $('#departments-table').DataTable().ajax.reload();
                            toastr.success('Department created successfully!');
                            $(form).trigger("reset");
                            $('#saveDepartmentBtn')
                                .prop('disabled', false)
                                .html('Save Department');
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Error: ' + error);
                            $('#saveDepartmentBtn')
                                .prop('disabled', false)
                                .html('Save Department');
                        },
                    });
                    return false;
                }
            });

            $('#company').change(function() {
                $(this).valid();
                let companyId = $(this).val();
                if (companyId) {
                    // $.ajax({
                    //     url: '/api/companies/' + companyId + '/departments',
                    //     type: 'GET',
                    //     dataType: 'json',
                    //     success: function(data) {
                    //         $('#parentDepartment').empty();
                    //         $('#parentDepartment').append(
                    //             '<option value="" selected>None (Top Level)</option>');

                    //         $.each(data, function(key, value) {
                    //             $('#parentDepartment').append('<option value="' + value
                    //                 .id + '">' + value.name + '</option>');
                    //         });
                    //     }
                    // });
                }
            });

            $('#createDepartmentModal').on('shown.bs.modal', function() {
                $('.select2').select2({
                    width: '100%',
                    dropdownParent: $('#createDepartmentModal')
                });
            });

            $('#createDepartmentModal').on('hidden.bs.modal', function() {
                $('#departmentForm').removeClass('was-validated');
                $('#departmentForm')[0].reset();
            });
        });
    </script>
@endpush
