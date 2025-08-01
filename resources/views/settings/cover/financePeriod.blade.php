@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>finance period</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#financePeriod" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add financePeriod </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_financePeriod" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add financePeriod</button> --}}

    <table class="table" id="financePeriod-table">
        <thead class="text-uppercase">
            <tr>
                <th>account year</th>
                <th>account month</th>
                <th>last year</th>
                <th>last month</th>
                <th style="width: 20%">action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="financePeriod" tabindex="-1" aria-labelledby="financePeriod" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new treaty type </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_financePeriod" action="{{ route('financePeriod.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <label class="form-label">Account year</label>
                            <select class="form-control" id="account_year" name="account_year">
                                <option value="">--Select year--</option>
                                {{ $last= date('Y')-120 }}
                                {{ $now = date('Y') }}
                                @for ($i = $now; $i >= $last; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Account month</label>
                            <select class="form-control text-capitalize" id="account_month" name="account_month">
                                <option value="">--Select month--</option>
                                @foreach (["jan"=>1,"feb"=>2,"march"=>3,"april"=>4,"may"=>5,"june"=>6,"july"=>7,"aug"=>8,"sep"=>9,"oct"=>10,"nov"=>11,"dec"=>12] as $month => $value)
                                <option value="{{ $value }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">last year</label>
                            <select class="form-control" id="last_year" name="last_year">
                                <option value="">--Select year--</option>
                                {{ $last= date('Y')-120 }}
                                {{ $now = date('Y') }}
                                @for ($i = $now; $i >= $last; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">last month</label>
                            <select class="form-control text-capitalize" id="last_month" name="last_month">
                                <option value="">--Select month--</option>
                                @foreach (["jan"=>1,"feb"=>2,"march"=>3,"april"=>4,"may"=>5,"june"=>6,"july"=>7,"aug"=>8,"sep"=>9,"oct"=>10,"nov"=>11,"dec"=>12] as $month => $value)
                                <option value="{{ $value }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="form-label">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="form-label">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_financePeriod">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_financePeriodModal" tabindex="-1" aria-labelledby="edit_financePeriodModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing financePeriod</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_financePeriod" action="{{ route('financePeriod.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_treaty_code" name="ed_treaty_code">
                        <div class="col-md-12">
                            <label class="form-label">treaty type Name</label>
                            <input type="text" class="form-control" placeholder="treaty type Name" aria-label="treaty type Name" id="ed_treaty_name" name="ed_treaty_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_financePeriod">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('financePeriod.delete') }}" method="post" id="del_financePeriod_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_treaty_code" id="del_treaty_code">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#financePeriod-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('financePeriod.data') }}",
            columns: [{
                    data: 'account_year',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'account_month',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'last_year',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'last_month',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_financePeriod', function() {
            var ed_treaty_code = $(this).closest('tr').find('td:eq(1)').text();
            var ed_treaty_name = $(this).closest('tr').find('td:eq(2)').text();
            $("#ed_treaty_code").val(ed_treaty_code);
            $("#ed_treaty_name").val(ed_treaty_name);
            $('#edit_financePeriodModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_financePeriod', function() {
            var del_id = $(this).closest('tr').find('td:eq(0)').text();
            var status = $(this).closest('tr').find('td:eq(5)').find('#activate_financePeriod').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the financePeriod',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_id").val(del_id);
                    $('#del_financePeriod_form').submit();
                }
            });
        });

        $("#store_financePeriod").validate({
            rules: {
                financePeriod_cov_no: {
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
                financePeriod_cov_no: {
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