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

    .status-generated {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-sent {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-signed {
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

    .type-premium {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .type-surplus {
        background-color: #f3e8ff;
        color: #6b21a8;
    }

    .type-quota-share {
        background-color: #d1fae5;
        color: #065f46;
    }

    .type-excess-of-loss {
        background-color: #fef3c7;
        color: #92400e;
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
        padding: .45rem 29px;
        font-size: 14px;
        border-radius: .25rem;
        transition: all 0.2s ease;
        font-weight: 500;
        align-items: center;
    }

    .quick-action-btn i {
        vertical-align: -1px;
    }

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

    /* File upload area */
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

    /* Clause item */
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
// Cover data with fallback
$cover =
$cover ??
(object) [
'id' => 1,
'cover_no' => 'TRY-2024-001',
'cover_title' => 'Property Treaty 2024',
'policy_number' => 'POL-2024-001',
'treaty_name' => 'Property Treaty 2024',
'treaty_type' => 'Quota Share',
'class_of_business' => 'Property All Risks',
'cover_from' => '2024-01-01',
'cover_to' => '2024-12-31',
'sum_insured' => 150000000,
'premium' => 2500000,
'ceded_premium' => 1750000,
'retention_percentage' => 30.0,
'ceded_percentage' => 70.0,
'currency' => 'KES',
'status' => 'A',
];

// Customer data with fallback
$customer =
$customer ??
(object) [
'id' => 1,
'name' => 'Heritage Insurance Company',
'email' => 'underwriting@heritage.co.ke',
'phone' => '+254 20 123 4567',
];

// Debit items collection
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

// Reinsurers collection
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

// Cedant details
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

// Documents collection
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

// Clauses collection
$clauses =
$clauses ??
collect([
(object) [
'id' => 1,
'clause_code' => 'CL001',
'clause_title' => 'Premium Payment Clause',
'clause_text' =>
'Premium shall be paid within 30 days of the inception of the policy period or within 30 days of receipt of debit note, whichever is later.',
'is_mandatory' => true,
],
(object) [
'id' => 2,
'clause_code' => 'CL002',
'clause_title' => 'Claims Cooperation Clause',
'clause_text' =>
'The Reinsured shall cooperate with the Reinsurer in all matters pertaining to claims, including but not limited to, providing all relevant documentation and information.',
'is_mandatory' => true,
],
(object) [
'id' => 3,
'clause_code' => 'CL003',
'clause_title' => 'Errors and Omissions Clause',
'clause_text' =>
'Any inadvertent error or omission in reporting shall not void the coverage, provided such error or omission is rectified promptly upon discovery.',
'is_mandatory' => false,
],
(object) [
'id' => 4,
'clause_code' => 'CL004',
'clause_title' => 'Currency Clause',
'clause_text' =>
'All premiums, claims, and other amounts payable under this Agreement shall be in Kenya Shillings (KES) unless otherwise agreed.',
'is_mandatory' => true,
],
(object) [
'id' => 5,
'clause_code' => 'CL005',
'clause_title' => 'Arbitration Clause',
'clause_text' =>
'Any dispute arising out of or in connection with this Agreement shall be settled by arbitration in Nairobi, Kenya in accordance with the Arbitration Act.',
'is_mandatory' => true,
],
]);

// Attachments collection
$attachments =
$attachments ??
collect([
(object) [
'id' => 1,
'file_name' => 'Treaty_Agreement_2024.pdf',
'file_type' => 'pdf',
'file_size' => '2.4 MB',
'uploaded_by' => 'Mary Njeri',
'uploaded_at' => '2024-01-10',
'description' => 'Signed Treaty Agreement',
],
(object) [
'id' => 2,
'file_name' => 'Reinsurer_Placement_Slip.pdf',
'file_type' => 'pdf',
'file_size' => '1.1 MB',
'uploaded_by' => 'Peter Kamau',
'uploaded_at' => '2024-01-12',
'description' => 'Placement Slip with Reinsurer Shares',
],
(object) [
'id' => 3,
'file_name' => 'Q1_Premium_Bordereau.xlsx',
'file_type' => 'xlsx',
'file_size' => '856 KB',
'uploaded_by' => 'System',
'uploaded_at' => '2024-04-01',
'description' => 'Q1 2024 Premium Bordereau',
],
]);

// Calculate totals
$totalGrossPremium = $debitItems->sum('gross_premium');
$totalCommission = $debitItems->sum('commission_amount');
$totalNetAmount = $debitItems->sum('net_amount');
$totalReinsurerShare = $reinsurers->sum('share_premium');
@endphp

<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <div>
        <h1 class="page-title fw-semibold fs-18 mb-1">Quarterly Debit Statement</h1>
        <p class="text-muted mb-0 fw-medium">{{ $cover->cover_no }} - {{ $cover->cover_title ?? $cover->treaty_name }}
        </p>
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
    <button class="btn btn-outline-dark quick-action-btn" onclick="previewSlip()">
        <i class="ri-file-text-line"></i> Preview Slip
    </button>
    <button class="btn btn-outline-primary quick-action-btn" onclick="generateStatement()">
        <i class="ri-file-list-3-line"></i> Generate Statement
    </button>
    <button class="btn btn-outline-success quick-action-btn" onclick="exportData()">
        <i class="ri-download-2-line"></i> Export Data
    </button>
    <button class="btn btn-primary quick-action-btn" data-bs-toggle="modal" data-bs-target="#addDebitItemModal">
        <i class="ri-add-line"></i> Add Debit Item
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
            <span class="summary-label">Underwriting Year</span>
            <span class="summary-value">{{ $cedantDetails->treaty_year }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Policy Period</span>
            <span class="summary-value">
                {{ \Carbon\Carbon::parse($cover->cover_from)->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($cover->cover_to)->format('d M Y') }}
            </span>
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
            <span
                class="status-badge status-{{ $cover->status === 'A' || $cover->status === 'active' ? 'active' : 'pending' }}">
                {{ $cover->status === 'A' || $cover->status === 'active' ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>
</div>

<!-- Main Content Card with Tabs -->
<div class="row-cols-12">
    <div class="card mb-2 custom-card border col">
        <div class="card-body pt-0">
            <nav>
                <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                    id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-debit-items-tab" data-bs-toggle="tab"
                        data-bs-target="#debit-items-tab" type="button" role="tab" aria-selected="true">
                        <i class="bx bx-table me-1 align-middle"></i>Debit Items
                        <span class="badge bg-primary ms-1">{{ $debitItems->count() }}</span>
                    </button>

                    <button class="nav-link" id="nav-reinsurers-tab" data-bs-toggle="tab"
                        data-bs-target="#reinsurers-tab" type="button" role="tab" aria-selected="false">
                        <i class="ri-building-2-line"></i> Participating Reinsurers
                        <span class="badge bg-info ms-1">{{ $reinsurers->count() }}</span>
                    </button>

                    <button class="nav-link" id="nav-cedant-tab" data-bs-toggle="tab" data-bs-target="#cedant-tab"
                        type="button" role="tab" aria-selected="false">
                        <i class="bx bx-briefcase"></i> Cedant
                    </button>

                    <button class="nav-link" id="nav-approvals-tab" data-bs-toggle="tab"
                        data-bs-target="#approvals-tab" type="button" role="tab" aria-selected="false">
                        <i class="bx bx-medal me-1 align-middle"></i>Approvals
                        <span class="badge bg-warning ms-1">{{ $clauses->count() }}</span>
                    </button>

                    <button class="nav-link" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#docs-tab"
                        type="button" role="tab" aria-selected="false">
                        <i class="ri-printer-line me-1 align-middle"></i>Print-outs
                        <span class="badge bg-success ms-1">{{ $documents->count() }}</span>
                    </button>
                </div>
            </nav>
            <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                <!-- Debit Items Tab -->
                <div class="tab-pane fade show active" id="debit-items-tab" role="tabpanel"
                    aria-labelledby="nav-debit-items-tab">
                    <div class="card border-0 shadow-none">
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="debitItemsTable" class="table table-bordered table-hover w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="12%">Item Number</th>
                                            <th width="10%">Date</th>
                                            <th width="10%">Treaty Type</th>
                                            <th width="12%">Class</th>
                                            <th width="12%">Reinsurer</th>
                                            <th width="10%">Gross Premium</th>
                                            <th width="10%">Commission</th>
                                            <th width="10%">Net Amount</th>
                                            <th width="7%">Status</th>
                                            <th width="8%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="table-light">
                                        <tr class="fw-bold">
                                            <td colspan="6" class="text-end">Totals:</td>
                                            <td class="amount-cell" id="totalGrossPremium">-</td>
                                            <td class="amount-cell" id="totalCommission">-</td>
                                            <td class="amount-cell amount-positive" id="totalNetAmount">-</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="reinsurers-tab" role="tabpanel" aria-labelledby="nav-reinsurers-tab">
                    <div class="card border-0 shadow-none">
                        <div class="card-body px-0">
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

                <div class="tab-pane fade" id="cedant-tab" role="tabpanel" aria-labelledby="nav-cedant-tab">
                    <div class="card border-0 shadow-none">

                        <div class="card-body px-0">
                            {{-- Cedant Details Card --}}
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

                <div class="tab-pane fade" id="approvals-tab" role="tabpanel" aria-labelledby="nav-approvals-tab">
                    <div class="card border-0 shadow-none">
                        <div class="card-body px-0">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="docs-tab" role="tabpanel" aria-labelledby="nav-docs-tab">
                    <div class="card border-0 shadow-none">
                        <div class="card-header bg-transparent border-0 px-0 pt-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="ri-printer-line text-success me-1"></i>Generated Documents
                                </h6>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="DocumentsTable.reload()">
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
                                                <a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="DocumentsManager.generate('debit_note')">
                                                    <i class="ri-file-text-line me-2 text-primary"></i>Debit Note
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="DocumentsManager.generate('credit_note')">
                                                    <i class="ri-file-text-line me-2 text-success"></i>Credit Note
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="DocumentsManager.generate('statement')">
                                                    <i class="ri-file-list-3-line me-2 text-info"></i>Statement of
                                                    Account
                                                </a>
                                            </li>
                                            <!-- <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <h6 class="dropdown-header">Treaty Documents</h6>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                        onclick="DocumentsManager.generate('bordereau')">
                                                        <i class="ri-table-line me-2 text-warning"></i>Bordereau
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                        onclick="DocumentsManager.generate('closing_slip')">
                                                        <i class="ri-file-paper-2-line me-2 text-danger"></i>Closing Slip
                                                    </a>
                                                </li> -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0">
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

<!-- Add Debit Item Modal -->
<div class="modal fade" id="addDebitItemModal" tabindex="-1" aria-labelledby="addDebitItemModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="addDebitItemModalLabel">
                    <i class="ri-add-circle-line text-primary me-2"></i>Add Debit Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addDebitItemForm" novalidate>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="cover_id" value="{{ $cover->id ?? '' }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Treaty Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="treaty_type" id="treatyType" required>
                                <option value="">Select Treaty Type</option>
                                <option value="SURPLUS">Surplus</option>
                                <option value="QUOTA_SHARE">Quota Share</option>
                                <option value="EXCESS_OF_LOSS">Excess of Loss</option>
                            </select>
                            <div class="invalid-feedback">Please select a treaty type.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Item Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="item_date" id="itemDate"
                                value="{{ date('Y-m-d') }}" required>
                            <div class="invalid-feedback">Please select a date.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class Group <span class="text-danger">*</span></label>
                            <select class="form-select" name="class_group" id="classGroup" required>
                                <option value="">Select Class Group</option>
                                <option value="FIRE">Fire</option>
                                <option value="ENGINEERING">Engineering</option>
                                <option value="MARINE">Marine</option>
                                <option value="MOTOR">Motor</option>
                                <option value="AVIATION">Aviation</option>
                                <option value="MISCELLANEOUS">Miscellaneous</option>
                            </select>
                            <div class="invalid-feedback">Please select a class group.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="class_name" id="className"
                                placeholder="e.g., Fire Industrial" required>
                            <div class="invalid-feedback">Please enter class name.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reinsurer <span class="text-danger">*</span></label>
                            <select class="form-select" name="reinsurer_id" id="reinsurerId" required>
                                <option value="">Select Reinsurer</option>
                                {{-- Will be populated via AJAX --}}
                            </select>
                            <div class="invalid-feedback">Please select a reinsurer.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gross Premium <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $cover->currency ?? 'KES' }}</span>
                                <input type="number" class="form-control" name="gross_premium" id="grossPremium"
                                    step="0.01" min="0" placeholder="0.00" required>
                                <div class="invalid-feedback">Please enter gross premium.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission Rate (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="commission_rate" id="commissionRate"
                                step="0.01" min="0" max="100" placeholder="0.00" required>
                            <div class="invalid-feedback">Please enter commission rate (0-100).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $cover->currency ?? 'KES' }}</span>
                                <input type="text" class="form-control bg-light" id="commissionAmount" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Net Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $cover->currency ?? 'KES' }}</span>
                                <input type="text" class="form-control bg-light" id="netAmount" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="itemStatus">
                                <option value="pending" selected>Pending</option>
                                <option value="paid">Paid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveDebitItemBtn">
                        <i class="ri-save-line me-1"></i>Save Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="viewItemModalTitle">Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewItemModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    /**
     * ============================================================================
     * Quarterly Debit Statement - DataTables & AJAX Manager
     * ============================================================================
     * Production-ready JavaScript module for managing DataTables with server-side
     * processing, CRUD operations, and real-time updates.
     *
     * @version 1.0.0
     * @requires jQuery 3.6+
     * @requires DataTables 1.13+
     * @requires Bootstrap 5.3+
     * ============================================================================
     */

    "use strict";

    // ============================================================================
    // CONFIGURATION
    // ============================================================================

    const CONFIG = {
        coverId: document.querySelector('input[name="cover_id"]')?.value || null,
        currency: "KES",
        csrfToken: document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "",

        // API Endpoints
        endpoints: {
            debitItems: {
                list: "/api/covers/{coverId}/debit-items",
                store: "/api/debit-items",
                show: "/api/debit-items/{id}",
                update: "/api/debit-items/{id}",
                destroy: "/api/debit-items/{id}",
                export: "/api/covers/{coverId}/debit-items/export",
            },
            reinsurers: {
                list: "/api/covers/{coverId}/reinsurers",
                store: "/api/cover-reinsurers",
                show: "/api/cover-reinsurers/{id}",
                update: "/api/cover-reinsurers/{id}",
                destroy: "/api/cover-reinsurers/{id}",
                available: "/api/reinsurers/available",
            },
            cedant: {
                show: "/api/covers/{coverId}/cedant",
                update: "/api/covers/{coverId}/cedant",
            },
            attachments: {
                list: "/api/covers/{coverId}/attachments",
                store: "/api/attachments",
                show: "/api/attachments/{id}",
                download: "/api/attachments/{id}/download",
                destroy: "/api/attachments/{id}",
            },
            clauses: {
                list: "/api/covers/{coverId}/clauses",
                store: "/api/clauses",
                show: "/api/clauses/{id}",
                update: "/api/clauses/{id}",
                destroy: "/api/clauses/{id}",
            },
            documents: {
                list: "/api/covers/{coverId}/documents",
                generate: "/api/covers/{coverId}/documents/generate",
                show: "/api/documents/{id}",
                download: "/api/documents/{id}/download",
                email: "/api/documents/{id}/email",
            },
        },

        // DataTable defaults
        dataTableDefaults: {
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100],
            ],
            order: [
                [0, "desc"]
            ],
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6"f>>' +
                '<"row"<"col-12"tr>>' +
                '<"row align-items-center"<"col-md-5"i><"col-md-7"p>>',
            language: {
                processing: '<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading...</div>',
                emptyTable: '<div class="text-center py-4 text-muted"><i class="ri-inbox-line fs-1 d-block mb-2"></i>No records found</div>',
                zeroRecords: '<div class="text-center py-4 text-muted"><i class="ri-search-line fs-1 d-block mb-2"></i>No matching records</div>',
                search: "",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_",
                info: "Showing _START_ to _END_ of _TOTAL_",
                paginate: {
                    first: '<i class="ri-skip-back-mini-line"></i>',
                    last: '<i class="ri-skip-forward-mini-line"></i>',
                    next: '<i class="ri-arrow-right-s-line"></i>',
                    previous: '<i class="ri-arrow-left-s-line"></i>',
                },
            },
        },
    };

    // ============================================================================
    // UTILITY FUNCTIONS
    // ============================================================================

    const Utils = {
        /**
         * Build URL with parameters
         */
        buildUrl(template, params = {}) {
            let url = template.replace("{coverId}", CONFIG.coverId);
            Object.keys(params).forEach((key) => {
                url = url.replace(`{${key}}`, params[key]);
            });
            return url;
        },

        /**
         * Format currency
         */
        formatCurrency(amount, currency = CONFIG.currency) {
            const num = parseFloat(amount) || 0;
            return `${currency} ${num.toLocaleString("en-US", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
        },

        /**
         * Format date
         */
        formatDate(dateString, format = "medium") {
            if (!dateString) return "-";
            const date = new Date(dateString);
            const options =
                format === "short" ? {
                    day: "2-digit",
                    month: "short",
                    year: "numeric"
                } : {
                    day: "2-digit",
                    month: "long",
                    year: "numeric"
                };
            return date.toLocaleDateString("en-GB", options);
        },

        /**
         * Show toast notification
         */
        showToast(type, message, title = null) {
            const icons = {
                success: "ri-check-line",
                error: "ri-error-warning-line",
                warning: "ri-alert-line",
                info: "ri-information-line",
            };

            const bgColors = {
                success: "bg-success",
                error: "bg-danger",
                warning: "bg-warning",
                info: "bg-info",
            };

            const toastHtml = `
            <div class="toast align-items-center text-white ${
                bgColors[type]
            } border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="${icons[type]} me-2 fs-5"></i>
                        <div>
                            ${
                                title
                                    ? `<strong class="d-block">${title}</strong>`
                                    : ""
                            }
                            <span>${message}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

            let container = document.getElementById("toast-container");
            if (!container) {
                container = document.createElement("div");
                container.id = "toast-container";
                container.className =
                    "toast-container position-fixed top-0 end-0 p-3";
                container.style.zIndex = "9999";
                document.body.appendChild(container);
            }

            const toastElement = document.createElement("div");
            toastElement.innerHTML = toastHtml;
            const toast = toastElement.firstElementChild;
            container.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast, {
                delay: 4000
            });
            bsToast.show();

            toast.addEventListener("hidden.bs.toast", () => toast.remove());
        },

        /**
         * Confirm dialog
         */
        confirm(message, title = "Confirm Action") {
            return new Promise((resolve) => {
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        title: title,
                        text: message,
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "Yes, proceed",
                        cancelButtonText: "Cancel",
                    }).then((result) => resolve(result.isConfirmed));
                } else {
                    resolve(confirm(message));
                }
            });
        },

        /**
         * AJAX request wrapper
         */
        async ajax(url, options = {}) {
            const defaults = {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": CONFIG.csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                },
            };

            const config = {
                ...defaults,
                ...options
            };

            if (config.data && !(config.data instanceof FormData)) {
                config.body = JSON.stringify(config.data);
                delete config.data;
            } else if (config.data instanceof FormData) {
                delete config.headers["Content-Type"];
                config.body = config.data;
                delete config.data;
            }

            try {
                const response = await fetch(url, config);
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || "Request failed");
                }

                return data;
            } catch (error) {
                console.error("AJAX Error:", error);
                throw error;
            }
        },

        /**
         * Get status badge HTML
         */
        getStatusBadge(status) {
            const statusConfig = {
                paid: {
                    class: "status-paid",
                    label: "Paid"
                },
                pending: {
                    class: "status-pending",
                    label: "Pending"
                },
                overdue: {
                    class: "status-overdue",
                    label: "Overdue"
                },
                active: {
                    class: "status-active",
                    label: "Active"
                },
                inactive: {
                    class: "status-pending",
                    label: "Inactive"
                },
                generated: {
                    class: "status-generated",
                    label: "Generated"
                },
                sent: {
                    class: "status-sent",
                    label: "Sent"
                },
                signed: {
                    class: "status-signed",
                    label: "Signed"
                },
                approved: {
                    class: "status-approved",
                    label: "Approved"
                },
                rejected: {
                    class: "status-rejected",
                    label: "Rejected"
                },
            };

            const config = statusConfig[status?.toLowerCase()] || {
                class: "status-pending",
                label: status,
            };
            return `<span class="status-badge ${config.class}">${config.label}</span>`;
        },

        /**
         * Get treaty type badge
         */
        getTreatyTypeBadge(type) {
            const slug = type?.toLowerCase().replace(/\s+/g, "-") || "default";
            return `<span class="type-badge type-${slug}">${type || "-"}</span>`;
        },
    };

    // ============================================================================
    // DEBIT ITEMS TABLE MANAGER
    // ============================================================================

    const DebitItemsTable = {
        table: null,
        totals: {
            grossPremium: 0,
            commission: 0,
            netAmount: 0
        },

        init() {
            if (!document.getElementById("debitItemsTable")) return;

            this.table = $("#debitItemsTable").DataTable({
                ...CONFIG.dataTableDefaults,
                ajax: {
                    url: Utils.buildUrl(CONFIG.endpoints.debitItems.list),
                    type: "GET",
                    dataSrc: (response) => {
                        this.updateTotals(response.totals || {});
                        this.updateCount(response.recordsTotal || 0);
                        return response.data || [];
                    },
                    error: (xhr, error, thrown) => {
                        console.error("DataTable AJAX Error:", error);
                        Utils.showToast("error", "Failed to load debit items");
                    },
                },
                columns: [{
                        data: null,
                        render: (data, type, row, meta) =>
                            meta.row + meta.settings._iDisplayStart + 1,
                    },
                    {
                        data: "item_number",
                        render: (data) =>
                            `<strong class="text-primary">${data || "-"}</strong>`,
                    },
                    {
                        data: "item_date",
                        render: (data) => Utils.formatDate(data, "short"),
                    },
                    {
                        data: "treaty_type",
                        render: (data) => Utils.getTreatyTypeBadge(data),
                    },
                    {
                        data: null,
                        render: (data) => `
                        <span class="fw-medium">${
                            data.class_group || "-"
                        }</span><br>
                        <small class="text-muted">${
                            data.class_name || "-"
                        }</small>
                    `,
                    },
                    {
                        data: "reinsurer_name",
                        render: (data) => data || "-",
                    },
                    {
                        data: "gross_premium",
                        className: "text-end font-monospace",
                        render: (data) => Utils.formatCurrency(data),
                    },
                    {
                        data: null,
                        className: "text-end",
                        render: (data) => `
                        <span class="font-monospace">${Utils.formatCurrency(
                            data.commission_amount
                        )}</span><br>
                        <small class="text-muted">(${parseFloat(
                            data.commission_rate || 0
                        ).toFixed(1)}%)</small>
                    `,
                    },
                    {
                        data: "net_amount",
                        className: "text-end font-monospace text-success fw-medium",
                        render: (data) => Utils.formatCurrency(data),
                    },
                    {
                        data: "status",
                        className: "text-center",
                        render: (data) => Utils.getStatusBadge(data),
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                        render: (data) => `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info btn-sm" onclick="DebitItemsTable.view(${data.id})" title="View">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="DebitItemsTable.edit(${data.id})" title="Edit">
                                <i class="ri-edit-line"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="DebitItemsTable.delete(${data.id})" title="Delete">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    `,
                    },
                ],
                order: [
                    [2, "desc"]
                ],
                drawCallback: () => {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
            });

            this.bindEvents();
        },

        bindEvents() {
            // Form submission
            $("#addDebitItemForm").on("submit", async (e) => {
                e.preventDefault();
                await this.store(e.target);
            });

            // Auto-calculate amounts
            $("#grossPremium, #commissionRate").on("input", () => {
                this.calculateAmounts();
            });

            // Reinsurer selection - auto-fill commission rate
            $("#reinsurerId").on("change", function() {
                const rate = $(this).find(":selected").data("commission-rate");
                if (rate) {
                    $("#commissionRate").val(rate);
                    DebitItemsTable.calculateAmounts();
                }
            });

            // Load reinsurers on modal open
            $("#addDebitItemModal").on("show.bs.modal", () => {
                this.loadReinsurersDropdown();
            });

            // Reset form on modal close
            $("#addDebitItemModal").on("hidden.bs.modal", () => {
                $("#addDebitItemForm")[0].reset();
                $("#addDebitItemForm").removeClass("was-validated");
                $("#commissionAmount, #netAmount").val("");
            });
        },

        calculateAmounts() {
            const gross = parseFloat($("#grossPremium").val()) || 0;
            const rate = parseFloat($("#commissionRate").val()) || 0;
            const commission = (gross * rate) / 100;
            const net = gross - commission;

            $("#commissionAmount").val(commission.toFixed(2));
            $("#netAmount").val(net.toFixed(2));
        },

        async loadReinsurersDropdown() {
            try {
                const response = await Utils.ajax(
                    Utils.buildUrl(CONFIG.endpoints.reinsurers.list)
                );
                const select = $("#reinsurerId");
                select.find("option:not(:first)").remove();

                (response.data || []).forEach((r) => {
                    select.append(`
                    <option value="${r.id}" data-commission-rate="${r.commission_rate}">
                        ${r.name} (${r.share_percentage}%)
                    </option>
                `);
                });
            } catch (error) {
                Utils.showToast("error", "Failed to load reinsurers");
            }
        },

        async store(form) {
            const $form = $(form);

            if (!form.checkValidity()) {
                $form.addClass("was-validated");
                return;
            }

            const $btn = $("#saveDebitItemBtn");
            const originalHtml = $btn.html();
            $btn.prop("disabled", true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'
            );

            try {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                await Utils.ajax(CONFIG.endpoints.debitItems.store, {
                    method: "POST",
                    data: data,
                });

                Utils.showToast("success", "Debit item created successfully");
                $("#addDebitItemModal").modal("hide");
                this.reload();
            } catch (error) {
                Utils.showToast(
                    "error",
                    error.message || "Failed to save debit item"
                );
            } finally {
                $btn.prop("disabled", false).html(originalHtml);
            }
        },

        async view(id) {
            const modal = new bootstrap.Modal("#viewItemModal");
            $("#viewItemModalTitle").text("Debit Item Details");
            $("#viewItemModalBody").html(
                '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>'
            );
            modal.show();

            try {
                const url = Utils.buildUrl(CONFIG.endpoints.debitItems.show, {
                    id,
                });
                const response = await Utils.ajax(url);
                const item = response.data;

                $("#viewItemModalBody").html(`
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Item Number</small>
                            <strong>${item.item_number}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Date</small>
                            <strong>${Utils.formatDate(item.item_date)}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Treaty Type</small>
                            ${Utils.getTreatyTypeBadge(item.treaty_type)}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Status</small>
                            ${Utils.getStatusBadge(item.status)}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Class</small>
                            <strong>${item.class_group}</strong> - ${
                item.class_name
            }
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block">Reinsurer</small>
                            <strong>${item.reinsurer_name || "-"}</strong>
                        </div>
                    </div>
                    <div class="col-12"><hr class="my-2"></div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <small class="text-muted d-block">Gross Premium</small>
                            <strong class="fs-5">${Utils.formatCurrency(
                                item.gross_premium
                            )}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <small class="text-muted d-block">Commission (${
                                item.commission_rate
                            }%)</small>
                            <strong class="fs-5 text-warning">${Utils.formatCurrency(
                                item.commission_amount
                            )}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center bg-success-subtle">
                            <small class="text-muted d-block">Net Amount</small>
                            <strong class="fs-5 text-success">${Utils.formatCurrency(
                                item.net_amount
                            )}</strong>
                        </div>
                    </div>
                </div>
            `);
            } catch (error) {
                $("#viewItemModalBody").html(`
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line me-2"></i>${
                        error.message || "Failed to load item details"
                    }
                </div>
            `);
            }
        },

        edit(id) {
            // For simplicity, redirect to edit page
            // In production, you might want inline editing
            window.location.href = `/debit-items/${id}/edit`;
        },

        async delete(id) {
            const confirmed = await Utils.confirm(
                "Are you sure you want to delete this debit item?",
                "Delete Item"
            );
            if (!confirmed) return;

            try {
                const url = Utils.buildUrl(CONFIG.endpoints.debitItems.destroy, {
                    id,
                });
                await Utils.ajax(url, {
                    method: "DELETE"
                });

                Utils.showToast("success", "Debit item deleted successfully");
                this.reload();
            } catch (error) {
                Utils.showToast("error", error.message || "Failed to delete item");
            }
        },

        reload() {
            if (this.table) {
                this.table.ajax.reload(null, false);
            }
        },

        updateTotals(totals) {
            $("#totalGrossPremium").text(
                Utils.formatCurrency(totals.gross_premium || 0)
            );
            $("#totalCommission").text(
                Utils.formatCurrency(totals.commission || 0)
            );
            $("#totalNetAmount").text(Utils.formatCurrency(totals.net_amount || 0));
        },

        updateCount(count) {
            $("#debitItemsCount").text(`${count} item${count !== 1 ? "s" : ""}`);
        },

        export (format = "excel") {
            const url =
                Utils.buildUrl(CONFIG.endpoints.debitItems.export) +
                `?format=${format}`;
            window.location.href = url;
        },
    };

    // ============================================================================
    // REINSURERS TABLE MANAGER
    // ============================================================================

    const ReinsurersTable = {
        table: null,

        init() {
            if (!document.getElementById("reinsurersTable")) return;

            this.table = $("#reinsurersTable").DataTable({
                ...CONFIG.dataTableDefaults,
                ajax: {
                    url: Utils.buildUrl(CONFIG.endpoints.reinsurers.list),
                    type: "GET",
                    dataSrc: (response) => {
                        this.updateSummary(response.summary || {});
                        return response.data || [];
                    },
                    error: () =>
                        Utils.showToast("error", "Failed to load reinsurers"),
                },
                columns: [{
                        data: null,
                        render: (data, type, row, meta) =>
                            meta.row + meta.settings._iDisplayStart + 1,
                    },
                    {
                        data: null,
                        render: (data) => `
                        <strong>${data.name || "-"}</strong><br>
                        <small class="text-muted">${data.email || "-"}</small>
                    `,
                    },
                    {
                        data: "contact_person",
                        render: (d) => d || "-"
                    },
                    {
                        data: "share_percentage",
                        className: "text-center",
                        render: (data) =>
                            `<span class="badge bg-primary">${parseFloat(
                            data || 0
                        ).toFixed(1)}%</span>`,
                    },
                    {
                        data: "commission_rate",
                        className: "text-center",
                        render: (data) => `${parseFloat(data || 0).toFixed(1)}%`,
                    },
                    {
                        data: "share_premium",
                        className: "text-end font-monospace",
                        render: (data) => Utils.formatCurrency(data),
                    },
                    {
                        data: "share_sum_insured",
                        className: "text-end font-monospace",
                        render: (data) => Utils.formatCurrency(data),
                    },
                    {
                        data: "status",
                        className: "text-center",
                        render: (data) => Utils.getStatusBadge(data),
                    },
                    {
                        data: null,
                        orderable: false,
                        className: "text-center",
                        render: (data) => `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info btn-sm" onclick="ReinsurersTable.view(${data.id})">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="ReinsurersTable.edit(${data.id})">
                                <i class="ri-edit-line"></i>
                            </button>
                        </div>
                    `,
                    },
                ],
                order: [
                    [3, "desc"]
                ],
            });

            this.bindEvents();
        },

        bindEvents() {
            $("#addReinsurerForm").on("submit", async (e) => {
                e.preventDefault();
                await this.store(e.target);
            });

            $("#addReinsurerModal").on("show.bs.modal", () => {
                this.loadAvailableReinsurers();
            });

            $("#addReinsurerModal").on("hidden.bs.modal", () => {
                $("#addReinsurerForm")[0].reset();
            });
        },

        async loadAvailableReinsurers() {
            try {
                const response = await Utils.ajax(
                    CONFIG.endpoints.reinsurers.available
                );
                const select = $("#reinsurerSelect");
                select.find("option:not(:first)").remove();

                (response.data || []).forEach((r) => {
                    select.append(`<option value="${r.id}">${r.name}</option>`);
                });
            } catch (error) {
                Utils.showToast("error", "Failed to load available reinsurers");
            }
        },

        async store(form) {
            const $form = $(form);
            if (!form.checkValidity()) {
                $form.addClass("was-validated");
                return;
            }

            const $btn = $form.find('[type="submit"]');
            const originalHtml = $btn.html();
            $btn.prop("disabled", true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Adding...'
            );

            try {
                const formData = new FormData(form);
                await Utils.ajax(CONFIG.endpoints.reinsurers.store, {
                    method: "POST",
                    data: Object.fromEntries(formData.entries()),
                });

                Utils.showToast("success", "Reinsurer added successfully");
                $("#addReinsurerModal").modal("hide");
                this.reload();
            } catch (error) {
                Utils.showToast(
                    "error",
                    error.message || "Failed to add reinsurer"
                );
            } finally {
                $btn.prop("disabled", false).html(originalHtml);
            }
        },

        view(id) {
            window.location.href = `/cover-reinsurers/${id}`;
        },

        edit(id) {
            window.location.href = `/cover-reinsurers/${id}/edit`;
        },

        reload() {
            if (this.table) {
                this.table.ajax.reload(null, false);
            }
        },

        updateSummary(summary) {
            $("#totalReinsurers").text(summary.count || 0);
            $("#totalSharePercentage").text(
                `${parseFloat(summary.total_share || 0).toFixed(1)}%`
            );
            $("#totalPremiumShare").text(
                Utils.formatCurrency(summary.total_premium || 0)
            );
            $("#avgCommissionRate").text(
                `${parseFloat(summary.avg_commission || 0).toFixed(1)}%`
            );
        },
    };

    // ============================================================================
    // CEDANT MANAGER
    // ============================================================================

    const CedantManager = {
        init() {
            if (!document.getElementById("cedantDetailsCard")) return;
            this.load();
            this.bindDragDrop();
        },

        async load() {
            try {
                const response = await Utils.ajax(
                    Utils.buildUrl(CONFIG.endpoints.cedant.show)
                );
                const cedant = response.data;

                $("#cedant_name").text(cedant.name || "-");
                $("#cedant_registration").text(cedant.registration_no || "-");
                $("#cedant_address").text(cedant.address || "-");
                $("#cedant_contact").text(
                    `${cedant.contact_person || "-"} (${cedant.designation || "-"})`
                );
                $("#cedant_email").text(cedant.email || "-");
                $("#cedant_phone").text(cedant.phone || "-");
                $("#cedant_treaty_period").text(cedant.treaty_period || "-");
                $("#cedant_capacity").text(
                    Utils.formatCurrency(cedant.treaty_capacity || 0)
                );
            } catch (error) {
                console.error("Failed to load cedant details:", error);
            }
        },

        edit() {
            window.location.href = Utils.buildUrl("/covers/{coverId}/cedant/edit");
        },

        bindDragDrop() {
            const dropZone = document.getElementById("dropZone");
            const fileInput = document.getElementById("fileInput");

            if (!dropZone || !fileInput) return;

            ["dragenter", "dragover"].forEach((event) => {
                dropZone.addEventListener(event, (e) => {
                    e.preventDefault();
                    dropZone.classList.add("border-primary", "bg-primary-subtle");
                });
            });

            ["dragleave", "drop"].forEach((event) => {
                dropZone.addEventListener(event, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove(
                        "border-primary",
                        "bg-primary-subtle"
                    );
                });
            });

            dropZone.addEventListener("drop", (e) => {
                const files = e.dataTransfer.files;
                if (files.length) {
                    AttachmentsTable.uploadFiles(files);
                }
            });

            dropZone.addEventListener("click", () => fileInput.click());

            fileInput.addEventListener("change", () => {
                if (fileInput.files.length) {
                    AttachmentsTable.uploadFiles(fileInput.files);
                    fileInput.value = "";
                }
            });
        },
    };

    // ============================================================================
    // ATTACHMENTS TABLE MANAGER
    // ============================================================================

    const AttachmentsTable = {
        table: null,

        init() {
            if (!document.getElementById("attachmentsTable")) return;

            this.table = $("#attachmentsTable").DataTable({
                ...CONFIG.dataTableDefaults,
                ajax: {
                    url: Utils.buildUrl(CONFIG.endpoints.attachments.list),
                    type: "GET",
                    dataSrc: (response) => response.data || [],
                    error: () =>
                        Utils.showToast("error", "Failed to load attachments"),
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) =>
                            m.row + m.settings._iDisplayStart + 1,
                    },
                    {
                        data: null,
                        render: (data) => {
                            const icon = this.getFileIcon(data.file_type);
                            return `<i class="${icon} me-2"></i><span class="fw-medium">${data.file_name}</span>`;
                        },
                    },
                    {
                        data: "description",
                        render: (d) => d || "-"
                    },
                    {
                        data: "file_size",
                        render: (d) => d || "-"
                    },
                    {
                        data: "uploaded_by",
                        render: (d) => d || "-"
                    },
                    {
                        data: "uploaded_at",
                        render: (d) => Utils.formatDate(d, "short"),
                    },
                    {
                        data: null,
                        orderable: false,
                        className: "text-center",
                        render: (data) => `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info btn-sm" onclick="AttachmentsTable.view(${data.id})">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm" onclick="AttachmentsTable.download(${data.id})">
                                <i class="ri-download-line"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="AttachmentsTable.delete(${data.id})">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    `,
                    },
                ],
                order: [
                    [5, "desc"]
                ],
            });

            this.bindEvents();
        },

        getFileIcon(type) {
            const icons = {
                pdf: "ri-file-pdf-line text-danger",
                doc: "ri-file-word-line text-primary",
                docx: "ri-file-word-line text-primary",
                xls: "ri-file-excel-line text-success",
                xlsx: "ri-file-excel-line text-success",
                jpg: "ri-image-line text-warning",
                jpeg: "ri-image-line text-warning",
                png: "ri-image-line text-info",
            };
            return icons[type?.toLowerCase()] || "ri-file-line text-secondary";
        },

        bindEvents() {
            $("#uploadAttachmentForm").on("submit", async (e) => {
                e.preventDefault();
                await this.store(e.target);
            });

            $("#uploadAttachmentModal").on("hidden.bs.modal", () => {
                $("#uploadAttachmentForm")[0].reset();
            });
        },

        async store(form) {
            const $btn = $("#uploadAttachmentBtn");
            const originalHtml = $btn.html();
            $btn.prop("disabled", true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...'
            );

            try {
                const formData = new FormData(form);
                await Utils.ajax(CONFIG.endpoints.attachments.store, {
                    method: "POST",
                    data: formData,
                });

                Utils.showToast("success", "File uploaded successfully");
                $("#uploadAttachmentModal").modal("hide");
                this.reload();
            } catch (error) {
                Utils.showToast("error", error.message || "Failed to upload file");
            } finally {
                $btn.prop("disabled", false).html(originalHtml);
            }
        },

        async uploadFiles(files) {
            for (const file of files) {
                if (file.size > 10 * 1024 * 1024) {
                    Utils.showToast("warning", `${file.name} exceeds 10MB limit`);
                    continue;
                }

                const formData = new FormData();
                formData.append("file", file);
                formData.append("cover_id", CONFIG.coverId);

                try {
                    await Utils.ajax(CONFIG.endpoints.attachments.store, {
                        method: "POST",
                        data: formData,
                    });
                    Utils.showToast("success", `${file.name} uploaded`);
                } catch (error) {
                    Utils.showToast("error", `Failed to upload ${file.name}`);
                }
            }
            this.reload();
        },

        view(id) {
            const url = Utils.buildUrl(CONFIG.endpoints.attachments.show, {
                id
            });
            window.open(url, "_blank");
        },

        download(id) {
            const url = Utils.buildUrl(CONFIG.endpoints.attachments.download, {
                id,
            });
            window.location.href = url;
        },

        async delete(id) {
            const confirmed = await Utils.confirm(
                "Delete this attachment?",
                "Delete Attachment"
            );
            if (!confirmed) return;

            try {
                await Utils.ajax(
                    Utils.buildUrl(CONFIG.endpoints.attachments.destroy, {
                        id
                    }), {
                        method: "DELETE",
                    }
                );
                Utils.showToast("success", "Attachment deleted");
                this.reload();
            } catch (error) {
                Utils.showToast("error", "Failed to delete attachment");
            }
        },

        reload() {
            if (this.table) {
                this.table.ajax.reload(null, false);
            }
        },
    };

    // ============================================================================
    // CLAUSES MANAGER
    // ============================================================================

    const ClausesManager = {
        clauses: [],
        filter: "all",

        init() {
            if (!document.getElementById("clausesList")) return;
            this.load();
            this.bindEvents();
        },

        bindEvents() {
            $('input[name="clauseFilter"]').on("change", (e) => {
                this.filter = e.target.value;
                this.render();
            });

            $("#addClauseForm").on("submit", async (e) => {
                e.preventDefault();
                await this.store(e.target);
            });

            $("#addClauseModal").on("hidden.bs.modal", () => {
                $("#addClauseForm")[0].reset();
            });
        },

        async load() {
            try {
                const response = await Utils.ajax(
                    Utils.buildUrl(CONFIG.endpoints.clauses.list)
                );
                this.clauses = response.data || [];
                this.updateStats(response.stats || {});
                this.render();
            } catch (error) {
                $("#clausesList").html(`
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line me-2"></i>Failed to load clauses
                </div>
            `);
            }
        },

        render() {
            const filtered = this.clauses.filter((c) => {
                if (this.filter === "mandatory") return c.is_mandatory;
                if (this.filter === "optional") return !c.is_mandatory;
                return true;
            });

            if (filtered.length === 0) {
                $("#clausesList").html(`
                <div class="text-center py-5 text-muted">
                    <i class="ri-file-list-3-line fs-1 d-block mb-2"></i>
                    <p>No clauses found</p>
                </div>
            `);
                return;
            }

            const html = filtered
                .map(
                    (clause) => `
            <div class="clause-item" data-id="${clause.id}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge ${
                            clause.is_mandatory ? "bg-danger" : "bg-info"
                        } me-2">
                            ${clause.is_mandatory ? "Mandatory" : "Optional"}
                        </span>
                        <code class="text-muted">${clause.clause_code}</code>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-sm" onclick="ClausesManager.edit(${
                            clause.id
                        })">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="ClausesManager.delete(${
                            clause.id
                        })">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
                <h6 class="clause-title mb-2">${clause.clause_title}</h6>
                <p class="clause-text mb-0">${clause.clause_text}</p>
            </div>
        `
                )
                .join("");

            $("#clausesList").html(html);
        },

        updateStats(stats) {
            $("#approvedCount").text(stats.approved || 0);
            $("#pendingCount").text(stats.pending || 0);
            $("#rejectedCount").text(stats.rejected || 0);
            $("#totalClauses").text(stats.total || this.clauses.length);
        },

        async store(form) {
            const $form = $(form);
            if (!form.checkValidity()) {
                $form.addClass("was-validated");
                return;
            }

            const $btn = $form.find('[type="submit"]');
            const originalHtml = $btn.html();
            $btn.prop("disabled", true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'
            );

            try {
                const formData = new FormData(form);
                await Utils.ajax(CONFIG.endpoints.clauses.store, {
                    method: "POST",
                    data: Object.fromEntries(formData.entries()),
                });

                Utils.showToast("success", "Clause added successfully");
                $("#addClauseModal").modal("hide");
                this.load();
            } catch (error) {
                Utils.showToast("error", error.message || "Failed to add clause");
            } finally {
                $btn.prop("disabled", false).html(originalHtml);
            }
        },

        edit(id) {
            window.location.href = `/clauses/${id}/edit`;
        },

        async delete(id) {
            const confirmed = await Utils.confirm(
                "Delete this clause?",
                "Delete Clause"
            );
            if (!confirmed) return;

            try {
                await Utils.ajax(
                    Utils.buildUrl(CONFIG.endpoints.clauses.destroy, {
                        id
                    }), {
                        method: "DELETE",
                    }
                );
                Utils.showToast("success", "Clause deleted");
                this.load();
            } catch (error) {
                Utils.showToast("error", "Failed to delete clause");
            }
        },
    };

    // ============================================================================
    // DOCUMENTS TABLE MANAGER
    // ============================================================================

    const DocumentsTable = {
        table: null,

        init() {
            if (!document.getElementById("documentsTable")) return;

            this.table = $("#documentsTable").DataTable({
                ...CONFIG.dataTableDefaults,
                ajax: {
                    url: Utils.buildUrl(CONFIG.endpoints.documents.list),
                    type: "GET",
                    dataSrc: (response) => {
                        this.updateStats(response.stats || {});
                        return response.data || [];
                    },
                    error: () =>
                        Utils.showToast("error", "Failed to load documents"),
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) =>
                            m.row + m.settings._iDisplayStart + 1,
                    },
                    {
                        data: "document_type",
                        render: (data) => `<strong>${data}</strong>`,
                    },
                    {
                        data: "reference",
                        render: (data) => `<code>${data}</code>`,
                    },
                    {
                        data: "description",
                        render: (d) => d || "-"
                    },
                    {
                        data: "generated_date",
                        render: (d) => Utils.formatDate(d, "short"),
                    },
                    {
                        data: "generated_by",
                        render: (d) => d || "System"
                    },
                    {
                        data: "status",
                        className: "text-center",
                        render: (data) => Utils.getStatusBadge(data),
                    },
                    {
                        data: null,
                        orderable: false,
                        className: "text-center",
                        render: (data) => `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info btn-sm" onclick="DocumentsManager.view(${data.id})">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm" onclick="DocumentsManager.download(${data.id})">
                                <i class="ri-download-line"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="DocumentsManager.email(${data.id})">
                                <i class="ri-mail-send-line"></i>
                            </button>
                        </div>
                    `,
                    },
                ],
                order: [
                    [4, "desc"]
                ],
            });
        },

        updateStats(stats) {
            $("#docGenerated").text(stats.generated || 0);
            $("#docSent").text(stats.sent || 0);
            $("#docSigned").text(stats.signed || 0);
        },

        reload() {
            if (this.table) {
                this.table.ajax.reload(null, false);
            }
        },
    };

    // ============================================================================
    // DOCUMENTS MANAGER (Generation, View, Download, Email)
    // ============================================================================

    const DocumentsManager = {
        async generate(type) {
            const typeNames = {
                debit_note: "Debit Note",
                credit_note: "Credit Note",
                statement: "Statement of Account",
                bordereau: "Bordereau",
                closing_slip: "Closing Slip",
            };

            const confirmed = await Utils.confirm(
                `Generate ${typeNames[type] || type}?`,
                "Generate Document"
            );

            if (!confirmed) return;

            try {
                Utils.showToast("info", "Generating document...", "Please wait");

                await Utils.ajax(
                    Utils.buildUrl(CONFIG.endpoints.documents.generate), {
                        method: "POST",
                        data: {
                            document_type: type
                        },
                    }
                );

                Utils.showToast("success", "Document generated successfully");
                DocumentsTable.reload();
            } catch (error) {
                Utils.showToast(
                    "error",
                    error.message || "Failed to generate document"
                );
            }
        },

        view(id) {
            const url = Utils.buildUrl(CONFIG.endpoints.documents.show, {
                id
            });
            window.open(url, "_blank");
        },

        download(id) {
            const url = Utils.buildUrl(CONFIG.endpoints.documents.download, {
                id
            });
            window.location.href = url;
        },

        async email(id) {
            const confirmed = await Utils.confirm(
                "Send this document via email?",
                "Email Document"
            );
            if (!confirmed) return;

            try {
                await Utils.ajax(
                    Utils.buildUrl(CONFIG.endpoints.documents.email, {
                        id
                    }), {
                        method: "POST",
                    }
                );
                Utils.showToast("success", "Document sent successfully");
                DocumentsTable.reload();
            } catch (error) {
                Utils.showToast(
                    "error",
                    error.message || "Failed to send document"
                );
            }
        },
    };

    // ============================================================================
    // INITIALIZATION
    // ============================================================================

    document.addEventListener("DOMContentLoaded", () => {
        // Validate configuration
        if (!CONFIG.coverId) {
            console.warn("Cover ID not found. Some features may not work.");
        }

        // Initialize all managers
        DebitItemsTable.init();
        ReinsurersTable.init();
        CedantManager.init();
        AttachmentsTable.init();
        ClausesManager.init();
        DocumentsTable.init();

        // Tab change handlers - lazy load data
        $('button[data-bs-toggle="tab"]').on("shown.bs.tab", (e) => {
            const targetId = $(e.target).data("bs-target");

            switch (targetId) {
                case "#reinsurers-tab":
                    if (ReinsurersTable.table)
                        ReinsurersTable.table.columns.adjust();
                    break;
                case "#cedant-tab":
                    if (AttachmentsTable.table)
                        AttachmentsTable.table.columns.adjust();
                    break;
                case "#docs-tab":
                    if (DocumentsTable.table) DocumentsTable.table.columns.adjust();
                    break;
            }
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        console.log("Quarterly Debit Statement initialized successfully");
    });

    // Export for global access
    window.DebitItemsTable = DebitItemsTable;
    window.ReinsurersTable = ReinsurersTable;
    window.CedantManager = CedantManager;
    window.AttachmentsTable = AttachmentsTable;
    window.ClausesManager = ClausesManager;
    window.DocumentsTable = DocumentsTable;
    window.DocumentsManager = DocumentsManager;
    window.Utils = Utils;
</script>
@endsection