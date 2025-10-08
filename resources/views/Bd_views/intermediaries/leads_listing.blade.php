@extends('layouts.app')

@section('styles')
    @include('Bd_views.intermediaries.partials.styles')
@endsection

@section('content')
    <div class="container-fluid mt-3">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Facultative Pipeline</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/">Business Development</a></li>
                        <li class="breadcrumb-item"><a href="/">Pipeline</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Facultative</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value">{{ number_format($kpis['active_opportunities']['value']) }}</div>
                    <div class="kpi-label">Active Opportunities</div>

                    @if ((int) $kpis['active_opportunities']['value'] > 0)
                        @if ($kpis['active_opportunities']['trend'])
                            <div class="kpi-trend trend-{{ $kpis['active_opportunities']['trend']['direction'] }}">
                                <i
                                    class="bx bx-arrow-{{ $kpis['active_opportunities']['trend']['direction'] == 'up' ? 'up' : 'down' }}"></i>
                                {{ $kpis['active_opportunities']['trend']['direction'] == 'up' ? '+' : '-' }}{{ $kpis['active_opportunities']['trend']['percentage'] }}%
                                this month
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value">KES {{ number_format($kpis['pipeline_premium']['value'] / 1000000, 1) }}M</div>
                    <div class="kpi-label">Pipeline Premium</div>

                    @if ((int) $kpis['pipeline_premium']['value'] > 0)
                        @if ((int) $kpis['pipeline_premium']['value'] > 0)
                            @if ($kpis['pipeline_premium']['trend'])
                                <div class="kpi-trend trend-{{ $kpis['pipeline_premium']['trend']['direction'] }}">
                                    <i
                                        class="bx bx-arrow-{{ $kpis['pipeline_premium']['trend']['direction'] == 'up' ? 'up' : 'down' }}"></i>
                                    {{ $kpis['pipeline_premium']['trend']['direction'] == 'up' ? '+' : '-' }}{{ $kpis['pipeline_premium']['trend']['percentage'] }}%
                                    QoQ
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['conversion_rate']['value'] }}%</div>
                    <div class="kpi-label">Conversion Rate</div>

                    @if ((int) $kpis['conversion_rate']['value'] > 0)
                        @if ($kpis['conversion_rate']['trend'])
                            <div class="kpi-trend trend-{{ $kpis['conversion_rate']['trend']['direction'] }}">
                                <i
                                    class="bx bx-arrow-{{ $kpis['conversion_rate']['trend']['direction'] == 'up' ? 'up' : 'down' }}"></i>
                                {{ $kpis['conversion_rate']['trend']['direction'] == 'up' ? '+' : '' }}{{ $kpis['conversion_rate']['trend']['percentage'] }}%
                                improvement
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['critical_deadlines']['value'] }}</div>
                    <div class="kpi-label">Critical Deadlines</div>

                    @if ((int) $kpis['conversion_rate']['value'] > 0)
                        <div class="kpi-trend trend-down">
                            <i class="bx bx-clock text-warning"></i> Requires attention
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-primary btn-lg me-3" onclick="onboardProspect()">
                            <i class="bx bx-user-plus me-1" style="font-size: 20px; vertical-align: -3px;"></i>
                            Onboard New Prospect
                        </button>
                    </div>
                    <div>
                        {{-- <button type="button" class="btn btn-outline-info" onclick="showAnalytics()">
                            <i class="bx bx-chart-bar me-2"></i>Analytics
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="exportData()">
                            <i class="bx bx-download me-2"></i>Export
                        </button> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header p-0">
                        <div class="urgency-legend">
                            <div class="legend-title">
                                <i class="bx bx-info-circle me-2"></i>Urgency Classification
                            </div>
                            <div class="legend-items">
                                <div class="legend-item">
                                    <span class="color-indicator" style="background-color: #fef2f2;"></span>
                                    <span><strong>Critical:</strong> ≤ 7 days to effective date</span>
                                </div>
                                <div class="legend-item">
                                    <span class="color-indicator" style="background-color: #fffbeb;"></span>
                                    <span><strong>Urgent:</strong> 8-14 days to effective date</span>
                                </div>
                                <div class="legend-item">
                                    <span class="color-indicator" style="background-color: #eff6ff;"></span>
                                    <span><strong>Upcoming:</strong> 15-30 days to effective date</span>
                                </div>
                                <div class="legend-item">
                                    <span class="color-indicator" style="background-color: #f0fdf4;"></span>
                                    <span><strong>Normal:</strong> 31+ days to effective date</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="pipeline-table-container">
                            <div class="table-header">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="table-controls">
                                            <input type="search" class="form-inputs mb-0"
                                                placeholder="Search opportunities..." id="globalSearch">

                                            <select class="filter-select form-select" id="statusFilter"
                                                placeholder="Select status">
                                                <option value="">All Statuses</option>
                                                {{-- @foreach ($statuses as $key => $status)
                                                    <option value="{{ $key }}">{{ $status }}</option>
                                                @endforeach --}}
                                            </select>

                                            <select class="filter-select form-select" id="classGroupFilter"
                                                placeholder="Select class group">
                                                <option value="">All Class Group</option>
                                                {{-- @foreach ($classes as $key => $class)
                                                    <option value="{{ $key }}">{{ $class }}</option>
                                                @endforeach --}}
                                            </select>

                                            <select class="filter-select form-select" id="classFilter"
                                                placeholder="Select class">
                                                <option value="">All Class</option>
                                                {{-- @foreach ($classes as $key => $class)
                                                    <option value="{{ $key }}">{{ $class }}</option>
                                                @endforeach --}}
                                            </select>

                                            <select class="filter-select form-select" id="priorityFilter"
                                                placeholder="Select priority">
                                                <option value="">All Priorities</option>
                                                {{-- @foreach ($priorities as $key => $priority)
                                                    <option value="{{ $key }}">{{ $priority }}</option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- {{ html()->form('POST', route('cover.store'))->id('newCoverForm')->open() }}
                        @csrf
                        <input type="hidden" name="customer_id" id="customerId">
                        <input type="hidden" name="trans_type" id="transType">
                        <input type="hidden" name="prospect_id" id="prospectId">
                        {{ html()->form()->close() }} --}}

                        <table class="table text-nowrap table-striped table-hover" id="opportunities_table">
                            <thead>
                                <tr>
                                    <th>Opportunity ID</th>
                                    <th>Client Category</th>
                                    <th>Priority</th>
                                    <th>Cedant Name</th>
                                    <th>Class of Business</th>
                                    <th>Status</th>
                                    <th>Gross Premium</th>
                                    <th>Commission %</th>
                                    {{-- <th>Expected Premium</th> --}}
                                    <th>Effective Date</th>
                                    <th>Expiry Date</th>
                                    {{-- <th>Quote Deadline</th> --}}
                                    <th>Prospect Lead</th>
                                    {{-- <th>Territory</th> --}}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('Bd_views.intermediaries.partials.scripts')
@endpush
