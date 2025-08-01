@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Finance </a><span> ➤ Deductions / Addition account details</span>
    </nav>

    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#Bank" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add Deduction / Addition account </button>

    <div class="table-responsive">
        <table class="table" id="cbDeductions-table">
            <thead class="text-capitalize text-nowrap">
                <tr>
                    <th>doc type</th>
                    <th>deduction code</th>
                    <th>deduction name</th>
                    <th>percentage flag</th>
                    <th>percentage</th>
                    <th>default amount</th>
                    <th>percentage basis</th>
                    <th>add deduct</th>
                    <th>account no</th>
                    <th style="width: 20%">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

{{-- //Start of modal --}}
<div class="modal fade" id="Bank" tabindex="-1" aria-labelledby="Bank" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new CB Deduction </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_cbDeduction" action="{{ route('cbDeductions.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <label class="form-label">Doc Type</label>
                            <input type="text" class="form-control" placeholder="Doc Type" aria-label="Doc Type" id="doc_type" name="doc_type" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Deduction Code</label>
                            <input type="text" class="form-control" placeholder="Deduction Code" aria-label="Deduction Code" id="deduction_code" name="deduction_code" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Deduction Name</label>
                            <input type="text" class="form-control" placeholder="Deduction Name" aria-label="Deduction Name" id="deduction_name" name="deduction_name" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Percentage Flag</label>
                            <select name="percentage_flag" id="percentage_flag" class="form-control" required>
                                <option value="">-Select Percentage Flag--</option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Percentage</label>
                            <select name="percentage" id="percentage" class="form-control" required>
                                <option value="">-Select Percentage Basis--</option>
                                @for ($i=0; $i <= 100; $i++) <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dfault Amount</label>
                            <input type="text" class="form-control" placeholder="Dfault Amount" aria-label="Dfault Amount" id="default_amount" name="default_amount" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Percentage Basis</label>
                            <select name="percentage_basis" id="percentage_basis" class="form-control" required>
                                <option value="">-Select Percentage Basis--</option>
                                <option value="N">Net</option>
                                <option value="G">Gross</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Add Deduct</label>
                            <select name="add_deduct" id="add_deduct" class="form-control" required>
                                <option value="">-Select Deduct/Add--</option>
                                <option value="A">Add</option>
                                <option value="D">Deduct</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Account Number (Optional)</label>
                            <select name="account_no" id="account_no" class="form-control">
                                <option value="">-Select Account Number--</option>
                                @foreach($glaccounts as $glaccount)
                                <option value="{{ $glaccount->account_number }}">{{ $glaccount->account_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_banks">Save</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>
{{-- //End of modal --}}

<div class="modal fade" id="edit_cbDeductionsModal" tabindex="-1" aria-labelledby="edit_cbDeductionsModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing Banks</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_cbDeductions" action="{{ route('cbDeductions.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_deduction_code" name="ed_deduction_code">
                        <div class="col-md-6">
                            <label class="form-label">Doc Type</label>
                            <input type="text" class="form-control" placeholder="Doc Type" aria-label="Doc Type" id="ed_doc_type" name="ed_doc_type" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Deduction Name</label>
                            <input type="text" class="form-control" placeholder="Deduction Name" aria-label="Deduction Name" id="ed_deduction_name" name="ed_deduction_name" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Percentage Flag</label>
                            <select name="ed_percentage_flag" id="ed_percentage_flag" class="form-control" required>
                                <option value="">-Select Percentage Flag--</option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Percentage</label>
                            <select name="ed_percentage" id="ed_percentage" class="form-control" required>
                                <option value="">-Select Percentage Basis--</option>
                                @for ($i=0; $i <= 100; $i++) <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dfault Amount</label>
                            <input type="text" class="form-control" placeholder="Dfault Amount" aria-label="Dfault Amount" id="ed_default_amount" name="ed_default_amount" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Percentage Basis</label>
                            <select name="ed_percentage_basis" id="ed_percentage_basis" class="form-control" required>
                                <option value="">-Select Percentage Basis--</option>
                                <option value="N">Net</option>
                                <option value="G">Gross</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Add Deduct</label>
                            <select name="ed_add_deduct" id="ed_add_deduct" class="form-control" required>
                                <option value="">-Select Deduct/Add--</option>
                                <option value="A">Add</option>
                                <option value="D">Deduct</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Account Number (Optional)</label>
                            <select name="ed_account_no" id="ed_account_no" class="form-control">
                                <option value="">-Select Account Number--</option>
                                @foreach($glaccounts as $glaccount)
                                <option value="{{ $glaccount->account_number }}">{{ $glaccount->account_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_cbDeductions">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#cbDeductions-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('cbDeductions.data') }}",
            columns: [{
                    data: 'doc_type'
                },
                {
                    data: 'deduction_code'
                },
                {
                    data: 'deduction_name'
                },
                {
                    data: 'percentage_flag'
                },
                {
                    data: 'percentage'
                },
                {
                    data: 'default_amount'
                },
                {
                    data: 'percentage_basis'
                },
                {
                    data: 'add_deduct'
                },
                {
                    data: 'account_no'
                },
                {
                    data: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_cbDeductions', function() {
            // var tableColumns = ['doc_type', 'deduction_code', 'deduction_name', 'percentage_flag', 'percentage', 'default_amount', 'percentage_basis', 'account_no'];
            // $.each(tableColumns, function(key, val) {
            //     $('#ed_' + val).val($(this).closest('tr').find('td:eq(' + key + ')').text());
            // });
            var ed_doc_type = $(this).closest('tr').find('td:eq(0)').text();
            $("#ed_doc_type").val(ed_doc_type);
            var ed_deduction_code = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_deduction_code").val(ed_deduction_code);
            var ed_deduction_name = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_deduction_name").val(ed_deduction_name);
            var ed_percentage_flag = $(this).closest('tr').find('td:eq(3)').text();
            $("#ed_percentage_flag").val(ed_percentage_flag);
            var ed_percentage = $(this).closest('tr').find('td:eq(4)').text();
            $("#ed_percentage").val(ed_percentage);
            var ed_default_amount = $(this).closest('tr').find('td:eq(5)').text();
            $("#ed_default_amount").val(ed_default_amount);
            var ed_percentage_basis = $(this).closest('tr').find('td:eq(6)').text();
            $("#ed_percentage_basis").val(ed_percentage_basis);
            var ed_add_deduct = $(this).closest('tr').find('td:eq(7)').text();
            $("#ed_add_deduct").val(ed_add_deduct);
            var ed_account_no = $(this).closest('tr').find('td:eq(8)').text();
            $("#ed_account_no").val(ed_account_no);

            $('#edit_cbDeductionsModal').modal('show');
        });

        $("#store_cbDeduction").validate({
            rules: {},
            messages: {},
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