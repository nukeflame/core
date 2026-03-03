{{-- Treaty Non-Proportional Layers Section --}}

@if ($trans_type !== 'EDIT')
    {{-- New Layer Template --}}
    <div class="layer-sections" id="layer-section-0" data-counter="0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Layer 1</h6>
        </div>

        <div class="row g-3">
            {{-- Limit Per Reinclass Flag --}}
            <div class="col-md-3">
                <label class="form-label required">Capture Limits per Class?</label>
                <select class="form-control select2 limit_per_reinclass" name="limit_per_reinclass[]"
                    id="limit_per_reinclass-0-0" required>
                    <option value="">Select Option</option>
                    <option value="N" selected>No</option>
                    <option value="Y">Yes</option>
                </select>
                <small class="text-muted">If Yes, separate limits will be captured for each selected reinsurance
                    class</small>
            </div>
        </div>

        <div class="row g-3 mt-2">
            {{-- Reinclass --}}
            <div class="col-md-2">
                <label class="form-label required">Reinclass</label>
                <input type="hidden" class="form-control layer_no" id="layer_no-0-0" name="layer_no[]" value="1"
                    readonly>
                <input type="hidden" class="form-control nonprop_reinclass" id="nonprop_reinclass-0-0"
                    name="nonprop_reinclass[]" value="ALL" readonly>
                <input type="text" class="form-control nonprop_reinclass_desc" id="nonprop_reinclass_desc-0-0"
                    name="nonprop_reinclass_desc[]" value="ALL" readonly>
            </div>

            {{-- Indemnity Limit --}}
            <div class="col-md-2">
                <label class="form-label required">Limit</label>
                <input type="text" class="form-control amount indemnity_treaty_limit" id="indemnity_treaty_limit-0-0"
                    name="indemnity_treaty_limit[]" placeholder="0.00" required>
                <small class="text-muted">Treaty limit amount</small>
            </div>

            {{-- Underlying Limit (Deductible) --}}
            <div class="col-md-2">
                <label class="form-label required">Deductible Amount</label>
                <input type="text" class="form-control amount underlying_limit" id="underlying_limit-0-0"
                    name="underlying_limit[]" placeholder="0.00" required>
                <small class="text-muted">Amount retained before treaty applies</small>
            </div>

            {{-- EGNPI (Estimated Gross Net Premium Income) --}}
            <div class="col-md-2">
                <label class="form-label required">EGNPI</label>
                <input type="text" class="form-control amount egnpi" id="egnpi-0-0" name="egnpi[]" placeholder="0.00"
                    required>
                <small class="text-muted">Estimated premium</small>
            </div>
        </div>

        {{-- Burning Cost Fields (Hidden by default) --}}
        <div class="row g-3 mt-2 burning_rate_section" style="display: none;">
            <div class="col-12">
                <h6 class="text-muted">Burning Cost Method</h6>
            </div>

            {{-- Minimum Burning Cost Rate --}}
            <div class="col-md-3 burning_rate_div">
                <label class="form-label required">Minimum BC Rate (%)</label>
                <input type="text" class="form-control burning_rate" id="min_bc_rate-0-0" name="min_bc_rate[]"
                    placeholder="0.00" min="0" max="100">
                <small class="text-muted">Minimum burning cost rate</small>
            </div>

            {{-- Maximum Burning Cost Rate --}}
            <div class="col-md-3 burning_rate_div">
                <label class="form-label required">Maximum BC Rate (%)</label>
                <input type="text" class="form-control burning_rate" id="max_bc_rate-0-0" name="max_bc_rate[]"
                    placeholder="0.00" min="0" max="100">
                <small class="text-muted">Maximum burning cost rate</small>
            </div>

            {{-- Upper Adjustment --}}
            <div class="col-md-3 burning_rate_div">
                <label class="form-label required">Upper Adjustment Rate (%)</label>
                <input type="text" class="form-control burning_rate" id="upper_adj-0-0" name="upper_adj[]"
                    placeholder="0.00" min="0" max="100">
                <small class="text-muted">Annual upper adjustment</small>
            </div>

            {{-- Lower Adjustment --}}
            <div class="col-md-3 burning_rate_div">
                <label class="form-label required">Lower Adjustment Rate (%)</label>
                <input type="text" class="form-control burning_rate" id="lower_adj-0-0" name="lower_adj[]"
                    placeholder="0.00" min="0" max="100">
                <small class="text-muted">Annual lower adjustment</small>
            </div>
        </div>

        {{-- Flat Rate Fields (Hidden by default) --}}
        <div class="row g-3 mt-2 flat_rate_section" style="display: none;">
            <div class="col-12">
                <h6 class="text-muted">Flat Rate Method</h6>
            </div>

            {{-- Flat Rate --}}
            <div class="col-md-4 flat_rate_div">
                <label class="form-label required">Flat Rate (%)</label>
                <input type="text" class="form-control flat_rate" id="flat_rate-0-0" name="flat_rate[]"
                    placeholder="0.00" min="0" max="100">
                <small class="text-muted">Fixed rate for the layer</small>
            </div>
        </div>

        {{-- Common Fields --}}
        <div class="row g-3 mt-2">
            {{-- Minimum Deposit Premium --}}
            <div class="col-md-3">
                <label class="form-label required">Minimum Deposit Premium</label>
                <input type="text" class="form-control amount min_deposit" id="min_deposit-0-0"
                    name="min_deposit[]" placeholder="0.00" required>
                <small class="text-muted">Minimum premium payable</small>
            </div>

            {{-- Reinstatement Type --}}
            <div class="col-md-3">
                <label class="form-label required">Reinstatement Type</label>
                <select name="reinstatement_type[]" id="reinstatement_type-0-0" class="form-control select2"
                    required>
                    <option value="">Select Type</option>
                    <option value="NOR">Number of Reinstatements</option>
                    <option value="AAL">Annual Aggregate Limit</option>
                </select>
                <small class="text-muted">How limit resets after claim</small>
            </div>

            {{-- Reinstatement Value --}}
            <div class="col-md-3">
                <label class="form-label required">Reinstatement Value</label>
                <input type="text" class="form-control amount reinstatement_value" id="reinstatement_value-0-0"
                    name="reinstatement_value[]" placeholder="0.00" required>
                <small class="text-muted">Number of times or aggregate amount</small>
            </div>
        </div>

        <hr class="my-3">
    </div>
