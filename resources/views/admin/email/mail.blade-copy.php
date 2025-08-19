@extends('layouts.app', [
'pageTitle' => 'Mail App- ' . $company->company_name,
])

@section('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #f5f5f5;
        color: #333;
    }

    /* Menu Bar */
    .menu-bar {
        background: linear-gradient(to bottom, #e8e8e8, #d0d0d0);
        border-bottom: 1px solid #bbb;
        padding: 4px 8px;
        font-size: 13px;
        display: flex;
        gap: 20px;
        margin-left: -20px;
        margin-right: -20px;
    }
    }

    .menu-item {
        padding: 4px 8px;
        cursor: pointer;
        border-radius: 3px;
    }

    .menu-item:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }

    /* Toolbar */
    .toolbar {
        background: linear-gradient(to bottom, #f8f8f8, #e8e8e8);
        border-bottom: 1px solid #ccc;
        padding: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .toolbar-button {
        background: linear-gradient(to bottom, #ffffff, #f0f0f0);
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 6px 12px;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        color: #333;
    }

    .toolbar-button:hover {
        background: linear-gradient(to bottom, #f0f0f0, #e0e0e0);
    }

    .toolbar-button:active {
        background: linear-gradient(to bottom, #e0e0e0, #d0d0d0);
    }

    .toolbar-separator {
        width: 1px;
        height: 24px;
        background-color: #ccc;
        margin: 0 4px;
    }

    /* Search Box */
    .search-box {
        margin-left: auto;
        display: flex;
        align-items: center;
    }

    .search-input {
        border: 1px solid #ccc;
        border-radius: 15px;
        padding: 4px 12px;
        width: 200px;
        font-size: 13px;
    }

    /* Main Layout */
    .main-container {
        display: flex;
        height: calc(100vh - 80px);
    }

    /* Sidebar */
    .sidebar {
        width: 200px;
        background-color: #f9f9f9;
        border-right: 1px solid #ddd;
        overflow-y: auto;
    }

    .account-header {
        background: linear-gradient(to bottom, #e8e8e8, #d8d8d8);
        padding: 8px 12px;
        font-weight: bold;
        border-bottom: 1px solid #ccc;
        font-size: 13px;
    }

    .folder-list {
        padding: 4px 0;
    }

    .folder-item {
        padding: 4px 12px;
        cursor: pointer;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: #333;
    }

    .folder-item:hover {
        background-color: #e8e8e8;
        text-decoration: none;
        color: #333;
    }

    .folder-item.selected {
        background-color: #316ac5;
        color: white;
    }

    .folder-icon {
        width: 16px;
        height: 16px;
        opacity: 0.7;
    }

    .unread-count {
        margin-left: auto;
        background-color: #666;
        color: white;
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 11px;
        min-width: 16px;
        text-align: center;
    }

    .folder-item.selected .unread-count {
        background-color: rgba(255, 255, 255, 0.3);
    }

    /* Message List */
    .message-list {
        width: 350px;
        border-right: 1px solid #ddd;
        background-color: white;
        overflow-y: auto;
    }

    .message-list-header {
        background: linear-gradient(to bottom, #f0f0f0, #e0e0e0);
        border-bottom: 1px solid #ccc;
        padding: 8px 12px;
        font-weight: bold;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .message-item {
        border-bottom: 1px solid #eee;
        padding: 8px 12px;
        cursor: pointer;
        transition: background-color 0.1s;
    }

    .message-item:hover {
        background-color: #f5f5f5;
    }

    .message-item.selected {
        background-color: #316ac5;
        color: white;
    }

    .message-item.unread {
        font-weight: bold;
        background-color: #fafafa;
    }

    .message-item.unread:hover {
        background-color: #f0f0f0;
    }

    .message-sender {
        font-size: 13px;
        margin-bottom: 2px;
    }

    .message-subject {
        font-size: 13px;
        margin-bottom: 2px;
        color: #333;
    }

    .message-item.selected .message-subject {
        color: white;
    }

    .message-preview {
        font-size: 12px;
        color: #666;
        line-height: 1.3;
    }

    .message-item.selected .message-preview {
        color: rgba(255, 255, 255, 0.8);
    }

    .message-date {
        font-size: 11px;
        color: #888;
        float: right;
    }

    .message-item.selected .message-date {
        color: rgba(255, 255, 255, 0.8);
    }

    /* Message Preview */
    .message-preview-pane {
        flex: 1;
        background-color: white;
        display: flex;
        flex-direction: column;
    }

    .message-header {
        background: linear-gradient(to bottom, #f8f8f8, #f0f0f0);
        border-bottom: 1px solid #ddd;
        padding: 12px;
    }

    .message-subject-line {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #333;
    }

    .message-meta {
        font-size: 12px;
        color: #666;
        line-height: 1.4;
    }

    .message-content {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        font-size: 14px;
        line-height: 1.5;
    }

    /* Status Bar */
    .status-bar {
        background: linear-gradient(to bottom, #e8e8e8, #d0d0d0);
        border-top: 1px solid #ccc;
        padding: 4px 12px;
        font-size: 12px;
        color: #666;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Alerts */
    .alert {
        padding: 12px;
        margin: 12px;
        border-radius: 4px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    /* Connect page styles */
    .connect-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f5f5f5;
    }

    .connect-card {
        background: white;
        border-radius: 8px;
        padding: 40px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 400px;
    }

    .connect-title {
        font-size: 24px;
        margin-bottom: 16px;
        color: #333;
    }

    .connect-description {
        color: #666;
        margin-bottom: 24px;
        line-height: 1.5;
    }

    .connect-button {
        background: linear-gradient(to bottom, #0078d4, #106ebe);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .connect-button:hover {
        background: linear-gradient(to bottom, #106ebe, #005a9e);
        text-decoration: none;
        color: white;
    }

    /* Loading states */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ccc;
        border-top: 2px solid #333;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
@endsection

@section('content')
@include('admin.email.partials.menu-bar')
@include('admin.email.partials.toolbar')

<div class="main-container">
    <!-- Sidebar -->
    @include('mail.partials.sidebar', [
    'folders' => $folders,
    'currentFolder' => $folder ?? ($inboxFolder ?? null),
    ])

    {{-- <!-- Message List -->
        @include('mail.partials.message-list', [
            'messages' => $messages,
            'currentFolder' => $folder ?? ($inboxFolder ?? null),
            'selectedMessage' => $selectedMessage ?? null,
        ])

        <!-- Message Preview -->
        @include('mail.partials.message-preview', [
            'message' => $selectedMessage ?? null,
        ]) --}}
</div>

{{-- @include('mail.partials.status-bar', [
        'messages' => $messages ?? collect(),
        'account' => $account ?? null,
    ])  --}}
@endsection

{{-- @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle message selection
            const messages = document.querySelectorAll('.message-item[data-message-id]');
            messages.forEach(message => {
                message.addEventListener('click', function(e) {
                    e.preventDefault();

                    const messageId = this.dataset.messageId;

                    // Update selection state
                    messages.forEach(m => m.classList.remove('selected'));
                    this.classList.add('selected');

                    // Mark as read if unread
                    if (this.classList.contains('unread')) {
                        this.classList.remove('unread');
                        markMessageAsRead(messageId);
                    }

                    // Load message content
                    window.location.href = `/message/${messageId}`;
                });
            });

            // Handle toolbar buttons
            const toolbarButtons = document.querySelectorAll('.toolbar-button');
            toolbarButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Visual feedback
                    const originalStyle = this.style.background;
                    this.style.background = 'linear-gradient(to bottom, #d0d0d0, #c0c0c0)';
                    setTimeout(() => {
                        this.style.background = originalStyle;
                    }, 150);
                });
            });

            function markMessageAsRead(messageId) {
                if (!token) return;

                fetch(`/message/${messageId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Failed to mark message as read');
                        }
                    })
                    .catch(error => {
                        console.error('Error marking message as read:', error);
                    });
            }
        });
    </script>
@endpush --}}