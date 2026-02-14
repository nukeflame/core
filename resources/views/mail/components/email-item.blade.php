<li class="mail-page {{ !$email->is_read ? 'unread' : '' }}" data-email-id="{{ $email->id }}"
    data-email-uid="{{ $email->uid ?? $email->message_id }}">
    <div class="d-flex align-items-top">
        <!-- Checkbox -->
        <div class="me-3 mt-1">
            <input class="form-check-input email-checkbox mailc-checkbox" type="checkbox" id="checkbox-{{ $email->id }}"
                value="{{ $email->id }}">
        </div>

        <!-- Avatar -->
        {{-- <div class="me-1 lh-1 btn_select_field">
            <span
                class="avatar avatar-md {{ $email->from->is_online ?? true ? 'online' : 'offline' }} me-2 avatar-rounded mail-msg-avatar">
                <img src="{{ $email->from->avatar ?? asset('assets/images/faces/default.png') }}" alt="">
            </span>
        </div> --}}

        <!-- Email Content -->
        <div class="flex-fill email-content" role="button" tabindex="0">
            <div class="email-header">
                <p class="mb-1 fs-12 d-flex justify-content-between align-items-center">
                    <span class="sender-name {{ !$email->is_read ? 'fw-bold' : '' }}">
                        {{ $email->from_name }}
                        @if ($email->from->category ?? false)
                            <span class="badge bg-{{ $email->from->category_color ?? 'primary' }} ms-1">
                                {{ $email->from->category }}
                            </span>
                        @endif
                    </span>
                    <span class="text-muted fw-normal fs-11 d-flex align-items-center mailc-date">
                        @if ($email->has_attachments)
                            <i class="ri-attachment-2 align-middle fs-12 me-1"></i>
                        @endif
                        {{-- @if ($email->is_important)
                            <i class="ri-error-warning-line align-middle fs-12 me-1 text-warning"></i>
                        @endif --}}
                        @php
                            $emailDate = \Carbon\Carbon::parse($email->date_received);
                            $today = \Carbon\Carbon::today();
                            $yesterday = \Carbon\Carbon::yesterday();

                            if ($emailDate->isToday()) {
                                $dateFormat = $emailDate->format('g:i A');
                            } elseif ($emailDate->isYesterday()) {
                                $dateFormat = 'Yesterday';
                            } elseif ($emailDate->isCurrentWeek()) {
                                $dateFormat = $emailDate->format('l'); // Day name (Monday, Tuesday, etc.)
                            } elseif ($emailDate->isCurrentYear()) {
                                $dateFormat = $emailDate->format('M j'); // Jan 15
                            } else {
                                $dateFormat = $emailDate->format('n/j/y'); // 1/15/23
                            }
                        @endphp
                        {{ $dateFormat }}
                    </span>
                </p>
                {{-- @if ($email->has_attachments && isset($email->attachments) && $email->attachments->count() > 0)
                    <div class="email-attachments mt-1">
                        @foreach ($email->attachments->take(3) as $attachment)
                            <span class="attachment-preview d-inline-flex align-items-center me-2">
                                <i
                                    class="attachment-icon me-1
                                        @switch(pathinfo($attachment->filename, PATHINFO_EXTENSION))
                                            @case('pdf')
                                                ri-file-pdf-line text-danger
                                                @break
                                            @case('doc')
                                            @case('docx')
                                                ri-file-word-line text-primary
                                                @break
                                            @case('xls')
                                            @case('xlsx')
                                                ri-file-excel-line text-success
                                                @break
                                            @case('ppt')
                                            @case('pptx')
                                                ri-file-ppt-line text-warning
                                                @break
                                            @case('jpg')
                                            @case('jpeg')
                                            @case('png')
                                            @case('gif')
                                                ri-image-line text-info
                                                @break
                                            @case('zip')
                                            @case('rar')
                                            @case('7z')
                                                ri-file-zip-line text-secondary
                                                @break
                                            @default
                                                ri-attachment-2 text-muted
                                        @endswitch
                                    "></i>
                                <span class="attachment-name">{{ Str::limit($attachment->filename, 15) }}</span>
                            </span>
                        @endforeach
                        @if ($email->attachments->count() > 3)
                            <span class="attachment-more text-muted">
                                +{{ $email->attachments->count() - 3 }} more
                            </span>
                        @endif
                    </div>
                @endif --}}
            </div>

            <div class="email-body">
                <p class="mail-msg mb-0">
                    <span class="d-block mb-0 {{ !$email->is_read ? 'fw-semibold' : '' }} text-truncate email-subject">
                        {{ $email->subject ?: '(No Subject)' }}
                    </span>
                    <span class="fs-11 text-muted text-wrap text-truncate email-preview">
                        @php
                            $preview = $email->body_preview ?? $email->body_text ?? '';
                            $preview = html_entity_decode(strip_tags((string) $preview));
                            echo \Illuminate\Support\Str::limit(trim($preview), 120);
                        @endphp

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
