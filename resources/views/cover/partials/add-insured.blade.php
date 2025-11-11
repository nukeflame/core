{{-- Add Insured Modal --}}
<div class="modal fade" id="addInsuredDataModal" tabindex="-1" aria-labelledby="addInsuredModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addInsuredModalLabel">
                    <i class="bx bx-user-plus me-2"></i>Add New Insured Party
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="addInsuredForm" method="POST">
                    @csrf

                    {{-- Basic Information --}}
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary bg-opacity-10">
                            <h6 class="card-title mb-0 text-primary">
                                <i class="bx bx-info-circle me-2"></i>Basic Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                {{-- Partner Name --}}
                                <div class="col-md-6">
                                    <label for="partnerName" class="form-label required">Partner Name</label>
                                    <input type="text" class="form-control" id="partnerName" name="partnerName"
                                        placeholder="Enter full name or company name" required>
                                    <div class="invalid-feedback">Please enter partner name</div>
                                </div>

                                {{-- Customer Type --}}
                                <div class="col-md-6">
                                    <label for="customerType" class="form-label required">Type of Customer</label>
                                    <select class="form-control select2" id="customerType" name="customerType" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="individual">Individual</option>
                                        <option value="company">Company</option>
                                        <option value="partnership">Partnership</option>
                                        <option value="trust">Trust</option>
                                        <option value="government">Government Entity</option>
                                        <option value="ngo">NGO/Non-Profit</option>
                                    </select>
                                    <div class="invalid-feedback">Please select customer type</div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="email@example.com">
                                    <div class="invalid-feedback">Please enter a valid email</div>
                                </div>

                                {{-- Telephone --}}
                                <div class="col-md-6">
                                    <label for="telephone" class="form-label">Telephone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone"
                                        placeholder="+254 700 000000">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Identification & Registration --}}
                    <div class="card border-info mb-3">
                        <div class="card-header bg-info bg-opacity-10">
                            <h6 class="card-title mb-0 text-info">
                                <i class="bx bx-id-card me-2"></i>Identification & Registration
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                {{-- Identity Type --}}
                                <div class="col-md-4">
                                    <label for="identityType" class="form-label">Identity Type</label>
                                    <select class="form-control select2" id="identityType" name="identityType">
                                        <option value="">-- Select ID Type --</option>
                                        <option value="national_id">National ID</option>
                                        <option value="passport">Passport</option>
                                        <option value="driving_license">Driving License</option>
                                        <option value="military_id">Military ID</option>
                                        <option value="alien_id">Alien ID</option>
                                    </select>
                                </div>

                                {{-- Identity Number --}}
                                <div class="col-md-4">
                                    <label for="identityNo" class="form-label">Identity Number</label>
                                    <input type="text" class="form-control" id="identityNo" name="identityNo"
                                        placeholder="ID Number">
                                </div>

                                {{-- Tax Number --}}
                                <div class="col-md-4">
                                    <label for="taxNo" class="form-label">Tax Number (PIN)</label>
                                    <input type="text" class="form-control" id="taxNo" name="taxNo"
                                        placeholder="A000000000X">
                                </div>

                                {{-- Incorporation Number --}}
                                <div class="col-md-6">
                                    <label for="incorporationNo" class="form-label">Incorporation/Registration
                                        Number</label>
                                    <input type="text" class="form-control" id="incorporationNo"
                                        name="incorporationNo" placeholder="Company Registration Number">
                                </div>

                                {{-- Website --}}
                                <div class="col-md-6">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="website" name="website"
                                        placeholder="https://example.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Address Information --}}
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="card-title mb-0 text-success">
                                <i class="bx bx-map me-2"></i>Address Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                {{-- Street Address --}}
                                <div class="col-md-12">
                                    <label for="street" class="form-label">Street Address</label>
                                    <input type="text" class="form-control" id="street" name="street"
                                        placeholder="Building, Street Name">
                                </div>

                                {{-- City --}}
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City/Town</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        placeholder="Nairobi">
                                </div>

                                {{-- Postal Code --}}
                                <div class="col-md-4">
                                    <label for="postalCode" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postalCode" name="postalCode"
                                        placeholder="00100">
                                </div>

                                {{-- Country --}}
                                <div class="col-md-4">
                                    <label for="country" class="form-label">Country</label>
                                    <select class="form-control select2" id="country" name="country">
                                        <option value="">-- Select Country --</option>
                                        <option value="KE" selected>Kenya</option>
                                        <option value="TZ">Tanzania</option>
                                        <option value="UG">Uganda</option>
                                        <option value="RW">Rwanda</option>
                                        <option value="BI">Burundi</option>
                                        <option value="SS">South Sudan</option>
                                        <option value="ET">Ethiopia</option>
                                        <option value="SO">Somalia</option>
                                        {{-- Add more countries as needed --}}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Financial Ratings (Optional) --}}
                    <div class="card border-warning">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h6 class="card-title mb-0 text-warning">
                                <i class="bx bx-bar-chart me-2"></i>Financial Ratings (Optional)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                {{-- Financial Rating --}}
                                <div class="col-md-6">
                                    <label for="financialRating" class="form-label">Financial Rating</label>
                                    <select class="form-control select2" id="financialRating" name="financialRating">
                                        <option value="">-- Select Rating --</option>
                                        <option value="AAA">AAA - Excellent</option>
                                        <option value="AA">AA - Very Good</option>
                                        <option value="A">A - Good</option>
                                        <option value="BBB">BBB - Average</option>
                                        <option value="BB">BB - Fair</option>
                                        <option value="B">B - Poor</option>
                                        <option value="CCC">CCC - Very Poor</option>
                                    </select>
                                    <small class="text-muted">Credit rating or financial standing</small>
                                </div>

                                {{-- Agency Rating --}}
                                <div class="col-md-6">
                                    <label for="agencyRating" class="form-label">Agency Rating</label>
                                    <input type="text" class="form-control" id="agencyRating" name="agencyRating"
                                        placeholder="e.g., S&P, Moody's rating">
                                    <small class="text-muted">Rating from external agency</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveInsuredBtn">
                    <i class="bx bx-save me-1"></i> Save Insured
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize select2 in modal
        $('#addInsuredDataModal').on('shown.bs.modal', function() {
            $('#addInsuredDataModal .select2').select2({
                dropdownParent: $('#addInsuredDataModal'),
                width: '100%',
                theme: 'bootstrap-5'
            });
        });

        // Handle form submission
        $('#saveInsuredBtn').on('click', function() {
            const form = $('#addInsuredForm');

            // Validate required fields
            if (!form[0].checkValidity()) {
                form.addClass('was-validated');
                toastr.error('Please fill in all required fields');
                return false;
            }

            // Show loading state
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> Saving...');

            // Get form data
            const formData = form.serialize();

            // Submit via AJAX
            $.ajax({
                url: '{{ route('insured.store') }}', // Update with your actual route
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Insured party added successfully');

                        // Add to insured dropdown
                        const newOption = new Option(response.data.name, response.data.name,
                            true, true);
                        $('#insured_name').append(newOption).trigger('change');

                        // Close modal
                        $('#addInsuredDataModal').modal('hide');

                        // Reset form
                        form[0].reset();
                        form.removeClass('was-validated');
                    } else {
                        toastr.error(response.message || 'Failed to add insured party');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    const message = xhr.responseJSON?.message ||
                        'Failed to add insured party';
                    toastr.error(message);
                },
                complete: function() {
                    // Reset button state
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Reset form when modal is hidden
        $('#addInsuredDataModal').on('hidden.bs.modal', function() {
            $('#addInsuredForm')[0].reset();
            $('#addInsuredForm').removeClass('was-validated');
        });

        // Customer type change handler
        $('#customerType').on('change', function() {
            const type = $(this).val();

            // Show/hide fields based on customer type
            if (type === 'individual') {
                $('#incorporationNo').closest('.col-md-6').hide();
                $('#website').closest('.col-md-6').hide();
            } else {
                $('#incorporationNo').closest('.col-md-6').show();
                $('#website').closest('.col-md-6').show();
            }
        });
    });
</script>

<style>
    #addInsuredDataModal .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    #addInsuredDataModal .modal-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    #addInsuredDataModal .card {
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    #addInsuredDataModal .card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #addInsuredDataModal .card-header {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    #addInsuredDataModal .form-label.required::after {
        content: '*';
        color: #dc3545;
        margin-left: 4px;
    }

    #addInsuredDataModal .was-validated .form-control:invalid {
        border-color: #dc3545;
    }

    #addInsuredDataModal .was-validated .form-control:valid {
        border-color: #28a745;
    }
</style>
