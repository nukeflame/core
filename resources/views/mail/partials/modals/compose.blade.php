<div class="modal fade effect-scale evolution-modal" id="mail-compose-modal" data-bs-backdrop="static"
    aria-labelledby="mail-compose-label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="compose-email-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title" id="mail-compose-label"><i class="bx bx-envelope pr-2"></i> Compose Mail
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="evolution-toolbar">
                    <button type="submit" class="evolution-btn" id="sendEmailBtn">
                        <i class="ri-send-plane-2-line"></i> Send
                        <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                    </button>
                    <button type="button" class="evolution-btn" id="saveDraftBtn">
                        <i class="ri-save-line"></i> Save Draft
                    </button>
                    <div class="evolution-separator"></div>
                    <button type="button" class="evolution-btn" id="toggleAttachments">
                        <i class="ri-attachment-2"></i> Attach
                    </button>
                    <button type="button" class="evolution-btn" id="insertSignature">
                        <i class="ri-quill-pen-line"></i> Signature
                    </button>
                    <div class="evolution-separator"></div>
                    <button type="button" class="evolution-btn" id="checkSpelling">
                        <i class="ri-translate-2"></i> Spelling
                    </button>
                    <button type="button" class="evolution-btn" data-bs-toggle="modal"
                        data-bs-target="#template-modal">
                        <i class="ri-file-text-line"></i> Templates
                    </button>
                </div>

                <div class="modal-body">
                    <div class="evolution-fields">
                        <div class="evolution-field-row">
                            <label class="evolution-field-label">From:</label>
                            <input type="email" class="form-control text-muted evolution-field-input" id="fromMail"
                                name="from" value="tech@acentriagroup.com" readonly>
                        </div>

                        <div class="evolution-field-row">
                            <label class="evolution-field-label">To:</label>
                            <select class="form-inputs evolution-field-input select2" name="to[]" id="toMail"
                                multiple required>
                                <option value="pknuek@gmail.com">Ken Peters (pknuek@gmail.com)</option>
                                <option value="nueklabs@gmail.com">John Doe (nueklabs@gmail.com)</option>
                            </select>
                            <div class="evolution-field-controls">
                                <button type="button" class="evolution-toggle-btn" id="toggleCcBcc">Cc/Bcc</button>
                                <button type="button" class="evolution-toggle-btn">
                                    <i class="ri-contacts-book-line"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please select at least one recipient.</div>
                        </div>

                        <div class="evolution-hidden-fields" id="cc-bcc-fields">
                            <div class="evolution-field-row mb-2">
                                <label class="evolution-field-label">Cc:</label>
                                <select class="form-inputs evolution-field-input select2" name="cc[]" id="mailCC"
                                    multiple>
                                    <option value="pknuek@gmail.com">Ken Peters (pknuek@gmail.com)</option>
                                </select>
                            </div>
                            <div class="evolution-field-row  mb-2">
                                <label class="evolution-field-label">Bcc:</label>
                                <select class="form-inputs evolution-field-input select2" name="bcc[]" id="mailBcc"
                                    multiple>
                                    <option value="pknuek@gmail.com">Ken Peters (pknuek@gmail.com)</option>
                                </select>
                            </div>
                        </div>

                        <div class="evolution-field-row">
                            <label class="evolution-field-label">Subject:</label>
                            <input type="text" class="form-control evolution-field-input" id="mailSubject"
                                name="subject" placeholder="Enter email subject" required>
                            <div class="evolution-field-controls">
                                {{-- <select class="form-input select2 evolution-priority-select" id="mailPriority"
                                    style="width: 50px;" name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="low">Low</option>
                                    <option value="high">High</option>
                                </select> --}}
                            </div>
                            <div class="invalid-feedback">Please enter a subject.</div>
                        </div>
                    </div>

                    <div class="evolution-content-area">
                        <div id="toolbar-container">
                            <span class="ql-formats">
                                <select class="ql-font"></select>
                                <select class="ql-size"></select>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                                <button class="ql-strike"></button>
                            </span>
                            <span class="ql-formats">
                                <select class="ql-color"></select>
                                <select class="ql-background"></select>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-script" value="sub"></button>
                                <button class="ql-script" value="super"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-header" value="1"></button>
                                <button class="ql-header" value="2"></button>
                                <button class="ql-blockquote"></button>
                                <button class="ql-code-block"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-list" value="ordered"></button>
                                <button class="ql-list" value="bullet"></button>
                                <button class="ql-indent" value="-1"></button>
                                <button class="ql-indent" value="+1"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-direction" value="rtl"></button>
                                <select class="ql-align"></select>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-link"></button>
                                <button class="ql-image"></button>
                                <button class="ql-video"></button>
                                <button class="ql-formula"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-clean"></button>
                            </span>
                        </div>
                        {{-- <div class="evolution-format-toolbar hidden">
                            <button type="button" class="evolution-format-btn formatText" title="Bold"
                                data-command="bold">
                                <strong>B</strong>
                            </button>
                            <button type="button" class="evolution-format-btn formatText" data-command="italic"
                                title="Italic">
                                <em>I</em>
                            </button>
                            <button type="button" class="evolution-format-btn formatText"
                                onclick="formatText('underline')" title="Underline">
                                <u>U</u>
                            </button>
                            <div class="evolution-separator"></div>
                            <button type="button" class="evolution-format-btn insertList" data-type="ul"
                                title="Bullet List">
                                <i class="ri-list-unordered"></i>
                            </button>
                            <button type="button" class="evolution-format-btn insertList" data-type="ol"
                                title="Numbered List">
                                <i class="ri-list-ordered"></i>
                            </button>
                            <div class="evolution-separator"></div>
                            <button type="button" class="evolution-format-btn insertLink" title="Insert Link">
                                <i class="ri-link"></i>
                            </button>
                            <button type="button" class="evolution-format-btn insertTable" title="Insert Table">
                                <i class="ri-table-line"></i>
                            </button>
                        </div> --}}

                        <div class="mail-compose evol">
                            <div class="evolution-editor" id="mail-compose-editor" name="body"></div>
                        </div>
                    </div>

                    <div class="evolution-attachment-area" id="attachmentArea">
                        <div class="evolution-attachment-list" id="attachmentList">
                        </div>
                        <input type="file" class="form-control" id="fileInput" name="attachments[]" multiple>
                        <div class="form-text">Maximum 10MB per file, 25MB total</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="evolution-status w-100">
                        <div class="evolution-status-info">
                            <span>Ready</span>
                            <span id="wordCount">Words: 0</span>
                            <span id="characterCount">Characters: 0</span>
                        </div>
                        <div>
                            <span>Rich Text</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade evolution-template-modal" id="template-modal" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Email Templates</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card evolution-template-card" data-template="meeting-request">
                            <div class="card-body">
                                <h6 class="card-title">Meeting Request</h6>
                                <p class="card-text text-muted">Schedule a meeting with...</p>
                                <button class="btn btn-sm btn-outline-primary select-template">Use
                                    Template</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card evolution-template-card" data-template="follow-up">
                            <div class="card-body">
                                <h6 class="card-title">Follow Up</h6>
                                <p class="card-text text-muted">Following up on our previous...</p>
                                <button class="btn btn-sm btn-outline-primary select-template">Use
                                    Template</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card evolution-template-card" data-template="thank-you">
                            <div class="card-body">
                                <h6 class="card-title">Thank You</h6>
                                <p class="card-text text-muted">Thank you for your time...</p>
                                <button class="btn btn-sm btn-outline-primary select-template">Use
                                    Template</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card evolution-template-card" data-template="proposal">
                            <div class="card-body">
                                <h6 class="card-title">Proposal</h6>
                                <p class="card-text text-muted">We are pleased to present...</p>
                                <button class="btn btn-sm btn-outline-primary select-template">Use
                                    Template</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            let quillCompose = null;
            const $composeEditor = $("#mail-compose-editor");
            const routes = {
                sendEmail: "{{ route('mail.send') }}",
            };

            if ($composeEditor.length) {
                quillCompose = new Quill("#mail-compose-editor", {
                    modules: {
                        toolbar: '#toolbar-container',
                    },
                    theme: "snow",
                    placeholder: "Write your message here...",
                });

                const quillEditor = document.querySelector('#mail-compose-editor .ql-editor');
                if (quillEditor) {
                    quillEditor.style.minHeight = '300px';
                }

                quillCompose.on('text-change', function(e) {
                    updateCounts();
                });
            }

            $('#toggleCcBcc').on('click', function() {
                $('#cc-bcc-fields').toggleClass('show');
            });

            $('#toggleAttachments').on('click', function() {
                $('#attachmentArea').toggleClass('show');
            });

            $('#fileInput').on('change', function(e) {
                const files = e.target.files;
                const $attachmentList = $('#attachmentList');

                $.each(files, function(_, file) {
                    const $item = $(`
                    <div class="evolution-attachment-item">
                        <i class="ri-file-line"></i>
                        <span>${file.name}</span>
                        <button type="button" class="evolution-attachment-remove">×</button>
                    </div>
                `);
                    $attachmentList.append($item);
                });

                if (files.length > 0) {
                    $('#attachmentArea').addClass('show');
                }
            });

            $('#attachmentList').on('click', '.evolution-attachment-remove', function() {
                $(this).parent().remove();
                if ($('#attachmentList').children().length === 0) {
                    $('#attachmentArea').removeClass('show');
                }
            });

            function updateCounts() {
                let text = '';
                let wordCount = 0;
                let charCount = 0;

                if (quillCompose) {
                    text = quillCompose.getText().trim();
                    wordCount = text ? text.split(/\s+/).filter(word => word.length > 0).length : 0;
                    charCount = text.length;
                } else {
                    text = $('#mail-compose-editor').val() || '';
                    wordCount = text.trim() ? text.trim().split(/\s+/).length : 0;
                    charCount = text.length;
                }

                $('#wordCount').text(`Words: ${wordCount}`);
                $('#characterCount').text(`Characters: ${charCount}`);
            }

            $('#insertSignature').on('click', function() {
                if (quillCompose) {
                    const signature = '\n\n--\nBest regards,\nYour Name\nyour.email@example.com';
                    const currentLength = quillCompose.getLength();
                    quillCompose.insertText(currentLength - 1, signature);
                } else {
                    $('.evol #mail-compose-editor').val(function(_, val) {
                        return val + '\n\n--\nBest regards,\nYour Name\nyour.email@example.com';
                    });
                }
                updateCounts();
            });

            $('#checkSpelling').on('click', function() {
                alert('Spell check complete!');
            });

            $('.insertList').on('click', function() {
                const type = $(this).data('type');
                alert(`Insert ${type} list`);
            });

            $('#insertLink').on('click', function() {
                const url = prompt('Enter URL:');
                if (url) {
                    if (quillCompose) {
                        const selection = quillCompose.getSelection();
                        if (selection) {
                            quillCompose.format('link', url);
                        } else {
                            const currentLength = quillCompose.getLength();
                            quillCompose.insertText(currentLength - 1, ' ' + url, 'link', url);
                        }
                    } else {
                        $('.evol #mail-compose-editor').val(function(_, val) {
                            return val + ' ' + url;
                        });
                    }
                    updateCounts();
                }
            });

            $('#insertTable').on('click', function() {
                alert('Table insertion dialog would open here');
            });

            $('.select-template').on('click', function() {
                const templateType = $(this).closest('.evolution-template-card').data('template');

                const templates = {
                    'meeting-request': {
                        subject: 'Meeting Request',
                        body: 'Dear [Name],\n\nI would like to schedule a meeting to discuss...\n\nBest regards,\n[Your Name]'
                    },
                    'follow-up': {
                        subject: 'Follow Up',
                        body: 'Dear [Name],\n\nFollowing up on our previous conversation...\n\nBest regards,\n[Your Name]'
                    },
                    'thank-you': {
                        subject: 'Thank You',
                        body: 'Dear [Name],\n\nThank you for your time and consideration...\n\nBest regards,\n[Your Name]'
                    },
                    'proposal': {
                        subject: 'Business Proposal',
                        body: 'Dear [Name],\n\nWe are pleased to present our proposal...\n\nBest regards,\n[Your Name]'
                    }
                };

                const template = templates[templateType];
                if (template) {
                    $('#mailSubject').val(template.subject);

                    if (quillCompose) {
                        quillCompose.setText(template.body);
                    } else {
                        $('.evol #mail-compose-editor').val(template.body);
                    }
                    updateCounts();
                }

                const templateModal = bootstrap.Modal.getInstance($('#template-modal')[0]);
                templateModal.hide();
            });

            $('#compose-email-form').on('submit', async function(e) {
                e.preventDefault();
                let isValid = true;
                const $toField = $('#toMail');
                const $subjectField = $('#mailSubject');

                if ($toField.val() === null || $toField.val().length === 0) {
                    $toField.addClass('is-invalid');
                    isValid = false;
                } else {
                    $toField.removeClass('is-invalid');
                }

                if (!$.trim($subjectField.val())) {
                    $subjectField.addClass('is-invalid');
                    isValid = false;
                } else {
                    $subjectField.removeClass('is-invalid');
                }

                if (isValid) {
                    const $form = $('#compose-email-form');
                    const formData = new FormData($form[0]);
                    const $sendBtn = $("#sendEmailBtn");
                    const $spinner = $sendBtn.find(".spinner-border");

                    if (quillCompose) {
                        formData.set("body", quillCompose.root.innerHTML);
                    }

                    // // Debug FormData contents
                    // for (let pair of formData.entries()) {
                    //     console.log(`[DEBUG] FormData: ${pair[0]} =`, pair[1]);
                    // }

                    try {
                        $sendBtn.prop("disabled", true);
                        $spinner.removeClass("d-none");

                        const result = await $.ajax({
                            url: routes.sendEmail,
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        console.log('[DEBUG] AJAX success response:', result);
                        // Uncomment when ready to handle success
                        // if (result.success) {
                        //     console.log('[DEBUG] Email sent successfully');
                        //     $("#mail-compose-modal").modal("hide");
                        //     $form[0].reset();
                        //     if (quillCompose) quillCompose.setContents([]);
                        //     updateCounts();
                        // } else {
                        //     console.log('[DEBUG] Server reported failure:', result.message);
                        // }
                    } catch (error) {
                        showError("Network error occurred");
                        console.error("Send email error:", error);

                    } finally {
                        $sendBtn.prop("disabled", false);
                        $spinner.addClass("d-none");
                        updateCounts();
                        if (quillCompose) {
                            quillCompose.setContents([]);
                        }
                    }
                }
            });

            $('#saveDraftBtn, #saveDraftBtn2').on('click', function() {
                if (quillCompose) {
                    console.log('Draft HTML Content:', quillCompose.root.innerHTML);
                    console.log('Draft Text Content:', quillCompose.getText());
                }

                alert('Draft saved successfully!');
            });

            function showError(message) {
                showToast(message, "error");
            }

            function showToast(message, type = "info", options = {}) {
                const defaultOptions = {
                    timeOut: 5000,
                    extendedTimeOut: 1000,
                    closeButton: true,
                    progressBar: true,
                    preventDuplicates: true,
                    onclick: null,
                    onShown: null,
                    onHidden: null,
                    ...options,
                };

                toastr.options = defaultOptions;

                switch (type) {
                    case "success":
                        return toastr.success(message);
                    case "error":
                        return toastr.error(message);
                    case "warning":
                        return toastr.warning(message);
                    case "info":
                    default:
                        return toastr.info(message);
                }
            }

            updateCounts();
        });
    </script>
@endpush
