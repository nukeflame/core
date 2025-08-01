<div class="mail-navigation border">
    <div class="d-grid align-items-top p-3 border-bottom">
        <button class="btn btn-success d-flex align-items-center justify-content-center" data-bs-toggle="modal"
            data-bs-target="#mail-Compose" disabled>
            <i class="ri-add-circle-line fs-16 align-middle me-1"></i>Compose Mail
        </button>

        <div class="modal modal-lg  fade" id="mail-Compose" tabindex="-1" aria-labelledby="mail-ComposeLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="mail-ComposeLabel">Compose Mail</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4">
                        <div class="row">
                            <div class="col-xl-6 mb-2">
                                <label for="fromMail" class="form-label">From<sup><i
                                            class="ri-star-s-fill text-success fs-8"></i></sup></label>
                                <input type="email" class="form-control" id="fromMail"
                                    value="tech@acentriagroup.com">
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label for="toMail" class="form-label">To<sup><i
                                            class="ri-star-s-fill text-success fs-8"></i></sup></label>
                                <select class="form-control" name="toMail" id="toMail" multiple>
                                    <option value="" selected></option>
                                </select>
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label for="mailCC" class="form-label text-dark fw-semibold">Cc</label>
                                <input type="email" class="form-control" id="mailCC">
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label for="mailBcc" class="form-label text-dark fw-semibold">Bcc</label>
                                <input type="email" class="form-control" id="mailBcc">
                            </div>
                            <div class="col-xl-12 mb-2">
                                <label for="Subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="Subject" placeholder="Subject">
                            </div>
                            <div class="col-xl-12">
                                <label class="col-form-label">Content :</label>
                                <div class="mail-compose">
                                    {{-- <div id="mail-compose-editor"></div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="d-flex align-items-top p-3 bg-primary" id="userInfo">
        <div>
            <span class="avatar avatar-md online avatar-rounded">
                <img src="/assets/images/faces/9.jpg
            " alt="">
            </span>
        </div>
        <div class="ms-2">
            <p class="fw-semibold mb-0 text-fixed-white" id="displayName"></p>
            <p class="text-fixed-white op-7 fs-11 mb-0" id="displayEmail"></p>
        </div>
    </div>
    <div>
        <ul class="list-unstyled mail-main-nav" id="mail-main-nav">
            <li class="px-0 pt-0">
                <span class="fs-11 text-muted op-7 fw-semibold">MAILS</span>
            </li>
            <li class="mail-type {{ request()->routeIs('admin.email') ? 'active' : '' }}">
                <a href="{{ route('admin.email') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-inbox-archive-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            All Mails
                        </span>

                        @if ($countAll > 0)
                            <span class="badge bg-success-transparent rounded-pill">{{ $countAll }}</span>
                        @endif

                    </div>
                </a>
            </li>

            <li
                class="mail-type {{ request()->routeIs('admin.folder') && request()->route('folder') === 'inbox' ? 'active' : '' }}">
                <a href="{{ route('admin.folder', 'inbox') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-inbox-archive-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Inbox
                        </span>
                        @if ($countInbox > 0)
                            <span class="badge bg-primary-transparent rounded-circle">{{ $countInbox }}</span>
                        @endif
                    </div>
                </a>
            </li>
            <li
                class="mail-type {{ request()->routeIs('admin.folder') && request()->route('folder') === 'sent' ? 'active' : '' }}">
                <a href="{{ route('admin.folder', 'sent') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-send-plane-2-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Sent
                        </span>
                    </div>
                </a>
            </li>
            <li
                class="mail-type {{ request()->routeIs('admin.folder') && request()->route('folder') === 'drafts' ? 'active' : '' }}">
                <a href="{{ route('admin.folder', 'drafts') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-draft-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Drafts
                        </span>
                    </div>
                </a>
            </li>
            <li
                class="mail-type {{ request()->routeIs('admin.folder') && request()->route('folder') === 'spam' ? 'active' : '' }}">
                <a href="{{ route('admin.folder', 'spam') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-spam-2-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Spam
                        </span>
                        @if ($countSpam > 0)
                            <span class="badge bg-danger-transparent rounded-circle">{{ $countSpam }}</span>
                        @endif
                    </div>
                </a>
            </li>
            <li
                class="mail-type {{ request()->routeIs('admin.folder') && request()->route('folder') === 'important' ? 'active' : '' }}">
                <a href="{{ route('admin.folder', 'important') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-bookmark-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Important
                        </span>
                    </div>
                </a>
            </li>
            <li
                class="mail-type {{ request()->routeIs('admin.folder') && request()->route('folder') === 'trash' ? 'active' : '' }}">
                <a href="{{ route('admin.folder', 'trash') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-delete-bin-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Trash
                        </span>
                    </div>
                </a>
            </li>
            <li
                class="mail-type {{ request()->routeIs('admin.folder') && request()->route('folder') === 'archive' ? 'active' : '' }}">
                <a href="{{ route('admin.folder', 'archive') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-archive-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Archive
                        </span>
                    </div>
                </a>
            </li>
            <li
                class="mail-type {{ request()->routeIs('admin.folder') && request()->route('folder') === 'starred' ? 'active' : '' }}">
                <a href="{{ route('admin.folder', 'starred') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-star-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Starred
                        </span>
                        @if ($countStarred > 0)
                            <span class="badge bg-warning-transparent rounded-circle">{{ $countStarred }}</span>
                        @endif
                    </div>
                </a>
            </li>
            {{-- <li class="px-0">
                <span class="fs-11 text-muted op-7 fw-semibold">SETTINGS</span>
            </li>
            <li>
                <a href="{{ route('admin.folder', 'settings') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-settings-3-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Settings
                        </span>
                    </div>
                </a>
            </li>
            <li class="px-0">
                <span class="fs-11 text-muted op-7 fw-semibold">LABELS</span>
            </li>
            <li>
                <a href="{{ route('admin.folder', 'labels') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-price-tag-line align-middle fs-14 fw-semibold text-secondary"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Mail
                        </span>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.folder', 'home') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-price-tag-line align-middle fs-14 fw-semibold text-danger"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Home
                        </span>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.folder', 'work') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-price-tag-line align-middle fs-14 fw-semibold text-success"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Work
                        </span>
                    </div>
                </a>
            </li> --}}
            {{-- <li>
                <a href="{{ route('admin.folder', 'friends') }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-price-tag-line align-middle fs-14 fw-semibold text-dark"></i>
                        </span>
                        <span class="flex-fill text-nowrap">
                            Friends
                        </span>
                    </div>
                </a>
            </li> --}}
        </ul>
    </div>
</div>
