<div class="modal fade reinsurer-wrapper-modal" id="addReinsurerModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="reinsurerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 70%">
        <div class="modal-content">
            <form method="POST" id="reinsurerForm" data-url="{{ route('cover.save_reinsurance_data') }}"
                data-edit-url="{{ route('cover.edit_reinsurance_data') }}">
                @csrf
                <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                <input type="hidden" name="edit_mode" id="edit_mode" value="0">
                <input type="hidden" name="edit_tran_no" id="edit_tran_no" value="">
                <input type="hidden" name="edit_partner_no" id="edit_partner_no" value="">

                <div class="modal-header bg-primary text-white">
                    <div>
                        <h6 class="modal-title" id="reinsurerModalLabel">
                            <i class="fa fa-handshake"></i> Reinsurance Placement
                        </h6>
                        <small class="d-block mt-1">Cover: {{ $cover->cover_no }} | Endorsement:
                            {{ $cover->endorsement_no }}</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body customScrollBar" data-cedant-comm-rate="{{ $cover->cedant_comm_rate }}">
                    <div id="reinsurer-modal-loader" class="reinsurer-modal-loader d-none" aria-live="polite"
                        aria-label="Loading reinsurer placement">
                        <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
                        <span class="ms-2">Loading placement data...</span>
                    </div>
                    <div class="modal-cover-section">
                        @if (isset($coverTreaties) && $coverTreaties->count() > 1)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button class="btn btn-success btn-sm" type="button" id="add-treaty-reinsurer">
                                        <i class="fa fa-plus-circle"></i> Add Treaty-Reinsurer Section
                                    </button>
                                </div>
                            </div>
                        @endif
                        <div id="treaty-div" class="reinsure-model-container">
                            @include('cover.modals.partials.reinsurer-treaty-section', [
                                'counter' => 0,
                                'cover' => $cover,
                                'coverTreaties' => $coverTreaties,
                                'reinsurers' => $reinsurers,
                                'coverpart' => $coverpart,
                                'whtRates' => $whtRates,
                                'paymethods' => $paymethods,
                            ])
                        </div>

                        {{-- @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                            <div id="distribution-summary" class="mt-2">
                                @include('cover.modals.partials.reinsurer-distribution-summary')
                            </div>
                        @endif --}}

                        <div id="validation-messages" class="mt-2" style="display: none;">
                            <div class="alert alert-danger" role="alert">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Validation Errors:</strong>
                                <ul id="validation-list" class="mb-0 mt-2"></ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i>
                                All fields marked with <span class="text-danger">*</span> are required
                            </small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                                id="dismiss-partner-btn">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <button type="submit" id="partner-save-btn" class="btn btn-primary btn-sm">
                                <i class="fa fa-save"></i> Save Placement
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="treaty-section-template">
    @include('cover.modals.partials.reinsurer-treaty-section', [
        'counter' => 'COUNTER_PLACEHOLDER',
        'cover' => $cover,
        'coverTreaties' => $coverTreaties,
        'reinsurers' => $reinsurers,
        'coverpart' => $coverpart,
        'whtRates' => $whtRates,
        'paymethods' => $paymethods,
        'isTemplate' => true,
    ])
</template>

<template id="reinsurer-row-template">
    @include('cover.modals.partials.reinsurer-row', [
        'treatyCounter' => 'TREATY_COUNTER_PLACEHOLDER',
        'counter' => 'COUNTER_PLACEHOLDER',
        'cover' => $cover,
        'reinsurers' => $reinsurers,
        'coverpart' => $coverpart,
        'whtRates' => $whtRates,
        'paymethods' => $paymethods,
        'isTemplate' => true,
    ])
</template>

<style>
    .modal-body {
        max-height: calc(100vh - 190px);
        overflow-x: hidden;
        overflow-y: auto;
    }

    .rein-modal .card-header:first-child {
        border-radius: 0px !important;
    }

    #addReinsurerModal .modal-body {
        position: relative;
    }

    #addReinsurerModal .reinsurer-modal-loader {
        position: absolute;
        inset: 0;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.72);
        font-weight: 600;
    }

    #addReinsurerModal.reinsurer-loading .modal-cover-section {
        opacity: 0.55;
        pointer-events: none;
    }
</style>


@push('script')
@endpush
