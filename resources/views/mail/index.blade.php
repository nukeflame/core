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
    <div class="sync-progress-container" id="syncProgressWidget">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Email Sync Progress</h6>
            <button type="button" class="btn-close btn-sm"
                onclick="document.getElementById('syncProgressWidget').classList.remove('active')"></button>
        </div>
        <p class="text-muted small mb-2" id="syncStatus">Syncing emails...</p>
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" id="syncProgressBar"
                style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                0%
            </div>
        </div>
        <div class="mt-2 text-center small text-muted">
            <span id="processedCount">0</span> / <span id="totalCount">0</span> emails
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/mail-app.js') }}"></script>

    <script>
        @if (isset($email) && !empty($email))
            window.emailData = @json($email);
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
            let syncWidget = $('#syncProgressWidget');
            let progressBar = $('#syncProgressBar');

            function updateSyncProgress(data, eventType) {
                switch (eventType) {
                    case 'progress':
                        syncWidget.addClass('active');

                        if (!syncStartTime) {
                            syncStartTime = new Date();
                        }

                        const displayPercentage = Math.max(10, data.percentage);

                        progressBar.css('width', displayPercentage + '%');
                        progressBar.text(displayPercentage + '%');
                        progressBar.attr('aria-valuenow', displayPercentage);

                        $('#syncStatus').text(`${data.status} - Batch #${data.batch_number}`);

                        $('#processedCount').text(data.processed);
                        $('#totalCount').text(data.total);

                        progressBar.removeClass('bg-success bg-warning bg-danger bg-info');
                        if (data.percentage === 100) {
                            progressBar.addClass('bg-success');
                            progressBar.removeClass('progress-bar-animated');
                        } else if (data.percentage > 50) {
                            progressBar.addClass('bg-info');
                        } else {
                            progressBar.addClass('bg-primary');
                        }
                        break;

                    case 'completed':
                        progressBar.css('width', '100%');
                        progressBar.text('100%');
                        progressBar.attr('aria-valuenow', 100);
                        progressBar.removeClass('progress-bar-animated bg-primary bg-info');
                        progressBar.addClass('bg-success');

                        $('#syncStatus').text('Sync Completed Successfully!');

                        $('#insertedCount').text(data.total_inserted);
                        $('#updatedCount').text(data.total_updated);
                        $('#deletedCount').text(data.total_deleted);
                        $('#processedCount').text(data.total_processed);
                        $('#totalCount').text(data.total_processed);

                        let duration = '';
                        if (syncStartTime) {
                            const endTime = new Date();
                            const durationMs = endTime - syncStartTime;
                            const seconds = Math.floor(durationMs / 1000);
                            duration = seconds > 0 ? ` in ${seconds}s` : '';
                        }

                        setTimeout(() => {
                            syncWidget.removeClass('active');
                            syncStartTime = null;

                            progressBar.css('width', '0%');
                            progressBar.text('0%');
                            progressBar.attr('aria-valuenow', 0);
                            progressBar.addClass('progress-bar-animated');

                            window.location.reload();

                        }, 2000);

                        if (typeof mailApp.refreshEmailList === 'function') {
                            setTimeout(() => {
                                mailApp.refreshEmailList();
                            }, 1000);
                        }
                        break;

                    case 'failed':
                        progressBar.removeClass('bg-primary bg-info bg-success progress-bar-animated');
                        progressBar.addClass('bg-danger');

                        $('#syncStatus').html(`<span class="text-danger">Sync Failed</span>`);

                        setTimeout(() => {
                            syncWidget.removeClass('active');
                            syncStartTime = null;

                            progressBar.css('width', '0%');
                            progressBar.text('0%');
                            progressBar.attr('aria-valuenow', 0);
                            progressBar.removeClass('bg-danger');
                            progressBar.addClass('bg-primary progress-bar-animated');
                        }, 3000);
                        break;

                    default:
                        console.warn('Unknown sync event type:', eventType, data);
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

                    } catch (error) {
                        console.error('❌ Error subscribing to email sync channel:', error);
                    }
                } else {
                    setTimeout(initializeEmailSync, 100);
                }
            }
            setTimeout(initializeEmailSync, 500);
        });
    </script>
@endpush
