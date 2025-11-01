@extends('layouts.app')
@section('header', 'Treaty Pipeline Management')
@section('content')
    <style>
        /* Custom Styles */
        :root {
            --primary-blue: #3B82F6;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-900: #111827;
            --blue-50: #EFF6FF;
            --blue-100: #DBEAFE;
            --purple-100: #F3E8FF;
            --yellow-100: #FEF3C7;
            --orange-100: #FFEDD5;
            --green-100: #D1FAE5;
        }

        /* KPI Cards */
        .kpi-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            height: 100%;
        }

        .kpi-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .kpi-icon.blue {
            background: var(--blue-50);
            color: var(--primary-blue);
        }

        .kpi-icon.green {
            background: var(--green-100);
            color: #10B981;
        }

        .kpi-icon.purple {
            background: var(--purple-100);
            color: #8B5CF6;
        }

        .kpi-icon.orange {
            background: var(--orange-100);
            color: #F59E0B;
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0.5rem 0;
        }

        .kpi-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        /* Stage Filter Pills */
        .stage-pills {
            display: flex;
            gap: 0.75rem;
            overflow-x: auto;
            padding: 0.5rem 0;
            margin: 1.5rem 0;
        }

        .stage-pill {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            border: 2px solid var(--gray-200);
            background: white;
            cursor: pointer;
            white-space: nowrap;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stage-pill:hover {
            border-color: var(--primary-blue);
            background: var(--blue-50);
        }

        .stage-pill.active {
            background: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        .stage-pill .badge {
            background: rgba(0, 0, 0, 0.1);
            padding: 0.125rem 0.5rem;
            border-radius: 10px;
            font-size: 0.75rem;
        }

        .stage-pill.active .badge {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Toolbar */
        .toolbar {
            background: white;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.625rem 1rem 0.625rem 2.5rem;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
        }

        /* Table Styles */
        .treaty-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        .treaty-table thead {
            background: var(--gray-50);
        }

        .treaty-table thead th {
            padding: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
        }

        .treaty-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-200);
        }

        .treaty-table tbody tr:hover {
            background: var(--gray-50);
        }

        .treaty-id {
            color: var(--primary-blue);
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .treaty-id:hover {
            text-decoration: underline;
        }

        /* Stage Badges */
        .stage-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .stage-badge.qualification {
            background: var(--blue-100);
            color: #1E40AF;
        }

        .stage-badge.proposal {
            background: var(--purple-100);
            color: #6D28D9;
        }

        .stage-badge.due-diligence {
            background: var(--yellow-100);
            color: #92400E;
        }

        .stage-badge.negotiation {
            background: var(--orange-100);
            color: #C2410C;
        }

        .stage-badge.approval {
            background: var(--green-100);
            color: #065F46;
        }

        /* Priority Badges */
        .priority-badge {
            padding: 0.25rem 0.625rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .priority-high {
            background: #FEE2E2;
            color: #991B1B;
        }

        .priority-medium {
            background: #FEF3C7;
            color: #92400E;
        }

        .priority-low {
            background: #D1FAE5;
            color: #065F46;
        }

        /* Progress Bar */
        .progress {
            height: 8px;
            border-radius: 4px;
            background: var(--gray-200);
            overflow: hidden;
        }

        .progress-bar {
            background: var(--primary-blue);
            height: 100%;
            transition: width 0.3s ease;
        }

        /* Action Buttons */
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid var(--gray-200);
            background: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 0 0.25rem;
        }

        .action-btn:hover {
            background: var(--gray-100);
            border-color: var(--gray-300);
        }

        .action-btn.primary:hover {
            background: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .kpi-value {
                font-size: 1.5rem;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                min-width: 100%;
            }
        }

        /* Table scroll on mobile */
        .table-responsive {
            overflow-x: auto;
        }
    </style>

    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1" style="color: var(--gray-900); font-weight: 700;">Treaty Pipeline</h2>
                <p class="mb-0" style="color: var(--gray-600);">Manage and track all treaty reinsurance opportunities</p>
            </div>
            <a href="{{ route('treaty.leads.onboarding') }}?trans_type=NEW" class="btn btn-primary">
                <i class="bx bx-plus"></i> New Treaty
            </a>
        </div>

        <!-- KPI Dashboard -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="kpi-label">Total Pipeline Value</div>
                            <div class="kpi-value" id="total-pipeline-value">$0.0M</div>
                        </div>
                        <div class="kpi-icon blue">
                            <i class="bx bx-dollar-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="kpi-label">Weighted Value</div>
                            <div class="kpi-value" id="weighted-value">$0.0M</div>
                        </div>
                        <div class="kpi-icon green">
                            <i class="bx bx-trending-up"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="kpi-label">Active Treaties</div>
                            <div class="kpi-value" id="active-treaties">0</div>
                        </div>
                        <div class="kpi-icon purple">
                            <i class="bx bx-file"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="kpi-label">Avg Probability</div>
                            <div class="kpi-value" id="avg-probability">0%</div>
                        </div>
                        <div class="kpi-icon orange">
                            <i class="bx bx-bar-chart-alt-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage Filter Pills -->
        <div class="stage-pills">
            <div class="stage-pill active" data-stage="all">
                All Treaties <span class="badge" id="badge-all">0</span>
            </div>
            <div class="stage-pill" data-stage="qualification">
                Qualification <span class="badge" id="badge-qualification">0</span>
            </div>
            <div class="stage-pill" data-stage="proposal">
                Proposal <span class="badge" id="badge-proposal">0</span>
            </div>
            <div class="stage-pill" data-stage="due-diligence">
                Due Diligence <span class="badge" id="badge-due-diligence">0</span>
            </div>
            <div class="stage-pill" data-stage="negotiation">
                Negotiation <span class="badge" id="badge-negotiation">0</span>
            </div>
            <div class="stage-pill" data-stage="approval">
                Approval <span class="badge" id="badge-approval">0</span>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="search-box">
                <i class="bx bx-search"></i>
                <input type="text" id="search-input" placeholder="Search by Treaty ID, Client Name, or Line of Business..." class="form-control-sm">
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" id="refresh-btn">
                    <i class="bx bx-refresh"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="export-btn">
                    <i class="bx bx-download"></i> Export
                </button>
            </div>
        </div>

        <!-- Data Table -->
        <div class="treaty-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="treaty-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>Treaty ID</th>
                            <th>Client Name</th>
                            <th>Treaty Type</th>
                            <th>Line of Business</th>
                            <th>Est. Premium</th>
                            <th>Stage</th>
                            <th>Probability</th>
                            <th>Priority</th>
                            <th>Next Action</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let currentStage = 'all';
            let currentSearch = '';

            // Initialize DataTable
            const table = $('#treaty-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url: "{{ route('treaty.leads.get') }}",
                    type: "get",
                    data: function(d) {
                        d.stage = currentStage;
                        d.search_query = currentSearch;
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="row-select" value="' + row.opportunity_id + '">';
                        }
                    },
                    {
                        data: 'opportunity_id',
                        name: 'opportunity_id',
                        render: function(data, type, row) {
                            return '<a href="{{ route("treaty.leads.onboarding") }}?prospect=' + data + '&trans_type=EDIT" class="treaty-id">' + data + '</a>';
                        }
                    },
                    {
                        data: 'insured_name',
                        name: 'insured_name',
                        render: function(data, type, row) {
                            return '<div class="d-flex align-items-center"><i class="bx bx-building me-2" style="color: var(--gray-600);"></i><strong>' + (data || 'N/A') + '</strong></div>';
                        }
                    },
                    {
                        data: 'type_of_bus',
                        name: 'type_of_bus',
                        render: function(data, type, row) {
                            const types = {
                                'TPR': 'Proportional',
                                'TNP': 'Non-Proportional XL',
                                'QS': 'Quota Share',
                                'SL': 'Stop Loss',
                                'SR': 'Surplus'
                            };
                            return types[data] || data || 'N/A';
                        }
                    },
                    {
                        data: 'class',
                        name: 'class',
                        render: function(data, type, row) {
                            return data || 'N/A';
                        }
                    },
                    {
                        data: 'fac_date_offered',
                        name: 'fac_date_offered',
                        render: function(data, type, row) {
                            return data ? 'KES ' + parseFloat(data).toLocaleString() : '-';
                        }
                    },
                    {
                        data: 'stage',
                        name: 'stage',
                        render: function(data, type, row) {
                            const stages = {
                                '1': { name: 'Qualification', class: 'qualification' },
                                '2': { name: 'Proposal', class: 'proposal' },
                                '3': { name: 'Due Diligence', class: 'due-diligence' },
                                '4': { name: 'Negotiation', class: 'negotiation' },
                                '5': { name: 'Approval', class: 'approval' }
                            };
                            const stage = stages[data] || { name: 'Unknown', class: 'qualification' };
                            return '<span class="stage-badge ' + stage.class + '">' + stage.name + '</span>';
                        }
                    },
                    {
                        data: 'probability',
                        name: 'probability',
                        render: function(data, type, row) {
                            const prob = data || 0;
                            return '<div><div class="progress mb-1"><div class="progress-bar" style="width: ' + prob + '%"></div></div><small>' + prob + '%</small></div>';
                        }
                    },
                    {
                        data: 'priority',
                        name: 'priority',
                        render: function(data, type, row) {
                            const priorities = {
                                'high': 'priority-high',
                                'medium': 'priority-medium',
                                'low': 'priority-low'
                            };
                            const priority = (data || 'medium').toLowerCase();
                            return '<span class="priority-badge ' + priorities[priority] + '">' + priority.charAt(0).toUpperCase() + priority.slice(1) + '</span>';
                        }
                    },
                    {
                        data: 'next_action',
                        name: 'next_action',
                        render: function(data, type, row) {
                            const action = data || 'Follow up';
                            const dueDate = row.closing_date || '';
                            return '<div><div>' + action + '</div><small class="text-muted"><i class="bx bx-time-five"></i> ' + dueDate + '</small></div>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex">
                                    <a href="{{ route('treaty.leads.onboarding') }}?prospect=${row.opportunity_id}&trans_type=EDIT" class="action-btn primary" title="View">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <a href="{{ route('treaty.leads.onboarding') }}?prospect=${row.opportunity_id}&trans_type=EDIT" class="action-btn" title="Edit">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <button class="action-btn" onclick="deleteTreaty('${row.opportunity_id}')" title="Delete">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                drawCallback: function() {
                    updateKPIs();
                }
            });

            // Stage Filter
            $('.stage-pill').on('click', function() {
                $('.stage-pill').removeClass('active');
                $(this).addClass('active');
                currentStage = $(this).data('stage');
                table.ajax.reload();
            });

            // Search
            $('#search-input').on('keyup', function() {
                currentSearch = $(this).val();
                table.search(currentSearch).draw();
            });

            // Refresh
            $('#refresh-btn').on('click', function() {
                table.ajax.reload();
            });

            // Select All
            $('#select-all').on('change', function() {
                $('.row-select').prop('checked', $(this).prop('checked'));
            });

            // Update KPIs
            function updateKPIs() {
                $.ajax({
                    url: "{{ route('treaty.leads.kpis') }}",
                    type: "GET",
                    success: function(response) {
                        // Format currency values
                        const totalValue = formatCurrency(response.total_value);
                        const weightedValue = formatCurrency(response.weighted_value);

                        $('#total-pipeline-value').text(totalValue);
                        $('#weighted-value').text(weightedValue);
                        $('#active-treaties').text(response.active_count);
                        $('#avg-probability').text(response.avg_probability + '%');

                        // Update stage badges
                        $('#badge-all').text(response.stage_counts.all);
                        $('#badge-qualification').text(response.stage_counts.qualification);
                        $('#badge-proposal').text(response.stage_counts.proposal);
                        $('#badge-due-diligence').text(response.stage_counts.due_diligence);
                        $('#badge-negotiation').text(response.stage_counts.negotiation);
                        $('#badge-approval').text(response.stage_counts.approval);
                    },
                    error: function(error) {
                        console.error('Error fetching KPIs:', error);
                    }
                });
            }

            // Format currency helper
            function formatCurrency(value) {
                if (value >= 1000000) {
                    return 'KES ' + (value / 1000000).toFixed(1) + 'M';
                } else if (value >= 1000) {
                    return 'KES ' + (value / 1000).toFixed(1) + 'K';
                } else {
                    return 'KES ' + value.toFixed(2);
                }
            }

            // Initial KPI update
            updateKPIs();
        });

        function deleteTreaty(id) {
            if (confirm('Are you sure you want to delete this treaty?')) {
                // Implement delete functionality
                console.log('Delete treaty:', id);
            }
        }
    </script>
@endpush
