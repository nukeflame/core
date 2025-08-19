message-preview.blade.php
?>
<div class="message-preview-pane">
    @if ($message)
        <div class="message-header">
            <div class="message-subject-line">
                @if ($message->has_attachments)
                    📎
                @endif
                @if ($message->importance === 'high')
                    ❗
                @endif
                {{ $message->subject ?: '(No Subject)' }}
            </div>
            <div class="message-meta">
                <strong>From:</strong> {{ $message->sender_name }} &lt;{{ $message->sender_email }}&gt;<br>
                <strong>To:</strong> {{ $message->emailAccount->email_address }}<br>
                <strong>Date:</strong> {{ $message->received_at->format('M j, Y g:i A') }}<br>
                @if ($message->received_at != $message->sent_at && $message->sent_at)
                    <strong>Sent:</strong> {{ $message->sent_at->format('M j, Y g:i A') }}<br>
                @endif
                <strong>Subject:</strong> {{ $message->subject ?: '(No Subject)' }}
            </div>
        </div>
        <div class="message-content">
            @if ($message->body_content)
                @if ($message->body_content_type === 'html')
                    {!! $message->body_content !!}
                @else
                    {!! nl2br(e($message->body_content)) !!}
                @endif
            @else
                <p style="color: #666; font-style: italic;">No content available</p>
            @endif

            @if ($message->attachments->count() > 0)
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <strong>Attachments:</strong>
                    @foreach ($message->attachments as $attachment)
                        <div style="margin: 8px 0; padding: 8px; background: #f9f9f9; border-radius: 4px;">
                            <span>📎</span>
                            @if ($attachment->is_downloaded)
                                <a href="{{ route('mail.download-attachment', [$message, $attachment]) }}"
                                    style="color: #0066cc;">
                                    {{ $attachment->name }}
                                </a>
                            @else
                                <span style="color: #666;">{{ $attachment->name }}</span>
                                <small>({{ $attachment->formatted_size }})</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="message-header">
            <div class="message-subject-line">Select a message to view</div>
        </div>
        <div class="message-content">
            <p style="color: #666; text-align: center; margin-top: 50px;">
                Choose a message from the list to view its contents here.
            </p>
        </div>
    @endif
</div>
