// @pk305
(function (window) {
    "use strict";

    const ENDORSEMENT_FIELD_CONFIG = {
        "change-brokerage-rate": {
            show: [
                "brokerage_comm_type",
                "brokerage_comm_rate",
                "brokerage_comm_amt",
            ],
            showCurrentSection: true,
        },

        "change-due-date": {
            show: [
                "start_date",
                "ppw_days",
                "extension_days",
                "premium_due_date",
                "new_premium_due_date",
            ],
            showCurrentSection: true,
        },

        "change-premium": {
            show: [
                "change_in_sum_insured_type",
                "current_total_sum_insured",
                "new_total_sum_insured",
                "apply_eml",
                "eml_rate",
                "eml_amt",
                "effective_sum_insured",
                "new_effective_sum_insured",
                "current_cede_premium",
                "new_cede_premium",
                "endorsed_total_sum_insured",
                "endorsed_cede_premium",
                "current_fac_share_offered",
                "current_rein_premium",
                "new_fac_share_offered",
            ],
            showCurrentSection: true,
        },

        "change-inception-date": {
            show: ["coverfrom", "coverto", "new_coverfrom", "new_coverto"],
            showCurrentSection: true,
        },

        "change-insured": {
            show: ["insured_name", "new_insured_name"],
            showCurrentSection: true,
        },

        "cancel-policy": {
            show: [],
            showCurrentSection: false,
        },

        "change-sum-insured": {
            show: [
                "change_in_sum_insured_type",
                "current_total_sum_insured",
                "new_total_sum_insured",
                "apply_eml",
                "eml_rate",
                "eml_amt",
                "effective_sum_insured",
                "new_effective_sum_insured",
                "current_cede_premium",
                "new_cede_premium",
                "endorsed_total_sum_insured",
                "endorsed_cede_premium",
                "current_fac_share_offered",
                "current_rein_premium",
                "new_fac_share_offered",
            ],
            showCurrentSection: true,
        },

        "refund-endorsement": {
            show: [
                "current_total_sum_insured",
                "new_total_sum_insured",
                "apply_eml",
                "eml_rate",
                "eml_amt",
                "effective_sum_insured",
                "new_effective_sum_insured",
                "current_cede_premium",
                "new_cede_premium",
                "endorsed_total_sum_insured",
                "endorsed_cede_premium",
                "current_fac_share_offered",
                "current_rein_premium",
                "new_fac_share_offered",
            ],
            showCurrentSection: true,
        },
    };

    const ALL_FIELD_NAMES = [
        "change_in_sum_insured_type",
        "current_total_sum_insured",
        "effective_sum_insured",
        "current_fac_share_offered",
        "current_cede_premium",
        "current_rein_premium",
        "start_date",
        "ppw_days",
        "premium_due_date",
        "coverfrom",
        "coverto",
        "insured_name",
        "new_insured_name",
        "endorsed_total_sum_insured",
        "endorsed_cede_premium",
        "new_fac_share_offered",
        "apply_eml",
        "eml_rate",
        "eml_amt",
        "new_total_sum_insured",
        "new_cede_premium",
        "new_effective_sum_insured",
        "new_rein_premium",
        "brokerage_comm_type",
        "brokerage_comm_rate",
        "brokerage_comm_amt",
        "new_coverfrom",
        "new_coverto",
        "extension_days",
        "new_premium_due_date",
    ];

    const CoverEndorsement = {
        config: null,
        elements: {},
        state: {
            currentSumInsured: 0,
            currentPremium: 0,
            currentReinsurerPremium: 0,
        },
        dataTable: null,
        init: function (serverConfig) {
            this.config = serverConfig;
            this.state.currentSumInsured =
                parseFloat(serverConfig.latestEndorsement.total_sum_insured) ||
                0;
            this.state.currentPremium =
                parseFloat(serverConfig.latestEndorsement.cedant_premium) || 0;
            this.state.currentReinsurerPremium =
                parseFloat(serverConfig.latestEndorsement.rein_premium) || 0;

            this.cacheElements();
            this.initDataTable();
            this.bindEvents();
            this.initSelect2Modals();
            this.initFormValidation();
            this.initPortfolioHandlers();
            this.initQuarterlyFiguresHandlers();
            this.initMDPHandlers();
        },
        cacheElements: function () {
            this.elements = {
                endorsementTable: $("#endorsement-list-table"),
                endorseModal: $("#endorse-cover-modal"),
                quarterlyFiguresModal: $("#quarterly-figures-modal"),
                profitCommissionModal: $("#profit-commission-modal"),
                portfolioModal: $("#portfolio-modal"),
                mdpInstallmentModal: $("#mdpInstallmentModal"),

                endorseForm: $("#coverEndorsementForm"),
                coverActionForm: $("#new_cover_form"),
                renewalNoticeForm: $("#new_renewal_notice"),
                quarterlyFiguresForm: $("#QuarterlyFiguresForm"),
                profitCommissionForm: $("#ProfitCommissionForm"),
                portfolioForm: $("#PortfolioForm"),
                mdpInstallmentForm: $("#mdpInstallmentForm"),

                endorseType: $("#endorse_type"),
                currentSection: $("#current_section_div"),
                endorsedSection: $("#endorsed_section_div"),

                endorsedSumInsured: $("#endorsed_total_sum_insured"),
                endorsedPremium: $("#endorsed_cede_premium"),
                newSumInsured: $("#new_total_sum_insured"),
                newPremium: $("#new_cede_premium"),
                newEffectiveSumInsured: $("#new_effective_sum_insured"),
                changeType: $("#change_in_sum_insured_type"),
                currentSumInsured: $("#current_total_sum_insured"),
                effectiveSumInsured: $("#effective_sum_insured"),

                applyEml: $("#apply_eml"),
                emlRate: $("#eml_rate"),
                emlAmt: $("#eml_amt"),

                premiumDueDate: $("#premium_due_date"),
                newPremiumDueDate: $("#new_premium_due_date"),
                extensionDays: $("#extension_days"),
                newCoverFrom: $("#new_coverfrom"),
                newCoverTo: $("#new_coverto"),
                coverFrom: $("#coverfrom"),
                coverTo: $("#coverto"),
                startDate: $("#start_date"),

                brokerageCommType: $("#brokerage_comm_type"),
                brokerageCommRate: $("#brokerage_comm_rate"),
                brokerageCommAmt: $("#brokerage_comm_amt"),

                endorseNarration: $("#endorse_narration"),

                portfolioYear: $("#portfolio_year"),
                origEndorsement: $("#orig_endorsement"),
                portReinsurer: $("#port_reinsurer"),
                portfolioAddReinsurer: $("#portfolio_add_reinsurer"),
                portShare: $("#port_share"),
                portAmt: $("#port_amt"),
                portPrmRate: $("#port_prm_rate"),
                portLossRate: $("#port_loss_rate"),
                portfolioType: $("#portfolio_type"),

                treatyYear: $("#treaty_year"),
                profitCommissionDiv: $("#ProfitCommissionDiv"),

                mdpInstallment: $("#mdp-installment"),
                mdpInstallmentsSection: $("#mdp-installments-section"),
            };
        },

        initDataTable: function () {
            const self = this;

            this.dataTable = this.elements.endorsementTable.DataTable({
                columnDefs: [{ targets: 0, orderable: false }],
                order: [[1, "desc"]],
                processing: true,
                serverSide: true,
                autoWidth: false,
                lengthChange: false,
                language: { processing: "Processing..." },
                ajax: {
                    url: self.config.routes.endorseDatatable,
                    data: function (d) {
                        d.customer_id = self.config.customerId;
                        d.cover_no = self.config.coverNo;
                    },
                },
                columns: [
                    {
                        data: "id_no",
                        searchable: false,
                        className: "highlight-idx",
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        },
                    },
                    { data: "endorsement_no", searchable: true },
                    { data: "transaction_type", searchable: true },
                    { data: "cover_from", searchable: false },
                    { data: "cover_to", searchable: false },
                    {
                        data: "status_verification",
                        searchable: false,
                        sortable: false,
                    },
                    { data: "actions", searchable: false, sortable: false },
                ],
            });
        },
        initSelect2Modals: function () {
            const modals = [
                "#quarterly-figures-modal",
                "#profit-commission-modal",
                "#portfolio-modal",
                "#endorse-cover-modal",
                "#mdpInstallmentModal",
            ];

            modals.forEach(function (modalId) {
                $(modalId).on("shown.bs.modal", function () {
                    $(".select2.form-inputs, .select2", this).select2({
                        dropdownParent: $(this),
                    });
                });
            });
        },

        getFieldConfig: function () {
            return ENDORSEMENT_FIELD_CONFIG;
        },

        getAllFieldNames: function () {
            return ALL_FIELD_NAMES;
        },
    };

    window.CoverUtils = {
        numberWithCommas: function (x) {
            if (x === null || x === undefined || isNaN(x)) return "0.00";
            return parseFloat(x)
                .toFixed(2)
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        removeCommas: function (str) {
            if (str === null || str === undefined) return "0";
            return str.toString().replace(/,/g, "");
        },

        parseAmount: function (value) {
            return parseFloat(this.removeCommas(value)) || 0;
        },

        daysBetween: function (startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffMs = end - start;
            return Math.max(0, Math.ceil(diffMs / (1000 * 60 * 60 * 24)));
        },

        addDays: function (dateStr, days) {
            const date = new Date(dateStr);
            date.setDate(date.getDate() + parseInt(days));
            return date.toISOString().split("T")[0];
        },

        ajax: function (url, options) {
            const defaults = {
                headers: {
                    "X-CSRF-TOKEN": window.CoverEndorsement.config.csrfToken,
                    "Content-Type": "application/json",
                },
            };
            return $.ajax(url, $.extend(true, defaults, options));
        },

        fetchWithCsrf: function (url, options) {
            const defaults = {
                headers: {
                    "X-CSRF-TOKEN": window.CoverEndorsement.config.csrfToken,
                    "Content-Type": "application/json",
                },
            };
            return fetch(url, Object.assign({}, defaults, options));
        },
    };

    window.CoverEndorsement = CoverEndorsement;
})(window);
