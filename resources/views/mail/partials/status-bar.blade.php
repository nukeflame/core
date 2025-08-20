<div class="status-bar">
    <div>
        @if ($messages->count() > 0)
            {{ $messages->count() }} messages,
            {{ $messages->where('is_read', false)->count() }} unread
        @else
            0 messages, 0 unread
        @endif
    </div>
    <div>
        @if (isset($account) && $account->last_sync_at)
            Last sync: {{ $account->last_sync_at->diffForHumans() }}
        @else
            Not connected
        @endif
    </div>
</div>
