@extends('layouts.app')

@section('content')
    <style>
        /* Clean, minimal hover effects */
        .table-hover tbody tr {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Status badge styling */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background-color: #22c55e;
            color: white;
        }

        .status-inactive {
            background-color: #94a3b8;
            color: white;
        }

        .status-pending {
            background-color: #f59e0b;
            color: white;
        }

        .status-approved {
            background-color: #10b981;
            color: white;
        }

        .status-rejected {
            background-color: #ef4444;
            color: white;
        }

        /* Card sections */
        .info-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .section-title {
            color: #2563eb;
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #374151;
            min-width: 180px;
            flex-shrink: 0;
        }

        .info-value {
            color: #6b7280;
            flex-grow: 1;
        }

        .info-value.highlight {
            color: #2563eb;
            font-weight: 600;
            font-size: 1.125rem;
        }

        .info-value.amount {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #059669;
        }

        /* Quick Action buttons */
        .quick-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .quick-action-btn {
            padding: 0.625rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .quick-action-btn i {
            font-size: 1rem;
        }

        .btn-preview-slip {
            background-color: #3b82f6;
            color: white;
        }

        .btn-preview-slip:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
        }

        .btn-debit {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-debit:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-profit-commission {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-profit-commission:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-portfolio {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-portfolio:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-commission-adjustment {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-commission-adjustment:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }

        /* Tab styling */
        .custom-tabs {
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }

        .custom-tabs .nav-link {
            color: #6b7280;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border: none;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .custom-tabs .nav-link:hover {
            color: #2563eb;
            border-bottom-color: #93c5fd;
        }

        .custom-tabs .nav-link.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
            background: transparent;
        }

        /* Table styling */
        .data-table {
            width: 100%;
            margin-top: 1rem;
        }

        .data-table thead {
            background-color: #f9fafb;
        }

        .data-table th {
            font-weight: 600;
            color: #374151;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 0.875rem 1rem;
            color: #6b7280;
        }

        .data-table .amount-cell {
            font-family: 'Courier New', monospace;
            font-weight: 500;
            text-align: right;
        }

        .data-table .positive-amount {
            color: #059669;
        }

        .data-table .negative-amount {
            color: #dc2626;
        }

        /* Financial Summary Cards */
        .financial-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 1.25rem;
            color: white;
        }

        .summary-card.debit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .summary-card.commission {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .summary-card.profit {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .summary-card.portfolio {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .summary-card-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .summary-card-value {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            margin: 0;
            font-size: 0.875rem;
        }

        /* Action buttons in tables */
        .btn-table-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.813rem;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 8px;
            border: none;
        }

        .modal-header {
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            border-radius: 8px 8px 0 0;
        }

        .modal-title {
            color: #2563eb;
            font-weight: 600;
        }

        /* Form styling */
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #d1d5db;
            padding: 0.625rem 0.875rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .quick-actions {
                flex-direction: column;
            }

            .quick-action-btn {
                width: 100%;
                justify-content: center;
            }

            .financial-summary {
                grid-template-columns: 1fr;
            }

            .data-table {
                font-size: 0.813rem;
            }

            .data-table th,
            .data-table td {
                padding: 0.5rem 0.625rem;
            }
        }
    </style>


    @php
        // SINGLE POLICY DATA
        $policy = (object) [
            'id' => 1,
            'policy_number' => 'POL-2024-001',
            'cover_number' => 'COV-2024-001',
            'insured_by' => 'East African Breweries Ltd',
            'insured_contact' => 'John Mwangi',
            'insured_email' => 'jmwangi@eabl.com',
            'insured_phone' => '+254 712 345 678',
            'class_of_business' => 'Property All Risks',
            'risk_location' => 'Thika Road, Nairobi',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'sum_insured' => 150000000,
            'premium' => 2500000,
            'broker_name' => 'Kenya Insurance Brokers Ltd',
            'brokerage_rate' => 10.0,
            'brokerage_amount' => 250000,
            'status' => 'active',
            'currency' => 'KES',
            'treaty_name' => 'Property Treaty 2024',
            'treaty_type' => 'Quota Share',
            'retention_percentage' => 30.0,
            'ceded_percentage' => 70.0,
            'ceded_premium' => 1750000,
            'created_at' => '2024-01-01',
            'created_by' => 'Peter Kamau',
        ];

        // REINSURERS PARTICIPATING IN THIS POLICY
        $reinsurers = [
            (object) [
                'id' => 1,
                'name' => 'Swiss Re Africa',
                'share_percentage' => 30.0,
                'share_premium' => 525000,
                'share_sum_insured' => 45000000,
                'status' => 'active',
            ],
            (object) [
                'id' => 2,
                'name' => 'Munich Re Kenya',
                'share_percentage' => 25.0,
                'share_premium' => 437500,
                'share_sum_insured' => 37500000,
                'status' => 'active',
            ],
            (object) [
                'id' => 3,
                'name' => 'Hannover Re',
                'share_percentage' => 15.0,
                'share_premium' => 262500,
                'share_sum_insured' => 22500000,
                'status' => 'active',
            ],
        ];

        // DEBIT NOTES - 20 records
        $debits = [
            (object) [
                'id' => 1,
                'debit_note_number' => 'ITM-2024-00001',
                'debit_date' => '2024-01-15',
                'reinsurer' => (object) ['name' => 'Swiss Re Africa'],
                'debit_type' => 'premium',
                'description' => 'Q1 2024 premium debit - Property All Risks',
                'gross_premium' => 525000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 52500,
                'taxes' => 0,
                'net_amount' => 472500,
                'due_date' => '2024-02-14',
                'payment_date' => '2024-02-10',
                'status' => 'paid',
            ],
            (object) [
                'id' => 2,
                'debit_note_number' => 'ITM-2024-00002',
                'debit_date' => '2024-01-15',
                'reinsurer' => (object) ['name' => 'Munich Re Kenya'],
                'debit_type' => 'premium',
                'description' => 'Q1 2024 premium debit - Property All Risks',
                'gross_premium' => 437500,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 43750,
                'taxes' => 0,
                'net_amount' => 393750,
                'due_date' => '2024-02-14',
                'payment_date' => '2024-02-12',
                'status' => 'paid',
            ],
            (object) [
                'id' => 3,
                'debit_note_number' => 'ITM-2024-00003',
                'debit_date' => '2024-01-15',
                'reinsurer' => (object) ['name' => 'Hannover Re'],
                'debit_type' => 'premium',
                'description' => 'Q1 2024 premium debit - Property All Risks',
                'gross_premium' => 262500,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 26250,
                'taxes' => 0,
                'net_amount' => 236250,
                'due_date' => '2024-02-14',
                'payment_date' => '2024-02-13',
                'status' => 'paid',
            ],
            (object) [
                'id' => 4,
                'debit_note_number' => 'ITM-2024-00004',
                'debit_date' => '2024-04-01',
                'reinsurer' => (object) ['name' => 'Swiss Re Africa'],
                'debit_type' => 'adjustment',
                'description' => 'Premium adjustment - Sum insured increase',
                'gross_premium' => 75000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 7500,
                'taxes' => 0,
                'net_amount' => 67500,
                'due_date' => '2024-05-01',
                'payment_date' => null,
                'status' => 'pending',
            ],
            (object) [
                'id' => 5,
                'debit_note_number' => 'ITM-2024-00005',
                'debit_date' => '2024-04-01',
                'reinsurer' => (object) ['name' => 'Munich Re Kenya'],
                'debit_type' => 'adjustment',
                'description' => 'Premium adjustment - Sum insured increase',
                'gross_premium' => 62500,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 6250,
                'taxes' => 0,
                'net_amount' => 56250,
                'due_date' => '2024-05-01',
                'payment_date' => null,
                'status' => 'pending',
            ],
            (object) [
                'id' => 6,
                'debit_note_number' => 'DN-2024-00006',
                'debit_date' => '2024-07-01',
                'reinsurer' => (object) ['name' => 'Swiss Re Africa'],
                'debit_type' => 'additional',
                'description' => 'Additional premium - Extended coverage',
                'gross_premium' => 50000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 5000,
                'taxes' => 0,
                'net_amount' => 45000,
                'due_date' => '2024-07-31',
                'payment_date' => '2024-07-28',
                'status' => 'paid',
            ],
            (object) [
                'id' => 7,
                'debit_note_number' => 'DN-2024-00007',
                'debit_date' => '2024-07-01',
                'reinsurer' => (object) ['name' => 'Munich Re Kenya'],
                'debit_type' => 'additional',
                'description' => 'Additional premium - Extended coverage',
                'gross_premium' => 42000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 4200,
                'taxes' => 0,
                'net_amount' => 37800,
                'due_date' => '2024-07-31',
                'payment_date' => '2024-07-29',
                'status' => 'paid',
            ],
            (object) [
                'id' => 8,
                'debit_note_number' => 'DN-2024-00008',
                'debit_date' => '2024-10-01',
                'reinsurer' => (object) ['name' => 'Hannover Re'],
                'debit_type' => 'deposit',
                'description' => 'Deposit premium - Q4 2024',
                'gross_premium' => 65000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 6500,
                'taxes' => 0,
                'net_amount' => 58500,
                'due_date' => '2024-10-31',
                'payment_date' => null,
                'status' => 'pending',
            ],
            (object) [
                'id' => 9,
                'debit_note_number' => 'DN-2024-00009',
                'debit_date' => '2024-02-15',
                'reinsurer' => (object) ['name' => 'Swiss Re Africa'],
                'debit_type' => 'reinstatement',
                'description' => 'Reinstatement premium - After partial loss',
                'gross_premium' => 30000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 3000,
                'taxes' => 0,
                'net_amount' => 27000,
                'due_date' => '2024-03-17',
                'payment_date' => '2024-03-15',
                'status' => 'paid',
            ],
            (object) [
                'id' => 10,
                'debit_note_number' => 'DN-2024-00010',
                'debit_date' => '2024-05-20',
                'reinsurer' => (object) ['name' => 'Munich Re Kenya'],
                'debit_type' => 'adjustment',
                'description' => 'Premium correction - Rate adjustment',
                'gross_premium' => -25000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => -2500,
                'taxes' => 0,
                'net_amount' => -22500,
                'due_date' => '2024-06-20',
                'payment_date' => '2024-06-18',
                'status' => 'paid',
            ],
            (object) [
                'id' => 11,
                'debit_note_number' => 'DN-2024-00011',
                'debit_date' => '2024-03-10',
                'reinsurer' => (object) ['name' => 'Hannover Re'],
                'debit_type' => 'premium',
                'description' => 'Q2 2024 premium debit',
                'gross_premium' => 65625,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 6562.5,
                'taxes' => 0,
                'net_amount' => 59062.5,
                'due_date' => '2024-04-10',
                'payment_date' => '2024-04-08',
                'status' => 'paid',
            ],
            (object) [
                'id' => 12,
                'debit_note_number' => 'DN-2024-00012',
                'debit_date' => '2024-06-15',
                'reinsurer' => (object) ['name' => 'Swiss Re Africa'],
                'debit_type' => 'additional',
                'description' => 'Mid-term adjustment - Machinery addition',
                'gross_premium' => 40000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 4000,
                'taxes' => 0,
                'net_amount' => 36000,
                'due_date' => '2024-07-15',
                'payment_date' => '2024-07-12',
                'status' => 'paid',
            ],
            (object) [
                'id' => 13,
                'debit_note_number' => 'DN-2024-00013',
                'debit_date' => '2024-08-20',
                'reinsurer' => (object) ['name' => 'Munich Re Kenya'],
                'debit_type' => 'deposit',
                'description' => 'Advance deposit premium',
                'gross_premium' => 55000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 5500,
                'taxes' => 0,
                'net_amount' => 49500,
                'due_date' => '2024-09-20',
                'payment_date' => null,
                'status' => 'overdue',
            ],
            (object) [
                'id' => 14,
                'debit_note_number' => 'DN-2024-00014',
                'debit_date' => '2024-09-05',
                'reinsurer' => (object) ['name' => 'Hannover Re'],
                'debit_type' => 'adjustment',
                'description' => 'Premium adjustment - Reduced risk',
                'gross_premium' => -15000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => -1500,
                'taxes' => 0,
                'net_amount' => -13500,
                'due_date' => '2024-10-05',
                'payment_date' => '2024-10-03',
                'status' => 'paid',
            ],
            (object) [
                'id' => 15,
                'debit_note_number' => 'DN-2024-00015',
                'debit_date' => '2024-11-01',
                'reinsurer' => (object) ['name' => 'Swiss Re Africa'],
                'debit_type' => 'premium',
                'description' => 'Q4 2024 premium debit',
                'gross_premium' => 131250,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 13125,
                'taxes' => 0,
                'net_amount' => 118125,
                'due_date' => '2024-12-01',
                'payment_date' => null,
                'status' => 'pending',
            ],
            (object) [
                'id' => 16,
                'debit_note_number' => 'DN-2024-00016',
                'debit_date' => '2024-11-01',
                'reinsurer' => (object) ['name' => 'Munich Re Kenya'],
                'debit_type' => 'premium',
                'description' => 'Q4 2024 premium debit',
                'gross_premium' => 109375,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 10937.5,
                'taxes' => 0,
                'net_amount' => 98437.5,
                'due_date' => '2024-12-01',
                'payment_date' => null,
                'status' => 'pending',
            ],
            (object) [
                'id' => 17,
                'debit_note_number' => 'DN-2024-00017',
                'debit_date' => '2024-11-01',
                'reinsurer' => (object) ['name' => 'Hannover Re'],
                'debit_type' => 'premium',
                'description' => 'Q4 2024 premium debit',
                'gross_premium' => 65625,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 6562.5,
                'taxes' => 0,
                'net_amount' => 59062.5,
                'due_date' => '2024-12-01',
                'payment_date' => null,
                'status' => 'pending',
            ],
            (object) [
                'id' => 18,
                'debit_note_number' => 'DN-2024-00018',
                'debit_date' => '2024-04-15',
                'reinsurer' => (object) ['name' => 'Swiss Re Africa'],
                'debit_type' => 'reinstatement',
                'description' => 'Reinstatement premium - Fire damage recovery',
                'gross_premium' => 35000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 3500,
                'taxes' => 0,
                'net_amount' => 31500,
                'due_date' => '2024-05-15',
                'payment_date' => '2024-05-14',
                'status' => 'paid',
            ],
            (object) [
                'id' => 19,
                'debit_note_number' => 'DN-2024-00019',
                'debit_date' => '2024-07-25',
                'reinsurer' => (object) ['name' => 'Munich Re Kenya'],
                'debit_type' => 'additional',
                'description' => 'Additional premium - New building added',
                'gross_premium' => 45000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 4500,
                'taxes' => 0,
                'net_amount' => 40500,
                'due_date' => '2024-08-25',
                'payment_date' => '2024-08-22',
                'status' => 'paid',
            ],
            (object) [
                'id' => 20,
                'debit_note_number' => 'DN-2024-00020',
                'debit_date' => '2024-10-15',
                'reinsurer' => (object) ['name' => 'Hannover Re'],
                'debit_type' => 'adjustment',
                'description' => 'Final premium adjustment - Year end',
                'gross_premium' => 20000,
                'brokerage_rate' => 10.0,
                'brokerage_amount' => 2000,
                'taxes' => 0,
                'net_amount' => 18000,
                'due_date' => '2024-11-15',
                'payment_date' => null,
                'status' => 'pending',
            ],
        ];

        // PROFIT COMMISSION CALCULATIONS - 20 records (quarterly and annual)
        $profitCommissions = [
            (object) [
                'id' => 1,
                'reference_number' => 'PC-2024-Q1-001',
                'calculation_date' => '2024-04-01',
                'period' => 'Q1 2024',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 437500,
                'claims_incurred' => 0,
                'expenses' => 43750,
                'underwriting_profit' => 393750,
                'loss_ratio' => 0.0,
                'commission_rate' => 20.0,
                'profit_commission' => 78750,
                'payment_status' => 'approved',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 2,
                'reference_number' => 'PC-2024-Q1-002',
                'calculation_date' => '2024-04-01',
                'period' => 'Q1 2024',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 364583,
                'claims_incurred' => 0,
                'expenses' => 36458,
                'underwriting_profit' => 328125,
                'loss_ratio' => 0.0,
                'commission_rate' => 20.0,
                'profit_commission' => 65625,
                'payment_status' => 'approved',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 3,
                'reference_number' => 'PC-2024-Q1-003',
                'calculation_date' => '2024-04-01',
                'period' => 'Q1 2024',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 218750,
                'claims_incurred' => 0,
                'expenses' => 21875,
                'underwriting_profit' => 196875,
                'loss_ratio' => 0.0,
                'commission_rate' => 20.0,
                'profit_commission' => 39375,
                'payment_status' => 'approved',
                'reinsurer' => 'Hannover Re',
            ],
            (object) [
                'id' => 4,
                'reference_number' => 'PC-2024-Q2-001',
                'calculation_date' => '2024-07-01',
                'period' => 'Q2 2024',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 472500,
                'claims_incurred' => 150000,
                'expenses' => 47250,
                'underwriting_profit' => 275250,
                'loss_ratio' => 31.75,
                'commission_rate' => 17.0,
                'profit_commission' => 46792.5,
                'payment_status' => 'approved',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 5,
                'reference_number' => 'PC-2024-Q2-002',
                'calculation_date' => '2024-07-01',
                'period' => 'Q2 2024',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 393750,
                'claims_incurred' => 125000,
                'expenses' => 39375,
                'underwriting_profit' => 229375,
                'loss_ratio' => 31.75,
                'commission_rate' => 17.0,
                'profit_commission' => 38993.75,
                'payment_status' => 'approved',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 6,
                'reference_number' => 'PC-2024-Q2-003',
                'calculation_date' => '2024-07-01',
                'period' => 'Q2 2024',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 236250,
                'claims_incurred' => 75000,
                'expenses' => 23625,
                'underwriting_profit' => 137625,
                'loss_ratio' => 31.75,
                'commission_rate' => 17.0,
                'profit_commission' => 23396.25,
                'payment_status' => 'approved',
                'reinsurer' => 'Hannover Re',
            ],
            (object) [
                'id' => 7,
                'reference_number' => 'PC-2024-Q3-001',
                'calculation_date' => '2024-10-01',
                'period' => 'Q3 2024',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 445500,
                'claims_incurred' => 200000,
                'expenses' => 44550,
                'underwriting_profit' => 200950,
                'loss_ratio' => 44.89,
                'commission_rate' => 12.0,
                'profit_commission' => 24114,
                'payment_status' => 'calculated',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 8,
                'reference_number' => 'PC-2024-Q3-002',
                'calculation_date' => '2024-10-01',
                'period' => 'Q3 2024',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 371250,
                'claims_incurred' => 166667,
                'expenses' => 37125,
                'underwriting_profit' => 167458,
                'loss_ratio' => 44.89,
                'commission_rate' => 12.0,
                'profit_commission' => 20094.96,
                'payment_status' => 'calculated',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 9,
                'reference_number' => 'PC-2024-Q3-003',
                'calculation_date' => '2024-10-01',
                'period' => 'Q3 2024',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 222750,
                'claims_incurred' => 100000,
                'expenses' => 22275,
                'underwriting_profit' => 100475,
                'loss_ratio' => 44.89,
                'commission_rate' => 12.0,
                'profit_commission' => 12057,
                'payment_status' => 'calculated',
                'reinsurer' => 'Hannover Re',
            ],
            (object) [
                'id' => 10,
                'reference_number' => 'PC-2024-PROV-001',
                'calculation_date' => '2024-06-30',
                'period' => 'H1 2024 (Provisional)',
                'calculation_method' => 'provisional',
                'net_premium' => 910000,
                'claims_incurred' => 150000,
                'expenses' => 91000,
                'underwriting_profit' => 669000,
                'loss_ratio' => 16.48,
                'commission_rate' => 18.5,
                'profit_commission' => 123765,
                'payment_status' => 'pending_payment',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 11,
                'reference_number' => 'PC-2024-PROV-002',
                'calculation_date' => '2024-06-30',
                'period' => 'H1 2024 (Provisional)',
                'calculation_method' => 'provisional',
                'net_premium' => 758333,
                'claims_incurred' => 125000,
                'expenses' => 75833,
                'underwriting_profit' => 557500,
                'loss_ratio' => 16.48,
                'commission_rate' => 18.5,
                'profit_commission' => 103137.5,
                'payment_status' => 'pending_payment',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 12,
                'reference_number' => 'PC-2024-PROV-003',
                'calculation_date' => '2024-06-30',
                'period' => 'H1 2024 (Provisional)',
                'calculation_method' => 'provisional',
                'net_premium' => 455000,
                'claims_incurred' => 75000,
                'expenses' => 45500,
                'underwriting_profit' => 334500,
                'loss_ratio' => 16.48,
                'commission_rate' => 18.5,
                'profit_commission' => 61882.5,
                'payment_status' => 'pending_payment',
                'reinsurer' => 'Hannover Re',
            ],
            (object) [
                'id' => 13,
                'reference_number' => 'PC-2024-ANNUAL-001',
                'calculation_date' => '2024-12-31',
                'period' => 'FY 2024 (Projection)',
                'calculation_method' => 'fixed',
                'net_premium' => 1575000,
                'claims_incurred' => 500000,
                'expenses' => 157500,
                'underwriting_profit' => 917500,
                'loss_ratio' => 31.75,
                'commission_rate' => 15.0,
                'profit_commission' => 137625,
                'payment_status' => 'calculated',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 14,
                'reference_number' => 'PC-2024-ANNUAL-002',
                'calculation_date' => '2024-12-31',
                'period' => 'FY 2024 (Projection)',
                'calculation_method' => 'fixed',
                'net_premium' => 1312500,
                'claims_incurred' => 416667,
                'expenses' => 131250,
                'underwriting_profit' => 764583,
                'loss_ratio' => 31.75,
                'commission_rate' => 15.0,
                'profit_commission' => 114687.45,
                'payment_status' => 'calculated',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 15,
                'reference_number' => 'PC-2024-ANNUAL-003',
                'calculation_date' => '2024-12-31',
                'period' => 'FY 2024 (Projection)',
                'calculation_method' => 'fixed',
                'net_premium' => 787500,
                'claims_incurred' => 250000,
                'expenses' => 78750,
                'underwriting_profit' => 458750,
                'loss_ratio' => 31.75,
                'commission_rate' => 15.0,
                'profit_commission' => 68812.5,
                'payment_status' => 'calculated',
                'reinsurer' => 'Hannover Re',
            ],
            (object) [
                'id' => 16,
                'reference_number' => 'PC-2024-ADJ-001',
                'calculation_date' => '2024-05-15',
                'period' => 'Q1 2024 Adjustment',
                'calculation_method' => 'fixed',
                'net_premium' => 50000,
                'claims_incurred' => 0,
                'expenses' => 5000,
                'underwriting_profit' => 45000,
                'loss_ratio' => 0.0,
                'commission_rate' => 20.0,
                'profit_commission' => 9000,
                'payment_status' => 'approved',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 17,
                'reference_number' => 'PC-2024-ADJ-002',
                'calculation_date' => '2024-08-10',
                'period' => 'Q2 2024 Adjustment',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 35000,
                'claims_incurred' => 10000,
                'expenses' => 3500,
                'underwriting_profit' => 21500,
                'loss_ratio' => 28.57,
                'commission_rate' => 18.0,
                'profit_commission' => 3870,
                'payment_status' => 'approved',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 18,
                'reference_number' => 'PC-2024-FINAL-001',
                'calculation_date' => '2024-11-30',
                'period' => 'Q4 2024 (Preliminary)',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 400000,
                'claims_incurred' => 180000,
                'expenses' => 40000,
                'underwriting_profit' => 180000,
                'loss_ratio' => 45.0,
                'commission_rate' => 12.0,
                'profit_commission' => 21600,
                'payment_status' => 'pending_payment',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 19,
                'reference_number' => 'PC-2024-FINAL-002',
                'calculation_date' => '2024-11-30',
                'period' => 'Q4 2024 (Preliminary)',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 333333,
                'claims_incurred' => 150000,
                'expenses' => 33333,
                'underwriting_profit' => 150000,
                'loss_ratio' => 45.0,
                'commission_rate' => 12.0,
                'profit_commission' => 18000,
                'payment_status' => 'pending_payment',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 20,
                'reference_number' => 'PC-2024-FINAL-003',
                'calculation_date' => '2024-11-30',
                'period' => 'Q4 2024 (Preliminary)',
                'calculation_method' => 'sliding_scale',
                'net_premium' => 200000,
                'claims_incurred' => 90000,
                'expenses' => 20000,
                'underwriting_profit' => 90000,
                'loss_ratio' => 45.0,
                'commission_rate' => 12.0,
                'profit_commission' => 10800,
                'payment_status' => 'pending_payment',
                'reinsurer' => 'Hannover Re',
            ],
        ];

        // PORTFOLIO TRANSFERS/ACTIVITIES - 10 records
        $portfolioActivities = [
            (object) [
                'id' => 1,
                'reference_number' => 'PT-2024-001',
                'activity_type' => 'valuation',
                'activity_date' => '2024-01-15',
                'description' => 'Initial portfolio valuation',
                'premium_reserve' => 2187500,
                'claims_reserve' => 0,
                'ibnr_reserve' => 125000,
                'total_value' => 2312500,
                'status' => 'completed',
            ],
            (object) [
                'id' => 2,
                'reference_number' => 'PT-2024-002',
                'activity_type' => 'valuation',
                'activity_date' => '2024-04-01',
                'description' => 'Q1 portfolio revaluation',
                'premium_reserve' => 1640625,
                'claims_reserve' => 0,
                'ibnr_reserve' => 93750,
                'total_value' => 1734375,
                'status' => 'completed',
            ],
            (object) [
                'id' => 3,
                'reference_number' => 'PT-2024-003',
                'activity_type' => 'valuation',
                'activity_date' => '2024-07-01',
                'description' => 'Mid-year portfolio valuation',
                'premium_reserve' => 1093750,
                'claims_reserve' => 350000,
                'ibnr_reserve' => 62500,
                'total_value' => 1506250,
                'status' => 'completed',
            ],
            (object) [
                'id' => 4,
                'reference_number' => 'PT-2024-004',
                'activity_type' => 'adjustment',
                'activity_date' => '2024-04-15',
                'description' => 'Portfolio adjustment - Sum insured increase',
                'premium_reserve' => 187500,
                'claims_reserve' => 0,
                'ibnr_reserve' => 10000,
                'total_value' => 197500,
                'status' => 'completed',
            ],
            (object) [
                'id' => 5,
                'reference_number' => 'PT-2024-005',
                'activity_type' => 'analysis',
                'activity_date' => '2024-06-30',
                'description' => 'H1 2024 performance analysis',
                'premium_reserve' => 1250000,
                'claims_reserve' => 150000,
                'ibnr_reserve' => 75000,
                'total_value' => 1475000,
                'status' => 'completed',
            ],
            (object) [
                'id' => 6,
                'reference_number' => 'PT-2024-006',
                'activity_type' => 'valuation',
                'activity_date' => '2024-10-01',
                'description' => 'Q3 portfolio valuation',
                'premium_reserve' => 546875,
                'claims_reserve' => 500000,
                'ibnr_reserve' => 31250,
                'total_value' => 1078125,
                'status' => 'completed',
            ],
            (object) [
                'id' => 7,
                'reference_number' => 'PT-2024-007',
                'activity_type' => 'adjustment',
                'activity_date' => '2024-07-15',
                'description' => 'Portfolio adjustment - Extended coverage',
                'premium_reserve' => 92000,
                'claims_reserve' => 0,
                'ibnr_reserve' => 5000,
                'total_value' => 97000,
                'status' => 'completed',
            ],
            (object) [
                'id' => 8,
                'reference_number' => 'PT-2024-008',
                'activity_type' => 'analysis',
                'activity_date' => '2024-09-30',
                'description' => 'Q3 2024 loss ratio analysis',
                'premium_reserve' => 625000,
                'claims_reserve' => 400000,
                'ibnr_reserve' => 37500,
                'total_value' => 1062500,
                'status' => 'completed',
            ],
            (object) [
                'id' => 9,
                'reference_number' => 'PT-2024-009',
                'activity_type' => 'review',
                'activity_date' => '2024-11-15',
                'description' => 'Year-end portfolio review (Preliminary)',
                'premium_reserve' => 312500,
                'claims_reserve' => 650000,
                'ibnr_reserve' => 18750,
                'total_value' => 981250,
                'status' => 'in_progress',
            ],
            (object) [
                'id' => 10,
                'reference_number' => 'PT-2024-010',
                'activity_type' => 'projection',
                'activity_date' => '2024-12-01',
                'description' => 'FY 2024 final projection',
                'premium_reserve' => 250000,
                'claims_reserve' => 700000,
                'ibnr_reserve' => 15000,
                'total_value' => 965000,
                'status' => 'pending',
            ],
        ];

        // COMMISSION ADJUSTMENTS - 20 records
        $commissionAdjustments = [
            (object) [
                'id' => 1,
                'reference_number' => 'CA-2024-001',
                'adjustment_date' => '2024-01-20',
                'adjustment_type' => 'rate_change',
                'adjustment_basis' => 'increase',
                'description' => 'Commission rate increase for 2024',
                'original_rate' => 9.0,
                'new_rate' => 10.0,
                'original_amount' => 225000,
                'new_amount' => 250000,
                'adjustment_amount' => 25000,
                'status' => 'approved',
                'reinsurer' => 'All Reinsurers',
            ],
            (object) [
                'id' => 2,
                'reference_number' => 'CA-2024-002',
                'adjustment_date' => '2024-02-15',
                'adjustment_type' => 'correction',
                'adjustment_basis' => 'increase',
                'description' => 'Error correction - Swiss Re commission',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 50000,
                'new_amount' => 52500,
                'adjustment_amount' => 2500,
                'status' => 'approved',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 3,
                'reference_number' => 'CA-2024-003',
                'adjustment_date' => '2024-04-10',
                'adjustment_type' => 'additional_premium',
                'adjustment_basis' => 'increase',
                'description' => 'Commission on additional premium',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 0,
                'new_amount' => 13750,
                'adjustment_amount' => 13750,
                'status' => 'approved',
                'reinsurer' => 'All Reinsurers',
            ],
            (object) [
                'id' => 4,
                'reference_number' => 'CA-2024-004',
                'adjustment_date' => '2024-05-25',
                'adjustment_type' => 'return_premium',
                'adjustment_basis' => 'decrease',
                'description' => 'Commission reversal on returned premium',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 265625,
                'new_amount' => 243125,
                'adjustment_amount' => -22500,
                'status' => 'approved',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 5,
                'reference_number' => 'CA-2024-005',
                'adjustment_date' => '2024-06-30',
                'adjustment_type' => 'override',
                'adjustment_basis' => 'increase',
                'description' => 'Volume bonus - H1 2024 performance',
                'original_rate' => 10.0,
                'new_rate' => 12.0,
                'original_amount' => 122500,
                'new_amount' => 147000,
                'adjustment_amount' => 24500,
                'status' => 'approved',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 6,
                'reference_number' => 'CA-2024-006',
                'adjustment_date' => '2024-07-10',
                'adjustment_type' => 'additional_premium',
                'adjustment_basis' => 'increase',
                'description' => 'Commission on extended coverage premium',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 0,
                'new_amount' => 9200,
                'adjustment_amount' => 9200,
                'status' => 'approved',
                'reinsurer' => 'All Reinsurers',
            ],
            (object) [
                'id' => 7,
                'reference_number' => 'CA-2024-007',
                'adjustment_date' => '2024-08-05',
                'adjustment_type' => 'sliding_scale',
                'adjustment_basis' => 'increase',
                'description' => 'Performance-based adjustment - Q2 loss ratio',
                'original_rate' => 10.0,
                'new_rate' => 11.5,
                'original_amount' => 109375,
                'new_amount' => 125781.25,
                'adjustment_amount' => 16406.25,
                'status' => 'approved',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 8,
                'reference_number' => 'CA-2024-008',
                'adjustment_date' => '2024-09-15',
                'adjustment_type' => 'correction',
                'adjustment_basis' => 'decrease',
                'description' => 'Overpayment correction',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 70000,
                'new_amount' => 65625,
                'adjustment_amount' => -4375,
                'status' => 'approved',
                'reinsurer' => 'Hannover Re',
            ],
            (object) [
                'id' => 9,
                'reference_number' => 'CA-2024-009',
                'adjustment_date' => '2024-10-01',
                'adjustment_type' => 'rate_change',
                'adjustment_basis' => 'decrease',
                'description' => 'Rate reduction for Q4 2024',
                'original_rate' => 10.0,
                'new_rate' => 9.5,
                'original_amount' => 306250,
                'new_amount' => 290937.5,
                'adjustment_amount' => -15312.5,
                'status' => 'pending',
                'reinsurer' => 'All Reinsurers',
            ],
            (object) [
                'id' => 10,
                'reference_number' => 'CA-2024-010',
                'adjustment_date' => '2024-03-05',
                'adjustment_type' => 'override',
                'adjustment_basis' => 'increase',
                'description' => 'Special arrangement bonus',
                'original_rate' => 10.0,
                'new_rate' => 12.5,
                'original_amount' => 78750,
                'new_amount' => 98437.5,
                'adjustment_amount' => 19687.5,
                'status' => 'approved',
                'reinsurer' => 'Hannover Re',
            ],
            (object) [
                'id' => 11,
                'reference_number' => 'CA-2024-011',
                'adjustment_date' => '2024-04-20',
                'adjustment_type' => 'contingent',
                'adjustment_basis' => 'increase',
                'description' => 'Q1 performance contingent commission',
                'original_rate' => 10.0,
                'new_rate' => 13.0,
                'original_amount' => 157500,
                'new_amount' => 204750,
                'adjustment_amount' => 47250,
                'status' => 'approved',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 12,
                'reference_number' => 'CA-2024-012',
                'adjustment_date' => '2024-05-15',
                'adjustment_type' => 'additional_premium',
                'adjustment_basis' => 'increase',
                'description' => 'Commission on reinstatement premium',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 0,
                'new_amount' => 6500,
                'adjustment_amount' => 6500,
                'status' => 'approved',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 13,
                'reference_number' => 'CA-2024-013',
                'adjustment_date' => '2024-07-20',
                'adjustment_type' => 'correction',
                'adjustment_basis' => 'increase',
                'description' => 'Calculation error correction',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 40000,
                'new_amount' => 42000,
                'adjustment_amount' => 2000,
                'status' => 'approved',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 14,
                'reference_number' => 'CA-2024-014',
                'adjustment_date' => '2024-08-25',
                'adjustment_type' => 'additional_premium',
                'adjustment_basis' => 'increase',
                'description' => 'Commission on new building addition',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 0,
                'new_amount' => 11250,
                'adjustment_amount' => 11250,
                'status' => 'approved',
                'reinsurer' => 'All Reinsurers',
            ],
            (object) [
                'id' => 15,
                'reference_number' => 'CA-2024-015',
                'adjustment_date' => '2024-09-30',
                'adjustment_type' => 'sliding_scale',
                'adjustment_basis' => 'increase',
                'description' => 'Loss ratio performance adjustment',
                'original_rate' => 10.0,
                'new_rate' => 11.0,
                'original_amount' => 65625,
                'new_amount' => 72187.5,
                'adjustment_amount' => 6562.5,
                'status' => 'approved',
                'reinsurer' => 'Hannover Re',
            ],
            (object) [
                'id' => 16,
                'reference_number' => 'CA-2024-016',
                'adjustment_date' => '2024-10-20',
                'adjustment_type' => 'return_premium',
                'adjustment_basis' => 'decrease',
                'description' => 'Commission reversal - Risk reduction',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 20625,
                'new_amount' => 19125,
                'adjustment_amount' => -1500,
                'status' => 'pending',
                'reinsurer' => 'Hannover Re',
            ],
            (object) [
                'id' => 17,
                'reference_number' => 'CA-2024-017',
                'adjustment_date' => '2024-11-10',
                'adjustment_type' => 'override',
                'adjustment_basis' => 'increase',
                'description' => 'Year-end retention bonus',
                'original_rate' => 10.0,
                'new_rate' => 12.0,
                'original_amount' => 131250,
                'new_amount' => 157500,
                'adjustment_amount' => 26250,
                'status' => 'pending',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 18,
                'reference_number' => 'CA-2024-018',
                'adjustment_date' => '2024-06-15',
                'adjustment_type' => 'additional_premium',
                'adjustment_basis' => 'increase',
                'description' => 'Commission on machinery addition premium',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 0,
                'new_amount' => 12000,
                'adjustment_amount' => 12000,
                'status' => 'approved',
                'reinsurer' => 'Swiss Re Africa',
            ],
            (object) [
                'id' => 19,
                'reference_number' => 'CA-2024-019',
                'adjustment_date' => '2024-09-05',
                'adjustment_type' => 'contingent',
                'adjustment_basis' => 'increase',
                'description' => 'Q3 low loss ratio contingent commission',
                'original_rate' => 10.0,
                'new_rate' => 11.5,
                'original_amount' => 109375,
                'new_amount' => 125781.25,
                'adjustment_amount' => 16406.25,
                'status' => 'approved',
                'reinsurer' => 'Munich Re Kenya',
            ],
            (object) [
                'id' => 20,
                'reference_number' => 'CA-2024-020',
                'adjustment_date' => '2024-11-25',
                'adjustment_type' => 'correction',
                'adjustment_basis' => 'increase',
                'description' => 'Final year adjustment',
                'original_rate' => 10.0,
                'new_rate' => 10.0,
                'original_amount' => 20000,
                'new_amount' => 22000,
                'adjustment_amount' => 2000,
                'status' => 'pending',
                'reinsurer' => 'Hannover Re',
            ],
        ];

        // Calculate totals for summary cards
        $totalDebits = array_sum(array_column($debits, 'net_amount'));
        $totalProfitCommission = array_sum(array_column($profitCommissions, 'profit_commission'));
        $totalCommissionAdjustments = array_sum(array_column($commissionAdjustments, 'adjustment_amount'));
        $currentPortfolioValue = end($portfolioActivities)->total_value;
    @endphp

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Treaty Transaction Details</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="">Cover</a></li>
                    <li class="breadcrumb-item"><a href="">Treaty</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ firstUpper($customer->name) }}</li>
                    <li class="breadcrumb-item"><a href="">Transactions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $cover->cover_no }}</li>
                </ol>
            </nav>
        </div>
    </div>


    <div class="row row-cols-12 mx-0">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 pb-2 px-0 pt-2">
                <h6 class="mb-0 fw-semibold">
                    <i class="ri-information-line me-1" style="vertical-align: -2px;"></i> Summary
                </h6>
            </div>
            <div class="card-body pt-2 px-0">
                <div class="row">
                    <div class="col-md-6">
                        <div class="summary-items">
                            @if ($customer)
                                <div class="summary-item">
                                    <span class="summary-label">Cedant</span>
                                    <span class="summary-value">{{ firstUpper($customer->name) }}</span>
                                </div>
                            @endif

                            <div class="summary-item">
                                <span class="summary-label">Debit No: </span>
                                <span class="summary-value">DN-2024-00001</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="summary-item">
                            <span class="summary-label">Policy Period</span>
                            <span class="summary-value">01/01/2024 - 31/12/2024</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-card">
        <ul class="nav nav-tabs custom-tabs" id="transactionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="debits-tab" data-bs-toggle="tab" data-bs-target="#debits" type="button"
                    role="tab"> Debit Items
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profit-tab" data-bs-toggle="tab" data-bs-target="#profit" type="button"
                    role="tab"> Reinsurers
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="portfolio-tab" data-bs-toggle="tab" data-bs-target="#portfolio" type="button"
                    role="tab">
                    <i class="bi bi-briefcase"></i> Cedant
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="commission-adj-tab" data-bs-toggle="tab" data-bs-target="#commission-adj"
                    type="button" role="tab"> Print Outs
                </button>
            </li>
        </ul>



        <div class="tab-content" id="transactionTabsContent">
            <!-- Debits Tab -->
            <div class="tab-pane fade show active" id="debits" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Items</h6>
                    <button class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Item
                    </button>
                </div>
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>Item No.</th>
                            <th>Treaty</th>
                            <th>Date</th>
                            <th>Class Group</th>
                            <th>Class Name</th>
                            <th>Commission Rate</th>
                            {{-- <th class="text-end">Gross Premium</th> --}}
                            {{-- <th class="text-end">Brokerage</th> --}}
                            <th>Net Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (array_slice($debits, 0, 5) as $debit)
                            <tr>
                                <td>{{ $debit->debit_note_number }}</td>
                                <td>SURPLUS</td>
                                <td>{{ \Carbon\Carbon::parse($debit->debit_date)->format('d/m/Y') }}</td>
                                <td>FIRE</td>
                                <td>FIRE INDUSTRIAL</td>
                                {{-- <td><span class="badge bg-info">{{ ucfirst($debit->debit_type) }}</span></td> --}}
                                {{-- <td class="amount-cell">{{ number_format($debit->gross_premium, 2) }}</td>
                                <td class="amount-cell negative-amount">
                                    {{ number_format($debit->brokerage_amount, 2) }}
                                </td> --}}
                                <td>{{ number_format($debit->net_amount, 2) }}</td>
                                {{-- <td>{{ \Carbon\Carbon::parse($debit->due_date)->format('d/m/Y') }}</td> --}}
                                <td>
                                    <span
                                        class="badge bg-{{ $debit->status == 'paid' ? 'success' : ($debit->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($debit->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-table-action">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Profit Commission Tab -->
            <div class="tab-pane fade" id="profit" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Profit Commission Calculations</h6>
                    <button class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Calculate Profit Commission
                    </button>
                </div>
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Period</th>
                            <th>Reinsurer</th>
                            <th>Method</th>
                            <th class="text-end">Net Premium</th>
                            <th class="text-end">Claims</th>
                            <th class="text-end">Loss Ratio %</th>
                            <th class="text-end">Rate %</th>
                            <th class="text-end">Commission</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (array_slice($profitCommissions, 0, 10) as $pc)
                            <tr>
                                <td>{{ $pc->reference_number }}</td>
                                <td>{{ $pc->period }}</td>
                                <td>{{ $pc->reinsurer }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($pc->calculation_method) }}</span></td>
                                <td class="amount-cell">{{ number_format($pc->net_premium, 2) }}</td>
                                <td class="amount-cell negative-amount">{{ number_format($pc->claims_incurred, 2) }}</td>
                                <td class="amount-cell">{{ number_format($pc->loss_ratio, 2) }}%</td>
                                <td class="amount-cell">{{ number_format($pc->commission_rate, 2) }}%</td>
                                <td class="amount-cell positive-amount">{{ number_format($pc->profit_commission, 2) }}
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $pc->payment_status == 'approved' ? 'success' : ($pc->payment_status == 'pending_payment' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $pc->payment_status)) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-table-action">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Portfolio Tab -->
            <div class="tab-pane fade" id="portfolio" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Portfolio Activities</h6>
                    <button class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> New Portfolio Activity
                    </button>
                </div>
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Date</th>
                            <th>Activity Type</th>
                            <th>Description</th>
                            <th class="text-end">Premium Reserve</th>
                            <th class="text-end">Claims Reserve</th>
                            <th class="text-end">IBNR</th>
                            <th class="text-end">Total Value</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (array_slice($portfolioActivities, 0, 10) as $activity)
                            <tr>
                                <td>{{ $activity->reference_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity->activity_date)->format('d/m/Y') }}</td>
                                <td><span class="badge bg-primary">{{ ucfirst($activity->activity_type) }}</span></td>
                                <td>{{ $activity->description }}</td>
                                <td class="amount-cell">{{ number_format($activity->premium_reserve, 2) }}</td>
                                <td class="amount-cell">{{ number_format($activity->claims_reserve, 2) }}</td>
                                <td class="amount-cell">{{ number_format($activity->ibnr_reserve, 2) }}</td>
                                <td class="amount-cell positive-amount">{{ number_format($activity->total_value, 2) }}
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $activity->status == 'completed' ? 'success' : ($activity->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-table-action">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Commission Adjustments Tab -->
            <div class="tab-pane fade" id="commission-adj" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Commission Adjustments</h6>
                    <button class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Adjustment
                    </button>
                </div>
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reinsurer</th>
                            <th class="text-end">Original Rate %</th>
                            <th class="text-end">New Rate %</th>
                            <th class="text-end">Original Amount</th>
                            <th class="text-end">Adjustment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (array_slice($commissionAdjustments, 0, 10) as $adj)
                            <tr>
                                <td>{{ $adj->reference_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($adj->adjustment_date)->format('d/m/Y') }}</td>
                                <td><span
                                        class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $adj->adjustment_type)) }}</span>
                                </td>
                                <td>{{ $adj->reinsurer }}</td>
                                <td class="amount-cell">{{ number_format($adj->original_rate, 2) }}%</td>
                                <td class="amount-cell">{{ number_format($adj->new_rate, 2) }}%</td>
                                <td class="amount-cell">{{ number_format($adj->original_amount, 2) }}</td>
                                <td
                                    class="amount-cell {{ $adj->adjustment_amount >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                    {{ number_format($adj->adjustment_amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $adj->status == 'approved' ? 'success' : 'warning' }}">
                                        {{ ucfirst($adj->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-table-action">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('cover.modals.treaty-debit')
    {{-- @include('transactions.modals.profit-commission-modal') --}}
    {{-- @include('transactions.modals.portfolio-modal') --}}
    {{-- @include('transactions.modals.commission-adjustment-modal')  --}}

    {{-- @push('scripts')
        <script>
            // Preview Slip
            function previewSlip() {
                window.open("{{ route('transactions.preview-slip', $cover->id ?? '') }}", '_blank');
            }

            // Download Statement
            function downloadStatement(statementId) {
                window.location.href = `/statements/${statementId}/download`;
            }

            // View Debit
            function viewDebit(debitId) {
                window.location.href = `/debits/${debitId}`;
            }

            // View Commission
            function viewCommission(commissionId) {
                window.location.href = `/commissions/${commissionId}`;
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        </script>
    @endpush --}}
@endsection
<style>
    .summary-items {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.5rem 0;
    }

    .summary-item:not(:last-child) {
        border-bottom: 1px solid #f1f3f5;
    }

    .summary-label {
        font-size: 0.8125rem;
        color: #6c757d;
        font-weight: 500;
    }

    .summary-value {
        font-size: 0.875rem;
        color: #212529;
        font-weight: 600;
        text-align: right;
    }
</style>
