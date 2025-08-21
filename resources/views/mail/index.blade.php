@extends('layouts.app', [
    'pageTitle' => 'Mail App - ' . (auth()->user()->company->company_name ?? 'Dashboard'),
])

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/mail-app.css') }}">
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
    </script>
@endpush
