<div id="bdComposeFormDiv">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0" id="composeTitle">Compose New Email</h2>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFormBtn" style="display: none;">
            Start New Email Instead
        </button>
    </div>

    <!-- Recipients Row -->
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
        <div class="col-md-6">
            <label for="ccEmail" class="form-label">CC:</label>
            <select class="form-inputs select2" id="ccEmail" name="cc_email[]" multiple
                placeholder="-- Select CC emails --"></select>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label for="bccEmail" class="form-label">BCC:</label>
            <select class="form-inputs select2" id="bccEmail" name="bcc_email[]" multiple
                placeholder="-- Select BCC emails --"></select>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-8">
            <label for="subject" class="form-label">Subject: <span class="text-danger">*</span></label>
            <input type="text" class="form-inputs" id="subject" name="subject" placeholder="Subject"
                {{-- value="{{ is_array($claimSubject) ? implode(' ', $claimSubject) : $claimSubject }}" --}} required>
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
                        <iframe id="threadMessages" style="width:100%;height:400px;border:none;"
                            sandbox="allow-same-origin allow-popups allow-forms allow-scripts"></iframe>
                    </div>
                </div>
            </div>

            <textarea class="form-inputs resize-none @error('message') is-invalid @enderror" id="message" name="message"
                rows="10" required placeholder="Reply message...">{{ $defaultBdMessage ?? '' }}</textarea>
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
                                        {{-- Claim_Notice_{{ $ClaimRegister->intimation_no }} --}}
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
                                        {{-- Debit_Note_{{ $ClaimRegister->intimation_no }} --}}
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
                    <span id="fileCount">
                        {{ 2 + ($filesAttached && count($filesAttached) > 0 ? count($filesAttached) : 0) }}
                        files attached
                    </span>
                </small>
            </div>
        </div>
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

    .select2-container .select2-search--inline .select2-search__field {
        margin-block-start: 7px !important;
        font-size: 14px;
    }
</style>
