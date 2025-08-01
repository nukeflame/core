@section('styles')
    <style>
        :root {
            --primary: #0c5c8d;
            --primary-light: #1a7cb8;
            --secondary: #ededed;
            --text-dark: #333;
            --text-light: #f5f5f5;
            --accent: #ffb74d;
            --danger: #d32f2f;
            --success: #388e3c;
            --warning: #f57c00;
            --info: #0288d1;
        }

        .filter-bar {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .filter-item {
            display: flex;
            align-items: center;
        }

        .filter-item label {
            margin-right: 8px;
            font-size: 14px;
            font-weight: 500;
            width: 100%;
        }

        .filter-item select,
        .filter-item select option {
            width: 100%;
            min-width: 145px;
        }

        .filter-item input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
            width: 100%;
            min-width: 145px;
        }

        .filter-item select:active,
        .filter-item input:active {
            outline: none;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }


        .metric-card {
            background-color: white;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        .metric-title {
            font-size: 14px;
            margin-bottom: 10px;
            color: #0c5c8d;

        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        .metric-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
            font-size: 13px;
        }

        .trend-up {
            color: #2ecc71;
        }

        .trend-down {
            color: #e74c3c;
        }

        .report-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .report-grid th,
        .report-grid td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .report-grid th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--primary);
        }

        .report-grid tr:hover {
            background-color: #f9f9f9;
        }

        .user-menu {
            display: flex;
            align-items: center;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-primary {
            background-color: var(--primary-light);
            color: white;
        }

        .badge-success {
            background-color: var(--success);
            color: white;
        }

        .badge-warning {
            background-color: var(--warning);
            color: white;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            padding: 0;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .toggle-btn:hover {
            text-decoration: underline;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }

        .icon-btn {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .icon-btn:hover {
            background-color: #f0f0f0;
            color: var(--primary);
        }

        .tab-content .tab-pane {
            padding: 1rem;
            border-top: 1px solid inherit;
            border-radius: 0px
        }

        .content-area {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
        }

        .table-toolbar {
            display: flex;
            justify-content: space-between;
            padding: 14px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e0e4e8;
        }

        .table-title {
            font-size: 16px;
            font-weight: 500;
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background-color: #f0f2f5;
        }

        .export-btn {
            background-color: #3498db;
            color: white;
            border: none;
        }

        .export-btn:hover {
            background-color: #2980b9;
        }

        .swal2-select {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
        }

        .budget-selection-form {
            text-align: left;
            padding: 0 15px;
        }

        .budget-selection-form label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }
    </style>
@endsection
