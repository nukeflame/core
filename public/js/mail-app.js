class MailApp {
    constructor(config) {
        this.config = config;
        this.currentEmail = null;
        this.selectedEmails = new Set();
        this.quillCompose = null;
        this.quillReply = null;
        this.$body = $("body");
        this.$document = $(document);

        this.init();
    }

    init() {
        this.initializeEditors();
        this.bindEvents();
        // this.initializeSelects();
        this.setupNotifications();
    }

    initializeEditors() {
        // Initialize Quill editor for compose
        const $composeEditor = $("#mail-compose-editor");
        if ($composeEditor.length) {
            this.quillCompose = new Quill("#mail-compose-editor", {
                theme: "snow",
                modules: {
                    toolbar: [
                        [{ header: [1, 2, false] }],
                        ["bold", "italic", "underline", "strike"],
                        [{ color: [] }, { background: [] }],
                        [{ list: "ordered" }, { list: "bullet" }],
                        [{ align: [] }],
                        ["link", "image"],
                        ["clean"],
                    ],
                },
                placeholder: "Write your message here...",
            });
        }

        // Initialize Quill editor for reply
        const $replyEditor = $("#mail-reply-editor");
        if ($replyEditor.length) {
            this.quillReply = new Quill("#mail-reply-editor", {
                theme: "snow",
                modules: {
                    toolbar: [
                        ["bold", "italic", "underline"],
                        [{ list: "ordered" }, { list: "bullet" }],
                        ["link"],
                        ["clean"],
                    ],
                },
                placeholder: "Write your reply here...",
            });
        }
    }

    initializeSelects() {
        // Initialize Select2 for email recipients with enhanced options
        $("#toMail, #mailCC, #mailBcc").select2({
            tags: true,
            tokenSeparators: [",", " "],
            placeholder: "Enter email addresses",
            allowClear: true,
            width: "100%",
            createTag: (params) => {
                const term = params.term.trim();
                if (term === "") return null;

                // Enhanced email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(term)) return null;

                return {
                    id: term,
                    text: term,
                    newTag: true,
                };
            },
            templateResult: (data) => {
                if (data.newTag) {
                    return $(
                        '<span class="badge bg-primary">New: ' +
                            data.text +
                            "</span>"
                    );
                }
                return data.text;
            },
        });

        // Initialize priority select
        $("#mailPriority").select2({
            minimumResultsForSearch: Infinity,
            width: "100%",
        });
    }

    bindEvents() {
        // Email item clicks
        this.$document.on("click", ".email-content", (e) => {
            const $emailItem = $(e.currentTarget).closest(".email-item");
            const emailId = $emailItem.data("email-id");
            if (emailId) {
                this.loadEmail(emailId);
            }
        });

        // Checkbox selection
        this.$document.on("change", ".email-checkbox", (e) => {
            this.handleEmailSelection($(e.target));
        });

        this.$document.on("change", "#checkAll", (e) => {
            this.handleSelectAll($(e.target).is(":checked"));
        });

        // Star/unstar emails
        this.$document.on("click", ".mail-starred", (e) => {
            e.preventDefault();
            e.stopPropagation();
            const $btn = $(e.currentTarget);
            const emailId = $btn.data("email-id");
            this.toggleStar(emailId, $btn);
        });

        // Folder navigation
        this.$document.on("click", ".mail-folder-link", (e) => {
            e.preventDefault();
            const $link = $(e.currentTarget);
            const folder = $link.data("folder");
            this.switchFolder(folder);
        });

        // Compose form submission
        $("#compose-email-form").on("submit", (e) => {
            e.preventDefault();
            this.sendEmail();
        });

        // Search functionality
        $("#emailSearch").on(
            "input",
            this.debounce((e) => {
                this.searchEmails($(e.target).val());
            }, 300)
        );

        // Email actions
        this.$document.on("click", "[data-action]", (e) => {
            e.preventDefault();
            const action = $(e.currentTarget).data("action");
            this.handleEmailAction(action, $(e.currentTarget));
        });

        // Sync button
        $("#syncEmailsBtn").on("click", () => {
            this.syncEmails();
        });

        // Template selection
        this.$document.on("click", ".select-template", (e) => {
            const $templateCard = $(e.currentTarget).closest(".template-card");
            const template = $templateCard.data("template");
            this.loadTemplate(template);
        });
    }

    async loadEmail(emailId) {
        try {
            this.showLoading("email-content");

            const response = await fetch(
                this.config.routes.getEmail.replace(":id", emailId)
            );
            const email = await response.json();

            if (response.ok) {
                this.displayEmail(email);
                this.markAsRead(emailId);
                this.currentEmail = email;
            } else {
                this.showError("Failed to load email");
            }
        } catch (error) {
            this.showError("Network error occurred");
            console.error("Load email error:", error);
        } finally {
            this.hideLoading("email-content");
        }
    }

    displayEmail(email) {
        const emailContent = document.querySelector(".mails-information");
        if (!emailContent) return;

        // Update header
        const header = emailContent.querySelector(".mail-info-header");
        header.innerHTML = `
            <div class="me-1">
                <span class="avatar avatar-md online me-2 avatar-rounded mail-msg-avatar">
                    ${
                        email.from.avatar
                            ? `<img src="${email.from.avatar}" alt="">`
                            : `<div class="avatar-initial">${email.from.name
                                  .charAt(0)
                                  .toUpperCase()}</div>`
                    }
                </span>
            </div>
            <div class="flex-fill">
                <h6 class="mb-0 fw-semibold">${email.from.name}</h6>
                <span class="text-muted fs-12">${
                    email.from.emailAddress.address
                }</span>
            </div>
            <div class="mail-action-icons">
                <button class="btn btn-icon btn-light" data-action="star" data-email-id="${
                    email.id
                }">
                    <i class="ri-star-line"></i>
                </button>
                <button class="btn btn-icon btn-light ms-1" data-action="archive" data-email-id="${
                    email.id
                }">
                    <i class="ri-inbox-archive-line"></i>
                </button>
                <button class="btn btn-icon btn-light ms-1" data-action="spam" data-email-id="${
                    email.id
                }">
                    <i class="ri-spam-2-line"></i>
                </button>
                <button class="btn btn-icon btn-light ms-1" data-action="delete" data-email-id="${
                    email.id
                }">
                    <i class="ri-delete-bin-line"></i>
                </button>
                <button class="btn btn-icon btn-light ms-1" data-action="reply" data-email-id="${
                    email.id
                }">
                    <i class="ri-reply-line"></i>
                </button>
            </div>
        `;

        // Update body
        const body = emailContent.querySelector(".mail-info-body");
        const receivedDate = new Date(email.receivedDateTime).toLocaleString();

        body.innerHTML = `
            <div class="d-sm-flex d-block align-items-center justify-content-between mb-4">
                <div>
                    <p class="fs-20 fw-semibold mb-0">${email.subject}</p>
                </div>
                <div class="float-end">
                    <span class="me-2 fs-12 text-muted">${receivedDate}</span>
                </div>
            </div>
            <div class="main-mail-content mb-4">
                ${email.body.content}
            </div>
            ${this.renderAttachments(email.attachments)}
            <div class="mb-3">
                <span class="fs-14 fw-semibold">
                    <i class="ri-reply-all-line me-1 align-middle d-inline-block"></i>Reply:
                </span>
            </div>
            <div class="mail-reply">
                <div id="mail-reply-editor"></div>
            </div>
        `;

        // Reinitialize reply editor
        this.initializeReplyEditor();
    }

    renderAttachments(attachments) {
        if (!attachments || attachments.length === 0) return "";

        const totalSize = attachments.reduce((sum, att) => sum + att.size, 0);
        const formattedSize = this.formatFileSize(totalSize);

        return `
            <div class="mail-attachments mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mb-0">
                        <span class="fs-14 fw-semibold">
                            <i class="ri-attachment-2 me-1 align-middle"></i>Attachments (${formattedSize}):
                        </span>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-success-light" onclick="downloadAllAttachments()">Download All</button>
                    </div>
                </div>
                <div class="mt-2 d-flex flex-wrap">
                    ${attachments
                        .map((att) => this.renderAttachment(att))
                        .join("")}
                </div>
            </div>
        `;
    }

    renderAttachment(attachment) {
        const iconClass = this.getFileIcon(attachment.name);
        const size = this.formatFileSize(attachment.size);

        return `
            <a href="#" class="mail-attachment border me-2 mb-2" onclick="downloadAttachment('${attachment.id}')">
                <div class="attachment-icon">
                    <i class="${iconClass} fs-24"></i>
                </div>
                <div class="lh-1">
                    <p class="mb-1 attachment-name text-truncate">${attachment.name}</p>
                    <p class="mb-0 fs-11 text-muted">${size}</p>
                </div>
            </a>
        `;
    }

    async sendEmail() {
        const form = document.getElementById("compose-email-form");
        const formData = new FormData(form);
        const sendBtn = document.getElementById("sendEmailBtn");
        const spinner = sendBtn.querySelector(".spinner-border");

        // Get content from Quill editor
        if (this.quillCompose) {
            formData.set("body", this.quillCompose.root.innerHTML);
        }

        try {
            sendBtn.disabled = true;
            spinner.classList.remove("d-none");

            const response = await fetch(this.config.routes.sendEmail, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess("Email sent successfully!");
                bootstrap.Modal.getInstance(
                    document.getElementById("mail-compose-modal")
                ).hide();
                form.reset();
                if (this.quillCompose) this.quillCompose.setContents([]);
                this.refreshEmailList();
            } else {
                this.showError(result.message || "Failed to send email");
            }
        } catch (error) {
            this.showError("Network error occurred");
            console.error("Send email error:", error);
        } finally {
            sendBtn.disabled = false;
            spinner.classList.add("d-none");
        }
    }

    async syncEmails() {
        try {
            this.showLoading("email-list");

            const response = await fetch(this.config.routes.outlookSync, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            const result = await response.json();

            if (result.synced) {
                this.showSuccess("Emails synced successfully!");
                this.refreshEmailList();
            } else {
                this.showError("Failed to sync emails");
            }
        } catch (error) {
            this.showError("Network error occurred");
            console.error("Sync error:", error);
        } finally {
            this.hideLoading("email-list");
        }
    }

    async toggleStar(emailId, button) {
        try {
            const isStarred = button.classList.contains("starred");
            const icon = button.querySelector("i");

            // Optimistic update
            button.classList.toggle("starred");
            icon.className = isStarred
                ? "ri-star-line fs-14"
                : "ri-star-fill fs-14";

            const response = await fetch(`/mail/star/${emailId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.config.csrf,
                },
                body: JSON.stringify({ starred: !isStarred }),
            });

            if (!response.ok) {
                // Revert on failure
                button.classList.toggle("starred");
                icon.className = isStarred
                    ? "ri-star-fill fs-14"
                    : "ri-star-line fs-14";
                this.showError("Failed to update star status");
            }
        } catch (error) {
            this.showError("Network error occurred");
            console.error("Star toggle error:", error);
        }
    }

    handleEmailSelection(checkbox) {
        const emailId = checkbox.value;

        if (checkbox.checked) {
            this.selectedEmails.add(emailId);
        } else {
            this.selectedEmails.delete(emailId);
        }

        this.updateSelectionUI();
    }

    handleSelectAll(checked) {
        const checkboxes = document.querySelectorAll(".email-checkbox");

        checkboxes.forEach((checkbox) => {
            checkbox.checked = checked;
            this.handleEmailSelection(checkbox);
        });
    }

    updateSelectionUI() {
        const count = this.selectedEmails.size;
        const selectionInfo = document.getElementById("selection-info");

        if (selectionInfo) {
            if (count > 0) {
                selectionInfo.textContent = `${count} email(s) selected`;
                selectionInfo.classList.remove("d-none");
            } else {
                selectionInfo.classList.add("d-none");
            }
        }
    }

    searchEmails(query) {
        const emailItems = document.querySelectorAll(".email-item");

        emailItems.forEach((item) => {
            const subject = item
                .querySelector(".email-subject")
                .textContent.toLowerCase();
            const sender = item
                .querySelector(".sender-name")
                .textContent.toLowerCase();
            const preview = item
                .querySelector(".email-preview")
                .textContent.toLowerCase();

            const matches =
                subject.includes(query.toLowerCase()) ||
                sender.includes(query.toLowerCase()) ||
                preview.includes(query.toLowerCase());

            item.style.display = matches ? "block" : "none";
        });
    }

    async handleEmailAction(action, element) {
        const emailId = element.dataset.emailId;

        switch (action) {
            case "delete":
                await this.deleteEmail(emailId);
                break;
            case "archive":
                await this.archiveEmail(emailId);
                break;
            case "spam":
                await this.markAsSpam(emailId);
                break;
            case "reply":
                this.showReplyEditor();
                break;
            case "forward":
                this.forwardEmail(emailId);
                break;
            case "mark-read":
                await this.markAsRead(emailId);
                break;
            case "mark-unread":
                await this.markAsUnread(emailId);
                break;
        }
    }

    async deleteEmail(emailId) {
        if (!confirm("Are you sure you want to delete this email?")) return;

        try {
            const response = await fetch(`/mail/delete/${emailId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            if (response.ok) {
                this.showSuccess("Email deleted successfully");
                this.removeEmailFromList(emailId);
            } else {
                this.showError("Failed to delete email");
            }
        } catch (error) {
            this.showError("Network error occurred");
            console.error("Delete error:", error);
        }
    }

    async archiveEmail(emailId) {
        try {
            const response = await fetch(`/mail/archive/${emailId}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            if (response.ok) {
                this.showSuccess("Email archived successfully");
                this.removeEmailFromList(emailId);
            } else {
                this.showError("Failed to archive email");
            }
        } catch (error) {
            this.showError("Network error occurred");
            console.error("Archive error:", error);
        }
    }

    async markAsSpam(emailId) {
        try {
            const response = await fetch(`/mail/spam/${emailId}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            if (response.ok) {
                this.showSuccess("Email marked as spam");
                this.removeEmailFromList(emailId);
            } else {
                this.showError("Failed to mark as spam");
            }
        } catch (error) {
            this.showError("Network error occurred");
            console.error("Spam error:", error);
        }
    }

    async markAsRead(emailId) {
        try {
            const response = await fetch(`/mail/read/${emailId}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            if (response.ok) {
                this.updateEmailReadStatus(emailId, true);
            }
        } catch (error) {
            console.error("Mark read error:", error);
        }
    }

    async markAsUnread(emailId) {
        try {
            const response = await fetch(`/mail/unread/${emailId}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            if (response.ok) {
                this.updateEmailReadStatus(emailId, false);
            }
        } catch (error) {
            console.error("Mark unread error:", error);
        }
    }

    switchFolder(folder) {
        const currentActive = document.querySelector(".mail-type.active");
        if (currentActive) currentActive.classList.remove("active");

        const newActive = document
            .querySelector(`[data-folder="${folder}"]`)
            .closest(".mail-type");
        if (newActive) newActive.classList.add("active");

        this.loadFolder(folder);
    }

    async loadFolder(folder) {
        try {
            this.showLoading("email-list");

            const response = await fetch(`/mail/folder/${folder}`);
            const html = await response.text();

            // Update email list content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const newEmailList = doc.querySelector(".mail-messages");

            if (newEmailList) {
                document.querySelector(".mail-messages").innerHTML =
                    newEmailList.innerHTML;
            }
        } catch (error) {
            this.showError("Failed to load folder");
            console.error("Load folder error:", error);
        } finally {
            this.hideLoading("email-list");
        }
    }

    showReplyEditor() {
        const replySection = document.querySelector(".mail-reply");
        if (replySection) {
            replySection.style.display = "block";
            replySection.scrollIntoView({ behavior: "smooth" });

            if (this.quillReply) {
                this.quillReply.focus();
            }
        }
    }

    forwardEmail(emailId) {
        // Open compose modal with email content pre-filled
        const modal = new bootstrap.Modal(
            document.getElementById("mail-compose-modal")
        );
        modal.show();

        // Pre-fill subject with "Fwd: "
        if (this.currentEmail) {
            document.getElementById(
                "mailSubject"
            ).value = `Fwd: ${this.currentEmail.subject}`;

            if (this.quillCompose) {
                const forwardContent = `
                    <br><br>
                    ---------- Forwarded message ----------<br>
                    From: ${this.currentEmail.from.name} &lt;${
                    this.currentEmail.from.emailAddress.address
                }&gt;<br>
                    Date: ${new Date(
                        this.currentEmail.receivedDateTime
                    ).toLocaleString()}<br>
                    Subject: ${this.currentEmail.subject}<br><br>
                    ${this.currentEmail.body.content}
                `;
                this.quillCompose.root.innerHTML = forwardContent;
            }
        }
    }

    loadTemplate(templateName) {
        const templates = {
            "meeting-request": {
                subject: "Meeting Request",
                content: `<p>Dear [Name],</p>
                         <p>I would like to schedule a meeting with you to discuss [Topic].</p>
                         <p>Please let me know your availability for the following times:</p>
                         <ul>
                           <li>[Time Option 1]</li>
                           <li>[Time Option 2]</li>
                           <li>[Time Option 3]</li>
                         </ul>
                         <p>Best regards,<br>${this.config.user.name}</p>`,
            },
            "follow-up": {
                subject: "Following up on our conversation",
                content: `<p>Dear [Name],</p>
                         <p>I wanted to follow up on our recent conversation about [Topic].</p>
                         <p>[Add specific follow-up content here]</p>
                         <p>Please let me know if you have any questions or need additional information.</p>
                         <p>Best regards,<br>${this.config.user.name}</p>`,
            },
            "thank-you": {
                subject: "Thank you",
                content: `<p>Dear [Name],</p>
                         <p>Thank you for [specific reason]. I really appreciate [details].</p>
                         <p>[Add additional content if needed]</p>
                         <p>Best regards,<br>${this.config.user.name}</p>`,
            },
            proposal: {
                subject: "Proposal for [Project Name]",
                content: `<p>Dear [Name],</p>
                         <p>We are pleased to present our proposal for [Project Name].</p>
                         <p>Key highlights:</p>
                         <ul>
                           <li>[Highlight 1]</li>
                           <li>[Highlight 2]</li>
                           <li>[Highlight 3]</li>
                         </ul>
                         <p>Please review the attached proposal and let us know if you have any questions.</p>
                         <p>Best regards,<br>${this.config.user.name}</p>`,
            },
        };

        const template = templates[templateName];
        if (template) {
            document.getElementById("mailSubject").value = template.subject;
            if (this.quillCompose) {
                this.quillCompose.root.innerHTML = template.content;
            }

            // Close template modal
            bootstrap.Modal.getInstance(
                document.getElementById("template-modal")
            ).hide();
        }
    }

    removeEmailFromList(emailId) {
        const emailItem = document.querySelector(
            `[data-email-id="${emailId}"]`
        );
        if (emailItem) {
            emailItem.remove();
        }
    }

    updateEmailReadStatus(emailId, isRead) {
        const emailItem = document.querySelector(
            `[data-email-id="${emailId}"]`
        );
        if (emailItem) {
            if (isRead) {
                emailItem.classList.remove("unread");
            } else {
                emailItem.classList.add("unread");
            }
        }
    }

    refreshEmailList() {
        const currentFolder =
            document.querySelector(".mail-type.active [data-folder]")?.dataset
                .folder || "inbox";
        this.loadFolder(currentFolder);
    }

    initializeReplyEditor() {
        const replyEditor = document.getElementById("mail-reply-editor");
        if (replyEditor && !this.quillReply) {
            this.quillReply = new Quill("#mail-reply-editor", {
                theme: "snow",
                modules: {
                    toolbar: [
                        ["bold", "italic", "underline"],
                        [{ list: "ordered" }, { list: "bullet" }],
                        ["link"],
                        ["clean"],
                    ],
                },
                placeholder: "Write your reply here...",
            });
        }
    }

    setupNotifications() {
        // Request notification permission
        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }

        // Set up periodic email checking
        setInterval(() => {
            this.checkForNewEmails();
        }, 30000); // Check every 30 seconds
    }

    async checkForNewEmails() {
        try {
            const response = await fetch("/mail/check-new", {
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            const result = await response.json();

            if (result.newEmails > 0) {
                this.showNewEmailNotification(result.newEmails);
                this.refreshEmailList();
            }
        } catch (error) {
            // Silent fail for background checks
        }
    }

    showNewEmailNotification(count) {
        if ("Notification" in window && Notification.permission === "granted") {
            new Notification(
                `You have ${count} new email${count > 1 ? "s" : ""}`,
                {
                    icon: "/favicon.ico",
                    body: "Click to view your emails",
                    tag: "new-emails",
                }
            );
        }

        // Also show in-app notification
        this.showInfo(`${count} new email${count > 1 ? "s" : ""} received`);
    }

    // Utility methods
    showLoading(target) {
        const element =
            document.getElementById(target) ||
            document.querySelector(`.${target}`);
        if (element) {
            element.classList.add("loading");
            const overlay =
                element.querySelector(".loading-overlay") ||
                this.createLoadingOverlay();
            element.appendChild(overlay);
        }
    }

    hideLoading(target) {
        const element =
            document.getElementById(target) ||
            document.querySelector(`.${target}`);
        if (element) {
            element.classList.remove("loading");
            const overlay = element.querySelector(".loading-overlay");
            if (overlay) overlay.remove();
        }
    }

    createLoadingOverlay() {
        const overlay = document.createElement("div");
        overlay.className =
            "loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75";
        overlay.innerHTML =
            '<div class="spinner-border text-primary" role="status"></div>';
        return overlay;
    }

    showSuccess(message) {
        this.showToast(message, "success");
    }

    showError(message) {
        this.showToast(message, "danger");
    }

    showInfo(message) {
        this.showToast(message, "info");
    }

    showToast(message, type = "info") {
        const toastContainer =
            document.getElementById("toast-container") ||
            this.createToastContainer();
        const toast = this.createToast(message, type);
        toastContainer.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener("hidden.bs.toast", () => {
            toast.remove();
        });
    }

    createToastContainer() {
        const container = document.createElement("div");
        container.id = "toast-container";
        container.className = "toast-container position-fixed top-0 end-0 p-3";
        container.style.zIndex = "9999";
        document.body.appendChild(container);
        return container;
    }

    createToast(message, type) {
        const toast = document.createElement("div");
        toast.className =
            "toast align-items-center text-bg-" + type + " border-0";
        toast.setAttribute("role", "alert");
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        return toast;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return "0 Bytes";
        const k = 1024;
        const sizes = ["Bytes", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    }

    getFileIcon(filename) {
        const ext = filename.split(".").pop().toLowerCase();
        const iconMap = {
            pdf: "ri-file-pdf-line text-danger",
            doc: "ri-file-word-line text-primary",
            docx: "ri-file-word-line text-primary",
            xls: "ri-file-excel-line text-success",
            xlsx: "ri-file-excel-line text-success",
            ppt: "ri-file-ppt-line text-warning",
            pptx: "ri-file-ppt-line text-warning",
            jpg: "ri-image-line text-info",
            jpeg: "ri-image-line text-info",
            png: "ri-image-line text-info",
            gif: "ri-image-line text-info",
            zip: "ri-file-zip-line text-secondary",
            rar: "ri-file-zip-line text-secondary",
        };
        return iconMap[ext] || "ri-file-line text-muted";
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Global functions for attachment handling
window.downloadAttachment = function (attachmentId) {
    window.open(`/mail/attachment/${attachmentId}/download`, "_blank");
};

window.downloadAllAttachments = function () {
    if (window.mailApp && window.mailApp.currentEmail) {
        window.open(
            `/mail/email/${window.mailApp.currentEmail.id}/attachments/download`,
            "_blank"
        );
    }
};

// Initialize when DOM is ready
$(document).ready(function () {
    if (typeof mailAppConfig !== "undefined") {
        window.mailApp = new MailApp(mailAppConfig);
    }
});
