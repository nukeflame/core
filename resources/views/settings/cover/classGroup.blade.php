@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Class Groups</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#add_classGroup" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add class group </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_classGroup" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add class group</button> --}}

    <table class="table" id="classGroup-table">
        <thead>
            <tr>
                <th>Group Code</th>
                <th>Group Name</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="add_classGroup" tabindex="-1" aria-labelledby="add_classGroup" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new classGroup </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_classGroup" action="{{ route('classGroup.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">group Code</label>
                            <input type="text" class="form-control" placeholder="group Code" aria-label="group Code" id="group_code" name="group_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">group Name</label>
                            <input type="text" class="form-control" placeholder="group Name" aria-label="group Name" id="group_name" name="group_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_classGroup">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_classGroupModal" tabindex="-1" aria-labelledby="edit_classGroupModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing classGroup</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_classGroup" action="{{ route('classGroup.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">classGroup</label>
                            <input type="hidden" class="form-control" placeholder="classGroup" aria-label="classGroup" id="ed_group_code" name="ed_group_code">
                            <input type="text" class="form-control" placeholder="classGroup" aria-label="classGroup" id="ed_group_name" name="ed_group_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_classGroup">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('classGroup.delete') }}" method="post" id="del_classGroup_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_group_code" id="del_group_code">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#classGroup-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('classGroup.data') }}",
            columns: [{
                    data: 'group_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'group_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_classGroup', function() {
            var group_code = $(this).closest('tr').find('td:eq(0)').text();
            var group_name = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_group_code").val(group_code);
            $("#ed_group_name").val(group_name);
            $('#edit_classGroupModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_classGroup', function() {
            var group_code = $(this).closest('tr').find('td:eq(0)').text();
            var status = $(this).closest('tr').find('td:eq(2)').find('#activate_classGroup').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the classGroup',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_group_code").val(group_code);
                    $('#del_classGroup_form').submit();
                }
            });
        });

        $("#store_classGroup").validate({
            rules: {
                group_code: {
                    required: true,
                    maxlength: 5
                },
                group_name: {
                    required: true,
                    maxlength: 100
                }
            },
            messages: {
                group_code: {
                    required: "classGroup ISO is required",
                    maxlength: "classGroup ISO must be at most 5 characters"
                },
                group_name: {
                    required: "classGroup name is required",
                    maxlength: "classGroup name must be at most 100 characters"
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