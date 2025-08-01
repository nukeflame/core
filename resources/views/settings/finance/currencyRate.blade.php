@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Currency Rates</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#currencyRate" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add currencyRate </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_currencyRate" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add currencyRate</button> --}}

    <table class="table" id="currencyRate-table">
        <thead>
            <tr>
                <th>currency Code</th>
                <th>currency Date</th>
                <th>currency rate</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="currencyRate" tabindex="-1" aria-labelledby="currencyRate" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating New Currency Rate </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_currencyRate" action="{{ route('currencyRate.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">currency code</label>

                            <select name="currency_code" id="currency_code" class="form-control text-capitalize">
                                <option value="">--Select curreny code--</option>
                                @foreach ($currencies as $currency)
                                @if ($currency->status == 'A')
                                <option value="{{$currency->currency_code}}">{{$currency->currency_name}}</option>
                                @endif

                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-capitalize">currency date</label>
                            <input type="date" class="form-control" placeholder="currency date" aria-label="currency date" id="currency_date" name="currency_date">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-capitalize">currency rate</label>
                            <input type="text" class="form-control" placeholder="currency rate" aria-label="currency rate" id="currency_rate" name="currency_rate">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_currencyRate">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_currency_rateModal" tabindex="-1" aria-labelledby="edit_currency_rateModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing currencyRate</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_currency_rate" action="{{ route('currencyRate.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label text-capitalize">currency date</label>
                            <input type="hidden" class="form-control" placeholder="currencyRate" aria-label="currencyRate" id="ed_currency_code" name="ed_currency_code">
                            <input type="date" class="form-control" placeholder="currency date" aria-label="currency date" id="ed_currency_date" name="ed_currency_date">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-capitalize">currency rate</label>
                            <input type="text" class="form-control" placeholder="currency rate" aria-label="currency rate" id="ed_currency_rate" name="ed_currency_rate">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_currency_rate">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('currencyRate.delete') }}" method="post" id="del_currencyRate_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_currency_code" id="del_currency_code">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#currencyRate-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('currencyRate.data') }}",
            columns: [{
                    data: 'currency_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'currency_date',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'currency_rate',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_currency_rate', function() {
            var currency_code = $(this).closest('tr').find('td:eq(0)').text();
            var currency_date = $(this).closest('tr').find('td:eq(1)').text();
            var currency_rate = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_currency_code").val(currency_code);
            $("#ed_currency_date").val(currency_date);
            $("#ed_currency_rate").val(currency_rate);
            $('#edit_currency_rateModal').modal('show');

        });

        // $('.dataTable').on('click', 'tbody td #activate_currencyRate', function() {
        //     var currency_code = $(this).closest('tr').find('td:eq(0)').text();
        //     var status = $(this).closest('tr').find('td:eq(2)').find('#activate_currencyRate').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'Do you want to ' + status + ' the currencyRate',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_currency_code").val(currency_code);
        //             $('#del_currencyRate_form').submit();
        //         }
        //     });
        // });

        $("#store_currencyRate").validate({
            rules: {
                currency_code: {
                    required: true,
                    maxlength: 5
                },
                currency_rate: {
                    required: true,
                    maxlength: 100
                }
            },
            messages: {
                currency_code: {
                    required: "currencyRate ISO is required",
                    maxlength: "currencyRate ISO must be at most 5 characters"
                },
                currency_rate: {
                    required: "currencyRate name is required",
                    maxlength: "currencyRate name must be at most 100 characters"
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