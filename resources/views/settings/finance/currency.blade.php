@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Currency</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#currency" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add currency </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_currency" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i>  Add currency</button> --}}

    <table class="table" id="currency-table">
        <thead class="text-capitalize">
            <tr>
                <th>currency ISO</th>
                <th>currency Code</th>
                <th>currency Name</th>
                <th>base currency</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}
<div class="modal fade" id="currency" tabindex="-1" aria-labelledby="currency" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating New currency </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_currency" action="{{ route('currency.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">country code</label>
                            <select name="country_iso" id="country_iso" class="form-control text-capitalize">
                                <option value="">--Select country--</option>
                                @foreach ($countries as $country)
                                <option value="{{ $country['country_iso'] }}">{{ $country['country_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-capitalize">currency Code</label>
                            <input type="text" class="form-control" placeholder="currency Code" aria-label="currency Code" id="currency_code" name="currency_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-capitalize">currency name</label>
                            <input type="text" class="form-control" placeholder="currency" aria-label="currency" id="currency_name" name="currency_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="store_currency">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_currencyModal" tabindex="-1" aria-labelledby="edit_currencyModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing branch</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_currency" action="{{ route('currency.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label text-capitalize">currency name</label>
                            <input type="hidden" class="form-control" placeholder="branch" aria-label="branch" id="ed_currency_code" name="ed_currency_code">
                            <input type="text" class="form-control" placeholder="currency name" aria-label="currency name" id="ed_currency_name" name="ed_currency_name">
                        </div>
                        <div class="col-md-12">
                            <select name="ed_base_currency" id="" class="form-control">
                                <option value="">--Select base currency--</option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_currency">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('currency.delete') }}" method="post" id="del_currency_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_currency_code" id="del_currency_code">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {
        $('#currency-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('currency.data') }}",
            columns: [{
                    data: 'country_iso',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'currency_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'currency_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'base_currency',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                }
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_currency', function() {
            var currency_code = $(this).closest('tr').find('td:eq(1)').text();
            var currency_name = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_currency_code").val(currency_code);
            $("#ed_currency_name").val(currency_name);
            $('#edit_currencyModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_currency', function() {
            var currency_code = $(this).closest('tr').find('td:eq(1)').text();
            var status = $(this).closest('tr').find('td:eq(4)').find('#activate_currency').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the currency',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_currency_code").val(currency_code);
                    $('#del_currency_form').submit();
                }
            });
        });

        $("#store_currency").validate({
            rules: {
                currency_code: {
                    required: true,
                    minlength: 3,
                    maxlength: 3
                },
                currency_name: {
                    required: true,
                    maxlength: 10
                },

            },
            messages: {
                currency_code: {
                    required: "currency Code is required",
                    type: "MUST be an integer",
                    minlength: "currency Code must be at most 3 characters",
                    maxlength: "currency Code must be at most 3 characters"
                },
                currency_name: {
                    required: "currency Name is required",
                    minlength: "currency Name must be at most 10 characters"
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
    });
</script>
@endpush