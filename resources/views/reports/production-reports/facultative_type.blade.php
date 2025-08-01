@extends('layouts.app', [
    'pageTitle' => 'Production Summary Reports - ' . $company->company_name,
])

@section('styles')
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css"> --}}
    <style>
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }

        .financial-header {
            background-color: #0d6efd;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }

        .insurance-header {
            background-color: #198754;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }

        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box h5 {
            margin-top: 0;
            color: #0d6efd;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.04);
        }

        /* .nav-tabs .nav-link.active {
                        font-weight: bold;
                        border-bottom: 3px solid #0d6efd;
                    } */

        .summary-item {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
        }

        .summary-item .title {
            font-weight: bold;
            color: #6c757d;
        }

        .summary-item .value {
            font-size: 1.2rem;
            font-weight: bold;
            color: #212529;
        }

        .dataTables_info {
            padding-top: 0.85em;
            white-space: nowrap;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .currency {
            text-align: right;
        }

        .export-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 4px;
        }

        .export-section h5 {
            margin-top: 0;
        }

        .table-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        #analysis-content {
            display: none;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-top: 20px;
        }

        .pagination {
            justify-content: center;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Insurance Data Dashboard</h3>
                        <div>
                            <button class="btn btn-outline-primary" id="showAnalysisBtn">Show Analysis</button>
                            <div class="btn-group ms-2">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Export Options
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                    <li><a class="dropdown-item" href="#" id="exportCurrentTable">Export Current
                                            Table</a></li>
                                    <li><a class="dropdown-item" href="#" id="exportAnalysis">Export Analysis</a></li>
                                    <li><a class="dropdown-item" href="#" id="exportAll">Export All Data</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4" id="insuranceTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="current-tab" data-bs-toggle="tab"
                                    data-bs-target="#current" type="button" role="tab" aria-controls="current"
                                    aria-selected="true">Insurance Data</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial"
                                    type="button" role="tab" aria-controls="financial" aria-selected="false">Financial
                                    Summary</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard"
                                    type="button" role="tab" aria-controls="dashboard"
                                    aria-selected="false">Dashboard</button>
                            </li>
                        </ul>

                        <!-- Analysis section that will be toggled -->
                        <div id="analysis-content" class="mb-4">
                            <h4>Data Analysis</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-box">
                                        <h5>Business Categories Analysis</h5>
                                        <p>The table shows premium, commission, and various tax amounts categorized by
                                            business type:</p>
                                        <ul>
                                            <li><strong>Facultative (Quotations & Offers):</strong> $1,579,118.20 in GWP
                                            </li>
                                            <li><strong>Special Lines:</strong> $950,543.38 in GWP</li>
                                            <li><strong>External Markets:</strong> $450,000.00 in GWP</li>
                                            <li><strong>MinDeps:</strong> No financial figures reported</li>
                                            <li><strong>TreatyProp (Proportional):</strong> $1,343,184.05 in GWP</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <h5>Financial Metrics</h5>
                                        <ul>
                                            <li><strong>Total GWP (Gross Written Premium):</strong> $4,322,845.63</li>
                                            <li><strong>Total Commission:</strong> $393,831.65</li>
                                            <li><strong>W/Tax:</strong> Only $11,573.44 in total, primarily from Special
                                                Lines and External Markets</li>
                                            <li><strong>UE KES (Income):</strong> $3,917,440.54 total</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <h5>Performance Insights</h5>
                                        <ul>
                                            <li>Facultative business represents the largest segment of GWP at approximately
                                                36.5% of total</li>
                                            <li>TreatyProp is the second largest contributor at 31% of total GWP</li>
                                            <li>Commission rates vary across business types:
                                                <ul>
                                                    <li>Facultative: 7.5% commission rate</li>
                                                    <li>Special Lines: 6.3% commission rate</li>
                                                    <li>External Markets: 12.5% commission rate</li>
                                                    <li>TreatyProp: 11.8% commission rate</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="info-box">
                                        <h5>Additional Observations</h5>
                                        <ul>
                                            <li>No PRM Tax, REIN Tax, VAT, or Claims KES are reported for any business line
                                            </li>
                                            <li>The table appears to be paginated (page 1 of at least 3 pages)</li>
                                            <li>MinDeps category shows no financial activity in the current period</li>
                                        </ul>
                                        <p>This appears to be a summary of insurance/reinsurance business performance by
                                            category, focusing on premiums, commissions, taxes, and income metrics.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content" id="insuranceTabsContent">
                            <div class="tab-pane fade show active" id="current" role="tabpanel"
                                aria-labelledby="current-tab">
                                <div class="table-title">Insurance/Reinsurance Business Performance</div>
                                <table class="table table-striped table-bordered table-hover" id="insuranceTable"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>Type Code</th>
                                            <th>Type Name</th>
                                            <th>Premium KES (GWP)</th>
                                            <th>Commission KES</th>
                                            <th>PRM Tax KES</th>
                                            <th>REIN Tax KES</th>
                                            <th>W/Tax KES</th>
                                            <th>VAT KES</th>
                                            <th>Claims KES</th>
                                            <th>UE KES (Income)</th>
                                            <th>Comm %</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="total-row">
                                            <th colspan="2">TOTAL</th>
                                            <th>4,322,845.63</th>
                                            <th>393,831.65</th>
                                            <th>-</th>
                                            <th>-</th>
                                            <th>11,573.44</th>
                                            <th>-</th>
                                            <th>-</th>
                                            <th>3,917,440.54</th>
                                            <th>9.11%</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <nav aria-label="Page navigation example">
                                    <ul class="pagination mt-4">
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                                    </ul>
                                </nav>
                            </div>

                            <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                                <div class="table-title">Financial Performance Summary</div>
                                <table class="table table-striped table-bordered table-hover" id="financialTable"
                                    width="100%">
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
                                </table>
                            </div>

                            <div class="tab-pane fade" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="summary-item">
                                            <div class="title">Total GWP</div>
                                            <div class="value">$4,322,845.63</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="summary-item">
                                            <div class="title">Total Commission</div>
                                            <div class="value">$393,831.65</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="summary-item">
                                            <div class="title">Total Income</div>
                                            <div class="value">$3,917,440.54</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                Premium Distribution by Type
                                            </div>
                                            <div class="card-body">
                                                <canvas id="premiumDistributionChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                Commission Percentage by Type
                                            </div>
                                            <div class="card-body">
                                                <canvas id="commissionRateChart"></canvas>
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
@endsection
