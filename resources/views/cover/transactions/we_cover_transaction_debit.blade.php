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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .financial-card.commission {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .financial-card.portfolio {
            background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
        }

        .financial-card.adjustments {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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

        /* table.dataTable {
                                                                                                                    border-collapse: collapse !important;
                                                                                                                }

                                                                                                                table.dataTable thead th {
                                                                                                                    background-color: var(--bg-subtle);
                                                                                                                    font-weight: 600;
                                                                                                                    color: var(--text-dark);
                                                                                                                    font-size: 0.75rem;
                                                                                                                    text-transform: uppercase;
                                                                                                                    letter-spacing: 0.5px;
                                                                                                                    padding: 0.875rem 1rem;
                                                                                                                    border-bottom: 2px solid var(--border-light);
                                                                                                                }

                                                                                                                table.dataTable tbody td {
                                                                                                                    padding: 0.875rem 1rem;
                                                                                                                    vertical-align: middle;
                                                                                                                    font-size: 0.875rem;
                                                                                                                    color: var(--text-muted);
                                                                                                                    border-bottom: 1px solid #f3f4f6;
                                                                                                                }

                                                                                                                table.dataTable tbody tr:hover {
                                                                                                                    background-color: rgba(37, 99, 235, 0.02);
                                                                                                                } */

        /* .amount-cell {
                                                                                                                font-family: 'JetBrains Mono', 'Courier New', monospace;
                                                                                                                font-weight: 500;
                                                                                                                text-align: right;
                                                                                                            }

                                                                                                            .amount-cell--positive {
                                                                                                                color: var(--success-green);
                                                                                                            }

                                                                                                            .amount-cell--negative {
                                                                                                                color: var(--danger-red);
                                                                                                            } */

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
    </style>
@endsection

@section('content')
    @php
        $cover = $cover ?? null;
        $customer = $customer ?? null;
        $debitItems = $debitItems ?? collect();
        $reinsurers = $reinsurers ?? collect();
        $cedantDetails = $cedantDetails ?? null;
        $documents = $documents ?? collect();

        // Calculate totals - should be done in controller/service
        $totalGrossPremium = $debitItems->sum('gross_premium');
        $totalCommission = $debitItems->sum('commission_amount');
        $totalNetAmount = $debitItems->sum('net_amount');
        $totalReinsurerShare = $reinsurers->sum('share_premium');

        // Helper for currency formatting
        $formatCurrency = fn($amount, $currency = 'KES') => $currency . ' ' . number_format($amount ?? 0, 2);
    @endphp

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-1">Quarterly Debit Statement</h1>
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

    {{-- Quick Actions --}}
    <div class="quick-actions" role="toolbar" aria-label="Quick actions">
        <button type="button" class="btn btn-outline-dark quick-action-btn" id="btnPreviewSlip">
            <i class="ri-file-text-line"></i> Preview Slip
        </button>
        <button type="button" class="btn btn-outline-primary quick-action-btn" id="btnGenerateStatement">
            <i class="ri-file-list-3-line"></i> Generate Statement
        </button>
        <button type="button" class="btn btn-outline-success quick-action-btn" id="btnExportData">
            <i class="ri-download-2-line"></i> Export Data
        </button>
        <button type="button" class="btn btn-primary quick-action-btn" data-bs-toggle="modal"
            data-bs-target="#addDebitItemModal">
            <i class="ri-add-line"></i> Add Debit Item
        </button>
    </div>

    {{-- Financial Summary Cards --}}
    <div class="financial-grid">
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
            <div class="financial-label">Reinsurer Share</div>
            <div class="financial-value" id="summaryReinsurerShare">
                {{ $formatCurrency($totalReinsurerShare, $cover->currency ?? 'KES') }}
            </div>
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="summary-card mb-4">
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Cedant</span>
                <span class="summary-value highlight">{{ ucwords(strtolower($customer->name ?? 'N/A')) }}</span>
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
                    @if ($cover->cover_from && $cover->cover_to)
                        {{ \Carbon\Carbon::parse($cover->cover_from)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($cover->cover_to)->format('d M Y') }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Retention</span>
                <span class="summary-value">{{ number_format($cover->retention_percentage ?? 0, 1) }}%</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Ceded</span>
                <span class="summary-value">{{ number_format($cover->ceded_percentage ?? 0, 1) }}%</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Sum Insured</span>
                <span class="summary-value amount">
                    {{ $formatCurrency($cover->sum_insured ?? 0, $cover->currency ?? 'KES') }}
                </span>
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

    {{-- Main Content Card with Tabs --}}
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
                            <span class="badge bg-primary ms-1" id="debitItemsCount">{{ $debitItems->count() }}</span>
                        </button>

                        <button class="nav-link" id="nav-reinsurers-tab" data-bs-toggle="tab"
                            data-bs-target="#reinsurers-tab" type="button" role="tab" aria-controls="reinsurers-tab"
                            aria-selected="false">
                            <i class="ri-building-2-line me-1"></i> Reinsurers
                            <span class="badge bg-info ms-1" id="reinsurersCount">{{ $reinsurers->count() }}</span>
                        </button>

                        <button class="nav-link" id="nav-cedant-tab" data-bs-toggle="tab" data-bs-target="#cedant-tab"
                            type="button" role="tab" aria-controls="cedant-tab" aria-selected="false">
                            <i class="bx bx-briefcase me-1"></i> Cedant
                        </button>

                        <button class="nav-link" id="nav-approvals-tab" data-bs-toggle="tab"
                            data-bs-target="#approvals-tab" type="button" role="tab" aria-controls="approvals-tab"
                            aria-selected="false">
                            <i class="bx bx-medal me-1 align-middle"></i>Approvals
                            <span class="badge bg-warning ms-1"></span>
                        </button>

                        <button class="nav-link" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#docs-tab"
                            type="button" role="tab" aria-controls="docs-tab" aria-selected="false">
                            <i class="ri-printer-line me-1 align-middle"></i>Print-outs
                            <span class="badge bg-success ms-1" id="documentsCount">{{ $documents->count() }}</span>
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
                                                <th width="10%">Date</th>
                                                <th width="10%">Treaty Type</th>
                                                <th width="10%">Class</th>
                                                <th width="12%">Reinsurer</th>
                                                <th width="10%">Gross Premium</th>
                                                <th width="10%">Commission</th>
                                                <th width="10%">Net Amount</th>
                                                <th width="7%">Status</th>
                                                <th width="9%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="table-light">
                                            <tr class="fw-bold">
                                                <td colspan="6" class="text-end">Totals:</td>
                                                <td class="amount-cell" id="totalGrossPremium">-</td>
                                                <td class="amount-cell" id="totalCommission">-</td>
                                                <td class="amount-cell amount-cell--positive" id="totalNetAmount">-
                                                </td>
                                                <td colspan="2"></td>
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
                                                <th width="5%">#</th>
                                                <th width="20%">Reinsurer</th>
                                                <th width="15%">Contact Person</th>
                                                <th width="10%">Share %</th>
                                                <th width="10%">Commission</th>
                                                <th width="15%">Premium Share</th>
                                                <th width="15%">Sum Insured Share</th>
                                                <th width="8%">Status</th>
                                                <th width="8%">Net Amount</th>
                                                <th width="8%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Cedant Tab --}}
                    <div class="tab-pane fade" id="cedant-tab" role="tabpanel" aria-labelledby="nav-cedant-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                <div class="cedant-info-card mb-4" id="cedantDetailsCard">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <span class="info-label">Company Name</span>
                                                <span class="info-value" id="cedant_name">Loading...</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Registration No.</span>
                                                <span class="info-value" id="cedant_registration">Loading...</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Address</span>
                                                <span class="info-value" id="cedant_address">Loading...</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Contact Person</span>
                                                <span class="info-value" id="cedant_contact">Loading...</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <span class="info-label">Email</span>
                                                <span class="info-value" id="cedant_email">Loading...</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Phone</span>
                                                <span class="info-value" id="cedant_phone">Loading...</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Treaty Period</span>
                                                <span class="info-value" id="cedant_treaty_period">Loading...</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Treaty Capacity</span>
                                                <span class="info-value fw-semibold text-primary"
                                                    id="cedant_capacity">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Approvals Tab --}}
                    <div class="tab-pane fade" id="approvals-tab" role="tabpanel" aria-labelledby="nav-approvals-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                {{-- TODO: Implement approvals content --}}
                                <div class="empty-stated">
                                    <i class="ri-checkbox-circle-line"></i>
                                    <p>Approvals content coming soon</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Documents Tab --}}
                    <div class="tab-pane fade" id="docs-tab" role="tabpanel" aria-labelledby="nav-docs-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-header bg-transparent border-0 px-0 pt-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
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
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <h6 class="dropdown-header">Financial Documents</h6>
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
                                                <li>
                                                    <a class="dropdown-item" href="#" data-doc-type="statement">
                                                        <i class="ri-file-list-3-line me-2 text-info"></i>Statement of
                                                        Account
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <h6 class="dropdown-header">Treaty Documents</h6>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-doc-type="bordereau">
                                                        <i class="ri-table-line me-2 text-warning"></i>Bordereau
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-doc-type="closing_slip">
                                                        <i class="ri-file-paper-2-line me-2 text-danger"></i>Closing
                                                        Slip
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
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            'use strict';

            var CONFIG = {
                coverNo: '{{ $cover->cover_no ?? '' }}',
                endorsementNo: '{{ $cover->endorsement_no ?? '' }}',
                currency: '{{ $cover->currency ?? 'KES' }}',
                csrfToken: '{{ csrf_token() }}',
                routes: {
                    debitItems: '{{ route('treaty.debit-items.index', ['cover' => $cover->id ?? 0]) }}',
                    debitItemStore: '{{ route('treaty.debit-items.store') }}',
                    debitItemShow: '{{ url('treaty/debit-items') }}/:id',
                    debitItemUpdate: '{{ url('treaty/debit-items') }}/:id',
                    debitItemDelete: '{{ url('treaty/debit-items') }}/:id',
                    reinsurers: '{{ route('treaty.reinsurers.index', ['cover' => $cover->id ?? 0]) }}',
                    reinsurersList: '',
                    cedantDetails: '',
                    documents: '{{ route('treaty.documents.index', ['cover' => $cover->id ?? 0]) }}',
                    documentGenerate: '{{ route('treaty.documents.generate') }}',
                    documentDownload: '{{ url('treaty/documents') }}/:id/download',
                    previewSlip: '{{ route('treaty.slip.preview', ['cover' => $cover->id ?? 0]) }}',
                    generateStatement: '{{ route('treaty.statement.generate', ['cover' => $cover->id ?? 0]) }}',
                    exportData: '{{ route('treaty.export', ['cover' => $cover->id ?? 0]) }}'
                },
                dataTables: {
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
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
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: type,
                            title: this.escapeHtml(message),
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        console.log('[' + type.toUpperCase() + '] ' + message);
                        alert(message);
                    }
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
                            clearTimeout(timeout);
                            func.apply(context, args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
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
                                name: 'item_number'
                            },
                            {
                                data: 'posting_date',
                                name: 'item_date',
                                render: function(data) {
                                    return Utils.formatDate(data);
                                }
                            },
                            {
                                data: 'treaty_type',
                                name: 'treaty_type',
                                render: function(data) {
                                    return Utils.getTypeBadge(data);
                                }
                            },
                            // {
                            //     data: 'class_group',
                            //     name: 'class_group',
                            //     render: function(data, type, row) {
                            //         return '<span class="fw-medium">' + Utils.escapeHtml(row
                            //                 .class_group || '') + '</span><br>' +
                            //             '<small class="text-muted">' + Utils.escapeHtml(
                            //                 data || '') + '</small>';
                            //     }
                            // },
                            {
                                data: 'class_name',
                                name: 'class_name',
                                render: function(data, type, row) {
                                    return '<span class="fw-medium">' + Utils.escapeHtml(row
                                            .class_group || '') + '</span><br>' +
                                        '<small class="text-muted">' + Utils.escapeHtml(
                                            data || '') + '</small>';
                                }
                            },
                            {
                                data: 'reinsurer',
                                name: 'reinsurer'
                            },
                            {
                                data: 'gross_amount',
                                name: 'gross_premium',
                                className: 'amount-cell',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                }
                            },
                            {
                                data: 'commission_amount',
                                name: 'commission_amount',
                                className: 'amount-cell',
                                render: function(data, type, row) {
                                    var rate = row.commission_rate ? ' (' + row
                                        .commission_rate + '%)' : '';
                                    return Utils.formatCurrency(data) +
                                        '<br><small class="text-muted">' + rate +
                                        '</small>';
                                }
                            },
                            {
                                data: 'net_amount',
                                name: 'net_amount',
                                className: 'amount-cell amount-cell--positive',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                }
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data) {
                                    return Utils.getStatusBadge(data);
                                }
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
                                        '<button type="button" class="btn btn-outline-dark btn-action btn-edit-item" data-id="' +
                                        row.id + '" title="Edit">' +
                                        '<i class="ri-edit-line"></i>' +
                                        '</button>' +
                                        '<button type="button" class="btn btn-outline-danger btn-action btn-delete-item" data-id="' +
                                        row.id + '" title="Delete">' +
                                        '<i class="ri-delete-bin-line"></i>' +
                                        '</button>' +
                                        '</div>';
                                }
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
                        },
                        drawCallback: function(settings) {
                            self.updateTotals();
                        }
                    });
                },

                updateTotals: function() {
                    if (!this.table) return;

                    var totalGross = 0,
                        totalCommission = 0,
                        totalNet = 0;

                    this.table.rows().every(function() {
                        var data = this.data();
                        totalGross += parseFloat(data.gross_premium) || 0;
                        totalCommission += parseFloat(data.commission_amount) || 0;
                        totalNet += parseFloat(data.net_amount) || 0;
                    });

                    $('#totalGrossPremium').text(Utils.formatCurrency(totalGross));
                    $('#totalCommission').text(Utils.formatCurrency(totalCommission));
                    $('#totalNetAmount').text(Utils.formatCurrency(totalNet));
                },

                reload: function(resetPaging) {
                    if (this.table) {
                        this.table.ajax.reload(null, resetPaging || false);
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
                    this.table = $('#reinsurersTable').DataTable({
                        processing: true,
                        // serverSide: true,
                        // ajax: {
                        //     url: CONFIG.routes.reinsurers,
                        //     type: 'GET',
                        //     headers: {
                        //         'X-CSRF-TOKEN': CONFIG.csrfToken
                        //     },
                        //     data: function(d) {
                        //         d.cover_id = CONFIG.coverNo;
                        //     },
                        //     error: function(xhr, error) {
                        //         console.error('Reinsurers DataTable Error:', error);
                        //     }
                        // },
                        data: [],
                        columns: [{
                                data: null,
                                orderable: false,
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
                                }
                            },
                            {
                                data: 'contact_person',
                                name: 'contact_person'
                            },
                            {
                                data: 'share_percentage',
                                name: 'share_percentage',
                                className: 'text-center',
                                render: function(data) {
                                    return Utils.formatPercentage(data);
                                }
                            },
                            {
                                data: 'commission_rate',
                                name: 'commission_rate',
                                className: 'text-center',
                                render: function(data) {
                                    return Utils.formatPercentage(data);
                                }
                            },
                            {
                                data: 'share_premium',
                                name: 'share_premium',
                                className: 'amount-cell',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                }
                            },
                            {
                                data: 'share_sum_insured',
                                name: 'share_sum_insured',
                                className: 'amount-cell',
                                render: function(data) {
                                    return Utils.formatCurrency(data);
                                }
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data) {
                                    return Utils.getStatusBadge(data);
                                }
                            },
                            {
                                data: null,
                                name: 'net_amount',
                                className: 'amount-cell amount-cell--positive',
                                render: function(data, type, row) {
                                    var premium = parseFloat(row.share_premium) || 0;
                                    var rate = parseFloat(row.commission_rate) || 0;
                                    var net = premium - (premium * rate / 100);
                                    return Utils.formatCurrency(net);
                                }
                            },
                            {
                                data: null,
                                orderable: false,
                                render: function(data, type, row) {
                                    return '<div class="btn-group btn-group-sm">' +
                                        '<button class="btn btn-outline-primary btn-action btn-view-reinsurer" data-id="' +
                                        row.id + '" title="View">' +
                                        '<i class="ri-eye-line"></i>' +
                                        '</button>' +
                                        '<button class="btn btn-outline-info btn-action btn-send-statement" data-id="' +
                                        row.id + '" title="Send Statement">' +
                                        '<i class="ri-mail-send-line"></i>' +
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
                            emptyTable: '<div class="empty-state"><i class="ri-building-2-line"></i><p>No reinsurers found</p></div>'
                        }
                    });
                },

                reload: function(resetPaging) {
                    if (this.table) {
                        this.table.ajax.reload(null, resetPaging || false);
                    }
                },

                adjustColumns: function() {
                    if (this.table) {
                        this.table.columns.adjust();
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

                    this.table = $('#documentsTable').DataTable({
                        processing: true,
                        // serverSide: true,
                        // ajax: {
                        //     url: CONFIG.routes.documents,
                        //     type: 'GET',
                        //     headers: {
                        //         'X-CSRF-TOKEN': CONFIG.csrfToken
                        //     },
                        //     data: function(d) {
                        //         d.cover_id = CONFIG.coverNo;
                        //     },
                        //     dataSrc: function(json) {
                        //         self.updateStats(json.data || json);
                        //         return json.data || json;
                        //     },
                        //     error: function(xhr, error) {
                        //         console.error('Documents DataTable Error:', error);
                        //     }
                        // },
                        data: [],
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
                                    return '<i class="' + icon + ' me-2"></i>' + Utils
                                        .escapeHtml(data || '-');
                                }
                            },
                            {
                                data: 'reference',
                                name: 'reference'
                            },
                            {
                                data: 'description',
                                name: 'description'
                            },
                            {
                                data: 'generated_date',
                                name: 'generated_date',
                                render: function(data) {
                                    return Utils.formatDate(data);
                                }
                            },
                            {
                                data: 'generated_by',
                                name: 'generated_by'
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data) {
                                    return Utils.getStatusBadge(data);
                                }
                            },
                            {
                                data: null,
                                orderable: false,
                                render: function(data, type, row) {
                                    return '<div class="btn-group btn-group-sm">' +
                                        '<button class="btn btn-outline-primary btn-action btn-preview-doc" data-id="' +
                                        row.id + '" title="Preview">' +
                                        '<i class="ri-eye-line"></i>' +
                                        '</button>' +
                                        '<button class="btn btn-outline-success btn-action btn-download-doc" data-id="' +
                                        row.id + '" title="Download">' +
                                        '<i class="ri-download-line"></i>' +
                                        '</button>' +
                                        '<button class="btn btn-outline-info btn-action btn-send-doc" data-id="' +
                                        row.id + '" title="Send">' +
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
                            emptyTable: '<div class="empty-state"><i class="ri-file-list-3-line"></i><p>No documents generated yet</p></div>'
                        }
                    });
                },

                updateStats: function(data) {
                    var stats = {
                        generated: 0,
                        sent: 0,
                        signed: 0
                    };

                    (Array.isArray(data) ? data : []).forEach(function(doc) {
                        var status = (doc.status || '').toLowerCase();
                        if (status === 'generated') stats.generated++;
                        else if (status === 'sent') stats.sent++;
                        else if (status === 'signed') stats.signed++;
                    });

                    $('#docGenerated').text(stats.generated);
                    $('#docSent').text(stats.sent);
                    $('#docSigned').text(stats.signed);
                },

                reload: function(resetPaging) {
                    if (this.table) {
                        this.table.ajax.reload(null, resetPaging || false);
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
                    var modal = new bootstrap.Modal(document.getElementById('viewItemModal'));
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
                            $('#viewItemModalTitle').text('Item: ' + (item.item_number || 'N/A'));
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
                        Utils.escapeHtml(item.item_number || '-') + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Item Date</span><span class="info-value">' +
                        Utils.formatDate(item.item_date) + '</span></div>' +
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
                        Utils.formatCurrency(item.gross_premium) + '</span></div>' +
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
                            $form.find('[name="item_date"]').val(item.item_date);
                            $form.find('[name="class_group"]').val(item.class_group);
                            $form.find('[name="class_name"]').val(item.class_name);
                            $form.find('[name="reinsurer_id"]').val(item.reinsurer_id);
                            $form.find('[name="gross_premium"]').val(item.gross_premium);
                            $form.find('[name="commission_rate"]').val(item.commission_rate);
                            $form.find('[name="status"]').val(item.status);
                            $form.find('#commissionAmount').val(Utils.formatCurrency(item
                                .commission_amount));
                            $form.find('#netAmount').val(Utils.formatCurrency(item.net_amount));

                            $form.data('edit-id', id);

                            new bootstrap.Modal(document.getElementById('addDebitItemModal')).show();
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
                                    Utils.showToast((xhr.responseJSON && xhr.responseJSON
                                        .message) || 'Failed to delete item', 'error');
                                });
                        }
                    );
                }
            };

            var DocumentsManager = {
                generate: function(type) {
                    Utils.showLoading(true);

                    Utils.ajax({
                            url: CONFIG.routes.documentGenerate,
                            type: 'POST',
                            data: {
                                cover_id: CONFIG.coverNo,
                                document_type: type
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
                            Utils.showToast((xhr.responseJSON && xhr.responseJSON.message) ||
                                'Failed to generate document', 'error');
                        });
                },

                preview: function(id) {
                    window.open(CONFIG.routes.documentDownload.replace(':id', id) + '?preview=1', '_blank');
                },

                download: function(id) {
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

            var CedantManager = {
                load: function() {
                    var self = this;

                    Utils.ajax({
                            url: CONFIG.routes.cedantDetails,
                            type: 'GET'
                        })
                        .done(function(response) {
                            var cedant = response.data || response;
                            self.populate(cedant);
                        })
                        .fail(function() {
                            $('#cedantDetailsCard').html(
                                '<div class="alert alert-warning">Failed to load cedant details</div>'
                            );
                        });
                },

                populate: function(cedant) {
                    $('#cedant_name').text(cedant.name || '-');
                    $('#cedant_registration').text(cedant.registration_no || '-');
                    $('#cedant_address').text(cedant.address || '-');
                    $('#cedant_contact').text((cedant.contact_person || '-') + (cedant.designation ? ' (' +
                        cedant.designation + ')' : ''));
                    $('#cedant_email').text(cedant.email || '-');
                    $('#cedant_phone').text(cedant.phone || '-');
                    $('#cedant_treaty_period').text(cedant.treaty_period || '-');
                    $('#cedant_capacity').text(Utils.formatCurrency(cedant.treaty_capacity));
                }
            };

            var DebitItemForm = {
                init: function() {
                    this.loadReinsurers();
                    this.bindEvents();
                },

                loadReinsurers: function() {
                    Utils.ajax({
                            url: CONFIG.routes.reinsurersList,
                            type: 'GET'
                        })
                        .done(function(response) {
                            var reinsurers = response.data || response;
                            var $select = $('#reinsurerId');

                            $select.find('option:not(:first)').remove();

                            (Array.isArray(reinsurers) ? reinsurers : []).forEach(function(r) {
                                $select.append('<option value="' + r.id + '">' + Utils
                                    .escapeHtml(r.name) + '</option>');
                            });
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
                            bootstrap.Modal.getInstance(document.getElementById('addDebitItemModal'))
                                .hide();
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
                                Utils.showToast((xhr.responseJSON && xhr.responseJSON.message) ||
                                    'Failed to save item', 'error');
                            }
                        });
                },

                resetForm: function() {
                    var $form = $('#addDebitItemForm');
                    $form[0].reset();
                    $form.removeClass('was-validated');
                    $form.removeData('edit-id');
                    $form.find('.is-invalid').removeClass('is-invalid');
                    $('#addDebitItemModalLabel').html(
                        '<i class="ri-add-circle-line text-primary me-2"></i>Add Debit Item');
                    $('#commissionAmount, #netAmount').val('');
                }
            };

            DebitItemsTable.init();
            ReinsurersTable.init();
            DocumentsTable.init();
            CedantManager.load();
            DebitItemForm.init();

            $('#btnPreviewSlip').on('click', function() {
                window.open(CONFIG.routes.previewSlip, '_blank');
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
                        Utils.showToast((xhr.responseJSON && xhr.responseJSON.message) ||
                            'Failed to generate statement', 'error');
                    });
            });

            $('#btnExportData').on('click', function() {
                window.location.href = CONFIG.routes.exportData;
            });

            $('#btnRefreshDocs').on('click', function() {
                DocumentsTable.reload();
            });

            $(document).on('click', '[data-doc-type]', function(e) {
                e.preventDefault();
                DocumentsManager.generate($(this).data('doc-type'));
            });

            $(document).on('click', '.btn-view-item', function() {
                DebitItemManager.view($(this).data('id'));
            });

            $(document).on('click', '.btn-edit-item', function() {
                DebitItemManager.edit($(this).data('id'));
            });

            $(document).on('click', '.btn-delete-item', function() {
                DebitItemManager.delete($(this).data('id'));
            });

            $(document).on('click', '.btn-preview-doc', function() {
                DocumentsManager.preview($(this).data('id'));
            });

            $(document).on('click', '.btn-download-doc', function() {
                DocumentsManager.download($(this).data('id'));
            });

            $(document).on('click', '.btn-send-doc', function() {
                DocumentsManager.send($(this).data('id'));
            });

            $(document).on('click', '.btn-view-reinsurer', function() {
                Utils.showToast('Reinsurer details view coming soon', 'info');
            });

            $(document).on('click', '.btn-send-statement', function() {
                Utils.showConfirm(
                    'Send Statement',
                    'Send account statement to this reinsurer?',
                    function() {
                        Utils.showToast('Statement sent successfully', 'success');
                    }
                );
            });

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('data-bs-target');

                setTimeout(function() {
                    if (target === '#reinsurers-tab') {
                        ReinsurersTable.adjustColumns();
                    } else if (target === '#docs-tab') {
                        DocumentsTable.adjustColumns();
                    } else if (target === '#debit-items-tab') {
                        DebitItemsTable.adjustColumns();
                    }
                }, 10);
            });
        });
    </script>
@endpush
