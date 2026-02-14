<div class="total-mails border">
    <div class="p-3 d-flex align-items-center border-bottom">
        <div class="me-3">
            <input class="form-check-input mailc-checkbox" type="checkbox" id="checkAll" value="">
        </div>
        <div class="flex-fill">
            <h6 class="fw-semibold mb-0" style="line-height: 0px;">
                {{ ucfirst($folder) }}
                @if ($emails->count() > 0)
                    ({{ $emails->count() }})
                @endif
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
        @if ($emails->count() > 0)
            <ul class="list-unstyled mb-0 mail-messages-container customScrollBar">
                @foreach ($emails as $email)
                    @include('mail.components.email-item', ['email' => $email])
                @endforeach
            </ul>
        @else
            <div class="text-center p-5">
                <div class="mb-3">
                    <i class="ri-mail-line fs-48 text-muted"></i>
                </div>
                <h6 class="text-dark fw-500">No emails found</h6>
                <p class="text-muted mb-0">
                    @if (!($isOutlookConnected ?? auth()->user()->hasOutlookConnection()))
                        Connect your Outlook account to start receiving emails.
                    @else
                        Your {{ $folder }} folder is empty.
                    @endif
                </p>
                @if (!($isOutlookConnected ?? auth()->user()->hasOutlookConnection()))
                    <button class="btn btn-primary mt-3" id="connectOutlookBtn">
                        <i class="ri-microsoft-line me-2"></i>Connect Outlook
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Pagination -->
    {{-- @if ($emails->hasPages())
        <div class="p-3 border-top">
            {{ $emails->links() }}
        </div>
    @endif --}}
</div>

<div class="email-list-loading d-none">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<style>
    .mailc-date {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 12px !important;
        color: #605e5c !important;
        white-space: nowrap;
    }

    .mail-page {
        padding: 8px 12px !important;
        border-bottom: 1px solid #edebe9 !important;
        transition: background-color 0.1s ease;
        position: relative;
    }

    .mail-page:hover {
        background-color: #f3f2f1;
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
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #242424;
        font-size: 14px;
    }

    .email-subject {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #242424;
        font-size: 14px !important;
    }

    .email-preview {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #616161 !important;
        font-size: 14px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
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
</style>
