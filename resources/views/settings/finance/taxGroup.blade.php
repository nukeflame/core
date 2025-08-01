@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>tax groups</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#taxGroup" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add taxGroup </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_taxGroup" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add taxGroup</button> --}}

    <table class="table" id="taxGroup-table">
        <thead>
            <tr>
                <th>group id</th>
                <th>group description</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="taxGroup" tabindex="-1" aria-labelledby="taxGroup" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new tax group </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_taxGroup" action="{{ route('taxGroup.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">group id</label>
                            <input type="text" class="form-control" placeholder="group id" aria-label="group id" id="group_id" name="group_id">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">group description</label>
                            <input type="text" class="form-control" placeholder="group description" aria-label="group description" id="group_description" name="group_description">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_taxGroup">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_taxGroupModal" tabindex="-1" aria-labelledby="edit_taxGroupModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing taxGroup</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_taxGroup" action="{{ route('taxGroup.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_group_id" name="ed_group_id">
                        <div class="col-md-12">
                            <label class="form-label">group description</label>
                            <input type="text" class="form-control" placeholder="group description" aria-label="group description" id="ed_group_description" name="ed_group_description">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_taxGroup">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('taxGroup.delete') }}" method="post" id="del_taxGroup_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_group_id" id="del_group_id">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#taxGroup-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('taxGroup.data') }}",
            columns: [{
                    data: 'group_id',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'group_description',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_taxGroup', function() {
            var ed_group_id = $(this).closest('tr').find('td:eq(0)').text();
            var ed_group_description = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_group_id").val(ed_group_id);
            $("#ed_group_description").val(ed_group_description);
            $('#edit_taxGroupModal').modal('show');

        });

        // $('.dataTable').on('click', 'tbody td #activate_taxGroup', function() {
        //     var del_group_id = $(this).closest('tr').find('td:eq(1)').text();
        //     var status = $(this).closest('tr').find('td:eq(4)').find('#activate_taxGroup').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'Do you want to ' + status + ' the taxGroup',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_group_id").val(del_group_id);
        //             $('#del_taxGroup_form').submit();
        //         }
        //     });
        // });

        $("#store_taxGroup").validate({
            rules: {
                group_id: {
                    required: true,
                    maxlength: 20
                },
                group_description: {
                    required: true,
                    maxlength: 80
                },
            },
            messages: {
                group_id: {
                    required: "group id is required",
                    maxlength: "group id must be at most 3 characters"
                },
                group_description: {
                    required: "group description is required",
                    maxlength: "group description must be at most 100 characters"
                },
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