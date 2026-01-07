{{-- Treaty Proportional Reinsurance Classes Section --}}
@if ($trans_type !== 'EDIT')
    <div class="reinclass-section" id="reinclass-section-0" data-counter="0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="section-title mb-0">
                <i class="bx bx-layer me-2"></i>Section A
            </h6>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label required">Class Group</label>
                <div class="cover-card">
                    <select class="form-control select2 treaty_reinclass required" name="treaty_reinclass[]"
                        id="treaty_reinclass-0" data-counter="0">
                        <option value="">-- Select Class Group --</option>
                        @foreach ($reinsclasses as $reinsclass)
                            <option value="{{ $reinsclass->class_code }}">
                                {{ $reinsclass->class_code }} - {{ $reinsclass->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Quota Share Section --}}
        <div class="quota-share-section mt-3" style="display: none;">
            <div class="quota_header_div">
                <h6 class="text-primary">
                    <i class="bx bx-pie-chart me-2"></i>Quota Share
                </h6>
            </div>

            <div class="row g-3">
                <div class="col-md-3 quota_share_total_limit_div">
                    <label class="form-label required">100% Quota Share Limit</label>
                    <input type="text" class="form-control amount quota_share_total_limit required"
                        id="quota_share_total_limit-0" name="quota_share_total_limit[]" data-counter="0"
                        placeholder="0.00">
                    <small class="text-muted">Total sum insured</small>
                </div>

                <div class="col-md-2 retention_per_div">
                    <label class="form-label required">Retention (%)</label>
                    <input type="number" class="form-control retention_per required" id="retention_per-0"
                        name="retention_per[]" data-counter="0" placeholder="0.00" min="0" max="100"
                        step="0.01">
                    <small class="text-muted">% retained by cedant</small>
                </div>

                <div class="col-md-3 quota_retention_amt_div" style="display: none;">
                    <label class="form-label">Retention Amount</label>
                    <input type="text" class="form-control amount quota_retention_amt" id="quota_retention_amt-0"
                        name="quota_retention_amt[]" data-counter="0" readonly>
                    <small class="text-muted">Calculated automatically</small>
                </div>

                <div class="col-md-2 treaty_reice_div">
                    <label class="form-label required">Treaty (%)</label>
                    <input type="number" class="form-control treaty_reice required" id="treaty_reice-0"
                        name="treaty_reice[]" data-counter="0" placeholder="0.00" min="0" max="100"
                        step="0.01" readonly>
                    <small class="text-muted">% ceded to treaty</small>
                </div>

                <div class="col-md-3 quota_treaty_limit_div" style="display: none;">
                    <label class="form-label">Treaty Limit</label>
                    <input type="text" class="form-control amount quota_treaty_limit" id="quota_treaty_limit-0"
                        name="quota_treaty_limit[]" data-counter="0" readonly>
                    <small class="text-muted">Calculated automatically</small>
                </div>

                {{-- <div class="col-md-3 quota_treaty_limit_div" style="display: none;">
                    <label class="form-label">Treaty Capacity</label>
                    <input type="text" class="form-control amount quota_treaty_limit" id="quota_treaty_limit-0"
                        name="quota_treaty_limit[]" data-counter="0" readonly>
                    <small class="text-muted">Calculated automatically</small>
                </div> --}}
            </div>
        </div>

        {{-- Surplus Section --}}
        <div class="surplus-section mt-3" style="display: none;">
            <div class="surp_header_div">
                <h6 class="text-success">
                    <i class="bx bx-trending-up me-2"></i>Surplus
                </h6>
            </div>

            <div class="row g-3">
                <div class="col-md-3 surp_retention_amt_div" style="display: none;">
                    <label class="form-label">Retention Amount</label>
                    <input type="text" class="form-control amount surp_retention_amt" id="surp_retention_amt-0"
                        name="surp_retention_amt[]" data-counter="0">
                </div>

                <div class="col-md-2 no_of_lines_div">
                    <label class="form-label required">Number of Lines</label>
                    <input type="number" class="form-control no_of_lines required" id="no_of_lines-0"
                        name="no_of_lines[]" data-counter="0" min="1" placeholder="1">
                </div>

                <div class="col-md-3 surp_treaty_limit_div" style="display: none;">
                    <label class="form-label">Treaty Limit</label>
                    <input type="text" class="form-control amount surp_treaty_limit" id="surp_treaty_limit-0"
                        name="surp_treaty_limit[]" data-counter="0" readonly>
                </div>

                <div class="col-md-3 surp_treaty_capacity_div" style="display: none;">
                    <label class="form-label">Treaty Capacity</label>
                    <input type="text" class="form-control amount surp_treaty_capacity"
                        id="surp_treaty_capacity-0" name="surp_treaty_capacity[]" data-counter="0" readonly>
                </div>
            </div>
        </div>

        {{-- Financial Details --}}
        <div class="row g-3 mt-1">
            <div class="col-md-3 estimated_income_div">
                <label class="form-label required">Estimated Income</label>
                <input type="text" class="form-control amount estimated_income required" id="estimated_income-0"
                    name="estimated_income[]" data-counter="0" placeholder="0.00" required>
                <small class="text-muted">Expected premium income</small>
            </div>

            <div class="col-md-3 cashloss_limit_div">
                <label class="form-label required">Cash Loss Limit</label>
                <input type="text" class="form-control amount cashloss_limit required" id="cashloss_limit-0"
                    name="cashloss_limit[]" data-counter="0" placeholder="0.00">
                <small class="text-muted">Maximum claim amount</small>
            </div>
        </div>

        {{-- Commission Section --}}
        <div class="commission-section mt-4" id="comm-section-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-info mb-0">
                    <i class="bx bx-money me-2"></i>Commission Structure
                </h6>
            </div>

            <div class="comm-sections" id="comm-section-0-0" data-class-counter="0" data-counter="0">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 prem_type_treaty_div">
                        <label class="form-label required">Treaty</label>
                        <div class="cover-card">
                            <select class="form-control select2 prem_type_treaty required" name="prem_type_treaty[]"
                                id="prem_type_treaty-0-0" data-class-counter="0" data-counter="0">
                                <option value="">Select Treaty</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 prem_type_code_div">
                        <label class="form-label required">Class Name</label>
                        <input type="hidden" class="prem_type_reinclass" id="prem_type_reinclass-0-0"
                            name="prem_type_reinclass[]">
                        <div class="cover-card">
                            <select class="form-control select2 prem_type_code required" name="prem_type_code[]"
                                id="prem_type_code-0-0" data-class-counter="0" data-counter="0">
                                <option value="">--Select Class Name--</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 comm_type_div">
                        <label class="form-label required">Commission Type</label>
                        <div class="cover-card">
                            <select class="form-control select2 commission_type required"
                                name="treaty_commission_type[]" id="commission_type-0-0" data-class-counter="0"
                                data-counter="0">
                                <option value="">Select Type</option>
                                <option value="FLAT">Flat Rate</option>
                                <option value="SLIDING">Sliding Scale</option>
                            </select>
                        </div>
                    </div>

                    {{-- Flat Rate Commission --}}
                    <div class="col-md-3 flat_rate_div">
                        <label class="form-label required">Commission (%)</label>
                        <div class="input-group">
                            <input type="text" class="form-control prem_type_comm_rate required"
                                name="flat_prem_type_comm_rate[]" id="prem_type_comm_rate-0-0" data-counter="0"
                                placeholder="0.00">
                            <button class="btn btn-primary add-comm-section" type="button" id="add-comm-section-0-0"
                                data-counter="0" title="Add another commission rate">
                                <i class="bx bx-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Sliding Scale Commission --}}
                    <div class="col-md-2 sliding_scale_div" style="display: none;">
                        <label class="form-label">Commission Rate (%)</label>
                        <div class="input-group">
                            <input type="text" class="form-control prem_type_comm_rate"
                                name="sliding_treaty_prem_type_comm_rate[]" id="prem_type_comm_rate-sliding-0-0"
                                data-counter="0" placeholder="0.00" readonly>
                        </div>
                        <small class="text-muted">Average rate from scale</small>
                    </div>

                    <div class="col-md-3 sliding_scale_div config_scale_div" style="display: none;">
                        <label class="form-label required">Configure Scale</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary btn-block configure-sliding-btn"
                                data-class-counter="0" data-counter="0" style="width: 86%;">
                                <i class="bx bx-trending-up me-1"></i> Configure Scale
                            </button>
                            <button class="btn btn-primary add-comm-section" type="button"
                                id="add-comm-section-sliding-0-0" data-counter="0">
                                <i class="bx bx-plus"></i>
                            </button>
                        </div>
                        <small class="text-muted">Rates based on loss ratio</small>
                        <input type="hidden" class="sliding_scale_data" name="sliding_scale_data[]"
                            id="sliding_scale_data-0-0">
                    </div>
                </div>
            </div>
        </div>

    </div>
