@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">
            {{ $transactionTitles[$trans_type] ?? 'Cover Registration' }}
        </h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.info') }}">{{ $customer->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $transactionTitles[$trans_type] ?? '' }}
                    </li>
                    @if ($trans_type !== 'NEW')
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $old_endt_trans->endorsement_no }}
                        </li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>

    <div class="cover-wrapper form-group">
        <form id="register_cover"
            action="{{ $trans_type === 'EDIT' ? route('cover.editCoverRegister') : route('cover.register') }}"
            method="POST">
            @csrf

            {{-- Hidden Fields --}}
            @include('cover.partials.hidden-fields')

            {{-- Basic Information Section --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    @include('cover.partials.basic-info')
                </div>
            </div>

            {{-- Payment Information Section --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    @include('cover.partials.payment-info')
                </div>
            </div>

            {{-- Installment Section --}}
            <div class="card mb-3" id="installment_section" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Installment Plans</h5>
                </div>
                <div class="card-body">
                    @include('cover.partials.installments')
                </div>
            </div>

            {{-- FAC Section --}}
            <div class="card mb-3" id="fac_section" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Facultative Details</h5>
                </div>
                <div class="card-body">
                    @include('cover.partials.fac-section')
                </div>
            </div>

            {{-- Treaty Section --}}
            <div class="card mb-3" id="treaty_section" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Treaty Details</h5>
                </div>
                <div class="card-body">
                    @include('cover.partials.treaty-section')
                </div>
            </div>

            {{-- Cover Period Section --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cover Period</h5>
                </div>
                <div class="card-body">
                    @include('cover.partials.cover-period')
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row mb-3">
                <div class="col-12">
                    {{-- @include('cover.partials.action-buttons') --}}
                </div>
            </div>
        </form>
    </div>

    {{-- Modals --}}
    {{-- @include('cover.modals.add-insured') --}}
    {{-- @include('partials.page-loader') --}}
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cover-registration.css') }}">
@endpush

@push('script')
    <script>
        window.coverConfig = {
            trans_type: '{{ $trans_type }}',
            prospectId: '{{ $prospectId ?? '' }}',
            routes: {
                getTreatyTypes: '{{ route('cover.get_treatyperbustype') }}',
                getClasses: '{{ route('get_class') }}',
                getBinderCovers: '{{ route('get_binder_covers') }}',
                getTodaysRate: '{{ route('get_todays_rate') }}',
                getReinPremType: '{{ route('cover.get_reinprem_type') }}',
                // getProspectData: 'route('cover.prospect-data', ':id')',
            },
            oldData: @json($old_endt_trans ?? null)
        };
    </script>
    {{-- <script src="{{ asset('js/cover-registration.js') }}"></script> --}}
@endpush
