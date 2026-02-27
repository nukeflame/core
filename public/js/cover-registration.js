// @pk305
/**
 * Cover Registration Module
 *
 */

const CoverRegistration = (function () {
    "use strict";

    const config = {
        trans_type: "",
        prospectId: "",
        routes: {},
        premTypesData: [],
        oldData: null,
        resetableTransTypes: ["NEW"],
        installmentTotalAmount: 0,
        currentModalTarget: null,
        maxInstallments: 100,
        maxLayers: 20,
        maxReinClasses: 14,
        debounceDelay: 300,
        calculationPrecision: 2,
    };

    const state = {
        isSubmitting: false,
        isDirty: false,
        cachedData: new Map(),
        activeRequests: new Map(),
        hasInitializedTreatyType: false,
    };

    const elements = {
        form: null,
        typeOfBus: null,
        coverType: null,
        binderSection: null,
        brokerFlag: null,
        brokerSection: null,
        payMethod: null,
        currencyCode: null,
        todayCurrency: null,
        applyEml: null,
        facSection: null,
        treatySection: null,
        installmentSection: null,
        pageLoader: null,
        classGroup: null,
        classCode: null,
        prospectId: null,
    };

    function init() {
        try {
            loadConfiguration();
            cacheElements();
            validateElements();
            setupEventListeners();
            initializePlugins();
            setupValidation();
            initializeFields();
            setupBeforeUnloadWarning();
            hideLoader();
        } catch (error) {
            console.error("Initialization error:", error);
            Swal.fire({
                icon: "error",
                title: "Initialization Error",
                text: "Failed to initialize the cover registration form. Please refresh the page.",
            });
        }
    }

    function loadConfiguration() {
        if (typeof window.coverConfig !== "undefined") {
            Object.assign(config, window.coverConfig);

            config.maxInstallments = Math.min(
                Math.max(1, config.maxInstallments || 100),
                100,
            );
            config.maxLayers = Math.min(
                Math.max(1, config.maxLayers || 20),
                20,
            );
        }
    }

    function cacheElements() {
        const elementIds = {
            form: "#register_cover",
            typeOfBus: "#type_of_bus",
            coverType: "#covertype",
            binderSection: "#binder_section",
            brokerFlag: "#broker_flag",
            brokerSection: "#broker_section",
            payMethod: "#pay_method",
            currencyCode: "#currency_code",
            todayCurrency: "#today_currency",
            applyEml: "#apply_eml",
            facSection: "#fac_section",
            treatySection: "#treaty_section",
            installmentSection: "#installment_section",
            pageLoader: "#page-loader",
            classGroup: "#class_group",
            classCode: "#classcode",
            prospectId: "#prospect_id",
        };

        Object.entries(elementIds).forEach(([key, selector]) => {
            elements[key] = $(selector);
        });
    }

    function validateElements() {
        const requiredElements = [
            "form",
            "typeOfBus",
            "coverType",
            "pageLoader",
        ];
        const missingElements = requiredElements.filter(
            (key) => !elements[key].length,
        );

        if (missingElements.length > 0) {
            throw new Error(
                `Required elements not found: ${missingElements.join(", ")}`,
            );
        }
    }

    function setupEventListeners() {
        $(document).on("input keyup", "input, textarea", function () {
            $(this).removeClass("is-invalid");
            $(this).next(".invalid-feedback").remove();
            $(this).parent(".input-group").next(".invalid-feedback").remove();
        });

        $(document).on("change", "select", function () {
            $(this).removeClass("is-invalid");

            $(this)
                .next(".select2-container")
                .find(".select2-selection")
                .removeClass("is-invalid");

            $(this).closest(".cover-card").find(".invalid-feedback").remove();
        });

        elements.typeOfBus.on("change", handleBusinessTypeChange);
        elements.coverType.on("change", handleCoverTypeChange);
        elements.brokerFlag.on("change", handleBrokerFlagChange);
        elements.payMethod.on("change", handlePaymentMethodChange);
        elements.currencyCode.on("change", handleCurrencyChange);
        elements.applyEml.on("change", handleEmlChange);
        elements.classGroup.on("change", handleClassGroupChange);

        $("#register_cover").on("submit", handleFormSubmit);

        $("#coverfrom, #coverto").on("change", debounce(validateDates, 300));
        $("#coverfrom, #coverto").on("change", calculateCover);

        $("#add_fac_instalments").on("click", addInstallments);
        $(document).on("click", ".remove-installment", removeInstallment);

        $("#comm_rate").on("keyup", debounce(calculateCedantCommission, 300));
        $("#reins_comm_rate").on(
            "keyup",
            debounce(calculateReinsurerCommission, 300),
        );
        $("#cede_premium").on(
            "keyup",
            debounce(handleCedantPremiumChange, 300),
        );

        $("#reins_comm_type").on("change", handleReinsurerCommTypeChange);
        $("#brokerage_comm_type").on("change", handleBrokerageCommTypeChange);

        $("#eml_rate, #total_sum_insured").on(
            "keyup",
            debounce(calculateEml, 300),
        );

        $(document).on("change", ".treaty_reinclass", handleReinclassChange);
        $("#add_rein_class").on("click", addReinClass);
        $(document).on("click", ".add-comm-section", addCommissionSection);
        $(document).on(
            "click",
            ".remove-comm-section",
            removeCommissionSection,
        );
        $(document).on("click", ".remove-rein-class", removeReinClass);

        $(document).on(
            "change",
            ".commission_type",
            handleCommissionTypeChange,
        );
        $(document).on(
            "click",
            ".configure-sliding-btn",
            handleSlidingScaleConfig,
        );
        $("#save-sliding-scale").on("click", saveSlidingScale);
        $("#add-scale-tier").on("click", addSlidingScaleTier);
        $(document).on("click", ".remove-scale-row", removeSlidingScaleTier);
        $(document).on("click", ".load-template", loadSlidingTemplate);

        $("#import-sliding-csv").on("click", () =>
            $("#sliding-csv-file").click(),
        );
        $("#sliding-csv-file").on("change", handleSlidingCSVImport);

        $("#add-layer-section").on("click", addLayer);
        $(document).on("click", ".remove-layer-section", removeLayer);
        $(document).on(
            "change",
            ".limit_per_reinclass",
            handleLimitPerReinclassChange,
        );

        $("#treatytype").on("change", handleTreatyTypeChange);
        $("#method").on("change", handleMethodChange);

        elements.prospectId.on(
            "change",
            debounce(function () {
                const prospectId = $(this).val();
                if (prospectId && prospectId.length >= 3) {
                    loadProspectData(prospectId);
                }
            }, 500),
        );

        $("#addInsuredData").on("click", () =>
            $("#addInsuredDataModal").modal("show"),
        );

        $("#risk_details_content").on("paste", handleRiskDetailsPaste);

        $(document).on("keyup", ".amount", debounce(formatAmountField, 300));

        $(document).on(
            "keyup",
            ".retention_per, .quota_share_total_limit",
            debounce(calculateQuotaRetention, 300),
        );
        $(document).on(
            "keyup",
            ".no_of_lines",
            debounce(calculateSurplusLimit, 300),
        );

        elements.form.on("change", "input, select, textarea", () => {
            state.isDirty = true;
        });
    }

    function debounce(func, wait) {
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

    function sanitizeHTML(html) {
        const temp = document.createElement("div");
        temp.textContent = html;
        return temp.innerHTML;
    }

    function sanitizeInput(value) {
        if (typeof value !== "string") return value;
        return value.replace(/[<>]/g, "");
    }

    function parseNumber(value) {
        const cleaned = String(value).replace(/[^0-9.-]/g, "");
        const parsed = parseFloat(cleaned);
        return isNaN(parsed) ? 0 : parsed;
    }

    function parseDateInput(value) {
        if (!value) return null;

        if (value instanceof Date) {
            if (isNaN(value.getTime())) return null;
            return new Date(
                value.getFullYear(),
                value.getMonth(),
                value.getDate(),
            );
        }

        const str = String(value).trim();

        const isoMatch = str.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (isoMatch) {
            const [, year, month, day] = isoMatch;
            return new Date(Number(year), Number(month) - 1, Number(day));
        }

        const slashMatch = str.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
        if (slashMatch) {
            const [, month, day, year] = slashMatch;
            return new Date(Number(year), Number(month) - 1, Number(day));
        }

        const parsed = new Date(str);
        if (isNaN(parsed.getTime())) return null;
        return new Date(
            parsed.getFullYear(),
            parsed.getMonth(),
            parsed.getDate(),
        );
    }

    function formatDateForInput(date) {
        if (!(date instanceof Date) || isNaN(date.getTime())) return "";

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");

        return `${year}-${month}-${day}`;
    }

    function numberWithCommas(value, decimals = 2) {
        if (!value && value !== 0) return "";
        const num = typeof value === "number" ? value : parseNumber(value);
        return num.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function removeCommas(value) {
        return String(value).replace(/,/g, "");
    }

    function areDecimalsEqual(num1, num2, tolerance = 0.01) {
        return Math.abs(parseFloat(num1) - parseFloat(num2)) <= tolerance;
    }

    function formatAmountField() {
        const $input = $(this);
        return $input.val();
    }

    function validateNumericInput(value, min = null, max = null) {
        const num = parseNumber(value);

        if (min !== null && num < min) return false;
        if (max !== null && num > max) return false;

        return !isNaN(num);
    }

    function setupBeforeUnloadWarning() {
        window.addEventListener("beforeunload", (e) => {
            if (state.isDirty && !state.isSubmitting) {
                e.preventDefault();
                return "You have unsaved changes. Are you sure you want to leave?";
            }
        });
    }

    function makeAjaxRequest(options) {
        const {
            url,
            method = "GET",
            data = {},
            cache = false,
            cacheKey = null,
            successCallback,
            errorCallback,
            completeCallback,
            showLoading = true,
            loadingMessage = "Loading...",
        } = options;

        if (cache && cacheKey && state.cachedData.has(cacheKey)) {
            const cachedResult = state.cachedData.get(cacheKey);
            if (successCallback) successCallback(cachedResult);
            return Promise.resolve(cachedResult);
        }

        if (cacheKey && state.activeRequests.has(cacheKey)) {
            state.activeRequests.get(cacheKey).abort();
        }

        if (showLoading) {
            showLoader(loadingMessage);
        }

        const xhr = $.ajax({
            url: url,
            method: method,
            data: data,
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "X-Requested-With": "XMLHttpRequest",
            },
            timeout: 30000,
            success: function (response) {
                if (cache && cacheKey) {
                    state.cachedData.set(cacheKey, response);

                    setTimeout(() => {
                        state.cachedData.delete(cacheKey);
                    }, 300000);
                }

                if (successCallback) {
                    successCallback(response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", {
                    url: url,
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error: error,
                });

                if (xhr.status === 401) {
                    Swal.fire({
                        icon: "error",
                        title: "Session Expired",
                        text: "Your session has expired. Please log in again.",
                    }).then(() => {
                        window.location.href = "/login";
                    });
                    return;
                }

                if (xhr.status === 403) {
                    toastr.error(
                        "You do not have permission to perform this action",
                    );
                    return;
                }

                if (xhr.status === 419) {
                    toastr.error(
                        "CSRF token mismatch. Please refresh the page.",
                    );
                    return;
                }

                if (errorCallback) {
                    errorCallback(xhr, status, error);
                } else {
                    const message =
                        xhr.responseJSON?.message ||
                        "An error occurred. Please try again.";
                    toastr.error(message);
                }
            },
            complete: function () {
                if (cacheKey) {
                    state.activeRequests.delete(cacheKey);
                }

                if (showLoading) {
                    hideLoader();
                }

                if (completeCallback) {
                    completeCallback();
                }
            },
        });

        if (cacheKey) {
            state.activeRequests.set(cacheKey, xhr);
        }

        return xhr;
    }

    function handleBusinessTypeChange() {
        const bustype = $(this).val();

        hideSections();

        if (!bustype) {
            const treatySelect = $("#treatytype");
            treatySelect.empty().append('<option value="">Select Treaty Type</option>');
            treatySelect.trigger("change");
            return;
        }

        const sectionMap = {
            FPR: showFacSection,
            FNP: showFacSection,
            TPR: showTreatyProportionalSection,
            TNP: showTreatyNonProportionalSection,
        };

        const showSection = sectionMap[bustype];

        if (showSection) {
            showSection();
        }

        const shouldAutoPopulateTreatyType =
            !state.hasInitializedTreatyType &&
            config.trans_type !== "NEW" &&
            !!config.oldData;

        const preferredTreatyType = shouldAutoPopulateTreatyType
            ? config.oldData.treaty_type ||
              config.oldData.treaty_code ||
              config.oldData.treatytype ||
              ""
            : "";

        loadTreatyTypes(bustype, preferredTreatyType);

        if (shouldAutoPopulateTreatyType) {
            state.hasInitializedTreatyType = true;
        }
    }

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

    function handleCurrencyChange() {
        const currencyCode = $(this).val();

        if (!currencyCode) {
            elements.todayCurrency.val("");
            return;
        }

        makeAjaxRequest({
            url: config.routes.getTodaysRate,
            data: { currency_code: currencyCode },
            cache: true,
            cacheKey: `currency_${currencyCode}`,
            loadingMessage: "Loading exchange rate...",
            successCallback: function (response) {
                const data =
                    typeof response === "string"
                        ? JSON.parse(response)
                        : response;

                if (data.valid === 2) {
                    elements.todayCurrency.val("1.00");
                } else if (data.valid === 1) {
                    elements.todayCurrency.val(numberWithCommas(data.rate));
                } else {
                    elements.todayCurrency.val("");
                    toastr.warning("Currency rate for today not yet set");
                }
            },
        });
    }

    function handleEmlChange() {
        const applyEml = $(this).val();

        $(".eml-field").hide();
        $("#eml_rate, #eml_amt").prop("required", false);

        if (applyEml === "Y") {
            $(".eml-field").show();
            $("#eml_rate, #eml_amt").prop("required", true);

            if (!$("#eml_rate").val()) {
                $("#eml_rate").val(100);
            }

            calculateEml();
        } else {
            $("#eml_rate").val("");
            $("#eml_amt").val("");

            const totalSumInsured = $("#total_sum_insured").val();
            $("#effective_sum_insured").val(totalSumInsured);
        }
    }

    function handleClassGroupChange() {
        const classGroup = $(this).val();

        elements.classCode
            .empty()
            .append('<option value="">Select Class Name</option>');

        if (!classGroup) return;

        makeAjaxRequest({
            url: config.routes.getClasses,
            data: { class_group: classGroup },
            cache: true,
            cacheKey: `classes_${classGroup}`,
            loadingMessage: "Loading classes...",
            successCallback: function (response) {
                const classes = Array.isArray(response)
                    ? response
                    : JSON.parse(response);

                if (!classes || classes.length === 0) {
                    elements.classCode.append(
                        '<option value="">No classes available</option>',
                    );
                    return;
                }

                classes.forEach((cls) => {
                    elements.classCode.append(
                        $("<option>")
                            .val(cls.class_code)
                            .text(`${cls.class_code} - ${cls.class_name}`),
                    );
                });

                elements.classCode.trigger("change.select2");
            },
        });
    }

    function handleFormSubmit(e) {
        e.preventDefault();

        if (state.isSubmitting) {
            return false;
        }

        if (!elements.form.valid()) {
            toastr.error("Please correct the errors before submitting");
            scrollToFirstError();
            return false;
        }

        // if (!validateCommissionSections()) {
        //     return false;
        // }

        // // if (isInstallmentPayment() && !validateInstallments()) {
        // //     return false;
        // // }

        // // if (!validateBusinessTypeRequirements()) {
        // //     return false;
        // // }

        const isEditMode = config.trans_type === "EDIT";
        const confirmTitle = isEditMode
            ? "Confirm Update"
            : "Confirm Submission";
        const confirmText = isEditMode
            ? "Do you want to update this cover registration?"
            : "Do you want to submit this cover registration?";
        const confirmButtonText = isEditMode ? "Yes, update" : "Yes, submit";

        Swal.fire({
            title: confirmTitle,
            text: confirmText,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: confirmButtonText,
            cancelButtonText: "Cancel",
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-secondary",
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    }

    function submitForm() {
        state.isSubmitting = true;
        state.isDirty = false;

        const riskDetails = $("#risk_details_content").html();

        $("#hidden_risk_details").val(riskDetails);

        showLoader("Submitting cover registration...");

        const formData = new FormData(elements.form[0]);

        $.ajax({
            url: elements.form.attr("action"),
            method: elements.form.attr("method") || "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "X-Requested-With": "XMLHttpRequest",
            },
            timeout: 60000,
            success: function (response) {
                hideLoader();
                state.isSubmitting = false;

                if (response.success) {
                    // console.log(response);
                    const successText =
                        config.trans_type === "EDIT"
                            ? "Cover updated successfully"
                            : "Cover registered successfully";
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: successText,
                        confirmButtonText: "OK",
                    }).then(() => {
                        if (response.data.redirectUrl) {
                            window.location.href = response.data.redirectUrl;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: "Warning",
                        text:
                            response.message ||
                            "Cover registration completed with warnings",
                    });
                    state.isSubmitting = false;
                    state.isDirty = true;
                }
            },
            error: function (xhr, status, error) {
                hideLoader();
                state.isSubmitting = false;
                state.isDirty = true;

                if (xhr.status === 422) {
                    handleValidationErrors(xhr.responseJSON);
                } else if (xhr.status === 401) {
                    Swal.fire({
                        icon: "error",
                        title: "Session Expired",
                        text: "Your session has expired. Please log in again.",
                    }).then(() => {
                        window.location.href = "/login";
                    });
                } else if (xhr.status === 403) {
                    Swal.fire({
                        icon: "error",
                        title: "Access Denied",
                        text: "You do not have permission to perform this action.",
                    });
                } else if (xhr.status === 419) {
                    Swal.fire({
                        icon: "error",
                        title: "CSRF Token Mismatch",
                        text: "Your session token has expired. Please refresh the page and try again.",
                        confirmButtonText: "Refresh Page",
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (xhr.status === 500) {
                    Swal.fire({
                        icon: "error",
                        title: "Server Error",
                        text:
                            xhr.responseJSON?.message ||
                            "An internal server error occurred. Please try again or contact support.",
                        footer: xhr.responseJSON?.trace_id
                            ? `<small>Error ID: ${xhr.responseJSON.trace_id}</small>`
                            : "",
                    });
                } else if (status === "timeout") {
                    Swal.fire({
                        icon: "error",
                        title: "Request Timeout",
                        text: "The request took too long to complete. Please check your connection and try again.",
                    });
                } else if (status === "abort") {
                    toastr.info("Request cancelled");
                } else {
                    const errorMessage =
                        xhr.responseJSON?.message ||
                        xhr.responseJSON?.error ||
                        "An error occurred while submitting the form. Please try again.";

                    Swal.fire({
                        icon: "error",
                        title: "Submission Failed",
                        text: errorMessage,
                    });
                }
            },
        });
    }

    function handleValidationErrors(response) {
        const errors = response.errors || {};
        const errorMessages = [];

        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();

        Object.keys(errors).forEach((fieldName) => {
            const fieldErrors = errors[fieldName];
            const $field = $(`[name="${fieldName}"]`);

            if ($field.length) {
                $field.addClass("is-invalid");

                const errorHtml = `<div class="invalid-feedback d-block">${fieldErrors[0]}</div>`;

                if ($field.parent(".input-group").length) {
                    $field.parent().after(errorHtml);
                } else {
                    $field.after(errorHtml);
                }

                errorMessages.push(`${fieldName}: ${fieldErrors[0]}`);
            } else {
                errorMessages.push(`${fieldName}: ${fieldErrors[0]}`);
            }
        });

        const $firstError = $(".is-invalid:first");
        if ($firstError.length) {
            $("html, body").animate(
                {
                    scrollTop: $firstError.offset().top - 100,
                },
                500,
            );
            $firstError.focus();
        }

        const errorSummary =
            errorMessages.length <= 3
                ? errorMessages.join("<br>")
                : `${errorMessages.length} validation errors found. Please check the form.`;

        Swal.fire({
            icon: "error",
            title: "Validation Error",
            html: errorSummary,
            confirmButtonText: "OK",
        });
    }

    // function submitForm() {
    //     state.isSubmitting = true;
    //     state.isDirty = false;

    //     const riskDetails = $("#risk_details_content").html();
    //     // const sanitized = DOMPurify
    //     //     ? DOMPurify.sanitize(riskDetails, {
    //     //           ALLOWED_TAGS: [
    //     //               "p",
    //     //               "br",
    //     //               "strong",
    //     //               "em",
    //     //               "u",
    //     //               "ul",
    //     //               "ol",
    //     //               "li",
    //     //               "table",
    //     //               "tr",
    //     //               "td",
    //     //               "th",
    //     //           ],
    //     //           ALLOWED_ATTR: ["class", "style"],
    //     //       })
    //     //     : riskDetails;

    //     $("#hidden_risk_details").val(riskDetails);

    //     showLoader("Submitting cover registration...");

    //     elements.form.submit();
    // }

    function validateBusinessTypeRequirements() {
        const busType = elements.typeOfBus.val();

        if (!busType) {
            toastr.error("Please select a business type");
            return false;
        }

        if (["FPR", "FNP"].includes(busType)) {
            const cedantPremium = parseNumber($("#cede_premium").val());
            const shareOffered = parseNumber($("#fac_share_offered").val());

            if (cedantPremium <= 0) {
                toastr.error("Cedant premium must be greater than zero");
                return false;
            }

            if (shareOffered <= 0 || shareOffered > 100) {
                toastr.error("Share offered must be between 1 and 100%");
                return false;
            }
        }

        if (["TPR", "TNP"].includes(busType)) {
            const hasReinClass =
                $(".treaty_reinclass").filter(function () {
                    return $(this).val() !== "";
                }).length > 0;

            if (!hasReinClass) {
                toastr.error("Please add at least one reinsurance class");
                return false;
            }
        }

        return true;
    }

    function scrollToFirstError() {
        const $firstError = $(".is-invalid:first");
        if ($firstError.length) {
            $("html, body").animate(
                {
                    scrollTop: $firstError.offset().top - 100,
                },
                500,
            );
            $firstError.focus();
        }
    }

    function validateDates() {
        const coverFrom = parseDateInput($("#coverfrom").val());
        const coverTo = parseDateInput($("#coverto").val());

        if (!$("#coverfrom").val() || !$("#coverto").val()) {
            return;
        }

        if (!coverFrom || !coverTo || coverFrom >= coverTo) {
            Swal.fire({
                icon: "error",
                title: "Invalid Date Range",
                text: "Cover end date must be after cover start date",
            });

            $("#coverto").val("").addClass("is-invalid");
            return false;
        }

        $("#coverfrom, #coverto").removeClass("is-invalid");
        return true;
    }

    function addInstallments() {
        const noOfInstallments = parseInt($("#no_of_installments").val());

        // Validation
        if (!noOfInstallments || noOfInstallments < 1) {
            toastr.error("Please enter a valid number of installments");
            return false;
        }

        if (noOfInstallments > config.maxInstallments) {
            toastr.error(
                `Maximum ${config.maxInstallments} installments allowed`,
            );
            return false;
        }

        if (!validateInstallmentPrerequisites()) {
            return false;
        }

        const instalAmount = computeInstallmentAmount();
        config.installmentTotalAmount = parseFloat(instalAmount);

        if (config.installmentTotalAmount <= 0) {
            toastr.error("Invalid installment amount calculated");
            return false;
        }

        const installmentAmount = (
            config.installmentTotalAmount / noOfInstallments
        ).toFixed(2);

        $("#fac-installments-section").empty();

        for (let i = 1; i <= noOfInstallments; i++) {
            const row = createInstallmentRow(i, installmentAmount);
            $("#fac-installments-section").append(row);
        }

        elements.installmentSection.show();
        toastr.success(`${noOfInstallments} installments added successfully`);
    }

    function validateInstallmentPrerequisites() {
        const required = [
            { selector: "#cede_premium", label: "cedant premium" },
            { selector: "#fac_share_offered", label: "share offered" },
            { selector: "#comm_rate", label: "commission rate" },
        ];

        for (const item of required) {
            const value = parseNumber($(item.selector).val());
            if (!value || value <= 0) {
                toastr.error(`Please enter ${item.label}`);
                $(item.selector).focus();
                return false;
            }
        }

        return true;
    }

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
                    <label class="form-label required">Due Date</label>
                    <input type="date" name="installment_date[]"
                           id="instl_date_${index}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Amount</label>
                    <input type="text" name="installment_amt[]"
                           id="instl_amnt_${index}"
                           value="${numberWithCommas(amount)}"
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

    function removeInstallment() {
        const $row = $(this).closest(".installment-row");
        const currentCount = $(".installment-row").length;

        if (currentCount === 1) {
            $("#no_of_installments").val("");
            elements.installmentSection.hide();
        } else {
            $("#no_of_installments").val(currentCount - 1);
        }

        $row.remove();
        renumberInstallments();
    }

    function renumberInstallments() {
        $(".installment-row").each(function (index) {
            const newIndex = index + 1;
            $(this).attr("data-count", newIndex);
            $(this).find('input[name="installment_no[]"]').val(newIndex);
            $(this).find("input[readonly]").val(`Installment No. ${newIndex}`);
        });
    }

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
                    totalInstallments,
                )}) ` +
                    `does not match expected amount (${numberWithCommas(
                        expectedAmount,
                    )})`,
            );
            return false;
        }

        // Validate all dates are filled
        let hasEmptyDate = false;
        let hasDuplicateDate = false;
        const dates = [];

        $(".installment-row").each(function () {
            const dateInput = $(this).find('input[name="installment_date[]"]');
            const dateValue = dateInput.val();

            if (!dateValue) {
                hasEmptyDate = true;
                dateInput.addClass("is-invalid");
            } else {
                dateInput.removeClass("is-invalid");

                // Check for duplicate dates
                if (dates.includes(dateValue)) {
                    hasDuplicateDate = true;
                    dateInput.addClass("is-invalid");
                }
                dates.push(dateValue);
            }
        });

        if (hasEmptyDate) {
            toastr.error("Please enter due date for all installments");
            return false;
        }

        if (hasDuplicateDate) {
            toastr.error("Installment dates must be unique");
            return false;
        }

        return true;
    }

    function calculateTotalInstallments() {
        let total = 0;
        $('input[name="installment_amt[]"]').each(function () {
            const value = parseNumber($(this).val());
            total += value;
        });
        return total;
    }

    function computeInstallmentAmount() {
        const shareOffered = parseNumber($("#fac_share_offered").val());
        const rate = parseNumber($("#comm_rate").val());
        const cedantPremium = parseNumber($("#cede_premium").val());

        if (!shareOffered || !cedantPremium) {
            return 0;
        }

        const totalDr = (shareOffered / 100) * cedantPremium;
        const totalCr = (rate / 100) * totalDr;

        return (totalDr - totalCr).toFixed(2);
    }

    function calculateCedantCommission() {
        const rate = parseNumber($(this).val());
        const cedePremium = parseNumber($("#cede_premium").val());

        if (!validateNumericInput(rate, 0, 100)) {
            toastr.error("Commission rate must be between 0 and 100%");
            $(this).val("");
            return;
        }

        const commAmount = (rate / 100) * cedePremium;
        $("#comm_amt").val(numberWithCommas(commAmount));

        calculateBrokerageCommission();
    }

    function calculateReinsurerCommission() {
        const rate = parseNumber($(this).val());
        const reinPremium = parseNumber($("#rein_premium").val());

        if (!validateNumericInput(rate, 0, 100)) {
            toastr.error("Commission rate must be between 0 and 100%");
            $(this).val("");
            return;
        }

        const commAmount = (rate / 100) * reinPremium;
        $("#reins_comm_amt").val(numberWithCommas(commAmount));

        calculateBrokerageCommission();
    }

    function handleCedantPremiumChange() {
        const value = $(this).val();

        $("#comm_rate").trigger("keyup");
        $("#rein_premium").val(value);
    }

    function handleReinsurerCommTypeChange() {
        const commType = $(this).val();

        if (commType === "R") {
            $(".reins-comm-rate-field").show();
            $("#reins_comm_rate")
                .prop("disabled", false)
                .prop("required", true);
            $("#reins_comm_amt").prop("readonly", true).prop("required", false);
        } else if (commType === "A") {
            $(".reins-comm-rate-field").hide();
            $("#reins_comm_rate")
                .prop("disabled", true)
                .prop("required", false);
            $("#reins_comm_amt").prop("readonly", false).prop("required", true);
        }

        if (config.resetableTransTypes.includes(config.trans_type)) {
            $("#reins_comm_amt").val("");
        }
    }

    function handleBrokerageCommTypeChange() {
        const brokerageCommType = $(this).val();

        $(
            ".brokerage-amount-field, .brokerage-rate-field, .brokerage-rate-amount-field",
        ).hide();
        $("#brokerage_comm_amt").prop("disabled", true);
        $("#brokerage_comm_rate, #brokerage_comm_rate_amnt").val("");

        if (brokerageCommType === "R") {
            $(".brokerage-rate-field, .brokerage-rate-amount-field").show();
            calculateBrokerageCommission();
        } else if (brokerageCommType === "A") {
            $(".brokerage-amount-field").show();
            $("#brokerage_comm_amt").prop("disabled", false);
        }
    }

    function calculateBrokerageCommission() {
        const brokerageCommType = $("#brokerage_comm_type").val();

        if (brokerageCommType !== "R") return;

        const cedantCommRate = parseNumber($("#comm_rate").val());
        const reinCommRate = parseNumber($("#reins_comm_rate").val());
        const reinPremium = parseNumber($("#rein_premium").val());

        const brokerageCommRate = Math.max(0, reinCommRate - cedantCommRate);
        const brokerageCommAmt = (brokerageCommRate / 100) * reinPremium;

        $("#brokerage_comm_rate").val(numberWithCommas(brokerageCommRate));
        $("#brokerage_comm_rate_amnt").val(numberWithCommas(brokerageCommAmt));
    }

    function calculateEml() {
        const emlRate = parseNumber($("#eml_rate").val());
        const totalSumInsured = parseNumber($("#total_sum_insured").val());

        if (!emlRate || !totalSumInsured) {
            $("#effective_sum_insured").val(numberWithCommas(totalSumInsured));
            return;
        }

        if (!validateNumericInput(emlRate, 0, 100)) {
            toastr.error("EML rate must be between 0 and 100%");
            $("#eml_rate").val("");
            return;
        }

        const emlAmt = totalSumInsured * (emlRate / 100);

        $("#eml_amt").val(numberWithCommas(emlAmt));
        $("#effective_sum_insured").val(numberWithCommas(emlAmt));
    }

    function calculateQuotaRetention() {
        const $input = $(this);
        const row = $input.closest("[data-counter]");
        const counter = row.data("counter");

        const retentionPer = parseNumber($(`#retention_per-${counter}`).val());
        const quotaLimit = parseNumber(
            $(`#quota_share_total_limit-${counter}`).val(),
        );

        if (!retentionPer || !quotaLimit) return;

        if (!validateNumericInput(retentionPer, 0, 100)) {
            toastr.error("Retention percentage must be between 0 and 100%");
            $(`#retention_per-${counter}`).val("");
            return;
        }

        const treatyPer = 100 - retentionPer;
        const retentionAmt = (retentionPer / 100) * quotaLimit;
        const treatyLimit = (treatyPer / 100) * quotaLimit;

        $(`#treaty_reice-${counter}`).val(treatyPer.toFixed(2));
        $(`#quota_retention_amt-${counter}`).val(
            numberWithCommas(retentionAmt),
        );
        $(`#quota_treaty_limit-${counter}`).val(numberWithCommas(treatyLimit));

        $(`#surp_retention_amt-${counter}`).val(numberWithCommas(retentionAmt));
        $(`#no_of_lines-${counter}`).trigger("keyup");
    }

    function calculateSurplusLimit() {
        const $input = $(this);
        const row = $input.closest("[data-counter]");
        const counter = row.data("counter");

        const lines = parseNumber($(`#no_of_lines-${counter}`).val());
        const retention = parseNumber(
            $(`#surp_retention_amt-${counter}`).val(),
        );

        if (!lines || !retention) return;

        if (lines < 1 || lines > 100) {
            toastr.error("Number of lines must be between 1 and 100");
            $(`#no_of_lines-${counter}`).val("");
            return;
        }

        const treatyLimit = lines * retention;
        $(`#surp_treaty_limit-${counter}`).val(numberWithCommas(treatyLimit));

        const treatyCapacity = treatyLimit + retention;
        $(`#surp_treaty_capacity-${counter}`).val(
            numberWithCommas(treatyCapacity),
        );
    }

    function handleCommissionTypeChange() {
        const $select = $(this);
        const $parent = $select.closest(".comm-sections");
        const selectedType = $select.val();
        const classCounter = $parent.data("class-counter");
        const counter = $parent.data("counter");

        $parent.find(".flat_rate_div, .sliding_scale_div").hide();
        $parent.find(".prem_type_comm_rate").prop("required", false);

        $parent.find(".is-invalid").removeClass("is-invalid");
        $parent.find(".invalid-feedback").remove();

        switch (selectedType) {
            case "FLAT":
                $parent.find(".flat_rate_div").show();
                $parent
                    .find(".flat_rate_div .prem_type_comm_rate")
                    .prop("required", true)
                    .prop("readonly", false);

                $parent.find(".sliding_scale_data").val("");
                $parent.find(".sliding-preview").remove();
                break;

            case "SLIDING":
                $parent.find(".sliding_scale_div").show();

                const slidingData = $parent.find(".sliding_scale_data").val();
                if (
                    !slidingData ||
                    slidingData === "[]" ||
                    slidingData === ""
                ) {
                    $parent
                        .find(".configure-sliding-btn")
                        .addClass("btn-warning")
                        .removeClass("btn-outline-secondary");
                }

                $parent.find(".flat_rate_div .prem_type_comm_rate").val("");
                break;

            default:
                break;
        }
    }

    function handleSlidingScaleConfig() {
        const classCounter = $(this).data("class-counter");
        const counter = $(this).data("counter");
        config.currentModalTarget = `#sliding_scale_data-${classCounter}-${counter}`;

        const existingData = $(config.currentModalTarget).val();

        if (existingData && existingData !== "[]" && existingData !== "") {
            try {
                const data = JSON.parse(existingData);
                loadSlidingScaleData(data);
            } catch (e) {
                console.error("Error parsing sliding scale data:", e);
                resetSlidingScaleTable();
            }
        } else {
            resetSlidingScaleTable();
        }

        $("#slidingScaleModal").modal("show");
    }

    function loadSlidingScaleData(data) {
        resetSlidingScaleTable();

        if (Array.isArray(data) && data.length > 0) {
            $("#sliding-scale-rows").empty();
            data.forEach((tier) => addSlidingScaleTier(tier));
        }
    }

    function resetSlidingScaleTable() {
        $("#sliding-scale-rows").html(`
            <tr class="sliding-scale-row">
                <td>
                    <input type="number" class="form-control loss-ratio-min"
                           min="0" max="100" step="0.01" placeholder="0.00" required>
                </td>
                <td>
                    <input type="number" class="form-control loss-ratio-max"
                           min="0" max="100" step="0.01" placeholder="100.00" required>
                </td>
                <td>
                    <input type="number" class="form-control commission-rate"
                           min="0" max="100" step="0.01" placeholder="0.00" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-scale-row">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    }

    function addSlidingScaleTier(data = null) {
        const minVal = data ? parseNumber(data.loss_ratio_min) : "";
        const maxVal = data ? parseNumber(data.loss_ratio_max) : "";
        const commVal = data ? parseNumber(data.commission_rate) : "";

        const html = `
            <tr class="sliding-scale-row">
                <td>
                    <input type="number" class="form-control loss-ratio-min"
                           min="0" max="100" step="0.01" value="${minVal}" placeholder="0.00" required>
                </td>
                <td>
                    <input type="number" class="form-control loss-ratio-max"
                           min="0" max="100" step="0.01" value="${maxVal}" placeholder="100.00" required>
                </td>
                <td>
                    <input type="number" class="form-control commission-rate"
                           min="0" max="100" step="0.01" value="${commVal}" placeholder="0.00" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-scale-row">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $("#sliding-scale-rows").append(html);
    }

    function removeSlidingScaleTier() {
        const rowCount = $("#sliding-scale-rows .sliding-scale-row").length;

        if (rowCount > 1) {
            $(this).closest("tr").remove();
        } else {
            toastr.warning("You must have at least one tier.");
        }
    }

    function saveSlidingScale() {
        const tiers = [];
        let isValid = true;

        $("#sliding-scale-rows .sliding-scale-row").each(function () {
            const minRatio = parseFloat($(this).find(".loss-ratio-min").val());
            const maxRatio = parseFloat($(this).find(".loss-ratio-max").val());
            const commRate = parseFloat($(this).find(".commission-rate").val());

            if (isNaN(minRatio) || isNaN(maxRatio) || isNaN(commRate)) {
                toastr.error("All fields must be filled with valid numbers.");
                isValid = false;
                return false;
            }

            if (
                minRatio < 0 ||
                minRatio > 100 ||
                maxRatio < 0 ||
                maxRatio > 100 ||
                commRate < 0 ||
                commRate > 100
            ) {
                toastr.error("All values must be between 0 and 100.");
                isValid = false;
                return false;
            }

            if (minRatio >= maxRatio) {
                toastr.error(
                    "Loss Ratio Min must be less than Loss Ratio Max.",
                );
                isValid = false;
                return false;
            }

            tiers.push({
                loss_ratio_min: minRatio,
                loss_ratio_max: maxRatio,
                commission_rate: commRate,
            });
        });

        if (!isValid || tiers.length === 0) return;

        tiers.sort((a, b) => a.loss_ratio_min - b.loss_ratio_min);

        for (let i = 0; i < tiers.length - 1; i++) {
            if (tiers[i].loss_ratio_max > tiers[i + 1].loss_ratio_min) {
                toastr.error("Loss ratio ranges cannot overlap.");
                return;
            }
        }

        if (tiers[0].loss_ratio_min !== 0) {
            toastr.error("Sliding scale must start from 0%.");
            return;
        }

        if (tiers[tiers.length - 1].loss_ratio_max !== 100) {
            toastr.error("Sliding scale must end at 100%.");
            return;
        }

        $(config.currentModalTarget).val(JSON.stringify(tiers));

        const $btn = $(config.currentModalTarget)
            .closest(".sliding_scale_div")
            .find(".configure-sliding-btn");
        $btn.html(
            `<i class="bx bx-trending-up me-1"></i> Edit Scale (${tiers.length} tiers)`,
        );

        createSlidingScalePreview(config.currentModalTarget, tiers);

        $("#slidingScaleModal").modal("hide");
        toastr.success("Sliding scale saved successfully!");
    }

    function createSlidingScalePreview(targetId, tiers) {
        const $parent = $(targetId).closest(".comm-sections");
        $parent.find(".sliding-preview").remove();

        let html =
            '<div class="alert alert-success alert-sm mt-2 sliding-preview">';
        html += "<strong>Sliding Scale Tiers:</strong> ";

        tiers.forEach((tier) => {
            html += `<span class="badge bg-success me-1">
                ${tier.loss_ratio_min.toFixed(2)}-${tier.loss_ratio_max.toFixed(
                    2,
                )}%:
                ${tier.commission_rate.toFixed(2)}%
            </span>`;
        });

        html += "</div>";
        $parent.append(html);
    }

    function loadSlidingTemplate() {
        const template = $(this).data("template");

        const templates = {
            standard: [
                { loss_ratio_min: 0, loss_ratio_max: 40, commission_rate: 25 },
                { loss_ratio_min: 40, loss_ratio_max: 70, commission_rate: 20 },
                {
                    loss_ratio_min: 70,
                    loss_ratio_max: 100,
                    commission_rate: 15,
                },
            ],
            aggressive: [
                { loss_ratio_min: 0, loss_ratio_max: 30, commission_rate: 30 },
                { loss_ratio_min: 30, loss_ratio_max: 50, commission_rate: 25 },
                { loss_ratio_min: 50, loss_ratio_max: 65, commission_rate: 20 },
                { loss_ratio_min: 65, loss_ratio_max: 80, commission_rate: 15 },
                {
                    loss_ratio_min: 80,
                    loss_ratio_max: 100,
                    commission_rate: 10,
                },
            ],
        };

        const tiers = templates[template];

        if (tiers) {
            resetSlidingScaleTable();
            $("#sliding-scale-rows").empty();
            tiers.forEach((tier) => addSlidingScaleTier(tier));
            toastr.success(
                `${
                    template.charAt(0).toUpperCase() + template.slice(1)
                } template loaded!`,
            );
        }
    }

    function handleSlidingCSVImport(e) {
        const file = e.target.files[0];

        if (!file) return;

        if (!file.name.endsWith(".csv")) {
            toastr.error("Please select a CSV file");
            return;
        }

        if (file.size > 1048576) {
            toastr.error("File size must be less than 1MB");
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            try {
                const csv = e.target.result;
                parseSlidingScaleCSV(csv);
            } catch (error) {
                console.error("CSV parsing error:", error);
                toastr.error("Failed to parse CSV file");
            }
        };
        reader.onerror = function () {
            toastr.error("Failed to read file");
        };
        reader.readAsText(file);
    }

    function calculateCover() {
        const startVal = $("#coverfrom").val();
        const endVal = $("#coverto").val();

        if (!startVal) {
            $("#cover_duration").val("");
            return;
        }

        const start = parseDateInput(startVal);
        let end = endVal ? parseDateInput(endVal) : null;

        if (!start) {
            $("#cover_duration").val("");
            return;
        }

        if (!endVal) {
            end = new Date(start);
            end.setFullYear(end.getFullYear() + 1);
            end.setDate(end.getDate() - 1);

            $("#coverto").val(formatDateForInput(end));
        }

        if (end && !isNaN(end.getTime())) {
            const diffMs = end - start;
            const days = Math.ceil(diffMs / (1000 * 60 * 60 * 24));
            $("#cover_duration").val(days);
        }
    }

    function parseSlidingScaleCSV(csv) {
        const lines = csv.split("\n").filter((line) => line.trim() !== "");

        resetSlidingScaleTable();
        $("#sliding-scale-rows").empty();

        let imported = 0;
        const errors = [];

        const startIndex =
            lines[0].toLowerCase().includes("loss") ||
            lines[0].toLowerCase().includes("ratio")
                ? 1
                : 0;

        for (let i = startIndex; i < lines.length; i++) {
            const parts = lines[i].split(",").map((p) => p.trim());

            if (parts.length < 3) {
                errors.push(`Line ${i + 1}: Insufficient columns`);
                continue;
            }

            const minRatio = parseFloat(parts[0]);
            const maxRatio = parseFloat(parts[1]);
            const commRate = parseFloat(parts[2]);

            if (isNaN(minRatio) || isNaN(maxRatio) || isNaN(commRate)) {
                errors.push(`Line ${i + 1}: Invalid numeric values`);
                continue;
            }

            if (
                minRatio < 0 ||
                minRatio > 100 ||
                maxRatio < 0 ||
                maxRatio > 100 ||
                commRate < 0 ||
                commRate > 100
            ) {
                errors.push(`Line ${i + 1}: Values must be between 0 and 100`);
                continue;
            }

            if (minRatio >= maxRatio) {
                errors.push(`Line ${i + 1}: Min must be less than Max`);
                continue;
            }

            addSlidingScaleTier({
                loss_ratio_min: minRatio,
                loss_ratio_max: maxRatio,
                commission_rate: commRate,
            });
            imported++;
        }

        if (errors.length > 0) {
            console.warn("CSV import errors:", errors);
        }

        if (imported > 0) {
            toastr.success(`Imported ${imported} sliding scale tier(s)!`);
        } else {
            toastr.error("No valid data found in CSV file.");
        }

        $("#sliding-csv-file").val("");
    }

    $("#createNewInsurer").on("click", function (e) {
        e.preventDefault();
        window.open("/customer/customer-new", "_blank", "noopener,noreferrer");
    });

    $("#addInsurerModal").on("shown.bs.modal", function () {
        $(".select2-modal").select2({
            dropdownParent: $("#addInsurerModal"),
            width: "100%",
        });
    });

    $("#addInsurerForm").on("submit", function (e) {
        e.preventDefault();

        // Hide previous alerts
        $(".alert").addClass("d-none");

        // Basic validation
        let isValid = true;
        if ($("#insurer_name").val().trim() === "") {
            $("#insurer_name").addClass("is-invalid");
            isValid = false;
        } else {
            $("#insurer_name").removeClass("is-invalid");
        }

        if (!isValid) return false;

        // Disable submit button
        $("#saveInsurerBtn")
            .prop("disabled", true)
            .html('<i class="bx bx-loader bx-spin me-1"></i>Saving...');

        // AJAX request
        //{{-- $.ajax({
        //     url: '{{ route('insured.store') }}', // Adjust route name as needed
        //     method: 'POST',
        //     data: $(this).serialize(),
        //     success: function(response) {
        //         if (response.success) {
        //             // Show success message
        //             $('#insurerSuccessMessage').text(response.message ||
        //                 'Insurer added successfully!');
        //             $('#insurerSuccessAlert').removeClass('d-none');

        //             // Add new option to select
        //             const newOption = new Option(response.insurer.name, response.insurer
        //                 .name, true, true);
        //             $('#insured_name').append(newOption).trigger('change');

        //             // Close modal after 1.5 seconds
        //             setTimeout(function() {
        //                 $('#addInsurerModal').modal('hide');
        //                 $('#addInsurerForm')[0].reset();
        //                 $('#saveInsurerBtn').prop('disabled', false).html(
        //                     '<i class="bx bx-save me-1"></i>Save Insurer');
        //             }, 1500);
        //         }
        //     },
        //     error: function(xhr) {
        //         let errorMessage = 'An error occurred while saving the insurer.';

        //         if (xhr.responseJSON && xhr.responseJSON.message) {
        //             errorMessage = xhr.responseJSON.message;
        //         } else if (xhr.responseJSON && xhr.responseJSON.errors) {
        //             const errors = Object.values(xhr.responseJSON.errors).flat();
        //             errorMessage = errors.join('<br>');
        //         }

        //         $('#insurerErrorMessage').html(errorMessage);
        //         $('#insurerErrorAlert').removeClass('d-none');
        //         $('#saveInsurerBtn').prop('disabled', false).html(
        //             '<i class="bx bx-save me-1"></i>Save Insurer');
        //     }
        // }); --}}
    });

    $("#insurer_name").on("input", function () {
        $(this).removeClass("is-invalid");
    });
    function handleTreatyTypeChange() {
        const treatyType = $(this).val();
        const treatyTypeTxt = $(this).find("option:selected").text();

        // Target ALL sections instead of just the first one
        $(".quota_header_div, .surp_header_div").hide();
        $(
            ".quota_share_total_limit_div, .retention_per_div, .treaty_reice_div",
        ).hide();
        $(".quota_retention_amt_div, .quota_treaty_limit_div").hide();
        $(
            ".no_of_lines_div, .surp_retention_amt_div, .surp_treaty_limit_div, .surp_treaty_capacity_div",
        ).hide();
        $("#reinsurer_per_treaty_section").hide();
        $(".quota-share-section, .surplus-section").hide();

        if (!treatyType) {
            $(".prem_type_treaty")
                .empty()
                .append(
                    $("<option>")
                        .text("-- Select Treaty --")
                        .attr("value", ""),
                )
                .val("")
                .trigger("change.select2");
            return;
        }

        // Update treaty dropdown for ALL commission sections across ALL sections
        $(`.prem_type_treaty`)
            .empty()
            .append(
                $("<option>").text(treatyTypeTxt).attr("value", treatyType),
            )
            .val(treatyType)
            .trigger("change.select2");

        const sectionMap = {
            QUOT: () => {
                $(".quota-share-section").show();
                $(".quota_header_div").show();
                $(
                    ".quota_share_total_limit_div, .retention_per_div, .treaty_reice_div",
                ).show();
                $(".quota_retention_amt_div, .quota_treaty_limit_div").show();
            },
            SURP: () => {
                $(".surplus-section").show();
                $(".surp_header_div").show();
                $(
                    ".no_of_lines_div, .surp_retention_amt_div, .surp_treaty_limit_div, .surp_treaty_capacity_div",
                ).show();
            },
            SPQT: () => {
                $(".quota-share-section, .surplus-section").show();
                $(".quota_header_div, .surp_header_div").show();
                $(
                    ".quota_share_total_limit_div, .retention_per_div, .treaty_reice_div",
                ).show();
                $(".quota_retention_amt_div, .quota_treaty_limit_div").show();
                $(
                    ".no_of_lines_div, .surp_retention_amt_div, .surp_treaty_limit_div, .surp_treaty_capacity_div",
                ).show();
                $("#reinsurer_per_treaty_section").show();
            },
        };

        const showSections = sectionMap[treatyType];
        if (showSections) {
            showSections();
        }
    }

    function handleMethodChange() {
        const method = $(this).val();

        $(".burning_rate_div, .flat_rate_div").hide();
        $(".burning_rate, .flat_rate").prop("disabled", true).val("");

        if (method === "B") {
            $(".burning_rate_div").show();
            $(".burning_rate").prop("disabled", false).prop("required", true);
        } else if (method === "F") {
            $(".flat_rate_div").show();
            $(".flat_rate").prop("disabled", false).prop("required", true);
        }
    }

    function handleReinclassChange() {
        const $select = $(this);
        const counter = $select.data("counter");
        const reinclass = $select.val();
        const treatyType = $("#treatytype").val();

        if (!treatyType) {
            toastr.error("Please select treaty type first");
            $select.val("").trigger("change.select2");
            return false;
        }

        if (!reinclass) {
            return;
        }

        // Check for duplicates
        if (isReinclassAlreadySelected(reinclass, counter)) {
            toastr.error(
                "This Reinsurance Class Group is already selected in another section",
            );
            $select.val("").trigger("change.select2");
            return false;
        }

        $(`#prem_type_reinclass-${counter}-0`).val(reinclass);

        populateTreatyDropdown(counter, treatyType);
        loadPremTypesData(treatyType, counter, reinclass);
    }

    function isReinclassAlreadySelected(reinclass, currentCounter) {
        let isSelected = false;
        $(".treaty_reinclass").each(function () {
            const $this = $(this);
            const counter = $this.data("counter");
            if (counter !== currentCounter && $this.val() === reinclass) {
                isSelected = true;
                return false; // Exit each
            }
        });
        return isSelected;
    }

    function populateTreatyDropdown(counter, treatyType) {
        const $treatySelect = $(`#prem_type_treaty-${counter}-0`);

        if (!$treatySelect.length) {
            return;
        }

        const $treatyTypeSelect = $("#treatytype");
        const treatyCode = $treatyTypeSelect.val();
        const treatyName = $treatyTypeSelect.find("option:selected").text();

        if (!treatyCode) {
            console.warn("No treaty type selected");
            return;
        }

        $treatySelect.empty();
        $treatySelect.append(
            $("<option>")
                .val(treatyCode)
                .text(treatyName)
                .prop("selected", true),
        );

        $treatySelect.trigger("change.select2");
    }

    function addReinClass() {
        const lastSection = $(".reinclass-section").last();
        const prevCounter = parseInt(lastSection.attr("data-counter"));

        const reinClassVal = $(`#treaty_reinclass-${prevCounter}`).val();
        if (!reinClassVal) {
            const sectionLabel = String.fromCharCode(65 + prevCounter);
            toastr.error(
                `Please select reinsurance class in Section ${sectionLabel}`,
            );
            return false;
        }

        const currentSections = $(".reinclass-section").length;
        if (currentSections >= config.maxReinClasses) {
            toastr.error(
                `Maximum ${config.maxReinClasses} reinsurance classes allowed`,
            );
            return false;
        }

        const counter = prevCounter + 1;
        const newSection = lastSection.clone(true, false);

        newSection.attr("id", `reinclass-section-${counter}`);
        newSection.attr("data-counter", counter);

        newSection.find(".select2-container").remove();

        const sectionLabel = String.fromCharCode(65 + counter);
        newSection
            .find(".section-title")
            .html(`<i class="bx bx-layer me-2"></i> Section ${sectionLabel}`);

        newSection.find('input[type="text"], input[type="number"]').val("");
        newSection.find("select").val("").prop("selectedIndex", 0);

        updateSectionIds(newSection, counter);

        newSection.find(".comm-sections").slice(1).remove();

        if (!newSection.find(".remove-rein-class").length) {
            newSection.find(".section-title").parent().append(`
                <button type="button" class="btn btn-danger btn-sm remove-rein-class">
                    <i class="bx bx-trash me-1"></i> Remove Section
                </button>
            `);
        }

        lastSection.after(newSection);

        // Re-initialize Select2 with explicit width for proper alignment
        newSection.find(".select2").each(function () {
            $(this).select2({
                width: "100%",
                theme: "bootstrap-5",
                dropdownParent: $(this).closest(".cover-card").length
                    ? $(this).closest(".cover-card")
                    : null,
            });
        });

        toastr.success(`Section ${sectionLabel} added`);
    }

    function removeReinClass() {
        const $section = $(this).closest(".reinclass-section");
        const counter = $section.data("counter");
        const sectionLabel = String.fromCharCode(65 + counter);

        Swal.fire({
            title: "Remove Section?",
            text: `Are you sure you want to remove Section ${sectionLabel}?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, remove it",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $section.remove();
                renumberReinclassSections();
                toastr.success(`Section ${sectionLabel} removed`);
            }
        });
    }

    function renumberReinclassSections() {
        $(".reinclass-section").each(function (index) {
            const sectionLabel = String.fromCharCode(65 + index);
            $(this).attr("data-counter", index);
            $(this).find(".section-title").text(`Section ${sectionLabel}`);

            updateSectionIds($(this), index);
        });
    }

    function updateSectionIds($section, counter) {
        $section
            .find("[id], [data-counter], [data-class-counter]")
            .each(function () {
                const $el = $(this);

                const id = $el.attr("id");
                if (id) {
                    // Match prefix-DIGIT-DIGIT and replace only the first index (section index)
                    if (id.match(/-\d+-\d+$/)) {
                        const newId = id.replace(
                            /-(\d+)-(\d+)$/,
                            `-${counter}-$2`,
                        );
                        $el.attr("id", newId);
                    }
                    // Match prefix-DIGIT and replace it
                    else if (id.match(/-\d+$/)) {
                        const newId = id.replace(/-\d+$/, `-${counter}`);
                        $el.attr("id", newId);
                    }
                }

                if ($el.attr("data-counter") !== undefined) {
                    $el.attr("data-counter", counter);
                }

                if ($el.attr("data-class-counter") !== undefined) {
                    $el.attr("data-class-counter", counter);
                }
            });
    }

    function addCommissionSection() {
        const classCounter = $(this).data("counter");
        const $commContainer = $(`#comm-section-${classCounter}`);
        const lastCommSection = $commContainer.find(".comm-sections:last");
        const prevCounter = lastCommSection.data("counter") || 0;
        const counter = prevCounter + 1;

        const reinclass = $(`#treaty_reinclass-${classCounter}`).val();
        const treatyType = $("#treatytype").val();

        if (!reinclass) {
            toastr.error("Please select reinsurance class");
            return false;
        }

        if (!treatyType) {
            toastr.error("Please select treaty type first");
            return false;
        }

        const prevCommType = $(
            `#commission_type-${classCounter}-${prevCounter}`,
        ).val();
        if (!prevCommType) {
            toastr.error("Please select commission type in previous section");
            return false;
        }

        if (prevCommType === "FLAT") {
            const prevCommRate = $(
                `#prem_type_comm_rate-${classCounter}-${prevCounter}`,
            ).val();
            if (!prevCommRate || parseFloat(prevCommRate) <= 0) {
                toastr.error(
                    "Please enter commission rate in previous section",
                );
                return false;
            }
        } else if (prevCommType === "SLIDING") {
            const prevSlidingData = $(
                `#sliding_scale_data-${classCounter}-${prevCounter}`,
            ).val();
            if (
                !prevSlidingData ||
                prevSlidingData === "[]" ||
                prevSlidingData === ""
            ) {
                toastr.error(
                    "Please configure sliding scale in previous section",
                );
                return false;
            }
        }

        const prevPremType = $(
            `#prem_type_code-${classCounter}-${prevCounter}`,
        ).val();

        if (!prevPremType) {
            toastr.error("Please select premium type in previous section");
            return false;
        }

        const newSection = createCommissionSection(classCounter, counter);
        $commContainer.append(newSection);

        $(
            `#prem_type_treaty-${classCounter}-${counter}, #prem_type_code-${classCounter}-${counter}, #commission_type-${classCounter}-${counter}`,
        ).select2({
            width: "100%",
        });

        loadPremTypeTreaty(classCounter, counter, treatyType);

        $(`#prem_type_reinclass-${classCounter}-${counter}`).val(reinclass);

        loadPremTypeCode(
            classCounter,
            counter,
            treatyType,
            reinclass,
            prevCounter,
        );

        toastr.success("Commission section added");
    }

    function loadPremTypeTreaty(classCounter, premCounter, treatyType) {
        const $treatySelect = $(
            `#prem_type_treaty-${classCounter}-${premCounter}`,
        );
        const treatyName = $("#treatytype").find("option:selected").text();

        $treatySelect.empty();
        $treatySelect.append(
            $("<option>")
                .val(treatyType)
                .text(treatyName)
                .prop("selected", true),
        );

        $treatySelect.trigger("change.select2");
    }

    function loadPremTypeCode(
        classCounter,
        premCounter,
        treatyType,
        reinclass,
    ) {
        const premTypesData =
            config.sectionPremTypesData &&
            config.sectionPremTypesData[classCounter]
                ? config.sectionPremTypesData[classCounter]
                : [];
        const targetSelect = $(
            `#prem_type_code-${classCounter}-${premCounter}`,
        );

        targetSelect.empty();
        targetSelect.append(
            $("<option>")
                .val("")
                .text("-- Select Premium Type --")
                .prop("selected", true),
        );

        if (!premTypesData || premTypesData.length === 0) {
            targetSelect.trigger("change.select2");
            return;
        }

        const currentValue =
            targetSelect.data("current-value") || targetSelect.val();

        const selectedPremTypes = [];
        $(`.prem_type_code[data-class-counter="${classCounter}"]`).each(
            function () {
                const value = $(this).val();
                const selectId = $(this).attr("id");
                const targetId = targetSelect.attr("id");

                if (value && selectId !== targetId) {
                    selectedPremTypes.push(value);
                }
            },
        );

        premTypesData.forEach((premType) => {
            if (!premType.premtype_code || !premType.premtype_name) {
                return;
            }

            const isSelected = selectedPremTypes.includes(
                premType.premtype_code,
            );
            const isCurrentValue = premType.premtype_code === currentValue;

            if (!isSelected || isCurrentValue) {
                const optionText = `${premType.premtype_code} - ${premType.premtype_name}`;

                targetSelect.append(
                    $("<option>")
                        .val(premType.premtype_code)
                        .text(optionText)
                        .attr("data-reinclass", reinclass)
                        .attr("data-treaty", treatyType)
                        .attr("data-name", premType.premtype_name),
                );
            }
        });

        if (currentValue) {
            targetSelect.val(currentValue);
        }

        targetSelect.trigger("change.select2");
    }

    function createCommissionSection(classCounter, premCounter) {
        return `
            <div class="comm-sections"
                id="comm-section-${classCounter}-${premCounter}"
                data-class-counter="${classCounter}"
                data-counter="${premCounter}">
                <div class="row g-3 align-items-end mb-2">
                    <div class="col-md-3 prem_type_treaty_div">
                        <label class="form-label required">Treaty</label>
                        <div class="cover-card">
                            <select class="form-control select2 prem_type_treaty required"
                                    name="prem_type_treaty[]"
                                    id="prem_type_treaty-${classCounter}-${premCounter}"
                                    data-class-counter="${classCounter}"
                                    data-counter="${premCounter}"
                                    required>
                                <option value="">Select Treaty</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 prem_type_code_div">
                        <label class="form-label required">Class Name</label>
                        <input type="hidden"
                            class="prem_type_reinclass"
                            id="prem_type_reinclass-${classCounter}-${premCounter}"
                            name="prem_type_reinclass[]">
                        <div class="cover-card">
                            <select class="form-control select2 prem_type_code required"
                                    name="prem_type_code[]"
                                    id="prem_type_code-${classCounter}-${premCounter}"
                                    data-class-counter="${classCounter}"
                                    data-counter="${premCounter}"
                                    required>
                                <option value="">-- Select Name --</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 comm_type_div">
                        <label class="form-label required">Commission Type</label>
                        <div class="cover-card">
                            <select class="form-control select2 commission_type required"
                                    name="treaty_commission_type[]"
                                    id="commission_type-${classCounter}-${premCounter}"
                                    data-class-counter="${classCounter}"
                                    data-counter="${premCounter}"
                                    required>
                                <option value="">Select Type</option>
                                <option value="FLAT">Flat Rate</option>
                                <option value="SLIDING">Sliding Scale</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 flat_rate_div">
                        <label class="form-label required">Commission (%)</label>
                        <div class="input-group">
                            <input type="text"
                                class="form-control prem_type_comm_rate required"
                                name="flat_prem_type_comm_rate[]"
                                id="prem_type_comm_rate-${classCounter}-${premCounter}"
                                placeholder="0.00">
                            <button class="btn btn-danger remove-comm-section" type="button">
                                <i class="bx bx-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-2 sliding_scale_div" style="display: none;">
                        <label class="form-label">Commission Rate (%)</label>
                        <div class="input-group">
                            <input type="text"
                                class="form-control prem_type_comm_rate"
                                name="sliding_treaty_prem_type_comm_rate[]"
                                id="prem_type_comm_rate-sliding-${classCounter}-${premCounter}"
                                placeholder="0.00"
                                readonly>
                        </div>
                        <small class="text-muted">Average rate from scale</small>
                    </div>

                    <div class="col-md-3 sliding_scale_div" style="display: none;">
                        <label class="form-label required">Configure Scale</label>
                        <div class="input-group">
                            <button type="button"
                                    class="btn btn-outline-secondary btn-block configure-sliding-btn"
                                    data-class-counter="${classCounter}"
                                    data-counter="${premCounter}"
                                    style="width: 86%;">
                                <i class="bx bx-trending-up me-1"></i> Configure Scale
                            </button>
                            <button class="btn btn-danger remove-comm-section" type="button">
                                <i class="bx bx-minus"></i>
                            </button>
                        </div>
                        <small class="text-muted">Rates based on loss ratio</small>
                        <input type="hidden"
                            class="sliding_scale_data"
                            name="sliding_scale_data[]"
                            id="sliding_scale_data-${classCounter}-${premCounter}">
                    </div>
                </div>
            </div>
        `;
    }

    function validateCommissionSections() {
        let isValid = true;
        const errors = [];

        $(".comm-sections").each(function () {
            const $section = $(this);
            const classCounter = $section.data("class-counter");
            const counter = $section.data("counter");
            const sectionLabel = `Section ${String.fromCharCode(
                65 + classCounter,
            )} - Commission ${counter + 1}`;

            const treaty = $section.find(".prem_type_treaty").val();
            if (!treaty) {
                errors.push(`${sectionLabel}: Please select treaty`);
                isValid = false;
            }

            const premType = $section.find(".prem_type_code").val();
            if (!premType) {
                errors.push(`${sectionLabel}: Please select premium type`);
                isValid = false;
            }

            const commType = $section.find(".commission_type").val();
            if (!commType) {
                errors.push(`${sectionLabel}: Please select commission type`);
                isValid = false;
                return;
            }

            if (commType === "FLAT") {
                const commRate = $section
                    .find(".flat_rate_div .prem_type_comm_rate")
                    .val();
                if (!commRate || parseFloat(commRate) <= 0) {
                    errors.push(
                        `${sectionLabel}: Please enter commission rate`,
                    );
                    isValid = false;
                }
            } else if (commType === "SLIDING") {
                const slidingData = $section.find(".sliding_scale_data").val();
                if (
                    !slidingData ||
                    slidingData === "[]" ||
                    slidingData === ""
                ) {
                    errors.push(
                        `${sectionLabel}: Please configure sliding scale`,
                    );
                    isValid = false;
                }
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: "error",
                title: "Commission Validation Failed",
                html: errors.join("<br>"),
                confirmButtonText: "OK",
            });
        }

        return isValid;
    }

    function removeCommissionSection() {
        const $section = $(this).closest(".comm-sections");
        const $container = $section.closest("[id^='comm-section-']");

        const remainingSections = $section.length;
        if (remainingSections > 0) {
            $container.remove();
        }
    }

    function addLayer() {
        const $layerContainer = $("#layer-section");
        const lastLayer = $layerContainer.find(".layer-sections:last");
        const prevCounter = lastLayer.length
            ? parseInt(lastLayer.data("counter"))
            : -1;
        const counter = prevCounter + 1;

        const currentLayers = $layerContainer.find(".layer-sections").length;
        if (currentLayers >= config.maxLayers) {
            toastr.error(`Maximum ${config.maxLayers} layers allowed`);
            return false;
        }

        const newLayer = createLayerSection(counter);
        $layerContainer.append(newLayer);

        $(
            `#limit_per_reinclass-${counter}-0, #reinstatement_type-${counter}-0`,
        ).select2({
            width: "100%",
            theme: "bootstrap-5",
        });

        toastr.success(`Layer ${counter + 1} added successfully`);
    }

    function createLayerSection(counter) {
        return `
            <div class="layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Layer ${counter + 1}</h6>
                    ${
                        counter > 0
                            ? `
                        <button type="button" class="btn btn-danger btn-sm remove-layer-section">
                            <i class="bx bx-trash me-1"></i> Remove Layer
                        </button>
                    `
                            : ""
                    }
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label required">Capture Limits per Class?</label>
                        <div class="card-form">
                            <select class="form-control select2 limit_per_reinclass"
                                    name="limit_per_reinclass[]"
                                    id="limit_per_reinclass-${counter}-0"
                                    required>
                                <option value="">Select Option</option>
                                <option value="N" selected>No</option>
                                <option value="Y">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-2">
                        <label class="form-label required">Reinclass</label>
                        <input type="hidden" class="form-control layer_no"
                               id="layer_no-${counter}-0" name="layer_no[]"
                               value="${counter + 1}" readonly>
                        <input type="hidden" class="form-control nonprop_reinclass"
                               id="nonprop_reinclass-${counter}-0" name="nonprop_reinclass[]"
                               value="ALL" readonly>
                        <input type="text" class="form-control nonprop_reinclass_desc"
                               id="nonprop_reinclass_desc-${counter}-0" name="nonprop_reinclass_desc[]"
                               value="ALL" readonly>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label required">Limit</label>
                        <input type="text" class="form-control amount indemnity_treaty_limit"
                               id="indemnity_treaty_limit-${counter}-0"
                               name="indemnity_treaty_limit[]"
                               placeholder="0.00" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label required">Deductible Amount</label>
                        <input type="text" class="form-control amount underlying_limit"
                               id="underlying_limit-${counter}-0"
                               name="underlying_limit[]"
                               placeholder="0.00" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label required">EGNPI</label>
                        <input type="text" class="form-control amount egnpi"
                               id="egnpi-${counter}-0"
                               name="egnpi[]"
                               placeholder="0.00" required>
                    </div>
                </div>

                <div class="row g-3 mt-2 burning_rate_section" style="display: none;">
                    <div class="col-md-3 burning_rate_div">
                        <label class="form-label">Min BC Rate (%)</label>
                        <input type="text" class="form-control burning_rate"
                               id="min_bc_rate-${counter}-0" name="min_bc_rate[]"
                               placeholder="0.00">
                    </div>

                    <div class="col-md-3 burning_rate_div">
                        <label class="form-label">Max BC Rate (%)</label>
                        <input type="text" class="form-control burning_rate"
                               id="max_bc_rate-${counter}-0" name="max_bc_rate[]"
                               placeholder="0.00">
                    </div>

                    <div class="col-md-3 burning_rate_div">
                        <label class="form-label">Upper Adj (%)</label>
                        <input type="text" class="form-control burning_rate"
                               id="upper_adj-${counter}-0" name="upper_adj[]"
                               placeholder="0.00">
                    </div>

                    <div class="col-md-3 burning_rate_div">
                        <label class="form-label">Lower Adj (%)</label>
                        <input type="text" class="form-control burning_rate"
                               id="lower_adj-${counter}-0" name="lower_adj[]"
                               placeholder="0.00">
                    </div>
                </div>

                <div class="row g-3 mt-2 flat_rate_section" style="display: none;">
                    <div class="col-md-4 flat_rate_div">
                        <label class="form-label">Flat Rate (%)</label>
                        <input type="text" class="form-control flat_rate"
                               id="flat_rate-${counter}-0" name="flat_rate[]"
                               placeholder="0.00">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label class="form-label required">Min Deposit Premium</label>
                        <input type="text" class="form-control amount min_deposit"
                               id="min_deposit-${counter}-0"
                               name="min_deposit[]"
                               placeholder="0.00" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label required">Reinstatement Type</label>
                        <select name="reinstatement_type[]"
                                id="reinstatement_type-${counter}-0"
                                class="form-control select2"
                                required>
                            <option value="">Select Type</option>
                            <option value="NOR">Number of Reinstatements</option>
                            <option value="AAL">Annual Aggregate Limit</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label required">Reinstatement Value</label>
                        <input type="text" class="form-control amount reinstatement_value"
                               id="reinstatement_value-${counter}-0"
                               name="reinstatement_value[]"
                               placeholder="0.00" required>
                    </div>
                </div>

                <hr class="my-3">
            </div>
        `;
    }

    function removeLayer() {
        const $layer = $(this).closest(".layer-sections");
        const counter = $layer.data("counter");

        Swal.fire({
            title: "Remove Layer?",
            text: `Are you sure you want to remove Layer ${counter + 1}?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, remove it",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $layer.remove();
                renumberLayers();
                toastr.success(`Layer ${counter + 1} removed`);
            }
        });
    }

    function renumberLayers() {
        $(".layer-sections").each(function (index) {
            $(this).attr("data-counter", index);
            $(this)
                .find("h6:first")
                .text(`Layer ${index + 1}`);
            $(this)
                .find(".layer_no")
                .val(index + 1);

            updateLayerIds($(this), index);
        });
    }

    function updateLayerIds($layer, counter) {
        $layer.find("[id]").each(function () {
            const $el = $(this);
            const id = $el.attr("id");
            if (id) {
                const newId = id.replace(/-\d+-/, `-${counter}-`);
                $el.attr("id", newId);
            }
        });
    }

    function handleLimitPerReinclassChange() {}

    function loadTreatyTypes(businessType, preferredTreatyType = "") {
        if (!businessType) return;

        makeAjaxRequest({
            url: config.routes.getTreatyTypes,
            data: { type_of_bus: businessType },
            cache: true,
            cacheKey: `treaty_types_${businessType}`,
            loadingMessage: "Loading treaty types...",
            successCallback: function (response) {
                const treatySelect = $("#treatytype");
                treatySelect
                    .empty()
                    .append('<option value="">Select Treaty Type</option>');

                if (!Array.isArray(response) || response.length === 0) {
                    treatySelect.append(
                        '<option value="">No treaty types available</option>',
                    );
                    treatySelect.trigger("change");
                    return;
                }

                response.forEach((treaty) => {
                    treatySelect.append(
                        $("<option>")
                            .val(treaty.treaty_code)
                            .text(
                                `${treaty.treaty_code} - ${treaty.treaty_name}`,
                            ),
                    );
                });

                if (
                    preferredTreatyType &&
                    treatySelect.find(`option[value="${preferredTreatyType}"]`)
                        .length
                ) {
                    treatySelect.val(preferredTreatyType);
                } else {
                    treatySelect.val("");
                }

                treatySelect.trigger("change");
            },
        });
    }

    function loadBinderCovers() {
        makeAjaxRequest({
            url: config.routes.getBinderCovers,
            cache: true,
            cacheKey: "binder_covers",
            loadingMessage: "Loading binder covers...",
            successCallback: function (response) {
                const binders =
                    typeof response === "string"
                        ? JSON.parse(response)
                        : response;
                const binderSelect = $("#bindercoverno");

                binderSelect
                    .empty()
                    .append('<option value="">Select Binder Cover</option>');

                if (!Array.isArray(binders) || binders.length === 0) {
                    binderSelect.append(
                        '<option value="">No binder covers available</option>',
                    );
                    return;
                }

                binders.forEach((binder) => {
                    binderSelect.append(
                        $("<option>")
                            .val(binder.binder_cov_no)
                            .text(
                                `${binder.binder_cov_no} - ${binder.agency_name}`,
                            ),
                    );
                });

                binderSelect.trigger("change.select2");
            },
        });
    }

    function loadPremTypesData(treatyType, counter, reinClass) {
        if (!treatyType || counter === undefined || !reinClass) {
            console.warn(
                "Missing required parameters for loading premium types",
            );
            return;
        }

        const targetSelects = $(
            `.prem_type_code[data-class-counter="${counter}"]`,
        );

        if (!targetSelects.length) {
            console.warn(`No class name selects found for section ${counter}`);
            return;
        }

        const selectedPremTypes = getSelectedPremiumTypes(
            reinClass,
            treatyType,
        );

        targetSelects.prop("disabled", true);
        targetSelects
            .empty()
            .append(
                $("<option>").val("").text("Loading...").prop("disabled", true),
            );

        makeAjaxRequest({
            url: config.routes.getReinPremType,
            data: {
                reinclass: reinClass,
                selectedCodes: selectedPremTypes,
            },
            cache: true,
            cacheKey: `prem_types_${reinClass}_${treatyType}`,
            loadingMessage: false,
            successCallback: function (response) {
                targetSelects.prop("disabled", false);
                targetSelects.empty();
                targetSelects.append(
                    $("<option>")
                        .val("")
                        .text("-- Select Class Name --")
                        .prop("disabled", true)
                        .prop("selected", true),
                );

                if (!Array.isArray(response) || response.length === 0) {
                    targetSelects.append(
                        $("<option>")
                            .val("")
                            .text("No premium types available")
                            .prop("disabled", true),
                    );
                    return;
                }

                response.forEach((premType) => {
                    if (!premType.premtype_code || !premType.premtype_name) {
                        return;
                    }

                    const optionText = `${premType.premtype_code} - ${premType.premtype_name}`;

                    targetSelects.append(
                        $("<option>")
                            .val(premType.premtype_code)
                            .text(optionText)
                            .attr("data-reinclass", reinClass)
                            .attr("data-treaty", treatyType)
                            .attr("data-name", premType.premtype_name),
                    );
                });

                targetSelects.trigger("change.select2");

                if (!config.sectionPremTypesData)
                    config.sectionPremTypesData = {};
                config.sectionPremTypesData[counter] = response;
            },
            errorCallback: function () {
                targetSelects.prop("disabled", false);
                targetSelects
                    .empty()
                    .append(
                        $("<option>")
                            .val("")
                            .text("Error loading types")
                            .prop("disabled", true),
                    );
            },
        });
    }

    function getSelectedPremiumTypes(reinClass, treaty) {
        const selectedPremTypes = [];
        const selector = `.prem_type_code[data-reinclass="${reinClass}"][data-treaty="${treaty}"]`;

        $(selector).each(function () {
            const selectedVal = $(this).find("option:selected").val();
            if (selectedVal && selectedVal.trim() !== "") {
                selectedPremTypes.push(selectedVal);
            }
        });

        return selectedPremTypes;
    }

    function loadProspectData(prospectId) {
        if (!prospectId || prospectId.length < 3) return;

        if (!config.routes.getProspectData) {
            toastr.error("Prospect data endpoint not configured");
            return;
        }

        const url = config.routes.getProspectData.replace(
            ":id",
            encodeURIComponent(prospectId),
        );

        makeAjaxRequest({
            url: url,
            loadingMessage: "Loading prospect data...",
            successCallback: function (response) {
                if (response.status && response.data) {
                    populateProspectData(response.data);
                    toastr.success("Prospect data loaded successfully");
                } else {
                    toastr.info("No data found for this Prospect ID");
                }
            },
            errorCallback: function () {
                toastr.error("Failed to load prospect data");
            },
        });
    }

    function populateProspectData(data) {
        if (!data) return;

        const fieldMappings = {
            type_of_bus: elements.typeOfBus,
            covertype: elements.coverType,
            branchcode: $("#branchcode"),
            broker_flag: elements.brokerFlag,
            brokercode: $("#brokercode"),

            pay_method: $("#pay_method"),
            no_of_installments: $("#no_of_installments"),
            currency_code: $("#currency_code"),
            today_currency: $("#today_currency"),

            class_group: $("#class_group"),
            insured_name: $("#insured_name"),
            fac_date_offered: $("#fac_date_offered"),

            sum_insured_type: $("#sum_insured_type"),
            total_sum_insured: $("#total_sum_insured"),
            apply_eml: $("#apply_eml"),
            eml_rate: $("#eml_rate"),
            eml_amt: $("#eml_amt"),
            effective_sum_insured: $("#effective_sum_insured"),

            cede_premium: $("#cede_premium"),
            rein_premium: $("#rein_premium"),
            fac_share_offered: $("#fac_share_offered"),

            comm_rate: $("#comm_rate"),
            comm_amt: $("#comm_amt"),
            reins_comm_type: $("#reins_comm_type"),
            reins_comm_rate: $("#reins_comm_rate"),
            reins_comm_amt: $("#reins_comm_amt"),

            brokerage_comm_type: $("#brokerage_comm_type"),
            brokerage_comm_rate: $("#brokerage_comm_rate"),
            brokerage_comm_rate_amnt: $("#brokerage_comm_rate_amnt"),
            brokerage_comm_amt: $("#brokerage_comm_amt"),

            coverfrom: $("#coverfrom"),
            coverto: $("#coverto"),
            division: $("#division"),
            vat_charged: $("#vat_charged"),
            limit_per_reinclass: $("#limit_per_reinclass"),
            layer_no: $("#layer_no"),
            nonprop_reinclass: $("#nonprop_reinclass"),
            nonprop_reinclass_desc: $("#nonprop_reinclass_desc"),
            indemnity_treaty_limit: $("#indemnity_treaty_limit"),
            underlying_limit: $("#underlying_limit"),
        };

        Object.entries(fieldMappings).forEach(([key, $element]) => {
            if (
                data[key] !== undefined &&
                data[key] !== null &&
                $element.length
            ) {
                $element.val(data[key]).trigger("change");
            }
        });

        if (data.classcode) {
            const populateClasscode = () => {
                const $classcode = $("#classcode");
                const classOption = $classcode.find(
                    `option[value="${data.classcode}"]`,
                );

                if (classOption.length > 0) {
                    $classcode.val(data.classcode).trigger("change");
                } else {
                    setTimeout(populateClasscode, 200);
                }
            };

            setTimeout(populateClasscode, 300);
        }

        if (data.premium_payment_term) {
            const populatePremiumPaymentTerm = () => {
                const $premiumPaymentTerm = $("#premium_payment_term");
                const paymentTermOption = $premiumPaymentTerm.find(
                    `option[value="${data.premium_payment_term}"]`,
                );

                if (paymentTermOption.length > 0) {
                    $premiumPaymentTerm
                        .val(data.premium_payment_term)
                        .trigger("change");
                } else {
                    setTimeout(populatePremiumPaymentTerm, 200);
                }
            };

            setTimeout(populatePremiumPaymentTerm, 300);
        }

        if (data.risk_details) {
            $("#risk_details_content").html(data.risk_details);
        }

        if (data.pay_method) {
            setTimeout(() => {
                const selectedOption = $("#pay_method").find("option:selected");
                const shortDescription =
                    selectedOption.attr("data-description");

                if (shortDescription === "I") {
                    $("#installments_count_section").show();
                    $("#add_installment_btn_section").show();
                } else {
                    $("#installments_count_section").hide();
                    $("#add_installment_btn_section").hide();
                }
            }, 100);
        }
    }

    function populateExistingData() {
        if (!config.oldData) return;
    }

    function handleRiskDetailsPaste(e) {
        e.preventDefault();

        const clipboardData = (e.originalEvent || e).clipboardData;
        if (!clipboardData) return;

        const pastedHTML = clipboardData.getData("text/html");
        const pastedText = clipboardData.getData("text/plain");

        if (pastedHTML) {
            try {
                const parser = new DOMParser();
                const doc = parser.parseFromString(pastedHTML, "text/html");
                const table = doc.querySelector("table");

                if (table) {
                    const sanitized = DOMPurify
                        ? DOMPurify.sanitize(table.outerHTML, {
                              ALLOWED_TAGS: [
                                  "table",
                                  "tr",
                                  "td",
                                  "th",
                                  "thead",
                                  "tbody",
                                  "tfoot",
                              ],
                              ALLOWED_ATTR: ["class", "style"],
                          })
                        : table.outerHTML;

                    document.execCommand("insertHTML", false, sanitized);
                } else {
                    document.execCommand("insertText", false, pastedText);
                }
            } catch (error) {
                console.error("Error parsing pasted content:", error);
                document.execCommand("insertText", false, pastedText);
            }
        } else if (pastedText) {
            document.execCommand("insertText", false, pastedText);
        }
    }

    function hideSections() {
        elements.facSection.hide();
        elements.treatySection.hide();

        $("#fac_section :input").each(function () {
            $(this).rules("remove");
        });

        $("#treaty_section :input").each(function () {
            $(this).rules("remove");
        });

        $("#installment_section :input").each(function () {
            $(this).rules("remove");
        });

        $("#treaty_proportional_section").hide();
        $("#treaty_nonproportional_section").hide();
        $("#treaty_common_section").hide();
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
        $(`${selector} input, ${selector} select, ${selector} textarea`).prop(
            "disabled",
            false,
        );
        $(`${selector}-div`).show();
    }

    function disableSection(selector) {
        $(`${selector} input, ${selector} select, ${selector} textarea`)
            .prop("disabled", true)
            .val("");
        $(`${selector}-div`).hide();
    }

    function initializePlugins() {
        if ($.fn.tooltip) {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }

        if ($.fn.popover) {
            $('[data-bs-toggle="popover"]').popover();
        }

        $(".commission_type").each(function () {
            handleCommissionTypeChange.call(this);
        });
    }

    function setupValidation() {
        if (!$.fn.validate) {
            console.warn("jQuery Validation plugin not loaded");
            return;
        }

        $.validator.addMethod(
            "greaterThan",
            function (value, element, param) {
                const target = $(param);
                if (this.optional(element) && this.optional(target[0])) {
                    return true;
                }

                const currentDate = parseDateInput(value);
                const targetDate = parseDateInput(target.val());

                if (currentDate && targetDate) {
                    return currentDate > targetDate;
                }

                return parseNumber(value) > parseNumber(target.val());
            },
            "Must be greater than {0}",
        );

        $.validator.addMethod(
            "percentage",
            function (value, element) {
                return (
                    this.optional(element) ||
                    (parseNumber(value) >= 0 && parseNumber(value) <= 100)
                );
            },
            "Please enter a valid percentage (0-100)",
        );

        elements.form.validate({
            ignore: ":hidden:not(.select2-hidden-accessible)",
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
                coverto: {
                    required: true,
                    greaterThan: "#coverfrom",
                },

                class_group: { required: true },
                classcode: { required: true },
                insured_name: { required: true },
                fac_date_offered: { required: true },
                sum_insured_type: { required: true },
                total_sum_insured: { required: true },
                apply_eml: { required: true },
                cede_premium: { required: true },
                rein_premium: { required: true },
                fac_share_offered: { required: true },
                comm_rate: { required: true },
                comm_amt: { required: true },
                reins_comm_type: { required: true },
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
                coverto: {
                    required: "Please enter cover end date",
                    greaterThan: "End date must be after start date",
                },
                class_group: "Please select a class group",
                classcode: "Please select a class name",
                insured_name: "Please select an insured name",
                fac_date_offered: "Please enter date offered",
                sum_insured_type: "Please select sum insured type",
                total_sum_insured: "Please enter total sum insured",
                apply_eml: "Please select apply EML option",
                cede_premium: "Please enter cedant premium",
                rein_premium: "Please enter reinsurer premium",
                fac_share_offered: "Please enter share offered",
                comm_rate: "Please enter cedant commission rate",
                comm_amt: "Please enter cedant commission amount",
                reins_comm_type: "Please select reinsurer commission type",
            },
            ignore: function (index, element) {
                const $el = $(element);

                const $facSection = $("#fac_section");
                if (
                    $facSection.is(":hidden") ||
                    $facSection.css("display") === "none"
                ) {
                    if ($el.closest("#fac_section").length > 0) {
                        return true;
                    }
                }

                return !$el.is(":visible") || $el.closest(":hidden").length > 0;
            },
        });
    }

    function initializeFields() {
        hideSections();

        if (config.trans_type !== "NEW" && config.oldData) {
            populateExistingData();
        }

        elements.typeOfBus.trigger("change");
        elements.coverType.trigger("change");
        elements.brokerFlag.trigger("change");
        elements.payMethod.trigger("change");
        elements.applyEml.trigger("change");
        calculateCover();

        if (config.prospectId) {
            elements.prospectId.val(config.prospectId);
            loadProspectData(config.prospectId);
        }

        $(".commission_type").each(function () {
            handleCommissionTypeChange.call(this);
        });

        if (config.trans_type === "EDIT") {
            $(".reinclass-section").each(function () {
                const counter = $(this).data("counter");
                const treatyType = $("#treatytype").val();
                if (treatyType) {
                    populateTreatyDropdown(counter, treatyType);
                }
            });
        }
    }

    function showLoader(message = "Loading...") {
        if (!elements.pageLoader.length) {
            console.warn("Page loader element not found");
            return;
        }

        elements.pageLoader.find(".loader-text").text(message);
        elements.pageLoader.fadeIn(200);
    }

    function hideLoader() {
        if (!elements.pageLoader.length) return;
        elements.pageLoader.fadeOut(200);
    }

    function isInstallmentPayment() {
        const payMethodDesc = elements.payMethod
            .find("option:selected")
            .data("description");
        return payMethodDesc === "I";
    }

    return {
        init: init,
        showLoader: showLoader,
        hideLoader: hideLoader,
        numberWithCommas: numberWithCommas,
        parseNumber: parseNumber,
        sanitizeHTML: sanitizeHTML,
        sanitizeInput: sanitizeInput,
        formatAmountField: formatAmountField,
        validateNumericInput: validateNumericInput,

        _config: config,
        _state: state,
        _elements: elements,
    };
})();

$(document).ready(function () {
    try {
        CoverRegistration.init();
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Initialization Failed",
            text: "There was an error loading the page. Please refresh and try again.",
            footer: '<a href="javascript:window.location.reload()">Click here to reload</a>',
        });
    }
});

if (typeof module !== "undefined" && module.exports) {
    module.exports = CoverRegistration;
}