@else
    {{-- EDIT MODE --}}
    @if (isset($coverreinpropClasses) && count($coverreinpropClasses) > 0)
        @php
            $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
            $groupedClasses = $coverreinpropClasses->groupBy('reinclass');
        @endphp

        @foreach ($groupedClasses as $reinclass => $classData)
            @php
                $index = $loop->index;
                $quotaData = $classData->where('item_description', 'QUOTA')->first();
                $surpData = $classData->where('item_description', 'SURPLUS')->first();
                $classPremTypes = isset($premtypes) ? $premtypes->where('reinclass', $reinclass)->all() : [];
                $reinClassPremTypes = isset($reinPremTypes)
                    ? $reinPremTypes->where('reinclass', $reinclass)->all()
                    : [];
            @endphp

            <div class="reinclass-section" id="reinclass-section-{{ $index }}"
                data-counter="{{ $index }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="section-title mb-0">
                        <i class="bx bx-layer me-2"></i>Section {{ $sections[$index] }}
                    </h6>
                    @if ($index > 0)
                        <button type="button" class="btn btn-danger btn-sm remove-rein-class">
                            <i class="bx bx-trash me-1"></i> Remove Section
                        </button>
                    @endif
                </div>

                <div class="row g-3">
                    {{-- Reinsurance Class --}}
                    <div class="col-md-4">
                        <label class="form-label required">Class Group</label>
                        <select class="form-control select2 treaty_reinclass" name="treaty_reinclass[]"
                            id="treaty_reinclass-{{ $index }}" data-counter="{{ $index }}" required>
                            <option value="">-- Select Class Group --</option>
                            @foreach ($reinsclasses as $reinsclass)
                                <option value="{{ $reinsclass->class_code }}"
                                    {{ $reinsclass->class_code == $reinclass ? 'selected' : '' }}>
                                    {{ $reinsclass->class_code }} - {{ $reinsclass->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Quota Share Section --}}
                @if ($quotaData)
                    <div class="quota-share-section mt-3">
                        <div class="quota_header_div">
                            <h6 class="text-primary">
                                <i class="bx bx-pie-chart me-2"></i>Quota Share
                            </h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3 quota_share_total_limit_div">
                                <label class="form-label required">100% Quota Share Limit</label>
                                <input type="text" class="form-control amount quota_share_total_limit"
                                    id="quota_share_total_limit-{{ $index }}" name="quota_share_total_limit[]"
                                    data-counter="{{ $index }}"
                                    value="{{ number_format($quotaData->treaty_limit, 2) }}" required>
                            </div>

                            <div class="col-md-2 retention_per_div">
                                <label class="form-label required">Retention (%)</label>
                                <input type="number" class="form-control retention_per"
                                    id="retention_per-{{ $index }}" name="retention_per[]"
                                    data-counter="{{ $index }}"
                                    value="{{ number_format($quotaData->retention_rate, 2) }}" min="0"
                                    max="100" step="0.01" required>
                            </div>

                            <div class="col-md-3 quota_retention_amt_div">
                                <label class="form-label">Retention Amount</label>
                                <input type="text" class="form-control amount quota_retention_amt"
                                    id="quota_retention_amt-{{ $index }}" name="quota_retention_amt[]"
                                    data-counter="{{ $index }}"
                                    value="{{ number_format($quotaData->retention_amount, 2) }}" readonly>
                            </div>

                            <div class="col-md-2 treaty_reice_div">
                                <label class="form-label required">Treaty (%)</label>
                                <input type="number" class="form-control treaty_reice"
                                    id="treaty_reice-{{ $index }}" name="treaty_reice[]"
                                    data-counter="{{ $index }}"
                                    value="{{ number_format($quotaData->treaty_rate, 2) }}" min="0"
                                    max="100" step="0.01" readonly required>
                            </div>

                            <div class="col-md-3 quota_treaty_limit_div">
                                <label class="form-label">Treaty Limit</label>
                                <input type="text" class="form-control amount quota_treaty_limit"
                                    id="quota_treaty_limit-{{ $index }}" name="quota_treaty_limit[]"
                                    data-counter="{{ $index }}"
                                    value="{{ number_format($quotaData->treaty_amount, 2) }}" readonly>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Surplus Section --}}
                @if ($surpData)
                    <div class="surplus-section mt-3">
                        <div class="surp_header_div">
                            <h6 class="text-success">
                                <i class="bx bx-trending-up me-2"></i>Surplus
                            </h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3 surp_retention_amt_div">
                                <label class="form-label">Retention Amount</label>
                                <input type="text" class="form-control amount surp_retention_amt"
                                    id="surp_retention_amt-{{ $index }}" name="surp_retention_amt[]"
                                    data-counter="{{ $index }}"
                                    value="{{ number_format($surpData->retention_amount, 2) }}" readonly>
                            </div>

                            <div class="col-md-2 no_of_lines_div">
                                <label class="form-label required">Number of Lines</label>
                                <input type="number" class="form-control no_of_lines"
                                    id="no_of_lines-{{ $index }}" name="no_of_lines[]"
                                    data-counter="{{ $index }}" value="{{ $surpData->no_of_lines }}"
                                    min="1" required>
                            </div>

                            <div class="col-md-3 surp_treaty_limit_div">
                                <label class="form-label">Treaty Limit</label>
                                <input type="text" class="form-control amount surp_treaty_limit"
                                    id="surp_treaty_limit-{{ $index }}" name="surp_treaty_limit[]"
                                    data-counter="{{ $index }}"
                                    value="{{ number_format($surpData->treaty_amount, 2) }}" readonly>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Financial Details --}}
                <div class="row g-3 mt-3">
                    <div class="col-md-3 estimated_income_div">
                        <label class="form-label required">Estimated Income</label>
                        <input type="text" class="form-control amount estimated_income"
                            id="estimated_income-{{ $index }}" name="estimated_income[]"
                            data-counter="{{ $index }}"
                            value="{{ number_format($quotaData->estimated_income ?? ($surpData->estimated_income ?? 0), 2) }}"
                            required>
                    </div>

                    <div class="col-md-3 cashloss_limit_div">
                        <label class="form-label required">Cash Loss Limit</label>
                        <input type="text" class="form-control amount cashloss_limit"
                            id="cashloss_limit-{{ $index }}" name="cashloss_limit[]"
                            data-counter="{{ $index }}"
                            value="{{ number_format($quotaData->cashloss_limit ?? ($surpData->cashloss_limit ?? 0), 2) }}"
                            required>
                    </div>
                </div>

                {{-- Commission Section --}}
                <div class="commission-section mt-4" id="comm-section-{{ $index }}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-info mb-0">
                            <i class="bx bx-money me-2"></i>Commission Structure
                        </h6>
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                            data-bs-target="#commissionHelpModal">
                            <i class="bx bx-help-circle me-1"></i> Commission Types Guide
                        </button>
                    </div>

                    @foreach ($classPremTypes as $premType)
                        @php
                            $commType = $premType->commission_type ?? 'FLAT';
                            $slidingData = isset($premType->sliding_scale_data)
                                ? json_decode($premType->sliding_scale_data, true)
                                : null;
                        @endphp

                        <div class="comm-sections" id="comm-section-{{ $index }}-{{ $loop->index }}"
                            data-class-counter="{{ $index }}" data-counter="{{ $loop->index }}">
                            <div class="row g-3 align-items-end mb-2">
                                <div class="col-md-3 prem_type_treaty_div">
                                    <label class="form-label required">Treaty</label>
                                    <select class="form-control select2 prem_type_treaty" name="prem_type_treaty[]"
                                        id="prem_type_treaty-{{ $index }}-{{ $loop->index }}"
                                        data-class-counter="{{ $index }}" data-counter="{{ $loop->index }}"
                                        required>
                                        @foreach ($treatytypes as $treatytype)
                                            <option value="{{ $treatytype->treaty_code }}"
                                                {{ $treatytype->treaty_code == $premType->treaty ? 'selected' : '' }}>
                                                {{ $treatytype->treaty_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 prem_type_code_div">
                                    <label class="form-label required">Class Name</label>
                                    <input type="hidden" class="prem_type_reinclass"
                                        id="prem_type_reinclass-{{ $index }}-{{ $loop->index }}"
                                        name="prem_type_reinclass[]" value="{{ $reinclass }}">
                                    <select class="form-control select2 prem_type_code" name="prem_type_code[]"
                                        id="prem_type_code-{{ $index }}-{{ $loop->index }}"
                                        data-class-counter="{{ $index }}" data-counter="{{ $loop->index }}"
                                        required>
                                        @foreach ($reinClassPremTypes as $reinPremType)
                                            <option value="{{ $reinPremType->premtype_code }}"
                                                {{ $premType->premtype_code == $reinPremType->premtype_code ? 'selected' : '' }}>
                                                {{ $reinPremType->premtype_code }} -
                                                {{ $reinPremType->premtype_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2 comm_type_div">
                                    <label class="form-label required">Commission Type</label>
                                    <select class="form-control select2 commission_type"
                                        name="treaty_commission_type[]"
                                        id="commission_type-{{ $index }}-{{ $loop->index }}"
                                        data-class-counter="{{ $index }}" data-counter="{{ $loop->index }}"
                                        required>
                                        <option value="FLAT" {{ $commType == 'FLAT' ? 'selected' : '' }}>Flat Rate
                                        </option>
                                        <option value="SLIDING" {{ $commType == 'SLIDING' ? 'selected' : '' }}>Sliding
                                            Scale</option>
                                    </select>
                                </div>

                                {{-- Flat Rate Commission --}}
                                <div class="col-md-3 flat_rate_div"
                                    style="display: {{ $commType == 'FLAT' ? 'block' : 'none' }};">
                                    <label class="form-label">Commission (%)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control prem_type_comm_rate"
                                            name="flat_prem_type_comm_rate[]"
                                            id="prem_type_comm_rate-{{ $index }}-{{ $loop->index }}"
                                            value="{{ number_format($premType->comm_rate, 2) }}">
                                        @if ($loop->first)
                                            <button class="btn btn-primary add-comm-section" type="button"
                                                id="add-comm-section-{{ $index }}-{{ $loop->index }}"
                                                data-counter="{{ $index }}">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-danger remove-comm-section" type="button">
                                                <i class="bx bx-minus"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <small class="text-muted">Fixed rate for all premiums</small>
                                </div>

                                {{-- Sliding Scale Commission --}}
                                <div class="col-md-2 sliding_scale_div"
                                    style="display: {{ $commType == 'SLIDING' ? 'block' : 'none' }};">
                                    <label class="form-label">Commission Rate (%)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control prem_type_comm_rate"
                                            name="sliding_treaty_prem_type_comm_rate[]"
                                            id="prem_type_comm_rate-sliding-{{ $index }}-{{ $loop->index }}"
                                            value="{{ number_format($premType->comm_rate, 2) }}" readonly>
                                    </div>
                                    <small class="text-muted">Average rate from scale</small>
                                </div>

                                <div class="col-md-3 sliding_scale_div"
                                    style="display: {{ $commType == 'SLIDING' ? 'block' : 'none' }};">
                                    <label class="form-label">Configure Scale</label>
                                    <div class="input-group">
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-block configure-sliding-btn"
                                            data-class-counter="{{ $index }}"
                                            data-counter="{{ $loop->index }}" style="width: 86%;">
                                            <i class="bx bx-trending-up me-1"></i>
                                            @if ($slidingData)
                                                Edit Scale ({{ count($slidingData) }} tiers)
                                            @else
                                                Configure Scale
                                            @endif
                                        </button>
                                        @if ($loop->first)
                                            <button class="btn btn-primary add-comm-section" type="button"
                                                data-counter="{{ $index }}">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-danger remove-comm-section" type="button">
                                                <i class="bx bx-minus"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <small class="text-muted">Rates based on loss ratio</small>
                                    <input type="hidden" class="sliding_scale_data" name="sliding_scale_data[]"
                                        id="sliding_scale_data-{{ $index }}-{{ $loop->index }}"
                                        value="{{ json_encode($slidingData) }}">
                                </div>
                            </div>

                            @if ($commType == 'SLIDING' && $slidingData)
                                <div class="alert alert-success alert-sm mt-2 sliding-preview">
                                    <strong>Sliding Scale Tiers:</strong>
                                    @foreach ($slidingData as $tier)
                                        <span class="badge bg-success me-1">
                                            {{ $tier['loss_ratio_min'] }}-{{ $tier['loss_ratio_max'] }}%:
                                            {{ $tier['commission_rate'] }}%
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            No reinsurance classes configured yet. The default class will be added when you select a reinsurance class
            above.
        </div>
    @endif
@endif

{{-- Add Button for Multiple Reinsurance Classes --}}
<div class="text-center mt-3">
    <button type="button" class="btn btn-outline-primary" id="add_rein_class">
        <i class="bx bx-plus me-1"></i> Add Another Reinsurance Class
    </button>
</div>

@include('cover.partials.commission-modals')

<style>
    .reinclass-section {
        background: linear-gradient(to bottom, #ffffff, #f8f9fa);
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .reinclass-section:hover {
        border-color: #007bff;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
    }

    .section-title {
        padding: 0px;
        margin: 0px;
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
        display: inline-block;
    }

    .quota-share-section,
    .surplus-section {
        background-color: #fff;
        padding: .75rem;
        border-radius: 8px;
        border-left: 2px solid #007bff;
    }

    .surplus-section {
        border-left-color: #28a745;
    }

    .commission-section {
        background-color: #fff;
        padding: .75rem;
        border-radius: 8px;
        border-left: 2px solid #17a2b8;
    }

    .comm-sections {
        background-color: #f8f9fa;
        padding: 0.75rem;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .comm-sections:hover {
        border-color: #17a2b8;
        box-shadow: 0 2px 8px rgba(23, 162, 184, 0.1);
    }

    .quota-share-section h6,
    .surplus-section h6,
    .commission-section h6 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    .sliding_scale_div .prem_type_comm_rate {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .prem_type_treaty_div,
    .prem_type_code_div,
    .comm_type_div,
    .flat_rate_div,
    .sliding_scale_div {
        height: 95px;
    }

    .config_scale_div {
        height: auto;
        min-height: auto !important;
        margin-top: 0px !important;
    }
</style>
