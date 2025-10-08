{{-- <!-- Proposal Stage Modal -->
<div id="proposalModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticPropoalStageLabel" aria-hidden="true" role="dialog" aria-hidden="true">
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
                                    <h6 class="mb-2 fw-medium" style="font-size: 19px;"><i
                                            class="bx bx-building me-1"></i><span class="insured-name-display"></span>
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
                                                Last Contact Date
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="">
                                                <input type="date" class="form-inputs" value="2025-09-18" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                                    change="this.value=numberWithCommas(this.value)" readonly>
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
                                                    change="this.value=numberWithCommas(this.value)" readonly>
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
                                                <select class="sel" id="availableNegReinsurers"
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
                                                    min="0.01" max="100">
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
                                        <i class="bx bx-building me-1"></i>Selected Reinsurers
                                        <span class="badge bg-primary ms-2" id="reinsurerCount">0</span>
                                    </h6>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-stripped selected-reinsurers-table"
                                            id="reinsurersNegTable">
                                            <thead class="table-d">
                                                <tr>
                                                    <th style="width: 70%">Reinsurer</th>
                                                    <th style="width: 20%">Written Share (%)</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reinsurersTableBody">
                                            </tbody>
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
                                    <div id="documentsSubtitle" class="ms-3 fs-12 opacity-75"
                                        style="margin-left: 9px;">
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
                REQUIRED_FIELDS: ["total_sum_insured", "premium"],
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

            const fileIcons = {
                pdf: "bx-file-pdf",
                doc: "bx-file-doc",
                docx: "bx-file-doc",
                xls: "bx-file-excel",
                xlsx: "bx-file-excel",
                jpg: "bx-image",
                jpeg: "bx-image",
                png: "bx-image",
                default: "bx-file",
            };

            let uploadedFiles = {};
            let selectedReinsurers = new Set();
            let bdReinsurers = {};

            const $proposalForm = $("#proposalForm");
            const $proposalModal = $("#proposalModal");
            const $totalNegReinsurerShare = $("#totalNegReinsurerShare");
            const $availableNegReinsurers = $("#availableNegReinsurers");
            const $reinsurerNegShare = $("#reinsurerNegShare");
            const $opportunityNegId = $("#opportunityNegId");
            const $reinsurersTable = $("#reinsurersNegTable");
            const $reinsurerCount = $("#reinsurerCount");

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            const table = $reinsurersTable.DataTable({
                responsive: true,
                pageLength: 10,
                paging: false,
                searching: false,
                info: false,
                order: [
                    [0, "asc"]
                ],
                language: {
                    emptyTable: "No reinsurers selected yet. Add reinsurers using the form above.",
                },
                columnDefs: [{
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    className: "text-start",
                }],
            });

            $availableNegReinsurers.select2({
                placeholder: "Search and select reinsurer...",
                allowClear: true,
                minimumInputLength: 0,
                width: "100%",
                dropdownParent: $proposalModal,
                ajax: {
                    url: "/pipeline/search_reinsurers",
                    method: "GET",
                    dataType: "json",
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term || "",
                            page: params.page || 1,
                        };
                    },
                    processResults: function(data, params) {
                        bdReinsurers = data.results || [];
                        return {
                            results: data.results || [],
                            pagination: {
                                more: data.pagination && data.pagination.more,
                            },
                        };
                    },
                    cache: true,
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        showAlert("Failed to load reinsurers", "error");
                    },
                },
                templateResult: function(reinsurer) {
                    if (reinsurer.loading) return reinsurer.text;
                    if (!reinsurer.name) return reinsurer.text;

                    return `
                        <div class="reinsurer-option">
                            <div><strong>${reinsurer.name}</strong>
                                <span class="badge bg-secondary ms-1">${reinsurer.rating || 'N/A'}</span>
                            </div>
                            <div><small class="text-muted">${reinsurer.country || ''} | Email: ${reinsurer.email || 'N/A'}</small></div>
                        </div>
                    `;
                },
                templateSelection: function(reinsurer) {
                    if (!reinsurer.id) return reinsurer.text;

                    let option = $availableNegReinsurers.find(`option[value='${reinsurer.id}']`);
                    option.attr("data-name", reinsurer.name || "");
                    option.attr("data-email", reinsurer.email || "");
                    option.attr("data-country", reinsurer.country || "");

                    return `${reinsurer.name || 'Unknown'} (${reinsurer.email || 'No email'}) - ${reinsurer.country || 'Unknown'}`;
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
            });

            $("#addNegReinsurer").on("click", function() {
                const selectedOption = $availableNegReinsurers.find("option:selected");
                const writtenSharePercent = parseFloat($reinsurerNegShare.val());

                if (!selectedOption.val()) {
                    showAlert("Please select a reinsurer from the dropdown", "warning");
                    return;
                }

                if (!writtenSharePercent || writtenSharePercent <= 0 || writtenSharePercent > 100) {
                    showAlert("Please enter a valid written share percentage between 0.01% and 100%",
                        "error");
                    $reinsurerNegShare.focus();
                    return;
                }

                let currentTotalPlacedShares = calculateTotalPlacedShares();

                if (currentTotalPlacedShares + writtenSharePercent > 100) {
                    const remainingCapacity = 100 - currentTotalPlacedShares;
                    showAlert(
                        `Maximum available share is ${remainingCapacity.toFixed(2)}%. Total placed shares cannot exceed 100%.`,
                        "warning");
                    return;
                }

                if (selectedReinsurers.has(selectedOption.val())) {
                    showAlert("This reinsurer has already been added to the list", "info");
                    return;
                }

                const reinsurerData = {
                    id: selectedOption.val(),
                    name: selectedOption.data("name") || "Unknown",
                    email: selectedOption.data("email") || "N/A",
                    country: selectedOption.data("country") || "Unknown",
                    writtenShare: writtenSharePercent,
                };

                addReinsurerToTable(reinsurerData);
                updateSharesDisplay();
            });

            $totalNegReinsurerShare.on("input", function() {
                const value = parseFloat($(this).val());

                if (value > 100) {
                    $(this).val("100");
                    showAlert("Total Written Share cannot exceed 100%. Value has been adjusted to 100%.",
                        "warning");
                }

                if (value < 0) {
                    $(this).val("0");
                }
            });

            $(document).on("click", ".remove-reinsurer", function() {
                const reinsurerID = $(this).data("reinsurer-id");
                const row = $(this).closest("tr");
                const reinsurerName = row.find("td:first .fw-medium").text();

                Swal.fire({
                    title: "Remove Reinsurer?",
                    text: `Are you sure you want to remove ${reinsurerName} from the list?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, remove it!",
                    cancelButtonText: "Cancel",
                }).then((result) => {
                    if (result.isConfirmed) {
                        removeReinsurerFromTable(reinsurerID, row, reinsurerName);
                    }
                });
            });

            $(document).on("click", ".contacts-reinsurer", function(e) {
                e.preventDefault();

                const reinsurerID = $(this).data("reinsurer-id");
                const row = $(this).closest("tr");
                let reinsurerName = row.find("td:first .fw-medium").text();
                const opportunityId = $opportunityNegId.val();

                if (!reinsurerID) {
                    showAlert("Reinsurer ID not found", "error");
                    return;
                }

                const originalHtml = $(this).html();
                const $btn = $(this);
                $btn.html('<i class="bx bx-loader bx-spin"></i>').prop("disabled", true);

                $.ajax({
                    url: `/reinsurers/${reinsurerID}/contacts`,
                    method: "POST",
                    data: {
                        opportunityNegId: opportunityId,
                        reinsurer_id: reinsurerID,
                    },
                    success: function(response) {
                        if (response.success) {
                            reinsurerName = response?.data?.reinsurer?.name || reinsurerName;
                            populateContactsModal(response.data, reinsurerName);
                            $proposalModal.modal("hide");
                            $("#contactsModal").modal("show");
                        } else {
                            showAlert(response.message || "Failed to fetch contacts", "error");
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "Failed to fetch reinsurer contacts";

                        if (xhr.status === 404) {
                            errorMessage = "Reinsurer contacts not found";
                        } else if (xhr.status === 403) {
                            errorMessage = "Access denied to reinsurer contacts";
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        showAlert(errorMessage, "error");
                    },
                    complete: function() {
                        $btn.html(originalHtml).prop("disabled", false);
                    },
                });
            });

            $proposalForm.on("input blur", ".form-inputs", function() {
                validateField($(this));
            });

            $proposalForm.on("submit", function(e) {
                e.preventDefault();

                const validation = validateProposalForm();

                if (!validation.isValid) {
                    displayValidationErrors(validation.errors);
                    return false;
                }

                submitNegotiationForm();
            });

            $("#updateCategoryForm").on("submit", function(e) {
                e.preventDefault();

                const $categorySelect = $("#category_type");
                const $submitBtn = $("#updateCategorySubmitBtn");

                if (!$categorySelect.val()) {
                    $categorySelect.addClass("is-invalid").focus();
                    return false;
                }

                $categorySelect.removeClass("is-invalid");
                $submitBtn.addClass("btn-loading")
                    .html('<span class="">Updating...</span>')
                    .prop("disabled", true);

                const formData = new FormData(this);

                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showAlert("Category type updated successfully!", "success");
                            setTimeout(() => location.reload(), 2000);
                        }
                        $("#updateCategoryTypeModal").modal("hide");
                    },
                    error: function(xhr) {
                        let errorMessage = "An error occurred while updating the category.";

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                "<br>");
                        }

                        showAlert(errorMessage, "error");
                    },
                    complete: function() {
                        $submitBtn.removeClass("btn-loading")
                            .html('<i class="bi bi-check-circle me-1"></i>Update Category')
                            .prop("disabled", false);
                    },
                });
            });

            $(document).on("click", "#submitContactModal", function() {
                const contacts = [];

                const primaryData = {
                    id: parseInt($("#primary-contacts .primary-contact_id").val()),
                    name: $("#primary-contacts .primary-name").val(),
                    email: $("#primary-contacts .primary-email").val(),
                    cc_email: false,
                    is_primary: true,
                };

                if (primaryData.name || primaryData.email) {
                    contacts.push(primaryData);
                }

                $("#departmentContacts .contact-item").each(function() {
                    const contactData = {
                        id: parseInt($(this).data("contact-id")),
                        name: $(this).find(".contact-name").val().trim(),
                        email: $(this).find(".contact-email").val().trim(),
                        cc_email: $(this).find(".mailc-checkbox").is(":checked"),
                        is_primary: false,
                    };

                    if (contactData.name || contactData.email) {
                        contacts.push(contactData);
                    }
                });

                if (contacts.length === 0) {
                    showAlert("Please add at least one contact.", "warning");
                    return;
                }

                const $submitBtn = $(this);
                $submitBtn.prop("disabled", true);
                const opportunityNegId = $opportunityNegId.val();

                $.ajax({
                    url: "/reinsurers/contacts/update",
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        opportunityNegId: opportunityNegId,
                        contacts: contacts
                    }),
                    success: function(response) {
                        if (response.success) {
                            showAlert("Contact information has been updated.", "success");
                            $("#contactsModal").modal("hide");
                            $proposalModal.modal("show");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Contact update error:", error);
                        showAlert("Failed to update contacts", "error");
                    },
                    complete: function() {
                        $submitBtn.prop("disabled", false);
                    }
                });
            });

            // Modal Event Handlers
            $("#contactsModal").on("hidden.bs.modal", function() {
                $proposalModal.modal("show");
            });

            $proposalModal.on("shown.bs.modal", function() {
                $proposalForm.find(".is-invalid").removeClass("is-invalid");
                $proposalForm.find(".invalid-feedback").remove();
                $proposalForm.find(".reinsurer-validation-error").remove();
            });

            $("#updateCategoryTypeModal").on("hidden.bs.modal", function() {
                resetProposalModal();
            });

            $proposalModal.on("click", "button[data-bs-dismiss='modal']", function() {
                resetProposalModal();
            });

            function calculateTotalPlacedShares() {
                let total = 0;
                table.rows().every(function() {
                    const row = $(this.node());
                    const writtenShare = parseFloat(row.attr("data-written-share")) || 0;
                    total += writtenShare;
                });
                return total;
            }

            function addReinsurerToTable(reinsurerData) {
                const rowHtml = `
                    <tr data-reinsurer-id="${reinsurerData.id}" data-written-share="${reinsurerData.writtenShare}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="fw-medium">${reinsurerData.name}</div>
                                    <small class="text-muted">(${reinsurerData.email}) - ${reinsurerData.country}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-start">
                            <div class="share-display">
                                <strong>${reinsurerData.writtenShare.toFixed(2)}%</strong>
                            </div>
                        </td>
                        <td class="text-start">
                            <button type="button" class="btn btn-primary btn-sm contacts-reinsurer"
                                    data-reinsurer-id="${reinsurerData.id}"
                                    title="Contacts">
                                <i class="bx bx-book"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm remove-reinsurer"
                                    data-reinsurer-id="${reinsurerData.id}"
                                    title="Remove Reinsurer">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                try {
                    table.row.add($(rowHtml)).draw();
                    selectedReinsurers.add(reinsurerData.id);
                    updateReinsurerCount();
                    resetReinsurerForm();
                    toggleTotalWrittenShareField();

                    showAlert(
                        `${reinsurerData.name} has been successfully added with ${reinsurerData.writtenShare.toFixed(2)}% written share.`,
                        "success");
                } catch (error) {
                    console.error("Error adding reinsurer to table:", error);
                    showAlert("Failed to add reinsurer to table", "error");
                }
            }

            function removeReinsurerFromTable(reinsurerID, row, reinsurerName) {
                try {
                    table.row(row).remove().draw();
                    selectedReinsurers.delete(reinsurerID.toString());
                    updateReinsurerCount();
                    updateSharesDisplay();
                    toggleTotalWrittenShareField();

                    showAlert(`${reinsurerName} has been removed from the list.`, "info");
                } catch (error) {
                    console.error("Error removing reinsurer:", error);
                    showAlert("Failed to remove reinsurer", "error");
                }
            }

            function updateSharesDisplay() {
                let totalPlacedShares = calculateTotalPlacedShares();
                let totalNegReinsurerShare = parseFloat($totalNegReinsurerShare.val()) || 0;
                const totalUnplacedShares = totalNegReinsurerShare - totalPlacedShares;

                let sharesDisplay = $(".total-shares-display");

                if (sharesDisplay.length === 0) {
                    const displayHtml = `
                        <div class="total-shares-display mt-3">
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
                                        style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $(".selected-reinsurers-section").append(displayHtml);
                    sharesDisplay = $(".total-shares-display");
                }

                const placedValueClass = totalPlacedShares === totalNegReinsurerShare ? "text-success" :
                    totalPlacedShares > totalNegReinsurerShare ? "text-danger" : "text-primary";

                sharesDisplay.find(".placed-value")
                    .removeClass("text-success text-danger text-primary text-warning")
                    .addClass(placedValueClass)
                    .text(`${totalPlacedShares.toFixed(2)}%`);

                const unplacedValueClass = totalUnplacedShares === 0 ? "text-success" :
                    totalUnplacedShares < 0 ? "text-danger" : "text-warning";

                sharesDisplay.find(".unplaced-value")
                    .removeClass("text-success text-danger text-primary text-warning")
                    .addClass(unplacedValueClass)
                    .text(`${totalUnplacedShares.toFixed(2)}%`);

                let progressWidth = 0;
                if (totalNegReinsurerShare > 0) {
                    progressWidth = Math.min((totalPlacedShares / totalNegReinsurerShare) * 100, 100);
                }

                const progressClass = totalPlacedShares === totalNegReinsurerShare ? "bg-success" :
                    totalPlacedShares > totalNegReinsurerShare ? "bg-danger" : "bg-primary";

                sharesDisplay.find(".placed-progress")
                    .removeClass("bg-success bg-danger bg-primary")
                    .addClass(progressClass)
                    .css("width", `${progressWidth}%`)
                    .attr("aria-valuenow", progressWidth);

                $("#retainedShareValue").val(totalUnplacedShares.toFixed(2));
            }

            function updateReinsurerCount() {
                $reinsurerCount.text(selectedReinsurers.size);
            }

            function resetReinsurerForm() {
                $availableNegReinsurers.val(null).trigger("change");
                $reinsurerNegShare.val("");
            }

            function toggleTotalWrittenShareField() {
                const reinsurerCount = selectedReinsurers.size;

                if (reinsurerCount > 0) {
                    $totalNegReinsurerShare.prop("disabled", true).css({
                        "background-color": "#e9ecef",
                        "cursor": "not-allowed",
                        "opacity": "0.6"
                    });
                } else {
                    $totalNegReinsurerShare.prop("disabled", false).css({
                        "background-color": "",
                        "cursor": "",
                        "opacity": ""
                    });
                }
            }

            function validateField($field) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const fieldValue = $field.val().trim();
                const isRequired = $field.prop("required") ||
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

                if ($field.closest(".currency-input").length ||
                    fieldName.includes("premium") ||
                    fieldName.includes("sum_insured")) {
                    if (!FIELD_VALIDATORS.currency.pattern.test(fieldValue.replace(/,/g, ""))) {
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

                if (fieldName.includes("rate") || fieldName.includes("Share")) {
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

                if ($field.attr("type") === "email" || fieldName.includes("email")) {
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

            function validateProposalForm() {
                let isFormValid = true;
                const errors = [];

                $proposalForm.find(".form-inputs").each(function() {
                    if (!validateField($(this))) {
                        isFormValid = false;
                        const fieldLabel = $(this).closest(".form-group")
                            .find("label").text().replace("*", "").trim();
                        errors.push(`${fieldLabel}: Please check the entered value`);
                    }
                });

                if (!validateReinsurerSelection()) {
                    isFormValid = false;
                    errors.push("<b>Reinsurer Selection:</b> Please add at least one reinsurer");
                }

                const requiredFiles = $proposalForm.find('input[type="file"][required]');
                const missingFiles = [];

                requiredFiles.each(function() {
                    const fileName = $(this).attr('name');
                    const fileInput = this;

                    if (!fileInput.files || fileInput.files.length === 0) {
                        const fieldLabel = $(this).closest(".form-group")
                            .find("label").text().replace("*", "").trim();
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

            function validateReinsurerSelection() {
                const reinsurerCount = selectedReinsurers.size;
                const $reinsurerSection = $("#reinsurer-info");

                $reinsurerSection.find(".reinsurer-validation-error").remove();

                if (reinsurerCount < VALIDATION_CONFIG.MIN_REINSURERS) {
                    const errorHtml = `
                <div class="alert alert-danger reinsurer-validation-error mt-2">
                    <i class="bx bx-error-circle me-2"></i>
                    At least ${VALIDATION_CONFIG.MIN_REINSURERS} reinsurer must be selected
                </div>
            `;
                    $reinsurerSection.append(errorHtml);
                    return false;
                }

                return true;
            }

            function displayValidationErrors(errors) {
                let errorHtml = '<ul class="m-0 p-0" style="text-align: start;">';
                errors.forEach((error) => {
                    errorHtml += `<li class="mb-2">${error}</li>`;
                });
                errorHtml += "</ul>";

                Swal.fire({
                    icon: "error",
                    title: "Validation Failed",
                    html: errorHtml,
                    confirmButtonColor: "#dc3545",
                });

                const $firstError = $proposalForm.find(".is-invalid").first();
                if ($firstError.length) {
                    $firstError[0].scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                    setTimeout(() => $firstError.focus(), 500);
                }
            }

            function submitNegotiationForm() {
                const $submitBtn = $proposalForm.find("button[type='submit']");
                const originalBtnContent = $submitBtn.html();

                $submitBtn.html('<i class="bx bx-loader-alt bx-spin me-1"></i> Sending Negotiation...')
                    .prop("disabled", true);

                const formData = prepareFormData();

                $.ajax({
                    url: $proposalForm.attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 30000,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Negotiation Sent Successfully!",
                                text: response.message || "Your negotiation has been submitted",
                                showConfirmButton: true,
                            }).then((result) => {
                                if (result.isConfirmed && response.data) {
                                    handleSendBDNotification(response);
                                } else {
                                    $proposalModal.modal("hide");
                                    resetProposalModal();
                                }
                            });
                        } else {
                            throw new Error(response.message || "Submission failed");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Negotiation submission error:", {
                            status: xhr.status,
                            error: error,
                        });

                        let errorMessage =
                            "An unexpected error occurred while sending the negotiation.";

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join("<br>");
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (status === "timeout") {
                            errorMessage =
                                "Request timed out. Please check your connection and try again.";
                        } else if (xhr.status === 0) {
                            errorMessage = "Network error. Please check your internet connection.";
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
            }

            function prepareFormData() {
                const formData = new FormData();

                $proposalForm.find("input:not([type='file']), select, textarea").each(function() {
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

                $proposalForm.find("input[type='file']").each(function() {
                    const $fileInput = $(this);
                    const name = $fileInput.attr("name");

                    if (name && this.files && this.files.length > 0) {
                        Array.from(this.files).forEach(file => {
                            formData.append('facultative_files[]', file);
                        });
                    }
                });

                const reinsurersNegData = [];
                let totalPlacedShares = 0;

                $reinsurersTable.find("tbody tr").each(function() {
                    const $row = $(this);
                    const writtenShare = parseFloat($row.attr("data-written-share")) || 0;
                    totalPlacedShares += writtenShare;

                    reinsurersNegData.push({
                        id: $row.data("reinsurer-id"),
                        written_share: writtenShare,
                    });
                });

                formData.append("reinsurers_data", JSON.stringify(reinsurersNegData));
                formData.append("total_placed_shares", totalPlacedShares.toFixed(2));
                formData.append("total_unplaced_shares", (100 - totalPlacedShares).toFixed(2));

                return formData;
            }

            function handleSendBDNotification(res) {
                if (!res.data || !res.data.opportunityNegId) {
                    console.error("Missing opportunity data");
                    return;
                }

                const opportunityId = res.data.opportunityNegId;

                const data = {
                    partners: res.data.partners || [],
                    contacts: res.data.contacts || [],
                    bdEmailTitle: res.data.stageTitle || "Negotiation"
                };

                prepareBDEmailModal(opportunityId, data);
                $proposalModal.modal("hide");
            }

            function prepareBDEmailModal(opportunityId, data) {
                try {
                    const $bdNotificationForm = $("#bdNotificationForm");

                    if (!data || !data.bdEmailTitle) {
                        console.error('Missing required data:', data);
                        return;
                    }

                    $bdNotificationForm.find('.modal-bd-title').text(`- ${data.bdEmailTitle}`);
                    $bdNotificationForm.find('#category').val(data.bdEmailTitle.toLowerCase()).trigger('change');

                    const $contactsSelect = $bdNotificationForm.find('#toContacts');
                    const $bccEmailSelect = $bdNotificationForm.find('#bccEmail');
                    const $ccEmailSelect = $bdNotificationForm.find('#ccEmail');

                    const resetSelect = ($select, placeholder) => {
                        $select.empty().append(`<option value="" disabled>${placeholder}</option>`);
                    };

                    resetSelect($contactsSelect, '--Select contacts--');
                    resetSelect($ccEmailSelect, '--Select CC emails--');
                    resetSelect($bccEmailSelect, '--Select BCC emails--');

                    const partnerEmails = [];
                    if (Array.isArray(data.partners) && data.partners.length > 0) {
                        data.partners.forEach(partner => {
                            if (partner.email) {
                                partnerEmails.push(partner.email);
                            }
                        });
                    }

                    $bdNotificationForm.find("#toEmail").val(partnerEmails);
                    $bdNotificationForm.find("#partnerToEmail").val(data.partners || []);

                    const primaryContacts = [];
                    const regularContacts = [];

                    if (Array.isArray(data.contacts) && data.contacts.length > 0) {
                        data.contacts.forEach(contact => {
                            const email = contact.email;
                            if (!email) return;

                            let optionText = contact.name ? `${contact.name} (${email})` : email;
                            if (contact.phone) optionText += ` - ${contact.phone}`;
                            if (contact.isPrimary) optionText += ' [Primary]';

                            const createOption = () => $('<option></option>')
                                .attr('value', email)
                                .text(optionText)
                                .data('contact-data', contact)
                                .data('is-primary', !!contact.isPrimary);

                            $contactsSelect.append(createOption());

                            if (!contact.isPrimary) {
                                $ccEmailSelect.append(createOption());
                                $bccEmailSelect.append(createOption());
                            }

                            if (contact.isPrimary) {
                                primaryContacts.push(email);
                            } else {
                                regularContacts.push(email);
                            }
                        });

                        setTimeout(() => {
                            if (primaryContacts.length > 0) {
                                $contactsSelect.val(primaryContacts).trigger('change');
                            } else if (regularContacts.length === 1) {
                                $contactsSelect.val(regularContacts[0]).trigger('change');
                            }

                            [$contactsSelect, $ccEmailSelect, $bccEmailSelect].forEach($select => {
                                if ($select.hasClass('select2-hidden-accessible')) {
                                    $select.trigger('change.select2');
                                }
                            });
                        }, 100);
                    }

                    $("#sendBDEmailModal").modal("show");

                } catch (error) {
                    console.error('Error in prepareBDEmailModal:', error);
                    showAlert("Failed to prepare email modal", "error");
                }
            }

            function populateContactsModal(contactData, reinsurerName) {
                $("#contactsModalLabel").html(
                    `<i class="bx bx-building me-1"></i>${reinsurerName} - Contact Management`
                );

                if (contactData.primary_contact) {
                    $("#primary-contacts .primary-name").val(contactData.primary_contact.name || "N/A");
                    $("#primary-contacts .primary-email").val(contactData.primary_contact.email || "N/A");
                    $("#primary-contacts .primary-contact_id").val(contactData.primary_contact.id || "");
                }

                $("#departmentContacts").empty();

                if (contactData.department_contacts && contactData.department_contacts.length > 0) {
                    contactData.department_contacts.forEach(function(contact, index) {
                        const contactHtml = createContactItemHtml(contact, index);
                        $("#departmentContacts").append(contactHtml);
                    });
                } else {
                    $("#departmentContacts").html(`
                <div class="text-center py-4">
                    <i class="bx bx-info-circle bx-2x text-muted mb-2 fs-15"></i>
                    <p class="text-muted">No department contacts found for this reinsurer.</p>
                </div>
            `);
                }
            }

            function createContactItemHtml(contact, index) {
                const showLabels = index === 0;

                return `
            <div class="contact-item rounded px-3 pb-1" data-contact-id="${contact.id || index}">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        ${showLabels ? '<label class="form-label fw-semibold mb-1">Contact Name</label>' : ""}
                        <input type="text" class="form-control-plaintext contact-name"
                            value="${contact.name || ""}" data-field="name">
                    </div>
                    <div class="col-md-6">
                        ${showLabels ? '<label class="form-label fw-semibold mb-1">Email</label>' : ""}
                        <input type="email" class="form-control-plaintext contact-email"
                            value="${contact.email || ""}" data-field="email">
                    </div>
                    <div class="col-md-2">
                        ${showLabels ? '<label class="form-label fw-semibold mb-1">CC Email</label>' : ""}
                        <div class="form-check mt-2 px-0">
                            <input class="form-check-input mailc-checkbox" type="checkbox"
                                ${contact.cc_email ? "checked" : ""} data-field="cc_email">
                            <label class="form-check-label cc-email-indicator">
                                <i class="bx bx-envelope envlope-ico"></i>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </div>
        `;
            }

            function resetProposalModal() {
                $proposalForm[0].reset();

                $proposalForm.find(".is-invalid").removeClass("is-invalid");
                $proposalForm.find(".is-v").removeClass("is-v");
                $proposalForm.find(".invalid-feedback").remove();
                $proposalForm.find(".reinsurer-validation-error").remove();

                table.clear().draw();
                selectedReinsurers.clear();
                updateReinsurerCount();

                $availableNegReinsurers.val(null).trigger("change");

                $totalNegReinsurerShare.val("");
                $reinsurerNegShare.val("");
                $("#retainedShareValue").val("");
                $("#totalPlacedShares").val("");
                $("#totalUnplacedShares").val("");

                $totalNegReinsurerShare.prop("disabled", false).css({
                    "background-color": "",
                    "cursor": "",
                    "opacity": ""
                });

                $(".total-shares-display").remove();

                $opportunityNegId.val("");
                $("#currentNegStage").val("");

                $(".slip-display").text("");
                $(".created_at-display").text("");
                $(".insured-name-display").text("");
                $(".insured-contact-name-display").text("");
                $(".insured-email-display").text("");
                $(".insured-phone-display").text("");

                $("#documentFields").empty().hide();
                $("#documentsSubtitle").html("");
                uploadedFiles = {};
            }

            function showAlert(message, type = "info") {
                const iconMap = {
                    success: "success",
                    warning: "warning",
                    error: "error",
                    info: "info",
                };

                const titleMap = {
                    success: "Success",
                    warning: "Warning",
                    error: "Error",
                    info: "Information",
                };

                Swal.fire({
                    icon: iconMap[type] || "info",
                    title: titleMap[type] || "Information",
                    text: message,
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: "top-end",
                });
            }

            class BreakdownEditor {
                constructor() {
                    this.quill = null;
                    this.maxCharacters = 5000;
                    this.isPreviewMode = false;
                    this.currentTextarea = null;
                    this.textareaId = null;
                    this.templates = {
                        standard: `
                            <h3>Standard Coverage Breakdown</h3>
                            <ul>
                                <li><strong>Building Structure:</strong> Coverage for physical damage to buildings</li>
                                <li><strong>Contents:</strong> Protection for business equipment and inventory</li>
                                <li><strong>Business Interruption:</strong> Loss of income coverage</li>
                                <li><strong>Public Liability:</strong> Third party claims protection</li>
                            </ul>
                            <p><em>All amounts subject to policy terms and conditions.</em></p>
                        `,
                        property: `
                            <h3>Property Insurance Coverage</h3>
                            <ol>
                                <li><strong>Real Estate Value:</strong> Market value of land and buildings</li>
                                <li><strong>Replacement Cost:</strong> Cost to rebuild at current prices</li>
                                <li><strong>Personal Property:</strong> Furniture, fixtures, and equipment</li>
                                <li><strong>Additional Living Expenses:</strong> Temporary accommodation costs</li>
                            </ol>
                            <blockquote>Coverage limits may vary based on property location and risk assessment.</blockquote>
                        `,
                    };

                    this.init();
                }

                init() {
                    this.setupEventListeners();
                    this.initializeModal();
                    this.enhanceTextarea();
                }

                enhanceTextarea() {
                    $("textarea.breakdown-textarea")
                        .addClass("editor-enabled")
                        .attr({
                            readonly: true
                        })
                        .css({
                            cursor: "pointer",
                            background: "linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%)",
                            border: "2px solid #e9ecef",
                            transition: "all 0.3s ease",
                        });
                }

                setupEventListeners() {
                    $(document).on("click", "textarea.breakdown-textarea", (e) => {
                        e.preventDefault();
                        const $textarea = $(e.currentTarget);
                        const textareaId = $textarea.attr('id');
                        this.openModal($textarea, textareaId);
                    });

                    $(document).on("click", ".template-btn", (e) => {
                        const template = $(e.target).data("template");
                        this.applyTemplate(template);
                    });

                    $("#saveBreakdownBtn").on("click", () => {
                        this.saveChanges();
                    });

                    $("#previewBtn").on("click", () => {
                        this.togglePreview();
                    });

                    $("#breakdownModal").on("show.bs.modal", () => {
                        this.cleanupEditor();
                    });

                    $("#breakdownModal").on("shown.bs.modal", () => {
                        this.initializeQuill();
                    });

                    $("#breakdownModal").on("hidden.bs.modal", () => {
                        this.cleanupEditor();
                        this.currentTextarea = null;
                        $proposalModal.modal("show");
                    });

                    $("#breakdownModal").on("hide.bs.modal", () => {
                        this.cleanupEditor();
                    });
                }

                initializeModal() {
                    if (!document.getElementById("breakdownModal")) {
                        console.error("Breakdown modal not found!");
                        return;
                    }
                    this.modal = new bootstrap.Modal(document.getElementById("breakdownModal"));
                }

                cleanupEditor() {
                    if (this.quill) {
                        try {
                            this.quill.off("text-change");
                            this.quill = null;
                        } catch (error) {
                            console.warn("Error removing Quill listeners:", error);
                        }
                    }

                    const container = document.getElementById("breakdownEditor");
                    if (container) {
                        container.innerHTML = "";
                        container.className = "";
                        container.removeAttribute("style");
                    }

                    const quillContainer = $(".quill-container");
                    quillContainer.find(".ql-toolbar").remove();
                    quillContainer.find(".ql-container").remove();
                }

                openModal($textarea, textareaId) {
                    if (!this.modal) return;

                    this.currentTextarea = $(`#${textareaId}Content`);
                    this.textareaId = textareaId;

                    const fieldLabel = $textarea.closest(".form-group")
                        .find("label").first().text().trim();

                    $("#breakdownModalLabel").html(
                        `<i class="bx bx-edit-alt me-2"></i>${fieldLabel || ""}`
                    );

                    $proposalModal.modal("hide");
                    this.showLoading();
                    this.modal.show();
                }

                showLoading() {
                    $("#loadingOverlay").addClass("show");
                }

                hideLoading() {
                    $("#loadingOverlay").removeClass("show");
                }

                initializeQuill() {
                    if (typeof Quill === "undefined") {
                        this.hideLoading();
                        console.error("Quill is not loaded");
                        return;
                    }

                    this.cleanupEditor();

                    const editorContainer = document.getElementById("breakdownEditor");
                    if (!editorContainer) {
                        this.hideLoading();
                        return;
                    }

                    editorContainer.innerHTML = "";
                    editorContainer.className = "";

                    setTimeout(() => {
                        const toolbarOptions = [
                            [{
                                header: [1, 2, 3, false]
                            }],
                            ["bold", "italic", "underline"],
                            [{
                                color: []
                            }, {
                                background: []
                            }],
                            [{
                                list: "ordered"
                            }, {
                                list: "bullet"
                            }],
                            [{
                                indent: "-1"
                            }, {
                                indent: "+1"
                            }],
                            [{
                                align: []
                            }],
                            ["link"],
                            ["table"],
                            ["clean"],
                        ];

                        try {
                            this.quill = new Quill("#breakdownEditor", {
                                theme: "snow",
                                modules: {
                                    toolbar: toolbarOptions
                                },
                            });

                            if (this.currentTextarea) {
                                const existingContent = this.currentTextarea.val();
                                if (existingContent && existingContent.trim()) {
                                    this.quill.root.innerHTML = existingContent;
                                }
                            }

                            this.quill.on("text-change", () => {
                                this.updateStatistics();
                                this.validateContent();
                            });

                            this.updateStatistics();
                        } catch (error) {
                            console.error("Error initializing Quill:", error);
                        }

                        this.hideLoading();
                    }, 500);
                }

                updateStatistics() {
                    if (!this.quill) return;

                    const text = this.quill.getText();
                    const charCount = text.trim().length;

                    const counter = $("#characterCounter");
                    counter.text(`${charCount} / ${this.maxCharacters} characters`);

                    counter.removeClass("warning danger");
                    if (charCount > this.maxCharacters * 0.9) {
                        counter.addClass("warning");
                    }
                    if (charCount > this.maxCharacters) {
                        counter.addClass("danger");
                    }
                }

                validateContent() {
                    if (!this.quill) return;

                    const text = this.quill.getText();
                    const saveBtn = $("#saveBreakdownBtn");

                    if (text.length > this.maxCharacters) {
                        saveBtn.addClass("disabled");
                        saveBtn.attr("title", "Content exceeds maximum character limit");
                    } else {
                        saveBtn.removeClass("disabled");
                        saveBtn.removeAttr("title");
                    }
                }

                applyTemplate(templateName) {
                    if (!this.quill) return;

                    if (templateName === "clear") {
                        Swal.fire({
                            title: "Clear Content?",
                            text: "This will remove all current content. Continue?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#3085d6",
                            confirmButtonText: "Yes, clear it!",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.quill.setContents([]);
                                this.showToast("Content cleared", "info");
                            }
                        });
                        return;
                    }

                    if (this.templates[templateName]) {
                        Swal.fire({
                            title: "Apply Template?",
                            text: "This will replace your current content with the selected template.",
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#6c757d",
                            confirmButtonText: "Apply Template",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.quill.root.innerHTML = this.templates[templateName];
                                this.updateStatistics();
                                this.showToast("Template applied successfully", "success");
                            }
                        });
                    }
                }

                togglePreview() {
                    const container = $(".quill-container");
                    const btn = $("#previewBtn");

                    if (this.isPreviewMode) {
                        container.removeClass("preview-mode");
                        btn.html('<i class="bx bx-show me-1"></i>Preview');
                        this.isPreviewMode = false;
                    } else {
                        container.addClass("preview-mode");
                        btn.html('<i class="bx bx-edit me-1"></i>Edit');
                        this.isPreviewMode = true;
                    }
                }

                saveChanges() {
                    if (!this.quill || !this.currentTextarea) {
                        this.showToast("No content to save", "error");
                        return;
                    }

                    const text = this.quill.getText().trim();
                    const html = this.quill.root.innerHTML;

                    if (text.length > this.maxCharacters) {
                        this.showToast("Content exceeds maximum character limit", "error");
                        return;
                    }

                    const saveBtn = $("#saveBreakdownBtn");
                    const originalText = saveBtn.html();

                    saveBtn.html('<i class="bx bx-loader-alt bx-spin me-1"></i>Saving...')
                        .prop("disabled", true);

                    const formData = new FormData();
                    formData.append("breakdown_content", html);
                    formData.append("breakdown_title", this.textareaId);
                    formData.append("_update", true);
                    formData.append("opportunityNegId", $opportunityNegId.val() || "");
                    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

                    $.ajax({
                        url: $proposalForm.attr("action") || "/opportunities/update",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: (response) => {
                            if (response.success) {
                                const title = response.data.title;
                                const content = response.data.content;
                                const short_content = response.data.short_content;

                                const plainText = $('<div>').html(short_content).text();

                                $(`#${title}`).val(plainText);
                                $(`#${title}Content`).val(content);

                                this.modal.hide();
                                this.showToast("Saved successfully", "success");
                            } else {
                                throw new Error(response.message || "Save failed");
                            }
                        },
                        error: (xhr, status, error) => {
                            let errorMessage = "Failed to save breakdown";

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join(", ");
                            } else if (xhr.status === 0) {
                                errorMessage = "Network error - please check your connection";
                            }

                            this.showToast(errorMessage, "error");
                        },
                        complete: () => {
                            saveBtn.html(originalText).prop("disabled", false);
                        },
                    });
                }

                showToast(message, type = "info") {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    Toast.fire({
                        icon: type,
                        title: message,
                    });
                }
            }

            let breakdownEditor;
            try {
                breakdownEditor = new BreakdownEditor();
            } catch (error) {
                console.error("Failed to initialize BreakdownEditor:", error);
                showAlert("Failed to initialize the editor. Please refresh the page.", "error");
            }
        });
    </script>
@endpush --}}
