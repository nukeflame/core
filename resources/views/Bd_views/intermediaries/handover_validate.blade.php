@extends('layouts.app')

@section('content')
    <style>
        :root {
            --reins-primary: #fff;
            --reins-default: #333;
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

        .page-header {
            background: #fff;
            padding: 2rem;
            margin: -1rem -1rem 2rem -1rem;
            border-radius: 0;
            box-shadow: var(--reins-shadow);
        }

        .page-header h1 {
            font-size: 1.55rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header .insured-name {
            color: #198754;
            font-weight: 600;
            font-size: 18px;
            line-height: 2px;
        }

        .page-header .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

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

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--reins-accent);
        }

        .section-header i {
            font-size: 1.3rem;
            color: #fff;
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
            color: var(--reins-default);
        }

        .financial-summary {
            background: linear-gradient(135deg, #660909, #2d5f7f);
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

        .table-container {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--reins-shadow);
            margin-bottom: 1.5rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
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

        .table tfoot th {
            background: #f8f9fa;
            font-weight: 600;
            padding: 1rem;
            border-top: 2px solid var(--reins-border);
        }

        .filter-panel {
            background: white;
            border: 1px solid var(--reins-border);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--reins-shadow);
        }

        .filter-panel h6 {
            color: inherit;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

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
            background: linear-gradient(135deg, #6c757d, #a09292);
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

        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

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

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .empty-state-table {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--reins-text-muted);
        }

        .empty-state-table i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

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

        hr {
            border: none;
            border-top: 1px solid var(--reins-border);
            margin: 2rem 0;
        }

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

        .form-group {
            margin-bottom: 0px;
            border: none;
            padding: 0px;
        }

        .reinsurer-row {
            transition: all 0.2s ease;
        }

        .reinsurer-row:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
            padding: 0.5rem 0.75rem;
        }

        .badge-soft-written {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .badge-soft-signed {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .share-history {
            display: flex;
            align-items: center;
            justify-content: start;
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .reinsurer-name {
            display: flex;
            align-items: center;
        }

        .font-monospace {
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.875rem;
        }

        .stats-panel {
            background: white;
            border: 1px solid var(--reins-border);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--reins-shadow);
        }

        .stat-item {
            padding: 0.75rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 6px;
            text-align: center;
        }

        #loading-overlay-template {
            display: none;
        }
    </style>

    <template id="loading-overlay-template">
        <div class="loading-overlay">
            <div class="spinner"></div>
        </div>
    </template>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="page-header">
                        <h1>
                            <i class="bx bx-transfer"></i>
                            Prospect Handover -
                            <span class="insured-name">{{ $prospProperties->insured_name ?? 'N/A' }}</span>
                            {{-- @if ($approval == 1)
                                <span class="badge status-indicator approved">
                                    <i class="bx bx-check-circle"></i> Approved
                                </span>
                            @else
                                <span class="badge status-indicator pending">
                                    <i class="bx bx-time"></i> Pending Submission
                                </span>
                            @endif --}}
                        </h1>
                    </div>

                    <div class="card-body">
                        <form id="msform" method="POST">
                            @csrf
                            <input type="hidden" name="agent_onboard_client" value="Y">
                            <input type="hidden" name="prospect_id" value="{{ $pipeid }}">

                            <fieldset>
                                <div class="form-section">
                                    <h6 class="section-header">
                                        <span class="section-icon"><i class="bx bx-building"></i></span>
                                        Cedant Information
                                    </h6>

                                    <div class="data-grid">
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

                                        <div class="form-group">
                                            <label class="form-label">Lead Type</label>
                                            <input type="text" class="form-control"
                                                value="{{ $prospProperties->client_type ?? 'N/A' }}" readonly />
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Reference Number</label>
                                            <input type="text" class="form-control"
                                                value="{{ $quotes->first()->opportunity_id ?? 'N/A' }}" readonly />
                                        </div>

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
                                            <textarea class="form-control resize-none" name="risk_details" rows="3" readonly>{{ $prospProperties->risk_details ?? 'N/A' }}</textarea>
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

                                        {{-- Excess Type --}}
                                        <div class="form-group">
                                            <label class="form-label">
                                                Excess Type <span class="required-indicator">*</span>
                                            </label>
                                            <div class="cover-card">
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
                                                Max/Min<span class="required-indicator">*</span>
                                            </label>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="cover-card" style="flex-direction: column!important;">
                                                    <input type="text" name="max_min" class="form-control"
                                                        style="flex: 1; width:130px;"
                                                        value="{{ old('max_min', $handover_approval->{'max/min'} ?? '') }}"
                                                        {{ $approval == 1 ? 'disabled' : 'required' }} />
                                                </div>
                                                <div class="radio-group">
                                                    <div class="radio-option">
                                                        <input type="radio" name="range" id="range_min"
                                                            value="min" checked
                                                            {{ $approval == 1 ? 'disabled' : 'required' }} />
                                                        <label for="range_min">Minimum</label>
                                                    </div>
                                                    <div class="radio-option">
                                                        <input type="radio" name="range" id="range_max"
                                                            value="max" checked {{-- {{ ($handover_approval->range ?? '') == 'max' ? 'checked' : '' }} --}}
                                                            {{ $approval == 1 ? 'disabled' : 'required' }} />
                                                        <label for="range_max">Maximum</label>
                                                    </div>
                                                </div>
                                            </div>
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
                                                    <div class="financial-label">Sum Insured</div>
                                                    <div class="financial-value">
                                                        {{ $selectedCurrency->currency_symbol ?? '' }}
                                                        {{ number_format($prospProperties->cede_premium ?? 0, 2) }}
                                                    </div>
                                                </div>

                                                <div class="financial-item">
                                                    <div class="financial-label">Cedant Premium</div>
                                                    <div class="financial-value">
                                                        {{ $selectedCurrency->currency_symbol ?? '' }}
                                                        {{ number_format($prospProperties->effective_sum_insured ?? 0, 2) }}
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
                                                <h6 class="mb-3" style="opacity: 0.9;">Reinsurer Broker Details</h6>

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
                                        {{-- Cover Start Date --}}
                                        <div class="form-group">
                                            <label class="form-label">
                                                Cover Start Date <span class="required-indicator">*</span>
                                            </label>
                                            <input type="date" class="form-control" name="effective_date"
                                                value="{{ $prospProperties->effective_date ?? '' }}"
                                                {{ $approval == 1 ? 'disabled' : 'required' }} />
                                        </div>

                                        {{-- Cover End Date --}}
                                        <div class="form-group">
                                            <label class="form-label">Cover End Date <span
                                                    class="required-indicator">*</span></label>
                                            <input type="date" class="form-control" name="closing_date"
                                                value="{{ $prospProperties->closing_date ?? '' }}"
                                                {{ $approval == 1 ? 'disabled' : '' }} />
                                        </div>

                                        {{-- Account Handler --}}
                                        <div class="form-group">
                                            <label class="form-label">
                                                Account Handler <span class="required-indicator">*</span>
                                            </label>
                                            <div class="cover-card">

                                                <select class="form-select" name="handler"
                                                    {{ $approval == 1 ? 'disabled' : 'required' }}>
                                                    <option value="">Select Account Handler</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ ($handover_approval->handler ?? '') == $user->id ? 'selected' : '' }}>
                                                            {{ ucwords($user->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>

                                        {{-- Approver --}}
                                        <div class="form-group" style="grid-column: 4 / 6;">
                                            @php
                                                $selectedApprovers = isset($handover_approval)
                                                    ? json_decode($handover_approval->approver, true) ?? []
                                                    : [];
                                            @endphp
                                            <label class="form-label">
                                                Approver(s) <span class="required-indicator">*</span>
                                            </label>
                                            <div class="cover-card">
                                                <select class="form-select" name="approver[]" multiple
                                                    {{ $approval == 1 ? 'disabled' : 'required' }}>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ in_array($user->id, $selectedApprovers) ? 'selected' : '' }}>
                                                            {{ ucwords($user->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

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
                                                    @foreach ($stages as $stage)
                                                        <option value="{{ $stage['value'] }}">Stage {{ $stage['value'] }}
                                                            -
                                                            {{ ucwords($stage['key']) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-8 mt-3 mt-md-0">
                                                <div class="alert alert-info mb-0"
                                                    style="padding: 9px; padding-left: 15px;">
                                                    <i class="bx bx-info-circle"></i>
                                                    Select a stage to view reinsurers at that pipeline stage
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Quick Stats Panel --}}
                                    <div class="stats-panel">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="stat-item">
                                                    <div id="total-reinsurers">
                                                        <i class="bx bx-building"></i> Total: 0
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="stat-item">
                                                    <div id="total-written-share">
                                                        <i class="bx bx-trending-up"></i> Written: 0%
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="stat-item">
                                                    <div id="total-signed-share">
                                                        <i class="bx bx-check-circle"></i> Signed: 0%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-container position-relative" id="reinsurer-table-container">
                                        <table class="table" id="reinsurer-table-f">
                                            <thead>
                                                <tr>
                                                    <th style="width: 2%;"></th>
                                                    <th style="width: 40%;">Reinsurer Name</th>
                                                    <th style="width: 20%;">Written Share (%)</th>
                                                    <th style="width: 20%;">Signed Share (%)</th>
                                                    <th style="width: 18%;">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reinsurer-body">
                                                <tr>
                                                    <td colspan="5" class="text-center py-5">
                                                        <div class="empty-state-table">
                                                            <i class="bx bx-filter-alt"></i>
                                                            <p class="mb-0">Please select a stage to load reinsurers</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot id="reinsurer-totals" style="display: none;">
                                                <tr>
                                                    <th colspan="2" class="text-end">Totals:</th>
                                                    <th class="text-end">
                                                        <span id="footer-written-total" class="badge bg-primary">0%</span>
                                                    </th>
                                                    <th class="text-end">
                                                        <span id="footer-signed-total" class="badge bg-success">0%</span>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="filter-panel">
                                        <h6>
                                            <i class="bx bx-file"></i>
                                            Stage Document Uploaded
                                        </h6>

                                        <div class="alert alert-info mb-0">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Previous uploaded attachments for <span id="docStageName">selected</span> stage
                                        </div>
                                    </div>

                                    {{-- Documents Display Container --}}
                                    <div id="prospectStageDisplay" style="display: none;"></div>

                                    {{-- Empty State --}}
                                    <div id="prospectStageEmpty">
                                        <div class="empty-state-table">
                                            <i class="bx bx-file"></i>
                                            <p class="mb-0">Please select a stage to load documents</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section mt-4">
                                    <h6 class="section-header">
                                        <span class="section-icon"><i class="bx bx-file"></i></span>
                                        Document Attachments
                                    </h6>

                                    @if ($approval == 1)
                                        @php
                                            $baseAssetUrl = Storage::disk('s3')->url('uploads');
                                        @endphp

                                        @forelse($prosp_doc as $doc)
                                            <div class="document-row">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <div class="document-icon bg-warning">
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
                                            <div class="empty-state-table">
                                                <i class="bx bx-folder-open"></i>
                                                <p class="mb-0">No documents have been uploaded yet</p>
                                            </div>
                                        @endforelse
                                    @else
                                        <div class="alert alert-info mb-4">
                                            <i class="bx bx-info-circle"></i>
                                            <strong>Document Requirements:</strong> Please upload all mandatory documents
                                            marked with
                                            <span class="required-indicator">*</span>. Accepted formats: PDF, JPG, JPEG,
                                            PNG, DOC, DOCX
                                        </div>

                                        <div class="row">
                                            @foreach ($docs as $index => $doc)
                                                <div class="col-6">
                                                    <div class="document-row" data-division="{{ $doc->division }}">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <div class="document-icon">
                                                                    <i class="bx bx-upload"></i>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label class="form-label">
                                                                    Document Type
                                                                    @if ($doc->mandatory === 'Y')
                                                                        <span class="required-indicator">*</span>
                                                                    @endif
                                                                </label>
                                                                <input type="text" name="document_name[]"
                                                                    class="form-control" value="{{ $doc->doc_type }}"
                                                                    readonly />
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Select File</label>
                                                                <input type="file" name="document_file[]"
                                                                    id="document_file{{ $doc->id }}"
                                                                    class="form-control document_file"
                                                                    {{ $doc->mandatory === 'Y' ? 'required' : '' }}
                                                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                                                            </div>
                                                            <div class="col-md-4">
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
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

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
                                        <a href="{{ url()->route('pipeline.bd_handovers') }}"
                                            class="btn btn-info btn-lg">
                                            <i class="bx bx-arrow-back"></i> Return to BD Handovers
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

@push('script')
    <script>
        $(document).ready(function() {
            const CONFIG = {
                approval: @json($approval),
                prospect: @json($prospect),
                pipeid: @json($pipeid),
                opportunityId: @json(request('prospect')),
                routes: {
                    reinsurersFilter: '{{ route('reinsurers.filter') }}',
                    clientStage: '{{ route('client.stage') }}',
                    prospectDocs: '{{ route('prospect.documents') }}',
                }
            };

            let reinsurerTable = null;
            let currentStageData = [];

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function initializeDataTable() {
                if (reinsurerTable) {
                    reinsurerTable.destroy();
                }

                reinsurerTable = $('#reinsurer-table-df').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50, 100, -1],
                        [5, 10, 25, 50, 100, "All"]
                    ],
                    order: [
                        [1, 'asc']
                    ],
                    language: {
                        emptyTable: "No reinsurers found for the selected stage",
                        zeroRecords: "No matching reinsurers found",
                        info: "Showing _START_ to _END_ of _TOTAL_ reinsurers",
                        infoEmpty: "Showing 0 to 0 of 0 reinsurers",
                        infoFiltered: "(filtered from _MAX_ total reinsurers)",
                        search: '<i class="bx bx-search"></i> Search:',
                        lengthMenu: "Show _MENU_ entries",
                        paginate: {
                            first: '<i class="bx bx-chevrons-left"></i>',
                            last: '<i class="bx bx-chevrons-right"></i>',
                            next: '<i class="bx bx-chevron-right"></i>',
                            previous: '<i class="bx bx-chevron-left"></i>'
                        },
                        loadingRecords: "Loading reinsurers...",
                        processing: "Processing..."
                    },
                    columnDefs: [{
                            targets: 0,
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '2%'
                        },
                        {
                            targets: 1,
                            orderable: true,
                            width: '40%'
                        },
                        {
                            targets: 2,
                            orderable: true,
                            type: 'num',
                            className: 'text-end',
                            width: '20%',
                            render: function(data, type, row) {
                                if (type === 'display') {
                                    return data;
                                }
                                return parseFloat(data) || 0;
                            }
                        },
                        {
                            targets: 3,
                            orderable: true,
                            type: 'num',
                            className: 'text-end',
                            width: '20%',
                            render: function(data, type, row) {
                                if (type === 'display') {
                                    return data;
                                }
                                return parseFloat(data) || 0;
                            }
                        },
                        {
                            targets: 4,
                            orderable: false,
                            className: 'text-end',
                            width: '18%',
                        }
                    ],
                    drawCallback: function() {
                        const api = this.api();
                        api.column(0, {
                            search: 'applied',
                            order: 'applied'
                        }).nodes().each(function(cell, i) {
                            cell.innerHTML = i + 1;
                        });

                        updateTotalsFooter();
                    },
                    initComplete: function() {
                        updateQuickStats();
                    }
                });
            }

            function updateTotalsFooter() {
                if (!reinsurerTable || currentStageData.length === 0) {
                    $('#reinsurer-totals').hide();
                    return;
                }

                let totalWritten = 0;
                let totalSigned = 0;

                currentStageData.forEach(item => {
                    totalWritten += parseFloat(item.written_share) || 0;
                    totalSigned += parseFloat(item.signed_share) || 0;
                });

                $('#footer-written-total').text(totalWritten.toFixed(2) + '%');
                $('#footer-signed-total').text(totalSigned.toFixed(2) + '%');
                $('#reinsurer-totals').show();
            }

            function updateQuickStats() {
                if (currentStageData.length === 0) {
                    $('#total-reinsurers').html('<i class="bx bx-building"></i> Total: 0');
                    $('#total-written-share').html('<i class="bx bx-trending-up"></i> Written: 0%');
                    $('#total-signed-share').html('<i class="bx bx-check-circle"></i> Signed: 0%');
                    return;
                }

                let totalWritten = 0;
                let totalSigned = 0;

                currentStageData.forEach(item => {
                    totalWritten += parseFloat(item.written_share) || 0;
                    totalSigned += parseFloat(item.signed_share) || 0;
                });

                $('#total-reinsurers').html(`<i class="bx bx-building"></i> Total: ${currentStageData.length}`);
                $('#total-written-share').html(
                    `<i class="bx bx-trending-up"></i> Written: ${totalWritten.toFixed(2)}%`);
                $('#total-signed-share').html(
                    `<i class="bx bx-check-circle"></i> Signed: ${totalSigned.toFixed(2)}%`);
            }

            function getShareClass(value) {
                const numValue = parseFloat(value);
                if (numValue >= 50) return 'high';
                if (numValue >= 25) return 'medium';
                return 'low';
            }

            function formatReinsurerName(name) {
                return name.split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                    .join(' ');
            }

            $('#stage').on('change', function() {
                const stage = $(this).val();
                const $container = $('#reinsurer-table-container');
                const $tbody = $('#reinsurer-body');

                currentStageData = [];
                updateQuickStats();

                if (!stage) {
                    $('#prospectStageDisplay').hide();
                    $('#prospectStageEmpty').html(`
                        <div class="empty-state-table">
                            <i class="bx bx-file"></i>
                            <p class="mb-0">Please select a stage to load documents</p>
                        </div>
                    `).show();

                    if (reinsurerTable) {
                        reinsurerTable.destroy();
                        reinsurerTable = null;
                    }

                    $tbody.html(`
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state-table">
                                    <i class="bx bx-filter-alt"></i>
                                    <p class="mb-0">Please select a stage to load reinsurers</p>
                                </div>
                            </td>
                        </tr>
                    `);
                    $('#reinsurer-totals').hide();
                    return;
                }

                const loadingHtml = $('#loading-overlay-template').html();
                $container.append(loadingHtml);

                $.ajax({
                    url: CONFIG.routes.reinsurersFilter,
                    type: 'GET',
                    data: {
                        stage: stage,
                        opportunity_id: CONFIG.pipeid
                    },
                    success: function(response) {
                        let rows = '';

                        if (response.reinsurers?.length > 0) {
                            currentStageData = response.reinsurers;

                            response.reinsurers.forEach((item, index) => {
                                const name = formatReinsurerName(item.reinsurer_name);
                                let writtenShare = item.written_share || '--';
                                const signedShare = item.signed_share || '--';
                                const status = item.status || '--';

                                const writtenClass = writtenShare !== '--' ?
                                    getShareClass(writtenShare) : '';
                                const signedClass = signedShare !== '--' ?
                                    getShareClass(signedShare) : '';

                                let prevWrittenShare = 0;
                                if (item.stage == 2) {
                                    prevWrittenShare = item.updated_written_share;
                                }

                                if (item.stage == 3) {
                                    writtenShare = item.updated_written_share;
                                }

                                rows += `
                                    <tr class="reinsurer-row">
                                        <td class="text-center align-middle">
                                            <span class="badge bg-light text-dark">${index + 1}</span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="reinsurer-name">
                                                <strong class="text-dark">${name}</strong>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            ${renderWrittenShare(writtenShare, prevWrittenShare, writtenClass)}
                                        </td>
                                        <td class="text-start">
                                            <span class="badge ${getBadgeClass(signedClass)} font-monospace">
                                                ${signedShare !== '--' ? signedShare + '%' : '<span class="text-muted">—</span>'}
                                            </span>
                                        </td>
                                        <td class="text-start">
                                            <span class="badge ${getStatusBadgeClass(status)} text-light">
                                                <i class="${getStatusIcon(status)} me-1"></i>
                                                ${status ? status.charAt(0).toUpperCase() + status.slice(1) : '--'}
                                            </span>
                                        </td>
                                    </tr>
                                `;
                            });

                            $tbody.html(rows);
                            initializeDataTable();
                            updateQuickStats();
                            renderProspectDocs(stage);

                        } else {
                            if (reinsurerTable) {
                                reinsurerTable.destroy();
                                reinsurerTable = null;
                            }

                            rows = `
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state-table">
                                        <i class="bx bx-search-alt"></i>
                                        <p class="mb-0">No reinsurers found for Stage ${stage}</p>
                                        <small class="text-muted">Try selecting a different stage</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                            $tbody.html(rows);
                            $('#reinsurer-totals').hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        if (reinsurerTable) {
                            reinsurerTable.destroy();
                            reinsurerTable = null;
                        }

                        $tbody.html(`
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="alert alert-danger mb-0">
                                        <i class="bx bx-error"></i>
                                        <strong>Error Loading Data</strong>
                                        <p class="mb-0 mt-2">Failed to load reinsurer data. Please try again.</p>
                                    </div>
                                </td>
                            </tr>
                        `);
                        $('#reinsurer-totals').hide();

                        showToast('error', 'Failed to load reinsurer data');
                    },
                    complete: function() {
                        $container.find('.loading-overlay').remove();
                    }
                });
            });

            function renderProspectDocs(stage) {
                let currentStage = '';
                switch (stage) {
                    case "1":
                        currentStage = 'lead'
                        break;

                    case "2":
                        currentStage = 'proposal'
                        break;

                    case "3":
                        currentStage = 'negotiation'
                        break;

                    case "4":
                        currentStage = 'final_stage'
                        break;

                    default:
                        break;
                }

                const $prospectStageEmpty = $("#prospectStageEmpty");
                const $prospectStageDisplay = $("#prospectStageDisplay");
                const $docStageName = $("#docStageName");

                const stageName = `Stage ${currentStage}`;
                $docStageName.text(stageName);

                $prospectStageEmpty.hide();
                $prospectStageDisplay.empty().append(`
                    <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center">
                        <div class="spinner"></div>
                        <p class="mt-2 text-muted">Loading documents...</p>
                    </div>
                `).show();

                $.ajax({
                    url: CONFIG.routes.prospectDocs,
                    type: 'GET',
                    data: {
                        stage: currentStage,
                        opportunity_id: CONFIG.pipeid
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            if (response.documents && response.documents.length > 0) {
                                let docsHtml = '<div class="row">';

                                response.documents.forEach(function(doc) {
                                    docsHtml += `
                                    <div class="col-6 mb-3">
                                        <div class="document-row">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="document-icon bg-success">
                                                        <i class="bx bx-check"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label mb-0">Document Type</label>
                                                    <div class="fw-bold">${doc.description || 'N/A'}</div>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label mb-0">File Name</label>
                                                    <div class="text-muted small">${doc.file || 'N/A'}</div>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <a href="${doc.file_url}"
                                                    target="_blank"
                                                    class="btn btn-info btn-document-action">
                                                        <i class="bx bx-show"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                });

                                docsHtml += '</div>';
                                $prospectStageDisplay.html(docsHtml).show();
                                $prospectStageEmpty.hide();
                            } else {
                                $prospectStageDisplay.hide();
                                $prospectStageEmpty.html(`
                                    <div class="empty-state-table">
                                        <i class="bx bx-folder-open"></i>
                                        <p class="mb-0">No documents uploaded for ${stageName}</p>
                                    </div>
                                `).show();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        $prospectStageDisplay.hide();
                        $prospectStageEmpty.html(`
                        <div class="alert alert-danger mb-0">
                            <i class="bx bx-error"></i>
                            <strong>Error Loading Documents</strong>
                            <p class="mb-0 mt-2">Failed to load documents. Please try again.</p>
                        </div>
                    `).show();

                        showToast('error', 'Failed to load stage documents');
                    }
                });
            }

            function renderWrittenShare(writtenShare, prevWrittenShare, writtenClass) {
                const currentShare = writtenShare ?? '--';
                const previousShare = prevWrittenShare ?? 0;

                if (previousShare != 0) {
                    return `
                        <div class="share-history">
                            <small class="text-muted d-block mb-1">Change:</small>
                            <span class="badge badge-soft-${writtenClass} mb-1 text-primary">
                                ${currentShare}%
                            </span>
                            <i class="bx bx-right-arrow-alt mx-1 text-muted"></i>
                            <span class="badge ${getBadgeClass(writtenClass)}">
                                ${previousShare}%
                            </span>
                        </div>
                    `;
                }

                const displayValue = currentShare !== '--' ?
                    `${currentShare}%` :
                    '<span class="text-muted">—</span>';

                return `
                    <span class="badge ${getBadgeClass(writtenClass)} font-monospace">
                        ${displayValue}
                    </span>
                `;
            }

            function getBadgeClass(shareClass) {
                const classMap = {
                    'high': 'bg-success',
                    'medium': 'bg-warning',
                    'low': 'bg-info',
                    'zero': 'bg-secondary',
                    'written': 'bg-primary',
                    'signed': 'bg-success'
                };
                return classMap[shareClass] || 'bg-secondary';
            }

            function getStatusBadgeClass(status) {
                const statusMap = {
                    'written': 'bg-info text-dark',
                    'signed': 'bg-success',
                    'quoted': 'bg-warning text-dark',
                    'declined': 'bg-danger',
                    'pending': 'bg-secondary',
                    'approved': 'bg-primary',
                    'active': 'bg-success',
                    'inactive': 'bg-secondary'
                };
                return statusMap[status?.toLowerCase()] || 'bg-secondary';
            }

            function getStatusIcon(status) {
                const iconMap = {
                    'written': 'bx bx-edit',
                    'signed': 'bx bx-check-circle',
                    'quoted': 'bx bx-file',
                    'declined': 'bx bx-x-circle',
                    'pending': 'bx bx-time',
                    'approved': 'bx bx-check-double'
                };
                return iconMap[status?.toLowerCase()] || 'bx bx-circle';
            }

            function showToast(type, message) {
                const iconMap = {
                    success: 'bx-check-circle',
                    error: 'bx-error-circle',
                    info: 'bx-info-circle',
                    warning: 'bx-error'
                };

                const bgMap = {
                    success: 'success',
                    error: 'danger',
                    info: 'info',
                    warning: 'warning'
                };

                const toast = `
                    <div class="toast align-items-center text-white bg-${bgMap[type]} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bx ${iconMap[type]} me-2"></i>
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;

                $('body').append(toast);
                const $toast = $('.toast').last();
                const bsToast = new bootstrap.Toast($toast[0], {
                    delay: 3000
                });
                bsToast.show();

                $toast.on('hidden.bs.toast', function() {
                    $(this).remove();
                });
            }

            function updateExcessLabel() {
                const excessType = $('#excess_type').val();
                const label = excessType === 'R' ? 'Excess (%)' : 'Excess Amount';
                $('#excess_label').html(label + ' <span class="required-indicator">*</span>');
            }

            $('#excess_type').on('change', updateExcessLabel);
            updateExcessLabel();

            $('#effective_date').on('change', function() {
                const effectiveDate = $(this).val();
                if (effectiveDate) {
                    const date = new Date(effectiveDate);
                    date.setFullYear(date.getFullYear() + 1);
                    date.setDate(date.getDate() - 1);
                    $('#closing_date').val(date.toISOString().split('T')[0]);
                }
            });

            $(document).on('click', '.preview', function() {
                const $row = $(this).closest('.document-row');
                const fileInput = $row.find('input[type="file"]')[0];
                const file = fileInput?.files[0];

                if (!file) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please select a file first before previewing',
                        confirmButtonColor: '#f39c12'
                    });
                    return;
                }

                const fileURL = URL.createObjectURL(file);
                const $iframe = $('#preview_iframe');
                const $image = $('#preview_image');
                const $error = $('#preview_error');

                $iframe.hide();
                $image.hide();
                $error.hide();

                if (file.type === 'application/pdf') {
                    $iframe.attr('src', fileURL).show();
                } else if (file.type.startsWith('image/')) {
                    $image.attr('src', fileURL).show();
                } else {
                    $error.show();
                }

                $('#v_docs').modal('show');
            });

            $('#v_docs').on('hidden.bs.modal', function() {
                const $iframe = $('#preview_iframe');
                const $image = $('#preview_image');
                const iframeSrc = $iframe.attr('src');
                const imageSrc = $image.attr('src');

                if (iframeSrc && iframeSrc.startsWith('blob:')) {
                    URL.revokeObjectURL(iframeSrc);
                }
                if (imageSrc && imageSrc.startsWith('blob:')) {
                    URL.revokeObjectURL(imageSrc);
                }

                $iframe.attr('src', '');
                $image.attr('src', '');
            });

            let fileCounter = 0;
            $(document).on('click', '.addDocfac', function(e) {
                e.preventDefault();
                fileCounter++;

                const newRow = `
                    <div class="document-row new-document-row" style="animation: slideIn 0.3s ease;">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="document-icon">
                                    <i class="bx bx-plus"></i>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Document Type</label>
                                <input type="text" name="document_name[]" class="form-control"
                                       placeholder="Enter document name" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Select File</label>
                                <input type="file" name="document_file[]" class="form-control document_file"
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required />
                            </div>
                            <div class="col-md-4">
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

            $(document).on('click', '.remove-file', function() {
                $(this).closest('.new-document-row').fadeOut(300, function() {
                    $(this).remove();
                });
            });

            $('#submit').on('click', function(e) {
                e.preventDefault();

                clearValidationErrors();

                const validationErrors = validateForm();

                if (validationErrors.length > 0) {
                    displayValidationErrors(validationErrors);
                    return;
                }

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
                    cancelButtonColor: '#95a5a6',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            });

            function validateForm() {
                const errors = [];

                const offeredDate = $('input[name="offered_date"]').val();
                if (!offeredDate || offeredDate.trim() === '') {
                    errors.push({
                        field: 'offered_date',
                        message: 'Offered Date is required',
                        element: $('input[name="offered_date"]')
                    });
                }

                const excessType = $('#excess_type').val();
                if (!excessType || excessType.trim() === '') {
                    errors.push({
                        field: 'excess_type',
                        message: 'Excess Type is required',
                        element: $('#excess_type')
                    });
                }

                const excess = $('#excess').val();
                if (!excess || excess.trim() === '') {
                    errors.push({
                        field: 'excess',
                        message: 'Excess value is required',
                        element: $('#excess')
                    });
                } else if (isNaN(excess) || parseFloat(excess) < 0) {
                    errors.push({
                        field: 'excess',
                        message: 'Excess must be a valid positive number',
                        element: $('#excess')
                    });
                } else if (excessType === 'R' && parseFloat(excess) > 100) {
                    errors.push({
                        field: 'excess',
                        message: 'Excess rate cannot exceed 100%',
                        element: $('#excess')
                    });
                }

                const maxMin = $('input[name="max_min"]').val();
                if (!maxMin || maxMin.trim() === '') {
                    errors.push({
                        field: 'max_min',
                        message: 'Value is required',
                        element: $('input[name="max_min"]')
                    });
                } else if (isNaN(maxMin) || parseFloat(maxMin) < 0) {
                    errors.push({
                        field: 'max_min',
                        message: 'Value must be a valid positive number',
                        element: $('input[name="max_min"]')
                    });
                }

                const range = $('input[name="range"]:checked').val();
                if (!range) {
                    errors.push({
                        field: 'range',
                        message: 'Please select either Minimum or Maximum',
                        element: $('input[name="range"]').first()
                    });
                }

                const effectiveDate = $('input[name="effective_date"]').val();
                if (!effectiveDate || effectiveDate.trim() === '') {
                    errors.push({
                        field: 'effective_date',
                        message: 'Cover Start Date is required',
                        element: $('input[name="effective_date"]')
                    });
                }

                const closingDate = $('input[name="closing_date"]').val();
                if (!closingDate || closingDate.trim() === '') {
                    errors.push({
                        field: 'closing_date',
                        message: 'Cover End Date is required',
                        element: $('input[name="closing_date"]')
                    });
                }

                if (effectiveDate && closingDate) {
                    const startDate = new Date(effectiveDate);
                    const endDate = new Date(closingDate);

                    if (endDate <= startDate) {
                        errors.push({
                            field: 'closing_date',
                            message: 'Cover End Date must be after Cover Start Date',
                            element: $('input[name="closing_date"]')
                        });
                    }
                }

                const handler = $('select[name="handler"]').val();
                if (!handler || handler.trim() === '') {
                    errors.push({
                        field: 'handler',
                        message: 'Account Handler is required',
                        element: $('select[name="handler"]')
                    });
                }

                const approvers = $('select[name="approver[]"]').val();
                if (!approvers || approvers.length === 0) {
                    errors.push({
                        field: 'approver',
                        message: 'At least one Approver must be selected',
                        element: $('select[name="approver[]"]')
                    });
                }

                const mandatoryDocs = [];
                $('.document-row').each(function() {
                    const $row = $(this);
                    const $fileInput = $row.find('input[type="file"]');

                    if ($fileInput.length && $fileInput.prop('required')) {
                        const fileName = $fileInput.val();
                        const docName = $row.find('input[name="document_name[]"]').val();

                        if (!fileName || fileName.trim() === '') {
                            mandatoryDocs.push(docName || 'Unknown Document');
                            errors.push({
                                field: $fileInput.attr('id') || 'document_file',
                                message: `Document "${docName}" is required`,
                                element: $fileInput
                            });
                        }
                    }
                });

                $('input[type="file"]').each(function() {
                    const files = this.files;
                    if (files.length > 0) {
                        const file = files[0];
                        const maxSize = 10 * 1024 * 1024;
                        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ];

                        if (file.size > maxSize) {
                            errors.push({
                                field: $(this).attr('id') || 'document_file',
                                message: `File "${file.name}" exceeds 10MB limit`,
                                element: $(this)
                            });
                        }

                        if (!allowedTypes.includes(file.type)) {
                            errors.push({
                                field: $(this).attr('id') || 'document_file',
                                message: `File "${file.name}" has invalid format. Allowed: PDF, JPG, PNG, DOC, DOCX`,
                                element: $(this)
                            });
                        }
                    }
                });

                $('.new-document-row').each(function() {
                    const $row = $(this);
                    const docName = $row.find('input[name="document_name[]"]').val();
                    const $fileInput = $row.find('input[type="file"]');

                    if (!docName || docName.trim() === '') {
                        errors.push({
                            field: 'document_name',
                            message: 'Document name is required for additional documents',
                            element: $row.find('input[name="document_name[]"]')
                        });
                    }

                    if (!$fileInput.val() || $fileInput.val().trim() === '') {
                        errors.push({
                            field: 'document_file',
                            message: `File is required for document "${docName || 'Unnamed'}"`,
                            element: $fileInput
                        });
                    }
                });

                return errors;
            }

            function displayValidationErrors(errors) {
                if (errors.length > 0 && errors[0].element) {
                    $('html, body').animate({
                        scrollTop: errors[0].element.offset().top - 100
                    }, 500);
                }

                errors.forEach(error => {
                    if (error.element) {
                        error.element.addClass('is-invalid');

                        error.element.siblings('.invalid-feedback').remove();

                        error.element.after(`
                            <div class="invalid-feedback d-block" style="color: var(--reins-accent); font-size: 0.875rem; margin-top: 0.25rem;">
                                <i class="bx bx-error-circle"></i> ${error.message}
                            </div>
                        `);

                        error.element.css('border-color', 'var(--reins-accent)');
                    }
                });

                const errorList = errors.map(err => `<li class="text-start">${err.message}</li>`).join('');

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: `
                        <div class="text-start">
                            <p><strong>Please fix the following errors:</strong></p>
                            <ul style="max-height: 300px; overflow-y: auto;">
                                ${errorList}
                            </ul>
                        </div>
                    `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#e74c3c',
                    customClass: {
                        popup: 'validation-error-popup'
                    }
                });
            }

            function clearValidationErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                $('input, select, textarea').css('border-color', '');
            }

            function submitForm() {
                const form = $('#msform')[0];
                const formData = new FormData(form);
                const $submitBtn = $('#submit');

                $submitBtn
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Submitting...')
                    .prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: CONFIG.routes.clientStage,
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 60000,
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
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then(() => {
                                window.location.href = '/bd-handovers';
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
                    error: function(xhr, status, error) {
                        let errorMessage = 'An error occurred during submission';
                        let validationErrors = [];

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            validationErrors = Object.keys(errors).map(field => ({
                                field: field,
                                message: errors[field][0],
                                element: $(`[name="${field}"]`).length ? $(
                                    `[name="${field}"]`) : $(`#${field}`)
                            }));

                            displayValidationErrors(validationErrors);
                            errorMessage = 'Please check the highlighted fields';
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error. Please contact support';
                        } else if (status === 'timeout') {
                            errorMessage = 'Request timeout. Please try again';
                        }

                        if (validationErrors.length === 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Error',
                                html: errorMessage,
                                confirmButtonColor: '#e74c3c'
                            });
                        }

                        resetSubmitButton();
                    }
                });
            }

            function resetSubmitButton() {
                $('#submit')
                    .html('<i class="bx bx-transfer"></i> Submit for Handover')
                    .prop('disabled', false);
            }

            function initializeRealTimeValidation() {
                $('input[required], select[required], textarea[required]').on('input change', function() {
                    const $field = $(this);
                    if ($field.hasClass('is-invalid')) {
                        $field.removeClass('is-invalid');
                        $field.siblings('.invalid-feedback').fadeOut(300, function() {
                            $(this).remove();
                        });
                        $field.css('border-color', '');
                    }
                });

                $('input[name="range"]').on('change', function() {
                    $('input[name="range"]').removeClass('is-invalid');
                    $('input[name="range"]').siblings('.invalid-feedback').remove();
                });

                $('input[type="file"]').on('change', function() {
                    const $input = $(this);
                    $input.removeClass('is-invalid');
                    $input.siblings('.invalid-feedback').remove();
                    $input.css('border-color', '');

                    const fileName = $(this).val().split('\\').pop();
                    if (fileName) {
                        console.log(`File selected: ${fileName}`);
                    }
                });
            }

            initializeDataTable();

            initializeRealTimeValidation();
        });
    </script>
@endpush
