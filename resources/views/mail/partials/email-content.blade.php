<div class="mails-information border bg-transparent">
    <div class="mail-info-header d-flex flex-wrap gap-2 align-items-center empty-state-header" id="email-header">
        <div class="empty-state text-center" id="empty-email-state">
            <div class="envelope-icon">
                <i class="bi bi-envelope-open"></i>
            </div>
            <h6>Select an email to read</h6>
            <p>Choose an email from your inbox to view its contents here.</p>
        </div>
    </div>
    <div class="mail-info-body p-4" id="mail-info-body">
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

<style>
    .mails-information {
        max-height: calc(100vh - 112px);
        display: flex;
        flex-direction: column;
        background: transparent;
    }

    .mail-info-header,
    .mail-info-footer {
        flex: 0 0 auto;
    }

    .mail-info-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
    }

    @media (max-width: 991px) {
        .mails-information {
            /* height: calc(100vh - 112px); */
            /* max-height: calc(100vh - 112px); */
        }
    }
</style>
