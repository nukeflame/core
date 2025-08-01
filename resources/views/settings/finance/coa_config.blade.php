@extends('layouts.app')

@section('content')
<div>
    <nav class="breadcrumb">
        <a class="breadcrumb-item" href>COA </a><span> ➤ Config</span>
    </nav>
</div>
<div class="container">

    <div class="table-responsive">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs  pull-left">
                <li class="nav-item">
                    <a class="nav-link active" id="account_group-tab" data-toggle="tab" href="#account_group">Account Groups</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="account_section-tab" data-toggle="tab" href="#account_section">Account Sections</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="coa_listing-tab" data-toggle="tab" href="#coa_listing">Chart of Accounts</a>
                </li>
            </ul>
        </div>



        <div class="tab-content" style="margin-top:20px">

            <div id="account_group" class="tab-pane fade show active">
                <form method="post" action="" id="acc_grp_form">
                    {{csrf_field()}}
                    <input type="hidden" name="coa_segment_code" id="coa_segment_code">
                    <input type="hidden" name="coa_group_code" id="coa_group_code">
                    <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_grpaccount" data-bs-toggle="modal" data-bs-target="#accGrpModal"><i class='bx bx-plus'></i>New Account Group</button>
                    <button type="button" class="btn btn-primary btn-sm custom-btn" id="view_segments" data-bs-toggle="modal" data-bs-target="#accSegmentsModal">View Accounts Segments</button>
                    <table class="table table-black table-border data-table" id='accountsdatatable' style="width:100%">
                        <thead>
                            <tr>
                                <th> SEGMENT CODE</th>
                                <th> DESCRIPTION</th>
                                <th> GROUP CODE</th>
                                <th> Actions</th>
                            </tr>
                        </thead>
                    </table>
                </form>
            </div>

            <div id="account_section" class="tab-pane fade">
                <form method="post" action="" id="acc_sec_form">
                    {{csrf_field()}}
                    <input type="hidden" name="coa_segment_code" id="coa_segment_code">
                    <input type="hidden" name="coa_sec_code" id="coa_sec_code">
                    <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_secaccount" data-bs-toggle="modal"><i class='bx bx-plus'></i>New Account Section</button>
                    <table class="table table-black table-border data-table" id='account_sec_data' style="width:100%">
                        <thead>
                            <tr>
                                <th>SEGMENT CODE</th>
                                <th>ACCOUNT GROUP</th>
                                <th>GROUP DESCRIPTION</th>
                                <th>SECTION CODE</th>
                                <th>SECTION DESCRIPTION</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </form>
            </div>

            <div id="coa_listing" class="tab-pane fade">
                <form method="post" action="" id="chart_form">
                    {{csrf_field()}}
                    <input type="hidden" name="coa_segment_code" id="acc_segment_code">
                    <input type="hidden" name="coa_acc_number" id="coa_acc_number">
                    <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_accountcode" data-bs-toggle="modal"><i class='bx bx-plus'></i>New Account Code</button>
                    <table class="table table-black table-border data-table" id='coa_table' style="width:100%">
                        <thead>
                            <tr>
                                <th>SEGMENT CODE</th>
                                <th>ACCOUNT NUMBER</th>
                                <th>ACCOUNT DESCRIPTION</th>
                                <th>ACCOUNT GROUP</th>
                                <th>GROUP DESCRIPTION</th>
                                <th>ACCOUNT SECTION</th>
                                <th>SECTION DESCRIPTION</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </form>
            </div>

        </div>
    </div>
</div>

<!--Accounts Group Modal -->
<div class="modal fade" id="accGrpModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="accGrpForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Account Group Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">Group Code</label>
                            <input type="number" name="group_code" id="group_code" class="form-inputs" maxlength="3" minlength="3" min="1">
                        </div>
                        <div class="col-md-7">
                            <label for="">Group Name</label>
                            <input type="text" name="group_name" id="group_name" class="form-inputs">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="accgrp-save-btn" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Accounts Section Modal -->
<div class="modal fade" id="accSecModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="accSecForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Account Section Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7">
                            <label for="">Account Group</label>
                            <select class="form-inputs section" name="acc_grp_code" id="acc_grp_code" @required(true)>
                            </select>
                            <span class="text-danger">{{ $errors->first('acc_grp_code') }}</span>
                        </div>

                        <div class="col-md-3">
                            <label for="">Section Code</label>
                            <input type="number" name="section_code" id="section_code" class="form-inputs" maxlength="2" minlength="2" min="00">
                        </div>

                        <div class="col-md-10">
                            <label for="">Section Name</label>
                            <input type="text" name="section_name" id="section_name" class="form-inputs">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="accsection-save-btn" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Accounts Segments -->
