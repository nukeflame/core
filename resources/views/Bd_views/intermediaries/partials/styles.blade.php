<style>
    :root {
        --fac-primary-color: #2c3e50;
        --secondary-color: #3498db;
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --info-color: #3498db;
        --light-bg: #f8f9fa;
        --border-color: #dee2e6;
    }

    .page-header {
        background: linear-gradient(135deg, var(--fac-primary-color), var(--secondary-color));
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .page-title {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .kpi-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid var(--secondary-color);
        transition: transform 0.3s ease;
        margin-bottom: 1rem;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
    }

    .kpi-value {
        font-size: 20px;
        font-weight: bold;
        color: var(--fac-primary-color);
        margin-bottom: 0.5rem;
    }

    .kpi-label {
        color: #6c757d;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kpi-trend {
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .trend-up {
        color: var(--success-color);
    }

    .trend-down {
        color: var(--danger-color);
    }


    .pipeline-table-container {
        overflow: hidden;
    }

    .table-header {
        padding: 0px;
    }

    .table-title {
        font-size: 1.4rem;
        font-weight: 600;
        margin: 0;
        flex: 1;
    }

    .table-controls {
        padding: 0px;
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-direction: row;
        width: 100%;
        margin-bottom: 1rem;
    }

    .search-input {
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        min-width: 200px;
    }

    .filter-select {
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        min-width: 150px;
    }

    /* Priority and Status Indicators */
    .priority-badge {
        padding: 3px 15px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .priority-critical {
        background: #fee2e2;
        color: #991b1b;
    }

    .priority-high {
        background: #fef3c7;
        color: #92400e;
    }

    .priority-medium {
        background: #dbeafe;
        color: #1e40af;
    }

    .priority-low {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-inquiry {
        background: #f3f4f6;
        color: #374151;
    }

    .status-quoted {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-negotiation {
        background: #fef3c7;
        color: #92400e;
    }

    .status-bound {
        background: #d1fae5;
        color: #065f46;
    }

    .status-declined {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Enhanced Legend */
    .urgency-legend {
        background: #fff;
        border-radius: 8px;
        padding: 1rem;
        width: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .legend-title {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: var(--fac-primary-color);
    }

    .legend-items {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        font-family: inherit;
        font-size: 14px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .color-indicator {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* Table Row Highlighting */
    .table tbody tr.highlight-critical {
        background-color: #fef2f2 !important;
        border-left: 4px solid #ef4444;
    }

    .table tbody tr.highlight-urgent {
        background-color: #fffbeb !important;
        border-left: 4px solid #f59e0b;
    }

    .table tbody tr.highlight-upcoming {
        background-color: #eff6ff !important;
        border-left: 4px solid #3b82f6;
    }

    .table tbody tr.highlight-normal {
        background-color: #f0fdf4 !important;
        border-left: 4px solid #10b981;
    }

    /* Action Buttons */
    .action-btn-group {
        display: flex;
        gap: 0.25rem;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 0.8rem;
    }

    .btn-view {
        background: #e0f2fe;
        color: #0277bd;
    }

    .btn-edit {
        background: #f3e8ff;
        color: #7c3aed;
    }

    .btn-pipeline {
        background: #ecfdf5;
        color: #059669;
    }

    .btn-docs {
        background: #fef3c7;
        color: #d97706;
    }

    .action-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Currency and Premium Formatting */
    .currency {
        font-weight: 600;
        color: var(--success-color);
    }

    .percentage {
        font-weight: 500;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-controls {
            flex-direction: column;
            width: 100%;
        }

        .search-input,
        .filter-select {
            min-width: 100%;
        }

        .kpi-value {
            font-size: 2rem;
        }
    }

    /*
    table.dataTable tbody th,
    table.dataTable thead th,
    table.dataTable tbody td,
    table.dataTable tfoot th,
    table.dataTable tfoot td {
        overflow: inherit;
        text-overflow: inherit;
        white-space: inherit;
    } */

    /*  */

    /* Specific border colors for different column types */
    .border-left-primary {
        border-left: 3px solid #0d6efd !important;
        font-weight: 600;
    }

    .border-left-priority {
        border-left: 2px solid #ffc107 !important;
    }

    .border-left-status {
        border-left: 2px solid #198754 !important;
    }

    .border-left-warning {
        border-left: 2px solid #dc3545 !important;
    }

    .border-left-actions {
        border-left: 2px solid #6c757d !important;
    }

    .border-left-light {
        border-left: 1px solid #dee2e6 !important;
    }

    /* Priority row styling */
    .row-priority-critical {
        background-color: #fff5f5 !important;
        border-left: 4px solid #dc3545 !important;
    }

    .row-priority-high {
        background-color: #fffbf0 !important;
        border-left: 4px solid #fd7e14 !important;
    }

    .row-priority-medium {
        background-color: #f8f9fa !important;
        border-left: 4px solid #6c757d !important;
    }

    .row-priority-low {
        background-color: #f0f9ff !important;
        border-left: 4px solid #0dcaf0 !important;
    }

    /* Status badges */
    .badge {
        font-size: 0.75em;
        padding: 0.375em 0.75em;
        font-weight: 500;
    }

    /* Priority badges */
    .priority-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .priority-critical {
        background-color: #dc3545;
        color: white;
    }

    .priority-high {
        background-color: #fd7e14;
        color: white;
    }

    .priority-medium {
        background-color: #6c757d;
        color: white;
    }

    .priority-low {
        background-color: #0dcaf0;
        color: white;
    }

    /* Table header styling */
    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {

        .border-left-primary {
            border-left-width: 2px !important;
        }
    }

    /* Overdue deadline styling */
    .text-danger {
        color: #dc3545 !important;
    }

    .fw-bold {
        font-weight: 700 !important;
    }
</style>
