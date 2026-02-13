@extends('layouts.app')

@section('content')
    <div class="container">
        <nav class="breadcrumb pt-3">
            <a class="breadcrumb-item" href>tax types</a><span> ➤ Details</span>
        </nav>
        <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#taxType"
            style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add taxType </button>
        {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_taxType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add taxType</button> --}}

        <table class="table" id="taxType-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tax Type</th>
                    <th>Tax Desc</th>
                    <th>Control Acc</th>
                    <th>Trans Type</th>
                    <th>Basis</th>
                    <th>Tax Code</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    {{-- //Start of modal --}}

    <div class="modal fade" id="taxType" tabindex="-1" aria-labelledby="taxType" data-bs-keyboard="false"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel1">Creating new tax group </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="store_taxType" action="{{ route('taxType.store') }}" method="post">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <label class="form-label">Tax Type</label>
                                <input type="text" class="form-control" placeholder="Tax Type" aria-label="Tax Type"
                                    id="tax_type" name="tax_type">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tax Description</label>
                                <input type="text" class="form-control" placeholder="Tax Description"
                                    aria-label="Tax Description" id="type_description" name="type_description">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Control Account</label>
                                <input type="text" class="form-control" placeholder="Control Account"
                                    aria-label="Control Account" id="control_account" name="control_account">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TransType</label>
                                <input type="text" class="form-control" placeholder="TransType" aria-label="TransType"
                                    id="transtype" name="transtype">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Basis</label>
                                <input type="text" class="form-control" placeholder="Basis" aria-label="Basis"
                                    id="basis" name="basis">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tax Code</label>
                                <input type="text" class="form-control" placeholder="Tax Code" aria-label="Tax Code"
                                    id="tax_code" name="tax_code">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="add_taxType">Save Changes</button>
                            </div>
                        </div>
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- //End of modal --}}

    <div class="modal fade" id="edit_taxTypeModal" tabindex="-1" aria-labelledby="edit_taxTypeModal"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel1">Editing taxType</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_taxType" action="{{ route('taxType.edit') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <input type="hidden" id="ed_id" name="ed_id">
                            <div class="col-md-6">
                                <label class="form-label">Tax Type</label>
                                <input type="text" class="form-control" placeholder="Tax Type" aria-label="Tax Type"
                                    id="ed_tax_type" name="ed_tax_type">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tax Description</label>
                                <input type="text" class="form-control" placeholder="Tax Description"
                                    aria-label="Tax Description" id="ed_type_description" name="ed_type_description">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Control Account</label>
                                <input type="text" class="form-control" placeholder="Control Account"
                                    aria-label="Control Account" id="ed_control_account" name="ed_control_account">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TransType</label>
                                <input type="text" class="form-control" placeholder="TransType"
                                    aria-label="TransType" id="ed_transtype" name="ed_transtype">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Basis</label>
                                <input type="text" class="form-control" placeholder="Basis" aria-label="Basis"
                                    id="ed_basis" name="ed_basis">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tax Code</label>
                                <input type="text" class="form-control" placeholder="Tax Code" aria-label="Tax Code"
                                    id="ed_tax_code" name="ed_tax_code">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">add deduct option</label>
                                <select name="ed_add_deduct" id="ed_add_deduct" class="form-control">
                                    <option value="">--select add deduct opion--</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">analyse option</label>
                                <select name="ed_analyse" id="ed_analyse" class="form-control">
                                    <option value="">--select analysis opion--</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="edit_taxType">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- <form action="{{ route('taxType.delete') }}" method="post" id="del_taxType_form">
        {{ csrf_field() }}
        <input type="hidden" name="del_tax_type" id="del_tax_type">
    </form> -->
@endsection
@push('script')
    <script>
        $(document).ready(function() {

            $('#taxType-table').DataTable({ // New initialization
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: "{{ route('taxType.data') }}",
                columns: [{
                        data: 'id',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'tax_type',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'type_description',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'control_account',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'transtype',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'basis',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'tax_code',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'action',
                        defaultContent: 'action'
                    },
                ]
            });

            $('.dataTable').on('click', 'tbody td #edit_taxType', function() {
                var ed_id = $(this).closest('tr').find('td:eq(0)').text();
                $("#ed_id").val(ed_id);
                var ed_tax_type = $(this).closest('tr').find('td:eq(1)').text();
                $("#ed_tax_type").val(ed_tax_type);
                var ed_type_description = $(this).closest('tr').find('td:eq(2)').text();
                $("#ed_type_description").val(ed_type_description);
                var ed_control_account = $(this).closest('tr').find('td:eq(3)').text();
                $("#ed_control_account").val(ed_control_account);
                var ed_transtype = $(this).closest('tr').find('td:eq(4)').text();
                $("#ed_transtype").val(ed_transtype);
                var ed_basis = $(this).closest('tr').find('td:eq(5)').text();
                $("#ed_basis").val(ed_basis);
                var ed_tax_code = $(this).closest('tr').find('td:eq(6)').text();
                $("#ed_tax_code").val(ed_tax_code);
                $('#edit_taxTypeModal').modal('show');

            });

            // $('.dataTable').on('click', 'tbody td #activate_taxType', function() {
            //     var del_tax_type = $(this).closest('tr').find('td:eq(1)').text();
            //     var status = $(this).closest('tr').find('td:eq(4)').find('#activate_taxType').val();
            //     swal.fire({
            //         title: 'Are you sure?',
            //         text: 'Do you want to ' + status + ' the taxType',
            //         type: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#d33',
            //         cancelButtonColor: '#3085d6',
            //         confirmButtonText: 'Yes',
            //         cancelButtonText: 'Cancel'
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             $("#del_tax_type").val(del_tax_type);
            //             $('#del_taxType_form').submit();
            //         }
            //     });
            // });

            $("#store_taxType").validate({
                rules: {
                    tax_type: {
                        required: true,
                        maxlength: 20
                    },
                    group_description: {
                        required: true,
                        maxlength: 80
                    },
                },
                messages: {
                    tax_type: {
                        required: "Tax Type is required",
                        maxlength: "Tax Type must be at most 3 characters"
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
