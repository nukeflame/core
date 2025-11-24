<div class="cover-summary-card mb-3">
    <div class="card border-dark mb-0">
        <div class="card-header bg-dark bg-opacity-10">
            <h6 class="mb-0 fs-14">
                <i class="bx bxs-file-pdf"></i> Cover Information
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="info-group">
                        <label class="form-label text-muted mb-1">Business Type</label>
                        <div class="fw-bold">{{ $typeOfBus->bus_type_name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-group">
                        <label class="form-label text-muted mb-1">Cover Number</label>
                        <div class="fw-bold">{{ $cover->cover_no }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-group">
                        <label class="form-label text-muted mb-1">Insured Name</label>
                        <div class="fw-bold">{{ $cover->insured_name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
