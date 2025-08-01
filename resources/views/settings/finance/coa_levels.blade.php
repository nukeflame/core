@extends('layouts.app')

@section('content')
    <div>
        <nav class="breadcrumb">
            <a class="breadcrumb-item" href>COA </a><span> ➤ Levels</span>
        </nav>
    </div>
    <div class="container">
        @include('settings.finance.modals.new_coalevel')
        {{-- <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_coa_level"><i class='bx bx-plus'></i>Add Level</button>  --}}
        {{-- {{ html()->form('POST', '/settings/finance/coalevel-amend')->id('form_coa_level_datatable')->open() }} --}}
        <input type="text" id="level_id" name="level_id" hidden />
        <table class="table" id="coa-level-table">
            <thead>
                <tr>
                    <th>Level ID</th>
                    <th>Level Name</th>
                    <th>Status</th>
                    {{-- <th>Actions</th> --}}
                </tr>
            </thead>
        </table>
        {{-- {{ csrf_field() }} --}}
        {{-- {{ html()->form()->close() }} --}}
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {

            $('#coa-level-table').DataTable({ // New initialization
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: "{{ route('coa_level.datatable') }}",
                columns: [{
                        data: 'level_id',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'name',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'status',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    // { data: 'amend', searchable: false, defaultContent: "<b style=''>_</b>"  },
                ]
            });

            $('.dataTable').on('click', 'tbody td .amend_coa_level', function() {
                var rowIndex = $(this).parent().index('#coa-level-table tbody tr');
                var tdIndex = $(this).index('#coa-level-table tbody tr:eq(' + rowIndex + ') td');

                var level_no = $(this).closest('tr').find('td:eq(0)').text();
                $("#level_id").val(level_no);

                if (tdIndex < 6) {
                    $("#form_coa_level_datatable").submit();
                }

            });

            $('.amend_coa_level').on('click', 'tbody tr', function() {});

            function amendCOALevel(level_id) {
                $("#level_id").val(level_id);
                $("#form_coa_level_datatable").submit();

            }

            $("#add_coa_level").click(function() {
                $('#newcoalevel_modal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#newcoalevel_modal').modal('show');
            });

            //validate form and post
            $('#post_coalevel').on('click', function() {
                $('#newcoalevel_form').validate({
                    // Specify validation rules
                    rules: {
                        level_id: {
                            required: true,
                            digits: true
                        },
                        level_name: "required",
                    },
                    messages: {
                        level_id: {
                            required: "Please enter level id",
                            digits: "Please enter a number"
                        },
                        level_name: "Please provide description",
                    },

                    submitHandler: function(newcoalevel_form) {
                        //alert("success");
                        $.ajax({
                            url: "{{ route('COA_NewLevel') }}",
                            datatype: 'json',
                            data: $('#newcoalevel_form').serialize(),
                            type: "post",
                            success: function(scode) {
                                location.reload();
                            }
                        });
                    }
                });
            });

            // <!-- END -->
        });
    </script>
@endpush
