@extends('layouts.app')

@section('content')
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Sales Management
            </h1>
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

        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">Pipeline Details</div>
            </div>

            <div class="card-body">
                <div class="mb-4">
                    <form id="pip_year_form" action="{{ route('pipeline.view') }}" method="get">
                        <input type="hidden" id="opp_id" name="opp_id">
                        <div class="row">
                            <div class="col-md-3">
                                <x-SearchableSelect id="pip_year_select" req="" inputLabel="Pipeline Year"
                                    name="pipeline" placeholder="--Select Year--">
                                    {{-- @foreach ($pipelines as $pip_year)
                                        <option @if ($pip_year->id == $pip) selected @endif
                                            value="{{ $pip_year->id }}">
                                            {{ $pip_year->year }}
                                        </option>
                                    @endforeach --}}
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
                <div class="d-flex justify-content-center flex-wrap" style="height:300px;">
                    <div class="ct-chart-ranking ct-golden-section ct-series-a"></div>
                </div>

                <div class="row">
                    <hr>
                    <div class="d-flex justify-content-center flex-wrap">
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #453d3f; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Lead</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #f05b4f; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Proposal</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #f4c63d; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Negotiation</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #d17905; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Won</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #d70206; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Lost</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #59922b; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Final Stage</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-body">
                <ul class="nav nav-pills nav-style-3 mb-4 pb-1" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page"
                            href="#general_details" aria-selected="true">All Quarters</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#q1_details"
                            aria-selected="false" tabindex="-1">Quarter One</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#q2_details"
                            aria-selected="false" tabindex="-1">Quarter Two</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#q3_details"
                            aria-selected="false" tabindex="-1">Quarter Three</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#q4_details"
                            aria-selected="false" tabindex="-1">Quarter Four</a>
                    </li>
                </ul>

                <div class="tab-content p-0 mt-1 border-none">
                    <div class="tab-pane active border-none" id="general_details">
                        <div class="row">
                            <div class="table-responsive">
                                <table id="all_opps" class="table table-striped" style="width:100%">
                                    <thead>
                                        <th>ID</th>
                                        <th>Insured Name</th>
                                        <th>Division</th>
                                        <th>Business class</th>
                                        <th>Status</th>
                                        <th>Currency</th>
                                        <th>Sum Insured</th>
                                        <th>Premium</th>
                                        <th>Effective date</th>
                                        <th>Closing date</th>
                                        <th>Category</th>
                                        <th>Approval Status</th>
                                        <th>Stage Actions</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane border-none" id="q1_details">
                        <div class="row">
                            <div class="table-responsive">
                                <table id="q1_opps" class="table table-striped" style="width:100%">
                                    <thead>
                                        <th>ID</th>
                                        <th>Insured Name</th>
                                        <th>Division</th>
                                        <th>Business Class</th>
                                        <th>Status</th>
                                        <th>Currency</th>
                                        <th>Sum Insured</th>
                                        <th>Premium</th>
                                        <th>Effective date</th>
                                        <th>Closing date</th>
                                        <th>Category</th>
                                        <th>Approval Status</th>
                                        <th>Stage Actions</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane border-none" id="q2_details">
                        <div class="row">
                            <div class="table-responsive">
                                <table id="q2_opps" class="table table-striped" style="width:100%">
                                    <thead>
                                        <th>ID</th>
                                        <th>Insured Name</th>
                                        <th>Division</th>
                                        <th>Business Class</th>
                                        <th>Status</th>
                                        <th>Currency</th>
                                        <th>Sum Insured</th>
                                        <th>Premium</th>
                                        <th>Effective date</th>
                                        <th>Closing date</th>
                                        <th>Category</th>
                                        <th>Approval Status</th>
                                        <th>Stage Actions</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane border-none" id="q3_details">
                        <div class="row">
                            <div class="table-responsive">
                                <table id="q3_opps" class="table table-striped" style="width:100%">
                                    <thead class="mt-2">
                                        <th>ID</th>
                                        <th>Insured Name</th>
                                        <th>Division</th>
                                        <th>Business Class</th>
                                        <th>Status</th>
                                        <th>Currency</th>
                                        <th>Sum Insured</th>
                                        <th>Premium</th>
                                        <th>Effective date</th>
                                        <th>Closing date</th>
                                        <th>Category</th>
                                        <th>Approval Status</th>
                                        <th>Stage Actions</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane border-none" id="q4_details">
                        <div class="row">
                            <div class="table-responsive">
                                <table id="q4_opps" class="table table-striped" style="width:100%">
                                    <thead>
                                        <th>ID</th>
                                        <th>Insured Name</th>
                                        <th>Division</th>
                                        <th>Business Class</th>
                                        <th>Status</th>
                                        <th>Currency</th>
                                        <th>Sum Insured</th>
                                        <th>Premium</th>
                                        <th>Effective date</th>
                                        <th>Closing date</th>
                                        <th>Category</th>
                                        <th>Approval Status</th>
                                        <th>Stage Actions</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modals --}}
        @include('Bd_views.intermediaries.partials.modals.pipeline_view_modals')
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
        }

        .stage-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
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
    </style>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let chartInstance = initializePipelineChart();
            let currentDealId = null;
            let currentStage = "lead";

            const columnConfig = [{
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
            ];

            const stageFlow = {
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
            };

            const tableIds = ['#all_opps', '#q1_opps', '#q2_opps', '#q3_opps', '#q4_opps'];
            tableIds.forEach(tableId => {
                if ($(tableId).length > 0) {
                    try {
                        $(tableId).DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: "{{ route('pipeline.sales.get_pipeline_data') }}",
                                data: function(d) {
                                    d.pipeline_id = $('#pip_year_select').val();
                                    d.quarter = getQuarterFromTableId(tableId);
                                },
                                error: function(xhr, error, code) {
                                    console.error('DataTables AJAX error:', error);
                                    toastr.error(
                                        'Failed to load approval data. Please refresh the page.'
                                    );
                                }
                            },
                            columns: columnConfig,
                            order: [
                                [0, 'desc']
                            ],
                            language: {
                                processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>',
                                emptyTable: "No pipeline records found",
                                info: "Showing _START_ to _END_ of _TOTAL_ approvals",
                                infoEmpty: "No pipeline available",
                                infoFiltered: "(filtered from _MAX_ total pipeline)",
                                lengthMenu: "Show _MENU_ pipeline per page",
                                search: "Search pipeline:",
                                paginate: {
                                    first: "First",
                                    last: "Last",
                                    next: "Next",
                                    previous: "Previous"
                                }
                            },
                            drawCallback: function(settings) {
                                initializeActionHandlers();
                            }
                        });
                    } catch (error) {
                        console.error('Error initializing DataTable for', tableId, ':', error);
                    }
                }
            });


            $('#pip_year_select').on('change', function() {
                if (chartInstance) {
                    loadChartData(chartInstance);
                }
                tableIds.forEach(function(tableId) {
                    if ($.fn.DataTable.isDataTable(tableId)) {
                        $(tableId).DataTable().ajax.reload();
                    }
                });
            });

            function initializeActionHandlers() {
                $('.stage_btn_action').off('click').on('click', function(e) {
                    e.preventDefault();
                    const data = $(this).data();
                    console.log('Update status clicked:', data);

                    try {
                        currentDealId = data.deal_id;

                        const dealCurrentStage = data.current_stage;
                        currentStage = dealCurrentStage;

                        const stageInfo = stageFlow[currentStage];
                        if (!stageInfo) {
                            throw new Error(`Invalid stage: ${currentStage}`);
                        }

                        const nextStage = stageInfo.next;
                        if (nextStage) {
                            openStageModal(nextStage, currentDealId);
                        }
                    } catch (error) {
                        console.error("Error opening next stage modal:", error);
                    }
                });
            }

            function openStageModal(stage, dealId) {
                try {
                    currentDealId = dealId;
                    const modalId = stage + "Modal";
                    const modal = document.getElementById(modalId);

                    if (!modal) {
                        throw new Error(`Modal not found: ${modalId}`);
                    }
                    // populateModalData(modalId, dealId);
                    $(`#${modalId}`).modal('show')
                    addEscapeKeyListener();
                } catch (error) {
                    console.error("Error opening modal:", error);
                }
            }

            function populateModalData(modalId, dealId) {
                try {
                    const deal = dealData[dealId];
                    if (!deal) return;

                    const modal = document.getElementById(modalId);
                    if (!modal) return;
                    // const dealIdInput = modal.querySelector('input[value*="001625"]');
                    // if (dealIdInput) {
                    //     dealIdInput.value = `PROP-2025-${String(dealId).padStart(6, "0")}`;
                    // }

                } catch (error) {
                    console.error("Error populating modal data:", error);
                }
            }

            function addEscapeKeyListener() {
                if (escapeKeyHandler) return;

                escapeKeyHandler = function(event) {
                    if (event.key === "Escape") {
                        const openModal = document.querySelector('.modal[style*="block"]');
                        if (openModal) {
                            closeModal(openModal.id);
                        }
                    }
                };

                document.addEventListener("keydown", escapeKeyHandler);
            }

            function getQuarterFromTableId(tableId) {
                if (tableId.includes('q1')) return 1;
                if (tableId.includes('q2')) return 2;
                if (tableId.includes('q3')) return 3;
                if (tableId.includes('q4')) return 4;
                return 'all';
            }

            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr("href");

                const tableId = getTableIdFromTab(target);
                if (tableId && $.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).DataTable().columns.adjust().draw();
                }
            });

            function getTableIdFromTab(tabId) {
                const mapping = {
                    '#general_details': '#all_opps',
                    '#q1_details': '#q1_opps',
                    '#q2_details': '#q2_opps',
                    '#q3_details': '#q3_opps',
                    '#q4_details': '#q4_opps'
                };
                return mapping[tabId];
            }
        });

        function initializePipelineChart() {
            try {
                if ($('.ct-chart-ranking').length === 0) {
                    console.error('Chart container not found');
                    return;
                }

                let chart = new Chartist.Bar('.ct-chart-ranking', {
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

                chart.on('draw', function(data) {
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
                loadChartData(chart);

                return chart;
            } catch (error) {
                console.error('Error initializing chart:', error);
            }
        }

        function loadChartData(chart) {
            try {
                const pipelineId = $('#pip_year_select').val();

                $.ajax({
                    url: "{{ route('pipeline.sales.get_pipeline_chart_data') }}",
                    method: 'GET',
                    data: {
                        pipeline_id: pipelineId
                    },
                    success: function(response) {
                        if (response && response.data && Array.isArray(response.data)) {
                            updateChartData(chart, response.data);
                        } else {
                            updateChartData(chart, [0, 0, 0, 0]);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Status Code:', xhr.status);
                        updateChartData(chart, [0, 0, 0, 0]);
                    }
                });
            } catch (error) {
                console.error('Error in loadChartData:', error);
            }
        }

        function updateChartData(chart, data) {
            try {
                chart.update({
                    labels: ['Quarter One', 'Quarter Two', 'Quarter Three', 'Quarter Four'],
                    series: [data]
                });
            } catch (error) {
                console.error('Error updating chart:', error);
            }
        }
    </script>
@endpush
