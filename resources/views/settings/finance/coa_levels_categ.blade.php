@extends('layouts.app')

@section('content')
<div>
    <nav class="breadcrumb">
        <a class="breadcrumb-item" href>COA </a><span> ➤ Levels Categories</span>
    </nav>
</div>
<div class="container">
    @include('settings.finance.modals.new_coalevelcateg')
    @include('settings.finance.modals.edit_coalevelcateg')

    <button type="button" class="btn btn-primary btn-sm custom-btn" id="new_leveldtl_btn"><i class='bx bx-plus'></i>Add Level Category</button>
    <form>
        <div class="table-responsive">
            {{csrf_field()}}
            <table class="table table-black table-border data-table" id='levelcateg_table' width="100%">
                <thead>
                    <tr>
                        <th>CATEGORY ID</th>
                        <th>LEVEL</th>
                        <th>LEVEL</th>
                        <th>DESCRIPTION</th>
                        <th>PARENT</th>
                        <th>PARENT</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </form>
</div>



@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#levelcateg_table').dataTable({
            processing: true,
            bAutowidth: true,
            ajax: "{{ route('coa_levelsdtl_data') }}",
            columns: [{
                    data: 'level_categ_id'
                },
                {
                    data: 'level_id',
                    'visible': false
                },
                {
                    data: 'level_descr'
                },
                {
                    data: 'level_categ_name'
                },
                {
                    data: 'parent_id',
                    'visible': false
                },
                {
                    data: 'parent_descr'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#new_leveldtl_btn').on('click', function() {
            $('#new_coaleveldtl_modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#new_coaleveldtl_modal').modal('show');

        });

        //get parent
        $('#level_id').change(function() {
            let level = $(this).val();
            $.ajax({
                url: "{{ route('getcategparent') }}",
                data: {
                    "level_id": level
                },
                dataType: "json",
                type: "get",
                success: function(selectparent) {
                    if (selectparent.status || selectparent.status == 0) {
                        $("#parent_id").empty();
                        $("#parent_id").append('<option>' + selectparent.status + '</option>');
                    } else {
                        $("#parent_id").empty();
                        $('#parent_id').append(
                            $('<option></option>')
                            .text('Select Parent')
                            .val('')
                        );
                        $.each(selectparent, function(i, valpar) {
                            $('#parent_id').append(
                                $('<option></option>')
                                .text(valpar.level_categ_name)
                                .val(valpar.level_categ_id)
                            );
                        });
                    }
                }
            });
        });

        $('#post_levelcategory').click(function() {
            $('#newlevelcategory_form').submit();
        });

        //validate form and post
        $('#newlevelcategory_form').validate({
            rules: {
                level_id: {
                    required: true,
                },
                description: {
                    required: true,
                },
                parent_id: {
                    required: true
                },
            },

            messages: {
                level_id: {
                    required: "Please Select Level ID",
                },
                description: {
                    required: "Please Provide Description"
                },
                parent_id: {
                    required: "Please Select Parent"
                },
            },
            errorPlacement: function(error, element) {
                error.addClass("text-danger");
                error.insertAfter(element);
            },
            highlight: function(element) {
                $(element).addClass('error').removeClass('valid');
            },
            unhighlight: function(element) {
                $(element).removeClass('error').addClass('valid');
            },
            submitHandler: function(newlevelcategory_form) {
                $.ajax({
                    url: "{{ route('postnewlevelcategory') }}",
                    datatype: 'json',
                    data: $('#newlevelcategory_form').serialize(),
                    type: "post",
                    success: function(scode) {
                        location.reload();
                    }
                });
            }
        });



        //Edit category
        let categ_id = ""
        $('#levelcateg_table').on('click', '#edit_categ', function() {

            let currentRow = $(this).closest("tr");
            let data = $('#levelcateg_table').DataTable().row(currentRow).data();

            categ_id = data['level_categ_id']
            let level_id = data['level_id'];
            let descr = data['level_categ_name'];
            let parent = data['parent_id'];
            let parent_descr = data['parent_descr'];

            // alert('wewewe tuko 1');
            $('#old_category_id').val(categ_id);
            $('#edit_level_id').val(level_id);
            $('#edit_description').val(descr);
            $("#edit_parent_id").empty();
            $("#edit_parent_id").append(
                $('<option></option>')
                .text(parent_descr)
                .val(parent));

            // alert('wewewe tuko 2');

            $('#edit_coaleveldtl_modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#edit_coaleveldtl_modal').modal('show');

        });


        $('#update_levelcategory').on('click', function() {
            $('#newlevelcategory_form').submit();
        });

        //validate edit form and post changes
        $('#editlevelcategory_form').validate({
            rules: {
                edit_level_id: {
                    required: true,
                },
                edit_description: {
                    required: true,
                },
                edit_parent_id: {
                    required: true,
                },
            },

            messages: {
                edit_level_id: {
                    required: "Please Select Level ID",
                },
                edit_description: {
                    required: "Please Provide Description",
                },
                edit_parent_id: {
                    required: "Please Select Parent",
                },
            },

            submitHandler: function(editnlcategory_form) {
                $.ajax({
                    url: "{{ route('updatelevelcategory') }}",
                    datatype: 'json',
                    data: $('#editlevelcategory_form').serialize(),
                    type: "post",
                    success: function(edit) {
                        location.reload();
                    }
                });
            }
        });

        // <!-- END -->
    });
</script>
@endpush