@extends('layouts.app')
@section('header', 'Treaty Pipeline Management')
@section('styles')
    @include('business_development.intermediaries.partials.styles')
@endsection
@section('content')

    <div class="container-fluid mt-3 fac-pipeline-page">
        <!-- Header Section -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-0">Treaty Pipeline</h1>
                <p class="text-muted mb-0 mt-1 fs-13">Create a new insurance cover for</p>
            </div>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/">Business Development</a></li>
                        <li class="breadcrumb-item"><a href="/">Pipeline</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Treaty</li>
                    </ol>
                </nav>
            </div>
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
                            <i class="bi bi-cash-stack"></i>
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
                            <i class="bi bi-graph-up-arrow"></i>
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
                            <i class="bi bi-file-earmark-text"></i>
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
                            <i class="bi bi-percent"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('treaty.leads.onboarding') }}?trans_type=NEW" class="btn btn-primary btn-sm">
                            <i class="bi bi-person-plus-fill me-1"></i>
                            Onboard New Treaty
                        </a>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pipeline-table-container mb-3">
            <div class="table-header">
                <div class="row">
                    <div class="col-12">
                        <div class="table-controls">
                            <input type="search" class="form-inputs mb-0 filter-search-input"
                                style="font-size: 14px; border:1px solid #3634346e !important;"
                                placeholder="Search opportunities..." id="globalSearch">

                            <select class="filter-select form-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                @if ($statuses ?? false)
                                    @foreach ($statuses as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                @endif
                            </select>

                            <select class="filter-select form-select" id="classGroupFilter">
                                <option value="">All Class Group</option>
                                @if ($classGroups ?? false)
                                    @foreach ($classGroups as $key => $group)
                                        <option value="{{ $key }}">{{ $group }}</option>
                                    @endforeach
                                @endif
                            </select>

                            <select class="filter-select form-select" id="classFilter">
                                <option value="">All Class</option>
                                @if ($classes ?? false)
                                    @foreach ($classes as $key => $class)
                                        <option value="{{ $key }}">{{ $class }}</option>
                                    @endforeach
                                @endif
                            </select>

                            <select class="filter-select form-select" id="priorityFilter">
                                <option value="">All Priorities</option>
                                @if ($priorities ?? false)
                                    @foreach ($priorities as $key => $priority)
                                        <option value="{{ $key }}">{{ $priority }}</option>
                                    @endforeach
                                @endif
                            </select>

                            <button type="button" class="btn btn-primary" id="applyFiltersBtn">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="resetFiltersBtn">
                                <i class="bi bi-reset me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card custom-card mb-0">
            <div class="card-header p-0">
                <div class="urgency-legend">
                    <div class="legend-title">
                        <i class="bi bi-info-circle me-2"></i>Urgency Classification
                    </div>
                    <div class="legend-items">
                        <div class="legend-item">
                            <span class="color-indicator" style="background-color: #fef2f2;"></span>
                            <span><strong>Critical:</strong> ≤ 7 days to effective date</span>
                        </div>
                        <div class="legend-item">
                            <span class="color-indicator" style="background-color: #fffbeb;"></span>
                            <span><strong>Urgent:</strong> 8-14 days to effective date</span>
                        </div>
                        <div class="legend-item">
                            <span class="color-indicator" style="background-color: #eff6ff;"></span>
                            <span><strong>Upcoming:</strong> 15-30 days to effective date</span>
                        </div>
                        <div class="legend-item">
                            <span class="color-indicator" style="background-color: #f0fdf4;"></span>
                            <span><strong>Normal:</strong> 31+ days to effective date</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pb-0">
                <div class="table-responsive">
                    <table class="table text-nowrap table-striped table-hover mb-0" id="treaty-table">
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
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let currentStage = 'all';
            let currentSearch = '';
            let statusFilter = '';
            let classGroupFilter = '';
            let classFilter = '';
            let priorityFilter = '';

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
                        d.global_search = currentSearch;
                        d.status = statusFilter;
                        d.class_group = classGroupFilter;
                        d.class = classFilter;
                        d.priority = priorityFilter;
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="row-select" value="' + row
                                .opportunity_id + '">';
                        }
                    },
                    {
                        data: 'opportunity_id',
                        name: 'opportunity_id',
                        render: function(data, type, row) {
                            return '<a href="{{ route('treaty.leads.onboarding') }}?prospect=' +
                                data + '&trans_type=EDIT" class="treaty-id">' + data + '</a>';
                        }
                    },
                    {
                        data: 'insured_name',
                        name: 'insured_name',
                        render: function(data, type, row) {
                            return '<div class="d-flex align-items-center"><i class="bi bi-buildings me-2" style="color: var(--gray-600);"></i><strong>' +
                                (data || 'N/A') + '</strong></div>';
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
                                '1': {
                                    name: 'Qualification',
                                    class: 'qualification'
                                },
                                '2': {
                                    name: 'Proposal',
                                    class: 'proposal'
                                },
                                '3': {
                                    name: 'Due Diligence',
                                    class: 'due-diligence'
                                },
                                '4': {
                                    name: 'Negotiation',
                                    class: 'negotiation'
                                },
                                '5': {
                                    name: 'Approval',
                                    class: 'approval'
                                }
                            };
                            const stage = stages[data] || {
                                name: 'Unknown',
                                class: 'qualification'
                            };
                            return '<span class="stage-badge ' + stage.class + '">' + stage.name +
                                '</span>';
                        }
                    },
                    {
                        data: 'probability',
                        name: 'probability',
                        render: function(data, type, row) {
                            const prob = data || 0;
                            return '<div><div class="progress mb-1"><div class="progress-bar" style="width: ' +
                                prob + '%"></div></div><small>' + prob + '%</small></div>';
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
                            return '<span class="priority-badge ' + priorities[priority] + '">' +
                                priority.charAt(0).toUpperCase() + priority.slice(1) + '</span>';
                        }
                    },
                    {
                        data: 'next_action',
                        name: 'next_action',
                        render: function(data, type, row) {
                            const action = data || 'Follow up';
                            const dueDate = row.closing_date || '';
                            return '<div><div>' + action +
                                '</div><small class="text-muted"><i class="bi bi-clock"></i> ' +
                                dueDate + '</small></div>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex">
                                    <a href="{{ route('treaty.leads.onboarding') }}?prospect=${row.opportunity_id}&trans_type=EDIT" class="action-btn primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('treaty.leads.onboarding') }}?prospect=${row.opportunity_id}&trans_type=EDIT" class="action-btn" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="action-btn" onclick="deleteTreaty('${row.opportunity_id}')" title="Delete">
                                        <i class="bi bi-trash"></i>
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

            $('#applyFiltersBtn').on('click', function() {
                currentSearch = $('#globalSearch').val().trim();
                statusFilter = $('#statusFilter').val();
                classGroupFilter = $('#classGroupFilter').val();
                classFilter = $('#classFilter').val();
                priorityFilter = $('#priorityFilter').val();
                table.ajax.reload();
            });

            $('#resetFiltersBtn').on('click', function() {
                currentSearch = '';
                statusFilter = '';
                classGroupFilter = '';
                classFilter = '';
                priorityFilter = '';

                $('#globalSearch').val('');
                $('#statusFilter').val('');
                $('#classGroupFilter').val('');
                $('#classFilter').val('');
                $('#priorityFilter').val('');
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
