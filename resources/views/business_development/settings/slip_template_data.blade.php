@extends('layouts.app')

@section('content')
    @php
        $templateCount = 0;
        $selectedTreatyType = $treaty_type ?? 'N/A';
        $classCount = 0;
        $classGroupCount = 0;
        $templateStatus = 'A: 0 | I: 0';
        $businessTypeCount = 0;
    @endphp

    {{-- Page Header --}}
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Schedule Slip Template</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage Business Development schedule headers slip templates.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('bd.risk-particulars') }}">Business Development</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Schedule Slip Template</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                <i class="bi bi-building fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Templates</p>
                                    <h4 class="fw-semibold mt-1" id="stat-total-headers">{{ $templateCount }}</h4>
                                </div>
                                <div id="total-cedants-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="badge bg-primary-transparent"
                                        id="stat-total-change">{{ $templateStatus }}</span>
                                    <span class="text-muted ms-2 fs-12">Current template status</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-success-transparent">
                                <i class="bi bi-shield-check fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Filtered Templates</p>
                                    <h4 class="fw-semibold mt-1" id="stat-visible-rows">0</h4>
                                </div>
                                <div id="active-covers-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="badge bg-success-transparent" id="stat-covers-change">0 on page</span>
                                    <span class="text-muted ms-2 fs-12">Filtered records</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-info-transparent">
                                <i class="bi bi-diagram-3 fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Unique Classes</p>
                                    <h4 class="fw-semibold mt-1" id="stat-class-groups">{{ $classCount }}</h4>
                                </div>
                                <div id="cedant-types-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="text-muted fs-12" id="stat-types-breakdown">{{ $classGroupCount }} class
                                        groups mapped</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-warning-transparent">
                                <i class="bi bi-clock-history fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Business Types</p>
                                    <h4 class="fw-semibold mt-1" id="stat-business-types">{{ $businessTypeCount }}</h4>
                                </div>
                                <div id="recent-activity-spark"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="text-muted fs-12" id="stat-business-types-note">Across filtered
                                        templates</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Schedule Slip Template List</h5>
                        <small class="text-muted">View and manage schedule slip templates</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="newSlipClause" data-bs-toggle="modal"
                        data-bs-target="#slipTemplateModal" aria-label="Add new schedule headers slip template">
                        <i class='bx bx-plus me-1'></i>
                        Add Schedule Slip Template
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="coversliplist" class="table text-nowrap table-striped table-hover"
                            aria-label="Schedule headers slip template table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Schedule Title</th>
                                    <th>Class Group</th>
                                    <th>Class Name</th>
                                    <th style="width: 35%;">Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="slipTemplateModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="slipTemplateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" style="width: 75%; max-width: 75%;">
            <div class="modal-content">
                <form id="slipTemplateForm">
                    @csrf
                    <input type="hidden" name="id" id="st-id">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white" id="slipTemplateModalLabel">
                            <i class="bx bx-plus-circle me-2"></i>Add Schedule Slip Template
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="overflow: hidden">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="st-schedule-title" class="form-label">Schedule Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="st-schedule-title" name="schedule_title"
                                    maxlength="150" required>
                            </div>
                            <div class="col-md-6">
                                <label for="st-status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="st-status" name="status" required>
                                    <option value="A">Active</option>
                                    <option value="I">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="st-type-of-bus" class="form-label">Business Type</label>
                                <select class="form-select" id="st-type-of-bus" name="type_of_bus[]" multiple>
                                    @foreach ($businessTypes ?? [] as $busType)
                                        <option value="{{ $busType->bus_type_id }}">{{ $busType->bus_type_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="st-class-group-code" class="form-label">Class Group</label>
                                <select class="form-select" id="st-class-group-code" name="class_group_code">
                                    <option value="">-- Select Class Group --</option>
                                    @foreach ($classGroups as $classGroup)
                                        <option value="{{ $classGroup->group_code }}">
                                            {{ $classGroup->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="st-class-code" class="form-label">Class Name</label>
                                <select class="form-select" id="st-class-code" name="class_code">
                                    <option value="">-- Select Class --</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="st-description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="st-description" name="description"
                                    maxlength="255" placeholder="Short description">
                            </div>
                            <div class="col-12">
                                <label for="st-wording" class="form-label">Wording</label>
                                <input type="hidden" id="st-wording" name="wording">
                                <div id="st-wording-editor" class="border rounded bg-white"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="slipTemplateSaveBtn"><i
                                class="bi bi-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #st-wording-editor .ql-editor {
            min-height: 300px;
            max-height: 600px;
            overflow-y: hidden;
        }

        #st-wording-editor .ql-editor.customScroll {
            height: 600px;
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
@endpush

@push('script')
    <script>
        (function() {
            'use strict';

            $(function() {
                if (!$.fn.DataTable) {
                    return;
                }

                var classes = @json($class ?? []);
                var classGroups = @json($classGroups ?? []);
                var businessTypes = @json($businessTypes ?? []);
                var saveUrl = @json(route('bd.slip-template.store'));
                var deleteUrl = @json(route('bd.slip-template.delete'));
                var treatyType = @json($treaty_type ?? '');
                var modalEl = document.getElementById('slipTemplateModal');
                var slipTemplateModal = modalEl && window.bootstrap ? new bootstrap.Modal(modalEl) : null;
                var isEditMode = false;
                var wordingQuill = null;
                var wordingEditorMinHeight = 300;
                var wordingEditorMaxHeight = 780;
                var formValidator = null;

                function showMessage(type, text) {
                    if (window.toastr && typeof toastr[type] === 'function') {
                        toastr[type](text);
                        return;
                    }

                    if (type === 'error') {
                        alert(text);
                    }
                }

                function resetForm() {
                    var form = $('#slipTemplateForm')[0];
                    if (form) {
                        form.reset();
                    }

                    $('#st-id').val('');
                    if (treatyType) {
                        $('#st-type-of-bus').val([String(treatyType)]).trigger('change');
                    } else {
                        $('#st-type-of-bus').val([]).trigger('change');
                    }
                    $('#slipTemplateModalLabel').html(
                        '<i class="bx bx-plus-circle me-2"></i>Add Schedule Slip Template');
                    $('#slipTemplateSaveBtn').text('Save');
                    isEditMode = false;
                    populateClassOptions('', '');
                    if (wordingQuill) {
                        wordingQuill.setContents([]);
                    }
                    $('#st-wording').val('');
                    syncWordingScrollClass();
                }

                function populateClassOptions(groupCode, selectedClassCode) {
                    var $class = $('#st-class-code');
                    $class.empty().append('<option value="">-- Select Class --</option>');

                    if (!groupCode) {
                        return;
                    }

                    classes.forEach(function(item) {
                        if (!item || !item.class_code) {
                            return;
                        }

                        if ((item.class_group_code || '') !== groupCode) {
                            return;
                        }

                        var label = item.class_name ? item.class_code + ' - ' + item.class_name : item
                            .class_code;
                        var selected = selectedClassCode && selectedClassCode === item.class_code ?
                            ' selected' : '';
                        $class.append('<option value="' + item.class_code + '"' + selected + '>' +
                            label +
                            '</option>');
                    });
                }

                function resolveClassGroupCode(rowData, $btn) {
                    var directCode = rowData.class_group_code || $btn.data('class-group-code') || '';
                    if (directCode) {
                        return String(directCode).trim();
                    }

                    var groupName = rowData.class_group || $btn.data('class-group') || '';
                    if (!groupName) {
                        return '';
                    }

                    var normalized = String(groupName).trim().toLowerCase();
                    var matched = classGroups.find(function(item) {
                        return item && item.group_name && String(item.group_name).trim()
                        .toLowerCase() ===
                            normalized;
                    });

                    return matched && matched.group_code ? String(matched.group_code).trim() : '';
                }

                function resolveClassCode(rowData, $btn, classGroupCode) {
                    var directCode = rowData.class_code || rowData.rein_class || $btn.data('class-code') || '';
                    if (directCode) {
                        return String(directCode).trim();
                    }

                    var className = rowData.class_name || $btn.data('class-name') || '';
                    if (!className) {
                        return '';
                    }

                    var normalized = String(className).trim().toLowerCase();
                    var matchedClass = classes.find(function(item) {
                        if (!item || !item.class_code) {
                            return false;
                        }

                        var sameGroup = !classGroupCode || String(item.class_group_code || '')
                        .trim() ===
                            String(classGroupCode).trim();
                        var sameName = item.class_name && String(item.class_name).trim()
                        .toLowerCase() ===
                            normalized;

                        return sameGroup && sameName;
                    });

                    return matchedClass && matchedClass.class_code ? String(matchedClass.class_code).trim() :
                    '';
                }

                function normalizeTypeOfBusValues(value, treatyFallback) {
                    var result = [];

                    if (Array.isArray(value)) {
                        result = value;
                    } else if (typeof value === 'string' && value.trim() !== '') {
                        var trimmed = value.trim();
                        try {
                            var parsed = JSON.parse(trimmed);
                            if (Array.isArray(parsed)) {
                                result = parsed;
                            } else {
                                result = trimmed.split(',');
                            }
                        } catch (e) {
                            result = trimmed.split(',');
                        }
                    }

                    result = result.map(function(item) {
                        return String(item || '').trim().toUpperCase();
                    }).filter(Boolean);

                    if (!result.length && treatyFallback) {
                        result = [String(treatyFallback).trim().toUpperCase()];
                    }

                    var validIds = new Set((businessTypes || []).map(function(item) {
                        return String(item.bus_type_id || '').trim().toUpperCase();
                    }));

                    return result.filter(function(item) {
                        return !validIds.size || validIds.has(item);
                    });
                }

                function initWordingEditor() {
                    if (!window.Quill) {
                        showMessage('error', 'Quill editor is not available.');
                        return;
                    }

                    wordingQuill = new Quill('#st-wording-editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{
                                    header: [1, 2, false]
                                }],
                                ['bold', 'italic', 'underline'],
                                [{
                                    list: 'ordered'
                                }, {
                                    list: 'bullet'
                                }],
                                ['link', 'clean']
                            ]
                        },
                        placeholder: 'Enter schedule slip template wording...'
                    });

                    wordingQuill.on('text-change', function() {
                        $('#st-wording').val(wordingQuill.root.innerHTML);
                        syncWordingScrollClass();
                        if (formValidator) {
                            $('#st-wording').valid();
                        }
                    });
                }

                function syncWordingScrollClass() {
                    if (!wordingQuill) {
                        return;
                    }

                    var editor = wordingQuill.root;
                    var text = wordingQuill.getText().trim();
                    var hasContent = text.length > 0;
                    var contentHeight = editor.scrollHeight;
                    var targetHeight = wordingEditorMinHeight;

                    if (hasContent && contentHeight > wordingEditorMinHeight) {
                        targetHeight = Math.min(contentHeight, wordingEditorMaxHeight);
                    }

                    var shouldScroll = contentHeight > wordingEditorMaxHeight;

                    editor.style.minHeight = wordingEditorMinHeight + 'px';
                    editor.style.height = targetHeight + 'px';

                    editor.classList.toggle('customScroll', shouldScroll);
                    editor.classList.toggle('customScrollBar', shouldScroll);
                }

                function setWordingContent(html) {
                    var safeHtml = html || '';
                    if (wordingQuill) {
                        var decodedHtml = $('<textarea/>').html(safeHtml).text();
                        var htmlToUse = decodedHtml && decodedHtml.indexOf('<') !== -1 ? decodedHtml :
                            safeHtml;
                        wordingQuill.setContents([]);
                        wordingQuill.clipboard.dangerouslyPasteHTML(htmlToUse);
                        syncWordingScrollClass();
                    }
                    $('#st-wording').val(safeHtml);
                }

                initWordingEditor();
                setTimeout(syncWordingScrollClass, 0);

                if ($.fn.validate) {
                    formValidator = $('#slipTemplateForm').validate({
                        ignore: ':hidden:not(#st-wording)',
                        rules: {
                            schedule_title: {
                                required: true
                            },
                            status: {
                                required: true
                            }
                        },
                        messages: {
                            schedule_title: {
                                required: 'Schedule title is required.'
                            },
                            status: {
                                required: 'Status is required.'
                            },
                        },
                        errorClass: 'text-danger',
                        validClass: 'is-valid',
                        highlight: function(element) {
                            $(element).addClass('is-invalid').removeClass('is-valid');
                            if (element.id === 'st-wording') {
                                $('#st-wording-editor .ql-editor').addClass('is-invalid')
                                    .removeClass(
                                        'is-valid');
                            }
                        },
                        unhighlight: function(element) {
                            $(element).removeClass('is-invalid').addClass('is-valid');
                            if (element.id === 'st-wording') {
                                $('#st-wording-editor .ql-editor').removeClass('is-invalid')
                                    .addClass(
                                        'is-valid');
                            }
                        },
                        errorPlacement: function(error, element) {
                            if (element.attr('id') === 'st-wording') {
                                error.insertAfter('#st-wording-editor');
                                return;
                            }
                            error.insertAfter(element);
                        }
                    });
                }

                var table = $('#coversliplist').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: @json(route('bd.slip-template.data')),
                        type: 'GET',
                        data: function(d) {
                            d.treaty_type = @json($treaty_type ?? '');
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'schedule_title',
                            name: 'schedule_title',
                            defaultContent: '-'
                        },
                        {
                            data: 'class_group',
                            name: 'class_group',
                            defaultContent: '-'
                        },
                        {
                            data: 'class_name',
                            name: 'class_name',
                            defaultContent: '-'
                        },
                        {
                            data: 'description',
                            name: 'description',
                            defaultContent: '-'
                        },
                        {
                            data: 'status_badge',
                            name: 'status',
                            orderable: false,
                            searchable: false,
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
                    order: [
                        [1, 'asc']
                    ],
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    drawCallback: function(settings) {
                        var json = settings.json || {};
                        var meta = json.meta || {};
                        var visibleRows = this.api().rows({
                            page: 'current'
                        }).data().length;
                        var totalTemplates = meta.total_templates || json.recordsTotal || 0;
                        var filteredRows = json.recordsFiltered || totalTemplates;
                        var activeTemplates = meta.active_templates || 0;
                        var inactiveTemplates = meta.inactive_templates || 0;

                        $('#stat-total-headers').text(totalTemplates);
                        $('#stat-total-change').text('A: ' + activeTemplates + ' | I: ' + inactiveTemplates);
                        $('#stat-visible-rows').text(filteredRows);
                        $('#stat-class-groups').text(meta.class_names || 0);
                        $('#stat-types-breakdown').text((meta.class_groups || 0) +
                            ' class groups mapped');

                        $('#stat-business-types').text(meta.business_types || 0);
                        $('#stat-business-types-note').text('Across filtered templates');

                        $('#stat-covers-change').text(visibleRows + ' on page');
                    }
                });

                $('#newSlipClause').on('click', function() {
                    resetForm();
                });

                $('#st-class-group-code').on('change', function() {
                    populateClassOptions($(this).val() || '', '');
                });

                $(document).on('click', '.edit-slip-template', function() {
                    var $btn = $(this);
                    var $row = $btn.closest('tr');
                    if ($row.hasClass('child')) {
                        $row = $row.prev();
                    }

                    var rowData = table.row($row).data() || {};
                    var id = rowData.record_key || rowData.id || $btn.data('id') || '';
                    var scheduleTitle = rowData.schedule_title || $btn.data('schedule-title') || '';
                    var classGroupCode = resolveClassGroupCode(rowData, $btn);
                    var classCode = resolveClassCode(rowData, $btn, classGroupCode);
                    var description = rowData.description || $btn.data('description') || '';
                    var wording = rowData.wording || $btn.data('wording') || '';
                    var status = rowData.status || $btn.data('status') || 'A';
                    var typeOfBusValues = normalizeTypeOfBusValues(
                        rowData.type_of_bus_values || rowData.type_of_bus || $btn.data(
                            'type-of-bus') || '',
                        rowData.treaty_type || treatyType
                    );

                    $('#st-id').val(id);
                    $('#st-schedule-title').val(scheduleTitle);
                    $('#st-type-of-bus').val(typeOfBusValues).trigger('change');
                    $('#st-class-group-code').val(classGroupCode);
                    populateClassOptions(classGroupCode, classCode);
                    $('#st-class-code').val(classCode).trigger('change');
                    $('#st-description').val(description);
                    setWordingContent(wording);
                    $('#st-status').val(status);
                    $('#slipTemplateModalLabel').html(
                        '<i class="bx bx-edit-alt me-2"></i>Edit Schedule Slip Template');
                    $('#slipTemplateSaveBtn').text('Update');
                    isEditMode = true;

                    if (slipTemplateModal) {
                        slipTemplateModal.show();
                        setTimeout(function() {
                            setWordingContent(wording);
                        }, 50);
                    }
                });

                $(document).on('click', '.remove-slip-template', function() {
                    var $btn = $(this);
                    var id = $btn.data('id');
                    var title = $btn.data('schedule-title') || 'this template';

                    if (!id) {
                        showMessage('error', 'Template id is missing.');
                        return;
                    }

                    var proceedDelete = function() {
                        $.ajax({
                            url: deleteUrl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                _token: @json(csrf_token()),
                                id: id
                            },
                            success: function(resp) {
                                showMessage('success', resp.message ||
                                    'Slip template removed successfully.');
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                var message = (xhr.responseJSON && xhr.responseJSON
                                        .message) ?
                                    xhr.responseJSON.message :
                                    'Failed to remove slip template.';
                                showMessage('error', message);
                            }
                        });
                    };

                    if (typeof Swal !== 'undefined' && Swal.fire) {
                        Swal.fire({
                            title: 'Remove "' + title + '"?',
                            text: 'This action cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, remove',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                proceedDelete();
                            }
                        });
                        return;
                    }

                    if (window.confirm('Remove "' + title + '"?')) {
                        proceedDelete();
                    }
                });

                $('#slipTemplateForm').on('submit', function(e) {
                    e.preventDefault();

                    if (wordingQuill) {
                        $('#st-wording').val(wordingQuill.root.innerHTML);
                    }
                    if (formValidator && !$('#slipTemplateForm').valid()) {
                        return;
                    }

                    var $saveBtn = $('#slipTemplateSaveBtn');
                    $saveBtn.prop('disabled', true);

                    $.ajax({
                        url: saveUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: $(this).serialize(),
                        success: function(resp) {
                            showMessage('success', resp.message || (isEditMode ?
                                'Slip template updated successfully.' :
                                'Slip template created successfully.'));

                            if (slipTemplateModal) {
                                slipTemplateModal.hide();
                            }
                            table.ajax.reload(null, false);
                            resetForm();
                        },
                        error: function(xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message) ?
                                xhr.responseJSON.message :
                                'Failed to save slip template.';
                            showMessage('error', message);
                        },
                        complete: function() {
                            $saveBtn.prop('disabled', false);
                        }
                    });
                });

                $('#slipTemplateModal').on('hidden.bs.modal', function() {
                    resetForm();
                });

                $('#slipTemplateModal').on('shown.bs.modal', function() {
                    syncWordingScrollClass();
                });
            });
        })();
    </script>
@endpush
