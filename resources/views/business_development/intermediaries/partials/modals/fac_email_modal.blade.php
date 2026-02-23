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
                <input type="hidden" name="message_id" id="messageId" value="">
                <input type="hidden" name="conversation_id" id="conversationId" value="">

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
                        <button type="submit" class="btn btn-primary" id="sendNotificationBtn">
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
    #sendBDEmailModal .modal-dialog,
    #confirmationModal .modal-dialog {
        max-height: calc(100vh - 1.5rem);
        margin: 0.75rem auto;
    }

    #sendBDEmailModal .modal-content,
    #confirmationModal .modal-content {
        max-height: calc(100vh - 1.5rem);
        overflow: hidden;
    }

    #sendBDEmailModal .modal-body,
    #confirmationModal .modal-body {
        overflow-y: auto;
    }

    #bdEmailTabContent {
        min-height: 0;
        max-height: calc(100vh - 280px);
        overflow: hidden;
    }

    #bdEmailTabContent .tab-pane {
        padding-top: 1rem;
        border: none;
        max-height: inherit;
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
                initialFormData: null,

                reset() {
                    this.isReply = false;
                    this.originalMessage = null;
                    this.formData = {};
                },

                captureFormData() {
                    this.formData = {
                        isReply: $('#isReply').val() === '1',
                        messageId: $('#messageId').val(),
                        conversationId: $('#conversationId').val(),
                        toEmail: $('#toEmail').val(),
                        toContacts: $('#toContacts').val() || [],
                        ccEmail: $('#ccEmail').val() || [],
                        bccEmail: $('#bccEmail').val() || [],
                        subject: $('#subject').val(),
                        message: $('#message').val(),
                        category: $('#category').val(),
                        priority: $('#priority').val(),
                        reference: $('#reference').val(),
                        includeReplyAttachments: $('#includeReplyAttachments').is(':checked'),
                        showOriginalEmail: !$('#emailBody').hasClass('hidden')
                    };
                },

                captureInitialFormData() {
                    if ($('#isReply').val() !== '1' && !$(CONFIG.selectors.reference).val()?.trim()) {
                        generateReference();
                    }

                    this.captureFormData();
                    this.initialFormData = {
                        ...this.formData,
                        isReply: false,
                        messageId: '',
                        conversationId: '',
                        showOriginalEmail: false
                    };
                },

                restoreInitialFormData() {
                    if (!this.initialFormData || Object.keys(this.initialFormData).length === 0) {
                        return false;
                    }

                    const initial = this.initialFormData;

                    this.isReply = false;
                    this.originalMessage = null;
                    this.formData = { ...initial };

                    $('#isReply').val('0');
                    $('#messageId').val('');
                    $('#conversationId').val('');
                    $('#toEmail').val(initial.toEmail || '');
                    $('#subject').val(initial.subject || '').attr('readonly', false);
                    $('#message').val(initial.message || '');
                    $('#category').val(initial.category || 'lead').trigger('change.select2');
                    $('#priority').val(initial.priority || 'normal').trigger('change.select2');
                    $('#reference').val(initial.reference || '');
                    $('#toContacts').val(initial.toContacts || []).trigger('change.select2');
                    $('#ccEmail').val(initial.ccEmail || []).trigger('change.select2');
                    $('#bccEmail').val(initial.bccEmail || []).trigger('change.select2');
                    $('#includeReplyAttachments').prop('checked', false);

                    $('#composeTitle').text('Compose New Email');
                    $('#resetMailForm').hide();
                    $(CONFIG.selectors.form).removeClass('reply-context-highlight');
                    $('#emailBody').addClass('hidden');
                    syncToggleEmailBodyButton();
                    applyReplyAttachmentVisibility();
                    updateSendButtonVisibility();
                    syncResetButtonVisibility();

                    $('#originalFrom, #originalSent, #originalTo, #originalSubject').text('');
                    const threadFrame = document.getElementById('threadMessages');
                    if (threadFrame) {
                        threadFrame.srcdoc = '';
                    }

                    return true;
                },

                restoreFormData() {
                    if (!this.formData || Object.keys(this.formData).length === 0) {
                        return;
                    }

                    $('#isReply').val(this.formData.isReply ? '1' : '0');
                    $('#messageId').val(this.formData.messageId || '');
                    $('#conversationId').val(this.formData.conversationId || '');
                    $('#toEmail').val(this.formData.toEmail || '');
                    $('#subject').val(this.formData.subject || '');
                    $('#message').val(this.formData.message || '');
                    $('#category').val(this.formData.category || 'lead').trigger('change.select2');
                    $('#priority').val(this.formData.priority || 'normal').trigger('change.select2');
                    $('#reference').val(this.formData.reference || '');
                    $('#toContacts').val(this.formData.toContacts || []).trigger('change.select2');
                    $('#ccEmail').val(this.formData.ccEmail || []).trigger('change.select2');
                    $('#bccEmail').val(this.formData.bccEmail || []).trigger('change.select2');
                    $('#includeReplyAttachments').prop('checked', !!this.formData.includeReplyAttachments);

                    $('#emailBody').toggleClass('hidden', !this.formData.showOriginalEmail);
                    syncToggleEmailBodyButton();
                    applyReplyAttachmentVisibility();
                    updateSendButtonVisibility();
                    syncResetButtonVisibility();
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
                syncResetButtonVisibility();
                updateSendButtonVisibility();

                $('#generateRefBtn').on('click', generateReference);
                $('#toggleEmailBodyBtn').on('click', toggleEmailBody);
                $('#resetMailForm').on('click', resetMailForm);
                $('#insertTemplateBtn').on('click', insertTemplate);
                $('#saveDraftBtn').on('click', saveDraft);
                $('#confirmSendBtn').on('click', confirmAndSendEmail);
                $('#includeReplyAttachments').on('change', function() {
                    applyReplyAttachmentVisibility();
                    EmailState.captureFormData();
                });
                $('#cancelEmailConfirmation').on('click', () => {
                    $(CONFIG.selectors.modal).modal('show');
                });

                $(CONFIG.selectors.form).on('submit', handleEmailSubmit);
                $(CONFIG.selectors.form).on('change input', () => EmailState.captureFormData());
                $(CONFIG.selectors.category).on('change', handleCategoryChange);
                $('#emailTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                    updateSendButtonVisibility();
                    syncResetButtonVisibility();

                    const targetId = e.target.id;
                    if (targetId === 'replies-tab') {
                        EmailState.captureFormData();
                    } else if (targetId === 'compose-tab') {
                        EmailState.restoreFormData();
                    }
                });

                $(`${CONFIG.selectors.toContacts}, ${CONFIG.selectors.ccEmail}, ${CONFIG.selectors.bccEmail}`)
                    .on('change', handleEmailFieldChange);
            }

            function updateSendButtonVisibility() {
                const isReplyTabActive = $('#replies-tab').hasClass('active');
                const isReplyMode = $('#isReply').val() === '1';
                const $sendBtn = $('#sendNotificationBtn');

                $sendBtn.toggle(!isReplyTabActive);
                $sendBtn.html(
                    isReplyMode ?
                    '<i class="bx bx-reply me-1"></i>Send Reply' :
                    '<i class="bx bx-paper-plane me-1"></i>Send Notification'
                );
            }

            function syncResetButtonVisibility() {
                $('#resetMailForm').toggle($('#isReply').val() === '1');
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
                    const categoryValue = ($(CONFIG.selectors.category).val() || 'lead').toString();
                    const category = categoryValue.toUpperCase().substring(0, 3) || 'REF';
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
                $emailBody.toggleClass('hidden');
                syncToggleEmailBodyButton();
            }

            function syncToggleEmailBodyButton() {
                const $emailBody = $('#emailBody');
                const $toggleBtn = $('#toggleEmailBodyBtn');
                const isReply = $('#isReply').val() === '1';
                const isVisible = !$emailBody.hasClass('hidden');

                if (!isReply) {
                    $toggleBtn.hide();
                    return;
                }

                $toggleBtn
                    .show()
                    .attr('aria-expanded', isVisible ? 'true' : 'false')
                    .html(
                        isVisible ?
                        '<i class="bx bx-chevron-up me-1"></i>Hide Original Email' :
                        '<i class="bx bx-chevron-down me-1"></i>Show Original Email'
                    );
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
                const isReply = $('#isReply').val() === '1';
                const includeReplyAttachments = $('#includeReplyAttachments').is(':checked');

                $('#contactsTo').text(toEmails.length ? toEmails.join(', ') : 'None');
                $('#confirmTo').text(contactsTo.length ? contactsTo : 'None');
                $('#confirmCC').text(ccEmails.length ? ccEmails.join(', ') : 'None');
                $('#confirmBCC').text(bccEmails.length ? bccEmails.join(', ') : 'None');
                $('#confirmSubject').text($(CONFIG.selectors.subject).val());
                $('#confirmRef').text($(CONFIG.selectors.reference).val());
                $('#confirmPriority').text($(`${CONFIG.selectors.priority} option:selected`).text());
                $('#confirmCategory').text($(`${CONFIG.selectors.category} option:selected`).text());
                $('#confirmMessage').text($(CONFIG.selectors.message).val());
                $('#confirmAttachments').text(isReply && !includeReplyAttachments ? 'None' : ($('#fileCount').text() || 'None'));

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
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const firstField = Object.keys(xhr.responseJSON.errors)[0];
                            const firstMessage = xhr.responseJSON.errors[firstField]?.[0];
                            toastr.error(firstMessage || 'Validation failed. Please check the form.');
                            return;
                        }
                        toastr.error(xhr.responseJSON?.message || 'Failed to send email. Please try again.');
                    },
                    complete: () => {
                        setButtonLoadingState($sendBtn, $notificationBtn, false);
                    }
                });
            }

            function setButtonLoadingState($sendBtn, $notificationBtn, isLoading) {
                const loadingHtml = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
                const normalHtml = '<i class="bx bx-paper-plane me-1"></i>Send Notification';

                if (isLoading) {
                    $sendBtn.prop('disabled', true).html(loadingHtml);
                    $notificationBtn.prop('disabled', true).html(loadingHtml);
                    return;
                }

                $sendBtn.prop('disabled', false).html(normalHtml);
                $notificationBtn.prop('disabled', false).html(normalHtml);
                updateSendButtonVisibility();
            }

            function resetButtonState($sendBtn, $notificationBtn) {
                setButtonLoadingState($sendBtn, $notificationBtn, false);
            }

            function resetMailForm() {
                const $form = $(CONFIG.selectors.form);
                $('#compose-tab').tab('show');

                if (EmailState.restoreInitialFormData()) {
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    $('.error-message').remove();
                    updateSendButtonVisibility();
                    syncResetButtonVisibility();
                    return;
                }

                EmailState.reset();

                $('#isReply').val('0');
                $('#messageId').val('');
                $('#conversationId').val('');
                $('#composeTitle').text('Compose New Email');
                $form.removeClass('reply-context-highlight');
                $('#emailBody').addClass('hidden');
                syncToggleEmailBodyButton();
                $('#includeReplyAttachments').prop('checked', false);
                applyReplyAttachmentVisibility();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('.error-message').remove();

                const $originalSubject = $form.find('#subject');
                const normalizedSubject = $originalSubject.val().replace(/^re:\s*/i, '').trim();

                $originalSubject.val(normalizedSubject).attr('readonly', false);

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
                updateSendButtonVisibility();
                syncResetButtonVisibility();
            }

            function resetForm() {
                const $form = $(CONFIG.selectors.form);
                $form[0].reset();

                $('#compose-tab').tab('show');

                EmailState.reset();

                $('#isReply').val('0');
                $('#messageId').val('');
                $('#conversationId').val('');
                $('#composeTitle').text('Compose New Email');
                $('#resetMailForm').hide();
                $form.removeClass('reply-context-highlight');
                $('#emailBody').addClass('hidden');
                syncToggleEmailBodyButton();
                $('#includeReplyAttachments').prop('checked', false);
                applyReplyAttachmentVisibility();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('.error-message').remove();
                $(CONFIG.selectors.subject).attr('readonly', false);
                updateSendButtonVisibility();
                syncResetButtonVisibility();

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

                const isReply = $('#isReply').val() === '1';
                if (isReply) {
                    const replyTo = ($(CONFIG.selectors.toEmail).val() || '').trim();
                    if (!replyTo) {
                        showError(CONFIG.selectors.toEmail, 'Reply recipient is required');
                        return false;
                    }
                    return true;
                }

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
                    if (changedField === 'toContacts') {
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
                try {
                    $('#isReply').val('1');
                    const fallbackMessageId = message.messageId || message.id || '';
                    $('#messageId').val(fallbackMessageId);
                    $('#conversationId').val(message.conversationId || fallbackMessageId);
                    $(CONFIG.selectors.toEmail).val(message.from);

                    let subject = (message.subject || '').trim();
                    if (!/^re:/i.test(subject)) {
                        subject = `RE: ${subject}`;
                    }
                    $(CONFIG.selectors.subject).val(subject).attr('readonly', true);

                    if (message.category && $(CONFIG.selectors.category).find(`option[value="${message.category}"]`).length) {
                        $(CONFIG.selectors.category).val(message.category).trigger('change');
                    }
                    if (message.priority && $(CONFIG.selectors.priority).find(`option[value="${message.priority}"]`).length) {
                        $(CONFIG.selectors.priority).val(message.priority).trigger('change');
                    }
                    generateReference();
                    $(CONFIG.selectors.message).val('').attr('rows', 15);

                    populateOriginalMessageDetails(message);
                    createThreadMessage(message);
                    updateUIForReplyMode(message);
                    $('#compose-tab').tab('show');
                    EmailState.captureFormData();
                } catch (error) {
                    console.error('Error populating reply form:', error);
                    toastr.error('Failed to populate reply form');
                }
            }

            function populateOriginalMessageDetails(message) {
                $('#originalFrom').text(message.fromName || message.from);
                $('#originalSent').text(message.date);
                $('#originalTo').text(message.toList || message.from);
                $('#originalSubject').text(message.subject);
            }

            function createThreadMessage(message) {
                const threadFrame = document.getElementById('threadMessages');
                if (!threadFrame) return;

                const bodyHtml = message.bodyHtml || `
                    <div style="font-family: Arial, sans-serif; font-size: 13px;">
                        <p><strong>From:</strong> ${message.fromName || message.from} &lt;${message.from}&gt;</p>
                        <p><strong>Date:</strong> ${message.date}</p>
                        <p><strong>Subject:</strong> ${message.subject}</p>
                        <hr />
                        <p>${message.preview || 'Original message content...'}</p>
                    </div>
                `;

                const docHtml = `
                    <!doctype html>
                    <html>
                    <head>
                        <meta charset="utf-8">
                        <style>
                            body { margin: 0; padding: 12px; font-family: Arial, sans-serif; font-size: 13px; line-height: 1.5; color: #212529; }
                            img { max-width: 100%; height: auto; }
                            table { max-width: 100%; }
                        </style>
                    </head>
                    <body>${bodyHtml}</body>
                    </html>
                `;

                // Use srcdoc for reliable rendering in sandboxed iframe.
                threadFrame.srcdoc = docHtml;

                // Fallback for browsers/environments where srcdoc is restricted.
                try {
                    if (threadFrame.contentDocument && threadFrame.contentDocument.body) {
                        threadFrame.contentDocument.open();
                        threadFrame.contentDocument.write(docHtml);
                        threadFrame.contentDocument.close();
                    }
                } catch (e) {
                    // No-op: srcdoc path already covers rendering.
                }
            }

            function updateUIForReplyMode(message) {
                EmailState.isReply = true;
                EmailState.originalMessage = message;

                $('#composeTitle').text('Reply to Message');
                $(CONFIG.selectors.form).addClass('reply-context-highlight');
                $('#emailBody').removeClass('hidden');
                syncToggleEmailBodyButton();
                $('#includeReplyAttachments').prop('checked', false);
                applyReplyAttachmentVisibility();
                updateSendButtonVisibility();
                syncResetButtonVisibility();
            }

            function applyReplyAttachmentVisibility() {
                const isReply = $('#isReply').val() === '1';
                const includeReplyAttachments = $('#includeReplyAttachments').is(':checked');

                if (isReply) {
                    $('#replyAttachmentOptionWrap').show();
                    $('#attachedFilesList').toggle(includeReplyAttachments);
                } else {
                    $('#replyAttachmentOptionWrap').hide();
                    $('#attachedFilesList').show();
                }
            }

            window.BDEmailModal = {
                populateReplyForm,
                resetForm,
                captureInitialState: function() {
                    EmailState.captureInitialFormData();
                },
                captureFormData: function() {
                    EmailState.captureFormData();
                },
                restoreFormData: function() {
                    EmailState.restoreFormData();
                },
                checkEmailConnection: async function() {
                    if (window.emailManager && typeof window.emailManager.checkConnection === 'function') {
                        return await window.emailManager.checkConnection();
                    }
                    return true;
                },
                refreshConnectionStatus: async function() {
                    if (window.emailManager && typeof window.emailManager.checkConnection === 'function') {
                        return await window.emailManager.checkConnection();
                    }
                    return true;
                }
            };
        });
    </script>
@endpush
