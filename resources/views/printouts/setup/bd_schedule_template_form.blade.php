@extends('layouts.app')

@section('content')
    <div>
        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">New Schedule Template</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Schedule Template Setup</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $classGroup->group_name }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $classcode->class_name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Schedule Template</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Page Header Close -->

        <div class="cover-wrapper form-group">
            <form id="register_clause"
                action="{{ !empty($clauses) ? route('docs-setup.edit_bd_schedule_template_form') : route('docs-setup.save_bd_schedule_template_form') }}"
                method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="clause_id" name="clause_id" value="{{ $clauses?->clause_id }}">
                <input type="hidden" id="classcode" name="classcode" value="{{ $classcode->class_code }}">
                <input type="hidden" id="class_group_code" name="class_group_code" value="{{ $classGroup->group_code }}">
                <div class="form-group">
                    <div class="row row-cols-12">
                        <div class="col-md-6">
                            <label class="form-label required">Schedule Title</label>
                            <input type="text" class="form-inputs section color-blk" id="clause_title"
                                name="clause_title" value="{{ $clauses?->clause_title ?? '' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Business Type</label>
                            <div class="cover-card">
                                <select class="form-inputs section select2" name="type_of_bus[]" id="type_of_bus" multiple>
                                    <option value="">-- Choose Business Type --</option>
                                    @foreach ($types_of_bus as $type_of_bus)
                                        <option value="{{ $type_of_bus->bus_type_id }}"
                                            {{ isset($clauses->type_of_bus) && is_array($clauses->type_of_bus) && in_array($type_of_bus->bus_type_id, $clauses->type_of_bus) ? 'selected' : '' }}>
                                            {{ $type_of_bus->bus_type_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row row-cols-12 mt-2">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" for="clause-description">Details</label>
                                </div>
                            </div>
                            <textarea class="form-inputs" rows="6" name="details" id="clause-description">{{ $clauses->clause_wording ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <button type="button" id="save_template"
                            class="btn btn-primary btn-raised-shadow btn-sm btn-wave btn-block">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#type_of_bus').select2({
                minimumResultsForSearch: Infinity
            });

            tinymce.init({
                selector: 'textarea#clause-description',
                setup: function(editor) {
                    editor.on('change', function(e) {
                        editor.save();
                    });
                },
                plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars accordion paste', // Added 'paste' plugin
                menubar: 'edit view insert format table',
                toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | fullscreen preview | save print | pagebreak anchor codesample | ltr rtl | pastetext pasteword selectall", // Added paste-related buttons
                autosave_ask_before_unload: true,
                autosave_interval: '30s',
                autosave_prefix: '{path}{query}-{id}-',
                autosave_restore_when_empty: false,
                autosave_retention: '2m',
                image_advtab: false,
                importcss_append: true,
                automatic_uploads: false,
                branding: false,
                file_picker_types: "image",
                file_picker_callback: function(cb, value, meta) {
                    var input = document.createElement("input");
                    input.setAttribute("type", "file");
                    input.setAttribute("accept", "image/*");
                    input.onchange = function() {
                        var file = this.files[0];
                        var reader = new FileReader();
                        reader.onload = function() {
                            var id = "blobid" + new Date().getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(",")[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);
                            cb(blobInfo.blobUri(), {
                                title: file.name
                            });
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                },
                height: 550,
                image_caption: false,
                quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
                noneditable_class: 'mceNonEditable',
                toolbar_mode: 'sliding',
                contextmenu: 'link image table',
                skin: 'oxide',
                content_css: 'default',
                content_style: 'body { font-family:Open Sans,Arial,sans-serif; font-size:15px }'
            });


            $("form#register_clause").validate({
                ignore: ":hidden",
                rules: {
                    clause_title: {
                        required: true
                    },
                    type_of_bus: {
                        required: true
                    },
                },
                messages: {
                    covertype: {
                        required: "Clause Title is required"
                    },
                    type_of_bus: {
                        required: "Business Type is required"
                    },
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
            })

            $("form #save_template").on('click', function(e) {
                e.preventDefault()
                const editorData = tinymce.get('clause-description').getContent()
                $('#clause-description').val(editorData);
                if ($("form#register_clause").valid()) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to submit the form?",
                        icon: false,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, submit',
                        cancelButtonText: 'No, cancel',
                        customClass: {
                            confirmButton: 'custom-confirm',
                            cancelButton: 'swal2-cancel'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("form#register_clause").submit();
                            toastr.success("template saved successfully");
                        } else {
                            return false;
                        }
                    });
                } else {
                    toastr.error("Please correct the errors before submitting.");
                }
            })
        });
    </script>
@endpush
