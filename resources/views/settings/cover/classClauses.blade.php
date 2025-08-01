@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Clauses</a><span> ➤ Details</span>
    </nav>
    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#add_clause" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add clause </button>
    {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_class" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add class</button> --}}

    <table class="table" id="clause-table">
        <thead>
            <tr>
                <th>Class Code</th>
                <th>Class Name</th>
                <th>Clause ID</th>
                <th>Clause Title</th>
                <th>Clause Wordings</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}

<div class="modal fade" id="add_clause" tabindex="-1" aria-labelledby="add_clause" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new clause </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_clause" action="{{ route('clauseparam.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">class code</label>
                            <select name="class_code" id="class_code" class="form-control text-capitalize">
                                <option value="">--Select class--</option>
                                @foreach ($classes as $cls)
                                <option value="{{ $cls['class_code'] }}">{{ $cls['class_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">clause title</label>
                            <input type="text" class="form-control" placeholder="clause title" aria-label="clause title" id="clause_title" name="clause_title">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">clause Wording</label>
                            <textarea class="form-control" id="clause_wording" name="clause_wording" required></textarea>
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

<div class="modal fade" id="edit_clauseModal" tabindex="-1" aria-labelledby="edit_clauseModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing clause</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_clause" action="{{ route('clauseparam.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">class code</label>
                            <input type="hidden" class="form-control" placeholder="clause" aria-label="clause" id="ed_clause_id" name="ed_clause_id" required>
                            <select name="ed_class_code" id="ed_class_code" class="form-control text-capitalize">
                                <option value="">--Select class--</option>
                                @foreach ($classes as $cls)
                                <option value="{{ $cls['class_code'] }}">{{ $cls['class_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">clause title</label>
                            <input type="text" class="form-control" placeholder="clause title" aria-label="clause title" id="ed_clause_title" name="ed_clause_title" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">clause Wording</label>
                            <textarea class="form-control" id="ed_clause_wording" name="ed_clause_wording" required></textarea>
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

<form action="{{ route('clauseparam.delete') }}" method="post" id="del_clause_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_clause_id" id="del_clause_id">
</form>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#clause-table').DataTable({ // New initialization
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('clauseparam.data') }}",
            columns: [
                {
                    data: 'class_code',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'class_name',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'clause_id',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'clause_title',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'clause_wording',
                    defaultContent: "<b class='dashes' style=''>_</b>"
                },
                {
                    data: 'action',
                    defaultContent: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_clause', function() {
            var class_code = $(this).closest('tr').find('td:eq(0)').text();
            var clause_id = $(this).closest('tr').find('td:eq(2)').text();
            var clause_title = $(this).closest('tr').find('td:eq(3)').text();
            var clause_wording = $(this).closest('tr').find('td:eq(4)').text();
            $("#ed_class_code").val(class_code);
            $("#ed_clause_id").val(clause_id);
            $("#ed_clause_title").val(clause_title);
            $("#ed_clause_wording").val(clause_wording);
            $('#edit_clauseModal').modal('show');

        });

        $('.dataTable').on('click', 'tbody td #activate_clause', function() {
            var clause_id = $(this).closest('tr').find('td:eq(2)').text();
            var status = $(this).closest('tr').find('td:eq(5)').find('#activate_clause').val();
            swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + status + ' the clause',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#del_clause_id").val(clause_id);
                    $('#del_clause_form').submit();
                }
            });
        });

        $("#store_clause").validate({
            rules: {
                class_code: {
                    required: true,
                    maxlength: 10
                },
                clause_title: {
                    required: true,
                    maxlength: 300
                },
                clause_wording: {
                    required: true,
                }
            },
            messages: {
                class_code: {
                    required: "class code is required",
                    maxlength: "class code must be at most 10 characters"
                },
                clause_title: {
                    required: "clause title is required",
                    maxlength: "clause title must be at most 300 characters"
                },
                clause_wording: {
                    required: "clause wording is required",
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