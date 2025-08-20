<li class="email-item {{ !$email->is_read ? 'unread' : '' }}" data-email-id="{{ $email->id }}">
    <div class="d-flex align-items-top">
        <!-- Checkbox -->
        <div class="me-3 mt-1">
            <input class="form-check-input email-checkbox" type="checkbox" id="checkbox-{{ $email->id }}"
                value="{{ $email->id }}">
        </div>

        <!-- Avatar -->
        <div class="me-1 lh-1">
            <span
                class="avatar avatar-md {{ $email->from->is_online ?? false ? 'online' : 'offline' }} me-2 avatar-rounded mail-msg-avatar">
                @if (isset($email->from->avatar))
                    <img src="{{ $email->from->avatar }}" alt="{{ $email->from->name }}">
                @else
                    <div class="avatar-initial">{{ strtoupper(substr($email->from->name ?? 'U', 0, 1)) }}</div>
                @endif
            </span>
        </div>

        <!-- Email Content -->
        <div class="flex-fill email-content" role="button" tabindex="0">
            <div class="email-header">
                <p class="mb-1 fs-12 d-flex justify-content-between align-items-center">
                    <span class="sender-name {{ !$email->is_read ? 'fw-bold' : '' }}">
                        {{ $email->from->name ?? $email->from->address }}
                        @if ($email->from->category ?? false)
                            <span class="badge bg-{{ $email->from->category_color ?? 'primary' }} ms-1">
                                {{ $email->from->category }}
                            </span>
                        @endif
                    </span>
                    <span class="text-muted fw-normal fs-11 d-flex align-items-center">
                        @if ($email->has_attachments)
                            <i class="ri-attachment-2 align-middle fs-12 me-1"></i>
                        @endif
                        @if ($email->is_important)
                            <i class="ri-error-warning-line align-middle fs-12 me-1 text-warning"></i>
                        @endif
                        {{ $email->received_at->format('M j, g:i A') }}
                    </span>
                </p>
            </div>

            <div class="email-body">
                <p class="mail-msg mb-0">
                    <span class="d-block mb-0 {{ !$email->is_read ? 'fw-semibold' : '' }} text-truncate email-subject">
                        {{ $email->subject ?: '(No Subject)' }}
                    </span>
                    <span class="fs-11 text-muted text-wrap text-truncate email-preview">
                        {{ Str::limit($email->preview ?? '', 100) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="email-actions ms-2">
            <button class="btn p-0 lh-1 mail-starred border-0 {{ $email->is_starred ?? false ? 'starred' : '' }}"
                data-email-id="{{ $email->id }}" data-action="star">
                <i class="ri-star-{{ $email->is_starred ?? false ? 'fill' : 'line' }} fs-14"></i>
            </button>
        </div>
    </div>
</li>
