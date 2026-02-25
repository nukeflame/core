"use strict";

const STAGE_NAMES = {
    LEAD: "lead",
    PROPOSAL: "proposal",
    NEGOTIATION: "negotiation",
    FINAL_STAGE: "final_stage",
    WON: "won",
    LOST: "lost",
};

const CHART_LABELS = [
    "Lead",
    "Proposal",
    "Negotiation",
    "Won",
    "Lost",
    "Final Stage",
];

const DEFAULT_CHART_DATA = [0, 0, 0, 0, 0, 0];

const FILE_SIZE_UNITS = ["Bytes", "KB", "MB", "GB"];
const FILE_SIZE_BASE = 1024;

const DEFAULT_MAX_FILE_SIZE = 10485760; // 10MB in bytes

const AJAX_TIMEOUT = 10000; // 10 seconds
const EXTENDED_AJAX_TIMEOUT = 30000; // 30 seconds

const DEBOUNCE_DELAY = 300; // milliseconds

const ERROR_DISPLAY_DURATION = 5000; // 5 seconds

const EXCLUDED_TERMS = [
    "Premium",
    "Sum Insured Breakdown",
    "Reinsurer Commission Rate",
    "Allowed Commission",
    "Commission",
    "Deductible/Excess",
];

const DEDUCTIBLE_TERMS = ["Deductible/Excess"];

class PipelineManager {
    constructor() {
        this.chartInstance = null;
        this.totalSumInsured = null;
        this.currentDealId = null;
        this.currentStage = STAGE_NAMES.LEAD;
        this.escapeKeyHandler = null;
        this.dataTables = new Map();
        this.uploadedFiles = {};
        this.reinsurerDataTable = null;
        this.activeFileUrls = new Set();
        this.currentChartRequest = null;
        this.tableReloadToken = 0;
        this.bdTermsCache = new Map();

        this.$pipYearSelect = null;
        this.$loadingOverlay = null;
        this.$errorContainer = null;
        this.$errorMessage = null;
        this.$chartLoading = null;
        this.$chartError = null;
        this.$pipelineChart = null;
        this.$pipelineMetaName = null;
        this.$pipelineMetaYear = null;
        this.$pipelineMetaOpp = null;
        this.$pipelineMetaWon = null;
        this.$pipelineMetaLost = null;
        this.$pipelineMetaWorth = null;
        this.$pipelineSearch = null;
        this.$pipelineStatusFilter = null;
        this.$pipelineCategoryFilter = null;
        this.$pipelineUrgencyFilter = null;
        this.$pipelineClearFilters = null;

        this.config = {
            routes: {
                pipelineData: window.pipelineRoutes?.pipelineData || "",
                chartData: window.pipelineRoutes?.chartData || "",
                pipelineDetailsTemplate:
                    window.pipelineRoutes?.pipelineDetailsTemplate || "",
                scheduleHeaders: window.pipelineRoutes?.scheduleHeaders || "",
                slipDocuments: window.pipelineRoutes?.slipDocuments || "",
                bdEmailData: window.pipelineRoutes?.bdEmailData || "",
                getBdTerms: window.pipelineRoutes?.getBdTerms || "",
                declineReinsurer: window.pipelineRoutes?.declineReinsurer || "",
                resetProposalToLead:
                    window.pipelineRoutes?.resetProposalToLead || "",
                getSelectedReinsurers:
                    window.pipelineRoutes?.getSelectedReinsurers || "",
            },
            stageFlow: {
                [STAGE_NAMES.LEAD]: {
                    next: STAGE_NAMES.PROPOSAL,
                    button: "Update Lead",
                    class: "btn-lead",
                    altNext: STAGE_NAMES.LOST,
                    previous: null,
                    modalId: "leadModal",
                },
                [STAGE_NAMES.PROPOSAL]: {
                    next: STAGE_NAMES.NEGOTIATION,
                    button: "Update Proposal",
                    class: "btn-negotiation",
                    altNext: STAGE_NAMES.LOST,
                    previous: STAGE_NAMES.LEAD,
                    modalId: "proposalModal",
                },
                [STAGE_NAMES.NEGOTIATION]: {
                    next: STAGE_NAMES.FINAL_STAGE,
                    button: "Update Negotiation",
                    class: "btn-won",
                    altNext: STAGE_NAMES.LOST,
                    previous: STAGE_NAMES.PROPOSAL,
                    modalId: "negotiationModal",
                },
                [STAGE_NAMES.FINAL_STAGE]: {
                    next: STAGE_NAMES.WON,
                    button: "Update Status",
                    class: "status-final",
                    previous: STAGE_NAMES.NEGOTIATION,
                    modalId: "finalStageModal",
                },
                [STAGE_NAMES.WON]: {
                    next: null,
                    button: "Deal Complete",
                    class: "btn-final",
                    previous: null,
                    modalId: "wonModal",
                },
                [STAGE_NAMES.LOST]: {
                    next: null,
                    button: "Deal Closed",
                    class: "btn-lost",
                    previous: null,
                    modalId: "lostModal",
                },
            },
            columnConfig: [
                { data: "id", name: "id", title: "ID" },
                {
                    data: "insured_name",
                    name: "insured_name",
                    title: "Insured Name",
                },
                { data: "division", name: "division", title: "Division" },
                {
                    data: "business_class",
                    name: "business_class",
                    title: "Business Class",
                },
                { data: "status", name: "status", title: "Status" },
                {
                    data: "currency",
                    name: "currency",
                    title: "Currency",
                    defaultContent: "KES",
                },
                {
                    data: "sum_insured",
                    name: "sum_insured",
                    title: "Sum Insured",
                },
                { data: "premium", name: "premium", title: "Premium" },
                {
                    data: "effective_date",
                    name: "effective_date",
                    title: "Effective Date",
                },
                {
                    data: "closing_date",
                    name: "closing_date",
                    title: "Closing Date",
                },
                { data: "category", name: "category", title: "Category" },
                {
                    data: "approval_status",
                    name: "approval_status",
                    title: "Approval Status",
                    orderable: false,
                },
                {
                    data: "stage_actions",
                    name: "stage_actions",
                    title: "Stage Actions",
                },
                { data: "action", orderable: false, searchable: false },
            ],
        };

        this.init();
    }

    init() {
        try {
            this.cacheDOMElements();
            this.setupCSRF();
            this.setupErrorHandling();
            this.initializeChart();
            this.initializeDataTables();
            this.loadPipelineDetails();
            this.bindEvents();

            this.performInitialConnectionCheck();
        } catch (error) {
            this.handleError("Initialization failed", error);
        }
    }

    performInitialConnectionCheck() {
        if (
            typeof window.BDEmailModal !== "undefined" &&
            typeof window.BDEmailModal.checkEmailConnection === "function"
        ) {
            window.BDEmailModal.checkEmailConnection()
                .then((isConnected) => {
                    this.updateMailButtonStates(isConnected);
                })
                .catch(() => {
                    // Treat disconnected/unavailable email service as a non-fatal state.
                    this.updateMailButtonStates(false);
                });
        }
    }

    cacheDOMElements() {
        this.$pipYearSelect = $("#pip_year_select");
        this.$loadingOverlay = $("#loading-overlay");
        this.$errorContainer = $("#error-container");
        this.$errorMessage = $("#error-message");
        this.$chartLoading = $("#chart-loading");
        this.$chartError = $("#chart-error");
        this.$pipelineChart = $("#pipeline-chart");
        this.$pipelineMetaName = $("#pipeline-meta-name");
        this.$pipelineMetaYear = $("#pipeline-meta-year");
        this.$pipelineMetaOpp = $("#pipeline-meta-opp");
        this.$pipelineMetaWon = $("#pipeline-meta-won");
        this.$pipelineMetaLost = $("#pipeline-meta-lost");
        this.$pipelineMetaWorth = $("#pipeline-meta-worth");
        this.$pipelineSearch = $("#pipelineSearch");
        this.$pipelineStatusFilter = $("#pipelineStatusFilter");
        this.$pipelineCategoryFilter = $("#pipelineCategoryFilter");
        this.$pipelineUrgencyFilter = $("#pipelineUrgencyFilter");
        this.$pipelineClearFilters = $("#pipelineClearFilters");
    }

