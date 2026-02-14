<div class="total-mails border">
    <div class="p-3 d-flex align-items-center border-bottom">
        <div class="me-3">
            <input class="form-check-input mailc-checkbox" type="checkbox" id="checkAll" value="">
        </div>
        <div class="flex-fill">
            <h6 class="fw-semibold mb-0" style="line-height: 0px;">
                Inbox
            </h6>
        </div>
        <button class="btn btn-icon btn-light me-1 d-lg-none d-block total-mails-close">
            <i class="ri-close-line"></i>
        </button>
        <div class="d-flex">
            <button class="btn btn-icon btn-light btn-wave me-1" type="button" title="Sync Emails" id="syncEmailsBtn">
                <i class="ti ti-refresh"></i>
            </button>
            <div class="dropdown">
                <button class="btn btn-icon btn-light btn-wave" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false" title="More Options">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-action="recent">Recent</a></li>
                    <li><a class="dropdown-item" href="#" data-action="unread">Unread</a></li>
                    <li><a class="dropdown-item" href="#" data-action="mark-all-read">Mark All Read</a></li>
                    <li><a class="dropdown-item" href="#" data-action="spam">Move to Spam</a></li>
                    <li><a class="dropdown-item" href="#" data-action="delete-all">Delete All</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="p-3 border-bottom">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0" placeholder="Search Email" id="emailSearch">
            <button class="btn btn-light" type="button" id="searchButton">
                <i class="ri-search-line text-muted"></i>
            </button>
        </div>
    </div>

    <div class="mail-messages" id="mail-messages">
        <div class="text-center p-5">
            <div class="mb-3">
                <i class="ri-mail-line fs-48 text-muted"></i>
            </div>
            <h6 class="text-dark fw-500">No emails found</h6>
        </div>
    </div>

</div>

<div class="email-list-loading d-none">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<style>
    .mail-messages {
        height: calc(100vh - 260px);
        min-height: 340px;
    }

    .focused-actions-panel {
        height: calc(100vh - 260px);
        min-height: 340px;
        background: linear-gradient(165deg, #f3f6fd 0%, #f5ecf8 46%, #fcebf6 100%);
        border-top: 1px solid #eef0f3;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px 12px;
    }

    .focused-actions-inner {
        max-width: 320px;
        width: 100%;
    }

    .focused-actions-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto;
        border-radius: 50%;
        background: #ffffff;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        font-size: 34px;
    }

    .focused-actions-title {
        font-size: 14px;
        color: #334155;
    }

    .focused-actions-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: center;
    }

    .focused-action-btn {
        text-decoration: none !important;
        color: #6d28d9 !important;
        font-size: 14px;
        padding: 4px 10px;
    }

    .focused-action-btn:hover {
        color: #5b21b6 !important;
    }

    .focused-action-btn.cancel-btn {
        color: #7c3aed !important;
    }

    .mail-messages-container {
        height: 100%;
        max-height: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        margin: 0;
    }

    .mailc-date {
        font-family: 'Aptos', sans-serif !important;
        font-size: 12px !important;
        color: #4b5563 !important;
        white-space: nowrap;
        min-width: 72px;
        text-align: right;
        line-height: 1.2;
        margin-top: 2px;
    }

    .mail-page {
        padding: 8px 12px !important;
        border-bottom: 1px solid #edebe9 !important;
        transition: background-color 0.1s ease;
        position: relative;
        display: block;
        cursor: pointer;
        background: #f9fafb;
    }

    .mail-messages-container .mail-page:last-child {
        margin-bottom: 12px;
    }

    .mail-page:hover {
        background-color: #eef2f7;
    }

    .mail-page.active {
        background-color: #e9eef7;
    }

    .mail-page.unread {
        background-color: #fff;
        border-left: 3px solid var(--primary-color);
    }

    .mail-page.loading .align-items-top {
        display: none !important;
    }

    .mail-page.loading .loading-overlay {
        height: 92px;
    }

    .email-content {
        min-height: 44px;
        padding: 2px 0;
    }

    .sender-name {
        font-family: 'Aptos', sans-serif;
        color: #242424;
        font-size: 14px;
        line-height: 1.2;
        display: block;
    }

    .email-subject {
        font-family: 'Aptos', sans-serif;
        color: #242424;
        font-size: 14px !important;
        line-height: 1.25;
        margin-top: 2px;
        font-weight: 500;
    }

    .email-preview {
        font-family: 'Aptos', sans-serif;
        color: #616161 !important;
        font-size: 12px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 1.25;
        margin-top: 2px;
    }

    .mail-section-title {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #4b5563;
        text-transform: none;
        background: #f3f4f6;
        border-bottom: 1px solid #e5e7eb !important;
        padding: 6px 10px !important;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .mail-section-caret {
        color: #6b7280;
        display: inline-flex;
        align-items: center;
    }

    .mail-row {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .mail-left-controls {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 52px;
        margin-top: 2px;
    }

    .mail-caret {
        width: 12px;
        color: #6b7280;
        display: inline-flex;
        justify-content: center;
    }

    .mail-row-check {
        margin-top: 0 !important;
    }

    .mail-row-star {
        border: 0;
        background: transparent;
        padding: 0;
        color: #9ca3af;
        line-height: 1;
        display: inline-flex;
        align-items: center;
    }

    .mail-row-star:hover {
        color: #6b7280;
    }

    .mail-row-avatar {
        width: 24px;
        height: 24px;
        min-width: 24px;
        border-radius: 999px;
        background: #cbd5e1;
        color: #334155;
        font-size: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 1px;
    }

    .mail-row-content {
        flex: 1;
        min-width: 0;
    }

    .mail-row-top {
        display: flex;
        gap: 8px;
        align-items: baseline;
        justify-content: space-between;
    }

    .mail-thread-count {
        margin-left: 6px;
        font-size: 10px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1e40af;
        padding: 1px 6px;
        vertical-align: middle;
        font-weight: 700;
    }

    .loading-dim {
        opacity: 0.5;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .loading-dim:hover {
        opacity: 0.5;
        /* Prevent hover effects during loading */
    }

    .loading-dim {
        animation: pulse 1.5s ease-in-out infinite alternate;
    }

    @keyframes pulse {
        0% {
            opacity: 0.3;
        }

        100% {
            opacity: 0.7;
        }
    }

    .empty-state-header {
        justify-content: center;
        /* height: calc(100vh - 8.1rem); */
    }

    @media (max-width: 991px) {
        .mail-messages {
            height: calc(100vh - 240px);
            min-height: 300px;
        }

        .focused-actions-panel {
            height: calc(100vh - 240px);
            min-height: 300px;
        }

        .mailc-date {
            min-width: 64px;
        }
    }
</style>
