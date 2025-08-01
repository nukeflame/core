@extends('layouts.app', [
    'pageTitle' => 'Budget Allocation - ' . $company->company_name,
])

@section('content')
    <style>
        .business-table th,
        .business-table td {
            vertical-align: middle;
            white-space: nowrap;
        }

        .text-right {
            text-align: right;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .bg-header-main {
            background-color: #0d6efd;
            color: white;
        }

        .bg-header-sub {
            background-color: #0d6efd;
            opacity: 0.9;
            color: white;
        }

        .bg-total-row {
            background-color: #cfe2ff;
        }

        .bg-grand-total {
            background-color: #9ec5fe;
        }

        .btn-add-data {
            margin-bottom: 1rem;
        }

        .card.border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .card.border-left-success {
            border-left: 4px solid #1cc88a !important;
        }

        .card.border-left-danger {
            border-left: 4px solid #e74a3b !important;
        }

        tr.row-total {
            background-color: rgba(0, 0, 0, 0.05);
            font-weight: bold;
        }

        .allocation-card-counter.card-counter {
            padding: 20px;
            border-radius: 5px;
        }

        .allocation-card-counter.card-counter .icon-container {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-low {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-adequate {
            background-color: #d4edda;
            color: #155724;
        }

        .allocation-card.card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            padding: 0px;
            margin: 0px;
        }

        .chart-container {
            height: 300px;
        }

        .icon-container .ico-status {
            font-size: 25px;
            vertical-align: middle
        }

        .allocation-card-counter h3 {
            font-size: 21px;
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Budget Allocation</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.budget_allocation') }}">Budget Allocation</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Data view
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-sm-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Budget view</div>
                </div>
                <div class="card-body">
                    <div class="card caustom-card shadow-sm">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select class="form-inputs" id="fiscalYearFilter"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('admin.budget_allocation.incomes_create') }}"
                                        class="btn btn-primary me-2">
                                        <i class="bx bx-plus"></i> Add New Income Data
                                    </a>
                                    <a href="{{ route('admin.budget_allocation.expenses_create') }}"
                                        class="btn btn-dark me-2">
                                        <i class="bx bx-plus"></i> Add New Expense Data
                                    </a>
                                    <a href="{{ route('admin.budget_allocation.view') }}" class="btn btn-success">
                                        <i class="bx bx-upload"></i> Import Data
                                    </a>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-xl-3 border-end border-inline-end-dashed">
                                    <div class="d-flex flex-wrap align-items-top p-4">
                                        <div class="me-3 lh-1">
                                            <span class="avatar avatar-md avatar-rounded bg-primary shadow-sm">
                                                <i class="ti ti-wallet fs-18"></i>
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <h5 class="fw-semibold mb-1" id="total-income">0</h5>
                                            <p class="text-muted mb-0 fs-12">Total Income (KES)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 border-end border-inline-end-dashed">
                                    <div class="d-flex flex-wrap align-items-top p-4">
                                        <div class="me-3 lh-1">
                                            <span class="avatar avatar-md avatar-rounded bg-secondary shadow-sm">
                                                <i class="ti ti-wallet fs-18"></i>
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <h5 class="fw-semibold mb-1" id="total-expenses">0</h5>
                                            <p class="text-muted mb-0 fs-12">Total Expenses (KES)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 border-end border-inline-end-dashed">
                                    <div class="d-flex flex-wrap align-items-top p-4">
                                        <div class="me-3 lh-1">
                                            <span class="avatar avatar-md avatar-rounded bg-success shadow-sm">
                                                <i class="ti ti-wallet fs-18"></i>
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <h5 class="fw-semibold mb-1" id="gross-profit">0</h5>
                                            <p class="text-muted mb-0 fs-12">Gross Profit (KES)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="d-flex flex-wrap align-items-top p-4">
                                                <div class="me-3 lh-1">
                                                    <span class="avatar avatar-md avatar-rounded bg-success shadow-sm">
                                                        <i class="ti ti-trending-up fs-18"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <h5 class="fw-semibold mb-1" id="profit-margin">0%</h5>
                                                    <p class="text-muted mb-0 fs-12">Profit Margin(%)</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="d-flex flex-wrap align-items-top p-4">
                                                <div class="me-3 lh-1">
                                                    <span class="avatar avatar-md avatar-rounded bg-dark shadow-sm">
                                                        <i class="ti ti-credit-card fs-18"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <h5 class="fw-semibold mb-1" id="cost-income-ratio">0%</h5>
                                                    <p class="text-muted mb-0 fs-12">Cost Income Ratio (%)</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion accordion-primary" id="financialAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingIncome">
                                        <button class="accordion-button fs-16" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseIncome" aria-expanded="true"
                                            aria-controls="collapseIncome">
                                            <span>1. Income Statement</span>
                                        </button>
                                    </h2>
                                    <div id="collapseIncome" class="accordion-collapse collapse show"
                                        aria-labelledby="headingIncome" data-bs-parent="#financialAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="w-50 fs-13 p-0 text-uppercase">Income
                                                                        Category
                                                                    </th>
                                                                    <th class="text-end w-50 fs-13 text-uppercase">Amount
                                                                        (KES)
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="income-table-body">
                                                                <tr>
                                                                    <td colspan="2" class="text-center">No records
                                                                        found!.
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingExpense">
                                        <button class="accordion-button collapsed fs-16" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseExpense"
                                            aria-expanded="false" aria-controls="collapseExpense">
                                            <span>2. Expense Statement</span>
                                        </button>
                                    </h2>
                                    <div id="collapseExpense" class="accordion-collapse collapse"
                                        aria-labelledby="headingExpense" data-bs-parent="#financialAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="w-50 fs-13 p-0 text-uppercase">Expense
                                                                        Category
                                                                    </th>
                                                                    <th class="text-end w-50 fs-13 text-uppercase">Amount
                                                                        (KES)
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="expense-table-body">
                                                                <tr>
                                                                    <td colspan="2" class="text-center">No records
                                                                        found!.
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSummary">
                                        <button class="accordion-button collapsed fs-16" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseSummary"
                                            aria-expanded="false" aria-controls="collapseSummary">
                                            <span>3. Financial Summary</span>
                                        </button>
                                    </h2>
                                    <div id="collapseSummary" class="accordion-collapse collapse"
                                        aria-labelledby="headingSummary" data-bs-parent="#financialAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="w-50 fs-13 p-0 text-uppercase">Metric</th>
                                                                    <th class="text-end fs-13 w-50 text-uppercase">Value
                                                                        (KES)
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="summary-table-body">
                                                                <tr>
                                                                    <td colspan="2" class="text-center">No records
                                                                        found!.
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-sm-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Budget Subdivision List</div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <button type="button" id="allocateStaffBtn" class="btn btn-dark px-3">
                                Allocate Staff <i class="bx bx-plus"></i>
                            </button>
                        </div>
                    </div>
                    {{-- <div class="row mb-3">
                        <div class="col-md-2">
                            <select class="form-inputs select2" id="year_filter">
                                <option value="" selected disabled>Select Fiscal Year</option>
                                @if ($filters['fiscalYears'])
                                    @foreach ($filters['fiscalYears'] as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div> --}}

                    <div class="row mb-2">
                        <div class="col-md-4 mb-4">
                            <div class="card allocation-card-counter card-counter bg-light">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-primary">Total Income Budget ({{ now()->year }})</h6>
                                        <h3 class="total-budget-value">KES 0</h3>
                                    </div>
                                    <div class="icon-container bg-primary bg-opacity-25">
                                        <i class="bx bx-money ico-status text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card allocation-card-counter card-counter bg-light">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-success">Allocated Funds ({{ now()->year }})</h6>
                                        <h3 class="allocated-funds-value">KES 0</h3>
                                    </div>
                                    <div class="icon-container bg-success bg-opacity-25">
                                        <i class="bx bx-down-arrow ico-status text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card allocation-card-counter card-counter bg-light">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-warning">Unallocated Funds ({{ now()->year }})</h6>
                                        <h3 class="unallocated-funds-value">KES 0</h3>
                                    </div>
                                    <div class="icon-container bg-warning bg-opacity-25">
                                        <i class="bx bx-info-circle ico-status text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="businessPerformanceTable" class="table table-bordered table-striped business-table">
                            <thead>
                                <tr class="bg-header-main">
                                    <th rowspan="2" class="align-middle bg-white border-white invisible"
                                        style="min-width: 30px !important; max-width: 30px !important;">ID</th>
                                    <th rowspan="2" class="align-middle">Staff</th>
                                    <th colspan="5" class="text-center col-group-new-gwp">New Business GWP</th>
                                    <th colspan="5" class="text-center col-group-new-income">New Business Income</th>
                                    <th colspan="5" class="text-center col-group-renewal-gwp">Renewal GWP</th>
                                    <th colspan="5" class="text-center col-group-renewal-income">Renewal Income</th>
                                    <th rowspan="2" class="align-middle">Actions</th>
                                </tr>
                                <tr class="bg-header-sub">
                                    <!-- New Business GWP -->
                                    <th>Facultative</th>
                                    <th>Special Lines</th>
                                    <th>Treaties</th>
                                    <th>Int. Markets</th>
                                    <th class="total-column">Total</th>
                                    <!-- New Business Income -->
                                    <th>Facultative</th>
                                    <th>Special Lines</th>
                                    <th>Treaties</th>
                                    <th>Int. Markets</th>
                                    <th class="total-column">Total</th>
                                    <!-- Renewal GWP -->
                                    <th>Facultative</th>
                                    <th>Special Lines</th>
                                    <th>Treaties</th>
                                    <th>Int. Markets</th>
                                    <th class="total-column">Total</th>
                                    <!-- Renewal Income -->
                                    <th>Facultative</th>
                                    <th>Special Lines</th>
                                    <th>Treaties</th>
                                    <th>Int. Markets</th>
                                    <th class="total-column">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table body will be populated by DataTables -->
                            </tbody>
                            <tfoot>
                                <tr class="bg-total-row">
                                    <th class="align-middle"
                                        style="min-width: 30px !important; max-width: 30px !important;"></th>
                                    <td class="fw-bold">TOTAL</td>
                                    <!-- New Business GWP Totals -->
                                    <td class="text-right new-gwp-fac-total fw-bold">0</td>
                                    <td class="text-right new-gwp-special-total fw-bold">0</td>
                                    <td class="text-right new-gwp-treaty-total fw-bold">0</td>
                                    <td class="text-right new-gwp-market-total fw-bold">0</td>
                                    <td class="text-right new-gwp-total fw-bold total-column">0</td>

                                    <!-- New Business Income Totals -->
                                    <td class="text-right new-income-fac-total fw-bold">0</td>
                                    <td class="text-right new-income-special-total fw-bold">0</td>
                                    <td class="text-right new-income-treaty-total fw-bold">0</td>
                                    <td class="text-right new-income-market-total fw-bold">0</td>
                                    <td class="text-right new-income-total fw-bold total-column">0</td>

                                    <!-- Renewal GWP Totals -->
                                    <td class="text-right renewal-gwp-fac-total fw-bold">0</td>
                                    <td class="text-right renewal-gwp-special-total fw-bold">0</td>
                                    <td class="text-right renewal-gwp-treaty-total fw-bold">0</td>
                                    <td class="text-right renewal-gwp-market-total fw-bold">0</td>
                                    <td class="text-right renewal-gwp-total fw-bold total-column">0</td>

                                    <!-- Renewal Income Totals -->
                                    <td class="text-right renewal-income-fac-total fw-bold">0</td>
                                    <td class="text-right renewal-income-special-total fw-bold">0</td>
                                    <td class="text-right renewal-income-treaty-total fw-bold">0</td>
                                    <td class="text-right renewal-income-market-total fw-bold">0</td>
                                    <td class="text-right renewal-income-total fw-bold total-column">0</td>
                                    <td class="text-right actions-span fw-bold"></td>
                                </tr>
                                <tr class="bg-grand-total">
                                    <td colspan="2" class="fw-bold">Combined Total GWP</td>
                                    <td colspan="10" class="text-right combined-gwp-total fw-bold">KES 0</td>
                                    <td colspan="2" class="fw-bold">Combined Total Income</td>
                                    <td colspan="9" class="text-right combined-income-total fw-bold">KES 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="mt-3 small text-muted">
                        <p>Note: All figures are presented in KES.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="modal effect-fall md-wrapper" id="performanceRecordModal" tabindex="-1"
                aria-labelledby="performanceRecordModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title dc-modal-title" id="performanceRecordModalLabel"> <i
                                    class="bx bx-user-plus fs-18 me-2"></i> Performance Record Entry</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="performanceRecordForm" action="{{ route('admin.performance-records.store') }}"
                            method="POST">
                            @csrf
                            <div class="modal-body">
                                <ul class="nav nav-tabs mb-3" id="performanceTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="new-business-tab" data-bs-toggle="tab"
                                            data-bs-target="#new-business" type="button" role="tab"
                                            aria-controls="new-business" aria-selected="true">New Business</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="renewal-business-tab" data-bs-toggle="tab"
                                            data-bs-target="#renewal-business" type="button" role="tab"
                                            aria-controls="renewal-business" aria-selected="false">Renewal
                                            Business</button>
                                    </li>
                                </ul>

                                <div class="mb-4">
                                    <h6 class="mb-1">Account Information</h6>
                                    <hr class="pt-0 mt-0" />
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="accountHandler" class="form-label">Staff</label>
                                            <div class="card-md">
                                                <select class="form-inputs select2" id="accountHandler"
                                                    name="account_handler[]" required multiple>
                                                    <option disabled>-- Select Staff --</option>
                                                    @if ($staff)
                                                        @foreach ($staff as $s)
                                                            <option value="{{ $s->id }}">{{ $s->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-6">
                                            <label for="totalAllocatedFunds" class="form-label">Total Allocated
                                                Funds</label>
                                            <input type="text" class="form-inputs" id="totalAllocatedFunds"
                                                name="account_period" value="233" readonly />
                                        </div> --}}
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="accountPeriod" class="form-label">Account Period</label>
                                            <input type="text" class="form-inputs" id="accountPeriod"
                                                name="account_period" data-default-period="{{ $currentPeriod }}"
                                                value="{{ $currentPeriod }}" readonly />
                                        </div>

                                        <div class="col-md-6">
                                            <label for="recordDate" class="form-label">Record Date</label>
                                            <input type="date" class="form-inputs" id="recordDate" name="record_date"
                                                value="{{ date('Y-m-d') }}" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="new-business" role="tabpanel"
                                        aria-labelledby="new-business-tab">
                                        <div class="p-3">
                                            <div class="mb-3">
                                                <h6 class="mb-1">New Business GWP</h6>
                                                <hr class="pt-0 mt-0" />

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="newFacGwp" class="form-label">Facultative</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                id="newFacGwp"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                name="new_fac_gwp" placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="newSpecialGwp" class="form-label">Special
                                                            Lines</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                id="newSpecialGwp" name="new_special_gwp"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="newTreatyGwp" class="form-label">Treaty</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                id="newTreatyGwp" name="new_treaty_gwp"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="newMarketGwp" class="form-label">Market
                                                            Expansion</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                id="newMarketGwp" name="new_market_gwp"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-1 mt-4">
                                                <h6 class="mb-1">New Business Income</h6>
                                                <hr class="pt-0 mt-0" />

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="newFacIncome" class="form-label">Facultative</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                id="newFacIncome" name="new_fac_income"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="newSpecialIncome" class="form-label">Special
                                                            Lines</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                id="newSpecialIncome" name="new_special_income"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="newTreatyIncome" class="form-label">Treaty</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                id="newTreatyIncome" name="new_treaty_income"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="newMarketIncome" class="form-label">Market
                                                            Expansion</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                id="newMarketIncome" name="new_market_income"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="renewal-business" role="tabpanel"
                                        aria-labelledby="renewal-business-tab">
                                        <div class="p-3">
                                            <div class="mb-3">
                                                <h6 class="mb-1">Renewal Business GWP</h6>
                                                <hr class="pt-0 mt-0" />

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="renewalFacGwp" class="form-label">Facultative</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                id="renewalFacGwp" name="renewal_fac_gwp"
                                                                placeholder="0.00"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="renewalSpecialGwp" class="form-label">Special
                                                            Lines</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                id="renewalSpecialGwp" name="renewal_special_gwp"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="renewalTreatyGwp" class="form-label">Treaty</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                id="renewalTreatyGwp" name="renewal_treaty_gwp"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="renewalMarketGwp" class="form-label">Market
                                                            Expansion</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                id="renewalMarketGwp" name="renewal_market_gwp"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-1 mt-4">
                                                <h6 class="mb-1">Renewal Business Income</h6>
                                                <hr class="pt-0 mt-0" />

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="renewalFacIncome"
                                                            class="form-label">Facultative</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                id="renewalFacIncome" name="renewal_fac_income"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="renewalSpecialIncome" class="form-label">Special
                                                            Lines</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                id="renewalSpecialIncome" name="renewal_special_income"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="renewalTreatyIncome" class="form-label">Treaty</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                id="renewalTreatyIncome" name="renewal_treaty_income"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="renewalMarketIncome" class="form-label">Market
                                                            Expansion</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="text" class="form-control color-blk"
                                                                id="renewalMarketIncome" name="renewal_market_income"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                onchange="this.value=numberWithCommas(this.value)"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" id="submitPerformanceRecordBtn"
                                        class="btn btn-dark btn-wave waves-effect waves-light px-3">Submit
                                        Record</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="modal effect-super-scaled md-wrapper" id="staffAccountSummaryModal" tabindex="-1"
                aria-labelledby="staffAccountSummaryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title dc-modal-title" id="staffAccountSummaryModalLabel"> <i
                                    class="bi bi-file-text fs-18 me-2"></i> Account Summary</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body mb-0">
                            <div class="card custom-card mb-0 border-bottom-none">
                                <div class="card-header">
                                    <div class="card-title">Account Information</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label">Account Handler</label>
                                            <input class="form-control fw-bold" type="text" value="Kennedy Peter"
                                                aria-label="account_handler" readonly="">
                                        </div>
                                        {{-- <div class="col-md-6">
                                            <label class="form-label">Total Allocated Funds</label>
                                            <input class="form-control fw-bold" type="text" value="833,403.00"
                                                aria-label="total_allocated_funds" readonly="">
                                        </div> --}}
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Account Period</label>
                                            <input class="form-control fw-bold" type="text" value="2025/04"
                                                aria-label="account_period" readonly="">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Record Date</label>
                                            <input class="form-control fw-bold" type="text" value="10/04/2025"
                                                aria-label="record_date" readonly="">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Last Modified</label>
                                            <input class="form-control fw-bold" type="text" value="10/04/2025 09:99"
                                                aria-label="record_date" readonly="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-0 pb-0">
                                <div class="card-body mb-0 pb-0">
                                    <div class="row info-cards mb-0">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                    <div class="card border border-primary custom-card">
                                                        <div
                                                            class="d-flex flex-wrap align-items-top justify-content-between">
                                                            <div class="flex-fill">
                                                                <p class="mb-0 text-muted">Total Registerd Covers</p>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fs-5 fw-semibold">0</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                    <div class="card border border-info custom-card">
                                                        <div
                                                            class="d-flex flex-wrap align-items-top justify-content-between">
                                                            <div class="flex-fill">
                                                                <p class="mb-0 text-muted">Debited Covers</p>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fs-5 fw-semibold">0</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                    <div class="card border border-dark custom-card">
                                                        <div
                                                            class="d-flex flex-wrap align-items-top justify-content-between">
                                                            <div class="flex-fill">
                                                                <p class="mb-0 text-muted">Facultative Proportional</p>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fs-5 fw-semibold">0</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                    <div class="card border border-secondary custom-card">
                                                        <div
                                                            class="d-flex flex-wrap align-items-top justify-content-between">
                                                            <div class="flex-fill">
                                                                <p class="mb-0 text-muted">Facultative Non-Proportional</p>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fs-5 fw-semibold">0</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                    <div class="card border border-warning custom-card">
                                                        <div
                                                            class="d-flex flex-wrap align-items-top justify-content-between">
                                                            <div class="flex-fill">
                                                                <p class="mb-0 text-muted">Treaty Proportional</p>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fs-5 fw-semibold">0</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                    <div class="card border border-success custom-card">
                                                        <div
                                                            class="d-flex flex-wrap align-items-top justify-content-between">
                                                            <div class="flex-fill">
                                                                <p class="mb-0 text-muted">Treaty Non-Proportional</p>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fs-5 fw-semibold">0</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-summary">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">Total GWP (Combined)</h6>
                                                        <h3 class="card-title">KES {{ number_format(0, 2) }}</h3>
                                                        <p class="card-text text-success mb-0">
                                                            <i class="bi bi-arrow-up-right"></i> 0% vs Previous Period
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">Total Income (Combined)
                                                        </h6>
                                                        <h3 class="card-title">KES {{ number_format(0, 2) }}</h3>
                                                        <p class="card-text text-success mb-0">
                                                            <i class="bi bi-arrow-up-right"></i> 0% vs Previous Period
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">Commission Rate</h6>
                                                        <h3 class="card-title">24.3%</h3>
                                                        <p class="card-text text-danger mb-0">
                                                            <i class="bi bi-arrow-down-right"></i> 0% vs Previous Period
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="m-0 p-0" />

                                    <div class="card custom-card">
                                        <div class="card-header bg-light">
                                            <div class="card-title">Performance Breakdown</div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr class="fs-13">
                                                            <th>Category</th>
                                                            <th>New Business GWP</th>
                                                            <th>New Business Income</th>
                                                            <th>Renewal GWP</th>
                                                            <th>Renewal Income</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No records found</td>
                                                        </tr>
                                                    </tbody>
                                                    {{-- <tbody>
                                                        <tr>
                                                            <td>Facultative</td>
                                                            <td>KES {{ number_format(246000000, 2) }}</td>
                                                            <td>KES {{ number_format(15000000, 2) }}</td>
                                                            <td>KES {{ number_format(0, 2) }}</td>
                                                            <td>KES {{ number_format(0, 2) }}</td>
                                                            <td><span class="badge bg-success">On Target</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Special Lines</td>
                                                            <td>KES {{ number_format(60000000, 2) }}</td>
                                                            <td>KES {{ number_format(3000000, 2) }}</td>
                                                            <td>KES {{ number_format(0, 2) }}</td>
                                                            <td>KES {{ number_format(0, 2) }}</td>
                                                            <td><span class="badge bg-warning text-dark">Below
                                                                    Target</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Treaty</td>
                                                            <td>KES {{ number_format(120000000, 2) }}</td>
                                                            <td>KES {{ number_format(9900000, 2) }}</td>
                                                            <td>KES {{ number_format(103030554, 2) }}</td>
                                                            <td>KES {{ number_format(2575764, 2) }}</td>
                                                            <td><span class="badge bg-success">On Target</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Market Exapnsion</td>
                                                            <td>KES {{ number_format(120000000, 2) }}</td>
                                                            <td>KES {{ number_format(9900000, 2) }}</td>
                                                            <td>KES {{ number_format(103030554, 2) }}</td>
                                                            <td>KES {{ number_format(2575764, 2) }}</td>
                                                            <td><span class="badge bg-success">On Target</span></td>
                                                        </tr>
                                                    </tbody> --}}
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card custom-card mb-4">
                                        <div class="card-header bg-light">
                                            <div class="card-title">Performance Details</div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>New Business GWP</th>
                                                            <th>New Business Income</th>
                                                            <th>Renewal GWP</th>
                                                            <th>Renewal Income</th>
                                                            <th>Achievement</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="5" class="text-center">No records found</td>
                                                        </tr>
                                                    </tbody>

                                                    {{--  <tbody>
                                                        @foreach ($staffPerformance as $staff)
                                                        <tr>
                                                            <td>{{ $staff->name }}</td>
                                                            <td>${{ number_format($staff->new_business_gwp, 2) }}</td>
                                                            <td>${{ number_format($staff->new_business_income, 2) }}</td>
                                                            <td>${{ number_format($staff->renewal_gwp, 2) }}</td>
                                                            <td>${{ number_format($staff->renewal_income, 2) }}</td>
                                                            <td>{{ $staff->achievement }}%</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody> --}}
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer mt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var defaultYear = @json($filters['defaultYear']);
            const budgetData = @json($budgetData);

            let $bpTable = $('#businessPerformanceTable').DataTable({
                paging: true,
                ordering: true,
                info: true,
                searching: true,
                responsive: false,
                autoWidth: false, // Changed to false for better column width control
                pageLength: 25,
                lengthMenu: [25, 50, 100],
                stripeClasses: ['odd', 'even'],
                columnDefs: [{
                        orderable: true,
                        targets: [0]
                    },
                    {
                        width: '5%',
                        targets: 0
                    },
                    {
                        width: '10%',
                        targets: 1
                    },
                    {
                        width: '4%',
                        className: 'text-right',
                        targets: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                            21
                        ]
                    },
                    {
                        width: '5%',
                        className: 'text-right fw-medium total-column',
                        targets: [6, 11, 16, 21]
                    },
                    {
                        width: '5%',
                        targets: 22
                    }
                ],
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.staff_budget_allocation.data') }}",
                    type: "GET",
                    dataSrc: function(json) {
                        updateTotals(json.totals, json.combinedTotalGWP, json.combinedTotalIncome);
                        return json.data;
                    }
                },
                columns: [{
                        data: 'id',
                        searchable: false,
                        className: 'highlight-idx',
                        sortable: false,
                        orderable: false,
                    }, {
                        data: 'handler',
                        className: 'highlight-action'
                    },
                    // New Business GWP
                    {
                        data: 'newGWP.fac',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'newGWP.special',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'newGWP.treaty',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'newGWP.market_expansion',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'newGWP.total',
                        render: $.fn.dataTable.render.number(',', '.', 0),
                        className: 'fw-medium total-column'
                    },
                    // New Business Income
                    {
                        data: 'newIncome.fac',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'newIncome.special',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'newIncome.treaty',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'newIncome.market_expansion',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'newIncome.total',
                        render: $.fn.dataTable.render.number(',', '.', 0),
                        className: 'fw-medium total-column'
                    },
                    // Renewal GWP
                    {
                        data: 'renewalGWP.fac',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'renewalGWP.special',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'renewalGWP.treaty',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'renewalGWP.market_expansion',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'renewalGWP.total',
                        render: $.fn.dataTable.render.number(',', '.', 0),
                        className: 'fw-medium total-column'
                    },
                    // Renewal Income
                    {
                        data: 'renewalIncome.fac',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'renewalIncome.special',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'renewalIncome.treaty',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'renewalIncome.market_expansion',
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                    {
                        data: 'renewalIncome.total',
                        render: $.fn.dataTable.render.number(',', '.', 0),
                        className: 'fw-medium total-column'
                    },
                    {
                        data: 'actions',
                        sortable: false,
                        orderable: false,
                        className: 'fw-medium highlight-idx highlight-overflow',
                        render: function(data, type, row) {
                            return `<div id="dropdown-${row.id}" class="btn-group my-0">
                                        <button type="button" id="dropdown-btn-${row.id}" class="btn btn-icon btn-sm btn-light p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul id="dropdown-menu-${row.id}" class="dropdown-menu">
                                            <li><a id="summary-item-${row.id}" class="dropdown-item summary-allocate-btn" href="javascript:void(0);">
                                                <i class="bi bi-file-text me-2"></i>Account Summary
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a id="edit-item-${row.id}" class="dropdown-item edit-allocate-btn" href="javascript:void(0);">
                                                <i class="bi bi-pencil me-2"></i>Edit Record
                                            </a></li>
                                            <li><a id="delete-item-${row.id}" class="dropdown-item delete-allocate-btn text-danger" href="javascript:void(0);">
                                                <i class="bi bi-trash me-2"></i>Delete Record
                                            </a></li>
                                        </ul>
                                    </div>`;
                        }
                    },
                ],
                language: {
                    "oPaginate": {
                        "sFirst": "First",
                        "sPrevious": "<",
                        "sNext": ">",
                        "sLast": "Last"
                    }
                },
                drawCallback: function() {
                    $('.total-column').css({
                        'backgroundColor': '#f8f9fa',
                        'color': '#000'
                    });
                }
            });

            function updateTotals(totals, combinedTotalGWP, combinedTotalIncome) {
                $('.new-gwp-fac-total').text(formatNumber(totals.newGWP.fac));
                $('.new-gwp-special-total').text(formatNumber(totals.newGWP.special));
                $('.new-gwp-treaty-total').text(formatNumber(totals.newGWP.treaty));
                $('.new-gwp-market-total').text(formatNumber(totals.newGWP.market_expansion));
                $('.new-gwp-total').text(formatNumber(totals.newGWP.total));

                $('.new-income-fac-total').text(formatNumber(totals.newIncome.fac));
                $('.new-income-special-total').text(formatNumber(totals.newIncome.special));
                $('.new-income-treaty-total').text(formatNumber(totals.newIncome.treaty));
                $('.new-income-market-total').text(formatNumber(totals.newIncome.market_expansion));
                $('.new-income-total').text(formatNumber(totals.newIncome.total));

                $('.renewal-gwp-fac-total').text(formatNumber(totals.renewalGWP.fac));
                $('.renewal-gwp-special-total').text(formatNumber(totals.renewalGWP.special));
                $('.renewal-gwp-treaty-total').text(formatNumber(totals.renewalGWP.treaty));
                $('.renewal-gwp-market-total').text(formatNumber(totals.renewalGWP.market_expansion));
                $('.renewal-gwp-total').text(formatNumber(totals.renewalGWP.total));

                $('.renewal-income-fac-total').text(formatNumber(totals.renewalIncome.fac));
                $('.renewal-income-special-total').text(formatNumber(totals.renewalIncome.special));
                $('.renewal-income-treaty-total').text(formatNumber(totals.renewalIncome.treaty));
                $('.renewal-income-market-total').text(formatNumber(totals.renewalIncome.market_expansion));
                $('.renewal-income-total').text(formatNumber(totals.renewalIncome.total));

                $('.combined-gwp-total').text('KES ' + formatNumber(combinedTotalGWP));
                $('.combined-income-total').text('KES ' + formatNumber(combinedTotalIncome));
            }

            function formatNumber(number) {
                return new Intl.NumberFormat('en-US').format(number);
            }

            $("#allocateStaffBtn").click(function() {
                $('#performanceRecordModal').modal('show');
            });

            $("#accountHandler").on("change", function() {
                $(this).valid();
            })

            $('.form-control.color-blk').on('change', function() {
                calculateTotals();
            });

            function calculateTotals() {
                let newGwpTotal = 0;
                let newIncomeTotal = 0;

                newGwpTotal += parseFloat(stripCommas($('#newFacGwp').val()) || 0);
                newGwpTotal += parseFloat(stripCommas($('#newSpecialGwp').val()) || 0);
                newGwpTotal += parseFloat(stripCommas($('#newTreatyGwp').val()) || 0);
                newGwpTotal += parseFloat(stripCommas($('#newMarketGwp').val()) || 0);

                newIncomeTotal += parseFloat(stripCommas($('#newFacIncome').val()) || 0);
                newIncomeTotal += parseFloat(stripCommas($('#newSpecialIncome').val()) || 0);
                newIncomeTotal += parseFloat(stripCommas($('#newTreatyIncome').val()) || 0);
                newIncomeTotal += parseFloat(stripCommas($('#newMarketIncome').val()) || 0);

                let renewalGwpTotal = 0;
                let renewalIncomeTotal = 0;

                renewalGwpTotal += parseFloat(stripCommas($('#renewalFacGwp').val()) || 0);
                renewalGwpTotal += parseFloat(stripCommas($('#renewalSpecialGwp').val()) || 0);
                renewalGwpTotal += parseFloat(stripCommas($('#renewalTreatyGwp').val()) || 0);
                renewalGwpTotal += parseFloat(stripCommas($('#renewalMarketGwp').val()) || 0);

                renewalIncomeTotal += parseFloat(stripCommas($('#renewalFacIncome').val()) || 0);
                renewalIncomeTotal += parseFloat(stripCommas($('#renewalSpecialIncome').val()) || 0);
                renewalIncomeTotal += parseFloat(stripCommas($('#renewalTreatyIncome').val()) || 0);
                renewalIncomeTotal += parseFloat(stripCommas($('#renewalMarketIncome').val()) || 0);


                // $('#newGwpTotal').text(numberWithCommas(newGwpTotal.toFixed(2)));
                // $('#newIncomeTotal').text(numberWithCommas(newIncomeTotal.toFixed(2)));
                // $('#renewalGwpTotal').text(numberWithCommas(renewalGwpTotal.toFixed(2)));
                // $('#renewalIncomeTotal').text(numberWithCommas(renewalIncomeTotal.toFixed(2)));
            }

            function stripCommas(value) {
                return value ? value.toString().replace(/,/g, '') : '0';
            }

            function calculateRenewalBusinessTotals() {
                let renewalGwpTotal = 0;
                let renewalIncomeTotal = 0;

                renewalGwpTotal += parseFloat(stripCommas($('#renewalFacGwp').val()) || 0);
                renewalGwpTotal += parseFloat(stripCommas($('#renewalSpecialGwp').val()) || 0);
                renewalGwpTotal += parseFloat(stripCommas($('#renewalTreatyGwp').val()) || 0);
                renewalGwpTotal += parseFloat(stripCommas($('#renewalMarketGwp').val()) || 0);

                renewalIncomeTotal += parseFloat(stripCommas($('#renewalFacIncome').val()) || 0);
                renewalIncomeTotal += parseFloat(stripCommas($('#renewalSpecialIncome').val()) || 0);
                renewalIncomeTotal += parseFloat(stripCommas($('#renewalTreatyIncome').val()) || 0);
                renewalIncomeTotal += parseFloat(stripCommas($('#renewalMarketIncome').val()) || 0);

                if ($('#renewalGwpTotal').length) {
                    $('#renewalGwpTotal').text(numberWithCommas(renewalGwpTotal.toFixed(2)));
                }

                if ($('#renewalIncomeTotal').length) {
                    $('#renewalIncomeTotal').text(numberWithCommas(renewalIncomeTotal.toFixed(2)));
                }

                return {
                    gwp: renewalGwpTotal,
                    income: renewalIncomeTotal
                };
            }

            $bpTable.on('click', '.delete-allocate-btn', function(e) {
                e.preventDefault();
                const row = $bpTable.row($(this).closest('tr'));
                const data = row.data();

                Swal.fire({
                    title: 'Delete Budget Allocation',
                    text: `Are you sure you want to delete this budget allocation record? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.budget_allocation.destroy') }}",
                            type: 'DELETE',
                            data: {
                                id: data.id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(
                                        'Budget allocation deleted successfully');
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1500);
                                } else {
                                    toastr.error(response.message ||
                                        'Failed to delete budget allocation');
                                }
                            },
                            error: function(xhr) {
                                toastr.error(
                                    'An error occurred while deleting the budget allocation'
                                );
                            }
                        });
                    }
                });
            });

            $bpTable.on('click', '.summary-allocate-btn', function(e) {
                e.preventDefault()
                const row = $bpTable.row($(this).closest('tr'));
                const data = row.data();

                $('#staffAccountSummaryModal').modal('show');
            });


            if (typeof baseUrl === 'undefined') {
                baseUrl = $('meta[name="base-url"]').attr('content') || '';
            }

            $bpTable.on('click', '.edit-allocate-btn', function(e) {
                e.preventDefault()
                const row = $bpTable.row($(this).closest('tr'));
                const data = row.data();
                editPerformanceRecord(data);
            });

            function editPerformanceRecord(data) {
                const submitBtn = $("#submitPerformanceRecordBtn");
                const originalBtnText = submitBtn.html();
                submitBtn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );
                submitBtn.prop('disabled', true);
                $('#performanceRecordForm')[0].reset();
                $('#performanceRecordForm').attr('action', `${baseUrl}/admin/performance-records/${data.id}`);

                if ($('#methodField').length === 0) {
                    $('#performanceRecordForm').append(
                        '<input type="hidden" name="_method" id="methodField" value="PUT">');
                }
                $('#performanceRecordModalLabel').html(
                    '<i class="bx bx-edit fs-18 me-2"></i> Edit Performance Record');

                $('#accountHandler').val([data.actions.handlerId]).trigger('change');

                $('#accountPeriod').val(data.actions.accountPeriod);
                $('#recordDate').val(data.actions.recordDate);

                $('#newFacGwp').val(numberWithCommas(data.newGWP.fac || '0.00'));
                $('#newSpecialGwp').val(numberWithCommas(data.newGWP.special || '0.00'));
                $('#newTreatyGwp').val(numberWithCommas(data.newGWP.treaty || '0.00'));
                $('#newMarketGwp').val(numberWithCommas(data.newGWP.market_expansion || '0.00'));

                $('#newFacIncome').val(numberWithCommas(data.newIncome.fac || '0.00'));
                $('#newSpecialIncome').val(numberWithCommas(data.newIncome.special ||
                    '0.00'));
                $('#newTreatyIncome').val(numberWithCommas(data.newIncome.treaty || '0.00'));
                $('#newMarketIncome').val(numberWithCommas(data.newIncome.market_expansion || '0.00'));

                $('#renewalFacGwp').val(numberWithCommas(data.renewalGWP.fac || '0.00'));
                $('#renewalSpecialGwp').val(numberWithCommas(data.renewalGWP.special ||
                    '0.00'));
                $('#renewalTreatyGwp').val(numberWithCommas(data.renewalGWP.treaty ||
                    '0.00'));
                $('#renewalMarketGwp').val(numberWithCommas(data.renewalGWP.market_expansion ||
                    '0.00'));

                $('#renewalFacIncome').val(numberWithCommas(data.renewalIncome.fac ||
                    '0.00'));
                $('#renewalSpecialIncome').val(numberWithCommas(data.renewalIncome.special ||
                    '0.00'));
                $('#renewalTreatyIncome').val(numberWithCommas(data.renewalIncome.treaty ||
                    '0.00'));
                $('#renewalMarketIncome').val(numberWithCommas(data.renewalIncome.market_expansion ||
                    '0.00'));

                $('#performanceRecordModal').modal('show');

                submitBtn.html('Update Record');
                submitBtn.prop('disabled', false);
            }

            $('#performanceRecordModal').on('hidden.bs.modal', function() {
                resetPerformanceRecordForm();
            });

            function resetPerformanceRecordForm() {
                $('#performanceRecordForm')[0].reset();
                $('#performanceRecordForm').attr('action', `${baseUrl}/admin/performance-records`);
                $('#methodField').remove();
                $('#performanceRecordModalLabel').html(
                    '<i class="bx bx-user-plus fs-18 me-2"></i> Performance Record Entry');
                $('#submitPerformanceRecordBtn').html('Submit Record');
                $('.errorClass').remove();

                $('#accountPeriod').val($('#accountPeriod').data('default-period') || '');
                $('#recordDate').val(new Date().toISOString().split('T')[0]);

                $('#accountHandler').val(null).trigger('change');
                $('#new-business-tab').tab('show');
            }

            $('#performanceRecordForm').validate({
                errorClass: "errorClass",
                rules: {
                    account_handler: {
                        required: true
                    },
                    record_date: {
                        required: true,
                        date: true
                    },
                },
                submitHandler: function(form, e) {
                    e.preventDefault();
                    const submitBtn = $("#submitPerformanceRecordBtn");
                    const originalBtnText = submitBtn.html();
                    submitBtn.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                    );
                    submitBtn.prop('disabled', true);

                    const method = $('#methodField').length > 0 ? 'PUT' : 'POST';
                    // $.ajax({
                    //     url: $(form).attr('action'),
                    //     type: method,
                    //     data: $(form).serialize(),
                    //     success: function(response) {
                    //         if (response.success) {
                    //             Swal.fire({
                    //                 icon: 'success',
                    //                 title: 'Success',
                    //                 text: response.message,
                    //                 confirmButtonText: 'OK'
                    //             }).then((result) => {
                    //                 if (result.isConfirmed) {
                    //                     $('#performanceRecordModal').modal('hide');
                    //                     $bpTable.ajax.reload();
                    //                     resetPerformanceRecordForm();
                    //                 }
                    //             });
                    //         } else {
                    //             Swal.fire({
                    //                 icon: 'error',
                    //                 title: 'Error',
                    //                 text: response.message ||
                    //                     'Failed to save record',
                    //                 confirmButtonText: 'OK'
                    //             });
                    //         }
                    //     },
                    //     error: function(xhr) {
                    //         if (xhr.status === 422) {
                    //             const errors = xhr.responseJSON.errors;
                    //             let errorMessage = '<ul class="mb-0">';
                    //             Object.values(errors).forEach(value => {
                    //                 errorMessage += `<li>${value}</li>`;
                    //             });
                    //             errorMessage += '</ul>';

                    //             Swal.fire({
                    //                 icon: 'error',
                    //                 title: 'Validation Error',
                    //                 html: errorMessage,
                    //                 confirmButtonText: 'OK'
                    //             });
                    //         } else {
                    //             Swal.fire({
                    //                 icon: 'error',
                    //                 title: 'Error',
                    //                 text: 'Failed to save record. Please try again.',
                    //                 confirmButtonText: 'OK'
                    //             });
                    //         }
                    //     },
                    //     complete: function() {
                    //         submitBtn.html(originalBtnText);
                    //         submitBtn.prop('disabled', false);
                    //     }
                    // });

                    $.ajax({
                        url: $(form).attr('action'),
                        type: method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#performanceRecordModal').modal('hide');
                                toastr.success(response.message ||
                                    'Record saved successfully');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Unable to Process',
                                    text: response.message ||
                                        'We encountered an issue while processing your request.',
                                    confirmButtonText: 'Try Again',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                let errorContent = '<div class="text-left">';
                                errorContent +=
                                    '<p class="mb-2">Please address the following items:</p>';
                                errorContent += '<ul class="error-list p-0 m-0">';

                                Object.entries(errors).forEach(([field, messages]) => {
                                    const fieldName = field.replace(/([A-Z])/g,
                                            ' $1')
                                        .replace(/_/g, ' ')
                                        .replace(/^\w/, c => c.toUpperCase());

                                    errorContent +=
                                        `<li class="mb-1"><span class="font-weight-bold">${fieldName}:</span> ${messages[0]}</li>`;
                                });

                                errorContent += '</ul></div>';

                                Swal.fire({
                                    icon: 'info',
                                    title: 'Action Required',
                                    html: errorContent,
                                    confirmButtonText: 'Ok',
                                    confirmButtonColor: '#3085d6',
                                    showClass: {
                                        popup: 'animate__animated animate__fadeInDown'
                                    },
                                    hideClass: {
                                        popup: 'animate__animated animate__fadeOutUp'
                                    }
                                });
                            } else if (xhr.status === 403) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Access Restricted',
                                    text: 'You don\'t have permission to perform this action. Please contact your administrator.',
                                    confirmButtonText: 'Understood',
                                    confirmButtonColor: '#3085d6'
                                });
                            } else if (xhr.status === 429) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Too Many Requests',
                                    text: 'Please wait a moment before trying again.',
                                    confirmButtonText: 'Got it',
                                    confirmButtonColor: '#3085d6',
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'System Notification',
                                    html: '<p>We\'re unable to complete your request at this time.</p>' +
                                        '<p class="small mt-2">Reference: ' +
                                        (xhr.responseJSON?.reference ||
                                            generateErrorReference()) + '</p>',
                                    confirmButtonText: 'Acknowledge',
                                    confirmButtonColor: '#3085d6',
                                    footer: '<a href="#" onclick="reportIssue()">Report this issue</a>'
                                });
                            }
                        },
                        complete: function() {
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);

                            submitBtn.addClass('btn-ready').delay(700).queue(function() {
                                $(this).removeClass('btn-ready').dequeue();
                            });
                        }
                    });


                }
            });

            function generateErrorReference() {
                return 'ERR-' + new Date().getTime().toString().slice(-6) + '-' +
                    Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            }

            function formatCurrency(value) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'KES',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                }).format(value);
            }

            function loadFinancialData(year) {
                $('#income-table-body, #expense-table-body, #summary-table-body').html(
                    '<tr><td colspan="2" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: '{{ route('admin.budget_allocation.data') }}',
                    type: 'GET',
                    data: {
                        year: year
                    },
                    dataType: 'json',
                    success: function(data) {
                        const totalIncome = data.incomes?.find(item => item.subcategory ===
                            'Total Budgeted Income')?.amount ?? 0;
                        const totalExpenses = data.expenses?.find(item => item.subcategory ===
                            'Total Expenses')?.amount ?? 0;
                        const grossProfit = data.summary?.grossProfit ?? 0;

                        const formatValue = (value) => {
                            return value;
                        };

                        $('#total-income').text(formatValue(totalIncome));
                        $('#total-expenses').text(formatValue(totalExpenses));
                        $('#gross-profit').text(formatValue(grossProfit));
                        $('#profit-margin').text((parseFloat(data.summary?.profitMargin || 0).toFixed(
                            2) || '0.00') + '%');
                        $('#cost-income-ratio').text((parseFloat(data.summary?.costIncomeRatio || 0)
                            .toFixed(2) || '0.00') + '%');

                        // Populate income table
                        let incomeHtml = '<tr class="pb-2"></tr>';
                        (data.incomes || []).forEach(function(item) {
                            incomeHtml += `<tr class="${item.isTotal ? 'row-total' : ''}">
                            <td>${item.subcategory || ''}</td>
                            <td class="text-end">${formatValue(item.amount)}</td>
                        </tr>`;
                        });
                        $('#income-table-body').html(incomeHtml ||
                            '<tr><td colspan="2" class="text-center">No data available</td></tr>');

                        // Populate expense table
                        let expenseHtml = '<tr class="pb-2"></tr>';
                        (data.expenses || []).forEach(function(item) {
                            expenseHtml += `<tr class="${item.isTotal ? 'row-total' : ''}">
                            <td>${item.subcategory || ''}</td>
                            <td class="text-end">${formatValue(item.amount)}</td>
                        </tr>`;
                        });
                        $('#expense-table-body').html(expenseHtml ||
                            '<tr><td colspan="2" class="text-center">No data available</td></tr>');

                        // Populate summary table
                        const summaryHtml = `
                        <tr class="pb-2"></tr>
                        <tr>
                            <td>Total Income</td>
                            <td class="text-end">${formatValue(totalIncome)}</td>
                        </tr>
                        <tr>
                            <td>Total Expenses</td>
                            <td class="text-end">${formatValue(totalExpenses)}</td>
                        </tr>
                        <tr class="row-total">
                            <td>Gross Profit</td>
                            <td class="text-end">${formatValue(grossProfit)}</td>
                        </tr>
                        <tr>
                            <td>Cost-Income Ratio</td>
                            <td class="text-end">${(parseFloat(data.summary?.costIncomeRatio || 0).toFixed(2) || '0.00')}%</td>
                        </tr>
                        <tr>
                            <td>Profit Margin</td>
                            <td class="text-end">${(parseFloat(data.summary?.profitMargin || 0).toFixed(2) || '0.00')}%</td>
                        </tr>
                    `;
                        $('#summary-table-body').html(summaryHtml);
                    },
                    error: function() {
                        toast.error('Error loading financial data. Please try again.');
                    }
                });
            }

            $('#fiscalYearFilter').select2({
                placeholder: "Select Financial Year",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('admin.fiscal-years') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.years.map(function(year) {
                                return {
                                    id: year,
                                    text: 'Financial Year ' + year
                                };
                            }),
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            }).on('select2:open', function() {
                if (!$(this).val()) {
                    var $option = new Option('Financial Year ' + defaultYear, defaultYear, true, true);
                    $(this).append($option).trigger('change');
                }
            });

            var $defaultOption = new Option('Financial Year ' + defaultYear, defaultYear, true, true);
            $('#fiscalYearFilter').append($defaultOption).trigger('change');

            $('#fiscalYearFilter').on('change', function() {
                const selectedYear = $(this).val();
                if (selectedYear) {
                    const year = Array.isArray(selectedYear) ? selectedYear[0] : selectedYear;
                    loadFinancialData(parseInt(year));
                }
            });

            if (defaultYear) {
                const year = Array.isArray(defaultYear) ? defaultYear[0] : defaultYear;
                loadFinancialData(parseInt(year));
            }

            function updateDashboard(data) {
                const result = data ? JSON.parse(data) : 0;

                const totalBudget = result.totalBudget;
                const totalAllocated = result.allocated;
                const totalUnallocated = result.unallocated;

                $('.total-budget-value').text(formatCurrency(totalBudget));
                $('.allocated-funds-value').text(formatCurrency(totalAllocated));
                $('.unallocated-funds-value').text(formatCurrency(totalUnallocated));
            }

            function formatCurrency(amount) {
                return amount
            }

            updateDashboard(budgetData);
        });
    </script>
@endpush
