/**
 * Cover Endorsement Event Handlers
 * Updated to work with endorsement-modals.blade.php structure
 * @pk305
 */

(function ($, CoverEndorsement, CoverUtils) {
    "use strict";

    /**
     * Bind all event handlers
     */
    CoverEndorsement.bindEvents = function () {
        this.bindButtonEvents();
        this.bindEndorsementTypeChange();
        this.bindCalculationEvents();
        this.bindDateEvents();
        this.bindBrokerageEvents();
        this.bindTableActions();
        this.bindModalEvents();
    };

    /**
     * Bind button click events
     */
    CoverEndorsement.bindButtonEvents = function () {
        const self = this;

        // Endorse Cover button
        $("#btn-endorse-cover, #endorse_cover").on("click", function () {
            self.resetEndorsementForm();
            self.processSections(
                ".endorsement_section",
                ".endorsement_section_div",
                "disable",
            );
            self.elements.currentSection.hide();
            self.elements.endorseModal.modal("show");
        });

        // Renew Cover button
        $("#btn-renew-cover, #process_renew").on("click", function () {
            $("#trans_type, #form-trans-type").val("REN");
            self.elements.coverActionForm.submit();
        });

        // Renewal Notice button
        $("#btn-renewal-notice, #generateRenewalNotice").on(
            "click",
            function (e) {
                e.preventDefault();
                self.elements.renewalNoticeForm.submit();
            },
        );
    };

    /**
     * Bind endorsement type change event
     */
    CoverEndorsement.bindEndorsementTypeChange = function () {
        const self = this;
        const fieldConfig = this.getFieldConfig();

        this.elements.endorseType.on("change", function () {
            const slug = $(this).val();

            // Validate if validator exists
            if ($(this).data("validator")) {
                $(this).valid();
            }

            // First, disable all fields
            self.hideAllFields();

            // Hide change type wrapper by default
            $("#change_type_wrapper").addClass("d-none");

            if (!slug) {
                self.elements.currentSection.hide();
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

            // Show/hide current section
            if (config.showCurrentSection) {
                self.elements.currentSection.show();
            } else {
                self.elements.currentSection.hide();
            }

            // Show configured fields
            config.show.forEach(function (fieldName) {
                self.showField(fieldName);
            });

            // Show change type wrapper for types that need it
            if (config.show.includes("change_in_sum_insured_type")) {
                $("#change_type_wrapper").removeClass("d-none");
            }

            // Trigger change for dependent fields
            if (config.show.includes("brokerage_comm_type")) {
                self.elements.brokerageCommType.trigger("change");
            }

            if (config.show.includes("apply_eml")) {
                self.elements.applyEml.trigger("change");
            }

            // Reset premium fields for certain types
            if (["change-premium", "change-sum-insured"].includes(slug)) {
                self.elements.endorsedPremium.val("");
                self.elements.newPremium.val("0");
            }
        });
    };

    /**
     * Hide all endorsement fields
     */
    CoverEndorsement.hideAllFields = function () {
        const allFields = this.getAllFieldNames();
        const self = this;

        allFields.forEach(function (fieldName) {
            self.processSections(
                "." + fieldName,
                "." + fieldName + "_div",
                "disable",
            );
        });
    };

    /**
     * Show a specific field by name
     */
    CoverEndorsement.showField = function (fieldName) {
        this.processSections(
            "." + fieldName,
            "." + fieldName + "_div",
            "enable",
        );
    };

    /**
     * Process sections (show/hide and enable/disable)
     */
    CoverEndorsement.processSections = function (
        sectionClass,
        sectionDivClass,
        action,
    ) {
        if (action === "enable") {
            $(sectionClass + ", " + sectionDivClass).each(function () {
                if ($(this).hasClass(sectionDivClass.substr(1))) {
                    $(this).show();
                } else {
                    $(this).prop("disabled", false);
                }
            });
        } else {
            $(sectionClass + ", " + sectionDivClass).each(function () {
                if ($(this).hasClass(sectionDivClass.substr(1))) {
                    $(this).hide();
                } else {
                    $(this).prop("disabled", true);
                }
            });
        }
    };

    /**
     * Bind calculation events for sum insured and premium
     */
    CoverEndorsement.bindCalculationEvents = function () {
        const self = this;

        // Change type dropdown
        this.elements.changeType.on("change keyup", function () {
            if ($(this).data("validator")) {
                $(this).valid();
            }
            self.calculateNewValues();
        });

        // Endorsed sum insured input
        this.elements.endorsedSumInsured.on("change keyup", function () {
            $(this).val(
                CoverUtils.numberWithCommas(
                    CoverUtils.parseAmount($(this).val()),
                ),
            );
            self.calculateNewValues();
        });

        // Endorsed premium input
        this.elements.endorsedPremium.on("change keyup", function () {
            $(this).val(
                CoverUtils.numberWithCommas(
                    CoverUtils.parseAmount($(this).val()),
                ),
            );
            self.calculateNewValues();
        });

        // Apply EML dropdown
        this.elements.applyEml.on("change", function () {
            if ($(this).data("validator")) {
                $(this).valid();
            }

            const applyEml = $(this).val();

            if (applyEml === "Y") {
                self.processSections(".eml_rate", ".eml_rate_div", "enable");
                self.processSections(".eml_amt", ".eml_amt_div", "enable");
            } else {
                self.processSections(".eml_rate", ".eml_rate_div", "disable");
                self.processSections(".eml_amt", ".eml_amt_div", "disable");
            }

            self.calculateNewValues();
        });

        // EML rate input
        this.elements.emlRate.on("change keyup", function () {
            let emlRate = parseFloat($(this).val()) || 0;
            emlRate = Math.min(100, Math.max(0, emlRate));
            $(this).val(emlRate);

            const totalSumInsured = CoverUtils.parseAmount(
                self.elements.newSumInsured.val(),
            );
            const emlAmt = totalSumInsured * (emlRate / 100);

            const endorsedSumInsured = CoverUtils.parseAmount(
                self.elements.endorsedSumInsured.val(),
            );
            const endorsedEmlAmt = endorsedSumInsured * (emlRate / 100);

            self.elements.emlAmt.val(CoverUtils.numberWithCommas(emlAmt));
            self.elements.newEffectiveSumInsured.val(
                CoverUtils.numberWithCommas(emlAmt),
            );
            $("input[name='endorsed_effective_sum_insured']").val(
                CoverUtils.numberWithCommas(endorsedEmlAmt),
            );
        });

        // EML amount input
        this.elements.emlAmt.on("change keyup", function () {
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
    };

    /**
     * Calculate new sum insured and premium values
     */
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

        this.elements.newPremium.val(
            CoverUtils.numberWithCommas(endorsedPremium === 0 ? 0 : newPremium),
        );
        this.elements.newSumInsured.val(
            CoverUtils.numberWithCommas(newSumInsured),
        );
        this.elements.newEffectiveSumInsured.val(
            CoverUtils.numberWithCommas(effectiveSumInsured),
        );
        $("input[name='endorsed_effective_sum_insured']").val(
            CoverUtils.numberWithCommas(effectiveSumInsured),
        );
    };

    /**
     * Bind date-related events
     */
    CoverEndorsement.bindDateEvents = function () {
        const self = this;

        // Premium due date change
        this.elements.premiumDueDate.on("change", function () {
            self.calculateExtensionDays();
        });

        // New premium due date change
        this.elements.newPremiumDueDate.on("change", function () {
            self.calculateExtensionDays();
        });

        // Extension days change
        this.elements.extensionDays.on("change keyup", function () {
            const startDate = self.elements.premiumDueDate.val();
            const days = parseInt($(this).val()) || 0;

            if (startDate && days >= 0) {
                const endDate = CoverUtils.addDays(startDate, days);
                self.elements.newPremiumDueDate.val(endDate);
            }
        });

        // New cover from change - auto-set cover to
        this.elements.newCoverFrom.on("change", function () {
            const startDate = moment($(this).val());
            const endDate = startDate.add(1, "years").subtract(1, "days");
            self.elements.newCoverTo.val(endDate.format("YYYY-MM-DD"));
        });
    };

    /**
     * Calculate extension days between dates
     */
    CoverEndorsement.calculateExtensionDays = function () {
        const startDate = this.elements.premiumDueDate.val();
        const endDate = this.elements.newPremiumDueDate.val();

        if (startDate && endDate) {
            const days = CoverUtils.daysBetween(startDate, endDate);
            this.elements.extensionDays.val(days);
        }
    };

    /**
     * Bind brokerage commission events
     */
    CoverEndorsement.bindBrokerageEvents = function () {
        const self = this;

        this.elements.brokerageCommType.on("change", function () {
            const commType = $(this).val();

            // Hide both fields first
            $(".brokerage_comm_amt_div").hide();
            self.elements.brokerageCommAmt.hide().prop("disabled", true);
            $(".brokerage_comm_rate_div").hide();
            self.elements.brokerageCommRate.hide().prop("disabled", true);

            // Reset values
            self.elements.brokerageCommRate.val(null);
            self.elements.brokerageCommAmt.val(null);

            if (commType === "R") {
                $(".brokerage_comm_rate_div").show();
                self.elements.brokerageCommRate.show().prop("disabled", false);
                self.calculateBrokerageCommRate();
            } else {
                $(".brokerage_comm_amt_div").show();
                self.elements.brokerageCommAmt.show().prop("disabled", false);
            }
        });
    };

    /**
     * Calculate brokerage commission rate
     */
    CoverEndorsement.calculateBrokerageCommRate = function () {
        const cedantCommRate = CoverUtils.parseAmount($("#comm_rate").val());
        const reinCommRate = CoverUtils.parseAmount(
            $("#reins_comm_rate").val(),
        );
        let brokerageCommRate = 0;

        if (cedantCommRate && reinCommRate) {
            brokerageCommRate = reinCommRate - cedantCommRate;
        }

        this.elements.brokerageCommRate.val(brokerageCommRate);
    };

    /**
     * Bind table action events
     */
    CoverEndorsement.bindTableActions = function () {
        const self = this;

        // View endorsement
        $(document).on("click", ".view-endorsement-table", function (e) {
            e.preventDefault();

            const data = $(this).data();
            const baseUrl = self.config.routes.coverHome;
            const newUrl = `${baseUrl}?endorsement_no=${encodeURIComponent(data.endorsement_no)}`;

            $.ajax({
                url: newUrl,
                type: "POST",
                data: {
                    cover_no: data.cover_no,
                    endorsement_no: data.endorsement_no,
                    customer_id: data.customer_id,
                },
                headers: {
                    "X-CSRF-TOKEN": self.config.csrfToken,
                },
                success: function (response) {
                    if (response) {
                        window.location.href = newUrl;
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error("Failed to load endorsement details");
                },
            });
        });

        // Double-click on table row
        this.elements.endorsementTable.on("dblclick", "tbody tr", function () {
            const rowData = self.dataTable.row(this).data();
            if (rowData) {
                const $viewBtn = $(this).find(".view-endorsement-table");
                if ($viewBtn.length) {
                    $viewBtn.trigger("click");
                }
            }
        });

        // Remove endorsement
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

                CoverUtils.fetchWithCsrf(self.config.routes.deleteCover, {
                    method: "POST",
                    body: JSON.stringify({
                        cover_no: data.cover_no,
                        endorsement_no: data.endorsement_no,
                        customer_id: data.customer_id,
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.status === 201) {
                            toastr.success(
                                "Action was successful",
                                "Successful",
                            );
                            self.dataTable.ajax.reload();
                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        } else if (data.status === 422) {
                            self.showValidationErrors(data.errors);
                        } else {
                            toastr.error("Failed to remove details");
                        }
                    })
                    .catch(function () {
                        toastr.error("An internal error occurred");
                    });
            });
        });
    };

    /**
     * Bind modal events
     */
    CoverEndorsement.bindModalEvents = function () {
        const self = this;

        // Reset form when modal is hidden
        $(".cancelCoverEndorsementForm").on("click", function () {
            self.resetEndorsementForm();
        });

        this.elements.endorseModal.on("hidden.bs.modal", function () {
            self.resetEndorsementForm();
        });
    };

    /**
     * Reset the endorsement form
     */
    CoverEndorsement.resetEndorsementForm = function () {
        this.elements.endorseForm[0].reset();
        this.hideAllFields();
        this.elements.currentSection.hide();
        this.elements.endorseType.val("").trigger("change.select2");

        // Hide change type wrapper
        $("#change_type_wrapper").addClass("d-none");

        $(".errorClass").remove();
        $(".is-invalid").removeClass("is-invalid");
    };

    /**
     * Initialize form validation
     */
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
                    element.parent().length ? element : element.parent(),
                );
            },
            submitHandler: function (form) {
                if (self.validateEndorsementInputs()) {
                    form.submit();
                }
            },
        });
    };

    /**
     * Validate endorsement inputs
     */
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

    /**
     * Show validation errors
     */
    CoverEndorsement.showValidationErrors = function (errors) {
        if (typeof showServerSideValidationErrors === "function") {
            showServerSideValidationErrors(errors);
        } else {
            Object.keys(errors).forEach(function (key) {
                toastr.error(errors[key][0]);
            });
        }
    };

    /**
     * Initialize Portfolio handlers
     */
    CoverEndorsement.initPortfolioHandlers = function () {
        const self = this;

        // Hide portfolio fields initially
        this.elements.portfolioAddReinsurer.hide();
        $(".port_share_div").hide();

        // Portfolio year change
        this.elements.portfolioYear.on("change", function () {
            const treatyYear = $(this).val() || 0;
            const coverNo = self.config.coverNo;

            $.ajax({
                type: "GET",
                url: self.config.routes.getTreatyYearCover,
                data: {
                    cover_no: coverNo,
                    treaty_year: treatyYear,
                },
                cache: false,
                success: function (response) {
                    self.elements.origEndorsement.empty();
                    self.elements.origEndorsement.append(
                        $("<option>")
                            .text("-- Select Cover Reference--")
                            .attr("value", ""),
                    );

                    $.each(response, function (i, value) {
                        self.elements.origEndorsement.append(
                            $("<option>")
                                .text(
                                    value.endorsement_no +
                                        "-" +
                                        value.cover_from +
                                        " To " +
                                        value.cover_to,
                                )
                                .attr("value", value.endorsement_no),
                        );
                    });

                    self.elements.origEndorsement.trigger("change.select2");
                },
            });
        });

        // Original endorsement change
        this.elements.origEndorsement.on("change", function () {
            const origEndorsement = $(this).val();
            const treatyYear = self.elements.portfolioYear.val();
            const portfolioType = self.elements.portfolioType.val();
            const coverNo = self.config.coverNo;
            let portfolioShare = 0;
            let portPremRate = 0;
            let portLossRate = 0;

            $.ajax({
                type: "GET",
                url: self.config.routes.getReinsurersOrigEndorsement,
                data: {
                    portfolio_type: portfolioType,
                    cover_no: coverNo,
                    treaty_year: treatyYear,
                    orig_endorsement: origEndorsement,
                },
                cache: false,
                success: function (response) {
                    const count = response.count;
                    const reinsurers = response.reinsurers;

                    if (count > 0) {
                        self.elements.portfolioAddReinsurer.show();

                        self.elements.portReinsurer.empty();
                        self.elements.portReinsurer.append(
                            $("<option>")
                                .text("-- Select Reinsurer--")
                                .attr("value", ""),
                        );

                        $.each(reinsurers, function (i, value) {
                            if (portfolioType === "OUT") {
                                portfolioShare = value.share;
                                portPremRate = value.port_prem_rate;
                                portLossRate = value.port_loss_rate;
                            }

                            self.elements.portReinsurer.append(
                                $("<option>")
                                    .text(
                                        value.customer_id + " - " + value.name,
                                    )
                                    .attr("value", value.customer_id)
                                    .attr(
                                        "portfolio_share",
                                        parseFloat(portfolioShare).toFixed(2),
                                    )
                                    .attr(
                                        "port_prem_rate",
                                        parseFloat(portPremRate).toFixed(2),
                                    )
                                    .attr(
                                        "port_loss_rate",
                                        parseFloat(portLossRate).toFixed(2),
                                    ),
                            );
                        });

                        self.elements.portReinsurer.trigger("change.select2");
                    }
                },
            });
        });

        // Port reinsurer change
        this.elements.portReinsurer.on("change", function () {
            const share = $("select#port_reinsurer option:selected").attr(
                "portfolio_share",
            );
            const portPremRate = $(
                "select#port_reinsurer option:selected",
            ).attr("port_prem_rate");
            const portLossRate = $(
                "select#port_reinsurer option:selected",
            ).attr("port_loss_rate");

            $(".port_share_div").show();
            self.elements.portShare.val(share);
            self.elements.portPrmRate.val(portPremRate);
            self.elements.portLossRate.val(portLossRate);
        });

        // Port share validation
        this.elements.portShare.on("keyup", function () {
            let newShare = parseFloat($(this).val()).toFixed(2);
            const portfolioType = self.elements.portfolioType.val();

            if (newShare < 0) {
                toastr.error(
                    "You cannot have share less than zero",
                    "Incomplete data",
                );
                return false;
            }

            if (portfolioType === "OUT") {
                const origShare = $(
                    "select#port_reinsurer option:selected",
                ).attr("portfolio_share");

                if (parseInt(origShare) < parseInt(newShare)) {
                    $(this).val(origShare);
                    toastr.error(
                        "Please Adjust share, You cannot have OUT GO share more than Original Share",
                        "Incomplete data",
                    );
                    return false;
                }
            }
        });
    };

    /**
     * Initialize Quarterly Figures / Profit Commission handlers
     */
    CoverEndorsement.initQuarterlyFiguresHandlers = function () {
        const self = this;

        this.elements.treatyYear.on("change", function () {
            const treatyYear = $(this).val() || 0;
            const coverNo = self.config.coverNo;

            $("#pc_qtr_details").remove();

            $.ajax({
                type: "GET",
                url: self.config.routes.getQuarterlyFigures,
                data: {
                    cover_no: coverNo,
                    treaty_year: treatyYear,
                },
                cache: false,
                success: function (response) {
                    if (response.length > 0) {
                        self.renderQuarterlyFigures(response);
                    }
                },
            });
        });
    };

    /**
     * Render quarterly figures table
     */
    CoverEndorsement.renderQuarterlyFigures = function (response) {
        let html = `
            <div class="mb-3" id="pc_qtr_details">
                <table>
                    <thead>
                        <tr>
                            <th>Quarter</th>
                            <th>Rein Class</th>
                            <th>Treaty</th>
                            <th>Premium</th>
                            <th>Commission</th>
                            <th>Premium Tax</th>
                            <th>Reinsurance Tax</th>
                            <th>Claim Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="8"><h6>Summary Quarterly Figures Below</h6></td></tr>`;

        let prevQuarter = null;
        let totalRowAdded = false;
        let totalPremium = 0;
        let totalCommission = 0;
        let totalPremiumTax = 0;
        let totalReinsuranceTax = 0;
        let totalClaims = 0;

        $.each(response, function (i, value) {
            if (value.quarter !== prevQuarter) {
                if (prevQuarter !== null && !totalRowAdded) {
                    html += `
                        <tr class="total-row" style="font-weight: bold;">
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td>${CoverUtils.numberWithCommas(totalPremium)}</td>
                            <td>${CoverUtils.numberWithCommas(totalCommission)}</td>
                            <td>${CoverUtils.numberWithCommas(totalPremiumTax)}</td>
                            <td>${CoverUtils.numberWithCommas(totalReinsuranceTax)}</td>
                            <td>${CoverUtils.numberWithCommas(totalClaims)}</td>
                        </tr>`;
                    totalRowAdded = true;
                }

                totalPremium = 0;
                totalCommission = 0;
                totalPremiumTax = 0;
                totalReinsuranceTax = 0;
                totalClaims = 0;

                prevQuarter = value.quarter;
                totalRowAdded = false;
            }

            totalPremium += parseFloat(value.premium);
            totalCommission += parseFloat(value.commission);
            totalPremiumTax += parseFloat(value.premium_tax);
            totalReinsuranceTax += parseFloat(value.reinsurance_tax);
            totalClaims += parseFloat(value.claims);

            html += `
                <tr>
                    <td><input type="text" class="form-control pc_quarter" name="pc_quarter[]" value="${value.quarter}" readonly></td>
                    <td><input type="text" class="form-control pc_reinclass" name="pc_reinclass[]" value="${value.class_code}" readonly></td>
                    <td><input type="text" class="form-control pc_treaty" name="pc_treaty[]" value="${value.treaty}" readonly></td>
                    <td><input type="text" class="form-control pc_premium" name="pc_premium[]" value="${CoverUtils.numberWithCommas(value.premium)}" readonly></td>
                    <td><input type="text" class="form-control pc_commission" name="pc_commission[]" value="${CoverUtils.numberWithCommas(value.commission)}" readonly></td>
                    <td><input type="text" class="form-control pc_premium_tax" name="pc_premium_tax[]" value="${CoverUtils.numberWithCommas(value.premium_tax)}" readonly></td>
                    <td><input type="text" class="form-control pc_reinsurance_tax" name="pc_reinsurance_tax[]" value="${CoverUtils.numberWithCommas(value.reinsurance_tax)}" readonly></td>
                    <td><input type="text" class="form-control pc_claim_amount" name="pc_claim_amount[]" value="${CoverUtils.numberWithCommas(value.claims)}" readonly></td>
                </tr>`;
        });

        // Add final total row
        if (!totalRowAdded && prevQuarter !== null) {
            html += `
                <tr class="total-row" style="font-weight: bold;">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td>${CoverUtils.numberWithCommas(totalPremium)}</td>
                    <td>${CoverUtils.numberWithCommas(totalCommission)}</td>
                    <td>${CoverUtils.numberWithCommas(totalPremiumTax)}</td>
                    <td>${CoverUtils.numberWithCommas(totalReinsuranceTax)}</td>
                    <td>${CoverUtils.numberWithCommas(totalClaims)}</td>
                </tr>`;
        }

        html += `
                    </tbody>
                </table>
                <table>
                    <thead>
                        <tr>
                            <th>Portfolio Entry Premium</th>
                            <th>Portfolio Entry Loss</th>
                            <th>Portfolio Withdrawal Premium</th>
                            <th>Portfolio Withdrawal Loss</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="4"><h6>Capture Portfolios Below</h6></td></tr>
                        <tr>
                            <td><input type="text" class="form-inputs" id="port_entry_prem" name="port_entry_prem" onkeyup="this.value=numberWithCommas(this.value)" required></td>
                            <td><input type="text" class="form-inputs" id="port_entry_loss" name="port_entry_loss" onkeyup="this.value=numberWithCommas(this.value)" required></td>
                            <td><input type="text" class="form-inputs" id="port_withdrawal_prem" name="port_withdrawal_prem" onkeyup="this.value=numberWithCommas(this.value)" required></td>
                            <td><input type="text" class="form-inputs" id="port_withdrawal_loss" name="port_withdrawal_loss" onkeyup="this.value=numberWithCommas(this.value)" required></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="quaterly-figures-save-btn" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
            </div>`;

        this.elements.profitCommissionForm.append(html);
    };

    /**
     * Initialize MDP Installment handlers
     */
    CoverEndorsement.initMDPHandlers = function () {
        const self = this;

        this.elements.mdpInstallment.on("change", function (e) {
            e.preventDefault();

            const selectedInstallment = $(this).val();
            let selectedInstallmentTotalAmt =
                parseFloat(
                    $("select#mdp-installment option:selected").data(
                        "total_amt",
                    ),
                ) || 0;

            self.elements.mdpInstallmentsSection.empty();

            // Get MDP installments from config
            const mdpInstallments = self.config.mdpInsLayerwise || [];

            if (mdpInstallments.length > 0) {
                const selectedInstallemntLayers = mdpInstallments.filter(
                    (inst) => inst.installment_no == selectedInstallment,
                );

                selectedInstallemntLayers.forEach(function (Inslayer) {
                    const layerMdpAmt =
                        parseFloat(Inslayer.installment_amt) || 0;

                    self.elements.mdpInstallmentsSection.append(`
                        <div class="row installment-section">
                            <div class="col-md-4">
                                <label for="layer_no">Layer no.</label>
                                <input type="text" name="layer_no[]" value="${Inslayer.layer_no}" readonly class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="min_deposit">Total Installment Amount</label>
                                <input type="text" name="min_deposit[]" value="${CoverUtils.numberWithCommas(selectedInstallmentTotalAmt)}" readonly class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="installment_amt">Installment Amount</label>
                                <input type="text" name="installment_amt[]" value="${CoverUtils.numberWithCommas(layerMdpAmt)}" class="form-control amount" readonly required>
                            </div>
                        </div>
                    `);
                });
            }
        });
    };
})(jQuery, window.CoverEndorsement, window.CoverUtils);

// Global helper function for backward compatibility
function numberWithCommas(x) {
    return window.CoverUtils ? window.CoverUtils.numberWithCommas(x) : x;
}

function removeCommas(str) {
    return window.CoverUtils ? window.CoverUtils.removeCommas(str) : str;
}
