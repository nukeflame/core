@extends('layouts.app', [
    'pageTitle' => 'Production Detailed Reports - ' . $company->company_name,
])

@include('reports._report_styles')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Production Detailed Reports</p>
            <span class="fs-semibold text-muted">Analyze production data for by reporting across different
                time periods
            </span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Production</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detailed Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="filter-bar">
                        <div class="filter-item">
                            <label>Date Range:</label>
                            <select>
                                <option>Year to Date</option>
                                <option>Last Quarter</option>
                                <option>Last 6 Months</option>
                                <option>Last 12 Months</option>
                                <option>Custom Range</option>
                            </select>
                        </div>

                        <div class="filter-item">
                            <label>Currency:</label>
                            <select>
                                <option>KES</option>
                                <option>USD</option>
                                <option>EUR</option>
                                <option>GBP</option>
                            </select>
                        </div>

                        <div class="filter-item">
                            <label>Business Class:</label>
                            <select>
                                <option>All Classes</option>
                                <option>Property</option>
                                <option>Casualty</option>
                                <option>Marine</option>
                                <option>Aviation</option>
                            </select>
                        </div>

                        <button class="btn btn-primary">Apply Filters</button>
                    </div>

                    <div class="metrics">
                        <div class="metric-card">
                            <div class="metric-title">Total Premium (KES)</div>
                            <div class="metric-value">4,322,845.63</div>
                            <div class="metric-trend trend-up">+12.4% from last period</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-title">Commission Earned</div>
                            <div class="metric-value">393,831.65</div>
                            <div class="metric-trend trend-up">+8.7% from last period</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-title">Claims Ratio</div>
                            <div class="metric-value">32.5%</div>
                            <div class="metric-trend trend-down">-4.2% from last period</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-title">Active Policies</div>
                            <div class="metric-value">187</div>
                            <div class="metric-trend trend-up">+15 from last period</div>
                        </div>
                    </div>

                    <p class="mb-2 fw-medium" style="color:#333335;">Summary Production By :-</p>

                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab', 'debit-type') == 'debit-type' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', ['tab' => 'debit-type']) }}">
                                Debit Type
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'cedant' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', ['tab' => 'cedant']) }}">
                                Cedant
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'ceding-broker' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', ['tab' => 'ceding-broker']) }}">
                                Ceding Broker
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'reinsurer' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', ['tab' => 'reinsurer']) }}">
                                Reinsurer
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'insured' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', ['tab' => 'insured']) }}">
                                Insured
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'class' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', ['tab' => 'class']) }}">
                                Class
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'class-group' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', ['tab' => 'class-group']) }}">
                                Class Group
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'risk-location' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', ['tab' => 'risk-location']) }}">
                                Risk Location
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'region' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', ['tab' => 'region']) }}">
                                Region
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane {{ request('tab', 'debit-type') == 'debit-type' ? 'd-block' : 'd-none' }}"
                            id="debit-type" role="tabpanel">
                            d
                        </div>
                        <div class="tab-pane {{ request('tab') == 'cedant' ? 'd-block' : 'd-none' }}" id="cedant"
                            role="tabpanel">
                            @include('reports.cover-reports.cover_placement')
                        </div>
                        <div class="tab-pane {{ request('tab') == 'ceding-broker' ? 'd-block' : 'd-none' }}"
                            id="ceding-broker" role="tabpanel">
                            <div class="alert alert-info" role="alert">
                                Covers ending content here
                            </div>
                        </div>
                        <div class="tab-pane {{ request('tab') == 'reinsurer' ? 'd-block' : 'd-none' }}" id="reinsurer"
                            role="tabpanel">
                            <div class="alert alert-info" role="alert">
                                Renewed covers content here!
                            </div>
                        </div>
                        <div class="tab-pane {{ request('tab') == 'insured' ? 'd-block' : 'd-none' }}" id="insured"
                            role="tabpanel">
                            <div class="alert alert-info" role="alert">
                                Renewed covers content here!
                            </div>
                        </div>
                        <div class="tab-pane {{ request('tab') == 'class' ? 'd-block' : 'd-none' }}" id="class"
                            role="tabpanel">
                            <div class="alert alert-info" role="alert">
                                Renewed covers content here!
                            </div>
                        </div>
                        <div class="tab-pane {{ request('tab') == 'class-group' ? 'd-block' : 'd-none' }}" id="class-group"
                            role="tabpanel">
                            <div class="alert alert-info" role="alert">
                                Renewed covers content here!
                            </div>
                        </div>
                        <div class="tab-pane {{ request('tab') == 'risk-location' ? 'd-block' : 'd-none' }}"
                            id="risk-location" role="tabpanel">
                            <div class="alert alert-info" role="alert">
                                Renewed covers content here!
                            </div>
                        </div>
                        <div class="tab-pane {{ request('tab') == 'region' ? 'd-block' : 'd-none' }}" id="region"
                            role="tabpanel">
                            <div class="alert alert-info" role="alert">
                                Renewed covers content here!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {});
    </script>
@endpush
