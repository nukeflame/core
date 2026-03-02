// @pk305
(function ($) {
    "use strict";

    const CONFIG = Object.freeze({
        DECIMAL_PRECISION: 2,
        TOLERANCE: 0.001,
        MAX_INSTALLMENTS: 12,
        MIN_INSTALLMENTS: 1,
        ANIMATION_DURATION: 300,
        NOTIFICATION_DURATION: 5000,
        PERCENTAGE_MULTIPLIER: 100,
        SELECT2_INIT_DELAY: 150,
        DEBOUNCE_DELAY: 250,
        AJAX_TIMEOUT: 30000,
    });

    const BUSINESS_TYPES = Object.freeze({
        FPR: "FPR",
        FNP: "FNP",
        TPR: "TPR",
        TPN: "TPN",
    });

    const SELECTORS = Object.freeze({
        APP: "#coverDetailsApp",
        TREATY_DIV: "#treaty-div",
        TREATY_SECTION: ".treaty-div-section",
        REINSURER_SECTION: ".reinsurer-section",
        REINSURER_ROW_TEMPLATE: "#reinsurer-row-template",

        MODAL: "#addReinsurerModal",
        SCHEDULE_MODAL: "#addScheduleModal",
        FORM: "#reinsurerForm",
        SAVE_BUTTON: "#partner-save-btn",

        VALIDATION_MESSAGES: "#validation-messages",
        VALIDATION_LIST: "#validation-list",

        SCHEDULES_TABLE: "#schedules-table",
        ATTACHMENTS_TABLE: "#attachments-table",
        CLAUSES_TABLE: "#clauses-table",
        REINSURERS_TABLE: "#reinsurers-table",
        INS_CLASSES_TABLE: "#insclass-table",
        INSTALLMENTS_TABLE: "#installments-table",
        DEBITS_TABLE: "#debits-table",
        APPROVALS_TABLE: "#approvals-table",

        SCHEDULES_FORM: "#schedulesForm",
        ATTACHMENTS_FORM: "#attachmentsForm",
        CLAUSES_FORM: "#clausesForm",
        REINSURER_FORM: "#reinsurerForm",
        EDIT_REINSURER_FORM: "#EditReinsurerForm",
        VERIFY_FORM: "#verifyForm",
        FAC_DEBIT_FORM: "#facDebitForm",
    });

    const Utils = {
        removeCommas(value) {
            if (value === null || value === undefined || value === "") return 0;
            return parseFloat(String(value).replace(/,/g, "")) || 0;
        },

        formatNumber(value, decimals = CONFIG.DECIMAL_PRECISION) {
            if (value === null || value === undefined || value === "")
                return "-";
            const num = parseFloat(value);
            if (isNaN(num)) return value;
            const hasDecimals = num % 1 !== 0;
            return num.toLocaleString("en-US", {
                minimumFractionDigits: hasDecimals ? 2 : 0,
                maximumFractionDigits: hasDecimals ? 2 : 0,
            });
        },

        toDecimal(number, precision = CONFIG.DECIMAL_PRECISION) {
            return parseFloat(Number(number).toFixed(precision));
        },

        areEqual(num1, num2, tolerance = CONFIG.TOLERANCE) {
            return (
                Math.abs(this.toDecimal(num1) - this.toDecimal(num2)) <=
                tolerance
            );
        },

        getElementValue(selector, defaultValue = 0) {
            const $el = $(selector);
            if (!$el.length) return defaultValue;
            return this.removeCommas($el.val()) || defaultValue;
        },
        clampNumber(value, min = null, max = null, defaultValue = 0) {
            const num = this.removeCommas(value);
            if (isNaN(num)) return defaultValue;
            if (min !== null && num < min) return min;
            if (max !== null && num > max) return max;
            return num;
        },
        escapeHtml(text) {
            if (!text) return "";
            const div = document.createElement("div");
            div.textContent = text;
            return div.innerHTML;
        },

        replacePlaceholders(template, replacements) {
            return Object.entries(replacements).reduce(
                (result, [key, value]) =>
                    result.replace(new RegExp(key, "g"), value),
                template,
            );
        },

        debounce(func, wait = CONFIG.DEBOUNCE_DELAY) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        generateId(prefix = "id") {
            return `${prefix}-${Date.now()}-${Math.random()
                .toString(36)
                .substr(2, 9)}`;
        },

        getCsrfToken() {
            return $('meta[name="csrf-token"]').attr("content") || "";
        },
    };

    const NotificationService = {
        activeNotifications: new Set(),

        _createNotification(type, message) {
            const alertClasses = {
                success: "alert-success",
                error: "alert-danger",
                warning: "alert-warning",
                info: "alert-info",
            };

            const iconClasses = {
                success: "fa-check-circle",
                error: "fa-times-circle",
                warning: "fa-exclamation-triangle",
                info: "fa-info-circle",
            };

            const id = Utils.generateId("notification");
            const $notification = $(`
                <div id="${id}" class="alert ${
                    alertClasses[type] || alertClasses.info
                } alert-dismissible fade show position-fixed notification-alert"
                    style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
                    role="alert">
                    <i class="fa ${
                        iconClasses[type] || iconClasses.info
                    } me-2"></i>
                    <span>${Utils.escapeHtml(message)}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);

            return { id, $notification };
        },

        show(type, message) {
            const { id, $notification } = this._createNotification(
                type,
                message,
            );

            $("body").append($notification);
            this.activeNotifications.add(id);

            setTimeout(() => this.remove(id), CONFIG.NOTIFICATION_DURATION);

            $notification.on("closed.bs.alert", () =>
                this.activeNotifications.delete(id),
            );

            return id;
        },

        remove(id) {
            const $notification = $(`#${id}`);
            if ($notification.length) {
                $notification.fadeOut(CONFIG.ANIMATION_DURATION, function () {
                    $(this).remove();
                });
            }
            this.activeNotifications.delete(id);
        },

        clear() {
            this.activeNotifications.forEach((id) => this.remove(id));
            $(".notification-alert").remove();
        },

        success: (msg) => NotificationService.show("success", msg),
        error: (msg) => NotificationService.show("error", msg),
        warning: (msg) => NotificationService.show("warning", msg),
        info: (msg) => NotificationService.show("info", msg),
    };

    const Select2Manager = {
        defaultOptions: {
            width: "100%",
            allowClear: false,
        },

        init(selector = ".select2Placement", parentSelector = SELECTORS.MODAL) {
            if (!$.fn.select2) {
                console.warn("Select2 not loaded");
                return;
            }

            const $parent = $(parentSelector);
            const $elements = $(selector);

            $elements.each(function () {
                const $el = $(this);

                if ($el.hasClass("select2-hidden-accessible")) {
                    try {
                        $el.select2("destroy");
                    } catch (e) {
                        console.warn("Error destroying Select2:", e);
                    }
                }

                $el.select2({
                    ...Select2Manager.defaultOptions,
                    dropdownParent: $parent.length ? $parent : $("body"),
                    placeholder: $el.find("option:first").text() || "Select...",
                });
            });
        },

        initInContainer(containerSelector) {
            const $container = $(containerSelector);
            if (!$container.length) return;

            this.init(`${containerSelector} .select2Placement`);
        },
        setValue($element, value) {
            if (
                !$element ||
                !$element.length ||
                value === null ||
                value === undefined
            ) {
                return false;
            }

            const stringValue = String(value);
            const optionExists =
                $element.find(`option[value="${stringValue}"]`).length > 0;

            if (!optionExists) {
                console.warn(`Select2: Option "${stringValue}" not found`);
                return false;
            }

            $element.val(stringValue);

            if ($element.hasClass("select2-hidden-accessible")) {
                $element.trigger("change.select2");
            } else {
                $element.trigger("change");
            }

            return true;
        },

        getOptions($element) {
            return $element
                .find("option")
                .map((_, el) => ({
                    value: $(el).val(),
                    text: $(el).text().trim(),
                }))
                .get();
        },
        waitForReady(selector, timeout = 2000) {
            return new Promise((resolve) => {
                const $el = $(selector);
                if ($el.hasClass("select2-hidden-accessible")) {
                    resolve($el);
                    return;
                }

                const startTime = Date.now();
                const interval = setInterval(() => {
                    if ($(selector).hasClass("select2-hidden-accessible")) {
                        clearInterval(interval);
                        resolve($(selector));
                    } else if (Date.now() - startTime > timeout) {
                        clearInterval(interval);
                        resolve(null);
                    }
                }, 50);
            });
        },
    };

    class CalculationService {
        constructor(coverData) {
            this.coverData = coverData;
        }

        updateCoverData(data) {
            this.coverData = { ...this.coverData, ...data };
        }

        calculateShareAmounts(sharePercentage, commissionRate) {
            const shareDecimal = sharePercentage / CONFIG.PERCENTAGE_MULTIPLIER;
            const { total_sum_insured, rein_premium } = this.coverData;

            const sumInsured = shareDecimal * total_sum_insured;
            const premium = shareDecimal * rein_premium;
            const commission =
                (commissionRate / CONFIG.PERCENTAGE_MULTIPLIER) * premium;

            return {
                sumInsured: Utils.toDecimal(sumInsured),
                premium: Utils.toDecimal(premium),
                commission: Utils.toDecimal(commission),
            };
        }

        calculateCommissionAmount(premium, rate, options = {}) {
            let p = Utils.removeCommas(premium);
            const r = Utils.removeCommas(rate);

            if (options.premium_tax) {
                const tax = Utils.removeCommas(options.tax_amt || 0);
                p -= tax;
            }
            if (options.net_withholding_tax) {
                const wht = Utils.removeCommas(options.wht_amt || 0);
                p -= wht;
            }

            return Utils.toDecimal((p * r) / CONFIG.PERCENTAGE_MULTIPLIER);
        }

        calculateWHTAmount(premium, rate) {
            const p = Utils.removeCommas(premium);
            const r = Utils.removeCommas(rate);
            return Utils.toDecimal((p * r) / CONFIG.PERCENTAGE_MULTIPLIER);
        }

        calculateCommissionRate(premium, amount) {
            const p = Utils.removeCommas(premium);
            const a = Utils.removeCommas(amount);
            return p > 0
                ? Utils.toDecimal((a / p) * CONFIG.PERCENTAGE_MULTIPLIER)
                : 0;
        }

        calculateBrokerageCommission(
            premium,
            brokerageType,
            quotedAmount = 0,
            options = {},
        ) {
            const { cedant_comm_rate, rein_comm_rate } = this.coverData;
            let p = Utils.removeCommas(premium);

            if (options.net_of_tax) {
                const tax = Utils.removeCommas(options.tax_amt || 0);
                p -= tax;
            }
            if (options.net_of_commission) {
                const comm = Utils.removeCommas(options.comm_amt || 0);
                p -= comm;
            }
            if (options.net_of_claims) {
                const claims = Utils.removeCommas(options.claims_amt || 0);
                p -= claims;
            }

            if (brokerageType === "A") {
                const amount = Utils.removeCommas(quotedAmount);
                const rate =
                    p > 0 ? (amount / p) * CONFIG.PERCENTAGE_MULTIPLIER : 0;
                return {
                    rate: Utils.toDecimal(rate),
                    amount: Utils.toDecimal(amount),
                };
            }

            const brokerageRate = Math.max(
                0,
                Utils.removeCommas(rein_comm_rate) - cedant_comm_rate,
            );
            const brokerageAmount =
                (brokerageRate / CONFIG.PERCENTAGE_MULTIPLIER) * p;

            return {
                rate: Utils.toDecimal(brokerageRate),
                amount: Utils.toDecimal(brokerageAmount),
            };
        }

        calculateAdjustedBrokerage(premium, rate, options = {}) {
            let adjustedPremium = Utils.removeCommas(premium);

            if (options.net_of_tax) {
            }

            if (options.net_of_commission) {
                // Deduct commission from premium
            }

            const amount =
                (Utils.removeCommas(rate) / CONFIG.PERCENTAGE_MULTIPLIER) *
                adjustedPremium;
            return Utils.toDecimal(amount);
        }

        calculateRetroAmount(premium, rate) {
            const p = Utils.removeCommas(premium);
            const r = Utils.removeCommas(rate);
            return Utils.toDecimal((r / CONFIG.PERCENTAGE_MULTIPLIER) * p);
        }

        calculateRetroRate(premium, amount) {
            const p = Utils.removeCommas(premium);
            const a = Utils.removeCommas(amount);
            return p > 0
                ? Utils.toDecimal((a / p) * CONFIG.PERCENTAGE_MULTIPLIER)
                : 0;
        }

        generateInstallments(totalAmount, count) {
            if (count <= 0) return [];

            const amount = totalAmount / count;
            const percentage = CONFIG.PERCENTAGE_MULTIPLIER / count;

            return Array.from({ length: count }, (_, i) => ({
                number: i + 1,
                amount: Utils.toDecimal(amount),
                percentage: Utils.toDecimal(percentage),
            }));
        }
    }

    class ValidationService {
        constructor(coverData) {
            this.coverData = coverData;
            this.errors = [];
        }

        reset() {
            this.errors = [];
            return this;
        }

        addError(message) {
            this.errors.push(Utils.escapeHtml(message));
            return this;
        }

        hasErrors() {
            return this.errors.length > 0;
        }

        getErrors() {
            return [...this.errors];
        }

        getPrefix(number) {
            const { type_of_bus } = this.coverData;
            const prefixes = {
                FPR: "Facultative",
                FNP: "Facultative",
                TPR: "Treaty",
                TPN: "Treaty",
            };
            return `${prefixes[type_of_bus] || "Section"} ${number}`;
        }

        validateShare(share, remaining, number) {
            const prefix = this.getPrefix(number);

            if (share <= 0) {
                this.addError(
                    `${prefix}: Share percentage must be greater than 0`,
                );
                return false;
            }

            if (share > remaining + CONFIG.TOLERANCE) {
                this.addError(
                    `${prefix}: Share (${share.toFixed(
                        2,
                    )}%) exceeds remaining (${remaining.toFixed(2)}%)`,
                );
                return false;
            }

            return true;
        }

        validateSignedShare(signed, written, treatyNum, reinsurerNum) {
            if (signed > written + CONFIG.TOLERANCE) {
                this.addError(
                    `${this.getPrefix(
                        treatyNum,
                    )}, Reinsurer ${reinsurerNum}: ` +
                        `Signed share (${signed}%) cannot exceed written share (${written}%)`,
                );
                return false;
            }
            return true;
        }

        validateDistribution(remaining, number) {
            if (Math.abs(remaining) > CONFIG.TOLERANCE) {
                const status = remaining > 0 ? "remaining" : "over-distributed";
                this.addError(
                    `${this.getPrefix(number)}: Distribution incomplete ` +
                        `(${Math.abs(remaining).toFixed(2)}% ${status})`,
                );
                return false;
            }
            return true;
        }

        validateReinsurerFields($section, treatyNum, reinsurerNum) {
            const prefix = `${this.getPrefix(
                treatyNum,
            )}, Reinsurer ${reinsurerNum}`;
            let isValid = true;

            const fields = [
                {
                    selector: ".reinsurer",
                    message: "Please select a reinsurer",
                    checkEmpty: true,
                },
                {
                    selector: ".reinsurer-written-share",
                    message: "Written share is required",
                    checkZero: true,
                },
                {
                    selector: ".reinsurer-share",
                    message: "Signed share is required",
                    checkZero: true,
                },
                {
                    selector: ".reinsurer-wht",
                    message: "WHT rate is required",
                    checkNull: true,
                },
                {
                    selector: ".reins-pay-method",
                    message: "Payment method is required",
                    checkEmpty: true,
                },
            ];

            fields.forEach(
                ({ selector, message, checkEmpty, checkZero, checkNull }) => {
                    const $el = $section.find(selector);
                    const val = $el.val();

                    if (checkEmpty && !val) {
                        this.addError(`${prefix}: ${message}`);
                        isValid = false;
                    } else if (checkZero && Utils.removeCommas(val) <= 0) {
                        this.addError(`${prefix}: ${message}`);
                        isValid = false;
                    } else if (
                        checkNull &&
                        (val === null || val === undefined || val === "")
                    ) {
                        this.addError(`${prefix}: ${message}`);
                        isValid = false;
                    }
                },
            );

            const brokerageType = $section.find(".brokerage-comm-type").val();
            if (brokerageType === "A") {
                const amount = Utils.removeCommas(
                    $section.find(".reinsurer-brokerage-comm-amt").val(),
                );
                if (amount <= 0) {
                    this.addError(
                        `${prefix}: Brokerage commission amount is required for Quoted Amount type`,
                    );
                    isValid = false;
                }
            }

            return isValid;
        }

        displayErrors() {
            const $list = $(SELECTORS.VALIDATION_LIST);
            const $container = $(SELECTORS.VALIDATION_MESSAGES);

            $list.empty();

            if (this.hasErrors()) {
                this.errors.forEach((error) => {
                    $list.append(`<li class="text-danger">${error}</li>`);
                });

                $container.fadeIn(() => {
                    $("html, body").animate(
                        { scrollTop: $container.offset().top - 100 },
                        500,
                    );
                });
            } else {
                $container.fadeOut();
            }
        }
    }

    class DistributionManager {
        constructor(calculationService) {
            this.calc = calculationService;
            this.originalDistributed = 0;
            this.currentDistributed = 0;
        }

        initFromPartners(partners = []) {
            this.originalDistributed = partners.reduce(
                (sum, p) => sum + Utils.removeCommas(p.share),
                0,
            );
            this.currentDistributed = this.originalDistributed;
        }

        calculate(treatyCounter) {
            let totalDistributed = 0;
            const $signedShareFields = $(
                `#reinsurer-div-${treatyCounter} .reinsurer-share`,
            );

            if ($signedShareFields.length) {
                $signedShareFields.each(function () {
                    totalDistributed += Utils.clampNumber($(this).val(), 0);
                });
            } else {
                $(
                    `#reinsurer-div-${treatyCounter} .reinsurer-total-acceptance`,
                ).each(function () {
                    totalDistributed += Utils.clampNumber($(this).val(), 0);
                });
            }

            const offered =
                Utils.getElementValue(`#share_offered-${treatyCounter}`) ||
                this.calc.coverData.share_offered;
            const remaining = offered - totalDistributed;

            $(`#distributed_share-${treatyCounter}`).val(
                Utils.toDecimal(totalDistributed),
            );
            $(`#rem_share-${treatyCounter}`).val(Utils.toDecimal(remaining));

            this.updateIndicator(treatyCounter, remaining);
            SummaryManager.refresh();

            return { distributed: totalDistributed, remaining };
        }

        updateIndicator(treatyCounter, remaining) {
            const $field = $(`#rem_share-${treatyCounter}`);
            $field.removeClass("bg-danger bg-warning bg-success text-white");

            if (remaining < -CONFIG.TOLERANCE) {
                $field.addClass("bg-danger text-white");
            } else if (remaining > CONFIG.TOLERANCE) {
                $field.addClass("bg-warning");
            } else {
                $field.addClass("bg-success text-white");
            }
        }

        handleShareInput(treatyCounter, counter) {
            const $shareInput = $(`#share-${treatyCounter}-${counter}`);
            const sharePercentage = Utils.clampNumber(
                $shareInput.val(),
                0,
                100,
            );

            if (sharePercentage <= 0) return;

            const commRate = Utils.getElementValue(
                `#reinsurer-comm_rate-${treatyCounter}-${counter}`,
                this.calc.coverData.cedant_comm_rate,
            );

            const amounts = this.calc.calculateShareAmounts(
                sharePercentage,
                commRate,
            );
            this.updateShareFields(treatyCounter, counter, amounts, commRate);
            this.calculate(treatyCounter);
        }

        updateShareFields(treatyCounter, counter, amounts, commRate) {
            const prefix = `#reinsurer`;
            const suffix = `-${treatyCounter}-${counter}`;

            const fields = {
                [`${prefix}-sum_insured${suffix}`]: amounts.sumInsured,
                [`${prefix}-premium${suffix}`]: amounts.premium,
                [`${prefix}-comm_amt${suffix}`]: amounts.commission,
                [`${prefix}-rein_premium${suffix}`]: amounts.premium,
                [`${prefix}-cedant_premium${suffix}`]: amounts.premium,
                [`${prefix}-comm_rate${suffix}`]: commRate,
            };

            Object.entries(fields).forEach(([selector, value]) => {
                $(selector).val(Utils.formatNumber(value));
            });
        }
    }

    const SummaryManager = {
        refresh() {
            let totalOffered = 0;
            let totalDistributed = 0;
            let totalReinsurerShare = 0;

            $(SELECTORS.TREATY_SECTION).each(function () {
                const $section = $(this);
                totalOffered += Utils.getElementValue(
                    $section.find(".share_offered"),
                );
                totalDistributed += Utils.getElementValue(
                    $section.find(".distributed-share"),
                );
                $section.find(".reinsurer-share").each(function () {
                    totalReinsurerShare += Utils.removeCommas($(this).val());
                });
            });

            const remaining = totalOffered - totalDistributed;
            const percentage =
                totalOffered > 0
                    ? (totalDistributed / totalOffered) *
                      CONFIG.PERCENTAGE_MULTIPLIER
                    : 0;

            this.updateDisplay({
                offered: totalOffered,
                distributed: totalDistributed,
                remaining,
                reinsurerShare: totalReinsurerShare,
                percentage,
            });
        },

        updateDisplay({
            offered,
            distributed,
            remaining,
            reinsurerShare,
            percentage,
        }) {
            const elements = {
                "#summary-total-offered": `${Utils.toDecimal(offered)}%`,
                "#summary-total-distributed": `${Utils.toDecimal(
                    distributed,
                )}%`,
                "#summary-reinsurer-count": `${Utils.toDecimal(
                    reinsurerShare,
                )}%`,
            };

            Object.entries(elements).forEach(([selector, value]) => {
                $(selector).text(value);
            });

            $("#summary-total-remaining .remaining-value").text(
                `${Math.abs(Utils.toDecimal(remaining))}%`,
            );

            $("#distribution-progress-bar")
                .css("width", `${percentage}%`)
                .attr("aria-valuenow", percentage)
                .find(".progress-text")
                .text(`${percentage.toFixed(1)}% Placed`);

            this.updateStatusAlert(remaining);
        },

        updateStatusAlert(remaining) {
            const $alert = $("#distribution-status-alert");
            if (!$alert.length) return;

            $alert.removeClass(
                "alert-success alert-warning alert-danger alert-info",
            );

            if (Math.abs(remaining) < CONFIG.TOLERANCE) {
                $alert
                    .addClass("alert-success")
                    .html(
                        '<i class="fa fa-check-circle"></i> <strong>Perfect!</strong> All shares fully distributed.',
                    )
                    .fadeIn();
            } else if (remaining > 0) {
                $alert
                    .addClass("alert-warning")
                    .html(
                        `<i class="fa fa-exclamation-triangle"></i> <strong>Incomplete:</strong> ${Utils.toDecimal(
                            remaining,
                        )}% not yet distributed.`,
                    )
                    .fadeIn();
            } else {
                $alert
                    .addClass("alert-danger")
                    .html(
                        `<i class="fa fa-times-circle"></i> <strong>Over-distributed:</strong> ${Math.abs(
                            Utils.toDecimal(remaining),
                        )}% excess.`,
                    )
                    .fadeIn();
            }
        },
    };

    class BrokerageManager {
        constructor(calculationService) {
            this.calc = calculationService;
        }

        handleTypeChange(treatyCounter, counter) {
            const $section = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const type = $section.find(".brokerage-comm-type").val();

            const $rateDiv = $section.find(".brokerage_comm_rate_div");
            const $amountDiv = $section.find(".brokerage_comm_amt_div");

            const $rateInput = $(
                `#brokerage_comm_rate-${treatyCounter}-${counter}`,
            );
            const $rateAmountInput = $(
                `#brokerage_comm_rate_amnt-${treatyCounter}-${counter}`,
            );
            const $quotedInput = $(
                `#reinsurer-brokerage_comm_amt-${treatyCounter}-${counter}`,
            );

            if (type === "A") {
                $amountDiv.show();
                $rateDiv.hide();
                $rateInput.val("").prop("required", false);
                $rateAmountInput.val("").prop("required", false);
                $quotedInput.prop("required", true);
            } else if (type === "R") {
                $rateDiv.show();
                $amountDiv.hide();
                $quotedInput.val("").prop("required", false);
                this.calculate(treatyCounter, counter);
            } else {
                $rateDiv.hide();
                $amountDiv.hide();
                $rateInput.val("").prop("required", false);
                $rateAmountInput.val("").prop("required", false);
                $quotedInput.val("").prop("required", false);
            }
        }

        calculate(treatyCounter, counter) {
            const $section = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const type = $section.find(".brokerage-comm-type").val();
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
            );

            let brokerage;

            const whtRate =
                $section.find('select[name*="[wht_rate]"]').val() || 0;
            const whtAmt = this.calc.calculateWHTAmount(premium, whtRate);

            const options = {
                net_of_tax: $section
                    .find('input[name*="[net_of_tax]"]')
                    .is(":checked"),
                net_of_claims: $section
                    .find('input[name*="[net_of_claims]"]')
                    .is(":checked"),
                net_of_commission: $section
                    .find('input[name*="[net_of_commission]"]')
                    .is(":checked"),
                comm_amt: $section.find('input[name*="[comm_amt]"]').val() || 0,
                tax_amt: whtAmt,
                claims_amt: 0,
            };

            if (type === "A") {
                const quotedAmount = Utils.getElementValue(
                    `#reinsurer-brokerage_comm_amt-${treatyCounter}-${counter}`,
                );
                brokerage = this.calc.calculateBrokerageCommission(
                    premium,
                    "A",
                    quotedAmount,
                    options,
                );
            } else if (type === "R") {
                brokerage = this.calc.calculateBrokerageCommission(
                    premium,
                    "R",
                    0,
                    options,
                );
            } else {
                return;
            }

            $(`#brokerage_comm_rate-${treatyCounter}-${counter}`).val(
                Utils.formatNumber(brokerage.rate),
            );
            $(`#brokerage_comm_rate_amnt-${treatyCounter}-${counter}`).val(
                Utils.formatNumber(brokerage.amount),
            );
        }
    }

    class RetroFeeManager {
        constructor(calculationService) {
            this.calc = calculationService;
        }

        handleToggle(treatyCounter, counter) {
            const $section = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const applyRetro = $section.find(".apply-fronting").val();

            const $rateDiv = $(
                `#fronting_rate_div-${treatyCounter}-${counter}`,
            );
            const $amtDiv = $(`#fronting_amt_div-${treatyCounter}-${counter}`);
            const $rateInput = $(
                `#reinsurer-fronting_rate-${treatyCounter}-${counter}`,
            );
            const $amtInput = $(
                `#reinsurer-fronting_amt-${treatyCounter}-${counter}`,
            );

            if (applyRetro === "Y") {
                $rateDiv.fadeIn(CONFIG.ANIMATION_DURATION);
                $amtDiv.fadeIn(CONFIG.ANIMATION_DURATION);
                if (!$rateInput.val()) $rateInput.val("0.00");
                if (!$amtInput.val()) $amtInput.val("0.00");
            } else {
                $rateDiv.fadeOut(CONFIG.ANIMATION_DURATION);
                $amtDiv.fadeOut(CONFIG.ANIMATION_DURATION);
                $rateInput.val("0.00");
                $amtInput.val("0.00");
            }
        }

        calculateAmount(treatyCounter, counter) {
            const rate = Utils.getElementValue(
                `#reinsurer-fronting_rate-${treatyCounter}-${counter}`,
            );
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
            );
            const amount = this.calc.calculateRetroAmount(premium, rate);
            $(`#reinsurer-fronting_amt-${treatyCounter}-${counter}`).val(
                Utils.formatNumber(amount),
            );
        }

        calculateRate(treatyCounter, counter) {
            const amount = Utils.getElementValue(
                `#reinsurer-fronting_amt-${treatyCounter}-${counter}`,
            );
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
            );
            const rate = this.calc.calculateRetroRate(premium, amount);
            $(`#reinsurer-fronting_rate-${treatyCounter}-${counter}`).val(
                Utils.formatNumber(rate),
            );
        }
    }

    class CommissionManager {
        constructor(calculationService) {
            this.calc = calculationService;
        }

        calculate(treatyCounter, counter) {
            const $section = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
            );
            const rate = Utils.getElementValue(
                `#reinsurer-comm_rate-${treatyCounter}-${counter}`,
            );

            const whtRate =
                $section.find('select[name*="[wht_rate]"]').val() || 0;
            const whtAmt = this.calc.calculateWHTAmount(premium, whtRate);

            const options = {
                premium_tax: $section
                    .find('input[name*="[premium_tax]"]')
                    .is(":checked"),
                net_withholding_tax: $section
                    .find('input[name*="[net_withholding_tax]"]')
                    .is(":checked"),
                tax_amt: 0,
                wht_amt: whtAmt,
            };

            const amount = this.calc.calculateCommissionAmount(
                premium,
                rate,
                options,
            );
            $(`#reinsurer-comm_amt-${treatyCounter}-${counter}`).val(
                Utils.formatNumber(amount),
            );
        }

        calculateRate(treatyCounter, counter) {
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
            );
            const amount = Utils.getElementValue(
                `#reinsurer-comm_amt-${treatyCounter}-${counter}`,
            );
            const rate = this.calc.calculateCommissionRate(premium, amount);
            $(`#reinsurer-comm_rate-${treatyCounter}-${counter}`).val(
                Utils.formatNumber(rate),
            );
        }
    }

    class InstallmentManager {
        constructor(calculationService) {
            this.calc = calculationService;
        }

        generate(treatyCounter, counter) {
            const $row = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const numInstallments =
                parseInt($row.find(".no-of-installments").val()) || 1;
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
            );

            if (
                numInstallments < CONFIG.MIN_INSTALLMENTS ||
                numInstallments > CONFIG.MAX_INSTALLMENTS
            ) {
                NotificationService.warning(
                    `Installments must be between ${CONFIG.MIN_INSTALLMENTS} and ${CONFIG.MAX_INSTALLMENTS}`,
                );
                return;
            }

            if (premium <= 0) {
                NotificationService.warning(
                    "Please enter reinsurer premium first",
                );
                return;
            }

            const installments = this.calc.generateInstallments(
                premium,
                numInstallments,
            );
            const $container = $row.find(".reinsurer-plan-section").empty();

            installments.forEach((inst, idx) => {
                $container.append(
                    this._createRow(treatyCounter, counter, inst, idx),
                );
            });

            $row.find(".installments-box").fadeIn(CONFIG.ANIMATION_DURATION);
            NotificationService.success(
                `${numInstallments} installments generated`,
            );
        }

        _createRow(treatyCounter, counter, installment, index) {
            const prefix = `treaty[${treatyCounter}][reinsurers][${counter}][installments][${index}]`;
            return `
                <div class="row mb-2 installment-row">
                    <div class="col-md-2">
                        <label class="form-label">Installment ${
                            installment.number
                        }</label>
                        <input type="text" class="form-control" value="${
                            installment.number
                        }" readonly />
                        <input type="hidden" name="${prefix}[number]" value="${
                            installment.number
                        }" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Due Date</label>
                        <input type="date" class="form-control installment-date" name="${prefix}[due_date]" required />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control installment-amount"
                               name="${prefix}[amount]" value="${
                                   installment.amount
                               }" required />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Percentage</label>
                        <input type="text" class="form-control" value="${installment.percentage.toFixed(
                            2,
                        )}%" readonly />
                    </div>
                </div>
            `;
        }

        handlePaymentMethodChange(treatyCounter, counter, method) {
            const $row = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const isInstallment = method === "INS" || method === "INST";

            $row.find(".no-of-installments-section").toggle(isInstallment);
            $row.find(".add-installment-btn-section").toggle(isInstallment);

            if (!isInstallment) {
                $row.find(".installments-box").fadeOut(
                    CONFIG.ANIMATION_DURATION,
                );
            }
        }
    }
    class ReinsurerManager {
        constructor(calculationService, validationService, brokerageManager) {
            this.calc = calculationService;
            this.validation = validationService;
            this.brokerageManager = brokerageManager;
            this.counters = {};
        }

        getCalcOptionKey($checkbox) {
            const name = $checkbox.attr("name") || "";
            const match = name.match(
                /\[(net_of_tax|net_of_claims|net_of_commission|net_of_premium|premium_tax|net_withholding_tax)\]$/,
            );
            return match ? match[1] : null;
        }

        syncCalcOptionAcrossTreaty(treatyCounter, optionKey, checked) {
            if (!optionKey && optionKey !== "") return;

            const $rows = $(
                `#reinsurer-div-${treatyCounter} ${SELECTORS.REINSURER_SECTION}`,
            );

            $rows.each(function () {
                const $row = $(this);
                const rowCounter = $row.data("counter");
                const $checkbox = $row.find(`input[name*="[${optionKey}]"]`);

                if ($checkbox.length) {
                    $checkbox.prop("checked", checked);
                }

                if (
                    window.ReinsurerPlacement?.commissionManager &&
                    window.ReinsurerPlacement?.brokerageManager
                ) {
                    window.ReinsurerPlacement.commissionManager.calculate(
                        treatyCounter,
                        rowCounter,
                    );
                    window.ReinsurerPlacement.brokerageManager.calculate(
                        treatyCounter,
                        rowCounter,
                    );
                }
            });
        }

        copyCalcOptionsToNewRow(treatyCounter, counter) {
            const $rows = $(
                `#reinsurer-div-${treatyCounter} ${SELECTORS.REINSURER_SECTION}`,
            );
            if ($rows.length <= 1) return;

            const $sourceRow = $rows.first();
            const $targetRow = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            if (!$targetRow.length) return;

            const options = [
                "net_of_tax",
                "net_of_claims",
                "net_of_commission",
                "net_of_premium",
                "premium_tax",
                "net_withholding_tax",
            ];

            options.forEach((optionKey) => {
                const isChecked = $sourceRow
                    .find(`input[name*="[${optionKey}]"]`)
                    .is(":checked");
                $targetRow
                    .find(`input[name*="[${optionKey}]"]`)
                    .prop("checked", isChecked);
            });
        }

        addRow(treatyCounter) {
            if (this.counters[treatyCounter] === undefined) {
                this.counters[treatyCounter] = 0;
            }

            this.counters[treatyCounter]++;
            const counter = this.counters[treatyCounter];

            const template = $(SELECTORS.REINSURER_ROW_TEMPLATE).html();
            if (!template) {
                NotificationService.error("Reinsurer template not found");
                return null;
            }

            const newRow = Utils.replacePlaceholders(template, {
                TREATY_COUNTER_PLACEHOLDER: treatyCounter,
                COUNTER_PLACEHOLDER: counter,
                REINSURER_NUMBER_PLACEHOLDER: counter + 1,
            });

            const $container = $(`#reinsurer-div-${treatyCounter}`);
            if (!$container.length) {
                NotificationService.error("Reinsurer container not found");
                return null;
            }

            $container.append(newRow);

            requestAnimationFrame(() => {
                setTimeout(() => {
                    Select2Manager.initInContainer(
                        `#reinsurer-div-${treatyCounter}-${counter}`,
                    );
                    this.copyCalcOptionsToNewRow(treatyCounter, counter);
                    this.updateNumbers(treatyCounter);
                    this.brokerageManager.handleTypeChange(
                        treatyCounter,
                        counter,
                    );
                }, CONFIG.SELECT2_INIT_DELAY);
            });

            return counter;
        }

        removeRow(treatyCounter, counter) {
            const $container = $(`#reinsurer-div-${treatyCounter}`);
            const remaining = $container.find(
                SELECTORS.REINSURER_SECTION,
            ).length;

            if (remaining <= 1) {
                Swal.fire({
                    icon: "warning",
                    title: "Cannot Remove",
                    text: "At least one reinsurer is required.",
                    confirmButtonText: "OK",
                });
                return;
            }

            Swal.fire({
                title: "Remove Reinsurer?",
                text: "This action cannot be undone.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, remove",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    const $row = $(
                        `#reinsurer-div-${treatyCounter}-${counter}`,
                    );
                    $row.fadeOut(CONFIG.ANIMATION_DURATION, () => {
                        $row.remove();
                        this.updateNumbers(treatyCounter);
                        this.filterSelected(treatyCounter);
                        // Trigger distribution recalculation
                        $(document).trigger("reinsurer:removed", [
                            treatyCounter,
                        ]);
                    });
                }
            });
        }

        updateNumbers(treatyCounter) {
            $(
                `#reinsurer-div-${treatyCounter} ${SELECTORS.REINSURER_SECTION}`,
            ).each(function (index) {
                $(this)
                    .find(".reinsurer-number")
                    .text(index + 1);
            });
        }

        filterSelected(treatyCounter) {
            const selected = this.getSelected(treatyCounter);

            $(`#reinsurer-div-${treatyCounter} .reinsurer`).each(function () {
                const $select = $(this);
                const currentValue = $select.val();

                if (!$select.data("original-options")) {
                    const options = $select
                        .find("option")
                        .map((_, el) => ({
                            value: $(el).val(),
                            text: $(el).text(),
                            title: $(el).attr("title"),
                        }))
                        .get();
                    $select.data("original-options", options);
                }

                const originalOptions = $select.data("original-options");
                $select.empty();

                originalOptions.forEach((opt) => {
                    if (
                        !opt.value ||
                        opt.value === currentValue ||
                        !selected.includes(opt.value)
                    ) {
                        const $option = $("<option>")
                            .val(opt.value)
                            .text(opt.text);
                        if (opt.title) $option.attr("title", opt.title);
                        $select.append($option);
                    }
                });

                if (currentValue) $select.val(currentValue);
                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.trigger("change.select2");
                }
            });
        }

        getSelected(treatyCounter) {
            return $(`#reinsurer-div-${treatyCounter} .reinsurer`)
                .map((_, el) => $(el).val())
                .get()
                .filter(Boolean);
        }
    }

    class TreatyManager {
        constructor(reinsurerManager) {
            this.reinsurerManager = reinsurerManager;
        }

        addSection() {
            const $lastSection = $(
                `${SELECTORS.TREATY_DIV} ${SELECTORS.TREATY_SECTION}`,
            ).last();

            if (!$lastSection.length) {
                NotificationService.error("No treaty section found to clone");
                return;
            }

            const currentCounter =
                parseInt($lastSection.attr("data-counter")) || 0;
            const currentTreaty = $(
                `#reinsurer-treaty-${currentCounter}`,
            ).val();

            if (!currentTreaty) {
                NotificationService.error(
                    "Please select a treaty before adding a new section",
                );
                return;
            }

            const newCounter = currentCounter + 1;
            const $newSection = $lastSection.clone();

            this._cleanSection($newSection, newCounter);
            this._updateAttributes($newSection, newCounter, currentCounter);

            $lastSection.after($newSection);
            Select2Manager.initInContainer(`#treaty-div-section-${newCounter}`);
            this.reinsurerManager.counters[newCounter] = newCounter;

            return newCounter;
        }

        removeSection(counter) {
            Swal.fire({
                title: "Remove Treaty Section?",
                text: "This will remove all reinsurers in this section.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, remove",
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#treaty-div-section-${counter}`).fadeOut(
                        CONFIG.ANIMATION_DURATION,
                        function () {
                            $(this).remove();
                            SummaryManager.refresh();
                        },
                    );
                    delete this.reinsurerManager.counters[counter];
                }
            });
        }

        _cleanSection($section, counter) {
            $section.find(".select2-container").remove();
            $section.find("[data-select2-id]").removeAttr("data-select2-id");
            $section.find("input:not(.share_offered)").val("");
            $section
                .find(`${SELECTORS.REINSURER_SECTION}:not(:first)`)
                .remove();
            $section.attr({
                "data-counter": counter,
                id: `treaty-div-section-${counter}`,
            });
        }

        _updateAttributes($section, counter, oldCounter) {
            $section.find("[id]").each(function () {
                const id = $(this).attr("id");
                const newId = id.replace(/(-\d+)(-\d+)?$/, (match, p1, p2) =>
                    p2 ? `-${counter}-${counter}` : `-${counter}`,
                );
                $(this).attr({
                    id: newId,
                    "data-counter": counter,
                    "data-treaty-counter": counter,
                });
            });

            $section.find("[name]").each(function () {
                const name = $(this).attr("name");
                $(this).attr(
                    "name",
                    name.replace(
                        new RegExp(`\\[${oldCounter}\\]`, "g"),
                        `[${counter}]`,
                    ),
                );
            });
        }
    }

    class FormSubmissionManager {
        constructor(validationService, coverData) {
            this.validation = validationService;
            this.coverData = coverData;
        }

        /**
         * Validate and submit the form
         */
        validateAndSubmit() {
            this.validation.reset();
            const isEditMode =
                String($(SELECTORS.FORM).find("#edit_mode").val()) === "1";

            const isFacultative = [
                BUSINESS_TYPES.FPR,
                BUSINESS_TYPES.FNP,
            ].includes(this.coverData.type_of_bus);

            if (isFacultative) {
                let treatyNumber = 1;
                $(SELECTORS.TREATY_SECTION).each((_, element) => {
                    const $section = $(element);
                    const treatyCounter = $section.data("counter");
                    this._validateTreatySection(
                        $section,
                        treatyCounter,
                        treatyNumber++,
                        isEditMode,
                    );
                });
            }

            this._validateUniqueReinsurers();

            if (this.validation.hasErrors()) {
                this.validation.displayErrors();
                return;
            }

            this._submit();
        }

        _validateTreatySection(
            $section,
            treatyCounter,
            treatyNumber,
            isEditMode = false,
        ) {
            const $treatySelect = $section.find(".reinsurer-treaty");
            if ($treatySelect.length && !$treatySelect.val()) {
                this.validation.addError(
                    `Treaty section ${treatyNumber}: Please select a treaty`,
                );
            }

            if (!isEditMode) {
                const remaining = Utils.getElementValue(
                    `#rem_share-${treatyCounter}`,
                );
                this.validation.validateDistribution(remaining, treatyNumber);
            }

            let reinsurerNumber = 1;
            $(
                `#reinsurer-div-${treatyCounter} ${SELECTORS.REINSURER_SECTION}`,
            ).each((_, element) => {
                this.validation.validateReinsurerFields(
                    $(element),
                    treatyNumber,
                    reinsurerNumber++,
                );
            });
        }

        _validateUniqueReinsurers() {
            const seen = new Map();

            $(SELECTORS.TREATY_SECTION).each((sectionIndex, sectionEl) => {
                const $section = $(sectionEl);
                const sectionLabel = sectionIndex + 1;

                $section
                    .find('.reinsurer[name*="[reinsurer]"]')
                    .each((rowIndex, selectEl) => {
                        const $select = $(selectEl);
                        const value = $select.val();
                        if (!value) return;

                        const key = String(value);
                        const currentLabel = `Section ${sectionLabel}, Reinsurer ${rowIndex + 1}`;

                        if (seen.has(key)) {
                            const firstLabel = seen.get(key);
                            const reinsurerName =
                                $select.find("option:selected").text().trim() ||
                                `ID ${key}`;
                            this.validation.addError(
                                `Reinsurer "${reinsurerName}" is duplicated (${firstLabel} and ${currentLabel}). Reinsurer must be unique for this cover.`,
                            );
                        } else {
                            seen.set(key, currentLabel);
                        }
                    });
            });
        }

        _submit() {
            const $form = $(SELECTORS.FORM);
            const $button = $(SELECTORS.SAVE_BUTTON);
            const isEditMode = String($form.find("#edit_mode").val()) === "1";
            const url = isEditMode ? $form.data("edit-url") : $form.data("url");

            if (!url) {
                NotificationService.error("Form submission URL not configured");
                return;
            }

            NotificationService.clear();
            $button
                .prop("disabled", true)
                .html(
                    isEditMode
                        ? '<i class="fa fa-spinner fa-spin me-2"></i>Updating...'
                        : '<i class="fa fa-spinner fa-spin me-2"></i>Saving...',
                );

            if (isEditMode) {
                const $firstRow = $(
                    `${SELECTORS.TREATY_SECTION} ${SELECTORS.REINSURER_SECTION}`,
                ).first();

                if (!$firstRow.length) {
                    NotificationService.error(
                        "No reinsurer row found for edit",
                    );
                    this._resetButton($button);
                    return;
                }

                const payload = {
                    endorsement_no: $form.find('[name="endorsement_no"]').val(),
                    tran_no: $form.find("#edit_tran_no").val(),
                    reinsurer:
                        $firstRow.find('select[name*="[reinsurer]"]').val() ||
                        $form.find("#edit_partner_no").val(),
                    written_share: $firstRow
                        .find('input[name*="[written_share]"]')
                        .val(),
                    compulsory_acceptance: $firstRow
                        .find('input[name*="[compulsory_acceptance]"]')
                        .val(),
                    optional_acceptance: $firstRow
                        .find('input[name*="[optional_acceptance]"]')
                        .val(),
                    comm_rate:
                        $firstRow.find('input[name*="[comm_rate]"]').val() || 0,
                    wht_rate:
                        $firstRow.find('select[name*="[wht_rate]"]').val() || 0,
                    net_of_tax: $firstRow
                        .find('input[name*="[net_of_tax]"]')
                        .is(":checked")
                        ? 1
                        : 0,
                    net_of_claims: $firstRow
                        .find('input[name*="[net_of_claims]"]')
                        .is(":checked")
                        ? 1
                        : 0,
                    net_of_commission: $firstRow
                        .find('input[name*="[net_of_commission]"]')
                        .is(":checked")
                        ? 1
                        : 0,
                    net_of_premium: $firstRow
                        .find('input[name*="[net_of_premium]"]')
                        .is(":checked")
                        ? 1
                        : 0,
                    premium_tax: $firstRow
                        .find('input[name*="[premium_tax]"]')
                        .is(":checked")
                        ? 1
                        : 0,
                    net_withholding_tax: $firstRow
                        .find('input[name*="[net_withholding_tax]"]')
                        .is(":checked")
                        ? 1
                        : 0,
                };

                const typeOfBus = String(
                    this.coverData.type_of_bus || "",
                ).toUpperCase();
                const isTreatyBus =
                    typeOfBus === BUSINESS_TYPES.TPR ||
                    typeOfBus === BUSINESS_TYPES.TPN ||
                    typeOfBus === "TNP";

                payload.share = isTreatyBus
                    ? payload.written_share
                    : $firstRow.find('input[name*="[share]"]').val();

                $.ajax({
                    url,
                    method: "POST",
                    data: payload,
                    timeout: CONFIG.AJAX_TIMEOUT,
                    headers: { "X-CSRF-TOKEN": Utils.getCsrfToken() },
                })
                    .done((response) => this._handleSuccess(response, $button))
                    .fail((xhr, status) =>
                        this._handleError(xhr, status, $button),
                    );
                return;
            }

            $.ajax({
                url,
                method: "POST",
                data: $form.serialize(),
                timeout: CONFIG.AJAX_TIMEOUT,
                headers: { "X-CSRF-TOKEN": Utils.getCsrfToken() },
            })
                .done((response) => this._handleSuccess(response, $button))
                .fail((xhr, status) => this._handleError(xhr, status, $button));
        }

        _handleSuccess(response, $button) {
            console.log(response);
            if (response.success) {
                toastr.success("Reinsurance placement saved successfully");
                setTimeout(() => {
                    $(SELECTORS.MODAL).modal("hide");
                    if (typeof window.refreshCoverData === "function") {
                        window.refreshCoverData();
                    } else {
                        location.reload();
                    }
                }, 1500);
            } else {
                toastr.error(
                    response.message || "An error occurred while saving",
                );
                this._resetButton($button);
            }
        }

        _handleError(xhr, status, $button) {
            const message =
                status === "timeout"
                    ? "Request timed out. Please try again."
                    : "An error occurred while saving";

            if (typeof toastr !== "undefined") {
                toastr.error(message);
            } else {
                NotificationService.error(message);
            }
            this._resetButton($button);
        }

        _resetButton($button) {
            const isEditMode =
                String($(SELECTORS.FORM).find("#edit_mode").val()) === "1";
            $button
                .prop("disabled", false)
                .html(
                    isEditMode
                        ? '<i class="fa fa-save me-2"></i>Update Placement'
                        : '<i class="fa fa-save me-2"></i>Save Placement',
                );
        }
    }

    const PlacementManager = {
        config: {
            fetchUrl: null,
            endorsementNo: null,
            coverNo: null,
            cover: null,
            prospectId: null,
            editTarget: null,
        },

        initialized: false,

        /**
         * Set configuration
         */
        setConfig(config) {
            this.config = { ...this.config, ...config };
            return this;
        },

        setEditTarget(target) {
            this.config.editTarget = target || null;
            return this;
        },

        /**
         * Initialize the placement manager
         */
        init() {
            if (this.initialized) {
                // If already initialized, just fetch data again
                if (this.config.fetchUrl) {
                    this.fetchExisting();
                }
                return this;
            }

            this._bindEvents();
            this.initialized = true;

            if (this.config.fetchUrl) {
                this.fetchExisting();
            }

            return this;
        },

        /**
         * Get managers from ReinsurerPlacement
         */
        _getManagers() {
            const rp = window.ReinsurerPlacement;
            return {
                reinsurerManager: rp?.reinsurerManager,
                brokerageManager: rp?.brokerageManager,
                retroFeeManager: rp?.retroFeeManager,
                treatyManager: rp?.treatyManager,
            };
        },

        /**
         * Fetch existing reinsurers from API
         */
        async fetchExisting() {
            if (!this.config.fetchUrl) {
                console.warn("PlacementManager: No fetchUrl configured");
                return;
            }

            try {
                this._showLoader();

                const response = await $.ajax({
                    url: this.config.fetchUrl,
                    type: "GET",
                    data: {
                        cover_no: this.config.coverNo,
                        endorsement_no: this.config.endorsementNo,
                        ...(this.config.editTarget?.partner_no
                            ? { partner_no: this.config.editTarget.partner_no }
                            : {}),
                        ...(this.config.editTarget?.tran_no
                            ? { tran_no: this.config.editTarget.tran_no }
                            : {}),
                    },
                });

                if (response.success && response.data) {
                    await this.populate(response.data);
                }
            } catch (error) {
                NotificationService.error("Failed to load existing reinsurers");
                console.error("PlacementManager fetch error:", error);
            } finally {
                this.config.editTarget = null;
                this._hideLoader();
            }
        },

        /**
         * Populate form with data
         */
        async populate(data) {
            if (data.treaties && Array.isArray(data.treaties)) {
                await this._populateTreaties(data.treaties);
            } else if (data.reinsurers && Array.isArray(data.reinsurers)) {
                await this._populateReinsurers(0, data.reinsurers);
            }

            this._triggerCalculations();
        },

        async _populatereinsurerCalcOptions($row, reinsurer) {
            const options = [
                "net_of_tax",
                "net_of_claims",
                "net_of_commission",
                "net_of_premium",
                "premium_tax",
                "net_withholding_tax",
            ];

            options.forEach((opt) => {
                const $checkbox = $row.find(`input[name*="[${opt}]"]`);
                if ($checkbox.length) {
                    const raw = reinsurer[opt];
                    const checked =
                        raw === 1 ||
                        raw === "1" ||
                        raw === true ||
                        String(raw).toLowerCase() === "true";
                    $checkbox.prop("checked", checked);
                }
            });
        },

        async _populateTreaties(treaties) {
            const { treatyManager } = this._getManagers();

            for (let i = 0; i < treaties.length; i++) {
                const treaty = treaties[i];

                // Add treaty section if needed
                if (i > 0 && treatyManager) {
                    treatyManager.addSection();
                    await this._waitForElement(`#treaty-div-section-${i}`);
                }

                this._populateTreatyFields(i, treaty);

                if (treaty.reinsurers?.length) {
                    await this._populateReinsurers(i, treaty.reinsurers);
                }
            }
        },

        async _populateReinsurers(treatyIndex, reinsurers) {
            const { reinsurerManager } = this._getManagers();

            for (let j = 0; j < reinsurers.length; j++) {
                // Add row if needed
                if (j > 0 && reinsurerManager) {
                    reinsurerManager.addRow(treatyIndex);
                    await this._waitForElement(
                        `#reinsurer-div-${treatyIndex}-${j}`,
                    );
                }

                await this._waitForSelect2(`#reinsurer-${treatyIndex}-${j}`);
                this._populateReinsurerRow(treatyIndex, j, reinsurers[j]);
            }
        },

        _populateTreatyFields(index, treaty) {
            const prefix = `treaty[${index}]`;
            const treatyValue = treaty.treaty || treaty.treaty_id;
            if (treatyValue) {
                Select2Manager.setValue(
                    $(`select[name="${prefix}[treaty]"]`),
                    treatyValue,
                );
            }
            if (treaty.layer_no) {
                $(`input[name="${prefix}[layer_no]"]`).val(treaty.layer_no);
            }
        },

        _populateReinsurerRow(treatyIndex, reinsurerIndex, data) {
            const { brokerageManager, retroFeeManager } = this._getManagers();
            const prefix = `treaty[${treatyIndex}][reinsurers][${reinsurerIndex}]`;
            const $section = $(
                `#reinsurer-div-${treatyIndex}-${reinsurerIndex}`,
            );

            if (!$section.length) {
                console.error(
                    `PlacementManager: Section not found - reinsurer-div-${treatyIndex}-${reinsurerIndex}`,
                );
                return;
            }

            const reinsurerId =
                data.reinsurer_id ||
                data.reinsurer ||
                data.partner_no ||
                data.customer_id;

            const $reinsurerSelect = $(
                `#reinsurer-${treatyIndex}-${reinsurerIndex}`,
            );
            if ($reinsurerSelect.length && reinsurerId) {
                const success = Select2Manager.setValue(
                    $reinsurerSelect,
                    reinsurerId,
                );

                // Existing cover reinsurers can be filtered out from option lists.
                // Inject the current record option so edit mode can always reselect it.
                if (!success) {
                    const optionLabel =
                        data.reinsurer_name || data.partner_name || "Selected";
                    const optionExists =
                        $reinsurerSelect.find(
                            `option[value="${String(reinsurerId)}"]`,
                        ).length > 0;

                    if (!optionExists) {
                        const $option = $("<option>")
                            .val(String(reinsurerId))
                            .text(optionLabel)
                            .attr("title", optionLabel);
                        $reinsurerSelect.append($option);
                    }

                    Select2Manager.setValue($reinsurerSelect, reinsurerId);
                }
            }

            const select2Fields = [
                { name: "wht_rate", value: data.wht_rate },
                {
                    name: "brokerage_comm_type",
                    value: data.brokerage_comm_type,
                },
                { name: "apply_fronting", value: data.apply_fronting },
                { name: "pay_method", value: data.pay_method },
            ];

            select2Fields.forEach(({ name, value }) => {
                if (value !== null && value !== undefined) {
                    const $select = $section.find(
                        `select[name="${prefix}[${name}]"]`,
                    );
                    const success = Select2Manager.setValue($select, value);

                    if (!success && name === "pay_method" && $select.length) {
                        const normalized = String(value).trim().toUpperCase();
                        const $fallbackOption = $select
                            .find("option")
                            .filter(function () {
                                const optionValue = String($(this).val() || "")
                                    .trim()
                                    .toUpperCase();
                                const optionDesc = String(
                                    $(this).data("pay-method-desc") || "",
                                )
                                    .trim()
                                    .toUpperCase();
                                return (
                                    optionValue === normalized ||
                                    optionDesc === normalized
                                );
                            })
                            .first();

                        if ($fallbackOption.length) {
                            Select2Manager.setValue(
                                $select,
                                $fallbackOption.val(),
                            );
                        }
                    }
                }
            });

            const formatFixed2 = (value) => {
                if (value === null || value === undefined || value === "") {
                    return value;
                }
                const num = Number(String(value).replace(/,/g, ""));
                return Number.isFinite(num) ? num.toFixed(2) : value;
            };

            const inputFields = [
                {
                    name: "written_share",
                    value: formatFixed2(data.written_share),
                },
                { name: "share", value: formatFixed2(data.share) },
                {
                    name: "sum_insured",
                    value: Utils.formatNumber(data.sum_insured),
                },
                { name: "premium", value: Utils.formatNumber(data.premium) },
                { name: "comm_rate", value: data.comm_rate },
                { name: "comm_amt", value: Utils.formatNumber(data.comm_amt) },
                {
                    name: "brokerage_comm_amt",
                    value: Utils.formatNumber(data.brokerage_comm_amt),
                },
                {
                    name: "brokerage_comm_rate",
                    value: data.brokerage_comm_rate,
                },
                {
                    name: "brokerage_comm_rate_amnt",
                    value: Utils.formatNumber(data.brokerage_comm_rate_amnt),
                },
                { name: "fronting_rate", value: data.fronting_rate },
                {
                    name: "fronting_amt",
                    value: Utils.formatNumber(data.fronting_amt),
                },
                {
                    name: "compulsory_acceptance",
                    value: formatFixed2(data.compulsory_acceptance),
                },
                {
                    name: "optional_acceptance",
                    value: formatFixed2(data.optional_acceptance),
                },
            ];

            inputFields.forEach(({ name, value }) => {
                if (value !== null && value !== undefined && value !== "") {
                    $section
                        .find(`input[name="${prefix}[${name}]"]`)
                        .val(value);
                }
            });

            // Handle brokerage display
            if (data.brokerage_comm_type && brokerageManager) {
                brokerageManager.handleTypeChange(treatyIndex, reinsurerIndex);
            }

            // Handle fronting display
            if (data.apply_fronting === "Y" && retroFeeManager) {
                retroFeeManager.handleToggle(treatyIndex, reinsurerIndex);
            }

            // Populate calculation basis options
            this._populatereinsurerCalcOptions($section, data);
        },

        _waitForElement(selector, timeout = 2000) {
            return new Promise((resolve) => {
                if ($(selector).length) {
                    setTimeout(() => resolve($(selector)), 100);
                    return;
                }

                const startTime = Date.now();
                const interval = setInterval(() => {
                    if ($(selector).length) {
                        clearInterval(interval);
                        setTimeout(() => resolve($(selector)), 100);
                    } else if (Date.now() - startTime > timeout) {
                        clearInterval(interval);
                        console.warn(
                            `PlacementManager: Element ${selector} not found within timeout`,
                        );
                        resolve(null);
                    }
                }, 50);
            });
        },

        _waitForSelect2(selector, timeout = 2000) {
            return Select2Manager.waitForReady(selector, timeout);
        },

        _triggerCalculations() {
            $(
                ".reinsurer-written-share, .reinsurer-share, .reinsurer-premium, .reinsurer-comm-rate, .reinsurer-compulsory-acceptance, .reinsurer-optional-acceptance",
            ).trigger("input");
        },

        _showLoader() {
            const $modal = $(SELECTORS.MODAL);
            $modal.addClass("reinsurer-loading");
            $("#reinsurer-modal-loader").removeClass("d-none");
            $(SELECTORS.SAVE_BUTTON).prop("disabled", true);
        },

        _hideLoader() {
            const $modal = $(SELECTORS.MODAL);
            $modal.removeClass("reinsurer-loading");
            $("#reinsurer-modal-loader").addClass("d-none");
            $(SELECTORS.SAVE_BUTTON).prop("disabled", false);
        },

        _bindEvents() {
            const self = this;

            $(document)
                .off("click.placementRefresh")
                .on(
                    "click.placementRefresh",
                    ".refresh-reinsurers-btn",
                    function () {
                        self.fetchExisting();
                    },
                );

            $(SELECTORS.MODAL)
                .off("hidden.bs.modal.placementLoader")
                .on("hidden.bs.modal.placementLoader", function () {
                    self._hideLoader();
                });
        },
    };

    window.PlacementManager = PlacementManager;

    const CoverDetails = {
        $el: {},
        state: {},
        tables: {},

        init() {
            this._cacheElements();
            this._loadState();
            this._initPlugins();
            this._initTables();
            this._bindEvents();
            this._setActiveTab();
        },

        _cacheElements() {
            this.$el = {
                app: $(SELECTORS.APP),
                forms: {
                    schedules: $(SELECTORS.SCHEDULES_FORM),
                    attachments: $(SELECTORS.ATTACHMENTS_FORM),
                    clauses: $(SELECTORS.CLAUSES_FORM),
                    reinsurer: $(SELECTORS.REINSURER_FORM),
                    editReinsurer: $(SELECTORS.EDIT_REINSURER_FORM),
                    verify: $(SELECTORS.VERIFY_FORM),
                    facDebitForm: $(SELECTORS.FAC_DEBIT_FORM),
                },
                tabNav: $(".reinsurers-details-card .nav-link"),
            };
        },

        _loadState() {
            const $app = this.$el.app;
            this.state = {
                coverId: $app.data("cover-id"),
                endorsementNo: $app.data("endorsement-no"),
                typeOfBus: $app.data("type-of-bus"),
                coverNo: $app.data("cover-no"),
                scheduleItemContentById: {},
                suppressScheduleAutofillOnce: false,
                scheduleBreakdownQuill: null,
            };
        },

        _initPlugins() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        },

        _initTables() {
            const tableConfigs = this._getTableConfigs();

            Object.entries(tableConfigs).forEach(([name, config]) => {
                const $table = $(config.selector);
                if ($table.length) {
                    this.tables[name] = $table.DataTable({
                        order: [[0, "asc"]],
                        processing: true,
                        bAutoWidth: false,
                        lengthChange: false,
                        ...config.options,
                    });
                }
            });
        },

        _getTableConfigs() {
            const self = this;
            const baseAjaxConfig = (selector) => ({
                url: $(selector).data("url"),
                data: (d) => {
                    d.endorsement_no = self.state.endorsementNo;
                },
            });

            return {
                schedules: {
                    selector: SELECTORS.SCHEDULES_TABLE,
                    options: {
                        order: [[2, "asc"]],
                        ajax: baseAjaxConfig(SELECTORS.SCHEDULES_TABLE),
                        columns: [
                            { data: "id", render: (d, t, r, m) => m.row + 1 },
                            { data: "title" },
                            { data: "schedule_position" },
                            { data: "description" },
                            {
                                data: "action",
                                searchable: false,
                                sortable: false,
                            },
                        ],
                    },
                },
                attachments: {
                    selector: SELECTORS.ATTACHMENTS_TABLE,
                    options: {
                        ajax: baseAjaxConfig(SELECTORS.ATTACHMENTS_TABLE),
                        columns: [
                            { data: "id" },
                            { data: "title" },
                            { data: "action", searchable: false },
                        ],
                    },
                },
                clauses: {
                    selector: SELECTORS.CLAUSES_TABLE,
                    options: {
                        ajax: baseAjaxConfig(SELECTORS.CLAUSES_TABLE),
                        columns: [
                            {
                                data: "clause_id",
                                render: (d, t, r, m) => m.row + 1,
                            },
                            { data: "clause_title" },
                            { data: "clause_wording", className: "clamp-text" },
                            {
                                data: "action",
                                searchable: false,
                                sortable: false,
                            },
                        ],
                    },
                },
                reinsurers: {
                    selector: SELECTORS.REINSURERS_TABLE,
                    options: {
                        ajax: baseAjaxConfig(SELECTORS.REINSURERS_TABLE),
                        columns: this._getReinsurersColumns(),
                        paging: false,
                        drawCallback: function (settings) {
                            self._calculateTableFooter(
                                this.api(),
                                "reinsurers",
                            );
                        },
                    },
                },
                insClasses: {
                    selector: SELECTORS.INS_CLASSES_TABLE,
                    options: {
                        ajax: baseAjaxConfig(SELECTORS.INS_CLASSES_TABLE),
                        columns: [
                            {
                                data: null,
                                render: (d, t, r, m) => m.row + 1,
                                searchable: false,
                                sortable: false,
                            },
                            { data: "reinclass_name", defaultContent: "-" },
                            { data: "class", defaultContent: "-" },
                            { data: "class_name", defaultContent: "-" },
                            { data: "class_group", defaultContent: "-" },
                            {
                                data: "action",
                                defaultContent: "",
                                searchable: false,
                                sortable: false,
                            },
                        ],
                        paging: false,
                    },
                },
                debits: {
                    selector: SELECTORS.DEBITS_TABLE,
                    options: {
                        ajax: baseAjaxConfig(SELECTORS.DEBITS_TABLE),
                        columns: [
                            { data: "id", render: (d, t, r, m) => m.row + 1 },
                            { data: "cedant" },
                            { data: "dr_no" },
                            { data: "installment" },
                            { data: "share" },
                            {
                                data: "sum_insured",
                                render: $.fn.dataTable.render.number(
                                    ",",
                                    ".",
                                    2,
                                    "",
                                ),
                            },
                            {
                                data: "premium",
                                render: $.fn.dataTable.render.number(
                                    ",",
                                    ".",
                                    2,
                                    "",
                                ),
                            },
                            {
                                data: "gross",
                                render: $.fn.dataTable.render.number(
                                    ",",
                                    ".",
                                    2,
                                    "",
                                ),
                            },
                            {
                                data: "net_amt",
                                render: $.fn.dataTable.render.number(
                                    ",",
                                    ".",
                                    2,
                                    "",
                                ),
                            },
                            {
                                data: "action",
                                searchable: false,
                                sortable: false,
                            },
                        ],
                        paging: false,
                        drawCallback: function (settings) {
                            self._calculateTableFooter(this.api(), "debits");
                        },
                    },
                },
                approvals: {
                    selector: SELECTORS.APPROVALS_TABLE,
                    options: {
                        ajax: baseAjaxConfig(SELECTORS.APPROVALS_TABLE),
                        columns: [
                            { data: "id", render: (d, t, r, m) => m.row + 1 },
                            { data: "approver" },
                            { data: "comment" },
                            { data: "approver_comment" },
                            { data: "status" },
                            {
                                data: "action",
                                searchable: false,
                                sortable: false,
                            },
                        ],
                        paging: false,
                    },
                },
            };
        },

        _getReinsurersColumns() {
            const isFacultative = [
                BUSINESS_TYPES.FPR,
                BUSINESS_TYPES.FNP,
            ].includes(this.state.typeOfBus);
            const numberRender = $.fn.dataTable.render.number(",", ".", 2, "");

            const smartNumberRender = function (data, type, row) {
                if (data === null || data === undefined || data === "")
                    return "-";
                const num = parseFloat(data);
                if (isNaN(num)) return data;
                const hasDecimals = num % 1 !== 0;
                return num.toLocaleString("en-US", {
                    minimumFractionDigits: hasDecimals ? 2 : 0,
                    maximumFractionDigits: hasDecimals ? 2 : 0,
                });
            };

            let columns = [
                {
                    data: "tran_no",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                },
                {
                    data: "partner_name",
                    name: "partner_name",
                    render: function (data, type, row) {
                        return (
                            '<span class="fw-semibold">' +
                            Utils.escapeHtml(data || "-") +
                            "</span><br>" +
                            '<small class="text-muted">' +
                            Utils.escapeHtml(row.partner.email || "") +
                            "</small>"
                        );
                    },
                    defaultContent: "-",
                },
                {
                    data: "share",
                    render: function (data, type, row) {
                        return smartNumberRender(data) + "%";
                    },
                },
            ];

            if (isFacultative) {
                columns = columns.concat([
                    { data: "sum_insured", render: smartNumberRender },
                    { data: "premium", render: smartNumberRender },
                    {
                        data: "comm_rate",
                        render: function (data, type, row) {
                            return smartNumberRender(data) + "%";
                        },
                    },
                    { data: "commission", render: smartNumberRender },
                    { data: "brokerage_comm_amt", render: smartNumberRender },
                    { data: "wht_amt", render: smartNumberRender },
                    { data: "brokerage_comm_amt", render: smartNumberRender },
                    { data: "fronting_amt", render: smartNumberRender },
                    { data: "net_amount", render: smartNumberRender },
                ]);
            }

            columns.push({
                data: "action",
                searchable: false,
                sortable: false,
            });
            return columns;
        },

        _calculateTableFooter(api, tableType) {
            const $table = api.table().node();
            const $tfoot = $($table).find("tfoot");

            if (!$tfoot.length) {
                $($table).append("<tfoot></tfoot>");
            }

            const columnsToSum =
                tableType === "reinsurers"
                    ? [2, 3, 4, 6, 7, 8, 9]
                    : [4, 5, 6, 7, 8];
            const columnDefs = api.settings()[0]?.aoColumns || [];

            const columnCount = api.columns().nodes().length;
            const colSpan = tableType === "reinsurers" ? 2 : 4;

            let footerHtml = `<tr><td colspan="${colSpan}" style="text-align:right;font-weight:bold;">Totals:</td>`;

            for (let i = colSpan; i < columnCount - 1; i++) {
                if (columnsToSum.includes(i)) {
                    const sum = api
                        .column(i, { search: "applied" })
                        .data()
                        .reduce(
                            (a, b) =>
                                Utils.removeCommas(a) + Utils.removeCommas(b),
                            0,
                        );
                    const columnKey = columnDefs[i]?.data;
                    const isReinsurerPercentColumn =
                        tableType === "reinsurers" &&
                        typeof columnKey === "string" &&
                        /(share|rate)/i.test(columnKey) &&
                        !/(amount|amt|insured|premium|commission)/i.test(
                            columnKey,
                        );
                    const formattedTotal = Utils.formatNumber(sum);

                    footerHtml += `<td style="font-weight:bold;">${
                        isReinsurerPercentColumn
                            ? `${formattedTotal}%`
                            : formattedTotal
                    }</td>`;
                } else {
                    footerHtml += "<td></td>";
                }
            }

            footerHtml += "<td></td></tr>";
            $($table).find("tfoot").html(footerHtml).css({
                "background-color": "#f5f5f5",
                "border-top": "2px solid #ddd",
            });
        },

        _bindEvents() {
            this._bindNavigationEvents();
            this._bindFormEvents();
            this._bindTableEvents();
        },

        _bindNavigationEvents() {
            const self = this;

            this.$el.tabNav.on("click", function () {
                const hash = $(this).data("bs-target");
                if (hash) {
                    window.history.pushState(
                        null,
                        null,
                        window.location.pathname +
                            window.location.search +
                            hash,
                    );
                }
            });

            $(window).on("hashchange", () => this._setActiveTab());
        },

        _bindFormEvents() {
            // Form validation and submission handlers
            Object.entries(this.$el.forms).forEach(([name, $form]) => {
                if ($form.length && $.fn.validate) {
                    $form.validate({
                        errorClass: "errorClass",
                        submitHandler: (form, event) => {
                            event.preventDefault();

                            this._handleFormSubmit(name, form);
                        },
                    });
                }
            });
        },

        _bindTableEvents() {
            const self = this;

            // Edit actions
            $(document).on("click", ".edit-schedule", function () {
                const scheduleId = $(this).data("id");
                self._loadScheduleForEdit(scheduleId);
            });

            $(document).on("click", ".edit-reinsurer", function () {
                if (!$("#edit-reinsurer-modal").length) {
                    const rowData = $(this).data();
                    const $form = $(SELECTORS.FORM);
                    $form.find("#edit_mode").val("1");
                    $form
                        .find("#edit_tran_no")
                        .val(rowData.tranNo || rowData.tran_no || "");
                    $form
                        .find("#edit_partner_no")
                        .val(
                            rowData.partnerNo ||
                                rowData.partner_no ||
                                rowData.customerId ||
                                "",
                        );
                    $(SELECTORS.SAVE_BUTTON).html(
                        '<i class="fa fa-save me-2"></i>Update Placement',
                    );

                    if (
                        window.PlacementManager &&
                        typeof window.PlacementManager.setEditTarget ===
                            "function"
                    ) {
                        window.PlacementManager.setEditTarget({
                            tran_no: rowData.tranNo || rowData.tran_no || null,
                            partner_no:
                                rowData.partnerNo ||
                                rowData.partner_no ||
                                rowData.customerId ||
                                null,
                        });
                    }

                    if ($("#addReinsurerModal").length) {
                        $("#addReinsurerModal").modal("show");
                    }
                    return;
                }
                self._populateEditReinsurerForm($(this).data());
            });

            // Remove actions
            $(document).on(
                "click",
                ".remove-schedule, .remove-attachment, .remove-clause, .remove-reinsurer",
                function () {
                    const type = $(this).hasClass("remove-schedule")
                        ? "schedule"
                        : $(this).hasClass("remove-attachment")
                          ? "attachment"
                          : $(this).hasClass("remove-clause")
                            ? "clause"
                            : "reinsurer";
                    self._confirmRemove(type, $(this).data());
                },
            );

            $(document).on(
                "click",
                ".re-escalate-approval, .re-send-approval",
                function () {
                    const approvalId = $(this).data("approval-id");
                    self._reEscalateApproval(approvalId);
                },
            );

            $(SELECTORS.SCHEDULE_MODAL).on("show.bs.modal", () => {
                const $modal = $(SELECTORS.SCHEDULE_MODAL);
                const mode = $modal.data("mode") || "create";

                if (mode === "create") {
                    this._resetScheduleFormForCreate();
                }
            });

            $(SELECTORS.SCHEDULE_MODAL).on("hidden.bs.modal", () => {
                $(SELECTORS.SCHEDULE_MODAL).data("mode", "create");
            });

            $(document).on("change", "#sched-header", function () {
                const selectedId = String($(this).val() || "");
                if (!selectedId) {
                    return;
                }

                if (self.state.suppressScheduleAutofillOnce) {
                    self.state.suppressScheduleAutofillOnce = false;
                    return;
                }

                const $selected = $(this).find("option:selected");
                $("#title").val($selected.data("name") || "");

                const selectedPosition = $selected.data("position");
                if (selectedPosition !== undefined && selectedPosition !== "") {
                    $("#schedule_position").val(selectedPosition);
                }

                self._applySelectedScheduleContent(selectedId);
            });

            $(document).on("click", "#schedule_details_preview", () => {
                const selectedId = String($("#sched-header").val() || "");
                if (!selectedId) {
                    toastr.warning("Select a schedule item first");
                    return;
                }
                this._openScheduleBreakdownEditor();
            });

            $(document).on("click", "#loadExistingScheduleContentBtn", () => {
                this._loadExistingScheduleBreakdown();
            });

            $(document).on("click", "#clearScheduleBreakdownBtn", () => {
                if (this.state.scheduleBreakdownQuill) {
                    this.state.scheduleBreakdownQuill.setContents([]);
                }
            });

            $(document).on("click", "#previewScheduleBreakdownBtn", () => {
                this._previewScheduleBreakdown();
            });

            $(document).on("click", "#saveScheduleBreakdownBtn", () => {
                this._saveScheduleBreakdownEditor();
            });
        },

        _reEscalateApproval(approvalId) {
            if (!approvalId) {
                toastr.error("Approval reference is missing");
                return;
            }

            const reEscalateUrl = $(SELECTORS.APPROVALS_TABLE).data(
                "re-escalate-url",
            );
            if (!reEscalateUrl) {
                toastr.error("Re-escalation URL not configured");
                return;
            }

            Swal.fire({
                title: "Re-escalate Approval",
                text: "This will send the approval request again to the same approver.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, re-escalate",
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                fetch(reEscalateUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-Token": Utils.getCsrfToken(),
                    },
                    body: JSON.stringify({ approval_id: approvalId }),
                })
                    .then((res) => res.json())
                    .then((response) => {
                        if (response.success) {
                            toastr.success(
                                response.message ||
                                    "Approval re-escalated successfully",
                            );
                            this.tables?.approvals?.ajax?.reload(null, false);
                        } else if (response.errors) {
                            this._showValidationErrors(response.errors);
                        } else {
                            toastr.error(
                                response.message || "Failed to re-escalate",
                            );
                        }
                    })
                    .catch(() =>
                        toastr.error(
                            "An error occurred while re-escalating approval",
                        ),
                    );
            });
        },

        async _handleFormSubmit(formName, form) {
            if (formName === "schedules") {
                this._submitScheduleFormAjax(form);
                return;
            }

            if (
                formName === "reinsurer" &&
                window.ReinsurerPlacement?.formSubmissionManager
            ) {
                window.ReinsurerPlacement.formSubmissionManager.validateAndSubmit();
                return;
            }

            if (formName === "verify") {
                const preCheck = await this._runPreVerificationChecks(form);
                if (!preCheck.ok) {
                    this._showValidationErrors({
                        verification: preCheck.errors,
                    });
                    return;
                }
            }

            // Generic form submission
            const $form = $(form);
            const method = $form.find('[name="_method"]').val() || "POST";
            const url =
                method === "POST"
                    ? $form.data("post-url") || $form.attr("action")
                    : $form.data("put-url") || $form.attr("action");

            if (!url) {
                NotificationService.error("Form URL not configured");
                return;
            }

            const formData = new FormData(form);

            fetch(url, {
                method: "POST",
                headers: { "X-CSRF-Token": Utils.getCsrfToken() },
                body: formData,
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.status === 201) {
                        toastr.success(`Sumitted successfully`);
                        if (data.redirectUrl) {
                            window.location.href = data.redirectUrl;
                        } else {
                            setTimeout(() => location.reload(), 1500);
                        }
                    } else if (data.status === 422) {
                        this._showValidationErrors(data.errors);
                    } else {
                        toastr.error(data.message || "Failed to save");
                    }
                })
                .catch(() => toastr.error("An error occurred"));
        },

        _submitScheduleFormAjax(form) {
            const $form = $(form);
            const method = $form.find('[name="_method"]').val() || "POST";
            const url =
                method === "POST"
                    ? $form.data("post-url") || $form.attr("action")
                    : $form.data("put-url") || $form.attr("action");

            if (!url) {
                toastr.error("Schedule form URL not configured");
                return;
            }

            const formData = new FormData(form);
            $("#schedule-save-btn").prop("disabled", true);

            $.ajax({
                url: url,
                type: "POST",
                headers: { "X-CSRF-Token": Utils.getCsrfToken() },
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    if (response.status === 201) {
                        toastr.success("Submitted successfully");
                        $("#addScheduleModal").modal("hide");
                        this.tables?.schedules?.ajax?.reload(null, false);
                    } else if (response.status === 422) {
                        this._showValidationErrors(response.errors || response.message);
                    } else {
                        toastr.error(response.message || "Failed to save");
                    }
                },
                error: (xhr) => {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        this._showValidationErrors(xhr.responseJSON.errors);
                        return;
                    }

                    toastr.error(xhr.responseJSON?.message || "An error occurred");
                },
                complete: () => {
                    $("#schedule-save-btn").prop("disabled", false);
                },
            });
        },

        async _runPreVerificationChecks(form) {
            const $form = $(form);
            const preVerifyUrl = $form.data("pre-verify-url");

            if (!preVerifyUrl) {
                return { ok: true, errors: [] };
            }

            try {
                const formData = new FormData();
                formData.append(
                    "endorsement_no",
                    this.state.endorsementNo ||
                        $form.find('[name="endorsement_no"]').val() ||
                        "",
                );

                const response = await fetch(preVerifyUrl, {
                    method: "POST",
                    headers: { "X-CSRF-Token": Utils.getCsrfToken() },
                    body: formData,
                });

                const result = await response.json();
                if (Array.isArray(result) && result.length > 0) {
                    return { ok: false, errors: result };
                }

                return { ok: true, errors: [] };
            } catch (error) {
                return {
                    ok: false,
                    errors: [
                        "Unable to run pre-verification checks. Please try again.",
                    ],
                };
            }
        },

        _populateScheduleForm(data) {
            const $form = this.$el.forms.schedules;
            $form[0].reset();
            $form.find('[name="_method"]').val("PUT");
            $form.find("#title").val(data.title);
            $form.find("#id").val(data.id);
            const selectedHeaderId = data.header || data.schedule_id;
            $form.find("#schedule_id").val(selectedHeaderId || "");
            $("#schedule_position").val(data.schedule_position || "");
            $("#schedule_item_description").val(data.description || "");
            this._setScheduleDetails(data.details || "");
            this.state.suppressScheduleAutofillOnce = true;

            this._loadAvailableScheduleItems({
                includeHeaderId: selectedHeaderId,
                selectedHeaderId: selectedHeaderId,
            });
        },

        _loadScheduleForEdit(scheduleId) {
            const $form = this.$el.forms.schedules;
            if (!$form.length) return;

            const fetchUrl = $form.data("fetch-url");
            if (!fetchUrl) {
                toastr.error("Schedule fetch URL not configured");
                return;
            }

            const endorsementNo =
                this.state.endorsementNo ||
                $form.find('[name="endorsement_no"]').val() ||
                "";

            if (!scheduleId || !endorsementNo) {
                toastr.error("Schedule reference missing");
                return;
            }

            const params = new URLSearchParams({
                id: String(scheduleId),
                endorsement_no: endorsementNo,
            });

            fetch(`${fetchUrl}?${params.toString()}`, {
                method: "GET",
                headers: { "X-CSRF-Token": Utils.getCsrfToken() },
            })
                .then((res) => res.json())
                .then((response) => {
                    if (response.status !== 200 || !response.data) {
                        toastr.error(response.message || "Failed to load schedule");
                        return;
                    }

                    this._populateScheduleForm(response.data);
                    $(SELECTORS.SCHEDULE_MODAL).data("mode", "edit").modal("show");
                })
                .catch(() => toastr.error("Failed to load schedule"));
        },

        _resetScheduleFormForCreate() {
            const $form = this.$el.forms.schedules;
            if (!$form.length) return;

            $form[0].reset();
            $form.find('[name="_method"]').val("POST");
            $form.find("#id").val("");
            $form.find("#schedule_id").val("");
            $form.find("#title").val("");
            $("#schedule_position").val("");
            $("#schedule_item_description").val("");
            this._setScheduleDetails("");
            $("#scheduleDynamicFields").empty();

            this._loadAvailableScheduleItems();
        },

        _loadAvailableScheduleItems({
            includeHeaderId = null,
            selectedHeaderId = null,
        } = {}) {
            const normalizeItemKey = (value) =>
                String(value || "")
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, "");
            const excludedItemKeys = new Set([
                "suminsured",
                "premium",
                "deductibleexcess",
            ]);

            const $form = this.$el.forms.schedules;
            if (!$form.length) return;

            const endpoint = $form.data("schedule-items-url");
            if (!endpoint) return;

            const endorsementNo =
                this.state.endorsementNo ||
                $form.find('[name="endorsement_no"]').val() ||
                "";

            if (!endorsementNo) return;

            const params = new URLSearchParams({ endorsement_no: endorsementNo });
            if (includeHeaderId) {
                params.set("include_header_id", String(includeHeaderId));
            }

            fetch(`${endpoint}?${params.toString()}`, {
                method: "GET",
                headers: { "X-CSRF-Token": Utils.getCsrfToken() },
            })
                .then((res) => res.json())
                .then((response) => {
                    const items = Array.isArray(response?.data)
                        ? response.data
                        : [];
                    const filteredItems = items.filter((item) => {
                        return !excludedItemKeys.has(
                            normalizeItemKey(item?.name),
                        );
                    });

                    this.state.scheduleItemContentById = {};

                    const $select = $("#sched-header");
                    $select.empty().append(
                        '<option value="">--Select Schedule items--</option>',
                    );

                    filteredItems.forEach((item) => {
                        const key = String(item.id);
                        this.state.scheduleItemContentById[key] = String(
                            item.content || "",
                        );

                        $select.append(
                            $("<option>", {
                                value: item.id,
                                text: item.name,
                                "data-name": item.name,
                                "data-position": item.position ?? "",
                            }),
                        );
                    });

                    if (selectedHeaderId) {
                        $select.val(String(selectedHeaderId));
                    }

                    $select.trigger("change");
                })
                .catch(() => {
                    toastr.error("Failed to load schedule items");
                });
        },

        _applySelectedScheduleContent(selectedId) {
            const html =
                this.state.scheduleItemContentById[String(selectedId)] || "";
            this._setScheduleDetails(html);
        },

        _setScheduleDetails(htmlContent) {
            const html = String(htmlContent || "");
            const plainText = $("<div>").html(html).text();

            $("#hidden_schedule_description").val(html);
            $("#schedule_details_preview").val(plainText);
        },

        _openScheduleBreakdownEditor() {
            const selectedId = String($("#sched-header").val() || "");
            if (!selectedId) {
                toastr.warning("Select a schedule item first");
                return;
            }

            this._initScheduleBreakdownEditor();

            const $selected = $("#sched-header").find("option:selected");
            const label = ($selected.data("name") || $selected.text() || "Breakdown")
                .toString()
                .trim();
            $("#scheduleBreakdownModalLabel").html(
                `<i class="bx bx-edit-alt me-1"></i> ${label}`,
            );

            const currentHtml = $("#hidden_schedule_description").val() || "";
            if (this.state.scheduleBreakdownQuill) {
                this.state.scheduleBreakdownQuill.clipboard.dangerouslyPasteHTML(
                    currentHtml || "",
                );
            }
            $("#scheduleBreakdownModal").modal("show");
        },

        _saveScheduleBreakdownEditor() {
            let editedHtml = "";
            if (this.state.scheduleBreakdownQuill) {
                editedHtml = this.state.scheduleBreakdownQuill.root.innerHTML || "";
            } else {
                editedHtml = $("#schedule_breakdown_editor").html() || "";
            }
            this._setScheduleDetails(editedHtml);
            $("#scheduleBreakdownModal").modal("hide");
        },

        _initScheduleBreakdownEditor() {
            if (this.state.scheduleBreakdownQuill || typeof Quill === "undefined") {
                return;
            }

            this.state.scheduleBreakdownQuill = new Quill("#schedule_breakdown_editor", {
                theme: "snow",
                modules: {
                    toolbar: "#schedule_breakdown_toolbar",
                },
                placeholder: "Write schedule breakdown details here...",
            });
        },

        _loadExistingScheduleBreakdown() {
            const selectedId = String($("#sched-header").val() || "");
            const existing =
                this.state.scheduleItemContentById[selectedId] ||
                $("#hidden_schedule_description").val() ||
                "";

            this._initScheduleBreakdownEditor();
            if (this.state.scheduleBreakdownQuill) {
                this.state.scheduleBreakdownQuill.clipboard.dangerouslyPasteHTML(
                    existing,
                );
            }
        },

        _previewScheduleBreakdown() {
            const html = this.state.scheduleBreakdownQuill
                ? this.state.scheduleBreakdownQuill.root.innerHTML || ""
                : $("#hidden_schedule_description").val() || "";

            const previewWindow = window.open("", "_blank", "noopener,noreferrer");
            if (!previewWindow) {
                toastr.error("Unable to open preview window");
                return;
            }

            previewWindow.document.write(`
                <html>
                    <head><title>Breakdown Preview</title></head>
                    <body style="font-family: Arial, sans-serif; padding: 20px;">
                        ${html}
                    </body>
                </html>
            `);
            previewWindow.document.close();
        },

        _populateEditReinsurerForm(data) {
            const reinsurerData = data.data;
            const reinsurer = data.reinsurer;

            $("#edtran_no").val(reinsurerData.tran_no);
            $("#edreinsurer-share").val(
                parseFloat(reinsurerData.share).toFixed(2),
            );
            $("#edreinsurer-written-share").val(
                parseFloat(reinsurerData.written_lines).toFixed(2),
            );
            $("#edreinsurer-wht_rate")
                .val(parseFloat(reinsurerData.wht_rate).toFixed(2))
                .trigger("change");
            $("#edreinsurer-sum_insured").val(
                Utils.formatNumber(reinsurerData.sum_insured),
            );
            $("#edreinsurer-premium").val(
                parseFloat(reinsurerData.premium).toFixed(2),
            );
            $("#edreinsurer-comm_rate").val(
                Utils.formatNumber(reinsurerData.comm_rate),
            );
            $("#edreinsurer-comm_amt").val(
                parseFloat(reinsurerData.commission).toFixed(2),
            );
            $("#edreinsurer").val(reinsurer.customer_id);
        },

        _confirmRemove(type, data) {
            const titles = {
                schedule: "Remove Schedule",
                attachment: "Remove Attachment",
                clause: "Remove Clause",
                reinsurer: "Remove Reinsurer",
            };

            Swal.fire({
                title: titles[type],
                text: `Are you sure you want to remove this ${type}?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, remove",
            }).then((result) => {
                if (result.isConfirmed) {
                    this._performRemove(type, data);
                }
            });
        },

        _performRemove(type, data) {
            const tableSelector = {
                schedule: SELECTORS.SCHEDULES_TABLE,
                attachment: SELECTORS.ATTACHMENTS_TABLE,
                clause: SELECTORS.CLAUSES_TABLE,
                reinsurer: SELECTORS.REINSURERS_TABLE,
            }[type];

            const url = $(tableSelector).data("delete-url");
            const record = this._normalizeActionData(data);
            const payload = {
                cover_no: this.state.coverNo,
                endorsement_no: this.state.endorsementNo,
                id: data.id,
                ...(type === "clause" && { clause_id: data.id }),
                ...(type === "reinsurer" && {
                    tran_no: record.tran_no,
                    reinsurer: record.partner_no,
                }),
            };

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": Utils.getCsrfToken(),
                },
                body: JSON.stringify(payload),
            })
                .then((res) => res.json())
                .then((response) => {
                    if (response.status === 201) {
                        toastr.success("Removed successfully");
                        const tableKey = {
                            schedule: "schedules",
                            attachment: "attachments",
                            clause: "clauses",
                            reinsurer: "reinsurers",
                        }[type];

                        const table = tableKey ? this.tables?.[tableKey] : null;
                        if (table?.ajax?.reload) {
                            table.ajax.reload(null, false);
                        } else {
                            location.reload();
                        }
                    } else {
                        toastr.error(response.message || "Failed to remove");
                    }
                })
                .catch(() => toastr.error("An error occurred"));
        },

        _normalizeActionData(data = {}) {
            const parsedData =
                typeof data.data === "string"
                    ? (() => {
                          try {
                              return JSON.parse(data.data);
                          } catch (e) {
                              return {};
                          }
                      })()
                    : data.data || {};

            const parsedReinsurer =
                typeof data.reinsurer === "string"
                    ? (() => {
                          try {
                              return JSON.parse(data.reinsurer);
                          } catch (e) {
                              return {};
                          }
                      })()
                    : data.reinsurer || {};

            return {
                tran_no: data.tranNo || data.tran_no || parsedData.tran_no,
                partner_no:
                    data.partnerNo ||
                    data.partner_no ||
                    parsedData.partner_no ||
                    parsedReinsurer.customer_id,
            };
        },

        _showValidationErrors(errors) {
            if (typeof errors === "object") {
                Object.values(errors)
                    .flat()
                    .forEach((error) => toastr.error(error));
            } else {
                toastr.error("Validation error occurred");
            }
        },

        _setActiveTab() {
            const isCoverHomePage =
                window.location.pathname.includes("/cover/cover-home");
            const hasInsClassesTab = $("#ins-classes-tab").length > 0;
            const defaultHash =
                isCoverHomePage && hasInsClassesTab
                    ? "#ins-classes-tab"
                    : "#schedules-tab";
            let hash = window.location.hash || defaultHash;

            if (!$(`.nav-link[data-bs-target="${hash}"]`).length) {
                hash = defaultHash;
            }

            this.$el.tabNav
                .removeClass("active")
                .attr("aria-selected", "false");
            $(".tab-pane").removeClass("active show");

            const $targetTab = $(`.nav-link[data-bs-target="${hash}"]`);
            $targetTab.addClass("active").attr("aria-selected", "true");
            $(hash).addClass("active show");
        },
    };

    const ReinsurerPlacement = {
        calculationService: null,
        validationService: null,

        // Managers
        reinsurerManager: null,
        treatyManager: null,
        distributionManager: null,
        commissionManager: null,
        installmentManager: null,
        brokerageManager: null,
        retroFeeManager: null,
        formSubmissionManager: null,
        isSyncingPayMethod: false,

        // Cover data
        coverData: {},

        init() {
            this._loadCoverData();
            this._initServices();
            this._bindEvents();
            this._initializeUI();
        },

        _loadCoverData() {
            this.coverData = {
                share_offered: Utils.getElementValue("#share_offered"),
                total_sum_insured: Utils.getElementValue("#total_sum_insured"),
                rein_premium: Utils.getElementValue("#rein_premium"),
                rein_comm_amount: Utils.getElementValue("#rein_comm_amount"),
                cedant_comm_rate: Utils.getElementValue("#cedant_comm_rate"),
                brokerage_comm_rate: Utils.getElementValue(
                    "#brokerage_comm_rate",
                ),
                rein_comm_rate: $("#rein_comm_rate").val() || 0,
                brokerage_comm_type: $("#brokerage_comm_type").val() || "R",
                type_of_bus: $("#type_of_bus").val() || BUSINESS_TYPES.FPR,
            };
        },

        _initServices() {
            this.calculationService = new CalculationService(this.coverData);
            this.validationService = new ValidationService(this.coverData);

            this.brokerageManager = new BrokerageManager(
                this.calculationService,
            );
            this.retroFeeManager = new RetroFeeManager(this.calculationService);
            this.commissionManager = new CommissionManager(
                this.calculationService,
            );
            this.installmentManager = new InstallmentManager(
                this.calculationService,
            );
            this.distributionManager = new DistributionManager(
                this.calculationService,
            );

            this.reinsurerManager = new ReinsurerManager(
                this.calculationService,
                this.validationService,
                this.brokerageManager,
            );

            this.treatyManager = new TreatyManager(this.reinsurerManager);

            this.formSubmissionManager = new FormSubmissionManager(
                this.validationService,
                this.coverData,
            );
        },

        _bindEvents() {
            const self = this;

            $(document).on("click", "#add-treaty-reinsurer", (e) => {
                e.preventDefault();
                this.treatyManager.addSection();
            });

            $(document).on("click", ".remove-treaty-section", function (e) {
                e.preventDefault();
                self.treatyManager.removeSection($(this).data("counter"));
            });

            $(document).on("click", ".add-reinsurer-btn", function (e) {
                e.preventDefault();
                const treatyCounter = $(this).data("treaty-counter");
                const distribution =
                    self.distributionManager.calculate(treatyCounter);

                if (distribution.remaining <= CONFIG.TOLERANCE) {
                    if (typeof toastr !== "undefined") {
                        toastr.warning(
                            "Cannot add more reinsurers: Undistributed is 0",
                        );
                    } else {
                        NotificationService.warning(
                            "Cannot add more reinsurers: Undistributed is 0",
                        );
                    }
                    return;
                }

                self.reinsurerManager.addRow(treatyCounter);
            });

            $(document).on("click", ".remove-reinsurer-btn", function (e) {
                e.preventDefault();
                const $btn = $(this);

                self.validationService.reset();
                $(SELECTORS.VALIDATION_LIST).empty();
                $(SELECTORS.VALIDATION_MESSAGES).hide();

                self.reinsurerManager.removeRow(
                    $btn.data("treaty-counter"),
                    $btn.data("counter"),
                );
            });

            $(document).on("change", ".reinsurer", function () {
                self.reinsurerManager.filterSelected(
                    $(this).data("treaty-counter"),
                );
            });

            const handleShareInput = Utils.debounce(function () {
                const $el = $(this);
                const tc = $el.data("treaty-counter");
                const c = $el.data("counter");

                self._validateSignedVsWritten(tc, c);
                self._enforceOfferedShareLimit(tc, c);
                self.distributionManager.calculate(tc);
                self.distributionManager.handleShareInput(tc, c);
            }, CONFIG.DEBOUNCE_DELAY);

            $(document).on(
                "input",
                ".reinsurer-written-share, .reinsurer-share",
                handleShareInput,
            );

            $(document).on(
                "input",
                ".reinsurer-premium, .reinsurer-comm-rate",
                function () {
                    const tc = $(this).data("treaty-counter");
                    const c = $(this).data("counter");
                    self.commissionManager.calculate(tc, c);
                    self.brokerageManager.calculate(tc, c);
                },
            );

            $(document).on("change", ".brokerage-comm-type", function () {
                self.brokerageManager.handleTypeChange(
                    $(this).data("treaty-counter"),
                    $(this).data("counter"),
                );
            });

            $(document).on(
                "input",
                ".reinsurer-brokerage-comm-amt",
                function () {
                    self.brokerageManager.calculate(
                        $(this).data("treaty-counter"),
                        $(this).data("counter"),
                    );
                },
            );

            $(document).on("change", ".reinsurer-calc-option", function () {
                const tc = $(this).data("treaty-counter");
                const c = $(this).data("counter");
                const $checkbox = $(this);
                const optionKey =
                    self.reinsurerManager.getCalcOptionKey($checkbox);

                if (optionKey) {
                    self.reinsurerManager.syncCalcOptionAcrossTreaty(
                        tc,
                        optionKey,
                        $checkbox.is(":checked"),
                    );
                } else {
                    self.commissionManager.calculate(tc, c);
                    self.brokerageManager.calculate(tc, c);
                }
            });

            $(document).on("change", ".apply-fronting", function () {
                self.retroFeeManager.handleToggle(
                    $(this).data("treaty-counter"),
                    $(this).data("counter"),
                );
            });

            $(document).on("input", ".reinsurer-fronting-rate", function () {
                self.retroFeeManager.calculateAmount(
                    $(this).data("treaty-counter"),
                    $(this).data("counter"),
                );
            });

            $(document).on("input", ".reinsurer-fronting-amt", function () {
                self.retroFeeManager.calculateRate(
                    $(this).data("treaty-counter"),
                    $(this).data("counter"),
                );
            });

            // Payment method events
            $(document).on("change", ".reins-pay-method", function () {
                if (self.isSyncingPayMethod) return;

                const selectedMethod = $(this).val();
                if (!selectedMethod) return;

                self.isSyncingPayMethod = true;

                $(SELECTORS.TREATY_SECTION)
                    .find(".reins-pay-method")
                    .each(function () {
                        const $payMethod = $(this);
                        const treatyCounter = $payMethod.data("treaty-counter");
                        const counter = $payMethod.data("counter");

                        if (
                            String($payMethod.val() || "") !==
                            String(selectedMethod)
                        ) {
                            Select2Manager.setValue($payMethod, selectedMethod);
                        }

                        self.installmentManager.handlePaymentMethodChange(
                            treatyCounter,
                            counter,
                            selectedMethod,
                        );
                    });

                self.isSyncingPayMethod = false;
            });

            $(document).on(
                "click",
                ".add-reinsurer-installments",
                function (e) {
                    e.preventDefault();
                    self.installmentManager.generate(
                        $(this).data("treaty-counter"),
                        $(this).data("counter"),
                    );
                },
            );

            // Modal events
            $(SELECTORS.MODAL).on("shown.bs.modal", () => {
                const $form = $(SELECTORS.FORM);
                const isEditMode =
                    String($form.find("#edit_mode").val()) === "1";
                if (!isEditMode) {
                    $form.find("#edit_mode").val("0");
                    $form.find("#edit_tran_no").val("");
                    $form.find("#edit_partner_no").val("");
                    $(SELECTORS.SAVE_BUTTON).html(
                        '<i class="fa fa-save me-2"></i>Save Placement',
                    );
                }
                Select2Manager.init();
                SummaryManager.refresh();
                this._initializeDisplays();
            });

            $(SELECTORS.MODAL).on("hidden.bs.modal", () => {
                const $form = $(SELECTORS.FORM);
                $form.find("#edit_mode").val("0");
                $form.find("#edit_tran_no").val("");
                $form.find("#edit_partner_no").val("");
                $(SELECTORS.SAVE_BUTTON).html(
                    '<i class="fa fa-save me-2"></i>Save Placement',
                );
            });

            // Custom event for reinsurer removal
            $(document).on("reinsurer:removed", (e, treatyCounter) => {
                this.distributionManager.calculate(treatyCounter);
            });
        },

        _initializeUI() {
            Select2Manager.init();
            this.distributionManager.initFromPartners(
                window.coverpartners || [],
            );
            this.distributionManager.calculate(0);
            this._initializeDisplays();
        },

        _initializeDisplays() {
            $(SELECTORS.REINSURER_SECTION).each((_, el) => {
                const tc = $(el).data("treaty-counter");
                const c = $(el).data("counter");

                if (tc !== undefined && c !== undefined) {
                    this.brokerageManager.handleTypeChange(tc, c);
                    this.retroFeeManager.handleToggle(tc, c);
                }
            });
        },

        _validateSignedVsWritten(treatyCounter, counter) {
            const written = Utils.getElementValue(
                `#written_share-${treatyCounter}-${counter}`,
            );
            const signed = Utils.getElementValue(
                `#share-${treatyCounter}-${counter}`,
            );
            const $signedInput = $(`#share-${treatyCounter}-${counter}`);

            if (signed > written + CONFIG.TOLERANCE) {
                $signedInput
                    .addClass("is-invalid")
                    .val(Utils.toDecimal(written));
                NotificationService.warning(
                    "Signed share cannot exceed written share",
                );
            } else {
                $signedInput.removeClass("is-invalid");
            }
        },

        _enforceOfferedShareLimit(treatyCounter, counter) {
            const $signedInput = $(`#share-${treatyCounter}-${counter}`);
            if (!$signedInput.length) return;

            const offered =
                Utils.getElementValue(`#share_offered-${treatyCounter}`) ||
                this.coverData.share_offered ||
                0;

            let otherSigned = 0;
            $(`#reinsurer-div-${treatyCounter} .reinsurer-share`).each(
                function () {
                    const $current = $(this);
                    if (
                        parseInt($current.data("counter"), 10) ===
                        parseInt(counter, 10)
                    ) {
                        return;
                    }
                    otherSigned += Utils.clampNumber($current.val(), 0);
                },
            );

            const currentSigned = Utils.clampNumber($signedInput.val(), 0);
            const maxForCurrent = Math.max(0, offered - otherSigned);

            if (currentSigned > maxForCurrent + CONFIG.TOLERANCE) {
                $signedInput
                    .addClass("is-invalid")
                    .val(Utils.toDecimal(maxForCurrent));
                NotificationService.warning(
                    `Signed share cannot exceed offered share (${Utils.toDecimal(
                        offered,
                    )}%)`,
                );
                return;
            }

            $signedInput.removeClass("is-invalid");
        },
    };

    const TreatyCalculations = {
        init() {
            this._bindEvents();
        },

        _bindEvents() {
            const self = this;

            $(document).on(
                "input change",
                ".reinsurer-compulsory-acceptance, .reinsurer-optional-acceptance",
                function () {
                    const tc = $(this).data("treaty-counter");
                    const c = $(this).data("counter");
                    self._calculateTotalAcceptance(tc, c);
                    self._validateAgainstWritten(tc, c);
                    self._updateDistributionSummary(tc);
                },
            );
        },

        _calculateTotalAcceptance(tc, c) {
            const compulsory = Utils.getElementValue(
                `#compulsory_acceptance-${tc}-${c}`,
            );
            const optional = Utils.getElementValue(
                `#optional_acceptance-${tc}-${c}`,
            );
            const total = compulsory + optional;

            const $totalInput = $(`#total_acceptance-${tc}-${c}`);
            $totalInput.val(total.toFixed(2));
            $totalInput
                .toggleClass("bg-success bg-opacity-10", total > 0)
                .toggleClass("bg-light", total <= 0);
        },

        _validateAgainstWritten(tc, c) {
            const written = Utils.getElementValue(`#written_share-${tc}-${c}`);
            const total = Utils.getElementValue(`#total_acceptance-${tc}-${c}`);

            const $totalInput = $(`#total_acceptance-${tc}-${c}`);
            const $writtenInput = $(`#written_share-${tc}-${c}`);

            $totalInput.removeClass("is-invalid is-valid");
            $writtenInput.removeClass("is-invalid");

            if (total > written + CONFIG.TOLERANCE) {
                $totalInput.addClass("is-invalid");
                $writtenInput.addClass("is-invalid");
                return false;
            }

            if (total > 0) {
                $totalInput.addClass("is-valid");
            }

            return true;
        },

        _updateDistributionSummary(tc) {
            const offered =
                Utils.getElementValue(`#share_offered-${tc}`) ||
                ReinsurerPlacement.coverData.share_offered;
            let distributed = 0;

            $(`.reinsurer-total-acceptance[data-treaty-counter="${tc}"]`).each(
                function () {
                    distributed += Utils.removeCommas($(this).val());
                },
            );

            const remaining = offered - distributed;

            $(`#distributed_share-${tc}`).val(distributed.toFixed(2));
            const $remainingField = $(`#rem_share-${tc}`);
            $remainingField.val(remaining.toFixed(2));

            $remainingField.removeClass(
                "bg-danger bg-warning bg-success text-white",
            );
            if (remaining < -CONFIG.TOLERANCE) {
                $remainingField.addClass("bg-danger text-white");
            } else if (remaining > CONFIG.TOLERANCE) {
                $remainingField.addClass("bg-warning");
            } else {
                $remainingField.addClass("bg-success text-white");
            }
        },
    };

    $(document).ready(function () {
        if ($(SELECTORS.APP).length) {
            CoverDetails.init();
        }

        window.ReinsurerPlacement = ReinsurerPlacement;
        ReinsurerPlacement.init();

        window.TreatyCalculations = TreatyCalculations;
        TreatyCalculations.init();
    });
})(jQuery);
