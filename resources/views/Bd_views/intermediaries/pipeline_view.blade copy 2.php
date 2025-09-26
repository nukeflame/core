@extends('layouts.app')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Sales Management</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/">Business Development</a></li>
                    <li class="breadcrumb-item"><a href="/">Sales Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Facultative</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Pipeline Chart Card -->
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
            <div class="d-flex justify-content-center flex-wrap" style="height:300px;">
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

    <!-- Data Tables Card -->
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
            <p class="mt-2">Processing request...</p>
        </div>
    </div>

    @include('Bd_views.intermediaries.partials.modals.proposal_modal')
    {{-- @includeIf('Bd_views.intermediaries.partials.modals.negotiation_modal')
        @includeIf('Bd_views.intermediaries.partials.modals.lead_modal')
        @includeIf('Bd_views.intermediaries.partials.modals.won_modal')
        @includeIf('Bd_views.intermediaries.partials.modals.lost_modal')
        @includeIf('Bd_views.intermediaries.partials.modals.final_modal')
        @includeIf('Bd_views.intermediaries.partials.modals.update_category_modal') --}}
</div>
@endsection

@section('styles')
<style>
    .border-none {
        border: none !important;
    }

    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        display: inline-block;
        min-width: 70px;
        text-align: center;
    }

    .status-proposal {
        background-color: #d70206;
        color: white;
    }

    .status-negotiation {
        background-color: #f05b4f;
        color: white;
    }

    .status-lead {
        background-color: #f4c63d;
        color: #000;
    }

    .status-won {
        background-color: #d17905;
        color: white;
    }

    .status-lost {
        background-color: #453d3f;
        color: white;
    }

    .status-final {
        background-color: #59922b;
        color: white;
    }

    .currency {
        font-weight: 600;
        color: #27ae60 !important;
    }

    .status-badge.quotation {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }

    .status-badge.facultative.offer {
        background-color: #cce7ff;
        color: #0056b3;
    }

    .stage-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .stage-btn {
        padding: 5px 15px;
        border: none;
        border-radius: 5px;
        font-size: inherit;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        text-transform: capitalize;
        letter-spacing: 0.5px;
        min-width: 140px;
        position: relative;
        overflow: hidden;
    }

    .stage-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .stage-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-lead {
        background: #2196f3;
        color: white;
    }

    .btn-proposal {
        background: #ff9800;
        color: white;
    }

    .btn-negotiation {
        background: #9c27b0;
        color: white;
    }

    .btn-won {
        background: #4caf50;
        color: white;
    }

    .btn-lost {
        background: #f44336;
        color: white;
    }

    .btn-final {
        background: #8bc34a;
        color: white;
    }

    .capitalize {
        text-transform: capitalize;
    }

    .pl-1 {
        padding-left: .3em !important;
    }

    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .overlay-content {
        background: white;
        padding: 2rem;
        border-radius: 0.5rem;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .pipeline-table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .pipeline-table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .ct-chart-ranking {
        min-height: 300px;
    }

    #chart-loading,
    #chart-error {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 300px;
        width: 100%;
    }

    @media (max-width: 768px) {
        .stage-btn {
            min-width: 120px;
            padding: 4px 12px;
        }

        .status-badge {
            min-width: 60px;
            font-size: 0.7rem;
        }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .slide-in {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-10px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
@endsection

@push('script')
<script>
    class PipelineManager {
        constructor() {
            this.chartInstance = null;
            this.currentDealId = null;
            this.currentStage = "lead";
            this.escapeKeyHandler = null;
            this.dataTables = new Map();

            this.config = {
                routes: {
                    pipelineData: "{{ route('pipeline.sales.get_pipeline_data') }}",
                    chartData: "{{ route('pipeline.sales.get_pipeline_chart_data') }}",
                    scheduleHeaders: "{{ route('schedule.headers.get') }}",
                    slipDocuments: "{{ route('schedule.get_stage_documents') }}"
                },
                stageFlow: {
                    lead: {
                        next: "proposal",
                        button: "Move to Proposal",
                        class: "btn-proposal",
                        altNext: "lost",
                        modalId: "leadModal",
                    },
                    proposal: {
                        next: "negotiation",
                        button: "Move to Negotiation",
                        class: "btn-negotiation",
                        altNext: "lost",
                        modalId: "proposalModal",
                    },
                    negotiation: {
                        next: "won",
                        button: "Mark as Won",
                        class: "btn-won",
                        altNext: "lost",
                        modalId: "negotiationModal",
                    },
                    won: {
                        next: "final",
                        button: "Move to Final",
                        class: "btn-final",
                        modalId: "wonModal",
                    },
                    lost: {
                        next: null,
                        button: "Deal Closed",
                        class: "btn-lost",
                        modalId: "lostModal",
                    },
                    final: {
                        next: null,
                        button: "Deal Complete",
                        class: "btn-final",
                        modalId: "finalModal",
                    },
                },
                columnConfig: [{
                        data: 'id',
                        name: 'id',
                        title: 'ID'
                    },
                    {
                        data: 'insured_name',
                        name: 'insured_name',
                        title: 'Insured Name'
                    },
                    {
                        data: 'division',
                        name: 'division',
                        title: 'Division'
                    },
                    {
                        data: 'business_class',
                        name: 'business_class',
                        title: 'Business Class'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        title: 'Status'
                    },
                    {
                        data: 'currency',
                        name: 'currency',
                        title: 'Currency',
                        defaultContent: 'KES'
                    },
                    {
                        data: 'sum_insured',
                        name: 'sum_insured',
                        title: 'Sum Insured'
                    },
                    {
                        data: 'premium',
                        name: 'premium',
                        title: 'Premium'
                    },
                    {
                        data: 'effective_date',
                        name: 'effective_date',
                        title: 'Effective Date'
                    },
                    {
                        data: 'closing_date',
                        name: 'closing_date',
                        title: 'Closing Date'
                    },
                    {
                        data: 'category',
                        name: 'category',
                        title: 'Category'
                    },
                    {
                        data: 'approval_status',
                        name: 'approval_status',
                        title: 'Approval Status',
                        orderable: false
                    },
                    {
                        data: 'stage_actions',
                        name: 'stage_actions',
                        title: 'Stage Actions'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            };

            this.init();
        }

        init() {
            try {
                this.setupCSRF();
                this.setupErrorHandling();
                this.initializeChart();
                this.initializeDataTables();
                this.bindEvents();
            } catch (error) {
                this.handleError('Initialization failed', error);
            }
        }

        setupCSRF() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        setupErrorHandling() {
            window.onerror = (message, source, lineno, colno, error) => {
                this.handleError('JavaScript Error', {
                    message,
                    source,
                    lineno,
                    error
                });
            };
        }

        initializeChart() {
            try {
                if (typeof Chartist === 'undefined') {
                    throw new Error('Chartist library not loaded');
                }

                if ($('.ct-chart-ranking').length === 0) {
                    throw new Error('Chart container not found');
                }

                this.showChartLoading();

                this.chartInstance = new Chartist.Bar('.ct-chart-ranking', {
                    labels: ['Quarter One', 'Quarter Two', 'Quarter Three', 'Quarter Four'],
                    series: [
                        [0, 0, 0, 0]
                    ]
                }, {
                    low: 0,
                    showArea: true,
                    height: '300px',
                    plugins: [
                        Chartist.plugins.tooltip()
                    ],
                    axisX: {
                        position: 'end'
                    },
                    axisY: {
                        showGrid: false,
                        showLabel: false,
                        offset: 0
                    }
                });

                this.chartInstance.on('draw', (data) => {
                    if (data.type === 'bar') {
                        data.element.animate({
                            y2: {
                                dur: 1000,
                                from: data.y1,
                                to: data.y2,
                                easing: Chartist.Svg.Easing.easeOutQuint
                            }
                        });
                    }
                });

                this.loadChartData();
                this.hideChartLoading();

            } catch (error) {
                this.handleError('Chart initialization failed', error);
                this.showChartError();
            }
        }

        showChartLoading() {
            $('#chart-loading').removeClass('d-none');
            $('#chart-error').addClass('d-none');
        }

        hideChartLoading() {
            $('#chart-loading').addClass('d-none');
        }

        showChartError() {
            $('#chart-loading').addClass('d-none');
            $('#chart-error').removeClass('d-none');
        }

        loadChartData() {
            const pipelineId = $('#pip_year_select').val();

            $.ajax({
                url: this.config.routes.chartData,
                method: 'GET',
                data: {
                    pipeline_id: pipelineId
                },
                timeout: 10000,
                success: (response) => {
                    if (response && response.data && Array.isArray(response.data)) {
                        this.updateChartData(response.data);
                    } else {
                        this.updateChartData([0, 0, 0, 0]);
                    }
                    this.hideChartLoading();
                },
                error: (xhr, status, error) => {
                    this.handleError('Failed to load chart data', {
                        xhr,
                        status,
                        error
                    });
                    this.updateChartData([0, 0, 0, 0]);
                    this.showChartError();
                }
            });
        }

        updateChartData(data) {
            if (this.chartInstance) {
                this.chartInstance.update({
                    labels: ['Quarter One', 'Quarter Two', 'Quarter Three', 'Quarter Four'],
                    series: [data]
                });
            }
        }

        initializeDataTables() {
            const tables = $('.pipeline-table');

            tables.each((index, table) => {
                const $table = $(table);
                const tableId = $table.attr('id');
                const quarter = $table.data('quarter');

                try {
                    const dataTable = $table.DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: this.config.routes.pipelineData,
                            data: (d) => {
                                d.pipeline_id = $('#pip_year_select').val();
                                d.quarter = quarter;
                            },
                            error: (xhr, error, code) => {
                                this.handleError(`DataTable AJAX error for ${tableId}`, {
                                    status: xhr.status,
                                    responseText: xhr.responseText,
                                    error: error
                                });
                            }
                        },
                        columns: this.config.columnConfig,
                        order: [
                            [0, 'desc']
                        ],
                        pageLength: 25,
                        responsive: true,
                        language: {
                            processing: this.getLoadingHTML(),
                            emptyTable: "No pipeline records found",
                            info: "Showing _START_ to _END_ of _TOTAL_ records",
                            infoEmpty: "No pipeline available",
                            infoFiltered: "(filtered from _MAX_ total records)",
                            lengthMenu: "Show _MENU_ records per page",
                            search: "Search pipeline:",
                            paginate: {
                                first: "First",
                                last: "Last",
                                next: "Next",
                                previous: "Previous"
                            }
                        },
                        drawCallback: () => {
                            this.initializeActionHandlers();
                            $table.addClass('fade-in');
                        }
                    });

                    this.dataTables.set(tableId, dataTable);
                } catch (error) {
                    this.handleError(`Error initializing DataTable for ${tableId}`, error);
                }
            });
        }

        getLoadingHTML() {
            return `
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;
        }

        bindEvents() {
            $('#pip_year_select').on('change', () => {
                this.debounce(() => {
                    this.loadChartData();
                    this.reloadAllTables();
                }, 300)();
            });

            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', (e) => {
                const target = $(e.target).attr("href");
                const tableId = this.getTableIdFromTab(target);

                if (tableId && this.dataTables.has(tableId)) {
                    this.dataTables.get(tableId).columns.adjust().draw();
                }
            });

            $(document).ajaxError((event, xhr, settings, thrownError) => {
                this.handleError('AJAX Error', {
                    url: settings.url,
                    status: xhr.status,
                    error: thrownError
                });
            });

            let uploadedFiles = {};
        }

        initializeActionHandlers() {
            $('.stage_btn_action').off('click').on('click', (e) => {
                e.preventDefault();
                this.handleStageAction(e.currentTarget);
            });

            $('.update_category_action').off('click').on('click', (e) => {
                e.preventDefault();
                this.handleCategoryUpdate(e.currentTarget);
            });
        }

        handleStageAction(button) {
            try {
                this.showLoading();

                const buttonData = $(button).data();
                this.currentDealId = buttonData.deal_id;

                const $row = $(button).closest("tr");
                const $table = $row.closest("table");
                const tableId = $table.attr("id");
                const dataTable = this.dataTables.get(tableId);
                const rowData = dataTable.row($row).data();
                const _original = rowData._original;

                const dealInfo = {
                    id: _original.opportunity_id,
                    created_at: _original.created_at,
                    insured_name: _original.insured_name,
                    insured_email: _original.insured_email,
                    insured_phone: _original.insured_phone,
                    contact_name: _original.contact_name,
                    total_sum_insured: _original.total_sum_insured,
                    premium: _original.premium,
                    brokerage_rate: _original.brokerage_rate,
                    written_share: _original.written_share,
                    type_of_business: buttonData.type_of_business,
                    class: _original.class,
                    class_group: _original.class_group,
                    category_type: _original.category_type,
                };

                window.currentDealInfo = dealInfo;
                const dealCurrentStage = buttonData.current_stage || rowData.status;
                this.currentStage = dealCurrentStage;

                const stageInfo = this.config.stageFlow[this.currentStage];
                if (!stageInfo) {
                    throw new Error(`Invalid stage: ${this.currentStage}`);
                }

                const nextStage = stageInfo.next;
                if (nextStage) {
                    this.openStageModal(nextStage, this.currentDealId, dealInfo);
                }

                this.hideLoading();
            } catch (error) {
                this.handleError("Error handling stage action", error);
                this.hideLoading();
            }
        }

        handleCategoryUpdate(button) {
            const buttonData = $(button).data();
            $("#updateCategoryForm #opportunity_id").val(buttonData.opportunity_id);
            $('#updateCategoryTypeModal').modal('show');
        }

        openStageModal(stage, dealId, dealInfo = null) {
            try {
                this.currentDealId = dealId;
                const modalId = stage + "Modal";
                const $modal = $(`#${modalId}`);

                if ($modal.length === 0) {
                    throw new Error(`Modal not found: ${modalId}`);
                }

                const data = {
                    dealId: dealId,
                    typeOfBus: dealInfo.type_of_business,
                    modalId: modalId,
                    class: dealInfo.class,
                    classGroup: dealInfo.class_group,
                    stage: stage,
                    category_type: dealInfo.category_type
                };

                this.loadScheduleHeaders(data);
                this.loadSlipDocuments(data)
                this.populateModalData(modalId, dealId, dealInfo);

                $modal.modal('show');
                $modal.addClass('slide-in');

                this.addEscapeKeyListener();
            } catch (error) {
                this.handleError("Error opening modal", error);
            }
        }

        populateModalData(modalId, dealId, dealInfo = null) {
            try {
                const $modal = $(`#${modalId}`);

                if (dealInfo) {
                    $modal.find('.slip-display').text(dealInfo.id || '');

                    if (dealInfo.created_at) {
                        const dateObj = new Date(dealInfo.created_at);
                        const options = {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        };
                        const formattedDate = dateObj.toLocaleDateString('en-US', options);
                        $modal.find('.created_at-display').text(formattedDate);
                    } else {
                        $modal.find('.created_at-display').text('');
                    }

                    let $slipTitle = '';
                    if (Number(dealInfo.category_type) === 1) {
                        $slipTitle = 'Quotation Slip'
                    } else {
                        $slipTitle = 'Facultative Slip'
                    }

                    $modal.find('.slip-title').text($slipTitle);
                    $modal.find('.insured-name-display').text(dealInfo.insured_name || '');

                    $modal.find('.insured-email-display').text(dealInfo.insured_email || '--');
                    $modal.find('.insured-phone-display').text(dealInfo.insured_phone || '--');
                    $modal.find('.insured-contact-name-display').text(dealInfo.contact_name || '--');
                    $modal.find('.total_sum_insured').val(dealInfo.total_sum_insured || '0.00');
                    $modal.find('.premium').val(dealInfo.premium || '0.00');
                    $modal.find('.brokerage_rate').val(dealInfo.brokerage_rate || '0.00');

                    $modal.find('#totalReinsurerShare').val(dealInfo.written_share || '0.00');
                    $modal.find('#classCodeValue').val(dealInfo.class);
                    $modal.find('#classGroupCodeValue').val(dealInfo.class_group);
                }
            } catch (error) {
                this.handleError("Error populating modal data", error);
            }
        }

        loadScheduleHeaders(data) {
            $.ajax({
                url: this.config.routes.scheduleHeaders,
                method: 'POST',
                data: {
                    opportunity_id: data.dealId,
                    class: data.class,
                    class_group: data.classGroup,
                    business_type: data.typeOfBus,
                },
                success: (response) => {
                    if (response.success && response.headers) {
                        this.renderScheduleHeaders(response.headers, data);
                    }
                },
                error: (xhr, status, error) => {
                    this.handleError('Error loading schedule headers', {
                        xhr,
                        status,
                        error
                    });
                    this.showError('Failed to load schedule headers');
                }
            });
        }

        loadSlipDocuments(data) {
            const $modal = $(`#${data.modalId}`);
            const $documentsSubtitle = $modal.find('#documentsSubtitle');
            $documentsSubtitle.html(
                '<small><span class="loading-spinner"></span> Loading documents...</small>');

            $.ajax({
                url: this.config.routes.slipDocuments,
                method: 'POST',
                data: {
                    opportunity_id: data.dealId,
                    class: data.class,
                    class_group: data.classGroup,
                    business_type: data.typeOfBus,
                    stage: data.stage,
                    category_type: data.category_type,
                },
                success: (response) => {
                    if (response.status) {
                        this.renderSlipDocuments(response, data, $modal);
                        $documentsSubtitle.html(`<small>Documents for ${response.class_name}</small>`);
                    } else {
                        $documentsSubtitle.html(`<small>No documents found</small>`);
                    }
                },
                error: (xhr, status, error) => {
                    this.handleError('Error loading slip documents', {
                        xhr,
                        status,
                        error
                    });
                    this.showError('Failed to load slip documents');
                    $documentsSubtitle.html(`<small>Error loading documents</small>`);
                }
            });
        }

        renderScheduleHeaders(headers, data) {
            const $modal = $(`#${data.modalId}`);
            const container = $modal.find('#termsConditions');
            container.empty();

            if (!headers || headers.length === 0) {
                container.html('<p class="text-muted text-center my-4">No schedule headers configured.</p>');
                return;
            }

            let fieldsHtml = '<div class="row">';

            headers.forEach((header, index) => {
                const fieldId = `schedule_${header.id}`;
                const colClass = 'col-md-6';
                const headerName = this.capitalize(header.name);

                fieldsHtml += `<div class="${colClass}">`;
                fieldsHtml += `<div class="form-group mb-3">`;
                fieldsHtml += `<label for="${fieldId}" class="form-label capitalize">${headerName}`;

                if (header.amount_field === 'Y') {
                    fieldsHtml += ' <span class="text-danger pl-1">*</span>';
                }

                fieldsHtml += `</label>`;

                fieldsHtml += this.generateFieldInput(header, fieldId);

                fieldsHtml += `<div class="invalid-feedback"></div>`;

                fieldsHtml += `</div></div>`;

                if (index % 2 === 1) {
                    fieldsHtml += '</div><div class="row">';
                }
            });

            fieldsHtml += '</div>';
            container.html(fieldsHtml);

            this.setupFieldValidation($modal);
        }

        renderSlipDocuments(res, data, $modal) {
            if (!res.docs.length > 0) return;

            const fallbackConfig = {
                name: "Sample Insurance Class",
                documents: [{
                        id: 'policy_schedule',
                        name: 'Policy Schedule',
                        required: true,
                        icon: 'bx-file-blank',
                        accepts: '.pdf,.doc,.docx',
                        description: 'Current policy terms and coverage details',
                        max_size: 10485760 // 10MB in bytes
                    },
                    {
                        id: 'claims_history',
                        name: 'Claims History',
                        required: true,
                        icon: 'bx-history',
                        accepts: '.pdf,.xls,.xlsx',
                        description: '5-year claims experience report',
                        max_size: 10485760
                    },
                    {
                        id: 'additional_docs',
                        name: 'Additional Documents',
                        required: false,
                        icon: 'bx-folder-plus',
                        accepts: '.pdf,.doc,.docx,.jpg,.jpeg,.png',
                        description: 'Any additional supporting documents',
                        max_size: 5242880, // 5MB in bytes
                        multiple: true
                    }
                ]
            };
            // documentConfigs[classId] = response.data;
            this.generateDocumentFields(fallbackConfig, $modal);
        }

        generateDocumentFields(config, $modal) {
            const $container = $('#documentFields');
            const $placeholder = $('#documentPlaceholder');
            const $summarySection = $('#documentSummarySection');

            $placeholder.hide();
            $container.empty().show();

            if (!config.documents || config.documents.length === 0) {
                $container.html(`
                        <div class="col-12 text-center py-4">
                            <i class="bx bx-info-circle bx-2x text-muted mb-2"></i>
                            <p class="text-muted">No specific documents required for this insurance class.</p>
                        </div>
                    `);
                $summarySection.hide();
                return;
            }

            config.documents.forEach((doc, index) => {
                const colSize = config.documents.length <= 2 ? 'col-12' : 'col-md-6';
                const maxSizeText = this.formatFileSize(doc.max_size || 10485760);
                const acceptsText = doc.accepts.replace(/\./g, '').toUpperCase();

                const fieldHtml = `
                        <div class="${colSize} fade-in" style="animation-delay: ${index * 0.1}s">
                            <div class="document-field-group">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">
                                        <i class="bx ${doc.icon} me-2"></i>
                                        ${doc.name}
                                        ${doc.required ? '<span class="required-indicator">*</span>' : ''}
                                    </label>
                                    <div class="file-upload-area" data-field="${doc.id}">
                                        <i class="bx ${doc.icon} upload-icon"></i>
                                        <div class="upload-text">${doc.name}</div>
                                        <div class="upload-subtext">${doc.description}</div>
                                        <input type="file" class="d-none file-input"
                                            name="${doc.id}"
                                            ${doc.required ? 'required' : ''}
                                            accept="${doc.accepts}"
                                            ${doc.multiple ? 'multiple' : ''}
                                            data-max-size="${doc.max_size || 10485760}">
                                        <div class="upload-constraints">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Max size: ${maxSizeText} | Formats: ${acceptsText}
                                        </div>
                                        <div class="file-count-badge">0</div>
                                    </div>
                                    <div class="file-preview-container"></div>
                                </div>
                            </div>
                        </div>
                    `;
                $container.append(fieldHtml);
            });

            $('#docCount').text(config.documents.length);

            this.initializeFileUploads();

            $summarySection.show();
            this.updateDocumentSummary(config);
        }

        initializeFileUploads() {
            $('.file-upload-area').each(function() {
                const $uploadArea = $(this);
                // const $input = $uploadArea.find('.file-input');
                // const $previewContainer = $uploadArea.parent().find('.file-preview-container');


                console.log($uploadArea)
                // // Remove existing event handlers to prevent duplicates
                // $uploadArea.off('click.fileUpload dragover.fileUpload dragleave.fileUpload drop.fileUpload');
                // $input.off('change.fileUpload');

                // // Click to upload
                // $uploadArea.on('click.fileUpload', function(e) {
                //     if (!$(e.target).hasClass('file-action-btn')) {
                //         $input.click();
                //     }
                // });

                // // File selection
                // $input.on('change.fileUpload', function(e) {
                //     handleFileSelection(e.target.files, $uploadArea, $previewContainer);
                // });

                // // Drag and drop
                // $uploadArea.on('dragover.fileUpload dragenter.fileUpload', function(e) {
                //     e.preventDefault();
                //     $(this).addClass('drag-over');
                // });

                // $uploadArea.on('dragleave.fileUpload', function(e) {
                //     e.preventDefault();
                //     $(this).removeClass('drag-over');
                // });

                // $uploadArea.on('drop.fileUpload', function(e) {
                //     e.preventDefault();
                //     $(this).removeClass('drag-over');
                //     handleFileSelection(e.originalEvent.dataTransfer.files, $uploadArea,
                //         $previewContainer);
                // });
            });
        }


        updateDocumentSummary(config) {
            // if (!currentClass || !documentConfigs[currentClass]) {
            //     $('#documentSummarySection').hide();
            //     return;
            // }

            // const config = documentConfigs[currentClass];
            const totalFiles = Object.values(uploadedFiles).reduce((sum, files) => sum + files.length, 0);
            const requiredDocs = config.documents.filter(doc => doc.required);
            const uploadedRequiredDocs = requiredDocs.filter(doc =>
                uploadedFiles[doc.id] && uploadedFiles[doc.id].length > 0
            );

            console.log(config)


            // const summaryHtml = `
            //     <div class="row g-3">
            //         <div class="col-md-3">
            //             <div class="text-center">
            //                 <div class="h4 text-primary mb-1">${totalFiles}</div>
            //                 <div class="small text-muted">Total Files</div>
            //             </div>
            //         </div>
            //         <div class="col-md-3">
            //             <div class="text-center">
            //                 <div class="h4 text-success mb-1">${uploadedRequiredDocs.length}/${requiredDocs.length}</div>
            //                 <div class="small text-muted">Required Documents</div>
            //             </div>
            //         </div>
            //         <div class="col-md-3">
            //             <div class="text-center">
            //                 <div class="h4 text-info mb-1">${getTotalFileSize()}</div>
            //                 <div class="small text-muted">Total Size</div>
            //             </div>
            //         </div>
            //         <div class="col-md-3">
            //             <div class="text-center">
            //                 <div class="h4 ${uploadedRequiredDocs.length === requiredDocs.length ? 'text-success' : 'text-warning'} mb-1">
            //                     ${uploadedRequiredDocs.length === requiredDocs.length ? 'Complete' : 'Incomplete'}
            //                 </div>
            //                 <div class="small text-muted">Status</div>
            //             </div>
            //         </div>
            //     </div>
            //     <div class="mt-3">
            //         <h6 class="mb-2">Document Status:</h6>
            //         <div class="row g-2">
            //             ${renderDocumentStatus(config.documents)}
            //         </div>
            //     </div>
            // `;

            // $('#uploadSummary').html(summaryHtml);
            // $('#documentSummarySection').show();
        }

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        getTotalFileSize() {
            let totalSize = 0;
            Object.values(uploadedFiles).forEach(files => {
                files.forEach(file => {
                    totalSize += file.size;
                });
            });
            return formatFileSize(totalSize);
        }


        renderDocumentStatus(documents) {
            config.documents.map(doc => {
                const uploaded = uploadedFiles[doc.id] && uploadedFiles[doc.id].length > 0;
                const statusClass = uploaded ? 'success' : (doc.required ? 'danger' : 'secondary');
                const statusIcon = uploaded ? 'bx-check' : (doc.required ? 'bx-x' : 'bx-minus');
                return `<div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bx ${statusIcon} text-${statusClass} me-2"></i>
                                        <span class="small">${doc.name} ${doc.required ? '*' : ''}</span>
                                        ${uploaded ? `<span class="badge bg-${statusClass} ms-auto">${uploadedFiles[doc.id].length}</span>` : ''}
                                    </div>
                                </div>
                            `;
            }).join('')
        }


        generateFieldInput(header, fieldId) {
            const baseInputClass = 'form-inputs';
            const required = header.amount_field === 'Y' ? 'required' : '';
            const placeholder = `Enter ${header.name.toLowerCase()}`;

            if (header.data_determinant === 'Sum Insured' ||
                header.data_determinant === 'Premium' ||
                header.name.toLowerCase().includes('amount')) {

                const currency = header.class_group === 'FIRE' ? 'KES' : 'USD';
                return `
                        <div class="input-group">
                            <span class="input-group-text">${currency}</span>
                            <input type="number" class="${baseInputClass}" id="${fieldId}"
                                   name="schedule_headers[${header.id}]" step="0.01" min="0"
                                   placeholder="${placeholder}" ${required}>
                        </div>`;
            } else if (header.name.toLowerCase().includes('date')) {
                return `<input type="date" class="${baseInputClass}" id="${fieldId}"
                                   name="schedule_headers[${header.id}]" ${required}>`;
            } else if (header.name.toLowerCase().includes('percentage') ||
                header.name.toLowerCase().includes('rate')) {
                return `
                        <div class="input-group">
                            <input type="number" class="${baseInputClass}" id="${fieldId}"
                                   name="schedule_headers[${header.id}]" step="0.01" min="0" max="100"
                                   placeholder="${placeholder}" ${required}>
                            <span class="input-group-text">%</span>
                        </div>`;
            } else if (header.type_of_sum_insured && header.type_of_sum_insured !== 'N/A') {
                let options = `<option value="">Select ${header.name}</option>`;
                if (header.type_of_sum_insured === 'TOTAL SUM INSURED') {
                    options += `
                            <option value="total_sum_insured">Total Sum Insured</option>
                            <option value="individual_sum_insured">Individual Sum Insured</option>`;
                }
                return `<select class="form-select ${baseInputClass}" id="${fieldId}"
                                    name="schedule_headers[${header.id}]" ${required}>${options}</select>`;
            } else {
                return `<input type="text" class="${baseInputClass}" id="${fieldId}"
                                   name="schedule_headers[${header.id}]" placeholder="${placeholder}" ${required}>`;
            }
        }

        setupFieldValidation($modal) {
            $modal.find('input[required], select[required]').on('blur change', function() {
                const $field = $(this);
                const value = $field.val();

                if (!value || value.trim() === '') {
                    $field.addClass('is-invalid');
                    $field.next('.invalid-feedback').text('This field is required.');
                } else {
                    $field.removeClass('is-invalid');
                    $field.next('.invalid-feedback').text('');
                }
            });
        }

        validateScheduleForm(modalId) {
            const $modal = $(`#${modalId}`);
            const requiredFields = $modal.find('input[required], select[required]');
            let isValid = true;

            requiredFields.each(function() {
                const $field = $(this);
                const value = $field.val();

                if (!value || value.trim() === '') {
                    $field.addClass('is-invalid');
                    $field.next('.invalid-feedback').text('This field is required.');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                    $field.next('.invalid-feedback').text('');
                }
            });

            return isValid;
        }

        addEscapeKeyListener() {
            if (this.escapeKeyHandler) return;

            this.escapeKeyHandler = (event) => {
                if (event.key === "Escape") {
                    const openModal = document.querySelector('.modal.show');
                    if (openModal) {
                        $(openModal).modal('hide');
                    }
                }
            };

            document.addEventListener("keydown", this.escapeKeyHandler);
        }

        removeEscapeKeyListener() {
            if (this.escapeKeyHandler) {
                document.removeEventListener("keydown", this.escapeKeyHandler);
                this.escapeKeyHandler = null;
            }
        }

        reloadAllTables() {
            this.dataTables.forEach((dataTable, tableId) => {
                dataTable.ajax.reload(null, false);
            });
        }

        getTableIdFromTab(tabId) {
            const mapping = {
                '#general_details': 'all_opps',
                '#q1_details': 'q1_opps',
                '#q2_details': 'q2_opps',
                '#q3_details': 'q3_opps',
                '#q4_details': 'q4_opps'
            };
            return mapping[tabId];
        }

        capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        showLoading() {
            $('#loading-overlay').removeClass('d-none');
        }

        hideLoading() {
            $('#loading-overlay').addClass('d-none');
        }

        showError(message) {
            $('#error-message').text(message);
            $('#error-container').removeClass('d-none');
            setTimeout(() => {
                $('#error-container').addClass('d-none');
            }, 5000);
        }

        handleError(context, error) {
            if (typeof toastr !== 'undefined') {
                toastr.error(`${context}: ${error.message || 'Unknown error'}`);
            } else {
                this.showError(`${context}: ${error.message || 'Unknown error'}`);
            }
        }

        destroy() {
            try {
                this.dataTables.forEach((dataTable, tableId) => {
                    if ($.fn.DataTable.isDataTable(`#${tableId}`)) {
                        dataTable.destroy();
                    }
                });
                this.dataTables.clear();

                this.removeEscapeKeyListener();
                $('.stage_btn_action').off('click');
                $('.update_category_action').off('click');
                $('#pip_year_select').off('change');
                $('a[data-bs-toggle="tab"]').off('shown.bs.tab');
                $(document).off('ajaxError');

                if (this.chartInstance && this.chartInstance.detach) {
                    this.chartInstance.detach();
                }
            } catch (error) {
                console.error('Error during cleanup:', error);
            }
        }
    }

    let pipelineManager;

    $(document).ready(function() {
        try {
            pipelineManager = new PipelineManager();
        } catch (error) {
            console.error('Failed to initialize Pipeline Manager:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to initialize the application. Please refresh the page.');
            }
        }
    });

    $(window).on('beforeunload', function() {
        if (pipelineManager) {
            pipelineManager.destroy();
        }
    });

    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
        if (pipelineManager) {
            pipelineManager.handleError('Unhandled Promise Rejection', event.reason);
        }
    });
</script>
@endpush