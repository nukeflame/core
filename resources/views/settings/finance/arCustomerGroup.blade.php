@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Settings </a><span> ➤ Customer Groups</span>
    </nav>

    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#arCustomerGroup" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add Customer Group </button>

    <table class="table" id="arCustomerGroup-table">
        <thead class="text-uppercase text-nowrap">
            <tr>
                <th>GROUP ID</th>
                <th>GROUP TITLE</th>
                <th>DEFAULT CURRENCY</th>
                <th>CONTROL ACCOUNT</th>
                <th>TAX GROUP</th>
                <th>TAX GROUP DESCRIPTION</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}
<div class="modal fade" id="arCustomerGroup" tabindex="-1" aria-labelledby="arCustomerGroup" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new Customer Groups </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_arCustomerGroup" action="{{ route('arCustomerGroup.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Group Code</label>
                            <input type="text" class="form-control" placeholder="Group Code" aria-label="Group Code" id="group_id" name="group_id">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Group Title</label>
                            <input type="text" class="form-control" placeholder="Group Title" aria-label="Group Title" id="group_title" name="group_title">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Group Description</label>
                            <input type="text" class="form-control" placeholder="Group Description" aria-label="Group Description" id="group_description" name="group_description">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Default Currency</label>
                            <input type="text" class="form-control" placeholder="Default Currency" aria-label="Default Currency" id="default_currency" name="default_currency">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Control Account</label>
                            <input type="text" class="form-control" placeholder="Control Account" aria-label="Control Account" id="control_account" name="control_account">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tax Category</label>
                            <select id="tax_category" name="tax_category" class="form-control">
                                <option value="">--Select tax category--</option>
                                @foreach ($taxGroup as $txgrp)
                                <option value="{{ $txgrp->group_id }}">{{ $txgrp->group_description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_arCustomerGroup">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>
{{-- //End of modal --}}

<div class="modal fade" id="edit_arCustomerGroupModal" tabindex="-1" aria-labelledby="edit_arCustomerGroupModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing Customer Groups</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_arCustomerGroup" action="{{ route('arCustomerGroup.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_group_id" name="ed_group_id">
                        <div class="col-md-12">
                            <label class="form-label">Group Title</label>
                            <input type="text" class="form-control" placeholder="Group Title" aria-label="Group Title" id="ed_group_title" name="ed_group_title">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Group Description</label>
                            <input type="text" class="form-control" placeholder="Group Description" aria-label="Group Description" id="ed_group_description" name="ed_group_description">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Default Currency</label>
                            <input type="text" class="form-control" placeholder="Default Currency" aria-label="Default Currency" id="ed_default_currency" name="ed_default_currency">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Control Account</label>
                            <input type="text" class="form-control" placeholder="Control Account" aria-label="Control Account" id="ed_control_account" name="ed_control_account">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tax Category</label>
                            <select id="ed_tax_category" name="ed_tax_category" class="form-control">
                                <option value="">--Select tax category--</option>
                                @foreach ($taxGroup as $txgrp)
                                <option value="{{ $txgrp->group_id }}">{{ $txgrp->group_description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_arCustomerGroup">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('arCustomerGroup.delete') }}" method="post" id="del_arCustomerGroup_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_id" id="del_id">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#arCustomerGroup-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('arCustomerGroup.data') }}",
            columns: [{
                    data: 'group_id'
                },
                {
                    data: 'group_title'
                },
                {
                    data: 'group_description'
                },
                {
                    data: 'default_currency'
                },
                {
                    data: 'control_account'
                },
                {
                    data: 'tax_category'
                },
                {
                    data: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_arCustomerGroup', function() {
            var ed_group_id = $(this).closest('tr').find('td:eq(0)').text();
            $("#ed_group_id").val(ed_group_id);
            var ed_group_title = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_group_title").val(ed_group_title);
            var ed_group_description = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_group_description").val(ed_group_description);
            var ed_default_currency = $(this).closest('tr').find('td:eq(3)').text();
            $("#ed_default_currency").val(ed_default_currency);
            var ed_control_account = $(this).closest('tr').find('td:eq(4)').text();
            $("#ed_control_account").val(ed_control_account);
            var ed_tax_category = $(this).closest('tr').find('td:eq(5)').text();
            $("#ed_tax_category").val(ed_tax_category);
            $('#edit_arCustomerGroupModal').modal('show');
        });

        // $('.dataTable').on('click', 'tbody td #activate_arCustomerGroup', function() {
        //     var del_id = $(this).closest('tr').find('td:eq(0)').text();
        //     var status = $(this).closest('tr').find('td:eq(3)').find('#activate_arCustomerGroup').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'You want to ' + status + ' the arCustomerGroup',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_id").val(del_id);
        //             $('#del_arCustomerGroup_form').submit();
        //         }
        //     });
        // });

        $("#store_arCustomerGroup").validate({
            rules: {
                group_id: {
                    required: true
                },
                group_title: {
                    required: true
                },
                group_description: {
                    required: true
                },
                default_currency: {
                    required: true
                },
                control_account: {
                    required: true
                },
                tax_category: {
                    required: true
                },
            },
            messages: {
                group_id: {
                    required: "group id is required"
                },
                group_title: {
                    required: "group title is required"
                },
                group_description: {
                    required: "group description is required"
                },
                default_currency: {
                    required: "default currency is required"
                },
                control_account: {
                    required: "control account is required"
                },
                tax_category: {
                    required: "tax category is required"
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