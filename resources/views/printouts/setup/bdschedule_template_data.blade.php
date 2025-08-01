@extends('layouts.app')

@section('content')
    <div>

        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Schedule Slip Template</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Schedule template Setup</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Schedule Slip</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Page Header Close -->

        <div class="row">
            <div class="col-xl-6">
                <button type="button" class="btn btn-sm btn-dark btn-wave" id="newSlipClause">Add new schedule
                    template</button>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Schedule Template list</div>
                    </div>
                    <div class="card-body">
                        {{-- {{ html()->form('POST', '/cover/endorsements_list')->id('form_cover_datatable')->open() }} --}}
                        {{-- <input type="text" id="customer_id" name="customer_id" hidden />
                        <input type="text" name="cover_no" id="cov_cover_no" hidden> --}}
                        <table id="coversliplist" class="table text-nowrap table-hover table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Schedule Title</th>
                                    <th>Class Group</th>
                                    <th>Class Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        {{-- {{ csrf_field() }} --}}
                        {{-- {{ html()->form()->close() }} --}}
                    </div>
                </div>
            </div>
        </div>

        <!--Choose clause -->
        <div class="modal effect-scale" id="newSlipModal" data-bs-backdrop="static" data-bs-keyboard="false"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editSlipform" action="{{ route('docs-setup.bd_schedule_template_form') }}">
                        @csrf
                        <input type="hidden" name="class_group" id="class_group">
                        <input type="hidden" name="classcode" id="classcode">
                        <input type="hidden" name="clause" id="clause">
                    </form>
                    <form id="newSlipform" action="{{ route('docs-setup.bd_schedule_template_form') }}">
                        @csrf
                        <div class="modal-body">
                            <h6 class="form-label md-title font-weight-bold mb-2">Create Bd Schedules Template</h6>
                            <div class="row">
                                <div class="col">
                                    <div class="d-flex flex-column ced-body">
                                        <label for="title" class="form-label md-title">Class Group</label>
                                        <div class="cover-card">
                                            <select class="form-slect select2" id="select_classgroup" name="class_group"
                                                required>
                                                @switch($trans_type)
                                                    @case('NEW')
                                                        <option selected value="">-- Select --</option>
                                                        @foreach ($classGroups as $classGroup)
                                                            <option value="{{ $classGroup->group_code }}">
                                                                {{ $classGroup->group_name }}
                                                            </option>
                                                        @endforeach
                                                    @break

                                                    @case('EXT')
                                                    @case('CNC')

                                                    @case('REN')
                                                    @case('RFN')

                                                    @case('NIL')
                                                    @case('INS')

                                                    @case('EDIT')
                                                        @foreach ($classGroups as $classGroup)
                                                            <option value="{{ $classGroup->group_code }}">
                                                                {{ $classGroup->group_name }}
                                                            </option>
                                                        @endforeach
                                                    @break
                                                @endswitch
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label required">Class Name</label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2 fac_section" name="classcode"
                                            id="sel_classcode" required>
                                            <option value="">-- Select --</option>


                                        </select>
                                        <div class="text-danger">{{ $errors->first('classcode') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                                id="dismissSlipBtn">Close</button>
                            <button type="button" id="saveSlipBtn"
                                class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#newSlipModal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#newSlipModal')
                });
            })

            $('#coversliplist').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: {
                    url: '{{ route('docs-setup.bd_schedule_datatable') }}',
                },
                columns: [{
                        data: 'clause_id',
                        className: 'highlight-idx',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                    }, {
                        data: 'clause_title',
                        searchable: true,
                        class: 'highlight-view-point'
                    },
                    {
                        data: 'clause_group',
                        searchable: true,
                    },
                    {
                        data: 'class_name',
                        searchable: true,
                    },
                    {
                        data: 'clause_wording',
                        searchable: true,
                        className: "highlight-description clamp-text",
                    },
                    {
                        data: 'status',
                        searchable: false,
                    },
                    {
                        data: 'action',
                        class: 'highlight-action',
                        searchable: false,
                        sortable: false,
                    },
                ]
            });

            $('#newSlipClause').click(function(e) {
                e.preventDefault();
                $('#newSlipModal').modal('show');
                $('#classcode').empty();
                $('#classcode').append($('<option>').text('-- Select --').attr('value', ''));
            });

            $("select#select_classgroup").change(function() {
                var class_group = $("select#select_classgroup option:selected").attr('value');
                $('#sel_classcode').append($('<option>').text('-- Select --').attr('value', ''));
                if ($(this).val() != '') {
                    $('#class').prop('disabled', false)
                    $.ajax({
                        url: "{{ route('get_class') }}",
                        data: {
                            "class_group": class_group
                        },
                        dataType: "json",
                        type: "get",
                        success: function(resp) {
                            console.log(resp)
                            if (Array.isArray(resp) && resp.length > 0) {
                                $('#sel_classcode').empty();
                                $('#sel_classcode').append($('<option>').text('-- Select --')
                                    .attr('value', ''));
                                $.each(resp, function(i, value) {
                                    $('#sel_classcode').append(`
                                    <option value="${value.class_code}">${value.class_code} - ${value.class_name}</option>
                                `);
                                });

                            } else {
                                $('#sel_classcode').append(
                                    '<option value="">No classes found</option>');
                            }
                        },
                        error: function(resp) {
                            console.error(resp);
                        }
                    })
                }
            });


            $('#saveSlipBtn').click(function(e) {
                  const selectedVal = $('#sel_classcode').val();
                const selectedText = $('#sel_classcode option:selected').text();

                console.log("Sending to backend:", selectedVal, selectedText);
                $("#newSlipform").submit();
            });

            $("#newSlipform").validate({
                errorClass: "errorClass",
                rules: {
                    class_group: {
                        required: true
                    },
                    class_code: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#saveSlipBtn')
                        .prop('disabled', true)
                        .html(`<span class="me-2">Next </span><div class="loading"></div>`);
                    form.submit();
                    $('#saveSlipBtn').prop('disabled', false).text('Next');
                }
            });

            $('#dismissSlipBtn').on('click', function() {
                $("#newSlipform")[0].reset();
                $("#select_classgroup-error").css({
                    display: "none"
                });
                $("#classcode-error").css({
                    display: "none"
                });
            });

            $(document).on('click', '.remove-clause', function() {
                const clauseId = $(this).data('clause-id');
                const classGroup = $(this).data('class-group');
                const classCode = $(this).data('class-code');
                const title = $(this).data('title');
                swal.fire({
                    title: 'Remove Clause Item',
                    text: `This action will remove the Item ${title} from this cover `,
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isDismissed) {
                        return false;
                    }
                    const data = {
                        class_code: classCode,
                        class_group_code: classGroup,
                        id: clauseId,
                    }

                    fetchWithCsrf("{!! route('docs-setup.delete-schedule-template') !!}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(data),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == 201) {
                                toastr.success("Clause deleted successful", 'Deleted')
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)
                            } else {
                                toastr.error("Failed to save details")
                            }
                        })
                        .catch(error => {
                            toastr.error("An internal error occured")
                        });
                });
            })

            $(document).on('click', '.edit-clause', function() {
                const clauseId = $(this).data('clause-id');
                const classGroup = $(this).data('class-group');
                const classCode = $(this).data('class-code');

                $("#class_group").val(classGroup);
                $("#classcode").val(classCode);
                $("#clause").val(clauseId);
                $("#editSlipform").submit();
            })

        })
    </script>
@endpush
