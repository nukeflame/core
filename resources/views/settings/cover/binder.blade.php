@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Binders</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#binder" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add binder </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_binder" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add binder</button> --}}

    <table class="table" id="binder-table">
        <thead>
            <tr>
                <th>Binder No</th>
                <th>Insured Name</th>
                <th>agency name</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="binder" tabindex="-1" aria-labelledby="binder" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new binder </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_binder" action="{{ route('binder.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Binder Number</label>
                            <input type="text" class="form-control" placeholder="Binder Number" aria-label="Binder Number" id="binder_cov_no" name="binder_cov_no">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">binder Name</label>
                            <input type="text" class="form-control" placeholder="binder Name" aria-label="binder Name" id="insured_name" name="insured_name">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">agency name</label>
                            <input type="text" class="form-control" placeholder="agency name" aria-label="agency name" id="agency_name" name="agency_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_binder">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_binderModal" tabindex="-1" aria-labelledby="edit_binderModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing binder</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_binder" action="{{ route('binder.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">insured name</label>
                            <input type="hidden" class="form-control" placeholder="Binder Number" aria-label="binder" id="ed_binder_cov_no" name="ed_binder_cov_no">
                            <input type="text" class="form-control" placeholder="binder name" aria-label="binder" id="ed_insured_name" name="ed_insured_name">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">agency name</label>
                            <input type="text" class="form-control" placeholder="agency name" aria-label="agency" id="ed_agency_name" name="ed_agency_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_binder">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('binder.delete') }}" method="post" id="del_binder_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_binder_cov_no" id="del_binder_cov_no">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#binder-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('binder.data') }}",
            columns: [{
                    data: 'binder_cov_no',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'insured_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'agency_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_binder', function() {
            var binder_cov_no = $(this).closest('tr').find('td:eq(0)').text();
            var insured_name = $(this).closest('tr').find('td:eq(1)').text();
            var agency_name = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_binder_cov_no").val(binder_cov_no);
            $("#ed_insured_name").val(insured_name);
            $("#ed_agency_name").val(agency_name);
            $('#edit_binderModal').modal('show');

        });

        // $('.dataTable').on('click', 'tbody td #activate_binder', function() {
        //     var binder_cov_no = $(this).closest('tr').find('td:eq(0)').text();
        //     var status = $(this).closest('tr').find('td:eq(2)').find('#activate_binder').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'Do you want to ' + status + ' the binder',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_binder_cov_no").val(binder_cov_no);
        //             $('#del_bus_type_form').submit();
        //         }
        //     });
        // });

        $("#store_binder").validate({
            rules: {
                binder_cov_no: {
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
                binder_cov_no: {
                    required: "Binder Number is required",
                    maxlength: "Binder Number must be at most 3 characters"
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