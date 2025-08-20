<div class="sidebar">
    <div class="account-header">
        {{ $account->email_address ?? 'No Account Connected' }}
    </div>
    <div class="folder-list">
        @if (isset($folders) && $folders->count() > 0)
            @foreach ($folders as $folder)
                <a href="{{ route('mail.folder', $folder) }}"
                    class="folder-item {{ $currentFolder && $currentFolder->id === $folder->id ? 'selected' : '' }}">
                    <span class="folder-icon">{{ $folder->icon }}</span>
                    {{ $folder->display_name }}
                    @if ($folder->unread_count > 0)
                        <span class="unread-count">{{ $folder->unread_count }}</span>
                    @endif
                </a>
            @endforeach
        @else
            <div class="folder-item">
                <span class="folder-icon">📥</span>
                No folders available
            </div>
        @endif
    </div>
</div>
