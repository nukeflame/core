@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Reins Class premtype</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#reinsClassPremtypes" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add Reins Class Premtype </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_reinsClassPremtypes" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add Reins Class Premtype</button> --}}

    <table class="table" id="reinsClassPremtypes-table">
        <thead class="text-capitalize">
            <tr>
                <th>rein class code</th>
                <th>rein class name</th>
                <th>premtype code</th>
                <th>premtype name</th>
                <th>status</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="reinsClassPremtypes" tabindex="-1" aria-labelledby="reinsClassPremtypes" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new reins Class premtype </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_reinsClassPremtypes" action="{{ route('reinsClassPremtypes.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label for="reinclass">Rein Class</label>
                            <select name="reinclass" id="reinclass" class="form-control">
                                <option value="">Select reinclass</option>
                                @foreach($reinclasses as $reinclass)
                                <option value="{{ $reinclass->class_code }}">{{ $reinclass->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Reins Class premtype Code</label>
                            <input type="text" class="form-control" placeholder="Reins Class premtype Code" aria-label="Reins Class premtype Code" id="premtype_code" name="premtype_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Reins Class premtype Name</label>
                            <input type="text" class="form-control" placeholder="Reins Class premtype Name" aria-label="Reins Class premtype Name" id="premtype_name" name="premtype_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_reinsClassPremtypes">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_reinsClassPremtypesModal" tabindex="-1" aria-labelledby="edit_reinsClassPremtypesModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing reinsClassPremtypes</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_reinsClassPremtypes" action="{{ route('reinsClassPremtypes.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_premtype_code" name="ed_premtype_code">
                        <input type="hidden" id="ed_reinclass" name="ed_reinclass">
                        <div class="col-md-12">
                            <label class="form-label">Reins Class premtype Name</label>
                            <input type="text" class="form-control" placeholder="Reins Class premtype Name" aria-label="Reins Class premtype Name" id="ed_premtype_name" name="ed_premtype_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_reinsClassPremtypes">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('reinsClassPremtypes.delete') }}" method="post" id="del_reinsClassPremtypes_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_reinclass" id="del_reinclass">
    <input type="hidden" name="del_premtype_code" id="del_premtype_code">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#reinsClassPremtypes-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('reinsClassPremtypes.data') }}",
            columns: [
                {
                    data: 'reinclass',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'reinclass_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'premtype_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'premtype_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'status',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_reinsClassPremtypes', function() {
            var ed_reinclass = $(this).closest('tr').find('td:eq(0)').text();
            var ed_premtype_code = $(this).closest('tr').find('td:eq(2)').text();
            var ed_premtype_name = $(this).closest('tr').find('td:eq(3)').text();
            $("#ed_reinclass").val(ed_reinclass);
            $("#ed_premtype_code").val(ed_premtype_code);
            $("#ed_premtype_name").val(ed_premtype_name);
            $('#edit_reinsClassPremtypesModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_reinsClassPremtypes', function() {
            var del_reinclass = $(this).closest('tr').find('td:eq(0)').text();
            var del_reinclass_name = $(this).closest('tr').find('td:eq(1)').text();
            var del_premtype_code = $(this).closest('tr').find('td:eq(2)').text();
            var del_premtype_name = $(this).closest('tr').find('td:eq(3)').text();
            var del_status = $(this).closest('tr').find('td:eq(4)').text();
            var status = '';
            if (del_status == 'A') {
                status = 'DeActivate';
            } else {
                status = 'Activate';
            }
            // var status = $(this).closest('tr').find('td:eq(4)').find('#activate_reinsClassPremtypes').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the '+del_reinclass_name+'-'+del_premtype_name,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_reinclass").val(del_reinclass);
                    $("#del_premtype_code").val(del_premtype_code);
                    $('#del_reinsClassPremtypes_form').submit();
                }
            });
        });

        $("#store_reinsClassPremtypes").validate({
            rules: {
                reinclass: {
                    required: true
                },
                premtype_code: {
                    required: true
                },
                premtype_name: {
                    required: true
                }
            },
            messages: {
                reinclass: {
                    required: "Reins Class is required"
                },
                premtype_code: {
                    required: "Premtype code is required"
                },
                premtype_name: {
                    required: "Premtype name is required"
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