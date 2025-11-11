{{-- Treaty Proportional Reinsurance Classes Section --}}

@if ($trans_type !== 'EDIT')
    {{-- New Reinsurance Class Template --}}
    <div class="reinclass-section" id="reinclass-section-0" data-counter="0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="section-title mb-0">
                <i class="bx bx-layer me-2"></i>Section A
            </h6>
        </div>

        <div class="row g-3">
            {{-- Reinsurance Main Class --}}
            <div class="col-md-4">
                <label class="form-label required">Reinsurance Class</label>
                <select class="form-control select2 treaty_reinclass" name="treaty_reinclass[]" id="treaty_reinclass-0"
                    data-counter="0" required>
                    <option value="">Choose Reinsurance Class</option>
                    @foreach ($reinsclasses as $reinsclass)
                        <option value="{{ $reinsclass->class_code }}">
                            {{ $reinsclass->class_code }} - {{ $reinsclass->class_name }}
                        </option>
                    @endforeach
                </select>
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
                {{-- 100% Quota Share Limit --}}
                <div class="col-md-3 quota_share_total_limit_div">
                    <label class="form-label required">100% Quota Share Limit</label>
                    <input type="text" class="form-control amount quota_share_total_limit"
                        id="quota_share_total_limit-0" name="quota_share_total_limit[]" data-counter="0"
                        placeholder="0.00" required>
                    <small class="text-muted">Total sum insured</small>
                </div>

                {{-- Retention Percentage --}}
                <div class="col-md-2 retention_per_div">
                    <label class="form-label required">Retention (%)</label>
                    <input type="number" class="form-control retention_per" id="retention_per-0" name="retention_per[]"
                        data-counter="0" min="0" max="100" step="0.01" placeholder="0.00" required>
                    <small class="text-muted">% retained by cedant</small>
                </div>

                {{-- Retention Amount --}}
                <div class="col-md-3 quota_retention_amt_div" style="display: none;">
                    <label class="form-label">Retention Amount</label>
                    <input type="text" class="form-control amount quota_retention_amt" id="quota_retention_amt-0"
                        name="quota_retention_amt[]" data-counter="0" readonly>
                    <small class="text-muted">Calculated automatically</small>
                </div>

                {{-- Treaty Share Percentage --}}
                <div class="col-md-2 treaty_reice_div">
                    <label class="form-label required">Treaty (%)</label>
                    <input type="number" class="form-control treaty_reice" id="treaty_reice-0" name="treaty_reice[]"
                        data-counter="0" min="0" max="100" step="0.01" placeholder="0.00" required>
                    <small class="text-muted">% ceded to treaty</small>
                </div>

                {{-- Treaty Limit Amount --}}
                <div class="col-md-3 quota_treaty_limit_div" style="display: none;">
                    <label class="form-label">Treaty Limit</label>
                    <input type="text" class="form-control amount quota_treaty_limit" id="quota_treaty_limit-0"
                        name="quota_treaty_limit[]" data-counter="0" readonly>
                    <small class="text-muted">Calculated automatically</small>
                </div>
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
                {{-- Retention Amount --}}
                <div class="col-md-3 surp_retention_amt_div" style="display: none;">
                    <label class="form-label">Retention Amount</label>
                    <input type="text" class="form-control amount surp_retention_amt" id="surp_retention_amt-0"
                        name="surp_retention_amt[]" data-counter="0" readonly>
                    <small class="text-muted">From quota retention</small>
                </div>

                {{-- Number of Lines --}}
                <div class="col-md-2 no_of_lines_div">
                    <label class="form-label required">Number of Lines</label>
                    <input type="number" class="form-control no_of_lines" id="no_of_lines-0" name="no_of_lines[]"
                        data-counter="0" min="1" placeholder="1" required>
                    <small class="text-muted">Lines above retention</small>
                </div>

                {{-- Treaty Limit --}}
                <div class="col-md-3 surp_treaty_limit_div" style="display: none;">
                    <label class="form-label">Treaty Limit</label>
                    <input type="text" class="form-control amount surp_treaty_limit" id="surp_treaty_limit-0"
                        name="surp_treaty_limit[]" data-counter="0" readonly>
                    <small class="text-muted">Lines × Retention</small>
                </div>
            </div>
        </div>

        {{-- Financial Details --}}
        <div class="row g-3 mt-3">
            {{-- Estimated Income --}}
            <div class="col-md-3 estimated_income_div">
                <label class="form-label required">Estimated Income</label>
                <input type="text" class="form-control amount estimated_income" id="estimated_income-0"
                    name="estimated_income[]" data-counter="0" placeholder="0.00" required>
                <small class="text-muted">Expected premium income</small>
            </div>

            {{-- Cash Loss Limit --}}
            <div class="col-md-3 cashloss_limit_div">
                <label class="form-label required">Cash Loss Limit</label>
                <input type="text" class="form-control amount cashloss_limit" id="cashloss_limit-0"
                    name="cashloss_limit[]" data-counter="0" placeholder="0.00" required>
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
                <div class="row g-3">
                    {{-- Treaty --}}
                    <div class="col-md-3 prem_type_treaty_div">
                        <label class="form-label required">Treaty</label>
                        <select class="form-control select2 prem_type_treaty" name="prem_type_treaty[]"
                            id="prem_type_treaty-0-0" data-class-counter="0" data-counter="0" required>
                            <option value="">Select Treaty</option>
                        </select>
                    </div>

                    {{-- Premium Type --}}
                    <div class="col-md-3 prem_type_code_div">
                        <label class="form-label required">Premium Type</label>
                        <input type="hidden" class="prem_type_reinclass" id="prem_type_reinclass-0-0"
                            name="prem_type_reinclass[]">
                        <select class="form-control select2 prem_type_code" name="prem_type_code[]"
                            id="prem_type_code-0-0" data-class-counter="0" data-counter="0" required>
                            <option value="">Select Premium Type</option>
                        </select>
                    </div>

                    {{-- Commission Rate --}}
                    <div class="col-md-3 prem_type_comm_rate_div">
                        <label class="form-label required">Commission (%)</label>
                        <div class="input-group">
                            <input type="text" class="form-control prem_type_comm_rate"
                                name="prem_type_comm_rate[]" id="prem_type_comm_rate-0-0" data-counter="0"
                                placeholder="0.00" required>
                            <button class="btn btn-primary add-comm-section" type="button" id="add-comm-section-0-0"
                                data-counter="0" title="Add another commission rate">
                                <i class="bx bx-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    {{-- Edit Mode - Populate Existing Reinsurance Classes --}}
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
                        <label class="form-label required">Reinsurance Class</label>
                        <select class="form-control select2 treaty_reinclass" name="treaty_reinclass[]"
                            id="treaty_reinclass-{{ $index }}" data-counter="{{ $index }}" required>
                            <option value="">Choose Reinsurance Class</option>
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
                                    max="100" required>
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
                                    max="100" required>
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
                    </div>

                    @foreach ($classPremTypes as $premType)
                        <div class="comm-sections" id="comm-section-{{ $index }}-{{ $loop->index }}"
                            data-class-counter="{{ $index }}" data-counter="{{ $loop->index }}">
                            <div class="row g-3 mb-2">
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
                                    <label class="form-label required">Premium Type</label>
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

                                <div class="col-md-3 prem_type_comm_rate_div">
                                    <label class="form-label required">Commission (%)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control prem_type_comm_rate"
                                            name="prem_type_comm_rate[]"
                                            id="prem_type_comm_rate-{{ $index }}-{{ $loop->index }}"
                                            value="{{ number_format($premType->comm_rate, 2) }}" required>
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
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        {{-- No existing classes - show default template --}}
        <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            No reinsurance classes configured yet. The default class will be added when you select a reinsurance class
            above.
        </div>
    @endif
