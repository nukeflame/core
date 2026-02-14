@extends('layouts.app')

@section('content')
    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#acknowledgement-modal">
        <i class="bx bx-envelope me-1"></i> Send Claim Notification
    </button> --}}
    <div id="acknowledgement-section">
        <div class="card">
            <form id="acknowledgementForm" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <input type="hidden" name="claim_no" value="4">
                <input type="hidden" name="endorsement_no" value="3">
                <input type="hidden" name="id" id="acknowledgement_id" value="4">
                <input type="hidden" name="prospect_id" id="prospect_id" value="{{ $opportunities[0]->opportunity_id }}">
                <input type="hidden" name="prospect_status" value="3">
                <input type="hidden" name="reinsurer_id" id="reinsurer_id" value="{{ $opportunities[0]->customer_id }}">

                <div class="card-header bg-primary text-white">
                    <h5 class="card-title text-center">Document Checklist</h5>
                </div>

                <div class="card-body">
                    <p><strong>Select the dates when the documents were received</strong></p>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <x-SearchableSelect name="reinsurer_emails[]" id="reinsurer_emails" req="required"
                                inputLabel="Cedant Email(The first one is the main contact)" multiple>
                            </x-SearchableSelect>
                        </div>
                        <div class="mb-3 col-md-4">
                            <x-SearchableSelect name="selected_dept_user_email[dept_user_email][]" id="dept_user_email"
                                req="required" inputLabel="CC Department Users" multiple>
                            </x-SearchableSelect>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-10">
                            <h6><b>Received Documents</b></h6>
                            <table class="table text-nowrap table-bordered table-striped" id="received-documents">
                                <thead>
                                    <th>Document</th>
                                    <th>Date Received</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <hr />
                            <h6><b>Missing Documents</b></h6>
                            <table class="table text-nowrap table-bordered table-striped" id="missing-documents">
                                <thead>
                                    <th>Document</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <hr />
                            <h6><b>New Documents</b></h6>
                            <table class="table text-nowrap table-bordered table-striped" id="new-documents">
                                <thead>
                                    <th>Select</th>
                                    <th>Document</th>
                                    <th>Date Received</th>
                                    <th>Attach</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="button" id="ack-save-btn"
                        class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for SearchableSelect
            $('#reinsurer_emails').select2({
                placeholder: "Select contacts",
                allowClear: true
            });
            $('#dept_user_email').select2({
                placeholder: "Select CC users",
                allowClear: true
            });

            // Custom validation method for date requirement
            $.validator.addMethod("dateRequiredIfChecked", function(value, element) {
                var $row = $(element).closest('tr');
                var checkbox = $row.find('input[type="checkbox"]');
                var hasFile = $row.find('.file-input').get(0).files.length > 0 || $row.data('has-file') === true;
                return checkbox.is(':checked') && hasFile ? value.trim() !== "" : true;
            }, "Please enter the date when the document is received.");

            // Update document tables
            function updateDocumentTables() {
                $('#received-documents tbody').empty();
                $('#missing-documents tbody').empty();

                $('#new-documents tbody tr').each(function() {
                    var $row = $(this);
                    var docId = $row.data('doc-id');
                    var docName = $row.find('td:eq(1)').text().replace(/<[^>]+>/g, '');
                    var isChecked = $row.find('.check-input').is(':checked');
                    var hasFile = $row.find('.file-input').get(0).files.length > 0 || $row.data('has-file') === true;
                    var dateReceived = $row.find('.ack_date').val();
                    var isPreExisting = $row.data('has-file') === true && $row.find('.file-input').get(0).files.length === 0;

                    // Update data-has-file and input states
                    if (isChecked && $row.find('.file-input').get(0).files.length > 0 && !isPreExisting) {
                        $row.data('has-file', true);
                        $row.find('.file-input').prop('disabled', true);
                    } else if (!isChecked && !isPreExisting) {
                        $row.data('has-file', false);
                        $row.find('.file-input').prop('disabled', false).val('');
                        $row.find('.ack_date').val(''); 
                    }

                    if (isChecked) {
                        var rowHtml = `<tr data-doc-id="${docId}">
                            <td>${docName}</td>
                            <td>${dateReceived || ''}</td>
                        </tr>`;
                        if (hasFile) {
                            $('#received-documents tbody').append(rowHtml);
                        } else {
                            $('#missing-documents tbody').append(rowHtml);
                        }
                    }
                });
            }

            // Event listeners for document table updates
            $('#new-documents').on('change', '.check-input, .file-input, .ack_date', function() {
                updateDocumentTables();
            });

            // Load contacts via AJAX
            $.ajax({
                type: "GET",
                data: {
                    'customer_id': $('#reinsurer_id').val()
                },
                url: "{{ route('get_cedant_contact') }}",
                success: function(resp) {
                    console.log('AJAX Response:', resp);
                    $('#reinsurer_emails').empty();
                    let contact_persons = resp.contact_persons || (resp.customers && resp.customers[0]?.contact_persons) || [];
                    if (Array.isArray(contact_persons)) {
                        contact_persons.forEach(contact => {
                            if (contact.contact_email) {
                                $('#reinsurer_emails').append(
                                    `<option value="${contact.contact_email}" data-name="${contact.contact_name || contact.contact_email}">${contact.contact_name || contact.contact_email} (${contact.contact_email})</option>`
                                );
                            }
                        });
                        $('#reinsurer_emails').trigger('change');
                    } else {
                        console.warn('No contact_persons found:', contact_persons);
                        
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.status, error, xhr.responseText);
                    toastr.error("Failed to load contacts: " + error);
                    $('#reinsurer_emails').empty()
                    $('#reinsurer_emails').trigger('change');
                }
            });

            // Load documents via AJAX
            const stage_id = 5;
            const category_type = 2;
            if (stage_id == 5 && category_type == 2) {
                $.ajax({
                    type: "GET",
                    data: {
                        'pipeline': 2,
                        'prospect': $('#prospect_id').val(),
                        'stage': 4,
                        'divisions': null,
                        'category_type': 2,
                        'type_of_business': 'TPR',
                    },
                    url: "{{ route('get_stage_documents') }}",
                    success: function(resp) {
                        populateDocuments(resp);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        toastr.error("Failed to load documents");
                    }
                });
            }
            var baseAssetUrl = "{{ Storage::disk('s3')->url('uploads/cedant_docs') }}";
            // Populate New Documents table
            function populateDocuments(resp) {
                if (resp.status == 1) {
                    $('#new-documents tbody').empty();
                    $('#dept_user_email').empty();
                    let validationRules = {
                        "missing_docs[]": {
                            required: true
                        },
                        claim_no: {
                            required: true
                        },
                        "reinsurer_emails[]": {
                            required: true
                        }
                    };
                    const existingCheckbox = resp.prosp_doc || [];

                    // Populate department user emails
                    if (resp.users && Array.isArray(resp.users)) {
                        resp.users.forEach(user => {
                            const displayText = user.name || user.email;
                            $('#dept_user_email').append(
                                `<option value="${user.email}" selected>${displayText}</option>`
                            );
                        });
                        $('#dept_user_email').trigger('change');
                    }

                    $.each(resp.docs, function() {
                        if (this.checkbox_doc == "2") {
                            const isMandatory = this.mandatory == 'Y';
                            let isChecked = false;
                            let isDisabled = false;
                            let fileUrl = '';
                            let receivedDate = '';

                            existingCheckbox.forEach((existingFile) => {
                                if (this.doc_type === existingFile.description) {
                                    isChecked = true;
                                    isDisabled = existingFile.file !== null;
                                    fileUrl = existingFile.file ?
                                        `${baseAssetUrl}/${existingFile.file}` : '';
                                    receivedDate = existingFile.received_date || '';
                                }
                            });

                            const rowHtml = `
                                <tr style="text-align: left; vertical-align: middle; border: 1px solid #ddd" data-doc-id="${this.id}" data-has-file="${isDisabled}">
                                    <td>
                                        <input type="checkbox" class="check-input ml-3" name="missing_docs[]" id="ack_doc_${this.id}" value="${this.doc_type}" ${isMandatory ? 'required' : ''} ${isChecked ? 'checked' : ''} ${isDisabled ? 'disabled' : ''}>
                                    </td>
                                    <td>${this.doc_type} ${fileUrl ? `<a href="${fileUrl}" target="_blank"><i class="bx bx-show"></i></a>` : ''}</td>
                                    <td>
                                        <input type="date" class="form-control ack_date" name="date_received[${this.id}]" id="date_received_${this.id}" value="${receivedDate}">
                                    </td>
                                    <td>
                                        <input type="file" class="form-control file-input" id="file_${this.id}" name="document_file_email_attachment[]" accept=".pdf, .doc, .docx, .png, .jpg" ${isDisabled ? 'disabled' : ''}>
                                        <input type="hidden" name="document_name_email_attachment[]" value="${this.doc_type}">
                                        <input type="hidden" name="cedant_checkbox_docs_id[]" value="${this.id}" ${isDisabled ? '' : 'disabled'}>
                                    </td>
                                </tr>`;
                            $('#new-documents tbody').append(rowHtml);

                            validationRules[`date_received[${this.id}]`] = {
                                dateRequiredIfChecked: true
                            };
                        }
                    });

                    // Initialize form validation
                    $("#acknowledgementForm").validate({
                        errorClass: "errorClass",
                        rules: validationRules,
                        submitHandler: function(form) {
                            $('#ack-save-btn').prop('disabled', true).text('Saving...');
                            let url = "{{ route('docs-setup.saveTenderDocs') }}";
                            let formData = new FormData(form);

                            // Append additional fields
                            formData.append('email_dated', new Date().toISOString().split('T')[0]);
                            formData.append('commence_year', new Date().getFullYear());
                            formData.append('contact_name', $('#reinsurer_emails option:selected').first().data('name') || 'Valued Customer');
                            formData.append('reinsurer_id', $('#reinsurer_id').val() || '');

                            // Append reinsurer emails
                            const reinsurerEmails = $('#reinsurer_emails').val() || [];
                            reinsurerEmails.forEach((email, index) => {
                                formData.append(`reinsurer_emails[${index}]`, email);
                            });

                            // Build reinsurers and received_docs_checkboxes arrays
                            let documents = [];
                            let receivedDocs = [];
                            let missingDocs = [];

                            $('#new-documents tbody tr').each(function() {
                                var $row = $(this);
                                var docId = $row.data('doc-id');
                                var docName = $row.find('td:eq(1)').text().replace(/<[^>]+>/g, '');
                                var isChecked = $row.find('.check-input').is(':checked');
                                var fileInput = $row.find('.file-input').get(0);
                                var dateReceived = $row.find('.ack_date').val();

                                if (isChecked) {
                                    let doc = {
                                        title: docName,
                                        received_date: dateReceived || null
                                    };
                                    let hasFile = fileInput.files.length > 0 || $row.data('has-file') === true;

                                    if (hasFile) {
                                        receivedDocs.push(docName);
                                        if (fileInput.files.length > 0) {
                                            doc.file = fileInput.files[0];
                                        }
                                    } else {
                                        missingDocs.push(docName);
                                    }
                                    documents.push(doc);
                                }
                            });

                            // Append reinsurers array
                            formData.append('reinsurers[0][reinsurer_id]', $('#reinsurer_id').val());
                            documents.forEach((doc, index) => {
                                formData.append(`reinsurers[0][documents][${index}][title]`, doc.title);
                                if (doc.file) {
                                    formData.append(`reinsurers[0][documents][${index}][file]`, doc.file);
                                }
                                if (doc.received_date) {
                                    formData.append(`reinsurers[0][documents][${index}][received_date]`, doc.received_date);
                                }
                            });

                           
                            receivedDocs.forEach((docName, index) => {
                                formData.append(`received_docs_checkboxes[${index}]`, docName);
                            });
                            missingDocs.forEach((docName, index) => {
                                formData.append(`missing_docs[${index}]`, docName);
                            });

                            // Clear previous missing_docs 
                            formData.delete('missing_docs[]');

                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                                },
                                body: formData,
                            })
                            .then(response => response.json())
                            .then(data => {
                                $('#acknowledgement-modal').modal('hide');
                                if (data.status === 201) {
                                    toastr.success("Document saved successfully");
                                    setTimeout(() => {
                                        location.reload();
                                    }, 3000);
                                } else if (data.status === 301) {
                                    toastr.success("Document saved successfully");
                                    setTimeout(() => {
                                        window.location.href = '{{ route('treaty.pipeline.view') }}';
                                    }, 1000);
                                } else if (data.status === 422) {
                                    showServerSideValidationErrors(data.errors);
                                    $('#ack-save-btn').prop('disabled', false).text('Submit');
                                } else {
                                    toastr.error(data.error || "Failed to save document");
                                    $('#ack-save-btn').prop('disabled', false).text('Submit');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                toastr.error("Failed to save document");
                                $('#ack-save-btn').prop('disabled', false).text('Submit');
                            });
                        }
                    });

                    updateDocumentTables();
                } else {
                    toastr.error("Failed to load documents");
                }
            }

            // Trigger form submission on button click
            $('#ack-save-btn').click(function() {
                $('#acknowledgementForm').submit();
            });
            updateDocumentTables();
        });
    </script>
@endpush
