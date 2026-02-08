/**
 * Cover Endorsement Configuration
 * @pk305
 */

(function (window) {
    "use strict";

    const ENDORSEMENT_FIELD_CONFIG = {
        "change-brokerage-rate": {
            show: [
                "brokerage-comm-type",
                "brokerage-comm-rate",
                "brokerage-comm-amt",
            ],
            hide: [
                "start-date",
                "ppw-days",
                "extension-days",
                "premium-due-date",
                "new-premium-due-date",
                "cover-from",
                "cover-to",
                "insured-name",
                "new-insured-name",
                "current-cede-premium",
                "current-rein-premium",
                "current-share-offered",
                "current-sum-insured",
                "new-sum-insured",
                "apply-eml",
                "eml-rate",
                "eml-amount",
                "effective-sum-insured",
                "new-effective-sum-insured",
                "new-cover-from",
                "new-cover-to",
                "new-rein-premium",
                "endorsed-sum-insured",
                "endorsed-cede-premium",
                "change-type",
                "new-share-offered",
                "new-cede-premium",
            ],
            showCurrentSection: true,
        },

        "change-due-date": {
            show: [
                "start-date",
                "ppw-days",
                "extension-days",
                "premium-due-date",
                "new-premium-due-date",
            ],
            hide: [
                "cover-from",
                "cover-to",
                "brokerage-comm-type",
                "brokerage-comm-rate",
                "brokerage-comm-amt",
                "insured-name",
                "new-insured-name",
                "new-cede-premium",
                "current-cede-premium",
                "current-rein-premium",
                "current-share-offered",
                "new-share-offered",
                "new-rein-premium",
                "current-sum-insured",
                "new-sum-insured",
                "apply-eml",
                "eml-rate",
                "eml-amount",
                "effective-sum-insured",
                "new-effective-sum-insured",
                "new-cover-from",
                "new-cover-to",
                "endorsed-sum-insured",
                "endorsed-cede-premium",
                "change-type",
            ],
            showCurrentSection: true,
        },

        "change-premium": {
            show: [
                "current-sum-insured",
                "new-sum-insured",
                "apply-eml",
                "eml-rate",
                "eml-amount",
                "effective-sum-insured",
                "new-effective-sum-insured",
                "current-cede-premium",
                "new-cede-premium",
                "endorsed-sum-insured",
                "endorsed-cede-premium",
                "current-share-offered",
                "current-rein-premium",
                "change-type",
                "new-share-offered",
            ],
            hide: [
                "start-date",
                "ppw-days",
                "extension-days",
                "premium-due-date",
                "new-premium-due-date",
                "insured-name",
                "new-insured-name",
                "cover-from",
                "cover-to",
                "brokerage-comm-type",
                "brokerage-comm-rate",
                "brokerage-comm-amt",
                "new-cover-from",
                "new-cover-to",
                "new-rein-premium",
            ],
            showCurrentSection: true,
        },

        "change-inception-date": {
            show: ["cover-from", "cover-to", "new-cover-from", "new-cover-to"],
            hide: [
                "start-date",
                "ppw-days",
                "extension-days",
                "premium-due-date",
                "new-premium-due-date",
                "brokerage-comm-type",
                "brokerage-comm-rate",
                "brokerage-comm-amt",
                "insured-name",
                "new-insured-name",
                "current-share-offered",
                "current-sum-insured",
                "new-sum-insured",
                "apply-eml",
                "eml-rate",
                "eml-amount",
                "effective-sum-insured",
                "new-effective-sum-insured",
                "current-rein-premium",
                "new-rein-premium",
                "new-share-offered",
                "endorsed-sum-insured",
                "endorsed-cede-premium",
                "change-type",
                "current-cede-premium",
                "new-cede-premium",
            ],
            showCurrentSection: true,
        },

        "change-insured": {
            show: ["insured-name", "new-insured-name"],
            hide: [
                "start-date",
                "ppw-days",
                "extension-days",
                "premium-due-date",
                "new-premium-due-date",
                "cover-from",
                "cover-to",
                "brokerage-comm-type",
                "brokerage-comm-rate",
                "brokerage-comm-amt",
                "current-rein-premium",
                "current-share-offered",
                "new-share-offered",
                "new-rein-premium",
                "current-sum-insured",
                "apply-eml",
                "eml-rate",
                "eml-amount",
                "effective-sum-insured",
                "new-effective-sum-insured",
                "new-cover-from",
                "new-cover-to",
                "endorsed-sum-insured",
                "endorsed-cede-premium",
                "change-type",
                "new-sum-insured",
                "current-cede-premium",
                "new-cede-premium",
            ],
            showCurrentSection: true,
        },

        "cancel-policy": {
            show: [],
            hide: [
                "new-cede-premium",
                "current-cede-premium",
                "current-rein-premium",
                "current-share-offered",
                "new-share-offered",
                "new-rein-premium",
                "start-date",
                "ppw-days",
                "extension-days",
                "premium-due-date",
                "new-premium-due-date",
                "insured-name",
                "new-insured-name",
                "cover-from",
                "cover-to",
                "brokerage-comm-type",
                "brokerage-comm-rate",
                "brokerage-comm-amt",
                "new-sum-insured",
                "apply-eml",
                "eml-rate",
                "eml-amount",
                "effective-sum-insured",
                "new-effective-sum-insured",
                "new-cover-from",
                "new-cover-to",
                "endorsed-sum-insured",
                "endorsed-cede-premium",
                "change-type",
                "current-sum-insured",
            ],
            showCurrentSection: false,
        },

        "change-sum-insured": {
            show: [
                "current-sum-insured",
                "new-sum-insured",
                "apply-eml",
                "eml-rate",
                "eml-amount",
                "effective-sum-insured",
                "new-effective-sum-insured",
                "current-cede-premium",
                "new-cede-premium",
                "endorsed-sum-insured",
                "endorsed-cede-premium",
                "current-share-offered",
                "current-rein-premium",
                "change-type",
                "new-share-offered",
            ],
            hide: [
                "start-date",
                "ppw-days",
                "extension-days",
                "premium-due-date",
                "new-premium-due-date",
                "insured-name",
                "new-insured-name",
                "cover-from",
                "cover-to",
                "brokerage-comm-type",
                "brokerage-comm-rate",
                "brokerage-comm-amt",
                "new-cover-from",
                "new-cover-to",
                "new-rein-premium",
            ],
            showCurrentSection: true,
        },

        "refund-endorsement": {
            show: [
                "current-sum-insured",
                "new-sum-insured",
                "apply-eml",
                "eml-rate",
                "eml-amount",
                "effective-sum-insured",
                "new-effective-sum-insured",
                "current-cede-premium",
                "new-cede-premium",
                "endorsed-sum-insured",
                "endorsed-cede-premium",
                "current-share-offered",
                "current-rein-premium",
                "new-share-offered",
            ],
            hide: [
                "change-type",
                "start-date",
                "ppw-days",
                "extension-days",
                "premium-due-date",
                "new-premium-due-date",
                "insured-name",
                "new-insured-name",
                "cover-from",
                "cover-to",
                "brokerage-comm-type",
                "brokerage-comm-rate",
                "brokerage-comm-amt",
                "new-cover-from",
                "new-cover-to",
                "new-rein-premium",
            ],
            showCurrentSection: true,
        },
    };

    const CoverEndorsement = {
        config: null,
        elements: {},
        state: {
            currentSumInsured: 0,
            currentPremium: 0,
            currentReinsurerPremium: 0,
        },

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
        },

        cacheElements: function () {
            this.elements = {
                endorsementTable: $("#endorsement-list-table"),
                endorseModal: $("#endorse-cover-modal"),
                endorseForm: $("#cover-endorsement-form"),
                coverActionForm: $("#cover-action-form"),
                renewalNoticeForm: $("#renewal-notice-form"),
                endorseType: $("#endorse-type"),
                currentSection: $("#current-values-section"),

                endorsedSumInsured: $("#endorsed-total-sum-insured"),
                endorsedPremium: $("#endorsed-cede-premium"),
                newSumInsured: $("#new-total-sum-insured"),
                newPremium: $("#new-cede-premium"),
                newEffectiveSumInsured: $("#new-effective-sum-insured"),
                changeType: $("#change-in-sum-insured-type"),
                applyEml: $("#apply-eml"),
                emlRate: $("#eml-rate"),
                emlAmt: $("#eml-amt"),

                premiumDueDate: $("#premium-due-date"),
                newPremiumDueDate: $("#new-premium-due-date"),
                extensionDays: $("#extension-days"),
                newCoverFrom: $("#new-cover-from"),
                newCoverTo: $("#new-cover-to"),

                brokerageCommType: $("#brokerage-comm-type"),
                brokerageCommRate: $("#brokerage-comm-rate"),
                brokerageCommAmt: $("#brokerage-comm-amt"),

                endorseNarration: $("#endorse-narration"),
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
            ];

            modals.forEach(function (modalId) {
                $(modalId).on("shown.bs.modal", function () {
                    $(".select2", this).select2({
                        dropdownParent: $(this),
                    });
                });
            });
        },

        getFieldConfig: function () {
            return ENDORSEMENT_FIELD_CONFIG;
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
    };

    window.CoverEndorsement = CoverEndorsement;
})(window);
