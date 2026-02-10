<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label required">Class Group</label>
        <div class="cover-card">
            <select class="form-control select2 @error('class_group') is-invalid @enderror" name="class_group"
                id="class_group" required>
                <option value="">Choose Class Group</option>
                @foreach ($classGroups as $classGroup)
                    <option value="{{ $classGroup->group_code }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->class_group_code == $classGroup->group_code ? 'selected' : '' }}>
                        {{ $classGroup->group_name }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('class_group')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required">Class Name</label>
        <div class="cover-card">
            <select class="form-control select2 @error('classcode') is-invalid @enderror" name="classcode"
                id="classcode" required>
                <option value="">Select Class Name</option>
            </select>
        </div>
        @error('classcode')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-{{ isset($prospectId) && $prospectId ? '2' : '3' }}">
        <label class="form-label required">Insured Name</label>
        <div class="cover-card">
            <select class="form-select select2 @error('insured_name') is-invalid @enderror" name="insured_name"
                id="insured_name" required>
                <option value="">Select Option</option>
                @foreach ($insured as $insured_name)
                    <option value="{{ $insured_name->name }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->insured_name == $insured_name->name ? 'selected' : '' }}>
                        {{ $insured_name->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('insured_name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @if (isset($prospectId) && $prospectId)
            <small class="text-muted d-block mt-1">
                Can't find the insurer?
                <a href="javascript:void(0)" id="createNewInsurer" class="text-primary">
                    <i class="bx bx-plus-circle"></i> Add New Insurer
                </a>
            </small>
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label required">Date Offered</label>
        <input type="date" class="form-control @error('fac_date_offered') is-invalid @enderror" id="fac_date_offered"
            name="fac_date_offered" value="{{ isset($old_endt_trans) ? $old_endt_trans->date_offered : '' }}" required>
        @error('fac_date_offered')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-3">
        <label class="form-label required">Sum Insured Type</label>
        <div class="cover-card">
            <select class="form-control select2 @error('sum_insured_type') is-invalid @enderror" name="sum_insured_type"
                id="sum_insured_type" required>
                <option value="">Choose Sum Insured Type</option>
                @foreach ($types_of_sum_insured as $type_of_sum_insured)
                    <option value="{{ $type_of_sum_insured->sum_insured_code }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->type_of_sum_insured == $type_of_sum_insured->sum_insured_code ? 'selected' : '' }}>
                        {{ $type_of_sum_insured->sum_insured_name }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('sum_insured_type')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required">100% Sum Insured <span id="sum_insured_label"></span></label>
        <input type="text" class="form-control amount @error('total_sum_insured') is-invalid @enderror"
            id="total_sum_insured" name="total_sum_insured"
            value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->total_sum_insured, 2) : '' }}" required>
        @error('total_sum_insured')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label required">Apply EML</label>
        <div class="cover-card">
            <select name="apply_eml" class="form-control select2 @error('apply_eml') is-invalid @enderror"
                id="apply_eml" required>
                <option value="">Select Option</option>
                <option value="Y"
                    {{ isset($old_endt_trans) && $old_endt_trans->apply_eml == 'Y' ? 'selected' : '' }}>
                    Yes</option>
                <option value="N"
                    {{ isset($old_endt_trans) && $old_endt_trans->apply_eml == 'N' ? 'selected' : '' }}>
                    No</option>
            </select>
        </div>
        @error('apply_eml')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2 eml-field" style="display: none;">
        <label class="form-label">EML Rate (%)</label>
        <input type="number" class="form-control" id="eml_rate" name="eml_rate"
            value="{{ isset($old_endt_trans) ? $old_endt_trans->eml_rate : 100 }}" min="0" max="100">
    </div>

    <div class="col-md-2 eml-field" style="display: none;">
        <label class="form-label">EML Amount</label>
        <input type="text" class="form-control amount" id="eml_amt" name="eml_amt"
            value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->eml_amount, 2) : '' }}" readonly>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-3">
        <label class="form-label">Effective Sum Insured</label>
        <input type="text" class="form-control amount" id="effective_sum_insured" name="effective_sum_insured"
            value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->eml_amount, 2) : '' }}" readonly>
    </div>

    <div class="col-md-9">
        <label class="form-label">Risk Details</label>
        <div class="risk-details-editor" id="risk_details_content" contenteditable="true"
            style="border: 1px solid #dee2e6; padding: 12px; min-height: 100px; border-radius: 4px; background-color: #fff;">
            {!! isset($old_endt_trans) ? $old_endt_trans->risk_details : '' !!}
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-3">
        <label class="form-label required">Cedant Premium</label>
        <input type="text" class="form-control amount @error('cede_premium') is-invalid @enderror"
            id="cede_premium" name="cede_premium"
            value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->cedant_premium, 2) : '' }}" required>
        @error('cede_premium')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required">Reinsurer Premium</label>
        <input type="text" class="form-control amount @error('rein_premium') is-invalid @enderror"
            id="rein_premium" name="rein_premium"
            value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->rein_premium, 2) : '' }}" required>
        @error('rein_premium')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required">Share Offered (%)</label>
        <input type="number" class="form-control @error('fac_share_offered') is-invalid @enderror"
            id="fac_share_offered" name="fac_share_offered" max="100" min="0"
            value="{{ isset($old_endt_trans) ? $old_endt_trans->share_offered : '' }}" required>
        @error('fac_share_offered')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-3">
        <label class="form-label required">Cedant Comm Rate (%)</label>
        <input type="text" class="form-control @error('comm_rate') is-invalid @enderror" id="comm_rate"
            name="comm_rate" value="{{ isset($old_endt_trans) ? $old_endt_trans->cedant_comm_rate : '' }}" required>
        @error('comm_rate')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Cedant Comm Amount</label>
        <input type="text" class="form-control amount" id="comm_amt" name="comm_amt"
            value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->cedant_comm_amount, 2) : '' }}"
            readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label required">Reinsurer Comm Type</label>
        <div class="cover-card">
            <select class="form-control select2 @error('reins_comm_type') is-invalid @enderror"
                name="reins_comm_type" id="reins_comm_type" required>
                <option value="">Choose Type</option>
                <option value="R"
                    {{ isset($old_endt_trans) && $old_endt_trans->rein_comm_type == 'R' ? 'selected' : '' }}>Rate
                </option>
                <option value="A"
                    {{ isset($old_endt_trans) && $old_endt_trans->rein_comm_type == 'A' ? 'selected' : '' }}>Amount
                </option>
            </select>
        </div>
        @error('reins_comm_type')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 reins-comm-rate-field" style="display: none;">
        <label class="form-label">Reinsurer Comm Rate (%)</label>
        <input type="text" class="form-control" id="reins_comm_rate" name="reins_comm_rate"
            value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->rein_comm_rate, 2) : '' }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Reinsurer Comm Amount</label>
        <input type="text" class="form-control amount" id="reins_comm_amt" name="reins_comm_amt"
            value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->rein_comm_amount, 2) : '' }}">
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-3">
        <label class="form-label required">Brokerage Comm Type</label>
        <select name="brokerage_comm_type" id="brokerage_comm_type"
            class="form-control select2 @error('brokerage_comm_type') is-invalid @enderror" required>
            <option value="">Select Basis</option>
            <option value="R"
                {{ isset($old_endt_trans) && $old_endt_trans->brokerage_comm_type == 'R' ? 'selected' : '' }}>
                Rate (Reinsurer - Cedant)
            </option>
            <option value="A"
                {{ isset($old_endt_trans) && $old_endt_trans->brokerage_comm_type == 'A' ? 'selected' : '' }}>
                Quoted Amount
            </option>
        </select>
        @error('brokerage_comm_type')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 brokerage-rate-field" style="display: none;">
        <label class="form-label">Brokerage Rate (%)</label>
        <input type="text" class="form-control amount" id="brokerage_comm_rate" name="brokerage_comm_rate"
            value="{{ isset($old_endt_trans) ? number_format($old_endt_trans->brokerage_comm_rate, 2) : '' }}"
            readonly>
    </div>

    <div class="col-md-3 brokerage-rate-amount-field" style="display: none;">
        <label class="form-label">Brokerage Amount</label>
        <input type="text" class="form-control amount" id="brokerage_comm_rate_amnt"
            name="brokerage_comm_rate_amnt" readonly>
    </div>

    <div class="col-md-3 brokerage-amount-field" style="display: none;">
        <label class="form-label">Brokerage Amount</label>
        <input type="text" class="form-control amount" id="brokerage_comm_amt" name="brokerage_comm_amt"
            value="{{ isset($old_endt_trans) ? $old_endt_trans->brokerage_comm_amt : '0' }}">
    </div>
</div>

<style>
    #createNewInsurer {
        font-family: inherit;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
    }

    #createNewInsurer:hover {
        text-decoration: underline;
    }

    .risk-details-editor:focus {
        outline: 2px solid #0d6efd;
        outline-offset: -1px;
    }

    .modal-header.bg-primary {
        background-color: #0d6efd !important;
    }
</style>
