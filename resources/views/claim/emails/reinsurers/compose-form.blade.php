<div id="composeForm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" id="composeTitle">Compose New Email</h2>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFormBtn" style="display: none;">
            Start New Email Instead
        </button>
    </div>

    <form id="emailForm" class="claimReinEmailForm" novalidate>
        @csrf
        <input type="hidden" id="replyToId" name="reply_to_id">
        <input type="hidden" id="originalMessageId" name="original_message_id">
        <input type="hidden" name="claim_no" value="{{ $ClaimRegister->claim_no }}">
        <input type="hidden" name="customer_id" value="{{ $ClaimRegister->customer_id }}">
        <input type="hidden" name="claim_notice_file" id="claimNoticeFile">
        <input type="hidden" name="debit_note_file" id="debitNoteFile">
        <input type="hidden" name="partner_email" id="partnerToEmail">

        <div class="row mb-2">
            <div class="col-md-12">
                <label for="toEmail" class="form-label">To: <span class="text-danger">*</span></label>
                <input type="text" class="form-inputs custom-disabled" id="toEmail" name="to_email" value=""
                    required readonly>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-12">
                <label for="contacts" class="form-label">Contacts: <span class="text-danger">*</span></label>
                <select class="form-inputs select2" id="contacts" name="contacts[]" multiple></select>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <label for="ccEmail" class="form-label">CC:</label>
                <select class="form-inputs select2" id="ccEmail" name="cc_email[]" multiple
                    aria-placeholder="Select CC Emails"></select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6">
                <label for="bccEmail" class="form-label">BCC:</label>
                <select class="form-inputs select2" id="bccEmail" name="bcc_email[]" multiple></select>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-8">
                <label for="subject" class="form-label">Subject: <span class="text-danger">*</span></label>
                <input type="text" class="form-inputs" id="subject" name="subject"
                    value="{{ is_array($claimSubject) ? implode(' ', $claimSubject) : $claimSubject }}" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <label for="priority" class="form-label">Priority:</label>
                <select class="form-inputs select2" id="priority" name="priority">
                    <option value="low">Low</option>
                    <option value="normal" selected>Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <label for="category" class="form-label">Category:</label>
                <select class="form-inputs select2" id="category" name="category">
                    <option value="claim">Claim Notification</option>
                    <option value="policy">Policy Communication</option>
                    <option value="risk">Risk Assessment</option>
                    <option value="settlement">Settlement</option>
                    <option value="general">General Correspondence</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6">
                <label for="reference" class="form-label">Reference Number:</label>
                <div class="input-group">
                    <input type="text" class="form-control color-blk @error('reference') is-invalid @enderror"
                        id="reference" name="reference" value="{{ old('reference') }}"
                        placeholder="REF-2025-XXXXXX">
                    <button type="button" class="btn btn-outline-dark" id="generateRefBtn">
                        Generate
                    </button>
                    @error('reference')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-12">
                <label for="message" class="form-label">Message: <span class="text-danger">*</span></label>
                <div class="d-flex gap-2 mb-2">
                    <button type="button" id="toggleEmailBodyBtn" class="btn btn-outline-secondary btn-sm"
                        style="display: none">
                        <i class="bx bx-dots-horizontal me-1"></i>Toggle Original Email
                    </button>
                    {{-- <button type="button" id="insertTemplateBtn" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-file-blank me-1"></i>Insert Template
                    </button> --}}
                </div>

                <div class="email-body hidden" id="emailBody">
                    <div class="email-details">
                        <h6 class="mb-2"><i class="bx bx-envelope me-1"></i>Original Message Details:</h6>
                        <div class="detail-row">
                            <span class="detail-label">From:</span>
                            <span class="detail-value" id="originalFrom"></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Sent:</span>
                            <span class="detail-value" id="originalSent"></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">To:</span>
                            <span class="detail-value" id="originalTo"></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Subject:</span>
                            <span class="detail-value" id="originalSubject"></span>
                        </div>
                    </div>

                    <div class="email-content-box">
                        <div class="original-content" id="originalContent">
                            <iframe id="threadMessages" style="width:100%;height:400px;border:none;"
                                sandbox="allow-same-origin allow-popups allow-forms allow-scripts"></iframe>
                        </div>
                    </div>
                </div>

                <textarea class="form-inputs @error('message') is-invalid @enderror" id="message" name="message" rows="14"
                    required placeholder="Reply message...">{{ $defaultMessage ?? '' }}</textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <label for="message" class="form-label fw-bold">
                    Attached Files:
                </label>
                <div id="attachedFilesList" class="attached-files-container compose_attachement">
                    <div class="row">
                        <!-- Claim Notice -->
                        <div class="col-md-4">
                            <a href="#" id="claimNoticeLink" target="_blank" rel="noopener noreferrer">
                                <div class="file-item d-flex align-items-center mb-2">
                                    <div class="file-icon me-3">
                                        <i class="bx bx-file"></i>
                                    </div>
                                    <div class="file-info flex-grow-1">
                                        <h6 class="mb-1">
                                            Claim_Notice_{{ $ClaimRegister->intimation_no }}
                                        </h6>
                                        <div class="file-meta">
                                            PDF Document
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4">
                            <a href="#" id="debitNoteLink" target="_blank" rel="noopener noreferrer">
                                <div class="file-item d-flex align-items-center mb-2">
                                    <div class="file-icon me-3">
                                        <i class="bx bx-file"></i>
                                    </div>
                                    <div class="file-info flex-grow-1">
                                        <h6 class="mb-1">
                                            Debit_Note_{{ $ClaimRegister->intimation_no }}
                                        </h6>
                                        <div class="file-meta">
                                            PDF Document
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        {{-- @if (isset($claimDocuments) && count($claimDocuments) > 0)
                            @foreach ($claimDocuments as $doc)
                                <div class="col-md-4">
                                    <a href="{{ $doc->file_path ?? '#' }}" target="_blank" rel="noopener noreferrer">
                                        <div class="file-item d-flex align-items-center mb-2">
                                            <div class="file-icon me-3">
                                                @php
                                                    $mimeType = $doc->mime_type ?? '';
                                                    $fileExtension = pathinfo($doc->file ?? '', PATHINFO_EXTENSION);
                                                @endphp
                                                @if (str_contains($mimeType, 'pdf') || strtolower($fileExtension) === 'pdf')
                                                    <i class="bx bx-file-pdf text-danger"></i>
                                                @elseif(str_contains($mimeType, 'word') ||
                                                        str_contains($mimeType, 'document') ||
                                                        in_array(strtolower($fileExtension), ['doc', 'docx']))
                                                    <i class="bx bx-file-doc text-primary"></i>
                                                @elseif(str_contains($mimeType, 'image') ||
                                                        in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                                                    <i class="bx bx-image text-success"></i>
                                                @elseif(str_contains($mimeType, 'sheet') ||
                                                        str_contains($mimeType, 'excel') ||
                                                        in_array(strtolower($fileExtension), ['xls', 'xlsx']))
                                                    <i class="bx bx-file-excel text-success"></i>
                                                @elseif(str_contains($mimeType, 'text') || strtolower($fileExtension) === 'txt')
                                                    <i class="bx bx-file-txt text-info"></i>
                                                @else
                                                    <i class="bx bx-file"></i>
                                                @endif
                                            </div>
                                            <div class="file-info flex-grow-1">
                                                <h6 class="mb-1">
                                                    {{ $doc->file ?? 'Document' }}
                                                </h6>
                                                <div class="file-meta">
                                                    @php
                                                        $displayType = 'Document';
                                                        if (str_contains($mimeType, 'pdf')) {
                                                            $displayType = 'PDF Document';
                                                        } elseif (str_contains($mimeType, 'image')) {
                                                            $displayType = 'Image File';
                                                        } elseif (
                                                            str_contains($mimeType, 'word') ||
                                                            str_contains($mimeType, 'document')
                                                        ) {
                                                            $displayType = 'Word Document';
                                                        } elseif (
                                                            str_contains($mimeType, 'sheet') ||
                                                            str_contains($mimeType, 'excel')
                                                        ) {
                                                            $displayType = 'Excel Document';
                                                        } elseif (str_contains($mimeType, 'text')) {
                                                            $displayType = 'Text Document';
                                                        } elseif ($fileExtension) {
                                                            $displayType = strtoupper($fileExtension) . ' Document';
                                                        }
                                                    @endphp
                                                    {{ $displayType }}
                                                    @if (isset($doc->file_size))
                                                        • {{ number_format($doc->file_size / 1024, 1) }} KB
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @endif

                        @if (!$claimDocuments || count($claimDocuments) == 0)
                            <div class="col-md-12">
                                <div id="additionalFilesMessage" class="text-center py-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        No additional claim documents attached.
                                    </small>
                                </div>
                            </div>
                        @endif --}}
                    </div>
                </div>

                <div class="mt-2 compose_attachement">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        {{-- <span id="fileCount">
                            {{ 2 + ($claimDocuments && count($claimDocuments) > 0 ? count($claimDocuments) : 0) }}
                            files attached
                        </span> --}}
                    </small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="readReceipt" name="read_receipt">
                    <label class="form-check-label" for="readReceipt" style="line-height: 23px;">
                        Request read receipt
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="scheduleSend" name="schedule_send">
                    <label class="form-check-label" for="scheduleSend" style="line-height: 23px;">
                        Schedule send
                    </label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-light" id="resetFormBtn">
                    Reset
                </button>
                <button type="button" class="btn btn-light" id="saveDraftBtn">
                    Save Draft
                </button>
                <button type="submit" class="btn btn-primary" id="sendReinNotification">
                    <i class="bx bx-paper-plane me-1"></i>Send Email
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    #composeTitle {
        margin-bottom: 0;
        font-size: 18px !important;
        font-weight: 600;
    }

    .form-label {
        font-weight: bold;
    }

    .email-body {
        padding: 12px;
        background: #f8f9fa;
        margin: 1rem 0;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        max-height: 300px;
        overflow-y: auto;
        transition: all 0.3s ease;
    }

    .email-body.hidden {
        display: none;
    }

    .email-details {
        background-color: #e3f2fd;
        border-left: 4px solid #2196f3;
        padding: 16px;
        margin-bottom: 16px;
        border-radius: 4px;
        font-size: 13px;
    }

    .detail-row {
        margin-bottom: 6px;
        display: flex;
    }

    .detail-label {
        font-weight: 600;
        width: 80px;
        color: #1976d2;
        flex-shrink: 0;
    }

    .detail-value {
        color: #424242;
        flex: 1;
        word-break: break-word;
    }

    .email-content-box {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 16px;
        background-color: #fafafa;
    }

    .original-content {
        font-size: 14px;
        line-height: 1.6;
    }

    .greeting {
        margin-bottom: 16px;
        color: #333;
        font-weight: 500;
    }

    .message-body {
        margin: 16px 0;
        color: #555;
    }

    .signature-section {
        background-color: #f0f0f0;
        padding: 12px;
        border-radius: 4px;
        margin-top: 16px;
        border-left: 3px solid #ddd;
    }

    .signature-text {
        font-size: 12px;
        color: #666;
        line-height: 1.4;
    }

    .attached-files-container {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1rem;
        background-color: #fafafa;
    }

    .file-item {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 0.75rem;
        transition: all 0.2s ease;
    }

    .file-item:hover {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .file-icon {
        width: 40px;
        height: 40px;
        background: #e3f2fd;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1976d2;
        font-size: 18px;
    }

    .file-info h6 {
        margin-bottom: 0.25rem;
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .file-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .no-files {
        color: #6c757d;
        font-style: italic;
    }

    .reply-context-highlight {
        background-color: #fff3cd !important;
        border-left: 4px solid #ffc107 !important;
    }

    #debugReplyModal pre {
        max-height: 200px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .btn-group-toggle {
        display: flex;
        gap: 0.5rem;
    }

    .confirmation-details {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin: 1rem 0;
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

            /**
             * Initialize the compose form
             */
            function initializeComposeForm() {
                captureFormState();
            }

            function handleCancelConfirmation() {
                $('#sendReinDocumentEmail').modal('show');
            }

            /**
             * Show confirmation modal before sending
             */
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

            /**
             * Populate form fields for reply
             */
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

            /**
             * Populate original message details in the email body section
             */
            function populateOriginalMessageDetails(message) {
                $('#originalFrom').text(message.fromName || message.from);
                $('#originalSent').text(message.date);
                $('#originalTo').text(message.from);
                $('#originalSubject').text(message.subject);
            }

            /**
             * Create thread message for email history
             */
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

            /**
             * Update UI for reply mode
             */
            function updateUIForReplyMode(message) {
                $('#composeTitle').text('Reply to Message');
                $('#clearFormBtn').show();

                $('#emailForm.claimReinEmailForm').addClass('reply-context-highlight');
            }

            /**
             * Generate reference number
             */
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

            /**
             * Toggle email body visibility
             */
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

            /**
             * Insert email template
             */
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

            /**
             * Save draft
             */
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

                    //  TODO: Implement actual API call
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

            /**
             * Handle email form submission
             */
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
                    console.error('Error submitting email:', error);
                    toastr.error('Error sending email: ' + error.message);
                }
            }

            /**
             * Confirm and send email after user confirmation
             */
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
                $('#emailForm.claimReinEmailForm')[0].reset();

                replyState.isReply = false;
                replyState.originalMessage = null;

                $('#replyToId').val('');
                $('#originalMessageId').val('');

                $('#composeTitle').text('Compose New Email');
                $('#clearFormBtn').hide();
                $('#emailForm').removeClass('reply-context-highlight');

                $('#emailBody').addClass('hidden');

                $('.claimReinEmailForm #message').val(@json($defaultMessage ?? ''));
                $('.claimReinEmailForm #subject').val(@json(is_array($claimSubject ?? '') ? implode(' ', $claimSubject) : $claimSubject ?? ''));
                $('.claimReinEmailForm #toEmail').val('{{ $reinserEmail ?? '' }}');

                $('#priority').val('normal').trigger('change');
                $('#category').val('general').trigger('change');

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            /**
             * Validate form fields
             */
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

            /**
             * Check if email is valid
             */
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            /**
             * Track form changes
             */
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

            /**
             * Capture initial form state
             */
            function captureFormState() {
                replyState.formData = {};
                trackFormChanges();
            }

            $('.claimReinEmailForm #contacts, .claimReinEmailForm #ccEmail, .claimReinEmailForm #bccEmail').on(
                'change',
                function() {
                    const changedDropdown = $(this);
                    const dropdownType = changedDropdown.attr('id');
                    const selectedEmails = changedDropdown.val() || [];

                    const toEmails = $('#contacts').val() || [];
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
                const toEmails = $('.claimReinEmailForm #contacts').val() || [];
                const ccEmails = $('.claimReinEmailForm #ccEmail').val() || [];
                const bccEmails = $('.claimReinEmailForm #bccEmail').val() || [];

                newlySelectedEmails.forEach(email => {
                    if (changedField === 'contacts') {
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
                const toEmails = $('.claimReinEmailForm #contacts').val() || [];
                // $('.claimReinEmailForm #toEmail').val(toEmails.join(', '));
            }

            function validateEmailSelection() {
                const toEmails = $('.claimReinEmailForm #contacts').val() || [];
                const ccEmails = $('.claimReinEmailForm #ccEmail').val() || [];
                const bccEmails = $('.claimReinEmailForm #bccEmail').val() || [];

                if (toEmails.length === 0 && ccEmails.length === 0 && bccEmails.length === 0) {
                    $("#contacts").after(
                        '<div class="error-message" style="color: red; font-size: 12px; margin-top: 5px;">Please select at least one recipient</div>'
                    );
                    return false;
                }

                const allEmails = [...toEmails, ...ccEmails, ...bccEmails];
                const duplicates = findDuplicateEmails(allEmails);

                if (duplicates.length > 0) {
                    $("#contacts").after(
                        '<div class="error-message" style="color: red; font-size: 12px; margin-top: 5px;">Duplicate email addresses found: ' +
                        duplicates.join(', ') + '</div>');
                    resolveDuplicates('contacts', toEmails);
                }

                return true;
            }
        });
    </script>
@endpush
