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

            if ($table.length === 0) {
                return;
            }

            // Check if DataTable already exists on this element
            if ($.fn.DataTable.isDataTable("#claimsTable")) {
                return;
            }

            try {
                const self = this;
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
                    footerCallback: function (row, data, start, end, display) {
                        self.claimsFooterCallback.call(
                            this,
                            row,
                            data,
                            start,
                            end,
                            display,
                        );
                    },
                });

                this.bindClaimsTableEvents();
            } catch (error) {
                console.error("Error initializing claims table:", error);
                // If reinitialization error, retrieve existing instance
                if (error.message.includes("Cannot reinitialise")) {
                    this.tables.claims = $.fn.DataTable.fnTables({
                        api: true,
                    }).filter((table) => table.dom.id === "claimsTable")[0];
                }
            }
        },

        /**
         * Calculate and update claims table footer totals
         */
        claimsFooterCallback: function (row, data, start, end, display) {
            // 'this' is the DataTable API instance in this context
            const api = this;
            let claimAmountTotal = 0;
            let claimedAmountTotal = 0;

            // Calculate totals from all rows (not just visible)
            api.column(4)
                .data()
                .each(function (value) {
                    const amount = parseFloat(
                        value.replace(/[^\d.-]/g, "") || 0,
                    );
                    claimAmountTotal += amount;
                });

            api.column(5)
                .data()
                .each(function (value) {
                    const amount = parseFloat(
                        value.replace(/[^\d.-]/g, "") || 0,
                    );
                    claimedAmountTotal += amount;
                });

            // Format and update footer
            const footerCells = $(api.table().footer()).find("td");
            const formatter = new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "USD",
            });

            if (footerCells.length > 0) {
                $(footerCells[1]).text(formatter.format(claimAmountTotal));
                $(footerCells[2]).text(formatter.format(claimedAmountTotal));
            }
        },

        /**
         * Initialize statements table with DataTable
         */
        initStatementsTable: function () {
            const $table = $("#reinsurersTable");

            if ($table.length === 0) {
                return;
            }

            // Check if DataTable already exists on this element
            if ($.fn.DataTable.isDataTable("#reinsurersTable")) {
                return;
            }

            try {
                const self = this;
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
                    footerCallback: function (row, data, start, end, display) {
                        self.statementsFooterCallback.call(
                            this,
                            row,
                            data,
                            start,
                            end,
                            display,
                        );
                    },
                });

                this.bindStatementsTableEvents();
            } catch (error) {
                console.error("Error initializing statements table:", error);
                // If reinitialization error, retrieve existing instance
                if (error.message.includes("Cannot reinitialise")) {
                    this.tables.statements = $.fn.DataTable.fnTables({
                        api: true,
                    }).filter((table) => table.dom.id === "reinsurersTable")[0];
                }
            }
        },

        /**
         * Calculate and update statements table footer totals
         */
        statementsFooterCallback: function (row, data, start, end, display) {
            // 'this' is the DataTable API instance in this context
            const api = this;
            const columnTotals = {
                sharePercent: 0,
                grossPremium: 0,
                commission: 0,
                brokerage: 0,
                premiumTax: 0,
                whtAmount: 0,
                riTax: 0,
                netAmount: 0,
            };

            // Calculate totals for each currency column
            const currencyColumns = [3, 4, 5, 6, 7, 8, 9];
            const percentColumn = 2;

            // Sum percentage column
            api.column(percentColumn)
                .data()
                .each(function (value) {
                    const amount = parseFloat(
                        value.replace(/[^\d.-]/g, "") || 0,
                    );
                    columnTotals.sharePercent += amount;
                });

            // Sum currency columns
            api.column(3)
                .data()
                .each(function (value) {
                    const amount = parseFloat(
                        value.replace(/[^\d.-]/g, "") || 0,
                    );
                    columnTotals.grossPremium += amount;
                });
            api.column(4)
                .data()
                .each(function (value) {
                    const amount = parseFloat(
                        value.replace(/[^\d.-]/g, "") || 0,
                    );
                    columnTotals.commission += amount;
                });
            api.column(5)
                .data()
                .each(function (value) {
                    const amount = parseFloat(
                        value.replace(/[^\d.-]/g, "") || 0,
                    );
                    columnTotals.brokerage += amount;
                });
            api.column(6)
                .data()
                .each(function (value) {
                    const amount = parseFloat(
                        value.replace(/[^\d.-]/g, "") || 0,
                    );
                    columnTotals.premiumTax += amount;
                });
            api.column(7)
                .data()
                .each(function (value) {
                    const amount = parseFloat(
                        value.replace(/[^\d.-]/g, "") || 0,
                    );
                    columnTotals.whtAmount += amount;
                });
            api.column(8)
                .data()
                .each(function (value) {
                    const amount = parseFloat(
                        value.replace(/[^\d.-]/g, "") || 0,
                    );
                    columnTotals.riTax += amount;
                });

            // Calculate net amount total
            columnTotals.netAmount =
                columnTotals.grossPremium -
                columnTotals.commission -
                columnTotals.brokerage +
                columnTotals.premiumTax +
                columnTotals.whtAmount +
                columnTotals.riTax;

            // Format and update footer
            const footerCells = $(api.table().footer()).find("td");
            const formatter = new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "USD",
            });

            if (footerCells.length > 0) {
                $(footerCells[1]).text(
                    columnTotals.sharePercent.toFixed(2) + "%",
                );
                $(footerCells[2]).text(
                    formatter.format(columnTotals.grossPremium),
                );
                $(footerCells[3]).text(
                    formatter.format(columnTotals.commission),
                );
                $(footerCells[4]).text(
                    formatter.format(columnTotals.brokerage),
                );
                $(footerCells[5]).text(
                    formatter.format(columnTotals.premiumTax),
                );
                $(footerCells[6]).text(
                    formatter.format(columnTotals.whtAmount),
                );
                $(footerCells[7]).text(formatter.format(columnTotals.riTax));
                $(footerCells[8]).text(
                    formatter.format(columnTotals.netAmount),
                );
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
