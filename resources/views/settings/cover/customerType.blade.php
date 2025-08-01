@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Customer Type</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#customerType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add Customer Type </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_customerType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add Customer Type</button> --}}

    <table class="table" id="customerType-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="customerType" tabindex="-1" aria-labelledby="customerType" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating New Customer Type </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_customerType" action="{{ route('customerType.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Customer Type Slug</label>
                            <input type="text" class="form-control" placeholder="Customer Type" aria-label="Customer Type Slug" id="code" name="code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Customer Type Name</label>
                            <input type="text" class="form-control" placeholder="Customer Type" aria-label="Customer Type" id="type_name" name="type_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_customerType">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_cust_typeModal" tabindex="-1" aria-labelledby="edit_cust_typeModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing Customer Type</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_customerType" action="{{ route('customerType.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Customer Type</label>
                            <input type="hidden" class="form-control" placeholder="Customer Type" aria-label="Customer Type" id="ed_type_id" name="ed_type_id">
                            <input type="hidden" class="form-control" placeholder="Customer Type Slug" aria-label="Customer Type Slug" id="ed_code" name="ed_code">
                            <input type="text" class="form-control" placeholder="Customer Type" aria-label="Customer Type" id="ed_type_name" name="ed_type_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_customerType">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('customerType.delete') }}" method="post" id="cust_type_delete">
    {{ csrf_field() }}
    <input type="hidden" name="del_type_id" id="del_type_id">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#customerType-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('customerType.data') }}",
            columns: [{
                    data: 'type_id',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'type_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_cust_type', function() {
            var type_id = $(this).closest('tr').find('td:eq(0)').text();
            var code = $(this).closest('tr').find('td:eq(1)').text();
            var type_name = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_type_id").val(type_id);
            $("#ed_code").val(code);
            $("#ed_type_name").val(type_name);
            $('#edit_cust_typeModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_cust_type', function() {
            var type_id = $(this).closest('tr').find('td:eq(0)').text();
            var status = $(this).closest('tr').find('td:eq(2)').find('#activate_cust_type').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the customer type',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_type_id").val(type_id);
                    $('#cust_type_delete').submit();
                }
            });
        });

        $("#store_customerType").validate({
            rules: {
                code: {
                    required: true,
                    maxlength: 10
                },
                type_name: {
                    required: true,
                    maxlength: 100
                },

            },
            messages: {
                code: {
                    required: "Customer Type Slug is required",
                    maxlength: "Customer Type Slug must be at most 10 characters"
                },
                type_name: {
                    required: "Customer Type is required",
                    maxlength: "Customer Type must be at most 100 characters"
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