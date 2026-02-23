<div id="bdComposeFormDiv" class="customScrollBar">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0" id="composeTitle">Compose New Email</h2>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="toEmail" class="form-label">To: <span class="text-danger">*</span></label>
            <input type="text" class="form-inputs custom-disabled" id="toEmail" name="to_email" value=""
                required readonly>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-12 mb-3">
            <label for="toContacts" class="form-label">Contacts: <span class="text-danger">*</span></label>
            <select class="form-inputs select2" id="toContacts" name="contacts[]" multiple
                placeholder="-- Select toContacts --"></select>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label for="ccEmail" class="form-label">CC:</label>
            <select class="form-inputs select2" id="ccEmail" name="cc_email[]" multiple
                placeholder="-- Select CC emails --"></select>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label for="bccEmail" class="form-label">BCC:</label>
            <select class="form-inputs select2" id="bccEmail" name="bcc_email[]" multiple
                placeholder="-- Select BCC emails --"></select>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-8">
            <label for="subject" class="form-label">Subject: <span class="text-danger">*</span></label>
            <input type="text" class="form-inputs subject" id="subject" name="subject" placeholder="Subject">
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

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="category" class="form-label">Category:</label>
            <select class="form-inputs select2" id="category" name="category">
                <option value="lead">Lead</option>
                <option value="proposal">Proposal</option>
                <option value="negotiation">Negotiation</option>
                <option value="won">Won</option>
                <option value="lost">Lost</option>
                <option value="final">Final</option>
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

    <div class="row mb-3">
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
                        <iframe id="threadMessages" style="width:100%;height:min(400px, 40vh);border:none;"
                            sandbox="allow-popups allow-forms allow-scripts"></iframe>
                    </div>
                </div>
            </div>

            <textarea class="form-inputs message resize-none @error('message') is-invalid @enderror" id="message"
                name="message" rows="10" placeholder="Enter Message..."></textarea>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="form-check mb-2" id="replyAttachmentOptionWrap" style="display: none;">
        <input class="form-check-input" type="checkbox" value="1" id="includeReplyAttachments"
            name="include_reply_attachments">
        <label class="form-check-label" for="includeReplyAttachments">
            Include attached files in this reply
        </label>
    </div>

    <div id="attachedFilesList">
        <label for="message" class="form-label fw-bold">
            Attached Files:
        </label>
        <div class="row"></div>
    </div>
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
        max-height: 700px;
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
        max-width: 260px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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

    .select2-container .select2-search--inline .select2-search__field {
        margin-block-start: 7px !important;
        font-size: 14px;
    }

    #bdComposeFormDiv {
        height: auto;
        max-height: calc(100vh - 360px);
        overflow-x: hidden;
        overflow-y: auto;
        margin-right: -8px;
        padding-right: 14px;
    }

    @media (max-width: 768px) {
        #bdComposeFormDiv {
            max-height: calc(100vh - 320px);
        }
    }
</style>
