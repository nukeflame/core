@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Bd Doc Type</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Bd Doc Type</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        @if (isset($StageDocuments))
                            Update
                        @else
                            Create
                        @endif
                    </li>

                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row row-cols-12">
        <div class="col">
            <form id="store_schedule_header" enctype="multipart/form-data" action="{{ route('doc.type.store') }}"
                method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($Documents) ? $Documents->id : '' }}">
                <input type="hidden" name="s3UploadedFilePath"
                    value="{{ isset($Documents) ? ($Documents->path ?? '') : '' }}">

                <div class="form-group">
                    <h4>Bd Doc type Details</h4>
                    <div class="row gy-4 partner_info">
                        <div class="col-xl-4">
                            <label class="form-label">Document</label>
                            <input type="text" class="form-inputs" aria-label="stage" id="doc_type" name="doc_type"
                                required value="{{ isset($Documents) ? $Documents->doc_type : '' }}">
                        </div>
                        <div class="col-xl-4">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-inputs" aria-label="description" id="description"
                                name="description" required value="{{ isset($Documents) ? $Documents->description : '' }}">

                        </div>
                    </div>
                    <div class="row gy-4 partner_info">
                        <div class="col-xl-2">
                            <label class="form-label">Business type</label>
                            <select id="business_type" name="bus_type" class="form-inputs" required>
                                <option value="">-- Select --</option>
                                <option value="FAC"
                                    {{ isset($Documents) && $Documents->bus_type == 'FAC' ? 'selected' : '' }}>
                                    Facultative
                                </option>
                                <option value="TRT"
                                    {{ isset($Documents) && $Documents->bus_type == 'TRT' ? 'selected' : '' }}>Treaty
                                </option>
                            </select>
                        </div>
                        <div class="col-xl-2" id="attachment_file_container" style="display: none;">
                            <div>
                                <label class="form-label">Attachment File?</label>
                                <select name="attachment_file" id="attachment_file" class="form-inputs">
                                    <option value="">--- Select ---</option>
                                    <option value="Y" {{ isset($Documents) && $Documents->attachment_file == 'Y' ? 'selected' : '' }}>YES</option>
                                    <option value="N" {{ isset($Documents) && $Documents->attachment_file == 'N' ? 'selected' : '' }}>NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2" id="checkbox_doc_container" style="display: none;">
                            <label class="form-label">Cedant/our document</label>
                            <select name="checkbox_doc" id="checkbox_doc" class="form-inputs select2">
                                <option value="">-- Select --</option>
                                <option value="1" {{ isset($Documents) && $Documents->checkbox_doc == 1 ? 'selected' : '' }}>Cedant </option>
                                <option value="2" {{ isset($Documents) && $Documents->checkbox_doc == 2 ? 'selected' : '' }}> Required Documents from Cedant</option>
                            </select>
                        </div>

                        <div class="col-xl-2" id="cedant_file_container" style="display: none;">
                            <label class="form-label" for="file_input">Cedant File</label>
                            <input type="file" id="file_input" name="cedant_file" class="form-control" />
                        </div>

                    </div>



                </div>



                <div class="row">
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary submit-btn" id="add_customer">Submit</button>
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $('.typeof_select').select2({
                placeholder: '-- Select --',
            });

            // Initialize container visibility
            $('#attachment_file_container').hide();
            $('#checkbox_doc_container').hide();
            $('#cedant_file_container').hide();

            // Function to update visibility and reset fields
            function updateVisibility() {
                const businessType = $('#business_type').val();
                const attachmentFile = $('#attachment_file').val();
                const checkboxDoc = $('#checkbox_doc').val();

                // Handle business_type
                if (businessType === 'TRT') {
                    $('#attachment_file_container').show();
                } else {
                    $('#attachment_file_container').hide();
                    $('#checkbox_doc_container').hide();
                    $('#cedant_file_container').hide();
                    // Reset dependent fields and trigger change
                    $('#attachment_file').val('').trigger('change.select2').trigger('change');
                    $('#checkbox_doc').val('').trigger('change.select2').trigger('change');
                    $('#file_input').val('');
                }

                // Handle attachment_file
                if (attachmentFile === 'N' && businessType === 'TRT') {
                    $('#checkbox_doc_container').show();
                } else {
                    $('#checkbox_doc_container').hide();
                    $('#cedant_file_container').hide();
                    // Reset dependent fields and trigger change
                    $('#checkbox_doc').val('').trigger('change.select2').trigger('change');
                    $('#file_input').val('');
                }

              
                if (checkboxDoc === '1' && attachmentFile === 'N' && businessType === 'TRT') {
                    $('#cedant_file_container').show();
                } else {
                    $('#cedant_file_container').hide();
                    $('#file_input').val('');
                }
            }

          
            updateVisibility();

           
            $('#business_type').on('change', function () {
                updateVisibility();
            });
            $('#attachment_file').on('change', function () {
                updateVisibility();
            });
            $('#checkbox_doc').on('change', function () {
                updateVisibility();
            });
           
            $("#store_schedule_header").validate({
                rules: {
                    doc_type: {
                        required: true,
                        maxlength: 100,
                    },
                    description: {
                        required: true,
                        maxlength: 100,
                    },

                },
                messages: {
                    description: {
                        required: "document  is required",
                        maxlength: "max length is 100",
                        // pattern: "Customer name should contain letters only"
                    },
                    doc_type: {
                        required: "doc_type is required",
                        maxlength: "max length is 100",
                    }
                },
                errorPlacement: function(error, element) {
                    error.addClass("text-danger"); // Add red color to the error message
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('error').removeClass('valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error').addClass('valid');
                },
                submitHandler: function(form, e) {
                    e.preventDefault();
                    var isConfirmed = confirm("Are you sure you want to submit the form?");
                    if (isConfirmed) {
                        form.submit();
                    } else {
                        return false;
                    }
                }
            });
        });
    </script>
@endpush
