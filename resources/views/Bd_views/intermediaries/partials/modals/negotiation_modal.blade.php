<!-- Negotiation Stage Modal -->
<div id="negotiationModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticPropoalStageLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="negotiationForm" action="{{ route('update.opp.status') }}" novalidate>
                <input type="hidden" class="opportunity_id" id="propOpportunityId" name="opportunity_id" />
                <input type="hidden" class="current_stage" id="propCurrentStage" name="current_stage" />
                <input type="hidden" name="class_code" class="class_code" id="propClassCode">
                <input type="hidden" name="class_group_code" class="class_group_code" id="propClassGroupCode">
                <input type="hidden" name="total_placed_shares" id="propTotalPlacedShares">
                <input type="hidden" name="total_unplaced_shares" class="reinsurers_data" id="propTotalUnplacedShares">

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
                                            <input type="text" class="form-inputs" name="risk_type"
                                                id="riskType" readonly />
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
                                                    <th style="width: 60%">Reinsurer</th>
                                                    <th style="width: 15%">Written Share (%)</th>
                                                    <th style="width: 15%">Signed Share (%)</th>
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
                                <i class="bx bx-paper-plane me-1"></i> Send Negotiation
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

            let negotiationState = {
                reinsurers: [],
                totalShare: 0,
                isInitialized: false
            };

            const $modal = $("#negotiationModal");
            const $form = $("#negotiationForm");
            const $table = $("#negReinsurersTable");
            let reinsurerDataTable = null;

            function initializeReinsurerTable() {
                if (reinsurerDataTable) {
                    try {
                        reinsurerDataTable.destroy();
                    } catch (e) {
                        console.warn('Failed to destroy existing table:', e);
                    }
                }

                reinsurerDataTable = $table.DataTable({
                    data: negotiationState.reinsurers,
                    columns: [{
                            data: 'name',
                            title: 'Reinsurer',
                            render: function(data, type, row) {
                                return `
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-medium">${data}</div>
                                            <small class="text-muted">${row.email || row.contact || ''}</small>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            data: 'written_share',
                            title: 'Written Share (%)',
                            className: 'text-start',
                            render: function(data, type, row) {
                                const percentage = parseFloat(data || 0);
                                const badgeClass = percentage >= 50 ? 'bg-success' :
                                    percentage >= 25 ? 'bg-primary' : 'bg-info';
                                return `<span class="badge ${badgeClass}">${percentage.toFixed(2)}%</span>`;
                            }
                        },
                        {
                            data: 'signed_share',
                            title: 'Signed Share (%)',
                            className: 'text-start',
                            render: function(data, type, row) {
                                const percentage = parseFloat(data || 0);
                                const badgeClass = percentage >= 50 ? 'bg-success' :
                                    percentage >= 25 ? 'bg-primary' : 'bg-info';
                                return `<span class="badge ${badgeClass}">${percentage.toFixed(2)}%</span>`;
                            }
                        },
                        {
                            data: null,
                            title: 'Action',
                            orderable: false,
                            className: 'text-center',
                            render: function(data, type, row, meta) {
                                return `
                                    <button type="button"
                                            class="btn btn-sm btn-primary edit-reinsurer-btn"
                                            data-index="${meta.row}"
                                            title="Edit Share">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-danger remove-reinsurer-btn"
                                            data-index="${meta.row}"
                                            title="Remove">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                `;
                            }
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

                updateTotalShare();
            }

            $table.DataTable({
                data: [],
                columns: [{
                        data: 'name',
                        title: 'Reinsurer'
                    },
                    {
                        data: 'written_share',
                        title: 'Written Share (%)'
                    }, {
                        data: 'signed_share',
                        title: 'Signed Share (%)'
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
                dropdownParent: $('#negotiationModal')
            });

            $("#negotiationForm").on("input blur", ".form-inputs", function() {
                validateField($(this));
            });

            function validateReinsurerSelection() {
                if (negotiationState.reinsurers.length < VALIDATION_CONFIG.MIN_REINSURERS) {
                    return {
                        isValid: false,
                        message: 'Please add at least one reinsurer'
                    };
                }

                if (Math.abs(negotiationState.totalShare - 100) > 0.01) {
                    return {
                        isValid: false,
                        message: `Total reinsurer share must equal 100% (current: ${negotiationState.totalShare.toFixed(2)}%)`
                    };
                }

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

                // Add reinsurer data
                formData.append("reinsurers_data", JSON.stringify(negotiationState.reinsurers));
                formData.append("total_placed_shares", negotiationState.totalShare.toFixed(2));
                formData.append("total_unplaced_shares", (100 - negotiationState.totalShare).toFixed(2));

                return formData;
            }

            $form.on('submit', function(e) {
                e.preventDefault();

                const $submitBtn = $form.find("button[type='submit']");
                const originalBtnContent = $submitBtn.html();

                // const validation = validateNegotiationForm();
                // if (!validation.isValid) {
                //     let errorHtml = '<ul class="text-start mb-0">';
                //     validation.errors.forEach((error) => {
                //         errorHtml += `<li class="mb-1">${error}</li>`;
                //     });
                //     errorHtml += "</ul>";

                //     Swal.fire({
                //         icon: "error",
                //         title: "Validation Failed",
                //         html: errorHtml,
                //         confirmButtonColor: "#dc3545",
                //     });

                //     // Scroll to first error
                //     const $firstError = $form.find(".is-invalid").first();
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
                            resetNegotiationModal();

                            Swal.fire({
                                icon: "success",
                                title: "Negotiation Sent Successfully!",
                                text: "Your negotiation has been submitted",
                                showConfirmButton: true,
                            }).then(() => {
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

            function addReinsurer() {
                const $select = $('#propAvailableReinsurers');
                const $shareInput = $('#reinsurerNegShare');

                const selectedReinsurerId = $select.val();
                const selectedShare = parseFloat($shareInput.val());

                // Validation
                if (!selectedReinsurerId) {
                    showValidationError('Please select a reinsurer');
                    $select.focus();
                    return;
                }

                if (!selectedShare || selectedShare <= 0 || selectedShare > 100) {
                    showValidationError('Please enter a valid share between 0.01 and 100');
                    $shareInput.focus();
                    return;
                }

                // Check if already added
                const existingIndex = negotiationState.reinsurers.findIndex(
                    r => r.id == selectedReinsurerId
                );

                if (existingIndex !== -1) {
                    showValidationError('This reinsurer has already been added');
                    return;
                }

                // Check total share
                const newTotal = negotiationState.totalShare + selectedShare;
                if (newTotal > 100) {
                    showValidationError(
                        `Cannot add ${selectedShare}%. Total would exceed 100% (current: ${negotiationState.totalShare.toFixed(2)}%)`
                    );
                    return;
                }

                // Get reinsurer details from select2
                const selectedOption = $select.find('option:selected');
                const reinsurerData = selectedOption.data('reinsurer') || {};

                // Add to state
                const newReinsurer = {
                    id: selectedReinsurerId,
                    name: selectedOption.text() || reinsurerData.name || 'Unknown',
                    email: reinsurerData.email || '',
                    contact: reinsurerData.contact || '',
                    written_share: selectedShare.toFixed(2),
                    country: reinsurerData.country || ''
                };

                negotiationState.reinsurers.push(newReinsurer);

                // Update table
                reinsurerDataTable.clear();
                reinsurerDataTable.rows.add(negotiationState.reinsurers);
                reinsurerDataTable.draw();

                // Update counter and total
                updateReinsurerCount();
                updateTotalShare();

                // Reset inputs
                $select.val(null).trigger('change');
                $shareInput.val('');

                showSuccessToast('Reinsurer added successfully');
            }

            function editReinsurer(index) {
                const reinsurer = negotiationState.reinsurers[index];
                if (!reinsurer) return;

                Swal.fire({
                    title: 'Edit Written Share',
                    html: `
                <div class="form-group text-start">
                    <label class="form-label fw-semibold mb-2">${reinsurer.name}</label>
                    <div class="input-group">
                        <input type="number"
                               id="editShareInput"
                               class="form-control"
                               value="${reinsurer.written_share}"
                               min="0.01"
                               max="100"
                               step="0.01"
                               placeholder="Enter share percentage">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        Current total: ${negotiationState.totalShare.toFixed(2)}%
                    </small>
                </div>
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const newShare = parseFloat(document.getElementById('editShareInput').value);

                        if (!newShare || newShare <= 0 || newShare > 100) {
                            Swal.showValidationMessage(
                                'Please enter a valid percentage between 0.01 and 100');
                            return false;
                        }

                        // Calculate new total (excluding current reinsurer's share)
                        const otherSharesTotal = negotiationState.totalShare - parseFloat(reinsurer
                            .written_share);
                        const newTotal = otherSharesTotal + newShare;

                        if (newTotal > 100) {
                            Swal.showValidationMessage(
                                `Total would exceed 100%. Maximum allowed: ${(100 - otherSharesTotal).toFixed(2)}%`
                            );
                            return false;
                        }

                        return newShare;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        negotiationState.reinsurers[index].written_share = result.value.toFixed(2);

                        reinsurerDataTable.clear();
                        reinsurerDataTable.rows.add(negotiationState.reinsurers);
                        reinsurerDataTable.draw();

                        updateTotalShare();
                        showSuccessToast('Share updated successfully');
                    }
                });

                // Focus on input when modal opens
                setTimeout(() => {
                    const input = document.getElementById('editShareInput');
                    if (input) {
                        input.focus();
                        input.select();
                    }
                }, 100);
            }

            function removeReinsurer(index) {
                const reinsurer = negotiationState.reinsurers[index];
                if (!reinsurer) return;

                Swal.fire({
                    title: 'Remove Reinsurer?',
                    html: `Are you sure you want to remove <strong>${reinsurer.name}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        negotiationState.reinsurers.splice(index, 1);

                        reinsurerDataTable.clear();
                        reinsurerDataTable.rows.add(negotiationState.reinsurers);
                        reinsurerDataTable.draw();

                        updateReinsurerCount();
                        updateTotalShare();

                        showSuccessToast('Reinsurer removed successfully');
                    }
                });
            }

            function attachReinsurerActionHandlers() {
                $table.off('click', '.edit-reinsurer-btn');
                $table.off('click', '.remove-reinsurer-btn');

                $table.on('click', '.edit-reinsurer-btn', function(e) {
                    e.preventDefault();
                    const index = $(this).data('index');
                    editReinsurer(index);
                });

                $table.on('click', '.remove-reinsurer-btn', function(e) {
                    e.preventDefault();
                    const index = $(this).data('index');
                    removeReinsurer(index);
                });
            }

            function updateReinsurerCount() {
                $('#reinsurerCount').text(negotiationState.reinsurers.length);
            }

            function updateTotalShare() {
                negotiationState.totalShare = negotiationState.reinsurers.reduce(
                    (sum, r) => sum + parseFloat(r.written_share || 0),
                    0
                );

                $('#totalNegReinsurerShare').val(negotiationState.totalShare.toFixed(2));

                // Show warning if not 100%
                const $warning = $('.share-warning');
                $warning.remove();

                if (Math.abs(negotiationState.totalShare - 100) > 0.01 && negotiationState.reinsurers.length > 0) {
                    const remaining = (100 - negotiationState.totalShare).toFixed(2);
                    const warningHtml = `
                <div class="alert alert-warning share-warning mt-2" role="alert">
                    <i class="bx bx-error me-2"></i>
                    <strong>Warning:</strong> Total share is ${negotiationState.totalShare.toFixed(2)}%.
                    Remaining: <strong>${remaining}%</strong>
                </div>
            `;
                    $table.closest('.table-responsive').after(warningHtml);
                }
            }

            function populateReinsurersDropdown(reinsurers) {
                const $select = $('#propAvailableReinsurers');

                $select.empty().append('<option value="">Search and select reinsurer...</option>');

                if (Array.isArray(reinsurers) && reinsurers.length > 0) {
                    reinsurers.forEach(reinsurer => {
                        const optionText = reinsurer.name +
                            (reinsurer.country ? ` (${reinsurer.country})` : '');

                        const $option = $('<option></option>')
                            .val(reinsurer.id)
                            .text(optionText)
                            .data('reinsurer', reinsurer);

                        $select.append($option);
                    });
                }
            }

            $('#propAvailableReinsurers').select2({
                placeholder: 'Search and select reinsurer...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#negotiationModal'),
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    const searchTerm = params.term.toLowerCase();
                    const text = data.text.toLowerCase();

                    if (text.indexOf(searchTerm) > -1) {
                        return data;
                    }

                    return null;
                }
            });

            $('#addNegReinsurer').on('click', function(e) {
                e.preventDefault();
                addReinsurer();
            });

            $('#reinsurerNegShare').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addReinsurer();
                }
            });

            function resetNegotiationModal() {
                // Reset form
                $form[0].reset();
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();

                // Reset reinsurers
                negotiationState.reinsurers = [];
                negotiationState.totalShare = 0;

                if (reinsurerDataTable) {
                    reinsurerDataTable.clear().draw();
                }

                updateReinsurerCount();
                updateTotalShare();

                // Reset select2
                $('#propAvailableReinsurers').val(null).trigger('change');
                $('#reinsurerNegShare').val('');

                // Remove warnings
                $('.share-warning').remove();
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

                // Currency validation
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

                // Percentage validation
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

                // Email validation
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

            $form.on("input blur", ".form-inputs", function() {
                validateField($(this));
            });

            $modal.on('shown.bs.modal', function() {
                if (!negotiationState.isInitialized) {
                    // initializeReinsurerTable();
                    negotiationState.isInitialized = true;
                }

                // Load available reinsurers
                loadAvailableReinsurers();
            });

            $modal.on('hidden.bs.modal', function() {
                resetNegotiationModal();
            });

            function loadAvailableReinsurers() {
                // Check if we need to load reinsurers from server
                if ($('#propAvailableReinsurers option').length <= 1) {
                    //{{-- $.ajax({
                    //     url: '/api/reinsurers', // Update with your actual endpoint
                    //     method: 'GET',
                    //     success: function(response) {
                    //         if (response.success && Array.isArray(response.data)) {
                    //             populateReinsurersDropdown(response.data);
                    //         }
                    //     },
                    //     error: function(xhr, status, error) {
                    //         console.error('Failed to load reinsurers:', error);
                    //     }
                    // }); --}}
                }
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

            // initializeReinsurerTable();
        });
    </script>
@endpush
