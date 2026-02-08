/**
 * Claims and Statements Table Handlers
 * Handles DataTable initialization for claims and statements tabs
 */

(function ($, window) {
    "use strict";

    const ClaimsStatements = {
        tables: {},
        initialized: false,

        /**
         * Initialize claims and statements tables
         */
        init: function () {
            if (this.initialized) return;

            this.bindTabEvents();
            this.initialized = true;
        },

        /**
         * Bind tab change events to lazy-load tables
         */
        bindTabEvents: function () {
            const self = this;

            // Listen for tab changes
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

        /**
         * Initialize claims table with DataTable
         */
        initClaimsTable: function () {
            const $table = $("#claimsTable");

            if ($table.length === 0 || this.tables.claims) {
                return;
            }

            try {
                this.tables.claims = $table.DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"],
                    ],
                    order: [[1, "desc"]],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: [0, 7],
                        },
                        {
                            className: "text-end",
                            targets: [4, 5],
                        },
                    ],
                    language: {
                        emptyTable: "No claims found",
                        zeroRecords: "No matching claims found",
                        search: "Filter claims:",
                    },
                    dom: '<"d-flex justify-content-between align-items-center mb-3"lf>t<"d-flex justify-content-between align-items-center mt-3"ip>',
                });

                this.bindClaimsTableEvents();
            } catch (error) {
                console.error("Error initializing claims table:", error);
            }
        },

        /**
         * Initialize statements table with DataTable
         */
        initStatementsTable: function () {
            const $table = $("#reinsurersTable");

            if ($table.length === 0 || this.tables.statements) {
                return;
            }

            try {
                this.tables.statements = $table.DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"],
                    ],
                    order: [[0, "asc"]],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: [0, 11],
                        },
                        {
                            className: "text-end",
                            targets: [2, 3, 4, 5, 6, 7, 8, 9],
                        },
                    ],
                    language: {
                        emptyTable: "No statements found",
                        zeroRecords: "No matching statements found",
                        search: "Filter statements:",
                    },
                    dom: '<"d-flex justify-content-between align-items-center mb-3"lf>t<"d-flex justify-content-between align-items-center mt-3"ip>',
                });

                this.bindStatementsTableEvents();
            } catch (error) {
                console.error("Error initializing statements table:", error);
            }
        },

        /**
         * Bind event handlers for claims table actions
         */
        bindClaimsTableEvents: function () {
            const self = this;

            $(document).on(
                "click",
                "#claimsTable a[data-claim-no]",
                function (e) {
                    e.preventDefault();
                    const claimNo = $(this).data("claim-no");

                    if ($(this).hasClass("btn-outline-primary")) {
                        // View claim details
                        self.viewClaimDetails(claimNo);
                    } else if ($(this).hasClass("btn-outline-info")) {
                        // View claim documents
                        self.viewClaimDocuments(claimNo);
                    }
                },
            );
        },

        /**
         * Bind event handlers for statements table actions
         */
        bindStatementsTableEvents: function () {
            const self = this;

            $(document).on(
                "click",
                "#reinsurersTable a[data-tran-no]",
                function (e) {
                    e.preventDefault();
                    const tranNo = $(this).data("tran-no");

                    if ($(this).hasClass("btn-outline-primary")) {
                        // View statement details
                        self.viewStatementDetails(tranNo);
                    } else if ($(this).hasClass("btn-outline-info")) {
                        // Generate statement document
                        self.generateStatement(tranNo);
                    }
                },
            );
        },

        /**
         * View claim details (placeholder)
         */
        viewClaimDetails: function (claimNo) {
            console.log("Viewing claim details for:", claimNo);
            // TODO: Implement view claim details
            alert("Claim Details: " + claimNo + "\n(To be implemented)");
        },

        /**
         * View claim documents (placeholder)
         */
        viewClaimDocuments: function (claimNo) {
            console.log("Viewing claim documents for:", claimNo);
            // TODO: Implement view claim documents
            alert("Claim Documents: " + claimNo + "\n(To be implemented)");
        },

        /**
         * View statement details (placeholder)
         */
        viewStatementDetails: function (tranNo) {
            console.log("Viewing statement details for:", tranNo);
            // TODO: Implement view statement details
            alert("Statement Details: " + tranNo + "\n(To be implemented)");
        },

        /**
         * Generate statement document (placeholder)
         */
        generateStatement: function (tranNo) {
            console.log("Generating statement for:", tranNo);
            // TODO: Implement generate statement
            alert("Generate Statement: " + tranNo + "\n(To be implemented)");
        },

        /**
         * Reload a table
         */
        reloadTable: function (tableName) {
            if (this.tables[tableName]) {
                this.tables[tableName].ajax.reload();
            }
        },

        /**
         * Destroy and reinitialize a table
         */
        reinitTable: function (tableName) {
            if (this.tables[tableName]) {
                this.tables[tableName].destroy();
                delete this.tables[tableName];

                if (tableName === "claims") {
                    this.initClaimsTable();
                } else if (tableName === "statements") {
                    this.initStatementsTable();
                }
            }
        },
    };

    // Expose to global scope
    window.ClaimsStatements = ClaimsStatements;

    // Initialize when DOM is ready
    $(document).ready(function () {
        ClaimsStatements.init();
    });
})(jQuery, window);
