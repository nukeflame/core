{{-- Common Treaty Fields --}}
<div id="treaty_common_section">
    <div class="row g-3">
        {{-- Treaty Type --}}
        <div class="col-md-3">
            <label class="form-label required">Treaty Type</label>
            <select class="form-control select2 required" name="treatytype" id="treatytype">
                <option value="">Choose Treaty Type</option>
                @foreach ($treatytypes as $treatytype)
                    <option value="{{ $treatytype->treaty_code }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->treaty_code == $treatytype->treaty_code ? 'selected' : '' }}>
                        {{ $treatytype->treaty_name }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Select the type of reinsurance treaty agreement</small>
        </div>

        {{-- Date Offered --}}
        <div class="col-md-3">
            <label class="form-label required">Date Offered</label>
            <input type="date" class="form-control required" id="date_offered" name="date_offered"
                value="{{ isset($old_endt_trans) ? $old_endt_trans->date_offered : '' }}">
            <small class="text-muted">Date when the treaty was offered to reinsurers</small>
        </div>

        {{-- Share Offered --}}
        <div class="col-md-2">
            <label class="form-label required">Share Offered (%)</label>
            <input type="number" class="form-control required" id="share_offered" name="share_offered" min="0"
                max="100" step="0.01"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->share_offered, 2) : '' }}">
            <small class="text-muted">Percentage of risk offered (0-100)</small>
        </div>

        {{-- Territorial Scope --}}
        <div class="col-md-2">
            <label class="form-label required">Territorial Scope</label>
            <select class="form-control select2 required" multiple name="territorial_scope[]" id="territorial_scope">
                <option value="">Select Option</option>
                <option value="WORLDWIDE">WORLDWIDE</option>
                <option value="WORLDWIDE EXCL. USA/CANADA">WORLDWIDE EXCL. USA/CANADA</option>
                <option value="WEST AFRICA">WEST AFRICA</option>
                <option value="UGANDA">UGANDA</option>
                <option value="TANZANIA">TANZANIA</option>
                <option value="RWANDA">RWANDA</option>
                <option value="BURUNDI">BURUNDI</option>
                <option value="SOUTH SUDAN">SOUTH SUDAN</option>
                <option value="Democratic Republic of Congo (DRC)">Democratic Republic of Congo (DRC)</option>
                <option value="KENYA" selected>KENYA</option>
            </select>
            <small class="text-muted">Geographic coverage area</small>
        </div>

        {{-- <div class="col-md-2">
            <label class="form-label required">Premium Tax Rate (%)</label>
            <input type="number" class="form-control required" id="prem_tax_rate" name="prem_tax_rate"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->prem_tax_rate, 2) : '' }}">
        </div>

        <div class="col-md-2">
            <label class="form-label required">RI Tax Rate (%)</label>
            <input type="number" class="form-control required" id="ri_tax_rate" name="ri_tax_rate" min="0"
                max="100"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->ri_tax_rate, 2) : '' }}">
        </div> --}}

        {{-- Basis of Acceptance --}}
        {{-- <div class="col-md-2">
            <label class="form-label required">Basis of Acceptance</label>
            <select class="form-control select2 required" name="basis_of_acceptance" id="basis_of_acceptance">
                <option value="">Select an option</option>
                <option value="DECLARATIONS">DECLARATIONS</option>
                <option value="FACULTATIVE OBLIGATORY">FACULTATIVE OBLIGATORY</option>
                <option value="OBLIGATORY">OBLIGATORY</option>
            </select>
            <small class="text-muted">How risks are accepted under treaty</small>
        </div> --}}
    </div>

    <div class="row g-3 mt-2">
        {{-- Brokerage Rate --}}
        <div class="col-md-3">
            <label class="form-label required">Brokerage Rate (%)</label>
            <input type="number" class="form-control required" id="treaty_brokerage_comm_rate"
                name="treaty_brokerage_comm_rate" min="0" max="100" step="0.01"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->brokerage_comm_rate, 2) : '' }}">
            <small class="text-muted">Commission paid to brokers (0-100)</small>
        </div>

        {{-- Reinsurers Per Treaty --}}
        <div class="col-md-3" id="reinsurer_per_treaty_section" style="display: none;">
            <label class="form-label required">Reinsurers Per Treaty?</label>
            <select class="form-control select2 required" name="reinsurer_per_treaty" id="reinsurer_per_treaty">
                <option value="">Select Option</option>
                <option value="N"
                    {{ isset($old_endt_trans) && $old_endt_trans->reinsurer_per_treaty == 'N' ? 'selected' : 'selected' }}>
                    No
                </option>
                <option value="Y"
                    {{ isset($old_endt_trans) && $old_endt_trans->reinsurer_per_treaty == 'Y' ? 'selected' : '' }}>
                    Yes
                </option>
            </select>
            <small class="text-muted">Multiple reinsurers participating in treaty</small>
        </div>
    </div>
