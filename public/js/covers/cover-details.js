// resources/js/covers/cover-details.js

import { CoverManager } from "./managers/CoverManager";
import { ReinsurerManager } from "./managers/ReinsurerManager";
import { ScheduleManager } from "./managers/ScheduleManager";
import { DocumentManager } from "./managers/DocumentManager";
import { VisualizationManager } from "./managers/VisualizationManager";

class CoverDetailsApp {
    constructor() {
        this.appElement = document.getElementById("coverDetailsApp");

        if (!this.appElement) {
            console.warn("CoverDetailsApp element not found");
            return;
        }

        this.coverData = window.coverData || {};
        this.managers = {};

        this.init();
    }

    init() {
        console.log("Initializing Cover Details App...", this.coverData);

        // Initialize managers
        this.initializeManagers();

        // Setup global event delegation
        this.setupEventDelegation();

        // Initialize DataTables with lazy loading
        this.initializeDataTables();

        // Setup form validations
        this.setupFormValidations();

        // Initialize visualizations
        this.initializeVisualizations();

        // Setup auto-save functionality
        this.setupAutoSave();

        console.log("Cover Details App initialized successfully");
    }

    initializeManagers() {
        this.managers.cover = new CoverManager(this.coverData);
        this.managers.reinsurer = new ReinsurerManager(this.coverData);
        this.managers.schedule = new ScheduleManager(this.coverData);
        this.managers.document = new DocumentManager(this.coverData);
        this.managers.visualization = new VisualizationManager(this.coverData);
    }

    setupEventDelegation() {
        // Use event delegation for better performance
        document.addEventListener("click", (e) => {
            const target = e.target.closest("[data-action]");
            if (!target) return;

            const action = target.dataset.action;
            const handler = this.actionHandlers[action];

            if (handler) {
                e.preventDefault();
                handler.call(this, target);
            }
        });

        // Listen for custom events
        document.addEventListener("participations-updated", () => {
            this.refreshParticipationData();
        });

        document.addEventListener("cover-updated", () => {
            this.refreshCoverData();
        });
    }

    actionHandlers = {
        "edit-cover": (target) => this.managers.cover.edit(),
        "add-reinsurer": (target) => this.managers.reinsurer.showAddModal(),
        "edit-reinsurer": (target) =>
            this.managers.reinsurer.edit(target.dataset.id),
        "remove-reinsurer": (target) =>
            this.managers.reinsurer.remove(target.dataset.id),
        "add-schedule": (target) => this.managers.schedule.showAddModal(),
        "edit-schedule": (target) =>
            this.managers.schedule.edit(target.dataset.id),
        "remove-schedule": (target) =>
            this.managers.schedule.remove(target.dataset.id),
        "add-attachment": (target) => this.managers.document.showAddModal(),
        "view-attachment": (target) =>
            this.managers.document.view(target.dataset.id),
        "remove-attachment": (target) =>
            this.managers.document.remove(target.dataset.id),
        "generate-slip": (target) => this.managers.cover.generateSlip(),
        "verify-details": (target) =>
            this.managers.cover.showVerificationModal(),
        "generate-debit": (target) => this.managers.cover.generateDebit(),
        "send-email": (target) =>
            this.managers.cover.sendEmail(target.dataset.type),
        "import-from-treaty": (target) =>
            this.managers.reinsurer.importFromTreaty(),
    };

    initializeDataTables() {
        // Lazy load DataTables only when tabs are activated
        const tabTriggerList = document.querySelectorAll(
            '[data-bs-toggle="tab"]'
        );

        tabTriggerList.forEach((tabTrigger) => {
            tabTrigger.addEventListener("shown.bs.tab", (e) => {
                const targetId = e.target.getAttribute("data-bs-target");
                const tableId = `${targetId.substring(1)}-table`;
                const table = document.getElementById(tableId);

                if (table && !$.fn.DataTable.isDataTable(`#${tableId}`)) {
                    this.initializeTable(tableId);
                }
            });
        });

        // Initialize first visible table
        const firstTable = document.querySelector(
            '.tab-pane.active table[id$="-table"]'
        );
        if (firstTable) {
            this.initializeTable(firstTable.id);
        }
    }

