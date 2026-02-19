<div class="modal effect-scale md-wrapper" id="docTypeModal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="docTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form id="docTypeForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="dt-id">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="docTypeModalLabel">
                        <i class="bx bx-plus-circle me-2"></i>Add Document Type
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="dt-code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" id="dt-code" name="code"
                                maxlength="50" required>
                            <small class="text-danger doc-type-error" data-error-for="code"></small>
                        </div>

                        <div class="col-md-4">
                            <label for="dt-country" class="form-label">Country <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="dt-country" name="country" maxlength="100"
                                placeholder="All" value="All" required>
                            <small class="text-danger doc-type-error" data-error-for="country"></small>
                        </div>

                        <div class="col-md-2">
                            <label for="dt-is-required" class="form-label">Required <span
                                    class="text-danger">*</span></label>
                            <select id="dt-is-required" name="is_required" class="form-select" required>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                            <small class="text-danger doc-type-error" data-error-for="is_required"></small>
                        </div>

                        <div class="col-md-2">
                            <label for="dt-is-default" class="form-label">Default <span
                                    class="text-danger">*</span></label>
                            <select id="dt-is-default" name="is_default" class="form-select" required>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                            <small class="text-danger doc-type-error" data-error-for="is_default"></small>
                        </div>

                        <div class="col-md-12">
                            <label for="dt-doc-type" class="form-label">Document Type <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="dt-doc-type" name="doc_type" maxlength="100"
                                required>
                            <small class="text-danger doc-type-error" data-error-for="doc_type"></small>
                        </div>

                        <div class="col-md-12">
                            <label for="dt-description" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="dt-description" name="description"
                                maxlength="100" required>
                            <small class="text-danger doc-type-error" data-error-for="description"></small>
                        </div>

                        <div class="col-12" id="cedant_file_container">
                            <label class="form-label">Upload Document File</label>
                            <div id="dt-upload-dropzone" class="upload-dropzone" role="button" tabindex="0">
                                <input type="file" id="dt-file-input" name="cedant_file" class="d-none"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" />
                                <div class="upload-dropzone-content">
                                    <i class="bx bx-cloud-upload fs-3"></i>
                                    <p class="mb-1 fw-semibold">Drop file here or click to browse</p>
                                    <small class="text-muted">Accepted: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (max
                                        10MB)</small>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1 d-none" id="dt-current-file-wrap">
                                Current file:
                                <a href="#" target="_blank" rel="noopener" id="dt-current-file-link">View</a>
                            </small>
                            <small class="text-muted d-block mt-1" id="dt-selected-file-name">No file selected</small>
                            <small class="text-danger doc-type-error" data-error-for="cedant_file"></small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="docTypeSaveBtn">
                        <i class="bi bi-save me-1"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
