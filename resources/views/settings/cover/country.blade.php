@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>country</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#country" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add country </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_country" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add country</button> --}}

    <table class="table" id="country-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="country" tabindex="-1" aria-labelledby="country" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new country </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_country" action="{{ route('country.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">Country ISO</label>
                            <input type="text" class="form-control" placeholder="Country ISO" aria-label="Country ISO" id="country_iso" name="country_iso">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Country Name</label>
                            <input type="text" class="form-control" placeholder="Country Name" aria-label="Country Name" id="country_name" name="country_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_country">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_countryModal" tabindex="-1" aria-labelledby="edit_countryModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing country</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_country" action="{{ route('country.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">country</label>
                            <input type="hidden" class="form-control" placeholder="country" aria-label="country" id="ed_country_iso" name="ed_country_iso">
                            <input type="text" class="form-control" placeholder="country" aria-label="country" id="ed_country_name" name="ed_country_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_country">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('country.delete') }}" method="post" id="del_country_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_country_iso" id="del_country_iso">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#country-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('country.data') }}",
            columns: [{
                    data: 'country_iso',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'country_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_country', function() {
            var country_iso = $(this).closest('tr').find('td:eq(0)').text();
            var country_name = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_country_iso").val(country_iso);
            $("#ed_country_name").val(country_name);
            $('#edit_countryModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_country', function() {
            var country_iso = $(this).closest('tr').find('td:eq(0)').text();
            var status = $(this).closest('tr').find('td:eq(2)').find('#activate_country').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the country',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_country_iso").val(country_iso);
                    $('#del_country_form').submit();
                }
            });
        });

        $("#store_country").validate({
            rules: {
                country_iso: {
                    required: true,
                    maxlength: 5
                },
                country_name: {
                    required: true,
                    maxlength: 100
                }
            },
            messages: {
                country_iso: {
                    required: "country ISO is required",
                    maxlength: "country ISO must be at most 5 characters"
                },
                country_name: {
                    required: "country name is required",
                    maxlength: "country name must be at most 100 characters"
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