<div class="card border-dark mb-0 pb-0">
    <div class="card-header bg-info bg-opacity-10">
        <h6 class="mb-0 fs-14">
            <i class="bx bx-pie-chart"></i> Distribution Summary
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="summary-item">
                    <label class="text-muted fs-16">Total Offered Share</label>
                    <div class="h5 mb-0 text-primary" id="summary-total-offered">0.00%</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-item">
                    <label class="text-muted fs-16">Total Distributed</label>
                    <div class="h5 mb-0 text-success" id="summary-total-distributed">0.00%</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-item">
                    <label class="text-muted fs-16">Total Remaining</label>
                    <div class="h5 mb-0" id="summary-total-remaining">
                        <span class="remaining-value">0.00%</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-item">
                    <label class="text-muted fs-16">Number of Reinsurers</label>
                    <div class="h5 mb-0 text-info" id="summary-reinsurer-count">0</div>
                </div>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-12">
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" id="distribution-progress-bar"
                        style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <span class="progress-text">0% Placed</span>
                    </div>
                </div>
                <small class="text-muted mt-1 d-block">
                    <i class="fa fa-info-circle"></i>
                    Progress bar shows percentage of offered share that has been distributed
                </small>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div id="distribution-status-alert" class="alert" role="alert" style="display: none;">
                    <!-- Dynamic status messages will appear here -->
                </div>
            </div>
        </div>
    </div>
</div>
