@section('content')
    @include('mail.partials.menu-bar')

    <div class="toolbar">
        <button type="button" class="toolbar-button" id="send-btn">📧 Send</button>
        <button type="button" class="toolbar-button" id="save-draft-btn">💾 Save Draft</button>
        <div class="toolbar-separator"></div>
        <button type="button" class="toolbar-button" id="attach-btn">📎 Attach</button>
        <button type="button" class="toolbar-button" id="priority-btn">❗ Priority</button>
        <div class="toolbar-separator"></div>
        <button type="button" class="toolbar-button" id="format-btn">🎨 Format</button>
        <div class="toolbar-separator"></div>
        <a href="{{ route('mail.index') }}" class="toolbar-button">← Back to Inbox</a>
    </div>

    <div style="padding: 20px; background: white; height: calc(100vh - 80px); overflow-y: auto;">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form id="compose-form">
            @csrf

            <!-- Recipients -->
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">To:</label>
                <div class="recipient-container">
                    <input type="email" name="to[]" required class="recipient-input"
                        style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"
                        placeholder="Enter email addresses..." value="{{ $replyTo ?? '' }}">
                </div>
                <button type="button" id="add-to-btn" class="add-recipient-btn">+ Add another recipient</button>
            </div>

            <div style="margin-bottom: 15px;" id="cc-section" {{ empty($ccRecipients) ? 'style=display:none;' : '' }}>
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">CC:</label>
                <div class="recipient-container">
                    <input type="email" name="cc[]" class="recipient-input"
                        style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"
                        placeholder="CC recipients..." value="{{ implode(', ', $ccRecipients ?? []) }}">
                </div>
                <button type="button" id="add-cc-btn" class="add-recipient-btn">+ Add another CC</button>
            </div>

            <div style="margin-bottom: 15px;" id="bcc-section" style="display: none;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">BCC:</label>
                <div class="recipient-container">
                    <input type="email" name="bcc[]" class="recipient-input"
                        style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"
                        placeholder="BCC recipients...">
                </div>
                <button type="button" id="add-bcc-btn" class="add-recipient-btn">+ Add another BCC</button>
            </div>

            <!-- Show CC/BCC toggle -->
            <div style="margin-bottom: 15px;">
                <button type="button" id="show-cc-btn" class="toolbar-button" style="font-size: 12px; padding: 4px 8px;"
                    {{ !empty($ccRecipients) ? 'style=display:none;' : '' }}>
                    + CC
                </button>
                <button type="button" id="show-bcc-btn" class="toolbar-button"
                    style="font-size: 12px; padding: 4px 8px; margin-left: 8px;">
                    + BCC
                </button>
            </div>

            <!-- Subject -->
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Subject:</label>
                <input type="text" name="subject" required
                    style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"
                    value="{{ $subject ?? '' }}" maxlength="998">
            </div>

            <!-- Priority -->
            <input type="hidden" name="priority" value="normal" id="priority-value">

            <!-- Body Type -->
            <div style="margin-bottom: 15px;">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="radio" name="body_type" value="text" checked> Plain Text
                </label>
                <label style="display: flex; align-items: center; gap: 8px; margin-left: 20px;">
                    <input type="radio" name="body_type" value="html"> HTML
                </label>
            </div>

            <!-- Message Body -->
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Message:</label>
                <textarea name="body" required rows="20"
                    style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: 'Courier New', monospace; resize: vertical;"
                    placeholder="Type your message here...">{{ $body ?? '' }}</textarea>
            </div>

            <!-- Original Message (for replies/forwards) -->
            @if (isset($originalMessage))
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
                    <div style="color: #666; font-size: 14px; margin-bottom: 10px;">
                        <strong>{{ $messageType === 'forward' ? 'Forwarded Message:' : 'Original Message:' }}</strong>
                    </div>
                    <div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #0066cc; font-size: 13px;">
                        <div style="margin-bottom: 8px;">
                            <strong>From:</strong> {{ $originalMessage->sender_name }}
                            &lt;{{ $originalMessage->sender_email }}&gt;
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong>Sent:</strong>
                            {{ $originalMessage->sent_at ? $originalMessage->sent_at->format('l, F j, Y \a\t g:i A') : $originalMessage->received_at->format('l, F j, Y \a\t g:i A') }}
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong>To:</strong> {{ $originalMessage->emailAccount->email_address }}
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong>Subject:</strong> {{ $originalMessage->subject }}
                        </div>
                        <hr style="margin: 12px 0; border: none; border-top: 1px solid #ddd;">
                        <div style="max-height: 300px; overflow-y: auto;">
                            @if ($originalMessage->body_content_type === 'html')
                                {!! $originalMessage->body_content !!}
                            @else
                                {!! nl2br(e($originalMessage->body_content)) !!}
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </div>

    <!-- Hidden file input for attachments -->
    <input type="file" id="attachment-input" multiple style="display: none;" accept="*/*">

    <!-- Priority dropdown -->
    <div id="priority-dropdown" class="dropdown-menu" style="display: none;">
        <ul>
            <li><a href="#" data-priority="low">🔵 Low Priority</a></li>
            <li><a href="#" data-priority="normal">⚪ Normal Priority</a></li>
            <li><a href="#" data-priority="high">🔴 High Priority</a></li>
        </ul>
    </div>
@endsection
