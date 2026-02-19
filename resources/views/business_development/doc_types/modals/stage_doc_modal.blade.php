<div class="modal effect-scale md-wrapper" id="stageDocModal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="stageDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form id="stageDocForm">
                @csrf
                <input type="hidden" name="id" id="sd-id">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="stageDocModalLabel">
                        <i class="bx bx-plus-circle me-2"></i>Add Stage Document
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="sd-stage" class="form-label">Stage <span class="text-danger">*</span></label>
                            <select id="sd-stage" name="stage" class="form-select" required>
                                <option value="">Select Stage</option>
                                <option value="lead">Lead</option>
                                <option value="proposal">Proposal</option>
                                <option value="negotiation">Negotiation</option>
                                <option value="final">Final</option>
                            </select>
                            <small class="text-danger stage-doc-error" data-error-for="stage"></small>
                        </div>

                        <div class="col-md-6">
                            <label for="sd-doc-type" class="form-label">Doc Type <span
                                    class="text-danger">*</span></label>
                            <select id="sd-doc-type" name="doc_type" class="form-select" required>
                                <option value="">Select Doc Type</option>
                                @foreach ($documents as $document)
                                    <option value="{{ $document->id }}">{{ $document->doc_type }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger stage-doc-error" data-error-for="doc_type"></small>
                        </div>

                        <div class="col-md-6">
                            <label for="sd-mandatory" class="form-label">Mandatory <span
                                    class="text-danger">*</span></label>
                            <select id="sd-mandatory" name="mandatory" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                            <small class="text-danger stage-doc-error" data-error-for="mandatory"></small>
                        </div>

                        <div class="col-md-6">
                            <label for="sd-category-type" class="form-label">Category Type <span
                                    class="text-danger">*</span></label>
                            <select id="sd-category-type" name="category_type" class="form-select" required>
                                <option value="">Select Category</option>
                                <option value="1">Quotation</option>
                                <option value="2">Facultative Offer</option>
                            </select>
                            <small class="text-danger stage-doc-error" data-error-for="category_type"></small>
                        </div>

                        <div class="col-md-12">
                            <label for="sd-type-of-bus" class="form-label">Business Type <span
                                    class="text-danger">*</span></label>
                            <select id="sd-type-of-bus" name="type_of_bus[]" class="form-select" multiple required>
                                @foreach ($typesOfBus as $busType)
                                    <option value="{{ $busType->bus_type_id }}">{{ $busType->bus_type_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger stage-doc-error" data-error-for="type_of_bus"></small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="stageDocSaveBtn">
                        <i class="bi bi-save me-1"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
