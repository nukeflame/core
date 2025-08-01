@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Sum Insurance Types</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#sumInsType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add sumInsType </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_sumInsType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add sumInsType</button> --}}

    <table class="table" id="sumInsType-table">
        <thead>
            <tr>
                <th>Insured Code</th>
                <th>Insured Name</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="sumInsType" tabindex="-1" aria-labelledby="sumInsType" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new sumInsType </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_sumInsType" action="{{ route('sumInsType.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Insured Code</label>
                            <input type="text" class="form-control" placeholder="Insured Code" aria-label="Insured Code" id="sum_insured_code" name="sum_insured_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Insured Name</label>
                            <input type="text" class="form-control" placeholder="Insured Name" aria-label="Insured Name" id="sum_insured_name" name="sum_insured_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_sumInsType">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_sumInsTypeModal" tabindex="-1" aria-labelledby="edit_sumInsTypeModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing sumInsType</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_sumInsType" action="{{ route('sumInsType.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" class="form-control" placeholder="Insured Code" aria-label="sumInsType" id="ed_sum_insured_code" name="ed_sum_insured_code">
                        <div class="col-md-12">
                            <label class="form-label">insured name</label>
                            <input type="text" class="form-control" placeholder="insured name" aria-label="agency" id="ed_sum_insured_name" name="ed_sum_insured_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_sumInsType">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('sumInsType.delete') }}" method="post" id="del_sumInsType_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_sum_insured_code" id="del_sum_insured_code">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#sumInsType-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('sumInsType.data') }}",
            columns: [{
                    data: 'sum_insured_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'sum_insured_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_sumInsType', function() {
            var sum_insured_code = $(this).closest('tr').find('td:eq(0)').text();
            var sum_insured_name = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_sum_insured_code").val(sum_insured_code);
            $("#ed_sum_insured_name").val(sum_insured_name);
            $('#edit_sumInsTypeModal').modal('show');

        });

        // $('.dataTable').on('click', 'tbody td #activate_sumInsType', function() {
        //     var sum_insured_code = $(this).closest('tr').find('td:eq(0)').text();
        //     var status = $(this).closest('tr').find('td:eq(2)').find('#activate_sumInsType').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'Do you want to ' + status + ' the sumInsType',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_sum_insured_code").val(sum_insured_code);
        //             $('#del_bus_type_form').submit();
        //         }
        //     });
        // });

        $("#store_sumInsType").validate({
            rules: {
                sum_insured_code: {
                    required: true,
                    maxlength: 20
                },
                sum_insured_name: {
                    required: true,
                    maxlength: 80
                },
                agency_name: {
                    required: true,
                    maxlength: 80
                }
            },
            messages: {
                sum_insured_code: {
                    required: "Insured Code is required",
                    maxlength: "Insured Code must be at most 3 characters"
                },
                sum_insured_name: {
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