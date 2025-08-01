@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Payment Methods</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#payMethod" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add payMethod </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_payMethod" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add payMethod</button> --}}

    <table class="table" id="payMethod-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>PAY METHOD CODE</th>
                <th>PAY METHOD NAME</th>
                <th>PAY DESCRIPTION</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="payMethod" tabindex="-1" aria-labelledby="payMethod" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new payMethod </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_payMethod" action="{{ route('payMethod.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Pay Method Code</label>
                            <input type="text" class="form-control" placeholder="Pay Method Code" aria-label="Pay Method Code" id="pay_method_code" name="pay_method_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Pay Method Name</label>
                            <input type="text" class="form-control" placeholder="Pay Method Name" aria-label="Pay Method Name" id="pay_method_name" name="pay_method_name">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Short Description</label>
                            <input type="text" class="form-control" placeholder="Short Description" aria-label="Short Description" id="short_description" name="short_description">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_payMethod">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_payMethodModal" tabindex="-1" aria-labelledby="edit_payMethodModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing payMethod</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_payMethod" action="{{ route('payMethod.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_pay_method_code" name="ed_pay_method_code">
                        <div class="col-md-12">
                            <label class="form-label">Pay Method Name</label>
                            <input type="text" class="form-control" placeholder="Pay Method Name" aria-label="Pay Method Name" id="ed_pay_method_name" name="ed_pay_method_name">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Short Description</label>
                            <input type="text" class="form-control" placeholder="Short Description" aria-label="Short Description" id="ed_short_description" name="ed_short_description">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_payMethod">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('payMethod.delete') }}" method="post" id="del_payMethod_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_id" id="del_id">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#payMethod-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('payMethod.data') }}",
            columns: [{
                    data: 'id',
                },
                {
                    data: 'pay_method_code',
                },
                {
                    data: 'pay_method_name',
                },
                {
                    data: 'short_description',
                },
                {
                    data: 'action',
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_payMethod', function() {
            var pay_method_code = $(this).closest('tr').find('td:eq(0)').text();
            var pay_method_name = $(this).closest('tr').find('td:eq(1)').text();
            var short_description = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_pay_method_code").val(pay_method_code);
            $("#ed_pay_method_name").val(pay_method_name);
            $("#ed_short_description").val(short_description);
            $('#edit_payMethodModal').modal('show');

        });

        // $('.dataTable').on('click', 'tbody td #activate_payMethod', function() {
        //     var id = $(this).closest('tr').find('td:eq(0)').text();
        //     var status = $(this).closest('tr').find('td:eq(2)').find('#activate_payMethod').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'Do you want to ' + status + ' the payMethod',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_id").val(id);
        //             $('#del_bus_type_form').submit();
        //         }
        //     });
        // });

        $("#store_payMethod").validate({
            rules: {
                pay_method_code: {
                    required: true,
                    maxlength: 20
                },
                pay_method_name: {
                    required: true,
                    maxlength: 80
                },
                short_description: {
                    required: true,
                    maxlength: 5
                }
            },
            messages: {
                pay_method_code: {
                    required: "pay method code is required",
                    maxlength: "pay method code must be at most 20 characters"
                },
                pay_method_name: {
                    required: "pay method name is required",
                    maxlength: "pay method name must be at most 80 characters"
                },
                short_description: {
                    required: "pay method short description is required",
                    maxlength: "pay method short description must be at most 5 characters"
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