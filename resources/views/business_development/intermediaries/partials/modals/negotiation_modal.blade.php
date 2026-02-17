<!-- Negotiation Stage Modal -->
<div id="negotiationModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticPropoalStageLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="negotiationForm" action="{{ route('update.opp.status') }}" novalidate>
                <input type="hidden" class="opportunity_id" name="opportunity_id" id="negOpportunityId" />
                <input type="hidden" class="cedant_id" name="cedant_id" id="negCedId" />
                <input type="hidden" class="current_stage" name="current_stage" />
                <input type="hidden" name="class_code" class="class_code">
                <input type="hidden" name="class_group_code" class="class_group_code">
                <input type="hidden" name="total_placed_shares" id="negPlacedShare">
                <input type="hidden" name="total_unplaced_shares" id="negUnPlacedShare">
                <input type="hidden" name="selected_reinsurers" class="selected_reinsurers">
                <input type="hidden" name="reinsurers_data" class="reinsurers_data" id="negReinsurersData">

                <div class="modal-body fac-slip-container">
                    <div class="fac-slip-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h1 class="slip-title">
                                    <i class="bx bx-shield me-1"></i>Facultative Slip
                                </h1>
                                <p class="slip-subtitle mb-0">Reinsurance Coverage Negotiation</p>
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
                        <!-- Negotiation Details Section -->
                        <div class="form-section">
                            <div class="section-header" data-section="coverage-details">
                                <div class="section-title">
                                    <span>
                                        <i class="bx bx-check section-icon"></i>
                                        Negotiation Details
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
                                            <input type="text" class="form-inputs risk_type" name="risk_type" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Last Contact Date
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <input type="date" class="form-inputs last_contact_date"
                                                name="last_contact_date" />
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
                                                <small class="form-text form-inputs cedant_name"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button"
                                                    class="btn btn-primary add_cedant_contacts btn-sm w-100"
                                                    style="padding: 2px 0px;">
                                                    <i class="bx bx-book" style="font-size: 27px;"></i>
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
                                <div class="selected-reinsurers-section">
                                    <h6 class="mb-3">
                                        <i class="bi bi-people-fill me-1"></i>Selected Reinsurers
                                        <span class="badge bg-primary ms-2" id="reinsurerCount">0</span>
                                    </h6>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped selected-reinsurers-table"
                                            id="negReinsurersTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="width: 1%"></th>
                                                    <th style="width: 41%">Reinsurer</th>
                                                    <th style="width: 20%">Written Share (%)</th>
                                                    <th style="width: 20%">Signed Share (%)</th>
                                                    <th style="width: 18%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="total-shares-display mt-3 d-block">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="shares-card placed-shares">
                                                <div class="shares-icon">
                                                    <i class="bx bx-check-circle"></i>
                                                </div>
                                                <div class="shares-info">
                                                    <span class="shares-label">Placed Shares</span>
                                                    <span class="shares-value placed-value">0.00%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="shares-card unplaced-shares">
                                                <div class="shares-icon">
                                                    <i class="bx bx-time-five"></i>
                                                </div>
                                                <div class="shares-info">
                                                    <span class="shares-label">Unplaced Shares</span>
                                                    <span class="shares-value unplaced-value">100.00%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shares-progress mt-2">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success placed-progress" role="progressbar"
                                                style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
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
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" id="negotiation-view-slip">
                                <i class="bx bx-file me-1"></i>Preview Slip
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-dark">
                                <i class="bx bx-paper-plane me-1"></i> Send Negotiation
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="negotiation-quoteslip-form" method="POST" action="{{ route('quote.quotationCoverSlip') }}"
    target="_blank" style="display: none;">
    @csrf
