@props([
    'cover',
    'typeOfBus' => null,
    'customer' => null,
    'summaryData' => null,
    'title' => null,
    'transactionInfo' => null,
    'coverreinprop' => [],
])

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 pb-2 px-0 pt-2">
        <h6 class="mb-0 fw-semibold">
            <i class="ri-information-line me-1" style="vertical-align: -2px;"></i> {{ $title ?? 'Cover Summary' }}
        </h6>
    </div>
    <div class="card-body pt-2 px-0">
        @if ($transactionInfo)
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="summary-items">
                                @if ($customer)
                                    <div class="summary-item">
                                        <span class="summary-label">Cedant</span>
                                        <span class="summary-value">{{ firstUpper($customer->name) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <span class="summary-label">Policy Period</span>
                                <span class="summary-value">01/01/2024 - 31/12/2024</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-top border-dark">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="summary-items">
                                <div class="summary-item">
                                    <span class="summary-label">Total Debits</span>
                                    <span class="summary-value">KES 0.00</span>
                                </div>


                                <div class="summary-item">
                                    <span class="summary-label">Total Commission</span>
                                    <span class="summary-value">KES 0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-items">
                                <div class="summary-item">
                                    <span class="summary-label">Profit Commission</span>
                                    <span class="summary-value">KES 0.00</span>
                                </div>


                                <div class="summary-item">
                                    <span class="summary-label">Portfolio Value</span>
                                    <span class="summary-value">KES 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-6">
                    <div class="summary-items">
                        @if ($customer)
                            <div class="summary-item">
                                <span class="summary-label">Cedant</span>
                                <span class="summary-value">{{ firstUpper($customer->name) }}</span>
                            </div>
                        @endif

                        <div class="summary-item">
                            <span class="summary-label">Transaction Type</span>
                            <span class="summary-value text-uppercase">{{ $cover->transaction_type ?? 'N/A' }}</span>
                        </div>

                        @if (in_array($cover->type_of_bus ?? '', ['FPR', 'FNP']))
                            <div class="summary-item mt-2 pt-2">
                                <span class="summary-label">Total Sum Insured</span>
                                <span class="summary-value text-primary fw-bold">
                                    {{ number_format($cover->effective_sum_insured ?? 0, 2) }}
                                </span>
                            </div>

                            <div class="summary-item">
                                <span class="summary-label">Premium</span>
                                <span class="summary-value text-success fw-bold">
                                    {{ number_format($cover->rein_premium ?? 0, 2) }}
                                </span>
                            </div>

                            <div class="summary-item mt-1 pt-2">
                                <span class="summary-label">Our Share</span>
                                <div>
                                    <span class="summary-value d-block">
                                        {{ number_format($cover->share_offered ?? 0, 2) }}%
                                    </span>
                                    <small class="text-muted">
                                        {{ number_format((($cover->share_offered ?? 0) / 100) * ($cover->rein_premium ?? 0), 2) }}
                                    </small>
                                </div>
                            </div>

                            <div class="summary-item">
                                <span class="summary-label">Reinsurer's Commission</span>
                                <div>
                                    <span class="summary-value d-block">
                                        {{ number_format($cover->rein_comm_rate ?? 0, 2) }}%
                                    </span>
                                    <small class="text-muted">
                                        {{ number_format($cover->rein_comm_amount ?? 0, 2) }}
                                    </small>
                                </div>
                            </div>

                            @if (($cover->no_of_installments ?? 0) > 1)
                                <div class="summary-item mt-2 pt-2">
                                    <span class="summary-label">Installments</span>
                                    <span class="summary-value">{{ $cover->no_of_installments }}</span>
                                </div>
                            @endif
                        @endif

                        @if (in_array($cover->type_of_bus ?? '', ['TPR', 'TNP']))
                            <div class="summary-item mt-2 pt-2">
                                <span class="summary-label">Treaty</span>
                                <span class="summary-value">{{ $cover->cover_title ?? 'N/A' }}</span>
                            </div>

                            @if (isset($coverreinprop) && $coverreinprop)
                                <div class="summary-item">
                                    <span class="summary-label">No. of Lines</span>
                                    <span class="summary-value">
                                        {{ $coverreinprop['no_of_lines'] }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Reinsurers</span>
                                    <span class="summary-value">{{ $coverreinprop['total_reinsurers'] }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-items">
                        @if ($typeOfBus)
                            <div class="summary-item">
                                <span class="summary-label">Cover Type</span>
                                <span class="summary-value">{{ $typeOfBus->bus_type_name ?? 'N/A' }}</span>
                            </div>
                        @endif

                        @if (in_array($cover->type_of_bus ?? '', ['FPR', 'FNP']))
                            <div class="summary-item mt-2 pt-2">
                                <span class="summary-label">Effective Sum Insured</span>
                                <span class="summary-value text-primary fw-bold">
                                    {{ number_format($cover->effective_sum_insured ?? 0, 2) }}
                                </span>
                            </div>

                            <div class="summary-item mt-2 pt-2">
                                <span class="summary-label">Brokerage Amount</span>
                                <div>
                                    <span class="summary-value d-block">
                                        {{ number_format($cover->brokerage_comm_rate ?? 0, 2) }}%
                                    </span>
                                    <small class="text-muted">
                                        {{ number_format($cover->brokerage_comm_amt ?? 0, 2) }}
                                    </small>
                                </div>
                            </div>

                            <div class="summary-item">
                                <span class="summary-label">Cedant's Commission</span>
                                <div>
                                    <span class="summary-value d-block">
                                        {{ number_format($cover->rein_comm_rate ?? 0, 2) }}%
                                    </span>
                                    <small class="text-muted">
                                        {{ number_format($cover->rein_comm_amount ?? 0, 2) }}
                                    </small>
                                </div>
                            </div>

                            @if (($cover->no_of_installments ?? 0) > 1)
                                <div class="summary-item border-top mt-2 pt-2">
                                    <span class="summary-label">Installments</span>
                                    <span class="summary-value">{{ $cover->no_of_installments }}</span>
                                </div>
                            @endif
                        @endif

                        @if (in_array($cover->type_of_bus ?? '', ['TPR', 'TNP']))
                            <div class="summary-item">
                                <span class="summary-label">Treaty Limit</span>
                                <span class="summary-value">
                                    {{ $coverreinprop ? $coverreinprop['treaty_limit'] : '' }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Treaty Capacity</span>
                                <span class="summary-value">
                                    {{ $coverreinprop ? $coverreinprop['treaty_capacity'] : '' }}</span>
                            </div>
                        @endif

                        @if (in_array($cover->type_of_bus ?? '', ['FPR', 'FNP']))
                            <div class="summary-item">
                                <span class="summary-label">Insured Name</span>
                                <span class="summary-value">{{ $cover->insured_name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .summary-items {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.5rem 0;
    }

    .summary-item:not(:last-child) {
        border-bottom: 1px solid #f1f3f5;
    }

    .summary-label {
        font-size: 0.8125rem;
        color: #6c757d;
        font-weight: 500;
    }

    .summary-value {
        font-size: 0.875rem;
        color: #212529;
        font-weight: 600;
        text-align: right;
    }
</style>
