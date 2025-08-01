@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>treaty type</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#treatyType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add treatyType </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_treatyType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add treatyType</button> --}}

    <table class="table" id="treatyType-table">
        <thead>
            <tr>
                <th>TREATY OF BUS</th>
                <th>TREATY CODE</th>
                <th>TREATY NAME</th>
                <th>STATUS</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="treatyType" tabindex="-1" aria-labelledby="treatyType" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new treaty type </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_treatyType" action="{{ route('treatyType.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Type of Business</label>
                            <select class="form-inputs section" name="type_of_bus" id="type_of_bus" required>
                                    <option value="" >Choose Type Of Business</option>
                                    <option value="TPR" >Treaty Proportional</option>
                                    <option value="TNP" >Treaty Non Proportional</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">treaty type Code</label>
                            <input type="text" class="form-control" placeholder="treaty type Code" aria-label="treaty type Code" id="treaty_code" name="treaty_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">treaty type Name</label>
                            <input type="text" class="form-control" placeholder="treaty type Name" aria-label="treaty type Name" id="treaty_name" name="treaty_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_treatyType">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_treatyTypeModal" tabindex="-1" aria-labelledby="edit_treatyTypeModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing treatyType</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_treatyType" action="{{ route('treatyType.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_treaty_code" name="ed_treaty_code">
                        <div class="col-md-12">
                            <label class="form-label">treaty type Name</label>
                            <input type="text" class="form-control" placeholder="treaty type Name" aria-label="treaty type Name" id="ed_treaty_name" name="ed_treaty_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_treatyType">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('treatyType.delete') }}" method="post" id="del_treatyType_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_treaty_code" id="del_treaty_code">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#treatyType-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('treatyType.data') }}",
            columns: [
                {
                    data: 'type_of_bus',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'treaty_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'treaty_name',
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

        $('.dataTable').on('click', 'tbody td #edit_treatyType', function() {
            var ed_treaty_code = $(this).closest('tr').find('td:eq(1)').text();
            var ed_treaty_name = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_treaty_code").val(ed_treaty_code);
            $("#ed_treaty_name").val(ed_treaty_name);
            $('#edit_treatyTypeModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_treatyType', function() {
            var del_treaty_code = $(this).closest('tr').find('td:eq(1)').text();
            var status = $(this).closest('tr').find('td:eq(3)').text();
            var status_name='';
                if (status=='A') {
                    status_name='DeActivate'
                } else {
                    status_name='Activate'
                }
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status_name + ' the treatyType',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_treaty_code").val(del_treaty_code);
                    $('#del_treatyType_form').submit();
                }
            });
        });

        $("#store_treatyType").validate({
            rules: {
                treatyType_cov_no: {
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
                treatyType_cov_no: {
                    required: "treaty type Code is required",
                    maxlength: "treaty type Code must be at most 3 characters"
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