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
        this.initializeSelects();
        this.setupNotifications();
        this.setupTooltips();
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

    setupTooltips() {
        // Initialize Bootstrap tooltips using jQuery
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    bindEvents() {
        // Email item clicks using jQuery delegation
        this.$document.on("click", ".email-content", (e) => {
            const $emailItem = $(e.currentTarget).closest(".email-item");
            const emailId = $emailItem.data("email-id");
            if (emailId) {
                this.loadEmail(emailId);
            }
        });

        // Checkbox selection with jQuery
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

        // Search functionality with jQuery
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

        // Bulk actions
        $("#bulkActionBtn").on("click", () => {
            this.showBulkActionMenu();
        });

        this.$document.on("click", ".bulk-action-item", (e) => {
            const action = $(e.currentTarget).data("bulk-action");
            this.performBulkAction(action);
        });

        // Mark all as read
        $("#markAllReadBtn").on("click", () => {
            this.markAllAsRead();
        });

        // Auto-save drafts
        $("#compose-email-form input, #compose-email-form textarea").on(
            "input",
            this.debounce(() => {
                this.autoSaveDraft();
            }, 2000)
        );

        // Keyboard shortcuts
        this.$document.on("keydown", (e) => {
            this.handleKeyboardShortcuts(e);
        });

        // Email list infinite scroll
        $(".mail-messages").on("scroll", (e) => {
            this.handleInfiniteScroll($(e.currentTarget));
        });
    }

    async loadEmail(emailId) {
        try {
            this.showLoading($(".mails-information"));

            const response = await $.ajax({
                url: this.config.routes.getEmail.replace(":id", emailId),
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            this.displayEmail(response);
            this.markAsRead(emailId);
            this.currentEmail = response;

            // Update URL without page reload
            history.pushState({ emailId }, "", `/mail/email/${emailId}`);
        } catch (error) {
            this.showError("Failed to load email");
            console.error("Load email error:", error);
        } finally {
            this.hideLoading($(".mails-information"));
        }
    }

    displayEmail(email) {
        const $emailContent = $(".mails-information");
        if (!$emailContent.length) return;

        // Update header with jQuery template
        const $header = $emailContent.find(".mail-info-header");
        const headerHtml = this.renderEmailHeader(email);
        $header.html(headerHtml);

        // Update body
        const $body = $emailContent.find(".mail-info-body");
        const bodyHtml = this.renderEmailBody(email);
        $body.html(bodyHtml);

        // Fade in animation
        $emailContent.hide().fadeIn(300);

        // Reinitialize reply editor
        this.initializeReplyEditor();

        // Setup image lazy loading
        $emailContent.find("img[data-src]").each((index, img) => {
            this.setupLazyLoading($(img));
        });
    }

    renderEmailHeader(email) {
        return `
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
                }"
                        data-bs-toggle="tooltip" title="Star this email">
                    <i class="ri-star-line"></i>
                </button>
                <button class="btn btn-icon btn-light ms-1" data-action="archive" data-email-id="${
                    email.id
                }"
                        data-bs-toggle="tooltip" title="Archive">
                    <i class="ri-inbox-archive-line"></i>
                </button>
                <button class="btn btn-icon btn-light ms-1" data-action="spam" data-email-id="${
                    email.id
                }"
                        data-bs-toggle="tooltip" title="Mark as spam">
                    <i class="ri-spam-2-line"></i>
                </button>
                <button class="btn btn-icon btn-light ms-1" data-action="delete" data-email-id="${
                    email.id
                }"
                        data-bs-toggle="tooltip" title="Delete">
                    <i class="ri-delete-bin-line"></i>
                </button>
                <button class="btn btn-icon btn-light ms-1" data-action="reply" data-email-id="${
                    email.id
                }"
                        data-bs-toggle="tooltip" title="Reply">
                    <i class="ri-reply-line"></i>
                </button>
            </div>
        `;
    }

    renderEmailBody(email) {
        const receivedDate = new Date(email.receivedDateTime).toLocaleString();

        return `
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
            <div class="mail-reply" style="display: none;">
                <div id="mail-reply-editor"></div>
                <div class="mt-3">
                    <button class="btn btn-primary" id="sendReplyBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2"></span>
                        Send Reply
                    </button>
                    <button class="btn btn-secondary ms-2" id="cancelReplyBtn">Cancel</button>
                </div>
            </div>
        `;
    }

    renderAttachments(attachments) {
        if (!attachments || attachments.length === 0) return "";

        const totalSize = attachments.reduce((sum, att) => sum + att.size, 0);
        const formattedSize = this.formatFileSize(totalSize);

        const attachmentItems = attachments
            .map((att) => this.renderAttachment(att))
            .join("");

        return `
            <div class="mail-attachments mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mb-0">
                        <span class="fs-14 fw-semibold">
                            <i class="ri-attachment-2 me-1 align-middle"></i>Attachments (${formattedSize}):
                        </span>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-success-light" onclick="downloadAllAttachments()">
                            <i class="ri-download-line me-1"></i>Download All
                        </button>
                    </div>
                </div>
                <div class="mt-2 d-flex flex-wrap">
                    ${attachmentItems}
                </div>
            </div>
        `;
    }

    renderAttachment(attachment) {
        const iconClass = this.getFileIcon(attachment.name);
        const size = this.formatFileSize(attachment.size);

        return `
            <a href="#" class="mail-attachment border me-2 mb-2"
               onclick="downloadAttachment('${attachment.id}')"
               data-bs-toggle="tooltip" title="Download ${attachment.name}">
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
        const $sendBtn = $("#sendEmailBtn");
        const $spinner = $sendBtn.find(".spinner-border");

        // Get content from Quill editor
        const formData = new FormData($form[0]);
        if (this.quillCompose) {
            formData.set("body", this.quillCompose.root.innerHTML);
        }

        try {
            $sendBtn.prop("disabled", true);
            $spinner.removeClass("d-none");

            await $.ajax({
                url: this.config.routes.sendEmail,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            this.showSuccess("Email sent successfully!");

            // Hide modal and reset form
            $("#mail-compose-modal").modal("hide");
            $form[0].reset();
            $("#toMail, #mailCC, #mailBcc").val(null).trigger("change");

            if (this.quillCompose) {
                this.quillCompose.setContents([]);
            }

            this.refreshEmailList();
            this.clearDraft();
        } catch (error) {
            this.showError(
                error.responseJSON?.message || "Failed to send email"
            );
            console.error("Send email error:", error);
        } finally {
            $sendBtn.prop("disabled", false);
            $spinner.addClass("d-none");
        }
    }

    async syncEmails() {
        try {
            this.showLoading($(".mail-messages"));

            const $syncBtn = $("#syncEmailsBtn");
            const $icon = $syncBtn.find("i");

            // Add spinning animation
            $icon.addClass("fa-spin");

            const response = await $.ajax({
                url: this.config.routes.outlookSync,
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            if (response.synced) {
                this.showSuccess("Emails synced successfully!");
                this.refreshEmailList();
            } else {
                this.showError("Failed to sync emails");
            }
        } catch (error) {
            this.showError("Network error occurred");
            console.error("Sync error:", error);
        } finally {
            this.hideLoading($(".mail-messages"));
            $("#syncEmailsBtn i").removeClass("fa-spin");
        }
    }

    async toggleStar(emailId, $button) {
        try {
            const isStarred = $button.hasClass("starred");
            const $icon = $button.find("i");

            // Optimistic update with animation
            $button.toggleClass("starred");
            $icon.toggleClass("ri-star-line ri-star-fill");

            // Add bounce animation
            $button.addClass("animate__animated animate__pulse");

            await $.ajax({
                url: `/mail/star/${emailId}`,
                method: "POST",
                data: JSON.stringify({ starred: !isStarred }),
                contentType: "application/json",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            setTimeout(() => {
                $button.removeClass("animate__animated animate__pulse");
            }, 600);
        } catch (error) {
            // Revert on failure
            $button.toggleClass("starred");
            $button.find("i").toggleClass("ri-star-line ri-star-fill");
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
        this.updateBulkActionsVisibility();
    }

    handleSelectAll(checked) {
        $(".email-checkbox").each((index, checkbox) => {
            const $checkbox = $(checkbox);
            $checkbox.prop("checked", checked);
            this.handleEmailSelection($checkbox);
        });
    }

    updateSelectionUI() {
        const count = this.selectedEmails.size;
        const $selectionInfo = $("#selection-info");

        if (count > 0) {
            $selectionInfo
                .text(`${count} email(s) selected`)
                .removeClass("d-none");
        } else {
            $selectionInfo.addClass("d-none");
        }
    }

    updateBulkActionsVisibility() {
        const $bulkActions = $(".bulk-actions");

        if (this.selectedEmails.size > 0) {
            $bulkActions.fadeIn(200);
        } else {
            $bulkActions.fadeOut(200);
        }
    }

    searchEmails(query) {
        const $emailItems = $(".email-item");
        const searchTerm = query.toLowerCase();

        $emailItems.each((index, item) => {
            const $item = $(item);
            const subject = $item.find(".email-subject").text().toLowerCase();
            const sender = $item.find(".sender-name").text().toLowerCase();
            const preview = $item.find(".email-preview").text().toLowerCase();

            const matches =
                subject.includes(searchTerm) ||
                sender.includes(searchTerm) ||
                preview.includes(searchTerm);

            if (matches) {
                $item.fadeIn(200);
            } else {
                $item.fadeOut(200);
            }
        });

        // Update search results count
        const visibleCount = $emailItems.filter(":visible").length;
        $("#searchResultsCount").text(`${visibleCount} results found`);
    }

    async performBulkAction(action) {
        if (this.selectedEmails.size === 0) {
            this.showWarning("Please select emails first");
            return;
        }

        const emailIds = Array.from(this.selectedEmails);

        try {
            await $.ajax({
                url: `/mail/bulk/${action}`,
                method: "POST",
                data: JSON.stringify({ emailIds }),
                contentType: "application/json",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            this.showSuccess(`Bulk ${action} completed successfully`);

            // Remove processed emails from UI
            emailIds.forEach((id) => {
                $(`.email-item[data-email-id="${id}"]`).fadeOut(
                    300,
                    function () {
                        $(this).remove();
                    }
                );
            });

            this.selectedEmails.clear();
            this.updateSelectionUI();
            this.updateBulkActionsVisibility();
        } catch (error) {
            this.showError(`Failed to perform bulk ${action}`);
            console.error("Bulk action error:", error);
        }
    }

    async markAllAsRead() {
        try {
            await $.ajax({
                url: "/mail/mark-all-read",
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            });

            $(".email-item.unread").removeClass("unread").addClass("read");
            this.showSuccess("All emails marked as read");
        } catch (error) {
            this.showError("Failed to mark all emails as read");
            console.error("Mark all read error:", error);
        }
    }

    handleKeyboardShortcuts(e) {
        // Only handle shortcuts when not typing in inputs
        if ($(e.target).is("input, textarea, [contenteditable]")) return;

        switch (e.key.toLowerCase()) {
            case "c":
                if (e.ctrlKey || e.metaKey) return; // Allow Ctrl+C
                $("#composeBtn").click();
                break;
            case "r":
                if (this.currentEmail) {
                    this.showReplyEditor();
                }
                break;
            case "s":
                if (this.currentEmail) {
                    this.toggleStar(
                        this.currentEmail.id,
                        $(
                            `.mail-starred[data-email-id="${this.currentEmail.id}"]`
                        )
                    );
                }
                break;
            case "delete":
            case "d":
                if (this.currentEmail) {
                    this.deleteEmail(this.currentEmail.id);
                }
                break;
            case "a":
                if (this.currentEmail) {
                    this.archiveEmail(this.currentEmail.id);
                }
                break;
            case "/":
                e.preventDefault();
                $("#emailSearch").focus();
                break;
        }
    }

    handleInfiniteScroll($container) {
        const scrollTop = $container.scrollTop();
        const scrollHeight = $container[0].scrollHeight;
        const clientHeight = $container.height();

        if (scrollTop + clientHeight >= scrollHeight - 100) {
            this.loadMoreEmails();
        }
    }

    async loadMoreEmails() {
        if (this.loadingMore) return;

        this.loadingMore = true;
        const $loadingIndicator = $("#loadMoreIndicator");
        $loadingIndicator.show();

        try {
            const currentFolder =
                $(".mail-type.active [data-folder]").data("folder") || "inbox";
            const currentCount = $(".email-item").length;

            const response = await $.ajax({
                url: `/mail/folder/${currentFolder}`,
                data: { offset: currentCount, limit: 20 },
            });

            if (response.emails && response.emails.length > 0) {
                const $emailList = $(".mail-messages");
                response.emails.forEach((email) => {
                    const emailHtml = this.renderEmailListItem(email);
                    $emailList.append(emailHtml);
                });
            }
        } catch (error) {
            console.error("Load more emails error:", error);
        } finally {
            this.loadingMore = false;
            $loadingIndicator.hide();
        }
    }

    autoSaveDraft() {
        if (!$("#compose-email-form").is(":visible")) return;

        const formData = {
            to: $("#toMail").val(),
            subject: $("#mailSubject").val(),
            body: this.quillCompose ? this.quillCompose.root.innerHTML : "",
        };

        // Only save if there's actual content
        if (formData.to || formData.subject || formData.body.trim()) {
            $.ajax({
                url: "/mail/save-draft",
                method: "POST",
                data: formData,
                headers: {
                    "X-CSRF-TOKEN": this.config.csrf,
                },
            }).done(() => {
                $("#draftStatus")
                    .text("Draft saved")
                    .fadeIn()
                    .delay(2000)
                    .fadeOut();
            });
        }
    }

    clearDraft() {
        $.ajax({
            url: "/mail/clear-draft",
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": this.config.csrf,
            },
        });
    }

    setupLazyLoading($img) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const $target = $(entry.target);
                    $target.attr("src", $target.data("src"));
                    $target.removeClass("lazy");
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe($img[0]);
    }

    // Enhanced UI methods using jQuery
    showLoading($element) {
        $element.addClass("loading position-relative");

        const $overlay = $("<div>", {
            class: "loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75",
            html: '<div class="spinner-border text-primary" role="status"></div>',
        });

        $element.append($overlay);
        $overlay.hide().fadeIn(200);
    }

    hideLoading($element) {
        $element.removeClass("loading");
        $element.find(".loading-overlay").fadeOut(200, function () {
            $(this).remove();
        });
    }

    showSuccess(message) {
        this.showToast(message, "success");
    }

    showError(message) {
        this.showToast(message, "danger");
    }

    showWarning(message) {
        this.showToast(message, "warning");
    }

    showInfo(message) {
        this.showToast(message, "info");
    }

    showToast(message, type = "info") {
        const $toastContainer = this.getToastContainer();
        const $toast = this.createToast(message, type);

        $toastContainer.append($toast);

        // Slide in animation
        $toast.hide().slideDown(300);

        // Auto hide after 5 seconds
        setTimeout(() => {
            $toast.slideUp(300, function () {
                $(this).remove();
            });
        }, 5000);
    }

    getToastContainer() {
        let $container = $("#toast-container");
        if (!$container.length) {
            $container = $("<div>", {
                id: "toast-container",
                class: "toast-container position-fixed top-0 end-0 p-3",
                css: { zIndex: 9999 },
            });
            $("body").append($container);
        }
        return $container;
    }

    createToast(message, type) {
        const icons = {
            success: "ri-check-line",
            danger: "ri-error-warning-line",
            warning: "ri-alert-line",
            info: "ri-information-line",
        };

        return $("<div>", {
            class: `alert alert-${type} alert-dismissible fade show`,
            html: `
                <i class="${icons[type]} me-2"></i>
                ${message}
                <button type="button" class="btn-close" onclick="$(this).closest('.alert').slideUp(300, function(){ $(this).remove(); })"></button>
            `,
        });
    }

    // Utility methods remain the same but can be enhanced with jQuery
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
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialize when DOM is ready using jQuery
$(document).ready(function () {
    if (typeof mailAppConfig !== "undefined") {
        window.mailApp = new MailApp(mailAppConfig);
    }
});
