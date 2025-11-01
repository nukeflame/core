'use strict';

/**
 * Pipeline Manager
 *
 * @description Manages the sales pipeline view including chart rendering,
 * data table management, stage transitions, document uploads, and email notifications.
 *
 * @version 1.0.0
 * @requires jQuery, DataTables, Chartist, Bootstrap, SweetAlert2, Toastr
 */

/* ============================================================================
   Constants
   ========================================================================== */
const STAGE_NAMES = {
    LEAD: 'lead',
    PROPOSAL: 'proposal',
    NEGOTIATION: 'negotiation',
    FINAL_STAGE: 'final_stage',
    WON: 'won',
    LOST: 'lost'
};

const CHART_LABELS = ['Quarter One', 'Quarter Two', 'Quarter Three', 'Quarter Four'];

const DEFAULT_CHART_DATA = [0, 0, 0, 0];

const FILE_SIZE_UNITS = ['Bytes', 'KB', 'MB', 'GB'];
const FILE_SIZE_BASE = 1024;

const DEFAULT_MAX_FILE_SIZE = 10485760; // 10MB in bytes

const AJAX_TIMEOUT = 10000; // 10 seconds
const EXTENDED_AJAX_TIMEOUT = 30000; // 30 seconds

const DEBOUNCE_DELAY = 300; // milliseconds

const ERROR_DISPLAY_DURATION = 5000; // 5 seconds

const EXCLUDED_TERMS = [
    'Premium',
    'Sum Insured Breakdown',
    'Reinsurer Commission Rate',
    'Allowed Commission',
    'Commission',
    'Deductible/Excess'
];

const DEDUCTIBLE_TERMS = ['Deductible/Excess'];

/* ============================================================================
   PipelineManager Class
   ========================================================================== */
class PipelineManager {
    /**
     * Creates a new PipelineManager instance
     */
    constructor() {
        // Core properties
        this.chartInstance = null;
        this.totalSumInsured = null;
        this.currentDealId = null;
        this.currentStage = STAGE_NAMES.LEAD;
        this.escapeKeyHandler = null;
        this.dataTables = new Map();
        this.uploadedFiles = {};
        this.reinsurerDataTable = null;
        this.activeFileUrls = new Set();

        // Cache frequently used DOM elements
        this.$pipYearSelect = null;
        this.$loadingOverlay = null;
        this.$errorContainer = null;
        this.$errorMessage = null;
        this.$chartLoading = null;
        this.$chartError = null;
        this.$pipelineChart = null;

        // Configuration object with routes and stage flow
        this.config = {
            routes: {
                pipelineData: window.pipelineRoutes?.pipelineData || '',
                chartData: window.pipelineRoutes?.chartData || '',
                scheduleHeaders: window.pipelineRoutes?.scheduleHeaders || '',
                slipDocuments: window.pipelineRoutes?.slipDocuments || '',
                getBdTerms: window.pipelineRoutes?.getBdTerms || '',
                declineReinsurer: window.pipelineRoutes?.declineReinsurer || '',
                getSelectedReinsurers: window.pipelineRoutes?.getSelectedReinsurers || '',
            },
            stageFlow: {
                [STAGE_NAMES.LEAD]: {
                    next: STAGE_NAMES.PROPOSAL,
                    button: 'Update Lead',
                    class: 'btn-proposal',
                    altNext: STAGE_NAMES.LOST,
                    previous: null,
                    modalId: 'leadModal',
                },
                [STAGE_NAMES.PROPOSAL]: {
                    next: STAGE_NAMES.NEGOTIATION,
                    button: 'Update Proposal',
                    class: 'btn-negotiation',
                    altNext: STAGE_NAMES.LOST,
                    previous: STAGE_NAMES.LEAD,
                    modalId: 'proposalModal',
                },
                [STAGE_NAMES.NEGOTIATION]: {
                    next: STAGE_NAMES.FINAL_STAGE,
                    button: 'Update Negotiation',
                    class: 'btn-won',
                    altNext: STAGE_NAMES.LOST,
                    previous: STAGE_NAMES.PROPOSAL,
                    modalId: 'negotiationModal',
                },
                [STAGE_NAMES.FINAL_STAGE]: {
                    next: STAGE_NAMES.WON,
                    button: 'Update Status',
                    class: 'btn-final',
                    previous: STAGE_NAMES.NEGOTIATION,
                    modalId: 'finalStageModal',
                },
                [STAGE_NAMES.WON]: {
                    next: null,
                    button: 'Deal Complete',
                    class: 'btn-final',
                    previous: null,
                    modalId: 'wonModal',
                },
                [STAGE_NAMES.LOST]: {
                    next: null,
                    button: 'Deal Closed',
                    class: 'btn-lost',
                    previous: null,
                    modalId: 'lostModal',
                },
            },
            columnConfig: [
                { data: 'id', name: 'id', title: 'ID' },
                { data: 'insured_name', name: 'insured_name', title: 'Insured Name' },
                { data: 'division', name: 'division', title: 'Division' },
                { data: 'business_class', name: 'business_class', title: 'Business Class' },
                { data: 'status', name: 'status', title: 'Status' },
                { data: 'currency', name: 'currency', title: 'Currency', defaultContent: 'KES' },
                { data: 'sum_insured', name: 'sum_insured', title: 'Sum Insured' },
                { data: 'premium', name: 'premium', title: 'Premium' },
                { data: 'effective_date', name: 'effective_date', title: 'Effective Date' },
                { data: 'closing_date', name: 'closing_date', title: 'Closing Date' },
                { data: 'category', name: 'category', title: 'Category' },
                { data: 'approval_status', name: 'approval_status', title: 'Approval Status', orderable: false },
                { data: 'stage_actions', name: 'stage_actions', title: 'Stage Actions' },
                { data: 'action', orderable: false, searchable: false }
            ]
        };

        this.init();
    }

    /**
     * Initializes the pipeline manager
     */
    init() {
        try {
            this.cacheDOMElements();
            this.setupCSRF();
            this.setupErrorHandling();
            this.initializeChart();
            this.initializeDataTables();
            this.bindEvents();
        } catch (error) {
            this.handleError('Initialization failed', error);
        }
    }

    /**
     * Cache frequently accessed DOM elements
     */
    cacheDOMElements() {
        this.$pipYearSelect = $('#pip_year_select');
        this.$loadingOverlay = $('#loading-overlay');
        this.$errorContainer = $('#error-container');
        this.$errorMessage = $('#error-message');
        this.$chartLoading = $('#chart-loading');
        this.$chartError = $('#chart-error');
        this.$pipelineChart = $('#pipeline-chart');
    }

    /**
     * Sets up CSRF token for AJAX requests
     */
    setupCSRF() {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
    }

    /**
     * Sets up global error handling
     */
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

    /**
     * Initializes the Chartist bar chart
     */
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
                labels: CHART_LABELS,
                series: [DEFAULT_CHART_DATA]
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

    /**
     * Shows the chart loading indicator
     */
    showChartLoading() {
        this.$chartLoading?.removeClass('d-none');
        this.$chartError?.addClass('d-none');
        this.$pipelineChart?.addClass('d-none');
    }

    /**
     * Hides the chart loading indicator
     */
    hideChartLoading() {
        this.$chartLoading?.addClass('d-none');
        this.$pipelineChart?.removeClass('d-none');
    }

    /**
     * Shows the chart error message
     */
    showChartError() {
        this.$chartLoading?.addClass('d-none');
        this.$chartError?.removeClass('d-none');
        this.$pipelineChart?.addClass('d-none');
    }

    /**
     * Loads chart data from the server
     */
    loadChartData() {
        const pipelineId = this.$pipYearSelect?.val();

        if (!pipelineId) {
            this.hideChartLoading();
            return;
        }

        $.ajax({
            url: this.config.routes.chartData,
            method: 'GET',
            data: { pipeline_id: pipelineId },
            timeout: AJAX_TIMEOUT,
            success: (response) => {
                if (response?.data && Array.isArray(response.data)) {
                    this.updateChartData(response.data);
                } else {
                    this.updateChartData(DEFAULT_CHART_DATA);
                }
                this.hideChartLoading();
            },
            error: (xhr, status, error) => {
                this.handleError('Failed to load chart data', { xhr, status, error });
                this.updateChartData(DEFAULT_CHART_DATA);
                this.showChartError();
            }
        });
    }

    /**
     * Updates chart with new data
     *
     * @param {Array<number>} data - Array of 4 numbers representing quarterly data
     */
    updateChartData(data) {
        if (!this.chartInstance) {
            return;
        }

        try {
            if (!Array.isArray(data) || data.length !== 4) {
                data = DEFAULT_CHART_DATA;
            }

            this.chartInstance.update({
                labels: CHART_LABELS,
                series: [data]
            });
        } catch (error) {
            this.handleError('Failed to update chart', error);
            this.chartInstance.update({
                labels: CHART_LABELS,
                series: [DEFAULT_CHART_DATA]
            });
        }
    }

