    <div class="modal effect-scale md-wrapper" id="addClauseModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticPolicyLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="clausesForm">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <div class="modal-header">
                        <h5 class="modal-title dc-modal-title" id="staticPolicyLabel">Policy Clauses</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label fs-14">Select Clauses</label>
                                <select class="form-inputs select2" id="clauses" name="clauses[]" multiple="multiple"
                                    placeholder="Select clauses">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="clauses-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
