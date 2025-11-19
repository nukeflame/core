/**
 * Cover Details Page
 *
 */

(function ($) {
    "use strict";

    const CoverDetails = {
        $el: {},

        state: {
            coverData: null,
            distributedShare: 0,
            origDistributedShare: 0,
            selectedReinsurers: [],
            installmentTotalAmount: 0,
            lastReinData: {
                tranNo: null,
                debitUrl: null,
                claimNoticeUrl: null,
            },
        },

        config: {
            dataTablesOptions: {
                order: [[0, "desc"]],
                processing: true,
                // serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
            },
        },

        init: function () {
            this.cacheDOMElements();
            this.loadCoverData();
            this.initializePlugins();
            this.initializeDataTables();
            this.bindEvents();
            this.setActiveTab();
        },

        cacheDOMElements: function () {
            this.$el = {
                app: $("#coverDetailsApp"),

                // Forms
                schedulesForm: $("#schedulesForm"),
                attachmentsForm: $("#attachmentsForm"),
                clausesForm: $("#clausesForm"),
                reinsurerForm: $("#reinsurerForm"),
                editReinsurerForm: $("#EditReinsurerForm"),
                verifyForm: $("#verifyForm"),
                debitForm: $("#debitForm"),
                insuranceClassForm: $("#insuranceClassForm"),

                // Modals
                schedulesModal: $("#schedulesModal"),
                attachmentsModal: $("#attachments-modal"),
                clausesModal: $("#clauses-modal"),
                reinsurerModal: $("#reinsurer-modal"),
                editReinsurerModal: $("#edit-reinsurer-modal"),
                verifyModal: $("#verify-modal"),
                debitModal: $("#debit-modal"),
                insuranceClassModal: $("#insurance-class-modal"),
                sendEmailModal: $("#sendReinDocumentEmail"),

                // Tables
                schedulesTable: $("#schedules-table"),
                attachmentsTable: $("#attachments-table"),
                clausesTable: $("#clauses-table"),
                reinsurersTable: $("#reinsurers-table"),
                installmentsTable: $("#installments-table"),
                insClassTable: $("#insclass-table"),
                approvalsTable: $("#approvals-table"),
                debitsTable: $("#debits-table"),
                endorseNarrationTable: $("#endorse-narration-table"),

                // Buttons
                editCoverBtn: $("#edit-cover"),
                verifyDetailsBtn: $("#verify_details"),
                generateSlipBtn: $("#generate_slip"),
                commitCoverBtn: $("#commit-cover"),

                // Navigation
                tabNav: $(".reinsurers-details-card .nav-link"),

                // Other elements
                scheduleDescription: $("#schedule_description"),
                hiddenScheduleDescription: $("#hidden_schedule_description"),
            };
        },

        loadCoverData: function () {
            const $app = this.$el.app;

            this.state.coverData = {
                id: $app.data("cover-id"),
                endorsement_no: $app.data("endorsement-no"),
                type_of_bus: $app.data("type-of-bus"),
                cover_no: $app.data("cover-no"),
            };
        },

        initializePlugins: function () {
            // this.initializeSelect2();

            // Initialize TinyMCE if needed
            // this.initializeTinyMCE();

            // Initialize tooltips
            this.initializeTooltips();
        },

        initializeSelect2: function () {
            const self = this;

            this.$el.clausesModal.on("shown.bs.modal", function () {
                $(".form-inputs", this).select2({
                    dropdownParent: self.$el.clausesModal,
                });
            });

            this.$el.attachmentsModal.on("shown.bs.modal", function () {
                $(".form-inputs", this).select2({
                    dropdownParent: self.$el.attachmentsModal,
                });
            });

            this.$el.verifyModal.on("shown.bs.modal", function () {
                $(".form-inputs", this).select2({
                    dropdownParent: self.$el.verifyModal,
                });
            });

            this.$el.insuranceClassModal.on("shown.bs.modal", function () {
                $(".form-inputs", this).select2({
                    dropdownParent: self.$el.insuranceClassModal,
                });
            });

            this.$el.reinsurerModal.on("shown.bs.modal", function () {
                $(".form-inputs", this).select2({
                    dropdownParent: self.$el.reinsurerModal,
                });
            });

            this.$el.editReinsurerModal.on("shown.bs.modal", function () {
                $(".form-inputs", this).select2({
                    dropdownParent: self.$el.editReinsurerModal,
                });
            });

            this.$el.schedulesModal.on("shown.bs.modal", function () {
                $(".form-inputs", this).select2({
                    dropdownParent: self.$el.schedulesModal,
                });
            });
        },

        initializeTooltips: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        },

        initializeDataTables: function () {
            this.initSchedulesTable();
            this.initAttachmentsTable();
            this.initClausesTable();
            this.initReinsurersTable();
            this.initInstallmentsTable();
            this.initInsClassTable();
            this.initApprovalsTable();
            this.initDebitsTable();
            this.initEndorseNarrationTable();
        },

        initSchedulesTable: function () {
            if (!this.$el.schedulesTable.length) return;

            const self = this;

            this.$el.schedulesTable.DataTable({
                ...this.config.dataTablesOptions,
                ajax: {
                    url: this.$el.schedulesTable.data("url"),
                    data: function (d) {
                        d.endorsement_no = self.state.coverData.endorsement_no;
                    },
                },
                columns: [
                    {
                        data: "id",
                        className: "highlight-idx",
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        },
                    },
                    {
                        data: "title",
                        searchable: true,
                        className: "highlight-view-point",
                    },
                    {
                        data: "details",
                        searchable: true,
                        className: "highlight-description clamp-text",
                    },
                    {
                        data: "schedule_position",
                        searchable: false,
                    },
                    {
                        data: "action",
                        className: "highlight-action",
                        searchable: false,
                        sortable: false,
                    },
                ],
            });
        },

        initAttachmentsTable: function () {
            if (!this.$el.attachmentsTable.length) return;

            const self = this;

            this.$el.attachmentsTable.DataTable({
                ...this.config.dataTablesOptions,
                ajax: {
                    url: this.$el.attachmentsTable.data("url"),
                    data: function (d) {
                        d.endorsement_no = self.state.coverData.endorsement_no;
                    },
                },
                columns: [
                    { data: "id", searchable: true },
                    { data: "title", searchable: true },
                    { data: "action", searchable: false },
                ],
            });
        },

        initClausesTable: function () {
            if (!this.$el.clausesTable.length) return;

            const self = this;

            this.$el.clausesTable.DataTable({
                ...this.config.dataTablesOptions,
                ajax: {
                    url: this.$el.clausesTable.data("url"),
                    data: function (d) {
                        d.endorsement_no = self.state.coverData.endorsement_no;
                    },
                },
                columns: [
                    {
                        data: "clause_id",
                        className: "highlight-idx",
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        },
                    },
                    { data: "clause_title", searchable: true },
                    {
                        data: "clause_wording",
                        searchable: true,
                        className: "highlight-description clamp-text",
                    },
                    {
                        data: "action",
                        searchable: false,
                        sortable: false,
                    },
                ],
            });
        },

        initReinsurersTable: function () {
            if (!this.$el.reinsurersTable.length) return;

            const self = this;
            const typeOfBus = this.state.coverData.type_of_bus;

            let columns = [
                {
                    data: "tran_no",
                    searchable: true,
                    className: "highlight-index",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                },
                {
                    data: "partner_name",
                    searchable: true,
                    className: "highlight-view-point",
                },
                {
                    data: "share",
                    searchable: true,
                    render: $.fn.dataTable.render.number(",", ".", 2, ""),
                },
            ];

            if (["FPR", "FNP"].includes(typeOfBus)) {
                columns = columns.concat([
                    {
                        data: "sum_insured",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "premium",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "comm_rate",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "commission",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "brokerage_comm_amt",
                        searchable: false,
                        className: "highlight-2view-point",
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "wht_amt",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "fronting_amt",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                ]);
            }

            columns.push({
                data: "action",
                searchable: false,
                sortable: false,
                className: "highlight-view-more2",
            });

            this.$el.reinsurersTable.DataTable({
                ...this.config.dataTablesOptions,
                ajax: {
                    url: this.$el.reinsurersTable.data("url"),
                    data: function (d) {
                        d.endorsement_no = self.state.coverData.endorsement_no;
                    },
                },
                columns: columns,
                paging: false,
                drawCallback: function (settings) {
                    self.calculateReinsurersTableFooter(this.api(), typeOfBus);
                },
            });
        },

        calculateReinsurersTableFooter: function (api, businessType) {
            $("#reinsurers-table tfoot").empty();

            let columnsToSum = [2];

            if (businessType === "FPR" || businessType === "FNP") {
                columnsToSum = columnsToSum.concat([3, 4, 6, 7, 8, 9]);
            }

            let footerRow = "<tr>";
            footerRow +=
                '<td colspan="2" style="text-align:right !important; font-weight:bold; color: #000; padding: 6px 8px; font-size: 13px;">Totals:</td>';

            const columns = api.columns().nodes().length;
            for (let i = 2; i < columns - 1; i++) {
                if (columnsToSum.includes(i)) {
                    const sum = api
                        .column(i, { search: "applied" })
                        .data()
                        .reduce(function (a, b) {
                            const aFloat =
                                parseFloat(a.toString().replace(/,/g, "")) || 0;
                            const bFloat =
                                parseFloat(b.toString().replace(/,/g, "")) || 0;
                            return aFloat + bFloat;
                        }, 0);

                    const formattedSum = $.fn.dataTable.render
                        .number(",", ".", 2, "")
                        .display(sum);
                    footerRow +=
                        '<td style="font-weight:bold; padding: 6px 8px; color: #000;">' +
                        formattedSum +
                        "</td>";
                } else {
                    footerRow += "<td></td>";
                }
            }
            footerRow += "<td></td></tr>";

            if (!$("#reinsurers-table tfoot").length) {
                $("#reinsurers-table").append("<tfoot></tfoot>");
            }
            $("#reinsurers-table tfoot").html(footerRow);

            $("#reinsurers-table tfoot tr").css({
                "background-color": "#f5f5f5",
                "border-top": "2px solid #ddd",
            });
        },

        initInstallmentsTable: function () {
            if (!this.$el.installmentsTable.length) return;

            const self = this;

            this.$el.installmentsTable.DataTable({
                ...this.config.dataTablesOptions,
                ajax: {
                    url: this.$el.installmentsTable.data("url"),
                    data: function (d) {
                        d.endorsement_no = self.state.coverData.endorsement_no;
                    },
                },
                columns: [
                    { data: "installment_no", searchable: true },
                    { data: "installment_date", searchable: true },
                    {
                        data: "installment_amt",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    { data: "action", searchable: false },
                ],
            });
        },

        initInsClassTable: function () {
            if (!this.$el.insClassTable.length) return;

            const self = this;

            this.$el.insClassTable.DataTable({
                ...this.config.dataTablesOptions,
                // ajax: {
                //     url: this.$el.insClassTable.data("url"),
                //     data: function (d) {
                //         d.endorsement_no = self.state.coverData.endorsement_no;
                //     },
                // },
                data: [],
                columns: [
                    { data: "id", searchable: true },
                    { data: "reinclass_name", searchable: true },
                    { data: "class", searchable: true },
                    { data: "class_name", searchable: true },
                    { data: "action", searchable: false },
                ],
            });
        },

        initApprovalsTable: function () {
            if (!this.$el.approvalsTable.length) return;

            const self = this;

            this.$el.approvalsTable.DataTable({
                ...this.config.dataTablesOptions,
                ajax: {
                    url: this.$el.approvalsTable.data("url"),
                    data: function (d) {
                        d.endorsement_no = self.state.coverData.endorsement_no;
                    },
                },
                columns: [
                    { data: "id", searchable: true },
                    { data: "approver", searchable: true },
                    { data: "comment", searchable: true },
                    { data: "approver_comment", searchable: true },
                    { data: "status", searchable: false },
                    {
                        data: "action",
                        searchable: false,
                        sortable: false,
                    },
                ],
            });
        },

        initDebitsTable: function () {
            if (!this.$el.debitsTable.length) return;

            const self = this;

            this.$el.debitsTable.DataTable({
                ...this.config.dataTablesOptions,
                ajax: {
                    url: this.$el.debitsTable.data("url"),
                    data: function (d) {
                        d.endorsement_no = self.state.coverData.endorsement_no;
                    },
                },
                columns: [
                    {
                        data: "id",
                        searchable: false,
                        className: "highlight-idx",
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        },
                    },
                    {
                        data: "cedant",
                        searchable: true,
                        className: "highlight-2view-point",
                    },
                    { data: "dr_no", searchable: true },
                    { data: "installment", searchable: true },
                    { data: "share", searchable: true },
                    {
                        data: "sum_insured",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "premium",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "gross",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "net_amt",
                        searchable: false,
                        render: $.fn.dataTable.render.number(",", ".", 2, ""),
                    },
                    {
                        data: "action",
                        searchable: false,
                        sortable: false,
                        className: "highlight-view-more",
                    },
                ],
                paging: false,
                drawCallback: function (settings) {
                    self.calculateDebitsTableFooter(this.api());
                },
            });
        },

        calculateDebitsTableFooter: function (api) {
            $("#debits-table tfoot").empty();
            const columnsToSum = [4, 5, 6, 7, 8];

            let footerRow = "<tr>";
            footerRow +=
                '<td colspan="4" style="text-align:right !important; font-weight:bold; color: #000; padding: 6px 8px; font-size: 13px;">Totals:</td>';

            const columns = api.columns().nodes().length;
            for (let i = 4; i < columns - 1; i++) {
                if (columnsToSum.includes(i)) {
                    const sum = api
                        .column(i, { search: "applied" })
                        .data()
                        .reduce(function (a, b) {
                            const aFloat =
                                parseFloat(a.toString().replace(/,/g, "")) || 0;
                            const bFloat =
                                parseFloat(b.toString().replace(/,/g, "")) || 0;
                            return aFloat + bFloat;
                        }, 0);

                    const formattedSum = $.fn.dataTable.render
                        .number(",", ".", 2, "")
                        .display(sum);
                    footerRow +=
                        '<td style="font-weight:bold; padding: 6px 8px; color: #000;">' +
                        formattedSum +
                        "</td>";
                } else {
                    footerRow += "<td></td>";
                }
            }
            footerRow += "<td></td></tr>";

            if (!$("#debits-table tfoot").length) {
                $("#debits-table").append("<tfoot></tfoot>");
            }
            $("#debits-table tfoot").html(footerRow);

            $("#debits-table tfoot tr").css({
                "background-color": "#f5f5f5",
                "border-top": "2px solid #ddd",
            });
        },

        initEndorseNarrationTable: function () {
            if (!this.$el.endorseNarrationTable.length) return;

            const self = this;

            this.$el.endorseNarrationTable.DataTable({
                ...this.config.dataTablesOptions,
                ajax: {
                    url: this.$el.endorseNarrationTable.data("url"),
                    data: function (d) {
                        d.cover_no = self.state.coverData.cover_no;
                        d.endorsement_no = self.state.coverData.endorsement_no;
                    },
                },
                columns: [
                    {
                        data: "document_no",
                        className: "highlight-idx",
                    },
                    { data: "endorsement_type", searchable: true },
                    { data: "narration", searchable: true },
                    { data: "extension_days", searchable: false },
                    {
                        data: "action",
                        searchable: false,
                        sortable: false,
                    },
                ],
            });
        },

        // ============================================================================
        // EVENT BINDING
        // ============================================================================

        bindEvents: function () {
            this.bindNavigationEvents();
            this.bindFormEvents();
            this.bindButtonEvents();
            this.bindModalEvents();
            this.bindTableEvents();
            this.bindCalculationEvents();
            this.bindDynamicFieldEvents();
        },

        bindNavigationEvents: function () {
            const self = this;

            // Tab navigation
            this.$el.tabNav.on("click", function () {
                const hash = $(this).data("bs-target");
                if (hash) {
                    window.history.pushState(
                        null,
                        null,
                        window.location.pathname + window.location.search + hash
                    );
                }
                self.$el.tabNav.removeClass("active");
                $(this).addClass("active");
            });

            $(window).on("hashchange", function () {
                self.setActiveTab();
            });

            // Navigation links
            $("#to-cover").on("click", function (e) {
                e.preventDefault();
                $("#coverForm").submit();
            });

            $("#to-customer").on("click", function (e) {
                e.preventDefault();
                $("#customerForm").submit();
            });

            $("#edit-cover").on("click", function (e) {
                e.preventDefault();
                $("#editCoverForm").submit();
            });
        },

        bindFormEvents: function () {
            const self = this;

            // Schedule form validation
            if (this.$el.schedulesForm.length) {
                this.$el.schedulesForm.validate({
                    errorClass: "errorClass",
                    rules: {
                        title: { required: true },
                    },
                    submitHandler: function (form) {
                        self.handleScheduleFormSubmit(form);
                    },
                });
            }

            // Attachments form validation
            if (this.$el.attachmentsForm.length) {
                this.$el.attachmentsForm.validate({
                    errorClass: "errorClass",
                    rules: {
                        title: { required: true },
                        file: { required: true },
                    },
                    submitHandler: function (form) {
                        self.handleAttachmentFormSubmit(form);
                    },
                });
            }

            // Clauses form validation
            if (this.$el.clausesForm.length) {
                this.$el.clausesForm.validate({
                    errorClass: "errorClass",
                    rules: {
                        clause_id: { required: true },
                    },
                    submitHandler: function (form) {
                        self.handleClausesFormSubmit(form);
                    },
                });
            }

            // Reinsurer form validation
            if (this.$el.reinsurerForm.length) {
                this.$el.reinsurerForm.validate({
                    errorClass: "errorClass",
                    submitHandler: function (form) {
                        self.handleReinsurerFormSubmit(form);
                    },
                });
            }

            // Edit Reinsurer form validation
            if (this.$el.editReinsurerForm.length) {
                this.$el.editReinsurerForm.validate({
                    errorClass: "errorClass",
                    submitHandler: function (form) {
                        self.handleEditReinsurerFormSubmit(form);
                    },
                });
            }

            // Verify form validation
            if (this.$el.verifyForm.length) {
                this.$el.verifyForm.validate({
                    errorClass: "errorClass",
                    rules: {
                        process: { required: true },
                        process_action: { required: true },
                        comment: { required: true },
                    },
                    submitHandler: function (form) {
                        self.handleVerifyFormSubmit(form);
                    },
                });
            }

            // Debit form validation
            if (this.$el.debitForm.length) {
                this.$el.debitForm.validate({
                    errorClass: "errorClass",
                    rules: {
                        endorsement_no: { required: true },
                        installment: { required: true, min: 1 },
                        amount: { required: true },
                    },
                    submitHandler: function (form) {
                        self.handleDebitFormSubmit(form);
                    },
                });
            }

            // Insurance class form validation
            if (this.$el.insuranceClassForm.length) {
                this.$el.insuranceClassForm.validate({
                    errorClass: "errorClass",
                    rules: {
                        reinclass: { required: true },
                        class: { required: true },
                    },
                    submitHandler: function (form) {
                        self.handleInsuranceClassFormSubmit(form);
                    },
                });
            }
        },

        bindButtonEvents: function () {
            const self = this;

            // Verify details button
            this.$el.verifyDetailsBtn.on("click", function (e) {
                e.preventDefault();
                self.handleVerifyDetails();
            });

            // Generate slip button
            this.$el.generateSlipBtn.on("click", function (e) {
                e.preventDefault();
                self.handleGenerateSlip();
            });

            // Save buttons
            $("#schedule-save-btn").on("click", function () {
                self.$el.schedulesForm.submit();
            });

            $("#attachments-save-btn").on("click", function () {
                self.$el.attachmentsForm.submit();
            });

            $("#clauses-save-btn").on("click", function () {
                self.$el.clausesForm.submit();
            });

            $("#partner-save-btn").on("click", function () {
                self.$el.reinsurerForm.submit();
            });

            $("#partner-edit-btn").on("click", function () {
                self.$el.editReinsurerForm.submit();
            });

            $("#verify-save-btn").on("click", function () {
                self.$el.verifyForm.submit();
            });

            $("#debit-save-btn").on("click", function () {
                self.$el.debitForm.submit();
            });

            $("#ins-class-save-btn").on("click", function () {
                self.$el.insuranceClassForm.submit();
            });

            // Close buttons
            $(".closeScheduleForm").on("click", function (e) {
                e.preventDefault();
                self.$el.schedulesForm[0].reset();
            });

            $("#dismiss-partner-btn").on("click", function () {
                self.$el.reinsurerForm[0].reset();
            });
        },

        bindModalEvents: function () {
            const self = this;

            // Schedule details modal
            $(document).on("click", "#schedule-details", function () {
                self.$el.schedulesForm[0].reset();
                self.$el.schedulesForm.find('[name="_method"]').val("POST");
            });

            // Attachments modal
            $(document).on("click", "#attachments", function () {
                self.$el.attachmentsForm[0].reset();
                self.$el.attachmentsForm.find('[name="_method"]').val("POST");
            });

            // Schedule header change
            $(document).on("change", "#sched-header", function () {
                const schedTitle = $(this).find("option:selected").data("name");
                $("#title").val(schedTitle);
            });

            // Apply fronting change
            $(document).on("change", ".apply_fronting", function () {
                const counter = $(this).data("counter");
                const option = $(this).val();

                $(`#fronting_amt_div-${counter}`).hide();
                $(`#fronting_rate_div-${counter}`).hide();
                $(`#fronting_rate-${counter}`).val(null);
                $(`#fronting_amt-${counter}`).val(null);

                if (option === "Y") {
                    $(`#fronting_amt_div-${counter}`).show();
                    $(`#fronting_rate_div-${counter}`).show();
                }
            });

            // Brokerage commission type change
            $("#brokerage_comm_type").on("change", function () {
                const brokerageCommType = $(this).val();

                $(".brokerage_comm_amt_div").hide();
                $(".brokerage_comm_rate_div").hide();

                if (brokerageCommType === "R") {
                    $(".brokerage_comm_rate_div").show();
                    $("#brokerage_comm_rate").show();
                    $("#brokerage_comm_rate_amnt").show();
                    self.calculateBrokerageCommRate();
                } else {
                    $(".brokerage_comm_amt_div").show();
                    $("#reinsurer-brokerage_comm_amt-0")
                        .show()
                        .prop("disabled", false);
                }
            });
        },

        bindTableEvents: function () {
            const self = this;

            // Edit schedule
            $(document).on("click", ".edit-schedule", function () {
                const data = $(this).data();
                self.populateScheduleForm(data);
            });

            // Remove schedule
            $(document).on("click", ".remove-schedule", function () {
                const dataId = $(this).data("id");
                const dataName = $(this).data("name");
                self.confirmRemoveSchedule(dataId, dataName);
            });

            // Edit attachment
            $(document).on("click", ".edit-attachment", function () {
                const data = $(this).data();
                self.populateAttachmentForm(data);
            });

            // Remove attachment
            $(document).on("click", ".remove-attachment", function () {
                const data = $(this).data();
                self.confirmRemoveAttachment(data);
            });

            // View attachment
            $(document).on("click", ".view-attachment", function () {
                const base64Data = $(this).data("base64");
                const mimeType = $(this).data("mime");
                self.showAttachmentPreview(base64Data, mimeType);
            });

            // Remove clause
            $(document).on("click", ".remove-clause", function () {
                const data = $(this).data();
                self.confirmRemoveClause(data);
            });

            // Edit reinsurer
            $(document).on("click", ".edit-reinsurer", function () {
                const data = $(this).data("data");
                const reinsurer = $(this).data("reinsurer");
                const distributedShare = $(this).data("distributed-share");
                self.populateEditReinsurerForm(
                    data,
                    reinsurer,
                    distributedShare
                );
            });

            // Remove reinsurer
            $(document).on("click", ".remove-reinsurer", function () {
                const shareData = $(this).data("data");
                const reinsurer = $(this).data("reinsurer");
                self.confirmRemoveReinsurer(shareData, reinsurer);
            });

            // Send reinsurer email
            $(document).on("click", ".send_reinsurer_email", function (e) {
                e.preventDefault();
                self.openReinsurerEmailModal($(this));
            });

            // Send cedant email
            $(document).on("click", ".send-cedant-email", function (e) {
                e.preventDefault();
                self.openCedantEmailModal($(this));
            });
        },

        bindCalculationEvents: function () {
            const self = this;

            // Reinsurer share input
            $("#reinsurer-modal").on("input", ".reinsurer-share", function () {
                self.handleReinsurerShareInput($(this));
            });

            $("#edit-reinsurer-modal").on(
                "input",
                "#edreinsurer-share",
                function () {
                    self.handleEditReinsurerShareInput($(this));
                }
            );

            // Commission rate input
            $("#reinsurer-modal").on(
                "keyup",
                'input[name="comm_rate"]',
                function () {
                    const counter = $(this).data("counter");
                    self.computeCommissionAmt(counter);
                }
            );

            $("#edit-reinsurer-modal").on(
                "keyup change",
                'input[name="comm_rate"], input[name="premium"], input[name="apply_fronting"]',
                function () {
                    self.computeEditReinsurerCommission();
                }
            );

            // Commission amount input
            $("#reinsurer-modal").on(
                "keyup",
                ".reinsurer-comm-amt",
                function () {
                    const counter = $(this).data("counter");
                    self.computeCommissionRate(counter);
                }
            );

            // Fronting rate input
            $(document).on("keyup", ".reinsurer-fronting_rate", function () {
                const counter = $(this).data("counter");
                self.computeFrontingAmount(counter);
            });

            $(document).on("keyup", "#edreinsurer-fronting_rate", function () {
                self.computeEditFrontingAmount();
            });

            // Premium input
            $(document).on("keyup", ".reinsurer-premium", function () {
                const counter = $(this).data("counter");
                self.computeCommissionAmt(counter);
            });
        },

        bindDynamicFieldEvents: function () {
            const self = this;

            // Add treaty-reinsurer section
            $("#add-treaty-reinsurer").on("click", function () {
                self.addTreatyReinsurerSection();
            });

            // Add reinsurer
            $(document).on("click", ".add-reinsurer", function () {
                const counter = $(this).data("counter");
                self.addReinsurerSection(counter);
            });

            // Remove reinsurer
            $("#reinsurer_plan_section").on(
                "click",
                "#remove_reinsurer_instalment",
                function () {
                    self.removeReinsurerInstallment($(this));
                }
            );

            // Payment method change
            $("select#reins_pay_method").on("change", function () {
                self.handlePaymentMethodChange();
            });

            // Number of installments change
            $("#no_of_installments").on("change keyup", function () {
                const inst = $(this).val();
                if (!inst) {
                    $("#add_installments_box").hide();
                }
            });

            // Add installments
            $("#add_reinsurer_instalments").on("click", function () {
                self.addReinsurerInstallments();
            });

            // Treaty change
            $(document).on("change", ".reinsurer-treaty", function () {
                self.handleTreatyChange($(this));
            });
        },

        // ============================================================================
        // FORM SUBMISSION HANDLERS
        // ============================================================================

        handleScheduleFormSubmit: function (form) {
            const self = this;
            let method = this.$el.schedulesForm.find('[name="_method"]').val();
            let url =
                method === "POST"
                    ? this.$el.schedulesForm.data("post-url")
                    : this.$el.schedulesForm.data("put-url");

            let formData = new FormData(form);
            formData.append("details", this.$el.scheduleDescription.html());

            fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams(formData),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === 201) {
                        toastr.success("Schedule Successfully saved");
                        setTimeout(() => window.location.reload(), 1500);
                    } else if (data.status === 422) {
                        self.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to save details");
                    }
                })
                .catch((error) => {
                    toastr.error("An error occurred");
                    console.error(error);
                });
        },

        handleAttachmentFormSubmit: function (form) {
            const self = this;
            $("#attachments-save-btn")
                .prop("disabled", true)
                .html(
                    '<span class="me-2">Submiting...</span><div class="loading"></div>'
                );

            let method = this.$el.attachmentsForm
                .find('[name="_method"]')
                .val();
            let url =
                method === "POST"
                    ? this.$el.attachmentsForm.data("post-url")
                    : this.$el.attachmentsForm.data("put-url");

            let formData = new FormData(form);

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#attachments-save-btn")
                        .prop("disabled", false)
                        .text("Submit");

                    if (data.status === 201) {
                        toastr.success("Attachment saved Successfully");
                        setTimeout(() => window.location.reload(), 2000);
                    } else if (data.status === 422) {
                        self.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to save attachment");
                    }
                })
                .catch((error) => {
                    toastr.error("Failed to save attachment");
                    $("#attachments-save-btn")
                        .prop("disabled", false)
                        .text("Submit");
                });
        },

        handleClausesFormSubmit: function (form) {
            const self = this;
            $("#clauses-save-btn").prop("disabled", true).text("Saving...");

            let method = this.$el.clausesForm.find('[name="_method"]').val();
            let url =
                method === "POST"
                    ? this.$el.clausesForm.data("post-url")
                    : this.$el.clausesForm.data("put-url");

            let formData = new FormData(form);

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#clauses-save-btn")
                        .prop("disabled", false)
                        .text("Submit");

                    if (data.status === 201) {
                        toastr.success("Clauses saved Successfully");
                        setTimeout(() => window.location.reload(), 2000);
                    } else if (data.status === 422) {
                        self.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to save clauses");
                    }
                })
                .catch((error) => {
                    toastr.error("Failed to save clauses");
                    $("#clauses-save-btn")
                        .prop("disabled", false)
                        .text("Submit");
                });
        },

        handleReinsurerFormSubmit: function (form) {
            const self = this;
            $("#partner-save-btn")
                .prop("disabled", true)
                .html(
                    '<span class="me-2">Saving...</span><div class="loading"></div>'
                );

            let formData = new FormData(form);

            fetch(this.$el.reinsurerForm.data("url"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams(formData),
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#partner-save-btn").prop("disabled", false).text("Save");

                    if (data.status === 201) {
                        toastr.success(data.message);
                        setTimeout(() => window.location.reload(), 2000);
                    } else if (data.status === 422) {
                        self.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to save details");
                    }
                })
                .catch((error) => {
                    toastr.error("Failed to save details");
                    $("#partner-save-btn").prop("disabled", false).text("Save");
                });
        },

        handleEditReinsurerFormSubmit: function (form) {
            const self = this;
            $("#partner-edit-btn")
                .prop("disabled", true)
                .html(
                    '<span class="me-2">Saving...</span><div class="loading"></div>'
                );

            let formData = new FormData(form);

            fetch(this.$el.editReinsurerForm.data("url"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams(formData),
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#partner-edit-btn").prop("disabled", false).text("Save");

                    if (data.status === 201) {
                        toastr.success(data.message);
                        setTimeout(() => window.location.reload(), 2000);
                    } else if (data.status === 422) {
                        self.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to save details");
                    }
                })
                .catch((error) => {
                    toastr.error("Failed to save details");
                    $("#partner-edit-btn").prop("disabled", false).text("Save");
                });
        },

        handleVerifyFormSubmit: function (form) {
            const self = this;
            $("#verify-save-btn")
                .prop("disabled", true)
                .html(
                    '<span class="me-2">Submitting...</span><div class="loading"></div>'
                );

            let formData = new FormData(form);

            fetch(this.$el.verifyForm.attr("action"), {
                method: "POST",
                headers: {
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#verify-save-btn")
                        .prop("disabled", false)
                        .text("Submit");

                    if (data.status === 201) {
                        toastr.success(
                            "Verification request Successfully sent"
                        );
                        setTimeout(() => window.location.reload(), 2000);
                    } else if (data.status === 422) {
                        self.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to send verification request");
                    }
                })
                .catch((error) => {
                    toastr.error("Failed to send verification request");
                    $("#verify-save-btn")
                        .prop("disabled", false)
                        .text("Submit");
                });
        },

        handleDebitFormSubmit: function (form) {
            const self = this;
            $("#debit-save-btn").prop("disabled", true).text("Generating...");

            let formData = new FormData(form);

            fetch(this.$el.debitForm.attr("action"), {
                method: "POST",
                headers: {
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#debit-save-btn")
                        .prop("disabled", false)
                        .text("Generate");

                    if (data.status === 201) {
                        toastr.success("Debit note generated successfully");
                        setTimeout(() => window.location.reload(), 2000);
                    } else if (data.status === 422) {
                        self.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to generate debit note");
                    }
                })
                .catch((error) => {
                    toastr.error("Failed to generate debit note");
                    $("#debit-save-btn")
                        .prop("disabled", false)
                        .text("Generate");
                });
        },

        handleInsuranceClassFormSubmit: function (form) {
            const self = this;
            $("#ins-class-save-btn").prop("disabled", true).text("Saving...");

            let formData = new FormData(form);

            fetch(this.$el.insuranceClassForm.attr("action"), {
                method: "POST",
                headers: {
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#ins-class-save-btn")
                        .prop("disabled", false)
                        .text("Submit");

                    if (data.status === 201) {
                        toastr.success("Class(es) saved Successfully");
                        setTimeout(() => window.location.reload(), 2000);
                    } else if (data.status === 422) {
                        self.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to save class(es)");
                    }
                })
                .catch((error) => {
                    toastr.error("Failed to save class(es)");
                    $("#ins-class-save-btn")
                        .prop("disabled", false)
                        .text("Submit");
                });
        },

        // ============================================================================
        // CALCULATION METHODS
        // ============================================================================

        computeCommissionAmt: function (counter) {
            const premium =
                parseFloat(
                    $(`#reinsurer-premium-${counter}`).val().replace(/,/g, "")
                ) || 0;
            const commRate =
                parseFloat($(`#reinsurer-comm_rate-${counter}`).val()) || 0;
            const commAmt = (premium * commRate) / 100;

            $(`#reinsurer-comm_amt-${counter}`).val(
                this.numberWithCommas(commAmt.toFixed(2))
            );

            this.calculateBrokerageCommRate();
        },

        computeCommissionRate: function (counter) {
            const premium =
                parseFloat(
                    $(`#reinsurer-premium-${counter}`).val().replace(/,/g, "")
                ) || 0;
            const commAmt =
                parseFloat(
                    $(`#reinsurer-comm_amt-${counter}`).val().replace(/,/g, "")
                ) || 0;
            const commRate = (commAmt / premium) * 100;

            $(`#reinsurer-comm_rate-${counter}`).val(commRate.toFixed(2));
        },

        computeFrontingAmount: function (counter) {
            const frontingRate =
                parseFloat($(`#reinsurer-fronting_rate-${counter}`).val()) || 0;
            const premium =
                parseFloat(
                    $(`#reinsurer-premium-${counter}`).val().replace(/,/g, "")
                ) || 0;
            const commAmt =
                parseFloat(
                    $(`#reinsurer-comm_amt-${counter}`).val().replace(/,/g, "")
                ) || 0;
            const frontingAmt = (frontingRate / 100) * (premium - commAmt);

            $(`#reinsurer-fronting_amt-${counter}`).val(
                this.numberWithCommas(frontingAmt.toFixed(2))
            );
        },

        computeEditReinsurerCommission: function () {
            const reinsurercommRate =
                parseFloat(
                    $("#edreinsurer-comm_rate").val().replace(/,/g, "")
                ) || 0;
            const premium =
                parseFloat($("#edreinsurer-premium").val().replace(/,/g, "")) ||
                0;
            const reinsurercommAmount = (reinsurercommRate / 100) * premium;

            $("#edreinsurer-comm_amt").val(
                this.numberWithCommas(reinsurercommAmount.toFixed(2))
            );
        },

        computeEditFrontingAmount: function () {
            const frontingRate = $("#edreinsurer-fronting_rate").val();
            const premium =
                parseFloat($("#edreinsurer-premium").val().replace(/,/g, "")) ||
                0;
            const reinsurerCommAmt =
                parseFloat(
                    $("#edreinsurer-comm_amt").val().replace(/,/g, "")
                ) || 0;
            const frontingAmt =
                (frontingRate / 100) * (premium - reinsurerCommAmt) || 0;

            $("#edreinsurer-fronting_amt").val(
                this.numberWithCommas(frontingAmt)
            );
        },

        calculateBrokerageCommRate: function () {
            const cedantCommRate =
                parseFloat($("#reinsurer-modal").data("cedant-comm-rate")) || 0;
            const reinCommRate =
                parseFloat(
                    $("#reinsurer-comm_rate-0").val().replace(/,/g, "")
                ) || 0;
            const premium =
                parseFloat($("#reinsurer-premium-0").val().replace(/,/g, "")) ||
                0;

            const brokerageCommRate = Math.max(
                0,
                reinCommRate - cedantCommRate
            );
            const brokerageCommRateAmnt = (brokerageCommRate / 100) * premium;

            $("#brokerage_comm_rate").val(
                this.numberWithCommas(brokerageCommRate.toFixed(2))
            );
            $("#brokerage_comm_rate_amnt").val(
                this.numberWithCommas(brokerageCommRateAmnt.toFixed(2))
            );
        },

        handleReinsurerShareInput: function ($input) {
            // Implementation for reinsurer share calculation
            const sharePercentage = parseFloat($input.val()) || 0;
            const counter = $input.data("counter");
            const treatyCounter = $input.data("treaty-counter");

            // Add your share calculation logic here
            // This is a simplified version - add full logic from original file

            this.computeCommissionAmt(counter);
        },

        handleEditReinsurerShareInput: function ($input) {
            // Implementation for edit reinsurer share calculation
            const sharePercentage = parseFloat($input.val()) || 0;

            // Add your share calculation logic here
        },

        // ============================================================================
        // HELPER METHODS
        // ============================================================================

        setActiveTab: function () {
            const hash = window.location.hash;
            this.$el.tabNav
                .attr("aria-selected", "false")
                .attr("tabindex", "-1")
                .removeClass("active");
            $(".reinsurers-tabpane-card .tab-pane")
                .removeClass("active")
                .removeClass("show");

            if (hash) {
                const $targetTab = $(
                    `.reinsurers-details-card .nav-link#nav-${hash.substring(
                        1
                    )}`
                );
                $targetTab
                    .attr("data-bs-target", hash)
                    .removeAttr("tabindex")
                    .addClass("active")
                    .attr("aria-selected", "true");

                $(
                    `.reinsurers-tabpane-card .tab-pane[aria-labelledby="nav-${hash.substring(
                        1
                    )}"]`
                )
                    .addClass("active")
                    .addClass("show");
            } else {
                window.history.pushState(
                    null,
                    null,
                    window.location.pathname +
                        window.location.search +
                        "#schedules-tab"
                );
            }
        },

        handleVerifyDetails: function () {
            const self = this;
            const data = {
                endorsement_no: this.state.coverData.endorsement_no,
            };

            fetch(this.$el.verifyDetailsBtn.data("pre-verify-url"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: JSON.stringify(data),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.length > 0) {
                        data.forEach((msg) => toastr.error(msg));
                    } else {
                        self.$el.verifyModal.modal("show");
                    }
                })
                .catch((error) => {
                    toastr.error("Failed to load pre-verification checks");
                });
        },

        handleGenerateSlip: function () {
            const data = {
                endorsement_no: this.state.coverData.endorsement_no,
            };

            fetch(this.$el.generateSlipBtn.data("pre-slip-url"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: JSON.stringify(data),
            })
                .then((response) => response.json())
                .then((r) => {
                    if (r.data.length > 0) {
                        r.data.forEach((msg) => toastr.error(msg));
                    } else {
                        const slipUrl =
                            this.$el.generateSlipBtn.data("slip-url");
                        window.open(slipUrl, "_blank");
                    }
                })
                .catch((error) => {
                    toastr.error("Failed to load pre-verification checks");
                });
        },

        handlePaymentMethodChange: function () {
            const payMethod = $("select#reins_pay_method option:selected").attr(
                "pay_method_desc"
            );

            if (payMethod === "I") {
                $("#no_of_installments_section").show();
                $("#add_reinsurer_btn_section").show();
            } else {
                $("#no_of_installments_section").hide();
                $("#add_reinsurer_btn_section").hide();
                $("#add_installments_box").hide();
            }
        },

        handleTreatyChange: function ($select) {
            const selectedTreaty = $select.val();
            const id = $select.attr("id");
            const counter = $select.data("counter");

            // Reset distributed share for selected treaty
            this.state.distributedShare = 0;
            this.state.origDistributedShare = 0;

            // Calculate distributed share from coverpartners for this treaty
            // Add your logic here

            this.appendReinsurers(counter, selectedTreaty);
        },

        appendReinsurers: function (treatyCounter, treaty) {
            const counter = 0; // Get actual counter
            const $select = $(`#reinsurer-${treatyCounter}-${counter}`);

            $select.empty();
            $select.append(
                $("<option>").text("-- Select Reinsurer--").attr("value", "")
            );

            // Add reinsurers logic here
            // Filter and append reinsurers based on treaty

            $select.trigger("change.select2");
        },

        addTreatyReinsurerSection: function () {
            const $lastSection = $("#treaty-div .treaty-div-section").last();
            const currCounter = parseInt($lastSection.attr("data-counter"));

            // Validate current treaty selection
            const currTreaty = $(`#reinsurer-treaty-${currCounter}`).val();
            if (!currTreaty || currTreaty === "" || currTreaty === " ") {
                toastr.error("Please Select Treaty", "Incomplete data");
                return false;
            }

            // Clone and update section
            const $newSection = $lastSection.clone();
            const counter = currCounter + 1;

            $newSection.find(".select2-container").remove();
            $newSection.find("input:not(.share_offered)").val("");
            $newSection.attr("data-counter", counter);
            $newSection.attr("id", `treaty-div-section-${counter}`);

            // Update IDs and counters
            this.updateSectionIds($newSection, counter);

            $lastSection.after($newSection);

            // Reinitialize Select2
            $("#treaty-div .form-select").select2({
                dropdownParent: this.$el.reinsurerModal,
            });
        },

        addReinsurerSection: function (treatyCounter) {
            const $lastSection = $(
                `#treaty-div #treaty-div-section-${treatyCounter} .reinsurer-section`
            ).last();
            const prevCounter = parseInt($lastSection.attr("data-counter"));

            // Validate current reinsurer selection
            const currReinsurer = $(
                `#reinsurer-${treatyCounter}-${prevCounter}`
            ).val();
            if (
                !currReinsurer ||
                currReinsurer === "" ||
                currReinsurer === " "
            ) {
                toastr.error("Please Select Reinsurer", "Incomplete data");
                return false;
            }

            // Clone and update section
            const $newSection = $lastSection.clone();
            const counter = prevCounter + 1;

            const $clonedSelect = $newSection.find(".reinsurer");
            $clonedSelect.select2("destroy");
            $clonedSelect.next(".select2-container").remove();
            $clonedSelect.removeAttr("data-select2-id");
            $clonedSelect.removeClass("select2-hidden-accessible");

            $newSection.attr("data-counter", counter);
            $newSection.attr("id", `reinsurer-div-${treatyCounter}-${counter}`);
            $newSection.find("input").val("");

            // Update IDs and names
            this.updateSectionIds($newSection, counter);

            $lastSection.after($newSection);

            // Reinitialize Select2
            $("#reinsurer-div .form-select").select2({
                dropdownParent: this.$el.reinsurerModal,
            });
        },

        addReinsurerInstallments: function () {
            const noOfInstallments =
                parseInt($("#no_of_installments").val()) || 0;

            if (noOfInstallments === 0) {
                toastr.error("Please enter number of installments");
                return;
            }

            $("#add_installments_box").show();
            $("#reinsurer_plan_section").empty();

            // Calculate installment amount
            const totalDr =
                parseFloat($("#reinsurer-premium-0").val().replace(/,/g, "")) ||
                0;
            // Add full calculation logic here

            const totalFacInstAmt = (totalDr / noOfInstallments).toFixed(2);
            this.state.installmentTotalAmount = totalDr;

            // Generate installment rows
            for (let i = 1; i <= noOfInstallments; i++) {
                const row = this.createInstallmentRow(i, totalFacInstAmt);
                $("#reinsurer_plan_section").append(row);
            }
        },

        createInstallmentRow: function (installmentNo, amount) {
            return `
                <div class="row reinsurer-instalament-row" data-count="${installmentNo}">
                    <div class="col-md-3">
                        <label class="">Installment</label>
                        <input type="hidden" name="installment_no[]" value="${installmentNo}" readonly class="form-control"/>
                        <input type="text" value="Installment No. ${installmentNo}" id="instl_no_${installmentNo}" readonly class="form-control" required/>
                    </div>
                    <div class="col-md-3">
                        <label for="instl_date_${installmentNo}">Installment Due Date</label>
                        <input type="date" name="installment_date[]" id="instl_date_${installmentNo}" class="form-control" required/>
                    </div>
                    <div class="col-md-3">
                        <label for="instl_amnt_${installmentNo}">Total Installment Amount</label>
                        <div class="input-group mb-3">
                            <input type="text" name="installment_amt[]" id="instl_amnt_${installmentNo}"
                                value="${this.numberWithCommas(
                                    amount
                                )}" class="form-control amount"
                                onkeyup="this.value=numberWithCommas(this.value)" required/>
                            <button class="btn btn-danger btn-sm" type="button" id="remove_reinsurer_instalment">
                                <i class="bx bx-minus align-middle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        },

        removeReinsurerInstallment: function ($button) {
            const currentInstallments = parseInt(
                $("#no_of_installments").val()
            );
            const remainingInstallment =
                currentInstallments >= 1 ? currentInstallments - 1 : 0;

            if (remainingInstallment > 0) {
                $("#no_of_installments").val(remainingInstallment);
            } else {
                $("#no_of_installments").val("");
                $("#reinsurer_plan_section").empty();
                $("#add_installments_box").hide();
            }

            $("#no_of_installments").trigger("change");
            $button.closest(".reinsurer-instalament-row").remove();
        },

        updateSectionIds: function ($section, counter) {
            $section.find("[id]").each(function () {
                const id = $(this).attr("id");
                $(this).attr(
                    "id",
                    id.replace(/(-\d)(-\d)?$/, function (match, p1, p2) {
                        return p2 ? `-${counter}-${counter}` : `-${counter}`;
                    })
                );
                $(this).attr("data-counter", counter);
                $(this).attr("data-treaty-counter", counter);
            });
        },

        populateScheduleForm: function (data) {
            this.$el.schedulesForm.find("#title").val("");
            this.$el.schedulesForm.find("#id").val("");
            this.$el.schedulesForm.find("#schedule_id").val("");
            this.$el.schedulesForm.find("#schedule_position").val("");
            this.$el.scheduleDescription.html("");

            this.$el.schedulesForm.find("#title").val(data.title);
            this.$el.schedulesForm.find("#id").val(data.id);
            this.$el.schedulesForm.find("#schedule_id").val(data.schedule_id);
            this.$el.schedulesForm
                .find("#schedule_position")
                .val(data.schedule_id);
            this.$el.schedulesForm.find('[name="_method"]').val("PUT");
            this.$el.scheduleDescription.html(data.details);
            this.$el.schedulesForm
                .find("#sched-header")
                .val(data.schedule_id)
                .trigger("change");
        },

        populateAttachmentForm: function (data) {
            this.$el.attachmentsForm.find("#attachments_id").val(data.id);
            this.$el.attachmentsForm
                .find("#title")
                .val(data.title)
                .trigger("change");
            this.$el.attachmentsForm.find('[name="_method"]').val("PUT");
        },

        populateEditReinsurerForm: function (
            data,
            reinsurer,
            distributedShare
        ) {
            this.state.distributedShare = distributedShare;
            this.state.origDistributedShare = distributedShare;

            const remShare =
                this.$el.app.data("share-offered") - distributedShare;
            const share = parseFloat(data.share).toFixed(2);
            const writtenLines = parseFloat(data.written_lines).toFixed(2);

            let applyFronting = "N";
            if (parseFloat(data.fronting_rate) > 0) {
                applyFronting = "Y";
            }

            $("#edtran_no").val(data.tran_no);
            $("#eddistributed_share").val(distributedShare);
            $("#edrem_share").val(remShare);
            $("#edreinsurer-share").val(share);
            $("#edreinsurer-written-share").val(writtenLines);
            $("#edreinsurer-orig-share").val(share);
            $("#edreinsurer-wht_rate")
                .val(parseFloat(data.wht_rate).toFixed(2))
                .trigger("change");
            $("#edreinsurer-apply_fronting").val(applyFronting);
            $("#edreinsurer-fronting_rate").val(data.fronting_rate);
            $("#edreinsurer-fronting_amt").val(data.fronting_amt);
            $("#edreinsurer-brokerage_comm_amt").val(data.brokerage_comm_amt);
            $("#edreinsurer").val(reinsurer.customer_id);
            $("#edreinsurer-sum_insured").val(
                this.numberWithCommas(parseFloat(data.sum_insured).toFixed(2))
            );
            $("#edreinsurer-premium").val(parseFloat(data.premium).toFixed(2));
            $("#edreinsurer-comm_rate").val(
                this.numberWithCommas(parseFloat(data.comm_rate).toFixed(2))
            );
            $("#edreinsurer-comm_amt").val(
                parseFloat(data.commission).toFixed(2)
            );
        },

        showAttachmentPreview: function (base64Data, mimeType) {
            let element;

            if (mimeType.startsWith("image/")) {
                element = `<img src="data:${mimeType};base64,${base64Data}" width="100%" alt="Document Image"/>`;
            } else if (mimeType === "application/pdf") {
                element = `<iframe src="data:${mimeType};base64,${base64Data}" width="100%" height="800"></iframe>`;
            } else if (mimeType.startsWith("text/")) {
                element = `<iframe src="data:${mimeType};base64,${base64Data}" width="100%" height="800"></iframe>`;
            } else {
                element = `<a href="data:${mimeType};base64,${base64Data}" download="document" style="color:blue;text-decoration:underline;width: 100%;">Download Document</a>`;
            }

            $("#attachment-document-modal #preview-container").html(element);
            $("#attachment-document-modal").modal("show");
        },

        confirmRemoveSchedule: function (dataId, dataName) {
            const self = this;

            Swal.fire({
                title: "Remove Schedule Item",
                text: `This action will remove the Item ${dataName} from this cover`,
                showCancelButton: true,
                confirmButtonText: "Remove",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    self.removeSchedule(dataId);
                }
            });
        },

        removeSchedule: function (dataId) {
            const data = {
                cover_no: this.state.coverData.cover_no,
                endorsement_no: this.state.coverData.endorsement_no,
                id: dataId,
            };

            this.fetchWithCsrf($("#schedules-table").data("delete-url"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === 201) {
                        toastr.success("Action was successful", "Successful");
                        setTimeout(() => location.reload(), 2000);
                    } else if (data.status === 422) {
                        this.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to remove item");
                    }
                })
                .catch((error) => {
                    toastr.error("An internal error occurred");
                });
        },

        confirmRemoveAttachment: function (data) {
            const self = this;

            Swal.fire({
                title: "Remove Attachment",
                text: `This action will remove ${data.title} from this cover`,
                showCancelButton: true,
                confirmButtonText: "Remove",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    self.removeAttachment(data.id);
                }
            });
        },

        removeAttachment: function (id) {
            const data = {
                cover_no: this.state.coverData.cover_no,
                endorsement_no: this.state.coverData.endorsement_no,
                id: id,
            };

            this.fetchWithCsrf($("#attachments-table").data("delete-url"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === 201) {
                        toastr.success("Action was successful", "Successful");
                        setTimeout(() => location.reload(), 2000);
                    } else if (data.status === 422) {
                        this.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to remove attachment");
                    }
                })
                .catch((error) => {
                    toastr.error("An internal error occurred");
                });
        },

        confirmRemoveClause: function (data) {
            const self = this;

            Swal.fire({
                title: "Remove Clause",
                text: `This action will remove ${data.title} from this cover`,
                showCancelButton: true,
                confirmButtonText: "Remove",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    self.removeClause(data.id);
                }
            });
        },

        removeClause: function (clauseId) {
            const data = {
                cover_no: this.state.coverData.cover_no,
                endorsement_no: this.state.coverData.endorsement_no,
                clause_id: clauseId,
            };

            this.fetchWithCsrf($("#clauses-table").data("delete-url"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === 201) {
                        toastr.success("Action was successful", "Successful");
                        setTimeout(() => location.reload(), 2000);
                    } else if (data.status === 422) {
                        this.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to remove clause");
                    }
                })
                .catch((error) => {
                    toastr.error("An internal error occurred");
                });
        },

        confirmRemoveReinsurer: function (shareData, reinsurer) {
            const self = this;

            Swal.fire({
                title: "Remove Reinsurer",
                text: `This action will remove the reinsurer ${reinsurer.name} from this cover and their share`,
                showCancelButton: true,
                confirmButtonText: "Remove",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    self.removeReinsurer(shareData);
                }
            });
        },

        removeReinsurer: function (shareData) {
            const data = {
                endorsement_no: this.state.coverData.endorsement_no,
                tran_no: shareData.tran_no,
                reinsurer: shareData.partner_no,
            };

            this.fetchWithCsrf($("#reinsurers-table").data("delete-url"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === 201) {
                        toastr.success("Action was successful", "Successful");
                        setTimeout(() => location.reload(), 2000);
                    } else if (data.status === 422) {
                        this.showValidationErrors(data.errors);
                    } else {
                        toastr.error("Failed to remove reinsurer");
                    }
                })
                .catch((error) => {
                    toastr.error("An internal error occurred");
                });
        },

        openReinsurerEmailModal: function ($button) {
            this.state.lastReinData.tranNo = $button.data("tran_no");
            this.state.lastReinData.debitUrl = $button.data("debit_url");
            this.state.lastReinData.claimNoticeUrl =
                $button.data("claim_notice_url");

            // Show email modal
            this.$el.sendEmailModal.modal("show");
        },

        openCedantEmailModal: function ($button) {
            const endorsementNo = $button.data("endorsement_no");
            const coverNo = $button.data("cover_no");
            const emails = $button.data("client_emails");

            // Populate email form
            // Show email modal
            this.$el.sendEmailModal.modal("show");
        },

        showValidationErrors: function (errors) {
            if (typeof errors === "object") {
                Object.keys(errors).forEach((key) => {
                    if (Array.isArray(errors[key])) {
                        errors[key].forEach((error) => {
                            toastr.error(error);
                        });
                    } else {
                        toastr.error(errors[key]);
                    }
                });
            } else {
                toastr.error("Validation error occurred");
            }
        },

        fetchWithCsrf: function (url, options = {}) {
            options.headers = options.headers || {};
            options.headers["X-CSRF-Token"] = $('meta[name="csrf-token"]').attr(
                "content"
            );
            return fetch(url, options);
        },

        numberWithCommas: function (x) {
            if (!x) return "0.00";
            const parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        },

        removeCommas: function (str) {
            return str.toString().replace(/,/g, "");
        },

        toDecimal: function (number) {
            return parseFloat(Number(number).toFixed(2));
        },

        areDecimalsEqual: function (num1, num2, tolerance = 0.1) {
            return (
                Math.abs(this.toDecimal(num1) - this.toDecimal(num2)) <=
                tolerance
            );
        },
    };

    $(document).ready(function () {
        if ($("#coverDetailsApp").length) {
            CoverDetails.init();
        }
    });
})(jQuery);
