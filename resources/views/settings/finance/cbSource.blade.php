@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Settings </a><span> ➤ CB Source</span>
    </nav>

    <button type="button" class="btn btn-primary btn-sm custom-btn" data-bs-toggle="modal" data-bs-target="#cbSource" style="background-color: #231747;  color: white;"><i class='bx bx-plus'></i> Add CB source </button>

    <table class="table" id="cbSource-table">
        <thead class="text-uppercase text-nowrap">
            <tr>
                <th>SOURCE CODE</th>
                <th>SOURCE NAME</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- //Start of modal --}}
<div class="modal fade" id="cbSource" tabindex="-1" aria-labelledby="cbSource" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Creating new CB sources </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="store_cbSource" action="{{ route('cbSource.store') }}" method="post">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <label class="form-label">SOURCE CODE</label>
                            <input type="text" class="form-control" placeholder="SOURCE CODE" aria-label="SOURCE CODE" id="source_code" name="source_code">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">SOURCE NAME</label>
                            <input type="text" class="form-control" placeholder="SOURCE NAME" aria-label="SOURCE NAME" id="source_name" name="source_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add_cbSource">Save Changes</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>
{{-- //End of modal --}}

<div class="modal fade" id="edit_cbSourceModal" tabindex="-1" aria-labelledby="edit_cbSourceModal" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel1">Editing CB sources</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_cbSource" action="{{ route('cbSource.edit') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row gy-4">
                        <input type="hidden" id="ed_source_code" name="ed_source_code">
                        <div class="col-md-12">
                            <label class="form-label">SOURCE NAME</label>
                            <input type="text" class="form-control" placeholder="SOURCE NAME" aria-label="SOURCE NAME" id="ed_source_name" name="ed_source_name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_cbSource">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('cbSource.delete') }}" method="post" id="del_cbSource_form">
    {{ csrf_field() }}
    <input type="hidden" name="del_id" id="del_id">
</form> -->

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#cbSource-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('cbSource.data') }}",
            columns: [{
                    data: 'source_code'
                },
                {
                    data: 'source_name'
                },
                {
                    data: 'action'
                },
            ]
        });

        $('.dataTable').on('click', 'tbody td #edit_cbSource', function() {
            var ed_source_code = $(this).closest('tr').find('td:eq(0)').text();
            $("#ed_source_code").val(ed_source_code);
            var ed_source_name = $(this).closest('tr').find('td:eq(1)').text();
            $("#ed_source_name").val(ed_source_name);
            $('#edit_cbSourceModal').modal('show');
        });

        // $('.dataTable').on('click', 'tbody td #activate_cbSource', function() {
        //     var del_id = $(this).closest('tr').find('td:eq(0)').text();
        //     var status = $(this).closest('tr').find('td:eq(3)').find('#activate_cbSource').val();
        //     swal.fire({
        //         title: 'Are you sure?',
        //         text: 'You want to ' + status + ' the cbSource',
        //         type: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Yes',
        //         cancelButtonText: 'Cancel'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $("#del_id").val(del_id);
        //             $('#del_cbSource_form').submit();
        //         }
        //     });
        // });

        $("#store_cbSource").validate({
            rules: {
                source_code: {
                    required: true,
                    maxlength: 3
                },
                source_name: {
                    required: true
                },
            },
            messages: {
                source_code: {
                    required: "Source Code is required",
                    maxlength: "Source Code must be at most 3 characters"
                },
                source_name: {
                    required: "source name is required"
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