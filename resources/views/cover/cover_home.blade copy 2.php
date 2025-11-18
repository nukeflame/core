@extends('layouts.app')

@section('title', 'Cover Details - ' . $coverReg->cover_no)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cover-details.css') }}">
@endpush

@section('content')
    <div class="cover-details-container" id="coverDetailsApp" data-cover-id="{{ $coverReg->id }}"
        data-endorsement-no="{{ $coverReg->endorsement_no }}" data-type-of-bus="{{ $coverReg->type_of_bus }}">

        {{-- Header Component --}}
        <x-cover.header :cover="$coverReg" :customer="$customer" :actionable="$actionable" />

        <div class="container-fluid mt-4">
            <div class="row">
                {{-- Sidebar Summary --}}
                <div class="col-lg-3">
                    {{-- <x-cover.summary-card :cover="$coverReg" :typeOfBus="$type_of_bus" :summaryData="$summaryData" /> --}}

                    {{-- Quick Actions Card --}}
                    @if ($actionable)
                        {{-- <x-cover.action-card :cover="$coverReg" :endorsementNarration="$endorsementNarration" /> --}}
                    @endif

                    {{-- Financial Summary Card --}}
                    @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                        {{-- <x-cover.financial-summary :cover="$coverReg" /> --}}
                    @endif
                </div>

                {{-- Main Content --}}
                <div class="col-lg-9">
                    {{-- Tabs Navigation --}}
                    {{-- <x-cover.tabs-navigation :cover="$coverReg" :endorsementNarration="$endorsementNarration" /> --}}

                    {{-- Tab Content --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="tab-content" id="coverTabContent">
                                {{-- Schedules Tab --}}
                                {{-- @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                    <div class="tab-pane fade show active" id="schedules-tab" role="tabpanel">
                                        @include('covers.tabs.schedules')
                                    </div>

                                    <div class="tab-pane fade" id="attachments-tab" role="tabpanel">
                                        @include('covers.tabs.attachments')
                                    </div>

                                    <div class="tab-pane fade" id="clauses-tab" role="tabpanel">
                                        @include('covers.tabs.clauses')
                                    </div>
                                @endif --}}

                                {{-- Reinsurers Tab --}}
                                <div class="tab-pane fade @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP'])) show active @endif"
                                    id="reinsurers-tab" role="tabpanel">
                                    {{-- @include('covers.tabs.reinsurers') --}}
                                </div>

                                {{-- Insurance Classes Tab (Treaty Only) --}}
                                @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP']))
                                    <div class="tab-pane fade" id="ins-classes-tab" role="tabpanel">
                                        {{-- @include('covers.tabs.insurance-classes') --}}
                                    </div>
                                @endif

                                {{-- Installments Tab --}}
                                @if ($coverReg->no_of_installments > 1)
                                    <div class="tab-pane fade" id="installments-tab" role="tabpanel">
                                        {{-- @include('covers.tabs.installments') --}}
                                    </div>
                                @endif

                                {{-- Endorsement Narration Tab --}}
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
        </div>
    </div>

    {{-- Modals --}}
    {{-- <x-cover.modals.schedules :cover="$coverReg" :schedHeaders="$schedHeaders" />
<x-cover.modals.attachments :cover="$coverReg" />
<x-cover.modals.clauses :cover="$coverReg" :clauses="$clauses" />
<x-cover.modals.reinsurers :cover="$coverReg" :reinsurers="$reinsurers" :whtRates="$whtRates" :paymethods="$paymethods" />
<x-cover.modals.verification :cover="$coverReg" :verifiers="$verifiers ?? []" />
<x-cover.modals.debit :cover="$coverReg" /> --}}

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
