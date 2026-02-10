<div class="row g-3">
    {{-- Cover Start Date --}}
    <div class="col-md-3">
        <label class="form-label required" for="coverfrom">Cover Start Date</label>
        <input type="date" class="form-control" id="coverfrom" name="coverfrom"
            value="{{ isset($old_endt_trans) ? $old_endt_trans->cover_from->format('Y-m-d') : '' }}"
            @if (!in_array($trans_type, ['NEW', 'REN', 'EDIT'])) readonly @endif required>
    </div>

    {{-- Cover End Date --}}
    <div class="col-md-3">
        <label class="form-label required" for="coverto">Cover End Date</label>
        <input type="date" class="form-control" id="coverto" name="coverto"
            value="{{ isset($old_endt_trans) ? $old_endt_trans->cover_to->format('Y-m-d') : '' }}"
            @if (!in_array($trans_type, ['NEW', 'REN', 'EDIT'])) readonly @endif required>
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
    </div>

    {{-- Cover Duration Display --}}
    <div class="col-md-3">
        <label class="form-label">Cover Duration</label>
        <input type="text" class="form-control" id="cover_duration" readonly placeholder="Auto-calculated">
    </div>
</div>
