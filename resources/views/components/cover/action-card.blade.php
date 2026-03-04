@props([
    'cover',
    'endorsementNarration',
    'pendingApproverId' => null,
    'isTransaction' => false,
    'isCoverStatus' => true,
])

@php
    $isUnverified = in_array($cover->verified, [null, 'R']);
    $isPending = $cover->verified === 'P';
    $isApproved = $cover->verified === 'A';
    $isNotCancelled = $cover->status !== 'cancelled';

    $isFacultative = in_array($cover->type_of_bus, ['FPR', 'FNP']);
    $isTreaty = in_array($cover->type_of_bus, ['TNP', 'TPR']);
    $isNewOrRenewal = in_array($cover->transaction_type, ['NEW', 'REN']);
    $isNewRenewalOrExtension = in_array($cover->transaction_type, ['NEW', 'REN', 'EXT']);

    $statusConfig = [
        null => ['badge' => 'bg-danger', 'text' => 'Pending'],
        'R' => ['badge' => 'bg-danger', 'text' => 'Rejected'],
        'P' => ['badge' => 'bg-warning', 'text' => 'Awaiting Verification'],
        'A' => ['badge' => 'bg-success', 'text' => 'Approved'],
    ];

    logger($cover->verified);

    $currentStatus = $statusConfig[$cover->verified] ?? ['badge' => 'bg-secondary', 'text' => 'Unknown'];
