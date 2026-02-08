@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Policy ({{ $cover_no }}) Details</h1>
        <nav class="ms-md-1 ms-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">{{ $customer->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $cover_no }}</li>
            </ol>
        </nav>
    </div>

    @include('cover.partials.action-buttons', [
        'type_of_bus' => $type_of_bus,
        'cover_no' => $cover_no,
        'cover' => $latest_endorsement,
        'reinsurer' => $reinsurer ?? null,
    ])

    <div class="form-group">
        <div class="gy-4">
            <div class="customer-endorsement row">
                {{-- Cover Details --}}
                <div class="col-sm-7">
                    <table class="table table-responsive">
                        <tbody>
                            <tr>
                                <th><strong>Cover Number</strong></th>
                                <td><strong class="text-primary">{{ $cover_no }}</strong></td>
                            </tr>
                            <tr>
                                <th><strong>Business Type</strong></th>
                                <td>{{ $type_of_bus->bus_type_name }}</td>
                            </tr>
                            @if (in_array($type_of_bus->bus_type_id, ['FPR', 'FNP']))
                                <tr>
                                    <th><strong>Class</strong></th>
                                    <td>{{ $class->class_name }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Cover Period & Status --}}
                <div class="col-sm-5">
                    <table class="table table-responsive">
                        <tbody>
                            <tr>
                                <th><strong>Current Cover From</strong></th>
                                <td>{{ formatDate($latest_endorsement->cover_from) }}</td>
                            </tr>
                            <tr>
                                <th><strong>Current Cover To</strong></th>
                                <td>{{ formatDate($latest_endorsement->cover_to) }}</td>
                            </tr>
                            <tr>
                                <th><strong>Current Status</strong></th>
                                <td>
                                    @if ($latest_endorsement->status == 'A')
                                        <span class="badge bg-success-gradient">Active</span>
                                    @else
                                        <span class="badge bg-danger-gradient">Not Active</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row-cols-12">
        <div class="card mb-2 custom-card border col">
            <div class="card-body pt-0">
                <nav>
                    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                        id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-endorsement-list" data-bs-toggle="tab"
                            data-bs-target="#endorsement-list" type="button" role="tab"
                            aria-controls="endorsement-list" aria-selected="true">
                            <i class="bx bx-table me-1 align-middle"></i>Endorsements List
                            <span class="badge bg-primary ms-1" id="endorsmentListCount">0</span>
                        </button>

                        <button class="nav-link" id="nav-claimlist-tab" data-bs-toggle="tab" data-bs-target="#claimlist-tab"
                            type="button" role="tab" aria-controls="claimlist-tab" aria-selected="false">
                            <i class="bx bx-table me-1 align-middle"></i>Claim List
                            <span class="badge bg-danger ms-1" id="claimListCount">{{ $claims->count() }}</span>
                        </button>

                        <button class="nav-link" id="nav-statement-tab" data-bs-toggle="tab" data-bs-target="#statement-tab"
                            type="button" role="tab" aria-controls="statement-tab" aria-selected="false">
                            <i class="ri-building-2-line me-1"></i> Statement
                            <span class="badge bg-info ms-1"
                                id="statementListCount">{{ $reinsurerStatements->count() }}</span>
                        </button>
                    </div>
                </nav>

                <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                    <div class="tab-pane fade show active" id="endorsement-list" role="tabpanel"
                        aria-labelledby="nav-endorsement-list">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
                                    <table id="endorsement-list-table" class="table table-bordered table-hover w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="8%">ID No.</th>
                                                <th width="15%">Endorsement No.</th>
                                                <th width="15%">Transaction Type</th>
                                                <th width="15%">Cover From</th>
                                                <th width="15%">Expiry Date</th>
                                                <th width="12%">Status</th>
                                                <th width="20%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="claimlist-tab" role="tabpanel" aria-labelledby="nav-claimlist-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
                                    <table id="claimsTable" class="table table-bordered table-hover w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="12%">Claim Number</th>
                                                <th width="12%">Claim Date</th>
                                                <th width="12%">Date of Loss</th>
                                                <th width="12%">Claim Amount</th>
                                                <th width="12%">Claimed Amount</th>
                                                <th width="10%">Status</th>
                                                <th width="12%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="table-light">
                                            <tr class="fw-bold">
                                                <td colspan="4" class="text-end">Total Claims:</td>
                                                <td class="amount-cell text-end">
                                                    {{ formatCurrency($claims->sum('claim_amount')) }}
                                                </td>
                                                <td class="amount-cell text-end">
                                                    {{ formatCurrency($claims->sum('claimed_amount')) }}
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="statement-tab" role="tabpanel" aria-labelledby="nav-statement-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
                                    <table id="reinsurersTable" class="table table-bordered table-hover w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="3%">#</th>
                                                <th width="15%">Reinsurer</th>
                                                <th width="7%">Share %</th>
                                                <th width="9%">Gross Premium</th>
                                                <th width="9%">Commission</th>
                                                <th width="9%">Brokerage</th>
                                                <th width="9%">Prem. Tax</th>
                                                <th width="9%">WHT Amount</th>
                                                <th width="9%">RI Tax</th>
                                                <th width="9%">Net Amount</th>
                                                <th width="7%">Status</th>
                                                <th width="10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="table-light">
                                            <tr class="fw-bold">
                                                <td colspan="2" class="text-end">Totals:</td>
                                                <td class="amount-cell text-end">
                                                    {{ number_format($reinsurerStatements->sum('share_percent') ?? 0, 2) }}%
                                                </td>
                                                <td class="amount-cell text-end">
                                                    {{ formatCurrency($reinsurerStatements->sum('gross_premium') ?? 0) }}
                                                </td>
                                                <td class="amount-cell text-end">
                                                    {{ formatCurrency($reinsurerStatements->sum('commission') ?? 0) }}
                                                </td>
                                                <td class="amount-cell text-end">
                                                    {{ formatCurrency($reinsurerStatements->sum('brokerage') ?? 0) }}
                                                </td>
                                                <td class="amount-cell text-end">
                                                    {{ formatCurrency($reinsurerStatements->sum('premium_tax') ?? 0) }}
                                                </td>
                                                <td class="amount-cell text-end">
                                                    {{ formatCurrency($reinsurerStatements->sum('wht_amount') ?? 0) }}
                                                </td>
                                                <td class="amount-cell text-end">
                                                    {{ formatCurrency($reinsurerStatements->sum('ri_tax') ?? 0) }}
                                                </td>
                                                <td class="amount-cell amount-cell--positive text-end">
                                                    @php
                                                        $totalNet =
                                                            $reinsurerStatements->sum('gross_premium') -
                                                            $reinsurerStatements->sum('commission') -
                                                            $reinsurerStatements->sum('brokerage') +
                                                            $reinsurerStatements->sum('premium_tax') +
                                                            $reinsurerStatements->sum('wht_amount') +
                                                            $reinsurerStatements->sum('ri_tax');
                                                    @endphp
                                                    {{ formatCurrency($totalNet) }}
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/covers/endorsement-config.js') }}"></script>
    <script src="{{ asset('js/covers/endorsement-handlers.js') }}"></script>
    <script src="{{ asset('js/covers/claims-statements.js') }}"></script>
    <script>
        window.CoverEndorsement.init({
            customerId: @json($customer->customer_id),
            coverNo: @json($cover_no),
            latestEndorsement: @json($latest_endorsement),
            premiumDueDate: @json($premium_due_date),
            routes: {
                endorseDatatable: @json(route('endorse.datatable')),
                coverHome: @json(route('cover.CoverHome')),
                deleteCover: @json(route('cover.delete_cover')),
                getTreatyYearCover: @json(route('cover.get_treaty_year_cover')),
                getReinsurersOrigEndorsement: @json(route('cover.get_reinsurers_orig_endorsement')),
                getQuarterlyFigures: @json(route('cover.get_quarterly_figures'))
            },
            csrfToken: @json(csrf_token())
        });
    </script>
@endpush
