<div class="modal fade effect-scale md-wrapper" id="sendBDEmailModal" tabindex="-1" aria-labelledby="sendBDEmailLabel"
    data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendBDEmailLabel">
                    <i class="bx bx-briefcase me-2"></i>
                    Business Development Notification
                    <span class="ms-1 modal-bd-title"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="bdNotificationForm" action="{{ route('bd.notification.send') }}" method="POST">
                @csrf
                {{-- <input type="hidden" name="opportunity_id" value="{{ $bdOpportunity->id }}">
                <input type="hidden" name="stage" value="{{ $bdOpportunity->stage }}">
                <input type="hidden" name="client_id" value="{{ $bdOpportunity->client_id }}">
                <input type="hidden" name="user_id" value="{{ auth()->id() }}"> --}}

                {{-- <input type="hidden" id="replyToId" name="reply_to_id">
                <input type="hidden" id="originalMessageId" name="original_message_id">
                <input type="hidden" name="claim_no" value="{{ $ClaimRegister->claim_no }}">
                <input type="hidden" name="customer_id" value="{{ $ClaimRegister->customer_id }}">
                <input type="hidden" name="claim_notice_file" id="claimNoticeFile">
                <input type="hidden" name="debit_note_file" id="debitNoteFile">
                <input type="hidden" name="partner_email" id="partnerToEmail"> --}}


                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-12">
                            <!-- Navigation Tabs -->
                            <div class="card-header bg-light border-bottom">
                                <ul class="nav nav-tabs card-header-tabs" id="emailTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="compose-tab" data-bs-toggle="tab"
                                            data-bs-target="#compose" type="button" role="tab">
                                            <i class="bx bx-envelope me-2 fs-15"
                                                style="vertical-align: middle"></i>Compose
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="replies-tab" data-bs-toggle="tab"
                                            data-bs-target="#replies" type="button" role="tab">
                                            <i class="bx bx-reply me-2 fs-15" style="vertical-align: middle"></i>Reply
                                            to
                                            Messages
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content" id="bdEmailTabContent">
                                <div class="tab-pane fade show active" id="compose" role="tabpanel">
                                    @include('Bd_views.intermediaries.partials.bd.reinsurers.compose-form')
                                </div>

                                <div class="tab-pane fade" id="replies" role="tabpanel">
                                    @include('Bd_views.intermediaries.partials.bd.reinsurers.messages-list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default btn-sm" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-paper-plane me-1"></i>Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal effect-scale md-wrapper" id="confirmationModal" tabindex="-1"
    aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white text-center" id="confirmationModalLabel">
                    <i class="bx bx-send me-2"></i>Confirm Email Send
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Please review your email details before sending:</p>

                <div class="confirmation-details p-3 bg-light rounded">
                    <div class="row mb-2">
                        <div class="col-3 fw-bold">To:</div>
                        <div class="col-9">
                            <div id="confirmTo"
                                style="white-space: pre-wrap; margin: 0; font-family: inherit; overflow-wrap: break-word;">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-3 fw-bold">CC:</div>
                        <div class="col-9" id="confirmCC"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-3 fw-bold">BCC:</div>
                        <div class="col-9" id="confirmBCC"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-3 fw-bold">Subject:</div>
                        <div class="col-9">
                            <div id="confirmSubject"></div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-3 fw-bold">Priority:</div>
                        <div class="col-9" id="confirmPriority"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-3 fw-bold">Category:</div>
                        <div class="col-9" id="confirmCategory"></div>
                    </div>
                    <div class="row">
                        <div class="col-3 fw-bold">Attachments:</div>
                        <div class="col-9" id="confirmAttachments"></div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">Message Preview:</label>
                    <div class="border p-3 bg-light rounded" style="max-height: 200px; overflow-y: auto;">
                        <pre id="confirmMessage" style="white-space: pre-wrap; margin: 0; font-family: inherit;"></pre>
                    </div>
                </div>

                <div class="mt-3" id="replyWarning" style="display: none;">
                    <div class="alert alert-warning">
                        <i class="bx bx-warning me-2"></i>
                        <strong>Reply Mode:</strong> This email is a reply to an existing message.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="cancelEmailConfirmation">
                    <i class="bx bx-x me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmSendBtn">
                    <i class="bx bx-paper-plane me-1"></i>Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #bdEmailTabContent {
        min-height: 50vh;
    }

    #bdEmailTabContent .tab-pane {
        padding-top: 1rem;
        border: none;
    }
