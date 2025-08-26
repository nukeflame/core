<div class="mail-navigation border">
    <!-- Compose Button -->
    <div class="d-grid align-items-top p-3 border-bottom">
        <button class="btn btn-success d-flex align-items-center justify-content-center" data-bs-toggle="modal"
            data-bs-target="#mail-compose-modal">
            <i class="ri-add-circle-line fs-16 align-middle me-1"></i>Compose Mail
        </button>
    </div>

    <!-- User Profile -->
    <div class="d-flex align-items-top p-3 bg-primary">
        <div>
            <span class="avatar avatar-md online avatar-rounded">
                <img src="{{ auth()->user()->avatar ?? asset('assets/images/faces/default.png') }}" alt="">
            </span>
        </div>
        <div class="ms-2">
            <p class="fw-semibold mb-0 text-fixed-white">{{ auth()->user()->name }}</p>
            <p class="text-fixed-white op-7 fs-11 mb-0">{{ auth()->user()->email }}</p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div>
        <ul class="list-unstyled mail-main-nav customScrollBar" id="mail-main-nav"
            style="overflow-y: auto;height: 964px;">
            <li class="px-0 pt-0">
                <span class="fs-11 text-muted op-7 fw-semibold">MAILS</span>
            </li>

            @php
                $navigationItems = [
                    [
                        'id' => 'all',
                        'icon' => 'ri-inbox-archive-line',
                        'label' => 'All Mails',
                        'count' => $emails->count(),
                        'badge' => 'success',
                    ],
                    [
                        'id' => 'inbox',
                        'icon' => 'ri-inbox-archive-line',
                        'label' => 'Inbox',
                        'count' => $emails->where('folder', 'inbox')->count(),
                        'badge' => 'primary',
                    ],
                    ['id' => 'sent', 'icon' => 'ri-send-plane-2-line', 'label' => 'Sent'],
                    ['id' => 'drafts', 'icon' => 'ri-draft-line', 'label' => 'Drafts'],
                    ['id' => 'spam', 'icon' => 'ri-spam-2-line', 'label' => 'Spam', 'count' => 4, 'badge' => 'danger'],
                    ['id' => 'important', 'icon' => 'ri-bookmark-line', 'label' => 'Important'],
                    ['id' => 'trash', 'icon' => 'ri-delete-bin-line', 'label' => 'Trash'],
                    ['id' => 'archive', 'icon' => 'ri-archive-line', 'label' => 'Archive'],
                    [
                        'id' => 'starred',
                        'icon' => 'ri-star-line',
                        'label' => 'Starred',
                        'count' => 12,
                        'badge' => 'warning',
                    ],
                ];
            @endphp

            @foreach ($navigationItems as $item)
                <li class="mail-type {{ $folder === $item['id'] ? 'active' : '' }}">
                    <a href="{{ route('mail.index', ['folder' => $item['id']]) }}" class="mail-folder-link"
                        data-folder="{{ $item['id'] }}">
                        <div class="d-flex align-items-center">
                            <span class="me-2 lh-1">
                                <i class="{{ $item['icon'] }} align-middle fs-14"></i>
                            </span>
                            <span class="flex-fill text-nowrap">{{ $item['label'] }}</span>
                            @if (isset($item['count']) && $item['count'] > 0)
                                <span
                                    class="badge bg-{{ $item['badge'] ?? 'secondary' }}-transparent rounded-{{ isset($item['badge']) && $item['badge'] === 'success' ? 'pill' : 'circle' }}">
                                    {{ number_format($item['count']) }}
                                </span>
                            @endif
                        </div>
                    </a>
                </li>
            @endforeach

            <!-- Settings -->
            {{-- <li class="px-0">
                <span class="fs-11 text-muted op-7 fw-semibold">SETTINGS</span>
            </li>
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#settings-modal">
                    <div class="d-flex align-items-center">
                        <span class="me-2 lh-1">
                            <i class="ri-settings-3-line align-middle fs-14"></i>
                        </span>
                        <span class="flex-fill text-nowrap">Settings</span>
                    </div>
                </a>
            </li> --}}

            <li class="px-0">
                <span class="fs-11 text-muted op-7 fw-semibold">LABELS</span>
            </li>
            @php
                $labels = [
                    ['name' => 'Mail', 'color' => 'secondary'],
                    ['name' => 'Home', 'color' => 'danger'],
                    ['name' => 'Work', 'color' => 'success'],
                    ['name' => 'Friends', 'color' => 'dark'],
                ];
            @endphp

            @foreach ($labels as $label)
                <li>
                    <a href="#" data-label="{{ strtolower($label['name']) }}">
                        <div class="d-flex align-items-center">
                            <span class="me-2 lh-1">
                                <i
                                    class="ri-price-tag-line align-middle fs-14 fw-semibold text-{{ $label['color'] }}"></i>
                            </span>
                            <span class="flex-fill text-nowrap">{{ $label['name'] }}</span>
                        </div>
                    </a>
                </li>
            @endforeach

            <li class="px-0">
                <span class="fs-11 text-muted op-7 fw-semibold">ONLINE USERS</span>
            </li>

            <li class="mail-online-users">
                {{-- {{ logger()->debug(json_encode($onlineUsers, JSON_PRETTY_PRINT)) }} --}}
                <ul class="mail-online-list customScrollBar">
                    @if (isset($onlineUsers) && $onlineUsers->count() > 0)
                        @foreach ($onlineUsers->take(30) as $onlineUser)
                            <li>
                                <a href="#" class="contact-item online-contact"
                                    data-contact-id="{{ $onlineUser['id'] }}" data-email="{{ $onlineUser['email'] }}"
                                    data-name="{{ $onlineUser['name'] }}">
                                    <div class="d-flex align-items-top lh-1">
                                        <div class="me-2">
                                            <span
                                                class="avatar avatar-sm {{ $onlineUser['isOnline'] ? 'online' : 'offline' }} avatar-rounded">
                                                <img src="{{ $onlineUser->avatar ?? asset('assets/images/faces/default.png') }}"
                                                    alt="">
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-default fw-medium mb-1">{{ $onlineUser['name'] }}</p>
                                            <p class="fs-12 text-muted mb-0">
                                                {{ Str::limit($contact->status ?? 'Available', 25) }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </li>
        </ul>
    </div>
</div>
