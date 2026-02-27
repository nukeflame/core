// @pk305
(function ($) {
    "use strict";

    const cfg = window.MailAppConfig || {};
    const $messages = $("#mail-messages");
    const $loading = $(".email-list-loading");
    const $folderTitle = $(".total-mails h6.fw-semibold");

    const state = {
        folder: "inbox",
        page: 1,
        limit: 50,
        rawEmails: [],
        emails: [],
        focusedPanelActive: false,
        previousMailInfoHtml: null,
        composeModeActive: false,
    };

    function ensureConversationStyles() {
        if (document.getElementById("mail-conversation-styles")) {
            return;
        }

        const css = `
            .mail-info-body { background: #f8fafc; padding: 16px !important; }
            .thread-shell { max-width: 1120px; margin: 0 auto; }
            .main-mail-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 14px 16px;
                margin-bottom: 14px;
            }
            .main-mail-subject {
                font-size: 18px;
                line-height: 1.3;
                font-weight: 700;
                color: #111827;
                margin-bottom: 6px;
            }
            .main-mail-meta {
                font-size: 12px;
                color: #6b7280;
                line-height: 1.45;
                margin-bottom: 10px;
            }
            .main-mail-content {
                border-top: 1px solid #e5e7eb;
                padding-top: 10px;
                font-size: 14px;
                color: #111827;
                line-height: 1.6;
                word-break: break-word;
            }
            .main-mail-content p:last-child { margin-bottom: 0; }
            .trail-wrap { margin-top: 6px; }
            .trail-title { font-size: 13px; color: #6b7280; margin: 0 0 8px; font-weight: 600; }
            .trail-item { display: flex; gap: 10px; margin-bottom: 10px; }
            .trail-item.sent { justify-content: flex-end; }
            .trail-card {
                width: 100%;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 10px 12px;
                background: #fff;
            }
            .trail-item.sent .trail-card { background: #f5f9ff; border-color: #dbeafe; }
            .trail-avatar {
                width: 32px;
                height: 32px;
                min-width: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 12px;
                color: #fff;
                background: #6b7280;
                margin-top: 2px;
            }
            .trail-item.sent .trail-avatar { background: #2563eb; }
            .trail-top { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
            .trail-name { font-weight: 700; font-size: 14px; color: #111827; }
            .trail-meta { font-size: 12px; color: #6b7280; }
            .trail-date { margin-left: auto; font-size: 11px; color: #6b7280; white-space: nowrap; }
            .trail-subject { font-size: 15px; font-weight: 600; color: #111827; line-height: 1.35; margin: 6px 0 4px; }
            .trail-quoted {
                border-top: 1px solid #e5e7eb;
                margin-top: 6px;
                padding-top: 6px;
                font-size: 12px;
                color: #374151;
                line-height: 1.45;
            }
            .trail-preview { margin-top: 8px; font-size: 13px; color: #111827; line-height: 1.5; }
            .trail-current { font-size: 10px; background: #eef2ff; color: #3730a3; padding: 2px 6px; border-radius: 999px; }
            .compose-inline-panel {
                background: #fff;
                border: 0;
                border-radius: 0;
                min-height: calc(100vh - 130px);
                display: flex;
                flex-direction: column;
            }
            .compose-inline-toolbar {
                border-bottom: 1px solid #d1d5db;
                padding: 8px 10px 6px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .compose-inline-toolbar .toolbar-right {
                display: inline-flex;
                gap: 10px;
                color: #6b7280;
                font-size: 14px;
                align-items: center;
            }
            .compose-inline-send {
                background: #7c3aed;
                border: 0;
                color: #fff;
                border-radius: 3px;
                padding: 5px 20px;
                font-size: 13px;
            }

            .compose-inline-fields .compose-row {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 12px;
                border-bottom: 1px solid #d1d5db;
                font-size: 13px;
                min-height: 36px;
            }
            .compose-inline-fields .compose-label {
                color: #1f2937;
                min-width: 24px;
                border: 1px solid #cbd5e1;
                border-radius: 3px;
                padding: 2px 10px;
                line-height: 1.2;
                background: #f8fafc;
            }
            .compose-inline-fields .compose-input {
                flex: 1;
                border: 0;
                outline: none;
                background: transparent;
                color: #111827;
            }
            .compose-inline-fields .compose-subject-wrap {
                margin-left: auto;
                font-size: 11px;
                color: #6b7280;
                white-space: nowrap;
            }
            .compose-inline-editor {
                flex: 1;
                padding: 12px;
                color: #374151;
                font-size: 14px;
                min-height: 320px;
                background: #fff;
                                outline: none;
            }
        `;

        $("<style>", { id: "mail-conversation-styles", text: css }).appendTo(
            "head",
        );
    }

    function escapeHtml(value) {
        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function folderLabel(folder) {
        const labels = {
            all: "All Mails",
            inbox: "Inbox",
            sent: "Sent",
            drafts: "Drafts",
            spam: "Spam",
            important: "Important",
            trash: "Trash",
            archive: "Archive",
            starred: "Starred",
        };
        return labels[folder] || "Inbox";
    }

    function formatDate(dateStr) {
        if (!dateStr) {
            return "";
        }

        const dt = new Date(dateStr);
        if (Number.isNaN(dt.getTime())) {
            return "";
        }

        const now = new Date();
        const sameDay = dt.toDateString() === now.toDateString();
        if (sameDay) {
            return dt.toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
            });
        }

        return dt.toLocaleDateString([], { month: "short", day: "numeric" });
    }

    function formatDateTime(dateStr) {
        if (!dateStr) {
            return "";
        }

        const dt = new Date(dateStr);
        if (Number.isNaN(dt.getTime())) {
            return "";
        }

        return dt.toLocaleString([], {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    function setLoading(active) {
        $loading.toggleClass("d-none", !active);
        $messages.toggleClass("loading-dim", active);
    }

    function getRowCheckboxes() {
        return $messages.find(
            ".mail-messages-container .email-checkbox.mailc-checkbox",
        );
    }

    function selectedRowCount() {
        return getRowCheckboxes().filter(":checked").length;
    }

    function renderFocusedPanelHtml(selectedCount) {
        const titleText = selectedCount + " emails are selected";
        return (
            '<div class="focused-actions-panel">' +
            '<div class="focused-actions-inner text-center">' +
            '<div class="focused-actions-icon mb-3"><i class="ri-mail-check-line"></i></div>' +
            '<p class="focused-actions-title mb-3" id="focusedActionsTitle">' +
            escapeHtml(titleText) +
            "</p>" +
            '<div class="focused-actions-list">' +
            '<button type="button" class="btn btn-link focused-action-btn" data-action="delete"><i class="ri-delete-bin-line me-2"></i>Empty Focused</button>' +
            '<button type="button" class="btn btn-link focused-action-btn" data-action="flag"><i class="ri-flag-line me-2"></i>Flag</button>' +
            '<button type="button" class="btn btn-link focused-action-btn" data-action="mark-read"><i class="ri-mail-open-line me-2"></i>Mark as read</button>' +
            '<button type="button" class="btn btn-link focused-action-btn" data-action="mark-unread"><i class="ri-mail-line me-2"></i>Mark as unread</button>' +
            '<button type="button" class="btn btn-link focused-action-btn" data-action="move"><i class="ri-folder-transfer-line me-2"></i>Move</button>' +
            '<button type="button" class="btn btn-link focused-action-btn" data-action="insights"><i class="ri-heart-line me-2"></i>Viva Insights</button>' +
            "</div>" +
            '<hr class="my-3">' +
            '<button type="button" class="btn btn-link focused-action-btn cancel-btn" data-action="cancel"><i class="ri-close-line me-2"></i>Cancel</button>' +
            "</div>" +
            "</div>"
        );
    }

    function setFocusedPanelVisible(visible, selectedCount) {
        const $body = $("#mail-info-body");
        if (!$body.length) {
            return;
        }

        if (visible) {
            if (!state.focusedPanelActive) {
                state.previousMailInfoHtml = $body.html();
            }
            state.focusedPanelActive = true;
            $("#empty-email-state").addClass("d-none");
            $body.html(renderFocusedPanelHtml(Number(selectedCount || 0)));
            return;
        }

        if (state.focusedPanelActive) {
            if (typeof state.previousMailInfoHtml === "string") {
                $body.html(state.previousMailInfoHtml);
            }
            state.focusedPanelActive = false;
            state.previousMailInfoHtml = null;
        }
    }

    function clearSelections() {
        getRowCheckboxes().prop("checked", false);
        $("#checkAll").prop("checked", false).prop("indeterminate", false);
        setFocusedPanelVisible(false, 0);
    }

    function syncCheckAllState() {
        const $checkAll = $("#checkAll");
        if (!$checkAll.length) {
            return;
        }

        const $rows = getRowCheckboxes();
        const total = $rows.length;
        const checked = $rows.filter(":checked").length;

        if (!total) {
            $checkAll.prop("checked", false).prop("indeterminate", false);
            setFocusedPanelVisible(false, 0);
            return;
        }

        $checkAll.prop("checked", checked === total);
        $checkAll.prop("indeterminate", checked > 0 && checked < total);
        setFocusedPanelVisible(checked > 2, checked);
    }

    function notifyError(message) {
        if (window.toastr && typeof window.toastr.error === "function") {
            window.toastr.error(message);
            return;
        }
        // Fallback when toastr is unavailable
        console.error(message);
    }

    function formatRecipients(recipients) {
        if (!Array.isArray(recipients) || recipients.length === 0) {
            return "-";
        }

        return recipients
            .map(function (r) {
                if (!r) {
                    return "";
                }

                const name = r.name ? String(r.name).trim() : "";
                const email = r.email ? String(r.email).trim() : "";
                if (name && email) {
                    return name + " <" + email + ">";
                }
                return email || name;
            })
            .filter(Boolean)
            .join(", ");
    }

    function getInitials(nameOrEmail) {
        const raw = String(nameOrEmail || "").trim();
        if (!raw) {
            return "U";
        }

        const parts = raw.split(/\s+/).filter(Boolean);
        if (parts.length >= 2) {
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }

        return raw.slice(0, 2).toUpperCase();
    }

    function stripHtml(text) {
        return String(text || "")
            .replace(/<[^>]*>/g, " ")
            .replace(/&nbsp;/gi, " ")
            .trim();
    }

    function truncate(text, maxLength) {
        const str = String(text || "");
        if (str.length <= maxLength) {
            return str;
        }
        return str.slice(0, maxLength - 1).trimEnd() + "…";
    }

    function safeColorClass(rawColor) {
        const color = String(rawColor || "primary")
            .toLowerCase()
            .replace(/[^a-z0-9_-]/g, "");
        return color || "primary";
    }

    function isSentByMe(email) {
        const me = String(cfg.currentUserEmail || "").toLowerCase();
        const from = String(email || "").toLowerCase();
        return !!me && !!from && me === from;
    }

    function renderTrail(trail, currentId) {
        if (!Array.isArray(trail) || trail.length === 0) {
            return (
                '<div class="trail-wrap">' +
                '<p class="trail-title">Email Trail</p>' +
                '<div class="text-muted fs-12">No conversation trail found.</div>' +
                "</div>"
            );
        }

        const items = trail
            .map(function (item) {
                const isCurrent = item.id === currentId;
                const sentByMe = isSentByMe(item.from_email);
                const attach = item.has_attachments
                    ? ' <i class="ri-attachment-2"></i>'
                    : "";
                const fromName = item.from_name || item.from_email;
                const toLine = formatRecipients(item.to || []);
                const toLabel = sentByMe ? "to " + (toLine || "-") : "to me";

                return (
                    '<div class="trail-item ' +
                    (sentByMe ? "sent" : "received") +
                    '">' +
                    '<div class="trail-avatar">' +
                    escapeHtml(getInitials(fromName)) +
                    "</div>" +
                    '<div class="trail-card">' +
                    '<div class="trail-top">' +
                    '<span class="trail-name">' +
                    escapeHtml(fromName) +
                    "</span>" +
                    '<span class="trail-meta">&lt;' +
                    escapeHtml(item.from_email || "") +
                    "&gt;</span>" +
                    (isCurrent
                        ? '<span class="trail-current">Current</span>'
                        : "") +
                    '<span class="trail-date">' +
                    escapeHtml(
                        formatDateTime(item.received_at || item.sent_at),
                    ) +
                    "</span>" +
                    "</div>" +
                    '<div class="trail-meta">' +
                    escapeHtml(toLabel) +
                    "</div>" +
                    '<div class="trail-subject">' +
                    escapeHtml(item.subject || "[No Subject]") +
                    attach +
                    "</div>" +
                    '<div class="trail-quoted">' +
                    "<div><b>From:</b> " +
                    escapeHtml(fromName) +
                    " &lt;" +
                    escapeHtml(item.from_email || "") +
                    "&gt;</div>" +
                    "<div><b>Sent:</b> " +
                    escapeHtml(
                        formatDateTime(item.sent_at || item.received_at),
                    ) +
                    "</div>" +
                    "<div><b>To:</b> " +
                    escapeHtml(toLine || "-") +
                    "</div>" +
                    "<div><b>Subject:</b> " +
                    escapeHtml(item.subject || "[No Subject]") +
                    "</div>" +
                    "</div>" +
                    '<div class="trail-preview">' +
                    escapeHtml(item.preview || "") +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "</div>"
                );
            })
            .join("");

        return (
            '<div class="trail-wrap"><p class="trail-title">Email Trail</p>' +
            items +
            "</div>"
        );
    }

    function renderEmailDetail(email) {
        console.log(email);
        const scaffold = ensureEmailDetailScaffold();
        const $header = scaffold.$header;
        const $body = scaffold.$body;
        const $container = scaffold.$container;

        if (!$header.length || !$body.length || !$container.length) {
            return;
        }
        state.composeModeActive = false;

        const bodyHtml = email.body_html || "<p>No content available.</p>";

        $container.removeClass("bg-transparent");
        $header
            .removeClass("empty-state-header")
            .html(
                '<div class="me-1">' +
                    '<span class="avatar avatar-md offline me-2 avatar-rounded mail-msg-avatar">' +
                    '<img src="/assets/images/faces/default.png" alt="">' +
                    "</span>" +
                    "</div>" +
                    '<div class="flex-fill">' +
                    '<h6 class="mb-0 fw-semibold">' +
                    escapeHtml(email.from_name || "Unknown Sender") +
                    "</h6>" +
                    '<span class="text-muted fs-12">' +
                    escapeHtml(email.from_email || "") +
                    "</span>" +
                    "</div>" +
                    '<div class="text-muted fs-12">' +
                    escapeHtml(
                        formatDateTime(email.received_at || email.sent_at),
                    ) +
                    "</div>",
            );

        $body.html(
            '<div class="thread-shell">' +
                '<div class="main-mail-card">' +
                '<div class="main-mail-subject">' +
                escapeHtml(email.subject || "[No Subject]") +
                "</div>" +
                '<div class="main-mail-meta"><strong>To:</strong> ' +
                escapeHtml(formatRecipients(email.to)) +
                "</div>" +
                '<div class="main-mail-meta"><strong>CC:</strong> ' +
                escapeHtml(formatRecipients(email.cc)) +
                "</div>" +
                '<div class="main-mail-content">' +
                bodyHtml +
                "</div>" +
                "</div>" +
                renderTrail(email.trail, email.id) +
                "</div>",
        );
    }

    function renderInlineComposeAnalyzed() {
        const $container = $(".mails-information").first();
        if (!$container.length) {
            return;
        }

        $container.removeClass("bg-transparent");
        $container.html(
            '<div class="compose-inline-panel">' +
                '<div class="compose-inline-toolbar">' +
                '<button type="button" class="compose-inline-send"><i class="ri-send-plane-line me-1"></i>Send</button>' +
                '<div class="toolbar-right"><i class="ri-contacts-book-line" title="Contacts"></i><i class="ri-arrow-down-s-line" title="More"></i><i class="ri-delete-bin-line" title="Discard"></i><i class="ri-external-link-line" title="Pop-out"></i></div>' +
                "</div>" +
                '<div class="compose-inline-fields">' +
                '<div class="compose-row"><span class="compose-label">To</span><input class="compose-input" type="text"><span class="text-muted fs-12">Bcc</span></div>' +
                '<div class="compose-row"><span class="compose-label">Cc</span><input class="compose-input" type="text"></div>' +
                '<div class="compose-row"><input class="compose-input" type="text" placeholder="Add a subject"><span class="compose-subject-wrap">Draft saved at 2:08 PM</span></div>' +
                "</div>" +
                '<div class="compose-inline-editor" contenteditable="true"></div>' +
                "</div>",
        );

        state.composeModeActive = true;
        $container
            .find(".compose-inline-fields .compose-input")
            .first()
            .trigger("focus");
    }

    function ensureEmailDetailScaffold() {
        const $container = $(".mails-information").first();
        if (!$container.length) {
            return {
                $container: $container,
                $header: $(),
                $body: $(),
                $footer: $(),
            };
        }

        let $header = $("#email-header");
        let $body = $("#mail-info-body");
        let $footer = $("#email-footer");

        if (!$header.length || !$body.length) {
            $container.html(
                '<div class="mail-info-header d-flex flex-wrap gap-2 align-items-center empty-state-header" id="email-header">' +
                    '<div class="empty-state text-center" id="empty-email-state">' +
                    '<div class="envelope-icon"><i class="bi bi-envelope-open"></i></div>' +
                    "<h6>Select an email to read</h6>" +
                    "<p>Choose an email from your inbox to view its contents here.</p>" +
                    "</div>" +
                    "</div>" +
                    '<div class="mail-info-body p-4" id="mail-info-body"></div>' +
                    '<div class="mail-info-footer d-flex flex-wrap gap-2 align-items-center justify-content-between d-none" id="email-footer">' +
                    "<div>" +
                    '<button class="btn btn-icon btn-light" data-action="print" title="Print"><i class="ri-printer-line"></i></button>' +
                    '<button class="btn btn-icon btn-light ms-1" data-action="mark-read" title="Mark as read"><i class="ri-mail-open-line"></i></button>' +
                    '<button class="btn btn-icon btn-light ms-1" data-action="refresh" title="Refresh"><i class="ri-refresh-line"></i></button>' +
                    "</div>" +
                    "<div>" +
                    '<button class="btn btn-secondary" data-action="forward"><i class="ri-share-forward-line me-1"></i>Forward</button>' +
                    '<button class="btn btn-danger ms-1" data-action="reply"><i class="ri-reply-all-line me-1"></i>Reply</button>' +
                    "</div>" +
                    "</div>",
            );
        }

        $header = $("#email-header");
        $body = $("#mail-info-body");
        $footer = $("#email-footer");
        return {
            $container: $container,
            $header: $header,
            $body: $body,
            $footer: $footer,
        };
    }

    function renderEmpty(text) {
        $messages.html(
            '<div class="text-center p-5">' +
                '<div class="mb-3"><i class="ri-mail-line fs-48 text-muted"></i></div>' +
                '<h6 class="text-dark fw-500">' +
                escapeHtml(text) +
                "</h6>" +
                '<p class="text-muted mb-0">No emails found for the last 3 months.</p>' +
                "</div>",
        );
        syncCheckAllState();
        setFocusedPanelVisible(false, 0);
    }

    function renderEmails(emails) {
        if (!Array.isArray(emails) || emails.length === 0) {
            renderEmpty("No emails found");
            return;
        }

        function bucketKey(dateStr) {
            const dt = new Date(dateStr || 0);
            if (Number.isNaN(dt.getTime())) {
                return "older";
            }

            const now = new Date();
            const startToday = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
            );
            const startTarget = new Date(
                dt.getFullYear(),
                dt.getMonth(),
                dt.getDate(),
            );
            const diffDays = Math.floor((startToday - startTarget) / 86400000);

            if (diffDays === 0) return "today";
            if (diffDays === 1) return "yesterday";
            if (diffDays <= 6) return "this_week";
            return "older";
        }

        const sectionOrder = ["today", "yesterday", "this_week", "older"];
        const sectionLabels = {
            today: "Today",
            yesterday: "Yesterday",
            this_week: "This Week",
            older: "Older",
        };

        const sections = {
            today: [],
            yesterday: [],
            this_week: [],
            older: [],
        };

        emails.forEach(function (email) {
            sections[bucketKey(email.date_received || email.received_at)].push(
                email,
            );
        });

        const items = sectionOrder
            .map(function (sectionKey) {
                const rows = sections[sectionKey];
                if (!rows.length) {
                    return "";
                }

                const rowHtml = rows
                    .map(function (email, idx) {
                        const unreadClass = email.is_read ? "" : " unread";
                        const emailId = email.id || "";
                        const emailUid =
                            email.uid || email.message_id || emailId;
                        const checkboxId =
                            "checkbox-" +
                            (emailId || "row-" + sectionKey + "-" + idx);
                        const fromName =
                            email.from_name ||
                            email.sender_name ||
                            email.from_email ||
                            email.sender_email ||
                            "Unknown Sender";
                        const fromCategory =
                            email.from_category ||
                            (email.from && email.from.category) ||
                            "";
                        const fromCategoryColor = safeColorClass(
                            email.from_category_color ||
                                (email.from && email.from.category_color) ||
                                "primary",
                        );
                        const hasAttachments = !!email.has_attachments;
                        const isStarred = !!email.is_starred;
                        const subject = email.subject || "(No Subject)";
                        const previewText = truncate(
                            stripHtml(
                                email.body_preview ||
                                    email.body_text ||
                                    email.preview ||
                                    "",
                            ),
                            120,
                        );

                        return (
                            '<li class="mail-page' +
                            unreadClass +
                            '" data-email-id="' +
                            escapeHtml(emailId) +
                            '" data-email-uid="' +
                            escapeHtml(emailUid) +
                            '">' +
                            '<div class="d-flex align-items-top">' +
                            '<div class="me-3 mt-1">' +
                            '<input class="form-check-input email-checkbox mailc-checkbox" type="checkbox" id="' +
                            escapeHtml(checkboxId) +
                            '" value="' +
                            escapeHtml(emailId) +
                            '">' +
                            "</div>" +
                            '<div class="flex-fill email-content" role="button" tabindex="0">' +
                            '<div class="email-header">' +
                            '<p class="mb-1 fs-12 d-flex justify-content-between align-items-center">' +
                            '<span class="sender-name ' +
                            (email.is_read ? "" : "fw-bold") +
                            '">' +
                            escapeHtml(fromName) +
                            (fromCategory
                                ? '<span class="badge bg-' +
                                  escapeHtml(fromCategoryColor) +
                                  ' ms-1">' +
                                  escapeHtml(fromCategory) +
                                  "</span>"
                                : "") +
                            "</span>" +
                            '<span class="text-muted fw-normal fs-11 d-flex align-items-center mailc-date">' +
                            (hasAttachments
                                ? '<i class="ri-attachment-2 align-middle fs-12 me-1"></i>'
                                : "") +
                            escapeHtml(
                                formatDate(
                                    email.date_received || email.received_at,
                                ),
                            ) +
                            "</span>" +
                            "</p>" +
                            "</div>" +
                            '<div class="email-body">' +
                            '<p class="mail-msg mb-0">' +
                            '<span class="d-block mb-0 ' +
                            (email.is_read ? "" : "fw-semibold ") +
                            'text-truncate email-subject">' +
                            escapeHtml(subject) +
                            "</span>" +
                            '<span class="fs-11 text-muted text-wrap text-truncate email-preview">' +
                            escapeHtml(previewText) +
                            "</span>" +
                            "</p>" +
                            "</div>" +
                            "</div>" +
                            '<div class="email-actions ms-2">' +
                            '<button class="btn p-0 lh-1 mail-starred border-0 ' +
                            (isStarred ? "starred" : "") +
                            '" data-email-id="' +
                            escapeHtml(emailId) +
                            '" data-action="star">' +
                            '<i class="ri-star-' +
                            (isStarred ? "fill" : "line") +
                            ' fs-14"></i>' +
                            "</button>" +
                            "</div>" +
                            "</div>" +
                            "</li>"
                        );
                    })
                    .join("");

                return (
                    '<li class="mail-section-title">' +
                    '<span class="mail-section-caret"><i class="ri-arrow-down-s-line"></i></span>' +
                    "<span>" +
                    sectionLabels[sectionKey] +
                    "</span>" +
                    "</li>" +
                    rowHtml
                );
            })
            .join("");

        $messages.html(
            '<ul class="list-unstyled mb-0 mail-messages-container customScrollBar">' +
                items +
                "</ul>",
        );
        syncCheckAllState();
    }

    function groupByConversation(emails) {
        if (!Array.isArray(emails) || emails.length === 0) {
            return [];
        }

        const bucket = new Map();

        emails.forEach(function (item) {
            const key = item.conversation_id || item.id;
            const ts = new Date(item.received_at || 0).getTime() || 0;

            if (!bucket.has(key)) {
                bucket.set(key, {
                    latest: item,
                    latestTs: ts,
                    thread_count: 1,
                    has_unread: !item.is_read,
                });
                return;
            }

            const group = bucket.get(key);
            group.thread_count += 1;
            group.has_unread = group.has_unread || !item.is_read;

            if (ts > group.latestTs) {
                group.latest = item;
                group.latestTs = ts;
            }

            bucket.set(key, group);
        });

        return Array.from(bucket.values())
            .map(function (group) {
                return Object.assign({}, group.latest, {
                    thread_count: group.thread_count,
                    is_read: !group.has_unread,
                });
            })
            .sort(function (a, b) {
                const at = new Date(a.received_at || 0).getTime() || 0;
                const bt = new Date(b.received_at || 0).getTime() || 0;
                return bt - at;
            });
    }

    function applySearchFilter(term) {
        const q = String(term || "")
            .trim()
            .toLowerCase();

        if (!q) {
            renderEmails(state.emails);
            return;
        }

        const filtered = state.emails.filter(function (email) {
            return (
                String(email.subject || "")
                    .toLowerCase()
                    .includes(q) ||
                String(email.preview || "")
                    .toLowerCase()
                    .includes(q) ||
                String(email.body_preview || "")
                    .toLowerCase()
                    .includes(q) ||
                String(email.body_text || "")
                    .toLowerCase()
                    .includes(q) ||
                String(email.from_name || "")
                    .toLowerCase()
                    .includes(q) ||
                String(email.sender_name || "")
                    .toLowerCase()
                    .includes(q) ||
                String(email.from_email || "")
                    .toLowerCase()
                    .includes(q) ||
                String(email.sender_email || "")
                    .toLowerCase()
                    .includes(q)
            );
        });

        if (filtered.length === 0) {
            renderEmpty("No matching emails");
            return;
        }

        renderEmails(filtered);
    }

    function fetchCurrentMonthEmails(options) {
        const opts = options || {};
        if (!cfg.currentMonthEndpoint) {
            notifyError("Missing last 3 months email endpoint configuration.");
            return;
        }

        setLoading(true);

        $.ajax({
            url: cfg.currentMonthEndpoint,
            type: "GET",
            dataType: "json",
            data: {
                folder: state.folder,
                limit: state.limit,
                page: state.page,
                force_refresh: opts.forceRefresh ? 1 : 0,
            },
        })
            .done(function (response) {
                if (!response || !response.success) {
                    notifyError(
                        response && response.message
                            ? response.message
                            : "Failed to fetch emails.",
                    );
                    renderEmpty("Unable to load emails");
                    return;
                }

                state.rawEmails = Array.isArray(response.data)
                    ? response.data
                    : [];
                state.emails = groupByConversation(state.rawEmails);
                renderEmails(state.emails);
            })
            .fail(function (xhr) {
                const message =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : "Failed to fetch last 3 months emails.";

                notifyError(message);
                renderEmpty("Unable to load emails");
            })
            .always(function () {
                setLoading(false);
            });
    }

    function loadEmailDetail(messageId, $item) {
        if (!messageId) {
            return;
        }

        const scaffold = ensureEmailDetailScaffold();
        if (!scaffold.$body.length) {
            return;
        }
        state.composeModeActive = false;
        $("#empty-email-state").addClass("d-none");
        $("#mail-recepients .text-center.text-muted").addClass("d-none");

        if (!cfg.messageDetailEndpoint) {
            notifyError("Missing email detail endpoint configuration.");
            return;
        }

        const detailUrl = cfg.messageDetailEndpoint.replace(
            "__ID__",
            encodeURIComponent(messageId),
        );
        $(".mail-page").removeClass("active");
        if ($item && $item.length) {
            $item.addClass("active");
        }

        scaffold.$body.html(
            '<div class="d-flex flex-column align-items-center justify-content-center text-center" style="width: 100%; height: calc(100vh - 250px); max-height: calc(100vh - 250px); background: #ffffff;">' +
                '<div class="spinner-border text-primary mb-3" role="status"></div>' +
                '<span class="text-muted">Loading email...</span>' +
                "</div>",
        );

        $.ajax({
            url: detailUrl,
            type: "GET",
            dataType: "json",
        })
            .done(function (response) {
                if (!response || !response.success || !response.data) {
                    notifyError(
                        response && response.message
                            ? response.message
                            : "Failed to load email detail.",
                    );
                    return;
                }

                renderEmailDetail(response.data);
            })
            .fail(function (xhr) {
                const message =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : "Failed to load email detail.";
                notifyError(message);
            });
    }

    function bindEvents() {
        $(document).on("change", "#checkAll", function () {
            const isChecked = $(this).is(":checked");
            getRowCheckboxes().prop("checked", isChecked);
            $(this).prop("indeterminate", false);
            const checkedCount = isChecked ? getRowCheckboxes().length : 0;
            setFocusedPanelVisible(isChecked && checkedCount > 2, checkedCount);
        });

        $(document).on(
            "change",
            ".mail-messages-container .email-checkbox.mailc-checkbox",
            function () {
                syncCheckAllState();
            },
        );

        $(document).on(
            "click",
            ".email-checkbox, .mail-starred",
            function (event) {
                event.stopPropagation();
            },
        );

        $(document).on("click", ".mail-page", function () {
            if (selectedRowCount() > 2) {
                return;
            }
            ensureEmailDetailScaffold().$header.removeClass("d-none");
            const detailId =
                $(this).data("emailId") || $(this).data("emailUid");
            loadEmailDetail(detailId, $(this));
        });

        $(document).on("click", "#composeInlineBtn", function (event) {
            event.preventDefault();
            renderInlineComposeAnalyzed();
        });

        $(document).on(
            "click",
            "#mail-info-body .focused-action-btn",
            function (event) {
                event.preventDefault();
                const action = $(this).data("action");

                if (action === "cancel") {
                    clearSelections();
                    return;
                }

                const labels = {
                    delete: "Empty Focused",
                    flag: "Flag",
                    "mark-read": "Mark as read",
                    "mark-unread": "Mark as unread",
                    move: "Move",
                    insights: "Viva Insights",
                };

                if (window.toastr && typeof window.toastr.info === "function") {
                    window.toastr.info(
                        (labels[action] || "This action") +
                            " is not wired yet.",
                    );
                }
            },
        );

        $(document).on("click", ".mail-folder-link", function (event) {
            event.preventDefault();

            const nextFolder = $(this).data("folder") || "inbox";
            state.folder = String(nextFolder);
            state.page = 1;

            $(".mail-folder-link").removeClass("active");
            $(this).addClass("active");

            $folderTitle.text(folderLabel(state.folder) + " (Last 3 Months)");
            fetchCurrentMonthEmails();
        });

        $("#syncEmailsBtn").on("click", function () {
            fetchCurrentMonthEmails({ forceRefresh: true });
        });

        $("#searchButton").on("click", function () {
            applySearchFilter($("#emailSearch").val());
        });

        $("#emailSearch").on("input", function () {
            applySearchFilter($(this).val());
        });
    }

    $(function () {
        ensureConversationStyles();
        bindEvents();
        setFocusedPanelVisible(false, 0);
        $folderTitle.text(folderLabel(state.folder) + " (Last 3 Months)");
        fetchCurrentMonthEmails();
    });
})(jQuery);
