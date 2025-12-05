@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --secondary: #475569;
            --success: #059669;
            --success-light: #10b981;
            --warning: #d97706;
            --warning-light: #f59e0b;
            --danger: #dc2626;
            --info: #0891b2;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --bg-subtle: #f1f5f9;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --radius: 0.5rem;
            --radius-lg: 0.75rem;
        }

        /* Statement Type Themes */
        .theme-quarterly {
            --theme-primary: #1e40af;
            --theme-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }

        .theme-profit {
            --theme-primary: #059669;
            --theme-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }

        .theme-adjustment {
            --theme-primary: #7c3aed;
            --theme-gradient: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
        }

        .theme-portfolio {
            --theme-primary: #ea580c;
            --theme-gradient: linear-gradient(135deg, #ea580c 0%, #fb923c 100%);
        }

        body {
            background-color: var(--bg-page);
        }

        /* Page Header */
        .page-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }

        .page-header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .statement-type-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-lg);
            background: var(--theme-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: var(--shadow-md);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin: 0.25rem 0 0 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .statement-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: var(--theme-gradient);
            color: white;
        }

        .breadcrumb {
            margin: 0;
            padding: 0;
            background: transparent;
            font-size: 0.8125rem;
        }

        .breadcrumb-item a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb-item a:hover {
            color: var(--theme-primary);
        }

        .breadcrumb-item.active {
            color: var(--text-muted);
        }

        /* Action Bar */
        .action-bar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-action:hover {
            border-color: var(--theme-primary);
            color: var(--theme-primary);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-action.primary {
            background: var(--theme-gradient);
            border-color: transparent;
            color: white;
        }

        .btn-action.primary:hover {
            opacity: 0.9;
            color: white;
            box-shadow: var(--shadow-md);
        }

        .action-separator {
            width: 1px;
            height: 24px;
            background: var(--border);
        }

        /* Statement Type Selector */
        .statement-type-selector {
            display: flex;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 0.25rem;
            gap: 0.25rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        .type-option {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            background: transparent;
            text-decoration: none;
        }

        .type-option:hover {
            background: var(--bg-subtle);
            color: var(--text-primary);
        }

        .type-option.active {
            background: var(--theme-gradient);
            color: white;
            box-shadow: var(--shadow);
        }

        .type-option i {
            font-size: 1rem;
        }

        /* Financial Summary Grid */
        .financial-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .financial-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            transition: all 0.2s;
        }

        .financial-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .financial-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--card-accent, var(--theme-gradient));
        }

        .financial-card.highlight {
            background: var(--theme-gradient);
            border-color: transparent;
            color: white;
        }

        .financial-card.highlight::before {
            display: none;
        }

        .financial-card.highlight .financial-label {
            color: rgba(255, 255, 255, 0.8);
        }

        .financial-card.highlight .financial-value {
            color: white;
        }

        .financial-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius);
            background: var(--bg-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
            color: var(--theme-primary);
        }

        .financial-card.highlight .financial-icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .financial-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .financial-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', 'SF Mono', monospace;
        }

        .financial-change {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 0.5rem;
            padding: 0.125rem 0.375rem;
            border-radius: 4px;
        }

        .financial-change.positive {
            background: #dcfce7;
            color: #166534;
        }

        .financial-change.negative {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Info Panel */
        .info-panel {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        .info-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-subtle);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        .info-panel-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .info-panel-body {
            padding: 1.25rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-label {
            font-size: 0.6875rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .info-value.mono {
            font-family: 'JetBrains Mono', monospace;
            color: var(--success);
        }

        /* Main Card */
        .main-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        /* Tabs */
        .nav-tabs-custom {
            display: flex;
            gap: 0.25rem;
            padding: 0.75rem 1rem 0;
            background: var(--bg-subtle);
            border-bottom: 1px solid var(--border);
            overflow-x: auto;
        }

        .nav-tabs-custom .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-secondary);
            border: none;
            border-radius: var(--radius) var(--radius) 0 0;
            background: transparent;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .nav-tabs-custom .nav-link:hover {
            color: var(--theme-primary);
            background: rgba(0, 0, 0, 0.02);
        }

        .nav-tabs-custom .nav-link.active {
            color: var(--theme-primary);
            background: var(--bg-card);
            box-shadow: 0 -2px 0 var(--theme-primary) inset;
        }

        .nav-tabs-custom .nav-link .badge {
            font-size: 0.625rem;
            padding: 0.125rem 0.375rem;
            border-radius: 9999px;
            font-weight: 600;
        }

        .tab-content {
            padding: 1.25rem;
        }

        .tab-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .tab-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        /* Table Styling */
        .table-container {
            overflow-x: auto;
        }

        table.dataTable {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0;
        }

        table.dataTable thead th {
            background: var(--bg-subtle);
            font-size: 0.6875rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        table.dataTable tbody td {
            padding: 0.875rem 1rem;
            font-size: 0.8125rem;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        table.dataTable tbody tr:hover {
            background: var(--bg-subtle);
        }

        table.dataTable tbody tr:last-child td {
            border-bottom: none;
        }

        table.dataTable tfoot td {
            background: var(--bg-subtle);
            font-weight: 600;
            padding: 0.875rem 1rem;
            border-top: 2px solid var(--border);
        }

        .cell-primary {
            font-weight: 600;
            color: var(--text-primary);
        }

        .cell-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8125rem;
        }

        .cell-positive {
            color: var(--success);
        }

        .cell-negative {
            color: var(--danger);
        }

        /* Status & Type Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-status.paid,
        .badge-status.approved,
        .badge-status.active,
        .badge-status.completed {
            background: #dcfce7;
            color: #166534;
        }

        .badge-status.pending,
        .badge-status.calculated,
        .badge-status.in-progress {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-status.overdue,
        .badge-status.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-type {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.6875rem;
            font-weight: 500;
        }

        .badge-type.surplus {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-type.quota-share {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .badge-type.excess-loss {
            background: #fce7f3;
            color: #be185d;
        }

        /* Action Buttons */
        .btn-table-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text-secondary);
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-table-action:hover {
            border-color: var(--theme-primary);
            color: var(--theme-primary);
            background: var(--bg-subtle);
        }

        .action-group {
            display: flex;
            gap: 0.375rem;
        }

        /* DataTables Overrides */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            margin-left: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 3px rgba(var(--theme-primary), 0.1);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.375rem 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            margin: 0 2px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--theme-gradient) !important;
            border-color: transparent !important;
            color: white !important;
        }

        /* Quarter Period Cards - For Quarterly Statement */
        .quarter-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .quarter-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quarter-card:hover {
            border-color: var(--theme-primary);
        }

        .quarter-card.active {
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        .quarter-card.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--theme-gradient);
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .quarter-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quarter-value {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-top: 0.25rem;
            font-family: 'JetBrains Mono', monospace;
        }

        .quarter-status {
            font-size: 0.6875rem;
            margin-top: 0.5rem;
        }

        /* Profit Commission Specific */
        .profit-breakdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.75rem;
            padding: 1rem;
            background: var(--bg-subtle);
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        .breakdown-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px dashed var(--border);
        }

        .breakdown-item:last-child {
            border-bottom: none;
        }

        .breakdown-label {
            font-size: 0.8125rem;
            color: var(--text-secondary);
        }

        .breakdown-value {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Portfolio Transfer Specific */
        .portfolio-direction {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-subtle);
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        .direction-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .direction-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
        }

        .direction-box {
            padding: 0.75rem 1rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-weight: 600;
            color: var(--text-primary);
        }

        .direction-arrow {
            color: var(--theme-primary);
            font-size: 1.5rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--bg-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--text-muted);
            font-size: 1.5rem;
        }

        .empty-state-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .empty-state-text {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .quarter-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .page-header-content {
                flex-direction: column;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-action {
                justify-content: center;
            }

            .action-separator {
                display: none;
            }

            .statement-type-selector {
                flex-direction: column;
            }

            .quarter-grid {
                grid-template-columns: 1fr;
            }

            .financial-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        // Statement Types Configuration
        $statementTypes = [
            'quarterly' => [
                'title' => 'Quarterly Debit Statement',
                'short' => 'Quarterly',
                'icon' => 'ri-calendar-check-line',
                'theme' => 'theme-quarterly',
                'description' => 'Quarterly treaty premium and commission figures',
            ],
            'profit_commission' => [
                'title' => 'Profit Commission Statement',
                'short' => 'Profit Commission',
                'icon' => 'ri-funds-line',
                'theme' => 'theme-profit',
                'description' => 'Profit commission calculation and distribution',
            ],
            'adjustment' => [
                'title' => 'Commission Adjustment Statement',
                'short' => 'Adjustment',
                'icon' => 'ri-exchange-funds-line',
                'theme' => 'theme-adjustment',
                'description' => 'Commission rate adjustments and corrections',
            ],
            'portfolio' => [
                'title' => 'Portfolio Transfer Statement',
                'short' => 'Portfolio',
                'icon' => 'ri-arrow-left-right-line',
                'theme' => 'theme-portfolio',
                'description' => 'Portfolio entry/withdrawal premium transfers',
            ],
        ];

        // Current statement type from route/controller
        $currentType = $statementType ?? 'quarterly';
        $typeConfig = $statementTypes[$currentType];

        // Cover and customer data (from controller)
        $cover =
            $cover ??
            (object) [
                'id' => 1,
                'cover_no' => 'TRY-2024-001',
                'policy_number' => 'POL-2024-001',
                'treaty_name' => 'Property Surplus Treaty 2024',
                'treaty_type' => 'Surplus',
                'class_of_business' => 'Property All Risks',
                'underwriting_year' => '2024',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'sum_insured' => 500000000,
                'premium' => 25000000,
                'ceded_premium' => 17500000,
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
                'code' => 'HIC',
            ];

        // Financial totals based on statement type
        $financialData = match ($currentType) {
            'quarterly' => [
                'gross_premium' => 4125000,
                'commission' => 441562.5,
                'net_premium' => 3683437.5,
                'claims' => 825000,
                'items_count' => 8,
            ],
            'profit_commission' => [
                'earned_premium' => 15750000,
                'incurred_claims' => 4725000,
                'management_expense' => 1575000,
                'reserve_adjustment' => 787500,
                'profit' => 8662500,
                'commission_rate' => 25.0,
                'commission_payable' => 2165625,
            ],
            'adjustment' => [
                'original_commission' => 1750000,
                'adjustment_amount' => 175000,
                'new_commission' => 1925000,
                'variance_percentage' => 10.0,
                'adjustments_count' => 5,
            ],
            'portfolio' => [
                'entry_premium' => 8750000,
                'withdrawal_premium' => 7350000,
                'net_transfer' => 1400000,
                'entry_reserve' => 2625000,
                'withdrawal_reserve' => 2205000,
            ],
            default => [],
        };

        // Quarter data for quarterly statements
        $quarters = collect([
            ['quarter' => 'Q1', 'period' => 'Jan - Mar 2024', 'premium' => 4125000, 'status' => 'completed'],
            ['quarter' => 'Q2', 'period' => 'Apr - Jun 2024', 'premium' => 4875000, 'status' => 'completed'],
            ['quarter' => 'Q3', 'period' => 'Jul - Sep 2024', 'premium' => 5250000, 'status' => 'pending'],
            ['quarter' => 'Q4', 'period' => 'Oct - Dec 2024', 'premium' => 0, 'status' => 'upcoming'],
        ]);

        $selectedQuarter = $selectedQuarter ?? 'Q1';

        // Debit items (common across types with type-specific data)
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

        // Reinsurers
        $reinsurers =
            $reinsurers ??
            collect([
                (object) [
                    'id' => 1,
                    'name' => 'Swiss Re Africa',
                    'share_percentage' => 30.0,
                    'share_premium' => 5250000,
                    'commission_rate' => 10.0,
                    'status' => 'active',
                    'contact_person' => 'James Ochieng',
                    'email' => 'james.ochieng@swissre.com',
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Munich Re Kenya',
                    'share_percentage' => 25.0,
                    'share_premium' => 4375000,
                    'commission_rate' => 10.0,
                    'status' => 'active',
                    'contact_person' => 'Sarah Wanjiku',
                    'email' => 'sarah.wanjiku@munichre.com',
                ],
                (object) [
                    'id' => 3,
                    'name' => 'Hannover Re',
                    'share_percentage' => 15.0,
                    'share_premium' => 2625000,
                    'commission_rate' => 12.5,
                    'status' => 'active',
                    'contact_person' => 'Peter Mwangi',
                    'email' => 'peter.mwangi@hannover-re.com',
                ],
                (object) [
                    'id' => 4,
                    'name' => 'Africa Re',
                    'share_percentage' => 20.0,
                    'share_premium' => 3500000,
                    'commission_rate' => 10.0,
                    'status' => 'active',
                    'contact_person' => 'Grace Kimani',
                    'email' => 'grace.kimani@africa-re.com',
                ],
                (object) [
                    'id' => 5,
                    'name' => 'Kenya Re',
                    'share_percentage' => 10.0,
                    'share_premium' => 1750000,
                    'commission_rate' => 15.0,
                    'status' => 'active',
                    'contact_person' => 'John Kamau',
                    'email' => 'john.kamau@kenyare.co.ke',
                ],
            ]);

        // Documents
        $documents =
            $documents ??
            collect([
                (object) [
                    'id' => 1,
                    'document_type' => 'Debit Note',
                    'reference' => 'DN-2024-Q1-001',
                    'description' => 'Q1 2024 Treaty Premium Debit',
                    'generated_date' => '2024-04-01',
                    'generated_by' => 'System',
                    'status' => 'sent',
                ],
                (object) [
                    'id' => 2,
                    'document_type' => 'Credit Note',
                    'reference' => 'CN-2024-Q1-001',
                    'description' => 'Q1 2024 Commission Credit',
                    'generated_date' => '2024-04-01',
                    'generated_by' => 'System',
                    'status' => 'sent',
                ],
                (object) [
                    'id' => 3,
                    'document_type' => 'Statement',
                    'reference' => 'SOA-2024-Q1',
                    'description' => 'Q1 2024 Statement of Account',
                    'generated_date' => '2024-04-05',
                    'generated_by' => 'Peter Kamau',
                    'status' => 'generated',
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
            ]);

        // Calculate totals
        $totalGrossPremium = $debitItems->sum('gross_premium');
        $totalCommission = $debitItems->sum('commission_amount');
        $totalNetAmount = $debitItems->sum('net_amount');
    @endphp

    <div class="{{ $typeConfig['theme'] }}">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container-fluidd">
                <div class="page-header-content">
                    <div class="page-title-section">
                        <div class="statement-type-icon">
                            <i class="{{ $typeConfig['icon'] }}"></i>
                        </div>
                        <div>
                            <h1 class="page-title">{{ $typeConfig['title'] }}</h1>
                            <p class="page-subtitle">
                                <span>{{ $cover->cover_no }}</span>
                                <span>•</span>
                                <span>{{ $cover->treaty_name }}</span>
                                <span class="statement-badge">
                                    <i class="{{ $typeConfig['icon'] }}"></i>
                                    {{ $typeConfig['short'] }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') ?? '#' }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Treaties</a></li>
                            <li class="breadcrumb-item"><a href="#">{{ $cover->cover_no }}</a></li>
                            <li class="breadcrumb-item active">{{ $typeConfig['short'] }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <!-- Statement Type Selector -->
            <div class="statement-type-selector">
                @foreach ($statementTypes as $type => $config)
                    <a href="#" class="type-option {{ $currentType === $type ? 'active' : '' }}"
                        data-type="{{ $type }}">
                        <i class="{{ $config['icon'] }}"></i>
                        {{-- {{ route('covers.statement', ['cover' => $cover->id, 'type' => $type]) ?? '#' }} --}}
                        <span>{{ $config['short'] }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <button class="btn-action primary" data-action="preview-statement">
                    <i class="ri-file-text-line"></i>
                    <span>Preview Statement</span>
                </button>
                <button class="btn-action" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="ri-add-line"></i>
                    <span>Add Item</span>
                </button>
                <div class="action-separator"></div>
                <button class="btn-action" data-action="generate-pdf">
                    <i class="ri-file-pdf-line"></i>
                    <span>Generate PDF</span>
                </button>
                <button class="btn-action" data-action="export-excel">
                    <i class="ri-file-excel-line"></i>
                    <span>Export Excel</span>
                </button>
                <button class="btn-action" data-action="send-email">
                    <i class="ri-mail-send-line"></i>
                    <span>Send to Reinsurers</span>
                </button>
            </div>

            <!-- Quarter Selector (Only for Quarterly) -->
            @if ($currentType === 'quarterly')
                <div class="quarter-grid">
                    @foreach ($quarters as $q)
                        <div class="quarter-card {{ $selectedQuarter === $q['quarter'] ? 'active' : '' }}"
                            data-quarter="{{ $q['quarter'] }}">
                            <div class="quarter-label">{{ $q['quarter'] }} • {{ $q['period'] }}</div>
                            <div class="quarter-value">{{ $cover->currency }} {{ number_format($q['premium'], 0) }}</div>
                            <div class="quarter-status">
                                <span class="badge-status {{ $q['status'] }}">{{ ucfirst($q['status']) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Financial Summary -->
            <div class="financial-summary">
                @if ($currentType === 'quarterly')
                    <div class="financial-card">
                        <div class="financial-icon"><i class="ri-money-dollar-circle-line"></i></div>
                        <div class="financial-label">Gross Premium</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['gross_premium'], 2) }}</div>
                    </div>
                    <div class="financial-card" style="--card-accent: linear-gradient(135deg, #059669, #10b981);">
                        <div class="financial-icon"><i class="ri-percent-line"></i></div>
                        <div class="financial-label">Commission</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['commission'], 2) }}</div>
                    </div>
                    <div class="financial-card highlight">
                        <div class="financial-icon"><i class="ri-hand-coin-line"></i></div>
                        <div class="financial-label">Net Premium Due</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['net_premium'], 2) }}</div>
                    </div>
                    <div class="financial-card" style="--card-accent: linear-gradient(135deg, #dc2626, #f87171);">
                        <div class="financial-icon"><i class="ri-shield-cross-line"></i></div>
                        <div class="financial-label">Claims</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['claims'], 2) }}</div>
                    </div>
                @elseif($currentType === 'profit_commission')
                    <div class="financial-card">
                        <div class="financial-icon"><i class="ri-funds-box-line"></i></div>
                        <div class="financial-label">Earned Premium</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['earned_premium'], 2) }}</div>
                    </div>
                    <div class="financial-card" style="--card-accent: linear-gradient(135deg, #dc2626, #f87171);">
                        <div class="financial-icon"><i class="ri-shield-cross-line"></i></div>
                        <div class="financial-label">Incurred Claims</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['incurred_claims'], 2) }}</div>
                    </div>
                    <div class="financial-card" style="--card-accent: linear-gradient(135deg, #7c3aed, #a78bfa);">
                        <div class="financial-icon"><i class="ri-line-chart-line"></i></div>
                        <div class="financial-label">Net Profit</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['profit'], 2) }}</div>
                    </div>
                    <div class="financial-card highlight">
                        <div class="financial-icon"><i class="ri-gift-line"></i></div>
                        <div class="financial-label">Profit Commission ({{ $financialData['commission_rate'] }}%)</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['commission_payable'], 2) }}</div>
                    </div>
                @elseif($currentType === 'adjustment')
                    <div class="financial-card">
                        <div class="financial-icon"><i class="ri-history-line"></i></div>
                        <div class="financial-label">Original Commission</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['original_commission'], 2) }}</div>
                    </div>
                    <div class="financial-card" style="--card-accent: linear-gradient(135deg, #7c3aed, #a78bfa);">
                        <div class="financial-icon"><i class="ri-add-circle-line"></i></div>
                        <div class="financial-label">Adjustment (+{{ $financialData['variance_percentage'] }}%)</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['adjustment_amount'], 2) }}</div>
                    </div>
                    <div class="financial-card highlight">
                        <div class="financial-icon"><i class="ri-checkbox-circle-line"></i></div>
                        <div class="financial-label">Revised Commission</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['new_commission'], 2) }}</div>
                    </div>
                    <div class="financial-card">
                        <div class="financial-icon"><i class="ri-file-list-3-line"></i></div>
                        <div class="financial-label">Adjustments Count</div>
                        <div class="financial-value">{{ $financialData['adjustments_count'] }}</div>
                    </div>
                @elseif($currentType === 'portfolio')
                    <div class="financial-card" style="--card-accent: linear-gradient(135deg, #059669, #10b981);">
                        <div class="financial-icon"><i class="ri-login-box-line"></i></div>
                        <div class="financial-label">Portfolio Entry</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['entry_premium'], 2) }}</div>
                    </div>
                    <div class="financial-card" style="--card-accent: linear-gradient(135deg, #dc2626, #f87171);">
                        <div class="financial-icon"><i class="ri-logout-box-line"></i></div>
                        <div class="financial-label">Portfolio Withdrawal</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['withdrawal_premium'], 2) }}</div>
                    </div>
                    <div class="financial-card highlight">
                        <div class="financial-icon"><i class="ri-arrow-left-right-line"></i></div>
                        <div class="financial-label">Net Transfer</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['net_transfer'], 2) }}</div>
                    </div>
                    <div class="financial-card">
                        <div class="financial-icon"><i class="ri-safe-2-line"></i></div>
                        <div class="financial-label">Reserve Movement</div>
                        <div class="financial-value">{{ $cover->currency }}
                            {{ number_format($financialData['entry_reserve'] - $financialData['withdrawal_reserve'], 2) }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Profit Commission Breakdown (Only for Profit Commission) -->
            @if ($currentType === 'profit_commission')
                <div class="info-panel">
                    <div class="info-panel-header">
                        <h6 class="info-panel-title">
                            <i class="ri-calculator-line"></i>
                            Profit Commission Calculation
                        </h6>
                    </div>
                    <div class="info-panel-body">
                        <div class="profit-breakdown">
                            <div class="breakdown-item">
                                <span class="breakdown-label">Earned Premium</span>
                                <span class="breakdown-value">{{ $cover->currency }}
                                    {{ number_format($financialData['earned_premium'], 2) }}</span>
                            </div>
                            <div class="breakdown-item">
                                <span class="breakdown-label">Less: Incurred Claims</span>
                                <span
                                    class="breakdown-value cell-negative">({{ number_format($financialData['incurred_claims'], 2) }})</span>
                            </div>
                            <div class="breakdown-item">
                                <span class="breakdown-label">Less: Management Expense</span>
                                <span
                                    class="breakdown-value cell-negative">({{ number_format($financialData['management_expense'], 2) }})</span>
                            </div>
                            <div class="breakdown-item">
                                <span class="breakdown-label">Less: Reserve Adjustment</span>
                                <span
                                    class="breakdown-value cell-negative">({{ number_format($financialData['reserve_adjustment'], 2) }})</span>
                            </div>
                            <div class="breakdown-item"
                                style="border-top: 2px solid var(--border); padding-top: 0.75rem;">
                                <span class="breakdown-label"><strong>Net Profit</strong></span>
                                <span
                                    class="breakdown-value cell-positive"><strong>{{ number_format($financialData['profit'], 2) }}</strong></span>
                            </div>
                            <div class="breakdown-item">
                                <span class="breakdown-label">Profit Commission @
                                    {{ $financialData['commission_rate'] }}%</span>
                                <span class="breakdown-value cell-positive"><strong>{{ $cover->currency }}
                                        {{ number_format($financialData['commission_payable'], 2) }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Portfolio Direction (Only for Portfolio) -->
            @if ($currentType === 'portfolio')
                <div class="info-panel">
                    <div class="info-panel-header">
                        <h6 class="info-panel-title">
                            <i class="ri-arrow-left-right-line"></i>
                            Portfolio Transfer Direction
                        </h6>
                    </div>
                    <div class="info-panel-body">
                        <div class="portfolio-direction">
                            <div class="direction-indicator">
                                <div class="direction-box">
                                    <div class="direction-label">From Treaty</div>
                                    <div>Property Surplus 2023</div>
                                </div>
                                <i class="ri-arrow-right-line direction-arrow"></i>
                                <div class="direction-box">
                                    <div class="direction-label">To Treaty</div>
                                    <div>{{ $cover->treaty_name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Treaty Summary Panel -->
            <div class="info-panel">
                <div class="info-panel-header">
                    <h6 class="info-panel-title">
                        <i class="ri-file-shield-line"></i>
                        Treaty Summary
                    </h6>
                    <span class="badge-status {{ $cover->status }}">{{ ucfirst($cover->status) }}</span>
                </div>
                <div class="info-panel-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Cedant</span>
                            <span class="info-value">{{ $customer->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Treaty Type</span>
                            <span class="info-value">{{ $cover->treaty_type }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Class of Business</span>
                            <span class="info-value">{{ $cover->class_of_business }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Underwriting Year</span>
                            <span class="info-value">{{ $cover->underwriting_year }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Treaty Period</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($cover->start_date)->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($cover->end_date)->format('d M Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Retention / Cession</span>
                            <span class="info-value">{{ number_format($cover->retention_percentage, 0) }}% /
                                {{ number_format($cover->ceded_percentage, 0) }}%</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Sum Insured</span>
                            <span class="info-value mono">{{ $cover->currency }}
                                {{ number_format($cover->sum_insured, 2) }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ceded Premium</span>
                            <span class="info-value mono">{{ $cover->currency }}
                                {{ number_format($cover->ceded_premium, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="main-card">
                <ul class="nav nav-tabs-custom" id="statementTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items"
                            type="button" role="tab">
                            <i class="ri-list-check-2"></i>
                            <span>{{ $currentType === 'adjustment' ? 'Adjustments' : ($currentType === 'portfolio' ? 'Transfers' : 'Items') }}</span>
                            <span class="badge bg-primary">{{ $debitItems->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reinsurers-tab" data-bs-toggle="tab" data-bs-target="#reinsurers"
                            type="button" role="tab">
                            <i class="ri-building-2-line"></i>
                            <span>Reinsurers</span>
                            <span class="badge bg-secondary">{{ $reinsurers->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents"
                            type="button" role="tab">
                            <i class="ri-file-list-3-line"></i>
                            <span>Documents</span>
                            <span class="badge bg-secondary">{{ $documents->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history"
                            type="button" role="tab">
                            <i class="ri-history-line"></i>
                            <span>History</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="statementTabsContent">
                    <!-- Items Tab -->
                    <div class="tab-pane fade show active" id="items" role="tabpanel">
                        <div class="tab-header">
                            <h6 class="tab-title">
                                <i class="ri-list-unordered"></i>
                                @switch($currentType)
                                    @case('quarterly')
                                        {{ $selectedQuarter }} 2024 Debit Items
                                    @break

                                    @case('profit_commission')
                                        Profit Commission Line Items
                                    @break

                                    @case('adjustment')
                                        Commission Adjustment Items
                                    @break

                                    @case('portfolio')
                                        Portfolio Transfer Items
                                    @break
                                @endswitch
                            </h6>
                            <button class="btn-action" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                <i class="ri-add-line"></i>
                                <span>Add Item</span>
                            </button>
                        </div>

                        <div class="table-container">
                            <table class="table" id="itemsTable" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Item No.</th>
                                        <th>Treaty</th>
                                        <th>Date</th>
                                        <th>Class</th>
                                        <th>Reinsurer</th>
                                        <th class="text-center">Comm. %</th>
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
                                            <td class="cell-primary">{{ $item->item_number }}</td>
                                            <td>
                                                <span
                                                    class="badge-type {{ strtolower(str_replace(' ', '-', $item->treaty_type)) }}">
                                                    {{ $item->treaty_type }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->item_date)->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="cell-primary">{{ $item->class_group }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                                    {{ $item->class_name }}</div>
                                            </td>
                                            <td>{{ $item->reinsurer }}</td>
                                            <td class="text-center">{{ number_format($item->commission_rate, 1) }}%</td>
                                            <td class="text-end cell-mono">{{ number_format($item->gross_premium, 2) }}
                                            </td>
                                            <td class="text-end cell-mono cell-negative">
                                                {{ number_format($item->commission_amount, 2) }}</td>
                                            <td class="text-end cell-mono cell-positive">
                                                {{ number_format($item->net_amount, 2) }}</td>
                                            <td><span
                                                    class="badge-status {{ $item->status }}">{{ ucfirst($item->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="action-group">
                                                    <button class="btn-table-action" title="View"
                                                        data-action="view-item" data-id="{{ $item->id }}">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    <button class="btn-table-action" title="Edit"
                                                        data-action="edit-item" data-id="{{ $item->id }}">
                                                        <i class="ri-edit-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end"><strong>Totals:</strong></td>
                                        <td class="text-end cell-mono">
                                            <strong>{{ number_format($totalGrossPremium, 2) }}</strong>
                                        </td>
                                        <td class="text-end cell-mono cell-negative">
                                            <strong>{{ number_format($totalCommission, 2) }}</strong>
                                        </td>
                                        <td class="text-end cell-mono cell-positive">
                                            <strong>{{ number_format($totalNetAmount, 2) }}</strong>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Reinsurers Tab -->
                    <div class="tab-pane fade" id="reinsurers" role="tabpanel">
                        <div class="tab-header">
                            <h6 class="tab-title">
                                <i class="ri-building-2-line"></i>
                                Participating Reinsurers
                            </h6>
                            <button class="btn-action" data-bs-toggle="modal" data-bs-target="#addReinsurerModal">
                                <i class="ri-add-line"></i>
                                <span>Add Reinsurer</span>
                            </button>
                        </div>

                        <div class="table-container">
                            <table class="table" id="reinsurersTable" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Reinsurer</th>
                                        <th>Contact</th>
                                        <th class="text-center">Share %</th>
                                        <th class="text-center">Comm. %</th>
                                        <th class="text-end">Share Premium</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reinsurers as $reinsurer)
                                        <tr>
                                            <td class="cell-primary">{{ $reinsurer->name }}</td>
                                            <td>
                                                <div>{{ $reinsurer->contact_person }}</div>
                                                <div style="font-size: 0.75rem;">
                                                    <a href="mailto:{{ $reinsurer->email }}"
                                                        style="color: var(--primary);">{{ $reinsurer->email }}</a>
                                                </div>
                                            </td>
                                            <td class="text-center cell-mono">
                                                {{ number_format($reinsurer->share_percentage, 1) }}%</td>
                                            <td class="text-center cell-mono">
                                                {{ number_format($reinsurer->commission_rate, 1) }}%</td>
                                            <td class="text-end cell-mono cell-positive">
                                                {{ number_format($reinsurer->share_premium, 2) }}</td>
                                            <td><span
                                                    class="badge-status {{ $reinsurer->status }}">{{ ucfirst($reinsurer->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="action-group">
                                                    <button class="btn-table-action" title="View"
                                                        data-action="view-reinsurer" data-id="{{ $reinsurer->id }}">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    <button class="btn-table-action" title="Send Statement"
                                                        data-action="send-statement" data-id="{{ $reinsurer->id }}">
                                                        <i class="ri-mail-send-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Totals:</strong></td>
                                        <td class="text-center cell-mono">
                                            <strong>{{ number_format($reinsurers->sum('share_percentage'), 1) }}%</strong>
                                        </td>
                                        <td></td>
                                        <td class="text-end cell-mono cell-positive">
                                            <strong>{{ number_format($reinsurers->sum('share_premium'), 2) }}</strong>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div class="tab-pane fade" id="documents" role="tabpanel">
                        <div class="tab-header">
                            <h6 class="tab-title">
                                <i class="ri-file-list-3-line"></i>
                                Generated Documents
                            </h6>
                            <div class="dropdown">
                                <button class="btn-action dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="ri-add-line"></i>
                                    <span>Generate</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" data-generate="debit-note"><i
                                                class="ri-file-text-line me-2"></i>Debit Note</a></li>
                                    <li><a class="dropdown-item" href="#" data-generate="credit-note"><i
                                                class="ri-file-text-line me-2"></i>Credit Note</a></li>
                                    <li><a class="dropdown-item" href="#" data-generate="statement"><i
                                                class="ri-file-list-3-line me-2"></i>Statement of Account</a></li>
                                    <li><a class="dropdown-item" href="#" data-generate="bordereau"><i
                                                class="ri-table-line me-2"></i>Bordereau</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#" data-generate="closing-slip"><i
                                                class="ri-file-shield-line me-2"></i>Closing Slip</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="table" id="documentsTable" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Generated</th>
                                        <th>By</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $doc)
                                        <tr>
                                            <td class="cell-primary">{{ $doc->reference }}</td>
                                            <td><span class="badge-type surplus">{{ $doc->document_type }}</span></td>
                                            <td>{{ $doc->description }}</td>
                                            <td>{{ \Carbon\Carbon::parse($doc->generated_date)->format('d/m/Y') }}</td>
                                            <td>{{ $doc->generated_by }}</td>
                                            <td><span
                                                    class="badge-status {{ $doc->status }}">{{ ucfirst($doc->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="action-group">
                                                    <button class="btn-table-action" title="View"
                                                        data-action="view-doc" data-id="{{ $doc->id }}">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    <button class="btn-table-action" title="Download"
                                                        data-action="download-doc" data-id="{{ $doc->id }}">
                                                        <i class="ri-download-line"></i>
                                                    </button>
                                                    <button class="btn-table-action" title="Email"
                                                        data-action="email-doc" data-id="{{ $doc->id }}">
                                                        <i class="ri-mail-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="ri-history-line"></i>
                            </div>
                            <div class="empty-state-title">Statement History</div>
                            <div class="empty-state-text">View all changes and audit trail for this statement.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-add-circle-line me-2"></i>
                        Add
                        {{ $currentType === 'adjustment' ? 'Adjustment' : ($currentType === 'portfolio' ? 'Transfer' : 'Debit Item') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addItemForm">
                        @csrf
                        <input type="hidden" name="statement_type" value="{{ $currentType }}">
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
                                <label class="form-label">Date <span class="text-danger">*</span></label>
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
                            <div class="col-md-4">
                                <label class="form-label">Commission Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="commission_rate" step="0.01"
                                    min="0" max="100" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Commission Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $cover->currency }}</span>
                                    <input type="number" class="form-control" name="commission_amount" step="0.01"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                    <button type="button" class="btn btn-primary" id="saveItemBtn">
                        <i class="ri-save-line me-1"></i>Save Item
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        (function() {
            'use strict';

            const config = {
                coverId: @json($cover->id ?? 1),
                currency: @json($cover->currency ?? 'KES'),
                statementType: @json($currentType),
                csrfToken: @json(csrf_token()),
            };

            // Initialize DataTables
            function initTables() {
                const commonConfig = {
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        paginate: {
                            next: '<i class="ri-arrow-right-s-line"></i>',
                            previous: '<i class="ri-arrow-left-s-line"></i>'
                        }
                    }
                };

                if ($.fn.DataTable.isDataTable('#itemsTable')) {
                    $('#itemsTable').DataTable().destroy();
                }
                $('#itemsTable').DataTable({
                    ...commonConfig,
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

                if ($.fn.DataTable.isDataTable('#reinsurersTable')) {
                    $('#reinsurersTable').DataTable().destroy();
                }
                $('#reinsurersTable').DataTable({
                    ...commonConfig,
                    order: [
                        [2, 'desc']
                    ],
                    columnDefs: [{
                            targets: [4],
                            className: 'text-end'
                        },
                        {
                            targets: [-1],
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                if ($.fn.DataTable.isDataTable('#documentsTable')) {
                    $('#documentsTable').DataTable().destroy();
                }
                $('#documentsTable').DataTable({
                    ...commonConfig,
                    order: [
                        [3, 'desc']
                    ],
                    columnDefs: [{
                        targets: [-1],
                        orderable: false,
                        searchable: false
                    }]
                });
            }

            // Form calculations
            function initFormCalculations() {
                const form = document.getElementById('addItemForm');
                if (!form) return;

                const grossInput = form.querySelector('[name="gross_premium"]');
                const rateInput = form.querySelector('[name="commission_rate"]');
                const commInput = form.querySelector('[name="commission_amount"]');
                const netInput = form.querySelector('[name="net_amount"]');

                function calculate() {
                    const gross = parseFloat(grossInput.value) || 0;
                    const rate = parseFloat(rateInput.value) || 0;
                    const commission = (gross * rate) / 100;
                    commInput.value = commission.toFixed(2);
                    netInput.value = (gross - commission).toFixed(2);
                }

                grossInput?.addEventListener('input', calculate);
                rateInput?.addEventListener('input', calculate);
            }

            // Event delegation
            function initEventHandlers() {
                document.addEventListener('click', function(e) {
                    const target = e.target.closest('[data-action]');
                    if (!target) return;

                    const action = target.dataset.action;
                    const id = target.dataset.id;

                    switch (action) {
                        case 'preview-statement':
                            window.open(
                                `/covers/${config.coverId}/preview-statement?type=${config.statementType}`,
                                '_blank');
                            break;
                        case 'generate-pdf':
                            window.location.href =
                                `/covers/${config.coverId}/generate-pdf?type=${config.statementType}`;
                            break;
                        case 'export-excel':
                            window.location.href =
                                `/covers/${config.coverId}/export-excel?type=${config.statementType}`;
                            break;
                        case 'view-item':
                        case 'edit-item':
                            console.log(`${action}: ${id}`);
                            break;
                    }
                });

                // Quarter selection
                document.querySelectorAll('.quarter-card').forEach(card => {
                    card.addEventListener('click', function() {
                        const quarter = this.dataset.quarter;
                        window.location.href = `?type=${config.statementType}&quarter=${quarter}`;
                    });
                });

                // Save item
                document.getElementById('saveItemBtn')?.addEventListener('click', function() {
                    const form = document.getElementById('addItemForm');
                    if (!form.checkValidity()) {
                        form.classList.add('was-validated');
                        return;
                    }
                    // Submit logic here
                    console.log('Saving item...');
                });
            }

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                initTables();
                initFormCalculations();
                initEventHandlers();

                // Initialize tooltips
                document.querySelectorAll('[title]').forEach(el => {
                    new bootstrap.Tooltip(el);
                });
            });
        })();
    </script>
@endsection