    initializeTable(tableId) {
        const config = this.getTableConfig(tableId);
        if (!config) return;

        try {
            const table = $(`#${tableId}`).DataTable(config);

            // Store table instance for later use
            if (!this.tables) this.tables = {};
            this.tables[tableId] = table;

            console.log(`Table ${tableId} initialized successfully`);
        } catch (error) {
            console.error(`Failed to initialize table ${tableId}:`, error);
        }
    }

    getTableConfig(tableId) {
        const baseConfig = {
            processing: true,
            serverSide: true,
            lengthChange: false,
            pageLength: 10,
            responsive: true,
            language: {
                processing:
                    '<div class="spinner-border text-primary" role="status"></div>',
                emptyTable: "No data available",
                zeroRecords: "No matching records found",
            },
            drawCallback: function (settings) {
                // Initialize tooltips after table draw
                const tooltips = document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]'
                );
                tooltips.forEach((el) => new bootstrap.Tooltip(el));
            },
        };

        const configs = {
            "reinsurers-table": {
                ...baseConfig,
                ajax: {
                    url: "/api/covers/reinsurers",
                    data: (d) => ({
                        endorsement_no: this.coverData.endorsement_no,
                    }),
                    error: (xhr, error, code) => {
                        console.error("DataTables Ajax error:", error);
                        toastr.error("Failed to load reinsurers data");
                    },
                },
                columns: this.getReinsurerColumns(),
                order: [[0, "asc"]],
                footerCallback: (tfoot, data, start, end, display) => {
                    this.updateTableFooter("reinsurers-table", tfoot, data);
                },
            },
            "schedules-table": {
                ...baseConfig,
                ajax: {
                    url: "/api/covers/schedules",
                    data: (d) => ({
                        endorsement_no: this.coverData.endorsement_no,
                    }),
                },
                columns: this.getScheduleColumns(),
                order: [[3, "asc"]],
            },
            "attachments-table": {
                ...baseConfig,
                ajax: {
                    url: "/api/covers/attachments",
                    data: (d) => ({
                        endorsement_no: this.coverData.endorsement_no,
                    }),
                },
                columns: [
                    { data: "id" },
                    { data: "title" },
                    { data: "action", orderable: false, searchable: false },
                ],
                order: [[0, "desc"]],
            },
            "clauses-table": {
                ...baseConfig,
                ajax: {
                    url: "/api/covers/clauses",
                    data: (d) => ({
                        endorsement_no: this.coverData.endorsement_no,
                    }),
                },
                columns: [
                    {
                        data: "clause_id",
                        render: (data, type, row, meta) => meta.row + 1,
                    },
                    { data: "clause_title" },
                    { data: "clause_wording", className: "clamp-text" },
                    { data: "action", orderable: false, searchable: false },
                ],
                order: [[0, "asc"]],
            },
            "approvals-table": {
                ...baseConfig,
                ajax: {
                    url: "/api/covers/approvals",
                    data: (d) => ({
                        endorsement_no: this.coverData.endorsement_no,
                    }),
                },
                columns: [
                    { data: "id" },
                    { data: "approver" },
                    { data: "comment" },
                    { data: "approver_comment" },
                    { data: "status" },
                    { data: "action", orderable: false, searchable: false },
                ],
                order: [[0, "desc"]],
            },
            "debits-table": {
                ...baseConfig,
                ajax: {
                    url: "/api/covers/debits",
                    data: (d) => ({
                        endorsement_no: this.coverData.endorsement_no,
                    }),
                },
                columns: [
                    {
                        data: "id",
                        render: (data, type, row, meta) => meta.row + 1,
                    },
                    { data: "cedant" },
                    { data: "dr_no" },
                    { data: "installment" },
                    {
                        data: "share",
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "sum_insured",
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "premium",
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "gross",
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "net_amt",
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    { data: "action", orderable: false, searchable: false },
                ],
                order: [[0, "desc"]],
                footerCallback: (tfoot, data, start, end, display) => {
                    this.updateTableFooter("debits-table", tfoot, data);
                },
            },
        };

        return configs[tableId];
    }

    getReinsurerColumns() {
        const typeOfBus = this.coverData.type_of_bus;

        const baseColumns = [
            {
                data: "tran_no",
                render: (data, type, row, meta) => meta.row + 1,
                className: "text-center",
            },
            {
                data: "partner_name",
                render: (data, type, row) => {
                    return `
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm">
                                <div class="avatar-title bg-primary-subtle text-primary rounded">
                                    ${data.substring(0, 2).toUpperCase()}
                                </div>
                            </div>
                            <div>
                                <div class="fw-semibold">${data}</div>
                                <small class="text-muted">${
                                    row.country || ""
                                }</small>
                            </div>
                        </div>
                    `;
                },
            },
            {
                data: "share",
                render: $.fn.dataTable.render.number(",", ".", 4, ""),
                className: "text-end",
            },
        ];

        if (["FPR", "FNP"].includes(typeOfBus)) {
            return [
                ...baseColumns,
                {
                    data: "sum_insured",
                    render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    className: "text-end",
                },
                {
                    data: "premium",
                    render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    className: "text-end",
                },
                {
                    data: "comm_rate",
                    render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    className: "text-end",
                },
                {
                    data: "commission",
                    render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    className: "text-end",
                },
                {
                    data: "brokerage_comm_amt",
                    render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    className: "text-end",
                },
                {
                    data: "wht_amt",
                    render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    className: "text-end",
                },
                {
                    data: "fronting_amt",
                    render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    className: "text-end",
                },
                {
                    data: "action",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                },
            ];
        }

        return [
            ...baseColumns,
            {
                data: "action",
                orderable: false,
                searchable: false,
                className: "text-center",
            },
        ];
    }

    getScheduleColumns() {
        return [
            {
                data: "id",
                render: (data, type, row, meta) => meta.row + 1,
                className: "text-center",
            },
            { data: "title" },
            {
                data: "details",
                className: "clamp-text",
                render: (data) => {
                    const stripped = data.replace(/<[^>]*>/g, "");
                    return stripped.length > 100
                        ? stripped.substring(0, 100) + "..."
                        : stripped;
                },
            },
            { data: "schedule_position", className: "text-center" },
            {
                data: "action",
                orderable: false,
                searchable: false,
                className: "text-center",
            },
        ];
    }

    updateTableFooter(tableId, tfoot, data) {
        const api = this.tables[tableId];
        if (!api) return;

        let columnsToSum = [];

        if (
            tableId === "reinsurers-table" &&
            ["FPR", "FNP"].includes(this.coverData.type_of_bus)
        ) {
            columnsToSum = [2, 3, 4, 6, 7, 8, 9]; // Share, Sum Insured, Premium, Commission, Brokerage, WHT, Fronting
        } else if (tableId === "debits-table") {
            columnsToSum = [4, 5, 6, 7, 8]; // Share, Sum Insured, Premium, Gross, Net
        }

        if (columnsToSum.length === 0) return;

        let footerHtml = "<tr>";
        footerHtml += '<td colspan="2" class="text-end fw-bold">Totals:</td>';

        const totalColumns = api.columns().count();

        for (let i = 2; i < totalColumns - 1; i++) {
            if (columnsToSum.includes(i)) {
                const sum = api
                    .column(i, { search: "applied" })
                    .data()
                    .reduce((a, b) => {
                        const aFloat =
                            parseFloat(String(a).replace(/,/g, "")) || 0;
                        const bFloat =
                            parseFloat(String(b).replace(/,/g, "")) || 0;
                        return aFloat + bFloat;
                    }, 0);

                const formatted = new Intl.NumberFormat("en-US", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }).format(sum);

                footerHtml += `<td class="text-end fw-bold">${formatted}</td>`;
            } else {
                footerHtml += "<td></td>";
            }
        }

        footerHtml += "<td></td></tr>";

        if (!$(tfoot).length) {
            $(`#${tableId}`).append("<tfoot></tfoot>");
        }

        $(`#${tableId} tfoot`).html(footerHtml);
    }

    setupFormValidations() {
        // Setup global form validation defaults
        if ($.validator) {
            $.validator.setDefaults({
                errorClass: "is-invalid",
                validClass: "is-valid",
                errorElement: "div",
                errorPlacement: (error, element) => {
                    error.addClass("invalid-feedback");
                    const container = element.closest(
                        ".form-group, .mb-3, .col-md-6, .col-md-4, .col-md-3"
                    );
                    if (container.length) {
                        container.append(error);
                    } else {
                        element.after(error);
                    }
                },
                highlight: (element) => {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: (element) => {
                    $(element).removeClass("is-invalid").addClass("is-valid");
                },
            });
        }
    }

    initializeVisualizations() {
        if (this.managers.visualization) {
            this.managers.visualization.renderShareDistribution();
        }
    }

    setupAutoSave() {
        // Auto-save draft changes every 30 seconds
        let autoSaveTimer;

        $(document).on("input", "form[data-autosave]", () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                this.autoSaveDraft();
            }, 30000);
        });
    }

    autoSaveDraft() {
        // Implementation for auto-saving drafts
        console.log("Auto-saving draft...");
    }

    refreshParticipationData() {
        // Refresh reinsurers table
        if (this.tables["reinsurers-table"]) {
            this.tables["reinsurers-table"].ajax.reload(null, false);
        }

        // Refresh summary stats
        this.refreshSummaryStats();

        // Refresh visualizations
        if (this.managers.visualization) {
            this.managers.visualization.renderShareDistribution();
        }
    }

    async refreshSummaryStats() {
        try {
            const response = await fetch(
                `/api/covers/${this.coverData.id}/summary`
            );
            const data = await response.json();

            if (data.success) {
                this.updateSummaryUI(data.data);
            }
        } catch (error) {
            console.error("Failed to refresh summary:", error);
        }
    }

    updateSummaryUI(summaryData) {
        $("#totalReinsurers").text(summaryData.total_reinsurers || 0);
        $("#totalPlaced").text(
            this.formatNumber(summaryData.total_placed || 0, 2) + "%"
        );
        $("#remainingCapacity").text(
            this.formatNumber(summaryData.remaining_capacity || 0, 2) + "%"
        );
        $("#totalPremium").text(
            this.formatNumber(summaryData.total_premium || 0, 2)
        );

        $("#placedPercent").text(
            this.formatNumber(summaryData.total_placed || 0, 2) + "%"
        );
        $("#remainingPercent").text(
            this.formatNumber(summaryData.remaining_capacity || 0, 2) + "%"
        );
    }

    async refreshCoverData() {
        try {
            const response = await fetch(`/api/covers/${this.coverData.id}`);
            const data = await response.json();

            if (data.success) {
                this.coverData = { ...this.coverData, ...data.data };
            }
        } catch (error) {
            console.error("Failed to refresh cover data:", error);
        }
    }

    formatNumber(number, decimals = 2) {
        return new Intl.NumberFormat("en-US", {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        }).format(number);
    }

    destroy() {
        // Cleanup method
        if (this.tables) {
            Object.values(this.tables).forEach((table) => {
                if (table && typeof table.destroy === "function") {
                    table.destroy();
                }
            });
        }

        if (this.managers) {
            Object.values(this.managers).forEach((manager) => {
                if (manager && typeof manager.destroy === "function") {
                    manager.destroy();
                }
            });
        }

        console.log("Cover Details App destroyed");
    }
}

// Initialize app when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    window.coverDetailsApp = new CoverDetailsApp();
});

// Cleanup on page unload
window.addEventListener("beforeunload", () => {
    if (window.coverDetailsApp) {
        window.coverDetailsApp.destroy();
    }
});

export default CoverDetailsApp;
