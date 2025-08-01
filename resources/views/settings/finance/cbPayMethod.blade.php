@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Settings </a><span> ➤ CB Pay Method</span>
    </nav>

    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#cbPayMethod" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add CB Pay Method </button>

    <table class="table" id="cbPayMethod-table">
        <thead class="text-uppercase text-nowrap">
            <tr>
                <th>PAY METHOD CODE</th>
                <th>PAY METHOD NAME</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}
<div class="modal fade" id="cbPayMethod" tabindex="-1" aria-labelledby="cbPayMethod" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new CB Pay Methods </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_cbPayMethod" action="{{ route('cbPayMethod.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">PAY METHOD CODE</label>
                            <input type="text" class="form-control" placeholder="PAY METHOD CODE" aria-label="PAY METHOD CODE" id="pay_method_code" name="pay_method_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">PAY METHOD NAME</label>
                            <input type="text" class="form-control" placeholder="PAY METHOD NAME" aria-label="PAY METHOD NAME" id="pay_method_name" name="pay_method_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_cbPayMethod">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>
{{-- //End of modal --}}

<div class="modal fade" id="edit_cbPayMethodModal" tabindex="-1" aria-labelledby="edit_cbPayMethodModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing CB Pay Methods</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_cbPayMethod" action="{{ route('cbPayMethod.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_pay_method_code" name="ed_pay_method_code">
                        <div class="col-md-12">
                            <label class="form-label">PAY METHOD NAME</label>
                            <input type="text" class="form-control" placeholder="PAY METHOD NAME" aria-label="PAY METHOD NAME" id="ed_pay_method_name" name="ed_pay_method_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_cbPayMethod">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('cbPayMethod.delete') }}" method="post" id="del_cbPayMethod_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_id" id="del_id">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#cbPayMethod-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('cbPayMethod.data') }}",
            columns: [{
                    data: 'pay_method_code'
                },
                {
                    data: 'pay_method_name'
                },
                {
                    data: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_cbPayMethod', function() {
            var ed_pay_method_code = $(this).closest('tr').find('td:eq(0)').text();
            $("#ed_pay_method_code").val(ed_pay_method_code);
            var ed_pay_method_name = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_pay_method_name").val(ed_pay_method_name);
            $('#edit_cbPayMethodModal').modal('show');
        });

        // $('.dataTable').on('click', 'tbody td #activate_cbPayMethod', function() {
        //     var del_id = $(this).closest('tr').find('td:eq(0)').text();
        //     var status = $(this).closest('tr').find('td:eq(3)').find('#activate_cbPayMethod').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'You want to ' + status + ' the cbPayMethod',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_id").val(del_id);
        //             $('#del_cbPayMethod_form').submit();
        //         }
        //     });
        // });

        $("#store_cbPayMethod").validate({
            rules: {
                pay_method_code: {
                    required: true,
                    maxlength: 5
                },
                pay_method_name: {
                    required: true,
                    maxlength: 30
                },
            },
            messages: {
                pay_method_code: {
                    required: "PAY METHOD Code is required",
                    maxlength: "PAY METHOD Code must be at most 5 characters"
                },
                pay_method_name: {
                    required: "PAY METHOD NAME is required",
                    maxlength: "PAY METHOD Code must be at most 30 characters"
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