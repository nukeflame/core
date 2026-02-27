// @pk305
(function ($, window) {
    "use strict";

    const ClaimsStatements = {
        tables: {},
        initialized: false,
        config: {},

        init: function (config = {}) {
            if (this.initialized) {
                console.warn("ClaimsStatements already initialized");
                return;
            }

            this.config = config;
            this.bindTabEvents();
            this.initialized = true;
        },

        bindTabEvents: function () {
            const self = this;
            $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
                const target = $(e.target).data("bs-target");

                if (target === "#claimlist-tab" && !self.tables.claims) {
                    self.initClaimsTable();
                }

                if (target === "#statement-tab" && !self.tables.statements) {
                    self.initStatementsTable();
                }
            });
        },
        initClaimsTable: function () {
            const $table = $("#claimsTable");
            const self = this;

            if ($table.length === 0) {
                console.warn("Claims table element not found");
                return;
            }

            if (this.tables.claims) {
                console.warn("Claims table already initialized");
                return;
            }

            try {
                this.tables.claims = $table.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: window.CoverEndorsement?.config?.routes
                            ?.claimsDatatable,
                        data: function (d) {
                            d.cover_no =
                                window.CoverEndorsement?.config?.coverNo;
                            d.endorsement_no =
                                window.CoverEndorsement?.config?.latestEndorsement?.endorsement_no;
                        },
                        error: function (xhr, error, code) {
                            console.error("Claims table AJAX error:", error);
                            self._showTableError(
                                $table,
                                "Failed to load claims data",
                            );
                        },
                    },
                    columns: [
                        {
                            data: "claim_serial_no",
                            name: "claim_serial_no",
                            render: (data, type, row, meta) =>
                                meta.row + meta.settings._iDisplayStart + 1,
                        },
                        { data: "claim_no", name: "claim_no" },
                        {
                            data: "claim_date",
                            name: "claim_date",
                            render: function (data) {
                                return data || "-";
                            },
                        },
                        {
                            data: "date_of_loss",
                            name: "date_of_loss",
                            render: function (data) {
                                return data || "-";
                            },
                        },
                        {
                            data: "claim_amount",
                            name: "claim_amount",
                            className: "text-end",
                            render: function (data) {
                                return self._formatCurrency(data);
                            },
                        },
                        {
                            data: "claimed_amount",
                            name: "claimed_amount",
                            className: "text-end",
                            render: function (data) {
                                return self._formatCurrency(data);
                            },
                        },
                        {
                            data: "status",
                            name: "status",
                            render: function (data) {
                                return self._formatStatus(data);
                            },
                        },
                        {
                            data: "actions",
                            name: "actions",
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"],
                    ],
                    order: [[1, "desc"]],
                    language: {
                        emptyTable: "No claims found",
                        zeroRecords: "No matching claims found",
                        search: "Filter claims:",
                        processing:
                            '<i class="fa fa-spinner fa-spin fa-2x"></i> Loading claims...',
                    },
                    dom: '<"d-flex justify-content-between align-items-center mb-3"lf>t<"d-flex justify-content-between align-items-center mt-3"ip>',

                    footerCallback: function (row, data, start, end, display) {
                        const api = this.api();

                        const json = api.ajax.json();
                        if (json && json.totals) {
                            self._updateClaimsFooter(row, json.totals);
                        }
                    },

                    drawCallback: function (settings) {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    },
                });

                this.bindClaimsTableEvents();
            } catch (error) {
                console.error("Error initializing claims table:", error);
                this._showTableError(
                    $table,
                    "Failed to initialize claims table",
                );
            }
        },

        initStatementsTable: function () {
            const $table = $("#reinsurersTable");
            const self = this;

            if ($table.length === 0) {
                console.warn("Statements table element not found");
                return;
            }

            if (this.tables.statements) {
                console.warn("Statements table already initialized");
                return;
            }

            try {
                this.tables.statements = $table.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: window.CoverEndorsement?.config?.routes
                            ?.statementsDatatable,
                        data: function (d) {
                            d.endorsement_no =
                                window.CoverEndorsement?.config?.latestEndorsement?.endorsement_no;
                        },
                        error: function (xhr, error, code) {
                            console.error(
                                "Statements table AJAX error:",
                                error,
                            );
                            self._showTableError(
                                $table,
                                "Failed to load statements data",
                            );
                        },
                    },
                    columns: [
                        {
                            data: "tran_no",
                            name: "tran_no",
                            render: (data, type, row, meta) =>
                                meta.row + meta.settings._iDisplayStart + 1,
                        },
                        { data: "partner_name", name: "partner_name" },
                        {
                            data: "share",
                            name: "share",
                            className: "text-end",
                            render: function (data) {
                                return self._formatPercentage(data);
                            },
                        },
                        {
                            data: "gross_premium",
                            name: "gross_premium",
                            className: "text-end",
                            render: function (data) {
                                return self._formatCurrency(data);
                            },
                        },
                        {
                            data: "commission",
                            name: "commission",
                            className: "text-end",
                            render: function (data) {
                                return self._formatCurrency(data);
                            },
                        },
                        {
                            data: "brokerage",
                            name: "brokerage",
                            className: "text-end",
                            render: function (data) {
                                return self._formatCurrency(data);
                            },
                        },
                        {
                            data: "premium_tax",
                            name: "premium_tax",
                            className: "text-end",
                            render: function (data) {
                                return self._formatCurrency(data);
                            },
                        },
                        {
                            data: "wht_amount",
                            name: "wht_amount",
                            className: "text-end",
                            render: function (data) {
                                return self._formatCurrency(data);
                            },
                        },
                        {
                            data: "ri_tax",
                            name: "ri_tax",
                            className: "text-end",
                            render: function (data) {
                                return self._formatCurrency(data);
                            },
                        },
                        {
                            data: "net_amount",
                            name: "net_amount",
                            className: "text-end",
                            render: function (data) {
                                return self._formatCurrency(data);
                            },
                        },
                        {
                            data: "status",
                            name: "status",
                            render: function (data) {
                                return self._formatStatus(data);
                            },
                        },
                        {
                            data: "actions",
                            name: "actions",
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"],
                    ],
                    order: [[0, "asc"]],
                    language: {
                        emptyTable: "No statements found",
                        zeroRecords: "No matching statements found",
                        search: "Filter statements:",
                        processing:
                            '<i class="fa fa-spinner fa-spin fa-2x"></i> Loading statements...',
                    },
                    dom: '<"d-flex justify-content-between align-items-center mb-3"lf>t<"d-flex justify-content-between align-items-center mt-3"ip>',

                    footerCallback: function (row, data, start, end, display) {
                        const api = this.api();

                        const json = api.ajax.json();
                        if (json && json.totals) {
                            self._updateStatementsFooter(row, json.totals);
                        }
                    },

                    drawCallback: function (settings) {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    },
                });

                this.bindStatementsTableEvents();
            } catch (error) {
                console.error("Error initializing statements table:", error);
                this._showTableError(
                    $table,
                    "Failed to initialize statements table",
                );
            }
        },

        _updateClaimsFooter: function (row, totals) {
            if (!totals) return;

            const footer = `
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">Total Claims:</td>
                    <td class="amount-cell text-end">${this._formatCurrency(totals.claim_amount || 0)}</td>
                    <td class="amount-cell text-end">${this._formatCurrency(totals.claimed_amount || 0)}</td>
                    <td colspan="2"></td>
                </tr>
            `;

            $(row).closest("table").find("tfoot").html(footer);
        },

        _updateStatementsFooter: function (row, totals) {
            if (!totals) return;

            const footer = `
                <tr class="fw-bold">
                    <td colspan="2" class="text-end">Totals:</td>
                    <td class="amount-cell text-end">${this._formatPercentage(totals.share_percent || 0)}</td>
                    <td class="amount-cell text-end">${this._formatCurrency(totals.gross_premium || 0)}</td>
                    <td class="amount-cell text-end">${this._formatCurrency(totals.commission || 0)}</td>
                    <td class="amount-cell text-end">${this._formatCurrency(totals.brokerage || 0)}</td>
                    <td class="amount-cell text-end">${this._formatCurrency(totals.premium_tax || 0)}</td>
                    <td class="amount-cell text-end">${this._formatCurrency(totals.wht_amount || 0)}</td>
                    <td class="amount-cell text-end">${this._formatCurrency(totals.ri_tax || 0)}</td>
                    <td class="amount-cell amount-cell--positive text-end">${this._formatCurrency(totals.net_amount || 0)}</td>
                    <td colspan="2"></td>
                </tr>
            `;

            $(row).closest("table").find("tfoot").html(footer);
        },

        _formatCurrency: function (value) {
            if (!value || isNaN(value)) return "0.00";
            return parseFloat(value).toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },

        _formatPercentage: function (value) {
            if (!value || isNaN(value)) return "0.00%";
            return parseFloat(value).toFixed(2) + "%";
        },

        _formatStatus: function (status) {
            const statusMap = {
                A: '<span class="badge bg-success">Active</span>',
                P: '<span class="badge bg-warning">Pending</span>',
                C: '<span class="badge bg-secondary">Closed</span>',
                R: '<span class="badge bg-danger">Rejected</span>',
            };
            return statusMap[status] || status;
        },

        _showTableError: function ($table, message) {
            const errorHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <strong>Error!</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $table.closest(".card-body").prepend(errorHtml);
        },

        bindClaimsTableEvents: function () {
            const self = this;

            $(document).on(
                "click",
                "#claimsTable a[data-claim-no]",
                function (e) {
                    e.preventDefault();
                    const claimNo = $(this).data("claim-no");
                    const action = $(this).data("action");

                    if (
                        action === "view" ||
                        $(this).hasClass("btn-outline-primary")
                    ) {
                        self.viewClaimDetails(claimNo);
                    } else if (
                        action === "documents" ||
                        $(this).hasClass("btn-outline-info")
                    ) {
                        self.viewClaimDocuments(claimNo);
                    } else if (
                        action === "edit" ||
                        $(this).hasClass("btn-outline-warning")
                    ) {
                        self.editClaim(claimNo);
                    } else if (
                        action === "delete" ||
                        $(this).hasClass("btn-outline-danger")
                    ) {
                        self.deleteClaim(claimNo);
                    }
                },
            );

            $(document).on("dblclick", "#claimsTable tbody tr", function () {
                const rowData = self.tables.claims.row(this).data();
                if (rowData && rowData.claim_no) {
                    self.viewClaimDetails(rowData.claim_no);
                }
            });
        },

        bindStatementsTableEvents: function () {
            const self = this;

            $(document).on(
                "click",
                "#reinsurersTable a[data-tran-no]",
                function (e) {
                    e.preventDefault();
                    const tranNo = $(this).data("tran-no");
                    const action = $(this).data("action");

                    if (
                        action === "view" ||
                        $(this).hasClass("btn-outline-primary")
                    ) {
                        self.viewStatementDetails(tranNo);
                    } else if (
                        action === "generate" ||
                        $(this).hasClass("btn-outline-info")
                    ) {
                        self.generateStatement(tranNo);
                    } else if (
                        action === "edit" ||
                        $(this).hasClass("btn-outline-warning")
                    ) {
                        self.editStatement(tranNo);
                    } else if (
                        action === "delete" ||
                        $(this).hasClass("btn-outline-danger")
                    ) {
                        self.deleteStatement(tranNo);
                    }
                },
            );

            $(document).on(
                "dblclick",
                "#reinsurersTable tbody tr",
                function () {
                    const rowData = self.tables.statements.row(this).data();
                    if (rowData && rowData.tran_no) {
                        self.viewStatementDetails(rowData.tran_no);
                    }
                },
            );
        },

        viewClaimDetails: function (claimNo) {
            if (this.config.claimDetailsUrl) {
                window.location.href = this.config.claimDetailsUrl.replace(
                    ":claimNo",
                    claimNo,
                );
            } else {
                this._showDetailModal("claim", claimNo);
            }
        },

        viewClaimDocuments: function (claimNo) {
            if (this.config.claimDocumentsUrl) {
                window.location.href = this.config.claimDocumentsUrl.replace(
                    ":claimNo",
                    claimNo,
                );
            } else {
                this._showDetailModal("documents", claimNo);
            }
        },

        editClaim: function (claimNo) {
            if (this.config.editClaimUrl) {
                window.location.href = this.config.editClaimUrl.replace(
                    ":claimNo",
                    claimNo,
                );
            }
        },

        deleteClaim: function (claimNo) {
            const self = this;

            if (typeof Swal === "undefined") {
                if (!confirm("Are you sure you want to delete this claim?"))
                    return;
            } else {
                Swal.fire({
                    title: "Delete Claim?",
                    text: "This action cannot be undone.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        self._performDelete("claim", claimNo);
                    }
                });
            }
        },

        viewStatementDetails: function (tranNo) {
            if (this.config.statementDetailsUrl) {
                window.location.href = this.config.statementDetailsUrl.replace(
                    ":tranNo",
                    tranNo,
                );
            } else {
                this._showDetailModal("statement", tranNo);
            }
        },
        generateStatement: function (tranNo) {
            if (this.config.generateStatementUrl) {
                window.open(
                    this.config.generateStatementUrl.replace(":tranNo", tranNo),
                    "_blank",
                );
            } else {
                if (typeof toastr !== "undefined") {
                    toastr.info("Statement generation feature coming soon");
                }
            }
        },
        editStatement: function (tranNo) {
            if (this.config.editStatementUrl) {
                window.location.href = this.config.editStatementUrl.replace(
                    ":tranNo",
                    tranNo,
                );
            }
        },
        deleteStatement: function (tranNo) {
            const self = this;

            if (typeof Swal === "undefined") {
                if (!confirm("Are you sure you want to delete this statement?"))
                    return;
            } else {
                Swal.fire({
                    title: "Delete Statement?",
                    text: "This action cannot be undone.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        self._performDelete("statement", tranNo);
                    }
                });
            }
        },
        _showDetailModal: function (type, id) {
            if (typeof toastr !== "undefined") {
                toastr.info(`${type} details for ${id} - To be implemented`);
            } else {
                alert(`${type} Details: ${id}\n(To be implemented)`);
            }
        },
        _performDelete: function (type, id) {
            const url =
                type === "claim"
                    ? this.config.deleteClaimUrl
                    : this.config.deleteStatementUrl;

            if (!url) {
                console.error("Delete URL not configured");
                return;
            }

            $.ajax({
                url: url.replace(`:${type}No`, id),
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content",
                    ),
                },
                success: function (response) {
                    if (typeof toastr !== "undefined") {
                        toastr.success("Deleted successfully");
                    }

                    if (type === "claim" && ClaimsStatements.tables.claims) {
                        ClaimsStatements.tables.claims.ajax.reload();
                    } else if (
                        type === "statement" &&
                        ClaimsStatements.tables.statements
                    ) {
                        ClaimsStatements.tables.statements.ajax.reload();
                    }
                },
                error: function (xhr) {
                    console.error("Delete failed:", xhr);
                    if (typeof toastr !== "undefined") {
                        toastr.error("Failed to delete");
                    }
                },
            });
        },

        reloadTable: function (tableName) {
            if (this.tables[tableName]) {
                this.tables[tableName].ajax.reload(null, false); // false = stay on current page
            } else {
                console.warn(`Table ${tableName} not initialized`);
            }
        },

        reinitTable: function (tableName) {
            if (this.tables[tableName]) {
                this.tables[tableName].destroy();
                delete this.tables[tableName];

                if (tableName === "claims") {
                    this.initClaimsTable();
                } else if (tableName === "statements") {
                    this.initStatementsTable();
                }
            } else {
                console.warn(`Table ${tableName} not initialized`);
            }
        },
        destroy: function () {
            Object.keys(this.tables).forEach((tableName) => {
                if (this.tables[tableName]) {
                    this.tables[tableName].destroy();
                }
            });
            this.tables = {};
            this.initialized = false;
        },
    };

    window.ClaimsStatements = ClaimsStatements;

    $(document).ready(function () {
        try {
            ClaimsStatements.init();
        } catch (error) {
            console.error("Failed to initialize ClaimsStatements:", error);
        }
    });
})(jQuery, window);
