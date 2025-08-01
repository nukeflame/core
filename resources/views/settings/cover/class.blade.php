@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Class</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#add_class" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add class </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_class" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add class</button> --}}

    <table class="table" id="class-table">
        <thead>
            <tr>
                <th>Class Code</th>
                <th>Class Name</th>
                <th>Combined</th>
                <th>Class Group Code</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="add_class" tabindex="-1" aria-labelledby="add_class" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new class </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_class" action="{{ route('class.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">class group code</label>
                            <select name="class_group_code" id="class_group_code" class="form-control text-capitalize">
                                <option value="">--Select class group--</option>
                                @foreach ($clGrps as $clgr)
                                <option value="{{ $clgr['group_code'] }}">{{ $clgr['group_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">class Code</label>
                            <input type="text" class="form-control" placeholder="class Code" aria-label="class Code" id="class_code" name="class_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">class Name</label>
                            <input type="text" class="form-control" placeholder="class Name" aria-label="class Name" id="class_name" name="class_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_class">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- //End of modal --}}

<div class="modal fade" id="edit_classModal" tabindex="-1" aria-labelledby="edit_classModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing class</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_class" action="{{ route('class.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">class group code</label>
                            <input type="hidden" class="form-control" placeholder="class" aria-label="class" id="ed_class_code" name="ed_class_code" required>
                            <select name="ed_class_group_code" id="ed_class_group_code" class="form-control text-capitalize">
                                <option value="">--Select class group--</option>
                                @foreach ($clGrps as $clgr)
                                <option value="{{ $clgr['group_code'] }}">{{ $clgr['group_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">class Name</label>
                            <input type="text" class="form-control" placeholder="class Name" aria-label="class Name" id="ed_class_name" name="ed_class_name" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">combined</label>
                            <select name="ed_combined" id="ed_combined" class="form-control text-capitalize" required>
                                <option value="">--Select class group--</option>
                                <option value="N">NO</option>
                                <option value="Y">YES</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_class">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('class.delete') }}" method="post" id="del_class_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_class_code" id="del_class_code">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#class-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('class.data') }}",
            columns: [{
                    data: 'class_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'class_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'combined',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'class_group_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_class', function() {
            var class_code = $(this).closest('tr').find('td:eq(0)').text();
            var class_name = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_class_code").val(class_code);
            $("#ed_class_name").val(class_name);
            $('#edit_classModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_class', function() {
            var group_code = $(this).closest('tr').find('td:eq(0)').text();
            var status = $(this).closest('tr').find('td:eq(4)').find('#activate_class').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the class',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_class_code").val(group_code);
                    $('#del_class_form').submit();
                }
            });
        });

        $("#store_class").validate({
            rules: {
                group_code: {
                    required: true,
                    maxlength: 5
                },
                group_name: {
                    required: true,
                    maxlength: 100
                }
            },
            messages: {
                group_code: {
                    required: "class ISO is required",
                    maxlength: "class ISO must be at most 5 characters"
                },
                group_name: {
                    required: "class name is required",
                    maxlength: "class name must be at most 100 characters"
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