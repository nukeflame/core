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
            <form action="{{ route('update.category_type') }}" method="POST" enctype="multipart/form-data"
                id="updateCategoryForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="opportunity_id" id="opportunity_id" />

                    <div class="mb-4">
                        <label for="category_type" class="form-label">
                            Final Stage Status<span class="required-asterisk">*</span>
                        </label>
                        <select class="form-inputs select2" name="category_type" id="category_type"
                            aria-describedby="categoryTypeHelp">
                            <option value="" disabled selected>Select final stage status</option>
                            <option value="won">Closed Won - BD Facultative</option>
                            <option value="lost">Closed Lost - BD Facultative</option>
                        </select>
                        <div class="form-text" id="categoryTypeHelp">
                            Please select the final stage status for this BD Facultative opportunity.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div></div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" id="updateCategorySubmitBtn">
                                <i class="bx bx-check-circle me-1"></i> Update Stage
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
