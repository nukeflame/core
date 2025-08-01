@extends('layouts.app', [
    'pageTitle' => 'Cover Reports - ' . $company->company_name,
])

@section('styles')
    <style>
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            font-weight: 500;
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .summary-section {
            .stat-box {
                padding: 10px;
                border-radius: 5px;

                .stat-label {
                    color: #6c757d;
                    margin-right: 10px;
                }

                .stat-value {
                    font-size: 1.1em;
                }
            }
        }

        .badge.bg-success {
            background-color: #28a745 !important;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
        }

        .tab-content .tab-pane {
            padding: 1rem;
            border-top: 1px solid inherit;
            border-radius: 0px
        }

        .toggle-reinsurers {
            border: none !important;
            background: none !important;
            color: #007bff !important;
            text-decoration: none !important;
            padding: 0 !important;
            font-weight: 500;
        }

        .toggle-reinsurers:hover {
            color: #0056b3 !important;
            text-decoration: none !important;
        }

        .toggle-icon {
            transition: transform 0.3s ease;
            margin-right: 5px;
        }

        .reinsurer-details-row {
            background-color: #f8f9fa !important;
        }

        .reinsurer-details-cell {
            border-top: none !important;
            padding: 0 !important;
        }

        .reinsurer-details-container {
            border-left: 4px solid #007bff;
            margin-left: 20px;
        }

        .reinsurer-details-container .card {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .reinsurer-details-container .card:hover {
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .reinsurer-details-container .col-md-6 {
                margin-bottom: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Cover Reports</p>
            <span class="fs-semibold text-muted">Facultative Placement Reports with Advanced Filtering & Analytics</span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cover Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @include('reports._filters')

                    <ul class="nav nav-tabs mt-4">
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab', 'covers-placement') == 'covers-placement' ? 'active' : '' }}"
                                href="{{ route('cover-reports.index', ['tab' => 'covers-placement']) }}">
                                Covers Placement
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'covers-by-type' ? 'active' : '' }}"
                                href="{{ route('cover-reports.index', ['tab' => 'covers-by-type']) }}">
                                Covers by Type
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'covers-ending' ? 'active' : '' }}"
                                href="{{ route('cover-reports.index', ['tab' => 'covers-ending']) }}">
                                Covers Ending
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') == 'renewed-covers' ? 'active' : '' }}"
                                href="{{ route('cover-reports.index', ['tab' => 'renewed-covers']) }}">
                                Renewed Covers
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane {{ request('tab', 'covers-placement') == 'covers-placement' ? 'd-block' : 'd-none' }}"
                            id="covers-placement" role="tabpanel">
                            @include('reports.cover-reports.cover_placement')
                        </div>
                        <div class="tab-pane {{ request('tab') == 'covers-by-type' ? 'd-block' : 'd-none' }}"
                            id="covers-by-type" role="tabpanel">
                            @include('reports.cover-reports.cover_by_type')
                        </div>
                        <div class="tab-pane {{ request('tab') == 'covers-ending' ? 'd-block' : 'd-none' }}"
                            id="covers-ending" role="tabpanel">
                            @include('reports.cover-reports.cover_ending')

                        </div>
                        <div class="tab-pane {{ request('tab') == 'renewed-covers' ? 'd-block' : 'd-none' }}"
                            id="renewed-covers" role="tabpanel">
                            @include('reports.cover-reports.cover_renewed')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#region, #period, #line_of_business').on('change', function() {
                // We'll handle this with submit button instead of auto-submit
            });

            // View contract modal
            $('.view-contract').on('click', function(e) {
                e.preventDefault();

                const contractId = $(this).data('contract-id');

                // Show loading state
                $(this).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                // Fetch contract details via AJAX
                $.ajax({
                    url: `/api/contracts/${contractId}`,
                    method: 'GET',
                    success: function(response) {
                        // Create and show modal with contract details
                        const modal = createContractModal(response);
                        $('body').append(modal);
                        $('#contractModal').modal('show');

                        // Reset button state
                        $('.view-contract').text('View');
                    },
                    error: function() {
                        alert('Error loading contract details.');
                        $('.view-contract').text('View');
                    }
                });
            });

            function createContractModal(contract) {
                return `
            <div class="modal fade" id="contractModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Contract Details: ${contract.reference}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Basic Information</h6>
                                    <p><strong>Cedant:</strong> ${contract.cedant.name}</p>
                                    <p><strong>Type:</strong> ${contract.cover_type.name}</p>
                                    <p><strong>Premium:</strong> ${contract.formatted_premium}</p>
                                    <p><strong>Inception Date:</strong> ${contract.inception_date}</p>
                                    <p><strong>Status:</strong> <span class="badge bg-${contract.status_color}">${contract.status}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Additional Details</h6>
                                    <p><strong>Region:</strong> ${contract.region.name}</p>
                                    <p><strong>Line of Business:</strong> ${contract.line_of_business.name}</p>
                                    <p><strong>Placement Date:</strong> ${contract.placement_date || 'N/A'}</p>
                                    <p><strong>Completion Date:</strong> ${contract.completion_date || 'N/A'}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
            }
        });
    </script>
@endpush
