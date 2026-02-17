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
                <input type="hidden" name="category_templates" class="category_templates" id="categoryTemplates">
                <input type="hidden" name="opportunity_id" class="opportunity_id">
                <input type="hidden" name="is_reply" class="is_reply" id="isReply" value="0">
                <input type="hidden" name="customer_id" class="customer_id">

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
                                            to Messages
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
                                    @include('business_development.intermediaries.partials.bd.reinsurers.compose-form')
                                </div>

                                <div class="tab-pane fade" id="replies" role="tabpanel">
                                    @include('business_development.intermediaries.partials.bd.reinsurers.messages-list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div>
                        <button type="button" class="btn btn-outline-dark btn-sm" id="resetMailForm">
                            Reset <i class="bx bx-reset me-1"></i>
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-default btn-sm" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-paper-plane me-1"></i>Send Notification
                        </button>
                    </div>
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
                        <div class="col-3 fw-bold">Contacts:</div>
                        <div class="col-9">
                            <div id="contactsTo"
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
                    <div class="row mb-2">
                        <div class="col-3 fw-bold">Reference:</div>
                        <div class="col-9" id="confirmRef"></div>
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

    .invalid-feedback {
        font-size: 13px;
    }
</style>

@push('script')
    <script>
        $(document).ready(function() {
            const EmailState = {
                isReply: false,
                originalMessage: null,
                formData: {},

                reset() {
                    this.isReply = false;
                    this.originalMessage = null;
                    this.formData = {};
                },

                captureFormData() {
                    this.formData = {
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
            };

            const CONFIG = {
                selectors: {
                    form: '#bdNotificationForm',
                    modal: '#sendBDEmailModal',
                    confirmModal: '#confirmationModal',
                    toContacts: '#toContacts',
                    ccEmail: '#ccEmail',
                    bccEmail: '#bccEmail',
                    toEmail: '#toEmail',
                    subject: '#subject',
                    message: '#message',
                    category: '#category',
                    priority: '#priority',
                    reference: '#reference'
                },
                templates: {
                    acknowledgment: "Thank you for your message. We have received your request and will respond within 24 hours.",
                    followup: "I hope this email finds you well. I wanted to follow up on our previous conversation regarding...",
                    closing: "Thank you for your attention to this matter. Please don't hesitate to contact me if you need any clarification.",
                    urgent: "This is an urgent matter that requires immediate attention. Please review and respond at your earliest convenience."
                }
            };

            init();

            function init() {
                EmailState.captureFormData();
                bindEvents();
            }

            function bindEvents() {
                $("#resetMailForm").hide();

                $('#generateRefBtn').on('click', generateReference);
                $('#toggleEmailBodyBtn').on('click', toggleEmailBody);
                $('#resetMailForm').on('click', resetMailForm);
                $('#insertTemplateBtn').on('click', insertTemplate);
                $('#saveDraftBtn').on('click', saveDraft);
                $('#confirmSendBtn').on('click', confirmAndSendEmail);
                $('#cancelEmailConfirmation').on('click', () => {
                    $(CONFIG.selectors.modal).modal('show');
                });

                $(CONFIG.selectors.form).on('submit', handleEmailSubmit);
                $(CONFIG.selectors.form).on('change input', () => EmailState.captureFormData());
                $(CONFIG.selectors.category).on('change', handleCategoryChange);

                $(`${CONFIG.selectors.toContacts}, ${CONFIG.selectors.ccEmail}, ${CONFIG.selectors.bccEmail}`)
                    .on('change', handleEmailFieldChange);
            }

            function handleCategoryChange() {
                const templates = $("#categoryTemplates").val();
                const category = $(this).val();

                if (!templates) return;

                try {
                    const templateData = JSON.parse(templates);
                    const template = templateData[category];

                    if (template) {
                        $(CONFIG.selectors.subject).val(template.subject);
                        $(CONFIG.selectors.message).val(template.message);
                    }
                } catch (error) {
                    toastr.error('Failed to load template');
                }
            }

            function generateReference() {
                try {
                    const category = $(CONFIG.selectors.category).val().toUpperCase().substring(0, 3);
                    const year = new Date().getFullYear();
                    const random = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
                    const reference = `${category}-${year}-${random}`;

                    $(CONFIG.selectors.reference).val(reference);
                } catch (error) {
                    toastr.error('Error generating reference number');
                }
            }

            function toggleEmailBody() {
                const $emailBody = $('#emailBody');
                const $toggleBtn = $('#toggleEmailBodyBtn');
                const $message = $(CONFIG.selectors.message);

                if ($emailBody.hasClass('hidden')) {
                    $emailBody.removeClass('hidden');
                    $toggleBtn.html('<i class="bx bx-chevron-up me-1"></i>Hide Original Email');
                    $message.attr('rows', 15);
                } else {
                    $emailBody.addClass('hidden');
                    $toggleBtn.html('<i class="bx bx-chevron-down me-1"></i>Show Original Email');
                    $message.attr('rows', 15);
                }
            }

            function insertTemplate() {
                const templateKeys = Object.keys(CONFIG.templates);
                const templateList = templateKeys.map((k, i) => `${i + 1}. ${k}`).join('\n');
                const selection = prompt(`Select template:\n${templateList}`);

                if (!selection) return;

                const selectedKey = templateKeys[parseInt(selection) - 1];
                if (!selectedKey) {
                    toastr.warning('Invalid template selection');
                    return;
                }

                const template = CONFIG.templates[selectedKey];
                const $message = $(CONFIG.selectors.message);
                const currentMessage = $message.val();

                $message.val(currentMessage ? `${currentMessage}\n\n${template}` : template);
                toastr.success('Template inserted');
            }

            function saveDraft() {
                if (!confirm('Save current email as draft?')) return;

                try {
                    const formData = new FormData(document.querySelector(CONFIG.selectors.form));
                    formData.append('save_as_draft', '1');

                    if (EmailState.isReply && EmailState.originalMessage) {
                        formData.append('reply_context', JSON.stringify(EmailState.originalMessage));
                    }

                    toastr.success('Draft saved successfully!');

                    // Uncomment when backend endpoint is ready
                    /*
                    $.ajax({
                        url: '/api/emails/draft',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: (response) => toastr.success('Draft saved successfully!'),
                        error: (xhr) => toastr.error('Failed to save draft. Please try again.')
                    });
                    */
                } catch (error) {
                    console.error('Error saving draft:', error);
                    toastr.error(`Error saving draft: ${error.message}`);
                }
            }

            function handleEmailSubmit(e) {
                e.preventDefault();

                try {
                    if (!validateEmailSelection() || !validateForm()) {
                        toastr.error('Please fix the validation errors before sending');
                        return;
                    }

                    showConfirmationModal();
                } catch (error) {
                    console.error('Error in form submission:', error);
                    toastr.error(`Error sending email: ${error.message}`);
                }
            }

            function showConfirmationModal() {
                const toEmails = $(CONFIG.selectors.toContacts).val() || [];
                const contactsTo = $(CONFIG.selectors.toEmail).val() || [];
                const ccEmails = $(CONFIG.selectors.ccEmail).val() || [];
                const bccEmails = $(CONFIG.selectors.bccEmail).val() || [];

                $('#contactsTo').text(toEmails.length ? toEmails.join(', ') : 'None');
                $('#confirmTo').text(contactsTo.length ? contactsTo : 'None');
                $('#confirmCC').text(ccEmails.length ? ccEmails.join(', ') : 'None');
                $('#confirmBCC').text(bccEmails.length ? bccEmails.join(', ') : 'None');
                $('#confirmSubject').text($(CONFIG.selectors.subject).val());
                $('#confirmRef').text($(CONFIG.selectors.reference).val());
                $('#confirmPriority').text($(`${CONFIG.selectors.priority} option:selected`).text());
                $('#confirmCategory').text($(`${CONFIG.selectors.category} option:selected`).text());
                $('#confirmMessage').text($(CONFIG.selectors.message).val());
                $('#confirmAttachments').text($('#fileCount').text() || 'None');

                $('#replyWarning').toggle(EmailState.isReply);

                $(CONFIG.selectors.modal).modal('hide');
                $(CONFIG.selectors.confirmModal).modal('show');
            }

            function confirmAndSendEmail() {
                const $sendBtn = $('#confirmSendBtn');
                const $notificationBtn = $('#sendReinNotification');

                try {
                    const formData = new FormData(document.querySelector(CONFIG.selectors.form));
                    formData.delete('category_templates');

                    sendEmailRequest(formData, $sendBtn, $notificationBtn);
                } catch (error) {
                    console.error('Error sending email:', error);
                    toastr.error(`Error sending email: ${error.message}`);
                    resetButtonState($sendBtn, $notificationBtn);
                }
            }

            function sendEmailRequest(formData, $sendBtn, $notificationBtn) {
                setButtonLoadingState($sendBtn, $notificationBtn, true);

                $.ajax({
                    url: $('#bdNotificationForm').attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        toastr.success(response.message || 'Email sent successfully!');
                        $(CONFIG.selectors.confirmModal).modal('hide');
                        $(CONFIG.selectors.modal).modal('hide');
                        resetForm();
                    },
                    error: (xhr) => {
                        console.error('Email send failed:', xhr);
                        toastr.error('Failed to send email. Please try again.');
                    },
                    complete: () => {
                        setButtonLoadingState($sendBtn, $notificationBtn, false);
                    }
                });
            }

            function setButtonLoadingState($sendBtn, $notificationBtn, isLoading) {
                const loadingHtml = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
                const normalHtml = '<i class="bx bx-paper-plane me-1"></i>Send Email';

                $sendBtn.prop('disabled', isLoading).html(isLoading ? loadingHtml : normalHtml);
                $notificationBtn.prop('disabled', isLoading).html(isLoading ? loadingHtml : normalHtml);
            }

            function resetButtonState($sendBtn, $notificationBtn) {
                setButtonLoadingState($sendBtn, $notificationBtn, false);
            }

            function resetMailForm() {
                const $form = $(CONFIG.selectors.form);
                $('#compose-tab').tab('show');

                EmailState.reset();

                $('#replyToId, #originalMessageId').val('');
                $('#composeTitle').text('Compose New Email');
                $form.removeClass('reply-context-highlight');
                $('#emailBody').addClass('hidden');

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('.error-message').remove();

                const $originalSubject = $form.find('#subject');
                $val = $originalSubject.val().startsWith('Re: ') ? $originalSubject.val().substring(4).trim() :
                    $originalSubject.val()

                $originalSubject.val($val).attr('readonly', false);

                const templates = $("#categoryTemplates").val();
                const category = $(CONFIG.selectors.category);

                try {
                    const templateData = JSON.parse(templates);
                    const template = templateData[category];

                    // if (template) {
                    //     $(CONFIG.selectors.subject).val(template.subject);
                    //     $(CONFIG.selectors.message).val(template.message);
                    // }
                } catch (error) {}

                $('#emailBody').addClass('hidden');
                $('#resetMailForm').hide();
            }

            function resetForm() {
                const $form = $(CONFIG.selectors.form);
                $form[0].reset();

                $('#compose-tab').tab('show');

                EmailState.reset();

                $('#replyToId, #originalMessageId').val('');
                $('#composeTitle').text('Compose New Email');
                $('#resetMailForm').hide();
                $form.removeClass('reply-context-highlight');
                $('#emailBody').addClass('hidden');

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('.error-message').remove();

                $(`${CONFIG.selectors.toContacts}, ${CONFIG.selectors.ccEmail}, ${CONFIG.selectors.bccEmail}`)
                    .val(null).trigger('change');
            }

            function validateForm() {
                let isValid = true;
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const requiredFields = {
                    'subject': 'Subject is required',
                    'message': 'Message is required',
                    'reference': 'Reference is required'
                };

                Object.entries(requiredFields).forEach(([fieldId, errorMsg]) => {
                    const $field = $(`#${fieldId}`);
                    if (!$field.val()?.trim()) {
                        $field.addClass('is-invalid');
                        $field.next('.invalid-feedback').text(errorMsg);
                        isValid = false;
                    }
                });

                return isValid;
            }

            function validateEmailSelection() {
                $('.error-message').remove();

                const toEmails = $(CONFIG.selectors.toContacts).val() || [];
                const ccEmails = $(CONFIG.selectors.ccEmail).val() || [];
                const bccEmails = $(CONFIG.selectors.bccEmail).val() || [];

                if (toEmails.length === 0 && ccEmails.length === 0 && bccEmails.length === 0) {
                    showError(CONFIG.selectors.toContacts, 'Please select at least one recipient');
                    return false;
                }

                const allEmails = [...toEmails, ...ccEmails, ...bccEmails];
                const duplicates = findDuplicateEmails(allEmails);

                if (duplicates.length > 0) {
                    showError(CONFIG.selectors.toContacts, `Duplicate email addresses: ${duplicates.join(', ')}`);
                    return false;
                }

                return true;
            }

            function showError(selector, message) {
                $(selector).after(
                    `<div class="error-message" style="color: red; font-size: 12px; margin-top: 5px;">${message}</div>`
                );
            }

            function handleEmailFieldChange() {
                const $changedDropdown = $(this);
                const dropdownType = $changedDropdown.attr('id');
                const selectedEmails = $changedDropdown.val() || [];

                const allEmails = [
                    ...$(CONFIG.selectors.toContacts).val() || [],
                    ...$(CONFIG.selectors.ccEmail).val() || [],
                    ...$(CONFIG.selectors.bccEmail).val() || []
                ];

                const duplicates = findDuplicateEmails(allEmails);

                if (duplicates.length > 0) {
                    showDuplicateWarning(duplicates);
                    resolveDuplicates(dropdownType, selectedEmails);
                }
            }

            function findDuplicateEmails(emailArray) {
                const emailCounts = {};
                return emailArray.filter(email => {
                    if (!email) return false;
                    emailCounts[email] = (emailCounts[email] || 0) + 1;
                    return emailCounts[email] === 2;
                });
            }

            function showDuplicateWarning(duplicates) {
                toastr.warning(
                    `The email(s) "${duplicates.join(', ')}" are selected in multiple fields. They will be automatically moved to avoid duplicates.`, {
                        timeOut: 5000
                    }
                );
            }

            function resolveDuplicates(changedField, newlySelectedEmails) {
                newlySelectedEmails.forEach(email => {
                    if (changedField === 'tocontacts') {
                        removeEmailFromDropdown(CONFIG.selectors.ccEmail, email);
                        removeEmailFromDropdown(CONFIG.selectors.bccEmail, email);
                    } else if (changedField === 'ccEmail') {
                        const toEmails = $(CONFIG.selectors.toContacts).val() || [];
                        if (toEmails.includes(email)) {
                            removeEmailFromDropdown(CONFIG.selectors.ccEmail, email);
                        } else {
                            removeEmailFromDropdown(CONFIG.selectors.bccEmail, email);
                        }
                    } else if (changedField === 'bccEmail') {
                        const toEmails = $(CONFIG.selectors.toContacts).val() || [];
                        const ccEmails = $(CONFIG.selectors.ccEmail).val() || [];
                        if (toEmails.includes(email) || ccEmails.includes(email)) {
                            removeEmailFromDropdown(CONFIG.selectors.bccEmail, email);
                        }
                    }
                });
            }

            function removeEmailFromDropdown(selector, emailToRemove) {
                const $dropdown = $(selector);
                const currentValues = $dropdown.val() || [];
                const newValues = currentValues.filter(email => email !== emailToRemove);

                if (currentValues.length !== newValues.length) {
                    $dropdown.val(newValues).trigger('change.select2');
                }
            }

            function populateReplyForm(message) {
                if (!confirm('This will switch to reply mode. Continue?')) return;

                try {
                    $('#replyToId').val(message.id);
                    $('#originalMessageId').val(message.id);
                    $(CONFIG.selectors.toEmail).val(message.from);

                    let subject = message.subject;
                    if (!subject.toLowerCase().startsWith('re:')) {
                        subject = `RE: ${subject}`;
                    }
                    $(CONFIG.selectors.subject).val(subject);

                    if (message.category) $(CONFIG.selectors.category).val(message.category).trigger('change');
                    if (message.priority) $(CONFIG.selectors.priority).val(message.priority).trigger('change');
                    if (message.reference) $(CONFIG.selectors.reference).val(message.reference);

                    populateOriginalMessageDetails(message);
                    createThreadMessage(message);
                    updateUIForReplyMode(message);
                } catch (error) {
                    console.error('Error populating reply form:', error);
                    toastr.error('Failed to populate reply form');
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
                `.trim();

                $('#threadMessage').val(threadContent);
            }

            function updateUIForReplyMode(message) {
                EmailState.isReply = true;
                EmailState.originalMessage = message;

                $('#composeTitle').text('Reply to Message');
                $('#resetMailForm').show();
                $(CONFIG.selectors.form).addClass('reply-context-highlight');
            }

            window.BDEmailModal = {
                populateReplyForm,
                resetForm
            };
        });
    </script>
@endpush
