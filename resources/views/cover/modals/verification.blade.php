    <div class="modal effect-scale md-wrapper verify-modal-wrapper" id="verificationModal" tabindex="-1"
        aria-labelledby="verificationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="verifyForm" action="{{ route('approvals.send-for-approval') }}">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
                    <input type="hidden" name="process" value="{{ $process?->id ?? '' }}" />
                    <input type="hidden" name="process_action" value="{{ $verifyprocessAction?->id ?? '' }}" />
                    <div class="modal-header">
                        <h5 class="modal-title" id="verificationModalLabel">Send to Verifier</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="approver" class="form-label">Approver</label>
                                <div class="verify-modal-card">
                                    <select name="approver" id="approver" class="form-inputs select2" required>
                                        <option value="">--Select Approver--</option>
                                        @foreach ($verifiers as $verifier)
                                            <option value="{{ $verifier->id }}">{{ $verifier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority</label>
                                <div class="verify-modal-card">
                                    <select name="priority" id="priority" class="form-inputs select2" required>
                                        <option value="">--Select Priority--</option>
                                        <option value="critical">Critical</option>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low" selected>Low</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="verify-comment" class="form-label">Comment</label>
                                <textarea name="comment" id="verify-comment" rows="4" class="form-control form-control-sm resize-none color-blk"
                                    required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="verify-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
