<div>
    <div class="summary-section mb-4 hidden">
        <div class="card custom-card cpc-highlight-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <div class="fw-semibold fs-18 text-fixed-dark mb-2">Filter Results:-</div>
                        <div class="d-flex">
                            <div class="subtitle pr-3">
                                <span class="text-dark">Reporting Period: </span><span
                                    class="text-muted">{{ date('m/d/Y', strtotime($startDate ?? now()->startOfYear())) }}
                                    -
                                    {{ date('m/d/Y', strtotime($endDate ?? now())) }}</span>
                            </div>
                            <div class="subtitle pr-3">
                                <span class="text-dark">Business Type: </span>
                                <span class="text-muted">All</span>
                            </div>
                            <div class="subtitle pr-3">
                                <span class="text-dark">Reinsurer: </span>
                                <span class="text-muted">All</span>
                            </div>
                            <div class="subtitle pr-3">
                                <span class="text-dark">Insurer: </span>
                                <span class="text-muted">All</span>
                            </div>
                            <div class="subtitle pr-3">
                                <span class="text-dark">Cedant: </span>
                                <span class="text-muted">All</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-3">
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-box">
                                <span class="stat-label">Total Policies:</span>
                                <span class="stat-value fw-bold">{{ $summary['totalPlacements'] }}</span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-box">
                                <span class="stat-label">Total Premium:</span>
                                <span class="stat-value fw-bold">KES
                                    {{ number_format($summary['totalPremium'], 2) }}</span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-box">
                                <span class="stat-label">Average Share:</span>
                                <span class="stat-value fw-bold">{{ $summary['avgPlacementTime'] }} days</span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-box">
                                <span class="stat-label">Active Reinsureds:</span>
                                <span class="stat-value fw-bold">{{ $summary['avgPlacementTime'] }} days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3 hidden">
        <div class="col-md-3">
            <label for="cedant-filter">Cedant:</label>
            <select id="cedant-filter" class="form-control">
                <option value="">All Cedants</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="currency-filter">Currency:</label>
            <select id="currency-filter" class="form-control">
                <option value="">All Currencies</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="class-filter">Class:</label>
            <select id="class-filter" class="form-control">
                <option value="">All Classes</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="reinsurer-filter">Reinsurer:</label>
            <select id="reinsurer-filter" class="form-control">
                <option value="">All Reinsurers</option>
            </select>
        </div>
    </div>
    <div class="row mb-3 hidden">
        <div class="col-md-3">
            <label for="date-from">Date From:</label>
            <input type="date" id="date-from" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="date-to">Date To:</label>
            <input type="date" id="date-to" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="premium-min">Min Premium:</label>
            <input type="number" id="premium-min" class="form-control" placeholder="Min Premium">
        </div>
        <div class="col-md-3">
            <label for="premium-max">Max Premium:</label>
            <input type="number" id="premium-max" class="form-control" placeholder="Max Premium">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="cover-placement-table">
            <thead>
                <tr>
                    <th>Cover No</th>
                    <th>Cover Title</th>
                    <th>Cedant</th>
                    <th>Insured</th>
                    <th>Business Type</th>
                    <th>Currency</th>
                    <th>Class</th>
                    <th>Date Offered</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Our Share %</th>
                    <th>Reinsurer(s)</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            let $table = $('#cover-placement-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                lengthChange: true,
                pageLength: 25,
                lengthMenu: [
                    [25, 50, 100],
                    [25, 50, 100]
                ],
                ajax: {
                    url: "{!! route('cover-reports.cover_placement.data') !!}",
                    data: function(d) {
                        d.cedant = $('#cedant-filter').val();
                        d.currency = $('#currency-filter').val();
                        d.class = $('#class-filter').val();
                        d.reinsurer = $('#reinsurer-filter').val();
                        d.date_from = $('#date-from').val();
                        d.date_to = $('#date-to').val();
                        d.premium_min = $('#premium-min').val();
                        d.premium_max = $('#premium-max').val();
                    }
                },
                columns: [{
                        data: 'cover_no',
                        searchable: true,
                        orderable: false,
                    },
                    {
                        data: 'cover_title',
                        searchable: true,
                    },
                    {
                        data: 'cedant',
                        searchable: true,
                    },
                    {
                        data: 'insured',
                        searchable: true,
                    },
                    {
                        data: 'biz_type',
                        sortable: false,
                        render: function(data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: 'currency',
                        searchable: true,
                    },
                    {
                        data: 'class',
                        searchable: true,
                    },
                    {
                        data: 'date_offerd',
                        searchable: true,
                    },
                    {
                        data: 'start_date',
                        searchable: true,
                    },
                    {
                        data: 'end_date',
                        searchable: true,
                    },
                    {
                        data: 'our_share',
                        searchable: true,
                    },
                    {
                        data: 'reinsurer',
                        searchable: true,
                        render: function(data, type, row) {
                            return data;
                        }
                    },
                ],
                initComplete: function() {
                    populateFilterDropdowns();

                    $('#cover-placement-table tbody').on('click', '.toggle-reinsurers', function(e) {
                        e.preventDefault();

                        const $button = $(this);
                        const $icon = $button.find('.toggle-icon');
                        const coverNo = $button.data('cover');
                        const $row = $button.closest('tr');
                        const rowData = $table.row($row).data();

                        const $nextRow = $row.next();
                        if ($nextRow.hasClass('reinsurer-details-row')) {

                            $nextRow.remove();
                            $icon.removeClass('bi-chevron-down').addClass('bi-chevron-right');
                        } else {
                            $icon.removeClass('bi-chevron-right').addClass('bi-chevron-down');

                            // Build details HTML
                            const detailsHtml = buildReinsurerDetailsHtml(rowData
                                .reinsurer_details);

                            // Insert details row
                            const $detailsRow = $(`
                                <tr class="reinsurer-details-row">
                                    <td colspan="14" class="reinsurer-details-cell">
                                        ${detailsHtml}
                                    </td>
                                </tr>
                            `);

                            $row.after($detailsRow);

                            // Add animation
                            $detailsRow.hide().fadeIn(300);
                        }
                    });
                }
            });

            $('#cedant-filter, #currency-filter, #class-filter, #reinsurer-filter').on('change', function() {
                $table.draw();
            });

            $('#date-from, #date-to, #premium-min, #premium-max').on('change keyup', function() {
                $table.draw();
            });

            function populateFilterDropdowns() {
                // Populate Cedant filter
                $.ajax({
                    url: "{!! route('cover-reports.filter-options') !!}",
                    method: 'GET',
                    data: {
                        type: 'cedant'
                    },
                    success: function(data) {
                        console.log(`data`, data)
                        // var select = $('#cedant-filter');
                        // select.empty().append('<option value="">All Cedants</option>');
                        // $.each(data, function(key, value) {
                        //     select.append('<option value="' + value + '">' + value +
                        //         '</option>');
                        // });
                    }
                });

                // Populate Currency filter
                $.ajax({
                    url: "{!! route('cover-reports.filter-options') !!}",
                    method: 'GET',
                    data: {
                        type: 'currency'
                    },
                    success: function(data) {
                        // var select = $('#currency-filter');
                        // select.empty().append('<option value="">All Currencies</option>');
                        // $.each(data, function(key, value) {
                        //     select.append('<option value="' + value + '">' + value +
                        //         '</option>');
                        // });
                    }
                });

                // Populate Class filter
                $.ajax({
                    url: "{!! route('cover-reports.filter-options') !!}",
                    method: 'GET',
                    data: {
                        type: 'class'
                    },
                    success: function(data) {
                        // var select = $('#class-filter');
                        // select.empty().append('<option value="">All Classes</option>');
                        // $.each(data, function(key, value) {
                        //     select.append('<option value="' + value + '">' + value +
                        //         '</option>');
                        // });
                    }
                });

                // Populate Reinsurer filter
                $.ajax({
                    url: "{!! route('cover-reports.filter-options') !!}",
                    method: 'GET',
                    data: {
                        type: 'reinsurer'
                    },
                    success: function(data) {
                        // var select = $('#reinsurer-filter');
                        // select.empty().append('<option value="">All Reinsurers</option>');
                        // $.each(data, function(key, value) {
                        //     select.append('<option value="' + value + '">' + value +
                        //         '</option>');
                        // });
                    }
                });
            }

            function buildReinsurerDetailsHtml(reinsurers) {
                if (!reinsurers || reinsurers.length === 0) {
                    return '<div class="alert alert-info">No reinsurer details available</div>';
                }

                let html = `
                    <div class="reinsurer-details-container" style="padding: 15px; background-color: #f8f9fa;">
                        <h6 class="mb-3"><i class="fas fa-building"></i> Reinsurer Breakdown:</h6>
                        <div class="row">
                        `;

                reinsurers.forEach(function(reinsurer, index) {
                    html += `
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body p-3">
                                        <h6 class="card-title text-primary mb-2" style="font-size: 0.9rem;">
                                            ${reinsurer.name}
                                        </h6>
                                        <div class="reinsurer-details" style="font-size: 0.8rem;">
                                            <div class="mb-1">
                                                <strong>Share:</strong>
                                                <span class="badge badge-info">${reinsurer.share}</span>
                                            </div>
                                            <div class="mb-1">
                                                <strong>Sum Insured:</strong>
                                                <span class="text-success">${reinsurer.sum_insured}</span>
                                            </div>
                                            <div class="mb-1">
                                                <strong>Premium:</strong>
                                                <span class="text-warning">${reinsurer.premium}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                });

                html += `
                  </div>
                </div>
                `;

                return html;
            }

            function clearAllFilters() {
                $('#cedant-filter, #currency-filter, #class-filter, #reinsurer-filter').val('');
                $('#date-from, #date-to, #premium-min, #premium-max').val('');
                $table.draw();
            }

        });
    </script>
@endpush
