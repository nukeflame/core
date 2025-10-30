<!-- BD Facultative Final Stage Modal -->
<div class="modal fade effect-scale md-wrapper" id="finalStageModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticUpdateCategoryTypeModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="staticUpdateCategoryTypeModalLabel">
                    <i class="bi bi-pencil-square"></i>
                    BD Facultative Final Stage
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('update.opp.status') }}" method="POST" id="finStageForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="opportunity_id" class="opportunity_id" id="finOpportunityId" />
                    <input type="hidden" class="current_stage" id="finCurrentStage" name="current_stage" />
                    <input type="hidden" name="class_code" class="class_code" id="finClassCode">
                    <input type="hidden" name="class_group_code" class="class_group_code" id="finClassGroupCode">

                    <div class="mb-4">
                        <label for="finStageLabel" class="form-label">
                            Final Stage Status<span class="required-asterisk">*</span>
                        </label>
                        <select class="form-inputs select2" name="stageSatus" id="finStageLabel"
                            aria-describedby="statusTypeHelp">
                            <option value="" disabled selected>Select final stage status</option>
                            <option value="won">Closed Won - BD Facultative</option>
                            <option value="lost">Closed Lost - BD Facultative</option>
                        </select>
                        <div class="invalid-feedback" id="category_type_error"></div>
                        <div class="form-text" id="statusTypeHelp">
                            Please select the final stage status for this BD Facultative opportunity.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div></div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" id="finStageBtn">
                                <i class="bx bx-check-circle me-1"></i> Update Stage
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
            const $form = $("#finStageForm");
            const $finStageType = $("#finStageLabel");
            const $submitBtn = $("#finStageBtn");

            if ($.fn.select2) {
                $finStageType.select2({
                    dropdownParent: $('#finalStageModal'),
                    placeholder: 'Select final stage status',
                    allowClear: false,
                    width: '100%'
                });
            }

            $finStageType.on('change', function() {
                clearFieldError($(this));
            });

            $form.on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) return false;

                let originalBtnText = $submitBtn.html();
                $submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...'
                );

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
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Stage updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            console.log(response)
                            $('#finalStageModal').modal('hide');
                            $form[0].reset();

                            // Reload page or update UI as needed
                            if (typeof location !== 'undefined') {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while updating the stage.';
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            displayValidationErrors(xhr.responseJSON.errors);
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                            showFormError(errorMessage);
                        } else {
                            showFormError(errorMessage);
                        }
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            function validateForm() {
                clearAllErrors();
                let isValid = true;

                const opportunityId = $('#finOpportunityId').val();
                if (!opportunityId || opportunityId.trim() === '') {
                    showFieldError($('#finOpportunityId'), 'Opportunity ID is missing.');
                    isValid = false;
                }

                const stageStatus = $finStageType.val();
                if (!stageStatus || stageStatus.trim() === '') {
                    showFieldError($finStageType, 'Please select a final stage status.');
                    isValid = false;
                }

                return isValid;
            }

            function showFieldError($field, message) {
                const fieldId = $field.attr('id');
                const $errorDiv = $('#' + fieldId + '_error');

                $field.addClass('is-invalid');
                if ($errorDiv.length) {
                    $errorDiv.text(message).show();
                } else {
                    $('<div class="invalid-feedback d-block" id="' + fieldId + '_error">' + message + '</div>')
                        .insertAfter($field);
                }
            }

            function clearFieldError($field) {
                const fieldId = $field.attr('id');
                const $errorDiv = $('#' + fieldId + '_error');

                $field.removeClass('is-invalid');
                $errorDiv.text('').hide();
            }

            function clearAllErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('').hide();
            }

            function displayValidationErrors(errors) {
                $.each(errors, function(field, messages) {
                    const $field = $('[name="' + field + '"]');
                    if ($field.length) showFieldError($field, messages[0]);
                });
            }

            function showFormError(message) {
                const errorMessage = $('<div class="alert alert-danger mt-2" role="alert">' + message + '</div>');
                $form.prepend(errorMessage);
                setTimeout(() => errorMessage.fadeOut(() => errorMessage.remove()), 3000);
            }

            function prepareFormData() {
                const formData = new FormData();
                $form.find("input:not([type='file']), select, textarea").each(function() {
                    const $el = $(this);
                    const name = $el.attr("name");
                    const type = $el.attr("type");

                    if (!name) return;

                    if ((type === "checkbox" || type === "radio") && !$el.is(":checked")) return;

                    let value = $el.val();
                    if (value !== null && value !== "") {
                        if (name.includes('sum_insured') || name.includes('premium')) {
                            value = value.replace(/,/g, '');
                        }
                        formData.append(name, value);
                    }
                });

                if (typeof negotiationState !== 'undefined') {
                    formData.append("reinsurers_data", JSON.stringify(negotiationState.reinsurers || []));
                    formData.append("total_placed_shares", (negotiationState.totalShare || 0).toFixed(2));
                    formData.append("total_unplaced_shares", (100 - (negotiationState.totalShare || 0)).toFixed(2));
                }

                return formData;
            }

            $('#finalStageModal').on('hidden.bs.modal', function() {
                $form[0].reset();
                $('#finOpportunityId').val('');
                if ($.fn.select2) $finStageType.val('').trigger('change');
                clearAllErrors();
            });

            window.openFinalStageModal = function(opportunityId) {
                if (!opportunityId) {
                    showFormError('Invalid opportunity ID.');
                    return;
                }
                $('#finOpportunityId').val(opportunityId);
                $('#finalStageModal').modal('show');
            };
        });
    </script>
@endpush
