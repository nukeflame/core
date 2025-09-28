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
                this.uploadedFiles = {}

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
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
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
                        this.showChartError();
                        return;
                    }

                    const chartContainer = $('.ct-chart-ranking');
                    if (chartContainer.length === 0) {
                        this.showChartError();
                        return;
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
                        plugins: typeof Chartist.plugins !== 'undefined' ? [Chartist.plugins.tooltip()] : [],
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

                } catch (error) {
                    this.handleError('Chart initialization failed', error);
                    this.showChartError();
                }
            }

            showChartLoading() {
                $('#chart-loading').removeClass('d-none');
                $('#chart-error').addClass('d-none');
                $('#pipeline-chart').addClass('d-none');
            }

            hideChartLoading() {
                $('#chart-loading').addClass('d-none');
                $('#pipeline-chart').removeClass('d-none');
            }

            showChartError() {
                $('#chart-loading').addClass('d-none');
                $('#chart-error').removeClass('d-none');
                $('#pipeline-chart').addClass('d-none');
            }

            loadChartData() {
                const pipelineId = $('#pip_year_select').val();

                if (!pipelineId) {
                    this.hideChartLoading();
                    return;
                }

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

                        //  // if (response && response.data && Array.isArray(response.data)) {
                        // //     this.updateChartData(response.data);
                        // // } else {
                        // this.updateChartData([0, 0, 0, 0]);
                        // // }
                        // // this.hideChartLoading();
                        // console.log(response)
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
                // if (this.chartInstance) {
                //     this.chartInstance.update({
                //         labels: ['Quarter One', 'Quarter Two', 'Quarter Three', 'Quarter Four'],
                //         series: [data]
                //     });
                // }

                // if (this.chartInstance) {
                //     this.chartInstance.update({
                //         labels: ['Quarter One', 'Quarter Two', 'Quarter Three', 'Quarter Four'],
                //         series: [data]
                //     });
                // }
            }

            initializeDataTables() {
                const tables = $('.pipeline-table');

                if (tables.length === 0) {
                    return;
                }

                tables.each((index, table) => {
                    const $table = $(table);
                    const tableId = $table.attr('id');
                    const quarter = $table.data('quarter');

                    try {
                        if ($.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().destroy();
                        }

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
                $('#pip_year_select').off('change').on('change', () => {
                    this.debounce(() => {
                        this.loadChartData();
                        this.reloadAllTables();
                    }, 300)();
                });

                $('a[data-bs-toggle="tab"]').off('shown.bs.tab').on('shown.bs.tab', (e) => {
                    const target = $(e.target).attr("href");
                    const tableId = this.getTableIdFromTab(target);

                    if (tableId && this.dataTables.has(tableId)) {
                        this.dataTables.get(tableId).columns.adjust().draw();
                    }
                });

                $(document).off('ajaxError').on('ajaxError', (event, xhr, settings, thrownError) => {
                    this.handleError('AJAX Error', {
                        url: settings.url,
                        status: xhr.status,
                        error: thrownError
                    });
                });
            }

            initializeActionHandlers() {
                $('.stage_btn_action').off('click.pipeline');
                $('.update_category_action').off('click.pipeline');

                $('.stage_btn_action').on('click.pipeline', (e) => {
                    e.preventDefault();
                    this.handleStageAction(e.currentTarget);
                });

                $('.update_category_action').on('click.pipeline', (e) => {
                    e.preventDefault();
                    this.handleCategoryUpdate(e.currentTarget);
                });
            }

            handleStageAction(button) {
                try {
                    this.showLoading();

                    const buttonData = $(button).data();
                    this.currentDealId = buttonData.deal_id;

                    if (!this.currentDealId) {
                        throw new Error('Deal ID not found in button data');
                    }

                    const $row = $(button).closest("tr");
                    const $table = $row.closest("table");
                    const tableId = $table.attr("id");

                    if (!this.dataTables.has(tableId)) {
                        throw new Error(`DataTable not found: ${tableId}`);
                    }

                    const dataTable = this.dataTables.get(tableId);
                    const rowData = dataTable.row($row).data();

                    if (!rowData || !rowData._original) {
                        throw new Error('Row data not available');
                    }

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
                        sum_insured_type: _original.sum_insured_type
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
                try {
                    const buttonData = $(button).data();

                    if (!buttonData.opportunity_id) {
                        throw new Error('Opportunity ID not found');
                    }

                    $("#updateCategoryForm #opportunity_id").val(buttonData.opportunity_id);
                    $('#updateCategoryTypeModal').modal('show');
                } catch (error) {
                    this.handleError('Error handling category update', error);
                }
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
                        typeOfBus: dealInfo ? dealInfo.type_of_business : null,
                        modalId: modalId,
                        class: dealInfo ? dealInfo.class : null,
                        classGroup: dealInfo ? dealInfo.class_group : null,
                        stage: stage,
                        categoryType: dealInfo ? dealInfo.category_type : null,
                        sumInsuredType: dealInfo?.sum_insured_type
                    };

                    this.loadScheduleHeaders(data);
                    this.loadSlipDocuments(data);
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

                    if (!dealInfo) {
                        return;
                    }

                    $modal.find('.slip-display').text(dealInfo.id || '');

                    if (dealInfo.created_at) {
                        try {
                            const dateObj = new Date(dealInfo.created_at);
                            const options = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            };
                            const formattedDate = dateObj.toLocaleDateString('en-US', options);
                            $modal.find('.created_at-display').text(formattedDate);
                        } catch (dateError) {
                            console.warn('Error formatting date:', dateError);
                            $modal.find('.created_at-display').text('');
                        }
                    }

                    let $slipTitle = '';
                    if (Number(dealInfo.category_type) === 1) {
                        $slipTitle = 'Quotation Slip';
                    } else {
                        $slipTitle = 'Facultative Slip';
                    }
                    $modal.find('.slip-title').text($slipTitle);

                    $modal.find('.insured-name-display').text(dealInfo.insured_name || '');
                    $modal.find('.insured-email-display').text(dealInfo.insured_email || '--');
                    $modal.find('.insured-phone-display').text(dealInfo.insured_phone || '--');
                    $modal.find('.insured-contact-name-display').text(dealInfo.contact_name || '--');
                    $modal.find('.sum_insured_type').text(`(${dealInfo.sum_insured_type})` || '');

                    $modal.find('.total_sum_insured').val(dealInfo.total_sum_insured || '0.00');
                    $modal.find('.premium').val(dealInfo.premium || '0.00');
                    $modal.find('.brokerage_rate').val(dealInfo.brokerage_rate || '0.00');
                    $modal.find('#totalReinsurerShare').val(dealInfo.written_share || '0.00');
                    $modal.find('#classCodeValue').val(dealInfo.class || '');
                    $modal.find('#classGroupCodeValue').val(dealInfo.class_group || '');
                } catch (error) {
                    this.handleError("Error populating modal data", error);
                }
            }

            loadScheduleHeaders(data) {
                if (!data.dealId || !data.class || !data.classGroup) {
                    return;
                }

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
                        } else {
                            this.renderScheduleHeaders([], data);
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
                if (!data.dealId) {
                    return;
                }

                const $modal = $(`#${data.modalId}`);
                const $documentsSubtitle = $modal.find('#documentsSubtitle');

                if ($documentsSubtitle.length > 0) {
                    $documentsSubtitle.html(
                        '<small><span class="loading-spinner"></span> Loading documents...</small>');
                }

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
                            if ($documentsSubtitle.length > 0) {
                                $documentsSubtitle.html(
                                    `<small>Documents for ${response.class_name || 'this class'}</small>`
                                );
                            }
                        } else {
                            if ($documentsSubtitle.length > 0) {
                                $documentsSubtitle.html(`<small>No documents found</small>`);
                            }
                        }
                    },
                    error: (xhr, status, error) => {
                        this.handleError('Error loading slip documents', {
                            xhr,
                            status,
                            error
                        });
                        this.showError('Failed to load slip documents');
                        if ($documentsSubtitle.length > 0) {
                            $documentsSubtitle.html(`<small>Error loading documents</small>`);
                        }
                    }
                });
            }

            renderScheduleHeaders(headers, data) {
                if (!data || !data.modalId) {
                    return;
                }

                const $modal = $(`#${data.modalId}`);
                const container = $modal.find('#termsConditions');
                if (container.length === 0) {
                    return;
                }

                container.empty();

                if (!Array.isArray(headers)) {
                    container.html('<p class="text-muted text-center my-4">Invalid headers data.</p>');
                    return;
                }

                const excludedTerms = [
                    'Premium',
                    'Sum Insured Breakdown',
                    'Reinsurer Commission Rate',
                    'Allowed Commission',
                    'Commission',
                    'Deductible/Excess'
                ];

                const validHeaders = headers.filter(h => {
                    if (!h) {
                        return false;
                    }

                    const hasValidSumInsuredType = h.sum_insured_type?.trim() === '';
                    const isNotExcluded = !excludedTerms.some(term =>
                        h.name?.replace(/\s+/g, ' ').trim().includes(term)
                    );

                    return hasValidSumInsuredType && isNotExcluded;
                });

                validHeaders.sort((a, b) => {
                    const positionA = parseInt(a.position) || 0;
                    const positionB = parseInt(b.position) || 0;
                    return positionA - positionB;
                });



                const includeDeductible = ['Deductible/Excess'];
                const deductible = headers.filter(h =>
                    includeDeductible.some(term =>
                        h?.name?.replace(/\s+/g, ' ').trim().includes(term)
                    )
                );

                $('.deductible_excess_div').hide();
                if (deductible?.length > 0) {
                    $('.deductible_excess_div').show();
                }

                if (validHeaders.length === 0) {
                    container.html('<p class="text-muted text-center my-4">No schedule headers configured.</p>');
                    return;
                }

                let fieldsHtml = '';
                validHeaders.forEach((header, index) => {
                    if (index % 2 === 0) {
                        fieldsHtml += '<div class="row">';
                    }

                    const fieldId = `schedule_${header.id}`;

                    let headerName = this.capitalize(header.name);
                    let fieldInput = this.generateFieldInput(header, fieldId);

                    fieldsHtml += `
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="${fieldId}" class="form-label capitalize">
                                    ${headerName}${header.amount_field === 'Y' ? ' <span class="text-danger pl-1">*</span>' : ''}
                                </label>
                                ${fieldInput}
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    `;

                    if (index % 2 === 1 || index === validHeaders.length - 1) {
                        fieldsHtml += '</div>';
                    }
                });

                container.html(fieldsHtml);

                if (typeof this.setupFieldValidation === 'function') {
                    this.setupFieldValidation($modal);
                }
            }

            renderSlipDocuments(res, data, $modal) {
                if (!res.docs || !res.docs.length) {
                    return;
                }

                const fallbackConfig = {
                    name: res.class_name || "Insurance Class",
                    documents: [{
                            id: 'policy_schedule',
                            name: 'Policy Schedule',
                            required: true,
                            icon: 'bx-file-blank',
                            accepts: '.pdf,.doc,.docx',
                            description: 'Current policy terms and coverage details',
                            max_size: 10485760
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
                            max_size: 5242880,
                            multiple: true
                        }
                    ]
                };

                this.generateDocumentFields(fallbackConfig, $modal);
            }

            generateDocumentFields(config, $modal) {
                const $container = $modal.find('#documentFields');
                const $placeholder = $modal.find('#documentPlaceholder');
                const $summarySection = $modal.find('#documentSummarySection');

                if ($container.length === 0) {
                    return;
                }

                if ($placeholder.length) $placeholder.hide();
                $container.empty().show();

                if (!config.documents || config.documents.length === 0) {
                    $container.html(`
                        <div class="col-12 text-center py-4">
                            <i class="bx bx-info-circle bx-2x text-muted mb-2"></i>
                            <p class="text-muted">No specific documents required for this insurance class.</p>
                        </div>
                    `);
                    if ($summarySection.length) $summarySection.hide();
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
                                        ${doc.required ? '<span class="required-indicator text-danger">*</span>' : ''}
                                    </label>
                                    <div class="file-upload-area border rounded p-3 text-center" data-field="${doc.id}">
                                        <i class="bx ${doc.icon} upload-icon fs-2 text-muted"></i>
                                        <div class="upload-text fw-semibold">${doc.name}</div>
                                        <div class="upload-subtext text-muted small">${doc.description}</div>
                                        <input type="file" class="d-none file-input"
                                            name="${doc.id}"
                                            ${doc.required ? 'required' : ''}
                                            accept="${doc.accepts}"
                                            ${doc.multiple ? 'multiple' : ''}
                                            data-max-size="${doc.max_size || 10485760}">
                                        <div class="upload-constraints small text-muted mt-2">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Max size: ${maxSizeText} | Formats: ${acceptsText}
                                        </div>
                                        <div class="file-count-badge badge bg-secondary position-absolute" style="top: 10px; right: 10px;">0</div>
                                    </div>
                                    <div class="file-preview-container mt-2"></div>
                                </div>
                            </div>
                        </div>
                    `;
                    $container.append(fieldHtml);
                });

                if ($modal.find('#docCount').length) {
                    $modal.find('#docCount').text(config.documents.length);
                }

                this.initializeFileUploads();

                if ($summarySection.length) $summarySection.show();
                this.updateDocumentSummary(config);
            }

            initializeFileUploads() {
                $('.file-upload-area').each((index, element) => {
                    const $uploadArea = $(element);
                    const $input = $uploadArea.find('.file-input');
                    const $previewContainer = $uploadArea.siblings('.file-preview-container');

                    $uploadArea.off('.fileUpload');
                    $input.off('.fileUpload');

                    $uploadArea.on('click.fileUpload', (e) => {
                        e.stopPropagation();
                        e.preventDefault();

                        if (!$(e.target).is('button, input, .file-action-btn, .file-remove-btn')) {
                            console.log('Triggering file input click for:', $uploadArea.data('field'));
                            $input.trigger('click');
                        }
                    });

                    $input.on('change.fileUpload', (e) => {
                        e.stopPropagation();
                        console.log('File input changed:', e.target.files.length, 'files');
                        if (e.target.files && e.target.files.length > 0) {
                            this.handleFileSelection(e.target.files, $uploadArea, $previewContainer);
                        }
                        // Clear the input value to allow selecting the same file again
                        e.target.value = '';
                    });

                    // Drag and drop events
                    $uploadArea.on('dragover.fileUpload dragenter.fileUpload', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        $uploadArea.addClass('drag-over border-primary');
                    });

                    $uploadArea.on('dragleave.fileUpload', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        // Only remove drag styling if we're leaving the upload area itself
                        if (!$uploadArea[0].contains(e.relatedTarget)) {
                            $uploadArea.removeClass('drag-over border-primary');
                        }
                    });

                    $uploadArea.on('drop.fileUpload', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        $uploadArea.removeClass('drag-over border-primary');

                        const files = e.originalEvent.dataTransfer?.files;
                        if (files && files.length > 0) {
                            console.log('Files dropped:', files.length);
                            this.handleFileSelection(files, $uploadArea, $previewContainer);
                        }
                    });
                });
            }

            handleFileSelection(files, $uploadArea, $previewContainer) {
                if (!files || files.length === 0) {
                    return;
                }

                const fieldId = $uploadArea.data('field');
                const maxSize = parseInt($uploadArea.find('.file-input').data('max-size')) || 10485760; // Default 10MB

                if (!this.uploadedFiles[fieldId]) {
                    this.uploadedFiles[fieldId] = [];
                }

                let validFiles = 0;
                let rejectedFiles = 0;

                Array.from(files).forEach((file, index) => {
                    if (file.size > maxSize) {
                        this.showError(
                            `File "${file.name}" exceeds maximum size of ${this.formatFileSize(maxSize)}`);
                        rejectedFiles++;
                        return;
                    }

                    const fileId = `file_${fieldId}_${Date.now()}_${index}`;
                    const fileWithId = Object.assign(file, {
                        fileId
                    });

                    this.uploadedFiles[fieldId].push(fileWithId);
                    validFiles++;

                    this.createFilePreview(fileWithId, $previewContainer, fieldId);
                });

                this.updateFileCountBadge($uploadArea, fieldId);

                console.log(`File selection complete:`, {
                    fieldId: fieldId,
                    validFiles: validFiles,
                    rejectedFiles: rejectedFiles,
                    totalFiles: this.uploadedFiles[fieldId].length
                });

                try {
                    if (typeof this.currentDocumentConfig !== 'undefined' && this.currentDocumentConfig) {
                        this.updateDocumentSummary(this.currentDocumentConfig);
                    }
                } catch (error) {
                    console.warn('Could not update document summary:', error);
                }
            }

            updateFileCountBadge($uploadArea, fieldId) {
                const $badge = $uploadArea.find('.file-count-badge');
                const fileCount = this.uploadedFiles[fieldId] ? this.uploadedFiles[fieldId].length : 0;

                $badge.text(fileCount);

                if (fileCount > 0) {
                    $badge.removeClass('bg-secondary').addClass('bg-success');
                } else {
                    $badge.removeClass('bg-success').addClass('bg-secondary');
                }
            }

            createFilePreview(file, $container, fieldId) {
                const fileId = file.fileId || `file_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                const fileSize = this.formatFileSize(file.size);
                const fileName = file.name || 'Unknown file';

                if ($container.find(`[data-file-id="${fileId}"]`).length > 0) {
                    return;
                }

                const previewHtml = `
                    <div class="file-preview-item d-flex align-items-center justify-content-between p-2 border rounded mb-2" data-file-id="${fileId}">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-file me-2 text-primary"></i>
                            <div>
                                <div class="fw-semibold text-truncate" style="max-width: 200px;" title="${fileName}">${fileName}</div>
                                <div class="small text-muted">${fileSize}</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger file-remove-btn"
                                data-field="${fieldId}"
                                data-file-id="${fileId}"
                                title="Remove file">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                `;

                $container.append(previewHtml);

                const $removeBtn = $container.find(`[data-file-id="${fileId}"] .file-remove-btn`);
                $removeBtn.off('click.fileRemove').on('click.fileRemove', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    this.removeFile(fieldId, fileId);
                });
            }

            removeFile(fieldId, fileId) {
                try {
                    const $previewItem = $(`.file-preview-item[data-file-id="${fileId}"]`);
                    if ($previewItem.length > 0) {
                        $previewItem.remove();
                    }

                    if (this.uploadedFiles[fieldId] && Array.isArray(this.uploadedFiles[fieldId])) {
                        const originalLength = this.uploadedFiles[fieldId].length;
                        this.uploadedFiles[fieldId] = this.uploadedFiles[fieldId].filter(f => f.fileId !== fileId);
                        const newLength = this.uploadedFiles[fieldId].length;

                        const $uploadArea = $(`[data-field="${fieldId}"]`);
                        if ($uploadArea.length > 0) {
                            this.updateFileCountBadge($uploadArea, fieldId);
                        }

                        try {
                            if (typeof this.currentDocumentConfig !== 'undefined' && this.currentDocumentConfig) {
                                this.updateDocumentSummary(this.currentDocumentConfig);
                            }
                        } catch (error) {
                            console.warn('Could not update document summary after file removal:', error);
                        }
                    }

                } catch (error) {
                    this.handleError('Error removing file', error);
                }
            }

            updateDocumentSummary(config) {
                const totalFiles = Object.values(this.uploadedFiles).reduce((sum, files) => sum + files.length, 0);
                const requiredDocs = config.documents.filter(doc => doc.required);
                const uploadedRequiredDocs = requiredDocs.filter(doc =>
                    this.uploadedFiles[doc.id] && this.uploadedFiles[doc.id].length > 0
                );

                const summaryData = {
                    totalFiles,
                    requiredDocs: requiredDocs.length,
                    uploadedRequiredDocs: uploadedRequiredDocs.length,
                    status: uploadedRequiredDocs.length === requiredDocs.length ? 'Complete' : 'Incomplete'
                };

                const $uploadSummary = $('#uploadSummary');
                if ($uploadSummary.length) {
                    const summaryHtml = `
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 text-primary mb-1">${summaryData.totalFiles}</div>
                                    <div class="small text-muted">Total Files</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 text-success mb-1">${summaryData.uploadedRequiredDocs}/${summaryData.requiredDocs}</div>
                                    <div class="small text-muted">Required Documents</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 text-info mb-1">${this.getTotalFileSize()}</div>
                                    <div class="small text-muted">Total Size</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 ${summaryData.status === 'Complete' ? 'text-success' : 'text-warning'} mb-1">
                                        ${summaryData.status}
                                    </div>
                                    <div class="small text-muted">Status</div>
                                </div>
                            </div>
                        </div>
                    `;
                    $uploadSummary.html(summaryHtml);
                }
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
                Object.values(this.uploadedFiles).forEach(files => {
                    files.forEach(file => {
                        totalSize += file.size || 0;
                    });
                });
                return this.formatFileSize(totalSize);
            }

            // generateFieldInput(header, fieldId) {
            //     const baseInputClass = 'form-control form-inputs';
            //     const required = header.amount_field === 'Y' ? 'required' : '';
            //     const placeholder = `Enter ${header.name.toLowerCase()}`;

            //     try {
            //         if (header.data_determinant === 'Sum Insured' ||
            //             header.data_determinant === 'Premium' ||
            //             header.name.toLowerCase().includes('amount')) {

            //             const currency = header.class_group === 'FIRE' ? 'KES' : 'USD';
            //             return `
        //                     <div class="input-group">
        //                         <span class="input-group-text">${currency}</span>
        //                         <input type="number" class="${baseInputClass}" id="${fieldId}"
        //                             name="schedule_headers[${header.id}]" step="0.01" min="0"
        //                             placeholder="${placeholder}" ${required}>
        //                     </div>
        //                 `;
            //         } else if (header.name.toLowerCase().includes('date')) {
            //             return `<input type="date" class="${baseInputClass}" id="${fieldId}"
        //                         name="schedule_headers[${header.id}]" ${required}>`;
            //         } else if (header.name.toLowerCase().includes('percentage') ||
            //             header.name.toLowerCase().includes('rate')) {
            //             return `
        //                 <div class="input-group">
        //                     <input type="number" class="${baseInputClass}" id="${fieldId}"
        //                         name="schedule_headers[${header.id}]" step="0.01" min="0" max="100"
        //                         placeholder="${placeholder}" ${required}>
        //                     <span class="input-group-text">%</span>
        //                 </div>`;
            //         } else if (header.type_of_sum_insured && header.type_of_sum_insured !== 'N/A') {
            //             let options = `<option value="">Select ${header.name}</option>`;
            //             if (header.type_of_sum_insured === 'TOTAL SUM INSURED') {
            //                 options += `
        //                             <option value="total_sum_insured">Total Sum Insured</option>
        //                             <option value="individual_sum_insured">Individual Sum Insured</option>`;
            //             }
            //             return `<select class="form-select ${baseInputClass.replace('form-control', '')}" id="${fieldId}"
        //                     name="schedule_headers[${header.id}]" ${required}>${options}</select>`;
            //         } else {
            //             return `<input type="text" class="${baseInputClass}" id="${fieldId}"
        //                    name="schedule_headers[${header.id}]" placeholder="${placeholder}" ${required}>`;
            //         }
            //     } catch (error) {
            //         return `<input type="text" class="${baseInputClass}" id="${fieldId}"
        //                name="schedule_headers[${header.id}]" placeholder="${placeholder}" ${required}>`;
            //     }
            // }

            generateFieldInput(header, fieldId) {
                const baseInputClass = 'form-control form-inputs';
                const required = header.amount_field === 'Y' ? 'required' : '';
                const placeholder = `Enter ${header.name?.toLowerCase() || 'value'}`;

                try {
                    if (header.data_determinant === 'Sum Insured' ||
                        header.data_determinant === 'Premium' ||
                        header.name?.toLowerCase().includes('amount')) {

                        const currency = header.class_group === 'FIRE' ? 'KES' : 'USD';
                        return `
                            <div class="input-group">
                                <span class="input-group-text">${currency}</span>
                                <input type="number" class="${baseInputClass}" id="${fieldId}"
                                    name="schedule_headers[${header.id}]" step="0.01" min="0"
                                    placeholder="${placeholder}" ${required}>
                            </div>
                        `;
                    } else if (header.name?.toLowerCase().includes('date')) {
                        return `<input type="date" class="${baseInputClass}" id="${fieldId}" name="schedule_headers[${header.id}]" ${required}>`;
                    } else if (header.name?.toLowerCase().includes('percentage') ||
                        header.name?.toLowerCase().includes('rate')) {
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
                        return `<select class="form-select ${baseInputClass.replace('form-control', '')}" id="${fieldId}" name="schedule_headers[${header.id}]" ${required}>${options}</select>`;
                    } else {
                        const isTextarea = !header.input_type || header.input_type === 'textarea';

                        if (isTextarea) {
                            return `<textarea class="form-inputs breakdown-textarea" id="${fieldId}" name="schedule_headers[${header.id}]" rows="4" maxlength="5000" aria-label="${header.name}" placeholder="${placeholder}" ${required} readonly></textarea>`;
                        } else {
                            return `<input type="text" class="${baseInputClass}" id="${fieldId}" name="schedule_headers[${header.id}]" placeholder="${placeholder}" ${required}>`;
                        }
                    }
                } catch (error) {
                    const isTextarea = !header?.input_type || header?.input_type === 'textarea';

                    if (isTextarea) {
                        return `<textarea class="form-inputs breakdown-textarea" id="${fieldId}" name="schedule_headers[${header.id}]" rows="4" maxlength="5000" aria-label="${header.name}" placeholder="${placeholder}" ${required} readonly></textarea>`;
                    } else {
                        return `<input type="text" class="${baseInputClass}" id="${fieldId}" name="schedule_headers[${header?.id || ''}]" placeholder="${placeholder}" ${required}>`;
                    }
                }
            }

            setupFieldValidation($modal) {
                $modal.find('input[required], select[required]').off('blur.validation change.validation')
                    .on('blur.validation change.validation', function() {
                        const $field = $(this);
                        const value = $field.val();

                        if (!value || value.toString().trim() === '') {
                            $field.addClass('is-invalid');
                            $field.siblings('.invalid-feedback').text('This field is required.');
                        } else {
                            $field.removeClass('is-invalid');
                            $field.siblings('.invalid-feedback').text('');
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

                    if (!value || value.toString().trim() === '') {
                        $field.addClass('is-invalid');
                        $field.siblings('.invalid-feedback').text('This field is required.');
                        isValid = false;
                    } else {
                        $field.removeClass('is-invalid');
                        $field.siblings('.invalid-feedback').text('');
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
                let reloadCount = 0;

                this.dataTables.forEach((dataTable, tableId) => {
                    try {
                        dataTable.ajax.reload((json) => {
                            reloadCount++;
                        }, false);
                    } catch (error) {
                        console.error(`Error reloading table ${tableId}:`, error);
                    }
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
                return mapping[tabId] || null;
            }

            capitalize(str) {
                if (!str || typeof str !== 'string') return '';
                return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
            }

            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func.apply(this, args);
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
                console.error('Showing error:', message);
                $('#error-message').text(message);
                $('#error-container').removeClass('d-none');

                setTimeout(() => {
                    $('#error-container').addClass('d-none');
                }, 5000);

                if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                }
            }

            handleError(context, error) {
                const errorMessage = error?.message || error?.statusText || 'Unknown error occurred';
                const fullMessage = `${context}: ${errorMessage}`;

                if (typeof toastr !== 'undefined') {
                    toastr.error(fullMessage);
                } else {
                    this.showError(fullMessage);
                }

                if (error?.xhr) {
                    console.error('XHR Error Details:', {
                        status: error.xhr.status,
                        statusText: error.xhr.statusText,
                        responseText: error.xhr.responseText
                    });
                }
            }

            destroy() {
                try {
                    let destroyCount = 0;
                    this.dataTables.forEach((dataTable, tableId) => {
                        try {
                            if ($.fn.DataTable.isDataTable(`#${tableId}`)) {
                                dataTable.destroy(true);
                                destroyCount++;
                                console.log(`DataTable ${tableId} destroyed`);
                            }
                        } catch (error) {
                            console.error(`Error destroying DataTable ${tableId}:`, error);
                        }
                    });
                    this.dataTables.clear();

                    this.removeEscapeKeyListener();
                    $('.stage_btn_action').off('.pipeline');
                    $('.update_category_action').off('.pipeline');
                    $('#pip_year_select').off('change');
                    $('a[data-bs-toggle="tab"]').off('shown.bs.tab');
                    $(document).off('ajaxError');
                    $('.file-upload-area').off('.fileUpload');
                    $('.file-input').off('.fileUpload');

                    if (this.chartInstance && typeof this.chartInstance.detach === 'function') {
                        this.chartInstance.detach();
                        console.log('Chart destroyed');
                    }

                    this.uploadedFiles = {};
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
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to initialize the application. Please refresh the page.');
                } else {
                    alert('Failed to initialize the application. Please refresh the page.');
                }
            }
        });

        $(window).on('beforeunload', function() {
            if (pipelineManager && typeof pipelineManager.destroy === 'function') {
                pipelineManager.destroy();
            }
        });

        window.addEventListener('unhandledrejection', function(event) {
            if (pipelineManager && typeof pipelineManager.handleError === 'function') {
                pipelineManager.handleError('Unhandled Promise Rejection', event.reason);
            }
        });
    </script>
@endpush
