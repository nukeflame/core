@extends('layouts.app')

@section('content')
    <style>
        :root {
            --reins-primary: ##fff;
            --reins-secondary: #2d5f7f;
            --reins-accent: #e74c3c;
            --reins-success: #27ae60;
            --reins-warning: #f39c12;
            --reins-light: #ecf0f1;
            --reins-border: #bdc3c7;
            --reins-text: #2c3e50;
            --reins-text-muted: #7f8c8d;
            --reins-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            --reins-shadow-hover: 0 4px 12px rgba(0, 0, 0, 0.15);
            --secondary-color: #4d4f51;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Typography */
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--reins-text);
            line-height: 1.6;
            background-color: #f8f9fa;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #e1251b 0%, var(--secondary-color) 100%) color: white;
            padding: 2rem;
            margin: -1rem -1rem 2rem -1rem;
            border-radius: 0;
            box-shadow: var(--reins-shadow);
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header .insured-name {
            color: #ffd700;
            font-weight: 700;
        }

        .page-header .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Card Improvements */
        .card {
            border: 1px solid var(--reins-border);
            border-radius: 8px;
            box-shadow: var(--reins-shadow);
            margin-bottom: 2rem;
            background: white;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--reins-shadow-hover);
        }

        .card-body {
            padding: 2rem;
        }

        /* Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--reins-primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--reins-accent);
        }

        .section-header i {
            font-size: 1.3rem;
            color: var(--reins-accent);
        }

        .section-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--reins-accent), #c0392b);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }

        /* Form Fields */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--reins-text);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
        }

        .form-label .required-indicator {
            color: var(--reins-accent);
            margin-left: 0.25rem;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--reins-border);
            border-radius: 6px;
            padding: 0.625rem 0.875rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            background-color: white;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--reins-secondary);
            box-shadow: 0 0 0 3px rgba(45, 95, 127, 0.1);
            outline: none;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #f8f9fa;
            border-color: #e9ecef;
            color: var(--reins-text-muted);
            cursor: not-allowed;
        }

        /* Info Cards for Key Data */
        .info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid var(--reins-secondary);
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .info-card-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--reins-text-muted);
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .info-card-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--reins-primary);
        }

        /* Premium & Financial Display */
        .financial-summary {
            background: linear-gradient(135deg, var(--reins-primary), var(--reins-secondary));
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .financial-item {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            backdrop-filter: blur(10px);
        }

        .financial-item:last-child {
            margin-bottom: 0;
        }

        .financial-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .financial-value {
            font-size: 1.25rem;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--reins-shadow);
            margin-bottom: 1.5rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--reins-primary);
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
            border: none;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            font-size: 0.9375rem;
            border-bottom: 1px solid var(--reins-light);
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Reinsurer Filter Panel */
        .filter-panel {
            background: white;
            border: 1px solid var(--reins-border);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--reins-shadow);
        }

        .filter-panel h6 {
            color: var(--reins-primary);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Badges */
        .badge {
            padding: 0.4rem 0.8rem;
            font-weight: 500;
            font-size: 0.8125rem;
            border-radius: 4px;
            letter-spacing: 0.3px;
        }

        .badge-stage-2 {
            background-color: #3498db;
        }

        .badge-stage-3 {
            background-color: #f39c12;
        }

        .badge-stage-4 {
            background-color: #27ae60;
        }

        .badge-declined {
            background-color: #e74c3c;
        }

        /* Document Section */
        .document-row {
            background: white;
            border: 1px solid var(--reins-border);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .document-row:hover {
            box-shadow: var(--reins-shadow);
            border-color: var(--reins-secondary);
        }

        .document-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--reins-secondary), var(--reins-primary));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .btn-document-action {
            min-width: 100px;
        }

        /* Buttons */
        .btn {
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            font-size: 0.9375rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--reins-primary), var(--reins-secondary));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 95, 127, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--reins-success), #229954);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--reins-accent), #c0392b);
            color: white;
        }

        .btn-info {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .btn-link {
            color: var(--reins-secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .btn-link:hover {
            color: var(--reins-primary);
            text-decoration: underline;
        }

        /* Action Bar */
        .action-bar {
            background: white;
            border: 1px solid var(--reins-border);
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: var(--reins-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            bottom: 20px;
            z-index: 100;
        }

        /* Radio Buttons */
        .radio-group {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .radio-option label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
        }

        /* Grid System Enhancement */
        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.25rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .data-grid {
                grid-template-columns: 1fr;
            }

            .action-bar {
                flex-direction: column;
                gap: 1rem;
            }
        }

        /* Loading State */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 8px;
        }

        .spinner {
            border: 3px solid var(--reins-light);
            border-top: 3px solid var(--reins-secondary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--reins-text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Modal Improvements */
        .modal-header {
            background: linear-gradient(135deg, var(--reins-primary), var(--reins-secondary));
            color: white;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
        }

        .modal-content {
            border: none;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-body {
            padding: 2rem;
        }

        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-indicator.approved {
            background-color: #d5f4e6;
            color: #27ae60;
        }

        .status-indicator.pending {
            background-color: #fff3cd;
            color: #f39c12;
        }

        /* Tooltips */
        .info-tooltip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            background: var(--reins-secondary);
            color: white;
            border-radius: 50%;
            font-size: 0.75rem;
            cursor: help;
            margin-left: 0.5rem;
        }

        /* Divider */
        hr {
            border: none;
            border-top: 1px solid var(--reins-border);
            margin: 2rem 0;
        }

        /* File Upload Area */
        .upload-area {
            border: 2px dashed var(--reins-border);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: var(--reins-secondary);
            background-color: #f8f9fa;
        }

        .upload-area.dragover {
            border-color: var(--reins-success);
            background-color: #d5f4e6;
        }

        /* Success/Error Messages */
        .alert {
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-success {
            background-color: #d5f4e6;
            color: #27ae60;
        }

        .alert-danger {
            background-color: #fadbd8;
            color: #e74c3c;
        }

        .alert-info {
            background-color: #d6eaf8;
            color: #3498db;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{-- Page Header --}}
                    <div class="page-header">
                        <h1>
                            <i class="bx bx-transfer"></i>
                            Prospect Handover
                            <span class="insured-name">{{ $prospProperties->insured_name ?? 'N/A' }}</span>
                            @if ($approval == 1)
                                <span class="badge status-indicator approved">
                                    <i class="bx bx-check-circle"></i> Approved
                                </span>
                            @else
                                <span class="badge status-indicator pending">
                                    <i class="bx bx-time"></i> Pending Submission
                                </span>
                            @endif
                        </h1>
                    </div>

                    <div class="card-body">
                        <form id="msform" method="POST">
                            @csrf
                            <input type="hidden" name="agent_onboard_client" value="Y">

                            <fieldset>
                                {{-- Cedant Details Section --}}
                                <div class="form-section">
                                    <h6 class="section-header">
                                        <span class="section-icon"><i class="bx bx-building"></i></span>
                                        Cedant Information
                                    </h6>

                                    <div class="data-grid">
                                        {{-- Type of Business --}}
                                        <div class="form-group">
                                            @php
                                                $selectedBus = $types_of_bus->firstWhere(
                                                    'bus_type_id',
                                                    $prospProperties->type_of_bus,
                                                );
                                            @endphp
                                            <label class="form-label">Type of Business</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedBus->bus_type_name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="type_of_bus"
                                                value="{{ $selectedBus->bus_type_id ?? '' }}">
                                        </div>

                                        {{-- Cedant --}}
                                        <div class="form-group">
                                            @php
                                                $selectedCustomer = $customers->firstWhere(
                                                    'customer_id',
                                                    $prospProperties->customer_id,
                                                );
                                            @endphp
                                            <label class="form-label">Cedant</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedCustomer->name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="customer_id"
                                                value="{{ $selectedCustomer->customer_id ?? '' }}">
                                        </div>

                                        {{-- Lead Type --}}
                                        <div class="form-group">
                                            <label class="form-label">Lead Type</label>
                                            <input type="text" class="form-control"
                                                value="{{ $prospProperties->client_type ?? 'N/A' }}" readonly />
                                        </div>

                                        {{-- Reference Number --}}
                                        <div class="form-group">
                                            <label class="form-label">Reference Number</label>
                                            <input type="text" class="form-control"
                                                value="{{ $quotes->first()->quote_number ?? 'N/A' }}" readonly />
                                        </div>

                                        {{-- Year --}}
                                        <div class="form-group">
                                            @php
                                                $selectedYear = $pipeYear->firstWhere(
                                                    'id',
                                                    (int) ($prospProperties->pip_year ?? 0),
                                                );
                                            @endphp
                                            <label class="form-label">Year</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedYear->year ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="lead_year"
                                                value="{{ $prospProperties->pip_year ?? '' }}">
                                        </div>

                                        {{-- Insured Category --}}
                                        <div class="form-group">
                                            @php
                                                $categoryMap = ['N' => 'New Prospect', 'O' => 'Organic Growth'];
                                            @endphp
                                            <label class="form-label">Insured Category</label>
                                            <input type="text" class="form-control"
                                                value="{{ $categoryMap[$prospProperties->client_category ?? ''] ?? 'N/A' }}"
                                                readonly />
                                            <input type="hidden" name="client_category"
                                                value="{{ $prospProperties->client_category ?? '' }}">
                                        </div>

                                        {{-- Country --}}
                                        <div class="form-group">
                                            @php
                                                $selectedCountry = $countries->firstWhere(
                                                    'country_iso',
                                                    $prospProperties->country_code,
                                                );
                                            @endphp
                                            <label class="form-label">Country</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedCountry->country_name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="country_code"
                                                value="{{ $prospProperties->country_code ?? '' }}">
                                        </div>

                                        {{-- Branch --}}
                                        <div class="form-group">
                                            @php
                                                $selectedBranch = $branches->firstWhere(
                                                    'branch_code',
                                                    $prospProperties->branchcode,
                                                );
                                            @endphp
                                            <label class="form-label">Branch</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedBranch->branch_name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="branchcode"
                                                value="{{ $prospProperties->branchcode ?? '' }}">
                                        </div>

                                        {{-- Prospect Lead --}}
                                        <div class="form-group">
                                            @php
                                                $leadOwner = $users->firstWhere('id', $prospProperties->lead_owner);
                                            @endphp
                                            <label class="form-label">Prospect Lead</label>
                                            <input type="text" class="form-control"
                                                value="{{ $leadOwner->name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="lead_owner"
                                                value="{{ $prospProperties->lead_owner ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Insurance Details Section --}}
                                <div class="form-section mt-4">
                                    <h6 class="section-header">
                                        <span class="section-icon"><i class="bx bx-shield"></i></span>
                                        Risk & Coverage Details
                                    </h6>

                                    <div class="data-grid">
                                        {{-- Division --}}
                                        <div class="form-group">
                                            @php
                                                $selectedDivision = $reinsdivisions->firstWhere(
                                                    'division_code',
                                                    $prospProperties->divisions,
                                                );
                                            @endphp
                                            <label class="form-label">Division</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedDivision->division_name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="division"
                                                value="{{ $prospProperties->divisions ?? '' }}">
                                        </div>

                                        {{-- Class Group --}}
                                        <div class="form-group">
                                            @php
                                                $selectedClassGroup = $classGroups->firstWhere(
                                                    'group_code',
                                                    $prospProperties->class_group,
                                                );
                                            @endphp
                                            <label class="form-label">Class Group</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedClassGroup->group_name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="class_group"
                                                value="{{ $selectedClassGroup->group_code ?? '' }}">
                                        </div>

                                        {{-- Class Name --}}
                                        <div class="form-group">
                                            @php
                                                $selectedClass = $class->firstWhere(
                                                    'class_code',
                                                    $prospProperties->classcode,
                                                );
                                            @endphp
                                            <label class="form-label">Class Name</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedClass->class_name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="classcode"
                                                value="{{ $selectedClass->class_code ?? '' }}">
                                        </div>

                                        {{-- Insured Name --}}
                                        <div class="form-group">
                                            <label class="form-label">Insured Name</label>
                                            <input type="text" class="form-control" name="insured_name"
                                                value="{{ $prospProperties->insured_name ?? 'N/A' }}" readonly />
                                        </div>

                                        {{-- Industry --}}
                                        <div class="form-group">
                                            <label class="form-label">Industry</label>
                                            <input type="text" class="form-control" name="industry"
                                                value="{{ $prospProperties->industry ?? 'N/A' }}" readonly />
                                        </div>

                                        {{-- Offered Date --}}
                                        <div class="form-group">
                                            <label class="form-label">
                                                Offered Date <span class="required-indicator">*</span>
                                            </label>
                                            <input type="date" class="form-control" name="offered_date"
                                                value="{{ old('offered_date', $handover_approval->inception_date ?? '') }}"
                                                {{ $approval == 1 ? 'disabled' : 'required' }} />
                                        </div>

                                        {{-- Currency --}}
                                        <div class="form-group">
                                            @php
                                                $selectedCurrency = $currencies->firstWhere(
                                                    'currency_code',
                                                    $prospProperties->currency_code,
                                                );
                                            @endphp
                                            <label class="form-label">Currency</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedCurrency->currency_name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="currency_code"
                                                value="{{ $prospProperties->currency_code ?? '' }}">
                                        </div>

                                        {{-- Exchange Rate --}}
                                        <div class="form-group">
                                            <label class="form-label">Exchange Rate</label>
                                            <input type="text" class="form-control" name="today_currency"
                                                value="{{ number_format($prospProperties->today_currency ?? 0, 4) }}"
                                                readonly />
                                        </div>

                                        {{-- Sum Insured Type --}}
                                        <div class="form-group">
                                            @php
                                                $selectedSumInsured = $types_of_sum_insured->firstWhere(
                                                    'sum_insured_code',
                                                    $prospProperties->sum_insured_type,
                                                );
                                            @endphp
                                            <label class="form-label">Sum Insured Type</label>
                                            <input type="text" class="form-control"
                                                value="{{ $selectedSumInsured->sum_insured_name ?? 'N/A' }}" readonly />
                                            <input type="hidden" name="sum_insured_type"
                                                value="{{ $prospProperties->sum_insured_type ?? '' }}">
                                        </div>

                                        {{-- 100% Sum Insured --}}
                                        <div class="form-group">
                                            <label class="form-label">100% Sum Insured</label>
                                            <input type="text" class="form-control" name="total_sum_insured"
                                                value="{{ number_format($prospProperties->total_sum_insured ?? 0, 2) }}"
                                                readonly />
                                        </div>

                                        {{-- Excess Type --}}
                                        <div class="form-group">
                                            <label class="form-label">
                                                Excess Type <span class="required-indicator">*</span>
                                            </label>
                                            <select class="form-select" name="excess_type" id="excess_type"
                                                {{ $approval == 1 ? 'disabled' : 'required' }}>
                                                <option value="">Select Excess Type</option>
                                                <option value="R"
                                                    {{ old('excess_type', $handover_approval->excess_type ?? '') === 'R' ? 'selected' : '' }}>
                                                    Rate (%)
                                                </option>
                                                <option value="A"
                                                    {{ old('excess_type', $handover_approval->excess_type ?? '') === 'A' ? 'selected' : '' }}>
                                                    Amount
                                                </option>
                                            </select>
                                        </div>

                                        {{-- Excess --}}
                                        <div class="form-group">
                                            <label class="form-label" id="excess_label">
                                                Excess <span class="required-indicator">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="excess" id="excess"
                                                value="{{ old('excess', $handover_approval->excess ?? '') }}"
                                                {{ $approval == 1 ? 'disabled' : 'required' }} />
                                        </div>

                                        {{-- Max/Min --}}
                                        <div class="form-group">
                                            <label class="form-label">
                                                Max/Min <span class="required-indicator">*</span>
                                            </label>
                                            <div class="d-flex align-items-center gap-3">
                                                <input type="text" name="max_min" class="form-control"
                                                    style="flex: 1;"
                                                    value="{{ old('max_min', $handover_approval->{'max/min'} ?? '') }}"
                                                    {{ $approval == 1 ? 'disabled' : 'required' }} />
                                                <div class="radio-group">
                                                    <div class="radio-option">
                                                        <input type="radio" name="range" id="range_min"
                                                            value="min"
                                                            {{ ($handover_approval->range ?? '') == 'min' ? 'checked' : '' }}
                                                            {{ $approval == 1 ? 'disabled' : 'required' }} />
                                                        <label for="range_min">Min</label>
                                                    </div>
                                                    <div class="radio-option">
                                                        <input type="radio" name="range" id="range_max"
                                                            value="max"
                                                            {{ ($handover_approval->range ?? '') == 'max' ? 'checked' : '' }}
                                                            {{ $approval == 1 ? 'disabled' : 'required' }} />
                                                        <label for="range_max">Max</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Apply EML --}}
                                        <div class="form-group">
                                            @php
                                                $emlMap = ['Y' => 'Yes', 'N' => 'No'];
                                            @endphp
                                            <label class="form-label">Apply EML</label>
                                            <input type="text" class="form-control"
                                                value="{{ $emlMap[$prospProperties->apply_eml ?? ''] ?? 'N/A' }}"
                                                readonly />
                                            <input type="hidden" name="apply_eml"
                                                value="{{ $prospProperties->apply_eml ?? '' }}">
                                        </div>

                                        {{-- EML Fields (Conditional) --}}
                                        @if (($prospProperties->apply_eml ?? '') === 'Y')
                                            <div class="form-group">
                                                <label class="form-label">EML Rate (%)</label>
                                                <input type="number" class="form-control" name="eml_rate"
                                                    value="{{ old('eml_rate', $prospProperties->eml_rate ?? '') }}"
                                                    min="0" max="100" readonly />
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">EML Amount</label>
                                                <input type="text" class="form-control" name="eml_amt"
                                                    value="{{ number_format($prospProperties->eml_amt ?? 0, 2) }}"
                                                    readonly />
                                            </div>
                                        @endif

                                        {{-- Effective Sum Insured --}}
                                        <div class="form-group">
                                            <label class="form-label">Effective Sum Insured</label>
                                            <input type="text" class="form-control" name="effective_sum_insured"
                                                value="{{ number_format($prospProperties->effective_sum_insured ?? 0, 2) }}"
                                                readonly />
                                        </div>

                                        {{-- Risk Details --}}
                                        <div class="form-group" style="grid-column: 1 / -1;">
                                            <label class="form-label">Risk Details</label>
                                            <textarea class="form-control" name="risk_details" rows="3" readonly>{{ $prospProperties->risk_details ?? 'N/A' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Premium & Commission Details --}}
                                <div class="form-section mt-4">
                                    <h6 class="section-header">
                                        <span class="section-icon"><i class="bx bx-dollar-circle"></i></span>
                                        Premium & Commission Structure
                                    </h6>

                                    <div class="row">
                                        <div class="col-lg-6 mb-4">
                                            <div class="financial-summary">
                                                <h6 class="mb-3" style="opacity: 0.9;">Cedant Details</h6>

                                                <div class="financial-item">
                                                    <div class="financial-label">Cedant Premium</div>
                                                    <div class="financial-value">
                                                        {{ $selectedCurrency->currency_symbol ?? '' }}
                                                        {{ number_format($prospProperties->cede_premium ?? 0, 2) }}
                                                    </div>
                                                </div>

                                                <div class="financial-item">
                                                    <div class="financial-label">Commission Rate</div>
                                                    <div class="financial-value">
                                                        {{ $prospProperties->comm_rate ?? 'N/A' }}%</div>
                                                </div>

                                                <div class="financial-item">
                                                    <div class="financial-label">Commission Amount</div>
                                                    <div class="financial-value">
                                                        {{ $selectedCurrency->currency_symbol ?? '' }}
                                                        {{ number_format($prospProperties->comm_amt ?? 0, 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb-4">
                                            <div class="financial-summary">
                                                <h6 class="mb-3" style="opacity: 0.9;">Reinsurer Details</h6>

                                                <div class="financial-item">
                                                    <div class="financial-label">Reinsurer Premium</div>
                                                    <div class="financial-value">
                                                        {{ $selectedCurrency->currency_symbol ?? '' }}
                                                        {{ number_format($prospProperties->rein_premium ?? 0, 2) }}
                                                    </div>
                                                </div>

                                                <div class="financial-item">
                                                    <div class="financial-label">Written Share</div>
                                                    <div class="financial-value">
                                                        {{ $prospProperties->fac_share_offered ?? 'N/A' }}%</div>
                                                </div>

                                                <div class="financial-item">
                                                    <div class="financial-label">Commission Rate</div>
                                                    <div class="financial-value">
                                                        {{ $prospProperties->reins_comm_rate ?? 'N/A' }}%</div>
                                                </div>

                                                <div class="financial-item">
                                                    <div class="financial-label">Commission Amount</div>
                                                    <div class="financial-value">
                                                        {{ $selectedCurrency->currency_symbol ?? '' }}
                                                        {{ number_format($prospProperties->reins_comm_amt ?? 0, 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Brokerage Details --}}
                                    <div class="data-grid">
                                        <div class="form-group">
                                            @php
                                                $brokerageTypeMap = ['R' => 'Rate', 'A' => 'Quoted Amount'];
                                            @endphp
                                            <label class="form-label">Brokerage Commission Type</label>
                                            <input type="text" class="form-control"
                                                value="{{ $brokerageTypeMap[$prospProperties->brokerage_comm_type ?? ''] ?? 'N/A' }}"
                                                readonly />
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Brokerage Rate (%)</label>
                                            <input type="text" class="form-control" name="brokerage_comm_rate"
                                                value="{{ $prospProperties->brokerage_comm_rate ?? 'N/A' }}" readonly />
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Brokerage Amount</label>
                                            <input type="text" class="form-control" name="brokerage_comm_amt"
                                                value="{{ number_format($prospProperties->brokerage_comm_amt ?? 0, 2) }}"
                                                readonly />
                                        </div>
                                    </div>

                                    {{-- Hidden inputs for premium data --}}
                                    <input type="hidden" name="cede_premium"
                                        value="{{ $prospProperties->cede_premium ?? 0 }}">
                                    <input type="hidden" name="rein_premium"
                                        value="{{ $prospProperties->rein_premium ?? 0 }}">
                                    <input type="hidden" name="fac_share_offered"
                                        value="{{ $prospProperties->fac_share_offered ?? 0 }}">
                                    <input type="hidden" name="comm_rate"
                                        value="{{ $prospProperties->comm_rate ?? 0 }}">
                                    <input type="hidden" name="comm_amt" value="{{ $prospProperties->comm_amt ?? 0 }}">
                                    <input type="hidden" name="reins_comm_rate"
                                        value="{{ $prospProperties->reins_comm_rate ?? 0 }}">
                                    <input type="hidden" name="reins_comm_amt"
                                        value="{{ $prospProperties->reins_comm_amt ?? 0 }}">
                                </div>

                                {{-- Cover Period Section --}}
                                <div class="form-section mt-4">
                                    <h6 class="section-header">
                                        <span class="section-icon"><i class="bx bx-calendar"></i></span>
                                        Cover Period & Assignment
                                    </h6>

                                    <div class="data-grid">
                                        {{-- Cover Start Date (Commented in original) --}}
                                        {{-- <div class="form-group">
                                            <label class="form-label">
                                                Cover Start Date <span class="required-indicator">*</span>
                                            </label>
                                            <input type="date" class="form-control" name="effective_date"
                                                   value="{{ $prospProperties->effective_date ?? '' }}"
                                                   {{ $approval == 1 ? 'disabled' : 'required' }} />
                                        </div> --}}

                                        {{-- Cover End Date --}}
                                        <div class="form-group">
                                            <label class="form-label">Cover End Date</label>
                                            <input type="date" class="form-control" name="closing_date"
                                                value="{{ $prospProperties->closing_date ?? '' }}"
                                                {{ $approval == 1 ? 'disabled' : '' }} />
                                        </div>

                                        {{-- Account Handler (Commented in original) --}}
                                        {{-- <div class="form-group">
                                            <label class="form-label">
                                                Account Handler <span class="required-indicator">*</span>
                                            </label>
                                            <select class="form-select" name="handler" {{ $approval == 1 ? 'disabled' : 'required' }}>
                                                <option value="">Select Account Handler</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ ($handover_approval->handler ?? '') == $user->id ? 'selected' : '' }}>
                                                        {{ ucwords($user->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> --}}

                                        {{-- Approver (Commented in original) --}}
                                        {{-- <div class="form-group">
                                            @php
                                                $selectedApprovers = isset($handover_approval)
                                                    ? json_decode($handover_approval->approver, true) ?? []
                                                    : [];
                                            @endphp
                                            <label class="form-label">
                                                Approver(s) <span class="required-indicator">*</span>
                                            </label>
                                            <select class="form-select" name="approver[]" multiple {{ $approval == 1 ? 'disabled' : 'required' }}>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ in_array($user->id, $selectedApprovers) ? 'selected' : '' }}>
                                                        {{ ucwords($user->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> --}}

                                        {{-- Remarks --}}
                                        <div class="form-group" style="grid-column: 1 / -1;">
                                            <label class="form-label">Remarks</label>
                                            <textarea class="form-control" name="remarks" rows="4" {{ $approval == 1 ? 'disabled' : '' }}>{{ old('remarks', $handover_approval->remarks ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Reinsurers Section --}}
                                <div class="form-section mt-4">
                                    <h6 class="section-header">
                                        <span class="section-icon"><i class="bx bx-group"></i></span>
                                        Participating Reinsurers
                                    </h6>

                                    {{-- Filter Panel --}}
                                    <div class="filter-panel">
                                        <h6>
                                            <i class="bx bx-filter"></i>
                                            Filter by Pipeline Stage
                                        </h6>
                                        <div class="row align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label">Select Stage</label>
                                                <select id="stage" class="form-select">
                                                    <option value="">All Stages</option>
                                                    <option value="2">Stage 2 - Quotation</option>
                                                    <option value="3">Stage 3 - Negotiation</option>
                                                    <option value="4">Stage 4 - Commitment</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8 mt-3 mt-md-0">
                                                <div class="alert alert-info mb-0">
                                                    <i class="bx bx-info-circle"></i>
                                                    Select a stage to view reinsurers at that pipeline stage
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Reinsurers Table --}}
                                    <div class="table-container position-relative" id="reinsurer-table-container">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50%;">Reinsurer Name</th>
                                                    <th style="width: 25%;">Written Share (%)</th>
                                                    <th style="width: 25%;">Signed Share (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reinsurer-body">
                                                <tr>
                                                    <td colspan="3" class="text-center py-5">
                                                        <div class="empty-state">
                                                            <i class="bx bx-filter-alt"></i>
                                                            <p class="mb-0">Please select a stage to load reinsurers</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Declined Reinsurers Section --}}
                                @if ($decline_reinsurers->isNotEmpty())
                                    <div class="form-section mt-4">
                                        <h6 class="section-header">
                                            <span class="section-icon"><i class="bx bx-x-circle"></i></span>
                                            Declined Reinsurers
                                        </h6>

                                        <div class="table-container">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 60%;">Reinsurer Name</th>
                                                        <th style="width: 40%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($decline_reinsurers as $index => $item)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge badge-declined">Declined</span>
                                                                    <strong>{{ $item->customer_name->name ?? 'N/A' }}</strong>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-link"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#reasonModal{{ $index }}">
                                                                    <i class="bx bx-show"></i> View Decline Reason
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Modals for Decline Reasons --}}
                                    @foreach ($decline_reinsurers as $index => $item)
                                        <div class="modal fade" id="reasonModal{{ $index }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="bx bx-message-square-detail"></i>
                                                            Decline Reason - {{ $item->customer_name->name ?? 'N/A' }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <strong>Reason:</strong>
                                                            <p class="mb-0 mt-2">
                                                                {{ $item->reason ?? 'No reason provided' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                {{-- Document Attachments Section --}}
                                <div class="form-section mt-4">
                                    <h6 class="section-header">
                                        <span class="section-icon"><i class="bx bx-file"></i></span>
                                        Document Attachments
                                    </h6>

                                    @if ($approval == 1)
                                        {{-- View Mode: Show uploaded documents --}}
                                        @php
                                            $baseAssetUrl = Storage::disk('s3')->url('uploads');
                                        @endphp

                                        @forelse($prosp_doc as $doc)
                                            <div class="document-row">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <div class="document-icon">
                                                            <i class="bx bx-file-blank"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label mb-0">Document Type</label>
                                                        <div class="fw-bold">{{ $doc->description }}</div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label mb-0">File Name</label>
                                                        <div class="text-muted small">{{ $doc->file }}</div>
                                                    </div>
                                                    <div class="col-md-3 text-end">
                                                        <a href="{{ $baseAssetUrl . '/' . $doc->file }}" target="_blank"
                                                            class="btn btn-primary btn-document-action">
                                                            <i class="bx bx-show"></i> View Document
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <i class="bx bx-folder-open"></i>
                                                <p class="mb-0">No documents have been uploaded yet</p>
                                            </div>
                                        @endforelse
                                    @else
                                        {{-- Edit Mode: Upload new documents --}}
                                        <div class="alert alert-info mb-4">
                                            <i class="bx bx-info-circle"></i>
                                            <strong>Document Requirements:</strong> Please upload all mandatory documents
                                            marked with
                                            <span class="required-indicator">*</span>. Accepted formats: PDF, JPG, JPEG,
                                            PNG, DOC, DOCX
                                        </div>

                                        @foreach ($docs as $index => $doc)
                                            <div class="document-row" data-division="{{ $doc->division }}">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <div class="document-icon">
                                                            <i class="bx bx-upload"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">
                                                            Document Type
                                                            @if ($doc->mandatory === 'Y')
                                                                <span class="required-indicator">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text" name="document_name[]" class="form-control"
                                                            value="{{ $doc->doc_type }}" readonly />
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label">Select File</label>
                                                        <input type="file" name="document_file[]"
                                                            id="document_file{{ $doc->id }}"
                                                            class="form-control document_file"
                                                            {{ $doc->mandatory === 'Y' ? 'required' : '' }}
                                                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <label class="form-label d-block">&nbsp;</label>
                                                        <button type="button" class="btn btn-info preview me-2"
                                                            data-doc-id="{{ $doc->id }}">
                                                            <i class="bx bx-show"></i> Preview
                                                        </button>
                                                        <button class="btn btn-success addDocfac" type="button">
                                                            <i class="bx bx-plus"></i> Add More
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- Show previously uploaded documents --}}
                                        @if ($prosp_doc->isNotEmpty())
                                            <h6 class="section-header mt-4"
                                                style="border-bottom: 1px solid var(--reins-border);">
                                                <span class="section-icon"><i class="bx bx-history"></i></span>
                                                Previously Uploaded Documents
                                            </h6>

                                            @php
                                                $baseAssetUrl = Storage::disk('s3')->url('uploads');
                                            @endphp

                                            @foreach ($prosp_doc as $doc)
                                                <div class="document-row">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <div class="document-icon">
                                                                <i class="bx bx-check"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label mb-0">Document Type</label>
                                                            <div class="fw-bold">{{ $doc->description }}</div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="form-label mb-0">File Name</label>
                                                            <div class="text-muted small">{{ $doc->file }}</div>
                                                        </div>
                                                        <div class="col-md-3 text-end">
                                                            <a href="{{ $baseAssetUrl . '/' . $doc->file }}"
                                                                target="_blank"
                                                                class="btn btn-primary btn-document-action">
                                                                <i class="bx bx-show"></i> View
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>

                                {{-- Action Bar --}}
                                @if ($approval != 1)
                                    <div class="action-bar">
                                        <div>
                                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                                <i class="bx bx-arrow-back"></i> Cancel
                                            </a>
                                        </div>
                                        <div>
                                            <button type="submit" id="submit" class="btn btn-success btn-lg">
                                                <i class="bx bx-transfer"></i> Submit for Handover
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-lg">
                                            <i class="bx bx-arrow-back"></i> Return to Dashboard
                                        </a>
                                    </div>
                                @endif
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Document Preview Modal --}}
    <div class="modal fade" id="v_docs" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-file"></i> Document Preview
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="preview_iframe" class="w-100"
                        style="height: 600px; border: none; display: none;"></iframe>
                    <img id="preview_image" src="" class="w-100" style="display: none;"
                        alt="Document Preview" />
                    <div id="preview_error" class="text-center py-5" style="display: none;">
                        <i class="bx bx-error-circle" style="font-size: 4rem; color: var(--reins-accent);"></i>
                        <p class="mt-3">Preview not available for this file type.</p>
                        <p class="text-muted">Please download the file to view it.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            'use strict';

            const approval = @json($approval);
            const prospect = @json($prospect);
            const pipeid = []; // Pipeline ID array (commented in original)

            // Initialize CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Update excess label based on type
            function updateExcessLabel() {
                const excessType = $('#excess_type').val();
                const label = excessType === 'R' ? 'Excess (%)' : 'Excess Amount';
                $('#excess_label').html(label + ' <span class="required-indicator">*</span>');
            }

            $('#excess_type').on('change', updateExcessLabel);
            updateExcessLabel();

            // Auto-calculate closing date (1 year - 1 day from effective date)
            $('#effective_date').on('change', function() {
                const effectiveDate = $(this).val();
                if (effectiveDate) {
                    const date = new Date(effectiveDate);
                    date.setFullYear(date.getFullYear() + 1);
                    date.setDate(date.getDate() - 1);
                    $('#closing_date').val(date.toISOString().split('T')[0]);
                }
            });

            // Load reinsurers by stage with improved UI
            $('#stage').on('change', function() {
                const stage = $(this).val();
                const $container = $('#reinsurer-table-container');
                const $tbody = $('#reinsurer-body');

                if (!stage) {
                    $tbody.html(`
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bx bx-filter-alt"></i>
                                    <p class="mb-0">Please select a stage to load reinsurers</p>
                                </div>
                            </td>
                        </tr>
                    `);
                    return;
                }

                // Show loading state
                $container.append('<div class="loading-overlay"><div class="spinner"></div></div>');

                $.ajax({
                    url: '{{ route('reinsurers.filter') }}',
                    type: 'GET',
                    data: {
                        stage,
                        opportunity_id: pipeid
                    },
                    success: function(response) {
                        let rows = '';

                        if (response.reinsurers?.length > 0) {
                            response.reinsurers.forEach(item => {
                                const name = item.reinsurer_name
                                    .split(' ')
                                    .map(word => word.charAt(0).toUpperCase() + word
                                        .slice(1).toLowerCase())
                                    .join(' ');

                                const writtenShare = item.written_share || 'N/A';
                                const signedShare = item.signed_share || 'N/A';

                                rows += `
                                    <tr>
                                        <td>
                                            <strong>${name}</strong>
                                            <span class="badge badge-stage-${stage} ms-2">Stage ${stage}</span>
                                        </td>
                                        <td><strong>${writtenShare}${writtenShare !== 'N/A' ? '%' : ''}</strong></td>
                                        <td><strong>${signedShare}${signedShare !== 'N/A' ? '%' : ''}</strong></td>
                                    </tr>
                                `;
                            });
                        } else {
                            rows = `
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bx bx-search-alt"></i>
                                            <p class="mb-0">No reinsurers found for this stage</p>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }

                        $tbody.html(rows);
                    },
                    error: function() {
                        $tbody.html(`
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="alert alert-danger mb-0">
                                        <i class="bx bx-error"></i>
                                        Failed to load reinsurer data. Please try again.
                                    </div>
                                </td>
                            </tr>
                        `);
                    },
                    complete: function() {
                        $container.find('.loading-overlay').remove();
                    }
                });
            });

            // Handle document preview with improved error handling
            $(document).on('click', '.preview', function() {
                const $row = $(this).closest('.document-row');
                const fileInput = $row.find('input[type="file"]')[0];
                const file = fileInput?.files[0];

                if (!file) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please select a file first before previewing'
                    });
                    return;
                }

                const fileURL = URL.createObjectURL(file);
                const $iframe = $('#preview_iframe');
                const $image = $('#preview_image');
                const $error = $('#preview_error');

                // Hide all preview elements
                $iframe.hide();
                $image.hide();
                $error.hide();

                // Show appropriate preview based on file type
                if (file.type === 'application/pdf') {
                    $iframe.attr('src', fileURL).show();
                } else if (file.type.startsWith('image/')) {
                    $image.attr('src', fileURL).show();
                } else {
                    $error.show();
                }

                // Show modal
                $('#v_docs').modal('show');

                // Clean up blob URL when modal is closed
                $('#v_docs').one('hidden.bs.modal', function() {
                    URL.revokeObjectURL(fileURL);
                });
            });

            // Add new document row
            let fileCounter = 0;
            $(document).on('click', '.addDocfac', function(e) {
                e.preventDefault();
                fileCounter++;

                const newRow = `
                    <div class="document-row new-document-row">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="document-icon">
                                    <i class="bx bx-plus"></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Document Type</label>
                                <input type="text" name="document_name[]" class="form-control"
                                       placeholder="Enter document name" />
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Select File</label>
                                <input type="file" name="document_file[]" class="form-control document_file"
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                            </div>
                            <div class="col-md-4 text-end">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="button" class="btn btn-info preview me-2">
                                    <i class="bx bx-show"></i> Preview
                                </button>
                                <button class="btn btn-danger remove-file" type="button">
                                    <i class="bx bx-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                $(this).closest('.document-row').after(newRow);
            });

            // Remove document row
            $(document).on('click', '.remove-file', function() {
                $(this).closest('.new-document-row').fadeOut(300, function() {
                    $(this).remove();
                });
            });

            // Form submission with validation
            $('#submit').on('click', function(e) {
                e.preventDefault();

                const form = $('#msform')[0];

                // Check form validity
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // Confirmation dialog
                Swal.fire({
                    icon: 'warning',
                    title: 'Confirm Handover Submission',
                    html: `
                        <p>You are about to submit this prospect for handover to operations.</p>
                        <p><strong>Please confirm that all information is correct.</strong></p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bx bx-check"></i> Yes, Submit',
                    cancelButtonText: '<i class="bx bx-x"></i> Cancel',
                    confirmButtonColor: '#27ae60',
                    cancelButtonColor: '#95a5a6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            });

            // Submit form function
            function submitForm() {
                const form = $('#msform')[0];
                const formData = new FormData(form);
                const $submitBtn = $('#submit');

                // Disable submit button and show loading state
                $submitBtn
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Submitting...')
                    .prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('client.stage') }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Handover Successful!',
                                html: `
                                    <p>The prospect has been successfully submitted for handover.</p>
                                    <p>Redirecting to pipeline view...</p>
                                `,
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '/pipelines_view';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Failed',
                                text: res.message || 'An error occurred during submission',
                                confirmButtonColor: '#e74c3c'
                            });
                            resetSubmitButton();
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred during submission';

                        if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 422) {
                            errorMessage = 'Please check all required fields and try again';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error. Please contact support';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Error',
                            text: errorMessage,
                            confirmButtonColor: '#e74c3c'
                        });
                        resetSubmitButton();
                    }
                });
            }

            // Reset submit button
            function resetSubmitButton() {
                $('#submit')
                    .html('<i class="bx bx-transfer"></i> Submit for Handover')
                    .prop('disabled', false);
            }
        });
    </script>
@endpush
