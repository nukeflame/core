{{-- Common Treaty Fields --}}
<div id="treaty_common_section">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label required">Treaty Type</label>
            <div class="cover-card">
                <select class="form-control select2 required" name="treatytype" id="treatytype">
                    <option value="">Choose Treaty Type</option>
                    @foreach ($treatytypes as $treatytype)
                        <option value="{{ $treatytype->treaty_code }}"
                            {{ isset($old_endt_trans) && $old_endt_trans->treaty_code == $treatytype->treaty_code ? 'selected' : '' }}>
                            {{ $treatytype->treaty_name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="col-md-3">
            <label class="form-label required">Date Offered</label>
            <input type="date" class="form-control required" id="date_offered" name="date_offered"
                value="{{ isset($old_endt_trans) ? $old_endt_trans->date_offered : '' }}">
        </div>

        <div class="col-md-2">
            <label class="form-label required">Share Offered (%)</label>
            <input type="text" class="form-control required" id="share_offered" name="share_offered"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->share_offered, 2) : '' }}">
        </div>

        <div class="col-md-2">
            <label class="form-label required">Premium Tax Rate (%)</label>
            <input type="number" class="form-control required" id="prem_tax_rate" name="prem_tax_rate"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->prem_tax_rate, 2) : '' }}">
        </div>

        <div class="col-md-2">
            <label class="form-label required">RI Tax Rate (%)</label>
            <input type="number" class="form-control required" id="ri_tax_rate" name="ri_tax_rate" min="0"
                max="100"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->ri_tax_rate, 2) : '' }}">
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-3">
            <label class="form-label required">Brokerage Comm Rate (%)</label>
            <input type="number" class="form-control required" id="treaty_brokerage_comm_rate"
                name="treaty_brokerage_comm_rate" min="0" max="100"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->brokerage_comm_rate, 2) : '' }}">
        </div>

        <div class="col-md-3" id="reinsurer_per_treaty_section" style="display: none;">
            <label class="form-label required">Reinsurers Per Treaty?</label>
            <div class="cover-card">
                <select class="form-control select2 required" name="reinsurer_per_treaty" id="reinsurer_per_treaty">
                    <option value="">Select Option</option>
                    <option value="N"
                        {{ isset($old_endt_trans) && $old_endt_trans->reinsurer_per_treaty == 'N' ? 'selected' : 'selected' }}>
                        No</option>
                    <option value="Y"
                        {{ isset($old_endt_trans) && $old_endt_trans->reinsurer_per_treaty == 'Y' ? 'selected' : '' }}>
                        Yes
                    </option>
                </select>
            </div>
        </div>
    </div>
</div>

<div id="treaty_proportional_section" style="display: none;">
    <hr class="my-4">
    <h6 class="mb-3">Proportional Treaty Details</h6>

    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label required">Portfolio Premium Rate (%)</label>
            <input type="number" class="form-control required" id="port_prem_rate" name="port_prem_rate" max="100"
                min="0"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->port_prem_rate, 2) : '' }}">
        </div>

        <div class="col-md-3">
            <label class="form-label required">Portfolio Loss Rate (%)</label>
            <input type="number" class="form-control" id="port_loss_rate" name="port_loss_rate" max="100"
                min="0"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->port_loss_rate, 2) : '' }}">
        </div>

        <div class="col-md-3">
            <label class="form-label required">Profit Comm Rate (%)</label>
            <input type="number" class="form-control" id="profit_comm_rate" name="profit_comm_rate" max="100"
                min="0"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->profit_comm_rate, 2) : '' }}">
        </div>

        <div class="col-md-3">
            <label class="form-label required">Mgnt Expense Rate (%)</label>
            <input type="number" class="form-control required" id="mgnt_exp_rate" name="mgnt_exp_rate" max="100"
                min="0"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->mgnt_exp_rate, 2) : '' }}">
        </div>

        <div class="col-md-3">
            <label class="form-label required">Deficit C/F (years)</label>
            <input type="number" class="form-control required" id="deficit_yrs" name="deficit_yrs" min="0"
                max="10" value="{{ isset($old_endt_trans) ? $old_endt_trans->deficit_yrs : '' }}">
        </div>
    </div>

    {{-- Reinsurance Classes Container --}}
    <div id="reinsurance-classes-container" class="mt-3">
        @include('cover.partials.treaty-reinsurance-classes')
    </div>
</div>

{{-- Treaty Non-Proportional Section --}}
<div id="treaty_nonproportional_section" style="display: none;">
    <hr class="my-4">
    <h6 class="mb-3">Non-Proportional Treaty Details</h6>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label required">Reinsurance Classes</label>
            <div class="cover-card">
                <select class="form-control select2 required" name="reinclass_code[]" id="tnp_reinclass_code"
                    multiple>
                    @foreach ($reinsclasses as $reinsclass)
                        <option value="{{ $reinsclass->class_code }}">
                            {{ $reinsclass->class_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label required">Method</label>
            <div class="cover-card">
                <select name="method" id="method" class="form-control select2 required">
                    <option value="">Select Method</option>
                    <option value="B"
                        {{ isset($old_endt_trans) && $old_endt_trans->method == 'B' ? 'selected' : '' }}>
                        Burning Cost (B)
                    </option>
                    <option value="F"
                        {{ isset($old_endt_trans) && $old_endt_trans->method == 'F' ? 'selected' : '' }}>
                        Flat Rate (F)
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <button type="button" class="btn btn-primary" id="add-layer-section">
                <i class="bx bx-plus me-1"></i> Add Layer
            </button>
        </div>
    </div>

    {{-- Layers Container --}}
    <div id="layer-section" class="mt-3">
        @include('cover.partials.treaty-layers')
    </div>
</div>