</div>

{{-- Proportional Treaty Section --}}
<div id="treaty_proportional_section" style="display: none;">
    <hr class="my-4">
    <h6 class="mb-0">Proportional Treaty Details</h6>
    <p class="text-muted small mb-3">
        Proportional treaties distribute premiums and claims proportionally. The reinsurer receives a percentage of
        premiums and pays the same percentage of losses based on the agreed participation rate.
    </p>

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
            <label class="form-label required">Profit Commission Rate (%)</label>
            <input type="number" class="form-control" id="profit_comm_rate" name="profit_comm_rate" max="100"
                min="0"
                value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->profit_comm_rate, 2) : '' }}">
        </div>

        <div class="col-md-3">
            <label class="form-label required">Management Expense Rate (%)</label>
            <input type="number" class="form-control required" id="mgnt_exp_rate" name="mgnt_exp_rate"
                max="100" min="0"
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

{{-- Non-Proportional Treaty Section --}}
<div id="treaty_nonproportional_section" style="display: none;">
    <hr class="my-4">
    <h6 class="mb-3">Non-Proportional Treaty Details</h6>
    <p class="text-muted small mb-3">
        Non-proportional treaties provide coverage above a retention limit, triggered when losses exceed threshold
    </p>

    <div class="row g-3">
        {{-- Reinsurance Classes --}}
        <div class="col-md-6">
            <label class="form-label required">Reinsurance Classes</label>
            <select class="form-control select2 required" name="reinclass_code[]" id="tnp_reinclass_code" multiple>
                @foreach ($reinsclasses as $reinsclass)
                    <option value="{{ $reinsclass->class_code }}">
                        {{ $reinsclass->class_name }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Select one or more classes covered by this treaty</small>
        </div>

        {{-- Method --}}
        <div class="col-md-6">
            <label class="form-label required">Method</label>
            <select name="method" id="method" class="form-control select2 required">
                <option value="">Select Method</option>
                <option value="B"
                    {{ isset($old_endt_trans) && $old_endt_trans->method == 'B' ? 'selected' : '' }}>
                    Burning Cost
                </option>
                <option value="F"
                    {{ isset($old_endt_trans) && $old_endt_trans->method == 'F' ? 'selected' : '' }}>
                    Flat Rate
                </option>
            </select>
            <small class="text-muted">
                <strong>Burning Cost:</strong> Based on historical claims |
                <strong>Flat Rate:</strong> Fixed premium rate
            </small>
        </div>
    </div>

    {{-- Add Layer Button --}}
    <div class="row mt-4">
        <div class="col-12">
            <button type="button" class="btn btn-primary" id="add-layer-section">
                <i class="bx bx-plus me-1"></i> Add Layer
            </button>
            <small class="text-muted d-block mt-2">
                Layers define coverage bands with specific retention and limit amounts
            </small>
        </div>
    </div>

    {{-- Layers Container --}}
    <div id="layer-section" class="mt-3">
        @include('cover.partials.treaty-layers')
    </div>
</div>