@endif

{{-- Information Card --}}
<div class="card bg-light mt-3">
    <div class="card-body">
        <h6 class="card-title">
            <i class="bx bx-info-circle me-2"></i>Treaty Structure Guide
        </h6>
        <div class="row">
            <div class="col-md-6">
                <p class="mb-2"><strong>Quota Share:</strong></p>
                <ul class="small mb-0">
                    <li>Fixed percentage of every risk</li>
                    <li>Simple proportional sharing</li>
                    <li>Retention + Treaty = 100%</li>
                    <li>Example: 30% retention, 70% treaty</li>
                </ul>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Surplus:</strong></p>
                <ul class="small mb-0">
                    <li>Based on multiples (lines) of retention</li>
                    <li>Varies by risk size</li>
                    <li>Treaty Limit = Lines × Retention</li>
                    <li>Example: 5 lines × $100,000 = $500,000</li>
                </ul>
            </div>
        </div>
        <hr>
        <p class="mb-0 small">
            <strong>Note:</strong> Combined treaties (SPQT) use both Quota Share and Surplus arrangements.
            The retention from Quota Share becomes the base for Surplus calculations.
        </p>
    </div>
</div>

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
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }

    .surplus-section {
        border-left-color: #28a745;
    }

    .commission-section {
        background-color: #fff;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #17a2b8;
    }

    .comm-sections {
        background-color: #f8f9fa;
        padding: 0.75rem;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        border: 1px solid #e0e0e0;
    }

    .quota-share-section h6,
    .surplus-section h6,
    .commission-section h6 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .card.bg-light {
        border-left: 4px solid #17a2b8;
        background-color: #f0f8ff !important;
    }
</style>
