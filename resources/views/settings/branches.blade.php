@extends('layouts.app', [
    'pageTitle' => 'Branches - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Branches</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Branches</li>
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
                            <div class="h6 fw-semibold mb-0">Manage branches</div>
                            <div class="d-flex mt-sm-0 mt-0 align-items-center">
                                <button class="btn btn-sm btn-dark btn-fs-13" id="add_user" data-bs-toggle="modal"
                                    data-bs-target="#newUserModal"><i class="ri-add-line" style="vertical-align: -2px;"></i>
                                    <span>Add Branch</span></button>

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
                    <div class="card-title">Branch list</div>
                </div>
                <div class="card-body">
                    <table id="branches-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-scale" id="branch" tabindex="-1" aria-labelledby="branch" data-bs-keyboard="false"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel1">Creating new branch </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="store_branch" action="{{ route('branch.store') }}" method="post">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">branch ISO</label>
                                <input type="text" class="form-control" placeholder="branch ISO" aria-label="branch ISO"
                                    id="branch_code" name="branch_code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">branch Name</label>
                                <input type="text" class="form-control" placeholder="branch Name"
                                    aria-label="branch Name" id="branch_name" name="branch_name">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="add_branch">Save Changes</button>
                            </div>
                        </div>
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-scale" id="edit_branchModal" tabindex="-1" aria-labelledby="edit_branchModal"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel1">Editing branch</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_branch" action="{{ route('branch.edit') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">branch name</label>
                                <input type="hidden" class="form-control" placeholder="branch" aria-label="branch"
                                    id="ed_branch_code" name="ed_branch_code">
                                <input type="text" class="form-control" placeholder="branch" aria-label="branch"
                                    id="ed_branch_name" name="ed_branch_name">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="edit_branch">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('branch.delete') }}" method="post" id="del_branch_form">
        {{ csrf_field() }}
        <input type="hidden" name="del_branch_code" id="del_branch_code">
    </form>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#branches-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 12,
                lengthMenu: [12, 24, 50, 100],
                order: [
                    [0, 'asc']
                ],
                ajax: "{{ route('settings.branch.data') }}",
                columns: [{
                        data: 'branch_code',
                        class: 'highlight-idx'
                    },
                    {
                        data: 'branch_name',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'branch_code',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'description',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'status',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    }, {
                        data: 'action',
                        searchable: false,
                        sortable: false,
                        defaultContent: "<b style=''>_</b>"
                    },
                ]
            });

            $('.dataTable').on('click', 'tbody td #edit_branch', function() {
                var branch_code = $(this).closest('tr').find('td:eq(0)').text();
                var branch_name = $(this).closest('tr').find('td:eq(1)').text();
                $("#ed_branch_code").val(branch_code);
                $("#ed_branch_name").val(branch_name);
                $('#edit_branchModal').modal('show');

            });

            $('.dataTable').on('click', 'tbody td #activate_branch', function() {
                var branch_code = $(this).closest('tr').find('td:eq(0)').text();
                var status = $(this).closest('tr').find('td:eq(2)').find('#activate_branch').val();
                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to ' + status + ' the branch',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#del_branch_code").val(branch_code);
                        $('#del_branch_form').submit();
                    }
                });
            });

            $("#store_branch").validate({
                rules: {
                    branch_code: {
                        required: true,
                        maxlength: 5
                    },
                    branch_name: {
                        required: true,
                        maxlength: 100
                    }
                },
                messages: {
                    branch_code: {
                        required: "branch ISO is required",
                        maxlength: "branch ISO must be at most 5 characters"
                    },
                    branch_name: {
                        required: "branch name is required",
                        maxlength: "branch name must be at most 100 characters"
                    }
                },
                errorPlacement: function(error, element) {
                    // Customize the placement of error messages
                    error.addClass("text-danger"); // Add red color to the error message
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    // Highlight the input field with an error
                    $(element).addClass('error').removeClass('valid');
                },
                unhighlight: function(element) {
                    // Remove the highlight from the input field on valid input
                    $(element).removeClass('error').addClass('valid');
                },
                submitHandler: function(form, event) {
                    // Custom logic before form submission
                    event.preventDefault();
                    // For example, you might want to show a confirmation dialog
                    var isConfirmed = confirm("Are you sure you want to submit the form?");

                    if (isConfirmed) {
                        // If confirmed, you can proceed with the form submission
                        form.submit();
                    } else {
                        // If not confirmed, prevent the form submission
                        return false;
                    }
                }
            });
            // <!-- END -->
        });
    </script>
@endpush
