@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Reins Division</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#reinsDivision" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add reinsDivision </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_reinsDivision" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add reinsDivision</button> --}}

    <table class="table" id="reinsDivision-table">
        <thead>
            <tr>
                <th>DIVISION CODE</th>
                <th>DIVISION NAME</th>
                <th>STATUS</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="reinsDivision" tabindex="-1" aria-labelledby="reinsDivision" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new reins division </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_reinsDivision" action="{{ route('reinsDivision.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Reins Division Code</label>
                            <input type="text" class="form-control" placeholder="Reins Division Code" aria-label="Reins Division Code" id="division_code" name="division_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Reins Division Name</label>
                            <input type="text" class="form-control" placeholder="Reins Division Name" aria-label="Reins Division Name" id="division_name" name="division_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_reinsDivision">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_reinsDivisionModal" tabindex="-1" aria-labelledby="edit_reinsDivisionModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing reinsDivision</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_reinsDivision" action="{{ route('reinsDivision.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_division_code" name="ed_division_code">
                        <div class="col-md-12">
                            <label class="form-label">Reins Division Name</label>
                            <input type="text" class="form-control" placeholder="Reins Division Name" aria-label="Reins Division Name" id="ed_division_name" name="ed_division_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_reinsDivision">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('reinsDivision.delete') }}" method="post" id="del_reinsDivision_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_division_code" id="del_division_code">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#reinsDivision-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('reinsDivision.data') }}",
            columns: [{
                    data: 'division_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'division_name',
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

        $('.dataTable').on('click', 'tbody td #edit_reinsDivision', function() {
            var ed_division_code = $(this).closest('tr').find('td:eq(1)').text();
            var ed_division_name = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_division_code").val(ed_division_code);
            $("#ed_division_name").val(ed_division_name);
            $('#edit_reinsDivisionModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_reinsDivision', function() {
            var del_division_code = $(this).closest('tr').find('td:eq(1)').text();
            var status = $(this).closest('tr').find('td:eq(4)').find('#activate_reinsDivision').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the reinsDivision',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_division_code").val(del_division_code);
                    $('#del_reinsDivision_form').submit();
                }
            });
        });

        $("#store_reinsDivision").validate({
            rules: {
                reinsDivision_cov_no: {
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
                reinsDivision_cov_no: {
                    required: "Reins Division Code is required",
                    maxlength: "Reins Division Code must be at most 3 characters"
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