@extends('layouts.app')

@section('title', 'Cover Details - ' . $coverReg->cover_no)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cover-details.css') }}">
@endpush

@section('content')
    <div class="cover-details-container" id="coverDetailsApp" data-cover-id="{{ $coverReg->id }}"
        data-endorsement-no="{{ $coverReg->endorsement_no }}" data-type-of-bus="{{ $coverReg->type_of_bus }}">

        <x-cover.header :cover="$coverReg" :customer="$customer" :actionable="$actionable" />

        <div class="row row-cols-12 mx-0">
            @if ($actionable)
                <x-cover.action-card :cover="$coverReg" :endorsementNarration="$endorsementNarration" />
            @endif

            <x-cover.summary-card :cover="$coverReg" :customer="$customer" :typeOfBus="$type_of_bus" :summaryData="[]" />
        </div>

        <div class="row row-cols-12 mx-0">
            <div class="row d-none">
                <div class="col-lg-12">
                    <x-cover.tabs-navigation :cover="$coverReg" :endorsementNarration="$endorsementNarration" />

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="tab-content" id="coverTabContent">
                                @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                    <div class="tab-pane fade show active" id="schedules-tab" role="tabpanel">
                                        @include('cover.tabs.schedules', ['cover' => $coverReg])
                                    </div>

                                    <div class="tab-pane fade" id="attachments-tab" role="tabpanel">
                                        {{-- @include('covers.tabs.attachments') --}}
                                    </div>

                                    <div class="tab-pane fade" id="clauses-tab" role="tabpanel">
                                        {{-- @include('covers.tabs.clauses') --}}
                                    </div>
                                @endif

                                <div class="tab-pane fade @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP'])) show active @endif"
                                    id="reinsurers-tab" role="tabpanel">
                                    {{-- @include('covers.tabs.reinsurers') --}}
                                </div>

                                @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP']))
                                    <div class="tab-pane fade" id="ins-classes-tab" role="tabpanel">
                                        {{-- @include('covers.tabs.insurance-classes') --}}
                                    </div>
                                @endif

                                @if ($coverReg->no_of_installments > 1)
                                    <div class="tab-pane fade" id="installments-tab" role="tabpanel">
                                        {{-- @include('covers.tabs.installments') --}}
                                    </div>
                                @endif

                                @if (count($endorsementNarration) > 0)
                                    <div class="tab-pane fade" id="endorse-narration-tab" role="tabpanel">
                                        {{-- @include('covers.tabs.endorsement-narration') --}}
                                    </div>
                                @endif

                                {{-- Approvals Tab --}}
                                <div class="tab-pane fade" id="approvals-tab" role="tabpanel">
                                    {{-- @include('covers.tabs.approvals') --}}
                                </div>

                                {{-- Debits Tab --}}
                                @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                    <div class="tab-pane fade" id="debits-tab" role="tabpanel">
                                        {{-- @include('covers.tabs.debits') --}}
                                    </div>
                                @endif

                                {{-- Documents Tab --}}
                                <div class="tab-pane fade" id="documents-tab" role="tabpanel">
                                    {{-- @include('covers.tabs.documents') --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-0 px-0">
                <div class="card border-0 shadow-sm mb-3 custom-card">
                    <div class="card-body pt-0">
                        <nav>
                            <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                                id="nav-tab" role="tablist">
                                @switch ($coverReg->type_of_bus)
                                    @case('FPR')
                                    @case('FNP')
                                        <button class="nav-link active" id="nav-schedules-tab" data-bs-toggle="tab"
                                            data-bs-target="#schedules-tab" type="button" role="tab" aria-selected="true"><i
                                                class="bx bx-table me-1 align-middle"></i>Schedule
                                            Details</button>
                                        <button class="nav-link" id="nav-attachments-tab" data-bs-toggle="tab"
                                            data-bs-target="#attachments-tab" type="button" role="tab" aria-selected="false"
                                            tabindex="-1"><i class="bx bx-file me-1 align-middle"></i>File
                                            & Support Docs</button>
                                        <button class="nav-link" id="nav-clauses-tab" data-bs-toggle="tab"
                                            data-bs-target="#clauses-tab" type="button" role="tab" aria-selected="false"
                                            tabindex="-1"><i class="bx bx-medal me-1 align-middle"></i>Policy Clauses</button>
                                        <button class="nav-link" id="nav-reinsurers-tab" data-bs-toggle="tab"
                                            data-bs-target="#reinsurers-tab" type="button" role="tab" aria-selected="false"
                                            tabindex="-1"><i class="bx bx-palette me-1 align-middle"></i>Reinsurers</button>
                                        @if ($coverReg->no_of_installments > 1)
                                            <button class="nav-link" id="nav-installments-tab" data-bs-toggle="tab"
                                                data-bs-target="#installments-tab" type="button" role="tab"
                                                aria-selected="false" tabindex="-1"><i
                                                    class="bi bi-archive me-1 align-middle"></i>Installment Details</button>
                                        @endif
                                    @break

                                    @case('TPR')
                                    @case('TNP')
                                        <button class="nav-link" id="nav-reinsurers-tab" data-bs-toggle="tab"
                                            data-bs-target="#reinsurers-tab" type="button" role="tab" aria-selected="false"
                                            tabindex="-1"><i class="bx bx-palette me-1 align-middle"></i>Reinsurers</button>
                                        <button class="nav-link" id="nav-ins-classes-tab" data-bs-toggle="tab"
                                            data-bs-target="#ins-classes-tab" type="button" role="tab"
                                            aria-selected="false" tabindex="-1"><i
                                                class="bx bx-award me-1 align-middle"></i>Insurance Classes</button>
                                    @break

                                @endswitch
                                @if (count($endorsementNarration) > 0)
                                    <button class="nav-link" id="nav-endorse-narration-tab" data-bs-toggle="tab"
                                        data-bs-target="#endorse-narration-tab" type="button" role="tab"
                                        aria-selected="false" tabindex="-1"><i
                                            class="bx bx-file-blank me-1 align-middle"></i>Narration</button>
                                @endif
                                <button class="nav-link" id="nav-approvals-tab" data-bs-toggle="tab"
                                    data-bs-target="#approvals-tab" type="button" role="tab" aria-selected="false"
                                    tabindex="-1"><i class="bx bx-check me-1 align-middle"></i>Approvals</button>
                                @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                    <button class="nav-link" id="nav-debits-tab" data-bs-toggle="tab"
                                        data-bs-target="#debits-tab" type="button" role="tab" aria-selected="false"
                                        tabindex="-1"><i class="bx bx-credit-card me-1 align-middle"></i>Cedant</button>
                                @endif
                                <button class="nav-link" id="nav-docs-tab" data-bs-toggle="tab"
                                    data-bs-target="#docs-tab" type="button" role="tab" aria-selected="false"
                                    tabindex="-1"><i class="bx bx-file-blank me-1 align-middle"></i>Print-outs</button>
                            </div>
                        </nav>
                        <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                            <div class="tab-pane active show" id="schedules-tab" role="tabpanel"
                                aria-labelledby="nav-schedules-tab" tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        <table id="schedules-table"
                                            class="table table-striped text-nowrap table-hover table-responsive"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No.</th>
                                                    <th scope="col">Title</th>
                                                    <th scope="col">Details</th>
                                                    <th scope="col">Position</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="attachments-tab" role="tabpanel"
                                aria-labelledby="nav-attachments-tab" tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        <table id="attachments-table"
                                            class="table table-striped text-nowrap table-hover table-responsive"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Title</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="clauses-tab" role="tabpanel" aria-labelledby="nav-clauses-tab"
                                tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        <table id="clauses-table"
                                            class="table table-striped text-nowrap table-hover table-responsive"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Clause ID</th>
                                                    <th scope="col">Clause Title</th>
                                                    <th scope="col">Description</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="reinsurers-tab" role="tabpanel"
                                aria-labelledby="nav-reinsurers-tab" tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        <table id="reinsurers-table"
                                            class="table table-striped text-nowrap table-hover table-responsive"
                                            style="width: 100%!important;">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Reinsurer</th>
                                                    <th scope="col">Share(%)</th>
                                                    @switch ($coverReg->type_of_bus)
                                                        @case('FPR')
                                                        @case('FNP')
                                                            <th scope="col">Sum insured</th>
                                                            <th scope="col">Premium</th>
                                                            <th scope="col">Commission rate</th>
                                                            <th scope="col">Commission</th>
                                                            <th scope="col">Brokerage Commission</th>
                                                            <th scope="col">WHT Amount</th>
                                                            <th scope="col">Retro Amount</th>
                                                        @break

                                                        @case('TPR')
                                                            @if (!in_array($coverReg->transaction_type, ['NEW', 'REN']))
                                                                {{-- <th scope="col">Sum insured</th> --}}
                                                                <th scope="col">Premium</th>
                                                                <th scope="col">Commission</th>
                                                                <th scope="col">Claim Amt</th>
                                                                <th scope="col">Premium Tax</th>
                                                                <th scope="col">Reinsurance Tax</th>
                                                            @endif
                                                        @break

                                                        @case('TNP')
                                                            @if (!in_array($coverReg->transaction_type, ['NEW', 'REN']))
                                                                <th scope="col">Total MDP</th>
                                                                <th scope="col">MDP</th>
                                                            @endif
                                                        @break

                                                    @endswitch
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="installments-tab" role="tabpanel"
                                aria-labelledby="nav-installments-tab" tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        <table id="installments-table"
                                            class="table table-striped text-nowrap table-hover table-responsive"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No.</th>
                                                    <th scope="col">Due Date</th>
                                                    <th scope="col">Installment Amount</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="ins-classes-tab" role="tabpanel"
                                aria-labelledby="nav-ins-classes-tab" tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        <table id="insclass-table"
                                            class="table table-striped text-nowrap table-hover table-responsive"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No.</th>
                                                    <th scope="col">Reinsurance Class</th>
                                                    <th scope="col">Class Code</th>
                                                    <th scope="col">Class Name</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="endorse-narration-tab" role="tabpanel"
                                aria-labelledby="nav-endorse-narration-tab" tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        <table id="endorse-narration-table"
                                            class="table table-striped text-nowrap table-hover table-responsive"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No.</th>
                                                    <th scope="col">Endorsment Type</th>
                                                    <th scope="col">Narration</th>
                                                    @if ($endorsementNarration)
                                                        @foreach ($endorsementNarration as $endorsementNarratio)
                                                        @endforeach
                                                        <th scope="col">Extension Days</th>
                                                    @endif
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="approvals-tab" role="tabpanel" aria-labelledby="nav-approvals-tab"
                                tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        <table id="approvals-table"
                                            class="table table-striped text-nowrap table-hover table-responsive"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Approver</th>
                                                    <th scope="col">Comment</th>
                                                    <th scope="col">Approver Comment</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="debits-tab" role="tabpanel" aria-labelledby="nav-debits-table"
                                tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        <table id="debits-table"
                                            class="table table-striped text-nowrap table-hover table-responsive"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID.</th>
                                                    <th scope="col">Cedant</th>
                                                    <th scope="col">Debit No.</th>
                                                    <th scope="col">Installment</th>
                                                    <th scope="col">Share(%)</th>
                                                    <th scope="col">Sum insured</th>
                                                    <th scope="col">Premium</th>
                                                    {{-- <th scope="col">Commission</th> --}}
                                                    <th scope="col">Gross</th>
                                                    <th scope="col">Net Amount</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="docs-tab" role="tabpanel" aria-labelledby="nav-docs-tab"
                                tabindex="0">
                                <div class="card">
                                    <div class="card-body py-3 px-2">
                                        @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP']) && in_array($coverReg->transaction_type, ['NEW', 'REN', 'EXT']))
                                        @else
                                            <a href="{{ route('docs.coverdebitnote', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                                                target="_blank" rel="noopener noreferrer" class="print-out-link pr-3"
                                                id="generateDebitNote">
                                                <i class="bx bx-file me-1 align-middle"></i>Debit Note
                                            </a>
                                            <a href="{{ route('docs.coverdebitnote', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                                                id="generateCreditNote" rel="noopener noreferrer"
                                                data-endorsementno="{{ $coverReg->endorsement_no }}"
                                                class="print-out-link pr-3">
                                                <i class="bx bx-file me-1 align-middle"></i>Credit Notes
                                            </a>
                                        @endif
                                        <a href="{{ route('docs.coverslip', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                                            target="_blank" rel="noopener noreferrer" class="print-out-link pr-3"
                                            id="generateCoverSlip"><i class="bx bx-file me-1 align-middle"></i>Cover Slip
                                        </a>

                                        @if (count($endorsementNarration) > 0)
                                            <a href="{{ route('docs.endorsementslip', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                                                target="_blank" rel="noopener noreferrer" class="print-out-link"
                                                id="generateEndorsementSlip">
                                                <i class="bx bx-file me-1 align-middle"></i> <span>Endorsement
                                                    Notice Slip</span></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Modals --}}
    {{-- <x-cover.modals.schedules :cover="$coverReg" :schedHeaders="$schedHeaders" />
<x-cover.modals.attachments :cover="$coverReg" />
<x-cover.modals.clauses :cover="$coverReg" :clauses="$clauses" /> --}}
    <x-cover.modals.reinsurers :cover="$coverReg" :reinsurers="$reinsurers" :whtRates="$whtRates" :paymethods="$paymethods" />
    {{-- <x-cover.modals.verification :cover="$coverReg" :verifiers="$verifiers ?? []" />
<x-cover.modals.debit :cover="$coverReg" />  --}}

    @push('scripts')
        {{-- @vite(['resources/js/covers/cover-details.js'])
    <script>
        // Pass PHP data to JavaScript
        window.coverData = @json([
            'id' => $coverReg->id,
            'endorsement_no' => $coverReg->endorsement_no,
            'cover_no' => $coverReg->cover_no,
            'type_of_bus' => $coverReg->type_of_bus,
            'estimated_premium_income' => $coverReg->rein_premium,
            'treaty_capacity' => $coverReg->effective_sum_insured,
            'total_placed' => $summaryData['total_placed'] ?? 0,
            'actionable' => $actionable
        ]);
    </script> --}}
    @endpush
@endsection
