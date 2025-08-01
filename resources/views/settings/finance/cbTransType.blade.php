@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Settings </a><span> ➤ CB Trans Type</span>
    </nav>

    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#cbTransType" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add CB Trans Type </button>

    <table class="table" id="cbTransType-table">
        <thead class="text-uppercase text-nowrap">
            <tr>
                <th>type_code</th>
                <th>doc_type</th>
                <th>source_code</th>
                <th>description</th>
                <th>debit_account</th>
                <th>credit_account</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}
<div class="modal fade" id="cbTransType" tabindex="-1" aria-labelledby="cbTransType" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new CB Trans Types </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_cbTransType" action="{{ route('cbTransType.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">DOC TYPE</label>
                            <input type="text" class="form-control" placeholder="DOC TYPE" aria-label="DOC TYPE" id="doc_type" name="doc_type" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">TYPE CODE</label>
                            <input type="text" class="form-control" placeholder="TYPE CODE" aria-label="TYPE CODE" id="type_code" name="type_code" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">SOURCE CODE</label>
                            <select id="source_code" name="source_code" class="form-control" required>
                                <option value="">--Select source code--</option>
                                @foreach ($cbSources as $cbSource)
                                <option value="{{ $cbSource->source_code }}">{{ $cbSource->source_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">description</label>
                            <input type="text" class="form-control" placeholder="description" aria-label="description" id="description" name="description" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">DEBIT ACCOUNT</label>
                            <input type="text" class="form-control" placeholder="DEBIT ACCOUNT" aria-label="DEBIT ACCOUNT" id="debit_account" name="debit_account">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CREDIT ACCOUNT</label>
                            <input type="text" class="form-control" placeholder="CREDIT ACCOUNT" aria-label="CREDIT ACCOUNT" id="credit_account" name="credit_account">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_cbTransType">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>
{{-- //End of modal --}}

<div class="modal fade" id="edit_cbTransTypeModal" tabindex="-1" aria-labelledby="edit_cbTransTypeModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing CB Trans Types</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_cbTransType" action="{{ route('cbTransType.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_type_code" name="ed_type_code" required>
                        <div class="col-md-12">
                            <label class="form-label">DOC TYPE</label>
                            <input type="text" class="form-control" placeholder="DOC TYPE" aria-label="DOC TYPE" id="ed_doc_type" name="ed_doc_type" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">SOURCE CODE</label>
                            <select id="ed_source_code" name="ed_source_code" class="form-control" required>
                                <option value="">--Select source code--</option>
                                @foreach ($cbSources as $cbSource)
                                <option value="{{ $cbSource->source_code }}">{{ $cbSource->source_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">description</label>
                            <input type="text" class="form-control" placeholder="description" aria-label="description" id="ed_description" name="ed_description" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">DEBIT ACCOUNT</label>
                            <input type="text" class="form-control" placeholder="DEBIT ACCOUNT" aria-label="DEBIT ACCOUNT" id="ed_debit_account" name="ed_debit_account">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CREDIT ACCOUNT</label>
                            <input type="text" class="form-control" placeholder="CREDIT ACCOUNT" aria-label="CREDIT ACCOUNT" id="ed_credit_account" name="ed_credit_account">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_cbTransType">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('cbTransType.delete') }}" method="post" id="del_cbTransType_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_id" id="del_id">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#cbTransType-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('cbTransType.data') }}",
            columns: [{
                    data: 'type_code'
                },
                {
                    data: 'doc_type'
                },
                {
                    data: 'source_code'
                },
                {
                    data: 'description'
                },
                {
                    data: 'debit_account'
                },
                {
                    data: 'credit_account'
                },
                {
                    data: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_cbTransType', function() {
            var ed_type_code = $(this).closest('tr').find('td:eq(0)').text();
            $("#ed_type_code").val(ed_type_code);
            var ed_doc_type = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_doc_type").val(ed_doc_type);
            var ed_source_code = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_source_code").val(ed_source_code);
            var ed_description = $(this).closest('tr').find('td:eq(3)').text();
            $("#ed_description").val(ed_description);
            var ed_debit_account = $(this).closest('tr').find('td:eq(4)').text();
            $("#ed_debit_account").val(ed_debit_account);
            var ed_credit_account = $(this).closest('tr').find('td:eq(5)').text();
            $("#ed_credit_account").val(ed_credit_account);
            $('#edit_cbTransTypeModal').modal('show');
        });

        // $('.dataTable').on('click', 'tbody td #activate_cbTransType', function() {
        //     var del_id = $(this).closest('tr').find('td:eq(0)').text();
        //     var status = $(this).closest('tr').find('td:eq(3)').find('#activate_cbTransType').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'You want to ' + status + ' the cbTransType',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_id").val(del_id);
        //             $('#del_cbTransType_form').submit();
        //         }
        //     });
        // });

        $("#store_cbTransType").validate({
            rules: {
                doc_type: {
                    required: true,
                    maxlength: 3
                },
                type_code: {
                    required: true,
                    maxlength: 3
                },
                source_code: {
                    required: true,
                    maxlength: 3
                },
                debit_account: {
                    // required: true,
                    maxlength: 8
                },
                credit_account: {
                    // required: true,
                    maxlength: 8
                },
            },
            messages: {
                doc_type: {
                    required: "doc_type is required",
                    maxlength: "doc_type must be at most 3 characters"
                },
                type_code: {
                    required: "type_code is required",
                    maxlength: "type_code must be at most 3 characters"
                },
                source_code: {
                    required: "source_code is required",
                    maxlength: "source_code must be at most 3 characters"
                },
                debit_account: {
                    // required: "debit_account is required",
                    maxlength: "debit_account must be at most 8 characters"
                },
                credit_account: {
                    // required: "credit_account is required",
                    maxlength: "credit_account must be at most 8 characters"
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