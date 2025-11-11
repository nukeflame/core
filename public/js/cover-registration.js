/**
 * Cover Registration Module
 * Handles all functionality for cover registration form
 */

const CoverRegistration = (function () {
    "use strict";

    // Private variables
    const config = {
        trans_type: "",
        prospectId: "",
        routes: {},
        oldData: null,
        resetableTransTypes: ["NEW"],
        installmentTotalAmount: 0,
    };

    // Cache DOM elements
    const elements = {};

    /**
     * Initialize the module
     */
    function init() {
        // Load configuration from window object
        if (typeof window.coverConfig !== "undefined") {
            Object.assign(config, window.coverConfig);
        }

        // Cache DOM elements
        cacheElements();

        // Setup event listeners
        setupEventListeners();

        // Initialize plugins
        initializePlugins();

        // Setup form validation
        setupValidation();

        // Initialize fields based on transaction type
        initializeFields();

        // Hide loader
        hideLoader();

        // Log initialization
        console.log("Cover Registration initialized:", config.trans_type);
    }

    /**
     * Cache frequently used DOM elements
     */
    function cacheElements() {
        elements.form = $("#register_cover");
        elements.typeOfBus = $("#type_of_bus");
        elements.coverType = $("#covertype");
        elements.binderSection = $("#binder_section");
        elements.brokerFlag = $("#broker_flag");
        elements.brokerSection = $("#broker_section");
        elements.payMethod = $("#pay_method");
        elements.currencyCode = $("#currency_code");
        elements.todayCurrency = $("#today_currency");
        elements.applyEml = $("#apply_eml");
        elements.facSection = $("#fac_section");
        elements.treatySection = $("#treaty_section");
        elements.installmentSection = $("#installment_section");
        elements.pageLoader = $("#page-loader");
        elements.classGroup = $("#class_group");
        elements.classCode = $("#classcode");
        elements.prospectId = $("#prospect_id");
    }

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Business type change
        elements.typeOfBus.on("change", handleBusinessTypeChange);

        // Cover type change
        elements.coverType.on("change", handleCoverTypeChange);

        // Broker flag change
        elements.brokerFlag.on("change", handleBrokerFlagChange);

        // Payment method change
        elements.payMethod.on("change", handlePaymentMethodChange);

        // Currency change
        elements.currencyCode.on("change", handleCurrencyChange);

        // Apply EML change
        elements.applyEml.on("change", handleEmlChange);

        // Class group change
        elements.classGroup.on("change", handleClassGroupChange);

        // Form submission
        $("#save_cover").on("click", handleFormSubmit);

        // Date validation
        $("#coverfrom, #coverto").on("change", validateDates);

        // Installment events
        $("#add_fac_instalments").on("click", addInstallments);
        $(document).on("click", ".remove-installment", removeInstallment);

        // Commission calculations
        $("#comm_rate").on("keyup", calculateCedantCommission);
        $("#reins_comm_rate").on("keyup", calculateReinsurerCommission);
        $("#cede_premium").on("keyup", handleCedantPremiumChange);
        $("#reins_comm_type").on("change", handleReinsurerCommTypeChange);
        $("#brokerage_comm_type").on("change", handleBrokerageCommTypeChange);

        // EML calculations
        $("#eml_rate, #total_sum_insured").on("keyup", calculateEml);

        // Treaty events
        $(document).on("change", ".treaty_reinclass", handleReinclassChange);
        $("#add_rein_class").on("click", addReinClass);
        $(document).on("click", ".add-comm-section", addCommissionSection);
        $(document).on(
            "click",
            ".remove-comm-section",
            removeCommissionSection
        );

        // Layer events
        $("#add-layer-section").on("click", addLayer);
        $(document).on("click", ".remove-layer-section", removeLayer);
        $(document).on(
            "change",
            ".limit_per_reinclass",
            handleLimitPerReinclassChange
        );

        // Treaty type change
        $("#treatytype").on("change", handleTreatyTypeChange);

        // Method change (for non-proportional)
        $("#method").on("change", handleMethodChange);

        // Prospect ID change
        elements.prospectId.on("change", function () {
            const prospectId = $(this).val();
            if (prospectId.length >= 3) {
                loadProspectData(prospectId);
            }
        });

        // Add insured modal
        $("#addInsuredData").on("click", function () {
            $("#addInsuredDataModal").modal("show");
        });

        // Risk details paste handling
        $("#risk_details_content").on("paste", handleRiskDetailsPaste);

        // Amount formatting
        $(document).on("keyup", ".amount", function () {
            const value = $(this).val().replace(/,/g, "");
            if (!isNaN(value) && value !== "") {
                $(this).val(numberWithCommas(value));
            }
        });

        // Quota/Surplus calculations
        $(document).on("keyup", ".retention_per", calculateQuotaRetention);
        $(document).on(
            "keyup",
            ".quota_share_total_limit",
            calculateQuotaRetention
        );
        $(document).on("keyup", ".no_of_lines", calculateSurplusLimit);
    }

    /**
     * Initialize jQuery plugins
     */
    function initializePlugins() {
        // Initialize Select2
        if ($.fn.select2) {
            $(".select2").select2({
                width: "100%",
                theme: "bootstrap-5",
                placeholder: function () {
                    return $(this).data("placeholder") || "Select an option";
                },
                allowClear: true,
            });
        }

        // Initialize tooltips
        if ($.fn.tooltip) {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }

        // Initialize popovers
        if ($.fn.popover) {
            $('[data-bs-toggle="popover"]').popover();
        }
    }

    /**
     * Setup form validation
     */
    function setupValidation() {
        if (!$.fn.validate) return;

        elements.form.validate({
            ignore: ":hidden",
            errorClass: "is-invalid",
            validClass: "is-valid",
            errorElement: "div",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                if (element.parent(".input-group").length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            rules: {
                type_of_bus: { required: true },
                covertype: { required: true },
                branchcode: { required: true },
                broker_flag: { required: true },
                currency_code: { required: true },
                pay_method: { required: true },
                premium_payment_term: { required: true },
                division: { required: true },
                coverfrom: { required: true },
                coverto: { required: true },
            },
            messages: {
                type_of_bus: "Please select a business type",
                covertype: "Please select a cover type",
                branchcode: "Please select a branch",
                broker_flag: "Please select broker flag",
                currency_code: "Please select a currency",
                pay_method: "Please select a payment method",
                premium_payment_term: "Please select payment terms",
                division: "Please select a division",
                coverfrom: "Please enter cover start date",
                coverto: "Please enter cover end date",
            },
        });
    }

    /**
     * Initialize fields based on transaction type and existing data
     */
    function initializeFields() {
        // Hide all dynamic sections initially
        hideSections();

        // If editing or viewing existing transaction
        if (config.trans_type !== "NEW" && config.oldData) {
            populateExistingData();
        }

        // Trigger initial changes
        elements.typeOfBus.trigger("change");
        elements.coverType.trigger("change");
        elements.brokerFlag.trigger("change");
        elements.payMethod.trigger("change");
        elements.applyEml.trigger("change");

        // Handle prospect data if provided
        if (config.prospectId) {
            elements.prospectId.val(config.prospectId);
            loadProspectData(config.prospectId);
        }
    }

    /**
     * Handle business type change
     */
    function handleBusinessTypeChange() {
        const bustype = $(this).val();

        // Hide all sections first
        hideSections();

        if (!bustype) return;

        // Show relevant sections based on business type
        if (["FPR", "FNP"].includes(bustype)) {
            showFacSection();
        } else if (bustype === "TPR") {
            showTreatyProportionalSection();
        } else if (bustype === "TNP") {
            showTreatyNonProportionalSection();
        }

        // Load treaty types
        loadTreatyTypes(bustype);
    }

    /**
     * Handle cover type change
     */
    function handleCoverTypeChange() {
        const coverTypeDesc = $(this)
            .find("option:selected")
            .data("description");

        elements.binderSection.hide();
        $("#bindercoverno").prop("required", false).val("");

        if (coverTypeDesc === "B") {
            elements.binderSection.show();
            $("#bindercoverno").prop("required", true);
            loadBinderCovers();
        }
    }

    /**
     * Handle broker flag change
     */
    function handleBrokerFlagChange() {
        const brokerFlag = $(this).val();

        if (brokerFlag === "Y") {
            elements.brokerSection.show();
            $("#brokercode").prop("required", true).prop("disabled", false);
        } else {
            elements.brokerSection.hide();
            $("#brokercode")
                .prop("required", false)
                .prop("disabled", true)
                .val("");
        }
    }

    /**
     * Handle payment method change
     */
    function handlePaymentMethodChange() {
        const payMethodDesc = $(this)
            .find("option:selected")
            .data("description");

        $("#installments_count_section").hide();
        $("#add_installment_btn_section").hide();
        elements.installmentSection.hide();
        $("#no_of_installments").prop("required", false).val("");

        if (payMethodDesc === "I") {
            $("#installments_count_section").show();
            $("#add_installment_btn_section").show();
            $("#no_of_installments").prop("required", true);

            if (config.trans_type !== "NEW") {
                elements.installmentSection.show();
            }
        } else {
            $("#no_of_installments").val(1);
        }
    }

    /**
     * Handle currency change
     */
    function handleCurrencyChange() {
        const currencyCode = $(this).val();

        if (!currencyCode) {
            elements.todayCurrency.val("");
            return;
        }

        showLoader("Loading exchange rate...");

        $.ajax({
            url: config.routes.getTodaysRate,
            data: { currency_code: currencyCode },
            type: "GET",
            success: function (response) {
                const data = JSON.parse(response);

                if (data.valid === 2) {
                    // Base currency
                    elements.todayCurrency.val("1.00");
                } else if (data.valid === 1) {
                    // Rate found
                    elements.todayCurrency.val(numberWithCommas(data.rate));
                } else {
                    // Rate not set
                    elements.todayCurrency.val("");
                    toastr.warning("Currency rate for today not yet set");
                }
            },
            error: function () {
                toastr.error("Failed to load exchange rate");
            },
            complete: function () {
                hideLoader();
            },
        });
    }

    /**
     * Handle EML application change
     */
    function handleEmlChange() {
        const applyEml = $(this).val();

        $(".eml-field").hide();
        $("#eml_rate, #eml_amt").prop("required", false);

        if (applyEml === "Y") {
            $(".eml-field").show();
            $("#eml_rate, #eml_amt").prop("required", true);
            calculateEml();
        } else {
            $("#eml_rate").val("");
            $("#eml_amt").val("");
            $("#effective_sum_insured").val($("#total_sum_insured").val());
        }
    }

    /**
     * Handle class group change
     */
    function handleClassGroupChange() {
        const classGroup = $(this).val();

        elements.classCode
            .empty()
            .append('<option value="">Select Class Name</option>');

        if (!classGroup) return;

        showLoader("Loading classes...");

        $.ajax({
            url: config.routes.getClasses,
            data: { class_group: classGroup },
            type: "GET",
            success: function (response) {
                const classes = response ? JSON.parse(response) : [];

                $.each(classes, function (i, cls) {
                    elements.classCode.append(
                        $("<option>")
                            .val(cls.class_code)
                            .text(`${cls.class_code} - ${cls.class_name}`)
                    );
                });

                elements.classCode.trigger("change.select2");
            },
            error: function () {
                toastr.error("Failed to load classes");
            },
            complete: function () {
                hideLoader();
            },
        });
    }

    /**
     * Handle form submission
     */
    function handleFormSubmit(e) {
        e.preventDefault();

        // Validate form
        if (!elements.form.valid()) {
            toastr.error("Please correct the errors before submitting");
            return false;
        }

        // Validate installments if applicable
        if (isInstallmentPayment() && !validateInstallments()) {
            return false;
        }

        // Confirm submission
        Swal.fire({
            title: "Confirm Submission",
            text: "Do you want to submit this cover registration?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, submit",
            cancelButtonText: "Cancel",
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-secondary",
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                // Set risk details content
                $("#hidden_risk_details").val(
                    $("#risk_details_content").html()
                );

                // Submit form
                showLoader("Submitting...");
                elements.form.submit();
            }
        });
    }

    /**
     * Validate dates
     */
    function validateDates() {
        const coverFrom = new Date($("#coverfrom").val());
        const coverTo = new Date($("#coverto").val());

        if (coverFrom >= coverTo) {
            Swal.fire({
                icon: "error",
                title: "Invalid Date Range",
                text: "Cover start date must be earlier than cover end date",
            });

            $("#coverfrom").val("");
            $("#coverto").val("");
        }
    }

    /**
     * Add installments
     */
    function addInstallments() {
        const noOfInstallments = parseInt($("#no_of_installments").val());
        const businessType = elements.typeOfBus.val();
        const cedantPremium = $("#cede_premium").val();
        const facShareOffered = $("#fac_share_offered").val();
        const commRate = $("#comm_rate").val();

        // Validate required fields
        if (!noOfInstallments) {
            toastr.error("Please enter number of installments");
            return false;
        }

        if (!businessType) {
            toastr.error("Please select business type");
            return false;
        }

        if (!cedantPremium) {
            toastr.error("Please enter cedant premium");
            return false;
        }

        if (!facShareOffered) {
            toastr.error("Please enter share offered");
            return false;
        }

        if (!commRate) {
            toastr.error("Please enter commission rate");
            return false;
        }

        // Calculate installment amount
        const instalAmount = computeInstallmentAmount();
        config.installmentTotalAmount = parseFloat(instalAmount);

        const installmentAmount = (
            config.installmentTotalAmount / noOfInstallments
        ).toFixed(2);

        // Clear existing installments
        $("#fac-installments-section").empty();

        // Add installment rows
        for (let i = 1; i <= noOfInstallments; i++) {
            const row = createInstallmentRow(i, installmentAmount);
            $("#fac-installments-section").append(row);
        }

        // Show installment section
        elements.installmentSection.show();

        toastr.success("Installments added successfully");
    }

    /**
     * Create installment row HTML
     */
    function createInstallmentRow(index, amount) {
        return `
            <div class="row g-3 mb-3 installment-row" data-count="${index}">
                <div class="col-md-3">
                    <label class="form-label">Installment</label>
                    <input type="hidden" name="installment_no[]" value="${index}">
                    <input type="hidden" name="installment_id[]" value="">
                    <input type="text" value="Installment No. ${index}"
                           id="instl_no_${index}" class="form-control" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="installment_date[]"
                           id="instl_date_${index}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Amount</label>
                    <input type="text" name="installment_amt[]"
                           id="instl_amnt_${index}" value="${numberWithCommas(
            amount
        )}"
                           class="form-control amount" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-danger w-100 remove-installment" type="button">
                        <i class="bx bx-minus me-1"></i> Remove
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Remove installment
     */
    function removeInstallment() {
        const currentCount = parseInt($("#no_of_installments").val());

        if (currentCount > 1) {
            $("#no_of_installments").val(currentCount - 1);
            $(this).closest(".installment-row").remove();
        } else {
            $("#no_of_installments").val("");
            elements.installmentSection.hide();
            $(this).closest(".installment-row").remove();
        }
    }

    /**
     * Validate installments
     */
    function validateInstallments() {
        if ($("#fac-installments-section").is(":empty")) {
            toastr.error("Please add installment details");
            return false;
        }

        const totalInstallments = calculateTotalInstallments();
        const expectedAmount = config.installmentTotalAmount;

        if (!areDecimalsEqual(expectedAmount, totalInstallments, 0.5)) {
            toastr.error(
                `Total installment amount (${numberWithCommas(
                    totalInstallments.toFixed(2)
                )}) does not match expected amount (${numberWithCommas(
                    expectedAmount.toFixed(2)
                )})`
            );
            return false;
        }

        // Validate each installment has a date
        let hasEmptyDate = false;
        $(".installment-row").each(function () {
            const dateInput = $(this).find('input[name="installment_date[]"]');
            if (!dateInput.val()) {
                hasEmptyDate = true;
                dateInput.addClass("is-invalid");
            }
        });

        if (hasEmptyDate) {
            toastr.error("Please enter due date for all installments");
            return false;
        }

        return true;
    }

    /**
     * Calculate total installments
     */
    function calculateTotalInstallments() {
        let total = 0;
        $('input[name="installment_amt[]"]').each(function () {
            const value = parseFloat(removeCommas($(this).val()));
            if (!isNaN(value)) {
                total += value;
            }
        });
        return total;
    }

    /**
     * Compute installment amount
     */
    function computeInstallmentAmount() {
        const shareOffered = parseNumber($("#fac_share_offered").val());
        const rate = parseNumber($("#comm_rate").val());
        const cedantPremium = parseNumber($("#cede_premium").val());

        const totalDr = (shareOffered / 100) * cedantPremium;
        const totalCr = (rate / 100) * totalDr;

        return (totalDr - totalCr).toFixed(2);
    }

    /**
     * Calculate cedant commission
     */
    function calculateCedantCommission() {
        const rate = parseNumber($(this).val());
        const cedePremium = parseNumber($("#cede_premium").val());

        const commAmount = (rate / 100) * cedePremium;
        $("#comm_amt").val(numberWithCommas(commAmount.toFixed(2)));

        calculateBrokerageCommission();
    }

    /**
     * Calculate reinsurer commission
     */
    function calculateReinsurerCommission() {
        const rate = parseNumber($(this).val());
        const reinPremium = parseNumber($("#rein_premium").val());

        const commAmount = (rate / 100) * reinPremium;
        $("#reins_comm_amt").val(numberWithCommas(commAmount.toFixed(2)));

        calculateBrokerageCommission();
    }

    /**
     * Handle cedant premium change
     */
    function handleCedantPremiumChange() {
        $("#comm_rate").trigger("keyup");
        $("#rein_premium").val($(this).val());
    }

    /**
     * Handle reinsurer commission type change
     */
    function handleReinsurerCommTypeChange() {
        const commType = $(this).val();

        if (commType === "R") {
            $(".reins-comm-rate-field").show();
            $("#reins_comm_rate").prop("disabled", false);
            $("#reins_comm_amt").prop("readonly", true);
        } else {
            $(".reins-comm-rate-field").hide();
            $("#reins_comm_rate").prop("disabled", true);
            $("#reins_comm_amt").prop("readonly", false);
        }

        if (config.resetableTransTypes.includes(config.trans_type)) {
            $("#reins_comm_amt").val("");
        }
    }

    /**
     * Handle brokerage commission type change
     */
    function handleBrokerageCommTypeChange() {
        const brokerageCommType = $(this).val();

        $(".brokerage-amount-field").hide();
        $(".brokerage-rate-field").hide();
        $(".brokerage-rate-amount-field").hide();

        $("#brokerage_comm_amt").prop("disabled", true);
        $("#brokerage_comm_rate").val("");
        $("#brokerage_comm_rate_amnt").val("");

        if (brokerageCommType === "R") {
            $(".brokerage-rate-field").show();
            $(".brokerage-rate-amount-field").show();
            calculateBrokerageCommission();
        } else if (brokerageCommType === "A") {
            $(".brokerage-amount-field").show();
            $("#brokerage_comm_amt").prop("disabled", false);
        }
    }

    /**
     * Calculate brokerage commission
     */
    function calculateBrokerageCommission() {
        const brokerageCommType = $("#brokerage_comm_type").val();

        if (brokerageCommType !== "R") return;

        const cedantCommRate = parseNumber($("#comm_rate").val());
        const reinCommRate = parseNumber($("#reins_comm_rate").val());
        const reinCommAmt = parseNumber($("#reins_comm_amt").val());

        const brokerageCommRate = Math.max(0, reinCommRate - cedantCommRate);
        const brokerageCommAmt = (brokerageCommRate / 100) * reinCommAmt;

        $("#brokerage_comm_rate").val(
            numberWithCommas(brokerageCommRate.toFixed(2))
        );
        $("#brokerage_comm_rate_amnt").val(
            numberWithCommas(brokerageCommAmt.toFixed(2))
        );
    }

    /**
     * Calculate EML
     */
    function calculateEml() {
        const emlRate = parseNumber($("#eml_rate").val());
        const totalSumInsured = parseNumber($("#total_sum_insured").val());

        if (!emlRate || !totalSumInsured) {
            $("#effective_sum_insured").val(
                numberWithCommas(totalSumInsured.toFixed(2))
            );
            return;
        }

        const emlAmt = totalSumInsured * (emlRate / 100);

        $("#eml_amt").val(numberWithCommas(emlAmt.toFixed(2)));
        $("#effective_sum_insured").val(numberWithCommas(emlAmt.toFixed(2)));
    }

    /**
     * Calculate quota retention
     */
    function calculateQuotaRetention() {
        const row = $(this).closest("[data-counter]");
        const counter = row.data("counter");

        const retentionPer = parseNumber($(`#retention_per-${counter}`).val());
        const quotaLimit = parseNumber(
            $(`#quota_share_total_limit-${counter}`).val()
        );

        if (!retentionPer || !quotaLimit) return;

        const treatyPer = 100 - retentionPer;
        const retentionAmt = (retentionPer / 100) * quotaLimit;
        const treatyLimit = (treatyPer / 100) * quotaLimit;

        $(`#treaty_reice-${counter}`).val(treatyPer);
        $(`#quota_retention_amt-${counter}`).val(
            numberWithCommas(retentionAmt.toFixed(2))
        );
        $(`#quota_treaty_limit-${counter}`).val(
            numberWithCommas(treatyLimit.toFixed(2))
        );
    }

    /**
     * Calculate surplus limit
     */
    function calculateSurplusLimit() {
        const row = $(this).closest("[data-counter]");
        const counter = row.data("counter");

        const lines = parseNumber($(`#no_of_lines-${counter}`).val());
        const retention = parseNumber(
            $(`#surp_retention_amt-${counter}`).val()
        );

        if (!lines || !retention) return;

        const treatyLimit = lines * retention;
        $(`#surp_treaty_limit-${counter}`).val(
            numberWithCommas(treatyLimit.toFixed(2))
        );
    }

    /**
     * Load treaty types based on business type
     */
    function loadTreatyTypes(businessType) {
        if (!businessType) return;

        $.ajax({
            url: config.routes.getTreatyTypes,
            data: { type_of_bus: businessType },
            type: "GET",
            success: function (response) {
                const treatySelect = $("#treatytype");
                treatySelect
                    .empty()
                    .append('<option value="">Select Treaty Type</option>');

                $.each(response, function (i, treaty) {
                    treatySelect.append(
                        $("<option>")
                            .val(treaty.treaty_code)
                            .text(
                                `${treaty.treaty_code} - ${treaty.treaty_name}`
                            )
                    );
                });

                treatySelect.trigger("change.select2");
            },
            error: function () {
                toastr.error("Failed to load treaty types");
            },
        });
    }

    /**
     * Load binder covers
     */
    function loadBinderCovers() {
        $.ajax({
            url: config.routes.getBinderCovers,
            type: "GET",
            success: function (response) {
                const binders = JSON.parse(response);
                const binderSelect = $("#bindercoverno");

                binderSelect
                    .empty()
                    .append('<option value="">Select Binder Cover</option>');

                $.each(binders, function (i, binder) {
                    binderSelect.append(
                        $("<option>")
                            .val(binder.binder_cov_no)
                            .text(
                                `${binder.binder_cov_no} - ${binder.agency_name}`
                            )
                    );
                });

                binderSelect.trigger("change.select2");
            },
            error: function () {
                toastr.error("Failed to load binder covers");
            },
        });
    }

    /**
     * Handle treaty type change
     */
    function handleTreatyTypeChange() {
        const treatyType = $(this).val();

        // Hide all treaty-specific sections
        $(".quota_header_div, .surp_header_div").hide();
        $(
            ".quota_share_total_limit_div, .retention_per_div, .treaty_reice_div"
        ).hide();
        $(".quota_retention_amt_div, .quota_treaty_limit_div").hide();
        $(
            ".no_of_lines_div, .surp_retention_amt_div, .surp_treaty_limit_div"
        ).hide();
        $("#reinsurer_per_treaty_section").hide();

        // Show relevant sections based on treaty type
        if (treatyType === "QUOT") {
            $(".quota_header_div").show();
            $(
                ".quota_share_total_limit_div, .retention_per_div, .treaty_reice_div"
            ).show();
            $(".quota_retention_amt_div, .quota_treaty_limit_div").show();
        } else if (treatyType === "SURP") {
            $(".surp_header_div").show();
            $(
                ".no_of_lines_div, .surp_retention_amt_div, .surp_treaty_limit_div"
            ).show();
        } else if (treatyType === "SPQT") {
            $(".quota_header_div, .surp_header_div").show();
            $(
                ".quota_share_total_limit_div, .retention_per_div, .treaty_reice_div"
            ).show();
            $(".quota_retention_amt_div, .quota_treaty_limit_div").show();
            $(
                ".no_of_lines_div, .surp_retention_amt_div, .surp_treaty_limit_div"
            ).show();
            $("#reinsurer_per_treaty_section").show();
        }
    }

    /**
     * Handle method change (for non-proportional)
     */
    function handleMethodChange() {
        const method = $(this).val();

        $(".burning_rate_div, .flat_rate_div").hide();
        $(".burning_rate, .flat_rate").prop("disabled", true).val("");

        if (method === "B") {
            $(".burning_rate_div").show();
            $(".burning_rate").prop("disabled", false);
        } else if (method === "F") {
            $(".flat_rate_div").show();
            $(".flat_rate").prop("disabled", false);
        }
    }

    /**
     * Handle reinclass change
     */
    function handleReinclassChange() {
        const counter = $(this).data("counter");
        const reinclass = $(this).val();
        const treatyType = $("#treatytype").val();

        if (!treatyType) {
            toastr.error("Please select treaty type first");
            $(this).val("");
            return false;
        }

        // Set reinclass for commission sections
        $(`#prem_type_reinclass-${counter}-0`).val(reinclass);
        $(`#prem_type_code-${counter}-0`).attr("data-reinclass", reinclass);
        $(`#prem_type_treaty-${counter}-0`).trigger("change");
    }

    /**
     * Add reinsurance class
     */
    function addReinClass() {
        const lastSection = $(".reinclass-section").last();
        const prevCounter = parseInt(lastSection.attr("data-counter"));
        const reinClassVal = $(`#treaty_reinclass-${prevCounter}`).val();

        if (!reinClassVal) {
            const sectionLabel = String.fromCharCode(65 + prevCounter);
            toastr.error(
                `Please select reinsurance class in Section ${sectionLabel}`
            );
            return false;
        }

        // Clone and update section
        const newSection = lastSection.clone();
        const counter = prevCounter + 1;

        // Update IDs and data attributes
        newSection.attr("id", `reinclass-section-${counter}`);
        newSection.attr("data-counter", counter);

        // Remove select2 containers
        newSection.find(".select2-container").remove();

        // Update section title
        const sectionLabel = String.fromCharCode(65 + counter);
        newSection.find(".section-title").text(`Section ${sectionLabel}`);

        // Reset values
        newSection.find('input[type="text"], input[type="number"]').val("");
        newSection.find("select").val("").removeAttr("selected");

        // Update counter attributes
        newSection.find("[id], [data-counter]").each(function () {
            const id = $(this).attr("id");
            if (id) {
                $(this).attr("id", id.replace(/-\d+$/, `-${counter}`));
            }
            $(this).attr("data-counter", counter);
        });

        // Remove all commission sections except first
        newSection.find(".comm-sections").slice(1).remove();

        // Insert new section
        lastSection.after(newSection);

        // Reinitialize select2
        newSection.find(".select2").select2({
            width: "100%",
            theme: "bootstrap-5",
        });

        toastr.success("Reinsurance class section added");
    }

    /**
     * Add commission section
     */
    function addCommissionSection() {
        const classCounter = $(this).data("counter");
        const lastCommSection = $(`#comm-section-${classCounter}`).find(
            ".comm-sections:last"
        );
        const prevCounter = lastCommSection.data("counter");
        const counter = prevCounter + 1;

        // Validate previous section
        const reinclass = $(`#treaty_reinclass-${classCounter}`).val();
        const premType = $(
            `#prem_type_code-${classCounter}-${prevCounter}`
        ).val();
        const commRate = $(
            `#prem_type_comm_rate-${classCounter}-${prevCounter}`
        ).val();

        if (!reinclass) {
            toastr.error("Please select reinsurance class");
            return false;
        }

        if (!premType) {
            toastr.error("Please select premium type");
            return false;
        }

        if (!commRate) {
            toastr.error("Please enter commission rate");
            return false;
        }

        // Create new commission section
        const newSection = createCommissionSection(classCounter, counter);
        $(`#comm-section-${classCounter}`).append(newSection);

        // Initialize select2
        $(
            `#prem_type_treaty-${classCounter}-${counter}, #prem_type_code-${classCounter}-${counter}`
        ).select2({
            width: "100%",
            theme: "bootstrap-5",
        });

        // Load premium types
        $(`#prem_type_treaty-${classCounter}-${counter}`).trigger("change");
    }

    /**
     * Create commission section HTML
     */
    function createCommissionSection(classCounter, premCounter) {
        return `
            <div class="row g-3 mb-2 comm-sections"
                 id="comm-section-${classCounter}-${premCounter}"
                 data-class-counter="${classCounter}"
                 data-counter="${premCounter}">
                <div class="col-md-4">
                    <label class="form-label required">Treaty</label>
                    <select class="form-control select2 prem_type_treaty"
                            name="prem_type_treaty[]"
                            id="prem_type_treaty-${classCounter}-${premCounter}"
                            data-class-counter="${classCounter}"
                            data-counter="${premCounter}"
                            required>
                        <option value="">Select Treaty</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Premium Type</label>
                    <input type="hidden"
                           class="prem_type_reinclass"
                           id="prem_type_reinclass-${classCounter}-${premCounter}"
                           name="prem_type_reinclass[]">
                    <select class="form-control select2 prem_type_code"
                            name="prem_type_code[]"
                            id="prem_type_code-${classCounter}-${premCounter}"
                            data-class-counter="${classCounter}"
                            data-counter="${premCounter}"
                            required>
                        <option value="">Select Premium Type</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Commission (%)</label>
                    <div class="input-group">
                        <input type="text"
                               class="form-control prem_type_comm_rate"
                               name="prem_type_comm_rate[]"
                               id="prem_type_comm_rate-${classCounter}-${premCounter}"
                               required>
                        <button class="btn btn-danger remove-comm-section" type="button">
                            <i class="bx bx-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Remove commission section
     */
    function removeCommissionSection() {
        $(this).closest(".comm-sections").remove();
    }

    /**
     * Add layer
     */
    function addLayer() {
        const lastLayer = $("#layer-section .layer-sections:last");
        const prevCounter = lastLayer.data("counter");
        const counter = prevCounter + 1;

        // Create new layer
        const newLayer = createLayerSection(counter);
        $("#layer-section").append(newLayer);

        // Initialize select2
        $(`#reinstatement_type-${counter}-0`).select2({
            width: "100%",
            theme: "bootstrap-5",
        });

        toastr.success("Layer added successfully");
    }

    /**
     * Create layer section HTML
     */
    function createLayerSection(counter) {
        return `
            <div class="layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                <h6 class="mt-3">Layer ${counter + 1}</h6>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label required">Capture Limits per Class?</label>
                        <select class="form-control limit_per_reinclass"
                                name="limit_per_reinclass[]"
                                id="limit_per_reinclass-${counter}-0"
                                required>
                            <option value="">Select Option</option>
                            <option value="N" selected>No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label required">Reinclass</label>
                        <input type="hidden" name="layer_no[]" value="${
                            counter + 1
                        }">
                        <input type="hidden" name="nonprop_reinclass[]"
                               id="nonprop_reinclass-${counter}-0" value="ALL">
                        <input type="text" class="form-control"
                               id="nonprop_reinclass_desc-${counter}-0"
                               value="ALL" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label required">Limit</label>
                        <input type="text" class="form-control amount"
                               name="indemnity_treaty_limit[]"
                               id="indemnity_treaty_limit-${counter}-0"
                               required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label required">Deductible Amount</label>
                        <input type="text" class="form-control amount"
                               name="underlying_limit[]"
                               id="underlying_limit-${counter}-0"
                               required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label required">EGNPI</label>
                        <input type="text" class="form-control amount"
                               name="egnpi[]"
                               id="egnpi-${counter}-0"
                               required>
                    </div>
                    <div class="col-md-2 burning_rate_div" style="display: none;">
                        <label class="form-label required">Min BC Rate (%)</label>
                        <input type="text" class="form-control burning_rate"
                               name="min_bc_rate[]"
                               id="min_bc_rate-${counter}-0">
                    </div>
                    <div class="col-md-2 burning_rate_div" style="display: none;">
                        <label class="form-label required">Max Rate (%)</label>
                        <input type="text" class="form-control burning_rate"
                               name="max_bc_rate[]"
                               id="max_bc_rate-${counter}-0">
                    </div>
                    <div class="col-md-2 flat_rate_div" style="display: none;">
                        <label class="form-label required">Flat Rate (%)</label>
                        <input type="text" class="form-control flat_rate"
                               name="flat_rate[]"
                               id="flat_rate-${counter}-0">
                    </div>
                    <div class="col-md-2 burning_rate_div" style="display: none;">
                        <label class="form-label required">Upper Adj Rate</label>
                        <input type="text" class="form-control burning_rate"
                               name="upper_adj[]"
                               id="upper_adj-${counter}-0">
                    </div>
                    <div class="col-md-2 burning_rate_div" style="display: none;">
                        <label class="form-label required">Lower Adj Rate</label>
                        <input type="text" class="form-control burning_rate"
                               name="lower_adj[]"
                               id="lower_adj-${counter}-0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label required">Min Deposit Premium</label>
                        <div class="input-group">
                            <input type="text" class="form-control amount"
                                   name="min_deposit[]"
                                   id="min_deposit-${counter}-0"
                                   required>
                            <button class="btn btn-danger remove-layer-section" type="button">
                                <i class="bx bx-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label required">Reinstatement Type</label>
                        <select name="reinstatement_type[]"
                                id="reinstatement_type-${counter}-0"
                                class="form-control select2"
                                required>
                            <option value="NOR">Number of Reinstatement</option>
                            <option value="AAL">Annual Aggregate Limit</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label required">Reinstatement Value</label>
                        <input type="text" class="form-control amount"
                               name="reinstatement_value[]"
                               id="reinstatement_value-${counter}-0"
                               required>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Remove layer
     */
    function removeLayer() {
        $(this).closest(".layer-sections").remove();
    }

    /**
     * Handle limit per reinclass change
     */
    function handleLimitPerReinclassChange() {
        // Implementation for handling limit per reinclass
        // This would create additional layer sections per reinclass
    }

    /**
     * Load prospect data
     */
    function loadProspectData(prospectId) {
        if (prospectId.length < 3) return;

        showLoader("Loading prospect data...");

        const url = config.routes.getProspectData.replace(
            ":id",
            encodeURIComponent(prospectId)
        );

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status) {
                    populateProspectData(response.data);
                    toastr.success("Prospect data loaded successfully");
                } else {
                    toastr.error("No data found for this Prospect ID");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error loading prospect data:", error);
                toastr.error("Failed to load prospect data");
            },
            complete: function () {
                hideLoader();
            },
        });
    }

    /**
     * Populate prospect data
     */
    function populateProspectData(data) {
        // Basic information
        elements.typeOfBus.val(data.type_of_bus).trigger("change");
        elements.coverType.val(data.covertype).trigger("change");
        $("#branchcode").val(data.branchcode).trigger("change");

        // Broker information
        if (data.broker_flag) {
            elements.brokerFlag.val(data.broker_flag).trigger("change");
            if (data.broker_flag === "Y" && data.broker_code) {
                $("#brokercode").val(data.broker_code).trigger("change");
            }
        }

        // Financial information
        if (data.currency_code) {
            elements.currencyCode.val(data.currency_code).trigger("change");
        }

        // Continue populating other fields...
        // This is a simplified version - expand based on your data structure
    }

    /**
     * Populate existing data (for edit mode)
     */
    function populateExistingData() {
        // Implementation for populating form with existing data
        // when editing or viewing
    }

    /**
     * Handle risk details paste
     */
    function handleRiskDetailsPaste(e) {
        const clipboardData = (e.originalEvent || e).clipboardData;
        const pastedText = clipboardData.getData("text/html");

        if (pastedText) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(pastedText, "text/html");
            const table = $(doc).find("table");

            if (table.length) {
                $("#hidden_risk_details").val(table.html());
            }
        }
    }

    /**
     * Show/hide sections
     */
    function hideSections() {
        elements.facSection.hide();
        elements.treatySection.hide();
        $("#treaty_proportional_section").hide();
        $("#treaty_nonproportional_section").hide();
    }

    function showFacSection() {
        elements.facSection.show();
        enableSection(".fac-section");
    }

    function showTreatyProportionalSection() {
        elements.treatySection.show();
        $("#treaty_common_section").show();
        $("#treaty_proportional_section").show();
        enableSection(".treaty-section");
        enableSection(".treaty-proportional");
    }

    function showTreatyNonProportionalSection() {
        elements.treatySection.show();
        $("#treaty_common_section").show();
        $("#treaty_nonproportional_section").show();
        enableSection(".treaty-section");
        enableSection(".treaty-nonproportional");
    }

    function enableSection(selector) {
        $(`${selector} input, ${selector} select, ${selector} textarea`)
            .prop("disabled", false)
            .removeClass("d-none");
        $(`${selector}-div`).show();
    }

    function disableSection(selector) {
        $(`${selector} input, ${selector} select, ${selector} textarea`)
            .prop("disabled", true)
            .val("");
        $(`${selector}-div`).hide();
    }

    /**
     * Utility functions
     */
    function showLoader(message = "Loading...") {
        elements.pageLoader.find(".loader-text").text(message);
        elements.pageLoader.fadeIn(200);
    }

    function hideLoader() {
        elements.pageLoader.fadeOut(200);
    }

    function parseNumber(value) {
        return parseFloat(String(value).replace(/,/g, "")) || 0;
    }

    function numberWithCommas(value) {
        if (!value) return "";
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function removeCommas(value) {
        return String(value).replace(/,/g, "");
    }

    function areDecimalsEqual(num1, num2, tolerance = 0.1) {
        return Math.abs(parseFloat(num1) - parseFloat(num2)) <= tolerance;
    }

    function isInstallmentPayment() {
        const payMethodDesc = elements.payMethod
            .find("option:selected")
            .data("description");
        return payMethodDesc === "I";
    }

    // Public API
    return {
        init: init,
        showLoader: showLoader,
        hideLoader: hideLoader,
        numberWithCommas: numberWithCommas,
        parseNumber: parseNumber,
    };
})();

// Initialize on document ready
$(document).ready(function () {
    CoverRegistration.init();
});
