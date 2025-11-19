@props(['cover', 'endorsementNarration'])

<div class="card border-0 shadow-sm mb-3 mb-2">
    <div class="card-header bg-white border-0 pb-2 px-0 pt-2">
        <h6 class="mb-0 fw-semibold">
            <i class="ri-settings-3-line me-2" style="vertical-align: -2px;"></i>Quick Actions
        </h6>
    </div>
    <div class="card-body px-0 pt-2 mx-0 cover-info-wrapper"
        style="background-color:var(--cover-bg);border-radius:0.375rem;">

        @if (in_array($cover->verified, [null, 'R']))
            <button type="button" class="btn btn-outline-dark btn-sm text-start me-2"
                onclick="editCover('{{ $cover->endorsement_no }}')">
                <i class="ri-edit-line me-2"></i>Edit Cover Details
            </button>
        @endif

        @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
            <button type="button" class="btn btn-outline-dark btn-sm text-start me-2" data-bs-toggle="modal"
                data-bs-target="#addScheduleModal">
                <i class="ri-table-line me-2"></i>Add Schedule
            </button>

            <button type="button" class="btn btn-outline-dark btn-sm text-start me-2" data-bs-toggle="modal"
                data-bs-target="#addClauseModal">
                <i class="ri-file-text-line me-2"></i>Add Clause
            </button>

            <button type="button" class="btn btn-outline-dark btn-sm text-start me-2" data-bs-toggle="modal"
                data-bs-target="#addAttachmentModal">
                <i class="ri-upload-line me-2"></i>Upload Document
            </button>
        @endif

        @if (in_array($cover->type_of_bus, ['TNP']))
            @if (in_array($cover->transaction_type, ['NEW', 'REN']))
                @if ($cover->verified == null)
                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                        id="insurance_classes" data-bs-toggle="modal" data-bs-target="#insurance-class-modal">
                        <i class="bx bx-plus me-1 align-middle"></i> Classes of Insurance
                    </button>
                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                        id="mdp_installments" data-bs-toggle="modal" data-bs-target="#mdpInstallmentModal">
                        <i class="bx bx-plus me-1 align-middle"></i> MDP Installments
                    </button>
                @endif
            @endif
        @endif

        @if (in_array($cover->type_of_bus, ['TPR']))
            @if (in_array($cover->transaction_type, ['NEW', 'REN']))
                @if ($cover->verified == null)
                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                        id="insurance_classes" data-bs-toggle="modal" data-bs-target="#insurance-class-modal">
                        <i class="bx bx-check-circle me-1 align-middle"></i> Classes of Insurance
                    </button>
                @endif
            @endif
        @endif

        @if ($cover->verified == null)
            <button type="button" class="btn btn-outline-success btn-sm text-start me-2" data-bs-toggle="modal"
                data-bs-target="#addReinsurerModal">
                <i class="ri-team-line me-2"></i>Add Reinsurer
            </button>

            <button type="button" class="btn btn-outline-secondary btn-sm text-start me-2"
                onclick="generateSlip('{{ $cover->endorsement_no }}')">
                <i class="ri-file-pdf-line me-2"></i>Generate Slip
            </button>
        @endif


        {{-- @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
            <button type="button" class="btn btn-outline-secondary btn-sm text-start me-2"
                onclick="generateSlip('{{ $cover->endorsement_no }}')">
                <i class="ri-file-pdf-line me-2"></i>Generate Slip
            </button>
            {{--
            <button type="button" class="btn btn-outline-secondary btn-sm text-start me-2" data-bs-toggle="modal"
                data-bs-target="#generateDebitModal">
                <i class="ri-file-list-3-line me-2"></i>Generate Debit Note
            </button>
        @endif --}}

        @if (in_array($cover->verified, [null, 'R']))
            <button type="button" class="btn btn-warning btn-sm text-start" data-bs-toggle="modal"
                data-bs-target="#verificationModal">
                <i class="ri-send-plane-line me-2"></i>Submit for Verification
            </button>
        @endif
        {{-- {{ logger()->debug($cover->verified) }} --}}

        @if ($cover->verified === 'P')
            {{-- && auth()->user()->can('verify_covers') --}}
            {{-- <button type="button" class="btn btn-success btn-sm text-start"
                onclick="verifyCover('{{ $cover->endorsement_no }}', 'A')">
                <i class="ri-check-double-line me-2"></i>Approve Cover
            </button>

            <button type="button" class="btn btn-danger btn-sm text-start"
                onclick="verifyCover('{{ $cover->endorsement_no }}', 'R')">
                <i class="ri-close-circle-line me-2"></i>Reject Cover
            </button> --}}
            <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" id="verify_details">
                <i class="bx bx-check me-1 align-middle"></i> <span id="verify-re-text">Re-escalate
                    Verification</span>
            </button>
            <button class="btn btn-outline-danger mr-2 btn-sm btn-wave waves-effect waves-light" disabled
                style="color: #ff0000;">
                <i class="bx bx-check-circle me-1 align-middle"></i>Pending Verification
            </button>
        @endif

        @if ($cover->verified === 'A' && $cover->status !== 'cancelled')
            {{-- <button type="button" class="btn btn-outline-danger btn-sm text-start"
                onclick="cancelCover('{{ $cover->endorsement_no }}')">
                <i class="ri-close-line me-2"></i>Cancel Cover
            </button> --}}
            @if (in_array($cover->type_of_bus, ['FPR', 'FNP']) ||
                    (!in_array($cover->type_of_bus, ['FPR', 'FNP']) && !in_array($cover->transaction_type, ['NEW', 'REN'])))
                <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                    data-bs-toggle="modal" data-bs-target="#debit-modal">
                    <i class="bx bx-credit-card"></i> Generate Debit
                </button>
            @elseif(!in_array($cover->type_of_bus, ['FPR', 'FNP']) && in_array($cover->transaction_type, ['NEW', 'REN', 'EXT']))
                {{-- <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" id="commit-cover">
                    <i class="bx bx-save"></i> Commit
                </button> --}}
                <button type="button" class="btn btn-outline-secondary btn-sm text-start me-2"
                    onclick="generateSlip('{{ $cover->endorsement_no }}')">
                    <i class="ri-file-pdf-line me-2"></i>Preview Slip
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm text-start me-2"
                    onclick="generateSlip('{{ $cover->endorsement_no }}')">
                    <i class="ri-file-pdf-line me-2"></i>Debit
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm text-start me-2"
                    onclick="generateSlip('{{ $cover->endorsement_no }}')">
                    <i class="ri-file-pdf-line me-2"></i>Profit Commission
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm text-start me-2"
                    onclick="generateSlip('{{ $cover->endorsement_no }}')">
                    <i class="ri-file-pdf-line me-2"></i>Portfolio
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm text-start me-2"
                    onclick="generateSlip('{{ $cover->endorsement_no }}')">
                    <i class="ri-file-pdf-line me-2"></i>Commission Adjustment
                </button>
            @endif
        @endif

        <div class="mt-3 pt-3 border-top">7
            <span class="text-muted d-block mb-2 fs-14">
                <i class="ri-information-line me-1 fs-14" style="vertical-align: -2px;"></i>Cover Status
            </span>
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted" style="font-size: 0.8125rem;">Status:</small>
                            <span
                                class="badge
                        @switch($cover->verified)
                            @case(null)
                            @case('R')
                                bg-danger
                                @break
                            @case('P')
                                bg-warning
                                @break
                            @case('A')
                                bg-success
                                @break
                            @default
                                bg-secondary
                        @endswitch
                    ">
                                @switch($cover->verified)
                                    @case(null)
                                    @case('R')
                                        Pending
                                    @break

                                    @case('P')
                                        Awaiting Verification
                                    @break

                                    @case('A')
                                        Approved
                                    @break

                                    @default
                                        Unknown
                                @endswitch
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted" style="font-size: 0.8125rem;">Created:</small>
                            <small class="fw-semibold">
                                {{ \Carbon\Carbon::parse($cover->created_at)->format('d M Y') }}
                            </small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted" style="font-size: 0.8125rem;">Last Updated:</small>
                            <small class="fw-semibold">
                                {{ \Carbon\Carbon::parse($cover->updated_at)->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @if ($cover->verified === 'A' && $cover->approved_by)
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted" style="font-size: 0.8125rem;">Approved By:</small>
                            <small class="fw-semibold">
                                {{ $cover->approver->name ?? 'N/A' }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card-body pt-2 d-none">
        <div class="card-body p-3 mx-0 cover-info-wrapper"
            style="background-color:var(--cover-bg);border-radius:0.375rem;">
            {{-- Edit Cover --}}
            @if (in_array($cover->verified, [null, 'R']))
                <button type="button" class="btn btn-outline-primary btn-sm text-start"
                    onclick="editCover('{{ $cover->endorsement_no }}')">
                    <i class="ri-edit-line me-2"></i>Edit Cover Details
                </button>
            @endif

            {{-- Add Schedule (Facultative Only) --}}
            @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                <button type="button" class="btn btn-outline-primary btn-sm text-start" data-bs-toggle="modal"
                    data-bs-target="#addScheduleModal">
                    <i class="ri-table-line me-2"></i>Add Schedule
                </button>

                {{-- Add Clause --}}
                <button type="button" class="btn btn-outline-primary btn-sm text-start" data-bs-toggle="modal"
                    data-bs-target="#addClauseModal">
                    <i class="ri-file-text-line me-2"></i>Add Clause
                </button>

                {{-- Upload Document --}}
                <button type="button" class="btn btn-outline-primary btn-sm text-start" data-bs-toggle="modal"
                    data-bs-target="#addAttachmentModal">
                    <i class="ri-upload-line me-2"></i>Upload Document
                </button>
            @endif

            {{-- Add Reinsurer --}}
            <button type="button" class="btn btn-outline-success btn-sm text-start" data-bs-toggle="modal"
                data-bs-target="#addReinsurerModal">
                <i class="ri-team-line me-2"></i>Add Reinsurer
            </button>

            {{-- Divider --}}
            <hr class="my-2">

            {{-- Generate Slip --}}
            @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                <button type="button" class="btn btn-outline-secondary btn-sm text-start"
                    onclick="generateSlip('{{ $cover->endorsement_no }}')">
                    <i class="ri-file-pdf-line me-2"></i>Generate Slip
                </button>

                {{-- Generate Debit Note --}}
                <button type="button" class="btn btn-outline-secondary btn-sm text-start" data-bs-toggle="modal"
                    data-bs-target="#generateDebitModal">
                    <i class="ri-file-list-3-line me-2"></i>Generate Debit Note
                </button>
            @endif

            {{-- Send Email --}}
            <button type="button" class="btn btn-outline-secondary btn-sm text-start"
                onclick="sendEmail('{{ $cover->endorsement_no }}')">
                <i class="ri-mail-send-line me-2"></i>Send Email
            </button>

            {{-- Divider --}}
            <hr class="my-2">

            {{-- Submit for Verification --}}
            @if (in_array($cover->verified, [null, 'R']))
                <button type="button" class="btn btn-warning btn-sm text-start" data-bs-toggle="modal"
                    data-bs-target="#verificationModal">
                    <i class="ri-send-plane-line me-2"></i>Submit for Verification
                </button>
            @endif

            {{-- Verify/Approve --}}
            @if ($cover->verified === 'P' && auth()->user()->can('verify_covers'))
                <button type="button" class="btn btn-success btn-sm text-start"
                    onclick="verifyCover('{{ $cover->endorsement_no }}', 'A')">
                    <i class="ri-check-double-line me-2"></i>Approve Cover
                </button>

                <button type="button" class="btn btn-danger btn-sm text-start"
                    onclick="verifyCover('{{ $cover->endorsement_no }}', 'R')">
                    <i class="ri-close-circle-line me-2"></i>Reject Cover
                </button>
            @endif

            {{-- Cancel Cover --}}
            @if ($cover->verified === 'A' && $cover->status !== 'cancelled')
                <button type="button" class="btn btn-outline-danger btn-sm text-start"
                    onclick="cancelCover('{{ $cover->endorsement_no }}')">
                    <i class="ri-close-line me-2"></i>Cancel Cover
                </button>
            @endif
        </div>

        {{-- Endorsement Narration Summary --}}
        @if (count($endorsementNarration) > 0)
            <div class="mt-3 pt-3 border-top">
                <small class="text-muted d-block mb-2">
                    <i class="ri-information-line me-1"></i>Recent Endorsements
                </small>
                <div class="list-group list-group-flush">
                    @foreach ($endorsementNarration->take(3) as $narration)
                        <div class="list-group-item px-0 py-2 border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <small class="fw-semibold d-block">
                                        {{ $narration->endorsementType->endorse_type_name ?? 'Endorsement' }}
                                    </small>
                                    <small class="text-muted">
                                        {{ Str::limit($narration->narration ?? 'No description', 50) }}
                                    </small>
                                </div>
                                <small class="text-muted ms-2">
                                    {{ \Carbon\Carbon::parse($narration->created_at)->format('d M') }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if (count($endorsementNarration) > 3)
                    <button type="button" class="btn btn-link btn-sm p-0 mt-2"
                        onclick="document.querySelector('[data-bs-target=\'#endorse-narration-tab\']').click()">
                        View all {{ count($endorsementNarration) }} endorsements
                    </button>
                @endif
            </div>
        @endif

        {{-- Cover Status Info --}}
        <div class="mt-3 pt-3 border-top">
            <small class="text-muted d-block mb-2">
                <i class="ri-information-line me-1"></i>Cover Status
            </small>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Status:</small>
                    <span
                        class="badge
                        @switch($cover->verified)
                            @case(null)
                            @case('R')
                                bg-danger
                                @break
                            @case('P')
                                bg-warning
                                @break
                            @case('A')
                                bg-success
                                @break
                            @default
                                bg-secondary
                        @endswitch
                    ">
                        @switch($cover->verified)
                            @case(null)
                            @case('R')
                                Pending
                            @break

                            @case('P')
                                Awaiting Verification
                            @break

                            @case('A')
                                Approved
                            @break

                            @default
                                Unknown
                        @endswitch
                    </span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Created:</small>
                    <small class="fw-semibold">
                        {{ \Carbon\Carbon::parse($cover->created_at)->format('d M Y') }}
                    </small>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Last Updated:</small>
                    <small class="fw-semibold">
                        {{ \Carbon\Carbon::parse($cover->updated_at)->diffForHumans() }}
                    </small>
                </div>

                @if ($cover->verified === 'A' && $cover->approved_by)
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Approved By:</small>
                        <small class="fw-semibold">
                            {{ $cover->approver->name ?? 'N/A' }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        function editCover(endorsementNo) {
            window.location.href = `/covers/${endorsementNo}/edit`;
        }

        function generateSlip(endorsementNo) {
            if (confirm('Generate placement slip for this cover?')) {
                window.open(`/covers/${endorsementNo}/generate-slip`, '_blank');
            }
        }

        function verifyCover(endorsementNo, action) {
            const actionText = action === 'A' ? 'approve' : 'reject';
            const actionColor = action === 'A' ? 'success' : 'danger';

            Swal.fire({
                title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} Cover?`,
                text: `Are you sure you want to ${actionText} this cover?`,
                icon: 'question',
                input: 'textarea',
                inputLabel: 'Comment (required)',
                inputPlaceholder: 'Enter your verification comment...',
                inputAttributes: {
                    'aria-label': 'Enter your verification comment'
                },
                showCancelButton: true,
                confirmButtonText: `Yes, ${actionText}`,
                confirmButtonColor: action === 'A' ? '#198754' : '#dc3545',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to provide a comment!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit verification
                    fetch(`/covers/${endorsementNo}/verify`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                action: action,
                                comment: result.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Success',
                                        `Cover ${action === 'A' ? 'approved' : 'rejected'} successfully!`,
                                        'success')
                                    .then(() => {
                                        window.location.reload();
                                    });
                            } else {
                                throw new Error(data.message || 'Verification failed');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message, 'error');
                        });
                }
            });
        }

        function cancelCover(endorsementNo) {
            Swal.fire({
                title: 'Cancel Cover?',
                text: 'This action cannot be undone. Are you sure you want to cancel this cover?',
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'Cancellation Reason (required)',
                inputPlaceholder: 'Enter reason for cancellation...',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'No, keep it',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to provide a cancellation reason!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit cancellation
                    fetch(`/covers/${endorsementNo}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                reason: result.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Cancelled', 'Cover has been cancelled successfully.', 'success')
                                    .then(() => {
                                        window.location.reload();
                                    });
                            } else {
                                throw new Error(data.message || 'Cancellation failed');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message, 'error');
                        });
                }
            });
        }
    </script>
@endpush

<style>
    .action-card .btn {
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .action-card .btn:hover {
        transform: translateX(4px);
    }

    .action-card .list-group-item {
        background: transparent;
        transition: background-color 0.2s ease;
    }

    .action-card .list-group-item:hover {
        background-color: #f8f9fa;
        border-radius: 6px;
    }

    .action-card hr {
        border-top: 1px solid #e9ecef;
        opacity: 0.5;
    }

    .action-card .btn-link {
        color: var(--cover-primary);
        text-decoration: none;
        font-size: 0.875rem;
    }

    .action-card .btn-link:hover {
        text-decoration: underline;
    }
</style>
