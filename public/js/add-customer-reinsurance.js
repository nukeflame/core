// @pk305
"use strict";

(function ($) {
    const CUSTOMER_TYPE_CONFIG = {
        reinsurer: {
            label: "Reinsurer",
            icon: "bi-shield-check",
            color: "danger",
            sections: {
                essential: {
                    visible: true,
                    fields: {
                        partnerName: {
                            required: true,
                            label: "Reinsurer Name",
                            placeholder: "e.g., Global Re Insurance Company",
                            helpText:
                                "Enter the official registered name of the reinsurer",
                        },
                        customerType: {
                            multipleAllowed: false,
                        },
                        email: {
                            required: true,
                            label: "Official Email",
                        },
                        telephone: {
                            required: true,
                            label: "Main Contact Number",
                        },
                    },
                },
                legal: {
                    visible: true,
                    collapsible: false,
                    fields: {
                        incorporationNo: {
                            required: true,
                            label: "Certificate of Incorporation/Registration Number",
                            placeholder: "e.g., PVT-XXXXXX",
                        },
                        taxNo: {
                            required: false,
                            label: "Tax Identification Number (Optional)",
                            placeholder: "e.g., AXXXXXXXXX",
                        },
                        identityType: {
                            visible: false,
                            required: false,
                        },
                        identityNo: {
                            visible: false,
                            required: false,
                        },
                        website: {
                            required: false,
                            label: "Company Website (Optional)",
                            visible: true,
                        },
                    },
                    additionalFields: {
                        securityRating: {
                            required: false,
                            label: "Security/Financial Rating (Optional)",
                            type: "select",
                            options: [
                                "AAA",
                                "AA",
                                "A",
                                "A-",
                                "A+",
                                "BBB",
                                "BB",
                                "B",
                                "B-",
                                "B+",
                                "CCC",
                                "C",
                                "NR",
                                "Not Rated",
                            ],
                            placeholder: "Select rating...",
                            position: "after-taxNo",
                            helpText:
                                "Credit rating or financial strength assessment",
                        },
                        ratingAgency: {
                            required: false,
                            label: "Rating Agency (Optional)",
                            type: "select",
                            options: [
                                "S&P (Standard & Poor's)",
                                "AM Best",
                                "Moody's",
                                "Fitch Ratings",
                                "Other",
                            ],
                            placeholder: "Select rating agency...",
                            position: "after-securityRating",
                        },
                        ratingDate: {
                            required: false,
                            label: "Last Assessment Date (Optional)",
                            type: "date",
                            placeholder: "Select date",
                            position: "after-ratingAgency",
                            helpText: "Date of the last rating assessment",
                        },
                        amlDetails: {
                            required: false,
                            label: "AML Details",
                            type: "text",
                            placeholder: "Enter AML/KYC compliance details",
                            position: "after-ratingDate",
                            helpText:
                                "Anti-Money Laundering and KYC compliance information",
                        },
                    },
                },
                address: {
                    visible: true,
                    fields: {
                        country: {
                            required: true,
                            label: "Country of Incorporation",
                        },
                        street: {
                            required: true,
                            label: "Registered Office Address",
                        },
                        city: {
                            required: true,
                        },
                        postalCode: {
                            required: true,
                        },
                    },
                },
                financial: {
                    visible: false,
                    required: false,
                },
                contacts: {
                    visible: true,
                    minContacts: 1,
                    maxContacts: 8,
                    suggestedDepartments: [
                        "underwriting",
                        "claims",
                        "finance",
                        "technical",
                    ],
                    helpText: "",
                },
            },
        },

        cedant: {
            label: "Cedant (Ceding Company)",
            icon: "bi-building",
            color: "primary",
            sections: {
                essential: {
                    visible: true,
                    fields: {
                        partnerName: {
                            required: true,
                            label: "Cedant Company Name",
                            placeholder: "e.g., ABC Insurance Company Ltd",
                            helpText:
                                "Enter the official name of the ceding company",
                        },
                        customerType: {
                            multipleAllowed: false,
                        },
                        email: {
                            required: true,
                            label: "Official Email",
                        },
                        telephone: {
                            required: true,
                            label: "Main Contact Number",
                        },
                    },
                },
                legal: {
                    visible: true,
                    collapsible: false,
                    fields: {
                        incorporationNo: {
                            required: true,
                            label: "Certificate of Incorporation Number",
                            placeholder: "e.g., PVT-XXXXXX",
                        },
                        taxNo: {
                            required: true,
                            label: "Tax Identification Number (PIN)",
                            placeholder: "e.g., AXXXXXXXXX",
                        },
                        identityType: {
                            visible: false,
                            required: false,
                        },
                        identityNo: {
                            visible: false,
                            required: false,
                        },
                        website: {
                            required: false,
                            label: "Company Website (Optional)",
                            visible: true,
                        },
                    },
                    additionalFields: {
                        regulatorLicenseNo: {
                            required: true,
                            label: "Regulator License No (IRA License)",
                            type: "text",
                            placeholder: "e.g., IRA-xxxxx",
                            position: "after-taxNo",
                            helpText:
                                "Insurance Regulatory Authority license number",
                        },
                        licensingTerritory: {
                            required: true,
                            label: "Licensing Territory",
                            type: "select",
                            placeholder: "Select licensing territory...",
                            position: "after-regulatorLicenseNo",
                            helpText: "Country where the license is valid",
                        },
                        amlDetails: {
                            required: false,
                            label: "AML Details",
                            type: "text",
                            placeholder: "Enter AML/KYC compliance details",
                            position: "after-licensingTerritory",
                            helpText:
                                "For financial compliance - Anti-Money Laundering information",
                        },
                    },
                },
                address: {
                    visible: true,
                    fields: {
                        country: {
                            required: true,
                            label: "Country of Incorporation",
                        },
                        street: {
                            required: true,
                            label: "Registered Office Address",
                        },
                        city: {
                            required: true,
                        },
                        postalCode: {
                            required: true,
                        },
                    },
                },
                financial: {
                    visible: false,
                    required: false,
                },
                contacts: {
                    visible: true,
                    minContacts: 1,
                    maxContacts: 8,
                    suggestedDepartments: [
                        "executive",
                        "underwriting",
                        "claims",
                        "finance",
                    ],
                    helpText: "Add key contact persons at the cedant company",
                },
            },
        },

        reinsurance_broker: {
            label: "Reinsurance Broker",
            icon: "bi-briefcase",
            color: "info",
            sections: {
                essential: {
                    visible: true,
                    fields: {
                        partnerName: {
                            required: true,
                            label: "Reinsurance Broker Firm Name",
                            placeholder: "e.g., XYZ Reinsurance Brokers Ltd",
                            helpText:
                                "Enter the official name of the reinsurance broker firm",
                        },
                        customerType: {
                            multipleAllowed: false,
                        },
                        email: {
                            required: true,
                            label: "Official Email",
                        },
                        telephone: {
                            required: true,
                            label: "Main Contact Number",
                        },
                    },
                },
                legal: {
                    visible: true,
                    collapsible: false,
                    fields: {
                        incorporationNo: {
                            required: true,
                            label: "Certificate of Incorporation Number",
                            placeholder: "e.g., PVT-XXXXXX",
                        },
                        taxNo: {
                            required: true,
                            label: "Tax Identification Number (PIN)",
                            placeholder: "e.g., AXXXXXXXXX",
                        },
                        identityType: {
                            visible: false,
                            required: false,
                        },
                        identityNo: {
                            visible: false,
                            required: false,
                        },
                        website: {
                            required: false,
                            label: "Company Website (Optional)",
                            visible: true,
                        },
                    },
                    additionalFields: {
                        regulatorLicenseNo: {
                            required: true,
                            label: "Regulator License No (IRA License)",
                            type: "text",
                            placeholder: "e.g., IRA-BROKER-xxxxx",
                            position: "after-taxNo",
                            helpText:
                                "Insurance Regulatory Authority broker license number",
                        },
                        licensingAuthority: {
                            required: true,
                            label: "Licensing Authority",
                            type: "text",
                            placeholder:
                                "e.g., Insurance Regulatory Authority (IRA)",
                            position: "after-regulatorLicenseNo",
                            helpText:
                                "Name of the regulatory body that issued the license",
                        },
                        licensingTerritory: {
                            required: true,
                            label: "Licensing Territory",
                            type: "select",
                            placeholder: "Select licensing territory...",
                            position: "after-licensingAuthority",
                            helpText: "Country where the license is valid",
                        },
                        amlDetails: {
                            required: false,
                            label: "AML Details",
                            type: "text",
                            placeholder: "Enter AML/KYC compliance details",
                            position: "after-licensingTerritory",
                            helpText:
                                "For financial compliance - Anti-Money Laundering information",
                        },
                    },
                },
                address: {
                    visible: true,
                    fields: {
                        country: {
                            required: true,
                            label: "Country of Incorporation",
                        },
                        street: {
                            required: true,
                            label: "Business Office Address",
                        },
                        city: {
                            required: true,
                        },
                        postalCode: {
                            required: true,
                        },
                    },
                },
                financial: {
                    visible: false,
                    required: false,
                },
                contacts: {
                    visible: true,
                    minContacts: 2,
                    maxContacts: 10,
                    requiredRoles: ["Principal Broker", "Operations Manager"],
                    helpText:
                        "Add key contact persons (minimum 2 required including Principal Broker)",
                },
            },
        },

        insurance_broker: {
            label: "Insurance Broker",
            icon: "bi-briefcase-fill",
            color: "success",
            sections: {
                essential: {
                    visible: true,
                    fields: {
                        partnerName: {
                            required: true,
                            label: "Insurance Broker Firm Name",
                            placeholder: "e.g., ABC Insurance Brokers Ltd",
                            helpText:
                                "Enter the official name of the insurance broker firm",
                        },
                        customerType: {
                            multipleAllowed: false,
                        },
                        email: {
                            required: true,
                            label: "Official Email",
                        },
                        telephone: {
                            required: true,
                            label: "Main Contact Number",
                        },
                    },
                },
                legal: {
                    visible: true,
                    collapsible: false,
                    fields: {
                        incorporationNo: {
                            required: true,
                            label: "Certificate of Incorporation Number",
                            placeholder: "e.g., PVT-XXXXXX",
                        },
                        taxNo: {
                            required: true,
                            label: "Tax Identification Number (PIN)",
                            placeholder: "e.g., AXXXXXXXXX",
                        },
                        identityType: {
                            visible: false,
                            required: false,
                        },
                        identityNo: {
                            visible: false,
                            required: false,
                        },
                        website: {
                            required: false,
                            label: "Company Website (Optional)",
                            visible: true,
                        },
                    },
                    additionalFields: {
                        regulatorLicenseNo: {
                            required: true,
                            label: "Regulator License No (IRA License)",
                            type: "text",
                            placeholder: "e.g., IRA-BROKER-xxxxx",
                            position: "after-taxNo",
                            helpText:
                                "Insurance Regulatory Authority broker license number",
                        },
                        licensingAuthority: {
                            required: true,
                            label: "Licensing Authority",
                            type: "text",
                            placeholder:
                                "e.g., Insurance Regulatory Authority (IRA)",
                            position: "after-regulatorLicenseNo",
                            helpText:
                                "Name of the regulatory body that issued the license",
                        },
                        licensingTerritory: {
                            required: true,
                            label: "Licensing Territory",
                            type: "select",
                            placeholder: "Select licensing territory...",
                            position: "after-licensingAuthority",
                            helpText: "Country where the license is valid",
                        },
                        amlDetails: {
                            required: false,
                            label: "AML Details",
                            type: "text",
                            placeholder: "Enter AML/KYC compliance details",
                            position: "after-licensingTerritory",
                            helpText:
                                "For financial compliance - Anti-Money Laundering information",
                        },
                    },
                },
                address: {
                    visible: true,
                    fields: {
                        country: {
                            required: true,
                            label: "Country of Incorporation",
                        },
                        street: {
                            required: true,
                            label: "Business Office Address",
                        },
                        city: {
                            required: true,
                        },
                        postalCode: {
                            required: true,
                        },
                    },
                },
                financial: {
                    visible: false,
                    required: false,
                },
                contacts: {
                    visible: true,
                    minContacts: 2,
                    maxContacts: 10,
                    requiredRoles: ["Principal Broker", "Operations Manager"],
                    helpText:
                        "Add key contact persons (minimum 2 required including Principal Broker)",
                },
            },
        },

        insured: {
            label: "Insured",
            icon: "bi-person-check",
            color: "warning",
            sections: {
                essential: {
                    visible: true,
                    fields: {
                        partnerName: {
                            required: true,
                            label: "Insured Name (Individual/Corporate)",
                            placeholder: "e.g., John Doe or XYZ Corporation",
                            helpText: "Enter the name of the insured party",
                        },
                        customerType: {
                            multipleAllowed: false,
                        },
                        email: {
                            required: true,
                            label: "Contact Email",
                        },
                        telephone: {
                            required: true,
                            label: "Contact Number",
                        },
                    },
                },
                legal: {
                    visible: true,
                    collapsible: false,
                    fields: {
                        incorporationNo: {
                            required: false,
                            label: "Certificate of Incorporation (For Corporate)",
                            placeholder: "e.g., PVT-XXXXXX (if applicable)",
                        },
                        taxNo: {
                            required: false,
                            label: "Tax Identification Number (Optional)",
                            placeholder: "e.g., AXXXXXXXXX",
                        },
                        identityType: {
                            required: true,
                            label: "Identity Document Type",
                            options: [
                                "National ID",
                                "Passport",
                                "Business Registration",
                                "Other",
                            ],
                        },
                        identityNo: {
                            required: true,
                            label: "Identity Document Number",
                            placeholder: "Enter ID/Registration number",
                        },
                        website: {
                            required: false,
                            label: "Website (Optional)",
                            visible: true,
                        },
                    },
                    additionalFields: {
                        insuredType: {
                            required: true,
                            label: "Type",
                            type: "select",
                            options: ["Individual", "Corporate"],
                            placeholder: "Select type...",
                            position: "before-incorporationNo",
                            helpText:
                                "Specify whether this is an individual or corporate insured",
                        },
                        industryOccupation: {
                            required: true,
                            label: "Industry/Occupation",
                            type: "text",
                            placeholder:
                                "e.g., Manufacturing, Healthcare, IT Professional",
                            position: "after-insuredType",
                            helpText:
                                "For corporate: Industry sector | For individual: Occupation",
                        },
                        dateOfBirthIncorporation: {
                            required: true,
                            label: "Date of Birth/Incorporation",
                            type: "date",
                            placeholder: "Select date",
                            position: "after-industryOccupation",
                            helpText:
                                "Date of birth for individuals or date of incorporation for corporates",
                        },
                        amlDetails: {
                            required: false,
                            label: "AML Details",
                            type: "text",
                            placeholder: "Enter AML/KYC compliance details",
                            position: "after-identityNo",
                            helpText:
                                "For financial compliance - Anti-Money Laundering information",
                        },
                    },
                },
                address: {
                    visible: true,
                    fields: {
                        country: {
                            required: true,
                            label: "Country (Location)",
                        },
                        street: {
                            required: true,
                            label: "Physical/Registered Address",
                        },
                        city: {
                            required: true,
                        },
                        postalCode: {
                            required: true,
                        },
                    },
                },
                financial: {
                    visible: false,
                    required: false,
                },
                contacts: {
                    visible: true,
                    minContacts: 1,
                    maxContacts: 5,
                    helpText: "Add contact persons for the insured party",
                },
            },
        },

        default: {
            label: "Other Customer Type",
            icon: "bi-question-circle",
            color: "secondary",
            sections: {
                essential: { visible: true },
                legal: {
                    visible: true,
                    fields: {
                        incorporationNo: { required: false },
                        taxNo: { required: false },
                        identityType: { required: false },
                        identityNo: { required: false },
                    },
                },
                address: { visible: true },
                financial: { visible: true, required: false },
                contacts: { visible: true, minContacts: 1, maxContacts: 10 },
            },
        },
    };

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function getCustomerTypeConfig(typeSlug) {
        return (
            CUSTOMER_TYPE_CONFIG[typeSlug] || CUSTOMER_TYPE_CONFIG["default"]
        );
    }

    function getMergedCustomerTypeConfig(typeSlugs) {
        if (!typeSlugs || typeSlugs.length === 0) {
            return CUSTOMER_TYPE_CONFIG["default"];
        }

        if (typeSlugs.length === 1) {
            return getCustomerTypeConfig(typeSlugs[0]);
        }

        const merged = {
            label: "Multiple Types",
            icon: "bi-layers",
            color: "primary",
            sections: {},
        };

        const configs = typeSlugs.map((slug) => getCustomerTypeConfig(slug));

        ["essential", "legal", "address", "financial", "contacts"].forEach(
            (section) => {
                merged.sections[section] = {
                    visible: configs.some((c) => c.sections[section]?.visible),
                    required: configs.some(
                        (c) => c.sections[section]?.required,
                    ),
                    fields: {},
                };

                configs.forEach((config) => {
                    if (config.sections[section]?.fields) {
                        Object.keys(config.sections[section].fields).forEach(
                            (fieldName) => {
                                const fieldConfig =
                                    config.sections[section].fields[fieldName];

                                if (
                                    !merged.sections[section].fields[fieldName]
                                ) {
                                    merged.sections[section].fields[fieldName] =
                                        { ...fieldConfig };
                                } else {
                                    if (fieldConfig.required) {
                                        merged.sections[section].fields[
                                            fieldName
                                        ].required = true;
                                    }
                                    if (fieldConfig.visible !== false) {
                                        merged.sections[section].fields[
                                            fieldName
                                        ].visible = true;
                                    }
                                }
                            },
                        );
                    }
                });

                if (merged.sections[section].visible) {
                    const allAdditionalFields = {};
                    configs.forEach((config) => {
                        if (config.sections[section]?.additionalFields) {
                            Object.assign(
                                allAdditionalFields,
                                config.sections[section].additionalFields,
                            );
                        }
                    });
                    if (Object.keys(allAdditionalFields).length > 0) {
                        merged.sections[section].additionalFields =
                            allAdditionalFields;
                    }
                }
            },
        );

        return merged;
    }

    function announceToScreenReader(message) {
        const $announcement = $("<div>")
            .attr({
                role: "status",
                "aria-live": "polite",
                "aria-atomic": "true",
            })
            .addClass("visually-hidden")
            .text(message);

        $("body").append($announcement);

        setTimeout(() => {
            $announcement.remove();
        }, 1000);
    }

    const FormManager = {
        currentConfig: null,
        currentTypes: [],
        fieldStates: {},
        $form: null,
        validator: null,
        isInitialized: false,
        typeMapping: {},
        dynamicFields: new Set(),
        contactCounter: 1,
        isSubmitting: false,
        initialDynamicValues: {},
        ajaxConfig: {
            url: null,
            method: "POST",
            dataType: "json",
            timeout: 30000,
            redirectOnSuccess: true,
            redirectUrl: null,
            showSuccessMessage: true,
            showErrorMessage: true,
            resetFormOnSuccess: false,
            scrollToTopOnError: true,
        },

        init() {
            try {
                this.$form = $("#customerForm");

                if (this.$form.length === 0) {
                    throw new Error("Customer form not found");
                }

                this.waitForDependencies()
                    .then(() => {
                        this.loadInitialDynamicValues();
                        this.loadTypeMapping();
                        this.cacheOriginalStates();
                        this.attachEventListeners();
                        this.initializeValidator();
                        this.initializeContactManager();
                        this.loadInitialState();
                        this.initializeAjaxConfig();
                        this.isInitialized = true;
                    })
                    .catch((error) => {
                        console.error(
                            "Failed to initialize Form Manager:",
                            error,
                        );
                    });
            } catch (error) {
                console.error("Initialization error:", error);
            }
        },

        loadInitialDynamicValues() {
            try {
                const raw = this.$form.attr("data-dynamic-values");
                this.initialDynamicValues = raw ? JSON.parse(raw) : {};
            } catch (error) {
                this.initialDynamicValues = {};
            }
        },

        getInitialDynamicValue(fieldName) {
            const value = this.initialDynamicValues?.[fieldName];
            if (value === undefined || value === null) {
                return "";
            }

            let normalized = String(value).trim();
            if (fieldName === "ratingDate") {
                if (/^\d{4}-\d{2}-\d{2}$/.test(normalized)) {
                    return normalized;
                }

                const dateOnly = normalized.split(" ")[0].split("T")[0];
                if (/^\d{4}-\d{2}-\d{2}$/.test(dateOnly)) {
                    return dateOnly;
                }
            }

            return normalized;
        },

        escapeHtml(value) {
            return String(value ?? "")
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#39;");
        },

        getCountryOptions() {
            const options = [];
            $("#country option[value!='']").each(function () {
                options.push({
                    value: String($(this).val() || "").trim(),
                    label: String($(this).text() || "").trim(),
                });
            });
            return options;
        },

        initializeAjaxConfig() {
            this.ajaxConfig.url =
                this.$form.attr("action") || window.location.href;

            this.ajaxConfig.method =
                this.$form.attr("method")?.toUpperCase() || "POST";

            const $form = this.$form;

            if ($form.data("redirect-url")) {
                this.ajaxConfig.redirectUrl = $form.data("redirect-url");
            }

            if ($form.data("redirect-on-success") !== undefined) {
                this.ajaxConfig.redirectOnSuccess = $form.data(
                    "redirect-on-success",
                );
            }

            if ($form.data("reset-on-success") !== undefined) {
                this.ajaxConfig.resetFormOnSuccess =
                    $form.data("reset-on-success");
            }
        },

        waitForDependencies() {
            return new Promise((resolve, reject) => {
                let attempts = 0;
                const maxAttempts = 50;

                const checkDependencies = () => {
                    attempts++;

                    const hasJQuery = typeof $ !== "undefined";
                    const hasSelect2 = $.fn.select2 !== undefined;
                    const hasValidation = $.fn.validate !== undefined;

                    if (hasJQuery && hasSelect2 && hasValidation) {
                        resolve();
                    } else if (attempts >= maxAttempts) {
                        reject(
                            new Error(
                                "Dependencies timeout: jQuery=" +
                                    hasJQuery +
                                    ", Select2=" +
                                    hasSelect2 +
                                    ", Validation=" +
                                    hasValidation,
                            ),
                        );
                    } else {
                        setTimeout(checkDependencies, 100);
                    }
                };

                checkDependencies();
            });
        },

        loadTypeMapping() {
            try {
                const $customerType = $("#customerType");
                const $options = $customerType.find('option[value!=""]');

                $options.each((index, option) => {
                    const $option = $(option);
                    const typeId = $option.val();
                    const typeName = $option.text().trim().toLowerCase();

                    let typeSlug = "default";

                    if (
                        typeName.includes("reinsurer") &&
                        !typeName.includes("broker")
                    ) {
                        typeSlug = "reinsurer";
                    } else if (
                        typeName.includes("cedant") ||
                        typeName.includes("ceding")
                    ) {
                        typeSlug = "cedant";
                    } else if (
                        typeName.includes("reinsurance") &&
                        typeName.includes("broker")
                    ) {
                        typeSlug = "reinsurance_broker";
                    } else if (
                        typeName.includes("insurance") &&
                        typeName.includes("broker")
                    ) {
                        typeSlug = "insurance_broker";
                    } else if (typeName.includes("insured")) {
                        typeSlug = "insured";
                    }

                    this.typeMapping[typeId] = typeSlug;
                });
            } catch (error) {
                this.typeMapping = {
                    1: "reinsurer",
                    2: "cedant",
                    3: "reinsurance_broker",
                    4: "insured",
                    5: "insurance_broker",
                };
            }
        },

        cacheOriginalStates() {
            try {
                this.$form
                    .find("input, select, textarea")
                    .each((index, element) => {
                        const $element = $(element);
                        const fieldName = $element.attr("name");

                        if (fieldName && !fieldName.startsWith("contacts[")) {
                            const $container = $element.closest(
                                ".col-12, .col-md-6, .col-lg-4, .col-lg-3",
                            );
                            const $label = $container.find("label").first();

                            this.fieldStates[fieldName] = {
                                visible: $container.is(":visible"),
                                required:
                                    $element.prop("required") ||
                                    $element.hasClass("required"),
                                label: $label
                                    .clone()
                                    .children()
                                    .remove()
                                    .end()
                                    .text()
                                    .trim(),
                                placeholder: $element.attr("placeholder") || "",
                                helpText: $container
                                    .find(".form-text")
                                    .text()
                                    .trim(),
                                container: $container,
                            };
                        }
                    });
            } catch (error) {
                console.error("Failed to cache field states:", error);
            }
        },

        initializeValidator() {
            const self = this;

            try {
                if ($.fn.validate) {
                    this.validator = this.$form.validate({
                        errorClass: "is-invalid",
                        validClass: "",
                        errorElement: "div",
                        ignore: ":hidden:not(.select2-hidden-accessible)",
                        errorPlacement: (error, element) => {
                            error.addClass("invalid-feedback");
                            if (element.parent(".input-group").length) {
                                error.insertAfter(element.parent());
                            } else if (
                                element.hasClass("select2-hidden-accessible")
                            ) {
                                error.insertAfter(
                                    element.next(".select2-container"),
                                );
                            } else {
                                error.insertAfter(element);
                            }
                        },
                        highlight: (element) => {
                            $(element)
                                .addClass("is-invalid")
                                .removeClass("is-valid");
                            if (
                                $(element).hasClass("select2-hidden-accessible")
                            ) {
                                $(element)
                                    .next(".select2-container")
                                    .addClass("is-invalid");
                            }
                        },
                        unhighlight: (element) => {
                            $(element)
                                .removeClass("is-invalid")
                                .addClass("is-valid");
                            if (
                                $(element).hasClass("select2-hidden-accessible")
                            ) {
                                $(element)
                                    .next(".select2-container")
                                    .removeClass("is-invalid");
                            }
                        },
                        submitHandler: (form, event) => {
                            event.preventDefault();
                            this.submitForm();
                            return false;
                        },
                    });

                    const checkNameUrl = this.$form.data("check-name-url");
                    if (checkNameUrl) {
                        const customerId = this.$form.data("customer-id") || "";
                        $("#partnerName").rules("add", {
                            remote: {
                                url: checkNameUrl,
                                type: "GET",
                                data: {
                                    partnerName: function () {
                                        return $("#partnerName").val();
                                    },
                                    customer_id: customerId,
                                },
                                dataFilter: function (response) {
                                    try {
                                        const parsed = JSON.parse(response);
                                        return parsed.valid
                                            ? "true"
                                            : '"Legal/Trading Name already exists."';
                                    } catch (e) {
                                        return "false";
                                    }
                                },
                            },
                        });
                    }
                }
            } catch (error) {
                console.error("Failed to initialize validator:", error);
            }
        },

        validateContacts() {
            const config = this.currentConfig;
            const minContacts = config?.sections?.contacts?.minContacts || 1;
            const currentCount = $(".contact-item").length;

            if (currentCount < minContacts) {
                this.showNotification(
                    "error",
                    `Please add at least ${minContacts} contact(s) for this entity type`,
                    "Validation Error",
                );

                // Scroll to contacts section
                $("html, body").animate(
                    {
                        scrollTop: $("#section-contacts").offset().top - 100,
                    },
                    500,
                );

                return false;
            }

            // Validate required contact fields
            let contactsValid = true;
            $(".contact-item").each(function (index) {
                const $contact = $(this);
                const isPrimary = index === 0;

                if (isPrimary) {
                    const name = $contact.find('input[name*="[name]"]').val();
                    const email = $contact.find('input[name*="[email]"]').val();
                    const mobile = $contact
                        .find('input[name*="[mobile]"]')
                        .val();

                    if (!name || !email || !mobile) {
                        contactsValid = false;
                        $contact.find(".card").addClass("border-danger");
                    } else {
                        $contact.find(".card").removeClass("border-danger");
                    }
                }
            });

            if (!contactsValid) {
                this.showNotification(
                    "error",
                    "Please fill in all required fields for the primary contact",
                    "Validation Error",
                );
                return false;
            }

            return true;
        },

        setLoadingState(isLoading) {
            const $submitBtn = this.$form.find(
                'button[type="submit"], input[type="submit"]',
            );
            const $form = this.$form;

            if (isLoading) {
                $submitBtn.data("original-html", $submitBtn.html());
                $submitBtn.data("original-width", $submitBtn.outerWidth());

                $submitBtn
                    .prop("disabled", true)
                    .css("min-width", $submitBtn.data("original-width") + "px")
                    .html(`
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Saving...
                    `);

                $form
                    .find("input, select, textarea, button")
                    .not($submitBtn)
                    .prop("disabled", true);

                announceToScreenReader("Form is being submitted, please wait");
            } else {
                $submitBtn
                    .prop("disabled", false)
                    .css("min-width", "")
                    .html($submitBtn.data("original-html") || "Submit");

                $form
                    .find("input, select, textarea, button")
                    .prop("disabled", false);

                $form
                    .find("select.select2-hidden-accessible")
                    .each(function () {
                        $(this).prop("disabled", false);
                    });

                $form.find(".form-loading-overlay").fadeOut(200, function () {
                    $(this).remove();
                });

                announceToScreenReader("Form submission complete");
            }
        },

        onBeforeSubmit(xhr) {
            this.clearValidationErrors();
            this.$form.trigger("customerForm:beforeSubmit", [xhr]);
        },

        onSubmitSuccess(response, textStatus, xhr) {
            this.$form.trigger("customerForm:success", [
                response,
                textStatus,
                xhr,
            ]);

            const message =
                response.message ||
                response.success_message ||
                "Customer saved successfully!";
            const redirectUrl =
                response.redirect_url ||
                response.redirect ||
                this.ajaxConfig.redirectUrl;

            if (this.ajaxConfig.showSuccessMessage) {
                this.showNotification("success", message, "Success");
            }

            if (this.ajaxConfig.resetFormOnSuccess) {
                this.$form[0].reset();
                this.resetToDefault();
            }

            if (this.ajaxConfig.redirectOnSuccess && redirectUrl) {
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1500);
            }
        },

        onSubmitError(xhr, textStatus, errorThrown) {
            this.$form.trigger("customerForm:error", [
                xhr,
                textStatus,
                errorThrown,
            ]);

            let errorMessage =
                "An error occurred while saving. Please try again.";
            let fieldErrors = {};

            if (xhr.status === 422) {
                const response = xhr.responseJSON;

                if (response && response.errors) {
                    fieldErrors = response.errors;
                    errorMessage =
                        response.message || "Please correct the errors below.";
                    this.displayValidationErrors(fieldErrors);
                } else if (response && response.message) {
                    errorMessage = response.message;
                }
            } else if (xhr.status === 419) {
                errorMessage =
                    "Your session has expired. Please refresh the page and try again.";
            } else if (xhr.status === 403) {
                errorMessage =
                    "You do not have permission to perform this action.";
            } else if (xhr.status === 404) {
                errorMessage = "The requested resource was not found.";
            } else if (xhr.status === 500) {
                errorMessage =
                    "A server error occurred. Please try again later.";
            } else if (textStatus === "timeout") {
                errorMessage =
                    "The request timed out. Please check your connection and try again.";
            } else if (textStatus === "abort") {
                errorMessage = "The request was cancelled.";
            } else if (xhr.status === 0) {
                errorMessage =
                    "Unable to connect to the server. Please check your internet connection.";
            }

            if (this.ajaxConfig.showErrorMessage) {
                this.showNotification("error", errorMessage, "Error");
            }

            if (
                this.ajaxConfig.scrollToTopOnError &&
                Object.keys(fieldErrors).length > 0
            ) {
                $("html, body").animate(
                    {
                        scrollTop: this.$form.offset().top - 100,
                    },
                    500,
                );
            }
        },

        onSubmitComplete(xhr, textStatus) {
            this.isSubmitting = false;
            this.setLoadingState(false);
            this.$form.trigger("customerForm:complete", [xhr, textStatus]);
        },

        displayValidationErrors(errors) {
            this.clearValidationErrors();

            Object.keys(errors).forEach((fieldName) => {
                const errorMessages = errors[fieldName];
                const errorMessage = Array.isArray(errorMessages)
                    ? errorMessages[0]
                    : errorMessages;

                let $field;

                if (fieldName.includes(".")) {
                    const bracketName =
                        fieldName
                            .replace(/\.(\d+)\./g, "[$1].")
                            .replace(/\./g, "][")
                            .replace("][", "[") +
                        (fieldName.split(".").length > 1 ? "" : "");
                    const convertedName = fieldName
                        .replace(/\.(\d+)\./g, "[$1][")
                        .replace(/\./g, "][");

                    $field = $(
                        `[name="${fieldName}"], [name="${convertedName}"], [name="${bracketName}"]`,
                    );

                    if (
                        $field.length === 0 &&
                        fieldName.startsWith("contacts.")
                    ) {
                        const parts = fieldName.split(".");
                        if (parts.length === 3) {
                            const altName = `contacts[${parts[1]}][${parts[2]}]`;
                            $field = $(`[name="${altName}"]`);
                        }
                    }
                } else {
                    $field = $(
                        `[name="${fieldName}"], [name="${fieldName}[]"]`,
                    );
                }

                if ($field.length > 0) {
                    this.clearFieldValidation($field);
                    $field.addClass("is-invalid");

                    if ($field.hasClass("select2-hidden-accessible")) {
                        $field
                            .next(".select2-container")
                            .addClass("is-invalid");
                    }

                    const $errorDiv = $("<div>")
                        .addClass("invalid-feedback d-block server-feedback")
                        .text(errorMessage);

                    if ($field.hasClass("select2-hidden-accessible")) {
                        $field.next(".select2-container").after($errorDiv);
                    } else if ($field.parent(".input-group").length) {
                        $field.parent(".input-group").after($errorDiv);
                    } else {
                        $field.after($errorDiv);
                    }

                    if (Object.keys(errors).indexOf(fieldName) === 0) {
                        const $container = $field.closest(
                            ".col-12, .col-md-6, .col-lg-4, .col-lg-3, .contact-item",
                        );
                        if ($container.length) {
                            $("html, body").animate(
                                {
                                    scrollTop: $container.offset().top - 100,
                                },
                                500,
                            );
                        }
                    }
                } else {
                    console.warn(`Field not found for error: ${fieldName}`);
                }
            });

            announceToScreenReader(
                `Form has ${Object.keys(errors).length} validation error(s)`,
            );
        },

        clearValidationErrors() {
            this.$form.find(".is-invalid").removeClass("is-invalid");
            this.$form
                .find(".select2-container.is-invalid")
                .removeClass("is-invalid");
            this.$form.find(".invalid-feedback").remove();
        },

        showNotification(type, message, title = "") {
            if (typeof toastr !== "undefined") {
                const options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: type === "error" ? 8000 : 5000,
                    extendedTimeOut: 2000,
                };

                toastr[type](message, title, options);
                return;
            }

            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: type === "error" ? "error" : type,
                    title: title || (type === "success" ? "Success!" : "Error"),
                    text: message,
                    timer: type === "error" ? null : 3000,
                    showConfirmButton: type === "error",
                });
                return;
            }

            const alertClass = type === "error" ? "danger" : type;
            const $alert = $(`
                <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed"
                     style="top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
                     role="alert">
                    ${title ? `<strong>${title}</strong><br>` : ""}
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);

            $("body").append($alert);

            setTimeout(
                () => {
                    $alert.fadeOut(300, function () {
                        $(this).remove();
                    });
                },
                type === "error" ? 8000 : 5000,
            );
        },

        initializeContactManager() {
            const self = this;

            $("#addContactBtn").on("click", function (e) {
                e.preventDefault();
                self.addNewContact();
            });

            $("#contactsContainer").on(
                "click",
                ".remove-contact-btn",
                function (e) {
                    e.preventDefault();
                    const $contactItem = $(this).closest(".contact-item");
                    self.removeContact($contactItem);
                },
            );
        },

        addNewContact() {
            const config = this.currentConfig;
            const maxContacts = config?.sections?.contacts?.maxContacts || 10;
            const currentCount = $(".contact-item").length;

            if (currentCount >= maxContacts) {
                if (typeof toastr !== "undefined") {
                    toastr.warning(
                        `Maximum ${maxContacts} contacts allowed for this entity type`,
                    );
                }
                return;
            }

            const contactIndex = this.contactCounter++;
            const contactHtml = this.generateContactHtml(contactIndex, false);

            $("#contactsContainer").append(contactHtml);

            const $newContact = $(`#contactsContainer .contact-item:last`);
            $newContact.find("select.select2").each(function () {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2({
                        theme: "bootstrap-5",
                        width: "100%",
                        placeholder: $(this).data("placeholder") || "Select...",
                    });
                }
            });

            $newContact.hide().slideDown(300);

            this.updateAddContactButton();

            announceToScreenReader(`Contact ${contactIndex + 1} added`);
        },

        generateContactHtml(index, isPrimary = false) {
            const deleteButton = !isPrimary
                ? `
                <button type="button" class="btn btn-sm btn-danger remove-contact-btn"
                        aria-label="Remove contact">
                    <i class="bi bi-trash me-1"></i>Remove
                </button>
            `
                : "";

            const requiredBadge = isPrimary
                ? '<span class="badge bg-primary">Required</span>'
                : "";

            return `
                <div class="contact-item mb-3" role="listitem" data-contact-index="${index}">
                    <div class="card border-start ${
                        isPrimary ? "border-primary" : "border-secondary"
                    } border-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 contact-title">
                                <i class="bi bi-person-badge me-2"></i>
                                ${
                                    isPrimary
                                        ? "Primary Contact"
                                        : `Contact ${index + 1}`
                                }
                            </h6>
                            <div class="d-flex gap-2 align-items-center">
                                ${requiredBadge}
                                ${deleteButton}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label ${
                                        isPrimary ? "required" : ""
                                    }">Full Name</label>
                                    <input type="text" class="form-control" name="contacts[${index}][name]"
                                           placeholder="John Doe" ${
                                               isPrimary ? "required" : ""
                                           } autocomplete="name">
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label ${
                                        isPrimary ? "required" : ""
                                    }">Position/Title</label>
                                    <input type="text" class="form-control" name="contacts[${index}][position]"
                                           placeholder="e.g., CEO, Manager" ${
                                               isPrimary ? "required" : ""
                                           }
                                           autocomplete="organization-title">
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label ${
                                        isPrimary ? "required" : ""
                                    }">Mobile Number</label>
                                    <input type="tel" class="form-control contact-phone" name="contacts[${index}][mobile]"
                                           placeholder="+254 700 000000" ${
                                               isPrimary ? "required" : ""
                                           } autocomplete="tel">
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label ${
                                        isPrimary ? "required" : ""
                                    }">Email Address</label>
                                    <input type="email" class="form-control" name="contacts[${index}][email]"
                                           placeholder="john@example.com" ${
                                               isPrimary ? "required" : ""
                                           } autocomplete="email">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Department</label>
                                    <select class="form-select select2" name="contacts[${index}][department]">
                                        <option value="">-- Select --</option>
                                        <option value="executive">Executive Management</option>
                                        <option value="underwriting">Underwriting</option>
                                        <option value="claims">Claims</option>
                                        <option value="sales">Sales</option>
                                        <option value="marketing">Marketing</option>
                                        <option value="finance">Finance</option>
                                        <option value="technical">Technical</option>
                                        <option value="operations">Operations</option>
                                        <option value="legal">Legal</option>
                                        <option value="hr">Human Resources</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Primary Contact</label>
                                    <select class="form-select primary-contact-select" name="contacts[${index}][isPrimary]">
                                        <option value="1" ${
                                            isPrimary ? "selected" : ""
                                        }>Yes</option>
                                        <option value="0" ${
                                            !isPrimary ? "selected" : ""
                                        }>No</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Main point of contact for this customer
                                    </small>
                                </div>
                            </div>
                            <input type="hidden" name="contacts[${index}][order]" value="${index}" class="contact-order">
                        </div>
                    </div>
                </div>
            `;
        },

        removeContact($contactItem) {
            const config = this.currentConfig;
            const minContacts = config?.sections?.contacts?.minContacts || 1;
            const currentCount = $(".contact-item").length;

            if (currentCount <= minContacts) {
                if (typeof toastr !== "undefined") {
                    toastr.warning(
                        `Minimum ${minContacts} contact(s) required for this entity type`,
                    );
                }
                return;
            }

            $contactItem.slideUp(300, function () {
                $(this).remove();
                FormManager.updateAddContactButton();
                FormManager.reindexContacts();
            });

            announceToScreenReader("Contact removed");
        },

        reindexContacts() {
            $(".contact-item").each(function (index) {
                const isPrimary = index === 0;
                $(this).find(".contact-title").html(`
                    <i class="bi bi-person-badge me-2"></i>
                    ${isPrimary ? "Primary Contact" : `Contact ${index + 1}`}
                `);
            });
            this.syncContactCounter();
        },

        syncContactCounter() {
            let maxIndex = -1;

            $("#contactsContainer")
                .find('input[name^="contacts["], select[name^="contacts["]')
                .each(function () {
                    const name = $(this).attr("name") || "";
                    const match = name.match(/^contacts\[(\d+)\]/);
                    if (!match) {
                        return;
                    }

                    const index = parseInt(match[1], 10);
                    if (!Number.isNaN(index)) {
                        maxIndex = Math.max(maxIndex, index);
                    }
                });

            this.contactCounter = Math.max(
                maxIndex + 1,
                $(".contact-item").length,
            );
        },

        updateAddContactButton() {
            const config = this.currentConfig;
            const maxContacts = config?.sections?.contacts?.maxContacts || 10;
            const currentCount = $(".contact-item").length;
            const $addBtn = $("#addContactBtn");

            if (currentCount >= maxContacts) {
                $addBtn
                    .prop("disabled", true)
                    .attr("title", `Maximum ${maxContacts} contacts allowed`);
            } else {
                $addBtn
                    .prop("disabled", false)
                    .attr("title", "Add new contact");
            }
        },
        attachEventListeners() {
            try {
                const debouncedTypeChange = debounce((e) => {
                    this.handleTypeChange(e);
                }, 300);

                $("#customerType").on("change", debouncedTypeChange);

                $("#resetFormBtn").on("click", () => {
                    if (
                        confirm(
                            "Are you sure you want to reset the form? All unsaved changes will be lost.",
                        )
                    ) {
                        this.$form[0].reset();
                        this.clearValidationErrors();
                        setTimeout(() => {
                            this.resetToDefault();
                        }, 100);
                    }
                });

                $("#partnerName").on("input", function () {
                    const length = $(this).val().length;
                    $("#partnerNameCount").text(length);

                    if (length > 240) {
                        $("#partnerNameCount").addClass("text-danger");
                    } else {
                        $("#partnerNameCount").removeClass("text-danger");
                    }
                });

                // Clear field-level validation error as user types/selects.
                this.$form.on(
                    "input change focus click",
                    "input, select, textarea",
                    (event) => {
                        this.clearFieldValidation($(event.target));
                    },
                );
            } catch (error) {
                console.error("Failed to attach event listeners:", error);
            }
        },

        clearFieldValidation($field) {
            if (!$field || $field.length === 0) return;

            $field.removeClass("is-invalid");

            if ($field.hasClass("select2-hidden-accessible")) {
                $field.next(".select2-container").removeClass("is-invalid");
                $field
                    .next(".select2-container")
                    .nextAll(".invalid-feedback")
                    .remove();
            } else if ($field.parent(".input-group").length) {
                $field
                    .parent(".input-group")
                    .nextAll(".invalid-feedback")
                    .remove();
            } else {
                $field.siblings(".invalid-feedback").remove();
                $field.nextAll(".invalid-feedback").remove();
            }
        },

        loadInitialState() {
            try {
                this.syncContactCounter();
                const selectedTypes = $("#customerType").val();
                if (selectedTypes && selectedTypes.length > 0) {
                    this.handleTypeChange({ target: $("#customerType")[0] });
                }
            } catch (error) {
                console.error("Failed to load initial state:", error);
            }
        },

        handleTypeChange(e) {
            try {
                const $select = $(e.target);
                const selectedValues = $select.val() || [];

                if (selectedValues.length === 0) {
                    this.resetToDefault();
                    return;
                }

                const selectedTypeSlugs = $("#customerType option:selected")
                    .map(function () {
                        return $(this).data("slug");
                    })
                    .get();

                const mappedSlugs = selectedTypeSlugs
                    .map((x) =>
                        Object.values(this.typeMapping).includes(x) ? x : null,
                    )
                    .filter(Boolean);

                this.currentConfig = getMergedCustomerTypeConfig(mappedSlugs);

                this.currentTypes = mappedSlugs;

                this.applyConfiguration();

                announceToScreenReader(
                    `Form updated for ${this.currentConfig.label}`,
                );
            } catch (error) {
                console.error("Error handling type change:", error);
            }
        },

        applyConfiguration() {
            try {
                const config = this.currentConfig;

                this.applySectionConfig("essential", config.sections.essential);
                this.applySectionConfig("legal", config.sections.legal);
                this.applySectionConfig("address", config.sections.address);
                this.applySectionConfig("financial", config.sections.financial);
                this.applySectionConfig("contacts", config.sections.contacts);
                this.updateEssentialCardTitle(config);

                this.updateValidationRules();

                if (this.validator) {
                    this.$form.valid();
                }
            } catch (error) {
                console.error("Error applying configuration:", error);
            }
        },

        updateEssentialCardTitle(config) {
            const $title = $("#section-essential .card-header h5");
            if ($title.length === 0) return;

            const defaultTitle = "Essential Information";
            const $icon = $title.find("i").first().clone();
            const partnerLabel =
                config?.sections?.essential?.fields?.partnerName?.label;

            const nextTitle = partnerLabel || defaultTitle;
            $title.empty();
            if ($icon.length) {
                $title.append($icon).append(" ");
            }
            $title.append(document.createTextNode(nextTitle));
        },

        applySectionConfig(sectionName, sectionConfig) {
            if (!sectionConfig) return;

            try {
                const $section = $(`#section-${sectionName}`);
                if ($section.length === 0) {
                    console.warn(`Section not found: ${sectionName}`);
                    return;
                }

                if (sectionConfig.visible === false) {
                    $section.slideUp(300, () => {
                        $section
                            .find("input, select, textarea")
                            .prop("required", false)
                            .removeClass("required");
                    });
                    return;
                } else {
                    $section.slideDown(300);
                }

                if (sectionConfig.collapsible === false) {
                    const $collapseDiv = $section.find(".collapse");
                    if ($collapseDiv.length) {
                        $collapseDiv.addClass("show");
                        $section.find(".section-toggle").hide();
                    }
                } else if (sectionConfig.collapsed === false) {
                    const $collapseDiv = $section.find(".collapse");
                    if ($collapseDiv.length && !$collapseDiv.hasClass("show")) {
                        $collapseDiv.collapse("show");
                    }
                }

                if (sectionConfig.fields) {
                    Object.keys(sectionConfig.fields).forEach((fieldName) => {
                        this.applyFieldConfig(
                            fieldName,
                            sectionConfig.fields[fieldName],
                        );
                    });
                }

                if (sectionConfig.additionalFields) {
                    this.addAdditionalFields(
                        sectionName,
                        sectionConfig.additionalFields,
                    );
                }

                if (
                    sectionName === "contacts" &&
                    sectionConfig.minContacts !== undefined
                ) {
                    this.updateContactsConfig(sectionConfig);
                }
            } catch (error) {
                console.error(
                    `Error applying section config: ${sectionName}`,
                    error,
                );
            }
        },

        applyFieldConfig(fieldName, fieldConfig) {
            try {
                const $field = $(
                    `[name="${fieldName}"], [name="${fieldName}[]"]`,
                );
                if ($field.length === 0) {
                    return;
                }

                const $container = $field.closest(
                    ".col-12, .col-md-6, .col-lg-4, .col-lg-3",
                );
                const $label = $container.find("label").first();

                if (fieldConfig.visible === false) {
                    $container.slideUp(200, () => {
                        $field.prop("required", false).removeClass("required");
                        $label.removeClass("required");

                        if (this.validator) {
                            this.validator.element($field);
                        }
                    });
                } else {
                    $container.slideDown(200);

                    if (fieldConfig.required === true) {
                        $field.prop("required", true).addClass("required");
                        $label.addClass("required");
                    } else if (fieldConfig.required === false) {
                        $field.prop("required", false).removeClass("required");
                        $label.removeClass("required");
                    }
                }

                if (fieldConfig.label) {
                    const $labelClone = $label.clone();
                    $labelClone.children().remove();
                    const currentText = $labelClone.text().trim();

                    if (currentText !== fieldConfig.label) {
                        const $icons = $label.find("i").clone();
                        $label.empty().text(fieldConfig.label + " ");
                        $icons.appendTo($label);
                    }
                }

                if (fieldConfig.placeholder) {
                    $field.attr("placeholder", fieldConfig.placeholder);
                }

                if (fieldConfig.helpText) {
                    let $helpText = $container.find(".form-text");
                    if ($helpText.length === 0) {
                        $helpText = $(
                            '<small class="form-text text-muted"></small>',
                        );
                        $field.after($helpText);
                    }
                    $helpText.text(fieldConfig.helpText);
                }

                if (fieldConfig.options && $field.is("select")) {
                    this.updateSelectOptions($field, fieldConfig.options);
                }
            } catch (error) {
                console.error(
                    `Error applying field config: ${fieldName}`,
                    error,
                );
            }
        },

        updateSelectOptions($select, options) {
            try {
                const currentValue = $select.val();
                const $placeholder = $select
                    .find('option[value=""]')
                    .first()
                    .clone();

                $select.empty();

                if ($placeholder.length) {
                    $select.append($placeholder);
                }

                options.forEach((option) => {
                    const $option = $("<option></option>")
                        .val(option)
                        .text(option);
                    $select.append($option);
                });

                if (
                    currentValue &&
                    $select.find(`option[value="${currentValue}"]`).length
                ) {
                    $select.val(currentValue);
                }

                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.trigger("change.select2");
                }
            } catch (error) {
                console.error("Error updating select options:", error);
            }
        },

        addAdditionalFields(sectionName, additionalFields) {
            try {
                const $section = $(`#section-${sectionName}`);
                const $cardBody = $section.find(".card-body").first();
                const $row = $cardBody.find(".row").first();

                Object.keys(additionalFields).forEach((fieldName) => {
                    if ($(`[name="${fieldName}"]`).length > 0) {
                        this.applyFieldConfig(
                            fieldName,
                            additionalFields[fieldName],
                        );
                        return;
                    }

                    const fieldConfig = additionalFields[fieldName];
                    const $fieldHtml = this.generateFieldHtml(
                        fieldName,
                        fieldConfig,
                    );

                    const position = fieldConfig.position || "end";
                    this.insertField($row, $fieldHtml, position);

                    this.dynamicFields.add(fieldName);

                    if (fieldConfig.type === "select") {
                        const $newField = $(`[name="${fieldName}"]`);
                        if (
                            $.fn.select2 &&
                            !$newField.hasClass("select2-hidden-accessible")
                        ) {
                            $newField.select2({
                                theme: "bootstrap-5",
                                width: "100%",
                                placeholder:
                                    fieldConfig.placeholder || "Select...",
                            });
                        }
                    }
                });
            } catch (error) {
                console.error("Error adding additional fields:", error);
            }
        },

        generateFieldHtml(fieldName, config) {
            const colClass = config.colClass || "col-12 col-md-6 col-lg-4";
            const fieldType = config.type || "text";
            const required = config.required ? "required" : "";
            const ariaRequired = config.required ? 'aria-required="true"' : "";
            const labelClass = config.required
                ? "form-label required"
                : "form-label";
            const initialValue = this.getInitialDynamicValue(fieldName);
            const escapedValue = this.escapeHtml(initialValue);

            let inputHtml = "";

            if (fieldType === "select") {
                let options = config.options || [];
                if (
                    fieldName === "licensingTerritory" &&
                    (!Array.isArray(options) || options.length === 0)
                ) {
                    options = this.getCountryOptions();
                }

                let resolvedInitialValue = initialValue;
                if (fieldName === "ratingAgency" && resolvedInitialValue) {
                    const lowerInitial = resolvedInitialValue.toLowerCase();
                    const matched = options.find((opt) => {
                        const optionValue = String(
                            typeof opt === "object" ? opt.value : opt,
                        )
                            .trim()
                            .toLowerCase();
                        return (
                            optionValue === lowerInitial ||
                            optionValue.includes(lowerInitial) ||
                            lowerInitial.includes(optionValue)
                        );
                    });

                    if (matched) {
                        resolvedInitialValue = String(
                            typeof matched === "object"
                                ? matched.value
                                : matched,
                        ).trim();
                    }
                }

                const optionsHtml = options
                    .map((opt) => {
                        const optionValue = String(
                            typeof opt === "object" ? opt.value : opt,
                        ).trim();
                        const optionLabel = String(
                            typeof opt === "object" ? opt.label : opt,
                        ).trim();
                        const selected =
                            optionValue === resolvedInitialValue
                                ? "selected"
                                : "";
                        const escapedOption = this.escapeHtml(optionValue);
                        const escapedLabel = this.escapeHtml(optionLabel);
                        return `<option value="${escapedOption}" ${selected}>${escapedLabel}</option>`;
                    })
                    .join("");

                inputHtml = `
                    <select class="form-select select2-dynamic"
                            id="${fieldName}"
                            name="${fieldName}"
                            ${required}
                            ${ariaRequired}>
                        <option value="">${
                            config.placeholder || "-- Select --"
                        }</option>
                        ${optionsHtml}
                    </select>
                `;
            } else if (fieldType === "date") {
                const minAttr =
                    config.min === "today"
                        ? `min="${new Date().toISOString().split("T")[0]}"`
                        : config.min
                          ? `min="${config.min}"`
                          : "";

                inputHtml = `
                    <input type="date"
                           class="form-control"
                           id="${fieldName}"
                           name="${fieldName}"
                           value="${escapedValue}"
                           ${required}
                           ${ariaRequired}
                           ${minAttr}>
                `;
            } else if (fieldType === "number") {
                inputHtml = `
                    <input type="number"
                           class="form-control"
                           id="${fieldName}"
                           name="${fieldName}"
                           value="${escapedValue}"
                           placeholder="${config.placeholder || ""}"
                           ${required}
                           ${ariaRequired}>
                `;
            } else if (fieldType === "textarea") {
                inputHtml = `
                    <textarea class="form-control"
                              id="${fieldName}"
                              name="${fieldName}"
                              placeholder="${config.placeholder || ""}"
                              rows="${config.rows || 3}"
                              ${required}
                              ${ariaRequired}>${escapedValue}</textarea>
                `;
            } else {
                inputHtml = `
                    <input type="${fieldType}"
                           class="form-control"
                           id="${fieldName}"
                           name="${fieldName}"
                           value="${escapedValue}"
                           placeholder="${config.placeholder || ""}"
                           ${required}
                           ${ariaRequired}>
                `;
            }

            const helpText = config.helpText
                ? `<small class="form-text text-muted">${config.helpText}</small>`
                : "";

            const $field = $(`
                <div class="${colClass} mb-3 dynamic-field" data-field="${fieldName}">
                    <label for="${fieldName}" class="${labelClass}">
                        ${config.label}
                    </label>
                    ${inputHtml}
                    ${helpText}
                </div>
            `);

            return $field;
        },

        insertField($container, $field, position) {
            try {
                $field.hide();

                if (position === "end") {
                    $container.append($field);
                } else if (position.startsWith("after-")) {
                    const afterField = position.replace("after-", "");
                    let $afterField = $(`[name="${afterField}"]`).closest(
                        ".col-12, .col-md-6, .col-lg-4, .col-lg-3",
                    );

                    if ($afterField.length === 0) {
                        $afterField = $(
                            `.dynamic-field[data-field="${afterField}"]`,
                        );
                    }

                    if ($afterField.length) {
                        $afterField.after($field);
                    } else {
                        $container.append($field);
                    }
                } else if (position.startsWith("before-")) {
                    const beforeField = position.replace("before-", "");
                    let $beforeField = $(`[name="${beforeField}"]`).closest(
                        ".col-12, .col-md-6, .col-lg-4, .col-lg-3",
                    );

                    if ($beforeField.length === 0) {
                        $beforeField = $(
                            `.dynamic-field[data-field="${beforeField}"]`,
                        );
                    }

                    if ($beforeField.length) {
                        $beforeField.before($field);
                    } else {
                        $container.prepend($field);
                    }
                }

                $field.slideDown(300);
            } catch (error) {
                console.error("Error inserting field:", error);
            }
        },

        updateContactsConfig(config) {
            try {
                const $section = $("#section-contacts");
                const currentCount = $(".contact-item").length;

                $section.data("minContacts", config.minContacts || 1);
                $section.data("maxContacts", config.maxContacts || 10);

                if (config.helpText) {
                    let $helpAlert = $section.find(".alert-info.contacts-help");
                    if ($helpAlert.length === 0) {
                        $helpAlert = $(`
                            <div class="alert alert-info contacts-help" role="status">
                                <i class="bi bi-info-circle me-2"></i>
                                <span class="help-text">${config.helpText}</span>
                            </div>
                        `);
                        $section.find(".card-body").prepend($helpAlert);
                        $helpAlert.hide().slideDown(300);
                    } else {
                        $helpAlert.find(".help-text").text(config.helpText);
                    }
                }

                if (currentCount < config.minContacts) {
                    if (typeof toastr !== "undefined") {
                        toastr.info(
                            `This entity type requires at least ${config.minContacts} contact(s)`,
                        );
                    }
                }

                this.updateAddContactButton();
            } catch (error) {
                console.error("Error updating contacts config:", error);
            }
        },

        updateValidationRules() {
            try {
                if (!this.validator) return;

                this.$form
                    .find("input, select, textarea")
                    .each((index, element) => {
                        const $element = $(element);
                        const fieldName = $element.attr("name");

                        if (!fieldName || fieldName.startsWith("contacts["))
                            return;

                        $element.rules("remove");

                        if (!$element.is(":visible")) {
                            return;
                        }

                        const rules = {};
                        const messages = {};
                        const isRequired =
                            $element.prop("required") ||
                            $element.hasClass("required");

                        if (isRequired) {
                            rules.required = true;
                            const requiredMessage =
                                this.getRequiredMessage(fieldName);
                            if (requiredMessage) {
                                messages.required = requiredMessage;
                            }
                        }

                        const fieldType = $element.attr("type");
                        if (fieldType === "email") {
                            rules.email = true;
                        } else if (fieldType === "url") {
                            rules.url = true;
                        } else if (fieldType === "date") {
                            const min = $element.attr("min");
                            if (min) {
                                rules.min = min;
                            }
                        }

                        const minlength = $element.attr("minlength");
                        const maxlength = $element.attr("maxlength");
                        if (minlength) rules.minlength = parseInt(minlength);
                        if (maxlength) rules.maxlength = parseInt(maxlength);

                        if (Object.keys(rules).length > 0) {
                            const rulePayload =
                                Object.keys(messages).length > 0
                                    ? { ...rules, messages }
                                    : rules;
                            $element.rules("add", rulePayload);
                        }
                    });
            } catch (error) {
                console.error("Error updating validation rules:", error);
            }
        },

        getRequiredMessage(fieldName) {
            const messageMap = {
                email: "Primary Email Address is required.",
            };

            return messageMap[fieldName] || null;
        },

        resetToDefault() {
            try {
                $(".dynamic-field").slideUp(200, function () {
                    $(this).remove();
                });
                $(".dynamic-help, .contacts-help").slideUp(200, function () {
                    $(this).remove();
                });

                this.dynamicFields.clear();

                $('[id^="section-"]').slideDown(200);

                Object.keys(this.fieldStates).forEach((fieldName) => {
                    const state = this.fieldStates[fieldName];
                    const $field = $(
                        `[name="${fieldName}"], [name="${fieldName}[]"]`,
                    );

                    if ($field.length) {
                        const $container = $field.closest(
                            ".col-12, .col-md-6, .col-lg-4, .col-lg-3",
                        );
                        const $label = $container.find("label").first();

                        if (state.visible) {
                            $container.show();
                        }

                        $field.prop("required", state.required);

                        if (state.required) {
                            $label.addClass("required");
                            $field.addClass("required");
                        } else {
                            $label.removeClass("required");
                            $field.removeClass("required");
                        }

                        $field.removeClass("is-invalid is-valid");
                        $container.find(".invalid-feedback").remove();
                    }
                });

                this.currentConfig = null;
                this.currentTypes = [];
                this.updateEssentialCardTitle(null);

                this.updateValidationRules();
            } catch (error) {
                console.error("Error resetting form:", error);
            }
        },

        getConfigSummary() {
            return {
                types: this.currentTypes,
                config: this.currentConfig,
                visibleSections: this.getVisibleSections(),
                requiredFields: this.getRequiredFields(),
                dynamicFields: Array.from(this.dynamicFields),
                contactCount: $(".contact-item").length,
            };
        },

        getVisibleSections() {
            const visible = [];
            $('[id^="section-"]').each(function () {
                if ($(this).is(":visible")) {
                    visible.push($(this).attr("id").replace("section-", ""));
                }
            });
            return visible;
        },

        getRequiredFields() {
            const required = [];
            $(
                "input[required]:visible, select[required]:visible, textarea[required]:visible",
            ).each(function () {
                const name = $(this).attr("name");
                if (name) {
                    required.push(name);
                }
            });
            return required;
        },
        submitForm() {
            if (this.isSubmitting) return;

            if (this.validator && !this.$form.valid()) {
                return;
            }

            if (!this.validateContacts()) {
                return;
            }

            const csrfToken =
                $('meta[name="csrf-token"]').attr("content") ||
                this.$form.find('input[name="_token"]').val();

            const formData = new FormData(this.$form[0]);
            if (!formData.get("_token") && csrfToken) {
                formData.append("_token", csrfToken);
            }

            this.isSubmitting = true;
            this.setLoadingState(true);
            this.onBeforeSubmit();

            console.log(this.ajaxConfig.url);

            $.ajax({
                url: this.ajaxConfig.url,
                method: this.ajaxConfig.method,
                data: formData,
                processData: false,
                contentType: false,
                dataType: this.ajaxConfig.dataType,
                timeout: this.ajaxConfig.timeout,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                success: (response, textStatus, xhr) => {
                    this.onSubmitSuccess(response, textStatus, xhr);
                },
                error: (xhr, textStatus, errorThrown) => {
                    this.onSubmitError(xhr, textStatus, errorThrown);
                },
                complete: (xhr, textStatus) => {
                    this.onSubmitComplete(xhr, textStatus);
                },
            });
        },

        destroy() {
            try {
                $("#customerType").off("change");
                $("#resetFormBtn").off("click");
                $("#partnerName").off("input");
                $("#addContactBtn").off("click");
                $("#contactsContainer").off("click", ".remove-contact-btn");

                $(".dynamic-field").remove();
                $(".dynamic-help, .contacts-help").remove();

                this.dynamicFields.clear();
                this.currentConfig = null;
                this.currentTypes = [];
                this.isInitialized = false;
                this.isSubmitting = false;
            } catch (error) {
                console.error("Error destroying form manager:", error);
            }
        },
    };

    function initializeBootstrapComponents() {
        try {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]'),
            );
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: "hover focus",
                });
            });
        } catch (error) {
            console.error("Error initializing Bootstrap components:", error);
        }
    }

    $(document).ready(function () {
        try {
            initializeBootstrapComponents();
            FormManager.init();

            window.FormManager = FormManager;
        } catch (error) {
            console.error("Document ready error:", error);
        }
    });

    window.CustomerFormUtils = {
        getConfig: (typeSlug) => getCustomerTypeConfig(typeSlug),
        getMergedConfig: (typeSlugs) => getMergedCustomerTypeConfig(typeSlugs),
        announce: (message) => announceToScreenReader(message),
        getConfigList: () => Object.keys(CUSTOMER_TYPE_CONFIG),
    };
})(jQuery);
