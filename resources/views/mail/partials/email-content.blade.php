<div class="mails-information border">
    <div class="mail-info-header d-flex flex-wrap gap-2 align-items-center" id="email-header">
        <div class="empty-state" id="empty-email-state">
            <i class="ri-mail-open-line"></i>
            <h6>Select an email to read</h6>
            <p>Choose an email from your inbox to view its contents here.</p>
        </div>
    </div>
    <div class="mail-info-body p-4 d-none" id="mail-info-body">
        <!-- Email content will be loaded here dynamically -->
    </div>
    <div class="mail-info-footer d-flex flex-wrap gap-2 align-items-center justify-content-between d-none"
        id="email-footer">
        <div>
            <button class="btn btn-icon btn-light" data-action="print" title="Print">
                <i class="ri-printer-line"></i>
            </button>
            <button class="btn btn-icon btn-light ms-1" data-action="mark-read" title="Mark as read">
                <i class="ri-mail-open-line"></i>
            </button>
            <button class="btn btn-icon btn-light ms-1" data-action="refresh" title="Refresh">
                <i class="ri-refresh-line"></i>
            </button>
        </div>
        <div>
            <button class="btn btn-secondary" data-action="forward">
                <i class="ri-share-forward-line me-1"></i>Forward
            </button>
            <button class="btn btn-danger ms-1" data-action="reply">
                <i class="ri-reply-all-line me-1"></i>Reply
            </button>
        </div>
    </div>
</div>