@else
    {{-- Edit Mode - Populate Existing Layers --}}
    @if (isset($coverReinLayers) && count($coverReinLayers) > 0)
        @php
            $layerNumbers = $coverReinLayers->pluck('layer_no')->unique()->sort();
        @endphp

        @foreach ($layerNumbers as $layerIndex => $layerNo)
            @php
                $layerData = $coverReinLayers->where('layer_no', $layerNo);
                $firstLayer = $layerData->first();
                $limitPerReinclass = $layerData->count() > 1 ? 'Y' : 'N';
            @endphp

            <div class="layer-sections" id="layer-section-{{ $layerIndex }}" data-counter="{{ $layerIndex }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Layer {{ $layerNo }}</h6>
                    @if ($layerIndex > 0)
                        <button type="button" class="btn btn-danger btn-sm remove-layer-section">
                            <i class="bx bx-trash me-1"></i> Remove Layer
                        </button>
                    @endif
                </div>

                <div class="row g-3">
                    {{-- Limit Per Reinclass Flag --}}
                    <div class="col-md-3">
                        <label class="form-label required">Capture Limits per Class?</label>
                        <select class="form-control select2 limit_per_reinclass" name="limit_per_reinclass[]"
                            id="limit_per_reinclass-{{ $layerIndex }}-0" required>
                            <option value="">Select Option</option>
                            <option value="N" {{ $limitPerReinclass == 'N' ? 'selected' : '' }}>No</option>
                            <option value="Y" {{ $limitPerReinclass == 'Y' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>

                @foreach ($layerData as $itemIndex => $layer)
                    <div class="row g-3 mt-2 layer-item" data-item="{{ $itemIndex }}">
                        {{-- Reinclass --}}
                        <div class="col-md-2">
                            <label class="form-label required">Reinclass</label>
                            <input type="hidden" class="form-control layer_no"
                                id="layer_no-{{ $layerIndex }}-{{ $itemIndex }}" name="layer_no[]"
                                value="{{ $layerNo }}" readonly>
                            <input type="hidden" class="form-control nonprop_reinclass"
                                id="nonprop_reinclass-{{ $layerIndex }}-{{ $itemIndex }}"
                                name="nonprop_reinclass[]" value="{{ $layer->reinclass }}" readonly>
                            <input type="text" class="form-control nonprop_reinclass_desc"
                                id="nonprop_reinclass_desc-{{ $layerIndex }}-{{ $itemIndex }}"
                                name="nonprop_reinclass_desc[]" value="{{ $layer->reinclass }}" readonly>
                        </div>

                        {{-- Indemnity Limit --}}
                        <div class="col-md-2">
                            <label class="form-label required">Limit</label>
                            <input type="text" class="form-control amount indemnity_treaty_limit"
                                id="indemnity_treaty_limit-{{ $layerIndex }}-{{ $itemIndex }}"
                                name="indemnity_treaty_limit[]"
                                value="{{ number_format($layer->indemnity_limit, 2) }}" required>
                        </div>

                        {{-- Underlying Limit --}}
                        <div class="col-md-2">
                            <label class="form-label required">Deductible Amount</label>
                            <input type="text" class="form-control amount underlying_limit"
                                id="underlying_limit-{{ $layerIndex }}-{{ $itemIndex }}"
                                name="underlying_limit[]" value="{{ number_format($layer->underlying_limit, 2) }}"
                                required>
                        </div>

                        {{-- EGNPI --}}
                        <div class="col-md-2">
                            <label class="form-label required">EGNPI</label>
                            <input type="text" class="form-control amount egnpi"
                                id="egnpi-{{ $layerIndex }}-{{ $itemIndex }}" name="egnpi[]"
                                value="{{ number_format($layer->egnpi, 2) }}" required>
                        </div>

                        @if ($itemIndex === 0)
                            {{-- Show method-specific fields only once per layer --}}
                            @if (isset($old_endt_trans) && $old_endt_trans->method === 'B')
                                {{-- Burning Cost Fields --}}
                                <div class="col-md-3 burning_rate_div">
                                    <label class="form-label required">Min BC Rate (%)</label>
                                    <input type="text" class="form-control burning_rate"
                                        id="min_bc_rate-{{ $layerIndex }}-{{ $itemIndex }}"
                                        name="min_bc_rate[]" value="{{ number_format($layer->min_bc_rate, 2) }}">
                                </div>

                                <div class="col-md-3 burning_rate_div">
                                    <label class="form-label required">Max BC Rate (%)</label>
                                    <input type="text" class="form-control burning_rate"
                                        id="max_bc_rate-{{ $layerIndex }}-{{ $itemIndex }}"
                                        name="max_bc_rate[]" value="{{ number_format($layer->max_bc_rate, 2) }}">
                                </div>

                                <div class="col-md-3 burning_rate_div">
                                    <label class="form-label required">Upper Adj (%)</label>
                                    <input type="text" class="form-control burning_rate"
                                        id="upper_adj-{{ $layerIndex }}-{{ $itemIndex }}" name="upper_adj[]"
                                        value="{{ number_format($layer->upper_adj, 2) }}">
                                </div>

                                <div class="col-md-3 burning_rate_div">
                                    <label class="form-label required">Lower Adj (%)</label>
                                    <input type="text" class="form-control burning_rate"
                                        id="lower_adj-{{ $layerIndex }}-{{ $itemIndex }}" name="lower_adj[]"
                                        value="{{ number_format($layer->lower_adj, 2) }}">
                                </div>
                            @elseif (isset($old_endt_trans) && $old_endt_trans->method === 'F')
                                {{-- Flat Rate Field --}}
                                <div class="col-md-4 flat_rate_div">
                                    <label class="form-label required">Flat Rate (%)</label>
                                    <input type="text" class="form-control flat_rate"
                                        id="flat_rate-{{ $layerIndex }}-{{ $itemIndex }}" name="flat_rate[]"
                                        value="{{ number_format($layer->flat_rate, 2) }}">
                                </div>
                            @endif

                            {{-- Common Fields --}}
                            <div class="col-md-3">
                                <label class="form-label required">Min Deposit Premium</label>
                                <input type="text" class="form-control amount min_deposit"
                                    id="min_deposit-{{ $layerIndex }}-{{ $itemIndex }}" name="min_deposit[]"
                                    value="{{ number_format($layer->min_deposit, 2) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Reinstatement Type</label>
                                <select name="reinstatement_type[]"
                                    id="reinstatement_type-{{ $layerIndex }}-{{ $itemIndex }}"
                                    class="form-control select2" required>
                                    <option value="">Select Type</option>
                                    <option value="NOR"
                                        {{ $layer->reinstatement_type == 'NOR' ? 'selected' : '' }}>
                                        Number of Reinstatements
                                    </option>
                                    <option value="AAL"
                                        {{ $layer->reinstatement_type == 'AAL' ? 'selected' : '' }}>
                                        Annual Aggregate Limit
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Reinstatement Value</label>
                                <input type="text" class="form-control amount reinstatement_value"
                                    id="reinstatement_value-{{ $layerIndex }}-{{ $itemIndex }}"
                                    name="reinstatement_value[]" value="{{ $layer->reinstatement_value }}" required>
                            </div>
                        @endif
                    </div>
                @endforeach

                <hr class="my-3">
            </div>
        @endforeach
    @else
        {{-- No existing layers - show default template --}}
        <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            No layers configured yet. Click "Add Layer" to create one.
        </div>
    @endif
@endif


<style>
    .layer-sections {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .layer-sections:hover {
        border-color: #007bff;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.1);
    }

    .layer-sections h6 {
        color: #2c3e50;
        font-weight: 600;
    }

    .layer-item {
        background-color: #fff;
        padding: 1rem;
        border-radius: 6px;
        margin-top: 1rem;
        border: 1px solid #e0e0e0;
    }

    .burning_rate_section,
    .flat_rate_section {
        background-color: #fff;
        padding: 1rem;
        border-radius: 6px;
        border-left: 3px solid #007bff;
    }

    .card.bg-light {
        border-left: 4px solid #17a2b8;
    }

    .card.bg-light ul {
        padding-left: 1.2rem;
    }

    .card.bg-light li {
        margin-bottom: 0.25rem;
    }
</style>
