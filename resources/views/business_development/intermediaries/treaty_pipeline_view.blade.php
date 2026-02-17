@extends('layouts.app')

@section('content')
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Treaty Sales Management</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/">Business Development</a></li>
                        <li class="breadcrumb-item"><a href="/">Treaty Sales Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Facultative</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">Pipeline Details</div>
            </div>
            <div class="card-body">
                <!-- Pipeline Year Selection -->
                <div class="mb-4">
                    <form id="pip_year_form" action="{{ route('pipeline.view') }}" method="get">
                        <input type="hidden" id="opp_id" name="opp_id">
                        <div class="row">
                            <div class="col-md-3">
                                <x-SearchableSelect id="pip_year_select" req="" inputLabel="Pipeline Year"
                                    name="pipeline" placeholder="--Select Year--">
                                    @foreach ($pipelines as $year)
                                        <option value="{{ $year->id }}"
                                            {{ old('lead_year', $pip ?? '') == $year->id ? 'selected' : '' }}>
                                            {{ $year->year }}
                                        </option>
                                    @endforeach
                                </x-SearchableSelect>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Chart Container -->
                <div class="d-flex justify-content-center flex-wrap chart-container">
                    <div id="pipeline-chart" class="ct-chart-ranking ct-golden-section ct-series-a"></div>
                    <div id="chart-loading" class="d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading chart...</span>
                        </div>
                    </div>
                    <div id="chart-error" class="d-none text-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Failed to load chart data</p>
                    </div>
                </div>

                <!-- Chart Legend -->
                <div class="row">
                    <hr>
                    <div class="d-flex justify-content-center flex-wrap">
                        @php
                            $stages = [
                                ['color' => '#453d3f', 'label' => 'Lead'],
                                ['color' => '#f05b4f', 'label' => 'Proposal'],
                                ['color' => '#f4c63d', 'label' => 'Negotiation'],
                                ['color' => '#d17905', 'label' => 'Won'],
                                ['color' => '#d70206', 'label' => 'Lost'],
                                ['color' => '#59922b', 'label' => 'Final Stage'],
                            ];
                        @endphp
                        @foreach ($stages as $stage)
                            <div class="d-flex align-items-center me-3 mb-2">
                                <span class="dot rounded-circle me-2"
                                    style="background-color: {{ $stage['color'] }}; width: 12px; height: 12px;"></span>
                                <span class="fw-normal small">{{ $stage['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-pills nav-style-3 mb-4 pb-1" role="tablist">
                    @php
                        $quarters = [
                            ['id' => 'general_details', 'label' => 'All Quarters', 'active' => true],
                            ['id' => 'q1_details', 'label' => 'Quarter One'],
                            ['id' => 'q2_details', 'label' => 'Quarter Two'],
                            ['id' => 'q3_details', 'label' => 'Quarter Three'],
                            ['id' => 'q4_details', 'label' => 'Quarter Four'],
                        ];
                    @endphp
                    @foreach ($quarters as $quarter)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $quarter['active'] ?? false ? 'active' : '' }}" data-bs-toggle="tab"
                                role="tab" aria-current="page" href="#{{ $quarter['id'] }}"
                                aria-selected="{{ $quarter['active'] ?? false ? 'true' : 'false' }}"
                                {{ !($quarter['active'] ?? false) ? 'tabindex="-1"' : '' }}>
                                {{ $quarter['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-0 mt-1 border-none">
                    @foreach ($quarters as $index => $quarter)
                        <div class="tab-pane {{ $quarter['active'] ?? false ? 'active' : '' }} border-none"
                            id="{{ $quarter['id'] }}">
                            <div class="row">
                                <div class="table-responsive">
                                    @php
                                        $tableIds = [
                                            'general_details' => 'all_opps',
                                            'q1_details' => 'q1_opps',
                                            'q2_details' => 'q2_opps',
                                            'q3_details' => 'q3_opps',
                                            'q4_details' => 'q4_opps',
                                        ];
                                    @endphp
                                    <table id="{{ $tableIds[$quarter['id']] }}" class="table table-striped pipeline-table"
                                        style="width:100%" data-quarter="{{ $index === 0 ? 'all' : $index }}">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Insured Name</th>
                                                <th>Division</th>
                                                <th>Business Class</th>
                                                <th>Status</th>
                                                <th>Currency</th>
                                                <th>Sum Insured</th>
                                                <th>Premium</th>
                                                <th>Effective Date</th>
                                                <th>Closing Date</th>
                                                <th>Category</th>
                                                <th>Approval Status</th>
                                                <th>Stage Actions</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="error-container" class="alert alert-danger d-none" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="error-message"></span>
        </div>

        <div id="loading-overlay" class="d-none">
            <div class="overlay-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 mb-0">Processing request...</p>
            </div>
        </div>

        {{-- Email Modal: For sending BD notifications to reinsurers and contacts --}}
        @include('business_development.intermediaries.partials.modals.fac_email_modal')

        {{-- Lead Modal: Initial stage - capture basic opportunity information --}}
        @include('business_development.intermediaries.partials.modals.lead_modal')

        {{-- Proposal Modal: Second stage - select reinsurers and prepare proposals --}}
        @include('business_development.intermediaries.partials.modals.proposal_modal')

        {{-- Negotiation Modal: Third stage - negotiate terms with selected reinsurers --}}
        @include('business_development.intermediaries.partials.modals.negotiation_modal')

        {{-- Final Stage Modal: Complete the deal and prepare for handover --}}
        @include('business_development.intermediaries.partials.modals.final_stage_modal')
    </div>

    <script>
        window.pipelineRoutes = {
            pipelineData: "{{ route('pipeline.sales.get_pipeline_data') }}",
            chartData: "{{ route('pipeline.sales.get_pipeline_chart_data') }}",
            scheduleHeaders: "{{ route('schedule.headers.get') }}",
            slipDocuments: "{{ route('schedule.get_stage_documents') }}",
            getBdTerms: "{{ route('get.bd_terms') }}",
            declineReinsurer: "{{ route('reinsurer.decline') }}",
            getSelectedReinsurers: "{{ route('get.selected_bd_reinsurers') }}",
            revert: "{{ route('prospect.revert') }}",
            addPipeline: "{!! route('prospect.add.pipeline') !!}"
        };
    </script>

    {{-- <script type="module" src="{{ asset('js/pipeline-manager.js') }}"></script> --}}
    {{-- <script type="module" src="js/pipeline-manager.js"></script> --}}
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pipeline-view.css') }}">
@endsection
