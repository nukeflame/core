<div class="row g-3">
    {{-- Cover Start Date --}}
    <div class="col-md-3">
        <label class="form-label required" for="coverfrom">Cover Start Date</label>
        <input type="date" class="form-control" id="coverfrom" name="coverfrom"
            value="{{ isset($old_endt_trans) ? $old_endt_trans->cover_from->format('Y-m-d') : '' }}"
            @if (!in_array($trans_type, ['NEW', 'REN', 'EDIT'])) readonly @endif required>
        <small class="text-muted">Date when insurance coverage begins</small>
    </div>

    {{-- Cover End Date --}}
    <div class="col-md-3">
        <label class="form-label required" for="coverto">Cover End Date</label>
        <input type="date" class="form-control" id="coverto" name="coverto"
            value="{{ isset($old_endt_trans) ? $old_endt_trans->cover_to->format('Y-m-d') : '' }}"
            @if (!in_array($trans_type, ['NEW', 'REN', 'EDIT'])) readonly @endif required>
        <small class="text-muted">Date when insurance coverage expires</small>
    </div>

    {{-- Underwriter --}}
    <div class="col-md-3">
        <label class="form-label" for="leadSource">Underwriter</label>
        <select class="form-control select2" id="leadSource" name="underwriter">
            <option value="">Select Staff</option>
            @if (!empty($staff))
                @foreach ($staff as $s)
                    <option value="{{ $s->id }}" {{ auth()->id() == $s->id ? 'selected' : '' }}>
                        {{ $s->name }}
                    </option>
                @endforeach
            @endif
        </select>
        <small class="text-muted">Staff member responsible for this cover</small>
    </div>

    {{-- Cover Duration Display --}}
    <div class="col-md-3">
        <label class="form-label">Cover Duration</label>
        <input type="text" class="form-control" id="cover_duration" readonly placeholder="Auto-calculated">
        <small class="text-muted">Total coverage period in days</small>
    </div>
</div>

{{-- <div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-info d-flex align-items-start">
            <i class="ri-information-line fs-18 me-2 mt-1"></i>
            <div>
                <strong>Coverage Period Guidelines:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    <li><small>Cover end date must be after the start date</small></li>
                    <li><small>Annual policies typically run for 365 days (1 year minus 1 day)</small></li>
                    <li><small>Short-term covers should specify exact duration needed</small></li>
                </ul>
            </div>
        </div>
    </div>
</div> --}}
