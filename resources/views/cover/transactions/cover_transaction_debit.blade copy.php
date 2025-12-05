@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
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

    /* Card styling */
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

    /* Summary card styling */
    .summary-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        padding: 1.25rem;
        border-left: 4px solid var(--primary-blue);
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

    /* Financial summary cards */
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

    /* Tab styling */
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

    /* Status badges */
    .status-badge {
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 0.375rem 0.625rem;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .status-paid,
    .status-approved,
    .status-completed,
    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-pending,
    .status-in_progress,
    .status-calculated {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-overdue,
    .status-rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }

    /* Type badges */
    .type-badge {
        font-size: 0.6875rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        text-transform: capitalize;
    }

    .type-premium {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .type-adjustment {
        background-color: #f3e8ff;
        color: #6b21a8;
    }

    .type-additional {
        background-color: #d1fae5;
        color: #065f46;
    }

    .type-deposit {
        background-color: #fef3c7;
        color: #92400e;
    }

    .type-reinstatement {
        background-color: #ffe4e6;
        color: #9f1239;
    }

    /* DataTable styling */
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

    table.dataTable {
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
    }

    .amount-cell {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-weight: 500;
        text-align: right;
    }

    .amount-positive {
        color: var(--success-green);
    }

    .amount-negative {
        color: var(--danger-red);
    }

    /* Action buttons */
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

    .quick-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .quick-action-btn {
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: 1px solid var(--border-light);
        background: white;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quick-action-btn:hover {
        border-color: var(--primary-blue);
        color: var(--primary-blue);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.1);
    }

    .quick-action-btn.primary {
        background: var(--primary-blue);
        color: white;
        border-color: var(--primary-blue);
    }

    .quick-action-btn.primary:hover {
        background: var(--primary-blue-dark);
        color: white;
    }

    /* Tab content header */
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

    /* Empty state */
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

    /* Responsive */
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
// Get cover and customer from controller or use defaults
$cover =
$cover ??
(object) [
'id' => 1,
'cover_no' => 'TRY-2024-001',
'policy_number' => 'POL-2024-001',
'treaty_name' => 'Property Treaty 2024',
'treaty_type' => 'Quota Share',
'class_of_business' => 'Property All Risks',
'start_date' => '2024-01-01',
'end_date' => '2024-12-31',
'sum_insured' => 150000000,
'premium' => 2500000,
'ceded_premium' => 1750000,
'retention_percentage' => 30.0,
'ceded_percentage' => 70.0,
'currency' => 'KES',
'status' => 'active',
];

$customer =
$customer ??
(object) [
'id' => 1,
'name' => 'Heritage Insurance Company',
'email' => 'underwriting@heritage.co.ke',
'phone' => '+254 20 123 4567',
];

// Debit Items - Using realistic treaty reinsurance data
$debitItems =
$debitItems ??
collect([
(object) [
'id' => 1,
'item_number' => 'ITM-2024-00001',
'treaty_type' => 'SURPLUS',
'item_date' => '2024-01-15',
'class_group' => 'FIRE',
'class_name' => 'Fire Industrial',
'gross_premium' => 525000,
'commission_rate' => 10.0,
'commission_amount' => 52500,
'net_amount' => 472500,
'status' => 'paid',
'reinsurer' => 'Swiss Re Africa',
],
(object) [
'id' => 2,
'item_number' => 'ITM-2024-00002',
'treaty_type' => 'QUOTA SHARE',
'item_date' => '2024-01-15',
'class_group' => 'FIRE',
'class_name' => 'Fire Domestic',
'gross_premium' => 437500,
'commission_rate' => 10.0,
'commission_amount' => 43750,
'net_amount' => 393750,
'status' => 'paid',
'reinsurer' => 'Munich Re Kenya',
],
(object) [
'id' => 3,
'item_number' => 'ITM-2024-00003',
'treaty_type' => 'SURPLUS',
'item_date' => '2024-01-20',
'class_group' => 'ENGINEERING',
'class_name' => 'CAR/EAR',
'gross_premium' => 262500,
'commission_rate' => 12.5,
'commission_amount' => 32812.5,
'net_amount' => 229687.5,
'status' => 'paid',
'reinsurer' => 'Hannover Re',
],
(object) [
'id' => 4,
'item_number' => 'ITM-2024-00004',
'treaty_type' => 'QUOTA SHARE',
'item_date' => '2024-02-01',
'class_group' => 'MARINE',
'class_name' => 'Marine Cargo',
'gross_premium' => 180000,
'commission_rate' => 15.0,
'commission_amount' => 27000,
'net_amount' => 153000,
'status' => 'pending',
'reinsurer' => 'Swiss Re Africa',
],
(object) [
'id' => 5,
'item_number' => 'ITM-2024-00005',
'treaty_type' => 'SURPLUS',
'item_date' => '2024-02-15',
'class_group' => 'FIRE',
'class_name' => 'Fire Industrial',
'gross_premium' => 750000,
'commission_rate' => 10.0,
'commission_amount' => 75000,
'net_amount' => 675000,
'status' => 'paid',
'reinsurer' => 'Africa Re',
],
(object) [
'id' => 6,
'item_number' => 'ITM-2024-00006',
'treaty_type' => 'QUOTA SHARE',
'item_date' => '2024-03-01',
'class_group' => 'MOTOR',
'class_name' => 'Motor Commercial',
'gross_premium' => 320000,
'commission_rate' => 20.0,
'commission_amount' => 64000,
'net_amount' => 256000,
'status' => 'paid',
'reinsurer' => 'Kenya Re',
],
(object) [
'id' => 7,
'item_number' => 'ITM-2024-00007',
'treaty_type' => 'SURPLUS',
'item_date' => '2024-03-10',
'class_group' => 'ENGINEERING',
'class_name' => 'Machinery Breakdown',
'gross_premium' => 450000,
'commission_rate' => 12.5,
'commission_amount' => 56250,
'net_amount' => 393750,
'status' => 'pending',
'reinsurer' => 'Munich Re Kenya',
],
(object) [
'id' => 8,
'item_number' => 'ITM-2024-00008',
'treaty_type' => 'QUOTA SHARE',
'item_date' => '2024-03-20',
'class_group' => 'AVIATION',
'class_name' => 'Aviation Hull',
'gross_premium' => 1200000,
'commission_rate' => 7.5,
'commission_amount' => 90000,
'net_amount' => 1110000,
'status' => 'overdue',
'reinsurer' => 'Swiss Re Africa',
],
]);

// Reinsurers participating in this treaty
$reinsurers =
$reinsurers ??
collect([
(object) [
'id' => 1,
'name' => 'Swiss Re Africa',
'share_percentage' => 30.0,
'share_premium' => 525000,
'share_sum_insured' => 45000000,
'commission_rate' => 10.0,
'status' => 'active',
'contact_person' => 'James Ochieng',
'email' => 'james.ochieng@swissre.com',
],
(object) [
'id' => 2,
'name' => 'Munich Re Kenya',
'share_percentage' => 25.0,
'share_premium' => 437500,
'share_sum_insured' => 37500000,
'commission_rate' => 10.0,
'status' => 'active',
'contact_person' => 'Sarah Wanjiku',
'email' => 'sarah.wanjiku@munichre.com',
],
(object) [
'id' => 3,
'name' => 'Hannover Re',
'share_percentage' => 15.0,
'share_premium' => 262500,
'share_sum_insured' => 22500000,
'commission_rate' => 12.5,
'status' => 'active',
'contact_person' => 'Peter Mwangi',
'email' => 'peter.mwangi@hannover-re.com',
],
(object) [
'id' => 4,
'name' => 'Africa Re',
'share_percentage' => 20.0,
'share_premium' => 350000,
'share_sum_insured' => 30000000,
'commission_rate' => 10.0,
'status' => 'active',
'contact_person' => 'Grace Kimani',
'email' => 'grace.kimani@africa-re.com',
],
(object) [
'id' => 5,
'name' => 'Kenya Re',
'share_percentage' => 10.0,
'share_premium' => 175000,
'share_sum_insured' => 15000000,
'commission_rate' => 15.0,
'status' => 'active',
'contact_person' => 'John Kamau',
'email' => 'john.kamau@kenyare.co.ke',
],
]);

// Cedant (Ceding Company) Information
$cedantDetails =
$cedantDetails ??
(object) [
'name' => $customer->name ?? 'Heritage Insurance Company',
'registration_no' => 'IRA/2024/001',
'address' => 'P.O. Box 12345-00100, Nairobi, Kenya',
'contact_person' => 'Mary Njeri',
'designation' => 'Reinsurance Manager',
'email' => 'mary.njeri@heritage.co.ke',
'phone' => '+254 722 123 456',
'treaty_year' => '2024',
'treaty_period' => '01 January 2024 - 31 December 2024',
'retention_limit' => 50000000,
'treaty_capacity' => 500000000,
];

// Print Outs / Documents
$documents =
$documents ??
collect([
(object) [
'id' => 1,
'document_type' => 'Debit Note',
'reference' => 'DN-2024-001',
'description' => 'Q1 2024 Treaty Premium Debit',
'generated_date' => '2024-04-01',
'generated_by' => 'System',
'status' => 'generated',
],
(object) [
'id' => 2,
'document_type' => 'Credit Note',
'reference' => 'CN-2024-001',
'description' => 'Q1 2024 Commission Credit',
'generated_date' => '2024-04-01',
'generated_by' => 'System',
'status' => 'generated',
],
(object) [
'id' => 3,
'document_type' => 'Statement of Account',
'reference' => 'SOA-2024-001',
'description' => 'Q1 2024 Account Statement',
'generated_date' => '2024-04-05',
'generated_by' => 'Peter Kamau',
'status' => 'sent',
],
(object) [
'id' => 4,
'document_type' => 'Bordereau',
'reference' => 'BDX-2024-Q1',
'description' => 'Q1 2024 Premium Bordereau',
'generated_date' => '2024-04-10',
'generated_by' => 'System',
'status' => 'generated',
],
(object) [
'id' => 5,
'document_type' => 'Closing Slip',
'reference' => 'CS-2024-001',
'description' => 'Treaty Closing Slip 2024',
'generated_date' => '2024-01-15',
'generated_by' => 'Mary Wanjiku',
'status' => 'signed',
],
]);

// Calculate totals
$totalGrossPremium = $debitItems->sum('gross_premium');
$totalCommission = $debitItems->sum('commission_amount');
$totalNetAmount = $debitItems->sum('net_amount');
$totalReinsurerShare = $reinsurers->sum('share_premium');
@endphp

<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <div>
        <h1 class="page-title fw-semibold fs-18 mb-1">Treaty Debit Statement</h1>
        <p class="text-muted mb-0">{{ $cover->cover_no }} - {{ $cover->treaty_name }}</p>
    </div>
    <div class="ms-md-1 ms-0 mt-3 mt-md-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') ?? '#' }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') ?? '#' }}">Covers</a></li>
                <li class="breadcrumb-item"><a href="#">Treaty</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $cover->cover_no }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <button class="quick-action-btn primary" onclick="previewSlip()">
        <i class="ri-file-text-line"></i> Preview Slip
    </button>
    <button class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#addDebitItemModal">
        <i class="ri-add-line"></i> Add Debit Item
    </button>
    <button class="quick-action-btn" onclick="generateStatement()">
        <i class="ri-file-list-3-line"></i> Generate Statement
    </button>
    <button class="quick-action-btn" onclick="exportData()">
        <i class="ri-download-2-line"></i> Export
    </button>
</div>

<!-- Financial Summary Cards -->
<div class="financial-grid">
    <div class="financial-card debits">
        <div class="financial-label">Total Gross Premium</div>
        <div class="financial-value">{{ $cover->currency }} {{ number_format($totalGrossPremium, 2) }}</div>
    </div>
    <div class="financial-card commission">
        <div class="financial-label">Total Commission</div>
        <div class="financial-value">{{ $cover->currency }} {{ number_format($totalCommission, 2) }}</div>
    </div>
    <div class="financial-card portfolio">
        <div class="financial-label">Net Amount Due</div>
        <div class="financial-value">{{ $cover->currency }} {{ number_format($totalNetAmount, 2) }}</div>
    </div>
    <div class="financial-card adjustments">
        <div class="financial-label">Reinsurer Share</div>
        <div class="financial-value">{{ $cover->currency }} {{ number_format($totalReinsurerShare, 2) }}</div>
    </div>
</div>

<!-- Summary Card -->
<div class="summary-card mb-4">
    <div class="summary-grid">
        <div class="summary-item">
            <span class="summary-label">Cedant</span>
            <span class="summary-value highlight">{{ ucwords(strtolower($customer->name)) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Treaty Type</span>
            <span class="summary-value">{{ $cover->treaty_type }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Class of Business</span>
            <span class="summary-value">{{ $cover->class_of_business }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Policy Period</span>
            <span class="summary-value">{{ \Carbon\Carbon::parse($cover->start_date)->format('d/m/Y') }} -
                {{ \Carbon\Carbon::parse($cover->end_date)->format('d/m/Y') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Retention</span>
            <span class="summary-value">{{ number_format($cover->retention_percentage, 1) }}%</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Ceded</span>
            <span class="summary-value">{{ number_format($cover->ceded_percentage, 1) }}%</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Sum Insured</span>
            <span class="summary-value amount">{{ $cover->currency }}
                {{ number_format($cover->sum_insured, 2) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Status</span>
            <span class="status-badge status-{{ $cover->status }}">{{ ucfirst($cover->status) }}</span>
        </div>
    </div>
</div>

<!-- Main Content Card with Tabs -->
<div class="info-card">
    <ul class="nav nav-tabs custom-tabs" id="transactionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="debits-tab" data-bs-toggle="tab" data-bs-target="#debits" type="button"
                role="tab" aria-controls="debits" aria-selected="true">
                <i class="ri-file-list-2-line"></i> Debit Items
                <span class="badge bg-primary">{{ $debitItems->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reinsurers-tab" data-bs-toggle="tab" data-bs-target="#reinsurers"
                type="button" role="tab" aria-controls="reinsurers" aria-selected="false">
                <i class="ri-building-2-line"></i> Reinsurers
                <span class="badge bg-secondary">{{ $reinsurers->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cedant-tab" data-bs-toggle="tab" data-bs-target="#cedant" type="button"
                role="tab" aria-controls="cedant" aria-selected="false">
                <i class="ri-briefcase-line"></i> Cedant
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents"
                type="button" role="tab" aria-controls="documents" aria-selected="false">
                <i class="ri-printer-line"></i> Print Outs
                <span class="badge bg-secondary">{{ $documents->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="transactionTabsContent">
        <!-- Debit Items Tab -->
        <div class="tab-pane fade show active" id="debits" role="tabpanel" aria-labelledby="debits-tab">
            <div class="tab-header">
                <h6><i class="ri-list-check-2"></i> Treaty Debit Items</h6>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDebitItemModal">
                    <i class="ri-add-line"></i> Add Item
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="debitItemsTable" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Item No.</th>
                            <th>Treaty</th>
                            <th>Date</th>
                            <th>Class Group</th>
                            <th>Class Name</th>
                            <th>Reinsurer</th>
                            <th>Commission %</th>
                            <th class="text-end">Gross Premium</th>
                            <th class="text-end">Commission</th>
                            <th class="text-end">Net Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($debitItems as $item)
                        <tr>
                            <td><strong>{{ $item->item_number }}</strong></td>
                            <td><span class="type-badge type-premium">{{ $item->treaty_type }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($item->item_date)->format('d/m/Y') }}</td>
                            <td>{{ $item->class_group }}</td>
                            <td>{{ $item->class_name }}</td>
                            <td>{{ $item->reinsurer }}</td>
                            <td class="text-center">{{ number_format($item->commission_rate, 1) }}%</td>
                            <td class="amount-cell">{{ number_format($item->gross_premium, 2) }}</td>
                            <td class="amount-cell amount-negative">
                                {{ number_format($item->commission_amount, 2) }}
                            </td>
                            <td class="amount-cell amount-positive">{{ number_format($item->net_amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $item->status }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-action"
                                        onclick="viewItem({{ $item->id }})" title="View">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-action"
                                        onclick="editItem({{ $item->id }})" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background-color: var(--bg-subtle);">
                            <td colspan="7" class="text-end">Totals:</td>
                            <td class="amount-cell">{{ number_format($totalGrossPremium, 2) }}</td>
                            <td class="amount-cell amount-negative">{{ number_format($totalCommission, 2) }}</td>
                            <td class="amount-cell amount-positive">{{ number_format($totalNetAmount, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Reinsurers Tab -->
        <div class="tab-pane fade" id="reinsurers" role="tabpanel" aria-labelledby="reinsurers-tab">
            <div class="tab-header">
                <h6><i class="ri-building-2-line"></i> Participating Reinsurers</h6>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addReinsurerModal">
                    <i class="ri-add-line"></i> Add Reinsurer
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="reinsurersTable" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Reinsurer</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th class="text-center">Share %</th>
                            <th class="text-center">Commission %</th>
                            <th class="text-end">Share Premium</th>
                            <th class="text-end">Share Sum Insured</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reinsurers as $reinsurer)
                        <tr>
                            <td><strong>{{ $reinsurer->name }}</strong></td>
                            <td>{{ $reinsurer->contact_person }}</td>
                            <td><a href="mailto:{{ $reinsurer->email }}">{{ $reinsurer->email }}</a></td>
                            <td class="text-center">{{ number_format($reinsurer->share_percentage, 1) }}%</td>
                            <td class="text-center">{{ number_format($reinsurer->commission_rate, 1) }}%</td>
                            <td class="amount-cell amount-positive">
                                {{ number_format($reinsurer->share_premium, 2) }}
                            </td>
                            <td class="amount-cell">{{ number_format($reinsurer->share_sum_insured, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $reinsurer->status }}">
                                    {{ ucfirst($reinsurer->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-action"
                                        onclick="viewReinsurer({{ $reinsurer->id }})" title="View">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-action"
                                        onclick="editReinsurer({{ $reinsurer->id }})" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background-color: var(--bg-subtle);">
                            <td colspan="3" class="text-end">Totals:</td>
                            <td class="text-center">{{ number_format($reinsurers->sum('share_percentage'), 1) }}%</td>
                            <td></td>
                            <td class="amount-cell amount-positive">
                                {{ number_format($reinsurers->sum('share_premium'), 2) }}
                            </td>
                            <td class="amount-cell">{{ number_format($reinsurers->sum('share_sum_insured'), 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Cedant Tab -->
        <div class="tab-pane fade" id="cedant" role="tabpanel" aria-labelledby="cedant-tab">
            <div class="tab-header">
                <h6><i class="ri-briefcase-line"></i> Ceding Company Details</h6>
                <button class="btn btn-outline-primary btn-sm" onclick="editCedant()">
                    <i class="ri-edit-line"></i> Edit Details
                </button>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="summary-card mb-3">
                        <h6 class="section-title" style="font-size: 1rem;"><i class="ri-building-line"></i> Company
                            Information</h6>
                        <div class="summary-item">
                            <span class="summary-label">Company Name</span>
                            <span class="summary-value">{{ $cedantDetails->name }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Registration No.</span>
                            <span class="summary-value">{{ $cedantDetails->registration_no }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Address</span>
                            <span class="summary-value">{{ $cedantDetails->address }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-card mb-3">
                        <h6 class="section-title" style="font-size: 1rem;"><i class="ri-user-line"></i> Contact
                            Information</h6>
                        <div class="summary-item">
                            <span class="summary-label">Contact Person</span>
                            <span class="summary-value">{{ $cedantDetails->contact_person }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Designation</span>
                            <span class="summary-value">{{ $cedantDetails->designation }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Email</span>
                            <span class="summary-value"><a
                                    href="mailto:{{ $cedantDetails->email }}">{{ $cedantDetails->email }}</a></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Phone</span>
                            <span class="summary-value">{{ $cedantDetails->phone }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="summary-card">
                        <h6 class="section-title" style="font-size: 1rem;"><i class="ri-file-shield-line"></i> Treaty
                            Details</h6>
                        <div class="summary-grid">
                            <div class="summary-item">
                                <span class="summary-label">Treaty Year</span>
                                <span class="summary-value">{{ $cedantDetails->treaty_year }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Treaty Period</span>
                                <span class="summary-value">{{ $cedantDetails->treaty_period }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Retention Limit</span>
                                <span class="summary-value amount">{{ $cover->currency }}
                                    {{ number_format($cedantDetails->retention_limit, 2) }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Treaty Capacity</span>
                                <span class="summary-value amount">{{ $cover->currency }}
                                    {{ number_format($cedantDetails->treaty_capacity, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents/Print Outs Tab -->
        <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
            <div class="tab-header">
                <h6><i class="ri-printer-line"></i> Generated Documents</h6>
                <div class="btn-group">
                    <button class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ri-add-line"></i> Generate New
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="generateDocument('debit_note')"><i
                                    class="ri-file-text-line me-2"></i>Debit Note</a></li>
                        <li><a class="dropdown-item" href="#" onclick="generateDocument('credit_note')"><i
                                    class="ri-file-text-line me-2"></i>Credit Note</a></li>
                        <li><a class="dropdown-item" href="#" onclick="generateDocument('statement')"><i
                                    class="ri-file-list-3-line me-2"></i>Statement of Account</a></li>
                        <li><a class="dropdown-item" href="#" onclick="generateDocument('bordereau')"><i
                                    class="ri-table-line me-2"></i>Bordereau</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" onclick="generateDocument('closing_slip')"><i
                                    class="ri-file-shield-line me-2"></i>Closing Slip</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="documentsTable" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Document Type</th>
                            <th>Description</th>
                            <th>Generated Date</th>
                            <th>Generated By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $doc)
                        <tr>
                            <td><strong>{{ $doc->reference }}</strong></td>
                            <td><span class="type-badge type-premium">{{ $doc->document_type }}</span></td>
                            <td>{{ $doc->description }}</td>
                            <td>{{ \Carbon\Carbon::parse($doc->generated_date)->format('d/m/Y') }}</td>
                            <td>{{ $doc->generated_by }}</td>
                            <td>
                                <span
                                    class="status-badge status-{{ $doc->status == 'signed' ? 'approved' : ($doc->status == 'sent' ? 'pending' : 'active') }}">
                                    {{ ucfirst($doc->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-action"
                                        onclick="viewDocument({{ $doc->id }})" title="View">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button class="btn btn-outline-success btn-action"
                                        onclick="downloadDocument({{ $doc->id }})" title="Download">
                                        <i class="ri-download-line"></i>
                                    </button>
                                    <button class="btn btn-outline-info btn-action"
                                        onclick="emailDocument({{ $doc->id }})" title="Email">
                                        <i class="ri-mail-send-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Debit Item Modal -->
<div class="modal fade" id="addDebitItemModal" tabindex="-1" aria-labelledby="addDebitItemModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDebitItemModalLabel">
                    <i class="ri-add-circle-line me-2"></i>Add Debit Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDebitItemForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Treaty Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="treaty_type" required>
                                <option value="">Select Treaty Type</option>
                                <option value="SURPLUS">Surplus</option>
                                <option value="QUOTA SHARE">Quota Share</option>
                                <option value="EXCESS OF LOSS">Excess of Loss</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Item Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="item_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class Group <span class="text-danger">*</span></label>
                            <select class="form-select" name="class_group" required>
                                <option value="">Select Class Group</option>
                                <option value="FIRE">Fire</option>
                                <option value="ENGINEERING">Engineering</option>
                                <option value="MARINE">Marine</option>
                                <option value="MOTOR">Motor</option>
                                <option value="AVIATION">Aviation</option>
                                <option value="MISCELLANEOUS">Miscellaneous</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="class_name"
                                placeholder="e.g., Fire Industrial" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reinsurer <span class="text-danger">*</span></label>
                            <select class="form-select" name="reinsurer_id" required>
                                <option value="">Select Reinsurer</option>
                                @foreach ($reinsurers as $reinsurer)
                                <option value="{{ $reinsurer->id }}">{{ $reinsurer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gross Premium <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $cover->currency }}</span>
                                <input type="number" class="form-control" name="gross_premium" step="0.01"
                                    min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission Rate (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="commission_rate" step="0.01"
                                min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $cover->currency }}</span>
                                <input type="number" class="form-control" name="commission_amount" step="0.01"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Net Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $cover->currency }}</span>
                                <input type="number" class="form-control" name="net_amount" step="0.01"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveDebitItem()">
                    <i class="ri-save-line me-1"></i>Save Item
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTables
        const debitItemsTable = $('#debitItemsTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            order: [
                [2, 'desc']
            ], // Order by date descending
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search items...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ items",
                paginate: {
                    first: '<i class="ri-skip-back-line"></i>',
                    last: '<i class="ri-skip-forward-line"></i>',
                    next: '<i class="ri-arrow-right-s-line"></i>',
                    previous: '<i class="ri-arrow-left-s-line"></i>'
                }
            },
            columnDefs: [{
                    targets: [7, 8, 9],
                    className: 'text-end'
                },
                {
                    targets: [-1],
                    orderable: false,
                    searchable: false
                }
            ]
        });

        const reinsurersTable = $('#reinsurersTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [3, 'desc']
            ], // Order by share percentage
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search reinsurers...",
            },
            columnDefs: [{
                    targets: [5, 6],
                    className: 'text-end'
                },
                {
                    targets: [-1],
                    orderable: false,
                    searchable: false
                }
            ]
        });

        const documentsTable = $('#documentsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [3, 'desc']
            ], // Order by date
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search documents...",
            },
            columnDefs: [{
                targets: [-1],
                orderable: false,
                searchable: false
            }]
        });

        // Auto-calculate commission and net amount
        const grossPremiumInput = document.querySelector('input[name="gross_premium"]');
        const commissionRateInput = document.querySelector('input[name="commission_rate"]');
        const commissionAmountInput = document.querySelector('input[name="commission_amount"]');
        const netAmountInput = document.querySelector('input[name="net_amount"]');

        function calculateAmounts() {
            const grossPremium = parseFloat(grossPremiumInput.value) || 0;
            const commissionRate = parseFloat(commissionRateInput.value) || 0;

            const commissionAmount = (grossPremium * commissionRate) / 100;
            const netAmount = grossPremium - commissionAmount;

            commissionAmountInput.value = commissionAmount.toFixed(2);
            netAmountInput.value = netAmount.toFixed(2);
        }

        if (grossPremiumInput && commissionRateInput) {
            grossPremiumInput.addEventListener('input', calculateAmounts);
            commissionRateInput.addEventListener('input', calculateAmounts);
        }

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Preview Slip
    function previewSlip() {
        const coverId = {
            {
                $cover - > id ?? 1
            }
        };
        window.open(`/covers/${coverId}/preview-slip`, '_blank');
    }

    // Generate Statement
    function generateStatement() {
        const coverId = {
            {
                $cover - > id ?? 1
            }
        };
        if (confirm('Generate Statement of Account for this treaty?')) {
            window.location.href = `/covers/${coverId}/generate-statement`;
        }
    }

    // Export Data
    function exportData() {
        const coverId = {
            {
                $cover - > id ?? 1
            }
        };
        window.location.href = `/covers/${coverId}/export`;
    }

    // View Item
    function viewItem(itemId) {
        window.location.href = `/debit-items/${itemId}`;
    }

    // Edit Item
    function editItem(itemId) {
        window.location.href = `/debit-items/${itemId}/edit`;
    }

    // View Reinsurer
    function viewReinsurer(reinsurerId) {
        window.location.href = `/reinsurers/${reinsurerId}`;
    }

    // Edit Reinsurer
    function editReinsurer(reinsurerId) {
        window.location.href = `/reinsurers/${reinsurerId}/edit`;
    }

    // Edit Cedant
    function editCedant() {
        const customerId = {
            {
                $customer - > id ?? 1
            }
        };
        window.location.href = `/customers/${customerId}/edit`;
    }

    // Generate Document
    function generateDocument(docType) {
        const coverId = {
            {
                $cover - > id ?? 1
            }
        };
        if (confirm(`Generate ${docType.replace('_', ' ')} for this treaty?`)) {
            fetch(`/covers/${coverId}/generate-document`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        document_type: docType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to generate document: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while generating the document.');
                });
        }
    }

    // View Document
    function viewDocument(docId) {
        window.open(`/documents/${docId}/view`, '_blank');
    }

    // Download Document
    function downloadDocument(docId) {
        window.location.href = `/documents/${docId}/download`;
    }

    // Email Document
    function emailDocument(docId) {
        if (confirm('Send this document via email?')) {
            fetch(`/documents/${docId}/email`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Document sent successfully!');
                    } else {
                        alert('Failed to send document: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending the document.');
                });
        }
    }

    // Save Debit Item
    function saveDebitItem() {
        const form = document.getElementById('addDebitItemForm');
        const formData = new FormData(form);

        // fetch('{{-- route('debit-items.store') ?? '/debit-items' --}}', {
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //         },
        //         body: formData
        //     })
        //     .then(response => response.json())
        //     .then(data => {
        //         if (data.success) {
        //             const modal = bootstrap.Modal.getInstance(document.getElementById('addDebitItemModal'));
        //             modal.hide();
        //             location.reload();
        //         } else {
        //             alert('Failed to save item: ' + (data.message || 'Unknown error'));
        //         }
        //     })
        //     .catch(error => {
        //         console.error('Error:', error);
        //         alert('An error occurred while saving the item.');
        //     });
    }
</script>
@endsection