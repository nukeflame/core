(function ($) {
    "use strict";

    const CONSTANTS = {
        DECIMAL_PRECISION: 2,
        TOLERANCE: 0.001,
        MAX_INSTALLMENTS: 12,
        MIN_INSTALLMENTS: 1,
        ANIMATION_DURATION: 300,
        NOTIFICATION_DURATION: 5000,
        PERCENTAGE_MULTIPLIER: 100,
        SELECT2_INIT_DELAY: 100,
        FPR: "FPR",
        FNP: "FNP",
    };

    const DOM_SELECTORS = {
        TREATY_DIV: "#treaty-div",
        TREATY_SECTION: ".treaty-div-section",
        REINSURER_SECTION: ".reinsurer-section",
        REINSURER_ROW_TEMPLATE: "#reinsurer-row-template",
        MODAL: "#addReinsurerModal",
        FORM: "#reinsurerForm",
        SAVE_BUTTON: "#partner-save-btn",
        VALIDATION_MESSAGES: "#validation-messages",
        VALIDATION_LIST: "#validation-list",
    };

    const Utils = {
        removeCommas(value) {
            if (!value) return 0;
            return parseFloat(value.toString().replace(/,/g, "")) || 0;
        },

        numberWithCommas(value) {
            const num = parseFloat(value);
            if (isNaN(num)) return "0.00";
            return num
                .toFixed(CONSTANTS.DECIMAL_PRECISION)
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        toDecimal(number) {
            return parseFloat(
                Number(number).toFixed(CONSTANTS.DECIMAL_PRECISION)
            );
        },

        areDecimalsEqual(num1, num2, tolerance = CONSTANTS.TOLERANCE) {
            return (
                Math.abs(this.toDecimal(num1) - this.toDecimal(num2)) <=
                tolerance
            );
        },

        getElementValue(selector, defaultValue = 0) {
            const $element = $(selector);
            if (!$element.length) return defaultValue;
            return this.removeCommas($element.val()) || defaultValue;
        },

        replacePlaceholders(template, replacements) {
            let result = template;
            Object.keys(replacements).forEach((key) => {
                const regex = new RegExp(key, "g");
                result = result.replace(regex, replacements[key]);
            });
            return result;
        },

        escapeHtml(text) {
            const map = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;",
            };
            return text.replace(/[&<>"']/g, (m) => map[m]);
        },

        validateNumber(value, defaultValue = 0, min = null, max = null) {
            const num = this.removeCommas(value);

            if (isNaN(num) || num === null || num === undefined) {
                return defaultValue;
            }

            if (min !== null && num < min) return min;
            if (max !== null && num > max) return max;

            return num;
        },
    };

    const NotificationService = {
        activeNotifications: [],

        show(type, message) {
            const alertClass =
                {
                    success: "alert-success",
                    error: "alert-danger",
                    warning: "alert-warning",
                    info: "alert-info",
                }[type] || "alert-info";

            const iconClass =
                {
                    success: "fa-check-circle",
                    error: "fa-times-circle",
                    warning: "fa-exclamation-triangle",
                    info: "fa-info-circle",
                }[type] || "fa-info-circle";

            const $notification = $(`
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed notification-alert"
                    style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
                    role="alert">
                    <i class="fa ${iconClass} me-2"></i>${Utils.escapeHtml(
                message
            )}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);

            $("body").append($notification);

            this.activeNotifications.push($notification);

            $notification.find(".btn-close").on("click", () => {
                this.removeFromTracking($notification);
            });

            setTimeout(() => {
                $notification.fadeOut(
                    CONSTANTS.ANIMATION_DURATION,
                    function () {
                        $(this).remove();
                    }
                );
                this.removeFromTracking($notification);
            }, CONSTANTS.NOTIFICATION_DURATION);

            return $notification;
        },

        success(message) {
            return this.show("success", message);
        },

        error(message) {
            return this.show("error", message);
        },

        warning(message) {
            return this.show("warning", message);
        },

        info(message) {
            return this.show("info", message);
        },

        clear() {
            this.activeNotifications.forEach(($notification) => {
                if ($notification && $notification.length) {
                    $notification.fadeOut(200, function () {
                        $(this).remove();
                    });
                }
            });

            this.activeNotifications = [];

            $(".notification-alert").fadeOut(200, function () {
                $(this).remove();
            });
        },

        removeFromTracking($notification) {
            const index = this.activeNotifications.indexOf($notification);
            if (index > -1) {
                this.activeNotifications.splice(index, 1);
            }
        },

        clearType(type) {
            const alertClass = {
                success: "alert-success",
                error: "alert-danger",
                warning: "alert-warning",
                info: "alert-info",
            }[type];

            if (alertClass) {
                $(`.notification-alert.${alertClass}`).fadeOut(
                    200,
                    function () {
                        $(this).remove();
                    }
                );

                this.activeNotifications = this.activeNotifications.filter(
                    ($notification) => {
                        return !$notification.hasClass(alertClass);
                    }
                );
            }
        },

        getCount() {
            return this.activeNotifications.length;
        },
    };

    const Select2Manager = {
        initialize() {
            if (!$.fn.select2) return;

            $(".select2Placement").each(function () {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2("destroy");
                }
            });

            $(".select2Placement").select2({
                dropdownParent: $(DOM_SELECTORS.MODAL),
                width: "100%",
                placeholder: function () {
                    return $(this).find("option:first").text();
                },
            });
        },

        initializeInContainer(containerSelector) {
            if (!$.fn.select2) return;

            $(`${containerSelector} .select2Placement`).each(function () {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2("destroy");
                }

                $(this).select2({
                    dropdownParent: $(DOM_SELECTORS.MODAL),
                    width: "100%",
                    placeholder: function () {
                        return $(this).find("option:first").text();
                    },
                });
            });
        },
    };

    const SummaryManager = {
        refreshSummary() {
            let totalOffered = 0;
            let totalDistributed = 0;
            let totalReinsurers = 0;

            $(DOM_SELECTORS.TREATY_SECTION).each(function () {
                const offered = Utils.getElementValue(
                    $(this).find(".share_offered"),
                    0
                );
                const distributed = Utils.getElementValue(
                    $(this).find(".distributed-share"),
                    0
                );

                totalOffered += offered;
                totalDistributed += distributed;
                totalReinsurers += $(this).find(
                    DOM_SELECTORS.REINSURER_SECTION
                ).length;
            });

            const totalRemaining = totalOffered - totalDistributed;
            const percentagePlaced =
                totalOffered > 0
                    ? (totalDistributed / totalOffered) *
                      CONSTANTS.PERCENTAGE_MULTIPLIER
                    : 0;

            this.updateSummaryDisplay(
                totalOffered,
                totalDistributed,
                totalRemaining,
                totalReinsurers,
                percentagePlaced
            );
        },

        updateSummaryDisplay(
            offered,
            distributed,
            remaining,
            count,
            percentage
        ) {
            if (!$("#summary-total-offered").length) return;

            $("#summary-total-offered").text(Utils.toDecimal(offered) + "%");
            $("#summary-total-distributed").text(
                Utils.toDecimal(distributed) + "%"
            );
            $("#summary-total-remaining .remaining-value").text(
                Math.abs(Utils.toDecimal(remaining)) + "%"
            );
            $("#summary-reinsurer-count").text(count);

            $("#distribution-progress-bar")
                .css("width", percentage + "%")
                .attr("aria-valuenow", percentage)
                .find(".progress-text")
                .text(percentage.toFixed(1) + "% Placed");

            this.updateStatusAlert(remaining, percentage);
        },

        updateStatusAlert(remaining, percentagePlaced) {
            const $alert = $("#distribution-status-alert");
            if (!$alert.length) return;

            $alert.removeClass(
                "alert-success alert-warning alert-danger alert-info"
            );

            if (Math.abs(remaining) < CONSTANTS.TOLERANCE) {
                $alert
                    .addClass("alert-success")
                    .html(
                        '<i class="fa fa-check-circle"></i> <strong>Perfect!</strong> All shares have been fully distributed.'
                    )
                    .fadeIn();
            } else if (remaining > 0) {
                $alert
                    .addClass("alert-warning")
                    .html(
                        `<i class="fa fa-exclamation-triangle"></i> <strong>Incomplete:</strong> ${Utils.toDecimal(
                            remaining
                        )}% of offered share is not yet distributed.`
                    )
                    .fadeIn();
            } else {
                $alert
                    .addClass("alert-danger")
                    .html(
                        `<i class="fa fa-times-circle"></i> <strong>Over-distributed:</strong> You have distributed ${Math.abs(
                            Utils.toDecimal(remaining)
                        )}% more than the offered share.`
                    )
                    .fadeIn();
            }
        },
    };

    class CalculationService {
        constructor(coverReg) {
            this.coverReg = coverReg;
        }

        calculateShareAmounts(sharePercentage, commissionRate) {
            const shareDecimal =
                sharePercentage / CONSTANTS.PERCENTAGE_MULTIPLIER;

            const sumInsured = shareDecimal * this.coverReg.total_sum_insured;
            const premium = shareDecimal * this.coverReg.rein_premium;
            const commission =
                (commissionRate / CONSTANTS.PERCENTAGE_MULTIPLIER) * premium;

            return {
                sumInsured: Utils.toDecimal(sumInsured),
                premium: Utils.toDecimal(premium),
                commission: Utils.toDecimal(commission),
            };
        }

        calculateCommissionAmount(premium, commissionRate) {
            const amount =
                (Utils.removeCommas(premium) *
                    Utils.removeCommas(commissionRate)) /
                CONSTANTS.PERCENTAGE_MULTIPLIER;
            return Utils.toDecimal(amount);
        }

        calculateCommissionRate(premium, commissionAmount) {
            const p = Utils.removeCommas(premium);
            const c = Utils.removeCommas(commissionAmount);

            if (p <= 0) return 0;

            const rate = (c / p) * CONSTANTS.PERCENTAGE_MULTIPLIER;
            return Utils.toDecimal(rate);
        }

        calculateBrokerageCommission(
            treatyCounter,
            counter,
            brokerageType,
            quotedAmount = 0
        ) {
            const cedantCommRate = this.coverReg.cedant_comm_rate || 0;
            const reinCommRate = this.coverReg.rein_comm_rate || 0;
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
                0
            );

            if (brokerageType === "A") {
                const amount = Utils.removeCommas(quotedAmount);
                const p = Utils.removeCommas(premium);
                const rate =
                    p > 0 ? (amount / p) * CONSTANTS.PERCENTAGE_MULTIPLIER : 0;

                return {
                    rate: Utils.toDecimal(rate),
                    amount: Utils.toDecimal(amount),
                };
            } else {
                const brokerageRate = Math.max(
                    0,
                    Utils.removeCommas(reinCommRate) - cedantCommRate
                );

                const brokerageAmount =
                    (brokerageRate / CONSTANTS.PERCENTAGE_MULTIPLIER) *
                    Utils.removeCommas(premium);

                return {
                    rate: Utils.toDecimal(brokerageRate),
                    amount: Utils.toDecimal(brokerageAmount),
                };
            }
        }

        calculateInstallments(totalAmount, numberOfInstallments) {
            if (numberOfInstallments <= 0) return [];

            const installmentAmount = totalAmount / numberOfInstallments;
            const installmentPercentage =
                CONSTANTS.PERCENTAGE_MULTIPLIER / numberOfInstallments;

            const installments = [];
            for (let i = 0; i < numberOfInstallments; i++) {
                installments.push({
                    number: i + 1,
                    amount: Utils.toDecimal(installmentAmount),
                    percentage: Utils.toDecimal(installmentPercentage),
                });
            }

            return installments;
        }
    }

    class ValidationService {
        constructor(calculationService, coverReg) {
            this.calculationService = calculationService;
            this.coverReg = coverReg;
            this.errors = [];
        }

        reset() {
            this.errors = [];
        }

        addError(message) {
            this.errors.push(Utils.escapeHtml(message));
        }

        hasErrors() {
            return this.errors.length > 0;
        }

        getErrors() {
            return this.errors;
        }

        getBusinessTypePrefix(treatyNumber) {
            const typeOfBus = this.coverReg.type_of_bus;

            if (typeOfBus === "FPR" || typeOfBus === "FNP") {
                return `Facultative ${treatyNumber}`;
            } else if (typeOfBus === "TPR" || typeOfBus === "TPN") {
                return `Treaty ${treatyNumber}`;
            }

            return `Section ${treatyNumber}`;
        }

        validateShareAllocation(sharePercentage, remainingShare, treatyNumber) {
            const prefix = this.getBusinessTypePrefix(treatyNumber);

            if (sharePercentage <= 0) {
                this.addError(
                    `${prefix}: Share percentage must be greater than 0`
                );
                return false;
            }

            if (sharePercentage > remainingShare) {
                this.addError(
                    `${prefix}: Share (${sharePercentage.toFixed(
                        2
                    )}%) exceeds remaining share (${remainingShare.toFixed(
                        2
                    )}%)`
                );
                return false;
            }

            return true;
        }

        validateSignedShare(
            signedShare,
            writtenShare,
            treatyNumber,
            reinsurerNumber
        ) {
            const prefix = this.getBusinessTypePrefix(treatyNumber);

            if (signedShare > writtenShare) {
                this.addError(
                    `${prefix}, Reinsurer ${reinsurerNumber}: Signed share (${signedShare}%) cannot exceed written share (${writtenShare}%)`
                );
                return false;
            }
            return true;
        }

        validateDistributionComplete(remaining, treatyNumber) {
            const prefix = this.getBusinessTypePrefix(treatyNumber);

            if (Math.abs(remaining) > CONSTANTS.TOLERANCE) {
                const status = remaining > 0 ? "remaining" : "over-distributed";
                this.addError(
                    `${prefix}: Distribution incomplete (${Math.abs(
                        remaining
                    ).toFixed(2)}% ${status})`
                );
                return false;
            }
            return true;
        }

        validateReinsurerFields($section, treatyNumber, reinsurerNumber) {
            const prefix = this.getBusinessTypePrefix(treatyNumber);
            let isValid = true;

            const reinsurer = $section.find(".reinsurer").val();
            if (!reinsurer) {
                this.addError(
                    `${prefix}, Reinsurer ${reinsurerNumber}: Please select a reinsurer`
                );
                isValid = false;
            }

            const writtenShare = Utils.removeCommas(
                $section.find(".reinsurer-written-share").val()
            );
            if (!writtenShare || writtenShare <= 0) {
                this.addError(
                    `${prefix}, Reinsurer ${reinsurerNumber}: Written share is required`
                );
                isValid = false;
            }

            const signedShare = Utils.removeCommas(
                $section.find(".reinsurer-share").val()
            );
            if (!signedShare || signedShare <= 0) {
                this.addError(
                    `${prefix}, Reinsurer ${reinsurerNumber}: Signed share is required`
                );
                isValid = false;
            }

            const wht = $section.find(".reinsurer-wht").val();
            if (wht === null || wht === undefined || wht === "") {
                this.addError(
                    `${prefix}, Reinsurer ${reinsurerNumber}: WHT rate is required`
                );
                isValid = false;
            }

            const payMethod = $section.find(".reins-pay-method").val();
            if (!payMethod) {
                this.addError(
                    `${prefix}, Reinsurer ${reinsurerNumber}: Payment method is required`
                );
                isValid = false;
            }

            const brokerageType = $section.find(".brokerage-comm-type").val();
            if (brokerageType === "A") {
                const brokerageAmount = Utils.removeCommas(
                    $section.find(".reinsurer-brokerage-comm-amt").val()
                );
                if (!brokerageAmount || brokerageAmount <= 0) {
                    this.addError(
                        `${prefix}, Reinsurer ${reinsurerNumber}: Brokerage commission amount is required when using Quoted Amount`
                    );
                    isValid = false;
                }
            }

            return isValid;
        }

        displayErrors() {
            const $validationList = $(DOM_SELECTORS.VALIDATION_LIST);
            const $validationMessages = $(DOM_SELECTORS.VALIDATION_MESSAGES);

            $validationList.empty();

            if (this.hasErrors()) {
                this.errors.forEach((error) => {
                    $validationList.append(
                        `<li class="text-danger">${error}</li>`
                    );
                });

                if (!$validationMessages.is(":visible")) {
                    $validationMessages.fadeIn(() => {
                        $("html, body").animate(
                            {
                                scrollTop:
                                    $validationMessages.offset().top - 100,
                            },
                            500
                        );
                    });
                }
            } else {
                $validationMessages.fadeOut();
            }
        }
    }
    class BrokerageCommissionManager {
        constructor(calculationService, coverReg) {
            this.calculationService = calculationService;
            this.coverReg = coverReg;
        }

        handleBrokerageTypeChange(treatyCounter, counter) {
            const $section = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const brokerageType = $section.find(".brokerage-comm-type").val();

            const $rateDiv = $section.find(".brokerage_comm_rate_div");
            const $amountDiv = $section.find(".brokerage_comm_amt_div");

            const $rateInput = $(
                `#brokerage_comm_rate-${treatyCounter}-${counter}`
            );
            const $rateAmountInput = $(
                `#brokerage_comm_rate_amnt-${treatyCounter}-${counter}`
            );
            const $quotedAmountInput = $(
                `#reinsurer-brokerage_comm_amt-${treatyCounter}-${counter}`
            );

            if (brokerageType === "A") {
                $amountDiv.show();
                $rateDiv.hide();

                $rateInput.val("").prop("required", false);
                $rateAmountInput.val("").prop("required", false);

                $quotedAmountInput.prop("required", true);
            } else if (brokerageType === "R") {
                $rateDiv.show();
                $amountDiv.hide();

                $quotedAmountInput.val("").prop("required", false);

                $rateInput.prop("required", false);
                $rateAmountInput.prop("required", false);

                this.calculateBrokerageCommission(treatyCounter, counter);
            } else {
                $rateDiv.hide();
                $amountDiv.hide();

                $rateInput.val("").prop("required", false);
                $rateAmountInput.val("").prop("required", false);
                $quotedAmountInput.val("").prop("required", false);
            }
        }

        calculateBrokerageCommission(treatyCounter, counter) {
            const $section = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const brokerageType = $section.find(".brokerage-comm-type").val();

            if (brokerageType === "A") {
                const quotedAmount = Utils.getElementValue(
                    `#reinsurer-brokerage_comm_amt-${treatyCounter}-${counter}`,
                    0
                );

                const brokerage =
                    this.calculationService.calculateBrokerageCommission(
                        treatyCounter,
                        counter,
                        "A",
                        quotedAmount
                    );

                $(`#brokerage_comm_rate-${treatyCounter}-${counter}`).val(
                    Utils.numberWithCommas(brokerage.rate)
                );
                $(`#brokerage_comm_rate_amnt-${treatyCounter}-${counter}`).val(
                    Utils.numberWithCommas(brokerage.amount)
                );
            } else if (brokerageType === "R") {
                const brokerage =
                    this.calculationService.calculateBrokerageCommission(
                        treatyCounter,
                        counter,
                        "R"
                    );

                $(`#brokerage_comm_rate-${treatyCounter}-${counter}`).val(
                    Utils.numberWithCommas(brokerage.rate)
                );
                $(`#brokerage_comm_rate_amnt-${treatyCounter}-${counter}`).val(
                    Utils.numberWithCommas(brokerage.amount)
                );
            }
        }

        handleQuotedAmountChange(treatyCounter, counter) {
            const $section = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const brokerageType = $section.find(".brokerage-comm-type").val();

            if (brokerageType === "A") {
                this.calculateBrokerageCommission(treatyCounter, counter);
            }
        }
    }

    class RetroFeeManager {
        constructor(calculationService) {
            this.calculationService = calculationService;
        }

        handleRetroFeeToggle(treatyCounter, counter) {
            const $section = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const applyRetro = $section.find(".apply-fronting").val();

            const $retroRateDiv = $(
                `#fronting_rate_div-${treatyCounter}-${counter}`
            );
            const $retroAmtDiv = $(
                `#fronting_amt_div-${treatyCounter}-${counter}`
            );

            const $retroRateInput = $(
                `#reinsurer-fronting_rate-${treatyCounter}-${counter}`
            );
            const $retroAmtInput = $(
                `#reinsurer-fronting_amt-${treatyCounter}-${counter}`
            );

            if (applyRetro === "Y") {
                $retroRateDiv.fadeIn(CONSTANTS.ANIMATION_DURATION);
                $retroAmtDiv.fadeIn(CONSTANTS.ANIMATION_DURATION);

                if (!$retroRateInput.val()) {
                    $retroRateInput.val("0.00");
                }
                if (!$retroAmtInput.val()) {
                    $retroAmtInput.val("0.00");
                }

                $retroRateInput.prop("required", false);
                $retroAmtInput.prop("required", false);
            } else {
                $retroRateDiv.fadeOut(CONSTANTS.ANIMATION_DURATION);
                $retroAmtDiv.fadeOut(CONSTANTS.ANIMATION_DURATION);

                $retroRateInput.val("0.00").prop("required", false);
                $retroAmtInput.val("0.00").prop("required", false);
            }
        }

        calculateRetroAmount(treatyCounter, counter) {
            const retroRate = Utils.getElementValue(
                `#reinsurer-fronting_rate-${treatyCounter}-${counter}`,
                0
            );
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
                0
            );

            const retroAmount =
                (retroRate / CONSTANTS.PERCENTAGE_MULTIPLIER) * premium;

            $(`#reinsurer-fronting_amt-${treatyCounter}-${counter}`).val(
                Utils.numberWithCommas(retroAmount)
            );
        }

        calculateRetroRate(treatyCounter, counter) {
            const retroAmount = Utils.getElementValue(
                `#reinsurer-fronting_amt-${treatyCounter}-${counter}`,
                0
            );
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
                0
            );

            if (premium <= 0) {
                $(`#reinsurer-fronting_rate-${treatyCounter}-${counter}`).val(
                    "0.00"
                );
                return;
            }

            const retroRate =
                (retroAmount / premium) * CONSTANTS.PERCENTAGE_MULTIPLIER;

            $(`#reinsurer-fronting_rate-${treatyCounter}-${counter}`).val(
                Utils.numberWithCommas(retroRate)
            );
        }
    }

    class DistributionManager {
        constructor(calculationService, brokerageManager) {
            this.calculationService = calculationService;
            this.origDistributedShare = 0;
            this.distributedShare = 0;
            this.brokerageManager = brokerageManager;
        }

        initializeOriginalDistribution() {
            const coverpartners = window.coverpartners || [];

            this.origDistributedShare = 0;
            coverpartners.forEach((partner) => {
                this.origDistributedShare += Utils.removeCommas(partner.share);
            });

            this.distributedShare = this.origDistributedShare;
        }

        calculateDistribution(treatyCounter) {
            let totalDistributed = 0;

            $(`#reinsurer-div-${treatyCounter} .reinsurer-share`).each(
                function () {
                    const share = Utils.validateNumber($(this).val(), 0, 0);
                    totalDistributed += share;
                }
            );

            const offeredShare = Utils.getElementValue(
                `#share_offered-${treatyCounter}`,
                0
            );
            const remaining = offeredShare - totalDistributed;

            $(`#distributed_share-${treatyCounter}`).val(
                Utils.toDecimal(totalDistributed)
            );
            $(`#rem_share-${treatyCounter}`).val(Utils.toDecimal(remaining));

            this.updateRemainingShareIndicator(treatyCounter, remaining);
            SummaryManager.refreshSummary();
        }

        updateRemainingShareIndicator(treatyCounter, remaining) {
            const $remainingField = $(`#rem_share-${treatyCounter}`);

            $remainingField.removeClass(
                "bg-danger bg-warning bg-success text-white"
            );

            if (remaining < 0) {
                $remainingField.addClass("bg-danger text-white");
            } else if (remaining > CONSTANTS.TOLERANCE) {
                $remainingField.addClass("bg-warning");
            } else {
                $remainingField.addClass("bg-success text-white");
            }
        }

        handleShareInput(treatyCounter, counter) {
            const $shareInput = $(`#share-${treatyCounter}-${counter}`);
            const sharePercentage = Utils.validateNumber(
                $shareInput.val(),
                0,
                0,
                100
            );

            if (sharePercentage <= 0) return;

            const commRate = Utils.getElementValue(
                `#reinsurer-comm_rate-${treatyCounter}-${counter}`,
                this.calculationService.coverReg.cedant_comm_rate
            );

            const amounts = this.calculationService.calculateShareAmounts(
                sharePercentage,
                commRate
            );

            this.updateShareRelatedFields(
                treatyCounter,
                counter,
                amounts,
                sharePercentage,
                commRate
            );

            this.calculateDistribution(treatyCounter);
        }

        updateShareRelatedFields(
            treatyCounter,
            counter,
            amounts,
            sharePercentage,
            commRate
        ) {
            const prefix = `#reinsurer`;
            const suffix = `-${treatyCounter}-${counter}`;

            $(`${prefix}-sum_insured${suffix}`).val(
                Utils.numberWithCommas(amounts.sumInsured)
            );
            $(`${prefix}-premium${suffix}`).val(
                Utils.numberWithCommas(amounts.premium)
            );
            $(`${prefix}-comm_amt${suffix}`).val(
                Utils.numberWithCommas(amounts.commission)
            );
            $(`${prefix}-rein_premium${suffix}`).val(
                Utils.numberWithCommas(amounts.premium)
            );
            $(`${prefix}-cedant_premium${suffix}`).val(
                Utils.numberWithCommas(amounts.premium)
            );
            $(`${prefix}-comm_rate${suffix}`).val(
                Utils.numberWithCommas(commRate)
            );

            this.brokerageManager.calculateBrokerageCommission(
                treatyCounter,
                counter
            );
        }
    }

    class CommissionManager {
        constructor(calculationService) {
            this.calculationService = calculationService;
        }

        calculateCommission(treatyCounter, counter) {
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
                0
            );
            const commRate = Utils.getElementValue(
                `#reinsurer-comm_rate-${treatyCounter}-${counter}`,
                0
            );

            const commAmount =
                this.calculationService.calculateCommissionAmount(
                    premium,
                    commRate
                );

            $(`#reinsurer-comm_amt-${treatyCounter}-${counter}`).val(
                Utils.numberWithCommas(commAmount)
            );
        }
    }

    class InstallmentManager {
        constructor(calculationService) {
            this.calculationService = calculationService;
        }

        generateInstallments(treatyCounter, counter) {
            const $row = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const numberOfInstallments =
                parseInt($row.find(".no-of-installments").val()) || 1;
            const premium = Utils.getElementValue(
                `#reinsurer-premium-${treatyCounter}-${counter}`,
                0
            );

            if (
                numberOfInstallments < CONSTANTS.MIN_INSTALLMENTS ||
                numberOfInstallments > CONSTANTS.MAX_INSTALLMENTS
            ) {
                NotificationService.warning(
                    `Number of installments must be between ${CONSTANTS.MIN_INSTALLMENTS} and ${CONSTANTS.MAX_INSTALLMENTS}`
                );
                return;
            }

            if (premium <= 0) {
                NotificationService.warning(
                    "Please enter reinsurer premium first"
                );
                return;
            }

            const installments = this.calculationService.calculateInstallments(
                premium,
                numberOfInstallments
            );

            const $container = $row.find(".reinsurer-plan-section");
            $container.empty();

            installments.forEach((installment, index) => {
                const html = this.createInstallmentRow(
                    treatyCounter,
                    counter,
                    installment,
                    index
                );
                $container.append(html);
            });

            $row.find(".installments-box").fadeIn(CONSTANTS.ANIMATION_DURATION);
            NotificationService.success(
                `${numberOfInstallments} installments generated successfully`
            );
        }

        createInstallmentRow(treatyCounter, counter, installment, index) {
            return `
                <div class="row mb-2 installment-row">
                    <div class="col-md-2">
                        <label class="form-label">Installment ${
                            installment.number
                        }</label>
                        <input type="text" class="form-control" value="${
                            installment.number
                        }" readonly />
                        <input type="hidden"
                               name="treaty[${treatyCounter}][reinsurers][${counter}][installments][${index}][number]"
                               value="${installment.number}" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Due Date</label>
                        <input type="date"
                               class="form-control installment-date"
                               name="treaty[${treatyCounter}][reinsurers][${counter}][installments][${index}][due_date]"
                               required />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount</label>
                        <input type="number"
                               step="0.01"
                               class="form-control installment-amount"
                               name="treaty[${treatyCounter}][reinsurers][${counter}][installments][${index}][amount]"
                               value="${installment.amount}"
                               required />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Percentage</label>
                        <input type="text"
                               class="form-control"
                               value="${installment.percentage.toFixed(2)}%"
                               readonly />
                    </div>
                </div>
            `;
        }

        handlePaymentMethodChange(treatyCounter, counter, payMethodCode) {
            const $row = $(`#reinsurer-div-${treatyCounter}-${counter}`);
            const $installmentsSection = $row.find(
                ".no-of-installments-section"
            );
            const $addButton = $row.find(".add-installment-btn-section");
            const $installmentsBox = $row.find(".installments-box");

            if (payMethodCode === "INS" || payMethodCode === "INST") {
                $installmentsSection.fadeIn(CONSTANTS.ANIMATION_DURATION);
                $addButton.fadeIn(CONSTANTS.ANIMATION_DURATION);
            } else {
                $installmentsSection.fadeOut(CONSTANTS.ANIMATION_DURATION);
                $addButton.fadeOut(CONSTANTS.ANIMATION_DURATION);
                $installmentsBox.fadeOut(CONSTANTS.ANIMATION_DURATION);
            }
        }
    }

    class ReinsurerManager {
        constructor(calculationService, validationService, brokerageManager) {
            this.calculationService = calculationService;
            this.validationService = validationService;
            this.reinsurerCounters = {};
            this.brokerageManager = brokerageManager;
        }

        addReinsurerRow(treatyCounter) {
            if (typeof this.reinsurerCounters[treatyCounter] === "undefined") {
                this.reinsurerCounters[treatyCounter] = 0;
            }

            this.reinsurerCounters[treatyCounter]++;
            const counter = this.reinsurerCounters[treatyCounter];

            const template = $(DOM_SELECTORS.REINSURER_ROW_TEMPLATE).html();
            if (!template) {
                console.error("Reinsurer row template not found");
                NotificationService.error(
                    "Unable to add reinsurer row. Template not found."
                );
                return;
            }

            const newRow = Utils.replacePlaceholders(template, {
                TREATY_COUNTER_PLACEHOLDER: treatyCounter,
                COUNTER_PLACEHOLDER: counter,
                REINSURER_NUMBER_PLACEHOLDER: counter + 1,
            });

            const $container = $(`#reinsurer-div-${treatyCounter}`);
            if (!$container.length) {
                console.error(
                    `Container not found: #reinsurer-div-${treatyCounter}`
                );
                NotificationService.error(
                    "Unable to add reinsurer row. Container not found."
                );
                return;
            }

            $container.append(newRow);

            requestAnimationFrame(() => {
                setTimeout(() => {
                    this.initializeSelect2InRow(treatyCounter, counter);
                    this.updateReinsurerNumbers(treatyCounter);

                    this.brokerageManager.handleBrokerageTypeChange(
                        treatyCounter,
                        counter
                    );

                    const $select = $(
                        `#reinsurer-div-${treatyCounter}-${counter} .select2Placement`
                    );
                    if (
                        $select.length &&
                        !$select.hasClass("select2-hidden-accessible")
                    ) {
                        console.warn(
                            "Select2 failed to initialize, retrying..."
                        );
                        this.initializeSelect2InRow(treatyCounter, counter);
                    }
                }, CONSTANTS.SELECT2_INIT_DELAY);
            });
        }

        removeReinsurerRow(treatyCounter, counter) {
            const $container = $(`#reinsurer-div-${treatyCounter}`);
            const remainingReinsurers = $container.find(
                DOM_SELECTORS.REINSURER_SECTION
            ).length;

            if (remainingReinsurers <= 1) {
                Swal.fire({
                    icon: "warning",
                    title: "Cannot Remove",
                    text: "Cannot remove the last reinsurer. At least one is required.",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#3085d6",
                });
                return;
            }

            Swal.fire({
                title: "Remove Reinsurer?",
                text: "Are you sure you want to remove this reinsurer? This action cannot be undone.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, remove it",
                cancelButtonText: "Cancel",
                reverseButtons: true,
                focusCancel: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    const $row = $(
                        `#reinsurer-div-${treatyCounter}-${counter}`
                    );

                    $row.fadeOut(CONSTANTS.ANIMATION_DURATION, () => {
                        $row.remove();
                        this.updateReinsurerNumbers(treatyCounter);
                        ReinsurerPlacement.distributionManager.calculateDistribution(
                            treatyCounter
                        );
                        this.filterSelectedReinsurers(treatyCounter);
                    });
                }
            });
        }

        updateReinsurerNumbers(treatyCounter) {
            $(
                `#reinsurer-div-${treatyCounter} ${DOM_SELECTORS.REINSURER_SECTION}`
            ).each(function (index) {
                $(this)
                    .find(".reinsurer-number")
                    .text(index + 1);
            });
        }

        filterSelectedReinsurers(treatyCounter) {
            const selectedReinsurers =
                this.getSelectedReinsurers(treatyCounter);

            $(`#reinsurer-div-${treatyCounter} .reinsurer`).each(function () {
                const $select = $(this);
                const currentValue = $select.val();

                if (!$select.data("original-options")) {
                    const options = [];
                    $select.find("option").each(function () {
                        options.push({
                            value: $(this).val(),
                            text: $(this).text(),
                            title: $(this).attr("title"),
                        });
                    });
                    $select.data("original-options", options);
                }

                const originalOptions = $select.data("original-options");
                $select.empty();

                originalOptions.forEach((option) => {
                    if (
                        !option.value ||
                        option.value === currentValue ||
                        !selectedReinsurers.includes(option.value)
                    ) {
                        const $option = $("<option></option>")
                            .val(option.value)
                            .text(option.text);

                        if (option.title) {
                            $option.attr("title", option.title);
                        }

                        $select.append($option);
                    }
                });

                if (currentValue) {
                    $select.val(currentValue);
                }

                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.trigger("change.select2");
                }
            });
        }

        getSelectedReinsurers(treatyCounter) {
            const selected = [];
            $(`#reinsurer-div-${treatyCounter} .reinsurer`).each(function () {
                const value = $(this).val();
                if (value) {
                    selected.push(value);
                }
            });
            return selected;
        }

        initializeSelect2InRow(treatyCounter, counter) {
            const containerSelector = `#reinsurer-div-${treatyCounter}-${counter}`;
            Select2Manager.initializeInContainer(containerSelector);
        }
    }

    class TreatyManager {
        constructor(reinsurerManager) {
            this.reinsurerManager = reinsurerManager;
        }

        addTreatySection() {
            const $lastSection = $(
                `${DOM_SELECTORS.TREATY_DIV} ${DOM_SELECTORS.TREATY_SECTION}`
            ).last();

            if (!$lastSection.length) {
                NotificationService.error("No treaty section found to clone");
                return;
            }

            const currCounter =
                parseInt($lastSection.attr("data-counter")) || 0;

            const currTreaty = $(`#reinsurer-treaty-${currCounter}`).val();
            if (!currTreaty) {
                NotificationService.error(
                    "Please select a treaty before adding a new section"
                );
                return;
            }

            const counter = currCounter + 1;
            const $newSection = $lastSection.clone();

            this.cleanClonedSection($newSection, counter);
            this.updateSectionAttributes($newSection, counter, currCounter);

            $lastSection.after($newSection);

            Select2Manager.initializeInContainer(
                `#treaty-div-section-${counter}`
            );
            this.reinsurerManager.reinsurerCounters[counter] = counter;
        }

        cleanClonedSection($section, counter) {
            $section.find(".select2-container").remove();
            $section.find("[data-select2-id]").removeAttr("data-select2-id");

            $section.find("input:not(.share_offered)").val("");

            $section
                .find(`${DOM_SELECTORS.REINSURER_SECTION}:not(:first)`)
                .remove();

            $section.attr({
                "data-counter": counter,
                id: `treaty-div-section-${counter}`,
            });
        }

        updateSectionAttributes($section, counter, currCounter) {
            $section.find("[id]").each(function () {
                const id = $(this).attr("id");
                const newId = id.replace(/(-\d)(-\d)?$/, (match, p1, p2) => {
                    return p2 ? `-${counter}-${counter}` : `-${counter}`;
                });

                $(this).attr({
                    id: newId,
                    "data-counter": counter,
                    "data-treaty-counter": counter,
                });
            });

            $section.find(".treaties").each(function () {
                const name = $(this).attr("name");
                const newName = name.replace(
                    `[${currCounter}]`,
                    `[${counter}]`
                );
                $(this).attr("name", newName);
            });

            $section.find(".reinsurers").each(function () {
                const name = $(this).attr("name");
                const newName = name.replace(/(\[\d+\])/g, `[${counter}]`);
                $(this).attr("name", newName);
            });
        }

        removeTreatySection(counter) {
            if (
                !confirm(
                    "Are you sure you want to remove this treaty section and all its reinsurers?"
                )
            ) {
                return;
            }

            $(`#treaty-div-section-${counter}`).fadeOut(
                CONSTANTS.ANIMATION_DURATION,
                function () {
                    $(this).remove();
                    SummaryManager.refreshSummary();
                }
            );

            delete this.reinsurerManager.reinsurerCounters[counter];
        }
    }

    class FormSubmissionManager {
        constructor(validationService, coverReg) {
            this.validationService = validationService;
            this.coverReg = coverReg;
        }

        validateAndSubmit() {
            this.validationService.reset();

            let treatyNumber = 1;

            if (
                [CONSTANTS.FPR, CONSTANTS.FNP].includes(
                    this.coverReg.type_of_bus
                )
            ) {
                $(DOM_SELECTORS.TREATY_SECTION).each((index, element) => {
                    const $section = $(element);
                    const treatyCounter = $section.data("counter");

                    this.validateTreatySection(
                        $section,
                        treatyCounter,
                        treatyNumber
                    );
                    treatyNumber++;
                });
            }

            if (this.validationService.hasErrors()) {
                this.validationService.displayErrors();
                return;
            }

            this.submitForm();
        }

        validateTreatySection($section, treatyCounter, treatyNumber) {
            if ($section.find(".reinsurer-treaty").length > 0) {
                const treatyValue = $section.find(".reinsurer-treaty").val();
                if (!treatyValue) {
                    this.validationService.addError(
                        `Treaty section ${treatyNumber}: Please select a treaty`
                    );
                }
            }

            const remaining = Utils.getElementValue(
                `#rem_share-${treatyCounter}`,
                0
            );
            this.validationService.validateDistributionComplete(
                remaining,
                treatyNumber
            );

            let reinsurerNumber = 1;
            $(
                `#reinsurer-div-${treatyCounter} ${DOM_SELECTORS.REINSURER_SECTION}`
            ).each((index, element) => {
                this.validationService.validateReinsurerFields(
                    $(element),
                    treatyNumber,
                    reinsurerNumber
                );
                reinsurerNumber++;
            });
        }

        submitForm() {
            const $form = $(DOM_SELECTORS.FORM);

            NotificationService.clear();

            const $button = $(DOM_SELECTORS.SAVE_BUTTON);
            const url = $form.data("url");

            if (!url) {
                NotificationService.error("Form submission URL not configured");
                return;
            }

            $button
                .prop("disabled", true)
                .html('<i class="fa fa-spinner fa-spin me-2"></i>Saving...');

            $.ajax({
                url: url,
                method: "POST",
                data: $form.serialize(),
                timeout: 30000,
                success: (response) =>
                    this.handleSubmitSuccess(response, $button),
                error: (xhr, status, error) => {
                    if (status === "timeout") {
                        NotificationService.error(
                            "Request timed out. Please try again."
                        );
                        $button
                            .prop("disabled", false)
                            .html(
                                '<i class="fa fa-save me-2"></i>Save Placement'
                            );
                    } else {
                        this.handleSubmitError(xhr, $button);
                    }
                },
            });
        }

        handleSubmitSuccess(response, $button) {
            if (response.success) {
                // console.log(response);
                toastr.success("Reinsurance placement saved successfully");

                setTimeout(() => {
                    $(DOM_SELECTORS.MODAL).modal("hide");

                    if (typeof window.refreshCoverData === "function") {
                        window.refreshCoverData();
                    } else {
                        location.reload();
                    }
                }, 1500);
            } else {
                toastr.error("An error occurred while saving");
                $button
                    .prop("disabled", false)
                    .html('<i class="fa fa-save me-2"></i>Save Placement');
            }
        }

        handleSubmitError(xhr, $button) {
            let errorMessage = "An error occurred while saving";

            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    const errors = Object.values(
                        xhr.responseJSON.errors
                    ).flat();
                    errorMessage = errors.join("<br>");
                }
            }

            NotificationService.error(errorMessage);
            $button
                .prop("disabled", false)
                .html('<i class="fa fa-save me-2"></i>Save Placement');
        }
    }

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

                schedulesForm: $("#schedulesForm"),
                attachmentsForm: $("#attachmentsForm"),
                clausesForm: $("#clausesForm"),
                reinsurerForm: $("#reinsurerForm"),
                editReinsurerForm: $("#EditReinsurerForm"),
                verifyForm: $("#verifyForm"),
                insuranceClassForm: $("#insuranceClassForm"),

                schedulesModal: $("#schedulesModal"),
                attachmentsModal: $("#attachments-modal"),
                clausesModal: $("#clauses-modal"),
                reinsurerModal: $("#reinsurer-modal"),
                editReinsurerModal: $("#edit-reinsurer-modal"),
                verifyModal: $("#verify-modal"),
                debitModal: $("#debit-modal"),
                insuranceClassModal: $("#insurance-class-modal"),
                sendEmailModal: $("#sendReinDocumentEmail"),

                schedulesTable: $("#schedules-table"),
                attachmentsTable: $("#attachments-table"),
                clausesTable: $("#clauses-table"),
                reinsurersTable: $("#reinsurers-table"),
                installmentsTable: $("#installments-table"),
                insClassTable: $("#insclass-table"),
                approvalsTable: $("#approvals-table"),
                debitsTable: $("#debits-table"),
                endorseNarrationTable: $("#endorse-narration-table"),

                editCoverBtn: $("#edit-cover"),
                verifyDetailsBtn: $("#verify_details"),
                generateSlipBtn: $("#generate_slip"),
                commitCoverBtn: $("#commit-cover"),

                tabNav: $(".reinsurers-details-card .nav-link"),

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
            this.initializeTooltips();
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

            if (this.$el.reinsurerForm.length) {
                this.$el.reinsurerForm.validate({
                    errorClass: "errorClass",
                    submitHandler: function (form) {
                        self.handleReinsurerFormSubmit(form);
                    },
                });
            }

            if (this.$el.editReinsurerForm.length) {
                this.$el.editReinsurerForm.validate({
                    errorClass: "errorClass",
                    submitHandler: function (form) {
                        self.handleEditReinsurerFormSubmit(form);
                    },
                });
            }

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

            this.$el.verifyDetailsBtn.on("click", function (e) {
                e.preventDefault();
                self.handleVerifyDetails();
            });

            this.$el.generateSlipBtn.on("click", function (e) {
                e.preventDefault();
                self.handleGenerateSlip();
            });

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

            $("#ins-class-save-btn").on("click", function () {
                self.$el.insuranceClassForm.submit();
            });

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

            $(document).on("click", "#schedule-details", function () {
                self.$el.schedulesForm[0].reset();
                self.$el.schedulesForm.find('[name="_method"]').val("POST");
            });

            $(document).on("click", "#attachments", function () {
                self.$el.attachmentsForm[0].reset();
                self.$el.attachmentsForm.find('[name="_method"]').val("POST");
            });

            $(document).on("change", "#sched-header", function () {
                const schedTitle = $(this).find("option:selected").data("name");
                $("#title").val(schedTitle);
            });

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

            $(document).on("click", ".edit-schedule", function () {
                const data = $(this).data();
                self.populateScheduleForm(data);
            });

            $(document).on("click", ".remove-schedule", function () {
                const dataId = $(this).data("id");
                const dataName = $(this).data("name");
                self.confirmRemoveSchedule(dataId, dataName);
            });

            $(document).on("click", ".edit-attachment", function () {
                const data = $(this).data();
                self.populateAttachmentForm(data);
            });

            $(document).on("click", ".remove-attachment", function () {
                const data = $(this).data();
                self.confirmRemoveAttachment(data);
            });

            $(document).on("click", ".view-attachment", function () {
                const base64Data = $(this).data("base64");
                const mimeType = $(this).data("mime");
                self.showAttachmentPreview(base64Data, mimeType);
            });

            $(document).on("click", ".remove-clause", function () {
                const data = $(this).data();
                self.confirmRemoveClause(data);
            });

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

            $(document).on("click", ".remove-reinsurer", function () {
                const shareData = $(this).data("data");
                const reinsurer = $(this).data("reinsurer");
                self.confirmRemoveReinsurer(shareData, reinsurer);
            });

            $(document).on("click", ".send_reinsurer_email", function (e) {
                e.preventDefault();
                self.openReinsurerEmailModal($(this));
            });

            $(document).on("click", ".send-cedant-email", function (e) {
                e.preventDefault();
                self.openCedantEmailModal($(this));
            });
        },

        bindCalculationEvents: function () {
            const self = this;

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

            $("#reinsurer-modal").on(
                "keyup",
                ".reinsurer-comm-amt",
                function () {
                    const counter = $(this).data("counter");
                    self.computeCommissionRate(counter);
                }
            );

            $(document).on("keyup", ".reinsurer-fronting_rate", function () {
                const counter = $(this).data("counter");
                self.computeFrontingAmount(counter);
            });

            $(document).on("keyup", "#edreinsurer-fronting_rate", function () {
                self.computeEditFrontingAmount();
            });

            $(document).on("keyup", ".reinsurer-premium", function () {
                const counter = $(this).data("counter");
                self.computeCommissionAmt(counter);
            });
        },

        bindDynamicFieldEvents: function () {
            const self = this;

            $("#add-treaty-reinsurer").on("click", function () {
                self.addTreatyReinsurerSection();
            });

            $(document).on("click", ".add-reinsurer", function () {
                const counter = $(this).data("counter");
                self.addReinsurerSection(counter);
            });

            $("#reinsurer_plan_section").on(
                "click",
                "#remove_reinsurer_instalment",
                function () {
                    self.removeReinsurerInstallment($(this));
                }
            );

            $("select#reins_pay_method").on("change", function () {
                self.handlePaymentMethodChange();
            });

            $("#no_of_installments").on("change keyup", function () {
                const inst = $(this).val();
                if (!inst) {
                    $("#add_installments_box").hide();
                }
            });

            $("#add_reinsurer_instalments").on("click", function () {
                self.addReinsurerInstallments();
            });

            $(document).on("change", ".reinsurer-treaty", function () {
                self.handleTreatyChange($(this));
            });
        },

        handleScheduleFormSubmit: function (form) {
            const self = this;
            let method = this.$el.schedulesForm.find('[name="_method"]').val();
            let url =
                method === "POST"
                    ? this.$el.schedulesForm.data("post-url")
                    : this.$el.schedulesForm.data("put-url");

            console.log(url);

            // let formData = new FormData(form);
            // formData.append("details", this.$el.scheduleDescription.html());

            // fetch(url, {
            //     method: method,
            //     headers: {
            //         "Content-Type": "application/x-www-form-urlencoded",
            //     },
            //     body: new URLSearchParams(formData),
            // })
            //     .then((response) => response.json())
            //     .then((data) => {
            //         if (data.status === 201) {
            //             toastr.success("Schedule Successfully saved");
            //             setTimeout(() => window.location.reload(), 1500);
            //         } else if (data.status === 422) {
            //             self.showValidationErrors(data.errors);
            //         } else {
            //             toastr.error("Failed to save details");
            //         }
            //     })
            //     .catch((error) => {
            //         toastr.error("An error occurred");
            //         console.error(error);
            //     });
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

        handleReinsurerFormSubmit: function () {
            if (
                window.ReinsurerPlacement &&
                window.ReinsurerPlacement.formSubmissionManager
            ) {
                window.ReinsurerPlacement.formSubmissionManager.validateAndSubmit();
            } else {
                NotificationService.error(
                    "Unable to submit form. Please refresh the page."
                );
            }
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

            const btn = $("#verify-save-btn");
            const btnText = btn.find(".btn-text");
            const spinner = btn.find(".spinner-border");

            $("#validation-errors").addClass("d-none");
            $("#error-list").empty();
            $(".is-invalid").removeClass("is-invalid");

            const errors = [];

            if (!$("#approver").val()) {
                errors.push("Please select an approver");
                $("#approver").addClass("is-invalid");
            }
            if (!$("#priority").val()) {
                errors.push("Please select a priority level");
                $("#priority").addClass("is-invalid");
            }
            const comment = $("#verify-comment").val().trim();
            if (comment.length < 7) {
                errors.push("Comment must be at least 7 characters");
                $("#verify-comment").addClass("is-invalid");
            }

            if (!$('input[name="cover_no"]').val()) {
                errors.push("Cover number is missing");
            }
            if (!$('input[name="process"]').val()) {
                errors.push("Process ID is missing");
            }

            if (errors.length > 0) {
                errors.forEach((error) => {
                    $("#error-list").append(`<li>${error}</li>`);
                });
                $("#validation-errors").removeClass("d-none");
                return false;
            }
            btn.prop("disabled", true);
            btnText.text("Submitting...");
            spinner.removeClass("d-none");

            $.ajax({
                url: this.$el.verifyForm.attr("action"),
                method: "POST",
                data: this.$el.verifyForm.serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.status == 201) {
                        toastr.success(
                            "Successfully sent for verification",
                            "Success"
                        );

                        $("#verificationModal").modal("hide");
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function (xhr) {
                    btn.prop("disabled", false);
                    btnText.text("Submit for Verification");
                    spinner.addClass("d-none");
                    const errorMsg = "Failed to submit for verification";
                    toastr.error(errorMsg);

                    if (xhr.responseJSON?.errors) {
                        const validationErrors = xhr.responseJSON.errors;
                        Object.keys(validationErrors).forEach((key) => {
                            validationErrors[key].forEach((error) => {
                                $("#error-list").append(`<li>${error}</li>`);
                            });
                        });
                        $("#validation-errors").removeClass("d-none");
                    }
                },
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

        computeCommissionAmt: function (counter) {
            const premium =
                parseFloat(
                    ($(`#reinsurer-premium-${counter}`).val() || "").replace(
                        /,/g,
                        ""
                    )
                ) || 0;
            const commRate =
                parseFloat($(`#reinsurer-comm_rate-${counter}`).val()) || 0;
            const commAmt = (premium * commRate) / 100;

            $(`#reinsurer-comm_amt-${counter}`).val(
                Utils.numberWithCommas(commAmt.toFixed(2))
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
                Utils.numberWithCommas(frontingAmt.toFixed(2))
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
                Utils.numberWithCommas(reinsurercommAmount.toFixed(2))
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
                Utils.numberWithCommas(frontingAmt)
            );
        },

        calculateBrokerageCommRate: function () {
            const cedantCommRate =
                parseFloat($("#reinsurer-modal").data("cedant-comm-rate")) || 0;

            const reinCommRaw = $("#reinsurer-comm_rate-0").val();
            const reinCommRate =
                parseFloat(
                    reinCommRaw ? reinCommRaw.toString().replace(/,/g, "") : "0"
                ) || 0;

            const premiumRaw = $("#reinsurer-premium-0").val();
            const premium =
                parseFloat(
                    premiumRaw ? premiumRaw.toString().replace(/,/g, "") : "0"
                ) || 0;

            const brokerageCommRate = Math.max(
                0,
                reinCommRate - cedantCommRate
            );
            const brokerageCommRateAmnt = (brokerageCommRate / 100) * premium;

            $("#brokerage_comm_rate").val(
                Utils.numberWithCommas(brokerageCommRate.toFixed(2))
            );
            $("#brokerage_comm_rate_amnt").val(
                Utils.numberWithCommas(brokerageCommRateAmnt.toFixed(2))
            );
        },

        handleReinsurerShareInput: function ($input) {
            const sharePercentage = parseFloat($input.val()) || 0;
            const counter = $input.data("counter");
            const treatyCounter = $input.data("treaty-counter");

            this.computeCommissionAmt(counter);
        },

        handleEditReinsurerShareInput: function ($input) {
            const sharePercentage = parseFloat($input.val()) || 0;
        },

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

                // ins-classes-tab
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

            this.state.distributedShare = 0;
            this.state.origDistributedShare = 0;

            this.appendReinsurers(counter, selectedTreaty);
        },

        appendReinsurers: function (treatyCounter, treaty) {
            const counter = 0;
            const $select = $(`#reinsurer-${treatyCounter}-${counter}`);

            $select.empty();
            $select.append(
                $("<option>").text("-- Select Reinsurer--").attr("value", "")
            );

            $select.trigger("change.select2");
        },

        addTreatyReinsurerSection: function () {
            const $lastSection = $("#treaty-div .treaty-div-section").last();
            const currCounter = parseInt($lastSection.attr("data-counter"));

            const currTreaty = $(`#reinsurer-treaty-${currCounter}`).val();
            if (!currTreaty || currTreaty === "" || currTreaty === " ") {
                toastr.error("Please Select Treaty", "Incomplete data");
                return false;
            }

            const $newSection = $lastSection.clone();
            const counter = currCounter + 1;

            $newSection.find(".select2-container").remove();
            $newSection.find("input:not(.share_offered)").val("");
            $newSection.attr("data-counter", counter);
            $newSection.attr("id", `treaty-div-section-${counter}`);

            this.updateSectionIds($newSection, counter);

            $lastSection.after($newSection);

            $("#treaty-div .select2Placement").select2({
                dropdownParent: this.$el.reinsurerModal,
            });
        },

        addReinsurerSection: function (treatyCounter) {
            const $lastSection = $(
                `#treaty-div #treaty-div-section-${treatyCounter} .reinsurer-section`
            ).last();
            const prevCounter = parseInt($lastSection.attr("data-counter"));

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

            this.updateSectionIds($newSection, counter);

            $lastSection.after($newSection);

            $("#reinsurer-div .select2Placement").select2({
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

            const totalDr =
                parseFloat($("#reinsurer-premium-0").val().replace(/,/g, "")) ||
                0;

            const totalFacInstAmt = (totalDr / noOfInstallments).toFixed(2);
            this.state.installmentTotalAmount = totalDr;

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
                                value="${Utils.numberWithCommas(
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
                Utils.numberWithCommas(parseFloat(data.sum_insured).toFixed(2))
            );
            $("#edreinsurer-premium").val(parseFloat(data.premium).toFixed(2));
            $("#edreinsurer-comm_rate").val(
                Utils.numberWithCommas(parseFloat(data.comm_rate).toFixed(2))
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

            this.$el.sendEmailModal.modal("show");
        },

        openCedantEmailModal: function ($button) {
            const endorsementNo = $button.data("endorsement_no");
            const coverNo = $button.data("cover_no");
            const emails = $button.data("client_emails");

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
    };

    const ReinsurerPlacement = {
        calculationService: null,
        validationService: null,
        reinsurerManager: null,
        treatyManager: null,
        distributionManager: null,
        commissionManager: null,
        installmentManager: null,
        formSubmissionManager: null,
        brokerageManager: null,
        retroFeeManager: null,

        coverReg: {
            share_offered: 0,
            total_sum_insured: 0,
            rein_premium: 0,
            rein_comm_amount: 0,
            cedant_comm_rate: 0,
            brokerage_comm_type: "R",
            brokerage_comm_rate: 0,
            rein_comm_rate: 0,
            type_of_bus: "FPR",
        },

        init() {
            this.loadCoverData();
            this.initializeServices();
            this.bindEvents();
            this.initializeSelect2();
            this.distributionManager.initializeOriginalDistribution();
            this.distributionManager.calculateDistribution(0);
            this.initializeBrokerageDisplays();
            this.initializeRetroDisplays();
        },

        loadCoverData() {
            this.coverReg = {
                share_offered: Utils.getElementValue("#share_offered", 0),
                total_sum_insured: Utils.getElementValue(
                    "#total_sum_insured",
                    0
                ),
                rein_premium: Utils.getElementValue("#rein_premium", 0),
                rein_comm_amount: Utils.getElementValue("#rein_comm_amount", 0),
                cedant_comm_rate: Utils.getElementValue("#cedant_comm_rate", 0),
                brokerage_comm_rate: Utils.getElementValue(
                    "#brokerage_comm_rate",
                    0
                ),
                rein_comm_rate: $("#rein_comm_rate").val() || 0,
                brokerage_comm_type: $("#brokerage_comm_type").val() || "R",
                type_of_bus: $("#type_of_bus").val() || "FPR",
            };
        },

        initializeServices() {
            this.calculationService = new CalculationService(this.coverReg);
            this.validationService = new ValidationService(
                this.calculationService,
                this.coverReg
            );
            this.brokerageManager = new BrokerageCommissionManager(
                this.calculationService,
                this.coverReg
            );
            this.reinsurerManager = new ReinsurerManager(
                this.calculationService,
                this.validationService,
                this.brokerageManager
            );
            this.treatyManager = new TreatyManager(this.reinsurerManager);
            this.distributionManager = new DistributionManager(
                this.calculationService,
                this.brokerageManager
            );
            this.commissionManager = new CommissionManager(
                this.calculationService
            );
            this.installmentManager = new InstallmentManager(
                this.calculationService
            );
            this.formSubmissionManager = new FormSubmissionManager(
                this.validationService,
                this.coverReg
            );
            this.retroFeeManager = new RetroFeeManager(this.calculationService);
        },

        initializeBrokerageDisplays() {
            const self = this;
            $(DOM_SELECTORS.REINSURER_SECTION).each(function () {
                const treatyCounter = $(this).data("treaty-counter");
                const counter = $(this).data("counter");

                if (treatyCounter !== undefined && counter !== undefined) {
                    self.brokerageManager.handleBrokerageTypeChange(
                        treatyCounter,
                        counter
                    );
                }
            });
        },

        initializeRetroDisplays() {
            const self = this;
            $(DOM_SELECTORS.REINSURER_SECTION).each(function () {
                const treatyCounter = $(this).data("treaty-counter");
                const counter = $(this).data("counter");

                if (treatyCounter !== undefined && counter !== undefined) {
                    self.retroFeeManager.handleRetroFeeToggle(
                        treatyCounter,
                        counter
                    );
                }
            });
        },

        bindEvents() {
            const self = this;

            $(document).on("click", "#add-treaty-reinsurer", (e) => {
                e.preventDefault();
                self.treatyManager.addTreatySection();
            });

            $(document).on("click", ".remove-treaty-section", function (e) {
                e.preventDefault();
                const counter = $(this).data("counter");
                self.treatyManager.removeTreatySection(counter);
            });

            $(document).on("click", ".add-reinsurer-btn", function (e) {
                e.preventDefault();
                const treatyCounter = $(this).data("treaty-counter");
                self.reinsurerManager.addReinsurerRow(treatyCounter);
            });

            $(document).on("click", ".remove-reinsurer-btn", function (e) {
                e.preventDefault();
                const treatyCounter = $(this).data("treaty-counter");
                const counter = $(this).data("counter");
                self.reinsurerManager.removeReinsurerRow(
                    treatyCounter,
                    counter
                );
            });

            $(document).on("change", ".reinsurer", function () {
                const treatyCounter = $(this).data("treaty-counter");
                self.reinsurerManager.filterSelectedReinsurers(treatyCounter);
            });

            $(document).on(
                "input",
                ".reinsurer-written-share, .reinsurer-share",
                function () {
                    const treatyCounter = $(this).data("treaty-counter");
                    const counter = $(this).data("counter");

                    self.distributionManager.calculateDistribution(
                        treatyCounter
                    );
                    self.validateSignedVsWrittenShare(treatyCounter, counter);
                    self.distributionManager.handleShareInput(
                        treatyCounter,
                        counter
                    );
                }
            );

            $(document).on(
                "input",
                ".reinsurer-premium, .reinsurer-comm-rate",
                function () {
                    const treatyCounter = $(this).data("treaty-counter");
                    const counter = $(this).data("counter");
                    self.commissionManager.calculateCommission(
                        treatyCounter,
                        counter
                    );
                    self.brokerageManager.calculateBrokerageCommission(
                        treatyCounter,
                        counter
                    );
                }
            );

            $(document).on("change", ".brokerage-comm-type", function () {
                const treatyCounter = $(this).data("treaty-counter");
                const counter = $(this).data("counter");

                self.brokerageManager.handleBrokerageTypeChange(
                    treatyCounter,
                    counter
                );
            });

            $(document).on(
                "input",
                ".reinsurer-brokerage-comm-amt",
                function () {
                    const treatyCounter = $(this).data("treaty-counter");
                    const counter = $(this).data("counter");
                    self.brokerageManager.handleQuotedAmountChange(
                        treatyCounter,
                        counter
                    );
                }
            );

            $(document).on("change", ".apply-fronting", function () {
                const treatyCounter = $(this).data("treaty-counter");
                const counter = $(this).data("counter");
                self.retroFeeManager.handleRetroFeeToggle(
                    treatyCounter,
                    counter
                );
            });

            $(document).on("input", ".reinsurer-fronting-rate", function () {
                const treatyCounter = $(this).data("treaty-counter");
                const counter = $(this).data("counter");
                self.retroFeeManager.calculateRetroAmount(
                    treatyCounter,
                    counter
                );
            });

            $(document).on("input", ".reinsurer-fronting-amt", function () {
                const treatyCounter = $(this).data("treaty-counter");
                const counter = $(this).data("counter");
                self.retroFeeManager.calculateRetroRate(treatyCounter, counter);
            });

            $(document).on("change", ".reins-pay-method", function () {
                const treatyCounter = $(this).data("treaty-counter");
                const counter = $(this).data("counter");
                const payMethod = $(this).val();
                self.installmentManager.handlePaymentMethodChange(
                    treatyCounter,
                    counter,
                    payMethod
                );
            });

            $(document).on(
                "click",
                ".add-reinsurer-installments",
                function (e) {
                    e.preventDefault();
                    const treatyCounter = $(this).data("treaty-counter");
                    const counter = $(this).data("counter");
                    self.installmentManager.generateInstallments(
                        treatyCounter,
                        counter
                    );
                }
            );

            $(DOM_SELECTORS.MODAL).on("shown.bs.modal", () => {
                self.initializeSelect2();
                SummaryManager.refreshSummary();
                self.initializeBrokerageDisplays();
                self.initializeRetroDisplays();
            });
        },

        validateSignedVsWrittenShare(treatyCounter, counter) {
            const writtenShare = Utils.getElementValue(
                `#written_share-${treatyCounter}-${counter}`,
                0
            );
            const signedShare = Utils.getElementValue(
                `#share-${treatyCounter}-${counter}`,
                0
            );

            const $signedInput = $(`#share-${treatyCounter}-${counter}`);

            if (signedShare > writtenShare) {
                $signedInput
                    .addClass("is-invalid")
                    .val(Utils.toDecimal(writtenShare));

                NotificationService.warning(
                    "Signed lines cannot exceed written lines"
                );
            } else {
                $signedInput.removeClass("is-invalid");
            }
        },

        initializeSelect2() {
            Select2Manager.initialize();
        },
    };

    const TreatyReinsurerCalculations = {
        init() {
            const self = this;

            $(document).on(
                "input change",
                ".reinsurer-compulsory-acceptance, .reinsurer-optional-acceptance",
                function () {
                    const $input = $(this);
                    const treatyCounter = $input.data("treaty-counter");
                    const counter = $input.data("counter");

                    self.calculateTotalAcceptance(treatyCounter, counter);
                    self.validateAgainstWrittenLines(treatyCounter, counter);
                    self.updateDistributionSummary(treatyCounter);
                }
            );

            $(document).on(
                "input change",
                ".treaty.reinsurer-written-share",
                function () {
                    const $input = $(this);
                    const treatyCounter = $input.data("treaty-counter");
                    const counter = $input.data("counter");

                    self.validateAgainstWrittenLines(treatyCounter, counter);
                    self.updateDistributionSummary(treatyCounter);
                }
            );
        },

        calculateTotalAcceptance(treatyCounter, counter) {
            const compulsoryId = `#compulsory_acceptance-${treatyCounter}-${counter}`;
            const optionalId = `#optional_acceptance-${treatyCounter}-${counter}`;
            const totalId = `#total_acceptance-${treatyCounter}-${counter}`;

            const compulsory = parseFloat($(compulsoryId).val()) || 0;
            const optional = parseFloat($(optionalId).val()) || 0;
            const total = compulsory + optional;

            $(totalId).val(total.toFixed(2));

            if (total > 0) {
                $(totalId)
                    .removeClass("bg-light")
                    .addClass("bg-success bg-opacity-10");
            } else {
                $(totalId)
                    .removeClass("bg-success bg-opacity-10")
                    .addClass("bg-light");
            }
        },

        validateAgainstWrittenLines(treatyCounter, counter) {
            const writtenLinesId = `#written_share-${treatyCounter}-${counter}`;
            const totalId = `#total_acceptance-${treatyCounter}-${counter}`;

            const writtenLines = parseFloat($(writtenLinesId).val()) || 0;
            const totalAcceptance = parseFloat($(totalId).val()) || 0;

            const $totalInput = $(totalId);
            const $writtenInput = $(writtenLinesId);

            $totalInput.removeClass("is-invalid is-valid");
            $writtenInput.removeClass("is-invalid");

            if (totalAcceptance > writtenLines) {
                $totalInput.addClass("is-invalid");
                $writtenInput.addClass("is-invalid");

                this.showValidationMessage(
                    treatyCounter,
                    counter,
                    `Total Acceptance (${totalAcceptance.toFixed(
                        2
                    )}%) exceeds Written Lines (${writtenLines.toFixed(2)}%)`,
                    "error"
                );

                return false;
            } else if (totalAcceptance > 0 && totalAcceptance <= writtenLines) {
                $totalInput.addClass("is-valid");
                this.clearValidationMessage(treatyCounter, counter);
                return true;
            }

            return true;
        },

        updateDistributionSummary(treatyCounter) {
            const shareOffered =
                parseFloat($(`#share_offered-${treatyCounter}`).val()) || 0;
            let totalDistributed = 0;

            $(
                `.reinsurer-total-acceptance[data-treaty-counter="${treatyCounter}"]`
            ).each(function () {
                const value = parseFloat($(this).val()) || 0;
                totalDistributed += value;
            });

            const remaining = shareOffered - totalDistributed;

            $(`#distributed_share-${treatyCounter}`).val(
                totalDistributed.toFixed(2)
            );
            const $remainingField = $(`#rem_share-${treatyCounter}`);
            $remainingField.val(remaining.toFixed(2));

            $remainingField.removeClass(
                "bg-danger bg-warning bg-success text-white"
            );

            if (remaining < 0) {
                $remainingField.addClass("bg-danger text-white");
            } else if (remaining > 0) {
                $remainingField.addClass("bg-warning");
            } else {
                $remainingField.addClass("bg-success text-white");
            }

            this.updateGlobalValidation();
        },

        showValidationMessage(treatyCounter, counter, message, type = "error") {
            const messageId = `validation-msg-${treatyCounter}-${counter}`;
            const $reinsurerSection = $(
                `#reinsurer-div-${treatyCounter}-${counter}`
            );

            $(`#${messageId}`).remove();

            const alertClass =
                type === "error"
                    ? "alert-danger"
                    : type === "warning"
                    ? "alert-warning"
                    : "alert-success";

            const icon =
                type === "error"
                    ? "fa-exclamation-triangle"
                    : type === "warning"
                    ? "fa-exclamation-circle"
                    : "fa-check-circle";

            const $message = $(`
                <div id="${messageId}" class="alert ${alertClass} alert-dismissible fade show mt-2" role="alert">
                    <i class="fas ${icon} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);

            $reinsurerSection.append($message);
        },

        clearValidationMessage(treatyCounter, counter) {
            const messageId = `validation-msg-${treatyCounter}-${counter}`;
            $(`#${messageId}`).fadeOut(300, function () {
                $(this).remove();
            });
        },

        updateGlobalValidation() {
            const $validationMessages = $("#validation-messages");
            const $validationList = $("#validation-list");
            const errors = [];

            $(".treaty-div-section").each(function () {
                const counter = $(this).data("counter");
                const remaining =
                    parseFloat($(`#rem_share-${counter}`).val()) || 0;

                if (remaining < 0) {
                    errors.push(
                        `Treaty Section ${
                            parseInt(counter) + 1
                        }: Over-distributed by ${Math.abs(remaining).toFixed(
                            2
                        )}%`
                    );
                }
            });

            $(".is-invalid").each(function () {
                const $input = $(this);
                if ($input.hasClass("reinsurer-total-acceptance")) {
                    const treatyCounter = $input.data("treaty-counter");
                    const counter = $input.data("counter");
                    errors.push(
                        `Treaty ${parseInt(treatyCounter) + 1}, Reinsurer ${
                            parseInt(counter) + 1
                        }: Total exceeds written lines`
                    );
                }
            });

            if (errors.length > 0) {
                $validationList.html(
                    errors.map((err) => `<li>${err}</li>`).join("")
                );
                $validationMessages.slideDown();

                $("#partner-save-btn")
                    .prop("disabled", true)
                    .addClass("disabled");
            } else {
                $validationMessages.slideUp();

                $("#partner-save-btn")
                    .prop("disabled", false)
                    .removeClass("disabled");
            }
        },

        recalculateAllInTreaty(treatyCounter) {
            const self = this;
            $(
                `.reinsurer-compulsory-acceptance[data-treaty-counter="${treatyCounter}"]`
            ).each(function () {
                const counter = $(this).data("counter");
                self.calculateTotalAcceptance(treatyCounter, counter);
                self.validateAgainstWrittenLines(treatyCounter, counter);
            });

            this.updateDistributionSummary(treatyCounter);
        },

        resetReinsurerRow(treatyCounter, counter) {
            $(`#compulsory_acceptance-${treatyCounter}-${counter}`).val("");
            $(`#optional_acceptance-${treatyCounter}-${counter}`).val("");
            $(`#total_acceptance-${treatyCounter}-${counter}`).val("");
            $(`#written_share-${treatyCounter}-${counter}`).val("");

            this.clearValidationMessage(treatyCounter, counter);
            this.updateDistributionSummary(treatyCounter);
        },

        getReinsurerData(treatyCounter, counter) {
            return {
                reinsurer: $(`#reinsurer-${treatyCounter}-${counter}`).val(),
                written_share:
                    parseFloat(
                        $(`#written_share-${treatyCounter}-${counter}`).val()
                    ) || 0,
                compulsory_acceptance:
                    parseFloat(
                        $(
                            `#compulsory_acceptance-${treatyCounter}-${counter}`
                        ).val()
                    ) || 0,
                optional_acceptance:
                    parseFloat(
                        $(
                            `#optional_acceptance-${treatyCounter}-${counter}`
                        ).val()
                    ) || 0,
                total_acceptance:
                    parseFloat(
                        $(`#total_acceptance-${treatyCounter}-${counter}`).val()
                    ) || 0,
                wht_rate: $(`#wht_rate-${treatyCounter}-${counter}`).val(),
                pay_method: $(
                    `#reins_pay_method-${treatyCounter}-${counter}`
                ).val(),
            };
        },
    };

    $(document).ready(function () {
        if ($("#coverDetailsApp").length) {
            CoverDetails.init();
        }

        window.ReinsurerPlacement = ReinsurerPlacement;
        ReinsurerPlacement.init();

        window.TreatyReinsurerCalculations = TreatyReinsurerCalculations;
        TreatyReinsurerCalculations.init();
    });
})(jQuery);
