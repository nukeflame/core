@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Business Types</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#businessType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add business type </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_businessType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add business type</button> --}}

    <table class="table" id="bus-type-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type Name</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="businessType" tabindex="-1" aria-labelledby="businessType" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new businessType </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_businessType" action="{{ route('businessType.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">business type ID</label>
                            <input type="text" class="form-control" placeholder="business type ID" aria-label="businessType ID" id="bus_type_id" name="bus_type_id">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">business type Name</label>
                            <input type="text" class="form-control" placeholder="business type Name" aria-label="businessType Name" id="bus_type_name" name="bus_type_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_businessType">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_businessTypeModal" tabindex="-1" aria-labelledby="edit_businessTypeModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing businessType</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_businessType" action="{{ route('businessType.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">business type name</label>
                            <input type="hidden" class="form-control" placeholder="business type id" aria-label="businessType" id="ed_bus_type_id" name="ed_bus_type_id">
                            <input type="text" class="form-control" placeholder="business type name" aria-label="businessType" id="ed_bus_type_name" name="ed_bus_type_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_businessType">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('businessType.delete') }}" method="post" id="del_businessType_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_bus_type_id" id="del_bus_type_id">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#bus-type-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('businessType.data') }}",
            columns: [{
                    data: 'bus_type_id',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'bus_type_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_businessType', function() {
            var bus_type_id = $(this).closest('tr').find('td:eq(0)').text();
            var bus_type_name = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_bus_type_id").val(bus_type_id);
            $("#ed_bus_type_name").val(bus_type_name);
            $('#edit_businessTypeModal').modal('show');

        });

        // $('.dataTable').on('click', 'tbody td #activate_businessType', function() {
        //     var bus_type_id = $(this).closest('tr').find('td:eq(0)').text();
        //     var status = $(this).closest('tr').find('td:eq(2)').find('#activate_businessType').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'Do you want to ' + status + ' the businessType',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_bus_type_id").val(bus_type_id);
        //             $('#del_bus_type_form').submit();
        //         }
        //     });
        // });

        $("#store_businessType").validate({
            rules: {
                bus_type_id: {
                    required: true,
                    maxlength: 3
                },
                bus_type_name: {
                    required: true,
                    maxlength: 100
                }
            },
            messages: {
                bus_type_id: {
                    required: "Business type ID is required",
                    maxlength: "Business type ID must be at most 3 characters"
                },
                bus_type_name: {
                    required: "Business type name is required",
                    maxlength: "Business type name must be at most 100 characters"
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