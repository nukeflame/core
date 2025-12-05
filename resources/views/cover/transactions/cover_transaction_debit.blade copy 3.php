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
                            <i class="ri-building-2-line"></i> Reinsurers
                            <span class="badge bg-info ms-1">{{ $reinsurers->count() }}</span>
                        </button>

                        <button class="nav-link" id="nav-attachments-tab" data-bs-toggle="tab"
                            data-bs-target="#attachments-tab" type="button" role="tab" aria-selected="false">
                            <i class="bx bx-briefcase"></i> Cedant
                        </button>

                        <button class="nav-link" id="nav-clauses-tab" data-bs-toggle="tab" data-bs-target="#clauses-tab"
                            type="button" role="tab" aria-selected="false">
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
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
                                    <table id="debitItemsTable" class="table table-bordered w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Item Number</th>
                                                <th>Date</th>
                                                <th>Treaty Type</th>
                                                <th>Class</th>
                                                <th>Reinsurer</th>
                                                <th>Gross Premium</th>
                                                <th>Commission</th>
                                                <th>Net Amount</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($debitItems as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><strong>{{ $item->item_number }}</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($item->item_date)->format('d M Y') }}</td>
                                                    <td>
                                                        <span class="type-badge type-{{ Str::slug($item->treaty_type) }}">
                                                            {{ $item->treaty_type }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-medium">{{ $item->class_group }}</span><br>
                                                        <small class="text-muted">{{ $item->class_name }}</small>
                                                    </td>
                                                    <td>{{ $item->reinsurer }}</td>
                                                    <td class="amount-cell">{{ $cover->currency }}
                                                        {{ number_format($item->gross_premium, 2) }}</td>
                                                    <td class="amount-cell">
                                                        {{ $cover->currency }}
                                                        {{ number_format($item->commission_amount, 2) }}
                                                        <br><small
                                                            class="text-muted">({{ $item->commission_rate }}%)</small>
                                                    </td>
                                                    <td class="amount-cell amount-positive">{{ $cover->currency }}
                                                        {{ number_format($item->net_amount, 2) }}</td>
                                                    <td>
                                                        <span
                                                            class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-info btn-action"
                                                                onclick="viewItem({{ $item->id }})" title="View">
                                                                <i class="ri-eye-line"></i>
                                                            </button>
                                                            <button class="btn btn-outline-primary btn-action"
                                                                onclick="editItem({{ $item->id }})" title="Edit">
                                                                <i class="ri-edit-line"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-action"
                                                                onclick="deleteItem({{ $item->id }})" title="Delete">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="fw-bold bg-light">
                                                <td colspan="6" class="text-end">Totals:</td>
                                                <td class="amount-cell">{{ $cover->currency }}
                                                    {{ number_format($totalGrossPremium, 2) }}</td>
                                                <td class="amount-cell">{{ $cover->currency }}
                                                    {{ number_format($totalCommission, 2) }}</td>
                                                <td class="amount-cell amount-positive">{{ $cover->currency }}
                                                    {{ number_format($totalNetAmount, 2) }}</td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>


                        </div>
                    </div>

                    <!-- Reinsurers Tab -->
                    <div class="tab-pane fade" id="reinsurers-tab" role="tabpanel" aria-labelledby="nav-reinsurers-tab">
                        <div class="tab-header">
                            <h6><i class="ri-building-2-line text-info"></i> Participating Reinsurers</h6>
                            <div>
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#addReinsurerModal">
                                    <i class="ri-add-line"></i> Add Reinsurer
                                </button>
                            </div>
                        </div>

                        <!-- Cedant Information -->
                        <div class="cedant-info-card mb-3">
                            <h6 class="mb-3"><i class="ri-user-star-line text-primary me-2"></i>Cedant Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Company Name</span>
                                        <span class="info-value">{{ $cedantDetails->name }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Registration No.</span>
                                        <span class="info-value">{{ $cedantDetails->registration_no }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Contact Person</span>
                                        <span class="info-value">{{ $cedantDetails->contact_person }}
                                            ({{ $cedantDetails->designation }})</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Email</span>
                                        <span class="info-value">{{ $cedantDetails->email }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Phone</span>
                                        <span class="info-value">{{ $cedantDetails->phone }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Treaty Capacity</span>
                                        <span class="info-value">{{ $cover->currency }}
                                            {{ number_format($cedantDetails->treaty_capacity, 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="reinsurersTable" class="table table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reinsurer</th>
                                        <th>Contact Person</th>
                                        <th>Share %</th>
                                        <th>Commission Rate</th>
                                        <th>Share Premium</th>
                                        <th>Share Sum Insured</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reinsurers as $index => $reinsurer)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $reinsurer->name }}</strong><br>
                                                <small class="text-muted">{{ $reinsurer->email }}</small>
                                            </td>
                                            <td>{{ $reinsurer->contact_person }}</td>
                                            <td class="text-center"><span
                                                    class="badge bg-primary">{{ number_format($reinsurer->share_percentage, 1) }}%</span>
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($reinsurer->commission_rate, 1) }}%</td>
                                            <td class="amount-cell">{{ $cover->currency }}
                                                {{ number_format($reinsurer->share_premium, 2) }}</td>
                                            <td class="amount-cell">{{ $cover->currency }}
                                                {{ number_format($reinsurer->share_sum_insured, 0) }}</td>
                                            <td><span
                                                    class="status-badge status-{{ $reinsurer->status }}">{{ ucfirst($reinsurer->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-info btn-action"
                                                        onclick="viewReinsurer({{ $reinsurer->id }})" title="View">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    <button class="btn btn-outline-primary btn-action"
                                                        onclick="editReinsurer({{ $reinsurer->id }})" title="Edit">
                                                        <i class="ri-edit-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold bg-light">
                                        <td colspan="3" class="text-end">Totals:</td>
                                        <td class="text-center"><span
                                                class="badge bg-success">{{ number_format($reinsurers->sum('share_percentage'), 1) }}%</span>
                                        </td>
                                        <td></td>
                                        <td class="amount-cell">{{ $cover->currency }}
                                            {{ number_format($totalReinsurerShare, 2) }}</td>
                                        <td class="amount-cell">{{ $cover->currency }}
                                            {{ number_format($reinsurers->sum('share_sum_insured'), 0) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>



                    <!-- Print-outs Tab -->
                    <div class="tab-pane fade" id="docs-tab" role="tabpanel" aria-labelledby="nav-docs-tab">
                        <div class="tab-header">
                            <h6><i class="ri-printer-line text-success"></i> Generated Documents</h6>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="ri-add-line"></i> Generate Document
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"
                                            onclick="generateDocument('debit_note')"><i
                                                class="ri-file-text-line me-2"></i>Debit Note</a></li>
                                    <li><a class="dropdown-item" href="#"
                                            onclick="generateDocument('credit_note')"><i
                                                class="ri-file-text-line me-2"></i>Credit Note</a></li>
                                    <li><a class="dropdown-item" href="#"
                                            onclick="generateDocument('statement')"><i
                                                class="ri-file-list-line me-2"></i>Statement of Account</a></li>
                                    <li><a class="dropdown-item" href="#"
                                            onclick="generateDocument('bordereau')"><i
                                                class="ri-table-line me-2"></i>Bordereau</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#"
                                            onclick="generateDocument('closing_slip')"><i
                                                class="ri-file-paper-line me-2"></i>Closing Slip</a></li>
                                </ul>
                            </div>
                        </div>

                        @if ($documents->count() > 0)
                            <div class="table-responsive">
                                <table id="documentsTable" class="table table-bordered w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Document Type</th>
                                            <th>Reference</th>
                                            <th>Description</th>
                                            <th>Generated Date</th>
                                            <th>Generated By</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($documents as $index => $doc)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $doc->document_type }}</strong></td>
                                                <td><code>{{ $doc->reference }}</code></td>
                                                <td>{{ $doc->description }}</td>
                                                <td>{{ \Carbon\Carbon::parse($doc->generated_date)->format('d M Y') }}
                                                </td>
                                                <td>{{ $doc->generated_by }}</td>
                                                <td><span
                                                        class="status-badge status-{{ $doc->status }}">{{ ucfirst($doc->status) }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-info btn-action"
                                                            onclick="viewDocument({{ $doc->id }})" title="View">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                        <button class="btn btn-outline-success btn-action"
                                                            onclick="downloadDocument({{ $doc->id }})"
                                                            title="Download">
                                                            <i class="ri-download-line"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary btn-action"
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
                        @else
                            <div class="empty-state">
                                <i class="ri-printer-line d-block"></i>
                                <h6>No documents generated</h6>
                                <p class="text-muted">Generate documents using the button above</p>
                            </div>
                        @endif
                    </div>
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
                        <input type="hidden" name="cover_id" value="{{ $cover->id }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Treaty Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="treaty_type" id="treatyType" required>
                                    <option value="">Select Treaty Type</option>
                                    <option value="SURPLUS">Surplus</option>
                                    <option value="QUOTA SHARE">Quota Share</option>
                                    <option value="EXCESS OF LOSS">Excess of Loss</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Item Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="item_date" id="itemDate" required>
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
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Class Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="class_name" id="className"
                                    placeholder="e.g., Fire Industrial" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reinsurer <span class="text-danger">*</span></label>
                                <select class="form-select" name="reinsurer_id" id="reinsurerId" required>
                                    <option value="">Select Reinsurer</option>
                                    @foreach ($reinsurers as $reinsurer)
                                        <option value="{{ $reinsurer->id }}"
                                            data-commission-rate="{{ $reinsurer->commission_rate }}">
                                            {{ $reinsurer->name }} ({{ $reinsurer->share_percentage }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gross Premium <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $cover->currency }}</span>
                                    <input type="number" class="form-control" name="gross_premium" id="grossPremium"
                                        step="0.01" min="0" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Commission Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="commission_rate" id="commissionRate"
                                    step="0.01" min="0" max="100" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Commission Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $cover->currency }}</span>
                                    <input type="number" class="form-control bg-light" name="commission_amount"
                                        id="commissionAmount" step="0.01" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Net Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $cover->currency }}</span>
                                    <input type="number" class="form-control bg-light" name="net_amount" id="netAmount"
                                        step="0.01" readonly>
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
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveDebitItemBtn">
                        <i class="ri-save-line me-1"></i>Save Item
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // ============================================
            // Initialize DataTables
            // ============================================
            const dataTableDefaults = {
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: '<i class="ri-skip-back-line"></i>',
                        last: '<i class="ri-skip-forward-line"></i>',
                        next: '<i class="ri-arrow-right-s-line"></i>',
                        previous: '<i class="ri-arrow-left-s-line"></i>'
                    }
                }
            };

            // Debit Items Table
            const debitItemsTable = $('#debitItemsTable').DataTable({
                ...dataTableDefaults,
                order: [
                    [2, 'desc']
                ],
                columnDefs: [{
                        targets: [6, 7, 8],
                        className: 'text-end'
                    },
                    {
                        targets: [-1],
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Reinsurers Table
            const reinsurersTable = $('#reinsurersTable').DataTable({
                ...dataTableDefaults,
                order: [
                    [3, 'desc']
                ],
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

            // Attachments Table
            const attachmentsTable = $('#attachmentsTable').DataTable({
                ...dataTableDefaults,
                order: [
                    [5, 'desc']
                ],
                columnDefs: [{
                    targets: [-1],
                    orderable: false,
                    searchable: false
                }]
            });

            // Documents Table
            const documentsTable = $('#documentsTable').DataTable({
                ...dataTableDefaults,
                order: [
                    [4, 'desc']
                ],
                columnDefs: [{
                    targets: [-1],
                    orderable: false,
                    searchable: false
                }]
            });

            // ============================================
            // Auto-calculate commission and net amount
            // ============================================
            function calculateAmounts() {
                const grossPremium = parseFloat($('#grossPremium').val()) || 0;
                const commissionRate = parseFloat($('#commissionRate').val()) || 0;

                const commissionAmount = (grossPremium * commissionRate) / 100;
                const netAmount = grossPremium - commissionAmount;

                $('#commissionAmount').val(commissionAmount.toFixed(2));
                $('#netAmount').val(netAmount.toFixed(2));
            }

            $('#grossPremium, #commissionRate').on('input', calculateAmounts);

            // Auto-fill commission rate when reinsurer is selected
            $('#reinsurerId').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const commissionRate = selectedOption.data('commission-rate');
                if (commissionRate) {
                    $('#commissionRate').val(commissionRate);
                    calculateAmounts();
                }
            });

            // ============================================
            // Save Debit Item
            // ============================================
            $('#saveDebitItemBtn').on('click', function() {
                const $btn = $(this);
                const $form = $('#addDebitItemForm');

                // Validate form
                if (!$form[0].checkValidity()) {
                    $form[0].reportValidity();
                    return;
                }

                $btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-1"></i>Saving...');

                const formData = new FormData($form[0]);

                //{{-- $.ajax({
                //     url: '{{ route('debit-items.store') ?? '/debit-items' }}',
                //     method: 'POST',
                //     data: formData,
                //     processData: false,
                //     contentType: false,
                //     headers: {
                //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                //     },
                //     success: function(response) {
                //         if (response.success) {
                //             $('#addDebitItemModal').modal('hide');
                //             showToast('success', 'Debit item saved successfully!');
                //             location.reload();
                //         } else {
                //             showToast('error', response.message || 'Failed to save item');
                //         }
                //     },
                //     error: function(xhr) {
                //         const message = xhr.responseJSON?.message ||
                //             'An error occurred while saving';
                //         showToast('error', message);
                //     },
                //     complete: function() {
                //         $btn.prop('disabled', false).html(
                //             '<i class="ri-save-line me-1"></i>Save Item');
                //     }
                // --}} });
            });

            // ============================================
            // Upload File
            // ============================================
            $('#uploadFileBtn').on('click', function() {
                const $btn = $(this);
                const $form = $('#uploadFileForm');
                const fileInput = $('#uploadFile')[0];

                if (!fileInput.files.length) {
                    showToast('warning', 'Please select a file to upload');
                    return;
                }

                $btn.prop('disabled', true).html(
                    '<i class="ri-loader-4-line ri-spin me-1"></i>Uploading...');

                const formData = new FormData($form[0]);

                //{{-- $.ajax({
                //     url: '{{ route('attachments.store') ?? '/attachments' }}',
                //     method: 'POST',
                //     data: formData,
                //     processData: false,
                //     contentType: false,
                //     headers: {
                //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                //     },
                //     success: function(response) {
                //         if (response.success) {
                //             $('#uploadFileModal').modal('hide');
                //             showToast('success', 'File uploaded successfully!');
                //             location.reload();
                //         } else {
                //             showToast('error', response.message || 'Failed to upload file');
                //         }
                //     },
                //     error: function(xhr) {
                //         const message = xhr.responseJSON?.message ||
                //             'An error occurred while uploading';
                //         showToast('error', message);
                //     },
                //     complete: function() {
                //         $btn.prop('disabled', false).html(
                //             '<i class="ri-upload-line me-1"></i>Upload');
                //     }
                // --}} });
            });

            // ============================================
            // Save Clause
            // ============================================
            $('#saveClauseBtn').on('click', function() {
                const $btn = $(this);
                const $form = $('#addClauseForm');

                if (!$form[0].checkValidity()) {
                    $form[0].reportValidity();
                    return;
                }

                $btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-1"></i>Saving...');

                const formData = new FormData($form[0]);

                //{{-- $.ajax({
                //     url: '{{ route('clauses.store') ?? '/clauses' }}',
                //     method: 'POST',
                //     data: formData,
                //     processData: false,
                //     contentType: false,
                //     headers: {
                //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                //     },
                //     success: function(response) {
                //         if (response.success) {
                //             $('#addClauseModal').modal('hide');
                //             showToast('success', 'Clause saved successfully!');
                //             location.reload();
                //         } else {
                //             showToast('error', response.message || 'Failed to save clause');
                //         }
                //     },
                //     error: function(xhr) {
                //         const message = xhr.responseJSON?.message ||
                //             'An error occurred while saving';
                //         showToast('error', message);
                //     },
                //     complete: function() {
                //         $btn.prop('disabled', false).html(
                //             '<i class="ri-save-line me-1"></i>Save Clause');
                //     }
                // }); --}}
            });

            // ============================================
            // Drag and Drop File Upload
            // ============================================
            const dropZone = $('#dropZone');
            const fileInput = $('#fileInput');

            dropZone.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('border-primary');
            });

            dropZone.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('border-primary');
            });

            dropZone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('border-primary');

                const files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    handleFileUpload(files);
                }
            });

            dropZone.on('click', function() {
                fileInput.click();
            });

            fileInput.on('change', function() {
                if (this.files.length) {
                    handleFileUpload(this.files);
                }
            });

            function handleFileUpload(files) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('cover_id', '{{ $cover->id }}');

                for (let i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }



            }

            // ============================================
            // Initialize Tooltips
            // ============================================
            $('[data-bs-toggle="tooltip"]').tooltip();

            // ============================================
            // Modal Reset on Close
            // ============================================
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form')[0]?.reset();
                $('#commissionAmount, #netAmount').val('');
            });
        });

        // ============================================
        // Global Functions
        // ============================================

        // Toast Notification
        function showToast(type, message) {
            const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning';
            const toast = $(`
                <div class="toast align-items-center text-white ${bgClass} border-0 position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `);
            $('body').append(toast);
            const bsToast = new bootstrap.Toast(toast[0], {
                delay: 4000
            });
            bsToast.show();
            toast.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }

        // Preview Slip
        function previewSlip() {
            const coverId = {{ $cover->id ?? 1 }};
            window.open(`/covers/${coverId}/preview-slip`, '_blank');
        }

        // Generate Statement
        function generateStatement() {
            const coverId = {{ $cover->id ?? 1 }};
            if (confirm('Generate Statement of Account for this treaty?')) {
                window.location.href = `/covers/${coverId}/generate-statement`;
            }
        }

        // Export Data
        function exportData() {
            const coverId = {{ $cover->id ?? 1 }};
            window.location.href = `/covers/${coverId}/export`;
        }

        // Refresh Debit Items
        function refreshDebitItems() {
            location.reload();
        }

        // View/Edit/Delete Item
        function viewItem(itemId) {
            window.location.href = `/debit-items/${itemId}`;
        }

        function editItem(itemId) {
            window.location.href = `/debit-items/${itemId}/edit`;
        }

        function deleteItem(itemId) {
            if (confirm('Are you sure you want to delete this debit item?')) {
                $.ajax({
                    url: `/debit-items/${itemId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Item deleted successfully!');
                            location.reload();
                        } else {
                            showToast('error', response.message || 'Failed to delete item');
                        }
                    },
                    error: function() {
                        showToast('error', 'An error occurred while deleting');
                    }
                });
            }
        }

        // View/Edit Reinsurer
        function viewReinsurer(reinsurerId) {
            window.location.href = `/reinsurers/${reinsurerId}`;
        }

        function editReinsurer(reinsurerId) {
            window.location.href = `/reinsurers/${reinsurerId}/edit`;
        }

        // Attachment Functions
        function viewAttachment(attachmentId) {
            window.open(`/attachments/${attachmentId}/view`, '_blank');
        }

        function downloadAttachment(attachmentId) {
            window.location.href = `/attachments/${attachmentId}/download`;
        }

        function deleteAttachment(attachmentId) {
            if (confirm('Are you sure you want to delete this attachment?')) {
                $.ajax({
                    url: `/attachments/${attachmentId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Attachment deleted successfully!');
                            location.reload();
                        } else {
                            showToast('error', response.message || 'Failed to delete attachment');
                        }
                    },
                    error: function() {
                        showToast('error', 'An error occurred while deleting');
                    }
                });
            }
        }

        // Clause Functions
        function editClause(clauseId) {
            window.location.href = `/clauses/${clauseId}/edit`;
        }

        function deleteClause(clauseId) {
            if (confirm('Are you sure you want to delete this clause?')) {
                $.ajax({
                    url: `/clauses/${clauseId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Clause deleted successfully!');
                            location.reload();
                        } else {
                            showToast('error', response.message || 'Failed to delete clause');
                        }
                    },
                    error: function() {
                        showToast('error', 'An error occurred while deleting');
                    }
                });
            }
        }

        // Document Functions
        function generateDocument(docType) {
            const coverId = {{ $cover->id ?? 1 }};
            const docTypeNames = {
                'debit_note': 'Debit Note',
                'credit_note': 'Credit Note',
                'statement': 'Statement of Account',
                'bordereau': 'Bordereau',
                'closing_slip': 'Closing Slip'
            };

            if (confirm(`Generate ${docTypeNames[docType] || docType} for this treaty?`)) {
                $.ajax({
                    url: `/covers/${coverId}/generate-document`,
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        document_type: docType
                    }),
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Document generated successfully!');
                            location.reload();
                        } else {
                            showToast('error', response.message || 'Failed to generate document');
                        }
                    },
                    error: function() {
                        showToast('error', 'An error occurred while generating the document');
                    }
                });
            }
        }

        function viewDocument(docId) {
            window.open(`/documents/${docId}/view`, '_blank');
        }

        function downloadDocument(docId) {
            window.location.href = `/documents/${docId}/download`;
        }

        function emailDocument(docId) {
            if (confirm('Send this document via email?')) {
                $.ajax({
                    url: `/documents/${docId}/email`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Document sent successfully!');
                        } else {
                            showToast('error', response.message || 'Failed to send document');
                        }
                    },
                    error: function() {
                        showToast('error', 'An error occurred while sending the document');
                    }
                });
            }
        }
    </script>
@endsection