</style>

@push('script')
    <script>
        $(document).ready(function() {
            let replyState = {
                isReply: false,
                originalMessage: null,
                formData: {}
            };

            initializeComposeForm();

            $('#generateRefBtn').on('click', generateReference);
            $('#toggleEmailBodyBtn').on('click', toggleEmailBody);
            // $('#clearFormBtn').on('click', startNewEmail);
            $('#insertTemplateBtn').on('click', insertTemplate);
            $('#saveDraftBtn').on('click', saveDraft);
            $('#emailForm').on('submit', handleEmailSubmit);
            $('#confirmSendBtn').on('click', confirmAndSendEmail);
            $('#cancelEmailConfirmation').on('click', handleCancelConfirmation);

            $('#emailForm').on('change input', trackFormChanges);

            function initializeComposeForm() {
                let $bdNotificationForm = ("#sendBDEmailModal #bdNotificationForm")
                captureFormState();
            }

            function handleCancelConfirmation() {
                $('#sendReinDocumentEmail').modal('show');
            }

            function showConfirmationModal() {
                const toEmails = $('#toEmail').val() || '';
                const ccEmails = $('#ccEmail').val() || [];
                const bccEmails = $('#bccEmail').val() || [];
                const subject = $('#subject').val();
                const message = $('#message').val();
                const priority = $('#priority option:selected').text();
                const category = $('#category option:selected').text();

                $('#confirmTo').text(toEmails ? toEmails : 'None');
                $('#confirmCC').text(ccEmails.length ? ccEmails.join(', ') : 'None');
                $('#confirmBCC').text(bccEmails.length ? bccEmails.join(', ') : 'None');
                $('#confirmSubject').text(subject);
                $('#confirmPriority').text(priority);
                $('#confirmCategory').text(category);
                $('#confirmMessage').text(message);
                $('#confirmAttachments').text($('#fileCount').text());

                if (replyState.isReply) {
                    $('#replyWarning').show();
                } else {
                    $('#replyWarning').hide();
                }

                $('#sendReinDocumentEmail').modal('hide');
                $('#confirmationModal').modal('show');
            }

            function populateReplyForm(message) {
                try {
                    if (!confirm('This will switch to reply mode. Continue?')) {
                        return;
                    }

                    $('#replyToId').val(message.id);
                    $('#originalMessageId').val(message.id);

                    $('#toEmail').val(message.from);

                    let subject = message.subject;
                    if (!subject.toLowerCase().startsWith('re:')) {
                        subject = 'RE: ' + subject;
                    }
                    $('#subject').val(subject);

                    if (message.category) {
                        $('#category').val(message.category).trigger('change');
                    }
                    if (message.priority) {
                        $('#priority').val(message.priority).trigger('change');
                    }
                    if (message.reference) {
                        $('#reference').val(message.reference);
                    }

                    populateOriginalMessageDetails(message);
                    createThreadMessage(message);
                } catch (error) {
                    console.error('Error populating reply form:', error);
                    throw error;
                }
            }

            function populateOriginalMessageDetails(message) {
                $('#originalFrom').text(message.fromName || message.from);
                $('#originalSent').text(message.date);
                $('#originalTo').text(message.from);
                $('#originalSubject').text(message.subject);
            }

            function createThreadMessage(message) {
                const threadContent = `
                        --- Original Message ---
                        From: ${message.fromName || message.from} <${message.from}>
                        Date: ${message.date}
                        Subject: ${message.subject}
                        ${message.reference ? 'Reference: ' + message.reference : ''}

                        ${message.preview || 'Original message content...'}
                `;

                $('#threadMessage').val(threadContent);
            }

            function updateUIForReplyMode(message) {
                $('#composeTitle').text('Reply to Message');
                $('#clearFormBtn').show();

                $('#emailForm#bdNotificationForm').addClass('reply-context-highlight');
            }

            function generateReference() {
                try {
                    const category = $('#category').val().toUpperCase().substring(0, 3);
                    const year = new Date().getFullYear();
                    const random = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
                    const reference = `${category}-${year}-${random}`;

                    $('#reference').val(reference);
                    toastr.success('Reference number generated');
                } catch (error) {
                    toastr.error('Error generating reference number');
                }
            }

            function toggleEmailBody() {
                const emailBody = document.getElementById('emailBody');
                if (emailBody.classList.contains('hidden')) {
                    emailBody.classList.remove('hidden');
                    $('#toggleEmailBodyBtn').html('<i class="bx bx-chevron-up me-1"></i>Hide Original Email');
                    $('#message').attr('rows', 5);
                } else {
                    emailBody.classList.add('hidden');
                    $('#toggleEmailBodyBtn').html('<i class="bx bx-chevron-down me-1"></i>Show Original Email');
                    $('#message').attr('rows', 21);
                }
            }

            function insertTemplate() {
                const templates = {
                    acknowledgment: "Thank you for your message. We have received your request and will respond within 24 hours.",
                    followup: "I hope this email finds you well. I wanted to follow up on our previous conversation regarding...",
                    closing: "Thank you for your attention to this matter. Please don't hesitate to contact me if you need any clarification.",
                    urgent: "This is an urgent matter that requires immediate attention. Please review and respond at your earliest convenience."
                };

                const templateKeys = Object.keys(templates);
                const selectedTemplate = prompt('Select template:\n' + templateKeys.map((k, i) => `${i + 1}. ${k}`)
                    .join('\n'));

                if (selectedTemplate && templateKeys[selectedTemplate - 1]) {
                    const template = templates[templateKeys[selectedTemplate - 1]];
                    const currentMessage = $('#message').val();
                    $('#message').val(currentMessage + '\n\n' + template);
                    toastr.success('Template inserted');
                }
            }

            function saveDraft() {
                try {
                    if (!confirm('Save current email as draft?')) {
                        return;
                    }

                    const formData = new FormData(document.getElementById('emailForm'));
                    formData.append('save_as_draft', '1');

                    // Add reply context if available
                    if (replyState.isReply && replyState.originalMessage) {
                        formData.append('reply_context', JSON.stringify(replyState.originalMessage));
                    }

                    toastr.success('Draft saved successfully!');

                    //{{-- $.ajax({
                    //     url: '{{ route('emails.draft') }}',
                    //     method: 'POST',
                    //     data: formData,
                    //     processData: false,
                    //     contentType: false,
                    //     success: function(response) {
                    //         console.log('Draft saved:', response);
                    //         toastr.success('Draft saved successfully!');
                    //     },
                    //     error: function(xhr, status, error) {
                    //         console.error('Draft save failed:', error);
                    //         toastr.error('Failed to save draft. Please try again.');
                    //     }
                    // --}}});

                } catch (error) {
                    toastr.error('Error saving draft: ' + error.message);
                }
            }

            function handleEmailSubmit(e) {
                e.preventDefault();
                try {
                    if (!validateEmailSelection()) {
                        toastr.error('Please fix the validation errors before sending');
                        return;
                    }

                    if (!validateForm()) {
                        toastr.error('Please fix the validation errors before sending');
                        return;
                    }

                    showConfirmationModal();
                } catch (error) {
                    toastr.error('Error sending email: ' + error.message);
                }
            }

            function confirmAndSendEmail() {
                try {
                    const formData = new FormData(document.getElementById('emailForm'));

                    const claimNoticeFile = $("#claimNoticeFile").val();
                    const debitNoteFile = $("#debitNoteFile").val();
                    const isReply = $("#replyToId").val();

                    const claimFiles = []

                    const filesToFetch = [];

                    if (claimNoticeFile) {
                        filesToFetch.push({
                            name: 'claim_notice_file',
                            url: claimNoticeFile
                        });
                    }

                    if (debitNoteFile) {
                        filesToFetch.push({
                            name: 'debit_note_file',
                            url: debitNoteFile
                        });
                    }

                    if (claimFiles && Array.isArray(claimFiles) && claimFiles?.length > 0) {
                        claimFiles.forEach(function(claimFile, index) {
                            if (claimFile && claimFile.file_path) {
                                filesToFetch.push({
                                    name: claimFile.file || `claim_file_${index}`,
                                    url: claimFile.file_path
                                });
                            }
                        });
                    }

                    const attachmentPromises = filesToFetch.map(function(file, index) {
                        return fetch(file.url)
                            .then(function(response) {
                                if (!response.ok) {
                                    throw new Error(`Failed to fetch ${file.name}`);
                                }
                                return response.blob();
                            })
                            .then(function(blob) {
                                let filename;
                                if (file.name === 'claim_notice_file' || file.name ===
                                    'debit_note_file') {
                                    filename = `${file.name}_${Date.now()}.pdf`;
                                } else {
                                    filename = file.name;
                                }

                                const result = new File([blob], filename);
                                if (filename) {
                                    return {
                                        file: result,
                                        name: file.name,
                                        originalName: filename
                                    };
                                }

                            })
                            .catch(function(error) {
                                console.error(`Error fetching ${file.name}:`, error);
                                return null;
                            });
                    });

                    Promise.all(attachmentPromises)
                        .then(function(attachments) {
                            const validAttachments = attachments.filter(function(attachment) {
                                return attachment !== null;
                            });

                            if (isReply) {
                                formData.append('attachments[]', '');
                            } else {
                                validAttachments.forEach((attachment, index) => {
                                    formData.append(`attachments[${index}]`, attachment.file);
                                });
                            }

                            $('#sendReinNotification').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-1"></span>Sending...');
                            $('#confirmSendBtn').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-1"></span>Sending...');

                            $.ajax({
                                url: '{{ route('emails.send_claim_reinsurer_email') }}',
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    toastr.success(response.message || 'Email sent successfully!');
                                    $('#confirmationModal').modal('hide');
                                    resetDocumentForm();
                                    $("#sendReinDocumentEmail").modal('hide');
                                },
                                error: function(xhr, status, error) {
                                    toastr.error('Failed to send email. Please try again.');
                                },
                                complete: function() {
                                    $('#sendReinNotification').prop('disabled', false).html(
                                        '<i class="bx bx-paper-plane me-1"></i>Send Email');
                                    $('#confirmSendBtn').prop('disabled', false).html(
                                        '<i class="bx bx-paper-plane me-1"></i>Send Email');
                                }
                            });
                        })
                        .catch(function(error) {
                            toastr.error('Error processing attachments: ' + error.message);
                            $('#sendReinNotification').prop('disabled', false).html(
                                '<i class="bx bx-paper-plane me-1"></i>Send Email');
                        });

                } catch (error) {
                    toastr.error('Error sending email: ' + error.message);
                    $('#sendReinNotification').prop('disabled', false).html(
                        '<i class="bx bx-paper-plane me-1"></i>Send Email');
                }
            }

            function resetDocumentForm() {
                $('#emailForm#bdNotificationForm')[0].reset();

                replyState.isReply = false;
                replyState.originalMessage = null;

                $('#replyToId').val('');
                $('#originalMessageId').val('');

                $('#composeTitle').text('Compose New Email');
                $('#clearFormBtn').hide();
                $('#emailForm').removeClass('reply-context-highlight');

                $('#emailBody').addClass('hidden');

                $('#bdNotificationForm #message').val(@json($defaultMessage ?? ''));
                $('#bdNotificationForm #subject').val(@json(is_array($claimSubject ?? '') ? implode(' ', $claimSubject) : $claimSubject ?? ''));
                $('#bdNotificationForm #toEmail').val('{{ $reinserEmail ?? '' }}');

                $('#priority').val('normal').trigger('change');
                $('#category').val('general').trigger('change');

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            function validateForm() {
                let isValid = true;

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const requiredFields = {
                    'toEmail': 'To email is required',
                    'subject': 'Subject is required',
                    'message': 'Message is required'
                };

                Object.keys(requiredFields).forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        field.nextElementSibling.textContent = requiredFields[fieldId];
                        isValid = false;
                    }
                });

                return isValid;
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function trackFormChanges() {
                replyState.formData = {
                    isReply: $('#replyToId').val() ? true : false,
                    originalMessageId: $('#originalMessageId').val(),
                    toEmail: $('#toEmail').val(),
                    subject: $('#subject').val(),
                    message: $('#message').val(),
                    category: $('#category').val(),
                    priority: $('#priority').val(),
                    reference: $('#reference').val()
                };
            }

            function captureFormState() {
                replyState.formData = {};
                trackFormChanges();
            }

            $('#bdNotificationForm #tocontacts, #bdNotificationForm #ccEmail, #bdNotificationForm #bccEmail').on(
                'change',
                function() {
                    const changedDropdown = $(this);
                    const dropdownType = changedDropdown.attr('id');
                    const selectedEmails = changedDropdown.val() || [];

                    const toEmails = $('#tocontacts').val() || [];
                    const ccEmails = $('#ccEmail').val() || [];
                    const bccEmails = $('#bccEmail').val() || [];

                    const allEmails = [...toEmails, ...ccEmails, ...bccEmails];
                    const duplicates = findDuplicateEmails(allEmails);

                    if (duplicates.length > 0) {
                        showDuplicateWarning(duplicates, dropdownType);
                        resolveDuplicates(dropdownType, selectedEmails);
                    }

                    updateToEmailField();
                });

            function findDuplicateEmails(emailArray) {
                const emailCounts = {};
                const duplicates = [];

                emailArray.forEach(email => {
                    if (email) {
                        emailCounts[email] = (emailCounts[email] || 0) + 1;
                        if (emailCounts[email] === 2) {
                            duplicates.push(email);
                        }
                    }
                });

                return duplicates;
            }

            function showDuplicateWarning(duplicates, changedField) {
                const duplicateList = duplicates.join(', ');
                const message =
                    `The email(s) "${duplicateList}" are selected in multiple fields. They will be automatically moved to avoid duplicates.`;
                toastr.warning(message, {
                    timeOut: 9000
                });
            }

            function resolveDuplicates(changedField, newlySelectedEmails) {
                const toEmails = $('#bdNotificationForm #tocontacts').val() || [];
                const ccEmails = $('#bdNotificationForm #ccEmail').val() || [];
                const bccEmails = $('#bdNotificationForm #bccEmail').val() || [];

                newlySelectedEmails.forEach(email => {
                    if (changedField === 'tocontacts') {
                        removeEmailFromDropdown('#ccEmail', email);
                        removeEmailFromDropdown('#bccEmail', email);
                    } else if (changedField === 'ccEmail') {
                        if (!toEmails.includes(email)) {
                            removeEmailFromDropdown('#bccEmail', email);
                        } else {
                            removeEmailFromDropdown('#ccEmail', email);
                        }
                    } else if (changedField === 'bccEmail') {
                        if (toEmails.includes(email) || ccEmails.includes(email)) {
                            removeEmailFromDropdown('#bccEmail', email);
                        }
                    }
                });
            }

            function removeEmailFromDropdown(dropdownSelector, emailToRemove) {
                const $dropdown = $(dropdownSelector);
                const currentValues = $dropdown.val() || [];
                const newValues = currentValues.filter(email => email !== emailToRemove);

                if (currentValues.length !== newValues.length) {
                    $dropdown.val(newValues).trigger('change.select2');
                }
            }

            function updateToEmailField() {
                const toEmails = $('#bdNotificationForm #tocontacts').val() || [];
                // $('#bdNotificationForm #toEmail').val(toEmails.join(', '));
            }

            function validateEmailSelection() {
                const toEmails = $('#bdNotificationForm #tocontacts').val() || [];
                const ccEmails = $('#bdNotificationForm #ccEmail').val() || [];
                const bccEmails = $('#bdNotificationForm #bccEmail').val() || [];

                if (toEmails.length === 0 && ccEmails.length === 0 && bccEmails.length === 0) {
                    $("#tocontacts").after(
                        '<div class="error-message" style="color: red; font-size: 12px; margin-top: 5px;">Please select at least one recipient</div>'
                    );
                    return false;
                }

                const allEmails = [...toEmails, ...ccEmails, ...bccEmails];
                const duplicates = findDuplicateEmails(allEmails);

                if (duplicates.length > 0) {
                    $("#tocontacts").after(
                        '<div class="error-message" style="color: red; font-size: 12px; margin-top: 5px;">Duplicate email addresses found: ' +
                        duplicates.join(', ') + '</div>');
                    resolveDuplicates('tocontacts', toEmails);
                }

                return true;
            }
        });
    </script>
@endpush