<div class="modal fade" id="accSegmentsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="accGrpForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Account Segments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form>
                            {{csrf_field()}}
                            <table class="table table-black table-border data-table" id='segments_table'>
                                <thead>
                                    <tr>
                                        <th>SEGMENT CODE</th>
                                        <th>SEGMENT DESCRIPTION</th>
                                        <th>Position</th>
                                        <th>LENGTH</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Accounts Code Modal -->
<div class="modal fade" id="accCodeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 1000px;">
        <div class="modal-content">
            <form method="POST" action="" id="accCodeForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Account Code Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 parent">
                            <label>Account Section</label>
                            <select class="form-inputs chosen" name="parent" id="parent" @required(true)>
                            </select>
                        </div>
                    </div>
                    <div class="row " style="margin-top: 20px">

                        <input id="segment_id" name="prid" value="COD" type="hidden">
                        <input id="namelength" hidden type="">
                        <div class="col-md-3 ">
                            <label id="code_label"> Code</label>
                            <input class="form-inputs" name="account_code_field" id="code" type="text" @required(true)>
                        </div>

                        <div class="col-md-6 ">
                            <label id="code_name"> Name</label>
                            <input class="form-inputs" name="account_name" id="account_name" type="text" onkeyup="this.value=toUpperCase(this.value)" @required(true)>
                        </div>

                        <div class="col-md-3 ">
                            <label>Normal Balance</label>
                            <select class="form-inputs" name="normal_bal" id="normal_bal" @required(true)>
                                <option value="">Select</option>
                                <option value="D">Dr</option>
                                <option value="C">Cr</option>
                                <option value="E">Either Dr or Cr</option>
                            </select>
                        </div>

                    </div>
                    <div class="row">
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="required">Type</label>
                                <select class="form-inputs section" name="categ_type" id="categ_type" @required(true)>
                                </select>
                                <span class="text-danger">{{ $errors->first('categ_type') }}</span>
                            </div>
                            <div class="col-sm-3">
                                <label class="required">Group</label>
                                <select class="form-inputs section" name="categ_group" id="categ_group" @required(true)>
                                </select>
                                <span class="text-danger">{{ $errors->first('categ_group') }}</span>
                            </div>
                            <div class="col-sm-3">
                                <label class="required">Sub Group</label>
                                <select class="form-inputs section" name="categ_sub_group" id="categ_sub_group" @required(true)>
                                </select>
                                <span class="text-danger">{{ $errors->first('categ_sub_group') }}</span>
                            </div>
                            {{-- <div class="col-sm-3">
                                <label class="required">Category</label>
                                <select class="form-inputs section" name="categ_final" id="categ_final" @required(true)>
                                </select>
                                <span class="text-danger">{{ $errors->first('categ_final') }}</span>
                            </div> --}}

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 ">
                            <label>Bank Account</label>
                            <select class="form-inputs" name="bank_flag" id="bank_flag" @required(true)>
                                <option value="Y">Yes</option>
                                <option value="N" selected>No</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label>Current Balance </label>
                            <input class="form-inputs" id="current_bal" required name="current_bal" type="text" @required(true)>
                        </div>

                    </div>
                    <div class="row" style="margin-top: 20px;">

                        <input type="text" id="multicurrency" name="multicurrency" value="N" hidden>

                        <div class="col-md-4" id="operatingcurrencydiv">
                            <label>Operating Currency </label>
                            <input type="text" class="form-inputs" name="currency" id="currency" @required(true) @readonly(true)>
                        </div>
                        <div class="col-md-4">
                            <label>Account Status</label>
                            <select class="form-inputs chosen" name="account_status" id="account_status" @required(true)>
                                <option value="A"> Active </option>
                                <option value="D"> Dormant </option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="acccode-save-btn" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        // segments js
        $('#segments_table').dataTable({
            processing: true,
            bAutowidth: true,
            ajax: "{{ route('getcoasegments') }}",
            columns: [{
                    data: 'segment_code'
                },
                {
                    data: 'segment_name'
                },
                {
                    data: 'segment_position'
                },
                {
                    data: 'segment_length'
                },
            ]
        });

        //Account Group
        var accountsGrpTable = $('#accountsdatatable').dataTable({
            processing: true,
            bAutowidth: true,
            ajax: {
                'type': 'get',
                'url': "{{ route('accgrpsegments') }}",
            },

            columns: [{
                    data: 'segment_code'
                },
                {
                    data: 'description'
                },

                {
                    data: 'account_number'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });


    // accounts group form
    $("#accGrpForm").validate({
        errorClass: "errorClass",
        rules: {
            group_code: {
                required: true
            },
            group_name: {
                required: true
            },
        },
        submitHandler: function(form) {
            $('#accgrp-save-btn').prop('disabled', true).text('Saving...');

            // Get form data
            var formData = new FormData(form);

            // Make a fetch request
            fetch("{!! route('add_newaccgrp') !!}", {
                method: 'POST',
                // No need to set Content-Type, browser will set it including boundaries
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status == 200) {
                    toastr.success("Account Group Successfully saved");
                    form.reset();
                    location.reload();
                } else {
                    console.log('data' + data);
                    toastr.error("Failed to save details");
                }
                $('#accgrp-save-btn').prop('disabled', false).text('Save');
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error("An error occurred while saving details");
                $('#accgrp-save-btn').prop('disabled', false).text('Save');
            });
        }
    });

        $('#accgrp-save-btn').click(function() {
            $('#accGrpForm').submit()
        });

        //Account Section
        var accountsSecTable = $('#account_sec_data').dataTable({
            processing: true,
            bAutowidth: true,
            ajax: {
                'type': 'get',
                'url': "{{ route('accsecsegments') }}",
            },
            columns: [{
                    data: 'segment_code'
                },
                {
                    data: 'seg_1',
                    name: 'seg_1',
                    searchable: true
                },
                {
                    data: 'seg_desc_1',
                    name: 'seg_desc_1',
                    searchable: true
                },
                {
                    data: 'account_number'
                },
                {
                    data: 'description'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#accountsdatatable').on('click', '#add_chartpar', function() {
            //show select segment modal
            $('#selectsegmentmodal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#selectsegmentmodal').modal('show');
            $("#selectsegmentmodal").appendTo("body");
        });

        $('#add_secaccount').on('click', function() {
            $('#accSecModal').modal('show');

            $.ajax({
                url: "{{ route('accgrpsegmentsdtl') }}",
                type: "get",
                success: function(resp) {
                    var accgroups = $.parseJSON(resp);

                    $('#acc_grp_code').append($('<option>').text('-- Select Account Group --').attr('value', ''));
                    $.each(accgroups, function(i, value) {
                        $('#acc_grp_code').append($('<option>').text(value.account_number + "-" + value.description)
                            .attr('value', value.account_number)
                        );


                    });

                    $('.section').trigger("chosen:updated");
                },
                error: function(resp) {
                    console.error;
                }
            })
        });

        $('#accsection-save-btn').click(function() {
            $('#accSecForm').submit()
        });

        // accounts section form
        $("#accSecForm").validate({
            errorClass: "errorClass",
            rules: {
                acc_grp_code: {
                    required: true
                },
                section_code: {
                    required: true
                },
                section_name: {
                    required: true
                },
            },
            submitHandler: function(form) {

                $('#accsection-save-btn').prop('disabled', true).text('Saving...')

                // Get form data
                var formData = new FormData(form);

                // Make a fetch request
                fetch("{!! route('add_newaccsec') !!}", {
                        method: 'POST',
                        body: new URLSearchParams(formData),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status == 200) {
                            toastr.success("Account Section Successfully saved")

                            form.reset()

                            location.reload();
                        } else {
                            console.log('data' + data);
                            toastr.error("Failed to save details")
                        }
                        $('#accsection-save-btn').prop('disabled', false).text('Save')
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error("An error occurred while saving details");
                        $('#accsection-save-btn').prop('disabled', false).text('Save')
                    });
            }
        });

        //Account Code

        //CHART OF ACCOUNTS LISTING
        var accountsCodeTable = $('#coa_table').dataTable({
            processing: true,
            serverSide: true,
            bAutowidth: true,
            ajax: "{{ route('getchartofaccounts') }}",
            columns: [{
                    data: 'segment_code'
                },
                {
                    data: 'account_number'
                },
                {
                    data: 'description'
                },
                {
                    data: 'seg_1',
                    name: 'seg_1',
                    searchable: true
                },
                {
                    data: 'seg_desc_1',
                    name: 'seg_desc_1',
                    searchable: true
                },
                {
                    data: 'seg_2',
                    name: 'seg_2',
                    searchable: true
                },
                {
                    data: 'seg_desc_2',
                    name: 'seg_desc_2',
                    searchable: true
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#add_accountcode').on('click', function() {
            $('#accCodeModal').modal('show');

            $.ajax({
                url: "{{ route('accgrpsegmentsdtl') }}",
                type: "get",
                success: function(resp) {
                    var accgroups = $.parseJSON(resp);

                    $('#acc_grp_code1').append($('<option>').text('-- Select Account Group --').attr('value', ''));
                    $.each(accgroups, function(i, value) {
                        $('#acc_grp_code1').append($('<option>').text(value.account_number + "-" + value.description)
                            .attr('value', value.account_number)
                        );


                    });

                    $('.section').trigger("chosen:updated");
                },
                error: function(resp) {
                    console.error;
                }
            })
        });

        $('#acccode-save-btn').click(function() {
            $('#accCodeForm').submit()
        });

        // accounts code form
        $("#accCodeForm").validate({
            errorClass: "errorClass",
            rules: {
                acc_grp_code: {
                    required: true
                },
                section_code: {
                    required: true
                },
                section_name: {
                    required: true
                },
            },
            submitHandler: function(form) {

                $('#acccode-save-btn').prop('disabled', true).text('Saving...')

                // Get form data
                var formData = new FormData(form);

                // Make a fetch request
                fetch("{!! route('add_newacccode') !!}", {
                        method: 'POST',
                        body: new URLSearchParams(formData),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status == 200) {
                            toastr.success("Account Code Successfully saved")

                            form.reset()

                            location.reload();
                        } else {
                            console.log('data' + data);
                            toastr.error("Failed to save details")
                        }
                        $('#acccode-save-btn').prop('disabled', false).text('Save')
                    })
                    .catch(error => {

                        console.error('Error:', error);
                        toastr.error("An error occurred while saving details");
                        $('#acccode-save-btn').prop('disabled', false).text('Save')
                    });
            }
        });


        $('#accCodeModal').on('shown.bs.modal', function() {

            //CHECK FOR PARENTS
            var segment = $('#segment_id').val();

            $.ajax({
                url: "{{route('getsegmentparents')}}",
                data: {
                    'segment': segment
                },
                type: "get",
                success: function(resp) {
                    if (resp) {
                        $('#parent').empty();
                        $('#parent').append(
                            $('<option></option>')
                            .text("Select Parent")
                            .val('')
                        );
                        $.each(resp, function(i, valentry) {



                            $('#parent').append(
                                $('<option></option>')
                                .text(valentry.account_number + ' ' + valentry.description)
                                .val(valentry.account_number)
                            );
                        });

                        $('#parent').trigger('chosen:updated');
                        $('#parent').trigger('choosen:change');
                        $('#parent').trigger('change');

                    } else {
                        $('#parent').trigger('change');
                        $('.parent').hide();

                    }

                },
                error: function(error) {
                    console.log(error)
                }
            });
            //END CHECK FOR PARENT

            //start of category type
            $.ajax({
                url: "{{route('getparentcategories')}}",
                data: {
                    'parent_id': 0
                },
                type: "get",
                success: function(resp) {
                    if (resp) {
                        $('#categ_type').empty();
                        $('#categ_type').append(
                            $('<option></option>')
                            .text("Select Type")
                            .val('')
                        );
                        $.each(resp, function(i, valentry) {



                            $('#categ_type').append(
                                $('<option></option>')
                                .text(valentry.level_categ_name)
                                .val(valentry.level_categ_id)
                            );
                        });

                        $('#categ_type').trigger('chosen:updated');
                        $('#categ_type').trigger('choosen:change');
                        $('#categ_type').trigger('change');

                    } else {
                        $('#categ_type').trigger('change');
                        $('#categ_type').hide();

                    }

                },
                error: function(error) {
                    console.log(error)
                }
            });
            //end of category type

            //get account statuses
            $.ajax({
                url: "{{route('getaccountstatuses')}}",
                type: "get",
                success: function(status) {
                    $('#account_status').empty();

                    $('#account_status').append(
                        $('<option></option>')
                        .text("Select Status")
                        .val('')
                    );
                    $.each(status, function(i, valstatus) {
                        $('#account_status').append(
                            $('<option></option>')
                            .val(valstatus.status_code)
                            .text(valstatus.status_name)
                        );
                    });

                    // $('#account_status').chosen();
                    $('#account_status').trigger('chosen:updated');
                },
                error: function(error) {
                    console.log(error)
                }
            });
            //end get account status

            //get base currency
            $.ajax({
                url: "{{route('getbasecurrency')}}",
                type: "get",
                success: function(resp) {
                    $('#currency').val(resp.currency_code);
                    // });
                },
                error: function(error) {
                    console.log(error)
                }
            });
            //end get base currency

        });

        $("#categ_type").change(function() {
            var parent = $("select#categ_type option:selected").attr('value');
            $('#categ_group').empty();
            $('#categ_group').prop('disabled', false)
            $.ajax({
                url: "{{route('getparentcategories')}}",
                data: {
                    'parent_id': parent
                },
                type: "get",
                success: function(resp) {
                    if (resp) {
                        $('#categ_group').empty();
                        $('#categ_group').append(
                            $('<option></option>')
                            .text("Select Group")
                            .val('')
                        );
                        $.each(resp, function(i, valentry) {
                            $('#categ_group').append(
                                $('<option></option>')
                                .text(valentry.level_categ_name)
                                .val(valentry.level_categ_id)
                            );
                        });

                        $('#categ_group').trigger('chosen:updated');
                        $('#categ_group').trigger('choosen:change');
                        $('#categ_group').trigger('change');

                    } else {
                        $('#categ_group').trigger('change');
                        $('#categ_group').hide();
                    }
                },
                error: function(resp) {
                    console.error;
                }
            })
        });

        $("#categ_group").change(function() {
            var parent = $("select#categ_group option:selected").attr('value');
            $('#categ_sub_group').empty();
            $('#categ_sub_group').prop('disabled', false)
            $.ajax({
                url: "{{route('getparentcategories')}}",
                data: {
                    'parent_id': parent
                },
                type: "get",
                success: function(resp) {
                    if (resp) {
                        $('#categ_sub_group').empty();
                        $('#categ_sub_group').append(
                            $('<option></option>')
                            .text("Select Sub Group")
                            .val('')
                        );
                        $.each(resp, function(i, valentry) {
                            $('#categ_sub_group').append(
                                $('<option></option>')
                                .text(valentry.level_categ_name)
                                .val(valentry.level_categ_id)
                            );
                        });

                        $('#categ_sub_group').trigger('chosen:updated');
                        $('#categ_sub_group').trigger('choosen:change');
                        $('#categ_sub_group').trigger('change');

                    } else {
                        $('#categ_sub_group').trigger('change');
                        $('#categ_sub_group').hide();
                    }
                },
                error: function(resp) {
                    console.error;
                }
            })
        });

        // $("#categ_sub_group").change(function() {
        //     var parent = $("select#categ_sub_group option:selected").attr('value');
        //     $('#categ_final').empty();
        //     $('#categ_final').prop('disabled', false)
        //     $.ajax({
        //         url: "{{route('getparentcategories')}}",
        //         data: {
        //             'parent_id': parent
        //         },
        //         type: "get",
        //         success: function(resp) {
        //             if (resp) {
        //                 $('#categ_final').empty();
        //                 $('#categ_final').append(
        //                     $('<option></option>')
        //                     .text("Select Category")
        //                     .val('')
        //                 );
        //                 $.each(resp, function(i, valentry) {
        //                     $('#categ_final').append(
        //                         $('<option></option>')
        //                         .text(valentry.level_categ_name)
        //                         .val(valentry.level_categ_id)
        //                     );
        //                 });

        //                 $('#categ_final').trigger('chosen:updated');
        //                 $('#categ_final').trigger('choosen:change');
        //                 $('#categ_final').trigger('change');

        //             } else {
        //                 $('#categ_final').trigger('change');
        //                 $('#categ_final').hide();
        //             }
        //         },
        //         error: function(resp) {
        //             console.error;
        //         }
        //     })
        // });
        
        // <!-- END -->
    });
</script>
@endpush