class MailApp2 {
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
        this.checkAndLoadInitialEmail();
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

    checkAndLoadInitialEmail() {
        if (typeof window.emailData !== "undefined" && window.emailData) {
            if (window.emailData.length > 0) {
                const data = window.emailData[0];
                this.displayEmail(data);
                this.markAsRead(data.id);
                this.currentEmail = data;

                const emailId = data.uid;
                const url = `/mail/inbox/id/${emailId}`;
                window.history.replaceState(null, "", url);

                this.highlightEmailInList(emailId);
            }
        }
    }

    highlightEmailInList(emailId) {
        $(".mail-page").removeClass("active");

        const $emailItem = $(`.mail-page[data-email-id="${emailId}"]`);
        if ($emailItem.length) {
            $emailItem.addClass("active");
        }
    }

    bindEvents() {
        // Email item clicks
        this.$document.on("click", ".email-content", (e) => {
            const $emailItem = $(e.currentTarget).closest(".mail-page");
            const emailId = $emailItem.data("email-id");

            if (emailId) {
                this.loadEmail(emailId, $emailItem);
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

    async loadEmail(emailId, $emailItem) {
        try {
            this.showLoadingForEmail($emailItem);

            const response = await $.ajax({
                url: this.config.routes.getEmail.replace(":id", emailId),
                method: "GET",
                dataType: "json",
            });

            this.displayEmail(response);
            this.markAsRead(emailId);
            this.currentEmail = response;
            this.highlightEmailInList(emailId);
        } catch (error) {
            this.showError("Failed to load email");
            console.error("Load email error:", error);
        } finally {
            this.hideLoadingForEmail($emailItem);
        }
    }

    displayEmail(email) {
        const $emailContent = $(".mails-information");
        if (!$emailContent.length) return;

        const url = `/mail/inbox/id/${email.uid}`;
        window.history.replaceState(null, "", url);

        const $emptyState = $emailContent.find(
            ".mail-info-header#email-header"
        );
        $emptyState.removeClass("empty-state-header");
        const $header = $emailContent.find(".mail-info-header");
        $emailContent.removeClass("bg-transparent");
        $header.html(`
        <div class="me-1">
           <span class="avatar avatar-md offline me-2 avatar-rounded mail-msg-avatar">
                <img src="/assets/images/faces/default.png" alt="">
            </span>
        </div>
        <div class="flex-fill">
            <h6 class="mb-0 fw-semibold">${email.from_name}</h6>
            <span class="text-muted fs-12">${email.from_email}</span>
        </div>
        <div class="mail-action-icons">
            <button class="btn btn-icon btn-light" data-action="star" data-email-id="${email.id}">
                <i class="ri-star-line"></i>
            </button>
            <button class="btn btn-icon btn-light ms-1" data-action="archive" data-email-id="${email.id}">
                <i class="ri-inbox-archive-line"></i>
            </button>
            <button class="btn btn-icon btn-light ms-1" data-action="spam" data-email-id="${email.id}">
                <i class="ri-spam-2-line"></i>
            </button>
            <button class="btn btn-icon btn-light ms-1" data-action="delete" data-email-id="${email.id}">
                <i class="ri-delete-bin-line"></i>
            </button>
            <button class="btn btn-icon btn-light ms-1" data-action="reply" data-email-id="${email.id}">
                <i class="ri-reply-line"></i>
            </button>
        </div>
    `);

        // Update body
        const $body = $emailContent.find(".mail-info-body");
        const receivedDate = new Date(email.date_received).toLocaleString();

        // Create a blob URL for the iframe content to handle HTML emails safely
        const emailContent = email.body_html || "";
        const blob = new Blob([emailContent], { type: "text/html" });
        const blobUrl = URL.createObjectURL(blob);
        // this.contentWindow.document.body.scrollHeight + "px";
        $body.html(`
        <div class="d-sm-flex d-block align-items-center justify-content-between mb-3">
            <div>
                <p class="fs-20 fw-semibold mb-0">${email.subject}</p>
            </div>
            <div class="float-end">
                <span class="me-2 fs-12 text-muted">${receivedDate}</span>
            </div>
        </div>
        <div class="main-mail-content mb-3">
            <iframe
                src="${blobUrl}"
                style="width: 100%; min-height: 0px; border: none;"
                frameborder="0"
                sandbox="allow-same-origin allow-scripts allow-popups allow-forms"
                onload="
                const contentHeight = this.contentWindow.document.body.scrollHeight;
                const screenHeight = window.innerHeight;
                let finalHeight = Math.max(300, Math.min(contentHeight, screenHeight - 300));
                this.style.height =  '488px';">
            </iframe>
        </div>
        ${this.renderAttachments(email.attachments)}
        <div class="mb-1">
            <div class="fs-14 mb-2 fw-semibold">
                <i class="ri-reply-all-line me-1 align-middle d-inline-block"></i>Reply:
            </div>
            <div class="composer-actions">
                <div class="send-btn-group">
                            <button class="send-btn" onclick="sendEmail()">
                                <i class="bx bx-send pr-2"></i>
                                Send
                            </button>
                        </div>
                    </div>
        </div>
        <div class="mail-reply">
            <div id="mail-reply-editor"></div>
        </div>
    `);

        //    <span class="fs-14 fw-semibold">
        //        <i class="ri-reply-all-line me-1 align-middle d-inline-block"></i>
        //        Reply:
        //    </span>;

        // Clean up the blob URL after a delay to prevent memory leaks
        setTimeout(() => {
            URL.revokeObjectURL(blobUrl);
        }, 1000);

        // Reinitialize reply editor
        this.quillReply = null;
        this.initializeReplyEditor();
    }

    renderAttachments(attachments) {
        // if (!attachments || attachments.length === 0) return "";

        // const totalSize = attachments.reduce((sum, att) => sum + att.size, 0);
        // const formattedSize = this.formatFileSize(totalSize);

        //  ${attachments
        //                 .map((att) => this.renderAttachment(att))
        //                 .join("")}
        return `
            <div class="mail-attachments mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mb-0">
                        <span class="fs-14 fw-semibold">
                            <i class="ri-attachment-2 me-1 align-middle"></i>Attachments 120mb:
                        </span>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-success-light" onclick="downloadAllAttachments()">Download All</button>
                    </div>
                </div>
                <div class="mt-2 d-flex flex-wrap">
                    <a href="#" class="mail-attachment border me-2 mb-2">
                <div class="attachment-icon">
                    <i class="bx bx-file fs-24"></i>
                </div>
                <div class="lh-1">
                    <p class="mb-1 attachment-name text-truncate">file.pdf</p>
                    <p class="mb-0 fs-11 text-muted">12mb</p>
                </div>
            </a>
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
        const $form = $("#compose-email-form");
        const formData = new FormData($form[0]);
        const $sendBtn = $("#sendEmailBtn");
        const $spinner = $sendBtn.find(".spinner-border");

        // Get content from Quill editor
        if (this.quillCompose) {
            formData.set("body", this.quillCompose.root.innerHTML);
        }

        try {
            $sendBtn.prop("disabled", true);
            $spinner.removeClass("d-none");

            const result = await $.ajax({
                url: this.config.routes.sendEmail,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
                dataType: "json",
            });

            if (result.success) {
                this.showSuccess("Email sent successfully!");
                $("#mail-compose-modal").modal("hide");
                $form[0].reset();
                if (this.quillCompose) this.quillCompose.setContents([]);
                this.refreshEmailList();
            } else {
                this.showError(result.message || "Failed to send email");
            }
        } catch (error) {
            this.showError("Network error occurred");
            console.error("Send email error:", error);
        } finally {
            $sendBtn.prop("disabled", false);
            $spinner.addClass("d-none");
        }
    }

    async syncEmails() {
        try {
            this.showLoading("email-list");

            const result = await $.ajax({
                url: this.config.routes.outlookSync,
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.config.csrf,
                },
                dataType: "json",
            });

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

    async toggleStar(emailId, $button) {
        try {
            const isStarred = $button.hasClass("starred");
            const $icon = $button.find("i");

            // Optimistic update
            $button.toggleClass("starred");
            $icon.attr(
                "class",
                isStarred ? "ri-star-line fs-14" : "ri-star-fill fs-14"
            );

            await $.ajax({
                url: `/mail/star/${emailId}`,
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.config.csrf,
                },
                data: JSON.stringify({ starred: !isStarred }),
                dataType: "json",
            });
        } catch (error) {
            // Revert on failure
            $button.toggleClass("starred");
            const $icon = $button.find("i");
            $icon.attr(
                "class",
                !isStarred ? "ri-star-line fs-14" : "ri-star-fill fs-14"
            );
            this.showError("Failed to update star status");
            console.error("Star toggle error:", error);
        }
    }

    handleEmailSelection($checkbox) {
        const emailId = $checkbox.val();

        if ($checkbox.is(":checked")) {
            this.selectedEmails.add(emailId);
        } else {
            this.selectedEmails.delete(emailId);
        }

        this.updateSelectionUI();
    }

    handleSelectAll(checked) {
        const $checkboxes = $(".email-checkbox");

        $checkboxes.each((index, checkbox) => {
            $(checkbox).prop("checked", checked);
            this.handleEmailSelection($(checkbox));
        });
    }

    updateSelectionUI() {
        const count = this.selectedEmails.size;
        const $selectionInfo = $("#selection-info");

        if ($selectionInfo.length) {
            if (count > 0) {
                $selectionInfo.text(`${count} email(s) selected`);
                $selectionInfo.removeClass("d-none");
            } else {
                $selectionInfo.addClass("d-none");
            }
        }
    }

    searchEmails(query) {
        const $emailItems = $(".mail-page");

        $emailItems.each((index, item) => {
            const $item = $(item);
            const subject = $item.find(".email-subject").text().toLowerCase();
            const sender = $item.find(".sender-name").text().toLowerCase();
            const preview = $item.find(".email-preview").text().toLowerCase();

            const matches =
                subject.includes(query.toLowerCase()) ||
                sender.includes(query.toLowerCase()) ||
                preview.includes(query.toLowerCase());

            $item.toggle(matches);
        });
    }

    async handleEmailAction(action, $element) {
        const emailId = $element.data("email-id");

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
            await $.ajax({
                url: `/mail/delete/${emailId}`,
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            this.showSuccess("Email deleted successfully");
            this.removeEmailFromList(emailId);
        } catch (error) {
            this.showError("Failed to delete email");
            console.error("Delete error:", error);
        }
    }

    async archiveEmail(emailId) {
        try {
            await $.ajax({
                url: `/mail/archive/${emailId}`,
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            this.showSuccess("Email archived successfully");
            this.removeEmailFromList(emailId);
        } catch (error) {
            this.showError("Failed to archive email");
            console.error("Archive error:", error);
        }
    }

    async markAsSpam(emailId) {
        try {
            await $.ajax({
                url: `/mail/spam/${emailId}`,
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            this.showSuccess("Email marked as spam");
            this.removeEmailFromList(emailId);
        } catch (error) {
            this.showError("Failed to mark as spam");
            console.error("Spam error:", error);
        }
    }

    async markAsRead(emailId) {
        this.updateEmailReadStatus(emailId, true);
    }

    async markAsUnread(emailId) {
        this.updateEmailReadStatus(emailId, false);
    }

    switchFolder(folder) {
        const $currentActive = $(".mail-type.active");
        $currentActive.removeClass("active");

        window.emailData = null;
        $(".mails-information").empty();

        const $newActive = $(`[data-folder="${folder}"]`).closest(".mail-type");
        $newActive.addClass("active");

        this.loadFolder(folder);
    }

    async loadFolder(folder) {
        try {
            this.showLoading("email-list");

            const html = await $.ajax({
                url: `/mail/folder/${folder}`,
                method: "GET",
            });

            // Update email list content
            const $tempDiv = $("<div>").html(html);
            const $newEmailList = $tempDiv.find(".mail-messages");

            if ($newEmailList.length) {
                $(".mail-messages").html($newEmailList.html());
            }
        } catch (error) {
            this.showError("Failed to load folder");
            console.error("Load folder error:", error);
        } finally {
            this.hideLoading("email-list");
        }
    }

    showReplyEditor() {
        const $replySection = $(".mail-reply");
        if ($replySection.length) {
            $replySection.show();
            $replySection[0].scrollIntoView({ behavior: "smooth" });

            if (this.quillReply) {
                this.quillReply.focus();
            }
        }
    }

    forwardEmail(emailId) {
        // Open compose modal with email content pre-filled
        $("#mail-compose-modal").modal("show");

        // Pre-fill subject with "Fwd: "
        if (this.currentEmail) {
            $("#mailSubject").val(`Fwd: ${this.currentEmail.subject}`);

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
            $("#mailSubject").val(template.subject);
            if (this.quillCompose) {
                this.quillCompose.root.innerHTML = template.content;
            }

            // Close template modal
            $("#template-modal").modal("hide");
        }
    }

    removeEmailFromList(emailId) {
        $(`[data-email-id="${emailId}"]`).remove();
    }

    updateEmailReadStatus(emailId, isRead) {
        const $emailItem = $(`[data-email-id="${emailId}"]`);
        if ($emailItem.length) {
            if (isRead) {
                $emailItem.removeClass("unread");
            } else {
                $emailItem.addClass("unread");
            }
        }
    }

    refreshEmailList() {
        const currentFolder =
            $(".mail-type.active [data-folder]").data("folder") || "inbox";
        this.loadFolder(currentFolder);
    }

    initializeReplyEditor() {
        const $replyEditor = $("#mail-reply-editor");
        if ($replyEditor.length && !this.quillReply) {
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
            const result = await $.ajax({
                url: "/mail/check-new",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
                dataType: "json",
            });

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

    showLoading(target) {
        const $element = target instanceof jQuery ? target : $(target);
        if ($element.length) {
            $element.addClass("loading");

            let $overlay = $element.find(".loading-overlay");
            if (!$overlay.length) {
                $overlay = this.createLoadingOverlay();
                $element.append($overlay);
            }
        }
    }

    showLoadingForEmail($emailItem) {
        if ($emailItem && $emailItem.length) {
            $emailItem.addClass("loading-dim");
        }
    }

    hideLoadingForEmail($emailItem) {
        if ($emailItem && $emailItem.length) {
            $emailItem.removeClass("loading-dim");
        }
    }

    hideLoading(target) {
        const $element = target instanceof jQuery ? target : $(target);
        if ($element.length) {
            $element.removeClass("loading");
            $element.find(".loading-overlay").remove();
        }
    }

    createLoadingOverlay() {
        return $(
            '<div class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75"><div class="spinner-border text-primary" role="status"></div></div>'
        );
    }

    showSuccess(message) {
        this.showToast(message, "success");
    }

    showError(message) {
        this.showToast(message, "error");
    }

    showInfo(message) {
        this.showToast(message, "info");
    }

    showToast(message, type = "info", options = {}) {
        const defaultOptions = {
            timeOut: 5000, // 5 seconds default
            extendedTimeOut: 1000,
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
            onclick: null,
            onShown: null,
            onHidden: null,
            ...options, // Merge with user options
        };

        // Configure toastr with options
        toastr.options = defaultOptions;

        // Map your type parameter to toastr methods
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