    /**
     * Initializes all DataTables for pipeline data
     */
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
                    const existingTable = $table.DataTable();
                    existingTable.destroy();
                    $table.empty();
                }

                const dataTable = $table.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: this.config.routes.pipelineData,
                        data: (d) => {
                            d.pipeline_id = this.$pipYearSelect?.val();
                            d.quarter = quarter;
                        },
                        error: (xhr, error, code) => {
                            console.error(`DataTable error for ${tableId}:`, {
                                status: xhr.status,
                                error: error,
                                response: xhr.responseText
                            });
                            this.handleAjaxError(xhr, tableId);
                        }
                    },
                    columns: this.config.columnConfig,
                    order: [[0, 'desc']],
                    pageLength: 25,
                    responsive: true,
                    language: {
                        processing: this.getLoadingHTML(),
                        emptyTable: "No pipeline records found",
                        loadingRecords: "Loading...",
                        zeroRecords: "No matching records found"
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

    /**
     * Handles AJAX errors from DataTables
     *
     * @param {Object} xhr - XMLHttpRequest object
     * @param {string} tableId - ID of the table that experienced the error
     */
    handleAjaxError(xhr, tableId) {
        let errorMessage = 'Failed to load data';

        if (xhr.status === 404) {
            errorMessage = 'Data endpoint not found';
        } else if (xhr.status === 500) {
            errorMessage = 'Server error occurred';
        } else if (xhr.status === 403) {
            errorMessage = 'Access denied';
        }

        this.showError(`${errorMessage} for ${tableId}`);
    }

    /**
     * Returns HTML for loading indicator
     *
     * @returns {string} HTML string for loading spinner
     */
    getLoadingHTML() {
        return `
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
    }

    /**
     * Binds all event handlers
     */
    bindEvents() {
        // Pipeline year selection change
        this.$pipYearSelect?.off('change').on('change', () => {
            this.debounce(() => {
                this.loadChartData();
                this.reloadAllTables();
            }, DEBOUNCE_DELAY)();
        });

        // Tab change event
        $('a[data-bs-toggle="tab"]').off('shown.bs.tab').on('shown.bs.tab', (e) => {
            const target = $(e.target).attr("href");
            const tableId = this.getTableIdFromTab(target);

            if (tableId && this.dataTables.has(tableId)) {
                this.dataTables.get(tableId).columns.adjust().draw();
            }
        });

        // Global AJAX error handler
        $(document).off('ajaxError').on('ajaxError', (event, xhr, settings, thrownError) => {
            this.handleError('AJAX Error', {
                url: settings.url,
                status: xhr.status,
                error: thrownError
            });
        });
    }

    /**
     * Initializes action button handlers for pipeline operations
     */
    initializeActionHandlers() {
        // Remove previous handlers
        $('.stage_btn_action').off('click.pipeline');
        $('.del_opp_sales').off('click.pipeline');
        $('.update_category_action').off('click.pipeline');
        $('.mail-btn').off('click.pipeline');
        $('.preview-pdf-btn').off('click.pipeline');
        $('.revert-pipeline').off('click.pipeline');

        // Attach new handlers
        $('.stage_btn_action').on('click.pipeline', (e) => {
            e.preventDefault();
            this.handleStageAction(e.currentTarget);
        });

        $('.update_category_action').on('click.pipeline', (e) => {
            e.preventDefault();
            this.handleCategoryUpdate(e.currentTarget);
        });

        $('.del_opp_sales').on('click.pipeline', (e) => {
            e.preventDefault();
            this.handleDelPipeline(e.currentTarget);
        });

        $('.mail-btn').on('click.pipeline', (e) => {
            e.preventDefault();
            this.handleSendBDNotification(e.currentTarget);
        });

        $('.revert-pipeline').on('click.pipeline', (e) => {
            e.preventDefault();
            this.handleRevertPipeline(e.currentTarget);
        });

        $('.preview-pdf-btn').on('click.pipeline', (e) => {
            e.preventDefault();
            this.handlePdfPreview(e.currentTarget);
        });
    }

    /**
     * Handles stage transition button clicks
     *
     * @param {HTMLElement} button - The clicked button element
     */
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

            if (!rowData?._original) {
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
                sum_insured_type: _original.sum_insured_type,
                risk_type: _original.risk_type,
                cedant: _original.cedant,
                last_updated: _original.last_updated,
            };

            window.currentDealInfo = dealInfo;
            const dealCurrentStage = buttonData.current_stage || rowData.status;
            this.currentStage = dealCurrentStage;

            const stageInfo = this.config.stageFlow[this.currentStage];

            if (!stageInfo) {
                throw new Error(`Invalid stage: ${this.currentStage}`);
            }

            const nextStage = stageInfo.next;
            const modalId = stageInfo.modalId;
            if (nextStage) {
                this.openStageModal(nextStage, modalId, this.currentDealId, dealInfo);
            }

            this.hideLoading();
        } catch (error) {
            this.handleError("Error handling stage action", error);
            this.hideLoading();
        }
    }

    /**
     * Handles category update button clicks
     *
     * @param {HTMLElement} button - The clicked button element
     */
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

    /**
     * Handles pipeline deletion
     *
     * @param {HTMLElement} button - The clicked button element
     */
    handleDelPipeline(button) {
        try {
            const buttonData = $(button).data();
            if (!buttonData.opp_id) {
                throw new Error('Opportunity ID not found');
            }

            const $row = $(button).closest("tr");
            const $table = $row.closest("table");
            const tableId = $table.attr("id");

            let insuredName = '';
            if (this.dataTables.has(tableId)) {
                const dataTable = this.dataTables.get(tableId);
                const rowData = dataTable.row($row).data();
                if (rowData?.insured_name) {
                    insuredName = rowData.insured_name;
                }
            }

            Swal.fire({
                title: 'Remove from Sales?',
                html: `Are you sure you want to delete this from sales.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    cancelButton: 'btn btn-sm btn-light me-2',
                    confirmButton: 'btn btn-danger btn-sm',
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deletePipeline(buttonData.opportunity_id, insuredName);
                }
            });
        } catch (error) {
            this.handleError('Error handling pipeline deletion', error);
        }
    }

    /**
     * Handles pipeline reversion
     *
     * @param {HTMLElement} button - The clicked button element
     */
    handleRevertPipeline(button) {
        try {
            const buttonData = $(button).data();
            const opportunityId = buttonData.opportunityId;
            if (!opportunityId) {
                throw new Error('Opportunity ID not found');
            }

            const $row = $(button).closest("tr");
            const $table = $row.closest("table");
            const tableId = $table.attr("id");

            let insuredName = '';
            if (this.dataTables.has(tableId)) {
                const dataTable = this.dataTables.get(tableId);
                const rowData = dataTable.row($row).data();
                if (rowData?.insured_name) {
                    insuredName = rowData.insured_name;
                }
            }

            const currentStage = buttonData.current_stage ? buttonData.current_stage.toLowerCase() : '';
            const stage = this.config.stageFlow[currentStage];
            const revertStage = stage?.previous ? this.capitalize(stage.previous) : null;

            Swal.fire({
                title: 'Revert Pipeline Stage?',
                html: `
                    <p>
                        Are you sure you want to revert <strong>${this.escapeHtml(insuredName) || 'this opportunity'}</strong>
                        back to a previous pipeline stage <strong>${this.escapeHtml(revertStage)}?</strong>
                    </p>
                    <p class="text-muted mb-0">This action will update its current sales stage accordingly.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, revert it',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    cancelButton: 'btn btn-sm btn-light me-2',
                    confirmButton: 'btn btn-primary btn-sm',
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    this.revertPipeline(opportunityId, insuredName);
                }
            });
        } catch (error) {
            this.handleError('Error handling pipeline revert', error);
        }
    }

    /**
     * Reverts pipeline to previous stage
     *
     * @param {number} dealId - The deal/opportunity ID
     * @param {string} insuredName - Name of the insured party
     */
    revertPipeline(dealId, insuredName) {
        $.ajax({
            type: 'POST',
            url: window.pipelineRoutes?.revert || '',
            data: {
                'prospect_id': dealId,
                'revert_to_sales': 1
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.status == 1) {
                    toastr.success(response.message);
                    this.reloadAllTables();
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                Swal.fire({
                    title: "Error",
                    text: textStatus,
                    icon: "error"
                });
            }
        });
    }

    /**
     * Deletes pipeline entry
     *
     * @param {number} dealId - The deal/opportunity ID
     * @param {string} insuredName - Name of the insured party
     */
    deletePipeline(dealId, insuredName) {
        $.ajax({
            type: 'POST',
            url: window.pipelineRoutes?.addPipeline || '',
            data: {
                'prospect': dealId,
                'revert_to_sales': true
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.status == 1) {
                    toastr.success(response.message);
                    this.reloadAllTables();
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                Swal.fire({
                    title: "Error",
                    text: textStatus,
                    icon: "error"
                });
            }
        });
    }

    /**
     * Opens stage modal for pipeline transitions
     *
     * @param {string} stage - The target stage
     * @param {string} modalId - ID of the modal to open
     * @param {number} dealId - The deal/opportunity ID
     * @param {Object} dealInfo - Deal information object
     */
    openStageModal(stage, modalId, dealId, dealInfo = null) {
        try {
            this.currentDealId = dealId;
            const $modal = $(`#${modalId}`);

            if ($modal.length === 0) {
                throw new Error(`Modal not found: ${modalId}`);
            }

            const data = {
                dealId: dealId,
                opportunityId: dealInfo.id,
                typeOfBus: dealInfo?.type_of_business,
                modalId: modalId,
                class: dealInfo?.class,
                classGroup: dealInfo?.class_group,
                stage: stage,
                categoryType: dealInfo?.category_type,
                sumInsuredType: dealInfo?.sum_insured_type,
                riskType: dealInfo?.risk_type,
            };

            if (this.currentStage === STAGE_NAMES.LEAD) {
                this.loadBdTerms(data);
            } else if (this.currentStage === STAGE_NAMES.PROPOSAL) {
                this.loadSelectedReinsurers(data);
            }

            this.loadSlipDocuments(data);
            this.loadScheduleHeaders(data);
            this.populateModalData(modalId, dealId, this.currentStage, dealInfo);

            $modal.modal('show');
            $modal.addClass('slide-in');

            this.addEscapeKeyListener();
        } catch (error) {
            this.handleError("Error opening modal", error);
        }
    }

    /**
     * Populates modal with deal information
     *
     * @param {string} modalId - ID of the modal
     * @param {number} dealId - The deal/opportunity ID
     * @param {string} stage - Current stage
     * @param {Object} dealInfo - Deal information object
     */
    populateModalData(modalId, dealId, stage, dealInfo = null) {
        try {
            const $modal = $(`#${modalId}`);
            if (!dealInfo) {
                return;
            }

            $modal.find('.slip-display').text(dealInfo.id || '');

            if (dealInfo.created_at) {
                try {
                    const dateObj = new Date(dealInfo.created_at);
                    const options = { year: 'numeric', month: 'long', day: 'numeric' };
                    const formattedDate = dateObj.toLocaleDateString('en-US', options);
                    $modal.find('.created_at-display').text(formattedDate);
                } catch (dateError) {
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

            // XSS Protection: User data is inserted into text nodes, not HTML
            $modal.find('.insured-name-display').text(dealInfo.insured_name || '');
            $modal.find('.insured-email-display').text(dealInfo.insured_email || '--');
            $modal.find('.insured-phone-display').text(dealInfo.insured_phone || '--');
            $modal.find('.insured-contact-name-display').text(dealInfo.contact_name || '--');
            $modal.find('.sum_insured_type').text(`(${dealInfo.sum_insured_type})` || '');

            $modal.find('.opportunity_id').val(dealInfo.id);
            $modal.find('.current_stage').val(stage);
            $modal.find('.cedant_id').val(dealInfo.cedant.customer_id);

            $modal.find('.total_sum_insured').val(dealInfo.total_sum_insured || '0.00');
            $modal.find('.premium').val(dealInfo.premium || '0.00');
            $modal.find('.brokerage_rate').val(dealInfo.brokerage_rate || '0.00');
            $modal.find('.total_reinsurer_share').val(dealInfo.written_share || '0.00');
            $modal.find('.class_code').val(dealInfo.class || '');
            $modal.find('.class_group_code').val(dealInfo.class_group || '');

            const $riskType = $modal.find('.risk_type');
            if ($riskType.length > 0) {
                $riskType.val(dealInfo?.risk_type || '');
            }

            const $cedantName = $modal.find('.cedant_name');
            if ($cedantName.length > 0) {
                $cedantName.text(dealInfo?.cedant?.name || '');
            }

            const $lastContactDate = $modal.find('.last_contact_date');
            if ($lastContactDate.length > 0) {
                $lastContactDate.val(dealInfo?.last_updated || '');
            }

            const $cedant = $modal.find('.add_cedant_contacts');
            if ($cedant.length > 0) {
                $cedant.attr('data-cedant-id', dealInfo.cedant.customer_id);
                $cedant.attr('data-cedant-name', dealInfo.cedant.name);
                $cedant.attr('data-opportunity-id', dealInfo.id);
            }

            $("#reinSelectionPlacement").hide();
        } catch (error) {
            this.handleError("Error populating modal data", error);
        }
    }

    /**
     * Loads schedule headers for the deal
     *
     * @param {Object} data - Data object containing deal information
     */
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
                this.handleError('Error loading schedule headers', { xhr, status, error });
                this.showError('Failed to load schedule headers');
            }
        });
    }

    /**
     * Loads selected reinsurers for proposal stage
     *
     * @param {Object} data - Data object containing deal information
     */
    loadSelectedReinsurers(data) {
        if (!data.dealId || !data.class || !data.classGroup) {
            return;
        }

        const $modal = $(`#${data.modalId}`);
        const $table = $modal.find('#propReinsurersTable');

        if (!$.fn.DataTable.isDataTable($table)) {
            this.showTableLoading($table);
        }

        $.ajax({
            url: this.config.routes.getSelectedReinsurers,
            method: 'GET',
            data: {
                opportunity_id: data.opportunityId,
            },
            success: (response) => {
                if (response.success) {
                    $modal.find('#reinsurerCount').text(response.count ?? 0);
                    const reinsurers = response.data.length > 0 ? response.data : [];

                    $modal.find('.selected_reinsurers').val(JSON.stringify(reinsurers));
                    this.renderReinsurersTable(reinsurers, $table, data);
                }
            },
            error: (xhr, status, error) => {
                this.handleError('Error loading selected reinsurers', { xhr, status, error });
                this.showError('Failed to load selected reinsurers');
                this.renderReinsurersTable([], $table, data);
            }
        });
    }

    /**
     * Renders reinsurers DataTable
     *
     * @param {Array} reinsurers - Array of reinsurer objects
     * @param {jQuery} $table - jQuery table element
     * @param {Object} data - Data object containing deal information
     */
    renderReinsurersTable(reinsurers, $table, data) {
        if (!$table || $table.length === 0) {
            return;
        }

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().destroy();
        }

        $table.find('tbody').empty();

        const tableData = this.transformReinsurerData(reinsurers);
        const totals = this.calculateTotals(tableData);

        const dataTable = $table.DataTable({
            data: tableData,
            columns: this.getReinsurerColumns(),
            paging: false,
            searching: false,
            info: false,
            ordering: true,
            order: [[1, 'desc']],
            language: {
                emptyTable: "No reinsurers selected"
            },
            drawCallback: () => {
                this.initializeReinsurerActions($table);
            }
        });

        this.reinsurerDataTable = dataTable;
    }

    /**
     * Transforms reinsurer data for DataTable
     *
     * @param {Array} reinsurers - Array of reinsurer objects
     * @returns {Array} Transformed data array
     */
    transformReinsurerData(reinsurers) {
        return reinsurers.map((reinsurer) => {
            return {
                id: reinsurer.reinsurer_id,
                name: reinsurer.reinsurer_name,
                written_share: parseFloat(0).toFixed(2),
                previous_written_share: parseFloat(reinsurer.written_share || 0).toFixed(2),
                commission: parseFloat(reinsurer.brokerage_rate || 0).toFixed(2),
                status: reinsurer.status,
                country: reinsurer.country,
                contact: reinsurer.email || '-',
                action: ''
            };
        });
    }

    /**
     * Calculates totals for reinsurer table
     *
     * @param {Array} tableData - Array of reinsurer data
     * @returns {Object} Object with totalShare and totalCommission
     */
    calculateTotals(tableData) {
        const totalShare = tableData.reduce((sum, r) => sum + parseFloat(r.written_share), 0);
        const totalCommission = tableData.reduce((sum, r) => sum + parseFloat(r.commission), 0);

        return { totalShare, totalCommission };
    }

    /**
     * Gets column configuration for reinsurer DataTable
     *
     * @returns {Array} Array of column configuration objects
     */
    getReinsurerColumns() {
        return [
            {
                data: 'name',
                title: 'Reinsurer',
                render: (data, type, row) => {
                    // XSS Protection: Escape HTML in reinsurer name and contact
                    const escapedName = this.escapeHtml(data);
                    const escapedContact = this.escapeHtml(row.contact);
                    return `
                        <div class="d-flex flex-start">
                            <div>
                                <div class="fw-medium">${escapedName}</div>
                                <small class="text-muted">(${escapedContact})</small>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'written_share',
                title: 'Written Share (%)',
                className: 'text-left',
                render: (data, type, row) => {
                    const percentage = parseFloat(data);
                    const badgeClass = this.getShareBadgeClass(percentage);
                    // XSS Protection: Numeric values are safe, but ID and name need escaping
                    const escapedName = this.escapeHtml(row.name);
                    return `
                        <span>
                            <span class="badge bg-success">${data}%</span>
                            <span class="badge bg-secondary edit-reinsurer-btn"
                                data-reinsurer-id="${row.id}"
                                data-reinsurer-name="${escapedName}"
                                data-previous-written-share="${row.previous_written_share}"
                                data-written-share="${row.written_share}"
                                style="margin-right: 0.25rem; cursor: pointer;"
                                title="Edit Written Share">
                                <i class="bx bx-edit"></i>
                            </span>
                        </span>
                    `;
                }
            },
            {
                data: 'action',
                title: 'Action',
                orderable: false,
                searchable: false,
                className: 'text-left',
                render: (data, type, row) => {
                    // XSS Protection: Escape reinsurer name
                    const escapedName = this.escapeHtml(row.name);
                    return `
                        <div>
                            <button type="button" class="btn btn-primary btn-sm contact-reinsurer-btn"
                                data-reinsurer-id="${row.id}"
                                title="Contacts">
                                <i class="bx bx-book"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm decline-reinsurer-btn"
                                data-reinsurer-id="${row.id}"
                                data-reinsurer-name="${escapedName}"
                                style="padding: 3px; margin-right: 0.25rem;"
                                title="Decline">
                                <i class="bx bx-x vx-f"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ];
    }

    /**
     * Gets badge class based on percentage
     *
     * @param {number} percentage - Share percentage
     * @returns {string} Bootstrap badge class
     */
    getShareBadgeClass(percentage) {
        if (percentage >= 50) return 'bg-success';
        if (percentage >= 25) return 'bg-primary';
        return 'bg-info';
    }

    /**
     * Shows loading indicator for table
     *
     * @param {jQuery} $table - jQuery table element
     */
    showTableLoading($table) {
        const $tbody = $table.find('tbody');
        $tbody.html(`
            <tr>
                <td colspan="3" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading reinsurers...</p>
                </td>
            </tr>
        `);
    }

    /**
     * Initializes event handlers for reinsurer actions
     *
     * @param {jQuery} $table - jQuery table element
     */
    initializeReinsurerActions($table) {
        $table.off('click', '.edit-reinsurer-btn');
        $table.off('click', '.decline-reinsurer-btn');
        $table.off('click', '.contact-reinsurer-btn');

        $table.on('click', '.edit-reinsurer-btn', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const reinsurerData = {
                id: $(e.currentTarget).data('reinsurer-id'),
                reinsurerName: $(e.currentTarget).data('reinsurer-name'),
                written_share: $(e.currentTarget).data('written-share'),
                previous_written_share: $(e.currentTarget).data('previous-written-share')
            };

            this.handleEditReinsurer(reinsurerData, $table);
        });

        $table.on('click', '.decline-reinsurer-btn', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const reinsurerData = {
                id: $(e.currentTarget).data('reinsurer-id'),
                reinsurerName: $(e.currentTarget).data('reinsurer-name')
            };

            this.handleDeclineReinsurer(reinsurerData, $table);
        });

        $table.on('click', '.contact-reinsurer-btn', (e) => {
            e.preventDefault();
            e.stopPropagation();
        });
    }

    /**
     * Handles declining a reinsurer
     *
     * @param {Object} reinsurerData - Reinsurer data object
     * @param {jQuery} $table - jQuery table element
     */
    handleDeclineReinsurer(reinsurerData, $table) {
        if ($('#declineReinsurerModal').length === 0) {
            // XSS Protection: Escape reinsurer name when creating modal
            const escapedName = this.escapeHtml(reinsurerData.reinsurerName);
            const modalHtml = `
                <div class="modal fade mod-popup effect-scale" id="declineReinsurerModal" tabindex="-1" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body pt-2 pb-3">
                                <h5 class="modal-title w-100 text-center">Decline Reinsurer</h5>
                                <small class="text-center w-100 text-muted align-items-center d-flex justify-content-center">(${escapedName})</small>
                                <div class="form-group d-flex flex-column justify-content-center align-items-center">
                                    <label for="reinDecTxt" class="form-label text-muted mb-3">Decline Reason</label>
                                    <textarea
                                        class="form-control"
                                        id="reinDecTxt"
                                        placeholder="Enter reason for declining"
                                        rows="4"
                                        style="width: 100%; font-size: 15px; resize: none;"
                                    ></textarea>
                                    <div class="invalid-feedback" id="declineReasonError"></div>
                                </div>
                            </div>
                            <div class="p-3 m-3 modal-footer border-0 justify-content-center">
                                <button type="button" class="btn btn-danger px-4" id="confirmDecline">Decline</button>
                                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
        }

        $('#reinDecTxt').val('').removeClass('is-invalid');
        $('#declineReasonError').text('');

        $('#declineReinsurerModal').modal('show');

        $('#confirmDecline').off('click').on('click', () => {
            const declineReason = $('#reinDecTxt').val().trim();
            const $textarea = $('#reinDecTxt');
            const $error = $('#declineReasonError');

            if (!declineReason) {
                $textarea.addClass('is-invalid');
                $error.text('Please provide a reason for declining');
                return;
            }

            const data = {
                reinsurerId: reinsurerData.id,
                opportunityId: $('#proposalModal').find(".opportunity_id").val()
            };

            $.ajax({
                url: this.config.routes.declineReinsurer,
                method: 'POST',
                data: {
                    reinsurerId: data.reinsurerId,
                    opportunityId: data.opportunityId,
                    declineReason: declineReason
                },
                success: (response) => {
                    if (response.success) {
                        toastr.success('Reinsurer declined successfully');
                        this.loadSelectedReinsurers({
                            dealId: data.opportunityId,
                            opportunityId: data.opportunityId,
                            modalId: 'proposalModal'
                        });
                    }
                },
                error: (xhr, status, error) => {
                    this.handleError('Error declining reinsurer', { xhr, status, error });
                    this.showError('Failed to decline reinsurer');
                },
                complete: () => {
                    $('#declineReinsurerModal').modal('hide');
                }
            });
        });
    }

    /**
     * Handles editing reinsurer share
     *
     * @param {Object} reinsurerData - Reinsurer data object
     * @param {jQuery} $table - jQuery table element
     */
    handleEditReinsurer(reinsurerData, $table) {
        if ($('#editReinsurerShareModal').length === 0) {
            // XSS Protection: Escape reinsurer name
            const escapedName = this.escapeHtml(reinsurerData.reinsurerName);
            const modalHtml = `
                <div class="modal fade mod-popup effect-scale" id="editReinsurerShareModal" tabindex="-1" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body pt-2 pb-3">
                                <h5 class="modal-title w-100 text-center">Edit Written Share</h5>
                                <small class="text-center w-100 text-muted align-items-center d-flex justify-content-center">(${escapedName})</small>
                                <div class="form-group d-flex flex-direction-column justify-content-center align-items-center">
                                    <label for="editShareInput" class="form-label text-muted mb-3" style="margin-left: -24px;">Written Share (%)</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="editShareInput"
                                        min="0.01"
                                        max="100"
                                        step="0.01"
                                        placeholder="50.00"
                                        style="width: 150px; font-size: 15px;"
                                    >
                                    <div class="invalid-feedback" id="shareInputError"></div>
                                </div>
                            </div>
                            <div class="p-3 m-3 modal-footer border-0 justify-content-center">
                                <button type="button" class="btn btn-success px-4" id="confirmShareUpdate">Update</button>
                                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
        }

        $('#editShareInput').val(reinsurerData.previous_written_share);
        $('#editShareInput').removeClass('is-invalid');
        $('#shareInputError').text('');

        const editModal = new bootstrap.Modal(document.getElementById('editReinsurerShareModal'));
        editModal.show();

        $('#confirmShareUpdate').off('click').on('click', () => {
            const value = $('#editShareInput').val();
            const numValue = parseFloat(value);

            if (value === '' || isNaN(numValue)) {
                $('#editShareInput').addClass('is-invalid');
                $('#shareInputError').text('Please enter a value');
                return;
            }

            if (numValue <= 0 || numValue > 100) {
                $('#editShareInput').addClass('is-invalid');
                $('#shareInputError').text('Please enter a valid share between 0.01 and 100');
                return;
            }

            this.updateReinsurerShare(reinsurerData.id, value, $table);
            editModal.hide();
        });

        $('#editReinsurerShareModal').on('shown.bs.modal', function() {
            $('#editShareInput').focus().select();
        });

        $('#editReinsurerShareModal').on('hidden.bs.modal', function() {
            $('#confirmShareUpdate').off('click');
        });
    }

    /**
     * Handles removing a reinsurer
     *
     * @param {number} reinsurerId - Reinsurer ID
     * @param {jQuery} $table - jQuery table element
     */
    handleRemoveReinsurer(reinsurerId, $table) {
        Swal.fire({
            title: 'Remove Reinsurer?',
            text: 'Are you sure you want to remove this reinsurer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.removeReinsurer(reinsurerId, $table);
            }
        });
    }

    /**
     * Updates reinsurer share in DataTable
     *
     * @param {number} reinsurerId - Reinsurer ID
     * @param {string} newShare - New share value
     * @param {jQuery} $table - jQuery table element
     */
    updateReinsurerShare(reinsurerId, newShare, $table) {
        const dataTable = $table.DataTable();
        const rowData = dataTable.rows().data().toArray();

        const updatedData = rowData.map(row => {
            if (row.id === reinsurerId) {
                return {
                    ...row,
                    written_share: parseFloat(newShare).toFixed(2)
                };
            }
            return row;
        });

        dataTable.clear();
        dataTable.rows.add(updatedData);
        dataTable.draw();

        const totalShare = updatedData.reduce((sum, r) => sum + parseFloat(r.written_share), 0);
        this.updatePlacedShare(totalShare);

        toastr.success('Reinsurer share updated successfully');
    }

    /**
     * Updates placed share total
     *
     * @param {number} totalShare - Total share value
     */
    updatePlacedShare(totalShare) {
        // Implementation placeholder for updating placed share display
    }

    /**
     * Removes reinsurer from DataTable
     *
     * @param {number} reinsurerId - Reinsurer ID
     * @param {jQuery} $table - jQuery table element
     */
    removeReinsurer(reinsurerId, $table) {
        const dataTable = $table.DataTable();
        const rowData = dataTable.rows().data().toArray();

        const updatedData = rowData.filter(row => row.id !== reinsurerId);

        dataTable.clear();
        dataTable.rows.add(updatedData);
        dataTable.draw();

        const totalShare = updatedData.reduce((sum, r) => sum + parseFloat(r.written_share), 0);

        const $counterBadge = $('#reinsurerCount');
        if ($counterBadge.length) {
            $counterBadge.text(updatedData.length);
        }

        if (typeof toastr !== 'undefined') {
            toastr.success('Reinsurer removed successfully');
        }
    }

    /**
     * Loads BD terms for lead stage
     *
     * @param {Object} data - Data object containing deal information
     */
    loadBdTerms(data) {
        if (!data.dealId || !data.class || !data.classGroup) {
            return;
        }

        $.ajax({
            url: this.config.routes.getBdTerms,
            method: 'GET',
            data: {
                opportunity_id: data.opportunityId,
            },
            success: (response) => {
                if (response.success) {
                    this.renderBdTerms(response.data, data);
                }
            },
            error: (xhr, status, error) => {
                this.handleError('Error loading BD terms', { xhr, status, error });
                this.showError('Failed to load BD terms');
            }
        });
    }

    /**
     * Renders BD terms in modal
     *
     * @param {Array} data - Array of term objects
     * @param {Object} dealInfo - Deal information object
     */
    renderBdTerms(data, dealInfo) {
        const $modal = $(`#${dealInfo.modalId}`);

        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                const v = data[i];

                const title = v.title;
                const content = v.content;
                const short_content = v.short_content;

                const plainText = $('<div>').html(short_content).text();

                $(`#${title}`).val(plainText);
                $(`#${title}Content`).val(content);
            }
        }
    }

    /**
     * Loads slip documents for the current stage
     *
     * @param {Object} data - Data object containing deal information
     */
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
                category_type: data.categoryType,
            },
            success: (response) => {
                if (response.status) {
                    if ($documentsSubtitle.length > 0) {
                        $documentsSubtitle.html(
                            `<small>Documents for ${response.class_name || 'this class'}</small>`
                        );
                    }
                    this.renderSlipDocuments(response, data, $modal);
                } else {
                    if ($documentsSubtitle.length > 0) {
                        $documentsSubtitle.html(`<small>No documents found</small>`);
                    }
                }
            },
            error: (xhr, status, error) => {
                this.handleError('Error loading slip documents', { xhr, status, error });
                this.showError('Failed to load slip documents');
                if ($documentsSubtitle.length > 0) {
                    $documentsSubtitle.html(`<small>Error loading documents</small>`);
                }
            }
        });
    }

    /**
     * Renders schedule headers in modal
     *
     * @param {Array} headers - Array of header objects
     * @param {Object} data - Data object containing deal information
     */
    renderScheduleHeaders(headers, data) {
        if (!data?.modalId) {
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

        const validHeaders = headers.filter(h => {
            if (!h) {
                return false;
            }

            const hasValidSumInsuredType = h.sum_insured_type?.trim() === '';
            const isNotExcluded = !EXCLUDED_TERMS.some(term =>
                h.name?.replace(/\s+/g, ' ').trim().includes(term)
            );

            return hasValidSumInsuredType && isNotExcluded;
        });

        validHeaders.sort((a, b) => {
            const positionA = parseInt(a.position) || 0;
            const positionB = parseInt(b.position) || 0;
            return positionA - positionB;
        });

        const deductible = headers.filter(h =>
            DEDUCTIBLE_TERMS.some(term =>
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

            let headerName = this.capitalize(header.name);
            const fieldId = this.toPascalCase(header.name);
            let fieldInput = this.generateFieldInput(header, fieldId);

            let hiddenInput = `<input type="hidden" id="${fieldId}Content" name="${fieldId}Content" />`;

            fieldsHtml += `
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="${fieldId}" class="form-label capitalize">
                            ${headerName}${header.amount_field === 'Y' ? ' <span class="text-danger pl-1">*</span>' : ''}
                        </label>
                        ${fieldInput}
                        ${hiddenInput}
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

    /**
     * Converts text to Pascal case
     *
     * @param {string} rawTxt - Raw text to convert
     * @returns {string} Pascal case string
     */
    toPascalCase(rawTxt) {
        return rawTxt
            .replace(/['"]/g, '')
            .split(/\s+/)
            .map((word, index) => {
                let clean = word.toLowerCase();
                if (index === 0) {
                    return clean;
                }
                return clean.charAt(0).toUpperCase() + clean.slice(1);
            })
            .join('');
    }

    /**
     * Renders slip documents in modal
     *
     * @param {Object} res - Response object containing documents
     * @param {Object} data - Data object containing deal information
     * @param {jQuery} $modal - jQuery modal element
     */
    renderSlipDocuments(res, data, $modal) {
        if (!res.docs || !res.docs.length) {
            const $container = $modal.find('#documentsContent');
            if ($container.length) {
                $container.html(
                    '<p class="text-muted text-center my-3">No documents available for this stage.</p>');
            }
            return;
        }

        let docs = res.docs;
        docs.push({
            name: 'Additional Documents',
            id: Math.floor(Math.random() * 10000),
            file_name: 'additionalDocs',
            doc_type: 'Additional Documents',
            mandatory: 'N',
            icon: 'bx-folder-plus',
            accepts: '.pdf,.doc,.docx,.jpg,.jpeg,.png',
            description: 'Any additional supporting documents',
            max_size: 5242880,
            multiple: true
        });

        const transformedDocs = docs.map((doc) => ({
            id: doc.id,
            name: doc.name || doc.doc_type,
            doc_type: doc.doc_type,
            file_name: doc.file_name,
            required: doc.mandatory === 'Y',
            icon: doc.icon ?? 'bx-file-blank',
            accepts: doc.mimetype ?? '.pdf,.doc,.docx,.jpg,.jpeg,.png',
            description: doc.description ?? '',
            max_size: doc.max_size ?? DEFAULT_MAX_FILE_SIZE,
            multiple: doc.multiple ?? true
        }));

        this.generateDocumentFields(transformedDocs, $modal);
    }

    /**
     * Generates document upload fields
     *
     * @param {Array} documents - Array of document objects
     * @param {jQuery} $modal - jQuery modal element
     */
    generateDocumentFields(documents, $modal) {
        const $container = $modal.find('#documentFields');
        const $placeholder = $modal.find('#documentPlaceholder');
        const $summarySection = $modal.find('#documentSummarySection');

        if ($container.length === 0) {
            return;
        }

        if ($placeholder.length) $placeholder.hide();
        $container.empty().show();

        if (!documents || documents.length === 0) {
            $container.html(`
                <div class="col-12 text-center py-4">
                    <i class="bx bx-info-circle bx-2x text-muted mb-2"></i>
                    <p class="text-muted">No specific documents required for this insurance class.</p>
                </div>
            `);
            if ($summarySection.length) $summarySection.hide();
            return;
        }

        documents.forEach((doc, index) => {
            const colSize = documents.length <= 2 ? 'col-12' : 'col-md-6';
            const maxSizeText = this.formatFileSize(doc.max_size || DEFAULT_MAX_FILE_SIZE);
            const acceptsText = doc.accepts.replace(/\./g, '').toUpperCase();

            // XSS Protection: Escape document name and description
            const escapedName = this.escapeHtml(doc.name);
            const escapedDescription = this.escapeHtml(doc.description);

            const fieldHtml = `
                <div class="${colSize} fade-in" style="animation-delay: ${index * 0.1}s">
                    <div class="document-field-group">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="bx ${doc.icon} me-2"></i>
                                ${escapedName}
                                ${doc.required ? '<span class="required-indicator text-danger">*</span>' : ''}
                            </label>
                            <div class="file-upload-area border rounded p-3 text-center" data-field="${doc.id}" data-field_name="${escapedName}">
                                <i class="bx ${doc.icon} upload-icon fs-2 text-muted"></i>
                                <div class="upload-text fw-semibold">${escapedName}</div>
                                <div class="upload-subtext text-muted small">${escapedDescription}</div>
                                <input type="file" class="d-none file-input"
                                    name="${doc.file_name}"
                                    ${doc.required ? 'required' : ''}
                                    accept="${doc.accepts}"
                                    ${doc.multiple ? 'multiple' : ''}
                                    data-max-size="${doc.max_size || DEFAULT_MAX_FILE_SIZE}">
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
            $modal.find('#docCount').text(documents.length);
        }

        this.initializeFileUploads();

        if ($summarySection.length) $summarySection.show();
    }

    /**
     * Initializes file upload handlers
     */
    initializeFileUploads() {
        $('.file-upload-area').off('.fileUpload');
        $('.file-input').off('.fileUpload');

        $('.file-upload-area').each((index, element) => {
            const $uploadArea = $(element);
            const $input = $uploadArea.find('.file-input');
            const $previewContainer = $uploadArea.siblings('.file-preview-container');

            $uploadArea.on('click.fileUpload', (e) => {
                const $target = $(e.target);
                const isInteractiveElement = $target.is(
                    'button, input, a, .file-action-btn, .file-remove-btn') ||
                    $target.closest('button, .file-action-btn, .file-remove-btn').length > 0;

                if (!isInteractiveElement) {
                    e.preventDefault();
                    e.stopPropagation();

                    if ($input.length > 0 && $input[0]) {
                        $input[0].click();
                    }
                }
            });

            $input.on('change.fileUpload', (e) => {
                e.stopPropagation();
                if (e.target.files && e.target.files.length > 0) {
                    this.handleFileSelection(e.target.files, $uploadArea, $previewContainer);
                }

                e.target.value = '';
            });

            $uploadArea.on('dragover.fileUpload dragenter.fileUpload', (e) => {
                e.preventDefault();
                e.stopPropagation();
                $uploadArea.addClass('drag-over border-primary');
            });

            $uploadArea.on('dragleave.fileUpload', (e) => {
                e.preventDefault();
                e.stopPropagation();
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
                    this.handleFileSelection(files, $uploadArea, $previewContainer);
                }
            });
        });
    }

    /**
     * Handles file selection from upload
     *
     * @param {FileList} files - Selected files
     * @param {jQuery} $uploadArea - jQuery upload area element
     * @param {jQuery} $previewContainer - jQuery preview container element
     */
    handleFileSelection(files, $uploadArea, $previewContainer) {
        if (!files || files.length === 0) {
            return;
        }

        const fieldId = $uploadArea.data('field');
        const fieldName = $uploadArea.data('field_name');
        const maxSize = parseInt($uploadArea.find('.file-input').data('max-size')) || DEFAULT_MAX_FILE_SIZE;

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
            const fileName = this.toPascalCase(fieldName);

            const fileWithId = Object.assign(file, {
                fileId,
                fileName
            });

            this.uploadedFiles[fieldId].push(fileWithId);
            validFiles++;

            this.createFilePreview(fileWithId, $previewContainer, fieldId);
        });

        this.updateFileCountBadge($uploadArea, fieldId);
    }

    /**
     * Updates file count badge
     *
     * @param {jQuery} $uploadArea - jQuery upload area element
     * @param {string} fieldId - Field ID
     */
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

    /**
     * Creates file preview element
     *
     * @param {File} file - File object
     * @param {jQuery} $container - jQuery container element
     * @param {string} fieldId - Field ID
     */
    createFilePreview(file, $container, fieldId) {
        const fileId = file.fileId || `file_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        const fileSize = this.formatFileSize(file.size);
        const fileName = file.name || 'Unknown file';

        if ($container.find(`[data-file-id="${fileId}"]`).length > 0) {
            return;
        }

        if (!file.fileId) {
            file.fileId = fileId;
        }

        // XSS Protection: Escape file name
        const escapedFileName = this.escapeHtml(fileName);

        const previewHtml = `
            <div class="file-preview-item d-flex align-items-center justify-content-between p-2 border rounded mb-2" data-file-id="${fileId}">
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="bx bx-file me-2 text-primary"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-truncate" style="max-width: 400px;" title="${escapedFileName}">${escapedFileName}</div>
                        <div class="small text-muted">${fileSize}</div>
                    </div>
                </div>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-primary file-view-btn"
                        data-field="${fieldId}"
                        data-file-id="${fileId}"
                        title="View file">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger file-remove-btn"
                        data-field="${fieldId}"
                        data-file-id="${fileId}"
                        title="Remove file">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
            </div>
        `;

        $container.append(previewHtml);

        const $removeBtn = $container.find(`[data-file-id="${fileId}"] .file-remove-btn`);
        $removeBtn.off('click.fileRemove').on('click.fileRemove', (e) => {
            e.stopPropagation();
            e.preventDefault();
            this.removeFile(fieldId, fileId);
        });

        const $viewBtn = $container.find(`[data-file-id="${fileId}"] .file-view-btn`);
        $viewBtn.off('click.fileView').on('click.fileView', (e) => {
            e.stopPropagation();
            e.preventDefault();
            this.viewFile(fieldId, fileId);
        });
    }

    /**
     * Removes file from upload
     *
     * @param {string} fieldId - Field ID
     * @param {string} fileId - File ID
     */
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

                if (newLength === 0) {
                    const $fileInput = $uploadArea.find('.file-input');
                    if ($fileInput.length > 0) {
                        $fileInput.val('');
                    }
                }
            }
        } catch (error) {
            this.handleError('Error removing file', error);
        }
    }

    /**
     * Views uploaded file
     *
     * @param {string} fieldId - Field ID
     * @param {string} fileId - File ID
     */
    viewFile(fieldId, fileId) {
        try {
            const fileToView = this.uploadedFiles[fieldId]?.find(f => f.fileId === fileId);

            if (!fileToView) {
                this.showToast('error', 'File not found');
                return;
            }

            const fileExtension = fileToView.name.split('.').pop().toLowerCase();
            const fileUrl = URL.createObjectURL(fileToView);

            if (!this.activeFileUrls) {
                this.activeFileUrls = new Set();
            }

            this.activeFileUrls.add(fileUrl);

            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            const pdfExtensions = ['pdf'];
            const textExtensions = ['txt', 'csv', 'json', 'xml', 'log'];

            if (imageExtensions.includes(fileExtension)) {
                this.showImageModal(fileToView.name, fileUrl);
            } else if (pdfExtensions.includes(fileExtension)) {
                window.open(fileUrl, '_blank');
                setTimeout(() => this.revokeFileUrl(fileUrl), 1000);
            } else if (textExtensions.includes(fileExtension)) {
                this.showTextFileModal(fileToView, fileUrl);
            } else {
                this.downloadFile(fileToView.name, fileUrl);
                setTimeout(() => this.revokeFileUrl(fileUrl), 1000);
            }
        } catch (error) {
            this.handleError('Error viewing file', error);
        }
    }

    /**
     * Shows toast notification
     *
     * @param {string} type - Toast type (success, error, info, warning)
     * @param {string} message - Toast message
     */
    showToast(type, message) {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        }
    }

    /**
     * Revokes object URL to free memory
     *
     * @param {string} url - Object URL to revoke
     */
    revokeFileUrl(url) {
        try {
            URL.revokeObjectURL(url);
            if (this.activeFileUrls) {
                this.activeFileUrls.delete(url);
            }
        } catch (error) {
            console.warn('Failed to revoke URL:', error);
        }
    }

    /**
     * Shows image in modal
     *
     * @param {string} fileName - File name
     * @param {string} fileUrl - File URL
     */
    showImageModal(fileName, fileUrl) {
        $('#leadModal').modal('hide');

        // XSS Protection: Escape file name
        const escapedFileName = this.escapeHtml(fileName);

        const modalHtml = `
            <div class="modal fade effect-scale md-wrapper" id="fileViewModal"
                tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-truncate" style="max-height: 200px; line-height: 18px;">${escapedFileName}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${fileUrl}" class="img-fluid" alt="${escapedFileName}">
                        </div>
                        <div class="modal-footer">
                            <a href="${fileUrl}" download="${escapedFileName}" class="btn btn-primary">
                                <i class="bx bx-download me-1"></i> Download
                            </a>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#fileViewModal').remove();
        $('body').append(modalHtml);

        const modal = new bootstrap.Modal(document.getElementById('fileViewModal'));

        $('#fileViewModal').on('hidden.bs.modal', () => {
            this.revokeFileUrl(fileUrl);
            $('#fileViewModal').remove();
            $('#leadModal').modal('show');
        });

        modal.show();
    }

    /**
     * Shows text file in modal
     *
     * @param {File} file - File object
     * @param {string} fileUrl - File URL
     */
    showTextFileModal(file, fileUrl) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const content = e.target.result;
            // XSS Protection: Escape file name and content
            const escapedFileName = this.escapeHtml(file.name);
            const escapedContent = this.escapeHtml(content);

            const modalHtml = `
                <div class="modal fade" id="fileViewModal" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${escapedFileName}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <pre class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto;">${escapedContent}</pre>
                            </div>
                            <div class="modal-footer">
                                <a href="${fileUrl}" download="${escapedFileName}" class="btn btn-primary">
                                    <i class="bx bx-download me-1"></i> Download
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#fileViewModal').remove();
            $('body').append(modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('fileViewModal'));

            $('#fileViewModal').on('hidden.bs.modal', function() {
                URL.revokeObjectURL(fileUrl);
                $(this).remove();
            });

            modal.show();
        };
        reader.readAsText(file);
    }

    /**
     * Downloads file
     *
     * @param {string} fileName - File name
     * @param {string} fileUrl - File URL
     */
    downloadFile(fileName, fileUrl) {
        const a = document.createElement('a');
        a.href = fileUrl;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        setTimeout(() => URL.revokeObjectURL(fileUrl), 100);
    }

    /**
     * Escapes HTML to prevent XSS attacks
     *
     * @param {string} text - Text to escape
     * @returns {string} Escaped HTML string
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Formats file size in human-readable format
     *
     * @param {number} bytes - File size in bytes
     * @returns {string} Formatted file size
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = FILE_SIZE_BASE;
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + FILE_SIZE_UNITS[i];
    }

    /**
     * Gets total size of all uploaded files
     *
     * @returns {string} Formatted total file size
     */
    getTotalFileSize() {
        let totalSize = 0;
        Object.values(this.uploadedFiles).forEach(files => {
            files.forEach(file => {
                totalSize += file.size || 0;
            });
        });
        return this.formatFileSize(totalSize);
    }

    /**
     * Generates field input based on header type
     *
     * @param {Object} header - Header object
     * @param {string} fieldId - Field ID
     * @returns {string} HTML string for input field
     */
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
                            name="schedule_headers[${fieldId}]" step="0.01" min="0"
                            placeholder="${placeholder}" ${required}>
                    </div>
                `;
            } else if (header.name?.toLowerCase().includes('date')) {
                return `<input type="date" class="${baseInputClass}" id="${fieldId}" name="schedule_headers[${fieldId}]" ${required}>`;
            } else if (header.name?.toLowerCase().includes('percentage') ||
                header.name?.toLowerCase().includes('rate')) {
                return `
                    <div class="input-group">
                        <input type="number" class="${baseInputClass}" id="${fieldId}"
                            name="schedule_headers[${fieldId}]" step="0.01" min="0" max="100"
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
                return `<select class="form-select ${baseInputClass.replace('form-control', '')}" id="${fieldId}" name="schedule_headers[${fieldId}]" ${required}>${options}</select>`;
            } else {
                const isTextarea = !header.input_type || header.input_type === 'textarea';

                if (isTextarea) {
                    return `<textarea class="form-inputs breakdown-textarea" id="${fieldId}" name="schedule_headers[${fieldId}]" rows="4" maxlength="5000" aria-label="${header.name}" placeholder="${placeholder}" ${required} readonly></textarea>`;
                } else {
                    return `<input type="text" class="${baseInputClass}" id="${fieldId}" name="schedule_headers[${fieldId}]" placeholder="${placeholder}" ${required}>`;
                }
            }
        } catch (error) {
            const isTextarea = !header?.input_type || header?.input_type === 'textarea';

            if (isTextarea) {
                return `<textarea class="form-inputs breakdown-textarea" id="${fieldId}" name="schedule_headers[${fieldId}]" rows="4" maxlength="5000" aria-label="${header.name}" placeholder="${placeholder}" ${required} readonly></textarea>`;
            } else {
                return `<input type="text" class="${baseInputClass}" id="${fieldId || ''}" name="schedule_headers[${fieldId || ''}]" placeholder="${placeholder}" ${required}>`;
            }
        }
    }

    /**
     * Sets up field validation
     *
     * @param {jQuery} $modal - jQuery modal element
     */
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

    /**
     * Validates schedule form fields
     *
     * @param {string} modalId - Modal ID
     * @returns {boolean} Validation result
     */
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

    /**
     * Adds escape key listener for modal closing
     */
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

    /**
     * Removes escape key listener
     */
    removeEscapeKeyListener() {
        if (this.escapeKeyHandler) {
            document.removeEventListener("keydown", this.escapeKeyHandler);
            this.escapeKeyHandler = null;
        }
    }

    /**
     * Reloads all DataTables
     */
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

    /**
     * Gets table ID from tab ID
     *
     * @param {string} tabId - Tab ID
     * @returns {string|null} Table ID or null
     */
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

    /**
     * Capitalizes first letter of string
     *
     * @param {string} str - String to capitalize
     * @returns {string} Capitalized string
     */
    capitalize(str) {
        if (!str || typeof str !== 'string') return '';
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }

    /**
     * Debounces function execution
     *
     * @param {Function} func - Function to debounce
     * @param {number} wait - Wait time in milliseconds
     * @returns {Function} Debounced function
     */
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

    /**
     * Shows loading overlay
     */
    showLoading() {
        this.$loadingOverlay?.removeClass('d-none');
    }

    /**
     * Hides loading overlay
     */
    hideLoading() {
        this.$loadingOverlay?.addClass('d-none');
    }

    /**
     * Shows error message
     *
     * @param {string} message - Error message
     */
    showError(message) {
        this.$errorMessage?.text(message);
        this.$errorContainer?.removeClass('d-none');

        setTimeout(() => {
            this.$errorContainer?.addClass('d-none');
        }, ERROR_DISPLAY_DURATION);

        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        }
    }

    /**
     * Handles errors and displays appropriate messages
     *
     * @param {string} context - Error context
     * @param {Error|Object} error - Error object
     */
    handleError(context, error) {
        let errorMessage = 'An error occurred';

        if (typeof error === 'string') {
            errorMessage = error;
        } else if (error?.message) {
            errorMessage = error.message;
        } else if (error?.xhr?.responseJSON?.message) {
            errorMessage = error.xhr.responseJSON.message;
        } else if (error?.statusText) {
            errorMessage = error.statusText;
        }

        const fullMessage = `${context}: ${errorMessage}`;

        console.error('Error:', {
            context,
            error,
            timestamp: new Date().toISOString()
        });

        if (typeof toastr !== 'undefined') {
            toastr.error(fullMessage);
        } else {
            this.showError(fullMessage);
        }

        if (error?.xhr) {
            console.error('XHR Details:', {
                status: error.xhr.status,
                statusText: error.xhr.statusText,
                responseText: error.xhr.responseText,
                url: error.xhr.responseURL
            });
        }
    }

    /**
     * Destroys the pipeline manager instance and cleans up resources
     */
    destroy() {
        try {
            // Destroy DataTables
            this.dataTables.forEach((dataTable, tableId) => {
                try {
                    if ($.fn.DataTable.isDataTable(`#${tableId}`)) {
                        dataTable.destroy(true);
                    }
                } catch (error) {
                    console.error(`Error destroying DataTable ${tableId}:`, error);
                }
            });

            this.dataTables.clear();

            // Remove event listeners
            this.removeEscapeKeyListener();
            $('.stage_btn_action').off('.pipeline');
            $('.update_category_action').off('.pipeline');
            $('.del_opp_sales').off('.pipeline');
            $('.mail-btn').off('.pipeline');
            $('.preview-pdf-btn').off('.pipeline');
            $('.revert-pipeline').off('.pipeline');
            this.$pipYearSelect?.off('change');
            $('a[data-bs-toggle="tab"]').off('shown.bs.tab');
            $(document).off('ajaxError');
            $('.file-upload-area').off('.fileUpload');
            $('.file-input').off('.fileUpload');
            $('.file-remove-btn').off('.fileRemove');
            $('.file-view-btn').off('.fileView');

            // Destroy chart
            if (this.chartInstance && typeof this.chartInstance.detach === 'function') {
                this.chartInstance.detach();
            }

            // Clean up uploaded files
            this.uploadedFiles = {};

            // Revoke all object URLs
            $('.file-preview-item').each(function() {
                const $img = $(this).find('img');
                if ($img.length && $img.attr('src')) {
                    URL.revokeObjectURL($img.attr('src'));
                }
            });

            if (this.activeFileUrls) {
                this.activeFileUrls.forEach(url => {
                    try {
                        URL.revokeObjectURL(url);
                    } catch (e) {
                        console.warn('Error revoking URL:', e);
                    }
                });
                this.activeFileUrls.clear();
            }
        } catch (error) {
            console.error('Error during cleanup:', error);
        }
    }

    /**
     * Gets all uploaded files
     *
     * @returns {Object} Object containing all uploaded files
     */
    getAllUploadedFiles() {
        return this.uploadedFiles;
    }

    /**
     * Handles sending BD notification email
     *
     * @param {HTMLElement} button - The clicked button element
     */
    handleSendBDNotification(button) {
        try {
            this.showLoading();

            const buttonData = $(button).data();
            this.currentDealId = buttonData.opportunity_id;

            if (!this.currentDealId) {
                throw new Error('Deal ID not found in button data');
            }

            const opportunityId = this.currentDealId;
            const currentStage = buttonData.current_stage;

            this.loadBdEssentials(opportunityId, currentStage);
        } catch (error) {
            this.handleError("Error handling BD notification", error);
        }
    }

    /**
     * Handles PDF preview
     *
     * @param {HTMLElement} button - The clicked button element
     */
    handlePdfPreview(button) {
        try {
            this.showLoading();

            const buttonData = $(button).data();
            this.currentDealId = buttonData.opportunity_id;

            if (!this.currentDealId) {
                throw new Error('Deal ID not found in button data');
            }

            const opportunityId = this.currentDealId;
            const stage = buttonData.current_stage;
            const printout_flag = 1;

            const $form = $("#previewPdfForm");
            const $s = stage.toLowerCase();
            const currentStage = this.config.stageFlow[$s];

            $form.find("#pdf_opportunity_id").val(opportunityId);
            $form.find("#pdf_current_stage").val($s);
            $form.find("#pdf_previous_stage").val(currentStage.previous);
            $("#previewPdfModal").modal('show');

            this.hideLoading();
        } catch (error) {
            this.hideLoading();
            this.handleError("Error handling PDF preview", error);
        }
    }

    /**
     * Loads BD essentials for email notification
     *
     * @param {number} opportunityId - Opportunity ID
     * @param {string} currentStage - Current stage
     */
    loadBdEssentials(opportunityId, currentStage) {
        $.ajax({
            url: 'bd/bd_email_data',
            method: "POST",
            data: {
                opportunity_id: opportunityId,
                current_stage: currentStage
            },
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            timeout: EXTENDED_AJAX_TIMEOUT,
            context: this,
            success: function(response) {
                if (response.success) {
                    const data = {
                        partners: response.data.partners,
                        contacts: response.data.contacts,
                        template: response.data.reinsurersTemplates,
                        attachedFiles: response.data.attachedFiles,
                        bdEmailTitle: currentStage,
                        opportunityId: opportunityId,
                        customerId: response.data.customerId,
                    };

                    this.prepareBDEmailModal(opportunityId, data);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        html: `An error occurred!`,
                        confirmButtonColor: "#dc3545",
                    });
                }
                this.hideLoading();
            },
            error: function(xhr, status, error) {
                this.hideLoading();
                Swal.fire({
                    icon: "error",
                    title: "Failed",
                    html: `An error occurred: <b>${error}</b>`,
                    confirmButtonColor: "#dc3545",
                });
            }
        });
    }

    /**
     * Prepares BD email modal with data
     *
     * @param {number} opportunityId - Opportunity ID
     * @param {Object} data - Email data object
     */
    prepareBDEmailModal(opportunityId, data) {
        try {
            const $bdMailModal = $("#sendBDEmailModal");
            const $bdNotificationForm = $("#bdNotificationForm");

            if (!data?.bdEmailTitle) {
                return;
            }

            const stageTitle = data.bdEmailTitle.toLowerCase();
            const stage = this.config.stageFlow[stageTitle];

            $bdMailModal.find('.modal-bd-title').text(`- ${data.bdEmailTitle}`);
            $bdMailModal.find('#category').val(stage.previous).trigger('change');

            const template = data.template[stage.previous];

            $bdNotificationForm.find(".subject").val(template.subject);
            $bdNotificationForm.find(".message").val(template.message);
            $bdNotificationForm.find(".category_templates").val(JSON.stringify(data.template));

            $bdNotificationForm.find(".opportunity_id").val(data.opportunityId);
            $bdNotificationForm.find(".customer_id").val(data.customerId);

            this.populateAttachedFiles(data.attachedFiles);

            const $contactsSelect = $bdNotificationForm.find('#toContacts');
            const $bccEmailSelect = $bdNotificationForm.find('#bccEmail');
            const $ccEmailSelect = $bdNotificationForm.find('#ccEmail');

            const resetSelect = ($select, placeholder) => {
                $select.empty().append(`<option value="" disabled>${placeholder}</option>`);
            };

            resetSelect($contactsSelect, '--Select contacts--');
            resetSelect($ccEmailSelect, '--Select CC emails--');
            resetSelect($bccEmailSelect, '--Select BCC emails--');

            const partnerEmails = [];
            if (Array.isArray(data.partners) && data.partners.length > 0) {
                data.partners.forEach(partner => {
                    if (partner.email) {
                        partnerEmails.push(partner.email);
                    }
                });
            }

            $bdNotificationForm.find("#toEmail").val(partnerEmails);
            $bdNotificationForm.find("#partnerToEmail").val(data.partners || []);

            const primaryContacts = [];
            const regularContacts = [];

            if (Array.isArray(data.contacts) && data.contacts.length > 0) {
                data.contacts.forEach(contact => {
                    const email = contact.email;
                    if (!email) return;

                    // XSS Protection: Escape contact data
                    let optionText = contact.name ? `${this.escapeHtml(contact.name)} (${this.escapeHtml(email)})` : this.escapeHtml(email);
                    if (contact.phone) optionText += ` - ${this.escapeHtml(contact.phone)}`;
                    if (contact.isPrimary) optionText += ' [Primary]';

                    const createOption = () => $('<option></option>')
                        .attr('value', email)
                        .text(optionText)
                        .data('contact-data', contact)
                        .data('is-primary', !!contact.isPrimary);

                    $contactsSelect.append(createOption());

                    if (!contact.isPrimary) {
                        $ccEmailSelect.append(createOption());
                        $bccEmailSelect.append(createOption());
                    }

                    if (contact.isPrimary) {
                        primaryContacts.push(email);
                    } else {
                        regularContacts.push(email);
                    }
                });

                setTimeout(() => {
                    if (primaryContacts.length > 0) {
                        $contactsSelect.val(primaryContacts).trigger('change');
                    } else if (regularContacts.length === 1) {
                        $contactsSelect.val(regularContacts[0]).trigger('change');
                    }

                    [$contactsSelect, $ccEmailSelect, $bccEmailSelect].forEach($select => {
                        if ($select.hasClass('select2-hidden-accessible')) {
                            $select.trigger('change.select2');
                        }
                    });
                }, 100);
            }

            $("#sendBDEmailModal").modal("show");
        } catch (error) {
            console.error('Error in prepareBDEmailModal:', error);
        }
    }

    /**
     * Populates attached files list
     *
     * @param {Array} filesArray - Array of file objects
     * @param {string} containerId - Container element ID
     */
    populateAttachedFiles(filesArray, containerId = 'attachedFilesList') {
        const $container = $(`#${containerId}`);

        if ($container.length === 0) {
            return;
        }

        const $rowContainer = $container.find('.row').first();
        if ($rowContainer.length === 0) {
            return;
        }

        $rowContainer.empty();

        if (!filesArray || filesArray.length === 0) {
            this.addNoFilesMessage($rowContainer);
            this.updateFileCount(0);
            return;
        }

        $('#additionalFilesMessage').remove();

        $.each(filesArray, (index, file) => {
            const $fileElement = this.createFileElement(file);
            $rowContainer.append($fileElement);
        });

        this.updateFileCount(filesArray.length);
    }

    /**
     * Creates file element for attached files list
     *
     * @param {Object} file - File object
     * @returns {jQuery} jQuery file element
     */
    createFileElement(file) {
        const fileUrl = file.s3_url;
        const fileName = file.original_name;
        const mimeType = file.mimetype;
        const fileSize = file.file_size;

        const fileInfo = this.getFileIconAndType(mimeType, fileName);

        const $col = $('<div>', { class: 'col-md-4' });
        const $link = $('<a>', {
            href: fileUrl,
            target: '_blank',
            rel: 'noopener noreferrer'
        });

        const $fileItem = $('<div>', {
            class: 'file-item d-flex align-items-center mb-2'
        });

        const $fileIcon = $('<div>', {
            class: 'file-icon me-3'
        }).html(`<i class="bx ${fileInfo.icon}"></i>`);

        const $fileInfoDiv = $('<div>', { class: 'file-info flex-grow-1' });

        // XSS Protection: Escape file name
        const $fileName = $('<h6>', {
            class: 'mb-1',
            text: fileName
        });

        const fileSizeText = fileSize ? '• ' + this.formatFileSize(fileSize) : '';
        const $fileMeta = $('<div>', {
            class: 'file-meta',
            html: fileInfo.displayType + ' ' + fileSizeText
        });

        $fileInfoDiv.append($fileName).append($fileMeta);
        $fileItem.append($fileIcon).append($fileInfoDiv);
        $link.append($fileItem);
        $col.append($link);

        return $col;
    }

    /**
     * Gets file icon and type based on MIME type
     *
     * @param {string} mimeType - File MIME type
     * @param {string} fileName - File name
     * @returns {Object} Object with icon and displayType
     */
    getFileIconAndType(mimeType, fileName) {
        const fileExtension = fileName.split('.').pop().toLowerCase();

        if (mimeType.includes('pdf') || fileExtension === 'pdf') {
            return {
                icon: 'bx-file text-danger',
                displayType: 'PDF Document'
            };
        }

        if (mimeType.includes('word') ||
            mimeType.includes('document') || ['doc', 'docx'].includes(fileExtension)) {
            return {
                icon: 'bx-file text-primary',
                displayType: 'Word Document'
            };
        }

        if (mimeType.includes('image') || ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(
                fileExtension)) {
            return {
                icon: 'bx-image text-success',
                displayType: 'Image File'
            };
        }

        if (mimeType.includes('sheet') ||
            mimeType.includes('excel') || ['xls', 'xlsx'].includes(fileExtension)) {
            return {
                icon: 'bx-file text-success',
                displayType: 'Excel Document'
            };
        }

        if (mimeType.includes('text') || fileExtension === 'txt') {
            return {
                icon: 'bx-file text-info',
                displayType: 'Text Document'
            };
        }

        return {
            icon: 'bx-file',
            displayType: fileExtension ? `${fileExtension.toUpperCase()} Document` : 'Document'
        };
    }

    /**
     * Adds "no files" message
     *
     * @param {jQuery} $container - jQuery container element
     */
    addNoFilesMessage($container) {
        if ($('#additionalFilesMessage').length > 0) return;

        const $col = $('<div>', { class: 'col-md-12' });
        const $message = $('<div>', {
            id: 'additionalFilesMessage',
            class: 'text-center py-2'
        }).html(`
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                No additional claim documents attached.
            </small>
        `);

        $col.append($message);
        $container.append($col);
    }

    /**
     * Updates file count display
     *
     * @param {number} dynamicCount - Dynamic file count
     * @param {number} staticCount - Static file count
     */
    updateFileCount(dynamicCount, staticCount = 2) {
        const totalCount = staticCount + dynamicCount;
        $('#fileCount').text(`${totalCount} files attached`);
    }
}

/* ============================================================================
   Initialization
   ========================================================================== */
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

// Clean up on page unload
$(window).on('beforeunload', function() {
    if (pipelineManager && typeof pipelineManager.destroy === 'function') {
        pipelineManager.destroy();
    }
});

// Handle unhandled promise rejections
window.addEventListener('unhandledrejection', function(event) {
    if (pipelineManager && typeof pipelineManager.handleError === 'function') {
        pipelineManager.handleError('Unhandled Promise Rejection', event.reason);
    }
});

/* ============================================================================
   Export for module usage (if needed)
   ========================================================================== */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PipelineManager;
}
