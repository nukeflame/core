<div class="row g-3">
    {{-- Business Type --}}
    <div class="col-md-3">
        <label class="form-label required">Business Type</label>
        <div class="cover-card">
            <select class="form-control select2" name="type_of_bus" id="type_of_bus" required
                @if (!in_array($trans_type, ['REN', 'EDIT', 'NEW'])) disabled @endif>
                <option value="">Choose Business Type</option>
                @foreach ($types_of_bus as $type_of_bus)
                    <option value="{{ $type_of_bus->bus_type_id }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->type_of_bus == $type_of_bus->bus_type_id ? 'selected' : '' }}>
                        {{ $type_of_bus->bus_type_name }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('type_of_bus')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Cover Type --}}
    <div class="col-md-3">
        <label class="form-label required">Cover Type</label>
        <div class="cover-card">
            <select class="form-control select2" name="covertype" id="covertype" required>
                <option value="">Choose Cover Type</option>
                @foreach ($covertypes as $covertype)
                    @if ($covertype->status === 'A')
                        <option value="{{ $covertype->type_id }}" data-description="{{ $covertype->short_description }}"
                            {{ isset($old_endt_trans) && $old_endt_trans->cover_type == $covertype->type_id ? 'selected' : '' }}>
                            {{ $covertype->type_name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        @error('covertype')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Binder Policy --}}
    <div class="col-md-2" id="binder_section" style="display: none;">
        <label class="form-label required">Binder Policy</label>
        <div class="cover-card">
            <select class="form-control select2" name="bindercoverno" id="bindercoverno">
                <option value="">Select Binder</option>
                @if (isset($old_endt_trans) && $old_endt_trans->binder_cov_no)
                    <option value="{{ $old_endt_trans->binder_cov_no }}" selected>
                        {{ $old_endt_trans->binder_cov_no }}
                    </option>
                @endif
            </select>
        </div>
    </div>

    {{-- Branch --}}
    <div class="col-md-2">
        <label class="form-label required">Branch</label>
        <div class="cover-card">
            <select class="form-control select2" name="branchcode" id="branchcode" required>
                <option value="">Choose Branch</option>
                @foreach ($branches as $branch)
                    @if ($branch->status === 'A')
                        <option value="{{ $branch->branch_code }}"
                            {{ isset($old_endt_trans) && $old_endt_trans->branch_code == $branch->branch_code ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        @error('branchcode')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Ceding Broker Flag --}}
    <div class="col-md-2">
        <label class="form-label required">Ceding Broker Flag</label>
        <div class="cover-card">
            <select class="form-control select2" name="broker_flag" id="broker_flag" required>
                <option value="">Select Option</option>
                <option value="N"
                    {{ isset($old_endt_trans) && $old_endt_trans->broker_flag == 'N' ? 'selected' : '' }}>No</option>
                <option value="Y"
                    {{ isset($old_endt_trans) && $old_endt_trans->broker_flag == 'Y' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
    </div>

    {{-- Prospect Reference ID --}}
    <div class="col-md-2">
        <label class="form-label required">Prospect Ref ID</label>
        <input type="text" name="prospect_id" id="prospect_id" class="form-control" placeholder="Enter Prospect ID"
            value="{{ $prospectId ?? '' }}">
    </div>

    {{-- Ceding Broker --}}
    <div class="col-md-3" id="broker_section" style="display: none;">
        <label class="form-label required">Ceding Broker</label>
        <div class="cover-card">
            <select class="form-control select2" name="brokercode" id="brokercode">
                <option value="">Choose Ceding Broker</option>
                @foreach ($brokers as $broker)
                    <option value="{{ $broker->broker_code }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->broker_code == $broker->broker_code ? 'selected' : '' }}>
                        {{ $broker->broker_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Division --}}
    <div class="col-md-3">
        <label class="form-label required">Division</label>
        <div class="cover-card">
            <select class="form-control select2" name="division" id="division" required>
                <option value="">Choose Division</option>
                @foreach ($reinsdivisions as $division)
                    <option value="{{ $division->division_code }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->division_code == $division->division_code ? 'selected' : '' }}>
                        {{ $division->division_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
