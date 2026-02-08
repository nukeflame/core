/**
 * Cover Endorsement Event Handlers
 * @pk305
 */

(function ($, CoverEndorsement, CoverUtils) {
    "use strict";

    CoverEndorsement.bindEvents = function () {
        this.bindButtonEvents();
        this.bindEndorsementTypeChange();
        this.bindCalculationEvents();
        this.bindDateEvents();
        this.bindBrokerageEvents();
        this.bindTableActions();
        this.bindModalEvents();
    };

    CoverEndorsement.bindButtonEvents = function () {
        const self = this;

        $("#btn-endorse-cover").on("click", function () {
            self.resetEndorsementForm();
            self.elements.endorseModal.modal("show");
        });

        $("#btn-renew-cover").on("click", function () {
            $("#form-trans-type").val("REN");
            self.elements.coverActionForm.submit();
        });

        $("#btn-renewal-notice").on("click", function (e) {
            e.preventDefault();
            self.elements.renewalNoticeForm.submit();
        });
    };

    CoverEndorsement.bindEndorsementTypeChange = function () {
        const self = this;
        const fieldConfig = this.getFieldConfig();

        this.elements.endorseType.on("change", function () {
            const slug = $(this).val();

            if ($(this).data("validator")) {
                $(this).valid();
            }

            self.hideAllFields();

            if (!slug) {
                self.elements.currentSection.addClass("d-none");
                return;
            }

            const config = fieldConfig[slug];

            if (!config) {
                console.warn(
                    "No configuration found for endorsement type:",
                    slug,
                );
                return;
            }

            if (config.showCurrentSection) {
                self.elements.currentSection.removeClass("d-none");
            } else {
                self.elements.currentSection.addClass("d-none");
            }

            config.show.forEach(function (fieldName) {
                self.showField(fieldName);
            });

            if (config.show.includes("brokerage-comm-type")) {
                self.elements.brokerageCommType.trigger("change");
            }

            if (config.show.includes("apply-eml")) {
                self.elements.applyEml.trigger("change");
            }

            if (["change-premium", "change-sum-insured"].includes(slug)) {
                self.elements.endorsedPremium.val("");
                self.elements.newPremium.val("0");
            }
        });
    };

    CoverEndorsement.hideAllFields = function () {
        $(".field-group").each(function () {
            $(this).addClass("d-none");
            $(this).find("input, select, textarea").prop("disabled", true);
        });
    };

    CoverEndorsement.showField = function (fieldName) {
        const $field = $(`.field-group[data-field="${fieldName}"]`);
        $field.removeClass("d-none");
        $field.find("input, select, textarea").prop("disabled", false);
    };

    CoverEndorsement.bindCalculationEvents = function () {
        const self = this;

        this.elements.changeType.on("change keyup", function () {
            if ($(this).data("validator")) {
                $(this).valid();
            }
            self.calculateNewValues();
        });

        this.elements.endorsedSumInsured.on("change keyup", function () {
            self.formatAmountField(this);
            self.calculateNewValues();
        });

        this.elements.endorsedPremium.on("change keyup", function () {
            self.formatAmountField(this);
            self.calculateNewValues();
        });

        this.elements.applyEml.on("change", function () {
            if ($(this).data("validator")) {
                $(this).valid();
            }

            const applyEml = $(this).val();
            const emlFields = ["eml-rate", "eml-amount"];

            if (applyEml === "Y") {
                emlFields.forEach(function (field) {
                    self.showField(field);
                });
            } else {
                emlFields.forEach(function (field) {
                    const $field = $(`.field-group[data-field="${field}"]`);
                    $field.addClass("d-none");
                    $field.find("input").prop("disabled", true);
                });
            }

            self.calculateNewValues();
        });

        this.elements.emlRate.on("change keyup", function () {
            let emlRate = parseFloat($(this).val()) || 0;
            emlRate = Math.min(100, Math.max(0, emlRate));
            $(this).val(emlRate);

            const totalSumInsured = CoverUtils.parseAmount(
                self.elements.newSumInsured.val(),
            );
            const emlAmt = totalSumInsured * (emlRate / 100);

            self.elements.emlAmt.val(CoverUtils.numberWithCommas(emlAmt));
            self.elements.newEffectiveSumInsured.val(
                CoverUtils.numberWithCommas(emlAmt),
            );
        });

        this.elements.emlAmt.on("change keyup", function () {
            self.formatAmountField(this);

            let emlAmt = CoverUtils.parseAmount($(this).val());
            const totalSumInsured = CoverUtils.parseAmount(
                self.elements.newSumInsured.val(),
            );

            emlAmt = Math.min(totalSumInsured, Math.max(0, emlAmt));
            const emlRate =
                totalSumInsured > 0 ? (emlAmt / totalSumInsured) * 100 : 0;

            self.elements.emlRate.val(emlRate.toFixed(2));
            self.elements.emlAmt.val(CoverUtils.numberWithCommas(emlAmt));
            self.elements.newEffectiveSumInsured.val(
                CoverUtils.numberWithCommas(emlAmt),
            );
        });

        $(".amount-field").on("blur", function () {
            self.formatAmountField(this);
        });
    };

    CoverEndorsement.formatAmountField = function (element) {
        const value = CoverUtils.parseAmount($(element).val());
        $(element).val(CoverUtils.numberWithCommas(value));
    };

    CoverEndorsement.calculateNewValues = function () {
        const changeType = this.elements.changeType.val();
        const endorsedSumInsured = CoverUtils.parseAmount(
            this.elements.endorsedSumInsured.val(),
        );
        const endorsedPremium = CoverUtils.parseAmount(
            this.elements.endorsedPremium.val(),
        );
        const emlRate = parseFloat(this.elements.emlRate.val()) || 0;
        const applyEml = this.elements.applyEml.val();

        let newSumInsured = 0;
        let newPremium = 0;

        if (changeType === "increase") {
            newSumInsured = Math.ceil(
                Math.max(0, this.state.currentSumInsured + endorsedSumInsured),
            );
            newPremium = Math.ceil(
                Math.max(0, this.state.currentPremium + endorsedPremium),
            );
        } else if (changeType === "decrease") {
            if (endorsedSumInsured > this.state.currentSumInsured) {
                toastr.error(
                    "Decrease amount cannot be greater than current sum insured",
                );
                this.elements.endorsedSumInsured.val("");
                return;
            }
            newSumInsured = Math.ceil(
                Math.max(0, this.state.currentSumInsured - endorsedSumInsured),
            );
            newPremium = Math.ceil(
                Math.max(0, this.state.currentPremium - endorsedPremium),
            );
        }

        let effectiveSumInsured = newSumInsured;

        if (applyEml === "Y" && emlRate > 0) {
            effectiveSumInsured = newSumInsured * (emlRate / 100);
            this.elements.emlAmt.val(
                CoverUtils.numberWithCommas(effectiveSumInsured),
            );
        }

        this.elements.newPremium.val(CoverUtils.numberWithCommas(newPremium));
        this.elements.newSumInsured.val(
            CoverUtils.numberWithCommas(newSumInsured),
        );
        this.elements.newEffectiveSumInsured.val(
            CoverUtils.numberWithCommas(effectiveSumInsured),
        );
        $("#endorsed-effective-sum-insured").val(
            CoverUtils.numberWithCommas(effectiveSumInsured),
        );
    };

    CoverEndorsement.bindDateEvents = function () {
        const self = this;

        this.elements.premiumDueDate.on("change", function () {
            self.calculateExtensionDays();
        });

        this.elements.newPremiumDueDate.on("change", function () {
            self.calculateExtensionDays();
        });

        this.elements.extensionDays.on("change keyup", function () {
            const startDate = self.elements.premiumDueDate.val();
            const days = parseInt($(this).val()) || 0;

            if (startDate && days >= 0) {
                const endDate = CoverUtils.addDays(startDate, days);
                self.elements.newPremiumDueDate.val(endDate);
            }
        });

        this.elements.newCoverFrom.on("change", function () {
            const startDate = moment($(this).val());
            const endDate = startDate.add(1, "years").subtract(1, "days");
            self.elements.newCoverTo.val(endDate.format("YYYY-MM-DD"));
        });
    };

    CoverEndorsement.calculateExtensionDays = function () {
        const startDate = this.elements.premiumDueDate.val();
        const endDate = this.elements.newPremiumDueDate.val();

        if (startDate && endDate) {
            const days = CoverUtils.daysBetween(startDate, endDate);
            this.elements.extensionDays.val(days);
        }
    };

    CoverEndorsement.bindBrokerageEvents = function () {
        const self = this;

        this.elements.brokerageCommType.on("change", function () {
            const commType = $(this).val();

            $(`.field-group[data-field="brokerage-comm-rate"]`).addClass(
                "d-none",
            );
            $(`.field-group[data-field="brokerage-comm-amt"]`).addClass(
                "d-none",
            );
            self.elements.brokerageCommRate.prop("disabled", true).val("");
            self.elements.brokerageCommAmt.prop("disabled", true).val("");

            if (commType === "R") {
                self.showField("brokerage-comm-rate");
            } else {
                self.showField("brokerage-comm-amt");
            }
        });
    };

    CoverEndorsement.bindTableActions = function () {
        const self = this;

        $(document).on("click", ".view-endorsement-table", function (e) {
            e.preventDefault();

            const data = $(this).data();
            const baseUrl = self.config.routes.coverHome;
            const newUrl = `${baseUrl}?endorsement_no=${encodeURIComponent(
                data.endorsement_no,
            )}`;

            CoverUtils.ajax(newUrl, {
                method: "POST",
                data: JSON.stringify({
                    cover_no: data.cover_no,
                    endorsement_no: data.endorsement_no,
                    customer_id: data.customer_id,
                }),
            })
                .done(function (response) {
                    if (response) {
                        window.location.href = newUrl;
                    }
                })
                .fail(function (xhr, status, error) {
                    toastr.error("Failed to load endorsement details");
                });
        });

        // Add dblclick support for table rows
        this.elements.endorsementTable.on("dblclick", "tbody tr", function () {
            const rowData = self.dataTable.row(this).data();
            if (rowData) {
                const $viewBtn = $(this).find(".view-endorsement-table");
                if ($viewBtn.length) {
                    $viewBtn.trigger("click");
                }
            }
        });

        $(document).on("click", ".remove-endorsement-table", function (e) {
            e.preventDefault();

            const data = $(this).data();

            Swal.fire({
                title: "Remove Item",
                text: "This action will remove this item from this cover",
                showCancelButton: true,
                confirmButtonText: "Yes, Remove",
                cancelButtonText: "Cancel",
                icon: "warning",
            }).then(function (result) {
                if (result.isDismissed) return;

                CoverUtils.ajax(self.config.routes.deleteCover, {
                    method: "POST",
                    data: JSON.stringify({
                        cover_no: data.cover_no,
                        endorsement_no: data.endorsement_no,
                        customer_id: data.customer_id,
                    }),
                })
                    .done(function (response) {
                        if (response.status === 201) {
                            toastr.success(
                                "Action was successful",
                                "Successful",
                            );
                            self.dataTable.ajax.reload();
                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        } else if (response.status === 422) {
                            self.showValidationErrors(response.errors);
                        } else {
                            toastr.error("Failed to remove details");
                        }
                    })
                    .fail(function () {
                        toastr.error("An internal error occurred");
                    });
            });
        });
    };

    CoverEndorsement.bindModalEvents = function () {
        const self = this;

        this.elements.endorseModal.on("hidden.bs.modal", function () {
            self.resetEndorsementForm();
        });
    };

    CoverEndorsement.resetEndorsementForm = function () {
        this.elements.endorseForm[0].reset();
        this.hideAllFields();
        this.elements.currentSection.addClass("d-none");
        this.elements.endorseType.val("").trigger("change.select2");

        $(".errorClass").remove();
        $(".is-invalid").removeClass("is-invalid");
    };

    CoverEndorsement.initFormValidation = function () {
        const self = this;

        this.elements.endorseForm.validate({
            errorClass: "errorClass",
            rules: {
                endorse_type: { required: true },
                endorse_narration: { required: true },
            },
            messages: {
                endorse_type: { required: "Select Endorsement Type" },
                endorse_narration: { required: "Narration is required" },
            },
            errorPlacement: function (error, element) {
                error.insertAfter(
                    element.closest(".field-group").length
                        ? element
                        : element.parent(),
                );
            },
            submitHandler: function (form) {
                if (self.validateEndorsementInputs()) {
                    form.submit();
                }
            },
        });
    };

    CoverEndorsement.validateEndorsementInputs = function () {
        const endorseType = this.elements.endorseType.val();
        const changeType = this.elements.changeType.val();
        const endorsedSumInsured = CoverUtils.parseAmount(
            this.elements.endorsedSumInsured.val(),
        );
        const narration = this.elements.endorseNarration.val();

        if (!endorseType) {
            toastr.error("Please select an endorsement type");
            return false;
        }

        if (
            changeType === "decrease" &&
            endorsedSumInsured > this.state.currentSumInsured
        ) {
            toastr.error(
                "Decrease amount cannot be greater than current sum insured",
            );
            this.elements.endorsedSumInsured.val("").focus();
            return false;
        }

        if (!narration || !narration.trim()) {
            toastr.error("Narration is required");
            this.elements.endorseNarration.focus();
            return false;
        }

        return true;
    };

    CoverEndorsement.showValidationErrors = function (errors) {
        if (typeof showServerSideValidationErrors === "function") {
            showServerSideValidationErrors(errors);
        } else {
            Object.keys(errors).forEach(function (key) {
                toastr.error(errors[key][0]);
            });
        }
    };
})(jQuery, window.CoverEndorsement, window.CoverUtils);
