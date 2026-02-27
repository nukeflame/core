@extends('layouts.app')

@section('styles')
    @include('business_development.intermediaries.partials.styles')
@endsection

@section('content')
    <div class="container-fluid mt-3 fac-pipeline-page">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-0">Facultative Pipeline</h1>
                <p class="text-muted mb-0 mt-1 fs-13">Create a new insurance cover for</p>
            </div>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/">Business Development</a></li>
                        <li class="breadcrumb-item"><a href="/">Pipeline</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Facultative</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value">{{ number_format($kpis['active_opportunities']['value']) }}</div>
                    <div class="kpi-label d-flex align-items-center gap-2">
                        <i class="bi bi-briefcase kpi-icon"></i>
                        <span>Active Opportunities</span>
                    </div>

                    @if ((int) $kpis['active_opportunities']['value'] > 0)
                        @if ($kpis['active_opportunities']['trend'])
                            <div class="kpi-trend trend-{{ $kpis['active_opportunities']['trend']['direction'] }}">
                                <i
                                    class="bi bi-arrow-{{ $kpis['active_opportunities']['trend']['direction'] == 'up' ? 'up' : 'down' }}"></i>
                                {{ $kpis['active_opportunities']['trend']['direction'] == 'up' ? '+' : '-' }}{{ $kpis['active_opportunities']['trend']['percentage'] }}%
                                this month
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value">KES {{ number_format($kpis['pipeline_premium']['value'] / 1000000, 1) }}M</div>
                    <div class="kpi-label d-flex align-items-center gap-2">
                        <i class="bi bi-cash-stack kpi-icon"></i>
                        <span>Pipeline Premium</span>
                    </div>

                    @if ((int) $kpis['pipeline_premium']['value'] > 0)
                        @if ((int) $kpis['pipeline_premium']['value'] > 0)
                            @if ($kpis['pipeline_premium']['trend'])
                                <div class="kpi-trend trend-{{ $kpis['pipeline_premium']['trend']['direction'] }}">
                                    <i
                                        class="bi bi-arrow-{{ $kpis['pipeline_premium']['trend']['direction'] == 'up' ? 'up' : 'down' }}"></i>
                                    {{ $kpis['pipeline_premium']['trend']['direction'] == 'up' ? '+' : '-' }}{{ $kpis['pipeline_premium']['trend']['percentage'] }}%
                                    QoQ
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['conversion_rate']['value'] }}%</div>
                    <div class="kpi-label d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up-arrow kpi-icon"></i>
                        <span>Conversion Rate</span>
                    </div>

                    @if ((int) $kpis['conversion_rate']['value'] > 0)
                        @if ($kpis['conversion_rate']['trend'])
                            <div class="kpi-trend trend-{{ $kpis['conversion_rate']['trend']['direction'] }}">
                                <i
                                    class="bi bi-arrow-{{ $kpis['conversion_rate']['trend']['direction'] == 'up' ? 'up' : 'down' }}"></i>
                                {{ $kpis['conversion_rate']['trend']['direction'] == 'up' ? '+' : '' }}{{ $kpis['conversion_rate']['trend']['percentage'] }}%
                                improvement
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['critical_deadlines']['value'] }}</div>
                    <div class="kpi-label d-flex align-items-center gap-2">
                        <i class="bi bi-alarm kpi-icon"></i>
                        <div>
                            Critical Deadlines -
                        </div>
                        @if ((int) $kpis['critical_deadlines']['value'] > 0)
                            <div class="kpi-trend trend-down mt-0">
                                <i class="bi bi-clock text-warning"></i> Requires attention
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="onboardProspect()">
                            <i class="bi bi-person-plus-fill me-1"></i>
                            Onboard New Prospect
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('leads.import.pipeline_opportunities.sample') }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download me-1"></i>
                            Download Sample
                        </a>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#pipelineImportModal">
                            <i class="bi bi-upload me-1"></i>
                            Import Opportunities
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3 pipeline-content-row">
            <div class="col-xl-12">
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
                        <div class="pipeline-table-container">
                            <div class="table-header">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-controls">
                                            <input type="search" class="form-inputs mb-0 filter-search-input"
                                                style="font-size: 14px; border:1px solid #3634346e !important;"
                                                placeholder="Search opportunities..." id="globalSearch">

                                            <select class="filter-select form-select" id="statusFilter"
                                                placeholder="Select status">
                                                <option value="">All Statuses</option>
                                                @if ($statuses)
                                                    @foreach ($statuses as $key => $status)
                                                        <option value="{{ $key }}">{{ $status }}</option>
                                                    @endforeach
                                                @endif
                                            </select>

                                            <select class="filter-select form-select" id="classGroupFilter"
                                                placeholder="Select class group">
                                                <option value="">All Class Group</option>
                                                @if ($classGroups)
                                                    @foreach ($classGroups as $key => $group)
                                                        <option value="{{ $key }}">{{ $group }}</option>
                                                    @endforeach
                                                @endif
                                            </select>

                                            <select class="filter-select form-select" id="classFilter"
                                                placeholder="Select class">
                                                <option value="">All Class</option>
                                                @foreach ($classes as $key => $class)
                                                    <option value="{{ $key }}">{{ $class }}</option>
                                                @endforeach
                                            </select>

                                            <select class="filter-select form-select" id="priorityFilter"
                                                placeholder="Select priority">
                                                <option value="">All Priorities</option>
                                                @foreach ($priorities as $key => $priority)
                                                    <option value="{{ $key }}">{{ $priority }}</option>
                                                @endforeach
                                            </select>

                                            <button type="button" class="btn btn-primary" id="applyFiltersBtn">
                                                <i class="bi bi-search-alt-2 me-1"></i>Search
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="resetFiltersBtn">
                                                <i class="bi bi-reset me-1"></i>Reset
                                            </button>
                                            <button type="button" class="btn btn-danger d-none" style="width: 57%"
                                                id="deleteSelectedOpportunitiesBtn">
                                                <i class="bi bi-trash me-1"></i>
                                                Delete Selected (<span id="selectedOpportunitiesCount">0</span>)
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table text-nowrap table-striped table-hover" id="opportunities_table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="selectAllOpportunities"
                                            aria-label="Select all opportunities">
                                    </th>
                                    <th>Opportunity ID</th>
                                    <th>Client Category</th>
                                    <th>Priority</th>
                                    <th>Cedant Name</th>
                                    <th>Class of Business</th>
                                    <th>Status</th>
                                    <th>Gross Premium</th>
                                    <th>Commission %</th>
                                    <th>Effective Date</th>
                                    <th>Expiry Date</th>
                                    <th>Prospect Lead</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="pipelineImportModal" tabindex="-1"
        aria-labelledby="pipelineImportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="pipelineImportForm" method="POST" action="{{ route('leads.import.pipeline_opportunities') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header text-white"
                        style="background: linear-gradient(135deg,#f91520 0%,#4d4f51 100%">
                        <h5 class="modal-title" id="pipelineImportModalLabel">Import Pipeline Opportunities</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="import_file" class="form-label">Excel/CSV file</label>
                            <input type="file" class="form-control" id="import_file" name="import_file"
                                accept=".csv,.xls,.xlsx" required>
                            <small class="text-muted d-block mt-2">
                                Use the sample file format. Supported: CSV, XLS, XLSX.
                            </small>
                        </div>

                        <div class="mb-3 d-none" id="pipelineImportProgressWrap">
                            <label class="form-label mb-1">Upload progress</label>
                            <div class="progress" style="height: 18px;">
                                <div id="pipelineImportProgressBar"
                                    class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: 0%;" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">0%</div>
                            </div>
                            <small id="pipelineImportProgressText" class="text-muted d-block mt-1">Preparing
                                upload...</small>
                        </div>

                        <div class="border rounded p-2 bg-light d-none" id="pipelineImportPreviewWrap"
                            style="max-height: 800px">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="fs-14">File Preview</strong>
                                <small class="text-muted" id="pipelineImportPreviewMeta"></small>
                            </div>
                            <div class="table-responsive" style="max-height: 260px;">
                                <table class="table table-sm table-striped mb-0" id="pipelineImportPreviewTable">
                                    <thead></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" id="pipelineImportResetBtn">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button id="pipelineImportSubmitBtn" type="submit" class="btn btn-success">
                            <i class="bi bi-upload me-1"></i>Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('business_development.intermediaries.partials.scripts')
    <script>
        $(document).ready(function() {
            const $form = $('#pipelineImportForm');
            const $fileInput = $('#import_file');
            const $submitBtn = $('#pipelineImportSubmitBtn');
            const $resetBtn = $('#pipelineImportResetBtn');
            const $progressWrap = $('#pipelineImportProgressWrap');
            const $progressBar = $('#pipelineImportProgressBar');
            const $progressText = $('#pipelineImportProgressText');
            const $previewWrap = $('#pipelineImportPreviewWrap');
            const $previewMeta = $('#pipelineImportPreviewMeta');
            const $previewHead = $('#pipelineImportPreviewTable thead');
            const $previewBody = $('#pipelineImportPreviewTable tbody');
            const previewUrl = "{{ route('leads.import.pipeline_opportunities.preview') }}";
            let isUploading = false;

            function resetProgress() {
                $progressBar.css('width', '0%').attr('aria-valuenow', '0').text('0%');
                $progressText.text('Preparing upload...');
                $progressWrap.addClass('d-none');
            }

            function setProgress(percent, text = '') {
                const value = Math.max(0, Math.min(100, Math.round(percent)));
                $progressBar.css('width', value + '%').attr('aria-valuenow', value).text(value + '%');
                if (text) {
                    $progressText.text(text);
                }
            }

            function clearPreview() {
                $previewHead.empty();
                $previewBody.empty();
                $previewMeta.text('');
                $previewWrap.addClass('d-none');
            }

            function resetImportForm() {
                if (isUploading) {
                    return;
                }
                $form[0].reset();
                clearPreview();
                resetProgress();
            }

            function renderPreview(payload) {
                const headers = payload.headers || [];
                const keys = payload.header_keys || [];
                const rows = payload.rows || [];

                if (!headers.length || !keys.length) {
                    clearPreview();
                    return;
                }

                let headHtml = '<tr>';
                headers.forEach((header) => {
                    headHtml += `<th>${$('<div/>').text(header).html()}</th>`;
                });
                headHtml += '</tr>';
                $previewHead.html(headHtml);

                const bodyHtml = rows.map((row) => {
                    let rowHtml = '<tr>';
                    keys.forEach((key) => {
                        const value = row[key] ?? '';
                        rowHtml += `<td>${$('<div/>').text(String(value)).html()}</td>`;
                    });
                    rowHtml += '</tr>';
                    return rowHtml;
                }).join('');

                $previewBody.html(bodyHtml || '<tr><td colspan="' + headers.length +
                    '" class="text-muted">No data rows found.</td></tr>');
                $previewMeta.text(
                    `Showing ${payload.preview_rows || rows.length} of ${payload.total_rows || rows.length} rows`
                );
                $previewWrap.removeClass('d-none');
            }

            $fileInput.on('change', function() {
                const file = this.files && this.files[0] ? this.files[0] : null;
                clearPreview();
                resetProgress();

                if (!file) {
                    return;
                }

                const formData = new FormData();
                formData.append('import_file', file);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $.ajax({
                    url: previewUrl,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        renderPreview(response);
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message ||
                            'Could not preview this file.';
                        if (typeof toastr !== 'undefined') {
                            toastr.error(message);
                        } else {
                            alert(message);
                        }
                    }
                });
            });

            $form.on('submit', function(e) {
                e.preventDefault();

                if (isUploading) {
                    return;
                }

                const file = $fileInput[0]?.files?.[0];
                if (!file) {
                    if (typeof toastr !== 'undefined') {
                        toastr.warning('Please select a file to import.');
                    }
                    return;
                }

                const formData = new FormData(this);
                isUploading = true;
                $submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Importing...'
                );
                $progressWrap.removeClass('d-none');
                setProgress(0, 'Starting upload...');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(event) {
                            if (event.lengthComputable) {
                                const percent = (event.loaded / event.total) * 100;
                                setProgress(percent,
                                    `Uploading ${Math.round(percent)}%...`);
                            }
                        });
                        return xhr;
                    },
                    success: function(response) {
                        setProgress(100, 'Upload complete. Processing finished.');

                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message ||
                                'Import completed successfully.');
                            if (response.import_errors && response.import_errors.length) {
                                toastr.warning(
                                    `Imported with ${response.import_errors.length} warning(s).`
                                );
                            }
                        }

                        setTimeout(function() {
                            window.location.reload();
                        }, 900);
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message ||
                            'Import failed. Please try again.';
                        if (typeof toastr !== 'undefined') {
                            toastr.error(message);
                        } else {
                            alert(message);
                        }
                    },
                    complete: function() {
                        isUploading = false;
                        $submitBtn.prop('disabled', false).html(
                            '<i class="bi bi-upload me-1"></i>Import');
                    }
                });
            });

            $resetBtn.on('click', function() {
                resetImportForm();
            });

            $('#pipelineImportModal').on('hidden.bs.modal', function() {
                if (!isUploading) {
                    resetImportForm();
                }
            });
        });
    </script>
@endpush
