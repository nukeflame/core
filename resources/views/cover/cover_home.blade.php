@extends('layouts.app')

@section('title', 'Cover Details - ' . $coverReg->cover_no)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cover-details.css') }}">
@endpush

@section('content')
    <div class="cover-details-container" id="coverDetailsApp" data-cover-id="{{ $coverReg->id }}"
        data-endorsement-no="{{ $coverReg->endorsement_no }}" data-cover-no="{{ $coverReg->cover_no }}"
        data-type-of-bus="{{ $coverReg->type_of_bus }}" data-share-offered="{{ $coverReg->share_offered }}"
        data-cedant-comm-rate="{{ $coverReg->cedant_comm_rate }}" data-rein-premium="{{ $coverReg->rein_premium }}"
        data-effective-sum-insured="{{ $coverReg->effective_sum_insured }}">

        {{-- Page Header --}}
        <x-cover.header :cover="$coverReg" :customer="$customer" :actionable="$actionable" />

        {{-- Action Cards --}}
        <div class="row row-cols-12 mx-0">
            @if ($actionable)
                <x-cover.action-card :cover="$coverReg" :endorsementNarration="$endorsementNarration" />
            @endif

            <x-cover.summary-card :cover="$coverReg" :customer="$customer" :typeOfBus="$type_of_bus" :summaryData="$summaryData" />
        </div>

        {{-- Main Content Area --}}
        <div class="row-cols-12">
            <div class="card mb-2 custom-card border col">
                <div class="card-body pt-0">
                    {{-- Tabs Navigation --}}
                    <x-cover.tabs-navigation :cover="$coverReg" :endorsementNarration="$endorsementNarration" />

                    {{-- Tab Content --}}
                    <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                        {{-- Schedules Tab (Facultative only) --}}
                        @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                            <div class="tab-pane fade show active" id="schedules-tab" role="tabpanel"
                                aria-labelledby="nav-schedules-tab">
                                @include('cover.tabs.schedules', ['cover' => $coverReg])
                            </div>

                            {{-- Attachments Tab --}}
                            <div class="tab-pane fade" id="attachments-tab" role="tabpanel"
                                aria-labelledby="nav-attachments-tab">
                                {{-- @include('cover.tabs.attachments', ['cover' => $coverReg]) --}}
                            </div>

                            {{-- Clauses Tab --}}
                            <div class="tab-pane fade" id="clauses-tab" role="tabpanel" aria-labelledby="nav-clauses-tab">
                                {{-- @include('cover.tabs.clauses', ['cover' => $coverReg]) --}}
                            </div>
                        @endif

                        @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP']))
                            <div class="tab-pane fade" id="debit-items-tab" role="tabpanel"
                                aria-labelledby="nav-debit-items-tab">
                                @include('cover.tabs.debit-items', ['cover' => $coverReg])
                            </div>
                        @endif

                        {{-- Reinsurers Tab --}}
                        <div class="tab-pane fade @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP'])) show active @endif" id="reinsurers-tab"
                            role="tabpanel" aria-labelledby="nav-reinsurers-tab">
                            @include('cover.tabs.reinsurers', ['cover' => $coverReg])
                        </div>

                        {{-- Insurance Classes Tab (Treaty only) --}}
                        @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP']))
                            <div class="tab-pane fade" id="ins-classes-tab" role="tabpanel"
                                aria-labelledby="nav-ins-classes-tab">
                                @include('cover.tabs.insurance-classes', ['cover' => $coverReg])
                            </div>
                        @endif

                        {{-- Installments Tab --}}
                        @if ($coverReg->no_of_installments > 1)
                            <div class="tab-pane fade" id="installments-tab" role="tabpanel"
                                aria-labelledby="nav-installments-tab">
                                {{-- @include('cover.tabs.installments', ['cover' => $coverReg]) --}}
                            </div>
                        @endif

                        {{-- Endorsement Narration Tab --}}
                        @if (count($endorsementNarration) > 0)
                            <div class="tab-pane fade" id="endorse-narration-tab" role="tabpanel"
                                aria-labelledby="nav-endorse-narration-tab">
                                {{-- @include('cover.tabs.endorsement-narration', ['cover' => $coverReg]) --}}
                            </div>
                        @endif

                        {{-- Approvals Tab --}}
                        <div class="tab-pane fade" id="approvals-tab" role="tabpanel" aria-labelledby="nav-approvals-tab">
                            @include('cover.tabs.approvals', ['cover' => $coverReg])
                        </div>

                        {{-- Debits Tab (Facultative only) --}}
                        @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                            <div class="tab-pane fade" id="debits-tab" role="tabpanel" aria-labelledby="nav-debits-tab">
                                {{-- @include('cover.tabs.debits', ['cover' => $coverReg]) --}}
                            </div>
                        @endif

                        {{-- Documents Tab --}}
                        <div class="tab-pane fade" id="docs-tab" role="tabpanel" aria-labelledby="nav-docs-tab">
                            @include('cover.tabs.documents', ['cover' => $coverReg])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Forms for Navigation --}}
    <form action="{{ route('endorsements_list') }}" method="post" id="coverForm">
        @csrf
        <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
        <input type="hidden" name="customer_id" value="{{ $coverReg->customer_id }}" />
    </form>

    <form action="{{ route('customer.dtl') }}" method="post" id="customerForm">
        @csrf
        <input type="hidden" name="customer_id" value="{{ $coverReg->customer_id }}" />
    </form>

    <form action="{{ route('cover.editCoverForm') }}" method="post" id="editCoverForm">
        @csrf
        <input type="hidden" name="trans_type" value="EDIT" />
        <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
        <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
        <input type="hidden" name="customer_id" value="{{ $coverReg->customer_id }}" />
    </form>

    {{-- Modals --}}
    @include('cover.modals.schedules', [
        'cover' => $coverReg,
        'schedHeaders' => $schedHeaders,
    ])

    {{-- @include('cover.modals.attachments', [
        'cover' => $coverReg,
    ]) --}}

    {{-- @include('cover.modals.clauses', [
        'cover' => $coverReg,
        'clauses' => $clauses,
    ]) --}}

    @include('cover.modals.reinsurers', [
        'cover' => $coverReg,
        'reinsurers' => $reinsurers,
        'whtRates' => $whtRates,
        'paymethods' => $paymethods,
        'coverpart' => $coverpart,
        'coverTreaties' => $coverTreaties ?? null,
    ])

    {{-- @include('cover.modals.edit-reinsurer', [
        'cover' => $coverReg,
        'reinsurers' => $reinsurers,
        'whtRates' => $whtRates,
        'paymethods' => $paymethods,
    ]) --}}
    {{--
    @include('cover.modals.insurance-classes', [
        'cover' => $coverReg,
        'coverReinclass' => $coverReinclass,
        'insClasses' => $ins_classes,
    ]) --}}

    @include('cover.modals.verification', [
        'cover' => $coverReg,
        'verifiers' => $verifiers ?? [],
        'process' => $process ?? null,
        'verifyprocessAction' => $verifyprocessAction ?? null,
    ])

    @include('cover.modals.treaty-debit', [
        'cover' => $coverReg,
        'nextInstallment' => $nextInstallment,
        'installmentAmount' => $installmentAmount,
    ])

    {{-- @include('cover.modals.treatyDebitModal', [
        'cover' => $coverReg,
        'nextInstallment' => $nextInstallment,
        'installmentAmount' => $installmentAmount,
    ]) --}}

    {{-- @include('cover.modals.generate-slip', ['cover' => $coverReg])

    @include('cover.modals.attachment-preview')

    @include('cover.modals.email-composer', [
        'cover' => $coverReg,
        'reinsurers' => $coverpart ?? [],
    ]) --}}
@endsection

@push('script')
    <script src="{{ asset('js/covers/cover-details.js') }}"></script>
@endpush
