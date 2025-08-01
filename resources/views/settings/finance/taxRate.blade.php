@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>tax rates</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#taxRate" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add taxRate </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_taxRate" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add taxRate</button> --}}

    <table class="table" id="taxRate-table">
        <thead class="text-uppercase">
            <tr>
                <th>id</th>
                <th>group id</th>
                <th>tax type</th>
                <th>tax code</th>
                <th>tax description</th>
                <th>tax rate</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="taxRate" tabindex="-1" aria-labelledby="taxRate" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new tax group </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_taxRate" action="{{ route('taxRate.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">group id</label>
                            <select name="group_id" id="group_id" class="form-control">
                                <option value="">--select tax group--</option>
                                @foreach ($taxGroup as $txgrp)
                                <option value="{{ $txgrp->group_id }}">{{ $txgrp->group_description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">tax type</label>
                            <input type="text" class="form-control" placeholder="tax type" aria-label="tax type" id="tax_type" name="tax_type">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">tax code</label>
                            <input type="text" class="form-control" placeholder="tax code" aria-label="tax code" id="tax_code" name="tax_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">tax description</label>
                            <input type="text" class="form-control" placeholder="tax description" aria-label="tax description" id="tax_description" name="tax_description">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">tax rate</label>
                            <input type="text" class="form-control" placeholder="tax rate" aria-label="tax rate" id="tax_rate" name="tax_rate">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_taxRate">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_taxRateModal" tabindex="-1" aria-labelledby="edit_taxRateModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing taxRate</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_taxRate" action="{{ route('taxRate.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_id" name="ed_id">
                        <div class="col-md-12">
                            <label class="form-label">group id</label>
                            <select name="ed_group_id" id="ed_group_id" class="form-control">
                                <option value="">--select tax group--</option>
                                @foreach ($taxGroup as $txgrp)
                                <option value="{{ $txgrp->group_id }}">{{ $txgrp->group_description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">tax type</label>
                            <input type="text" class="form-control" placeholder="tax type" aria-label="tax type" id="ed_tax_type" name="ed_tax_type">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">tax code</label>
                            <input type="text" class="form-control" placeholder="tax code" aria-label="tax code" id="ed_tax_code" name="ed_tax_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">tax description</label>
                            <input type="text" class="form-control" placeholder="tax description" aria-label="tax description" id="ed_tax_description" name="ed_tax_description">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">tax rate</label>
                            <input type="text" class="form-control" placeholder="tax rate" aria-label="tax rate" id="ed_tax_rate" name="ed_tax_rate">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_taxRate">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('taxRate.delete') }}" method="post" id="del_taxRate_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_group_id" id="del_group_id">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#taxRate-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('taxRate.data') }}",
            columns: [{
                    data: 'id',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'group_id',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'tax_type',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'tax_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'tax_description',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'tax_rate',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_taxRate', function() {
            var ed_id = $(this).closest('tr').find('td:eq(0)').text();
            var ed_group_id = $(this).closest('tr').find('td:eq(1)').text();
            var ed_tax_type = $(this).closest('tr').find('td:eq(2)').text();
            var ed_tax_code = $(this).closest('tr').find('td:eq(3)').text();
            var ed_tax_description = $(this).closest('tr').find('td:eq(4)').text();
            var ed_tax_rate = $(this).closest('tr').find('td:eq(5)').text();
            $("#ed_id").val(ed_id);
            $("#ed_group_id").val(ed_group_id);
            $("#ed_tax_type").val(ed_tax_type);
            $("#ed_tax_code").val(ed_tax_code);
            $("#ed_tax_description").val(ed_tax_description);
            $("#ed_tax_rate").val(ed_tax_rate);
            $('#edit_taxRateModal').modal('show');

        });

        // $('.dataTable').on('click', 'tbody td #activate_taxRate', function() {
        //     var del_group_id = $(this).closest('tr').find('td:eq(1)').text();
        //     var status = $(this).closest('tr').find('td:eq(4)').find('#activate_taxRate').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'Do you want to ' + status + ' the taxRate',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_group_id").val(del_group_id);
        //             $('#del_taxRate_form').submit();
        //         }
        //     });
        // });

        $("#store_taxRate").validate({
            rules: {
                group_id: {
                    required: true,
                    maxlength: 20
                },
                tax_type: {
                    required: true,
                    maxlength: 80
                },
            },
            messages: {
                group_id: {
                    required: "group id is required",
                    maxlength: "group id must be at most 3 characters"
                },
                tax_type: {
                    required: "tax type is required",
                    maxlength: "tax type must be at most 100 characters"
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