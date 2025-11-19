<div class="d-flex gap-2">
    @switch($trans_type)
        @case('NEW')
            <button type="submit" id="save_cover" class="btn btn-success btn-lg">
                <i class="bi bi-save me-2"></i>Save Cover
            </button>
        @break

        @case('EDIT')
            <button type="submit" id="save_cover" class="btn btn-primary btn-lg">
                <i class="bi bi-pencil me-2"></i>Update Details
            </button>
        @break

        @case('EXT')
        @case('CNC')

        @case('RFN')
        @case('INS')
            <button type="button" id="ext_cover" class="btn btn-primary btn-lg">
                <i class="bi bi-save me-2"></i>Save Endorsement
            </button>
        @break

        @case('REN')
            <button type="button" id="save_cover" class="btn btn-success btn-lg">
                <i class="bi bi-arrow-repeat me-2"></i>Renew Policy
            </button>
        @break

        @default
            <button type="button" id="save_cover" class="btn btn-primary btn-lg">
                <i class="bi bi-save me-2"></i>Save
            </button>
    @endswitch

    <a href="{{ route('customer.info') }}" class="btn btn-secondary btn-lg">
        <i class="bi bi-x-circle me-2"></i>Cancel
    </a>
</div>

<div class="mt-3">
    <small class="text-muted">
        <i class="bx bx-info-circle me-1"></i>
        All fields marked with <span class="text-danger">*</span> are required.
    </small>
</div>