@endphp

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 pb-2 px-0 pt-4">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-lightning-charge-fill"></i> Quick Actions
        </h6>
    </div>

    <div class="card-body px-2 pt-2 mx-0 cover-info-wrapper"
        style="background-color:var(--cover-bg);border-radius:0.375rem;">

        @if ($isUnverified)
            <button type="button" class="btn btn-outline-dark btn-sm text-start me-2" data-action="edit"
                data-endorsement="{{ $cover->endorsement_no }}">
                <i class="ri-edit-line me-2"></i>Edit Cover Details
            </button>
        @endif

        @if ($isFacultative)
            @if ($isUnverified)
                <button type="button" class="btn btn-outline-dark btn-sm text-start me-2" data-bs-toggle="modal"
                    data-bs-target="#addScheduleModal">
                    <i class="ri-table-line me-2"></i>Add Schedule Details
                </button>

                <button type="button" class="btn btn-outline-dark btn-sm text-start me-2" data-bs-toggle="modal"
                    data-bs-target="#addAttachemntModal">
                    <i class="ri-file-text-line me-2"></i>Add File &amp; Supporting Docs
                </button>
                {{--
                <button type="button" class="btn btn-outline-dark btn-sm text-start me-2" data-bs-toggle="modal"
                    data-bs-target="#addClauseModal">
                    <i class="ri-file-text-line me-2"></i>Add Policy Clauses
                </button> --}}

                {{-- <button type="button" class="btn btn-outline-dark btn-sm text-start me-2" data-bs-toggle="modal"
                    data-bs-target="#addAttachmentModal">
                    <i class="ri-upload-line me-2"></i>Upload Document
                </button> --}}
            @endif
        @endif

        @if ($isTreaty && $isNewOrRenewal && $isUnverified)
            <button class="btn btn-outline-dark btn-sm me-2" data-bs-toggle="modal"
                data-bs-target="#insurance-class-modal">
                <i class="ri-list-check me-2"></i>Classes of Insurance
            </button>

            @if ($cover->type_of_bus === 'TNP')
                <button class="btn btn-outline-dark btn-sm me-2" data-bs-toggle="modal"
                    data-bs-target="#mdpInstallmentModal">
                    <i class="ri-calendar-line me-2"></i>MDP Installments
                </button>
            @endif
        @endif

        @if ($isUnverified)
            <button type="button" class="btn btn-outline-success btn-sm text-start me-2" data-bs-toggle="modal"
                data-bs-target="#addReinsurerModal">
                <i class="ri-team-line me-2"></i>Add Reinsurers
            </button>

            <button type="button" class="btn btn-outline-secondary btn-sm text-start me-2" data-action="generate-slip"
                data-endorsement="{{ $cover->endorsement_no }}">
                <i class="ri-file-pdf-line me-2" style="vertical-align: -1px;"></i>Generate Slip
            </button>

            <button type="button" class="btn btn-warning btn-sm text-start" data-bs-toggle="modal"
                data-bs-target="#verificationModal">
                <i class="ri-send-plane-line me-2"></i>Submit for Verification
            </button>
        @endif

        @if ($isPending)
            <button class="badge bg-warning text-dark" disabled style="cursor: default;">
                <i class="ri-time-line me-1"></i>Awaiting Verification
            </button>
            <button type="button" class="btn btn-outline-dark btn-sm me-2" id="verify_detailss" data-bs-toggle="modal"
                data-bs-target="#verificationModal" data-exclude-approver-id="{{ $pendingApproverId ?? '' }}">
                <i class="ri-arrow-up-circle-line me-2"></i>Re-escalate Verification
            </button>
        @endif

        @if ($isApproved && $isNotCancelled)
            @if ($isFacultative || (!$isFacultative && !$isNewOrRenewal))
                <button class="btn btn-outline-dark btn-sm me-2" data-bs-toggle="modal" data-bs-target="#facDebitModal">
                    <i class="bi bi-cash-stack me-1"></i>Generate Debit
                </button>
            @elseif (!$isFacultative && $isNewRenewalOrExtension)
                @if ($isTransaction)
                    <button type="button" class="btn btn-dark btn-sm text-center w-10 me-1" data-bs-toggle="modal"
                        data-bs-target="#createQuarterlyFiguresModal">
                        <i class="bi bi-calculator me-1"></i>Quarterly Figures
                    </button>
                    <button type="button" class="btn btn-dark btn-sm text-center w-10 me-1" data-bs-toggle="modal"
                        data-bs-target="#addProfitCommissionModal">
                        <i class="bi bi-percent me-1"></i>Profit Commission
                    </button>
                    <button type="button" class="btn btn-dark btn-sm text-center w-10 me-1" data-bs-toggle="modal"
                        data-bs-target="#addPortfolioModal">
                        <i class="bi bi-briefcase me-1"></i>Portfolio
                    </button>
                    {{-- @if ($cover->type_of_bus === 'TPR')
                        <button type="button" class="btn btn-dark btn-sm text-center w-10 me-1"
                            data-bs-toggle="modal" data-bs-target="#adjustCommissionModal">
                            <i class="bi bi-arrow-repeat me-1"></i>Adjust Commission
                        </button>
                    @endif --}}
                @else
                    <button class="btn btn-dark btn-sm me-2" data-bs-toggle="modal" data-bs-target="#treatyDebitModal">
                        <i class="bi bi-cash-stack me-1"></i>Generate Debit
                    </button>
                @endif

                {{-- <button type="button" class="btn btn-secondary btn-sm me-2 text-center w-10" data-action="preview-slip"
                    data-endorsement="{{ $cover->endorsement_no }}">
                    <i class="bi bi-file-earmark-text"></i> Preview Slip
                </button> --}}

            @endif
        @endif

        @if ($isCoverStatus)
            <div class="mt-3 pt-3 border-top">
                <span class="text-muted d-block mb-2 fs-14">
                    <i class="ri-information-line me-1 fs-14" style="vertical-align: -2px;"></i>Cover Status
                </span>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Status:</small>
                                <span class="badge {{ $currentStatus['badge'] }}">
                                    {{ $currentStatus['text'] }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Created:</small>
                                <small class="fw-semibold">
                                    {{ $cover->created_at->format('d M Y') }}
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Last Updated:</small>
                                <small class="fw-semibold">
                                    {{ $cover->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>

                    @if ($isApproved && $cover->approved_by)
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Approved By:</small>
                                <small class="fw-semibold">
                                    {{ $cover->approver->name ?? 'N/A' }}
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@push('script')
    <script>
        (function() {
            'use strict';

            const CoverActions = {
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,

                init() {
                    if (!this.csrfToken) {
                        console.error('CSRF token not found');
                        return;
                    }
                    this.bindEvents();
                },

                bindEvents() {
                    document.querySelectorAll('[data-action]').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const action = e.currentTarget.dataset.action;
                            const endorsement = e.currentTarget.dataset.endorsement;
                            this.handleAction(action, endorsement);
                        });
                    });
                },

                handleAction(action, endorsementNo) {
                    const actionMap = {
                        'edit': () => this.editCover(endorsementNo),
                        'generate-slip': () => this.generateDocument(endorsementNo, 'slip'),
                        'preview-slip': () => this.generateDocument(endorsementNo, 'slip', true),
                        'generate-debit': () => this.generateDocument(endorsementNo, 'debit'),
                        'generate-profit-commission': () => this.generateDocument(endorsementNo,
                            'profit-commission'),
                        'generate-portfolio': () => this.generateDocument(endorsementNo, 'portfolio'),
                        'generate-commission-adjustment': () => this.generateDocument(endorsementNo,
                            'commission-adjustment')
                    };

                    const handler = actionMap[action];
                    if (handler) {
                        handler();
                    } else {
                        console.warn(`Unknown action: ${action}`);
                    }
                },

                editCover(endorsementNo) {
                    window.location.href = `/cover/${endorsementNo}/edit`;
                },

                generateDocument(endorsementNo, type, isPreview = false) {
                    const messages = {
                        'slip': 'Generate placement slip for this cover?',
                        'debit': 'Generate debit note for this cover?',
                        'profit-commission': 'Generate profit commission report?',
                        'portfolio': 'Generate portfolio report?',
                        'commission-adjustment': 'Generate commission adjustment?'
                    };

                    const message = messages[type] || 'Generate document?';

                    if (type === 'slip') {
                        const slipUrl = '/doc/coverslip/facultative';
                        const popupFeatures = [
                            'popup=yes',
                            'width=1280',
                            'height=900',
                            'left=120',
                            'top=80',
                            'resizable=yes',
                            'scrollbars=yes',
                            'noopener=yes',
                            'noreferrer=yes'
                        ].join(',');
                        const previewWindow = window.open(
                            'about:blank',
                            'facultative_coverslip_window',
                            popupFeatures
                        );

                        if (!previewWindow) {
                            this.showAlert('Popup blocked', 'Please allow popups for this site.', 'warning');
                            return;
                        }

                        previewWindow.location.href = slipUrl;
                        previewWindow.focus();
                        return;
                    }

                    if (type === 'debit') {
                        $('#treatyDebitModal').modal('show');
                    }

                    // const action = isPreview ? 'Preview' : 'Generate';

                    // if (confirm(`${action}: ${message}`)) {
                    //     const url = `/covers/${endorsementNo}/generate-${type}`;
                    //     window.open(url, '_blank');
                    // }
                },

                async apiRequest(url, data = {}) {
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        return await response.json();
                    } catch (error) {
                        console.error('API request failed:', error);
                        throw error;
                    }
                },

                showAlert(title, text, icon = 'info') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title,
                            text,
                            icon
                        });
                    } else {
                        alert(`${title}: ${text}`);
                    }
                }
            };

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => CoverActions.init());
            } else {
                CoverActions.init();
            }

            // Expose for legacy compatibility
            window.CoverActions = CoverActions;
        })();
    </script>
@endpush

<style>
    .cover-info-wrapper .btn {
        border-radius: 0.375rem;
        transition: all 0.2s ease-in-out;
    }

    .cover-info-wrapper .btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .cover-info-wrapper .btn:active:not(:disabled) {
        transform: translateY(0);
    }

    .cover-info-wrapper .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    .cover-info-wrapper small {
        font-size: 0.8125rem;
    }

    .cover-info-wrapper .border-top {
        border-color: rgba(0, 0, 0, 0.1) !important;
    }

    .cover-info-wrapper .btn.btn-sm i {
        vertical-align: -1px;
    }

    .w-10 {
        width: 235px !important;
    }
</style>
