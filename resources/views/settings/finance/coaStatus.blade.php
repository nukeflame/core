@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>COA Status</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#coaStatus" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add coaStatus </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_coaStatus" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add coaStatus</button> --}}

    <table class="table" id="coaStatus-table">
        <thead class="text-uppercase">
            <tr>
                <th>status code</th>
                <th>status name</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="coaStatus" tabindex="-1" aria-labelledby="coaStatus" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new tax group </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_coaStatus" action="{{ route('coaStatus.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">status code</label>
                            <input type="text" class="form-control" placeholder="status code" aria-label="status code" id="status_code" name="status_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">status name</label>
                            <input type="text" class="form-control" placeholder="status name" aria-label="status name" id="status_name" name="status_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_coaStatus">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_coaStatusModal" tabindex="-1" aria-labelledby="edit_coaStatusModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing coaStatus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_coaStatus" action="{{ route('coaStatus.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_status_code" name="ed_status_code">
                        <div class="col-md-12">
                            <label class="form-label">status name</label>
                            <input type="text" class="form-control" placeholder="status name" aria-label="status name" id="ed_status_name" name="ed_status_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_coaStatus">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('coaStatus.delete') }}" method="post" id="del_coaStatus_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_status_code" id="del_status_code">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#coaStatus-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('coaStatus.data') }}",
            columns: [{
                    data: 'status_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'status_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_coaStatus', function() {
            var ed_status_code = $(this).closest('tr').find('td:eq(0)').text();
            var ed_status_name = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_status_code").val(ed_status_code);
            $("#ed_status_name").val(ed_status_name);
            $('#edit_coaStatusModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_coaStatus', function() {
            var del_status_code = $(this).closest('tr').find('td:eq(0)').text();
            var status = $(this).closest('tr').find('td:eq(3)').find('#activate_coaStatus').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'You want to ' + status + ' the coaStatus',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_status_code").val(del_status_code);
                    $('#del_coaStatus_form').submit();
                }
            });
        });

        $("#store_coaStatus").validate({
            rules: {
                status_code: {
                    required: true,
                    maxlength: 20
                },
                status_name: {
                    required: true,
                    maxlength: 80
                },
            },
            messages: {
                status_code: {
                    required: "status code is required",
                    maxlength: "status code must be at most 3 characters"
                },
                status_name: {
                    required: "status name is required",
                    maxlength: "status name must be at most 100 characters"
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