<div class="">
    <div class="content-area summary-view">
        <div class="table-toolbar">
            <div class="table-title">Reinsurance production by cedant</div>
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

    <div class="mt-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="cedantTable">
                        <thead>
                            <tr class="table-header">
                                <th>#</th>
                                <th>CEDANT NAME</th>
                                <th>PREMIUM KES</th>
                                <th>REVENUE KES</th>
                                <th>% INCOME GENERATED</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
