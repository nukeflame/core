@extends('layouts.app', [
    'pageTitle' => 'Mail App- ' . $company->company_name,
])

@section('content')
    <div class="main-mail-container p-2 gap-2 d-flex">
        {{-- Mail Navigation  --}}
        @include('admin.email.includes._mail_navigation')

        @if (request()->routeIs('admin.email'))
            @include('admin.email.includes.inbox')
        @endif

        @if (request()->routeIs('admin.folder'))
            @switch(request()->route('folder'))
                @case('inbox')
                    @include('admin.email.includes.inbox')
                @break

                @case('sent')
                    @include('admin.email.includes.sent')
                @break

                @case('drafts')
                    @include('admin.email.includes.drafts')
                @break

                @case('spam')
                    @include('admin.email.includes.spam')
                @break

                @case('important')
                    @include('admin.email.includes.important')
                @break

                @case('trash')
                    @include('admin.email.includes.trash')
                @break

                @case('archive')
                    @include('admin.email.includes.archive')
                @break

                @case('starred')
                    @include('admin.email.includes.starred')
                @break

                @default
                    @include('admin.email.includes.inbox')
            @endswitch
        @endif
    </div>
@endsection

<x-outlook-connection :auto-show="true" :show-cancel-button="true" :fetch-emails-on-connect="true" :show-toast-message="false" :enable-loading-spinner="true" />
{{-- @include('admin.email.includes._connection_script') --}}

@push('script')
    <script>
        $(document).ready(function() {
            $("#outlookCancelBtn").on("click", function() {
                window.location.href = '/'
            })
        })
    </script>
@endpush
