<div class="modal effect-scale md-wrapper verify-modal-wrapper" id="verificationModal" tabindex="-1"
    aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="verifyForm" action="{{ route('approvals.send-for-approval') }}"
                data-post-url="{{ route('approvals.send-for-approval') }}">
                @csrf
                <input type="hidden"name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
                <input type="hidden" name="process" value="{{ $process?->id ?? '' }}" />
                <input type="hidden" name="process_action" value="{{ $verifyprocessAction?->id ?? '' }}" />

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title text-white mb-1" id="verificationModalLabel">
                            <i class="ri-user-search-line me-2"></i>Send to Verifier
                        </h5>
                        <small class="text-white-50">
                            Cover: {{ $coverReg->cover_no ?? 'N/A' }} |
                            Endorsement: {{ $coverReg->endorsement_no ?? 'N/A' }}
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="approver" class="form-label required">
                                <i class="ri-user-line me-1"></i>Approver
                            </label>
                            <div class="verify-modal-card">
                                <select name="approver" id="approver" class="form-select select2" required>
                                    <option value="">--Select Approver--</option>
                                    @forelse ($verifiers ?? [] as $verifier)
                                        <option value="{{ $verifier->id }}" data-email="{{ $verifier->email ?? '' }}"
                                            data-role="{{ $verifier->role ?? '' }}">
                                            {{ $verifier->name }}
                                            @if (isset($verifier->email))
                                                ({{ $verifier->email }})
                                            @endif
                                        </option>
                                    @empty
                                        <option value="" disabled>No verifiers available</option>
                                    @endforelse
                                </select>
                                <div class="invalid-feedback">
                                    Please select an approver
                                </div>
                                @if (empty($verifiers) || count($verifiers) === 0)
                                    <small class="text-danger">
                                        <i class="ri-alert-line"></i> No verifiers configured for this process
                                    </small>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="priority" class="form-label required">
                                <i class="ri-flag-line me-1"></i>Priority
                            </label>
                            <div class="verify-modal-card">
                                <select name="priority" id="priority" class="form-select select2" required>
                                    <option value="">--Select Priority--</option>
                                    <option value="critical" class="text-danger">
                                        <i class="ri-alert-fill"></i> Critical
                                    </option>
                                    <option value="high" class="text-warning">High</option>
                                    <option value="medium" class="text-info">Medium</option>
                                    <option value="low" class="text-success" selected>Low</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a priority level
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="ri-information-line"></i> Critical/High priority will notify immediately
                            </small>
                        </div>

                        <div class="col-md-12">
                            <label for="verify-comment" class="form-label required">
                                <i class="ri-message-3-line me-1"></i>Comment
                            </label>
                            <textarea name="comment" id="verify-comment" rows="4" class="form-control form-control-sm resize-none color-blk"
                                placeholder="Provide details about why this cover needs verification..." required minlength="7" maxlength="500"></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">
                                    <i class="ri-information-line"></i> Minimum 7 characters
                                </small>
                                <small class="text-muted" id="comment-counter">0/500</small>
                            </div>
                            <div class="invalid-feedback fs-12">
                                Please provide a comment (minimum 7 characters)
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="verify-attachments" class="form-label">
                                <i class="ri-attachment-line me-1"></i>Attachments (Optional)
                            </label>
                            <input type="file" class="form-control form-control-sm" id="verify-attachments"
                                name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <small class="text-muted">
                                Max 5MB per file. Allowed: PDF, Word, Excel, Images
                            </small>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="validation-errors" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul id="error-list" class="mb-0 mt-2"></ul>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancel
                    </button>
                    <button type="submit" id="verify-save-btn"
                        class="btn btn-primary btn-sm btn-wave waves-effect waves-light">
                        <i class="ri-send-plane-line me-1"></i>
                        <span class="btn-text">Submit for Verification</span>
                        <span class="spinner-border spinner-border-sm ms-1 d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            if ($.fn.select2) {
                $('#approver, #priority').select2({
                    dropdownParent: $('#verificationModal'),
                    width: '100%',
                    placeholder: function() {
                        return $(this).find('option:first').text();
                    }
                });
            }

            $('#verify-comment').on('input', function() {
                const length = $(this).val().length;
                $('#comment-counter').text(`${length}/500`);

                if (length > 500) {
                    $(this).val($(this).val().substring(0, 500));
                    $('#comment-counter').text('500/500').addClass('text-danger');
                } else {
                    $('#comment-counter').removeClass('text-danger');
                }
            });

            $('#verificationModal').on('show.bs.modal', function() {
                $('#verifyForm')[0].reset();
                $('#approver, #priority').val('').trigger('change');
                $('#comment-counter').text('0/500');
                $('#validation-errors').addClass('d-none');
            });

            $('#priority').on('change', function() {
                const priority = $(this).val();
                const card = $(this).closest('.verify-modal-card');

                card.removeClass('border-danger border-warning border-info border-success');

                switch (priority) {
                    case 'critical':
                        card.addClass('border-danger border-2');
                        break;
                    case 'high':
                        card.addClass('border-warning border-2');
                        break;
                    case 'medium':
                        card.addClass('border-info');
                        break;
                    case 'low':
                        card.addClass('border-success');
                        break;
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .verify-modal-card {
            transition: all 0.3s ease;
        }

        .verify-modal-card.border-danger {
            border-left: 4px solid #dc3545 !important;
            padding-left: 8px;
        }

        .verify-modal-card.border-warning {
            border-left: 4px solid #ffc107 !important;
            padding-left: 8px;
        }

        .verify-modal-card.border-info {
            border-left: 4px solid #0dcaf0 !important;
            padding-left: 8px;
        }

        .verify-modal-card.border-success {
            border-left: 4px solid #198754 !important;
            padding-left: 8px;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        #verificationModal .modal-dialog {
            max-width: 700px;
        }
    </style>
@endpush
