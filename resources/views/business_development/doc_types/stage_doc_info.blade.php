@extends('layouts.app')

@push('styles')
    <style>
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .action-btn {
            min-width: 90px;
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Stage Documents</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage Business Development stage document setup.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('bd.risk-particulars') }}">Business Development</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Stage Documents</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Documents</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-docs">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Mandatory</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-mandatory-docs">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Optional</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-optional-docs">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-rows">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Stage Document List</h5>
                        <small class="text-muted">View and manage stage document requirements</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="add_stage_document">
                        <i class='bx bx-plus me-1'></i>Add Stage Document
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover" id="stage-document-table"
                            aria-label="Stage document table" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:3%;">ID</th>
                                    <th style="width:10%;">Stage</th>
                                    <th style="width:22%;">Doc Type</th>
                                    <th style="width:8%;">Mandatory</th>
                                    <th style="width:8%;">Category Type</th>
                                    <th style="width:34%;">Business Type</th>
                                    <th style="width:15%;">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('business_development.doc_types.modals.stage_doc_modal')
@endsection

@push('script')
    <script>
        $(function() {
            var modalEl = document.getElementById('stageDocModal');
            var stageDocModal = modalEl && window.bootstrap ? new bootstrap.Modal(modalEl) : null;
            var formValidator = null;

            function initStageDocSelect2() {
                if (!$.fn.select2) {
                    return;
                }

                var $docType = $('#sd-doc-type');
                var $businessType = $('#sd-type-of-bus');
                var $categoryType = $('#sd-category-type');

                if ($docType.data('select2')) {
                    $docType.select2('destroy');
                }
                if ($businessType.data('select2')) {
                    $businessType.select2('destroy');
                }
                if ($categoryType.data('select2')) {
                    $categoryType.select2('destroy');
                }

                $docType.select2({
                    width: '100%',
                    dropdownParent: $('#stageDocModal'),
                    placeholder: 'Select Doc Type',
                    allowClear: true
                });

                $businessType.select2({
                    width: '100%',
                    dropdownParent: $('#stageDocModal'),
                    placeholder: 'Select Business Type'
                });

                $categoryType.select2({
                    width: '100%',
                    dropdownParent: $('#stageDocModal'),
                    placeholder: 'Select Category',
                    allowClear: true
                });
            }

            function notify(type, message) {
                if (window.toastr && typeof toastr[type] === 'function') {
                    toastr[type](message);
                    return;
                }
                alert(message);
            }

            function resetStageDocForm() {
                var form = $('#stageDocForm')[0];
                if (form) {
                    form.reset();
                }
                $('#sd-id').val('');
                $('#sd-doc-type').val(null).trigger('change');
                $('#sd-category-type').val(null).trigger('change');
                $('#sd-type-of-bus').val([]).trigger('change');
                $('.stage-doc-error').text('');
                $('#stageDocModalLabel').html('<i class="bx bx-plus-circle me-2"></i>Add Stage Document');
                $('#stageDocSaveBtn').html('<i class="bi bi-save me-1"></i>Save');

                if (formValidator) {
                    formValidator.resetForm();
                }
            }

            initStageDocSelect2();

            if ($.fn.validate) {
                formValidator = $('#stageDocForm').validate({
                    ignore: [],
                    rules: {
                        stage: {
                            required: true
                        },
                        doc_type: {
                            required: true
                        },
                        mandatory: {
                            required: true
                        },
                        category_type: {
                            required: true
                        },
                        'type_of_bus[]': {
                            required: true
                        }
                    },
                    messages: {
                        stage: {
                            required: 'Stage is required.'
                        },
                        doc_type: {
                            required: 'Doc Type is required.'
                        },
                        mandatory: {
                            required: 'Mandatory is required.'
                        },
                        category_type: {
                            required: 'Category Type is required.'
                        },
                        'type_of_bus[]': {
                            required: 'Business Type is required.'
                        }
                    },
                    errorClass: 'is-invalid',
                    validClass: 'is-valid',
                    errorElement: 'small',
                    errorPlacement: function(error, element) {
                        error.addClass('text-danger');
                        var key = element.attr('name').replace(/\[\]$/, '');
                        var $customError = $('[data-error-for="' + key + '"]');
                        if ($customError.length) {
                            $customError.text(error.text());
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid').removeClass('is-valid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid').addClass('is-valid');
                        var key = $(element).attr('name').replace(/\[\]$/, '');
                        $('[data-error-for="' + key + '"]').text('');
                    }
                });
            }

            $('#sd-stage, #sd-doc-type, #sd-mandatory, #sd-category-type, #sd-type-of-bus').on('change', function() {
                if (formValidator) {
                    $(this).valid();
                }
            });

            var table = $('#stage-document-table').DataTable({
                order: [],
                processing: true,
                serverSide: true,
                ajax: "{{ route('stage.doc.data') }}",
                pageLength: 13,
                lengthMenu: [
                    [13, 50, 100],
                    [13, 50, 100]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'stage',
                        name: 'stage'
                    },
                    {
                        data: 'doc_type',
                        name: 'doc_type',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'mandatory_1',
                        name: 'mandatory_1',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'category',
                        name: 'category',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'busines_type',
                        name: 'busines_type',
                        defaultContent: "<b class='dashes'>_</b>"
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {
                    var json = settings.json || {};
                    var rows = this.api().rows({
                        page: 'current'
                    }).data().toArray();
                    var mandatoryCount = rows.filter(function(row) {
                        return (row.mandatory || '').toString().toUpperCase() === 'Y';
                    }).length;
                    var totalVisible = rows.length;
                    var totalFiltered = json.recordsFiltered || 0;

                    $('#stat-total-docs').text(json.recordsTotal || 0);
                    $('#stat-filtered-rows').text(totalFiltered);
                    $('#stat-mandatory-docs').text(mandatoryCount);
                    $('#stat-optional-docs').text(Math.max(totalVisible - mandatoryCount, 0));
                }
            });

            $(document).on('click', '.update_stage_doc_type', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var id = $btn.data('id');
                var stage = ($btn.data('stage') || '').toString();
                var docType = ($btn.data('doc-type') || '').toString();
                var mandatory = ($btn.data('mandatory') || '').toString();
                var categoryType = ($btn.data('category-type') || '').toString();
                var typeOfBusRaw = $btn.attr('data-type-of-bus') || '[]';
                var typeOfBus = [];

                try {
                    typeOfBus = JSON.parse(typeOfBusRaw);
                } catch (err) {
                    typeOfBus = [];
                }

                resetStageDocForm();
                $('#sd-id').val(id);
                $('#sd-stage').val(stage).trigger('change');
                $('#sd-doc-type').val(docType).trigger('change');
                $('#sd-mandatory').val(mandatory).trigger('change');
                $('#sd-category-type').val(categoryType).trigger('change');
                $('#sd-type-of-bus').val(typeOfBus).trigger('change');

                $('#stageDocModalLabel').html('<i class="bx bx-edit me-2"></i>Update Stage Document');
                $('#stageDocSaveBtn').html('<i class="bi bi-save me-1"></i>Update');

                if (stageDocModal) {
                    stageDocModal.show();
                }
            });

            $(document).on('click', '.remove_stage_doc_type', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                var proceedDelete = function() {
                    $.ajax({
                        url: "{{ route('delete.stage.doc') }}",
                        method: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            notify('success', 'Stage document deleted successfully');
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr, status, error) {
                            notify('error', 'Failed to delete stage document');
                        }
                    });
                };

                if (window.Swal) {
                    Swal.fire({
                        title: 'Remove stage document?',
                        text: 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, remove',
                        cancelButtonText: 'Cancel'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            proceedDelete();
                        }
                    });
                    return;
                }

                if (confirm('Are you sure you want to remove this stage document?')) {
                    proceedDelete();
                }
            });


            $("#add_stage_document").click(function() {
                resetStageDocForm();
                if (stageDocModal) {
                    stageDocModal.show();
                }
            });

            $('#stageDocForm').on('submit', function(e) {
                e.preventDefault();
                if (formValidator && !$('#stageDocForm').valid()) {
                    return;
                }

                $('.stage-doc-error').text('');
                $('#stageDocSaveBtn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('stage.doc.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        notify('success', response.message || 'Stage document saved successfully');
                        if (stageDocModal) {
                            stageDocModal.hide();
                        }
                        resetStageDocForm();
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                var safeKey = key.replace(/\.\d+/g, '');
                                var text = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
                                $('[data-error-for="' + safeKey + '"]').text(text);
                            });
                        } else {
                            var message = (xhr.responseJSON && xhr.responseJSON.message) ?
                                xhr.responseJSON.message : 'Failed to save stage document';
                            notify('error', message);
                        }
                    },
                    complete: function() {
                        $('#stageDocSaveBtn').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