</form>

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

            let negotiationState = {
                reinsurers: [],
                totalShare: 0,
                isInitialized: false
            };

            const $modal = $("#negotiationModal");
            const $form = $("#negotiationForm");
            const $table = $modal.find("#negReinsurersTable");

            let reinsurerDataTable = null;

            $("#negotiationForm").on("input", ".form-inputs", function() {
                validateField($(this));
            });

            function validateReinsurerSelection() {
                if (negotiationState.reinsurers.length < VALIDATION_CONFIG.MIN_REINSURERS) {
                    return {
                        isValid: false,
                        message: 'Please add at least one reinsurer'
                    };
                }

                const missingSignedShares = negotiationState.reinsurers.filter(r => {
                    const signedShare = parseFloat(r.signed_share || 0);
                    return signedShare <= 0 && r.is_declined !== true;
                });

                // console.log(missingSignedShares)

                if (missingSignedShares.length > 0) {
                    const reinsurerNames = missingSignedShares
                        .map(r => r.reinsurer_name || r.name)
                        .join(', ');
                    return {
                        isValid: false,
                        message: `Please enter signed shares for: ${reinsurerNames}`
                    };
                }

                // if (Math.abs(negotiationState.totalShare - 100) > 0.01) {
                //     return {
                //         isValid: false,
                //         message: `Total signed share must equal 100% (current: ${negotiationState.totalShare.toFixed(2)}%)`
                //     };
                // }

                return {
                    isValid: true
                };
            }

            function validateNegotiationForm() {
                let isFormValid = true;
                const errors = [];

                $form.find(".form-inputs[required], .form-inputs").each(function() {
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

                const reinsurerValidation = validateReinsurerSelection();
                if (!reinsurerValidation.isValid) {
                    isFormValid = false;
                    errors.push(`<b>Reinsurer Selection:</b> ${reinsurerValidation.message}`);
                }

                if (typeof pipelineManager !== 'undefined' &&
                    typeof pipelineManager.getAllUploadedFiles === 'function') {

                    const allUploadedFiles = pipelineManager.getAllUploadedFiles();
                    const fileNames = Object.values(allUploadedFiles || {}).flatMap(innerArray =>
                        Object.values(innerArray || {}).map(fileObj => fileObj?.fileName)
                    ).filter(Boolean);

                    const requiredFiles = $form.find('input[type="file"][required]');
                    const missingFiles = [];

                    requiredFiles.each(function() {
                        const fileName = $(this).attr('name');
                        if (!fileNames.includes(fileName)) {
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
                }

                return {
                    isValid: isFormValid,
                    errors: errors,
                };
            }

            function prepareFormData() {
                const formData = new FormData();

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
                            const cleanValue = (name?.includes('sum_insured') || name?.includes(
                                    'premium')) ?
                                value.replace(/,/g, '') :
                                value;
                            formData.append(name, cleanValue);
                        }
                    }
                });

                if (typeof pipelineManager !== 'undefined' &&
                    typeof pipelineManager.getAllUploadedFiles === 'function') {

                    const allUploadedFiles = pipelineManager.getAllUploadedFiles();
                    Object.entries(allUploadedFiles || {}).forEach(([fieldId, files]) => {
                        if (Array.isArray(files)) {
                            files.forEach((file) => {
                                if (file instanceof File) {
                                    formData.append('facultative_files[]', file);
                                }
                            });
                        }
                    });
                }

                formData.append("reinsurers_data", JSON.stringify(negotiationState.reinsurers));
                formData.append("total_placed_shares", negotiationState.totalShare.toFixed(2));
                formData.append("total_unplaced_shares", (100 - negotiationState.totalShare).toFixed(2));

                return formData;
            }

            $form.on('submit', function(e) {
                e.preventDefault();

                const $submitBtn = $form.find("button[type='submit']");
                const originalBtnContent = $submitBtn.html();

                const validation = validateNegotiationForm();
                if (!validation.isValid) {
                    let errorHtml = '<ul class="text-start mb-0">';
                    validation.errors.forEach((error) => {
                        errorHtml += `<li class="mb-1">${error}</li>`;
                    });
                    errorHtml += "</ul>";

                    Swal.fire({
                        icon: "error",
                        title: "Validation Failed",
                        html: errorHtml,
                        confirmButtonColor: "#dc3545",
                    });

                    const $firstError = $form.find(".is-invalid").first();
                    if ($firstError.length) {
                        $firstError[0].scrollIntoView({
                            behavior: "smooth",
                            block: "center",
                        });
                        setTimeout(() => $firstError.focus(), 500);
                    }

                    return false;
                }

                $submitBtn
                    .html('<i class="bx bx-loader-alt bx-spin me-1"></i> Sending Negotiation...')
                    .prop("disabled", true);

                const formData = prepareFormData();

                $.ajax({
                    url: $form.attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    timeout: 60000,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Negotiation Sent Successfully!",
                                text: "Your negotiation has been submitted",
                                showConfirmButton: true,
                                timer: 3000
                            }).then(() => {
                                resetNegotiationModal();

                                $modal.modal("hide");

                                if (typeof pipelineManager !== 'undefined' &&
                                    typeof pipelineManager.reloadAllTables ===
                                    'function') {
                                    pipelineManager.reloadAllTables();
                                }

                                if (typeof pipelineManager !== 'undefined' &&
                                    typeof pipelineManager.loadChartData === 'function'
                                ) {
                                    pipelineManager.loadChartData();
                                }
                            });
                        } else {
                            throw new Error(response.message || "Submission failed");
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage =
                            "An unexpected error occurred while sending the negotiation.";

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const serverErrors = xhr.responseJSON.errors;
                            errorMessage = '<ul class="text-start mb-0">';
                            Object.values(serverErrors).flat().forEach(err => {
                                errorMessage += `<li>${err}</li>`;
                            });
                            errorMessage += '</ul>';
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

            function editReinsurer(reinsurerId, reinsurerName, currentWrittenShare, currentSignedShare) {
                const reinsurerIndex = negotiationState.reinsurers.findIndex(r =>
                    r.reinsurer_id === reinsurerId || r.id === reinsurerId
                );

                if (reinsurerIndex === -1) {
                    showValidationError('Reinsurer not found');
                    return;
                }

                const reinsurer = negotiationState.reinsurers[reinsurerIndex];
                const escapedName = escapeHtml(reinsurerName);

                $("#editReinsurerSharesModal").remove();

                const modalHtml = `
                    <div class="modal fade mod-popup effect-scale" id="editReinsurerSharesModal" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Edit Signed Share</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body pt-4 pb-3">
                                    <div class="text-center mb-3">
                                        <label class="form-label fw-semibold">${escapedName}</label>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label text-muted">Written Share</label>
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                class="form-control"
                                                value="${currentWrittenShare}%"
                                                readonly
                                                disabled
                                            >
                                        </div>
                                        <small class="text-muted">This value is set during initial placement</small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="editSignedShareInput" class="form-label">
                                            Signed Share (%)
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="editSignedShareInput"
                                                value="${currentSignedShare || ''}"
                                                min="0.01"
                                                max="100"
                                                step="0.01"
                                                placeholder="Enter signed share percentage"
                                            >
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="invalid-feedback" id="signedShareError"></div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Current total signed: ${negotiationState.totalShare.toFixed(2)}%
                                        (${(100 - negotiationState.totalShare).toFixed(2)}% remaining)
                                    </small>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-success" id="confirmSharesUpdate">
                                        <i class="bx bx-check me-1"></i>Update Share
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $("body").append(modalHtml);

                $("#editSignedShareInput").removeClass("is-invalid");
                $("#signedShareError").text("");

                const editModal = new bootstrap.Modal(
                    document.getElementById("editReinsurerSharesModal")
                );

                $("#editReinsurerSharesModal").one("shown.bs.modal", function() {
                    $("#editSignedShareInput").focus().select();
                });

                $("#editReinsurerSharesModal").one("hidden.bs.modal", function() {
                    $("#editReinsurerSharesModal").remove();
                });

                $("#confirmSharesUpdate")
                    .off("click")
                    .on("click", () => {
                        const signedValue = $("#editSignedShareInput").val();
                        const newSignedShare = parseFloat(signedValue);

                        $("#editSignedShareInput").removeClass("is-invalid");
                        $("#signedShareError").text("");

                        if (signedValue === "" || isNaN(newSignedShare)) {
                            $("#editSignedShareInput").addClass("is-invalid");
                            $("#signedShareError").text("Please enter a valid number");
                            return;
                        }

                        if (newSignedShare <= 0 || newSignedShare > 100) {
                            $("#editSignedShareInput").addClass("is-invalid");
                            $("#signedShareError").text("Please enter a value between 0.01 and 100");
                            return;
                        }

                        const otherSignedSharesTotal = negotiationState.reinsurers
                            .filter((r, idx) => idx !== reinsurerIndex)
                            .reduce((sum, r) => sum + parseFloat(r.signed_share || 0), 0);

                        const newTotal = otherSignedSharesTotal + newSignedShare;

                        if (newTotal > 100) {
                            $("#editSignedShareInput").addClass("is-invalid");
                            $("#signedShareError").text(
                                `Total would exceed 100%. Maximum allowed: ${(100 - otherSignedSharesTotal).toFixed(2)}%`
                            );
                            return;
                        }

                        negotiationState.reinsurers[reinsurerIndex].signed_share = newSignedShare.toFixed(2);

                        const tableData = transformReinsurerData(negotiationState.reinsurers);
                        reinsurerDataTable.clear();
                        reinsurerDataTable.rows.add(tableData);
                        reinsurerDataTable.draw();

                        $('.selected_reinsurers').val(JSON.stringify(tableData) || []);

                        updateTotalShare();
                        updatePlacementDisplay();

                        showSuccessToast('Signed share updated successfully');

                        editModal.hide();
                    });

                $("#editSignedShareInput").on("keypress", (e) => {
                    if (e.which === 13) {
                        e.preventDefault();
                        $("#confirmSharesUpdate").click();
                    }
                });

                editModal.show();
            }

            function removeReinsurer(reinsurerId, reinsurerName) {
                const reinsurerIndex = negotiationState.reinsurers.findIndex(r =>
                    r.reinsurer_id === reinsurerId || r.id === reinsurerId
                );

                if (reinsurerIndex === -1) {
                    showValidationError('Reinsurer not found');
                    return;
                }

                Swal.fire({
                    title: 'Remove Reinsurer?',
                    html: `Are you sure you want to remove <strong>${escapeHtml(reinsurerName)}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        negotiationState.reinsurers.splice(reinsurerIndex, 1);

                        const tableData = transformReinsurerData(negotiationState.reinsurers);
                        reinsurerDataTable.clear();
                        reinsurerDataTable.rows.add(tableData);
                        reinsurerDataTable.draw();

                        updateReinsurerCount();
                        updateTotalShare();
                        updatePlacementDisplay();

                        showSuccessToast('Reinsurer removed successfully');
                    }
                });
            }

            function attachReinsurerActionHandlers() {
                $table.off('click', '.edit-reinsurer-btn');
                $table.off('click', '.contact-reinsurer-btn');

                $table.on('click', '.edit-reinsurer-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const reinsurerId = $(this).data('reinsurer-id');
                    const reinsurerName = $(this).data('reinsurer-name');
                    const writtenShare = $(this).data('written-share');
                    const signedShare = $(this).data('signed-share') || 0;

                    editReinsurer(reinsurerId, reinsurerName, writtenShare, signedShare);
                });

                $table.on('click', '.contact-reinsurer-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const reinsurerId = $(this).data('reinsurer-id');
                });
            }

            function updateReinsurerCount() {
                $('#reinsurerCount').text(negotiationState.reinsurers.length);
            }

            function updateTotalShare() {
                negotiationState.totalShare = negotiationState.reinsurers.reduce(
                    (sum, r) => sum + parseFloat(r.signed_share || 0),
                    0
                );

                $('#negPlacedShare').val(negotiationState.totalShare.toFixed(2));
                $('#negUnPlacedShare').val((100 - negotiationState.totalShare).toFixed(2));

                const $warning = $('.share-warning');
                $warning.remove();
            }

            function updatePlacementDisplay() {
                const placedShare = negotiationState.totalShare;
                const unplacedShare = 100 - placedShare;

                $('.placed-value').text(placedShare.toFixed(2) + '%');
                $('.unplaced-value').text(unplacedShare.toFixed(2) + '%');

                const progressBar = $('.placed-progress');

                const displayWidth = Math.min(placedShare, 100);
                progressBar
                    .css('width', displayWidth + '%')
                    .attr('aria-valuenow', placedShare);

                if (Math.abs(placedShare - 100) < 0.01) {
                    progressBar.removeClass('bg-warning bg-danger').addClass('bg-success');
                } else if (placedShare > 100) {
                    progressBar.removeClass('bg-warning bg-success').addClass('bg-danger');
                } else {
                    progressBar.removeClass('bg-success bg-danger').addClass('bg-warning');
                }

                $('#negPlacedShare').val(placedShare.toFixed(2));
                $('#negUnPlacedShare').val(unplacedShare.toFixed(2));
            }

            function resetNegotiationModal() {
                $form[0].reset();

                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.is-valid').removeClass('is-valid');
                $form.find('.invalid-feedback').remove();

                negotiationState.reinsurers = [];
                negotiationState.totalShare = 0;
                negotiationState.isInitialized = false;

                if (reinsurerDataTable) {
                    reinsurerDataTable.clear().draw();
                }

                updateReinsurerCount();
                updateTotalShare();
                updatePlacementDisplay();

                $('.share-warning').remove();

                $('#negReinsurersData').val('');
                $('.selected_reinsurers').val('');
                $('#negPlacedShare').val('0.00');
                $('#negUnPlacedShare').val('100.00');

                if (typeof pipelineManager !== 'undefined' &&
                    typeof pipelineManager.clearAllFiles === 'function') {
                    pipelineManager.clearAllFiles();
                }
            }

            function validateField($field) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const fieldValue = $field.val()?.trim() || '';
                const isRequired = $field.prop("required") ||
                    VALIDATION_CONFIG.REQUIRED_FIELDS.includes(fieldName);

                $field.removeClass("is-invalid is-valid");
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
                    $field.addClass("is-valid");
                } else if (!isValid) {
                    $field.addClass("is-invalid");
                    $field.after(`<div class="invalid-feedback d-block">${errorMessage}</div>`);
                }

                return isValid;
            }

            function getFieldValidation($field) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const fieldValue = $field.val();
                const numericValue = parseFloat(fieldValue?.replace(/,/g, "") || 0);

                if ($field.closest(".currency-input").length ||
                    fieldName?.includes("premium") ||
                    fieldName?.includes("sum_insured")) {

                    const cleanValue = fieldValue?.replace(/,/g, "") || "";

                    if (!FIELD_VALIDATORS.currency.pattern.test(cleanValue)) {
                        return {
                            isValid: false,
                            message: FIELD_VALIDATORS.currency.message,
                        };
                    }
                    if (numericValue <= 0) {
                        return {
                            isValid: false,
                            message: "Amount must be greater than 0",
                        };
                    }
                }

                if (fieldName?.includes("rate") || fieldName?.includes("Share")) {
                    if (!FIELD_VALIDATORS.percentage.pattern.test(fieldValue)) {
                        return {
                            isValid: false,
                            message: FIELD_VALIDATORS.percentage.message,
                        };
                    }
                    if (numericValue < FIELD_VALIDATORS.percentage.min ||
                        numericValue > FIELD_VALIDATORS.percentage.max) {
                        return {
                            isValid: false,
                            message: `Percentage must be between ${FIELD_VALIDATORS.percentage.min} and ${FIELD_VALIDATORS.percentage.max}`,
                        };
                    }
                }

                if ($field.attr("type") === "email" || fieldName?.includes("email")) {
                    if (!FIELD_VALIDATORS.email.pattern.test(fieldValue)) {
                        return {
                            isValid: false,
                            message: FIELD_VALIDATORS.email.message,
                        };
                    }
                }

                return {
                    isValid: true
                };
            }

            $modal.on('shown.bs.modal', function() {
                if (!negotiationState.isInitialized) {
                    negotiationState.isInitialized = true;
                }

                loadAvailableReinsurers();
            });

            $modal.on('hidden.bs.modal', function() {
                if (negotiationState.reinsurers.length > 0 || negotiationState.isInitialized) {
                    resetNegotiationModal();
                }
            });

            function loadAvailableReinsurers() {
                try {
                    const reinsurersData = $("#negReinsurersData").val();
                    const reinsurers = reinsurersData ? JSON.parse(reinsurersData) : [];

                    if (reinsurers.length > 0) {
                        negotiationState.reinsurers = reinsurers;
                    }

                    initializeReinsurerTable();
                } catch (error) {
                    showValidationError('Failed to load reinsurer data');
                }
            }

            function initializeReinsurerTable() {
                if (reinsurerDataTable) {
                    try {
                        reinsurerDataTable.destroy();
                        reinsurerDataTable = null;
                    } catch (e) {}
                }

                const tableData = transformReinsurerData(negotiationState.reinsurers);

                reinsurerDataTable = $table.DataTable({
                    data: tableData,
                    columns: [{
                            data: 'id',
                            title: '',
                            width: "1%",
                            orderable: false,
                            searchable: false,
                            render: (data, type, row, index) => {
                                return `1`;
                            }
                        }, {
                            data: 'name',
                            title: 'Reinsurer',
                            render: (data, type, row) => {
                                const escapedName = escapeHtml(data);
                                const escapedContact = escapeHtml(row.contact);
                                return `
                                    <div class="d-flex flex-start">
                                        <div>
                                            <div class="fw-medium">${escapedName}</div>
                                            <small class="text-muted">${escapedContact}</small>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            data: 'written_share',
                            title: 'Written Share (%)',
                            className: 'text-start',
                            render: (data, type, row) => {
                                if (row.is_declined) {
                                    return '--';
                                }

                                return `
                                    <span class="badge bg-success">${data}%</span>
                                `;
                            }
                        },
                        {
                            data: 'signed_share',
                            title: 'Signed Share (%)',
                            className: 'text-start',
                            render: (data, type, row) => {
                                const escapedName = escapeHtml(row.name);
                                const signedShare = parseFloat(data);
                                const badgeClass = signedShare > 0 ? 'bg-secondary' : 'bg-danger';
                                const displayText = signedShare > 0 ? `${data}%` : 'Required';

                                if (row.is_declined) {
                                    return '--';
                                }

                                return `
                                <span>
                                    <span class="badge ${badgeClass}">${displayText}</span>
                                    <span class="badge bg-dark edit-reinsurer-btn"
                                        data-reinsurer-id="${row.id}"
                                        data-reinsurer-name="${escapedName}"
                                        data-written-share="${row.written_share}"
                                        data-signed-share="${row.signed_share}"
                                        style="margin-left: 0.25rem; cursor: pointer;"
                                        title="Edit Signed Share">
                                        <i class="bx bx-edit"></i>
                                    </span>
                                </span>
                            `;
                            }
                        },
                        {
                            data: "action",
                            title: "Action",
                            orderable: false,
                            searchable: false,
                            className: "text-left",
                            render: (data, type, row) => {
                                if (row.is_declined) {
                                    return `<span class="badge bg-danger">Declined</span>`
                                }

                                return `
                                    <div>
                                        <button type="button" class="btn btn-primary btn-sm contact-reinsurer-btn"
                                            data-reinsurer-id="${row.id}"
                                            title="Contacts">
                                            <i class="bx bx-book"></i>
                                        </button>
                                    </div>
                                `;
                            },
                        }
                    ],
                    paging: false,
                    searching: false,
                    info: false,
                    ordering: false,
                    language: {
                        emptyTable: "No reinsurers selected. Click '+' to add reinsurers."
                    },
                    drawCallback: function() {
                        attachReinsurerActionHandlers();
                    }
                });

                updateReinsurerCount();
                updateTotalShare();
                updatePlacementDisplay();
            }

            function showValidationError(message) {
                if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    alert(message);
                }
            }

            function showSuccessToast(message) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(message);
                }
            }

            function transformReinsurerData(reinsurers) {
                return reinsurers.map((reinsurer) => {
                    return {
                        id: reinsurer.reinsurer_id || reinsurer.id,
                        name: reinsurer.reinsurer_name || reinsurer.name,
                        written_share: parseFloat(reinsurer.updated_written_share || 0).toFixed(2),
                        signed_share: parseFloat(reinsurer.signed_share || 0).toFixed(2),
                        previous_written_share: parseFloat(reinsurer.previous_written_share || reinsurer
                            .written_share || 0).toFixed(2),
                        commission: parseFloat(reinsurer.brokerage_rate || reinsurer.commission || 0)
                            .toFixed(2),
                        status: reinsurer.status || 'pending',
                        is_declined: reinsurer.is_declined || false,
                        country: reinsurer.country || '',
                        contact: reinsurer.email || reinsurer.contact || "-",
                        action: "",
                    };
                });
            }

            function escapeHtml(text) {
                if (!text) return "";
                const div = document.createElement("div");
                div.textContent = text;
                return div.innerHTML;
            }

            function previewCoverSlip(printoutType = 0) {
                const sourceForm = $form
                const postForm = $('#negotiation-quoteslip-form');

                postForm.find('input[type="hidden"]:not([name="_token"])').remove();

                const formData = prepareFormData();

                for (let [key, value] of formData.entries()) {
                    if (value instanceof File) {
                        continue;
                    }

                    postForm.append($('<input>', {
                        type: 'hidden',
                        name: key,
                        value: value
                    }));
                }

                postForm.append($('<input>', {
                    type: 'hidden',
                    name: 'printout_flag',
                    value: printoutType
                }));

                postForm.submit();
            }

            $("#negotiation-view-slip").on("click", () => previewCoverSlip());
        });
    </script>
@endpush
