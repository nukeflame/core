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

        <!-- Recipients Row -->
        <div class="row mb-2">
            <div class="col-md-12">
                <label for="toEmail" class="form-label">To: <span class="text-danger">*</span></label>
                <input type="email" class="form-inputs custom-disabled" id="toEmail" name="to_email" value=""
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
                        id="reference" name="reference" value="{{ old('reference') }}" placeholder="REF-2025-XXXXXX">
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
                    <button type="button" id="insertTemplateBtn" class="btn btn-outline-primary btn-sm" disabled>
                        <i class="bx bx-file-blank me-1"></i>Insert Template
                    </button>
                </div>

                <div class="email-body hidden" id="emailBody">
                    <div class="email-details">
                        <h6 class="mb-2"><i class="bx bx-envelope me-1"></i>Original Message Details:</h6>
                        <div class="detail-row">
                            <span class="detail-label">From:</span>
                            <span class="detail-value" id="originalFrom">Tech Department</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Sent:</span>
                            <span class="detail-value" id="originalSent">Tuesday, July 22, 2025 9:29 AM</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">To:</span>
                            <span class="detail-value" id="originalTo">derrickriziki7@gmail.com</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Subject:</span>
                            <span class="detail-value" id="originalSubject">Request To Submit Treaty Documents</span>
                        </div>
                    </div>

                    <div class="email-content-box">
                        <div class="original-content" id="originalContent">
                            <div class="greeting">Dear User,</div>
                            <div class="message-body" id="originalMessageBody">
                                Original message content will appear here...
                            </div>
                            <div class="signature-section">
                                <div class="signature-text">
                                    Best regards,<br>
                                    <em>Signature</em><br>
                                    Acentria International
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thread Message (Hidden field to store in email) -->
                {{-- <textarea id="threadMessage" name="thread_message" style="display: none;"></textarea> --}}

                <textarea class="form-inputs @error('message') is-invalid @enderror" id="message" name="message" rows="14"
                    required placeholder="Type your message here...">{{ $defaultMessage ?? '' }}</textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-12">
                <label for="message" class="form-label fw-bold">
                    Attached Files:
                </label>
                <div id="attachedFilesList" class="attached-files-container">
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

                        <!-- Debit Note -->
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

                        @if ($claimDocuments && count($claimDocuments) > 0)
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

                        <!-- No Files Message (only show if no documents exist) -->
                        @if (!$claimDocuments || count($claimDocuments) == 0)
                            <div class="col-md-12">
                                <div id="additionalFilesMessage" class="text-center py-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        No additional claim documents attached.
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        <span id="fileCount">
                            {{ 2 + ($claimDocuments && count($claimDocuments) > 0 ? count($claimDocuments) : 0) }}
                            files attached
                        </span>
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
                <button type="button" class="btn btn-outline-light" id="clearFormBtn">
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
            $('#resetFormBtn').on('click', resetForm);
            $('#newEmailInstead').on('click', startNewEmail);
            $('#viewOriginalBtn').on('click', viewOriginalMessage);
            $('#insertTemplateBtn').on('click', insertTemplate);
            $('#saveDraftBtn').on('click', saveDraft);
            $('#emailForm').on('submit', handleEmailSubmit);
            $('#clearFormBtn').on('submit', resetForm);

            $('#emailForm').on('change input', trackFormChanges);

            /**
             * Initialize the compose form
             */
            function initializeComposeForm() {
                captureFormState();
            }

            /**
             * Handle reply message from message list
             * This function is called from the message list component
             */
            window.handleReply = function(messageId, messageData = null) {
                console.log('Handling reply for message:', messageId, messageData);

                // try {
                //     // Find the message data
                //     let message = messageData;
                //     if (!message && window.MessageListDebug) {
                //         const messages = window.MessageListDebug.getMessages();
                //         message = messages.allMessages.find(m => m.id === messageId);
                //     }

                //     if (!message) {
                //         console.error('Message not found for reply:', messageId);
                //         showToast('error', 'Could not find message to reply to');
                //         return;
                //     }

                //     console.log('Found message for reply:', message);

                //     // Set reply state
                //     replyState.isReply = true;
                //     replyState.originalMessage = message;

                //     // Populate form with reply data
                //     populateReplyForm(message);

                //     // Update UI for reply mode
                //     updateUIForReplyMode(message);

                //     // Show debug info in development
                //     if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
                //     }

                //     showToast('success', `Replying to: ${message.subject}`);

                // } catch (error) {
                //     console.error('Error handling reply:', error);
                //     showToast('error', 'Error setting up reply: ' + error.message);
                // }
            };

            /**
             * Populate form fields for reply
             */
            function populateReplyForm(message) {
                console.log('📝 Populating reply form with:', message);

                try {
                    // Set hidden fields
                    $('#isReply').val('1');
                    $('#originalMessageId').val(message.id);
                    $('#replyToId').val(message.id);

                    // Populate basic fields
                    $('#toEmail').val(message.from);

                    // Handle subject with RE: prefix
                    let subject = message.subject;
                    if (!subject.toLowerCase().startsWith('re:')) {
                        subject = 'RE: ' + subject;
                    }
                    $('#subject').val(subject);

                    // Set category and priority from original
                    if (message.category) {
                        $('#category').val(message.category).trigger('change');
                    }
                    if (message.priority) {
                        $('#priority').val(message.priority).trigger('change');
                    }
                    if (message.reference) {
                        $('#reference').val(message.reference);
                    }

                    // Populate original message details
                    populateOriginalMessageDetails(message);

                    // Create thread message
                    createThreadMessage(message);

                    console.log('Reply form populated successfully');

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
                $('#originalMessageBody').text(message.preview || 'Original message content...');
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
                $('#newEmailInstead').show();

                // Show reply context alert
                $('#replyContextAlert').removeClass('d-none');
                $('#replyContextDetails').html(`
                    <strong>Subject:</strong> ${message.subject}<br>
                    <strong>From:</strong> ${message.fromName || message.from} &lt;${message.from}&gt;<br>
                    <strong>Date:</strong> ${message.date}
                    ${message.reference ? '<br><strong>Reference:</strong> ' + message.reference : ''}
                `);

                $('#emailForm').addClass('reply-context-highlight');
            }

            /**
             * Start a new email (clear reply mode)
             */
            function startNewEmail() {
                if (confirm('Are you sure you want to start a new email? Any unsaved changes will be lost.')) {
                    resetForm(true);
                    showToast('info', 'Started new email composition');
                }
            }

            /**
             * Reset form to initial state
             */
            function resetForm(clearAll = false) {
                try {
                    document.getElementById('emailForm').reset();
                    $('.select2').val(null).trigger('change');

                    replyState.isReply = false;
                    replyState.originalMessage = null;

                    $('#isReply').val('0');
                    $('#originalMessageId').val('');
                    $('#replyToId').val('');
                    $('#threadMessage').val('');

                    $('#composeTitle').text('Compose New Email');
                    $('#newEmailInstead').hide();
                    $('#replyContextAlert').addClass('d-none');
                    $('#emailForm').removeClass('reply-context-highlight');

                    $('#emailBody').addClass('hidden');

                    if (clearAll) {
                        $('#message').val('');
                        $('#subject').val('');
                        $('#toEmail').val('{{ $reinserEmail ?? '' }}');
                    }

                    $('#priority').val('normal').trigger('change');
                    $('#category').val('general').trigger('change');

                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    captureFormState();
                    showToast('info', 'Form has been reset');

                } catch (error) {
                    showToast('error', 'Error resetting form: ' + error.message);
                }
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
                } catch (error) {
                    toastr('error', 'Error generating reference number');
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

                // Simple template selection (you can enhance this with a modal)
                const templateKeys = Object.keys(templates);
                const selectedTemplate = prompt('Select template:\n' + templateKeys.map((k, i) => `${i + 1}. ${k}`)
                    .join('\n'));

                if (selectedTemplate && templateKeys[selectedTemplate - 1]) {
                    const template = templates[templateKeys[selectedTemplate - 1]];
                    const currentMessage = $('#message').val();
                    $('#message').val(currentMessage + '\n\n' + template);
                    showToast('success', 'Template inserted');
                }
            }

            /**
             * Save draft
             */
            function saveDraft() {
                try {
                    const formData = new FormData(document.getElementById('emailForm'));
                    formData.append('save_as_draft', '1');

                    // Add reply context if available
                    if (replyState.isReply && replyState.originalMessage) {
                        formData.append('reply_context', JSON.stringify(replyState.originalMessage));
                    }

                    showToast('success', 'Draft saved successfully!');

                    //  TODO: Implement actual API call
                    //{{-- $.ajax({
                    //     url: '{{ route('emails.draft') }}',
                    //     method: 'POST',
                    //     data: formData,
                    //     processData: false,
                    //     contentType: false,
                    //     success: function(response) {
                    //         console.log('Draft saved:', response);
                    //         showToast('success', 'Draft saved successfully!');
                    //     },
                    //     error: function(xhr, status, error) {
                    //         console.error('Draft save failed:', error);
                    //         showToast('error', 'Failed to save draft. Please try again.');
                    //     }
                    // --}}});

                } catch (error) {
                    showToast('error', 'Error saving draft: ' + error.message);
                }
            }

            /**
             * Handle email form submission
             */
            function handleEmailSubmit(e) {
                e.preventDefault();
                if (!validateEmailSelection()) {
                    return;
                }

                console.log('object')
                // try {
                //     // Validate form
                //     if (!validateForm()) {
                //         showToast('error', 'Please fix the validation errors before sending');
                //         return;
                //     }

                //     const formData = new FormData(e.target);

                //     // Add reply context if available
                //     if (replyState.isReply && replyState.originalMessage) {
                //         formData.append('reply_context', JSON.stringify(replyState.originalMessage));
                //         formData.append('is_reply', '1');
                //     }

                //     // Disable submit button
                //     $('#sendEmailBtn').prop('disabled', true).html(
                //         '<span class="spinner-border spinner-border-sm me-1"></span>Sending...');

                //     showToast('success', 'Email sent successfully!');

                //     // TODO: Implement actual API call
                //     //{{--  $.ajax({
                //     //     url: '{{ route('emails.send') }}',
                //     //     method: 'POST',
                //     //     data: formData,
                //     //     processData: false,
                //     //     contentType: false,
                //     //     success: function(response) {
                //     //         console.log('Email sent:', response);
                //     //         showToast('success', 'Email sent successfully!');
                //     //         resetForm(true);
                //     //     },
                //     //     error: function(xhr, status, error) {
                //     //         console.error('Email send failed:', error);
                //     //         showToast('error', 'Failed to send email. Please try again.');
                //     //     },
                //     //     complete: function() {
                //     //         $('#sendEmailBtn').prop('disabled', false).html('<i class="bx bx-paper-plane me-1"></i>Send Email');
                //     //     }
                //     // --}} });

                //     // Reset button after demo
                //     setTimeout(() => {
                //         $('#sendEmailBtn').prop('disabled', false).html(
                //             '<i class="bx bx-paper-plane me-1"></i>Send Email');
                //         resetForm(true);
                //     }, 2000);

                // } catch (error) {
                //     console.error('Error submitting email:', error);
                //     showToast('error', 'Error sending email: ' + error.message);
                //     $('#sendEmailBtn').prop('disabled', false).html(
                //         '<i class="bx bx-paper-plane me-1"></i>Send Email');
                // }
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

                const emailField = document.getElementById('toEmail');
                if (emailField.value && !isValidEmail(emailField.value)) {
                    emailField.classList.add('is-invalid');
                    emailField.nextElementSibling.textContent = 'Please enter a valid email address';
                    isValid = false;
                }

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
                    isReply: $('#isReply').val(),
                    originalMessageId: $('#originalMessageId').val(),
                    toEmail: $('#toEmail').val(),
                    subject: $('#subject').val(),
                    message: $('#message').val(),
                    threadMessage: $('#threadMessage').val(),
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

            /**
             * View original message
             */
            function viewOriginalMessage() {
                if (replyState.originalMessage) {
                    $('#toggleEmailBodyBtn').click();
                    if ($('#emailBody').hasClass('hidden')) {
                        $('#toggleEmailBodyBtn').click();
                    }
                    showToast('info', 'Original message is now visible below');
                } else {
                    showToast('error', 'No original message available');
                }
            }

            /**
             * Show toast notification
             */
            function showToast(type, message) {
                const toastHtml = `
                    <div class="toast show align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">${escapeHtml(message)}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;

                let toastContainer = $('.toast-container');
                if (toastContainer.length === 0) {
                    toastContainer = $('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
                    $('body').append(toastContainer);
                }

                const $toast = $(toastHtml);
                toastContainer.append($toast);

                setTimeout(() => {
                    $toast.fadeOut(() => $toast.remove());
                }, 5000);
            }

            /**
             * Escape HTML to prevent XSS
             */
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            document.addEventListener('message-list:reply-selected', function(e) {
                window.handleReply(e.detail.messageId, e.detail.messageData);
            });

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
                const ccEmails = $('.claimReinEmailForm #ccEmail').val() || [];
                const bccEmails = $('.claimReinEmailForm #bccEmail').val() || [];

                $('.claimReinEmailForm #toEmail').val(toEmails.join(', '));
            }


            function validateEmailSelection() {
                const toEmails = $('.claimReinEmailForm #contacts').val() || [];
                const ccEmails = $('.claimReinEmailForm #ccEmail').val() || [];
                const bccEmails = $('.claimReinEmailForm #bccEmail').val() || [];

                if (toEmails.length === 0 && ccEmails.length === 0 && bccEmails.length === 0) {
                    toastr.info('Please select at least one recipient');
                    return false;
                }

                const allEmails = [...toEmails, ...ccEmails, ...bccEmails];
                const duplicates = findDuplicateEmails(allEmails);

                if (duplicates.length > 0) {
                    resolveDuplicates('contacts', toEmails);
                }

                return true;
            }
        });
    </script>
@endpush