    setupCSRF() {
        const csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
        });
    }

    setupErrorHandling() {
        window.onerror = (message, source, lineno, colno, error) => {
            this.handleError("JavaScript Error", {
                message,
                source,
                lineno,
                error,
            });
        };
    }

    initializeChart() {
        try {
            if (typeof Chartist === "undefined") {
                this.showChartError();
                return;
            }

            const chartContainer = $(".ct-chart-ranking");
            if (chartContainer.length === 0) {
                this.showChartError();
                return;
            }

            this.showChartLoading();

            this.chartInstance = new Chartist.Bar(
                ".ct-chart-ranking",
                {
                    labels: CHART_LABELS,
                    series: [DEFAULT_CHART_DATA],
                },
                {
                    low: 0,
                    showArea: true,
                    height: "300px",
                    plugins:
                        typeof Chartist.plugins !== "undefined"
                            ? [Chartist.plugins.tooltip()]
                            : [],
                    axisX: {
                        position: "end",
                    },
                    axisY: {
                        showGrid: false,
                        showLabel: false,
                        offset: 0,
                    },
                },
            );

            this.chartInstance.on("draw", (data) => {
                if (data.type === "bar") {
                    data.element.animate({
                        y2: {
                            dur: 1000,
                            from: data.y1,
                            to: data.y2,
                            easing: Chartist.Svg.Easing.easeOutQuint,
                        },
                    });
                }
            });

            this.loadChartData();
        } catch (error) {
            this.handleError("Chart initialization failed", error);
            this.showChartError();
        }
    }

    showChartLoading() {
        this.$chartLoading?.removeClass("d-none");
        this.$chartError?.addClass("d-none");
        // Keep chart container visible so Chartist can calculate width correctly.
        this.$pipelineChart?.removeClass("d-none");
    }

    hideChartLoading() {
        this.$chartLoading?.addClass("d-none");
        this.$pipelineChart?.removeClass("d-none");
    }

    showChartError() {
        this.$chartLoading?.addClass("d-none");
        this.$chartError?.removeClass("d-none");
        this.$pipelineChart?.addClass("d-none");
    }

    loadChartData() {
        const pipelineId = this.$pipYearSelect?.val();

        if (!pipelineId) {
            this.updateChartData(DEFAULT_CHART_DATA, CHART_LABELS);
            this.hideChartLoading();
            return;
        }

        if (
            this.currentChartRequest &&
            this.currentChartRequest.readyState !== 4
        ) {
            this.currentChartRequest.abort();
        }

        const requestedPipelineId = String(pipelineId);

        this.currentChartRequest = $.ajax({
            url: this.config.routes.chartData,
            method: "GET",
            data: { pipeline_id: requestedPipelineId },
            timeout: AJAX_TIMEOUT,
            success: (response) => {
                try {
                    const activePipelineId = String(
                        this.$pipYearSelect?.val() ?? "",
                    );
                    if (activePipelineId !== requestedPipelineId) {
                        return;
                    }

                    if (response?.data && Array.isArray(response.data)) {
                        this.updateChartData(response.data, response?.labels);
                    } else {
                        this.updateChartData(DEFAULT_CHART_DATA, CHART_LABELS);
                    }
                    this.hideChartLoading();
                } catch (error) {
                    this.handleError("Failed to render chart data", error);
                    this.updateChartData(DEFAULT_CHART_DATA, CHART_LABELS);
                    this.showChartError();
                }
            },
            error: (xhr, status, error) => {
                if (status === "abort") {
                    return;
                }

                this.handleError("Failed to load chart data", {
                    xhr,
                    status,
                    error,
                });
                this.updateChartData(DEFAULT_CHART_DATA, CHART_LABELS);
                this.showChartError();
            },
        });
    }

    updateChartData(data, labels = CHART_LABELS) {
        if (!this.chartInstance) {
            return;
        }

        try {
            if (!Array.isArray(labels) || labels.length === 0) {
                labels = CHART_LABELS;
            }

            if (!Array.isArray(data) || data.length !== labels.length) {
                data = DEFAULT_CHART_DATA;
                labels = CHART_LABELS;
            }

            this.chartInstance.update({
                labels: labels,
                series: [data],
            });
        } catch (error) {
            this.handleError("Failed to update chart", error);
            this.chartInstance.update({
                labels: CHART_LABELS,
                series: [DEFAULT_CHART_DATA],
            });
            this.showChartError();
        }
    }

    initializeDataTables() {
        const tables = $(".pipeline-table");

        if (tables.length === 0) {
            return;
        }

        let pendingInitialTables = tables.length;
        const finalizeInitialTableLoad = () => {
            pendingInitialTables = Math.max(0, pendingInitialTables - 1);
            if (pendingInitialTables === 0) {
                this.hideLoading();
            }
        };

        this.showLoading();

        tables.each((index, table) => {
            const $table = $(table);
            const tableId = $table.attr("id");
            const quarter = $table.data("quarter");
            let hasCompletedInitialDraw = false;

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
                            const filters = this.getPipelineFilters();
                            d.pipeline_id = this.$pipYearSelect?.val();
                            d.quarter = quarter;
                            d.search_query = filters.searchQuery;
                            d.status_filter = filters.status;
                            d.category_filter = filters.category;
                            d.urgency_filter = filters.urgency;
                        },
                        error: (xhr, error, code) => {
                            console.error(`DataTable error for ${tableId}:`, {
                                status: xhr.status,
                                error: error,
                                response: xhr.responseText,
                            });
                            this.handleAjaxError(xhr, tableId);
                            if (!hasCompletedInitialDraw) {
                                hasCompletedInitialDraw = true;
                                finalizeInitialTableLoad();
                            }
                        },
                    },
                    columns: this.config.columnConfig,
                    order: [[0, "desc"]],
                    pageLength: 25,
                    responsive: true,
                    searching: false,
                    language: {
                        processing: this.getLoadingHTML(),
                        emptyTable: "No pipeline records found",
                        loadingRecords: "Loading...",
                        zeroRecords: "No matching records found",
                    },
                    drawCallback: () => {
                        this.initializeActionHandlers();
                        $table.addClass("fade-in");

                        if (!hasCompletedInitialDraw) {
                            hasCompletedInitialDraw = true;
                            finalizeInitialTableLoad();
                        }
                    },
                    createdRow: (row, rowData) => {
                        this.applyRowUrgencyClass(row, rowData);
                    },
                });

                this.dataTables.set(tableId, dataTable);
            } catch (error) {
                this.handleError(
                    `Error initializing DataTable for ${tableId}`,
                    error,
                );
                finalizeInitialTableLoad();
            }
        });
    }

    handleAjaxError(xhr, tableId) {
        let errorMessage = "Failed to load data";

        if (xhr.status === 404) {
            errorMessage = "Data endpoint not found";
        } else if (xhr.status === 500) {
            errorMessage = "Server error occurred";
        } else if (xhr.status === 403) {
            errorMessage = "Access denied";
        }

        this.showError(`${errorMessage} for ${tableId}`);
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
        this.$pipYearSelect
            ?.off("change.pipeline")
            .on("change.pipeline", () => {
                this.debounce(() => {
                    this.updateBrowserUrl();
                    this.loadPipelineDetails();
                    this.loadChartData();
                    this.reloadAllTables();
                }, DEBOUNCE_DELAY)();
            });

        $('a[data-bs-toggle="tab"]')
            .off("shown.bs.tab.pipeline")
            .on("shown.bs.tab.pipeline", (e) => {
                const target = $(e.target).attr("href");
                const tableId = this.getTableIdFromTab(target);

                if (tableId && this.dataTables.has(tableId)) {
                    this.dataTables.get(tableId).columns.adjust().draw();
                }
            });

        $(document)
            .off("ajaxError.pipeline")
            .on("ajaxError.pipeline", (event, xhr, settings, thrownError) => {
                this.handleError("AJAX Error", {
                    url: settings.url,
                    status: xhr.status,
                    error: thrownError,
                });
            });

        this.$pipelineSearch?.off("input.pipeline").on(
            "input.pipeline",
            this.debounce(() => this.reloadAllTables(), DEBOUNCE_DELAY),
        );

        this.$pipelineStatusFilter
            ?.off("change.pipeline")
            .on("change.pipeline", () => this.reloadAllTables());

        this.$pipelineCategoryFilter
            ?.off("change.pipeline")
            .on("change.pipeline", () => this.reloadAllTables());

        this.$pipelineUrgencyFilter
            ?.off("change.pipeline")
            .on("change.pipeline", () => this.reloadAllTables());

        this.$pipelineClearFilters
            ?.off("click.pipeline")
            .on("click.pipeline", () => {
                this.clearPipelineFilters();
                this.reloadAllTables();
            });
    }

    getPipelineFilters() {
        return {
            searchQuery: (this.$pipelineSearch?.val() || "").trim(),
            status: this.$pipelineStatusFilter?.val() || "",
            category: this.$pipelineCategoryFilter?.val() || "",
            urgency: this.$pipelineUrgencyFilter?.val() || "",
        };
    }

    clearPipelineFilters() {
        this.$pipelineSearch?.val("");
        this.$pipelineStatusFilter?.val("");
        this.$pipelineCategoryFilter?.val("");
        this.$pipelineUrgencyFilter?.val("");
    }

    applyRowUrgencyClass(row, rowData) {
        const urgencyClass = rowData?.urgency_class;
        if (!urgencyClass) {
            return;
        }
        $(row).addClass(urgencyClass);
    }

    updateBrowserUrl() {
        const pipelineId = this.$pipYearSelect?.val();
        if (!pipelineId || !window.history?.replaceState) {
            return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set("pipeline", pipelineId);
        window.history.replaceState({}, "", url.toString());
    }

    getPipelineDetailsUrl(pipelineId) {
        const template = this.config.routes.pipelineDetailsTemplate;
        if (!template || !pipelineId) {
            return "";
        }

        return template.replace("__PIPELINE__", encodeURIComponent(pipelineId));
    }

    loadPipelineDetails() {
        const pipelineId = this.$pipYearSelect?.val();
        const url = this.getPipelineDetailsUrl(pipelineId);

        if (!pipelineId || !url) {
            this.resetPipelineMeta();
            return;
        }

        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            timeout: AJAX_TIMEOUT,
            headers: {
                Accept: "application/json",
            },
            success: (response) => {
                if (response?.status === 1 && response?.data) {
                    this.updatePipelineMeta(response.data);
                } else {
                    this.resetPipelineMeta();
                }
            },
            error: (xhr, status, error) => {
                this.handleError("Failed to fetch pipeline details", {
                    pipelineId,
                    status,
                    error,
                    response: xhr?.responseText,
                });
                this.resetPipelineMeta();
            },
        });
    }

    updatePipelineMeta(details) {
        this.$pipelineMetaName?.text(details?.name || "N/A");
        this.$pipelineMetaYear?.text(`Year: ${details?.year ?? "N/A"}`);
        this.$pipelineMetaOpp?.text(
            `Opportunities: ${Number(details?.opportunities ?? 0)}`,
        );
        this.$pipelineMetaWon?.text(`Won: ${Number(details?.won ?? 0)}`);
        this.$pipelineMetaLost?.text(`Lost: ${Number(details?.lost ?? 0)}`);
        this.$pipelineMetaWorth?.text(
            `Worth: ${this.formatCurrencyValue(details?.worth)}`,
        );
    }

    resetPipelineMeta() {
        this.$pipelineMetaName?.text("N/A");
        this.$pipelineMetaYear?.text("Year: N/A");
        this.$pipelineMetaOpp?.text("Opportunities: --");
        this.$pipelineMetaWon?.text("Won: --");
        this.$pipelineMetaLost?.text("Lost: --");
        this.$pipelineMetaWorth?.text("Worth: --");
    }

    formatCurrencyValue(amount) {
        const parsed = Number(amount || 0);
        if (Number.isNaN(parsed)) {
            return "0.00";
        }
        return parsed.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    initializeActionHandlers() {
        $(".stage_btn_action").off("click.pipeline");
        $(".del_opp_sales").off("click.pipeline");
        $(".update_category_action").off("click.pipeline");
        $(".mail-btn").off("click.pipeline");
        $(".preview-pdf-btn").off("click.pipeline");
        $(".revert-pipeline").off("click.pipeline");
        $(".reset_proposal_to_lead_btn").off("click.pipeline");

        $(".stage_btn_action").on("click.pipeline", (e) => {
            e.preventDefault();
            this.handleStageAction(e.currentTarget);
        });

        $(".update_category_action").on("click.pipeline", (e) => {
            e.preventDefault();
            this.handleCategoryUpdate(e.currentTarget);
        });

        $(".del_opp_sales").on("click.pipeline", (e) => {
            e.preventDefault();
            this.handleDelPipeline(e.currentTarget);
        });

        $(".mail-btn").on("click.pipeline", (e) => {
            e.preventDefault();
            this.handleSendBDNotification(e.currentTarget);
        });

        $(".revert-pipeline").on("click.pipeline", (e) => {
            e.preventDefault();
            this.handleRevertPipeline(e.currentTarget);
        });

        $(".reset_proposal_to_lead_btn").on("click.pipeline", (e) => {
            e.preventDefault();
            this.handleResetProposalToLead(e.currentTarget);
        });

        $(".preview-pdf-btn").on("click.pipeline", (e) => {
            e.preventDefault();
            this.handlePdfPreview(e.currentTarget);
        });
    }

    handleStageAction(button) {
        try {
            this.showLoading();

            const buttonData = $(button).data();
            this.currentDealId = buttonData.deal_id;

            if (!this.currentDealId) {
                throw new Error("Deal ID not found in button data");
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
                throw new Error("Row data not available");
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
                this.openStageModal(
                    nextStage,
                    modalId,
                    this.currentDealId,
                    dealInfo,
                );
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
                throw new Error("Opportunity ID not found");
            }

            $("#updateCategoryForm #opportunity_id").val(
                buttonData.opportunity_id,
            );
            $("#updateCategoryTypeModal").modal("show");
        } catch (error) {
            this.handleError("Error handling category update", error);
        }
    }

    handleDelPipeline(button) {
        try {
            const buttonData = $(button).data();
            if (!buttonData.opp_id) {
                throw new Error("Opportunity ID not found");
            }

            const $row = $(button).closest("tr");
            const $table = $row.closest("table");
            const tableId = $table.attr("id");

            let insuredName = "";
            if (this.dataTables.has(tableId)) {
                const dataTable = this.dataTables.get(tableId);
                const rowData = dataTable.row($row).data();
                if (rowData?.insured_name) {
                    insuredName = rowData.insured_name;
                }
            }

            Swal.fire({
                title: "Remove from Sales?",
                html: `Are you sure you want to delete this from sales.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel",
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    cancelButton: "btn btn-sm btn-light me-2",
                    confirmButton: "btn btn-danger btn-sm",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deletePipeline(buttonData.opportunity_id, insuredName);
                }
            });
        } catch (error) {
            this.handleError("Error handling pipeline deletion", error);
        }
    }

    handleRevertPipeline(button) {
        try {
            const buttonData = $(button).data();
            const opportunityId = buttonData.opportunityId;
            if (!opportunityId) {
                throw new Error("Opportunity ID not found");
            }

            const $row = $(button).closest("tr");
            const $table = $row.closest("table");
            const tableId = $table.attr("id");

            let insuredName = "";
            if (this.dataTables.has(tableId)) {
                const dataTable = this.dataTables.get(tableId);
                const rowData = dataTable.row($row).data();
                if (rowData?.insured_name) {
                    insuredName = rowData.insured_name;
                }
            }

            const currentStage = buttonData.current_stage
                ? buttonData.current_stage.toLowerCase()
                : "";
            const stage = this.config.stageFlow[currentStage];
            const revertStage = stage?.previous
                ? this.capitalize(stage.previous)
                : null;

            Swal.fire({
                title: "Revert Pipeline Stage?",
                html: `
                    <p>
                        Are you sure you want to revert <strong>${
                            this.escapeHtml(insuredName) || "this opportunity"
                        }</strong>
                        back to a previous pipeline stage <strong>${this.escapeHtml(
                            revertStage,
                        )}?</strong>
                    </p>
                    <p class="text-muted mb-0">This action will update its current sales stage accordingly.</p>
                `,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, revert it",
                cancelButtonText: "Cancel",
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    cancelButton: "btn btn-sm btn-light me-2",
                    confirmButton: "btn btn-primary btn-sm",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.revertPipeline(opportunityId, insuredName);
                }
            });
        } catch (error) {
            this.handleError("Error handling pipeline revert", error);
        }
    }

    handleResetProposalToLead(button) {
        try {
            const buttonData = $(button).data();
            const opportunityId =
                buttonData.deal_id || buttonData.opportunity_id;

            if (!opportunityId) {
                throw new Error("Opportunity ID not found");
            }

            Swal.fire({
                title: "Reset To Lead Stage?",
                html: `
                    <p class="mb-2">This will move this opportunity back to <strong>Lead</strong> stage.</p>
                    <p class="text-muted mb-0">Declined reinsurers will remain marked as declined so you can select replacements.</p>
                `,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f39c12",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, reset to Lead",
                cancelButtonText: "Cancel",
                reverseButtons: true,
                customClass: {
                    cancelButton: "btn btn-sm btn-light me-2",
                    confirmButton: "btn btn-warning btn-sm",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: this.config.routes.resetProposalToLead,
                    data: {
                        opportunity_id: opportunityId,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content",
                        ),
                    },
                    success: (response) => {
                        if (response?.status === 1) {
                            toastr.success(
                                response.message ||
                                    "Opportunity reset to Lead stage successfully.",
                            );
                            this.reloadAllTables();
                            this.loadChartData();
                        } else {
                            toastr.error(
                                response?.message || "Failed to reset stage.",
                            );
                        }
                    },
                    error: (xhr) => {
                        const message =
                            xhr?.responseJSON?.message ||
                            "Failed to reset opportunity to Lead stage.";
                        toastr.error(message);
                    },
                });
            });
        } catch (error) {
            this.handleError("Error resetting proposal to lead", error);
        }
    }

    revertPipeline(dealId, insuredName) {
        $.ajax({
            type: "POST",
            url: window.pipelineRoutes?.revert || "",
            data: {
                prospect_id: dealId,
                revert_to_sales: 1,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
                    icon: "error",
                });
            },
        });
    }

    deletePipeline(dealId, insuredName) {
        $.ajax({
            type: "POST",
            url: window.pipelineRoutes?.addPipeline || "",
            data: {
                prospect: dealId,
                revert_to_sales: true,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
                    icon: "error",
                });
            },
        });
    }

    openStageModal(stage, modalId, dealId, dealInfo = null) {
        try {
            if (!dealId) {
                throw new Error("dealId is required");
            }

            if (!dealInfo) {
                throw new Error("dealInfo is required");
            }

            this.currentDealId = dealId;

            const $modal = $(`#${modalId}`);
            if ($modal.length === 0) {
                throw new Error(`Modal not found: ${modalId}`);
            }
            $modal.attr("data-deal-id", dealId);

            const data = {
                dealId: dealId,
                opportunityId: dealInfo?.id,
                typeOfBus: dealInfo?.type_of_business,
                modalId: modalId,
                class: dealInfo?.class,
                classGroup: dealInfo?.class_group,
                stage: stage,
                currentStage: this.currentStage,
                categoryType: dealInfo?.category_type,
                sumInsuredType: dealInfo?.sum_insured_type,
                riskType: dealInfo?.risk_type,
            };

            // if (this.currentStage === STAGE_NAMES.LEAD) {
            //     this.loadBdTerms(data);
            // } else if (
            //     this.currentStage === STAGE_NAMES.PROPOSAL ||
            //     this.currentStage === STAGE_NAMES.NEGOTIATION
            // ) {
            //     this.loadSelectedReinsurers(data);
            // }

            this.loadSelectedReinsurers(data);
            this.loadSlipDocuments(data);
            this.loadScheduleHeaders(data);
            this.loadBdTerms(data);
            this.populateModalData(
                modalId,
                dealId,
                this.currentStage,
                dealInfo,
            );

            $modal.modal("show");
            $modal.addClass("slide-in");

            this.addEscapeKeyListener();
        } catch (error) {
            this.handleError("Error opening modal", error);
            throw error;
        }
    }

    populateModalData(modalId, dealId, stage, dealInfo = null) {
        try {
            const $modal = $(`#${modalId}`);
            if (!dealInfo) {
                return;
            }
            $modal.attr("data-deal-id", dealId || "");

            $modal.find(".slip-display").text(dealInfo.id || "");

            if (dealInfo.created_at) {
                try {
                    const dateObj = new Date(dealInfo.created_at);
                    const options = {
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                    };
                    const formattedDate = dateObj.toLocaleDateString(
                        "en-US",
                        options,
                    );
                    $modal.find(".created_at-display").text(formattedDate);
                } catch (dateError) {
                    $modal.find(".created_at-display").text("");
                }
            }

            const normalizedCategoryType =
                Number(dealInfo.category_type) === 1 ? 1 : 2;

            let $slipTitle = "";
            if (normalizedCategoryType === 1) {
                $slipTitle = "Quotation Slip";

                $modal.find(".slip_type").val("quotation");
                $modal.find(".category_type").val("1");

                $modal.find(".fac-rates").hide();

                $(".quote-rein").addClass("col-md-8");
                $(".quote-rein").attr("data-quote", "true");
            } else {
                $slipTitle = "Facultative Slip";

                $modal.find(".slip_type").val("facultative");
                $modal.find(".category_type").val("2");

                $modal.find(".fac-rates").show();

                $(".quote-rein").addClass("col-md-6");
                $(".quote-rein").attr("data-quote", "false");
            }

            $modal.find(".slip-title").text($slipTitle);

            $modal
                .find(".insured-name-display")
                .text(dealInfo.insured_name || "");
            $modal
                .find(".insured-email-display")
                .text(dealInfo.insured_email || "--");
            $modal
                .find(".insured-phone-display")
                .text(dealInfo.insured_phone || "--");
            $modal
                .find(".insured-contact-name-display")
                .text(dealInfo.contact_name || "--");
            $modal
                .find(".sum_insured_type")
                .text(`(${dealInfo.sum_insured_type})` || "");

            $modal.find(".opportunity_id").val(dealInfo.id);
            $modal.find(".current_stage").val(stage);
            $modal.find(".cedant_id").val(dealInfo.cedant.customer_id);

            $modal
                .find(".total_sum_insured")
                .val(dealInfo.total_sum_insured || "0.00");
            $modal.find(".premium").val(dealInfo.premium || "0.00");
            $modal
                .find(".brokerage_rate")
                .val(dealInfo.brokerage_rate || "0.00");
            $modal
                .find(".total_reinsurer_share")
                .val(dealInfo.written_share || "0.00");
            $modal.find(".class_code").val(dealInfo.class || "");
            $modal.find(".class_group_code").val(dealInfo.class_group || "");

            const $riskType = $modal.find(".risk_type");
            if ($riskType.length > 0) {
                $riskType.val(dealInfo?.risk_type || "");
            }

            const $cedantName = $modal.find(".cedant_name");
            if ($cedantName.length > 0) {
                $cedantName.text(dealInfo?.cedant?.name || "");
            }

            const $lastContactDate = $modal.find(".last_contact_date");
            if ($lastContactDate.length > 0) {
                $lastContactDate.val(dealInfo?.last_updated || "");
            }

            const $cedant = $modal.find(".add_cedant_contacts");
            if ($cedant.length > 0) {
                $cedant.attr("data-cedant-id", dealInfo.cedant.customer_id);
                $cedant.attr("data-cedant-name", dealInfo.cedant.name);
                $cedant.attr("data-opportunity-id", dealInfo.id);
            }
        } catch (error) {
            this.handleError("Error populating modal data", error);
        }
    }

    loadScheduleHeaders(data) {
        if (!data.dealId || !data.class || !data.classGroup) {
            return;
        }

        const $modal = $(`#${data.modalId}`);
        const $termsSubtitle = $modal.find("#termsSubtitle");

        if ($termsSubtitle.length > 0) {
            $termsSubtitle.html(
                '<small><span class="loading-spinner"></span> Loading terms...</small>',
            );
        }

        $.ajax({
            url: this.config.routes.scheduleHeaders,
            method: "POST",
            data: {
                opportunity_id: data.dealId,
                class: data.class,
                class_group: data.classGroup,
                business_type: data.typeOfBus,
            },
            success: (response) => {
                if (response.success && response.headers) {
                    if ($termsSubtitle.length > 0) {
                        $termsSubtitle.html(
                            `<small>Terms for ${
                                response.class_name || "this class"
                            }</small>`,
                        );
                    }
                    this.renderScheduleHeaders(response.headers, data);
                } else {
                    if ($termsSubtitle.length > 0) {
                        $termsSubtitle.html(`<small>No terms found</small>`);
                    }
                    this.renderScheduleHeaders([], data);
                }
            },
            error: (xhr, status, error) => {
                this.handleError("Error loading schedule headers", {
                    xhr,
                    status,
                    error,
                });
                this.showError("Failed to load schedule headers");
                if ($termsSubtitle.length > 0) {
                    $termsSubtitle.html(`<small>Error loading terms</small>`);
                }
            },
        });
    }

    loadSelectedReinsurers(data) {
        const opportunityId = data.opportunityId || data.dealId;
        if (!opportunityId) {
            return;
        }

        const $modal = $(`#${data.modalId}`);
        const $table = $modal.find("#propReinsurersTable");

        if (!$.fn.DataTable.isDataTable($table)) {
            this.showTableLoading($table);
        }

        $.ajax({
            url: this.config.routes.getSelectedReinsurers,
            method: "GET",
            data: {
                opportunity_id: opportunityId,
            },
            success: (response) => {
                if (response.success) {
                    $modal.find("#reinsurerCount").text(response.count ?? 0);
                    const reinsurers =
                        response.data.length > 0 ? response.data : [];

                    $(".reinsurers_data").val(JSON.stringify(reinsurers) || []);

                    $modal
                        .find(".selected_reinsurers")
                        .val(JSON.stringify(reinsurers));

                    $modal.trigger("pipeline:reinsurers-loaded", [
                        {
                            reinsurers,
                            count: response.count ?? reinsurers.length,
                        },
                    ]);
                    this.renderReinsurersTable(reinsurers, $table, data);
                }
            },
            error: (xhr, status, error) => {
                this.handleError("Error loading selected reinsurers", {
                    xhr,
                    status,
                    error,
                });
                this.showError("Failed to load selected reinsurers");
                this.renderReinsurersTable([], $table, data);
            },
        });
    }

    renderReinsurersTable(reinsurers, $table, data) {
        if (!$table || $table.length === 0) {
            return;
        }

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().destroy();
        }

        $table.find("tbody").empty();

        const tableData = this.transformReinsurerData(reinsurers);
        const totalShare = tableData.reduce(
            (sum, r) => sum + parseFloat(r.written_share || 0),
            0,
        );

        const dataTable = $table.DataTable({
            data: tableData,
            columns: this.getReinsurerColumns(),
            paging: false,
            searching: false,
            info: false,
            ordering: true,
            order: [[1, "desc"]],
            language: {
                emptyTable: "No reinsurers selected",
            },
            drawCallback: () => {
                this.initializeReinsurerActions($table);
            },
        });

        this.reinsurerDataTable = dataTable;
        this.updatePlacedShare(totalShare);
    }
    transformReinsurerData(reinsurers) {
        return reinsurers.map((reinsurer) => {
            const isDeclined =
                reinsurer.is_declined === true ||
                Number(reinsurer.is_declined) === 1;
            const parsedWrittenShare = parseFloat(reinsurer.written_share || 0);
            const normalizedWrittenShare = Number.isFinite(parsedWrittenShare)
                ? parsedWrittenShare
                : 0;

            return {
                id: reinsurer.reinsurer_id,
                name: reinsurer.reinsurer_name,
                written_share: (isDeclined
                    ? 0
                    : normalizedWrittenShare
                ).toFixed(2),
                previous_written_share: normalizedWrittenShare.toFixed(2),
                commission: parseFloat(reinsurer.brokerage_rate || 0).toFixed(
                    2,
                ),
                status: reinsurer.status,
                is_declined: isDeclined,
                country: reinsurer.country,
                contact: reinsurer.email || "-",
                action: "",
            };
        });
    }

    calculateTotals(tableData) {
        const totalShare = tableData.reduce(
            (sum, r) => sum + parseFloat(r.written_share),
            0,
        );
        const totalCommission = tableData.reduce(
            (sum, r) => sum + parseFloat(r.commission),
            0,
        );

        return { totalShare, totalCommission };
    }

    getReinsurerColumns() {
        return [
            {
                data: "name",
                title: "Reinsurer",
                render: (data, type, row) => {
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
                },
            },
            {
                data: "written_share",
                title: "Written Share (%)",
                className: "text-left",
                render: (data, type, row) => {
                    const isDeclined = row.is_declined;

                    if (isDeclined) {
                        return "--";
                    }

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
                },
            },
            {
                data: "action",
                title: "Action",
                orderable: false,
                searchable: false,
                className: "text-left",
                render: (data, type, row) => {
                    const isDeclined = row.is_declined;

                    if (isDeclined) {
                        return `<span class="badge bg-danger">Declined</span>`;
                    }

                    const escapedName = this.escapeHtml(row.name);
                    return `
                    <div>
                        <button type="button" class="btn btn-primary btn-sm contact-reinsurer-btn"
                            data-reinsurer-id="${row.id}"
                            data-reinsurer-name="${escapedName}"
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
                },
            },
        ];
    }

    getShareBadgeClass(percentage) {
        if (percentage >= 50) return "bg-success";
        if (percentage >= 25) return "bg-primary";
        return "bg-info";
    }

    showTableLoading($table) {
        const $tbody = $table.find("tbody");
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

    initializeReinsurerActions($table) {
        $table.off("click", ".edit-reinsurer-btn");
        $table.off("click", ".decline-reinsurer-btn");
        $table.off("click", ".contact-reinsurer-btn");

        $table.on("click", ".edit-reinsurer-btn", (e) => {
            e.preventDefault();
            e.stopPropagation();

            const reinsurerData = {
                id: $(e.currentTarget).data("reinsurer-id"),
                reinsurerName: $(e.currentTarget).data("reinsurer-name"),
                written_share: $(e.currentTarget).data("written-share"),
                previous_written_share: $(e.currentTarget).data(
                    "previous-written-share",
                ),
            };

            this.handleEditReinsurer(reinsurerData, $table);
        });

        $table.on("click", ".decline-reinsurer-btn", (e) => {
            e.preventDefault();
            e.stopPropagation();

            const reinsurerData = {
                id: $(e.currentTarget).data("reinsurer-id"),
                reinsurerName: $(e.currentTarget).data("reinsurer-name"),
            };

            this.handleDeclineReinsurer(reinsurerData, $table);
        });

        $table.on("click", ".contact-reinsurer-btn", (e) => {
            e.preventDefault();
            e.stopPropagation();

            const reinsurerData = {
                id: $(e.currentTarget).data("reinsurer-id"),
                reinsurerName:
                    $(e.currentTarget).data("reinsurer-name") ||
                    $(e.currentTarget)
                        .closest("tr")
                        .find("td:first .fw-medium")
                        .text()
                        .trim(),
            };

            this.handleContactReinsurer(
                reinsurerData,
                $table,
                $(e.currentTarget),
            );
        });
    }

    handleContactReinsurer(reinsurerData, $table, $button) {
        const reinsurerId = reinsurerData?.id;
        const reinsurerName = reinsurerData?.reinsurerName || "Reinsurer";
        const opportunityId =
            $table.closest(".modal").find(".opportunity_id").val() ||
            $(".opportunity_id").first().val() ||
            this.currentDealId;

        if (!reinsurerId) {
            this.showError("Reinsurer ID not found");
            return;
        }

        if (!opportunityId) {
            this.showError("Opportunity ID is required to load contacts");
            return;
        }

        if ($button.data("contact-loading")) {
            return;
        }

        const originalHtml = $button.html();
        $button.data("contact-loading", true);
        $button
            .html('<i class="bx bx-loader bx-spin"></i>')
            .prop("disabled", true);

        $.ajax({
            url: "/customer/contact-info",
            method: "GET",
            data: {
                customer_id: reinsurerId,
                opportunity_id: opportunityId,
            },
            success: (response) => {
                if (!response?.success) {
                    this.showError(
                        response?.message || "Failed to fetch contacts",
                    );
                    return;
                }

                this.populatePropContactsModal(response, reinsurerName);

                const $proposalModal = $("#proposalModal");
                if ($proposalModal.hasClass("show")) {
                    $proposalModal.modal("hide");
                }
                $("#propContactsModal").modal("show");
            },
            error: (xhr) => {
                const errorMessage =
                    xhr.responseJSON?.message ||
                    "Failed to fetch reinsurer contacts";
                this.showError(errorMessage);
            },
            complete: () => {
                $button.html(originalHtml).prop("disabled", false);
                $button.removeData("contact-loading");
            },
        });
    }

    populatePropContactsModal(response, customerName) {
        const $modal = $("#propContactsModal");
        if ($modal.length === 0) {
            this.showError("Contacts modal is unavailable");
            return;
        }

        const escapeHtml = (value) => this.escapeHtml(value || "");
        $("#propContactsModalLabel").html(
            `<i class="bx bx-building me-1"></i>${escapeHtml(customerName)} - Contact Management`,
        );

        const primaryContact = response?.primary_contact || null;
        $("#prop-primary-contacts .prop-primary-name").val(
            primaryContact?.contact_name || "N/A",
        );
        $("#prop-primary-contacts .prop-primary-email").val(
            primaryContact?.contact_email || "N/A",
        );
        $("#prop-primary-contacts .prop-primary-contact_id").val(
            primaryContact?.contact_id || "",
        );

        const $departmentContacts = $("#propDepartmentContacts");
        $departmentContacts.empty();

        const departmentContacts = Array.isArray(response?.department_contacts)
            ? response.department_contacts
            : [];

        if (departmentContacts.length === 0) {
            $departmentContacts.html(`
                <div class="text-center py-4">
                    <i class="bx bx-info-circle bx-2x text-muted mb-2 fs-15"></i>
                    <p class="text-muted">No department contacts found.</p>
                </div>
            `);
            return;
        }

        departmentContacts.forEach((contact, index) => {
            const showLabels = index === 0;
            const contactHtml = `
                <div class="contact-item rounded px-3 pb-1 mb-3" data-contact-id="${escapeHtml(contact.contact_id || index)}">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            ${showLabels ? '<label class="form-label fw-semibold small">Name</label>' : ""}
                            <input type="text" class="form-control-plaintext"
                                value="${escapeHtml(contact.contact_name || "N/A")}" readonly>
                        </div>
                        <div class="col-md-4">
                            ${showLabels ? '<label class="form-label fw-semibold small">Email</label>' : ""}
                            <input type="email" class="form-control-plaintext"
                                value="${escapeHtml(contact.contact_email || "N/A")}" readonly>
                        </div>
                        <div class="col-md-2">
                            ${showLabels ? '<label class="form-label fw-semibold small">Mobile</label>' : ""}
                            <input type="text" class="form-control-plaintext"
                                value="${escapeHtml(contact.contact_mobile_no || "N/A")}" readonly>
                        </div>
                        <div class="col-md-3">
                            ${showLabels ? '<label class="form-label fw-semibold small">Position</label>' : ""}
                            <input type="text" class="form-control-plaintext"
                                value="${escapeHtml(contact.contact_position || "N/A")}" readonly>
                        </div>
                    </div>
                </div>
            `;
            $departmentContacts.append(contactHtml);
        });
    }

    handleDeclineReinsurer(reinsurerData, $table) {
        if ($("#declineReinsurerModal").length === 0) {
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
            $("body").append(modalHtml);
        }

        $("#reinDecTxt").val("").removeClass("is-invalid");
        $("#declineReasonError").text("");

        $("#declineReinsurerModal").modal("show");

        $("#confirmDecline")
            .off("click")
            .on("click", () => {
                const declineReason = $("#reinDecTxt").val().trim();
                const $textarea = $("#reinDecTxt");
                const $error = $("#declineReasonError");

                if (!declineReason) {
                    $textarea.addClass("is-invalid");
                    $error.text("Please provide a reason for declining");
                    return;
                }

                const data = {
                    reinsurerId: reinsurerData.id,
                    opportunityId: $("#proposalModal")
                        .find(".opportunity_id")
                        .val(),
                };

                $.ajax({
                    url: this.config.routes.declineReinsurer,
                    method: "POST",
                    data: {
                        reinsurerId: data.reinsurerId,
                        opportunityId: data.opportunityId,
                        declineReason: declineReason,
                    },
                    success: (response) => {
                        if (response.status === 1) {
                            toastr.success("Reinsurer declined successfully");
                            const $proposalModal = $("#proposalModal");
                            const refreshData = {
                                dealId: data.opportunityId,
                                opportunityId: data.opportunityId,
                                modalId: "proposalModal",
                                class: $proposalModal.find(".class_code").val(),
                                classGroup: $proposalModal
                                    .find(".class_group_code")
                                    .val(),
                                typeOfBus:
                                    window.currentDealInfo?.type_of_business,
                                stage: "proposal",
                                categoryType: $proposalModal
                                    .find(".category_type")
                                    .val(),
                            };

                            this.loadSelectedReinsurers({
                                ...refreshData,
                            });
                            this.loadSlipDocuments(refreshData);
                            this.reloadAllTables();
                            this.loadChartData();
                            $("#declineReinsurerModal").modal("hide");
                        } else {
                            toastr.error("An error occured!");
                        }
                    },
                    error: (xhr, status, error) => {
                        console.error(error);
                        $("#declineReinsurerModal").modal("hide");
                    },
                });
            });
    }

    handleEditReinsurer(reinsurerData, $table) {
        const escapedName = this.escapeHtml(reinsurerData.reinsurerName);

        $("#editReinsurerShareModal").remove();

        const modalHtml = `
            <div class="modal fade mod-popup effect-scale" id="editReinsurerShareModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Edit Written Share</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body pt-4 pb-3">
                            <div class="text-center mb-3">
                                <small class="text-muted fw-semibold">${escapedName}</small>
                            </div>
                            <div class="form-group">
                                <label for="editShareInput" class="form-label">Written Share (%)</label>
                                <div class="input-group">
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="editShareInput"
                                        min="0.01"
                                        max="100"
                                        step="0.01"
                                        placeholder="50.00"
                                    >
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="invalid-feedback" id="shareInputError"></div>
                                <small class="text-muted mt-2 d-block">Enter a value between 0.01 and 100</small>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="confirmShareUpdate">
                                <i class="bx bx-check me-1"></i>Update Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $("body").append(modalHtml);
        const shareInputVal =
            parseInt(reinsurerData.written_share) === 0
                ? reinsurerData.previous_written_share
                : reinsurerData.written_share;

        $("#editShareInput").val(shareInputVal || "0.00");
        $("#editShareInput").removeClass("is-invalid");
        $("#shareInputError").text("");

        const editModal = new bootstrap.Modal(
            document.getElementById("editReinsurerShareModal"),
        );

        $("#editReinsurerShareModal").one("shown.bs.modal", function () {
            $("#editShareInput").focus().select();
        });

        $("#editReinsurerShareModal").one("hidden.bs.modal", function () {
            $("#editReinsurerShareModal").remove();
        });

        $("#confirmShareUpdate")
            .off("click")
            .on("click", () => {
                const value = $("#editShareInput").val();
                const numValue = parseFloat(value);

                if (value === "" || isNaN(numValue)) {
                    $("#editShareInput").addClass("is-invalid");
                    $("#shareInputError").text("Please enter a valid number");
                    return;
                }

                if (numValue <= 0 || numValue > 100) {
                    $("#editShareInput").addClass("is-invalid");
                    $("#shareInputError").text(
                        "Please enter a value between 0.01 and 100",
                    );
                    return;
                }

                this.updateReinsurerShare(reinsurerData.id, value, $table);
                editModal.hide();
            });

        $("#editShareInput").on("keypress", (e) => {
            if (e.which === 13) {
                e.preventDefault();
                $("#confirmShareUpdate").click();
            }
        });

        editModal.show();
    }

    handleRemoveReinsurer(reinsurerId, $table) {
        Swal.fire({
            title: "Remove Reinsurer?",
            text: "Are you sure you want to remove this reinsurer?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, remove",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                this.removeReinsurer(reinsurerId, $table);
            }
        });
    }

    updateReinsurerShare(reinsurerId, newShare, $table) {
        const dataTable = $table.DataTable();
        const rowData = dataTable.rows().data().toArray();

        const updatedData = rowData.map((row) => {
            if (row.id === reinsurerId) {
                return {
                    ...row,
                    written_share: parseFloat(newShare).toFixed(2),
                };
            }
            return row;
        });

        dataTable.clear();
        dataTable.rows.add(updatedData);
        dataTable.draw();

        const totalShare = updatedData.reduce(
            (sum, r) => sum + parseFloat(r.written_share),
            0,
        );

        $(".selected_reinsurers").val(JSON.stringify(updatedData) || []);

        this.updatePlacedShare(totalShare);
        toastr.success("Reinsurer share updated successfully");
    }

    updatePlacedShare(totalShare) {
        // const $modal = $("#proposalModal");
        // const unPlacedShare = 100 - totalShare;
        // const totalPlacedShares = Number(totalShare || 0).toFixed(2);
        // const totalUnplacedShares = Number(unPlacedShare).toFixed(2);
        // $modal.find("#propPlacedShare").val(totalPlacedShares);
        // $modal.find("#propUnPlacedShare").val(totalUnplacedShares);
        // let sharesDisplay = $modal.find(".proposal-total-shares-display");
        // const placedNum = Number(totalPlacedShares);
        // const unplacedNum = Number(totalUnplacedShares);
        // const targetTotal = 100;
        // const placedValueClass =
        //     placedNum === targetTotal
        //         ? "text-success"
        //         : placedNum > targetTotal
        //           ? "text-danger"
        //           : "text-primary";
        // sharesDisplay
        //     .find(".proposal-placed-value")
        //     .removeClass("text-success text-danger text-primary text-warning")
        //     .addClass(placedValueClass)
        //     .text(`${totalPlacedShares}%`);
        // const unplacedValueClass =
        //     unplacedNum === 0
        //         ? "text-success"
        //         : unplacedNum < 0
        //           ? "text-danger"
        //           : "text-warning";
        // sharesDisplay
        //     .find(".proposal-unplaced-value")
        //     .removeClass("text-success text-danger text-primary text-warning")
        //     .addClass(unplacedValueClass)
        //     .text(`${totalUnplacedShares}%`);
        // let progressWidth = 0;
        // if (targetTotal > 0) {
        //     progressWidth = (placedNum / targetTotal) * 100;
        //     progressWidth = Math.min(progressWidth, 100);
        // }
        // const progressClass =
        //     placedNum === targetTotal
        //         ? "bg-success"
        //         : placedNum > targetTotal
        //           ? "bg-danger"
        //           : "bg-primary";
        // sharesDisplay
        //     .find(".proposal-placed-progress")
        //     .removeClass("bg-success bg-danger bg-primary")
        //     .addClass(progressClass)
        //     .css("width", `${progressWidth}%`)
        //     .attr("aria-valuenow", progressWidth)
        //     .attr("aria-valuemax", 100);
        // $("#retainedShareValue").val(totalUnplacedShares);
    }

    removeReinsurer(reinsurerId, $table) {
        const dataTable = $table.DataTable();
        const rowData = dataTable.rows().data().toArray();

        const updatedData = rowData.filter((row) => row.id !== reinsurerId);

        dataTable.clear();
        dataTable.rows.add(updatedData);
        dataTable.draw();

        const totalShare = updatedData.reduce(
            (sum, r) => sum + parseFloat(r.written_share),
            0,
        );

        const $counterBadge = $("#reinsurerCount");
        if ($counterBadge.length) {
            $counterBadge.text(updatedData.length);
        }

        if (typeof toastr !== "undefined") {
            toastr.success("Reinsurer removed successfully");
        }

        this.updatePlacedShare(totalShare);
    }

    loadBdTerms(data) {
        if (!data?.opportunityId && !data?.dealId) {
            return;
        }

        $.ajax({
            url: this.config.routes.getBdTerms,
            method: "GET",
            data: {
                opportunity_id: data.opportunityId || data.dealId,
            },
            success: (response) => {
                if (response.success) {
                    const cacheKey = `${data.modalId}:${data.opportunityId || data.dealId}`;
                    this.bdTermsCache.set(cacheKey, response.data || []);
                    this.renderBdTerms(response.data || [], data);
                }
            },
            error: (xhr, status, error) => {
                this.handleError("Error loading BD terms", {
                    xhr,
                    status,
                    error,
                });
                this.showError("Failed to load BD terms");
            },
        });
    }

    renderBdTerms(data, dealInfo) {
        const $modal = $(`#${dealInfo.modalId}`);
        if (!$modal.length || !Array.isArray(data) || data.length === 0) {
            return;
        }

        for (let i = 0; i < data.length; i++) {
            const v = data[i];
            const title = v.title;
            const content = v.content;
            const short_content = v.short_content;

            const $plain = $modal.find(`#${title}`);
            const $html = $modal.find(`#${title}Content`);
            if (!$plain.length || !$html.length) {
                continue;
            }

            const plainText = $("<div>")
                .html(short_content || "")
                .text();
            $plain.val(plainText);
            $html.val(content || "");
        }
    }

    loadSlipDocuments(data) {
        if (!data.dealId) {
            return;
        }

        const $modal = $(`#${data.modalId}`);
        const $documentsSubtitle = $modal.find("#documentsSubtitle");

        if ($documentsSubtitle.length > 0) {
            $documentsSubtitle.html(
                '<small><span class="loading-spinner"></span> Loading documents...</small>',
            );
        }

        $.ajax({
            url: this.config.routes.slipDocuments,
            method: "POST",
            data: {
                opportunity_id: data.dealId,
                class: data.class,
                class_group: data.classGroup,
                business_type: data.typeOfBus,
                stage: data.stage,
                current_stage: data.currentStage,
                category_type: data.categoryType,
            },
            success: (response) => {
                if (response.status) {
                    if ($documentsSubtitle.length > 0) {
                        $documentsSubtitle.html(
                            `<small>Documents for ${
                                response.class_name || "this class"
                            }</small>`,
                        );
                    }
                    this.renderSlipDocuments(response, data, $modal);
                } else {
                    if ($documentsSubtitle.length > 0) {
                        $documentsSubtitle.html(
                            `<small>No documents found</small>`,
                        );
                    }
                }
            },
            error: (xhr, status, error) => {
                this.handleError("Error loading slip documents", {
                    xhr,
                    status,
                    error,
                });
                this.showError("Failed to load slip documents");
                if ($documentsSubtitle.length > 0) {
                    $documentsSubtitle.html(
                        `<small>Error loading documents</small>`,
                    );
                }
            },
        });
    }

    renderScheduleHeaders(headers, data) {
        if (!data?.modalId) {
            return;
        }

        const $modal = $(`#${data.modalId}`);
        const container = $modal.find("#termsConditions");
        const reinsurerCount = parseInt(
            ($modal.find("#reinsurerCount").first().text() || "0").toString(),
            10,
        );
        const hasSelectedReinsurers = Number.isFinite(reinsurerCount)
            ? reinsurerCount > 0
            : false;
        if (container.length === 0) {
            return;
        }

        container.empty();

        if (!Array.isArray(headers)) {
            container.html(
                '<p class="text-muted text-center my-4">Invalid headers data.</p>',
            );
            return;
        }

        const validHeaders = headers.filter((h) => {
            if (!h) {
                return false;
            }

            const normalizedSumInsuredType = (
                h.sum_insured_type ??
                h.type_of_sum_insured ??
                ""
            )
                .toString()
                .trim();
            const hasValidSumInsuredType = normalizedSumInsuredType === "";
            const isNotExcluded = !EXCLUDED_TERMS.some((term) =>
                h.name?.replace(/\s+/g, " ").trim().includes(term),
            );

            return hasValidSumInsuredType && isNotExcluded;
        });

        validHeaders.sort((a, b) => {
            const positionA = parseInt(a.position) || 0;
            const positionB = parseInt(b.position) || 0;
            return positionA - positionB;
        });

        const deductible = headers.filter((h) =>
            DEDUCTIBLE_TERMS.some((term) =>
                h?.name?.replace(/\s+/g, " ").trim().includes(term),
            ),
        );

        $(".deductible_excess_div").hide();
        if (deductible?.length > 0) {
            $(".deductible_excess_div").show();
        }

        if (validHeaders.length === 0) {
            container.html(
                '<p class="text-muted text-center my-4">No schedule headers configured.</p>',
            );
            const $docsSection = $modal
                .find("#documentsContent")
                .closest(".form-section");
            if ($docsSection.length) {
                if (data.modalId === "leadModal" && !hasSelectedReinsurers) {
                    $docsSection.hide();
                    $docsSection.prev("hr").hide();
                } else {
                    $docsSection.show();
                    $docsSection.prev("hr").show();
                }
            }
            return;
        }

        const $docsSection = $modal
            .find("#documentsContent")
            .closest(".form-section");
        if ($docsSection.length) {
            if (data.modalId === "leadModal" && !hasSelectedReinsurers) {
                $docsSection.hide();
                $docsSection.prev("hr").hide();
            } else {
                $docsSection.show();
                $docsSection.prev("hr").show();
            }
        }

        let fieldsHtml = "";
        validHeaders.forEach((header, index) => {
            if (index % 2 === 0) {
                fieldsHtml += '<div class="row">';
            }

            let headerName = this.capitalize(header.name);
            const fieldId = this.toPascalCase(header.name);
            let fieldInput = this.generateFieldInput(header, fieldId);

            let hiddenInput = `<input type="hidden" id="${fieldId}Content" data-sch_id="${header.id}" name="${fieldId}Content" />`;

            fieldsHtml += `
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="${fieldId}" class="form-label capitalize">
                            ${headerName}${
                                header.amount_field === "Y"
                                    ? ' <span class="text-danger pl-1">*</span>'
                                    : ""
                            }
                        </label>
                        ${fieldInput}
                        ${hiddenInput}
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            `;

            if (index % 2 === 1 || index === validHeaders.length - 1) {
                fieldsHtml += "</div>";
            }
        });

        container.html(fieldsHtml);

        const cacheKey = `${data.modalId}:${data.opportunityId || data.dealId}`;
        const cachedTerms = this.bdTermsCache.get(cacheKey);
        if (Array.isArray(cachedTerms) && cachedTerms.length > 0) {
            this.renderBdTerms(cachedTerms, data);
        }

        if (typeof this.setupFieldValidation === "function") {
            this.setupFieldValidation($modal);
        }
    }

    toPascalCase(rawTxt) {
        return rawTxt
            .replace(/['"]/g, "")
            .split(/\s+/)
            .map((word, index) => {
                let clean = word.toLowerCase();
                if (index === 0) {
                    return clean;
                }
                return clean.charAt(0).toUpperCase() + clean.slice(1);
            })
            .join("");
    }

    renderSlipDocuments(res, data, $modal) {
        this.renderSupportingDocumentsReinsurerStatus(res, data, $modal);

        const existingDocuments = Array.isArray(res.prosp_doc)
            ? res.prosp_doc
            : Array.isArray(res.prospect_dcos)
              ? res.prospect_dcos
              : Array.isArray(res.prospect_docs)
                ? res.prospect_docs
                : [];
        const hasConfiguredStageDocs =
            Array.isArray(res.docs) && res.docs.length > 0;

        if (
            data?.modalId === "proposalModal" ||
            data?.modalId === "leadModal" ||
            data?.modalId === "negotiationModal"
        ) {
            const prospectDocuments = existingDocuments.filter((doc) => {
                const fileName = (doc?.file || "").toString().trim();
                return fileName.length > 0;
            });

            const mergedDocs = hasConfiguredStageDocs ? [...res.docs] : [];
            const mergedDocKeys = new Set();

            const rememberDocKeys = (doc) => {
                const keys = [
                    this.normalizeDocumentKey(doc?.name),
                    this.normalizeDocumentKey(doc?.doc_type),
                    this.normalizeDocumentKey(doc?.file_name),
                    this.normalizeDocumentKey(
                        this.toPascalCase(doc?.name || ""),
                    ),
                    this.normalizeDocumentKey(
                        this.toPascalCase(doc?.doc_type || ""),
                    ),
                ].filter(Boolean);

                keys.forEach((key) => mergedDocKeys.add(key));
            };

            mergedDocs.forEach((doc) => rememberDocKeys(doc));

            prospectDocuments.forEach((doc) => {
                const docLabel = (
                    doc?.description ||
                    doc?.original_name ||
                    doc?.file ||
                    "Supporting Document"
                )
                    .toString()
                    .trim();

                const prospectDocKeys = [
                    this.normalizeDocumentKey(doc?.description),
                    this.normalizeDocumentKey(doc?.original_name),
                    this.normalizeDocumentKey(doc?.file),
                    this.normalizeDocumentKey(docLabel),
                    this.normalizeDocumentKey(this.toPascalCase(docLabel)),
                ].filter(Boolean);

                const alreadyExists = prospectDocKeys.some((key) =>
                    mergedDocKeys.has(key),
                );

                if (alreadyExists) {
                    return;
                }

                const prospectDoc = {
                    id: `prospect_doc_${doc.id}`,
                    name: docLabel,
                    doc_type: docLabel,
                    file_name: `prospectDoc_${doc.id}`,
                    mandatory: "N",
                    icon: "bx-file-blank",
                    mimetype: doc.mimetype || ".pdf,.doc,.docx,.jpg,.jpeg,.png",
                    description: doc?.description || "",
                    max_size: DEFAULT_MAX_FILE_SIZE,
                    multiple: false,
                    s3_path: doc?.s3_url || "",
                };

                mergedDocs.push(prospectDoc);
                rememberDocKeys(prospectDoc);
            });

            if (mergedDocs.length === 0) {
                const $container = $modal.find("#documentsContent");
                if ($container.length) {
                    $container.html(
                        '<p class="text-muted text-center my-3">No documents available for this stage.</p>',
                    );
                }
                return;
            }

            const isLeadModal = data?.modalId === "leadModal";
            let docs = [...mergedDocs];
            if (!isLeadModal) {
                docs = docs.filter(
                    (doc) =>
                        this.normalizeDocumentKey(doc?.file_name) !==
                        this.normalizeDocumentKey("additionalDocs"),
                );
            }
            const hasAdditionalDocsField = docs.some(
                (doc) =>
                    this.normalizeDocumentKey(doc?.file_name) ===
                    this.normalizeDocumentKey("additionalDocs"),
            );

            if (isLeadModal && !hasAdditionalDocsField) {
                docs.push({
                    name: "Additional Documents",
                    id: Math.floor(Math.random() * 10000),
                    file_name: "additionalDocs",
                    doc_type: "Additional Documents",
                    mandatory: "N",
                    icon: "bx-folder-plus",
                    accepts: ".pdf,.doc,.docx,.jpg,.jpeg,.png",
                    description: "Any additional supporting documents",
                    max_size: 5242880,
                    multiple: true,
                    s3_path: "",
                });
            }

            const transformedDocs = docs.map((doc) => {
                const existingDoc = this.findExistingDocumentForType(
                    existingDocuments,
                    doc,
                );

                return {
                    id: doc.id,
                    name: doc.name || doc.doc_type,
                    doc_type: doc.doc_type,
                    file_name: doc.file_name,
                    required: doc.mandatory === "Y",
                    icon: doc.icon ?? "bx-file-blank",
                    accepts:
                        doc.mimetype ??
                        doc.accepts ??
                        ".pdf,.doc,.docx,.jpg,.jpeg,.png",
                    description: doc.description ?? "",
                    max_size: doc.max_size ?? DEFAULT_MAX_FILE_SIZE,
                    multiple: doc.multiple ?? true,
                    existing_file_url: existingDoc?.s3_url || "",
                    existing_file_name:
                        existingDoc?.original_name || existingDoc?.file || "",
                    s3_path: doc.s3_path || "",
                };
            });

            this.generateDocumentFields(transformedDocs, $modal);
            return;
        }

        if (!res.docs || !res.docs.length) {
            const $container = $modal.find("#documentsContent");
            if ($container.length) {
                $container.html(
                    '<p class="text-muted text-center my-3">No documents available for this stage.</p>',
                );
            }
            return;
        }

        const isLeadModal = data?.modalId === "leadModal";
        let docs = [...res.docs];
        docs = isLeadModal
            ? docs
            : docs.filter(
                  (doc) =>
                      this.normalizeDocumentKey(doc?.file_name) !==
                      this.normalizeDocumentKey("additionalDocs"),
              );

        if (isLeadModal) {
            docs.push({
                name: "Additional Documents",
                id: Math.floor(Math.random() * 10000),
                file_name: "additionalDocs",
                doc_type: "Additional Documents",
                mandatory: "N",
                icon: "bx-folder-plus",
                accepts: ".pdf,.doc,.docx,.jpg,.jpeg,.png",
                description: "Any additional supporting documents",
                max_size: 5242880,
                multiple: true,
                s3_path: "",
            });
        }

        const transformedDocs = docs.map((doc) => {
            const existingDoc = this.findExistingDocumentForType(
                existingDocuments,
                doc,
            );

            return {
                id: doc.id,
                name: doc.name || doc.doc_type,
                doc_type: doc.doc_type,
                file_name: doc.file_name,
                required: doc.mandatory === "Y",
                icon: doc.icon ?? "bx-file-blank",
                accepts: doc.mimetype ?? ".pdf,.doc,.docx,.jpg,.jpeg,.png",
                description: doc.description ?? "",
                max_size: doc.max_size ?? DEFAULT_MAX_FILE_SIZE,
                multiple: doc.multiple ?? true,
                existing_file_url: existingDoc?.s3_url || "",
                existing_file_name:
                    existingDoc?.original_name || existingDoc?.file || "",
                s3_path: doc.s3_path || "",
            };
        });

        this.generateDocumentFields(transformedDocs, $modal);
    }

    renderSupportingDocumentsReinsurerStatus(res, data, $modal) {
        if (data?.modalId !== "proposalModal") {
            return;
        }

        const $documentsContent = $modal.find("#documentsContent");
        if ($documentsContent.length === 0) {
            return;
        }

        $documentsContent.find(".supporting-reinsurer-status").remove();

        const reinsurers = Array.isArray(res?.quoteReinsurers)
            ? res.quoteReinsurers
            : [];
        if (reinsurers.length === 0) {
            return;
        }

        const statusItems = reinsurers
            .map((reinsurer) => {
                const name = this.escapeHtml(
                    reinsurer?.reinsurer_name || "Unknown Reinsurer",
                );
                const isDeclined =
                    reinsurer?.is_declined === true ||
                    reinsurer?.is_declined === 1 ||
                    !!(
                        reinsurer?.decline_reason &&
                        reinsurer.decline_reason.toString().trim()
                    );
                const badgeClass = isDeclined ? "bg-danger" : "bg-success";
                const badgeText = isDeclined ? "Declined" : "Active";

                return `<span class="badge ${badgeClass} me-1 mb-1">${name}: ${badgeText}</span>`;
            })
            .join("");

        const statusHtml = `
            <div class="supporting-reinsurer-status mb-3">
                <div class="small text-muted mb-1">Reinsurer Status</div>
                <div>${statusItems}</div>
            </div>
        `;

        $documentsContent.prepend(statusHtml);
    }

    normalizeDocumentKey(value) {
        return (value || "")
            .toString()
            .toLowerCase()
            .replace(/[^a-z0-9]/g, "");
    }

    findExistingDocumentForType(existingDocuments, doc) {
        if (!Array.isArray(existingDocuments) || !doc) {
            return null;
        }

        const keys = [
            this.normalizeDocumentKey(doc.name),
            this.normalizeDocumentKey(doc.doc_type),
            this.normalizeDocumentKey(this.toPascalCase(doc.name || "")),
            this.normalizeDocumentKey(this.toPascalCase(doc.doc_type || "")),
            this.normalizeDocumentKey(doc.file_name),
        ].filter(Boolean);

        if (keys.length === 0) {
            return null;
        }

        return (
            existingDocuments.find((item) => {
                const descriptionKey = this.normalizeDocumentKey(
                    item?.description,
                );
                return keys.includes(descriptionKey);
            }) || null
        );
    }

    generateDocumentFields(documents, $modal) {
        const $container = $modal.find("#documentFields");
        const $placeholder = $modal.find("#documentPlaceholder");
        const $summarySection = $modal.find("#documentSummarySection");

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

        let hasRenderedAdditionalAddButton = false;
        documents.forEach((doc, index) => {
            const escapedName = this.escapeHtml(doc.name);
            const isAdditionalDocument = this.isAdditionalDocument(doc);
            const shouldShowAdditionalAddButton =
                isAdditionalDocument && !hasRenderedAdditionalAddButton;
            const existingFileUrl = (doc.s3_path || "").toString();
            const escapedExistingFileUrl = this.escapeHtml(existingFileUrl);
            const escapedExistingFileName = this.escapeHtml(
                doc.existing_file_name || "document",
            );
            const titleInputAttributes = isAdditionalDocument
                ? 'data-additional-title="1"'
                : "readonly";

            const fileAreaHtml = isAdditionalDocument
                ? `<div class="supporting-doc-upload-line"
                                    data-field="${doc.id}"
                                    data-field_name="${escapedName}"
                                    data-document-id="${doc.id ?? ""}"
                                    data-document-name="${escapedName}"
                                    data-is-additional="${isAdditionalDocument ? "1" : "0"}">
                                    <button type="button" class="supporting-doc-choose-btn">Choose File</button>
                                    <span class="supporting-doc-file-name">No file chosen</span>
                                    ${
                                        shouldShowAdditionalAddButton
                                            ? `<button type="button" class="supporting-doc-add-btn" title="Add file">
                                        <i class="bx bx-plus"></i>
                                    </button>`
                                            : ""
                                    }
                                    <button type="button" class="supporting-doc-view-trigger" title="View selected file" disabled>
                                        <i class="bx bx-show"></i>
                                    </button>
                                    <input type="file" class="d-none file-input"
                                        name="${doc.file_name}"
                                        ${doc.required ? "required" : ""}
                                        accept="${doc.accepts}"
                                        ${isAdditionalDocument ? "" : doc.multiple ? "multiple" : ""}
                                        data-max-size="${
                                            doc.max_size ||
                                            DEFAULT_MAX_FILE_SIZE
                                        }">
                                </div>`
                : `<div class="supporting-doc-static-line">
                                    <span class="supporting-doc-file-name">
                                        Document already populated
                                    </span>
                                    ${
                                        existingFileUrl
                                            ? `<a href="${escapedExistingFileUrl}" target="_blank" rel="noopener noreferrer" class="supporting-doc-preview-link" title="Preview ${escapedExistingFileName}">
                                        Preview
                                    </a>`
                                            : ""
                                    }
                                </div>`;

            const fieldHtml = `
                <div class="col-12 fade-in" style="animation-delay: ${
                    index * 0.1
                }s">
                    <div class="document-field-group supporting-doc-group"
                        data-document-id="${doc.id ?? ""}"
                        data-document-name="${escapedName}"
                        data-is-additional-document="${isAdditionalDocument ? "1" : "0"}">
                        <div class="supporting-doc-grid">
                            <div class="supporting-doc-title-col">
                                <label class="supporting-doc-label">Document Title</label>
                                <input type="text" class="form-control supporting-doc-title-input"
                                    value="${escapedName}" ${titleInputAttributes}>
                            </div>
                            <div class="supporting-doc-file-col">
                                <label class="supporting-doc-label">
                                    File${
                                        isAdditionalDocument && doc.required
                                            ? '<span class="text-danger">*</span>'
                                            : ""
                                    }
                                </label>
                                ${fileAreaHtml}
                            </div>
                            <div class="file-preview-container mt-2"></div>
                        </div>
                    </div>
                </div>
            `;
            $container.append(fieldHtml);
            if (shouldShowAdditionalAddButton) {
                hasRenderedAdditionalAddButton = true;
            }
        });

        if ($modal.find("#docCount").length) {
            $modal.find("#docCount").text(documents.length);
        }

        this.initializeFileUploads();

        if ($summarySection.length) $summarySection.show();
    }

    isAdditionalDocument(doc) {
        const name = (doc?.name || "").toString().toLowerCase();
        const fileName = (doc?.file_name || "").toString().toLowerCase();
        const docType = (doc?.doc_type || "").toString().toLowerCase();

        return (
            name.includes("additional") ||
            fileName.includes("additional") ||
            docType.includes("additional")
        );
    }

    createAdditionalFieldId() {
        return `additional_${Date.now()}_${Math.random()
            .toString(36)
            .slice(2, 10)}`;
    }

    createAdditionalDocumentRowHtml(config = {}) {
        const fieldId = this.createAdditionalFieldId();
        const accepts = this.escapeHtml(
            config.accepts || ".pdf,.doc,.docx,.jpg,.jpeg,.png",
        );
        const maxSize = parseInt(config.maxSize, 10) || DEFAULT_MAX_FILE_SIZE;
        const defaultTitle = this.escapeHtml(
            config.defaultTitle || "Additional Documents",
        );
        const showAddButton = config.showAddButton !== false;
        const showRemoveButton = config.showRemoveButton === true;

        return `
            <div class="col-12 fade-in">
                <div class="document-field-group supporting-doc-group"
                    data-document-id=""
                    data-document-name="${defaultTitle}"
                    data-is-additional-document="1">
                    <div class="supporting-doc-grid">
                        <div class="supporting-doc-title-col">
                            <label class="supporting-doc-label">Document Title</label>
                            <input type="text" class="form-control supporting-doc-title-input"
                                value="${defaultTitle}" data-additional-title="1" placeholder="Enter document title">
                        </div>
                        <div class="supporting-doc-file-col">
                            <label class="supporting-doc-label">File</label>
                            <div class="supporting-doc-upload-line"
                                data-field="${fieldId}"
                                data-field_name="${defaultTitle}"
                                data-document-id=""
                                data-document-name="${defaultTitle}"
                                data-is-additional="1">
                                <button type="button" class="supporting-doc-choose-btn">Choose File</button>
                                <span class="supporting-doc-file-name">No file chosen</span>
                                ${
                                    showAddButton
                                        ? `<button type="button" class="supporting-doc-add-btn" title="Add file">
                                    <i class="bx bx-plus"></i>
                                </button>`
                                        : ""
                                }
                                ${
                                    showRemoveButton
                                        ? `<button type="button" class="supporting-doc-row-remove-btn" title="Remove document">
                                    <i class="bx bx-trash"></i>
                                </button>`
                                        : ""
                                }
                                <button type="button" class="supporting-doc-view-trigger" title="View selected file" disabled>
                                    <i class="bx bx-show"></i>
                                </button>
                                <input type="file" class="d-none file-input"
                                    name="additionalDocs"
                                    accept="${accepts}"
                                    data-max-size="${maxSize}">
                            </div>
                        </div>
                        <div class="file-preview-container mt-2"></div>
                    </div>
                </div>
            </div>
        `;
    }

    addAdditionalDocumentRow($uploadArea) {
        const $groupColumn = $uploadArea.closest(".col-12");
        if ($groupColumn.length === 0) {
            return;
        }

        const $input = $uploadArea.find(".file-input");
        const rowHtml = this.createAdditionalDocumentRowHtml({
            accepts: $input.attr("accept"),
            maxSize: $input.data("max-size"),
            defaultTitle: "Additional Documents",
            showAddButton: false,
            showRemoveButton: true,
        });

        $groupColumn.after(rowHtml);
        this.initializeFileUploads();
    }

    removeAdditionalDocumentRow($removeBtn) {
        const $uploadArea = $removeBtn
            .closest(".supporting-doc-upload-line")
            .first();
        const fieldId = $uploadArea.data("field");

        if (fieldId && this.uploadedFiles[fieldId]) {
            delete this.uploadedFiles[fieldId];
        }

        const $groupColumn = $removeBtn.closest(".col-12");
        if ($groupColumn.length > 0) {
            $groupColumn.remove();
        }
    }

    syncAdditionalDocumentFieldName($titleInput) {
        const $group = $titleInput.closest(".supporting-doc-grid");
        const $uploadArea = $group.find(".supporting-doc-upload-line").first();
        if ($uploadArea.length === 0) {
            return;
        }

        const fieldId = $uploadArea.data("field");
        const title = ($titleInput.val() || "").toString().trim();
        const normalizedTitle = title || "Additional Documents";
        const normalizedFileName =
            this.toPascalCase(normalizedTitle) || "additionalDocuments";

        $uploadArea.attr("data-field_name", normalizedTitle);
        $uploadArea.data("field_name", normalizedTitle);
        $uploadArea.attr("data-document-name", normalizedTitle);
        $uploadArea.data("document-name", normalizedTitle);
        const $fieldGroup = $uploadArea.closest(".document-field-group");
        $fieldGroup.attr("data-document-name", normalizedTitle);
        $fieldGroup.data("document-name", normalizedTitle);

        if (fieldId && Array.isArray(this.uploadedFiles[fieldId])) {
            this.uploadedFiles[fieldId].forEach((file) => {
                file.fileName = normalizedFileName;
                file.documentTypeName = normalizedTitle;
            });
        }
    }

    resolveDocumentFieldName($uploadArea) {
        const $titleInput = $uploadArea
            .closest(".supporting-doc-grid")
            .find(".supporting-doc-title-input")
            .first();

        const titleFromInput = ($titleInput.val() || "").toString().trim();
        const titleFromData = ($uploadArea.data("field_name") || "")
            .toString()
            .trim();

        return titleFromInput || titleFromData || "additionalDocuments";
    }

    resolveDocumentTypeId($uploadArea, fieldId) {
        const rawDocumentId = ($uploadArea.data("document-id") ?? fieldId)
            .toString()
            .trim();
        if (!/^\d+$/.test(rawDocumentId)) {
            return null;
        }

        return parseInt(rawDocumentId, 10);
    }

    initializeFileUploads() {
        const uploadSelector = ".supporting-doc-upload-line, .file-upload-area";
        $(uploadSelector).off(".fileUpload");
        $(".file-input").off(".fileUpload");
        $(
            ".supporting-doc-choose-btn, .supporting-doc-add-btn, .supporting-doc-view-trigger",
        ).off(".fileUpload");
        $(".supporting-doc-row-remove-btn").off(".fileUpload");
        $(".supporting-doc-title-input[data-additional-title='1']").off(
            ".additionalTitle",
        );

        $(uploadSelector).each((index, element) => {
            const $uploadArea = $(element);
            const $input = $uploadArea.find(".file-input");
            const $previewContainer = $uploadArea.siblings(
                ".file-preview-container",
            );
            const $chooseBtn = $uploadArea.find(".supporting-doc-choose-btn");
            const $addBtn = $uploadArea.find(".supporting-doc-add-btn");
            const $removeRowBtn = $uploadArea.find(
                ".supporting-doc-row-remove-btn",
            );
            const $viewBtn = $uploadArea.find(".supporting-doc-view-trigger");

            $chooseBtn.on("click.fileUpload", (e) => {
                e.preventDefault();
                e.stopPropagation();
                if ($input.length > 0 && $input[0]) {
                    $input[0].click();
                }
            });

            $addBtn.on("click.fileUpload", (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.addAdditionalDocumentRow($uploadArea);
            });

            $removeRowBtn.on("click.fileUpload", (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.removeAdditionalDocumentRow($removeRowBtn);
            });

            $viewBtn.on("click.fileUpload", (e) => {
                e.preventDefault();
                e.stopPropagation();
                const fieldId = $uploadArea.data("field");
                const firstFile = this.uploadedFiles[fieldId]?.[0];
                if (firstFile) {
                    this.viewFile(fieldId, firstFile.fileId);
                }
            });

            $uploadArea.on("click.fileUpload", (e) => {
                const $target = $(e.target);
                const isInteractiveElement =
                    $target.is(
                        "button, input, a, .file-action-btn, .file-remove-btn",
                    ) ||
                    $target.closest(
                        "button, .file-action-btn, .file-remove-btn",
                    ).length > 0;

                if (!isInteractiveElement) {
                    e.preventDefault();
                    e.stopPropagation();

                    if ($input.length > 0 && $input[0]) {
                        $input[0].click();
                    }
                }
            });

            $input.on("change.fileUpload", (e) => {
                e.stopPropagation();
                if (e.target.files && e.target.files.length > 0) {
                    this.handleFileSelection(
                        e.target.files,
                        $uploadArea,
                        $previewContainer,
                    );
                }

                e.target.value = "";
            });

            $uploadArea.on("dragover.fileUpload dragenter.fileUpload", (e) => {
                e.preventDefault();
                e.stopPropagation();
                $uploadArea.addClass("drag-over border-primary");
            });

            $uploadArea.on("dragleave.fileUpload", (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (!$uploadArea[0].contains(e.relatedTarget)) {
                    $uploadArea.removeClass("drag-over border-primary");
                }
            });

            $uploadArea.on("drop.fileUpload", (e) => {
                e.preventDefault();
                e.stopPropagation();
                $uploadArea.removeClass("drag-over border-primary");

                const files = e.originalEvent.dataTransfer?.files;
                if (files && files.length > 0) {
                    this.handleFileSelection(
                        files,
                        $uploadArea,
                        $previewContainer,
                    );
                }
            });
        });

        $(".supporting-doc-title-input[data-additional-title='1']").on(
            "input.additionalTitle change.additionalTitle",
            (e) => {
                this.syncAdditionalDocumentFieldName($(e.currentTarget));
            },
        );
    }

    handleFileSelection(files, $uploadArea, $previewContainer) {
        if (!files || files.length === 0) {
            return;
        }

        const isAdditionalUpload =
            String($uploadArea.data("is-additional")) === "1";
        const fieldId = $uploadArea.data("field");
        const fieldName = this.resolveDocumentFieldName($uploadArea);
        const documentTypeId = this.resolveDocumentTypeId($uploadArea, fieldId);
        const documentTypeName = (
            $uploadArea.data("document-name") || fieldName
        )
            .toString()
            .trim();
        const maxSize =
            parseInt($uploadArea.find(".file-input").data("max-size")) ||
            DEFAULT_MAX_FILE_SIZE;

        if (!this.uploadedFiles[fieldId]) {
            this.uploadedFiles[fieldId] = [];
        }

        if (isAdditionalUpload) {
            this.uploadedFiles[fieldId] = [];
            $previewContainer.empty();
        }

        let validFiles = 0;
        let rejectedFiles = 0;
        const selectedFiles = isAdditionalUpload
            ? [files[0]].filter(Boolean)
            : Array.from(files);

        selectedFiles.forEach((file, index) => {
            if (file.size > maxSize) {
                this.showError(
                    `File "${
                        file.name
                    }" exceeds maximum size of ${this.formatFileSize(maxSize)}`,
                );
                rejectedFiles++;
                return;
            }

            const fileId = `file_${fieldId}_${Date.now()}_${index}`;
            const fileName = this.toPascalCase(fieldName);

            const fileWithId = Object.assign(file, {
                fileId,
                fileName,
                documentTypeId,
                documentTypeName,
            });

            this.uploadedFiles[fieldId].push(fileWithId);
            validFiles++;

            this.createFilePreview(fileWithId, $previewContainer, fieldId);
        });

        this.updateFileCountBadge($uploadArea, fieldId);
    }

    updateFileCountBadge($uploadArea, fieldId) {
        const $badge = $uploadArea.find(".file-count-badge");
        const $nameText = $uploadArea.find(".supporting-doc-file-name");
        const $viewTrigger = $uploadArea.find(".supporting-doc-view-trigger");
        const fileCount = this.uploadedFiles[fieldId]
            ? this.uploadedFiles[fieldId].length
            : 0;

        if ($badge.length) {
            $badge.text(fileCount);

            if (fileCount > 0) {
                $badge.removeClass("bg-secondary").addClass("bg-success");
            } else {
                $badge.removeClass("bg-success").addClass("bg-secondary");
            }
        }

        if ($nameText.length) {
            if (fileCount === 0) {
                $nameText.text("No file chosen");
            } else if (fileCount === 1) {
                $nameText.text(
                    this.uploadedFiles[fieldId][0]?.name || "1 file selected",
                );
            } else {
                $nameText.text(`${fileCount} files selected`);
            }
        }

        if ($viewTrigger.length) {
            $viewTrigger.prop("disabled", fileCount === 0);
        }
    }

    createFilePreview(file, $container, fieldId) {
        const fileId =
            file.fileId ||
            `file_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        const fileSize = this.formatFileSize(file.size);
        const fileName = file.name || "Unknown file";

        if ($container.find(`[data-file-id="${fileId}"]`).length > 0) {
            return;
        }

        if (!file.fileId) {
            file.fileId = fileId;
        }

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

        const $removeBtn = $container.find(
            `[data-file-id="${fileId}"] .file-remove-btn`,
        );
        $removeBtn.off("click.fileRemove").on("click.fileRemove", (e) => {
            e.stopPropagation();
            e.preventDefault();
            this.removeFile(fieldId, fileId);
        });

        const $viewBtn = $container.find(
            `[data-file-id="${fileId}"] .file-view-btn`,
        );
        $viewBtn.off("click.fileView").on("click.fileView", (e) => {
            e.stopPropagation();
            e.preventDefault();
            this.viewFile(fieldId, fileId);
        });
    }

    removeFile(fieldId, fileId) {
        try {
            const $previewItem = $(
                `.file-preview-item[data-file-id="${fileId}"]`,
            );
            if ($previewItem.length > 0) {
                $previewItem.remove();
            }

            if (
                this.uploadedFiles[fieldId] &&
                Array.isArray(this.uploadedFiles[fieldId])
            ) {
                const originalLength = this.uploadedFiles[fieldId].length;
                this.uploadedFiles[fieldId] = this.uploadedFiles[
                    fieldId
                ].filter((f) => f.fileId !== fileId);
                const newLength = this.uploadedFiles[fieldId].length;

                const $uploadArea = $(`[data-field="${fieldId}"]`);
                if ($uploadArea.length > 0) {
                    this.updateFileCountBadge($uploadArea, fieldId);
                }

                if (newLength === 0) {
                    const $fileInput = $uploadArea.find(".file-input");
                    if ($fileInput.length > 0) {
                        $fileInput.val("");
                    }
                }
            }
        } catch (error) {
            this.handleError("Error removing file", error);
        }
    }

    viewFile(fieldId, fileId) {
        try {
            const fileToView = this.uploadedFiles[fieldId]?.find(
                (f) => f.fileId === fileId,
            );

            if (!fileToView) {
                this.showToast("error", "File not found");
                return;
            }

            const fileUrl = URL.createObjectURL(fileToView);

            if (!this.activeFileUrls) {
                this.activeFileUrls = new Set();
            }

            this.activeFileUrls.add(fileUrl);

            const opened = window.open(
                fileUrl,
                "_blank",
                "noopener,noreferrer",
            );

            if (!opened) {
                this.downloadFile(fileToView.name, fileUrl);
            }

            const existingModalEl = document.getElementById("fileViewModal");
            if (existingModalEl) {
                const existingModal =
                    bootstrap.Modal.getInstance(existingModalEl);
                if (existingModal) {
                    existingModal.dispose();
                }
                existingModalEl.remove();
            }

            setTimeout(() => this.revokeFileUrl(fileUrl), 5000);
        } catch (error) {
            this.handleError("Error viewing file", error);
        }
    }

    showToast(type, message) {
        if (typeof toastr !== "undefined") {
            toastr[type](message);
        }
    }

    revokeFileUrl(url) {
        try {
            URL.revokeObjectURL(url);
            if (this.activeFileUrls) {
                this.activeFileUrls.delete(url);
            }
        } catch (error) {
            console.warn("Failed to revoke URL:", error);
        }
    }

    restoreParentModalLayout() {
        const hasOpenParentModal =
            $(".modal.show").not("#fileViewModal").length > 0;
        if (hasOpenParentModal) {
            $("body").addClass("modal-open");
        }
    }

    showImageModal(fileName, fileUrl) {
        const escapedFileName = this.escapeHtml(fileName);

        const modalHtml = `
            <div class="modal fade effect-scale md-wrapper" id="fileViewModal"
                tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="true">
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

        const existingModalEl = document.getElementById("fileViewModal");
        if (existingModalEl) {
            const existingModal = bootstrap.Modal.getInstance(existingModalEl);
            if (existingModal) {
                existingModal.dispose();
            }
            existingModalEl.remove();
        }
        $("body").append(modalHtml);

        const modal = new bootstrap.Modal(
            document.getElementById("fileViewModal"),
            {
                backdrop: false,
                keyboard: true,
                focus: true,
            },
        );

        $("#fileViewModal").on("hidden.bs.modal", () => {
            this.revokeFileUrl(fileUrl);
            $("#fileViewModal").remove();
            this.restoreParentModalLayout();
        });

        modal.show();
    }
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

            const existingModalEl = document.getElementById("fileViewModal");
            if (existingModalEl) {
                const existingModal =
                    bootstrap.Modal.getInstance(existingModalEl);
                if (existingModal) {
                    existingModal.dispose();
                }
                existingModalEl.remove();
            }
            $("body").append(modalHtml);
            const modal = new bootstrap.Modal(
                document.getElementById("fileViewModal"),
                {
                    backdrop: false,
                    keyboard: true,
                    focus: true,
                },
            );

            $("#fileViewModal").on("hidden.bs.modal", () => {
                this.revokeFileUrl(fileUrl);
                $("#fileViewModal").remove();
                this.restoreParentModalLayout();
            });

            modal.show();
        };
        reader.readAsText(file);
    }

    downloadFile(fileName, fileUrl) {
        const a = document.createElement("a");
        a.href = fileUrl;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        setTimeout(() => URL.revokeObjectURL(fileUrl), 100);
    }

    escapeHtml(text) {
        if (!text) return "";
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return "0 Bytes";
        const k = FILE_SIZE_BASE;
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return (
            parseFloat((bytes / Math.pow(k, i)).toFixed(2)) +
            " " +
            FILE_SIZE_UNITS[i]
        );
    }

    getTotalFileSize() {
        let totalSize = 0;
        Object.values(this.uploadedFiles).forEach((files) => {
            files.forEach((file) => {
                totalSize += file.size || 0;
            });
        });
        return this.formatFileSize(totalSize);
    }

    generateFieldInput(header, fieldId) {
        const baseInputClass = "form-control form-inputs";
        const required = header.amount_field === "Y" ? "required" : "";
        const placeholder = `Enter ${header.name?.toLowerCase() || "value"}`;
        const isCoverageDetails =
            header?.name?.toLowerCase().trim() === "coverage details";

        try {
            if (
                header.data_determinant === "Sum Insured" ||
                header.data_determinant === "Premium" ||
                header.name?.toLowerCase().includes("amount")
            ) {
                const currency = header.class_group === "FIRE" ? "KES" : "USD";
                return `
                    <div class="input-group">
                        <span class="input-group-text">${currency}</span>
                        <input type="number" class="${baseInputClass}" id="${fieldId}"
                            name="schedule_headers[${fieldId}]" data-sch_id="${header.id}" step="0.01" min="0"
                            placeholder="${placeholder}" ${required}>
                    </div>
                `;
            } else if (header.name?.toLowerCase().includes("date")) {
                return `<input type="date" class="${baseInputClass}" id="${fieldId}" data-sch_id="${header.id}" name="schedule_headers[${fieldId}]" ${required}>`;
            } else if (
                header.name?.toLowerCase().includes("percentage") ||
                header.name?.toLowerCase().includes("rate")
            ) {
                return `
                    <div class="input-group">
                        <input type="number" class="${baseInputClass}" id="${fieldId}" data-sch_id="${header.id}"
                            name="schedule_headers[${fieldId}]" step="0.01" min="0" max="100"
                            placeholder="${placeholder}" ${required}>
                        <span class="input-group-text">%</span>
                    </div>`;
            } else if (
                header.type_of_sum_insured &&
                header.type_of_sum_insured !== "N/A"
            ) {
                let options = `<option value="">Select ${header.name}</option>`;
                if (header.type_of_sum_insured === "TOTAL SUM INSURED") {
                    options += `
                    <option value="total_sum_insured">Total Sum Insured</option>
                    <option value="individual_sum_insured">Individual Sum Insured</option>`;
                }
                return `<select class="form-select ${baseInputClass.replace(
                    "form-control",
                    "",
                )}" id="${fieldId}" data-sch_id="${header.id}" name="schedule_headers[${fieldId}]" ${required}>${options}</select>`;
            } else {
                const isTextarea =
                    !header.input_type || header.input_type === "textarea";

                if (isTextarea) {
                    return `<textarea class="form-inputs breakdown-textarea" id="${fieldId}" data-sch_id="${header.id}" name="schedule_headers[${fieldId}]" rows="4" maxlength="5000" aria-label="${header.name}" placeholder="${placeholder}" ${required} ${
                        isCoverageDetails ? "" : "readonly"
                    }></textarea>`;
                } else {
                    return `<input type="text" class="${baseInputClass}" id="${fieldId}" name="schedule_headers[${fieldId}]" data-sch_id="${header.id}" placeholder="${placeholder}" ${required}>`;
                }
            }
        } catch (error) {
            const isTextarea =
                !header?.input_type || header?.input_type === "textarea";

            if (isTextarea) {
                return `<textarea class="form-inputs breakdown-textarea" id="${fieldId}" name="schedule_headers[${fieldId}]" rows="4" maxlength="5000" aria-label="${header.name}" data-sch_id="${header.id}" placeholder="${placeholder}" ${required}></textarea>`;
            } else {
                return `<input type="text" class="${baseInputClass}" id="${
                    fieldId || ""
                }" name="schedule_headers[${
                    fieldId || ""
                }]" placeholder="${placeholder}" ${required}>`;
            }
        }
    }

    setupFieldValidation($modal) {
        $modal
            .find("input[required], select[required]")
            .off("blur.validation change.validation")
            .on("blur.validation change.validation", function () {
                const $field = $(this);
                const value = $field.val();

                if (!value || value.toString().trim() === "") {
                    $field.addClass("is-invalid");
                    $field
                        .siblings(".invalid-feedback")
                        .text("This field is required.");
                } else {
                    $field.removeClass("is-invalid");
                    $field.siblings(".invalid-feedback").text("");
                }
            });
    }

    validateScheduleForm(modalId) {
        const $modal = $(`#${modalId}`);
        const requiredFields = $modal.find("input[required], select[required]");
        let isValid = true;

        requiredFields.each(function () {
            const $field = $(this);
            const value = $field.val();

            if (!value || value.toString().trim() === "") {
                $field.addClass("is-invalid");
                $field
                    .siblings(".invalid-feedback")
                    .text("This field is required.");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
                $field.siblings(".invalid-feedback").text("");
            }
        });

        return isValid;
    }

    addEscapeKeyListener() {
        if (this.escapeKeyHandler) return;

        this.escapeKeyHandler = (event) => {
            if (event.key === "Escape") {
                const openModal = document.querySelector(".modal.show");
                if (openModal) {
                    $(openModal).modal("hide");
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
        if (!this.dataTables || this.dataTables.size === 0) {
            return;
        }

        const setTableProcessing = (dataTable, isProcessing) => {
            if (dataTable && typeof dataTable.processing === "function") {
                dataTable.processing(isProcessing);
            }
        };

        const reloadToken = ++this.tableReloadToken;
        let pendingReloads = 0;

        this.showLoading();

        const finishReload = () => {
            pendingReloads = Math.max(0, pendingReloads - 1);
            if (pendingReloads === 0 && reloadToken === this.tableReloadToken) {
                this.hideLoading();
            }
        };

        this.dataTables.forEach((dataTable, tableId) => {
            if (!dataTable) {
                return;
            }

            try {
                pendingReloads++;
                setTableProcessing(dataTable, true);
                dataTable.ajax.reload(() => {
                    setTableProcessing(dataTable, false);
                    finishReload();
                }, false);
            } catch (error) {
                setTableProcessing(dataTable, false);
                console.error(`Error reloading table ${tableId}:`, error);
                finishReload();
            }
        });

        if (pendingReloads === 0 && reloadToken === this.tableReloadToken) {
            this.hideLoading();
            return;
        }

        setTimeout(() => {
            if (reloadToken !== this.tableReloadToken) {
                return;
            }

            this.dataTables.forEach((dataTable) => {
                try {
                    setTableProcessing(dataTable, false);
                } catch (error) {
                    console.error(
                        "Error stopping DataTable processing state",
                        error,
                    );
                }
            });

            this.hideLoading();
        }, AJAX_TIMEOUT + 2000);
    }

    getTableIdFromTab(tabId) {
        const mapping = {
            "#general_details": "all_opps",
            "#q1_details": "q1_opps",
            "#q2_details": "q2_opps",
            "#q3_details": "q3_opps",
            "#q4_details": "q4_opps",
        };
        return mapping[tabId] || null;
    }

    capitalize(str) {
        if (!str || typeof str !== "string") return "";
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
        this.$loadingOverlay?.removeClass("d-none");
    }

    hideLoading() {
        this.$loadingOverlay?.addClass("d-none");
    }

    showError(message) {
        this.$errorMessage?.text(message);
        this.$errorContainer?.removeClass("d-none");

        setTimeout(() => {
            this.$errorContainer?.addClass("d-none");
        }, ERROR_DISPLAY_DURATION);

        if (typeof toastr !== "undefined") {
            toastr.error(message);
        }
    }

    handleError(context, error) {
        let errorMessage = "An error occurred";

        if (typeof error === "string") {
            errorMessage = error;
        } else if (error?.message) {
            errorMessage = error.message;
        } else if (error?.xhr?.responseJSON?.message) {
            errorMessage = error.xhr.responseJSON.message;
        } else if (error?.statusText) {
            errorMessage = error.statusText;
        }

        const fullMessage = `${context}: ${errorMessage}`;

        console.error("Error:", {
            context,
            error,
            timestamp: new Date().toISOString(),
        });

        if (typeof toastr !== "undefined") {
            toastr.error(fullMessage);
        } else {
            this.showError(fullMessage);
        }

        if (error?.xhr) {
            console.error("XHR Details:", {
                status: error.xhr.status,
                statusText: error.xhr.statusText,
                responseText: error.xhr.responseText,
                url: error.xhr.responseURL,
            });
        }
    }

    destroy() {
        try {
            this.dataTables.forEach((dataTable, tableId) => {
                try {
                    if ($.fn.DataTable.isDataTable(`#${tableId}`)) {
                        dataTable.destroy(true);
                    }
                } catch (error) {
                    console.error(
                        `Error destroying DataTable ${tableId}:`,
                        error,
                    );
                }
            });

            this.dataTables.clear();

            this.removeEscapeKeyListener();
            $(".stage_btn_action").off(".pipeline");
            $(".update_category_action").off(".pipeline");
            $(".del_opp_sales").off(".pipeline");
            $(".mail-btn").off(".pipeline");
            $(".preview-pdf-btn").off(".pipeline");
            $(".revert-pipeline").off(".pipeline");
            $(".reset_proposal_to_lead_btn").off(".pipeline");
            this.$pipYearSelect?.off("change");
            $('a[data-bs-toggle="tab"]').off("shown.bs.tab");
            $(document).off("ajaxError");
            $(".supporting-doc-upload-line, .file-upload-area").off(
                ".fileUpload",
            );
            $(".file-input").off(".fileUpload");
            $(".file-remove-btn").off(".fileRemove");
            $(".file-view-btn").off(".fileView");

            if (
                this.chartInstance &&
                typeof this.chartInstance.detach === "function"
            ) {
                this.chartInstance.detach();
            }

            this.uploadedFiles = {};

            $(".file-preview-item").each(function () {
                const $img = $(this).find("img");
                if ($img.length && $img.attr("src")) {
                    URL.revokeObjectURL($img.attr("src"));
                }
            });

            if (this.activeFileUrls) {
                this.activeFileUrls.forEach((url) => {
                    try {
                        URL.revokeObjectURL(url);
                    } catch (e) {
                        console.warn("Error revoking URL:", e);
                    }
                });
                this.activeFileUrls.clear();
            }
        } catch (error) {
            console.error("Error during cleanup:", error);
        }
    }

    getAllUploadedFiles() {
        return this.uploadedFiles;
    }

    clearAllFiles() {
        if (this.activeFileUrls && this.activeFileUrls.size > 0) {
            this.activeFileUrls.forEach((url) => {
                try {
                    URL.revokeObjectURL(url);
                } catch (error) {
                    console.warn("Failed to revoke URL:", error);
                }
            });
            this.activeFileUrls.clear();
        }

        this.uploadedFiles = {};

        $(".file-preview-container").empty();
        $(".file-input").val("");
        $(".file-count-badge")
            .text("0")
            .removeClass("bg-success")
            .addClass("bg-secondary");
    }

    async handleSendBDNotification(button) {
        try {
            const buttonData = $(button).data();
            this.currentDealId = buttonData.opportunity_id;

            if (!this.currentDealId) {
                throw new Error("Deal ID not found in button data");
            }

            const opportunityId = this.currentDealId;
            const currentStage = this.normalizeStageKey(
                buttonData.current_stage,
            );
            const selectedStage =
                await this.promptEmailStageSelection(currentStage);

            if (!selectedStage) {
                return;
            }

            this.showLoading();
            this.checkEmailConnectionBeforeLoad(opportunityId, selectedStage);
        } catch (error) {
            this.handleError("Error handling BD notification", error);
            this.hideLoading();
        }
    }

    async promptEmailStageSelection(currentStage) {
        const normalizedCurrentStage =
            this.normalizeStageKey(currentStage) || STAGE_NAMES.LEAD;
        const stageOptions = this.getEmailStageOptions(normalizedCurrentStage);
        const optionEntries = Object.entries(stageOptions);
        const optionsHtml = optionEntries
            .map(([value, label]) => {
                const selected =
                    value === normalizedCurrentStage ? "selected" : "";
                return `<option value="${value}" ${selected}>${label}</option>`;
            })
            .join("");

        const result = await Swal.fire({
            title: "Select Stage",
            html: `
                <select id="swalStageSelect" class="form-inputs" style="width:100%;">
                    <option value="">Choose stage to send email</option>
                    ${optionsHtml}
                </select>
            `,
            showCancelButton: true,
            confirmButtonText: "Continue",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#3085d6",
            didOpen: () => {
                const $select = $("#swalStageSelect");
                if (typeof $select.select2 === "function") {
                    $select.select2({
                        width: "100%",
                        placeholder: "Choose stage to send email",
                        dropdownParent: $(Swal.getPopup()),
                    });
                }
            },
            preConfirm: () => {
                const value = $("#swalStageSelect").val();
                if (!value) {
                    Swal.showValidationMessage("Please select a stage.");
                    return false;
                }
                return value;
            },
            willClose: () => {
                const $select = $("#swalStageSelect");
                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.select2("destroy");
                }
            },
        });

        return result.isConfirmed ? result.value : null;
    }

    getEmailStageOptions(currentStage) {
        const stageOrder = [
            STAGE_NAMES.LEAD,
            STAGE_NAMES.PROPOSAL,
            STAGE_NAMES.NEGOTIATION,
            STAGE_NAMES.FINAL_STAGE,
        ];
        const stageLabels = {
            [STAGE_NAMES.LEAD]: "Lead",
            [STAGE_NAMES.PROPOSAL]: "Proposal",
            [STAGE_NAMES.NEGOTIATION]: "Negotiation",
            [STAGE_NAMES.FINAL_STAGE]: "Final Stage",
        };

        const normalizedCurrentStage =
            this.normalizeStageKey(currentStage) || STAGE_NAMES.LEAD;
        const currentIndex = stageOrder.indexOf(normalizedCurrentStage);
        const availableStages =
            currentIndex >= 0
                ? stageOrder.slice(0, currentIndex + 1)
                : [STAGE_NAMES.LEAD];

        return availableStages.reduce((acc, stageKey) => {
            acc[stageKey] = stageLabels[stageKey];
            return acc;
        }, {});
    }

    checkEmailConnectionBeforeLoad(opportunityId, currentStage) {
        if (
            typeof window.BDEmailModal !== "undefined" &&
            typeof window.BDEmailModal.checkEmailConnection === "function"
        ) {
            window.BDEmailModal.checkEmailConnection()
                .then((isConnected) => {
                    if (isConnected) {
                        this.loadBdEssentials(opportunityId, currentStage);
                    } else {
                        this.hideLoading();
                        this.promptMailRedirect();
                    }
                })
                .catch((error) => {
                    console.error("Connection check failed:", error);
                    this.hideLoading();
                    this.promptMailRedirect();
                });
        } else {
            this.hideLoading();
            this.promptMailRedirect();
        }
    }

    promptMailRedirect() {
        Swal.fire({
            icon: "warning",
            title: "Outlook Not Connected",
            html: `
                <p>Your Outlook account is not connected.</p>
                <p>Please connect from Mail before sending notifications.</p>
            `,
            confirmButtonText: "Go to Mail",
            showCancelButton: true,
            cancelButtonText: "Cancel",
            confirmButtonColor: "#3085d6",
        }).then((result) => {
            if (result.isConfirmed) {
                const returnTo = encodeURIComponent(window.location.href);
                window.location.href = `/mail?return_to=${returnTo}`;
            }
        });
    }

    handleEmailReconnect(opportunityId, currentStage) {
        this.showLoading();

        $.ajax({
            url: "/api/email/reconnect",
            method: "POST",
            timeout: 10000,
            success: (response) => {
                if (response.success) {
                    toastr.success("Email service reconnected successfully!");
                    // Now proceed with loading BD essentials
                    this.loadBdEssentials(opportunityId, currentStage);
                } else {
                    this.hideLoading();
                    toastr.error(response.message || "Failed to reconnect");
                }
            },
            error: (xhr) => {
                this.hideLoading();
                const errorMsg =
                    xhr.responseJSON?.message || "Reconnection failed";
                toastr.error(errorMsg);
            },
        });
    }

    handlePdfPreview(button) {
        try {
            this.showLoading();

            const buttonData = $(button).data();
            this.currentDealId = buttonData.opportunity_id;

            if (!this.currentDealId) {
                throw new Error("Deal ID not found in button data");
            }

            const opportunityId = this.currentDealId;
            const stage = this.normalizeStageKey(buttonData.current_stage);
            const printout_flag = 1;

            const $form = $("#previewPdfForm");
            const $s = stage || STAGE_NAMES.LEAD;
            const currentStage = this.config.stageFlow[$s];

            if (!currentStage) {
                throw new Error(`Invalid stage for PDF preview: ${$s}`);
            }

            $form.find("#pdf_opportunity_id").val(opportunityId);
            $form.find("#pdf_current_stage").val($s);
            $form.find("#pdf_previous_stage").val(currentStage.previous);
            $("#previewPdfModal").modal("show");

            this.hideLoading();
        } catch (error) {
            this.hideLoading();
            this.handleError("Error handling PDF preview", error);
        }
    }

    loadBdEssentials(opportunityId, currentStage) {
        const normalizedStage =
            this.normalizeStageKey(currentStage) || STAGE_NAMES.LEAD;

        $.ajax({
            url: this.config.routes.bdEmailData || "bd/bd_email_data",
            method: "POST",
            data: {
                opportunity_id: opportunityId,
                current_stage: normalizedStage,
            },
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            timeout: EXTENDED_AJAX_TIMEOUT,
            context: this,
            success: function (response) {
                if (response.success) {
                    const data = {
                        partners: response.data.partners,
                        contacts: response.data.contacts,
                        template: response.data.reinsurersTemplates,
                        attachedFiles: response.data.attachedFiles,
                        bdEmailTitle: normalizedStage,
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
            error: function (xhr, status, error) {
                this.hideLoading();

                if (
                    xhr.status === 503 ||
                    xhr.responseJSON?.error === "email_disconnected"
                ) {
                    Swal.fire({
                        icon: "warning",
                        title: "Email Service Disconnected",
                        html: `
                        <p>The email service is currently unavailable.</p>
                        <p>Please try reconnecting or contact support.</p>
                    `,
                        confirmButtonColor: "#ff9800",
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        html: `An error occurred: <b>${error}</b>`,
                        confirmButtonColor: "#dc3545",
                    });
                }
            },
        });
    }

    normalizeStageKey(stageValue) {
        return String(stageValue || "")
            .trim()
            .toLowerCase()
            .replace(/[\s-]+/g, "_");
    }

    normalizeTemplateMapKeys(templateMap) {
        const normalized = { ...(templateMap || {}) };

        if (normalized.final_stage && !normalized.final) {
            normalized.final = normalized.final_stage;
        }
        if (normalized.final && !normalized.final_stage) {
            normalized.final_stage = normalized.final;
        }

        return normalized;
    }

    mapStageToCategory(stageKey) {
        const normalized = this.normalizeStageKey(stageKey);
        if (normalized === STAGE_NAMES.FINAL_STAGE) {
            return "final";
        }
        return normalized || STAGE_NAMES.LEAD;
    }

    getStageDisplayName(stageKey) {
        const normalized = this.normalizeStageKey(stageKey);
        const labels = {
            [STAGE_NAMES.LEAD]: "Lead",
            [STAGE_NAMES.PROPOSAL]: "Proposal",
            [STAGE_NAMES.NEGOTIATION]: "Negotiation",
            [STAGE_NAMES.FINAL_STAGE]: "Final Stage",
            [STAGE_NAMES.WON]: "Won",
            [STAGE_NAMES.LOST]: "Lost",
        };

        return (
            labels[normalized] ||
            this.capitalize(normalized || STAGE_NAMES.LEAD)
        );
    }

    getAllowedCategoriesForStage(stageKey) {
        const normalized = this.normalizeStageKey(stageKey);
        const categoryFlow = {
            [STAGE_NAMES.LEAD]: ["lead"],
            [STAGE_NAMES.PROPOSAL]: ["lead", "proposal"],
            [STAGE_NAMES.NEGOTIATION]: ["lead", "proposal", "negotiation"],
            [STAGE_NAMES.FINAL_STAGE]: [
                "lead",
                "proposal",
                "negotiation",
                "final",
            ],
            [STAGE_NAMES.WON]: [
                "lead",
                "proposal",
                "negotiation",
                "final",
                "won",
            ],
            [STAGE_NAMES.LOST]: [
                "lead",
                "proposal",
                "negotiation",
                "final",
                "lost",
            ],
        };

        return categoryFlow[normalized] || categoryFlow[STAGE_NAMES.LEAD];
    }

    applyCategoryStageFilter($select, stageKey) {
        if (!$select || !$select.length) {
            return;
        }

        if (!$select.data("allCategoryOptions")) {
            const allOptions = [];
            $select.find("option").each((_, option) => {
                allOptions.push({
                    value: option.value,
                    text: $(option).text(),
                });
            });
            $select.data("allCategoryOptions", allOptions);
        }

        const allOptions = $select.data("allCategoryOptions") || [];
        const allowedCategories = new Set(
            this.getAllowedCategoriesForStage(stageKey),
        );

        $select.empty();
        allOptions.forEach((option) => {
            if (allowedCategories.has(option.value)) {
                $select.append(
                    $("<option></option>")
                        .attr("value", option.value)
                        .text(option.text),
                );
            }
        });
    }

    prepareBDEmailModal(opportunityId, data) {
        try {
            const $bdMailModal = $("#sendBDEmailModal");
            const $bdNotificationForm = $("#bdNotificationForm");

            if (!$bdMailModal.length || !$bdNotificationForm.length) {
                throw new Error(
                    "BD email modal/form is not available in the DOM",
                );
            }

            if (!data?.bdEmailTitle) {
                return;
            }

            const stageTitle = this.normalizeStageKey(data.bdEmailTitle);
            const stage = this.config.stageFlow[stageTitle] || {};
            const templateMap = this.normalizeTemplateMapKeys(
                data.template || {},
            );
            const selectedCategory = this.mapStageToCategory(stageTitle);
            const template = templateMap[selectedCategory] ||
                templateMap[stageTitle] ||
                templateMap[stage?.previous] ||
                templateMap[this.mapStageToCategory(stage?.previous)] ||
                templateMap[STAGE_NAMES.LEAD] || {
                    subject: "",
                    message: "",
                };

            $bdMailModal
                .find(".modal-bd-title")
                .text(`- ${this.getStageDisplayName(stageTitle)}`);

            const $categorySelect = $bdMailModal.find("#category");
            this.applyCategoryStageFilter($categorySelect, stageTitle);
            $categorySelect.val(selectedCategory).trigger("change");

            $bdNotificationForm.find(".subject").val(template.subject);
            $bdNotificationForm.find(".message").val(template.message);
            $bdNotificationForm
                .find(".category_templates")
                .val(JSON.stringify(templateMap));

            $bdNotificationForm.find(".opportunity_id").val(data.opportunityId);
            $bdNotificationForm.find(".customer_id").val(data.customerId);

            this.populateAttachedFiles(
                data.attachedFiles,
                "attachedFilesList",
                stageTitle,
            );

            const $contactsSelect = $bdNotificationForm.find("#toContacts");
            const $bccEmailSelect = $bdNotificationForm.find("#bccEmail");
            const $ccEmailSelect = $bdNotificationForm.find("#ccEmail");

            const resetSelect = ($select, placeholder) => {
                $select
                    .empty()
                    .append(
                        `<option value="" disabled>${placeholder}</option>`,
                    );
            };

            resetSelect($contactsSelect, "--Select contacts--");
            resetSelect($ccEmailSelect, "--Select CC emails--");
            resetSelect($bccEmailSelect, "--Select BCC emails--");

            const partnerEmails = [];
            if (Array.isArray(data.partners) && data.partners.length > 0) {
                data.partners.forEach((partner) => {
                    if (partner.email) {
                        partnerEmails.push(partner.email);
                    }
                });
            }

            $bdNotificationForm.find("#toEmail").val(partnerEmails);
            $bdNotificationForm
                .find("#partnerToEmail")
                .val(data.partners || []);

            const primaryContacts = [];
            const regularContacts = [];

            if (Array.isArray(data.contacts) && data.contacts.length > 0) {
                data.contacts.forEach((contact) => {
                    const email = contact.email;
                    if (!email) return;

                    let optionText = contact.name
                        ? `${this.escapeHtml(contact.name)} (${this.escapeHtml(
                              email,
                          )})`
                        : this.escapeHtml(email);
                    if (contact.phone)
                        optionText += ` - ${this.escapeHtml(contact.phone)}`;
                    if (contact.isPrimary) optionText += " [Primary]";

                    const createOption = () =>
                        $("<option></option>")
                            .attr("value", email)
                            .text(optionText)
                            .data("contact-data", contact)
                            .data("is-primary", !!contact.isPrimary);

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
                        $contactsSelect.val(primaryContacts).trigger("change");
                    } else if (regularContacts.length === 1) {
                        $contactsSelect
                            .val(regularContacts[0])
                            .trigger("change");
                    }

                    [$contactsSelect, $ccEmailSelect, $bccEmailSelect].forEach(
                        ($select) => {
                            if ($select.hasClass("select2-hidden-accessible")) {
                                $select.trigger("change.select2");
                            }
                        },
                    );
                }, 100);
            }

            $bdMailModal.modal("show");

            $bdMailModal.one("shown.bs.modal", () => {
                if (
                    typeof window.BDEmailModal !== "undefined" &&
                    typeof window.BDEmailModal.refreshConnectionStatus ===
                        "function"
                ) {
                    window.BDEmailModal.refreshConnectionStatus();
                }

                if (
                    typeof window.BDEmailModal !== "undefined" &&
                    typeof window.BDEmailModal.captureInitialState ===
                        "function"
                ) {
                    setTimeout(() => {
                        window.BDEmailModal.captureInitialState();
                    }, 180);
                }
            });
        } catch (error) {
            console.error("Error in prepareBDEmailModal:", error);
            this.handleError("Error preparing BD email modal", error);
        }
    }

    populateAttachedFiles(
        filesArray,
        containerId = "attachedFilesList",
        stageKey = null,
    ) {
        const $container = $(`#${containerId}`);

        if ($container.length === 0) {
            return;
        }

        const $rowContainer = $container.find(".row").first();
        if ($rowContainer.length === 0) {
            return;
        }

        $rowContainer.empty();

        if (!filesArray || filesArray.length === 0) {
            this.addNoFilesMessage($rowContainer);
            this.updateFileCount(0);
            return;
        }

        const normalizedStage = this.normalizeStageKey(stageKey);
        const filteredFiles = (filesArray || []).filter((file) => {
            if (normalizedStage === STAGE_NAMES.LEAD) {
                return !this.isProposalCoverSlipAttachment(file);
            }

            if (normalizedStage === STAGE_NAMES.PROPOSAL) {
                return !this.isLeadCoverSlipAttachment(file);
            }

            return true;
        });

        $("#additionalFilesMessage").remove();

        $.each(filteredFiles, (index, file) => {
            const $fileElement = this.createFileElement(file, stageKey);
            $rowContainer.append($fileElement);
        });

        this.updateFileCount(filteredFiles.length);
    }

    createFileElement(file, stageKey = null) {
        const fileUrl = file.s3_url;
        const fileName = file.original_name;
        const fileDescription = this.resolveAttachmentDisplayName(
            file,
            stageKey,
        );
        const mimeType = file.mimetype;
        const fileSize = file.file_size;

        const fileInfo = this.getFileIconAndType(mimeType, fileName);

        const $col = $("<div>", { class: "col-md-4" });
        const $link = $("<a>", {
            href: fileUrl,
            target: "_blank",
            rel: "noopener noreferrer",
        });

        const $fileItem = $("<div>", {
            class: "file-item d-flex align-items-center mb-2",
        });

        const $fileIcon = $("<div>", {
            class: "file-icon me-3",
        }).html(`<i class="bx ${fileInfo.icon}"></i>`);

        const $fileInfoDiv = $("<div>", { class: "file-info flex-grow-1" });

        const $fileName = $("<h6>", {
            class: "mb-1",
            text: fileDescription,
        });

        const fileSizeText = fileSize
            ? "• " + this.formatFileSize(fileSize)
            : "";
        const $fileMeta = $("<div>", {
            class: "file-meta",
            html: fileInfo.displayType + " " + fileSizeText,
        });

        $fileInfoDiv.append($fileName).append($fileMeta);
        $fileItem.append($fileIcon).append($fileInfoDiv);
        $link.append($fileItem);
        $col.append($link);

        return $col;
    }

    resolveAttachmentDisplayName(file, stageKey = null) {
        const fallback =
            file?.description || file?.original_name || "Unknown file";
        const normalizedStage = this.normalizeStageKey(stageKey);

        const haystack = [
            file?.description || "",
            file?.original_name || "",
            file?.file || "",
        ]
            .join(" ")
            .toLowerCase();

        if (normalizedStage === STAGE_NAMES.PROPOSAL) {
            if (haystack.includes("proposal cover slip")) {
                return "Cover Slip";
            }
            return fallback;
        }

        if (normalizedStage !== STAGE_NAMES.LEAD) {
            return fallback;
        }

        if (haystack.includes("lead cover slip")) {
            return "Cover Slip";
        }

        if (
            haystack.includes("lead cover") ||
            haystack.includes("cover email") ||
            haystack.includes("cover emails")
        ) {
            return "Lead Cover";
        }

        return fallback;
    }

    isProposalCoverSlipAttachment(file) {
        const haystack = [
            file?.description || "",
            file?.original_name || "",
            file?.file || "",
        ]
            .join(" ")
            .toLowerCase();

        return haystack.includes("proposal cover slip");
    }

    isLeadCoverSlipAttachment(file) {
        const haystack = [
            file?.description || "",
            file?.original_name || "",
            file?.file || "",
        ]
            .join(" ")
            .toLowerCase();

        return haystack.includes("lead cover slip");
    }

    getFileIconAndType(mimeType, fileName) {
        const fileExtension = fileName.split(".").pop().toLowerCase();

        if (mimeType.includes("pdf") || fileExtension === "pdf") {
            return {
                icon: "bx-file text-danger",
                displayType: "PDF Document",
            };
        }

        if (
            mimeType.includes("word") ||
            mimeType.includes("document") ||
            ["doc", "docx"].includes(fileExtension)
        ) {
            return {
                icon: "bx-file text-primary",
                displayType: "Word Document",
            };
        }

        if (
            mimeType.includes("image") ||
            ["jpg", "jpeg", "png", "gif", "bmp", "webp"].includes(fileExtension)
        ) {
            return {
                icon: "bx-image text-success",
                displayType: "Image File",
            };
        }

        if (
            mimeType.includes("sheet") ||
            mimeType.includes("excel") ||
            ["xls", "xlsx"].includes(fileExtension)
        ) {
            return {
                icon: "bx-file text-success",
                displayType: "Excel Document",
            };
        }

        if (mimeType.includes("text") || fileExtension === "txt") {
            return {
                icon: "bx-file text-info",
                displayType: "Text Document",
            };
        }

        return {
            icon: "bx-file",
            displayType: fileExtension
                ? `${fileExtension.toUpperCase()} Document`
                : "Document",
        };
    }

    addNoFilesMessage($container) {
        if ($("#additionalFilesMessage").length > 0) return;

        const $col = $("<div>", { class: "col-md-12" });
        const $message = $("<div>", {
            id: "additionalFilesMessage",
            class: "text-center py-2",
        }).html(`
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                No additional claim documents attached.
            </small>
        `);

        $col.append($message);
        $container.append($col);
    }

    updateFileCount(dynamicCount, staticCount = 2) {
        const totalCount = staticCount + dynamicCount;
        $("#fileCount").text(`${totalCount} files attached`);
    }

    async checkEmailServiceAvailability() {
        try {
            // const response = await $.ajax({
            //     url: "/api/email/check-connection",
            //     method: "GET",
            //     timeout: 5000,
            // });
            return false;
        } catch (error) {
            console.error("Email service check failed:", error);
            return false;
        }
    }
    showEmailConnectionWarning(retryCallback) {
        Swal.fire({
            icon: "warning",
            title: "Email Service Disconnected",
            html: `
            <div class="text-start">
                <p class="mb-2">Your email service is currently not connected.</p>
                <p class="mb-2">You won't be able to send notifications until the connection is restored.</p>
                <p class="text-muted small mb-0">
                    <i class="bx bx-info-circle me-1"></i>
                    This might be due to:
                </p>
                <ul class="text-muted small mt-2">
                    <li>Network connectivity issues</li>
                    <li>Email server maintenance</li>
                    <li>Authentication timeout</li>
                </ul>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: '<i class="bx bx-refresh me-1"></i> Reconnect',
            cancelButtonText: "Cancel",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#6c757d",
            reverseButtons: true,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-light",
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed && typeof retryCallback === "function") {
                retryCallback();
            }
        });
    }

    updateMailButtonStates(isConnected) {
        const $mailButtons = $(".mail-btn");

        if (isConnected) {
            $mailButtons
                .prop("disabled", false)
                .removeClass("btn-secondary")
                .addClass("btn-primary")
                .attr("title", "Send BD Notification");
        } else {
            $mailButtons
                .prop("disabled", false)
                .removeClass("btn-primary")
                .addClass("btn-secondary")
                .attr(
                    "title",
                    "Email service disconnected. Click to reconnect and continue",
                );
        }
    }
}

$(document).ready(function () {
    try {
        window.pipelineManager = new PipelineManager();
    } catch (error) {
        if (typeof toastr !== "undefined") {
            toastr.error(
                "Failed to initialize the application. Please refresh the page.",
            );
        } else {
            alert(
                "Failed to initialize the application. Please refresh the page.",
            );
        }
    }
});

$(window).on("beforeunload", function () {
    if (
        window.pipelineManager &&
        typeof window.pipelineManager.destroy === "function"
    ) {
        window.pipelineManager.destroy();
    }
});

window.addEventListener("unhandledrejection", function (event) {
    if (
        window.pipelineManager &&
        typeof window.pipelineManager.handleError === "function"
    ) {
        window.pipelineManager.handleError(
            "Unhandled Promise Rejection",
            event.reason,
        );
    }
});

if (typeof module !== "undefined" && module.exports) {
    module.exports = PipelineManager;
}
