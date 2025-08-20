<div class="toolbar">
    <button class="toolbar-button" type="button" id="sync-btn">📧 Get Mail</button>
    {{-- <a href="{{ route('mail.compose') }}" class="toolbar-button">✏️ Write</a> --}}
    <div class="toolbar-separator"></div>
    <button class="toolbar-button" type="button" id="reply-btn">↩️ Reply</button>
    <button class="toolbar-button" type="button" id="reply-all-btn">↪️ Reply All</button>
    <button class="toolbar-button" type="button" id="forward-btn">➡️ Forward</button>
    <div class="toolbar-separator"></div>
    <button class="toolbar-button" type="button" id="delete-btn">🗑️ Delete</button>
    <button class="toolbar-button" type="button" id="archive-btn">📂 Archive</button>
    <button class="toolbar-button" type="button" id="junk-btn">⚠️ Junk</button>
    <div class="toolbar-separator"></div>
    <button class="toolbar-button" type="button" id="tag-btn">🏷️ Tag</button>

    @if (isset($account))
        <div class="toolbar-separator"></div>
        <a href="{{ route('outlook.settings', $account) }}" class="toolbar-button">⚙️ Settings</a>
    @endif

    {{-- <div class="search-box">
        <form method="GET" action="{{ route('mail.index') }}">
            <input type="text" name="search" class="search-input" placeholder="Search messages..."
                value="{{ request('search') }}">
        </form>
    </div> --}}
</div>

{{-- @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sync button
            const syncBtn = document.getElementById('sync-btn');
            if (syncBtn) {
                syncBtn.addEventListener('click', function() {
                    @if (isset($account))
                        this.innerHTML = '<span class="spinner"></span> Syncing...';
                        this.disabled = true;

                        fetch('{{ route('outlook.sync', $account) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                },
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    setTimeout(() => window.location.reload(), 2000);
                                } else {
                                    alert('Sync failed: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Sync error:', error);
                                alert('Sync failed');
                            })
                            .finally(() => {
                                this.innerHTML = '📧 Get Mail';
                                this.disabled = false;
                            });
                    @else
                        alert('No email account connected');
                    @endif
                });
            }
        });
    </script>
@endpush --}}
