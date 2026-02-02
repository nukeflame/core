@extends('layouts.app', [
    'pageTitle' => 'Mail App - ' . (auth()->user()->company->company_name ?? 'Dashboard'),
])

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/mail-app.css') }}">
    <style>
        .sync-progress-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 350px;
            z-index: 9999;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 20px;
            display: none;
        }

        .sync-progress-container.active {
            display: block;
        }

        .progress {
            height: 25px;
            margin: 10px 0;
        }

        .progress-bar {
            transition: width 0.3s ease;
        }

        .sync-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 12px;
        }

        .sync-stats span {
            padding: 2px 8px;
            border-radius: 4px;
            background: #f0f0f0;
        }

        .sync-stats .inserted {
            background: #d4edda;
            color: #155724;
        }

        .sync-stats .updated {
            background: #d1ecf1;
            color: #0c5460;
        }

        .sync-stats .deleted {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
@endsection

@section('content')
    <div class="mail-wrapper main-mail-container p-2 gap-2 d-flex" id="mailApp">
        @if (!auth()->user()->hasOutlookConnection())
            @include('mail.partials.outlook-setup')
        @endif

        @include('mail.partials.navigation')
        @include('mail.partials.email-list')
        @include('mail.partials.email-content')
        @include('mail.partials.recipients')
        @include('mail.partials.modals.compose')
    </div>

    <!-- Sync Progress Widget -->
    <div class="loading-overlay d-none">
        <div class="overlay-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 mb-0 loader-txt">Syncing Emails...</p>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/mail-app.js') }}"></script>

    <script>
        @if (isset($email) && !empty($email))
            window.emailData = @json($email)
        @else
            window.emailData = null;
        @endif


        const mailApp = new MailApp({
            routes: {
                getEmail: '{{ route('mail.show', ':id') }}',
                sendEmail: '{{ route('mail.send') }}',
                outlookConnect: '{{ route('mail.outlook.connect') }}',
                outlookSync: '{{ route('mail.outlook.sync') }}'
            },
            csrf: '{{ csrf_token() }}',
            user: @json(auth()->user()->only(['name', 'email']))
        });

        $(document).ready(function() {
            const userId = {{ auth()->id() }};

            let syncStartTime = null;
            const $btnLoader = $(".loading-overlay");

            function updateSyncProgress(data, eventType) {
                switch (eventType) {
                    case 'progress':
                        $btnLoader.removeClass("d-none");
                        break;

                    case 'completed':
                        $btnLoader.addClass("d-none");
                        setTimeout(() => {
                            if (typeof mailApp !== 'undefined') {
                                mailApp.refreshEmailList();
                            } else {
                                window.location.reload();
                            }
                        }, 1000);
                        break;

                    case 'failed':
                        $btnLoader.addClass("d-none");
                        break;

                    default:
                }
            }

            function initializeEmailSync() {
                if (typeof window.Echo !== 'undefined' && window.Echo) {
                    try {
                        const channel = window.Echo.private(`email-sync.${userId}`);

                        channel.listen('.sync.progress', (data) => {
                            updateSyncProgress(data, 'progress');
                        });

                        channel.listen('.sync.completed', (data) => {
                            updateSyncProgress(data, 'completed');
                        });

                        channel.listen('.sync.failed', (data) => {
                            updateSyncProgress(data, 'failed');
                        });

                        // Listen for new email events
                        channel.listen('.email.new', (data) => {
                            handleNewEmail(data);
                        });

                    } catch (error) {
                        console.error('Error subscribing to email sync channel:', error);
                    }
                } else {
                    setTimeout(initializeEmailSync, 100);
                }
            }

            setTimeout(initializeEmailSync, 500);

            function handleNewEmail(data) {
                console.log('New email received:', data);

                // Show notification
                if (typeof mailApp !== 'undefined' && mailApp.showNotification) {
                    mailApp.showNotification({
                        title: 'New Email',
                        message: `From: ${data.email.from_name}\nSubject: ${data.email.subject}`,
                        type: 'info'
                    });
                }

                // Play notification sound (optional)
                try {
                    const audio = new Audio('/assets/sounds/notification.mp3');
                    audio.volume = 0.5;
                    audio.play().catch(e => console.log('Audio play failed:', e));
                } catch (e) {
                    console.log('Audio notification failed:', e);
                }

                // Update unread count in navigation
                updateUnreadCount();

                // If on current folder, optionally refresh the list
                const currentFolder = '{{ $folder }}';
                if (currentFolder === data.email.folder || currentFolder === 'all') {
                    // Show a refresh banner instead of auto-reloading
                    showRefreshBanner();
                }
            }

            function showRefreshBanner() {
                const banner = $(
                    '<div class="alert alert-info alert-dismissible fade show" role="alert" style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 9998; min-width: 300px;">' +
                    '<i class="bi bi-info-circle me-2"></i>New emails available. <a href="#" class="alert-link" onclick="window.location.reload(); return false;">Refresh to view</a>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');

                $('body').append(banner);

                setTimeout(() => {
                    banner.fadeOut(() => banner.remove());
                }, 10000);
            }

            function updateUnreadCount() {
                // Update the unread count via AJAX
                //{{-- fetch('{{ route('mail.check-new') }}') --}}
                //     .then(response => response.json())
                //     .then(data => {
                //         if (data.success && data.unreadEmails > 0) {
                //             // Update badge in navigation
                //             const $inboxBadge = $('.mail-folder-link[data-folder="inbox"]').find('.badge');
                //             if ($inboxBadge.length) {
                //                 $inboxBadge.text(data.unreadEmails);
                //             }
                //         }
                //     })
                //     .catch(error => console.error('Failed to update unread count:', error));
            }

            // Trigger automatic sync every 5 minutes
            setInterval(() => {
                fetch('{{ route('mail.sync.trigger') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Background sync triggered');
                        }
                    })
                    .catch(error => console.log('Background sync failed:', error));
            }, 300000); // 5 minutes
        });
    </script>
@endpush
