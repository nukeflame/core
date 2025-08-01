@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Reins Class</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#reinsClass" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add reinsClass </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_reinsClass" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add reinsClass</button> --}}

    <table class="table" id="reinsClass-table">
        <thead>
            <tr>
                <th>Group Code</th>
                <th>Class Code</th>
                <th>Class Description</th>
                <th>Status</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="reinsClass" tabindex="-1" aria-labelledby="reinsClass" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new reins Class </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_reinsClass" action="{{ route('reinsClass.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Class Group</label>
                            <select id="class_group" name="class_group" class="form-select select2" required >
                                <option selected value="">Choose Class Groups</option>
                                @foreach($classGroups as $classGroup)
                                <option value="{{$classGroup->group_code}}">{{$classGroup->group_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Reins Class Code</label>
                            <input type="text" class="form-control" placeholder="Reins Class Code" aria-label="Reins Class Code" id="class_code" name="class_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Reins Class Name</label>
                            <input type="text" class="form-control" placeholder="Reins Class Name" aria-label="Reins Class Name" id="class_name" name="class_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_reinsClass">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_reinsClassModal" tabindex="-1" aria-labelledby="edit_reinsClassModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing reinsClass</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_reinsClass" action="{{ route('reinsClass.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_class_code" name="ed_class_code">

                        <div class="col-md-12">
                            <label class="form-label">Class Group</label>
                            <select id="ed_class_group" name="ed_class_group" class="form-select select2" required >
                                <option value="">Choose Class Groups</option>
                                @foreach($classGroups as $classGroup)
                                <option value="{{$classGroup->group_code}}">{{$classGroup->group_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Reins Class Name</label>
                            <input type="text" class="form-control" placeholder="Reins Class Name" aria-label="Reins Class Name" id="ed_class_name" name="ed_class_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_reinsClass">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('reinsClass.delete') }}" method="post" id="del_reinsClass_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_class_code" id="del_class_code">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#reinsClass').on('shown.bs.modal', function () {
          $('.form-select').select2({
              dropdownParent: $('#reinsClass')
          });
      });

        $('#edit_reinsClassModal').on('shown.bs.modal', function () {
          $('.form-select').select2({
              dropdownParent: $('#edit_reinsClassModal')
          });
      });

        $('#reinsClass-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('reinsClass.data') }}",
            columns: [
                {
                    data: 'group_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'class_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'class_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'status',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_reinsClass', function() {
            var ed_class_group = $(this).closest('tr').find('td:eq(0)').text();
            var ed_class_code = $(this).closest('tr').find('td:eq(1)').text();
            var ed_class_name = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_class_code").val(ed_class_code);
            $("#ed_class_name").val(ed_class_name);
            $("#ed_class_group").val(ed_class_group);
            $('#edit_reinsClassModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_reinsClass', function() {
            var del_class_code = $(this).closest('tr').find('td:eq(1)').text();
            var status = $(this).closest('tr').find('td:eq(4)').find('#activate_reinsClass').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the reinsClass',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_class_code").val(del_class_code);
                    $('#del_reinsClass_form').submit();
                }
            });
        });

        $("#store_reinsClass").validate({
            rules: {
                reinsClass_cov_no: {
                    required: true,
                    maxlength: 20
                },
                insured_name: {
                    required: true,
                    maxlength: 80
                },
                agency_name: {
                    required: true,
                    maxlength: 80
                }
            },
            messages: {
                reinsClass_cov_no: {
                    required: "Reins Class Code is required",
                    maxlength: "Reins Class Code must be at most 3 characters"
                },
                insured_name: {
                    required: "insured name is required",
                    maxlength: "insured name must be at most 100 characters"
                },
                agency_name: {
                    required: "agency name is required",
                    maxlength: "agency name must be at most 100 characters"
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