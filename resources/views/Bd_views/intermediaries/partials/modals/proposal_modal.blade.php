<!-- Proposal Stage Modal -->
<div id="proposalModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticPropoalStageLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="proposalForm" action="{{ route('update.opp.status') }}" novalidate>
                <input type="hidden" id="opportunityNegId" name="opportunityNegId" />
                <input type="hidden" id="currentNegStage" name="current_stage" />

                <div class="modal-body fac-slip-container">
                    <div class="fac-slip-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h1 class="slip-title">
                                    <i class="bx bx-shield me-1"></i>Facultative Slip
                                </h1>
                                <p class="slip-subtitle mb-0">Reinsurance Coverage Proposal</p>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-light text-dark fs-6 px-3 py-2">
                                    Slip #: <span class="slip-display"></span>
                                </div>
                                <div class="mt-2 text-light opacity-75">
                                    <small>Created: <span class="created_at-display"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="company-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-2 fw-medium" style="font-size: 19px;">
                                        <i class="bx bx-building me-1"></i>
                                        <span class="insured-name-display"></span>
                                    </h6>
                                    <p class="mb-0 small" style="font-size: 13px;">Insured / Policyholder</p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="mb-0 small">Name: <span class="insured-contact-name-display"></span></p>
                                    <p class="mb-0 small">Contact: <span class="insured-email-display"></span></p>
                                    <p class="mb-0 small">Tel: <span class="insured-phone-display"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card custom-card section-box customScrollBar shadow-none mb-0">
                        <!-- Proposal Details Section -->
                        <div class="form-section">
                            <div class="section-header" data-section="coverage-details">
                                <div class="section-title">
                                    <span>
                                        <i class="bx bx-check section-icon"></i>
                                        Proposal Details
                                    </span>
                                </div>
                            </div>
                            <div class="section-content" id="coverage-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                100% Sum Insured
                                                <span class="sum_insured_type" style="padding-left: 6px;"></span>
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="currency-input">
                                                <div class="currency-symbol" id="currencySymbol">KES</div>
                                                <input type="text" class="form-inputs total_sum_insured"
                                                    name="total_sum_insured" required placeholder="0.00"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    onchange="this.value=numberWithCommas(this.value)" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Premium
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="currency-input">
                                                <div class="currency-symbol">KES</div>
                                                <input type="text" class="form-inputs premium" name="premium"
                                                    required placeholder="0.00"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    onchange="this.value=numberWithCommas(this.value)" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Risk Type</label>
                                            <input type="text" class="form-inputs" name="risk_type" id="riskType"
                                                readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Last Contact Date
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <input type="date" class="form-inputs" name="last_contact_date"
                                                id="lastContactDate" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cedant Information Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-title">
                                    <span>
                                        <i class="bi bi-shield-check section-icon"></i>
                                        Cedant Details
                                    </span>
                                </div>
                            </div>
                            <div class="section-content" id="cedant-info">
                                <div class="cedant-selection-panel mb-2">
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="form-group">
                                                <label class="form-label">Cedant</label>
                                                <small class="form-text form-inputs" id="cedantName"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-success w-100"
                                                    id="addPropReinsurer" style="padding: 2px 0px;">
                                                    <i class="bx bx-plus" style="font-size: 27px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reinsurer Information Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-title">
                                    <span>
                                        <i class="bx bx-disc section-icon"></i>
                                        Reinsurer Placement
                                    </span>
                                </div>
                            </div>
                            <div class="section-content" id="reinsurer-info">
                                <div class="reinsurer-selection-panel mb-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Add Reinsurer</label>
                                                <select class="sel" id="propAvailableReinsurers"
                                                    placeholder="Search and select reinsurer...">
                                                    <option value="">Search and select reinsurer...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Total Written Share (%)</label>
                                                <input type="number" class="form-inputs" id="totalNegReinsurerShare"
                                                    name="total_reinsurer_share" placeholder="0.00" step="0.01"
                                                    min="0.01" max="100" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Share (%)</label>
                                                <input type="number" class="form-inputs" id="reinsurerNegShare"
                                                    placeholder="0.00" step="0.01" min="0.01" max="100">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-success w-100"
                                                    id="addNegReinsurer" style="padding: 2px 0px;">
                                                    <i class="bx bx-plus" style="font-size: 27px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="selected-reinsurers-section">
                                    <h6 class="mb-3">
                                        <i class="bi bi-people-fill me-1"></i>Selected Reinsurers
                                        <span class="badge bg-primary ms-2" id="reinsurerCount">0</span>
                                    </h6>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped selected-reinsurers-table"
                                            id="propReinsurersTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="width: 70%">Reinsurer</th>
                                                    <th style="width: 20%">Written Share (%)</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <!-- Supporting Documents Section -->
                        <div class="form-section">
                            <div class="section-header" data-section="documents">
                                <div class="section-title">
                                    <div>
                                        <i class="bx bx-upload section-icon"></i>
                                        Supporting Documents
                                    </div>
                                    <div id="documentsSubtitle" class="ms-3 fs-12 opacity-75">
                                    </div>
                                </div>
                            </div>
                            <div class="documents-section-content" id="documentsContent">
                                <div id="documentFields" class="row g-4" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div></div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-dark">
                                <i class="bx bx-paper-plane me-1"></i> Send Proposal
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            const VALIDATION_CONFIG = {
                MIN_PERCENTAGE: 0.01,
                MAX_PERCENTAGE: 100,
                MIN_REINSURERS: 1,
                REQUIRED_FIELDS: ["total_sum_insured", "premium", "last_contact_date"],
            };

            const FIELD_VALIDATORS = {
                currency: {
                    pattern: /^\d+(\.\d{1,2})?$/,
                    message: "Please enter a valid currency amount (e.g., 1000.00)",
                },
                percentage: {
                    pattern: /^\d+(\.\d{1,2})?$/,
                    min: VALIDATION_CONFIG.MIN_PERCENTAGE,
                    max: VALIDATION_CONFIG.MAX_PERCENTAGE,
                    message: "Please enter a valid percentage between 0.01 and 100",
                },
                email: {
                    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                    message: "Please enter a valid email address",
                },
            };

            let proposal = [];
            let reinsurers = [];
            let cedantShare = 20;
            let totalPlaced = 80;

            const $modal = $("#proposalModal");
            const $table = $modal.find('#propReinsurersTable');

            $table.DataTable({
                data: [],
                columns: [{
                        data: 'name',
                        title: 'Reinsurer'
                    },
                    {
                        data: 'written_share',
                        title: 'Written Share (%)'
                    },
                    {
                        data: 'action',
                        title: 'Action',
                        orderable: false
                    }
                ],
                paging: false,
                searching: false,
                info: false,
                language: {
                    emptyTable: "No reinsurers have been selected yet."
                }
            });

            $('#propAvailableReinsurers').select2({
                placeholder: 'Search and select reinsurer...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#proposalModal')
            });

            $("#proposalForm").on("input blur", ".form-inputs", function() {
                validateField($(this));
            });

            function validateField($field) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const fieldValue = $field.val().trim();
                const isRequired =
                    $field.prop("required") ||
                    VALIDATION_CONFIG.REQUIRED_FIELDS.includes(fieldName);

                $field.removeClass("is-invalid");
                $field.siblings(".invalid-feedback").remove();
                let isValid = true;
                let errorMessage = "";

                if (isRequired && !fieldValue) {
                    isValid = false;
                    errorMessage = "This field is required";
                } else if (fieldValue) {
                    const validation = getFieldValidation($field);
                    if (validation && !validation.isValid) {
                        isValid = false;
                        errorMessage = validation.message;
                    }
                }

                if (isValid && fieldValue) {
                    $field.addClass("is-v");
                } else if (!isValid) {
                    $field.addClass("is-invalid");
                    $field.after(`<div class="invalid-feedback">${errorMessage}</div>`);
                }

                return isValid;
            }


            function getFieldValidation($field) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const fieldValue = $field.val();
                const numericValue = parseFloat(fieldValue.replace(/,/g, ""));

                // if (
                //     $field.closest(".currency-input").length ||
                //     fieldName.includes("premium") ||
                //     fieldName.includes("sum_insured")
                // ) {
                //     if (
                //         !FIELD_VALIDATORS.currency.pattern.test(fieldValue.replace(/,/g, ""))
                //     ) {
                //         return {
                //             isValid: false,
                //             message: FIELD_VALIDATORS.currency.message,
                //         };
                //     }
                //     if (numericValue <= 0) {
                //         return {
                //             isValid: false,
                //             message: "Amount must be greater than 0",
                //         };
                //     }
                // }

                // if (fieldName.includes("rate") || fieldName.includes("Share")) {
                //     if (!FIELD_VALIDATORS.percentage.pattern.test(fieldValue)) {
                //         return {
                //             isValid: false,
                //             message: FIELD_VALIDATORS.percentage.message,
                //         };
                //     }
                //     if (
                //         numericValue < FIELD_VALIDATORS.percentage.min ||
                //         numericValue > FIELD_VALIDATORS.percentage.max
                //     ) {
                //         return {
                //             isValid: false,
                //             message: `Percentage must be between ${FIELD_VALIDATORS.percentage.min} and ${FIELD_VALIDATORS.percentage.max}`,
                //         };
                //     }
                // }

                // if ($field.attr("type") === "email" || fieldName.includes("email")) {
                //     if (!FIELD_VALIDATORS.email.pattern.test(fieldValue)) {
                //         return {
                //             isValid: false,
                //             message: FIELD_VALIDATORS.email.message,
                //         };
                //     }
                // }

                return {
                    isValid: true,
                };
            }

            function validateProposalForm() {
                let isFormValid = true;
                const errors = [];

                $("#proposalForm .form-inputs").each(function() {
                    if (!validateField($(this))) {
                        isFormValid = false;
                        const fieldLabel = $(this)
                            .closest(".form-group")
                            .find("label")
                            .text()
                            .replace("*", "")
                            .trim();
                        errors.push(`${fieldLabel}: Please check the entered value`);
                    }
                });

                // if (!validateReinsurerSelection()) {
                //     isFormValid = false;
                //     errors.push("<b>Reinsurer Selection:</b> Please add at least one reinsurer");
                // }

                const allUploadedFiles = pipelineManager.getAllUploadedFiles();
                let fileNames = Object.values(allUploadedFiles).flatMap(innerArray =>
                    Object.values(innerArray).map(fileObj => fileObj.fileName)
                );

                const requiredFiles = $('#proposalForm input[type="file"][required]');
                const missingFiles = [];

                requiredFiles.each(function() {
                    const fileName = $(this).attr('name');
                    const isFileUploaded = fileNames.includes(fileName);

                    if (!isFileUploaded) {
                        const fieldLabel = $(this)
                            .closest(".form-group")
                            .find("label")
                            .text()
                            .replace("*", "")
                            .trim();
                        missingFiles.push(fieldLabel || fileName);
                    }
                });

                if (missingFiles.length > 0) {
                    isFormValid = false;
                    errors.push(`<b>Required Files:</b> Please upload: ${missingFiles.join(', ')}`);
                }

                return {
                    isValid: isFormValid,
                    errors: errors,
                };
            }

            function prepareFormData() {
                const formData = new FormData();
                const $form = $("#proposalForm");

                $form.find("input:not([type='file']), select, textarea").each(function() {
                    const $element = $(this);
                    const name = $element.attr("name");
                    const type = $element.attr("type");

                    if (!name) return;

                    if (type === "checkbox" || type === "radio") {
                        if ($element.is(":checked")) {
                            formData.append(name, $element.val());
                        }
                    } else {
                        const value = $element.val();
                        if (value !== null && value !== "") {
                            formData.append(name, value);
                        }
                    }
                });

                // const allUploadedFiles = pipelineManager.getAllUploadedFiles()

                // Object.entries(allUploadedFiles).forEach(([fieldId, files]) => {
                //     files.forEach((file) => {
                //         formData.append('facultative_files[]', file);
                //     });
                // });

                // const reinsurersData = [];
                // let totalPlacedShares = 0;

                // $("#reinsurersTable tbody tr").each(function() {
                //     const $row = $(this);
                //     const writtenShare = parseFloat($row.attr("data-written-share")) || 0;
                //     totalPlacedShares += writtenShare;

                //     reinsurersData.push({
                //         id: $row.data("reinsurer-id"),
                //         written_share: writtenShare,
                //     });
                // });

                // formData.append("reinsurers_data", JSON.stringify(reinsurersData));
                // formData.append("total_placed_shares", totalPlacedShares.toFixed(2));
                // formData.append("total_unplaced_shares", (100 - totalPlacedShares).toFixed(2));

                return formData;
            }

            $('#proposalForm').on('submit', function(e) {
                e.preventDefault();

                const $proposalForm = $("#proposalForm");
                const $submitBtn = $proposalForm.find("button[type='submit']");

                const originalBtnContent = $submitBtn.html();
                // const validation = validateProposalForm();

                // if (!validation.isValid) {
                //     let errorHtml = '<ul class="m-0 p-0">';
                //     validation.errors.forEach((error) => {
                //         errorHtml += `<li class="m-0 p-0" style="text-align: start;">${error}</li>`;
                //     });
                //     errorHtml += "</ul>";

                //     Swal.fire({
                //         icon: "error",
                //         title: "Validation Failed",
                //         html: errorHtml,
                //         confirmButtonColor: "#dc3545",
                //     });

                //     const $firstError = $proposalForm.find(".is-invalid").first();
                //     if ($firstError.length) {
                //         $firstError[0].scrollIntoView({
                //             behavior: "smooth",
                //             block: "center",
                //         });
                //         setTimeout(() => $firstError.focus(), 500);
                //     }

                //     return false;
                // }

                $submitBtn
                    .html('<i class="bx bx-loader-alt bx-spin me-1"></i> Sending...')
                    .prop("disabled", true);

                const formData = prepareFormData();

                $.ajax({
                    url: $proposalForm.attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    timeout: 30000,
                    success: function(response) {
                        console.log(response)
                        // if (response.success) {
                        //     // resetLeadModal();
                        //     Swal.fire({
                        //         icon: "success",
                        //         title: "Lead Saved Successfully!",
                        //         text: response.message ||
                        //             "Your lead has been submitted",
                        //         showConfirmButton: true,
                        //     }).then((result) => {
                        //         if (result.isConfirmed) {
                        //             handleSendBDNotification(response);
                        //         } else {
                        //             $("#leadModal").modal("hide");
                        //         }
                        //     });
                        // } else {
                        //     throw new Error(response.message || "Submission failed");
                        // }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage =
                            "An unexpected error occurred while sending the lead.";

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const serverErrors = xhr.responseJSON.errors;
                            errorMessage = Object.values(serverErrors).flat().join("<br>");
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (status === "timeout") {
                            errorMessage =
                                "Request timed out. Please check your connection and try again.";
                        } else if (xhr.status === 0) {
                            errorMessage =
                                "Network error. Please check your internet connection.";
                        } else if (xhr.status === 404) {
                            errorMessage =
                                "The submission endpoint was not found. Please contact support.";
                        } else if (xhr.status === 500) {
                            errorMessage =
                                "Server error occurred. Please try again later or contact support.";
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Submission Failed",
                            html: errorMessage,
                            confirmButtonColor: "#dc3545",
                        });
                    },
                    complete: function() {
                        $submitBtn.html(originalBtnContent).prop("disabled", false);
                    },
                });
            });
        });
    </script>
@endpush
