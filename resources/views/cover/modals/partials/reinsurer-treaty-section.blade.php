@php
    $isTemplate = $isTemplate ?? false;
    $sectionId = $isTemplate ? 'treaty-div-section-COUNTER_PLACEHOLDER' : "treaty-div-section-{$counter}";
    $dataCounter = $isTemplate ? 'COUNTER_PLACEHOLDER' : $counter;
@endphp

<div class="treaty-div-section mb-0 border rounded position-relative" id="{{ $sectionId }}"
    data-counter="{{ $dataCounter }}">
    @if (!$isTemplate && $counter > 0)
        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-treaty-section"
            data-counter="{{ $counter }}">
            <i class="bx bx-x"></i>
        </button>
    @endif

    <div class="card mb-0 border-0">
        <div class="card-header bg-dark bg-opacity-10 p-3">
            @if (in_array($cover->type_of_bus, ['TPR', 'TNP']))
                <h6 class="mb-0 fs-15">
                    <i class="bx bx-file"></i> Treaty Information
                </h6>
            @else
                <h6 class="mb-0 fs-15">
                    <i class="bx bx-file"></i> Facultative Information
                </h6>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                @if (in_array($cover->type_of_bus, ['TPR', 'TNP']))
                    <div class="col-md-3">
                        <label for="reinsurer-treaty-{{ $dataCounter }}" class="form-label required">
                            Treaty
                        </label>
                        @if (isset($coverTreaties) && $coverTreaties->count() == 1)
                            <input class="form-control bg-light"
                                value="{{ $coverTreaties[0]->treaty_dtl->treaty_name }}" readonly />
                            <input type="hidden" name="treaty[{{ $dataCounter }}][treaty]"
                                class="form-control treaties" value="{{ $coverTreaties[0]->treaty }}" readonly />
                        @else
                            <select name="treaty[{{ $dataCounter }}][treaty]" id="reinsurer-treaty-{{ $dataCounter }}"
                                class="form-select reinsurer-treaty treaties" data-counter="{{ $dataCounter }}"
                                required>
                                <option value="">--Select Treaty--</option>
                                @foreach ($coverTreaties as $coverTreaty)
                                    <option value="{{ $coverTreaty->treaty }}"
                                        title="{{ $coverTreaty->treaty_name }}">
                                        {{ $coverTreaty->treaty_name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                @endif

                <div class="col-md-3">
                    <label for="share_offered-{{ $dataCounter }}" class="form-label">
                        Offered Share (%)
                    </label>
                    <input type="number" step="0.01" name="treaty[{{ $dataCounter }}][share_offered]"
                        id="share_offered-{{ $dataCounter }}"
                        value="{{ number_format($cover->share_offered, 2, '.', '') }}"
                        class="form-control bg-light share_offered treaties" readonly />
                </div>

                <div class="col-md-3">
                    <label for="distributed_share-{{ $dataCounter }}" class="form-label">
                        Distributed (%)
                    </label>
                    <input type="number" step="0.01" name="treaty[{{ $dataCounter }}][distributed_share]"
                        id="distributed_share-{{ $dataCounter }}"
                        class="form-control bg-light distributed-share treaties" readonly />
                </div>

                <div class="col-md-3">
                    <label for="rem_share-{{ $dataCounter }}" class="form-label">
                        Undistributed (%)
                    </label>
                    <input type="number" step="0.01" name="treaty[{{ $dataCounter }}][rem_share]"
                        id="rem_share-{{ $dataCounter }}" class="form-control rem-share treaties" readonly />
                </div>
            </div>

            @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                <div class="row mt-1">
                    <div class="col-md-3">
                        <label for="reinsurer-total_sum_insured-{{ $dataCounter }}" class="form-label">
                            Total Effective Sum Insured
                        </label>
                        <input type="text" name="treaty[{{ $dataCounter }}][total_sum_insured]"
                            id="reinsurer-total_sum_insured-{{ $dataCounter }}"
                            value="{{ number_format($cover->effective_sum_insured, 2) }}" class="form-control bg-light"
                            readonly />
                    </div>

                    <div class="col-md-3">
                        <label for="reinsurer-total_rein_premium-{{ $dataCounter }}" class="form-label">
                            Total Cedant Premium
                        </label>
                        <input type="text" name="treaty[{{ $dataCounter }}][total_rein_premium]"
                            id="reinsurer-total_rein_premium-{{ $dataCounter }}"
                            value="{{ number_format($cover->rein_premium, 2) }}" class="form-control bg-light"
                            readonly />
                    </div>

                    <div class="col-md-3">
                        <label for="reinsurer-rein_comm_amt-{{ $dataCounter }}" class="form-label">
                            Total Reinsurers Commission
                        </label>
                        <input type="text" name="treaty[{{ $dataCounter }}][rein_comm_amt]"
                            id="reinsurer-rein_comm_amt-{{ $dataCounter }}"
                            value="{{ number_format($cover->rein_comm_amount, 2) }}" class="form-control bg-light"
                            readonly />
                    </div>
                    <div class="col-md-3">
                        <label for="reinsurer-total_rein_premium-{{ $dataCounter }}" class="form-label">Brokerage
                            Commission Rate (%)</label>
                        <input type="text" name="treaty[{{ $dataCounter }}][total_brokerage_comm_rate]"
                            id="treinsurer-total_rein_premium-{{ $dataCounter }}"
                            value="{{ number_format($cover->brokerage_comm_rate, 2) }}" class="form-control bg-light"
                            readonly />
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card m-0 p-0 rein-modal border-0">
        <div class="card-header border-0 bg-dark bg-opacity-10 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fs-15">
                <i class="fas fa-users me-2"></i>Reinsurer Details
            </h6>
            <button type="button" class="btn btn-sm btn-success add-reinsurer-btn"
                data-treaty-counter="{{ $dataCounter }}">
                <i class="fas fa-plus me-1"></i> Add Reinsurer
            </button>
        </div>
        <div class="card-body">
            <div id="reinsurer-div-{{ $dataCounter }}" class="reinsurer-container"
                data-treaty-counter="{{ $dataCounter }}">
                @include('cover.modals.partials.reinsurer-row', [
                    'treatyCounter' => $dataCounter,
                    'counter' => 0,
                    'cover' => $cover,
                    'reinsurers' => $reinsurers,
                    'coverpart' => $coverpart,
                    'whtRates' => $whtRates,
                    'paymethods' => $paymethods,
                    'isTemplate' => $isTemplate,
                ])
            </div>
        </div>
    </div>
</div>
