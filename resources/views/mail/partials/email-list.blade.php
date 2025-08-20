<div class="total-mails border">
    <!-- Header -->
    <div class="p-3 d-flex align-items-center border-bottom">
        <div class="me-3">
            <input class="form-check-input" type="checkbox" id="checkAll" value="">
        </div>
        <div class="flex-fill">
            <h6 class="fw-semibold mb-0">{{ ucfirst($folder) }} ({{ $emails->count() }})</h6>
        </div>
        <button class="btn btn-icon btn-light me-1 d-lg-none d-block total-mails-close">
            <i class="ri-close-line"></i>
        </button>
        <div class="dropdown">
            <button class="btn btn-icon btn-light btn-wave" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
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

    <!-- Search -->
    <div class="p-3 border-bottom">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0" placeholder="Search Email" id="emailSearch">
            <button class="btn btn-light" type="button" id="searchButton">
                <i class="ri-search-line text-muted"></i>
            </button>
        </div>
    </div>

    <!-- Email Messages -->
    <div class="mail-messages" id="mail-messages">
        @if ($emails->count() > 0)
            <ul class="list-unstyled mb-0 mail-messages-container">
                @foreach ($emails as $email)
                    @include('mail.components.email-item', ['email' => $email])
                @endforeach
            </ul>
        @else
            <div class="text-center p-5">
                <div class="mb-3">
                    <i class="ri-mail-line fs-48 text-muted"></i>
                </div>
                <h6 class="fw-semibold text-muted">No emails found</h6>
                <p class="text-muted mb-0">
                    @if (!auth()->user()->hasOutlookConnection())
                        Connect your Outlook account to start receiving emails.
                    @else
                        Your {{ $folder }} folder is empty.
                    @endif
                </p>
                @if (!auth()->user()->hasOutlookConnection())
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

<!-- Loading Overlay -->
<div class="email-list-loading d-none">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>
