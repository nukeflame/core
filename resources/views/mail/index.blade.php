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
        @if (!($isOutlookConnected ?? false))
            @include('mail.partials.outlook-setup')
        @endif


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
    <script></script>
@endpush
