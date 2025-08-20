<div class="message-list">
    <div class="message-list-header">
        <span>{{ $currentFolder->display_name ?? 'Messages' }}</span>
        <span>{{ $messages->count() }} messages</span>
    </div>

    @forelse($messages as $message)
        <div class="message-item {{ !$message->is_read ? 'unread' : '' }} {{ $selectedMessage && $selectedMessage->id === $message->id ? 'selected' : '' }}"
            data-message-id="{{ $message->id }}">
            <div class="message-date">{{ $message->formatted_received_at }}</div>
            <div class="message-sender">{{ $message->sender_name }}</div>
            <div class="message-subject">
                @if ($message->has_attachments)
                    📎
                @endif
                @if ($message->importance === 'high')
                    ❗
                @endif
                {{ Str::limit($message->subject ?: '(No Subject)', 40) }}
            </div>
            <div class="message-preview">
                {{ Str::limit($message->body_preview ?: 'No preview available', 80) }}
            </div>
        </div>
    @empty
        <div class="message-item">
            <div class="message-subject">No messages in this folder</div>
            <div class="message-preview">
                @if (isset($currentFolder))
                    This folder is empty. Messages will appear here when you receive them.
                @else
                    Connect an Outlook account to start viewing your emails.
                @endif
            </div>
        </div>
    @endforelse
</div>
