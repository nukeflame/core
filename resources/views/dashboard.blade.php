@extends('layouts.app', [
    'pageTitle' => 'Dashboard - ' . $company->company_name,
])

@section('content')
    <style>
        .kpi-card {
            transition: all 0.3s;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            position: relative;
            height: 350px;
        }

        .tab-content {
            padding: 20px 0;
        }

        .stats-label {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .stats-value {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .progress {
            height: 0.6rem;
        }

        .category-indicator {
            width: 12px;
            height: 12px;
            display: inline-block;
            margin-right: 5px;
            border-radius: 2px;
        }
    </style>

    <div class="row">
        <div class="col-xl-9">
            <div
                class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb bi-dashboard">
                <div class="dashboard">
                    <div class="header">
                        <div>
                            <p class="fw-semibold fs-18 mb-0">Welcome back, {{ $firstName }} | {{ date('F j, Y') }}</p>
                            <span class="fs-semibold text-muted">Easily track key metrics and monitor performance across your
                                entire reinsurance portfolio.</span>
                        </div>
                        <div class="btn-list mt-md-0 mt-2"></div>
                    </div>
                </div>
            </div>

            <div class="row info-cards">
                <div class="col-12">
                    <div class="row">
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-top">
                                        <div class="me-3">
                                            <span class="avatar avatar-md p-2 bg-primary">
                                                <svg class="svg-white" xmlns="http://www.w3.org/2000/svg"
                                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                                    width="24px" fill="#000000">
                                                    <g>
                                                        <rect fill="none" height="24" width="24" />
                                                        <g>
                                                            <path
                                                                d="M19,5v14H5V5H19 M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3L19,3z" />
                                                        </g>
                                                        <path d="M14,17H7v-2h7V17z M17,13H7v-2h10V13z M17,9H7V7h10V9z" />
                                                    </g>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                                <h5 class="fw-semibold mb-0 lh-1">{{ $totalCovers['amount'] }}</h5>
                                            </div>
                                            <p class="mb-0 fs-12 op-7 text-muted fw-semibold">
                                                {{ $totalCovers['title'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-top">
                                        <div class="me-3">
                                            <span class="avatar avatar-md p-2 bg-pink">
                                                <svg class="svg-white" xmlns="http://www.w3.org/2000/svg"
                                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                                    width="24px" fill="#000000">
                                                    <g>
                                                        <rect fill="none" height="24" width="24" />
                                                        <g>
                                                            <path
                                                                d="M19,5v14H5V5H19 M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3L19,3z" />
                                                        </g>
                                                        <path d="M14,17H7v-2h7V17z M17,13H7v-2h10V13z M17,9H7V7h10V9z" />
                                                    </g>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                                <h5 class="fw-semibold mb-0 lh-1">{{ $totalDebitedCovers['amount'] }}</h5>

                                            </div>
                                            <p class="mb-0 fs-12 op-7 text-muted fw-semibold">
                                                {{ $totalDebitedCovers['title'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-top">
                                        <div class="me-3">
                                            <span class="avatar avatar-md p-2 bg-purple">
                                                <svg class="svg-white" xmlns="http://www.w3.org/2000/svg"
                                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                                    width="24px" fill="#000000">
                                                    <g>
                                                        <rect fill="none" height="24" width="24" />
                                                        <g>
                                                            <path
                                                                d="M19,5v14H5V5H19 M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3L19,3z" />
                                                        </g>
                                                        <path d="M14,17H7v-2h7V17z M17,13H7v-2h10V13z M17,9H7V7h10V9z" />
                                                    </g>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                                <h5 class="fw-semibold mb-0 lh-1">{{ $totalTPRCovers['amount'] }}</h5>
                                            </div>
                                            <p class="mb-0 fs-12 op-7 text-muted fw-semibold">
                                                Fac. Proportional</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-top">
                                        <div class="me-3">
                                            <span class="avatar avatar-md p-2 bg-secondary">
                                                <svg class="svg-white" xmlns="http://www.w3.org/2000/svg"
                                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                                    width="24px" fill="#000000">
                                                    <g>
                                                        <rect fill="none" height="24" width="24" />
                                                        <g>
                                                            <path
                                                                d="M19,5v14H5V5H19 M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3L19,3z" />
                                                        </g>
                                                        <path d="M14,17H7v-2h7V17z M17,13H7v-2h10V13z M17,9H7V7h10V9z" />
                                                    </g>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                                <h5 class="fw-semibold mb-0 lh-1">{{ $totalFacCovers['amount'] }}</h5>
                                            </div>
                                            <p class="mb-0 fs-12 op-7 text-muted fw-semibold">
                                                {{ $totalFacCovers['title'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                            <div class="col">
                                <div class="card custom-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-top">
                                            <div class="me-3">
                                                <span class="avatar avatar-md p-2 bg-warning">
                                                    <svg class="svg-white" xmlns="http://www.w3.org/2000/svg"
                                                        enable-background="new 0 0 24 24" height="24px"
                                                        viewBox="0 0 24 24" width="24px" fill="#000000">
                                                        <g>
                                                            <rect fill="none" height="24" width="24" />
                                                            <g>
                                                                <path
                                                                    d="M19,5v14H5V5H19 M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3L19,3z" />
                                                            </g>
                                                            <path
                                                                d="M14,17H7v-2h7V17z M17,13H7v-2h10V13z M17,9H7V7h10V9z" />
                                                        </g>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="d-flex mb-1 align-items-top justify-content-between">
                                                    <h5 class="fw-semibold mb-0 lh-1">{{ $totalTPRCovers['amount'] }}</h5>


                                                </div>
                                                <p class="mb-0 fs-12 op-7 text-muted fw-semibold">
                                                    {{ $totalTPRCovers['title'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-top">
                                        <div class="me-3">
                                            <span class="avatar avatar-md p-2 bg-success">
                                                <svg class="svg-white" xmlns="http://www.w3.org/2000/svg"
                                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                                    width="24px" fill="#000000">
                                                    <g>
                                                        <rect fill="none" height="24" width="24" />
                                                        <g>
                                                            <path
                                                                d="M19,5v14H5V5H19 M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3L19,3z" />
                                                        </g>
                                                        <path d="M14,17H7v-2h7V17z M17,13H7v-2h10V13z M17,9H7V7h10V9z" />
                                                    </g>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                                <h5 class="fw-semibold mb-0 lh-1">{{ $totalTNPCovers['amount'] }}</h5>
                                            </div>
                                            <p class="mb-0 fs-12 op-7 text-muted fw-semibold">
                                                {{ $totalTNPCovers['title'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-semibold mb-3">Total New Business GWP</h6>
                                            <span class="fs-25 fw-semibold"><span class="fs-13">KES</span>
                                                {{ number_format(0, 0) }}</span>
                                            {{-- <span class="d-block text-success fs-12">+12% from target<i
                                                    class="ti ti-trending-up ms-1"></i></span> --}}
                                        </div>
                                        <div id="analytics-users"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-semibold mb-3">Total New Business Income</h6>
                                            <span class="fs-25 fw-semibold"><span class="fs-13">KES</span>
                                                {{ number_format(0, 0) }}</span>
                                            {{-- <span class="d-block text-success fs-12">+8% from target<i
                                                    class="ti ti-trending-down ms-1 d-inline-flex"></i></span> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-semibold mb-3">Total Renewals GWP</h6>
                                            <span class="fs-25 fw-semibold"><span class="fs-13">KES</span>
                                                {{ number_format(0, 0) }}</span>
                                            {{-- <span class="d-block text-danger fs-12">-2% from target<i
                                                    class="ti ti-trending-down ms-1 d-inline-flex"></i></span> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-semibold mb-3">Total Renewals Income</h6>
                                            <span class="fs-25 fw-semibold"><span class="fs-13">KES</span>
                                                {{ number_format(0, 0) }}</span>
                                            {{-- <span class="d-block text-danger fs-12">-5% from target<i
                                                    class="ti ti-trending-down ms-1 d-inline-flex"></i></span> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-7 col-lg-7 col-md-12 col-sm-12 appointment-col">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                My Appointments
                            </div>
                            <div class="w-22 d-flex justify-content-between">
                                <button type="button" class="btn btn-sm btn-dark fs-18 me-2" data-bs-toggle="modal"
                                    style="padding: 5px 23px;" data-bs-target="#appointmentModal"
                                    id="appointmentModalBtn">
                                    <i class="bx bx-plus"></i> New Appointment
                                </button>
                                <a href="javascript:void(0);" data-bs-toggle="collapse"
                                    data-bs-target="#collapseMyAppointments" aria-expanded="false"
                                    aria-controls="collapseMyAppointments" data-bs-toggle="tooltip" title="Collapse">
                                    <i class="ri-arrow-down-s-line fs-18 collapse-open"></i>
                                    <i class="ri-arrow-up-s-line collapse-close fs-18"></i>
                                </a>
                            </div>
                        </div>
                        <div class="collapse show" id="collapseMyAppointments">
                            <div class="card-body">
                                <div id="appointments-container">
                                    <div class="text-center py-3">
                                        <p class="text-muted mb-0 fs-13">No appointments scheduled for today</p>
                                    </div>
                                    <div class="table-responsive d-none">
                                        <table class="table table-striped" id="appointments-table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Client</th>
                                                    <th>Date & Time</th>
                                                    <th>Purpose</th>
                                                    <th>Actions</th>
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
                <div class="col-xl-5 col-lg-5 col-md-12 col-sm-12 todo-col">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between" style="padding-top: 18px;padding-bottom: 18px;">
                            <div class="card-title">
                                Todo List
                            </div>
                            <div class="w-22 d-flex justify-content-between">
                                <a href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseAddTask"
                                    aria-expanded="false" aria-controls="collapseAddTask" data-bs-toggle="tooltip"
                                    title="Collapse">
                                    <i class="ri-arrow-down-s-line fs-18 collapse-open"></i>
                                    <i class="ri-arrow-up-s-line collapse-close fs-18"></i>
                                </a>
                            </div>
                        </div>
                        <div class="collapse show" id="collapseAddTask">
                            <div class="card-body">
                                <div id="todo-container">
                                    <ul class="list-group mb-3" id="todo-list"></ul>
                                    <div id="todo-form-inline" class="input-group">
                                        <input type="text" id="quick-todo-input" class="form-control color-blk"
                                            placeholder="Add a quick task...">
                                        <button class="btn btn-outline-dark" type="button"
                                            id="quick-todo-btn">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card performance-wrapper">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="dashboardTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="performance-tab" data-bs-toggle="tab"
                                data-bs-target="#performance" type="button" role="tab" aria-controls="performance"
                                aria-selected="true">Performance Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="portfolio-tab" data-bs-toggle="tab" data-bs-target="#portfolio"
                                type="button" role="tab" aria-controls="portfolio" aria-selected="false">Portfolio
                                Breakdown</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="budget-tab" data-bs-toggle="tab" data-bs-target="#budget"
                                type="button" role="tab" aria-controls="budget" aria-selected="false">Budget vs
                                Actual</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="dashboardTabsContent">
                        <!-- Performance Tab -->
                        <div class="tab-pane fade show active" id="performance" role="tabpanel"
                            aria-labelledby="performance-tab">
                            <div class="row">
                                <div class="col-lg-12 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Quarterly Performance (2024)</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-container">
                                                <canvas id="quarterlyChart"
                                                    style="width: 100%; height: 300px; min-width: 0px;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">Business Mix</h5>
                                            <small class="text-muted">2025 YTD</small>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-container mb-3">
                                                <canvas id="businessMixChart"
                                                    style="width: 100%; height: 300px; min-width: 0px;"></canvas>
                                            </div>
                                            <div class="row text-center mt-3">
                                                <div class="col-6">
                                                    <div class="stats-label">New vs Renewal Ratio</div>
                                                    <div class="stats-value">0% : 0%</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="stats-label">Income to GWP Ratio</div>
                                                    <div class="stats-value">0%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Portfolio Tab -->
                        <div class="tab-pane fade" id="portfolio" role="tabpanel" aria-labelledby="portfolio-tab">
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">New Business GWP by Category</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-container">
                                                <canvas id="newBusinessChart"
                                                    style="width: 100%; height: 300px; min-width: 0px;"></canvas>
                                            </div>
                                            <div class="mt-4">
                                                <div class="mb-2 text-muted small">Category Breakdown:</div>
                                                <div class="row">
                                                    <div class="col-6 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <span class="category-indicator bg-primary"></span>
                                                            <span>Facultative: KES 0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <span class="category-indicator bg-success"></span>
                                                            <span>Special Line: KES 0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <span class="category-indicator bg-warning"></span>
                                                            <span>Treaty: KES 0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Renewals GWP by Category</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-container">
                                                <canvas id="renewalsChart"
                                                    style="width: 100%; height: 300px; min-width: 0px;"></canvas>
                                            </div>
                                            <div class="mt-4">
                                                <div class="mb-2 text-muted small">Category Breakdown:</div>
                                                <div class="row">
                                                    <div class="col-6 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <span class="category-indicator bg-primary"></span>
                                                            <span>Facultative: KES 0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <span class="category-indicator bg-warning"></span>
                                                            <span>Treaty: KES 0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Budget Tab -->
                        <div class="tab-pane fade" id="budget" role="tabpanel" aria-labelledby="budget-tab">
                            <div class="card">
                                <div class="card-header mb-0"
                                    style="height: 100%; align-items: center; justify-content: center;">
                                    <h5 class="card-title mb-2">2025 Budget vs Actual</h5>
                                    <span class="badge bg-warning d-flex align-items-center px-3 py-2">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        0% of Budget Target
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container mb-4">
                                        <canvas id="budgetChart"
                                            style="width: 100%; height: 300px; min-width: 0px;"></canvas>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Facultative</h6>
                                                <small class="text-muted">0% of target</small>
                                            </div>
                                            <div class="progress progress-xm mb-2 progress-animate custom-progress-4"
                                                role="progressbar" aria-valuenow="10" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="progress-bar bg-primary-gradient" style="width: 0%"></div>
                                                {{-- <div class="progress-bar-label">0%</div> --}}
                                            </div>
                                            <div class="d-flex justify-content-between small">
                                                <span>Actual: KES 0</span>
                                                <span>Budget: KES 0</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Treaty</h6>
                                                <small class="text-muted">0% of target</small>
                                            </div>
                                            <div class="progress progress-xm mb-2 progress-animate custom-progress-4"
                                                role="progressbar" aria-valuenow="10" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="progress-bar bg-info-gradient" style="width: 0%"></div>
                                                {{-- <div class="progress-bar-label">0%</div> --}}
                                            </div>
                                            <div class="d-flex justify-content-between small">
                                                <span>Actual: KES 0</span>
                                                <span>Budget: KES 0</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Special Lines</h6>
                                                <small class="text-muted">0% of target</small>
                                            </div>
                                            <div class="progress progress-xm mb-2 progress-animate custom-progress-4"
                                                role="progressbar" aria-valuenow="10" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="progress-bar bg-warning-gradient" style="width: 0%"></div>
                                                {{-- <div class="progress-bar-label">0%</div> --}}
                                            </div>
                                            <div class="d-flex justify-content-between small">
                                                <span>Actual: KES 0</span>
                                                <span>Budget: KES 0</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">External Market</h6>
                                                <small class="text-muted">0% of target</small>
                                            </div>
                                            <div class="progress progress-xm mb-2 progress-animate custom-progress-4"
                                                role="progressbar" aria-valuenow="10" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="progress-bar bg-success-gradient" style="width: 0%"></div>
                                                {{-- <div class="progress-bar-label">0%</div> --}}
                                            </div>
                                            <div class="d-flex justify-content-between small">
                                                <span>Actual: KES 0</span>
                                                <span>Budget: KES 0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row d-none">
                                        <div class="col-md-6 mb-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">Facultative</h6>
                                                        <small class="text-muted">0% of target</small>
                                                    </div>
                                                    <div class="progress mb-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 0%"
                                                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between small">
                                                        <span>Actual: KES 0</span>
                                                        <span>Budget: KES 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">Treaty</h6>
                                                        <small class="text-muted">0% of target</small>
                                                    </div>
                                                    <div class="progress mb-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 1%"
                                                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between small">
                                                        <span>Actual: KES 0</span>
                                                        <span>Budget: KES 0</span>
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

        <div class="col-xl-3" style="display: flex; flex-direction: column;">
            <div class="card shadow-sm mt-4" style="flex-grow: 1; margin-bottom: 0px;">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fs-16">Bulletin Board</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="bulletinFilter" data-bs-toggle="dropdown">
                                Filter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item filter-option" href="#" data-type="all">All</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-type="system">System</a>
                                </li>
                                <li><a class="dropdown-item filter-option" href="#" data-type="alert">Alert</a>
                                </li>
                                <li><a class="dropdown-item filter-option" href="#"
                                        data-type="announcement">Announcement</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body bulletin-wrapper" id="bulletinContainer">
                    <div class="text-center" id="bulletinLoader">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading notices...</p>
                    </div>
                </div>
            </div>
        </div>


        {{-- <div class="col-xl-3" style="display: flex; flex-direction: column;">
            <div class="card shadow-sm  mt-4" style="flex-grow: 1; margin-bottom: 0px;">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fs-16">Bullettin Board</h5>
                    </div>
                </div>
                <div class="card-body bulletin-wrapper">
                    {{-- <div class="bulletin-item">
                        <div class="bulletin-title">
                            System Updates
                        </div>
                        <p class="bulletin-content">
                            New treaty pricing module will be available starting
                            April 1, 2025. Training sessions scheduled for March
                            25-27.
                        </p>
                    </div>

                    <div class="bulletin-item">
                        <div class="bulletin-title">
                            Market Alerts
                        </div>
                        <p class="bulletin-content">
                            Recent catastrophic flooding in Southeast Asia may
                            impact regional property treaties. Risk assessment
                            report available in the Resources section.
                        </p>
                    </div> --
                    <div class="alert alert-info">None Found</div>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Appointment Modal -->
    <div class="modal effect-fall md-wrapper" id="appointmentModal" tabindex="-1"
        aria-labelledby="appointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="appointmentModalLabel"><i class="bx bx-calendar"></i>
                        Schedule an Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div id="stepTitle" style="padding: 1rem 0px 0px 8px;">
                    <p class="mb-0 ms-2 d-md-block fs-15">Complete the steps below to book your session</p>
                </div>

                <div id="stepWrapper" class="px-3 pt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted step-indicator">Step <span id="currentStep">1</span> of 4</span>
                        <span class="small text-muted">Select Date & Time</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div id="progressBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="modal-body p-4">
                    <form id="appointmentForm">
                        @csrf
                        <div id="step1" class="step-content">
                            <div class="mb-3">
                                <label for="appointment-date" class="form-label d-flex align-items-center">
                                    <i class="bi bi-calendar me-2"></i>
                                    Select Date
                                </label>
                                <input type="date" class="form-control color-blk" id="appointment-date"
                                    name="selected_date" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-flex align-items-center" for="appointment-time">
                                    <i class="bi bi-clock me-2"></i>
                                    Select Time
                                </label>
                                <input type="time" class="form-control color-blk" id="appointment-time"
                                    name="selected_time" required>
                            </div>
                        </div>
                        <div id="step2" class="step-content d-none">
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control color-blk" id="fullName" name="name"
                                    placeholder="Enter your full name" required>
                            </div>

                            <div class="mb-3">
                                <label for="emailAddress" class="form-label">Email Address</label>
                                <input type="email" class="form-control color-blk" id="emailAddress" name="email"
                                    placeholder="" required>
                            </div>

                            <div class="mb-3">
                                <label for="appointmentPurpose" class="form-label">Appointment Purpose</label>
                                <textarea class="form-control color-blk" id="appointmentPurpose" name="purpose" rows="6"
                                    placeholder="Briefly describe the reason for your appointment"></textarea>
                            </div>
                        </div>
                        <div id="step3" class="step-content d-none">
                            <h5 class="mb-3">Appointment Summary</h5>
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Date:</span>
                                    <span id="summaryDate" class="fw-medium"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Time:</span>
                                    <span id="summaryTime" class="fw-medium"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Name:</span>
                                    <span id="summaryName" class="fw-medium"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Email:</span>
                                    <span id="summaryEmail" class="fw-medium"></span>
                                </div>
                                <div>
                                    <span class="text-muted d-block mb-1">Purpose:</span>
                                    <p id="summaryPurpose" class="mb-0 small"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Confirmation -->
                        <div id="step4" class="step-content d-none text-center py-3">
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="mb-2">Appointment Confirmed!</h4>
                            <p class="text-muted mb-4">
                                We've sent a confirmation email to <span id="confirmationEmail"></span> with all the
                                details.
                            </p>
                            <div class="bg-light p-3 rounded d-inline-block text-start">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar me-2 text-primary"></i>
                                    <span id="confirmationDateTime" class="fw-medium"></span>
                                </div>
                                <p class="small text-muted mb-0">
                                    {{-- We look forward to meeting with you. --}}
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light" id="modalFooter">
                    <button type="button" class="btn btn-link text-muted d-none" id="prevButton">Back</button>
                    <button type="button" class="btn btn-dark px-3" id="nextButton">Continue <i
                            class="bx bx-right-arrow"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let currentStep = 1;
            const totalSteps = 4;
            let syncInProgress = false;
            let syncNeeded = false;
            let syncTimer = null;

            loadFromBackend()
            loadBulletinNotices()

            $('.filter-option').on('click', function(e) {
                e.preventDefault();
                const type = $(this).data('type');
                loadBulletinNotices(type);
            });

            // Auto-refresh every 5 minutes
            setInterval(loadBulletinNotices, 300000);

            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 12,
                lengthMenu: [12, 24, 50, 100],
                order: [
                    [0, 'asc']
                ],
                ajax: '{{ route('dashboard.appointments.data') }}',
                columns: [{
                        data: 'id',
                        className: "highlight-idx"
                    },
                    {
                        data: 'client',
                        className: "highlight-idx"
                    },
                    {
                        data: 'date_time',
                        className: "highlight-view-more"
                    },
                    {
                        data: 'purpose',
                        className: "highlight-view-more"
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false,
                        className: "highlight-idx"
                    },
                ]
            });

            function addTodoItem() {
                const todoText = $('#quick-todo-input').val().trim();
                if (todoText === '') {
                    return;
                }

                addTodoToDOM({
                    id: 'temp_' + Date.now(),
                    text: todoText,
                    completed: false
                });

                $('#quick-todo-input').val('');

                saveTodos();
            }

            $('#quick-todo-btn').on('click', function() {
                addTodoItem();
            });

            $('#quick-todo-input').on('keypress', function(e) {
                if (e.which === 13) {
                    addTodoItem();
                    e.preventDefault();
                }
            });

            $('#todo-list').on('click', '.todo-check', function() {
                const li = $(this).closest('li');
                const todoId = li.data('id');
                const completed = $(this).is(':checked');

                li.toggleClass('text-muted');
                li.find('label').toggleClass('text-decoration-line-through');

                if (!String(todoId).startsWith('temp_')) {
                    $.ajax({
                        url: `/todos/${todoId}`,
                        method: 'PUT',
                        data: {
                            completed: completed,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log(response)
                        },
                        error: function(error) {
                            console.error('Error updating todo status', error);
                        }
                    });
                }
            });

            $('#todo-list').on('click', '.delete-todo', function() {
                $(this).closest('li').fadeOut(300, function() {
                    $(this).remove();
                    saveTodos();
                });
            });

            function addTodoToDOM(todo) {
                const todoItem = `
                    <li class="list-group-item d-flex justify-content-between align-items-center ${todo.completed ? 'text-muted' : ''}" data-id="${todo.id}">
                        <div class="form-check form-check-md d-flex align-items-center">
                            <input class="form-check-input todo-check" type="checkbox" value="" id="checkebox-md-${todo.id}" ${todo.completed ? 'checked' : ''}>
                            <label class="form-check-label ${todo.completed ? 'text-decoration-line-through' : ''}" for="checkebox-md-${todo.id}">
                                 ${todo.text}
                            </label>
                        </div>
                        <button class="btn btn-sm btn-outline-danger delete-todo">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </li>
                `;

                $('#todo-list').append(todoItem);
            }

            function saveTodos() {
                const todos = [];
                $('#todo-list li').each(function() {
                    todos.push({
                        text: $(this).find('label').text().trim(),
                        completed: $(this).find('.todo-check').is(':checked')
                    });
                });
                console.log(todos)

                $.ajax({
                    url: '{{ route('todos.save') }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        todos: todos
                    },
                    // headers: {
                    //     'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                    //     'Accept': 'application/json'
                    // },
                    success: function(response) {
                        console.log('Server response:', response);


                        // if (response.success) {
                        //     if (typeof toastr !== 'undefined') {
                        //         toastr.success('Todo list updated successfully');
                        //     } else {
                        //         alert('Todo list updated successfully');
                        //     }
                        // } else {
                        //     const errorMessage = response.message || 'Failed to update todo list';
                        //     if (typeof toastr !== 'undefined') {
                        //         toastr.error(errorMessage);
                        //     } else {
                        //         alert('Error: ' + errorMessage);
                        //     }
                        //     console.error('Server returned error:', response);
                        // }
                    },
                    error: function(xhr, status, errorThrown) {
                        console.log(xhr)
                        // const responseData = xhr.responseJSON || {};
                        // const errorMessage = responseData.message || errorThrown || 'Unknown error';

                        // console.error('AJAX Error:', {
                        //     status: xhr.status,
                        //     statusText: xhr.statusText,
                        //     responseText: xhr.responseText,
                        //     errorThrown: errorThrown
                        // });

                        // if (typeof toastr !== 'undefined') {
                        //     toastr.error('Error saving todo list: ' + errorMessage);
                        // } else {
                        //     alert('Error saving todo list: ' + errorMessage);
                        // }
                    },
                    complete: function() {


                    }
                });
                // $.ajax({
                //     url: '{{ route('todos.save') }}',
                //     method: 'POST',
                //     data: {
                //         todos: todos,
                //         // _token: '{{ csrf_token() }}'
                //     },
                //     headers: {
                //         'X-CSRF-TOKEN': $('input[name="_token"]').val()
                //     },
                //     success: function(response) {
                //         console.log(response)
                //         if (response.success) {
                //             // Update the UI to reflect successful save
                //             toastr.success('Todo list updated successfully');
                //         } else {
                //             toastr.error('Failed to update todo list');
                //         }
                //     },
                //     error: function(xhr, status, error) {
                //         toastr.error('Error saving todo list: ' + (xhr.responseJSON?.message || error));
                //         console.error('Error syncing with backend:', xhr.responseJSON || error);
                //     }
                // });
            }

            function loadFromBackend() {
                $('#todo-list').empty();
                const todoList = [];
                todoList.forEach(function(todo) {
                    console.log(`todo`, todo)
                })
            }

            function loadBulletinNotices(type = 'all') {
                const params = {
                    active_only: true,
                    effective_only: true,
                    per_page: 20
                };

                if (type !== 'all') {
                    params.type = type;
                }

                $.ajax({
                    url: '{{ route('admin-notices.getNotices') }}',
                    method: 'GET',
                    data: params,
                    success: function(response) {
                        // console.log(response)
                        if (response.success && response.data.length > 0) {
                            renderBulletinNotices(response.data);
                        } else {
                            showNoNotices();
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error(error);
                    }
                });
            }

            function showNoNotices() {
                $('#bulletinContainer').html('<div class="alert alert-info">No notices found</div>');
            }


            function renderBulletinNotices(notices) {
                let html = '';

                notices.forEach(function(notice) {
                    const priorityClass = getPriorityClass(notice.priority);
                    const typeClass = getTypeClass(notice.type);

                    html += `
                            <div class="bulletin-item mb-3" data-notice-id="${notice.id}">
                                <div class="bulletin-title d-flex justify-content-between align-items-center">
                                    <strong>${escapeHtml(notice.notice)}</strong>
                                    <div class="bulletin-badges">
                                        ${notice.priority ? `<span class="badge badge-${priorityClass}">${notice.priority.charAt(0).toUpperCase() + notice.priority.slice(1)}</span>` : ''}
                                        ${notice.type ? `<span class="badge badge-${typeClass}">${notice.type.charAt(0).toUpperCase() + notice.type.slice(1)}</span>` : ''}
                                    </div>
                                </div>
                                <p class="bulletin-content mb-2">
                                    ${escapeHtml(notice.description)}
                                </p>
                                <div class="bulletin-meta">
                                    <small class="text-muted">
                                        ${notice.effective_from ? `From: ${formatDate(notice.effective_from)}` : ''}
                                        ${notice.expired_at ? ` • Expires: ${formatDate(notice.expired_at)}` : ''}
                                        ${notice.issued_by ? ` • By: ${escapeHtml(notice.issued_by)}` : ''}
                                    </small>
                                </div>
                            </div>
                    `;
                });

                $('#bulletinContainer').html(html);
            }

            function getPriorityClass(priority) {
                switch (priority) {
                    case 'high':
                        return 'danger';
                    case 'medium':
                        return 'warning';
                    case 'low':
                        return 'info';
                    default:
                        return 'secondary';
                }
            }

            function getTypeClass(type) {
                switch (type) {
                    case 'system':
                        return 'primary';
                    case 'alert':
                        return 'danger';
                    case 'announcement':
                        return 'success';
                    default:
                        return 'secondary';
                }
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            //    /================
            const colors = {
                primary: '#0d6efd',
                success: '#198754',
                warning: '#ffc107',
                danger: '#dc3545',
                info: '#0dcaf0',
                light: '#f8f9fa',
                dark: '#212529'
            };

            function formatNumber(value) {
                if (value >= 1000000000) {
                    return (value / 1000000000).toFixed(1) + 'B';
                } else if (value >= 1000000) {
                    return (value / 1000000).toFixed(1) + 'M';
                } else if (value >= 1000) {
                    return (value / 1000).toFixed(1) + 'K';
                }
                return value;
            }


            const quarterlyData = {
                labels: ['Q1 2024', 'Q2 2024', 'Q3 2024', 'Q4 2024'],
                datasets: [{
                        label: 'New Business GWP',
                        data: [0, 0, 0, 0],
                        borderColor: colors.primary,
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Renewal GWP',
                        data: [0, 0, 0, 0],
                        borderColor: colors.success,
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.4,
                        fill: false
                    }
                ]
            };

            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'KES ' + formatNumber(context.raw);
                                return label;
                            }
                        }
                    }
                }
            };
            const quarterlyChart = new Chart(
                document.getElementById('quarterlyChart').getContext('2d'), {
                    type: 'line',
                    data: quarterlyData,
                    options: chartOptions
                }
            );

            const businessMixData = {
                labels: ['New Business', 'Renewals'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: [colors.primary, colors.success],
                    borderWidth: 1
                }]
            };

            const businessMixChart = new Chart(
                document.getElementById('businessMixChart').getContext('2d'), {
                    type: 'doughnut',
                    data: businessMixData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: 20
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        cutout: '50%'
                    }
                }
            );
            document.getElementById('businessMixChart').style.height = '300px';

            const newBusinessData = {
                labels: ['Facultative', 'Special Line', 'Treaty'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: [colors.primary, colors.success, colors.warning],
                    borderWidth: 1
                }]
            };
            const newBusinessChart = new Chart(
                document.getElementById('newBusinessChart').getContext('2d'), {
                    type: 'pie',
                    data: newBusinessData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: 20
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        cutout: '50%'
                    }
                }
            );

            const renewalsData = {
                labels: ['Facultative', 'Treaty'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: [colors.primary, colors.warning],
                    borderWidth: 1
                }]
            };
            const renewalsChart = new Chart(
                document.getElementById('renewalsChart').getContext('2d'), {
                    type: 'pie',
                    data: renewalsData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: 20
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        cutout: '50%'
                    }
                }
            );

            const budgetData = {
                labels: ['Facultative', 'Treaty'],
                datasets: [{
                        label: 'Budget',
                        data: [0, 0],
                        backgroundColor: colors.primary,
                        barPercentage: 0.6,
                        categoryPercentage: 0.7
                    },
                    {
                        label: 'Actual',
                        data: [0, 0],
                        backgroundColor: colors.success,
                        barPercentage: 0.6,
                        categoryPercentage: 0.7
                    }
                ]
            };
            const budgetChart = new Chart(
                document.getElementById('budgetChart').getContext('2d'), {
                    type: 'bar',
                    data: budgetData,
                    options: {
                        ...chartOptions,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + formatNumber(value);
                                    }
                                }
                            }
                        }
                    }
                }
            );

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                quarterlyChart.render();
                businessMixChart.render();
                newBusinessChart.render();
                renewalsChart.render();
                budgetChart.render();
            });

            function updateProgress() {
                const percent = (currentStep / totalSteps) * 100;
                $('#progressBar').css('width', percent + '%').attr('aria-valuenow', percent);
                $('#currentStep').text(currentStep);
                let stepTitle;
                switch (currentStep) {
                    case 1:
                        // stepTitle = "Select Date & Time";
                        break;
                    case 2:
                        stepTitle = "Your Details";
                        break;
                    case 3:
                        stepTitle = "Confirm";
                        break;
                    case 4:
                        stepTitle = "Complete";
                        break;
                }
                $('#stepTitle').text(stepTitle);
            }

            function showStep(step) {
                $('.step-content').addClass('d-none');
                $('#step' + step).removeClass('d-none');

                if (step > 1) {
                    $('#prevButton').removeClass('d-none');
                } else {
                    $('#prevButton').addClass('d-none');
                }

                if (step === 3) {
                    $('#nextButton').html('Confirm Appointment <i class="bx bx-check"></i>');
                } else if (step < 4) {
                    $('#nextButton').html('Continue <i class="bx bx-right-arrow"></i>');
                }

                if (step === 4) {
                    $('#modalFooter').html(
                        `<button type="button" class="btn btn-primary w-100" id="bookAnotherBtn">Book Another Appointment</button>`
                    );
                    $('#bookAnotherBtn').click(function() {
                        resetAppointmentForm();
                    });
                } else {
                    $('#modalFooter').html(`
                        <button type="button" class="btn btn-link text-muted ${step === 1 ? 'd-none' : ''}" id="prevButton">Back</button>
                        <button type="button" class="btn btn-dark" id="nextButton">${step === 3 ? 'Confirm Appointment <i class="bx bx-check"></i>' : 'Continue <i class="bx bx-right-arrow"></i>'}</button>
                    `);

                    $('#prevButton').click(previousStep);
                    $('#nextButton').click(nextStep);
                }
            }

            function nextStep() {
                if (currentStep === 1) {
                    if (!$('#appointment-date').val() || !$('#appointment-time').val()) {
                        alert('Please select both a date and time');
                        return;
                    }
                } else if (currentStep === 2) {
                    if (!$('#fullName').val() || !$('#emailAddress').val()) {
                        alert('Please fill in your name and email');
                        return;
                    }
                } else if (currentStep === 3) {
                    // $('#confirmationEmail').text($('#emailAddress').val());
                    // $('#confirmationDateTime').text($('#appointment-date').val() + ' at ' + $('#appointment-time')
                    //     .val());

                    // $('#stepWrapper').addClass('d-none');
                    // $('#stepTitle').addClass('d-none');

                    submitAppointment()
                    return;
                }

                if (currentStep === 2) {
                    $('#summaryDate').text($('#appointment-date').val());
                    $('#summaryTime').text($('#appointment-time').val());
                    $('#summaryName').text($('#fullName').val());
                    $('#summaryEmail').text($('#emailAddress').val());
                    $('#summaryPurpose').text($('#appointmentPurpose').val() || 'Not specified');
                }

                currentStep++;
                showStep(currentStep);
            }

            function previousStep() {
                currentStep--;
                showStep(currentStep);
                updateProgress();
            }

            function resetAppointmentForm() {
                $('#stepWrapper').removeClass('d-none');
                $('#stepTitle').removeClass('d-none');
                $('#appointmentForm')[0].reset();
                currentStep = 1;
                showStep(currentStep);
                updateProgress();

            }

            $('#prevButton').click(previousStep);
            $('#nextButton').click(nextStep);

            $('.btn-check').change(function() {
                if ($(this).is(':checked')) {
                    const name = $(this).attr('name');
                    $(`input[name="${name}"]`).each(function() {
                        $(this).next('label').removeClass('btn-primary').addClass(
                            'btn-outline-primary');
                    });
                    $(this).next('label').removeClass('btn-outline-primary').addClass('btn-primary');
                }
            });

            $('#appointmentModal').on('hidden.bs.modal', function() {
                resetAppointmentForm();
            });

            function submitAppointment() {
                const formData = {
                    _token: "{{ csrf_token() }}",
                    selected_date: $('#appointment-date').val(),
                    selected_time: $('#appointment-time').val(),
                    name: $('#fullName').val(),
                    email: $('#emailAddress').val(),
                    purpose: $('#appointmentPurpose').val()
                };

                $.ajax({
                    url: "{{ route('dashboard.appointments.store') }}",
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#nextButton').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                        );
                    },
                    success: function(response) {
                        console.log(response)
                        // if (response.success) {
                        //     // Update confirmation step with details
                        //     $('#confirmationEmail').text(formData.email);
                        //     $('#confirmationDateTime').text(formData.selected_date + ' at ' + formData
                        //         .selected_time);
                        //     $('#stepWrapper').addClass('d-none');
                        //     $('#stepTitle').addClass('d-none');

                        //     // Move to confirmation step
                        //     currentStep = 4;
                        //     showStep(currentStep);
                        //     updateProgress();

                        //     // Show success message
                        //     toastr.success('Appointment booked successfully!');

                        //     // Refresh appointments list
                        // } else {
                        //     toastr.error('There was an error booking your appointment.');
                        //     $('#nextButton').prop('disabled', false).html(
                        //         'Confirm Appointment <i class="bx bx-check"></i>');
                        // }
                    },
                    error: function(error) {
                        console.log(error)

                        // let errorMessage = 'There was an error booking your appointment.';

                        // if (xhr.responseJSON && xhr.responseJSON.errors) {
                        //     const errors = xhr.responseJSON.errors;
                        //     errorMessage = Object.values(errors)[0][0];
                        // }

                        // toastr.error(errorMessage);
                        // $('#nextButton').prop('disabled', false).html(
                        //     'Confirm Appointment  <i class="bx bx-check"></i>');
                    }
                });
            }
        });
    </script>
@endpush
