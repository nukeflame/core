@extends('layouts.app')

@push('styles')
    <style>
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .action-btn {
            min-width: 120px;
        }

        .upload-dropzone {
            border: 2px dashed #cfd8dc;
            border-radius: 10px;
            background: #f8fafc;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .upload-dropzone:hover,
        .upload-dropzone.is-dragover {
            border-color: #0d6efd;
            background: #eef5ff;
        }

    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Document Types</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage Business Development document register.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('bd.risk-particulars') }}">Business Development</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Document Types</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-doc-types">{{ $docTypeStats['total'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Required</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-required-doc-types">{{ $docTypeStats['required'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Default</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-default-doc-types">{{ $docTypeStats['default'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Uploaded</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-uploaded-doc-types">{{ $docTypeStats['uploaded'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Document Type List</h5>
                        <small class="text-muted">ID, code, country, requirement, default and file status</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="add_doc_type">
                        <i class='bx bx-plus me-1'></i>Add Document Type
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover" id="doc-type-table"
                            aria-label="Document type table" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:3%;">ID</th>
                                    <th style="width:10%;">Code</th>
                                    <th style="width:20%;">Document Type</th>
                                    <th style="width:10%;">Country</th>
                                    <th style="width:8%;">Required</th>
                                    <th style="width:8%;">Default</th>
                                    <th style="width:8%;">File Status</th>
                                    <th style="width:18%;">Description</th>
                                    <th style="width:15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('business_development.doc_types.modals.doc_type_modal')
@endsection

@push('script')
    <script>
        $(function() {
            var modalEl = document.getElementById('docTypeModal');
            var docTypeModal = modalEl && window.bootstrap ? new bootstrap.Modal(modalEl) : null;
            var formValidator = null;

            function showMessage(type, message) {
                if (window.toastr && typeof toastr[type] === 'function') {
                    toastr[type](message);
                    return;
                }
                alert(message);
            }

            function resetForm() {
                var form = $('#docTypeForm')[0];
                if (form) {
                    form.reset();
                }

                $('#dt-id').val('');
                $('#dt-code').val('');
                $('#dt-country').val('All');
                $('#dt-is-required').val('Y');
                $('#dt-is-default').val('Y');
                $('#dt-s3-uploaded-file-path').val('');
                $('#docTypeModalLabel').html('<i class="bx bx-plus-circle me-2"></i>Add Document Type');
                $('#docTypeSaveBtn').html('<i class="bi bi-save me-1"></i>Save');
                $('.doc-type-error').text('');
                $('#docTypeForm').find('.is-invalid').removeClass('is-invalid');
                $('#dt-current-file-wrap').addClass('d-none');
                $('#dt-current-file-link').attr('href', '#');
                $('#dt-selected-file-name').text('No file selected');
                $('#dt-file-input').val('');

                if (formValidator) {
                    formValidator.resetForm();
                }
            }

            function fillFormFromButton($btn) {
                $('#dt-id').val($btn.data('id') || '');
                $('#dt-code').val(($btn.data('code') || '').toString().toUpperCase());
                $('#dt-doc-type').val($btn.data('doc-type') || '');
                $('#dt-description').val($btn.data('description') || '');
                $('#dt-country').val($btn.data('country') || 'All');
                $('#dt-is-required').val(($btn.data('is-required') || 'Y').toString().toUpperCase());
                $('#dt-is-default').val(($btn.data('is-default') || 'Y').toString().toUpperCase());
                $('#dt-s3-uploaded-file-path').val($btn.data('file-path') || '');
                $('#docTypeModalLabel').html('<i class="bx bx-edit me-2"></i>Update Document Type');
                $('#docTypeSaveBtn').html('<i class="bi bi-save me-1"></i>Update');

                var fileUrl = $btn.data('file-url') || '';
                if (fileUrl) {
                    $('#dt-current-file-link').attr('href', fileUrl);
                    $('#dt-current-file-wrap').removeClass('d-none');
                } else {
                    $('#dt-current-file-wrap').addClass('d-none');
                    $('#dt-current-file-link').attr('href', '#');
                }

                $('#dt-selected-file-name').text('No new file selected');
            }

            function bindUploadArea() {
                var $dropzone = $('#dt-upload-dropzone');
                var $fileInput = $('#dt-file-input');
                var $label = $('#dt-selected-file-name');

                $dropzone.on('click keydown', function(e) {
                    if (e.target === $fileInput[0]) {
                        return;
                    }

                    if (e.type === 'click' || e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        $fileInput.trigger('click');
                    }
                });

                $fileInput.on('click', function(e) {
                    e.stopPropagation();
                });

                $dropzone.on('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $dropzone.addClass('is-dragover');
                });

                $dropzone.on('dragleave drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $dropzone.removeClass('is-dragover');
                });

                $dropzone.on('drop', function(e) {
                    var files = e.originalEvent && e.originalEvent.dataTransfer ? e.originalEvent
                        .dataTransfer.files :
                        null;
                    if (!files || !files.length) {
                        return;
                    }

                    $fileInput[0].files = files;
                    $label.text(files[0].name);
                });

                $fileInput.on('change', function() {
                    if (this.files && this.files.length) {
                        $label.text(this.files[0].name);
                    } else {
                        $label.text('No file selected');
                    }
                });
            }

            var table = $('#doc-type-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('doc.type.data') }}",
                pageLength: 15,
                lengthMenu: [
                    [15, 30, 100],
                    [15, 30, 100]
                ],
                order: [
                    [0, 'asc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'code',
                        name: 'code',
                        defaultContent: '-'
                    },
                    {
                        data: 'doc_type',
                        name: 'doc_type',
                        defaultContent: '-'
                    },
                    {
                        data: 'country',
                        name: 'country',
                        defaultContent: 'All'
                    },
                    {
                        data: 'required_label',
                        name: 'required_label',
                        orderable: false,
                        searchable: false,
                        defaultContent: 'Yes'
                    },
                    {
                        data: 'default_label',
                        name: 'default_label',
                        orderable: false,
                        searchable: false,
                        defaultContent: 'Yes'
                    },
                    {
                        data: 'file_status',
                        name: 'file_status',
                        orderable: false,
                        searchable: false,
                        defaultContent: 'Not Uploaded'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        defaultContent: '-'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        defaultContent: '-'
                    }
                ],
                drawCallback: function(settings) {
                    var json = settings.json || {};
                    var stats = json.stats || {};

                    $('#stat-total-doc-types').text(stats.total || 0);
                    $('#stat-required-doc-types').text(stats.required || 0);
                    $('#stat-default-doc-types').text(stats.default || 0);
                    $('#stat-uploaded-doc-types').text(stats.uploaded || 0);
                }
            });

            $('#add_doc_type').on('click', function() {
                resetForm();
                if (docTypeModal) {
                    docTypeModal.show();
                }
            });

            bindUploadArea();

            if ($.fn.validate) {
                formValidator = $('#docTypeForm').validate({
                    ignore: [],
                    rules: {
                        code: {
                            required: true,
                            maxlength: 50
                        },
                        doc_type: {
                            required: true,
                            maxlength: 100
                        },
                        description: {
                            required: true,
                            maxlength: 100
                        },
                        country: {
                            required: true,
                            maxlength: 100
                        },
                        is_required: {
                            required: true
                        },
                        is_default: {
                            required: true
                        }
                    },
                    messages: {
                        code: {
                            required: 'Code is required.'
                        },
                        doc_type: {
                            required: 'Document type is required.'
                        },
                        description: {
                            required: 'Description is required.'
                        },
                        country: {
                            required: 'Country is required.'
                        },
                        is_required: {
                            required: 'Required field is mandatory.'
                        },
                        is_default: {
                            required: 'Default field is mandatory.'
                        }
                    },
                    errorClass: 'is-invalid',
                    validClass: 'is-valid',
                    errorElement: 'small',
                    errorPlacement: function(error, element) {
                        error.addClass('text-danger');
                        var key = element.attr('name');
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
                        var key = $(element).attr('name');
                        $('[data-error-for="' + key + '"]').text('');
                    }
                });
            }

            $('#dt-code').on('input', function() {
                this.value = this.value.toUpperCase();
            });

            $(document).on('click', '.update_doc_type', function(e) {
                e.preventDefault();
                resetForm();
                fillFormFromButton($(this));
                if (docTypeModal) {
                    docTypeModal.show();
                }
            });

            $(document).on('click', '.remove_doc_type', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var proceedDelete = function() {
                    $.ajax({
                        url: "{{ route('delete.doc.type') }}",
                        method: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            var msg = response.message ||
                                'Document type removed successfully';
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Removed',
                                    text: msg,
                                    timer: 1800,
                                    showConfirmButton: false
                                });
                            } else {
                                showMessage('success', msg);
                            }
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message) ?
                                xhr
                                .responseJSON.message : 'Failed to delete document type';
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Delete Failed',
                                    text: message
                                });
                            } else {
                                showMessage('error', message);
                            }
                        }
                    });
                };

                if (window.Swal) {
                    Swal.fire({
                        title: 'Remove document type?',
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

                if (confirm('Are you sure you want to remove this document type?')) {
                    proceedDelete();
                }
            });

            $('#docTypeForm').on('submit', function(e) {
                e.preventDefault();
                if (formValidator && !$('#docTypeForm').valid()) {
                    return;
                }

                var formData = new FormData(this);
                var selectedFile = $('#dt-file-input')[0] ? $('#dt-file-input')[0].files[0] : null;
                if (selectedFile) {
                    formData.set('cedant_file', selectedFile);
                }

                $('.doc-type-error').text('');
                $('#docTypeSaveBtn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('doc.type.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showMessage('success', response.message ||
                            'Document type saved successfully');
                        if (docTypeModal) {
                            docTypeModal.hide();
                        }
                        resetForm();
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                var text = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
                                $('[data-error-for="' + key + '"]').text(text);
                            });
                            return;
                        }

                        var message = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) ?
                            (xhr.responseJSON.message || xhr.responseJSON.error) :
                            'Failed to save document type';
                        showMessage('error', message);
                    },
                    complete: function() {
                        $('#docTypeSaveBtn').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
