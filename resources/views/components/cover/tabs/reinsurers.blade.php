<div class="p-4">
    {{-- Summary Stats --}}
    @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-subtle">
                        <i class="ri-team-line text-primary"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Reinsurers</div>
                        <div class="stat-value" id="totalReinsurers">
                            {{ $summaryData['total_reinsurers'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success-subtle">
                        <i class="ri-percent-line text-success"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Placed</div>
                        <div class="stat-value" id="totalPlaced">
                            {{ number_format($summaryData['total_placed'] ?? 0, 2) }}%
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning-subtle">
                        <i class="ri-pie-chart-line text-warning"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Remaining</div>
                        <div class="stat-value" id="remainingCapacity">
                            {{ number_format($summaryData['remaining_capacity'] ?? 0, 2) }}%
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info-subtle">
                        <i class="ri-money-dollar-circle-line text-info"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Premium</div>
                        <div class="stat-value" id="totalPremium">
                            {{ number_format($summaryData['total_premium'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Share Distribution Visual --}}
        <div class="card border-0 bg-light mb-4">
            <div class="card-body">
                <h6 class="mb-3 fw-semibold">
                    <i class="ri-bar-chart-line me-2"></i>Share Distribution
                </h6>
                <div class="share-distribution-bar mb-3" id="shareDistributionBar">
                    {{-- Populated by JavaScript --}}
                </div>
                <div class="d-flex justify-content-between text-muted small">
                    <span>0%</span>
                    <span>
                        Placed: <strong
                            id="placedPercent">{{ number_format($summaryData['total_placed'] ?? 0, 2) }}%</strong> |
                        Remaining: <strong
                            id="remainingPercent">{{ number_format($summaryData['remaining_capacity'] ?? 0, 2) }}%</strong>
                    </span>
                    <span>100%</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Action Buttons --}}
    @if ($actionable)
        <div class="mb-3 d-flex gap-2">
            <button type="button" class="btn btn-primary" data-action="add-reinsurer">
                <i class="ri-add-line me-2"></i>Add Reinsurer
            </button>
            @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP']))
                <button type="button" class="btn btn-outline-secondary" data-action="import-from-treaty">
                    <i class="ri-upload-2-line me-2"></i>Import from Treaty
                </button>
            @endif
        </div>
    @endif

    {{-- Reinsurers DataTable --}}
    <div class="table-responsive">
        <table class="table table-modern table-hover" id="reinsurers-table">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>Reinsurer</th>
                    <th class="text-end">Share (%)</th>
                    @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                        <th class="text-end">Sum Insured</th>
                        <th class="text-end">Premium</th>
                        <th class="text-end">Comm Rate</th>
                        <th class="text-end">Commission</th>
                        <th class="text-end">Brokerage</th>
                        <th class="text-end">WHT</th>
                        <th class="text-end">Fronting</th>
                    @endif
                    <th class="text-center" width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Populated by DataTables --}}
            </tbody>
            <tfoot class="table-light">
                {{-- Populated by DataTables --}}
            </tfoot>
        </table>
    </div>
</div>

<style>
    .stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.1);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #212529;
        line-height: 1;
    }

    .share-distribution-bar {
        display: flex;
        height: 40px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .share-segment {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .share-segment:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    .share-segment::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        margin-bottom: 5px;
    }

    .share-segment:hover::after {
        opacity: 1;
    }
</style>
