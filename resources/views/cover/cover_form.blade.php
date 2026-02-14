@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">
                {{ $transactionTitles[$trans_type] ?? 'Cover Registration' }}
            </h1>
            @if ($trans_type === 'NEW')
                <p class="text-muted mb-0 mt-1 fs-13">Create a new insurance cover for {{ $customer->name }}</p>
            @elseif ($trans_type === 'EDIT')
                <p class="text-muted mb-0 mt-1 fs-13">Editing endorsement {{ $old_endt_trans->endorsement_no }}</p>
            @endif
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}">
                            <i class="ri-home-4-line me-1"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.info') }}">{{ $customer->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $transactionTitles[$trans_type] ?? 'New Cover' }}
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

    @if ($trans_type === 'NEW')
        <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
            <i class="ri-information-line fs-18 me-2"></i>
            <div>
                <strong>New Cover Registration</strong> - Complete all required sections to register a new insurance cover.
            </div>
        </div>
    @endif

    <div class="new-cover-wrapper">
        <form id="register_cover"
            action="{{ $trans_type === 'EDIT' ? route('cover.editCoverRegister') : route('cover.register') }}"
            method="POST">
            @csrf

            <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer->customer_id }}">
            <input type="hidden" name="trans_type" id="trans_type" value="{{ $trans_type }}">
            @if (!empty($is_business_type_locked))
                <input type="hidden" name="type_of_bus" id="type_of_bus_locked"
                    value="{{ $selected_type_of_bus }}">
            @endif

            @if ($trans_type !== 'NEW')
                <input type="hidden" name="cover_no" id="cover_no" value="{{ $old_endt_trans->cover_no }}">
                <input type="hidden" name="endorsement_no" id="endorsement_no"
                    value="{{ $old_endt_trans->endorsement_no }}">
            @endif

            <input type="hidden" id="installment_total_amount" value="0">
            <input type="hidden" id="vat_charged" name="vat_charged" value="0">
            <input type="hidden" name="risk_details" id="hidden_risk_details">

            <div class="card mb-3">
                <div class="card-header d-flex align-items-center">
                    <i class="ri-file-text-line me-2 text-primary"></i>
                    <h5 class="card-title mb-0 fs-15 flex-grow-1">Basic Information</h5>
                    <span class="badge bg-primary-transparent">Required</span>
                </div>
                <div class="card-body">
                    @include('cover.partials.basic-info')
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex align-items-center">
                    <i class="ri-money-dollar-circle-line me-2 text-success"></i>
                    <h5 class="card-title mb-0 fs-15 flex-grow-1">Payment Information</h5>
                    <span class="badge bg-success-transparent">Required</span>
                </div>
                <div class="card-body">
                    @include('cover.partials.payment-info')
                </div>
            </div>

            <div class="card mb-3" id="installment_section" style="display: none;">
                <div class="card-header d-flex align-items-center">
                    <i class="ri-calendar-line me-2 text-warning"></i>
                    <h5 class="card-title mb-0 fs-15 flex-grow-1">Installment Plans</h5>
                    <span class="badge bg-warning-transparent">Optional</span>
                </div>
                <div class="card-body">
                    @include('cover.partials.installments')
                </div>
            </div>

            <div class="card mb-3" id="fac_section" style="display: none;">
                <div class="card-header d-flex align-items-center">
                    <i class="ri-shield-check-line me-2 text-info"></i>
                    <h5 class="card-title mb-0 fs-15 flex-grow-1">Facultative Details</h5>
                    <span class="badge bg-info-transparent">Conditional</span>
                </div>
                <div class="card-body">
                    @include('cover.partials.fac-section')
                </div>
            </div>

            <div class="card mb-3" id="treaty_section" style="display: none;">
                <div class="card-header d-flex align-items-center">
                    <i class="ri-file-shield-line me-2 text-purple"></i>
                    <h5 class="card-title mb-0 fs-15 flex-grow-1">Treaty Details</h5>
                    <span class="badge bg-purple-transparent">Conditional</span>
                </div>
                <div class="card-body">
                    @include('cover.partials.treaty-section')
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex align-items-center">
                    <i class="ri-calendar-check-line me-2 text-danger"></i>
                    <h5 class="card-title mb-0 fs-15 flex-grow-1">Cover Period</h5>
                    <span class="badge bg-danger-transparent">Required</span>
                </div>
                <div class="card-body">
                    @include('cover.partials.cover-period')
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="ri-information-line me-1"></i>
                                All fields marked with <span class="text-danger">*</span> are required
                            </small>
                            <div class="d-flex gap-2">
                                <a href="{{ route('customer.info') }}" class="btn btn-light">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-check-line me-1"></i>
                                    {{ $trans_type === 'EDIT' ? 'Update Cover' : 'Register Cover' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Modals --}}
    @include('cover.modals.add-insured')
    @include('cover.partials.page-loader')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cover-registration.css') }}">
    <style>
        .page-header-breadcrumb {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem !important;
            /* color: white; */
        }

        .page-header-breadcrumb .page-title {
            /* color: white; */
        }

        .page-header-breadcrumb .text-muted {
            /* color: rgba(255, 255, 255, 0.8) !important; */
        }

        .page-header-breadcrumb .breadcrumb {
            background: transparent;
            margin-bottom: 0;
        }

        .page-header-breadcrumb .breadcrumb-item a {
            /* color: rgba(255, 255, 255, 0.9); */
            text-decoration: none;
        }

        .page-header-breadcrumb .breadcrumb-item.active {
            /* color: white; */
        }

        .page-header-breadcrumb .breadcrumb-item+.breadcrumb-item::before {
            /* color: rgba(255, 255, 255, 0.7); */
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .badge {
            font-size: 11px;
            padding: 4px 8px;
        }
    </style>
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
                getProspectData: '{{ route('pipeline.get_prospect_data', ':id') }}'
            },
            oldData: @json($old_endt_trans ?? null)
        };
    </script>

    <script src="{{ asset('js/cover-registration.js') }}"></script>
@endpush
