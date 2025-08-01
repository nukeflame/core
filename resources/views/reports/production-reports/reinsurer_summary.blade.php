<style>
    .content-area {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
    }

    .table-toolbar {
        display: flex;
        justify-content: space-between;
        padding: 14px;
        background-color: #f9fafb;
        border-bottom: 1px solid #e0e4e8;
    }

    .table-title {
        font-size: 16px;
        font-weight: 500;
    }

    .table-actions {
        display: flex;
        gap: 10px;
    }

    .action-btn {
        padding: 6px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: white;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background-color: #f0f2f5;
    }

    .export-btn {
        background-color: #3498db;
        color: white;
        border: none;
    }

    .export-btn:hover {
        background-color: #2980b9;
    }

    .swal2-select {
        width: 100%;
        padding: 0.5rem;
        margin-bottom: 1rem;
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
    }

    .budget-selection-form {
        text-align: left;
        padding: 0 15px;
    }

    .budget-selection-form label {
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
    }
</style>


<div class="">
    <div class="content-area summary-view">
        <div class="table-toolbar">
            <div class="table-title">Reinsurance production by debit type</div>
            <div class="table-actions">
                <div class="dropdown">
                    <button class="action-btn dropdown-toggle btn-primary" type="button" id="exportDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-file"></i> Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown" style="width: 215px;">
                        <li><a class="dropdown-item"
                                href="{{ route('production-reports.export', ['tab' => 'debit-type', 'report' => 'business-performance', 'format' => 'excel'] + request()->except(['tab', 'report', 'format'])) }}">
                                1. Business
                                Perfomance Excel</a>
                        </li>
                        <li><a class="dropdown-item" id="printReportBtn"
                                href="{{ route('production-reports.export', ['tab' => 'debit-type', 'report' => 'financial-summary', 'format' => 'excel'] + request()->except(['tab', 'report', 'format'])) }}">2.
                                Financial
                                Summary Excel</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4" id="debitTypeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="business-tab" data-bs-toggle="tab" data-bs-target="#business"
                type="button" role="tab" aria-controls="business" aria-selected="true">Business
                Performance</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button"
                role="tab" aria-controls="financial" aria-selected="false">Financial Summary</button>
        </li>
    </ul>

    <div class="tab-content" id="debitTypeTabsContent">
        <div class="tab-pane fade show active" id="business" role="tabpanel" aria-labelledby="business-tab">
            <div class="table-title mb-3">Business Performance Summary</div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="businessPerformanceTable" width="100%">
                    <thead>
                        <tr>
                            <th>Index</th>
                            <th>Type Code</th>
                            <th>Type Name</th>
                            <th>Premium {{ request('currency', 'KES') }} (GWP)</th>
                            <th>Commission {{ request('currency', 'KES') }}</th>
                            <th>Premium Tax {{ request('currency', 'KES') }}</th>
                            <th>Rein. Tax {{ request('currency', 'KES') }}</th>
                            <th>W/Tax {{ request('currency', 'KES') }}</th>
                            <th>VAT {{ request('currency', 'KES') }}</th>
                            <th>Claims {{ request('currency', 'KES') }}</th>
                            <th>Revenue {{ request('currency', 'KES') }} (Income)</th>
                            <th>Commission %</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
            <div class="table-title mb-3">Financial Performance Summary</div>

            <table class="table table-striped table-hover" id="debitTypeFinancialTable" width="100%">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Type</th>
                        <th>Month</th>
                        <th>Budgeted</th>
                        <th>Actual</th>
                        <th>Variance</th>
                        <th>Achievement %</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
