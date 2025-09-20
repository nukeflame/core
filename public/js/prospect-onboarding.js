const ProspectOnboarding = {
    config: {
        formId: "",
        prospect: null,
        routes: {},
        csrfToken: "",
    },

    state: {
        contactCounter: 0,
        layerCounter: 0,
        commCounter: 0,
        isSubmitting: false,
        formValidated: false,
    },

    cache: {
        form: null,
        submitBtn: null,
        cancelBtn: null,
        loadingOverlay: null,
    },

    init(options) {
        this.config = { ...this.config, ...options };
        this.cacheElements();
        this.bindEvents();
        // this.initializeFormSections();
        // this.setupValidation();
    },

    cacheElements() {
        this.cache.form = document.getElementById(this.config.formId);
        this.cache.submitBtn = document.getElementById("submitBtn");
        this.cache.cancelBtn = document.getElementById("cancelBtn");
        this.cache.loadingOverlay = document.getElementById("loadingOverlay");

        if (!this.cache.form) {
            console.error("Form element not found");
            return;
        }
    },

    bindEvents() {
        // Form submission
        this.cache.form?.addEventListener(
            "submit",
            this.handleFormSubmit.bind(this)
        );

        // Button clicks
        this.cache.submitBtn?.addEventListener(
            "click",
            this.handleSubmit.bind(this)
        );
        this.cache.cancelBtn?.addEventListener(
            "click",
            this.handleCancel.bind(this)
        );

        this.bindContactEvents();
        this.bindInsuranceEvents();
        this.bindEngagementEvents();

        this.bindSearchEvents();

        $("#type_of_bus").on(
            "change",
            this.handleBusinessTypeChange.bind(this)
        );

        // Other form interactions
        this.bindMiscEvents();
    },

    /**
     * Bind contact form events
     */
    bindContactEvents() {
        $(document).on("click", ".add-contact", this.addContact.bind(this));
        $(document).on(
            "click",
            ".remove-contact",
            this.removeContact.bind(this)
        );
    },

    /**
     * Bind insurance form events
     */
    bindInsuranceEvents() {
        $("#class_group").on("change", this.handleClassGroupChange.bind(this));
        $("#sum_insured_type").on(
            "change",
            this.handleSumInsuredTypeChange.bind(this)
        );
        $("#apply_eml").on("change", this.handleEMLChange.bind(this));
        $("#eml_rate").on("keyup", this.calculateEML.bind(this));
        $("#total_sum_insured").on(
            "keyup",
            this.calculateTotalSumInsured.bind(this)
        );
        $("#comm_rate").on("keyup", this.calculateCommission.bind(this));
        $("#reins_comm_rate").on(
            "keyup",
            this.calculateReinsCommission.bind(this)
        );
        $("#brokerage_comm_type").on(
            "change",
            this.handleBrokerageCommissionType.bind(this)
        );
    },

    /**
     * Bind engagement form events
     */
    bindEngagementEvents() {
        $("#effective_date").on("change", this.calculateClosingDate.bind(this));
        $("#submitToSalesBtn").on("click", this.handleSubmitToSales.bind(this));
    },

    /**
     * Bind search events
     */
    bindSearchEvents() {
        $(document).on(
            "input",
            '[id^="contact_name-"]',
            this.handleContactSearch.bind(this)
        );
        $(document).on(
            "blur",
            '[id^="contact_name-"]',
            this.handleContactBlur.bind(this)
        );
        $(document).on(
            "click",
            ".fullname-option",
            this.handleContactSelect.bind(this)
        );
        $("#insured_name").on("input", this.handleInsuredNameSearch.bind(this));
        $("#insured_name").on("blur", this.handleInsuredNameBlur.bind(this));
        $(document).on(
            "click",
            ".insured-option",
            this.handleInsuredNameSelect.bind(this)
        );
        $("#lead_name").on("input", this.handleLeadNameSearch.bind(this));
        $("#lead_name").on("blur", this.handleLeadNameBlur.bind(this));

        $(document).on(
            "click",
            ".lead-option",
            this.handleLeadNameSelect.bind(this)
        );
    },

    /**
     * Bind miscellaneous events
     */
    bindMiscEvents() {
        $("#division").on("change", this.handleDivisionChange.bind(this));
        $("#currency_code").on("change", this.handleCurrencyChange.bind(this));
        $("#country").on("change", this.handleCountryChange.bind(this));
    },

    /**
     * Initialize form sections
     */
    initializeFormSections() {
        this.initializeBusinessType();
        this.initializeContactSection();
        this.initializeInsuranceSection();
        this.hideAdvancedSections();
    },

    /**
     * Initialize business type section
     */
    initializeBusinessType() {
        $("#fac_section").hide();
        $("#trt_common").hide();
        $("#treaty_grp").hide();
    },

    /**
     * Initialize contact section
     */
    initializeContactSection() {
        this.state.contactCounter = 0;
    },

    /**
     * Initialize insurance section
     */
    initializeInsuranceSection() {
        $(".eml-div").hide();
        $(".brokerage_comm_amt_div").hide();
        $(".brokerage_comm_rate_div").hide();
    },

    /**
     * Hide advanced sections initially
     */
    hideAdvancedSections() {
        $("#organic_growth_div").hide();
        $(".pq_cost").hide();
    },

    /**
     * Setup form validation
     */
    setupValidation() {
        if (
            typeof jQuery !== "undefined" &&
            typeof jQuery.fn.validate !== "undefined"
        ) {
            $(this.cache.form).validate({
                errorElement: "span",
                errorClass: "error-message",
                highlight: function (element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function (element) {
                    $(element).removeClass("is-invalid");
                },
                submitHandler: this.handleValidatedSubmit.bind(this),
            });
        }
    },

    /**
     * Handle business type change
     */
    handleBusinessTypeChange() {
        const busType = $("#type_of_bus").val();

        if (busType === "FPR" || busType === "FNP") {
            this.showFacultativeSection();
        } else {
            this.hideFacultativeSection();
        }
    },

    /**
     * Show facultative section
     */
    showFacultativeSection() {
        $(
            "#fac_section, #contactDetails, #engagementDetails, #contactDetails, #insuranceDetails"
        ).show();
        // $("#tpr_section, #tnp_section, #trt_common, #treaty_grp").hide();
        this.processSections(".fac_section", ".fac_section_div", "enable");
        this.processSections(
            ".reins_comm_rate",
            ".reins_comm_rate_div",
            "disable"
        );
    },

    /**
     * Hide facultative section
     */
    hideFacultativeSection() {
        $(
            "#fac_section, #contactDetails, #engagementDetails, #contactDetails, #insuranceDetails"
        ).hide();
        this.processSections(".fac_section", ".fac_section_div", "disable");
    },

    /**
     * Process sections enable/disable
     */
    processSections(sectionClass, sectionDivClass, action) {
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
    },

    /**
     * Add new contact
     */
    addContact() {
        const lastContactSection = $(".contactsContainers").last();
        const prevCounter = lastContactSection.data("counter") || 0;

        if (!this.validateContactSection(prevCounter)) {
            return false;
        }

        const counter = prevCounter + 1;
        const contactHtml = this.generateContactHTML(counter);
        console.log(contactHtml);
        $("#contactsContainer").append(contactHtml);
        this.state.contactCounter = counter;
    },

    /**
     * Validate contact section
     */
    validateContactSection(counter) {
        const contactName = $(`#contact_name-${counter}`).val();
        const email = $(`#email-${counter}`).val();
        const phoneNumber = $(`#phone_number-${counter}`).val();

        if (!contactName?.trim()) {
            this.showError("Please capture Contact Name");
            return false;
        }
        if (!email?.trim()) {
            this.showError("Please input Email");
            return false;
        }
        if (!phoneNumber?.trim()) {
            this.showError("Please input Mobile Phone Number");
            return false;
        }

        return true;
    },

    /**
     * Generate contact HTML
     */
    generateContactHTML(counter) {
        return `
            <div class="row contactsContainers" data-counter="${counter}">
                <div class="col-md-3 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="contact_name-${counter}">Contact Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="contact_name[]" id="contact_name-${counter}"
                            class="form-inputs contact_name-${counter}"
                            placeholder="Enter name" required
                            oninput="this.value = this.value.toUpperCase();" />
                        <div id="full_name_results_${counter}" class="dropdown-menu full-name-results" style="display: none; max-width: 500px; width: 100%;"></div>
                        <div class="error-message text-danger" id="full_name_error_${counter}"></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="email-${counter}">Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="email-${counter}" name="email[]"
                            class="form-inputs" required
                            placeholder="Enter email" />
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="phone_number-${counter}">Mobile <span class="text-danger">*</span></label>
                        <input type="tel" id="phone_number-${counter}" name="phone_number[]"
                            class="form-inputs phone" required
                            placeholder="Enter phone number" />
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="telephone-${counter}">Telephone</label>
                        <div class="input-group">
                            <input type="tel" id="telephone-${counter}"
                                class="form-control color-blk"
                                name="telephone[]"
                                placeholder="Enter telephone number" />
                            <button class="btn btn-danger remove-contact" type="button"
                                    id="remove-contact-${counter}" data-counter="${counter}">
                                <i class="bx bx-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },

    /**
     * Remove contact
     */
    removeContact() {
        $(event.target).closest(".contactsContainers").remove();
    },

    /**
     * Handle contact search
     */
    handleContactSearch(e) {
        const query = $(e.target).val().trim();
        const index = $(e.target).attr("id").split("-")[1];
        const resultsContainer = $(`#full_name_results_${index}`);

        if (query.length < 1) {
            resultsContainer.hide();
            return;
        }

        this.performSearch(
            this.config.routes.searchProspectNames,
            { q: query },
            (data) => {
                let results = "";
                if (data.length > 0) {
                    data.forEach((item) => {
                        results += `<div class="dropdown-item fullname-option"
                        data-id="${item.pipeline_id}" data-email="${item.email}"
                        data-phone="${item.phone}" data-telephone="${item.telephone}"
                        data-contact_name="${item.contact_name}" data-index="${index}">
                        ${item.contact_name}
                    </div>`;
                    });
                } else {
                    results =
                        '<div class="dropdown-item">No results found</div>';
                }
                resultsContainer.html(results).show();
            }
        );
    },

    /**
     * Handle contact selection
     */
    handleContactSelect(e) {
        const selectedContact = $(e.target);
        const index = selectedContact.data("index");
        const contactName = selectedContact.data("contact_name");
        const email = selectedContact.data("email");
        const phone = selectedContact.data("phone");
        const telephone = selectedContact.data("telephone");

        $(`#contact_name-${index}`).val(contactName);
        $(`#email-${index}`).val(email);
        $(`#phone_number-${index}`).val(phone);
        $(`#telephone-${index}`).val(telephone);

        $(`#full_name_results_${index}`).hide();
    },

    /**
     * Perform AJAX search
     */
    performSearch(url, data, callback) {
        $.ajax({
            url: url,
            method: "GET",
            data: data,
            success: callback,
            error: () => {
                console.error("Search request failed");
            },
        });
    },

    /**
     * Handle form submission
     */
    handleFormSubmit(e) {
        e.preventDefault();

        if (this.state.isSubmitting) {
            return false;
        }

        this.handleSubmit();
    },

    /**
     * Handle submit button click
     */
    handleSubmit() {
        if (this.state.isSubmitting) {
            return;
        }

        this.showConfirmDialog(
            "Are you sure you want to submit this form?",
            this.submitForm.bind(this)
        );
    },

    /**
     * Handle validated form submission
     */
    handleValidatedSubmit() {
        this.submitForm();
    },

    /**
     * Submit the form
     */
    submitForm() {
        this.state.isSubmitting = true;
        this.showLoading(true);
        this.updateSubmitButton(true);

        const formData = new FormData(this.cache.form);

        $.ajax({
            type: "POST",
            data: formData,
            url: this.config.routes.submit,
            processData: false,
            contentType: false,
            success: this.handleSubmitSuccess.bind(this),
            error: this.handleSubmitError.bind(this),
            complete: () => {
                this.state.isSubmitting = false;
                this.showLoading(false);
                this.updateSubmitButton(false);
            },
        });
    },

    /**
     * Handle successful form submission
     */
    handleSubmitSuccess(response) {
        if (response.status === 1) {
            this.showSuccess(response.message, () => {
                window.location.href = "/leads_listing";
            });
        } else {
            this.displayValidationErrors(response.errors);
        }
    },

    /**
     * Handle form submission error
     */
    handleSubmitError(xhr, textStatus, error) {
        console.error("Form submission error:", error);
        this.showError("An error occurred. Please try again later.");
    },

    /**
     * Display validation errors
     */
    displayValidationErrors(errors) {
        $(".error-message").remove();

        $.each(errors, (field, messages) => {
            const errorElement = $(`#${field}_error`);

            if (errorElement.length) {
                errorElement.html(
                    `<span class="text-danger">${messages[0]}</span>`
                );
            } else {
                const inputField = $(`[name="${field}"]`);
                const errorMessage = `<div class="error-message text-danger">${messages.join(
                    "<br>"
                )}</div>`;
                inputField.after(errorMessage);
            }
        });
    },

    /**
     * Handle cancel button
     */
    handleCancel() {
        window.location.href = "/leads_listing";
    },

    /**
     * Handle submit to sales
     */
    handleSubmitToSales() {
        const prospectId = $("#prospectId").val();

        this.showConfirmDialog(
            "Are you sure you want to add this prospect to Sales Management?",
            () => {
                $.ajax({
                    type: "POST",
                    data: { prospect: prospectId },
                    url: this.config.routes.submitToSales,
                    success: (response) => {
                        if (response.status === 1) {
                            this.showSuccess(response.message, () => {
                                window.location.href = "/leads_listing";
                            });
                        }
                    },
                    error: (xhr, textStatus, error) => {
                        this.showError("Error: " + textStatus);
                    },
                });
            }
        );
    },

    /**
     * Show loading overlay
     */
    showLoading(show) {
        if (this.cache.loadingOverlay) {
            this.cache.loadingOverlay.style.display = show ? "flex" : "none";
        }
    },

    /**
     * Update submit button state
     */
    updateSubmitButton(isSubmitting) {
        if (this.cache.submitBtn) {
            this.cache.submitBtn.disabled = isSubmitting;
            this.cache.submitBtn.innerHTML = isSubmitting
                ? '<i class="bx bx-loader bx-spin"></i> Saving...'
                : '<i class="bx bx-save"></i> Save Details';
        }
    },

    /**
     * Show confirmation dialog
     */
    showConfirmDialog(message, callback) {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Confirmation",
                text: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        } else {
            if (confirm(message)) {
                callback();
            }
        }
    },

    /**
     * Show success message
     */
    showSuccess(message, callback) {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: "success",
                title: "Success",
                text: message,
            }).then(callback);
        } else {
            alert(message);
            if (callback) callback();
        }
    },

    /**
     * Show error message
     */
    showError(message) {
        if (typeof toastr !== "undefined") {
            toastr.error(message);
        } else if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: message,
            });
        } else {
            alert(message);
        }
    },

    /**
     * Utility function to format numbers with commas
     */
    numberWithCommas(x) {
        if (!x) return "";
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },

    /**
     * Utility function to remove commas from numbers
     */
    removeCommas(str) {
        if (!str) return "";
        return str.replace(/,/g, "");
    },

    /**
     * Handle class group change
     */
    handleClassGroupChange() {
        const classGroup = $("#class_group").val();
        $("#classcode").empty();

        if (classGroup) {
            $("#classcode").prop("disabled", false);

            $.ajax({
                url: this.config.routes.getClasses,
                data: { class_group: classGroup },
                type: "get",
                success: (resp) => {
                    $("#classcode").empty();
                    const classes = JSON.parse(resp);

                    $("#classcode").append(
                        $("<option>")
                            .text("-- Select Class Name--")
                            .attr("value", "")
                    );

                    classes.forEach((classItem) => {
                        $("#classcode").append(
                            $("<option>")
                                .text(
                                    `${classItem.class_code} - ${classItem.class_name}`
                                )
                                .attr("value", classItem.class_code)
                        );
                    });

                    $(".section").trigger("chosen:updated");
                },
                error: (resp) => {
                    console.error("Error fetching classes:", resp);
                },
            });
        }
    },

    /**
     * Handle sum insured type change
     */
    handleSumInsuredTypeChange() {
        const labelText = $("#sum_insured_type option:selected").text().trim();
        $("#sum_insured_label").text(` (${labelText})`);
    },

    /**
     * Handle EML change
     */
    handleEMLChange(e) {
        e.preventDefault();
        const applyEML = $(e.target).val();

        $("#eml_rate, #eml_amt").hide();
        $(".eml-div").hide();

        if (applyEML === "Y") {
            $("#eml_rate, #eml_amt").show();
            $(".eml-div").show();
            $("#total_sum_insured").trigger("keyup");
        } else {
            const totalSumInsured = parseFloat(
                this.removeCommas($("#total_sum_insured").val())
            );
            $("#effective_sum_insured").val(
                this.numberWithCommas(totalSumInsured)
            );
        }
    },

    /**
     * Calculate EML
     */
    calculateEML() {
        const emlRate = $("#eml_rate").val();
        const totalSumInsured = parseFloat(
            this.removeCommas($("#total_sum_insured").val())
        );
        const emlAmt = totalSumInsured * (emlRate / 100);

        $("#eml_amt").val(this.numberWithCommas(emlAmt));
        $("#effective_sum_insured").val(this.numberWithCommas(emlAmt));
    },

    /**
     * Calculate total sum insured
     */
    calculateTotalSumInsured() {
        const totalSumInsured = this.removeCommas(
            $("#total_sum_insured").val()
        );
        let effectiveSumInsured = totalSumInsured;

        const emlRate = $("#eml_rate").val();
        const applyEml = $("#apply_eml").val();

        if (emlRate && applyEml === "Y" && totalSumInsured) {
            const emlAmt =
                parseFloat(totalSumInsured) * (parseFloat(emlRate) / 100);
            effectiveSumInsured = emlAmt;
            $("#eml_amt").val(this.numberWithCommas(emlAmt));
        }

        $("#effective_sum_insured").val(
            this.numberWithCommas(effectiveSumInsured)
        );
    },

    /**
     * Calculate commission
     */
    calculateCommission() {
        const rate = $("#comm_rate").val() || 0;
        const cedePremium =
            parseFloat(this.removeCommas($("#cede_premium").val())) || 0;
        const commAmount = (rate / 100) * cedePremium;

        $("#comm_amt").val(this.numberWithCommas(commAmount));
        this.calculateBrokerageCommRate();
    },

    /**
     * Calculate reinsurance commission
     */
    calculateReinsCommission() {
        const rate = $("#reins_comm_rate").val() || 0;
        const reinPremium =
            parseFloat(this.removeCommas($("#rein_premium").val())) || 0;
        const commAmount = (rate / 100) * reinPremium;

        $("#reins_comm_amt").val(this.numberWithCommas(commAmount));
        this.calculateBrokerageCommRate();
    },

    /**
     * Calculate brokerage commission rate
     */
    calculateBrokerageCommRate() {
        const cedantCommRate = this.removeCommas($("#comm_rate").val());
        const reinCommRate = this.removeCommas($("#reins_comm_rate").val());
        const cedantPremium = this.removeCommas($("#cede_premium").val());
        let brokerageCommRate = 0;
        let brokerageCommAmt = 0;

        if (cedantCommRate && reinCommRate) {
            brokerageCommRate =
                parseFloat(reinCommRate) - parseFloat(cedantCommRate);
        }

        brokerageCommAmt =
            parseFloat(cedantPremium) * (parseFloat(brokerageCommRate) / 100);

        $("#brokerage_comm_rate").val(brokerageCommRate);
        $("#brokerage_comm_rate_amt").val(
            this.numberWithCommas(brokerageCommAmt)
        );
    },

    /**
     * Handle brokerage commission type
     */
    handleBrokerageCommissionType() {
        const brokerageCommType = $("#brokerage_comm_type").val();

        // Hide all elements first
        $(
            ".brokerage_comm_amt_div, .brokerage_comm_rate_div, .brokerage_comm_rate_amt_div"
        ).hide();
        $(
            "#brokerage_comm_amt, #brokerage_comm_rate, #brokerage_comm_rate_amt"
        ).hide();
        $(
            "#brokerage_comm_rate_label, #brokerage_comm_rate_amount_label"
        ).hide();

        // Reset values
        $(
            "#brokerage_comm_rate, #brokerage_comm_amt, #brokerage_comm_rate_amt"
        ).val("");

        if (brokerageCommType === "R") {
            $(".brokerage_comm_rate_div, .brokerage_comm_rate_amt_div").show();
            $("#brokerage_comm_rate, #brokerage_comm_rate_amt").show();
            $(
                "#brokerage_comm_rate_label, #brokerage_comm_rate_amount_label"
            ).show();
            this.calculateBrokerageCommRate();
        } else if (brokerageCommType === "A") {
            $(".brokerage_comm_amt_div").show();
            $("#brokerage_comm_amt").show().prop("disabled", false);
        }
    },

    /**
     * Calculate closing date
     */
    calculateClosingDate() {
        const effectiveDate = $("#effective_date").val();

        if (effectiveDate) {
            const date = new Date(effectiveDate);
            date.setFullYear(date.getFullYear() + 1);
            date.setDate(date.getDate() - 1);
            const closingDate = date.toISOString().split("T")[0];
            $("#closing_date").val(closingDate);
        }
    },

    /**
     * Handle insured name search
     */
    handleInsuredNameSearch() {
        const query = $("#insured_name").val().trim();

        if (query.length < 1) {
            $("#insured_name_results").hide();
            return;
        }

        this.performSearch(
            this.config.routes.searchInsuredNames,
            { q: query },
            (data) => {
                let results = "";
                if (data.length > 0) {
                    data.forEach((item) => {
                        results += `<div class="dropdown-item insured-option" data-id="${item.pipeline_id}">${item.insured_name}</div>`;
                    });
                } else {
                    const error =
                        '<div class="dropdown-item">No results found</div>';
                    $("#insured_name_error").html(error).show();
                }
                $("#insured_name_results").html(results).show();
            }
        );
    },

    /**
     * Handle insured name selection
     */
    handleInsuredNameSelect(e) {
        const selectedName = $(e.target).text();

        $("#insured_name").val(selectedName);
        $("#insured_name_results").hide();
    },

    /**
     * Handle lead name search
     */
    handleLeadNameSearch() {
        const query = $("#lead_name").val().trim();

        if (query.length < 1) {
            $("#lead_name_results").hide();
            return;
        }

        this.performSearch(
            this.config.routes.searchLeadNames,
            { q: query },
            (data) => {
                let results = "";
                if (data.length > 0) {
                    data.forEach((item) => {
                        results += `<div class="dropdown-item lead-option" data-id="${item.pipeline_id}">${item.lead_name}</div>`;
                    });
                } else {
                    const error =
                        '<div class="dropdown-item">No results found</div>';
                    $("#lead_name_error").html(error).show();
                }
                $("#lead_name_results").html(results).show();
            }
        );
    },

    handleLeadNameBlur() {
        const query = $("#lead_name").val().trim();
        if (query) {
            $("#lead_name_error").html("").hide();
        }
    },

    handleInsuredNameBlur() {
        const query = $("#insured_name").val().trim();
        if (query) {
            $("#insured_name_error").html("").hide();
        }
    },

    handleContactBlur(e) {
        const query = $(e.target).val().trim();
        const index = $(e.target).attr("id").split("-")[1];
        const resultsContainer = $(`#full_name_results_${index}`);
    },

    /**
     * Handle lead name selection
     */
    handleLeadNameSelect(e) {
        $("#lead_name").val($(e.target).text());
        $("#lead_name_results").hide();
    },

    /**
     * Handle division change
     */
    handleDivisionChange() {
        const division = parseInt($("#division").val());

        $("#premium").trigger("change");

        if (division === 6) {
            $("#narration").attr("req", "required");
        } else {
            $("#narration").attr("req", "");
        }

        $.ajax({
            type: "GET",
            data: { division: division },
            url: this.config.routes.getDivisionClasses,
            success: (resp) => {
                if (resp.status === 1) {
                    $("#insurance_class").empty();
                    $("#insurance_class").append(
                        $("<option />").val("").text("Select class")
                    );

                    resp.classes.forEach((classItem) => {
                        $("#insurance_class").append(
                            $("<option />")
                                .val(classItem.id)
                                .text(classItem.class_name)
                        );
                    });
                }
            },
            error: (resp) => {
                console.error("Error fetching division classes:", resp);
            },
        });
    },

    /**
     * Handle currency change
     */
    handleCurrencyChange() {
        var currency_code = $("select#currency_code option:selected").attr(
            "value"
        );

        $.ajax({
            type: "GET",
            data: { currency_code: currency_code },
            url: this.config.routes.getTodaysRate,
            success: (resp) => {
                var status = $.parseJSON(resp);

                if (status.valid == 2) {
                    $("#today_currency").val(1);
                    $("#today_currency").prop("readonly", true);
                } else if (status.valid == 1) {
                    $("#today_currency").val(status.rate);
                    $("#today_currency").prop("readonly", true);
                } else {
                    $("#today_currency").prop("readonly", true);
                    $("#today_currency").val("");

                    Swal.fire({
                        icon: "warning",
                        title: "Currency Rate Not Set",
                        text: "Currency rate for the day not yet set",
                        confirmButtonText: "OK",
                    });
                }
            },
            error: (resp) => {
                console.error("Error fetching currency rate:", resp);

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to fetch currency rate. Please try again.",
                    confirmButtonText: "OK",
                });
            },
        });
    },

    /**
     * Handle country change
     */
    handleCountryChange() {},

    /**
     * Debounce function for search inputs
     */
    debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func(...args);
        };
    },

    /**
     * Validate form before submission
     */
    validateForm() {
        let isValid = true;
        const requiredFields = this.cache.form.querySelectorAll("[required]");

        requiredFields.forEach((field) => {
            if (!field.value.trim()) {
                field.classList.add("is-invalid");
                isValid = false;
            } else {
                field.classList.remove("is-invalid");
            }
        });

        return isValid;
    },

    /**
     * Reset form to initial state
     */
    resetForm() {
        this.cache.form.reset();
        $(".error-message").remove();
        $(".is-invalid").removeClass("is-invalid");
        this.state = {
            ...this.state,
            contactCounter: 0,
            layerCounter: 0,
            commCounter: 0,
            isSubmitting: false,
            formValidated: false,
        };
    },

    /**
     * Auto-save form data to localStorage
     */
    autoSave() {
        if (typeof Storage !== "undefined") {
            const formData = new FormData(this.cache.form);
            const dataObj = {};

            for (let [key, value] of formData.entries()) {
                dataObj[key] = value;
            }

            localStorage.setItem("prospect_form_data", JSON.stringify(dataObj));
        }
    },

    /**
     * Load saved form data from localStorage
     */
    loadSavedData() {
        if (typeof Storage !== "undefined") {
            const savedData = localStorage.getItem("prospect_form_data");

            if (savedData) {
                try {
                    const dataObj = JSON.parse(savedData);

                    Object.keys(dataObj).forEach((key) => {
                        const element = this.cache.form.querySelector(
                            `[name="${key}"]`
                        );
                        if (element) {
                            element.value = dataObj[key];
                        }
                    });
                } catch (e) {
                    console.error("Error loading saved data:", e);
                }
            }
        }
    },

    /**
     * Clear saved form data
     */
    clearSavedData() {
        if (typeof Storage !== "undefined") {
            localStorage.removeItem("prospect_form_data");
        }
    },
};

// Make sure the module is available globally
window.ProspectOnboarding = ProspectOnboarding;
