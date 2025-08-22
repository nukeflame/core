<div class="modal modal-xl fade effect-scale evolution-modal" id="mail-compose-modal" data-bs-backdrop="static"
    aria-labelledby="mail-compose-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="compose-email-form" enctype="multipart/form-data">
                <div class="modal-header">
                    <h6 class="modal-title" id="mail-compose-label"><i class="bx bx-envelope pr-2"></i> Compose Mail
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Evolution Style Toolbar -->
                <div class="evolution-toolbar">
                    <button type="submit" class="evolution-btn" id="sendEmailBtn">
                        <i class="ri-send-plane-2-line"></i> Send
                        <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                    </button>
                    <button type="button" class="evolution-btn" id="saveDraftBtn">
                        <i class="ri-save-line"></i> Save Draft
                    </button>
                    <div class="evolution-separator"></div>
                    <button type="button" class="evolution-btn" onclick="toggleAttachments()">
                        <i class="ri-attachment-2"></i> Attach
                    </button>
                    <button type="button" class="evolution-btn" onclick="insertSignature()">
                        <i class="ri-quill-pen-line"></i> Signature
                    </button>
                    <div class="evolution-separator"></div>
                    <button type="button" class="evolution-btn" onclick="checkSpelling()">
                        <i class="ri-translate-2"></i> Spelling
                    </button>
                    <button type="button" class="evolution-btn" data-bs-toggle="modal"
                        data-bs-target="#template-modal">
                        <i class="ri-file-text-line"></i> Templates
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Evolution Style Fields -->
                    <div class="evolution-fields">
                        <!-- From Field -->
                        <div class="evolution-field-row">
                            <label class="evolution-field-label">From:</label>
                            <input type="email" class="form-control text-muted evolution-field-input" id="fromMail"
                                name="from" value="tech@acentriagroup.com.com" readonly>
                        </div>

                        <!-- To Field -->
                        <div class="evolution-field-row">
                            <label class="evolution-field-label">To:</label>
                            <select class="form-inputs evolution-field-input select2" name="to[]" id="toMail"
                                multiple required>
                                <option value="contact1@example.com">John Doe (contact1@example.com)</option>
                                <option value="contact2@example.com">Jane Smith (contact2@example.com)</option>
                                <option value="contact3@example.com">Bob Johnson (contact3@example.com)</option>
                            </select>
                            <div class="evolution-field-controls">
                                <button type="button" class="evolution-toggle-btn"
                                    onclick="toggleCcBcc()">Cc/Bcc</button>
                                <button type="button" class="evolution-toggle-btn">
                                    <i class="ri-contacts-book-line"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please select at least one recipient.</div>
                        </div>

                        <!-- CC/BCC Fields (Hidden by default) -->
                        <div class="evolution-hidden-fields" id="cc-bcc-fields">
                            <div class="evolution-field-row mb-2">
                                <label class="evolution-field-label">Cc:</label>
                                <select class="form-inputs evolution-field-input select2" name="cc[]" id="mailCC"
                                    multiple>
                                    <option value="contact1@example.com">John Doe (contact1@example.com)</option>
                                    <option value="contact2@example.com">Jane Smith (contact2@example.com)</option>
                                    <option value="contact3@example.com">Bob Johnson (contact3@example.com)</option>
                                </select>
                            </div>
                            <div class="evolution-field-row  mb-2">
                                <label class="evolution-field-label">Bcc:</label>
                                <select class="form-inputs evolution-field-input select2" name="bcc[]" id="mailBcc"
                                    multiple>
                                    <option value="contact1@example.com">John Doe (contact1@example.com)</option>
                                    <option value="contact2@example.com">Jane Smith (contact2@example.com)</option>
                                    <option value="contact3@example.com">Bob Johnson (contact3@example.com)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Subject Field -->
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
                        <!-- Format Toolbar -->
                        <div class="evolution-format-toolbar">
                            <button type="button" class="evolution-format-btn" onclick="formatText('bold')"
                                title="Bold">
                                <strong>B</strong>
                            </button>
                            <button type="button" class="evolution-format-btn" onclick="formatText('italic')"
                                title="Italic">
                                <em>I</em>
                            </button>
                            <button type="button" class="evolution-format-btn" onclick="formatText('underline')"
                                title="Underline">
                                <u>U</u>
                            </button>
                            <div class="evolution-separator"></div>
                            <button type="button" class="evolution-format-btn" onclick="insertList('ul')"
                                title="Bullet List">
                                <i class="ri-list-unordered"></i>
                            </button>
                            <button type="button" class="evolution-format-btn" onclick="insertList('ol')"
                                title="Numbered List">
                                <i class="ri-list-ordered"></i>
                            </button>
                            <div class="evolution-separator"></div>
                            <button type="button" class="evolution-format-btn" onclick="insertLink()"
                                title="Insert Link">
                                <i class="ri-link"></i>
                            </button>
                            <button type="button" class="evolution-format-btn" onclick="insertTable()"
                                title="Insert Table">
                                <i class="ri-table-line"></i>
                            </button>
                        </div>

                        <!-- Message Body Editor -->
                        <div class="mail-compose">
                            <textarea class="evolution-editor" id="mail-compose-editor" name="body" placeholder="Type your message here..."></textarea>
                            <textarea id="mail-compose-content" name="body" class="d-none"></textarea>
                        </div>
                    </div>

                    <!-- Evolution Style Attachment Area -->
                    <div class="evolution-attachment-area" id="attachmentArea">
                        <div class="evolution-attachment-list" id="attachmentList">
                            <!-- Attachments will be added here dynamically -->
                        </div>
                        <input type="file" class="form-control" id="mailAttachments" name="attachments[]"
                            multiple onchange="handleFileSelect(event)">
                        <div class="form-text">Maximum 10MB per file, 25MB total</div>
                    </div>
                </div>

                <!-- Evolution Style Footer -->
                <div class="modal-footer">
                    <div class="evolution-status w-100">
                        <div class="evolution-status-info">
                            <span>Ready</span>
                            <span id="wordCount">Words: 0</span>
                            <span id="characterCount">Characters: 0</span>
                        </div>
                        <div>
                            <span>Plain Text</span>
                        </div>
                    </div>
                    <div class="hidden d-flex justify-content-between w-100">
                        <div>
                            <button type="button" class="btn btn-light me-2" id="saveDraftBtn2">
                                <i class="ri-save-line me-1"></i>Save Draft
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" id="sendEmailBtn2">
                                <i class="ri-send-plane-2-line me-1"></i>Send
                                <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                            </button>
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
        // Toggle Cc/Bcc fields
        function toggleCcBcc() {
            const fields = document.getElementById('cc-bcc-fields');
            fields.classList.toggle('show');
        }

        // Toggle attachment area
        function toggleAttachments() {
            const area = document.getElementById('attachmentArea');
            area.classList.toggle('show');
        }

        // Handle file selection
        function handleFileSelect(event) {
            const files = event.target.files;
            const attachmentList = document.getElementById('attachmentList');

            for (let file of files) {
                const attachmentItem = document.createElement('div');
                attachmentItem.className = 'evolution-attachment-item';
                attachmentItem.innerHTML = `
                    <i class="ri-file-line"></i>
                    <span>${file.name}</span>
                    <button type="button" class="evolution-attachment-remove" onclick="removeAttachment(this)">×</button>
                `;
                attachmentList.appendChild(attachmentItem);
            }

            // Show attachment area if files are added
            if (files.length > 0) {
                document.getElementById('attachmentArea').classList.add('show');
            }
        }

        // Remove attachment
        function removeAttachment(button) {
            button.parentElement.remove();

            // Hide attachment area if no attachments left
            const attachmentList = document.getElementById('attachmentList');
            if (attachmentList.children.length === 0) {
                document.getElementById('attachmentArea').classList.remove('show');
            }
        }

        // Update word and character count
        function updateCounts() {
            const messageBody = document.getElementById('mail-compose-editor');
            const text = messageBody.value;
            const wordCount = text.trim() ? text.trim().split(/\s+/).length : 0;
            const charCount = text.length;

            document.getElementById('wordCount').textContent = `Words: ${wordCount}`;
            document.getElementById('characterCount').textContent = `Characters: ${charCount}`;
        }

        // Add event listener for text changes
        document.getElementById('mail-compose-editor').addEventListener('input', updateCounts);

        // Toolbar functions
        function insertSignature() {
            const messageBody = document.getElementById('mail-compose-editor');
            const signature = '\n\n--\nBest regards,\nYour Name\nyour.email@example.com';
            messageBody.value += signature;
            updateCounts();
        }

        function checkSpelling() {
            alert('Spell check complete!');
        }

        function formatText(command) {
            alert(`Format: ${command}`);
        }

        function insertList(type) {
            alert(`Insert ${type} list`);
        }

        function insertLink() {
            const url = prompt('Enter URL:');
            if (url) {
                const messageBody = document.getElementById('mail-compose-editor');
                messageBody.value += ` ${url}`;
                updateCounts();
            }
        }

        function insertTable() {
            alert('Table insertion dialog would open here');
        }

        // Template selection
        document.addEventListener('DOMContentLoaded', function() {
            const templateButtons = document.querySelectorAll('.select-template');
            templateButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const templateCard = this.closest('.evolution-template-card');
                    const templateType = templateCard.dataset.template;

                    // Template content based on type
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
                        document.getElementById('mailSubject').value = template.subject;
                        document.getElementById('mail-compose-editor').value = template.body;
                        updateCounts();
                    }

                    // Close template modal
                    const templateModal = bootstrap.Modal.getInstance(document.getElementById(
                        'template-modal'));
                    templateModal.hide();
                });
            });
        });

        // Form validation and submission
        document.getElementById('compose-email-form').addEventListener('submit', function(e) {
            e.preventDefault();

            // Basic validation
            const toField = document.getElementById('toMail');
            const subjectField = document.getElementById('mailSubject');

            let isValid = true;

            if (toField.selectedOptions.length === 0) {
                toField.classList.add('is-invalid');
                isValid = false;
            } else {
                toField.classList.remove('is-invalid');
            }

            if (!subjectField.value.trim()) {
                subjectField.classList.add('is-invalid');
                isValid = false;
            } else {
                subjectField.classList.remove('is-invalid');
            }

            if (isValid) {
                // Show loading spinner
                const sendBtns = document.querySelectorAll('#sendEmailBtn, #sendEmailBtn2');
                sendBtns.forEach(btn => {
                    const spinner = btn.querySelector('.spinner-border');
                    if (spinner) {
                        spinner.classList.remove('d-none');
                    }
                    btn.disabled = true;
                });

                // Simulate sending
                setTimeout(() => {
                    alert('Email sent successfully!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById(
                        'mail-compose-modal'));
                    modal.hide();

                    // Reset form
                    this.reset();
                    updateCounts();

                    // Reset buttons
                    sendBtns.forEach(btn => {
                        const spinner = btn.querySelector('.spinner-border');
                        if (spinner) {
                            spinner.classList.add('d-none');
                        }
                        btn.disabled = false;
                    });
                }, 2000);
            }
        });

        // Save draft functionality
        document.querySelectorAll('#saveDraftBtn, #saveDraftBtn2').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Draft saved successfully!');
            });
        });

        // Initialize
        updateCounts();
    </script>
@endpush
