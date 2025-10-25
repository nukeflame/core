<!-- Proposal Stage Modal -->
<div id="proposalModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticPropoalStageLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="proposalForm" action="{{ route('update.opp.status') }}" novalidate>
                <input type="hidden" class="opportunity_id" name="opportunity_id" />
                <input type="hidden" class="current_stage" name="current_stage" />
                <input type="hidden" name="class_code" class="class_code">
                <input type="hidden" name="class_group_code" class="class_group_code">
                <input type="hidden" name="total_placed_shares">
                <input type="hidden" name="total_unplaced_shares" class="reinsurers_data">
                <input type="hidden" name="selected_reinsurers" class="selected_reinsurers">

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
                                            <input type="text" class="form-inputs risk_type" name="risk_type"
                                                readonly />
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
                                <div class="reinsurer-selection-panel mb-2" id="reinSelectionPlacement">
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
                                                <input type="number" class="form-inputs"
                                                    id="totalWrittenReinsurerShare" name="total_reinsurer_share"
                                                    placeholder="0.00" step="0.01" min="0.01" max="100"
                                                    readonly>
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
            let proposalState = {
                reinsurers: [],
                totalShare: 0,
                isInitialized: false
            };

            const $form = $("#proposalForm");
            const $modal = $("#proposalModal");

            function validateField($field) {
                const val = $field.val();
                const fieldType = $field.attr('type');
                const isRequired = $field.attr('required') !== undefined;

                $field.removeClass('is-invalid');

                if (isRequired && (!val || val.trim() === '')) {
                    $field.addClass('is-invalid');
                    return false;
                }

                if (fieldType === 'number' && val) {
                    const numVal = parseFloat(val);
                    const min = parseFloat($field.attr('min'));
                    const max = parseFloat($field.attr('max'));

                    if (isNaN(numVal) || (min && numVal < min) || (max && numVal > max)) {
                        $field.addClass('is-invalid');
                        return false;
                    }
                }

                if (fieldType === 'email' && val) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(val)) {
                        $field.addClass('is-invalid');
                        return false;
                    }
                }

                return true;
            }

            function validateReinsurerSelection() {
                if (typeof proposalState === 'undefined') {
                    return {
                        isValid: false,
                        message: 'Proposal state not initialized'
                    };
                }

                const reinsurers = proposalState.reinsurers || [];
                const totalShare = proposalState.totalShare || 0;


                if (reinsurers.length === 0) {
                    return {
                        isValid: false,
                        message: 'At least one reinsurer must be selected'
                    };
                }

                if (totalShare <= 0 || totalShare > 100) {
                    return {
                        isValid: false,
                        message: `Total share (${totalShare.toFixed(2)}%) must be between 0 and 100%`
                    };
                }

                return {
                    isValid: true,
                    message: 'Valid'
                };
            }

            function validateProposalForm() {
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
                    let missingFiles = [];

                    requiredFiles.each(function() {
                        const fileName = $(this).attr('name').trim();

                        const isMatch = fileNames.some(item =>
                            toCamelCase(item) === toCamelCase(fileName)
                        );

                        if (!isMatch) {
                            const fieldLabel = $(this)
                                .closest(".form-group")
                                .find("label")
                                .text()
                                .replace("*", "")
                                .trim();
                            missingFiles.push(fieldLabel);
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

            function toCamelCase(str) {
                return str
                    .replace(/[^a-zA-Z0-9]+(.)/g, (_, chr) => chr.toUpperCase());
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

                if (typeof proposalState !== 'undefined') {
                    formData.append("reinsurers_data", JSON.stringify(proposalState.reinsurers || []));
                    formData.append("total_placed_shares", (proposalState.totalShare || 0).toFixed(2));
                    formData.append("total_unplaced_shares", (100 - (proposalState.totalShare || 0)).toFixed(2));
                }

                return formData;
            }

            $form.on('submit', function(e) {
                e.preventDefault();

                const $submitBtn = $form.find("button[type='submit']");
                const originalBtnContent = $submitBtn.html();

                const validation = validateProposalForm();
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

                // $submitBtn
                //     .html('<i class="bx bx-loader-alt bx-spin me-1"></i> Sending Proposal...')
                //     .prop("disabled", true);

                // const formData = prepareFormData();

                console.log(`validation`, validation)
                // $.ajax({
                //     url: $form.attr("action"),
                //     method: "POST",
                //     data: formData,
                //     processData: false,
                //     contentType: false,
                //     headers: {
                //         "X-Requested-With": "XMLHttpRequest",
                //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                //     },
                //     timeout: 60000,
                //     success: function(response) {
                //         if (response.success) {
                //             resetProposalModal();

                //             Swal.fire({
                //                 icon: "success",
                //                 title: "Proposal Sent Successfully!",
                //                 text: response.message ||
                //                     "Your proposal has been submitted",
                //                 showConfirmButton: true,
                //             }).then(() => {
                //                 $modal.modal("hide");

                //                 if (typeof pipelineManager !== 'undefined' &&
                //                     typeof pipelineManager.reloadAllTables ===
                //                     'function') {
                //                     pipelineManager.reloadAllTables();
                //                 }

                //                 if (typeof pipelineManager !== 'undefined' &&
                //                     typeof pipelineManager.loadChartData === 'function'
                //                 ) {
                //                     pipelineManager.loadChartData();
                //                 }
                //             });
                //         } else {
                //             throw new Error(response.message || "Submission failed");
                //         }
                //     },
                //     error: function(xhr, status, error) {
                //         let errorMessage =
                //             "An unexpected error occurred while sending the proposal.";

                //         if (xhr.status === 422 && xhr.responseJSON?.errors) {
                //             const serverErrors = xhr.responseJSON.errors;
                //             errorMessage = '<ul class="text-start mb-0">';
                //             Object.values(serverErrors).flat().forEach(err => {
                //                 errorMessage += `<li>${err}</li>`;
                //             });
                //             errorMessage += '</ul>';
                //         } else if (xhr.responseJSON?.message) {
                //             errorMessage = xhr.responseJSON.message;
                //         } else if (status === "timeout") {
                //             errorMessage =
                //                 "Request timed out. Please check your connection and try again.";
                //         } else if (xhr.status === 0) {
                //             errorMessage =
                //                 "Network error. Please check your internet connection.";
                //         } else if (xhr.status === 404) {
                //             errorMessage =
                //                 "The submission endpoint was not found. Please contact support.";
                //         } else if (xhr.status === 500) {
                //             errorMessage =
                //                 "Server error occurred. Please try again later or contact support.";
                //         }

                //         Swal.fire({
                //             icon: "error",
                //             title: "Submission Failed",
                //             html: errorMessage,
                //             confirmButtonColor: "#dc3545",
                //         });
                //     },
                //     complete: function() {
                //         $submitBtn.html(originalBtnContent).prop("disabled", false);
                //     },
                // });
            });

            $form.find('.form-inputs').on('blur', function() {
                validateField($(this));
            });

            $modal.on('shown.bs.modal', function() {
                if (!proposalState.isInitialized) {
                    proposalState.isInitialized = true;

                    try {
                        const selectedReinsurersValue = $(".selected_reinsurers").val();

                        if (selectedReinsurersValue && selectedReinsurersValue.trim() !== '') {
                            const reinsurers = JSON.parse(selectedReinsurersValue);
                            proposalState.reinsurers = Array.isArray(reinsurers) ? reinsurers : [];
                        } else {
                            proposalState.reinsurers = [];
                        }

                        proposalState.totalShare = proposalState.reinsurers.reduce((sum, reinsurer) => {
                            const writtenShare = parseFloat(reinsurer.written_share || 0);
                            return sum + writtenShare;
                        }, 0);

                        $('#totalWrittenReinsurerShare').val(proposalState.totalShare.toFixed(2));
                        $('#reinsurerCount').text(proposalState.reinsurers.length);
                    } catch (error) {
                        proposalState.reinsurers = [];
                        proposalState.totalShare = 0;
                        $('#totalWrittenReinsurerShare').val('0.00');
                        $('#reinsurerCount').text('0');
                    }
                }
            });

            $modal.on('hidden.bs.modal', function() {
                resetProposalModal();
            });

            function resetProposalModal() {
                $form[0].reset();
                $form.find('.is-invalid').removeClass('is-invalid');

                if (typeof proposalState !== 'undefined') {
                    proposalState.reinsurers = [];
                    proposalState.totalShare = 0;
                    proposalState.isInitialized = false; // Reset this flag
                }

                if (typeof pipelineManager !== 'undefined' &&
                    typeof pipelineManager.clearAllFiles === 'function') {
                    pipelineManager.clearAllFiles();
                }

                $('#propReinsurersTable tbody').empty();
                $('#reinsurerCount').text('0');
                $('#totalNegReinsurerShare').val('0.00');
            }
        });
    </script>
@endpush
