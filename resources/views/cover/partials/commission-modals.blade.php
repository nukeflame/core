<div class="modal fade effect-scale" id="slidingScaleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="bx bx-trending-up me-2"></i>Configure Sliding Scale Commission
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bx bx-info-circle me-2"></i>
                    <strong>Sliding Scale:</strong> Commission rates vary based on loss ratio performance.
                    Lower loss ratios = Higher commissions.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="sliding-scale-table">
                        <thead class="table-light">
                            <tr>
                                <th width="30%">Loss Ratio Min (%)</th>
                                <th width="30%">Loss Ratio Max (%)</th>
                                <th width="30%">Commission Rate (%)</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="sliding-scale-rows">
                            {{-- Default row --}}
                            <tr class="sliding-scale-row">
                                <td>
                                    <input type="number" class="form-control loss-ratio-min" min="0"
                                        max="100" step="0.01" placeholder="0.00">
                                </td>
                                <td>
                                    <input type="number" class="form-control loss-ratio-max" min="0"
                                        max="100" step="0.01" placeholder="100.00">
                                </td>
                                <td>
                                    <input type="number" class="form-control commission-rate" min="0"
                                        max="100" step="0.01" placeholder="0.00">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-scale-row">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm" id="add-scale-tier">
                    <i class="bx bx-plus me-1"></i> Add Tier
                </button>

                <hr class="my-3">

                <div class="row g-3">
                    <div class="col-md-6">
                        <h6>Quick Templates:</h6>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2 load-template"
                            data-template="standard">
                            Standard (3 Tiers)
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm load-template"
                            data-template="aggressive">
                            Aggressive (5 Tiers)
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-success btn-sm me-2" id="import-sliding-csv">
                            <i class="bx bx-upload me-1"></i> Import from CSV
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-1" id="export-sliding-csv">
                            <i class="bx bx-download me-1"></i> Download Sample CSV
                        </button>
                        <input type="file" id="sliding-csv-file" accept=".csv" style="display: none;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-sliding-scale">
                    <i class="bx bx-save me-1"></i> Save Sliding Scale
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade effect-scale" id="commissionHelpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-help-circle me-2"></i>Commission Types Guide
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="bx bx-dollar-circle me-2"></i>Flat Rate Commission
                                </h6>
                                <p class="mb-2">A fixed commission percentage applied to all premiums uniformly.</p>
                                <p class="mb-0 small text-muted">
                                    <strong>Example:</strong> 15% commission on all premiums regardless of location or
                                    performance.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-4">
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="bx bx-map me-2"></i>Provincial Commission
                                </h6>
                                <p class="mb-2">Different commission rates for different counties based on
                                    regional factors.</p>
                                <p class="mb-0 small text-muted">
                                    <strong>Example:</strong> Nairobi: 12%, Mombasa: 15%, Rural areas: 18% (higher
                                    commissions for harder-to-reach areas)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="bx bx-trending-up me-2"></i>Sliding Scale Commission
                                </h6>
                                <p class="mb-2">Commission rates that vary based on loss ratio performance. Better
                                    performance = Higher commission.</p>
                                <p class="mb-1 small"><strong>Example:</strong></p>
                                <ul class="small mb-0">
                                    <li>Loss Ratio 0-40%: Commission 25%</li>
                                    <li>Loss Ratio 40-60%: Commission 20%</li>
                                    <li>Loss Ratio 60-80%: Commission 15%</li>
                                    <li>Loss Ratio 80-100%: Commission 10%</li>
                                </ul>
                                <p class="mt-2 mb-0 small text-muted">
                                    <strong>Loss Ratio =</strong> (Claims Paid / Premiums Earned) × 100
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .provincial-rate-input:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }

    #sliding-scale-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .modal-lg {
        max-width: 900px;
    }
</style>
