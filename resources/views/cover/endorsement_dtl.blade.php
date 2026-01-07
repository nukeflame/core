@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Policies</h1>
        <nav class="ms-md-1 ms-0">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">{{ $customer->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $cover_no }}</li>
            </ol>
        </nav>
    </div>

    {{-- Action Buttons --}}
    {{-- @include('covers.partials._action-buttons', [
        'type_of_bus' => $type_of_bus,
        'cover_no' => $cover_no,
    ]) --}}

    {{-- Hidden Forms --}}
    {{-- @include('covers.partials._hidden-forms', [
        'customer' => $customer,
        'cover_no' => $cover_no,
        'latest_endorsement' => $latest_endorsement,
    ]) --}}

    {{-- Cover Information Cards --}}
    <div class="form-group">
        <div class="gy-4">
            <div class="customer-endorsement row">
                {{-- Cover Details --}}
                <div class="col-sm-7">
                    <table class="table table-responsive">
                        <tbody>
                            <tr>
                                <th><strong>Cover Number</strong></th>
                                <td>{{ $cover_no }}</td>
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

    {{-- Endorsement List Tab Panel --}}
    <div class="row-cols-12">
        <div class="card mb-2 custom-card border col">
            <div class="card-body pt-0">
                <nav>
                    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                        id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-endorsement-list" data-bs-toggle="tab"
                            data-bs-target="#endorsement-list" type="button" role="tab" aria-selected="true">
                            <i class="bx bx-file me-1 align-middle"></i>Endorsement List
                        </button>

                        <button class="nav-link" id="nav-coverlist-tab" data-bs-toggle="tab" data-bs-target="#coverlist-tab"
                            type="button" role="tab" aria-selected="false" style="visibility:hidden">
                            <i class="bx bx-file me-1
                            align-middle"></i>Cover List
                        </button>
                        <button class="nav-link" id="nav-claimlist-tab" data-bs-toggle="tab" data-bs-target="#claimlist-tab"
                            type="button" role="tab" aria-selected="false" style="visibility:hidden">
                            <i class="bx bx-medal me-1
                            align-middle"></i>Claim List
                        </button>
                        <button class="nav-link" id="nav-statement-tab" data-bs-toggle="tab" data-bs-target="#statement-tab"
                            type="button" role="tab" aria-selected="false" style="visibility:hidden">
                            <i class="bx bx-file-blank me-1
                            align-middle"></i>Statement
                        </button>
                    </div>
                </nav>

                <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                    <div class="tab-pane active show" id="endorsement-list" role="tabpanel"
                        aria-labelledby="nav-endorsement-list">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <table id="endorsement-list-table"
                                    class="table table-striped text-nowrap table-hover table-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID No.</th>
                                            <th scope="col">Endorsement No.</th>
                                            <th scope="col">Transaction Type</th>
                                            <th scope="col">Cover From</th>
                                            <th scope="col">Expiry Date</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    {{-- @include('covers.partials._endorsement-modal', [
        'latest_endorsement' => $latest_endorsement,
        'EndorsementTypes' => $EndorsementTypes,
        'premium_due_date' => $premium_due_date,
    ])

    @if ($type_of_bus->bus_type_id == 'TPR')
        @include('covers.partials._quarterly-figures-modal')
        @include('covers.partials._profit-commission-modal')
        @include('covers.partials._portfolio-modal')
    @endif

    @if ($type_of_bus->bus_type_id == 'TNP')
        @include('covers.partials._mdp-installment-modal', [
            'mdpInsLayerwise' => $mdpInsLayerwise ?? [],
        ])
    @endif --}}
@endsection

@push('styles')
    <style>
        #endorsement-list-table tbody tr {
            cursor: pointer;
        }

        #endorsement-list-table tbody tr:hover {
            background-color: rgb(77, 77, 157);
        }

        .errorClass {
            color: #dc3545;
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('script')
    <script src="{{ asset('js/covers/endorsement-config.js') }}"></script>
    <script src="{{ asset('js/covers/endorsement-handlers.js') }}"></script>
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
