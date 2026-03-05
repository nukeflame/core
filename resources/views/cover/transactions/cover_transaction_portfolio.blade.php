@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-blue-dark: #1d4ed8;
            --success-green: #059669;
            --warning-amber: #f59e0b;
            --danger-red: #ef4444;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border-light: #e5e7eb;
            --bg-subtle: #f9fafb;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .section-title {
            color: var(--primary-blue);
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            padding: 1.25rem;
            border-left: 2px solid var(--primary-blue);
            border-right: 2px solid var(--primary-blue);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .summary-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 0.9375rem;
            color: var(--text-dark);
            font-weight: 600;
        }

        .summary-value.highlight {
            color: var(--primary-blue);
            font-size: 1.125rem;
        }

        .summary-value.amount {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            color: var(--success-green);
        }

        .financial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .financial-card {
            padding: 1.25rem;
            border-radius: 10px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .financial-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .financial-card.debits {
            background: linear-gradient(135deg, #6c757d 0%, #aaa 100%);

        }

        .financial-card.commission {
            background: linear-gradient(135deg, #6c757d 0%, #aaa 100%);
        }

        .financial-card.portfolio {
            background: linear-gradient(135deg, #6c757d 0%, #aaa 100%);
        }

        .financial-card.adjustments {
            background: linear-gradient(135deg, #6c757d 0%, #aaa 100%);
            =
        }

        .financial-label {
            font-size: 0.8125rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .financial-value {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
        }

        .custom-tabs {
            border-bottom: 2px solid var(--border-light);
            margin-bottom: 1.5rem;
            gap: 0.5rem;
        }

        .custom-tabs .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            padding: 0.875rem 1.25rem;
            border: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 8px 8px 0 0;
        }

        .custom-tabs .nav-link:hover {
            color: var(--primary-blue);
            background-color: rgba(37, 99, 235, 0.05);
        }

        .custom-tabs .nav-link.active {
            color: var(--primary-blue);
            border-bottom-color: var(--primary-blue);
            background: transparent;
        }

        .custom-tabs .nav-link .badge {
            font-size: 0.6875rem;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
        }

        .status-badge {
            font-size: 0.6875rem;
            font-weight: 600;
            padding: 0.375rem 0.625rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-badge--success {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-badge--warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-badge--danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-badge--info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-badge--primary {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-badge--secondary {
            background-color: #fce7f3;
            color: #9d174d;
        }

        .type-badge {
            font-size: 0.6875rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-transform: capitalize;
        }

        .type-badge--surplus {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        .type-badge--quota-share {
            background-color: #d1fae5;
            color: #065f46;
        }

        .type-badge--excess-of-loss {
            background-color: #fef3c7;
            color: #92400e;
        }

        .type-badge--premium {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .dataTables_wrapper {
            padding-top: 0.5rem;
        }

        .dataTables_filter input {
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .dataTables_filter input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .dataTables_length select {
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 0.375rem 0.5rem;
        }

        /* Action Buttons */
        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        .btn-action:hover {
            transform: translateY(-1px);
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .quick-action-btn {
            padding: 0.45rem 29px;
            font-size: 14px;
            border-radius: 0.25rem;
            transition: all 0.2s ease;
            font-weight: 500;
            align-items: center;
        }

        .quick-action-btn i {
            vertical-align: -1px;
        }

        /* Tab Header */
        .tab-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .tab-header h6 {
            margin: 0;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.4;
        }

        /* Cedant Info Card */
        .cedant-info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .cedant-info-card .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed var(--border-light);
        }

        .cedant-info-card .info-row:last-child {
            border-bottom: none;
        }

        .cedant-info-card .info-label {
            color: var(--text-muted);
            font-size: 0.8125rem;
        }

        .cedant-info-card .info-value {
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.875rem;
        }

        /* File Upload Area */
        .file-upload-area {
            border: 2px dashed var(--border-light);
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: var(--primary-blue);
            background-color: rgba(37, 99, 235, 0.02);
        }

        .file-upload-area i {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 0.75rem;
        }

        /* Clause Item */
        .clause-item {
            background: white;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }

        .clause-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .clause-item .clause-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .clause-item .clause-text {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Amount cell styling */
        .amount-cell {
            font-weight: 500;
        }

        .amount-cell--positive {
            color: var(--success-green);
        }

        .amount-cell--negative {
            color: var(--danger-red);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .quick-actions {
                flex-direction: column;
            }

            .quick-action-btn {
                width: 100%;
                justify-content: center;
            }

            .financial-grid {
                grid-template-columns: 1fr 1fr;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .financial-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Spinning animation for refresh buttons */
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Pulse animation for real-time update indicator */
        .pulse-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $cover = $cover ?? null;
        $customer = $customer ?? null;
        $debitItems = $debitItems ?? collect();
        $reinsurers = $reinsurers ?? collect();
        $totalReinsurers = $totalReinsurers ?? $reinsurers->count();
        $cedantDetails = $cedantDetails ?? null;
        $totalDocuments = $totalDocuments ?? 0;

        $totalGrossPremium = $debitItems->sum('gross_premium');
        $totalCommission = $debitItems->sum('commission_amount');
        $totalNetAmount = $debitItems->sum('net_amount');
        $formatCurrency = fn($amount, $currency = 'KES') => $currency . ' ' . number_format($amount ?? 0, 2);
    @endphp

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-1">Portfolio Statement</h1>
            <p class="text-muted mb-0 fw-medium">
                {{ $cover->cover_no ?? 'N/A' }} - {{ $cover->cover_title ?? ($cover->treaty_name ?? 'Untitled') }}
            </p>
        </div>
        <div class="ms-md-1 ms-0 mt-3 mt-md-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') ?? '#' }}">Covers</a></li>
                    <li class="breadcrumb-item"><a href="#">Treaty</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $cover->cover_no ?? 'Details' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-lightning-charge-fill"></i> Quick Actions
            </h6>
        </div>

        <div class="card-body mx-0 cover-info-wrapper" style="background-color:var(--cover-bg);border-radius:0.375rem;">
            <button type="button" class="btn btn-outline-dark btn-sm text-start me-2">
                <i class="ri-exchange-line me-2"></i>Transactions
            </button>
        </div>
    </div>

    {{-- <div class="financial-grid">
        <div class="financial-card debits">
            <div class="financial-label">Total Gross Premium</div>
            <div class="financial-value" id="summaryGrossPremium">
                {{ $formatCurrency($totalGrossPremium, $cover->currency ?? 'KES') }}
            </div>
        </div>
        <div class="financial-card commission">
            <div class="financial-label">Total Commission</div>
            <div class="financial-value" id="summaryCommission">
                {{ $formatCurrency($totalCommission, $cover->currency ?? 'KES') }}
            </div>
        </div>
        <div class="financial-card portfolio">
            <div class="financial-label">Net Amount Due</div>
            <div class="financial-value" id="summaryNetAmount">
                {{ $formatCurrency($totalNetAmount, $cover->currency ?? 'KES') }}
            </div>
        </div>
        <div class="financial-card adjustments">
            <div class="financial-label">Total Reinsurers</div>
            <div class="financial-value" id="summaryTotalReinsurers">
                {{ number_format($totalReinsurers) }}
            </div>
        </div>
    </div> --}}

    <div class="summary-card mb-4">
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Transaction Type</span>
                <span class="summary-value highlight">{{ $currentEntryTypeDisplay ?? 'Portfolio' }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Treaty Type</span>
                <span class="summary-value">{{ $cover->treaty_type ?? 'N/A' }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Underwriting Year</span>
                <span class="summary-value">{{ $cedantDetails->treaty_year ?? date('Y') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Policy Period</span>
                <span class="summary-value">
                    @if ($cover && $cover->cover_from && $cover->cover_to)
                        {{ \Carbon\Carbon::parse($cover->cover_from)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($cover->cover_to)->format('d M Y') }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Current Quarter</span>
                <span
                    class="summary-value">{{ $currentQuarterDisplay ?? 'Q' . now()->quarter . ' - ' . now()->year }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Status</span>
                @php
                    $isActive = in_array($cover->status ?? '', ['A', 'active', 'Active']);
                @endphp
                <span class="status-badge {{ $isActive ? 'status-badge--success' : 'status-badge--warning' }}">
                    {{ $isActive ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <div class="row-cols-12">
        <div class="card mb-2 custom-card border col">
            <div class="card-body pt-0">
                <nav>
                    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                        id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-debit-items-tab" data-bs-toggle="tab"
                            data-bs-target="#debit-items-tab" type="button" role="tab" aria-controls="debit-items-tab"
                            aria-selected="true">
                            <i class="bx bx-table me-1 align-middle"></i>Debit Items
                            <span class="badge bg-primary ms-1" id="debitItemsCount">{{ $totalDebitItems }}</span>
                        </button>

                        {{-- <button class="nav-link" id="nav-credit-items-tab" data-bs-toggle="tab"
                            data-bs-target="#credit-items-tab" type="button" role="tab"
                            aria-controls="credit-items-tab" aria-selected="false">
                            <i class="bx bx-table me-1 align-middle"></i>Credit Items
                            <span class="badge bg-danger ms-1" id="creditItemsCount">0</span>
                        </button> --}}

                        <button class="nav-link" id="nav-reinsurers-tab" data-bs-toggle="tab"
                            data-bs-target="#reinsurers-tab" type="button" role="tab" aria-controls="reinsurers-tab"
                            aria-selected="false">
                            <i class="ri-building-2-line me-1"></i> Reinsurers
                            <span class="badge bg-info ms-1" id="reinsurersCount">{{ $totalReinsurers }}</span>
                        </button>

                        <button class="nav-link" id="nav-cedant-tab" data-bs-toggle="tab" data-bs-target="#cedant-tab"
                            type="button" role="tab" aria-controls="cedant-tab" aria-selected="false">
                            <i class="bx bx-briefcase me-1"></i> Cedant
                        </button>

                        <button class="nav-link" id="nav-approvals-tab" data-bs-toggle="tab" data-bs-target="#approvals-tab"
                            type="button" role="tab" aria-controls="approvals-tab" aria-selected="false">
                            <i class="bx bx-medal me-1 align-middle"></i>Approvals
                            <span class="badge bg-warning ms-1"></span>
                        </button>

                        <button class="nav-link" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#docs-tab"
                            type="button" role="tab" aria-controls="docs-tab" aria-selected="false">
                            <i class="ri-printer-line me-1 align-middle"></i>Print-outs
                            <span class="badge bg-success ms-1" id="documentsCount">{{ $totalDocuments }}</span>
                        </button>
                    </div>
                </nav>

                <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                    {{-- Debit Items Tab --}}
                    <div class="tab-pane fade show active" id="debit-items-tab" role="tabpanel"
                        aria-labelledby="nav-debit-items-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
                                    <table id="debitItemsTable" class="table table-bordered table-hover w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="4%">#</th>
                                                <th width="12%">Item Number</th>
                                                <th width="12%">Transaction Type</th>
                                                <th width="10%">Date</th>
                                                <th width="10%">Quarter</th>
                                                <th width="10%">Treaty Type</th>
                                                <th width="10%">Class</th>
                                                <th width="10%">Commission %</th>
                                                <th width="10%">Gross Amount</th>
                                                <th width="7%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="table-light">
                                            <tr class="fw-bold">
                                                <td colspan="8" class="text-end">Totals:</td>
                                                <td class="amount-cell amount-cell--positive" id="totalAmount">-</td>
                                                <td colspan="1"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reinsurers Tab --}}
                    <div class="tab-pane fade" id="reinsurers-tab" role="tabpanel" aria-labelledby="nav-reinsurers-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
                                    <table id="reinsurersTable" class="table table-bordered table-hover w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="3%">#</th>
                                                <th width="15%">Reinsurer</th>
                                                <th width="7%">Share %</th>
                                                <th width="9%">Gross Premium</th>
                                                <th width="9%">Commission</th>
                                                <th width="9%">Prem. Tax Amount</th>
                                                <th width="9%">WHT Amount</th>
                                                <th width="9%">RI Tax</th>
                                                <th width="9%">Net Amount</th>
                                                <th width="7%">Status</th>
                                                <th width="10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="table-light">
                                            <tr class="fw-bold">
                                                <td colspan="2" class="text-end">Totals:</td>
                                                <td class="amount-cell" id="totalSharePercent">-</td>
                                                <td class="amount-cell" id="totalGrossPremium">-</td>
                                                <td class="amount-cell" id="totalCommission">-</td>
                                                <td class="amount-cell" id="totalPremiumTax">-</td>
                                                <td class="amount-cell" id="totalWHT">-</td>
                                                <td class="amount-cell" id="totalRITax">-</td>
                                                <td class="amount-cell amount-cell--positive" id="totalNetAmount">-</td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Cedant Tab --}}
                    <div class="tab-pane fade" id="cedant-tab" role="tabpanel" aria-labelledby="nav-cedant-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
                                    <table id="cedantTable" class="table table-bordered table-hover w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="3%">#</th>
                                                <th width="15%">Company Name</th>
                                                <th width="10%">Registration No.</th>
                                                <th width="15%">Address</th>
                                                <th width="12%">Contact Person</th>
                                                <th width="12%">Email</th>
                                                <th width="10%">Phone</th>
                                                <th width="12%">Treaty Period</th>
                                                <th width="10%">Treaty Capacity</th>
                                                <th width="7%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Approvals Tab --}}
                    <div class="tab-pane fade" id="approvals-tab" role="tabpanel" aria-labelledby="nav-approvals-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                <table id="approvalsTable" class="table table-bordered table-hover w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Approver</th>
                                            <th scope="col">Comment</th>
                                            <th scope="col">Approver Comment</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Approval Time</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Documents Tab --}}
                    <div class="tab-pane fade" id="docs-tab" role="tabpanel" aria-labelledby="nav-docs-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-header bg-transparent border-0 p-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 w-100">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="ri-printer-line text-success me-1"></i>Generated Documents
                                    </h6>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            id="btnRefreshDocs">
                                            <i class="ri-refresh-line"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-file-add-line me-1"></i>Generate Document
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 230px;">
                                                <li>
                                                    <h6 class="dropdown-header text-dark">Financial Documents</h6>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-doc-type="debit_note">
                                                        <i class="ri-file-text-line me-2 text-primary"></i>Debit Note
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-doc-type="credit_note">
                                                        <i class="ri-file-text-line me-2 text-success"></i>Credit Note
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-3 px-2">
                                {{-- Document Stats --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-md-2 col-4">
                                        <div class="text-center p-2 border rounded-3">
                                            <div class="fs-5 fw-bold text-primary" id="docGenerated">0</div>
                                            <div class="text-muted small">Generated</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <div class="text-center p-2 border rounded-3">
                                            <div class="fs-5 fw-bold text-info" id="docSent">0</div>
                                            <div class="text-muted small">Sent</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <div class="text-center p-2 border rounded-3">
                                            <div class="fs-5 fw-bold text-success" id="docSigned">0</div>
                                            <div class="text-muted small">Signed</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="documentsTable" class="table table-bordered table-hover w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="15%">Document Type</th>
                                                <th width="12%">Reference</th>
                                                <th width="23%">Description</th>
                                                <th width="12%">Generated Date</th>
                                                <th width="12%">Generated By</th>
                                                <th width="10%">Status</th>
                                                <th width="11%">Actions</th>
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
    </div>

    {{-- Add Debit Item Modal --}}
    {{-- @include('treaty.partials.add-debit-item-modal', ['cover' => $cover]) --}}

    {{-- View Item Modal --}}
    {{-- @include('treaty.partials.view-item-modal') --}}

    <div class="modal fade" id="sendStatementEmailModal" tabindex="-1" aria-labelledby="sendStatementEmailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendStatementEmailModalLabel">
                        <i class="ri-mail-send-line me-2 text-info"></i>Send Statement Email
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="sendStatementEmailForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="statementEmailTo" class="form-label">To</label>
                                <input type="email" id="statementEmailTo" class="form-control"
                                    placeholder="recipient@example.com" required>
                            </div>
                            <div class="col-12">
                                <label for="statementEmailCc" class="form-label">CC</label>
                                <input type="text" id="statementEmailCc" class="form-control"
                                    placeholder="Optional: cc1@example.com, cc2@example.com">
                            </div>
                            <div class="col-12">
                                <label for="statementEmailSubject" class="form-label">Subject</label>
                                <input type="text" id="statementEmailSubject" class="form-control"
                                    placeholder="Statement subject" required>
                            </div>
                            <div class="col-12">
                                <label for="statementEmailBody" class="form-label">Message Draft</label>
                                <textarea id="statementEmailBody" class="form-control" rows="10"
                                    placeholder="Write your statement email message here..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">
                            <i class="ri-send-plane-line me-1"></i>Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            'use strict';

            var coverId = '{{ $cover->id ?? 0 }}';

            var CONFIG = {
                coverNo: '{{ $cover->cover_no ?? '' }}',
                refNo: '{{ request()->route('refNo') ?? '' }}',
                endorsementNo: '{{ $cover->endorsement_no ?? '' }}',
                currency: '{{ $cover->currency ?? 'KES' }}',
                csrfToken: '{{ csrf_token() }}',
                routes: {
                    debitItems: '{{ route('treaty.debit-items.index', ['cover' => $cover->id ?? 0]) }}',
                    creditItems: '{{ route('treaty.credit-items.index', ['cover' => $cover->id ?? 0]) }}',
                    debitItemStore: '{{ route('treaty.debit-items.store') }}',
                    debitItemShow: '{{ url('treaty/debit-items') }}/:id',
                    debitItemUpdate: '{{ url('treaty/debit-items') }}/:id',
                    debitItemDelete: '{{ url('treaty/debit-items') }}/:id',
                    reinsurers: '{{ route('treaty.reinsurers.index', ['cover' => $cover->id ?? 0]) }}',
                    reinsurersList: '{{ route('treaty.reinsurers.list', ['cover' => $cover->id ?? 0], false) ?? '' }}',
                    cedantDetails: '{{ route('treaty.cedant.show', ['cover' => $cover->id ?? 0], false) ?? '' }}',
                    documents: '{{ route('treaty.documents.index', ['cover' => $cover->id ?? 0]) }}',
                    documentGenerate: '{{ route('treaty.documents.generate') }}',
                    documentDownload: '{{ url('treaty/documents') }}/:id/download',
                    previewSlip: '{{ route('treaty.slip.preview', ['cover' => $cover->id ?? 0]) }}',
                    generateStatement: '{{ route('treaty.statement.generate', ['cover' => $cover->id ?? 0]) }}',
                    exportData: '{{ route('treaty.export', ['cover' => $cover->id ?? 0]) }}',
                    summaryStats: '{{ route('treaty.summary-stats') }}',
                    summaryStats: '{{ route('treaty.summary-stats') }}',
                    reinsurerCreditNoteView: '{{ route('treaty.reinsurers.credit-note.view') }}',
                    cedantDebitNoteView: '{{ route('treaty.cedant.debit-note.view') }}'
                },
                dataTables: {
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    dom: '<"row mx-0"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
                }
            };

            var Utils = {
                formatCurrency: function(amount, currency) {
                    currency = currency || CONFIG.currency;
                    if (amount === null || amount === undefined || isNaN(amount)) {
                        return '-';
                    }
                    return currency + ' ' + parseFloat(amount).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                formatDate: function(dateString) {
                    if (!dateString) return '-';
                    try {
                        var date = new Date(dateString);
                        if (isNaN(date.getTime())) return '-';
                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    } catch (e) {
                        console.error('Date formatting error:', e);
                        return '-';
                    }
                },

                formatPercentage: function(value) {
                    if (value === null || value === undefined || isNaN(value)) {
                        return '-';
                    }
                    return parseFloat(value).toFixed(2) + '%';
                },

                getStatusBadge: function(status) {
                    var statusConfig = {
                        paid: {
                            modifier: 'success',
                            label: 'Paid'
                        },
                        approved: {
                            modifier: 'success',
                            label: 'Approved'
                        },
                        completed: {
                            modifier: 'success',
                            label: 'Completed'
                        },
                        active: {
                            modifier: 'success',
                            label: 'Active'
                        },
                        pending: {
                            modifier: 'warning',
                            label: 'Pending'
                        },
                        in_progress: {
                            modifier: 'warning',
                            label: 'In Progress'
                        },
                        calculated: {
                            modifier: 'warning',
                            label: 'Calculated'
                        },
                        overdue: {
                            modifier: 'danger',
                            label: 'Overdue'
                        },
                        rejected: {
                            modifier: 'danger',
                            label: 'Rejected'
                        },
                        generated: {
                            modifier: 'info',
                            label: 'Generated'
                        },
                        sent: {
                            modifier: 'primary',
                            label: 'Sent'
                        },
                        signed: {
                            modifier: 'secondary',
                            label: 'Signed'
                        }
                    };

                    var normalizedStatus = (status || '').toLowerCase().trim();
                    var config = statusConfig[normalizedStatus] || {
                        modifier: 'warning',
                        label: status || 'Unknown'
                    };

                    return '<span class="status-badge status-badge--' + config.modifier + '">' + this
                        .escapeHtml(config.label) + '</span>';
                },

                getTypeBadge: function(type) {
                    var typeConfig = {
                        surplus: 'surplus',
                        'quota share': 'quota-share',
                        quota_share: 'quota-share',
                        'excess of loss': 'excess-of-loss',
                        excess_of_loss: 'excess-of-loss',
                        premium: 'premium'
                    };

                    var normalizedType = (type || '').toLowerCase().trim();
                    var modifier = typeConfig[normalizedType] || 'premium';

                    return '<span class="type-badge type-badge--' + modifier + '">' + this.escapeHtml(
                        type || '-') + '</span>';
                },

                escapeHtml: function(text) {
                    if (!text) return '';
                    var div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                },

                showToast: function(message, type) {
                    type = type || 'success';
                    toastr[type](this.escapeHtml(message), type.toUpperCase());
                },

                showConfirm: function(title, text, confirmCallback) {
                    var self = this;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: self.escapeHtml(title),
                            text: text,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, proceed',
                            cancelButtonText: 'Cancel'
                        }).then(function(result) {
                            if (result.isConfirmed && typeof confirmCallback === 'function') {
                                confirmCallback();
                            }
                        });
                    } else if (confirm(text)) {
                        if (typeof confirmCallback === 'function') {
                            confirmCallback();
                        }
                    }
                },

                showLoading: function(show) {
                    show = show !== false;
                    if (typeof Swal !== 'undefined') {
                        if (show) {
                            Swal.fire({
                                title: 'Processing...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: function() {
                                    Swal.showLoading();
                                }
                            });
                        } else {
                            Swal.close();
                        }
                    }
                },

                ajax: function(options) {
                    var defaults = {
                        headers: {
                            'X-CSRF-TOKEN': CONFIG.csrfToken,
                            'Accept': 'application/json'
                        }
                    };
                    return $.ajax($.extend(true, {}, defaults, options));
                },

                debounce: function(func, wait) {
                    wait = wait || 300;
                    var timeout;
                    return function() {
                        var context = this;
                        var args = arguments;
                        var later = function() {
                            timeout = null;
                            func.apply(context, args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }
            };

            var StatementEmailComposer = {
                modal: null,

                init: function() {
                    var modalElement = document.getElementById('sendStatementEmailModal');
                    if (!modalElement) {
                        return;
                    }

                    this.modal = bootstrap.Modal.getOrCreateInstance(modalElement);

                    $('#sendStatementEmailForm').on('submit', function(e) {
                        e.preventDefault();
                        StatementEmailComposer.send();
                    });
                },

                open: function(payload) {
                    payload = payload || {};

                    var recipientType = payload.recipientType || 'recipient';
                    var recipientName = payload.recipientName || 'Partner';
                    var recipientEmail = payload.recipientEmail || '';

                    $('#statementEmailTo').val(recipientEmail);
                    $('#statementEmailCc').val('');
                    $('#statementEmailSubject').val(this.defaultSubject(recipientType));
                    $('#statementEmailBody').val(this.defaultBody(recipientName, recipientType));

                    if (this.modal) {
                        this.modal.show();
                    }
                },

                defaultSubject: function(recipientType) {
                    var statementReference = CONFIG.debitNoteNo || CONFIG.endorsementNo || CONFIG.coverNo ||
                        '';
                    var typeLabel = recipientType === 'cedant' ? 'Cedant' : 'Reinsurer';
                    return 'Account Statement - ' + typeLabel + ' - ' + statementReference;
                },

                defaultBody: function(recipientName, recipientType) {
                    var typeLabel = recipientType === 'cedant' ? 'cedant' : 'reinsurer';
                    var lines = [
                        'Dear ' + recipientName + ',',
                        '',
                        'Please find attached the account statement documents for your review.',
                        '',
                        'Cover No: ' + (CONFIG.coverNo || '-'),
                        'Endorsement No: ' + (CONFIG.endorsementNo || '-'),
                        '',
                        'Kindly confirm receipt and revert for any clarification.',
                        '',
                        'Best regards,'
                    ];

                    if (typeLabel) {
                        lines.splice(2, 0, 'This statement is for the ' + typeLabel + ' account.');
                    }

                    return lines.join('\n');
                },

                send: function() {
                    var to = $('#statementEmailTo').val().trim();
                    var cc = $('#statementEmailCc').val().trim();
                    var subject = $('#statementEmailSubject').val().trim();
                    var body = $('#statementEmailBody').val().trim();

                    if (!to || !subject || !body) {
                        Utils.showToast('Please complete To, Subject and Message Draft fields.', 'error');
                        return;
                    }

                    var mailtoUrl = 'mailto:' + encodeURIComponent(to) +
                        '?subject=' + encodeURIComponent(subject) +
                        '&body=' + encodeURIComponent(body);

                    if (cc) {
                        mailtoUrl += '&cc=' + encodeURIComponent(cc);
                    }

                    window.location.href = mailtoUrl;

                    if (this.modal) {
                        this.modal.hide();
                    }

                    Utils.showToast('Email draft opened. Attach the statement documents and send.',
                        'success');
                }
            };

            var DebitItemsTable = {
                table: null,

                init: function() {
                    var self = this;

                    this.table = $('#debitItemsTable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: CONFIG.routes.debitItems,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': CONFIG.csrfToken
                            },
                            data: function(d) {
                                d.cover_no = CONFIG.coverNo;
                                d.ref_no = CONFIG.refNo;
                                d.endorsement_no = CONFIG.endorsementNo;
                            },
                            error: function(xhr, error, thrown) {
                                console.error('DebitItems DataTable Error:', {
                                    xhr: xhr,
                                    error: error,
                                    thrown: thrown
                                });
                            }
                        },
                        columns: [{
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'item_no',
                                name: 'item_no',
                                defaultContent: '-'
                            },
                            {
                                data: 'transaction_type',
                                name: 'transaction_type',
                                defaultContent: '-'
                            },
                            {
                                data: 'posting_date',
                                name: 'posting_date',
                                render: function(data) {
                                    return Utils.formatDate(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'quarter_figure',
                                name: 'posting_quarter',
                                orderable: false,
                                searchable: false,
                                defaultContent: '-'
                            },
                            {
                                data: 'treaty_type',
                                name: 'treaty_type',
                                render: function(data) {
                                    return Utils.getTypeBadge(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'class_name',
                                name: 'class_name',
                                render: function(data, type, row) {
                                    return '<span class="fw-medium">' + Utils.escapeHtml(row
                                            .group_name || '-') +
                                        '</span><br>' +
                                        '<small class="text-muted">' + Utils.escapeHtml(
                                            data || '') + '</small>';
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'line_rate',
                                name: 'commission_rate',
                                render: function(data) {
                                    return Utils.formatPercentage(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'gross_amount',
                                name: 'amount',
                                className: 'amount-cell amount-cell--positive',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'status',
                                name: 'status',
                                orderable: false,
                                render: function(data) {
                                    return Utils.getStatusBadge(data);
                                },
                                defaultContent: '-'
                            }
                        ],
                        order: [
                            [1, 'asc']
                        ],
                        pageLength: CONFIG.dataTables.pageLength,
                        lengthMenu: CONFIG.dataTables.lengthMenu,
                        dom: CONFIG.dataTables.dom,
                        language: {
                            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Loading...',
                            emptyTable: 'No debit items found',
                            zeroRecords: 'No matching debit items found'
                        },
                        drawCallback: function(settings) {
                            self.updateTotals();
                            if (typeof SummaryManager !== 'undefined' && SummaryManager
                                .triggerRefresh) {
                                SummaryManager.triggerRefresh();
                            }
                        }
                    });
                },

                updateTotals: function() {
                    if (!this.table) return;

                    var totalCommission = 0,
                        totalAmount = 0;

                    this.table.rows().every(function() {
                        var data = this.data();

                        if (data) {
                            totalCommission += parseFloat(data.commission_amount) || 0;
                            totalAmount += parseFloat(data.gross_amount) || 0;
                        }
                    });

                    // $('#totalCommission').text(Utils.formatCurrency(totalCommission));
                    $('#totalAmount').text(Utils.formatCurrency(totalAmount));
                },

                reload: function(resetPaging) {
                    if (this.table) {
                        this.table.ajax.reload(null, resetPaging !== false);
                    }
                },

                adjustColumns: function() {
                    if (this.table) {
                        this.table.columns.adjust();
                    }
                }
            };

            var CreditItemsTable = {
                table: null,

                init: function() {
                    var self = this;

                    this.table = $('#creditItemsTable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: CONFIG.routes.creditItems,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': CONFIG.csrfToken
                            },
                            data: function(d) {
                                d.cover_no = CONFIG.coverNo;
                                d.endorsement_no = CONFIG.endorsementNo;
                            },
                            error: function(xhr, error, thrown) {
                                console.error('CreditItems DataTable Error:', {
                                    xhr: xhr,
                                    error: error,
                                    thrown: thrown
                                });
                            }
                        },
                        columns: [{
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'item_no',
                                name: 'item_no',
                                defaultContent: '-'
                            },
                            {
                                data: 'transaction_type',
                                name: 'transaction_type',
                                defaultContent: '-'
                            },
                            {
                                data: 'posting_date',
                                name: 'posting_date',
                                render: function(data) {
                                    return Utils.formatDate(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'treaty_type',
                                name: 'treaty_type',
                                render: function(data) {
                                    return Utils.getTypeBadge(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'class_name',
                                name: 'class_name',
                                render: function(data, type, row) {
                                    return '<span class="fw-medium">' + Utils.escapeHtml(row
                                            .group_name || '-') + '</span><br>' +
                                        '<small class="text-muted">' + Utils.escapeHtml(
                                            data || '') + '</small>';
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'line_rate',
                                name: 'commission_rate',
                                render: function(data) {
                                    return Utils.formatPercentage(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'gross_amount',
                                name: 'amount',
                                className: 'amount-cell amount-cell--negative',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data) {
                                    return Utils.getStatusBadge(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row) {
                                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Item actions">' +
                                        '<button type="button" class="btn btn-outline-dark btn-action btn-view-item" data-id="' +
                                        row.id + '" title="View">' +
                                        '<i class="ri-eye-line"></i>' +
                                        '</button>' +
                                        '</div>';
                                }
                            }
                        ],
                        order: [
                            [3, 'desc']
                        ],
                        pageLength: CONFIG.dataTables.pageLength,
                        lengthMenu: CONFIG.dataTables.lengthMenu,
                        dom: CONFIG.dataTables.dom,
                        language: {
                            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Loading...',
                            emptyTable: 'No credit items found',
                            zeroRecords: 'No matching credit items found'
                        },
                        drawCallback: function(settings) {
                            self.updateTotals();

                            // Update count badge
                            var totalRecords = settings.json ? settings.json.recordsTotal : 0;
                            $('#creditItemsCount').text(totalRecords);
                        }
                    });
                },

                updateTotals: function() {
                    if (!this.table) return;

                    var totalAmount = 0;

                    this.table.rows().every(function() {
                        var data = this.data();

                        if (data) {
                            totalAmount += parseFloat(data.gross_amount) || 0;
                        }
                    });

                    $('#totalCreditAmount').text(Utils.formatCurrency(totalAmount));
                },

                reload: function(resetPaging) {
                    if (this.table) {
                        this.table.ajax.reload(null, resetPaging !== false);
                    }
                },

                adjustColumns: function() {
                    if (this.table) {
                        this.table.columns.adjust();
                    }
                }
            };

            var ReinsurersTable = {
                table: null,

                init: function() {
                    var self = this;

                    var tableConfig = {
                        processing: true,
                        columns: [{
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'name',
                                name: 'name',
                                render: function(data, type, row) {
                                    return '<span class="fw-semibold">' + Utils.escapeHtml(
                                            data || '-') + '</span><br>' +
                                        '<small class="text-muted">' + Utils.escapeHtml(row
                                            .email || '') + '</small>';
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'share_percentage',
                                name: 'share_percentage',
                                render: function(data) {
                                    return Utils.formatPercentage(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'gross_premium',
                                name: 'gross_premium',
                                className: 'amount-cell',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'commission',
                                name: 'commission',
                                className: 'amount-cell',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'premium_tax_amount',
                                name: 'premium_tax_amount',
                                className: 'amount-cell',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'wht_amount',
                                name: 'wht_amount',
                                className: 'amount-cell',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'ri_tax',
                                name: 'ri_tax',
                                className: 'amount-cell',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'net_amount',
                                name: 'net_amount',
                                className: 'amount-cell',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data) {
                                    return Utils.getStatusBadge(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row) {
                                    var isCoverNote = !!row.is_cover_note;
                                    var noteLabel = isCoverNote ? 'Cover Note' : 'Credit Note';
                                    var noteType = isCoverNote ? 'cover_note' : 'credit_note';

                                    return '<div class="d-flex gap-2">' +
                                        '<a href="javascript:void(0)" class="text-primary btn-view-reinsurer text-center d-flex align-items-center" data-id="' +
                                        row.id + '" data-partner-no="' + row.partner_no +
                                        '" data-note-type="' + noteType + '" title="' +
                                        noteLabel + '">' +
                                        '<i class="ri-file-list-3-line fs-18"></i> <span class="d-none d-md-inline me-2">' +
                                        noteLabel + '</span>' +
                                        '</a>' +
                                        '<a href="javascript:void(0)" target="_blank" class="text-success text-center d-flex align-items-center" title="Cover Slip">' +
                                        '<i class="ri-file-shield-2-line fs-18"></i> <span class="d-none d-md-inline me-2">Cover Slip</span>' +
                                        '</a>' +
                                        '<a href="javascript:void(0)" class="text-info btn-send-statement text-center d-flex align-items-center" data-id="' +
                                        row.id + '" data-name="' + Utils.escapeHtml(row.name ||
                                            '') +
                                        '" data-email="' + Utils.escapeHtml(row.email || '') +
                                        '" title="Send Statement">' +
                                        '<i class="ri-mail-send-line fs-18"></i> <span class="d-none d-md-inline">Send Statement</span>' +
                                        '</a>' +
                                        '</div>';
                                }
                                // render: function(data, type, row) {
                                //     return '<div class="btn-group btn-group-sm" role="group" aria-label="Reinsurer actions">' +
                                //         '<button type="button" class="btn btn-outline-primary btn-action btn-view-reinsurer" data-id="' +
                                //         row.id + '" data-partner-no="' + row.partner_no +
                                //         '" title="View Details">' +
                                //         '<i class="ri-eye-line"></i>' +
                                //         '</button>' +
                                //         '<button type="button" class="btn btn-outline-info btn-action btn-send-statement" data-id="' +
                                //         row.id + '" title="Send Statement">' +
                                //         '<i class="ri-mail-send-line"></i>' +
                                //         '</button>' +
                                //         '</div>';
                                // }
                            }
                        ],
                        order: [
                            [2, 'desc']
                        ],
                        pageLength: CONFIG.dataTables.pageLength,
                        lengthMenu: CONFIG.dataTables.lengthMenu,
                        dom: CONFIG.dataTables.dom,
                        language: {
                            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Loading...',
                            emptyTable: 'No reinsurers found',
                            zeroRecords: 'No matching reinsurers found'
                        },
                        footerCallback: function(row, data, start, end, display) {
                            var api = this.api();

                            var parseCurrency = function(value) {
                                if (typeof value === 'number') return value;
                                if (typeof value === 'string') {
                                    return parseFloat(value.replace(/[^0-9.-]+/g, '')) || 0;
                                }
                                return 0;
                            };

                            var sharePercentTotal = api.column(2, {
                                page: 'current'
                            }).data().reduce(function(a, b) {
                                return parseCurrency(a) + parseCurrency(b);
                            }, 0);

                            var grossPremiumTotal = api.column(3, {
                                page: 'current'
                            }).data().reduce(function(a, b) {
                                return parseCurrency(a) + parseCurrency(b);
                            }, 0);

                            var commissionTotal = api.column(4, {
                                page: 'current'
                            }).data().reduce(function(a, b) {
                                return parseCurrency(a) + parseCurrency(b);
                            }, 0);

                            var premiumTaxTotal = api.column(5, {
                                page: 'current'
                            }).data().reduce(function(a, b) {
                                return parseCurrency(a) + parseCurrency(b);
                            }, 0);

                            var whtTotal = api.column(6, {
                                page: 'current'
                            }).data().reduce(function(a, b) {
                                return parseCurrency(a) + parseCurrency(b);
                            }, 0);

                            var riTaxTotal = api.column(7, {
                                page: 'current'
                            }).data().reduce(function(a, b) {
                                return parseCurrency(a) + parseCurrency(b);
                            }, 0);

                            var netAmountTotal = api.column(8, {
                                page: 'current'
                            }).data().reduce(function(a, b) {
                                return parseCurrency(a) + parseCurrency(b);
                            }, 0);

                            // Update footer
                            $('#totalSharePercent').text(Utils.formatPercentage(sharePercentTotal));
                            $('#totalGrossPremium').text(Utils.formatCurrency(grossPremiumTotal));
                            $('#totalCommission').text(Utils.formatCurrency(commissionTotal));
                            $('#totalPremiumTax').text(Utils.formatCurrency(premiumTaxTotal));
                            $('#totalWHT').text(Utils.formatCurrency(whtTotal));
                            $('#totalRITax').text(Utils.formatCurrency(riTaxTotal));
                            $('#totalNetAmount').text(Utils.formatCurrency(netAmountTotal));
                        }
                    };

                    if (CONFIG.routes.reinsurers && CONFIG.routes.reinsurers !== '') {
                        tableConfig.serverSide = true;
                        tableConfig.ajax = {
                            url: CONFIG.routes.reinsurers,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': CONFIG.csrfToken
                            },
                            data: function(d) {
                                d.cover_no = CONFIG.coverNo;
                                d.endorsement_no = CONFIG.endorsementNo;
                            },
                            error: function(xhr, error, thrown) {
                                console.error('Reinsurers DataTable Error:', {
                                    xhr: xhr,
                                    error: error,
                                    thrown: thrown
                                });
                                Utils.showToast('Failed to load reinsurers data', 'error');
                            }
                        };
                    } else {
                        tableConfig.data = [];
                        console.warn('Reinsurers route not configured, using client-side mode');
                    }

                    this.table = $('#reinsurersTable').DataTable(tableConfig);
                },

                reload: function(resetPaging) {
                    if (this.table) {
                        if (this.table.ajax) {
                            this.table.ajax.reload(null, resetPaging !== false);
                        } else {
                            this.table.draw(resetPaging !== false);
                        }
                    }
                },

                adjustColumns: function() {
                    if (this.table) {
                        this.table.columns.adjust().draw(false);
                    }
                },

                destroy: function() {
                    if (this.table) {
                        this.table.destroy();
                        this.table = null;
                    }
                }
            };

            var DocumentsTable = {
                table: null,

                init: function() {
                    var self = this;
                    var documentIcons = {
                        'Debit Note': 'ri-file-text-line text-primary',
                        'Credit Note': 'ri-file-text-line text-success',
                        'Statement of Account': 'ri-file-list-3-line text-info',
                        'Bordereau': 'ri-table-line text-warning',
                        'Closing Slip': 'ri-file-paper-2-line text-danger'
                    };

                    var tableConfig = {
                        processing: true,
                        columns: [{
                                data: null,
                                orderable: false,
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'document_type',
                                name: 'document_type',
                                render: function(data) {
                                    var icon = documentIcons[data] ||
                                        'ri-file-line text-secondary';
                                    return '<i class="' + icon +
                                        ' me-2" style="vertical-align: -1.5px;"></i>' +
                                        Utils.escapeHtml(data || '-');
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'reference',
                                name: 'reference',
                                defaultContent: '-'
                            },
                            {
                                data: 'description',
                                name: 'description',
                                defaultContent: '-'
                            },
                            {
                                data: 'generated_date',
                                name: 'generated_date',
                                render: function(data) {
                                    return Utils.formatDate(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'generated_by',
                                name: 'generated_by',
                                defaultContent: '-'
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data) {
                                    return Utils.getStatusBadge(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: null,
                                orderable: false,
                                render: function(data, type, row) {
                                    var fileUrl = row.file_path || '';
                                    var rowId = row.id || '';
                                    return '<div class="btn-group btn-group-sm">' +
                                        '<button class="btn btn-outline-primary btn-action btn-preview-doc" data-url="' +
                                        Utils.escapeHtml(fileUrl) + '" title="Preview">' +
                                        '<i class="ri-eye-line"></i>' +
                                        '</button>' +
                                        '<button class="btn btn-outline-success btn-action btn-download-doc" data-id="' +
                                        rowId + '" title="Download">' +
                                        '<i class="ri-download-line"></i>' +
                                        '</button>' +
                                        '<button class="btn btn-outline-info btn-action btn-send-doc" data-id="' +
                                        rowId + '" title="Send">' +
                                        '<i class="ri-mail-send-line"></i>' +
                                        '</button>' +
                                        '</div>';
                                }
                            }
                        ],
                        order: [
                            [4, 'desc']
                        ],
                        pageLength: CONFIG.dataTables.pageLength,
                        lengthMenu: CONFIG.dataTables.lengthMenu,
                        dom: CONFIG.dataTables.dom,
                        language: {
                            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Loading...',
                            emptyTable: 'No documents found',
                            zeroRecords: 'No matching documents found'
                        }
                    };

                    if (CONFIG.routes.documents && CONFIG.routes.documents !== '') {
                        tableConfig.serverSide = true;
                        tableConfig.ajax = {
                            url: CONFIG.routes.documents,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': CONFIG.csrfToken
                            },
                            data: function(d) {
                                d.cover_no = CONFIG.coverNo;
                                d.endorsement_no = CONFIG.endorsementNo;
                            },
                            dataSrc: function(json) {
                                var data = json.data || json;
                                self.updateStats(data);
                                return data;
                            },
                            error: function(xhr, error) {
                                console.error('Documents DataTable Error:', error);
                            }
                        };
                    } else {
                        tableConfig.data = [];
                    }

                    this.table = $('#documentsTable').DataTable(tableConfig);
                },

                updateStats: function(data) {
                    var stats = {
                        generated: 0,
                        sent: 0,
                        signed: 0
                    };

                    if (Array.isArray(data)) {
                        data.forEach(function(doc) {
                            var status = (doc.status || '').toLowerCase();
                            if (status === 'generated') stats.generated++;
                            else if (status === 'sent') stats.sent++;
                            else if (status === 'signed') stats.signed++;
                        });
                    }

                    $('#docGenerated').text(stats.generated);
                    $('#docSent').text(stats.sent);
                    $('#docSigned').text(stats.signed);
                },

                reload: function(resetPaging) {
                    if (this.table) {
                        this.table.ajax.reload(null, resetPaging !== false);
                    }
                },

                adjustColumns: function() {
                    if (this.table) {
                        this.table.columns.adjust();
                    }
                }
            };

            var ApprovalsTable = {
                table: null,

                init: function() {
                    var self = this;

                    this.table = $('#approvalsTable').DataTable({
                        processing: true,
                        ajax: {
                            url: "{!! route('cover.approvals_datatable') !!}",
                            data: function(d) {
                                d.endorsement_no = "{!! $cover->endorsement_no !!}";
                            }
                        },
                        columns: [{
                                data: 'id',
                                name: 'id',
                                defaultContent: '-'
                            },
                            {
                                data: 'approver',
                                name: 'approver',
                                defaultContent: '-'
                            },
                            {
                                data: 'comment',
                                name: 'comment',
                                defaultContent: '-'
                            },
                            {
                                data: 'approver_comment',
                                name: 'approver_comment',
                                defaultContent: '-'
                            },
                            {
                                data: 'status',
                                name: 'status',
                                // render: function(data) {
                                //     return Utils.getStatusBadge(data);
                                // },
                                defaultContent: '-'
                            },
                            {
                                data: 'approval_time',
                                name: 'approval_time',
                                defaultContent: '-'
                            },
                            {
                                data: null,
                                orderable: false,
                                render: function(data, type, row) {
                                    // var rowId = row.id || '';
                                    // return '<div class="btn-group btn-group-sm">' +
                                    //     '<button class="btn btn-outline-success btn-action btn-approve" data-id="' +
                                    //     rowId + '" title="Approve">' +
                                    //     '<i class="ri-check-line"></i>' +
                                    //     '</button>' +
                                    //     '<button class="btn btn-outline-danger btn-action btn-reject" data-id="' +
                                    //     rowId + '" title="Reject">' +
                                    //     '<i class="ri-close-line"></i>' +
                                    //     '</button>' +
                                    //     '</div>';
                                    return '';
                                }
                            }
                        ],
                        pageLength: CONFIG.dataTables.pageLength,
                        lengthMenu: CONFIG.dataTables.lengthMenu,
                        dom: CONFIG.dataTables.dom,
                        language: {
                            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Loading...',
                            emptyTable: 'No approvals pending',
                            zeroRecords: 'No matching approvals found'
                        }
                    });
                },

                reload: function(resetPaging) {
                    if (this.table && this.table.ajax) {
                        this.table.ajax.reload(null, resetPaging !== false);
                    }
                },

                adjustColumns: function() {
                    if (this.table) {
                        this.table.columns.adjust();
                    }
                }
            };

            var DebitItemManager = {
                view: function(id) {
                    var modalEl = document.getElementById('viewItemModal');
                    if (!modalEl) {
                        console.error('View modal not found');
                        return;
                    }
                    var modal = new bootstrap.Modal(modalEl);
                    var $body = $('#viewItemModalBody');

                    $body.html(
                        '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>'
                    );
                    modal.show();

                    Utils.ajax({
                            url: CONFIG.routes.debitItemShow.replace(':id', id),
                            type: 'GET'
                        })
                        .done(function(response) {
                            var item = response.data || response;
                            $('#viewItemModalTitle').text('Item: ' + (item.item_number || item
                                .item_no || 'N/A'));
                            $body.html(DebitItemManager.renderViewContent(item));
                        })
                        .fail(function(xhr) {
                            $body.html(
                                '<div class="alert alert-danger">Failed to load item details</div>');
                        });
                },

                renderViewContent: function(item) {
                    return '<div class="row g-3">' +
                        '<div class="col-md-6">' +
                        '<div class="cedant-info-card">' +
                        '<div class="info-row"><span class="info-label">Item Number</span><span class="info-value">' +
                        Utils.escapeHtml(item.item_no || '-') + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Item Date</span><span class="info-value">' +
                        Utils.formatDate(item.item_date || item.posting_date) + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Treaty Type</span><span class="info-value">' +
                        Utils.escapeHtml(item.treaty_type || '-') + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Class Group</span><span class="info-value">' +
                        Utils.escapeHtml(item.class_group || '-') + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Class Name</span><span class="info-value">' +
                        Utils.escapeHtml(item.class_name || '-') + '</span></div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                        '<div class="cedant-info-card">' +
                        '<div class="info-row"><span class="info-label">Reinsurer</span><span class="info-value">' +
                        Utils.escapeHtml(item.reinsurer || '-') + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Gross Premium</span><span class="info-value fw-semibold">' +
                        Utils.formatCurrency(item.gross_premium || item.amount) + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Commission Rate</span><span class="info-value">' +
                        Utils.formatPercentage(item.commission_rate) + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Commission Amount</span><span class="info-value">' +
                        Utils.formatCurrency(item.commission_amount) + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Net Amount</span><span class="info-value text-success fw-bold">' +
                        Utils.formatCurrency(item.net_amount) + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Status</span><span class="info-value">' +
                        Utils.getStatusBadge(item.status) + '</span></div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                },

                edit: function(id) {
                    Utils.ajax({
                            url: CONFIG.routes.debitItemShow.replace(':id', id),
                            type: 'GET'
                        })
                        .done(function(response) {
                            var item = response.data || response;
                            var $form = $('#addDebitItemForm');

                            $('#addDebitItemModalLabel').html(
                                '<i class="ri-edit-line text-primary me-2"></i>Edit Debit Item');

                            $form.find('[name="treaty_type"]').val(item.treaty_type);
                            $form.find('[name="item_date"]').val(item.item_date || item.posting_date);
                            $form.find('[name="class_group"]').val(item.class_group);
                            $form.find('[name="class_name"]').val(item.class_name);
                            $form.find('[name="reinsurer_id"]').val(item.reinsurer_id);
                            $form.find('[name="gross_premium"]').val(item.gross_premium || item.amount);
                            $form.find('[name="commission_rate"]').val(item.commission_rate);
                            $form.find('[name="status"]').val(item.status);
                            $form.find('#commissionAmount').val(Utils.formatCurrency(item
                                .commission_amount));
                            $form.find('#netAmount').val(Utils.formatCurrency(item.net_amount));

                            $form.data('edit-id', id);

                            var modalEl = document.getElementById('addDebitItemModal');
                            if (modalEl) {
                                new bootstrap.Modal(modalEl).show();
                            }
                        })
                        .fail(function() {
                            Utils.showToast('Failed to load item for editing', 'error');
                        });
                },

                delete: function(id) {
                    Utils.showConfirm(
                        'Delete Item',
                        'Are you sure you want to delete this debit item? This action cannot be undone.',
                        function() {
                            Utils.showLoading(true);

                            Utils.ajax({
                                    url: CONFIG.routes.debitItemDelete.replace(':id', id),
                                    type: 'DELETE'
                                })
                                .done(function(response) {
                                    Utils.showLoading(false);
                                    Utils.showToast(response.message || 'Item deleted successfully',
                                        'success');
                                    DebitItemsTable.reload();
                                })
                                .fail(function(xhr) {
                                    Utils.showLoading(false);
                                    var message = 'Failed to delete item';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        message = xhr.responseJSON.message;
                                    }
                                    Utils.showToast(message, 'error');
                                });
                        }
                    );
                }
            };

            var DocumentsManager = {
                generate: function(type) {
                    Utils.showLoading(true);
                    var postingYear = $('#posting_year').val() || '';
                    var postingQuarter = $('#posting_quarter').val() || '';

                    Utils.ajax({
                            url: CONFIG.routes.documentGenerate,
                            type: 'POST',
                            data: {
                                cover_no: CONFIG.coverNo,
                                endorsement_no: CONFIG.endorsementNo,
                                document_type: type,
                                posting_year: postingYear,
                                posting_quarter: postingQuarter
                            }
                        })
                        .done(function(response) {
                            Utils.showLoading(false);
                            Utils.showToast(response.message || 'Document generated successfully',
                                'success');
                            DocumentsTable.reload();
                        })
                        .fail(function(xhr) {
                            Utils.showLoading(false);
                            var message = 'Failed to generate document';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Utils.showToast(message, 'error');
                        });
                },

                preview: function(url) {
                    if (!url) {
                        Utils.showToast('No file URL available', 'warning');
                        return;
                    }
                    window.open(url, '_blank', 'noopener,noreferrer');
                },

                download: function(id) {
                    if (!id) {
                        Utils.showToast('Invalid document ID', 'warning');
                        return;
                    }
                    window.location.href = CONFIG.routes.documentDownload.replace(':id', id);
                },

                send: function(id) {
                    Utils.showConfirm(
                        'Send Document',
                        'Send this document to the relevant parties?',
                        function() {
                            Utils.showToast('Document sent successfully', 'success');
                            DocumentsTable.reload();
                        }
                    );
                }
            };

            var CedantTable = {
                table: null,

                init: function() {
                    var self = this;

                    var cedantData = [{
                        id: '{{ $customer->customer_id ?? '' }}',
                        name: '{{ $customer->name ?? '' }}',
                        registration_no: '{{ $customer->registration_no ?? ($customer->registration_number ?? ($customer->customer_id ?? '')) }}',
                        address: '{{ $customer->address ?? ($customer->postal_address ?? (trim(($customer->street ?? '') . ' ' . ($customer->city ?? '')) !== '' ? trim(($customer->street ?? '') . ' ' . ($customer->city ?? '')) : '')) }}',
                        contact_person: '{{ $customer->primaryContact->contact_name ?? ($customer->contact_person ?? '') }}',
                        designation: '{{ $customer->designation ?? '' }}',
                        email: '{{ $customer->email ?? '' }}',
                        phone: '{{ $customer->phone ?? ($customer->telephone ?? '') }}',
                        treaty_period: '{{ $cover && $cover->cover_from && $cover->cover_to ? \Carbon\Carbon::parse($cover->cover_from)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($cover->cover_to)->format('d M Y') : '' }}',
                        treaty_capacity: {{ $cedantTreatyCapacity ?? ($cover->effective_sum_insured ?? ($cover->total_sum_insured ?? ($cover->sum_insured ?? ($cover->treaty_capacity ?? 0)))) }},
                        partner_no: '{{ $customer->customer_id ?? '' }}',
                        is_cover_note: {{ !empty($cedantIsCoverNote) ? 'true' : 'false' }},
                    }];

                    this.table = $('#cedantTable').DataTable({
                        processing: true,
                        data: cedantData,
                        columns: [{
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'name',
                                name: 'name',
                                render: function(data, type, row) {
                                    return '<span class="fw-semibold">' + Utils.escapeHtml(
                                        data || '-') + '</span>';
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'registration_no',
                                name: 'registration_no',
                                defaultContent: '-'
                            },
                            {
                                data: 'address',
                                name: 'address',
                                defaultContent: '-'
                            },
                            {
                                data: 'contact_person',
                                name: 'contact_person',
                                render: function(data, type, row) {
                                    var contactText = Utils.escapeHtml(data || '-');
                                    if (row.designation) {
                                        contactText += '<br><small class="text-muted">' +
                                            Utils.escapeHtml(row.designation) + '</small>';
                                    }
                                    return contactText;
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'email',
                                name: 'email',
                                render: function(data) {
                                    if (data) {
                                        return '<a href="mailto:' + Utils.escapeHtml(data) +
                                            '">' + Utils.escapeHtml(data) + '</a>';
                                    }
                                    return '-';
                                },
                                defaultContent: '-'
                            },
                            {
                                data: 'phone',
                                name: 'phone',
                                defaultContent: '-'
                            },
                            {
                                data: 'treaty_period',
                                name: 'treaty_period',
                                defaultContent: '-'
                            },
                            {
                                data: 'treaty_capacity',
                                name: 'treaty_capacity',
                                className: 'amount-cell fw-semibold text-primary',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                },
                                defaultContent: '-'
                            },
                            {
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row) {
                                    var isCoverNote = !!row.is_cover_note;
                                    var noteLabel = isCoverNote ? 'Cover Note' :
                                        'Debit Note';
                                    var noteType = isCoverNote ? 'cover_note' :
                                        'debit_note';

                                    return '<div class="d-flex gap-2">' +
                                        '<a href="javascript:void(0)" class="text-primary btn-view-cedant text-center d-flex align-items-center" data-partner_no="' +
                                        row.partner_no + '" data-note-type="' + noteType +
                                        '" title="' + noteLabel + '">' +
                                        '<i class="ri-file-list-3-line fs-18"></i> <span class="d-none d-md-inline me-2">' +
                                        noteLabel + '</span>' +
                                        '</a>' +
                                        '<a href="' + CONFIG.routes.previewSlip +
                                        '" target="_blank" class="text-success text-center d-flex align-items-center" title="Cover Slip">' +
                                        '<i class="ri-file-shield-2-line fs-18"></i> <span class="d-none d-md-inline me-2">Cover Slip</span>' +
                                        '</a>' +
                                        '<a href="javascript:void(0)" class="text-info btn-send-cedant-statement text-center d-flex align-items-center" data-partner_no="' +
                                        row.partner_no + '" data-name="' + Utils.escapeHtml(
                                            row.name || '') +
                                        '" data-email="' + Utils.escapeHtml(row.email ||
                                            '') +
                                        '" title="Send Statement">' +
                                        '<i class="ri-mail-send-line fs-18"></i> <span class="d-none d-md-inline">Send Statement</span>' +
                                        '</a>' +
                                        '</div>';
                                }
                            }
                        ],
                        order: [
                            [1, 'asc']
                        ],
                        pageLength: CONFIG.dataTables.pageLength,
                        lengthMenu: CONFIG.dataTables.lengthMenu,
                        dom: CONFIG.dataTables.dom,
                        language: {
                            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Loading...',
                            emptyTable: 'No cedant information found',
                            zeroRecords: 'No matching cedant found'
                        }
                    });

                    this.bindEvents();
                },

                bindEvents: function() {
                    var self = this;

                    $('#cedantTable').on('click', '.btn-view-cedant', function() {
                        var partnerNo = $(this).data('partner_no');
                        var noteType = $(this).data('note-type') || 'debit_note';
                        var postingYear = $('#posting_year').val() || '';
                        var postingQuarter = $('#posting_quarter').val() || '';

                        if (partnerNo) {
                            var url = CONFIG.routes.cedantDebitNoteView +
                                '?cover_no=' + encodeURIComponent(CONFIG.coverNo) +
                                '&endorsement_no=' + encodeURIComponent(CONFIG
                                    .endorsementNo) +
                                '&cedant_id=' + encodeURIComponent(partnerNo) +
                                '&note_type=' + encodeURIComponent(noteType) +
                                '&posting_year=' + encodeURIComponent(postingYear) +
                                '&posting_quarter=' + encodeURIComponent(postingQuarter);
                            window.open(url, '_blank');
                        }

                    });
                },

                refresh: function() {
                    if (this.table) {
                        this.table.ajax.reload(null, false);
                    }
                },

                adjustColumns: function() {
                    if (this.table) {
                        this.table.columns.adjust();
                    }
                }
            };

            var DebitItemForm = {
                init: function() {
                    this.loadReinsurers();
                    this.bindEvents();
                },

                loadReinsurers: function() {
                    if (!CONFIG.routes.reinsurersList || CONFIG.routes.reinsurersList === '') {
                        console.warn('Reinsurers list route not configured');
                        return;
                    }

                    Utils.ajax({
                            url: CONFIG.routes.reinsurersList,
                            type: 'GET'
                        })
                        .done(function(response) {
                            var reinsurers = response.data || response;
                            var $select = $('#reinsurerId');

                            $select.find('option:not(:first)').remove();

                            if (Array.isArray(reinsurers)) {
                                reinsurers.forEach(function(r) {
                                    $select.append('<option value="' + r.id + '">' + Utils
                                        .escapeHtml(r.name) + '</option>');
                                });
                            }
                        })
                        .fail(function(xhr) {
                            console.error('Failed to load reinsurers list:', xhr);
                        });
                },

                bindEvents: function() {
                    var self = this;

                    var calculateAmounts = Utils.debounce(function() {
                        self.calculateAmounts();
                    }, 150);
                    $('#grossPremium, #commissionRate').on('input', calculateAmounts);

                    $('#addDebitItemForm').on('submit', function(e) {
                        e.preventDefault();
                        self.handleSubmit(this);
                    });

                    $('#addDebitItemModal').on('hidden.bs.modal', function() {
                        self.resetForm();
                    });
                },

                calculateAmounts: function() {
                    var gross = parseFloat($('#grossPremium').val()) || 0;
                    var rate = parseFloat($('#commissionRate').val()) || 0;
                    var commission = gross * (rate / 100);
                    var net = gross - commission;

                    $('#commissionAmount').val(Utils.formatCurrency(commission));
                    $('#netAmount').val(Utils.formatCurrency(net));
                },

                handleSubmit: function(form) {
                    var self = this;
                    var $form = $(form);

                    if (!form.checkValidity()) {
                        $form.addClass('was-validated');
                        return;
                    }

                    var editId = $form.data('edit-id');
                    var url = editId ?
                        CONFIG.routes.debitItemUpdate.replace(':id', editId) :
                        CONFIG.routes.debitItemStore;
                    var method = editId ? 'PUT' : 'POST';

                    Utils.showLoading(true);

                    Utils.ajax({
                            url: url,
                            type: method,
                            data: $form.serialize()
                        })
                        .done(function(response) {
                            Utils.showLoading(false);
                            Utils.showToast(response.message || 'Item saved successfully', 'success');
                            var modalEl = document.getElementById('addDebitItemModal');
                            if (modalEl) {
                                var modalInstance = bootstrap.Modal.getInstance(modalEl);
                                if (modalInstance) {
                                    modalInstance.hide();
                                }
                            }
                            DebitItemsTable.reload();
                            self.resetForm();
                        })
                        .fail(function(xhr) {
                            Utils.showLoading(false);
                            var errors = xhr.responseJSON && xhr.responseJSON.errors;

                            if (errors) {
                                Object.keys(errors).forEach(function(key) {
                                    var $input = $('[name="' + key + '"]');
                                    $input.addClass('is-invalid');
                                    $input.siblings('.invalid-feedback').text(errors[key][0]);
                                });
                            } else {
                                var message = 'Failed to save item';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Utils.showToast(message, 'error');
                            }
                        });
                },

                resetForm: function() {
                    var $form = $('#addDebitItemForm');
                    if ($form.length && $form[0]) {
                        $form[0].reset();
                    }
                    $form.removeClass('was-validated');
                    $form.removeData('edit-id');
                    $form.find('.is-invalid').removeClass('is-invalid');
                    $('#addDebitItemModalLabel').html(
                        '<i class="ri-add-circle-line text-primary me-2"></i>Add Debit Item');
                    $('#commissionAmount, #netAmount').val('');
                }
            };

            var SummaryManager = {
                refreshInterval: null,
                isLoading: false,
                autoRefreshEnabled: false,
                autoRefreshIntervalMs: 60000,

                init: function() {
                    var self = this;
                    this.fetchStats();

                    if (this.autoRefreshEnabled) {
                        this.startAutoRefresh();
                    }
                },

                fetchStats: function(callback) {
                    var self = this;

                    if (this.isLoading) {
                        return;
                    }

                    this.isLoading = true;

                    Utils.ajax({
                            url: CONFIG.routes.summaryStats,
                            type: 'GET',
                            data: {
                                cover_no: CONFIG.coverNo,
                                endorsement_no: CONFIG.endorsementNo
                            }
                        })
                        .done(function(response) {
                            if (response.success && response.data) {
                                self.updateUI(response.data);
                            }
                            if (typeof callback === 'function') {
                                callback(response);
                            }
                        })
                        .fail(function(xhr) {
                            console.error('Failed to fetch summary stats:', xhr);
                        })
                        .always(function() {
                            self.isLoading = false;
                        });
                },

                updateUI: function(data) {
                    var financial = data.financial || {};
                    var counts = data.counts || {};

                    $('#summaryGrossPremium').text(Utils.formatCurrency(financial.total_gross_premium ||
                        0));
                    $('#summaryCommission').text(Utils.formatCurrency(financial.total_commission || 0));
                    $('#summaryNetAmount').text(Utils.formatCurrency(financial.total_net_amount || 0));
                    $('#summaryTotalReinsurers').text((counts.reinsurers || 0).toLocaleString('en-US'));

                    $('#debitItemsCount').text(counts.debit_items || 0);
                    $('#reinsurersCount').text(counts.reinsurers || 0);
                    $('#documentsCount').text(counts.documents || 0);
                    $('#docGenerated').text(counts.documents_generated || 0);
                    $('#docSent').text(counts.documents_sent || 0);
                    $('#docSigned').text(counts.documents_signed || 0);

                    if (data.last_updated) {
                        var lastUpdated = new Date(data.last_updated);
                        var formattedTime = lastUpdated.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                },

                startAutoRefresh: function() {
                    var self = this;
                    if (this.refreshInterval) {
                        clearInterval(this.refreshInterval);
                    }
                    this.refreshInterval = setInterval(function() {
                        self.fetchStats();
                    }, this.autoRefreshIntervalMs);
                },

                stopAutoRefresh: function() {
                    if (this.refreshInterval) {
                        clearInterval(this.refreshInterval);
                        this.refreshInterval = null;
                    }
                },

                triggerRefresh: function() {
                    this.fetchStats();
                }
            };

            DebitItemsTable.init();
            CreditItemsTable.init();
            ReinsurersTable.init();
            ApprovalsTable
                .init();
            DocumentsTable.init();
            CedantTable.init();
            DebitItemForm.init();
            SummaryManager.init();
            StatementEmailComposer.init();

            $('#btnPreviewSlip').on('click', function() {
                if (CONFIG.routes.previewSlip) {
                    window.open(CONFIG.routes.previewSlip, '_blank');
                }
            });

            $('#btnGenerateStatement').on('click', function() {
                Utils.showLoading(true);
                Utils.ajax({
                        url: CONFIG.routes.generateStatement,
                        type: 'POST'
                    })
                    .done(function(response) {
                        Utils.showLoading(false);
                        Utils.showToast('Statement generated successfully', 'success');
                        if (response.download_url) {
                            window.location.href = response.download_url;
                        }
                        DocumentsTable.reload();
                    })
                    .fail(function(xhr) {
                        Utils.showLoading(false);
                        var message = 'Failed to generate statement';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Utils.showToast(message, 'error');
                    });
            });

            $('#btnExportData').on('click', function() {
                if (CONFIG.routes.exportData) {
                    window.location.href = CONFIG.routes.exportData;
                }
            });

            $('#btnRefreshDocs').on('click', function() {
                DocumentsTable.reload();
            });

            $('#btnRefreshSummary').on('click', function() {
                var $btn = $(this);
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="ri-loader-4-line spin"></i> Refreshing...');

                // Refresh all tables
                DebitItemsTable.reload(false);
                CreditItemsTable.reload(false);
                ReinsurersTable.reload(false);
                DocumentsTable.reload(false);

                SummaryManager.fetchStats(function() {
                    setTimeout(function() {
                        $btn.prop('disabled', false).html(originalHtml);
                        Utils.showToast('Data refreshed successfully', 'success');
                    }, 500);
                });
            });

            $(document).on('click', '[data-doc-type]', function(e) {
                e.preventDefault();
                var docType = $(this).data('doc-type');
                if (docType) {
                    DocumentsManager.generate(docType);
                }
            });

            $(document).on('click', '.btn-view-item', function() {
                var id = $(this).data('id');
                if (id) {
                    DebitItemManager.view(id);
                }
            });

            $(document).on('click', '.btn-edit-item', function() {
                var id = $(this).data('id');
                if (id) {
                    DebitItemManager.edit(id);
                }
            });

            $(document).on('click', '.btn-delete-item', function() {
                var id = $(this).data('id');
                if (id) {
                    DebitItemManager.delete(id);
                }
            });

            $(document).on('click', '.btn-preview-doc', function() {
                var url = $(this).data('url');
                DocumentsManager.preview(url);
            });

            $(document).on('click', '.btn-download-doc', function() {
                var id = $(this).data('id');
                DocumentsManager.download(id);
            });

            $(document).on('click', '.btn-send-doc', function() {
                var id = $(this).data('id');
                if (id) {
                    DocumentsManager.send(id);
                }
            });

            $(document).on('click', '.btn-view-reinsurer', function() {
                var partnerNo = $(this).data('partner-no');
                var noteType = $(this).data('note-type') || 'credit_note';
                if (partnerNo) {
                    Swal.fire({
                        title: 'Include Broking Commission?',
                        icon: 'question',
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: 'Yes',
                        denyButtonText: 'No',
                        width: '450px',
                        customClass: {
                            actions: 'swal_actions_btn',
                            confirmButton: 'order-2 btn-confirm',
                            denyButton: 'order-3 btn-deny',
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        var withBrokerage = null;
                        var postingYear = $('#posting_year').val() || '';
                        var postingQuarter = $('#posting_quarter').val() || '';

                        if (result.isConfirmed) {
                            withBrokerage = 1;
                            Swal.fire({
                                icon: 'success',
                                showConfirmButton: false,
                                confirmButtonText: 'OK',
                                title: 'Generating with Brokerage...',
                                text: '',
                                timer: 1500,
                                timerProgressBar: true
                            });
                        } else if (result.isDenied) {
                            withBrokerage = 0;
                            Swal.fire({
                                icon: 'success',
                                showConfirmButton: false,
                                confirmButtonText: 'OK',
                                title: 'Generating without Brokerage...',
                                text: '',
                                timer: 1500,
                                timerProgressBar: true
                            });
                        }

                        if (withBrokerage !== null) {
                            var url = CONFIG.routes.reinsurerCreditNoteView +
                                '?cover_no=' + encodeURIComponent(CONFIG.coverNo) +
                                '&endorsement_no=' + encodeURIComponent(CONFIG.endorsementNo) +
                                '&partner_no=' + encodeURIComponent(partnerNo) +
                                '&with_brokerage=' + withBrokerage +
                                '&note_type=' + encodeURIComponent(noteType) +
                                '&posting_year=' + encodeURIComponent(postingYear) +
                                '&posting_quarter=' + encodeURIComponent(postingQuarter);

                            window.open(url, '_blank');
                        }
                    });
                }
            });

            $(document).on('click', '.btn-send-statement', function() {
                var recipientName = $(this).data('name') || 'Reinsurer';
                var recipientEmail = $(this).data('email') || '';

                Utils.showConfirm(
                    'Send Statement',
                    'Send account statement to this reinsurer?',
                    function() {
                        StatementEmailComposer.open({
                            recipientType: 'reinsurer',
                            recipientName: recipientName,
                            recipientEmail: recipientEmail
                        });
                    }
                );
            });

            $(document).on('click', '.btn-send-cedant-statement', function() {
                var recipientName = $(this).data('name') || 'Cedant';
                var recipientEmail = $(this).data('email') || '';

                Utils.showConfirm(
                    'Send Statement',
                    'Send account statement to this cedant?',
                    function() {
                        StatementEmailComposer.open({
                            recipientType: 'cedant',
                            recipientName: recipientName,
                            recipientEmail: recipientEmail
                        });
                    }
                );
            });

            var TAB_QUERY_PARAM = 'tab';
            var tabStorageKey = 'portfolio_active_tab:' + (CONFIG.coverNo || '') + ':' + (CONFIG
                .endorsementNo || '');

            function normalizeTabTarget(value) {
                if (!value) {
                    return null;
                }

                return value.charAt(0) === '#' ? value : '#' + value;
            }

            function tabExists(target) {
                return !!$(
                    'button[data-bs-toggle="tab"][data-bs-target="' + target + '"]'
                ).length;
            }

            function persistActiveTab(target) {
                if (!target) {
                    return;
                }

                sessionStorage.setItem(tabStorageKey, target);

                var url = new URL(window.location.href);
                url.searchParams.set(TAB_QUERY_PARAM, target.replace('#', ''));
                window.history.replaceState({}, '', url.toString());
            }

            function restoreActiveTab() {
                var url = new URL(window.location.href);
                var tabFromQuery = normalizeTabTarget(url.searchParams.get(TAB_QUERY_PARAM));
                var tabFromHash = normalizeTabTarget(window.location.hash);
                var tabFromStorage = normalizeTabTarget(sessionStorage.getItem(tabStorageKey));
                var initialTab = null;

                if (tabFromQuery && tabExists(tabFromQuery)) {
                    initialTab = tabFromQuery;
                } else if (tabFromHash && tabExists(tabFromHash)) {
                    initialTab = tabFromHash;
                } else if (tabFromStorage && tabExists(tabFromStorage)) {
                    initialTab = tabFromStorage;
                }

                if (!initialTab) {
                    return;
                }

                var tabButton = document.querySelector(
                    'button[data-bs-toggle="tab"][data-bs-target="' + initialTab + '"]'
                );

                if (tabButton) {
                    bootstrap.Tab.getOrCreateInstance(tabButton).show();
                }
            }

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('data-bs-target');
                persistActiveTab(target);

                setTimeout(function() {
                    switch (target) {
                        case '#reinsurers-tab':
                            ReinsurersTable.adjustColumns();
                            break;
                        case '#docs-tab':
                            DocumentsTable.adjustColumns();
                            break;
                        case '#debit-items-tab':
                            DebitItemsTable.adjustColumns();
                            break;
                        case '#approvals-tab':
                            ApprovalsTable.adjustColumns();
                            break;
                    }
                }, 10);
            });

            restoreActiveTab();
        });
    </script>
@endpush
