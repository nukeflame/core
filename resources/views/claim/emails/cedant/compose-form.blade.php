{{-- <div id="composeForm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" id="composeTitle">Compose New Email</h2>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFormBtn" style="display: none;">
            Start New Email Instead
        </button>
    </div>

    <form id="cedEmailForm" class="claimCedEmailForm" novalidate>
        @csrf
        <input type="hidden" id="cedReplyToId" name="reply_to_id">
        <input type="hidden" id="cedOriginalMessageId" name="original_message_id">
        <input type="hidden" name="claim_no" value="{{ $ClaimRegister->claim_no }}">
        <input type="hidden" name="customer_id" value="{{ $ClaimRegister->customer_id }}">
        {{-- <input type="hidden" name="claim_notice_file" id="cedClaimNoticeFile">
        <input type="hidden" name="debit_note_file" id="CEDdebitNoteFile"> --
<input type="hidden" name="partner_email" id="cedantToEmail">

<!-- Recipients Row -->
<div class="row mb-2">
    <div class="col-md-12">
        <label for="cedToEmail" class="form-label">To: <span class="text-danger">*</span></label>
        <input type="text" class="form-inputs custom-disabled" id="cedToEmail" name="to_email" value="" required
            readonly>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-12">
        <label for="cedContacts" class="form-label">Contacts: <span class="text-danger">*</span></label>
        <select class="form-inputs select2" id="cedContacts" name="contacts[]" multiple></select>
        <div class="invalid-feedback"></div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-6">
        <label for="cedCcEmail" class="form-label">CC:</label>
        <select class="form-inputs select2" id="cedCcEmail" name="cc_email[]" multiple
            aria-placeholder="Select CC Emails"></select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6">
        <label for="cedCccEmail" class="form-label">BCC:</label>
        <select class="form-inputs select2" id="cedCccEmail" name="bcc_email[]" multiple></select>
        <div class="invalid-feedback"></div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-8">
        <label for="cedSubject" class="form-label">Subject: <span class="text-danger">*</span></label>
        <input type="text" class="form-inputs" id="cedSubject" name="subject"
            value="{{ is_array($claimSubject) ? implode(' ', $claimSubject) : $claimSubject }}" required>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-4">
        <label for="cedPriority" class="form-label">Priority:</label>
        <select class="form-inputs select2" id="cedPriority" name="priority">
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
        <label for="cedCategory" class="form-label">Category:</label>
        <select class="form-inputs select2" id="cedCategory" name="category">
            <option value="claim">Claim Notification</option>
            <option value="policy">Policy Communication</option>
            <option value="risk">Risk Assessment</option>
            <option value="settlement">Settlement</option>
            <option value="general">General Correspondence</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6">
        <label for="cedReference" class="form-label">Reference Number:</label>
        <div class="input-group">
            <input type="text" class="form-control color-blk @error('reference') is-invalid @enderror"
                id="cedReference" name="reference" value="{{ old('reference') }}" placeholder="REF-2025-XXXXXX">
            <button type="button" class="btn btn-outline-dark" id="cedGenerateRefBtn">
                Generate
            </button>
            @error('cedReference')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-12">
        <label for="cedMessage" class="form-label">Message: <span class="text-danger">*</span></label>
        <div class="d-flex gap-2 mb-2">
            <button type="button" id="cedToggleEmailBodyBtn" class="btn btn-outline-secondary btn-sm"
                style="display: none">
                <i class="bx bx-dots-horizontal me-1"></i>Toggle Original Email
            </button>
            <button type="button" id="cedInsertTemplateBtn" class="btn btn-outline-primary btn-sm">
                <i class="bx bx-file-blank me-1"></i>Insert Template
            </button>
        </div>

        <div class="email-body hidden" id="cedEmailBody">
            <div class="email-details">
                <h6 class="mb-2"><i class="bx bx-envelope me-1"></i>Original Message Details:</h6>
                <div class="detail-row">
                    <span class="detail-label">From:</span>
                    <span class="detail-value" id="cedOriginalFrom"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Sent:</span>
                    <span class="detail-value" id="cedOriginalSent"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">To:</span>
                    <span class="detail-value" id="cedOriginalTo"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Subject:</span>
                    <span class="detail-value" id="cedOriginalSubject"></span>
                </div>
            </div>

            <div class="email-content-box">
                <div class="original-content" id="cedOriginalContent">
                    <iframe id="cedThreadMessages" style="width:100%;height:400px;border:none;"
                        sandbox="allow-same-origin allow-popups allow-forms allow-scripts"></iframe>
                </div>
            </div>
        </div>

        <textarea class="form-inputs @error('cedMessage') is-invalid @enderror" id="cedMessage" name="message"
            rows="10" required placeholder="Reply message...">{{ $defaultCedantMessage ?? '' }}</textarea>
        <div class="invalid-feedback"></div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <label for="cedFiles" class="form-label fw-bold">
            Attached Files:
        </label>
        <div id="cedAttachedFilesList" class="attached-files-container compose_attachement">
            <div class="row">
                <div class="col-md-4">
                    <a href="#" id="ackLetterLink" target="_blank" rel="noopener noreferrer">
                        <div class="file-item d-flex align-items-center mb-2">
                            <div class="file-icon me-3">
                                <i class="bx bx-file"></i>
                            </div>
                            <div class="file-info flex-grow-1">
                                <h6 class="mb-1">
                                    Ack_Letter_{{ $ClaimRegister->intimation_no }}
                                </h6>
                                <div class="file-meta">
                                    PDF Document
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="#" id="creditNoteLink" target="_blank" rel="noopener noreferrer">
                        <div class="file-item d-flex align-items-center mb-2">
                            <div class="file-icon me-3">
                                <i class="bx bx-file"></i>
                            </div>
                            <div class="file-info flex-grow-1">
                                <h6 class="mb-1">
                                    Credit_Note_{{ $ClaimRegister->intimation_no }}
                                </h6>
                                <div class="file-meta">
                                    PDF Document
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-2 compose_attachement">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                <span id="cedFileCount">
                    2 files attached
                </span>
            </small>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center pt-3 border-top">
    <div class="d-flex gap-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="cedReadReceipt" name="read_receipt">
            <label class="form-check-label" for="readReceipt" style="line-height: 23px;">
                Request read receipt
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="cedScheduleSend" name="schedule_send">
            <label class="form-check-label" for="scheduleSend" style="line-height: 23px;">
                Schedule send
            </label>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-light" id="cedResetFormBtn">
            Reset
        </button>
        <button type="button" class="btn btn-light" id="cedSaveDraftBtn">
            Save Draft
        </button>
        <button type="submit" class="btn btn-primary" id="cedSendReinNotification">
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
</style> --}}
