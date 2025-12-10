<div class="modal fade effect-scale md-wrapper" id="addInsurerModal" tabindex="-1" aria-labelledby="addInsurerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title" id="addInsurerModalLabel">
                    <i class="bx bx-user-plus me-2 fs-16"></i>Add New Insurer
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="addInsurerForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Insurer Name --}}
                        <div class="col-md-6">
                            <label class="form-label">Insurer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="insurer_name" name="insurer_name" required>
                            <div class="invalid-feedback">Please enter insurer name</div>
                        </div>

                        {{-- Insurer Code --}}
                        <div class="col-md-6">
                            <label class="form-label">Insurer Code</label>
                            <input type="text" class="form-control" id="insurer_code" name="insurer_code">
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="insurer_email" name="insurer_email">
                            <div class="invalid-feedback">Please enter a valid email</div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="insurer_phone" name="insurer_phone">
                        </div>

                        {{-- Physical Address --}}
                        <div class="col-md-12">
                            <label class="form-label">Physical Address</label>
                            <textarea class="form-control" id="insurer_address" name="insurer_address" rows="2"></textarea>
                        </div>

                        {{-- Country --}}
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <select class="form-control select2-modal" id="insurer_country" name="insurer_country">
                                <option value="">Select Country</option>
                                <option value="Kenya" selected>Kenya</option>
                                <option value="Uganda">Uganda</option>
                                <option value="Tanzania">Tanzania</option>
                                <option value="Rwanda">Rwanda</option>
                            </select>
                        </div>

                        {{-- City --}}
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" id="insurer_city" name="insurer_city">
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-control select2-modal" id="insurer_status" name="insurer_status">
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        {{-- KRA PIN --}}
                        <div class="col-md-6">
                            <label class="form-label">KRA PIN</label>
                            <input type="text" class="form-control" id="insurer_kra_pin" name="insurer_kra_pin">
                        </div>
                    </div>

                    {{-- Alert Messages --}}
                    <div class="alert alert-success mt-3 d-none" id="insurerSuccessAlert">
                        <i class="bx bx-check-circle me-2"></i><span id="insurerSuccessMessage"></span>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="insurerErrorAlert">
                        <i class="bx bx-error-circle me-2"></i><span id="insurerErrorMessage"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveInsurerBtn">
                        <i class="bx bx-save me-1"></i>Save Insurer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
