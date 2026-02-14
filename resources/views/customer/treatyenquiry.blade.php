@extends('layouts.app')

@push('styles')
    <style>
        .card.custom-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card.custom-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .avatar {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
        }

        .avatar-rounded {
            border-radius: 0.5rem !important;
        }

        .bg-primary-transparent {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .bg-success-transparent {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .bg-info-transparent {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .bg-warning-transparent {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        #coverlist {
            font-size: 0.875rem;
        }

        #coverlist thead th {
            font-weight: 600;
            text-transform: capitalize;
            font-size: 0.72rem;
            letter-spacing: 0.45px;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }

        #coverlist tbody tr {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        #coverlist tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .table-stats .value {
            font-size: 1.25rem;
            font-weight: 600;
        }

        #cedant-fetch-status {
            display: none;
        }

        #coverlist_wrapper .dataTables_filter input {
            width: 26rem;
            max-width: 100%;
        }

        @media (max-width: 768px) {
            .page-header-breadcrumb {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .page-header-breadcrumb .ms-md-1 {
                margin-top: 0.5rem;
            }

            .avatar {
                width: 2.5rem;
                height: 2.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">{{ $treaty_name }}</h1>
            <p class="text-muted mb-0 mt-1">Browse covers and open endorsement details quickly</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i
                                class="bx bx-home-alt me-1"></i>Home</a></li>
                    <li class="breadcrumb-item">Treaty Enquiry</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $treaty_name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body table-stats">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                <i class="bi bi-file-earmark-text fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0" id="stat-total-title">Total Covers</p>
                            <h4 class="fw-semibold mt-1 value" id="stat-total-covers">0</h4>
                            <span class="text-muted fs-12" id="stat-total-note">All records</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body table-stats">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-success-transparent">
                                <i class="bi bi-funnel fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0" id="stat-filtered-title">Filtered Results</p>
                            <h4 class="fw-semibold mt-1 value" id="stat-filtered-covers">0</h4>
                            <span class="text-muted fs-12" id="stat-search-state">Current search</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body table-stats">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-info-transparent">
                                <i class="bi bi-diagram-3 fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0" id="stat-bus-title">Business Type</p>
                            <h4 class="fw-semibold mt-1 value" id="stat-bus-type">-</h4>
                            <span class="text-muted fs-12" id="stat-bus-note">Selected class mix</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body table-stats">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-warning-transparent">
                                <i class="bi bi-clock-history fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <p class="text-muted mb-0" id="stat-refresh-title">Last Updated</p>
                            <h4 class="fw-semibold mt-1 value" id="stat-last-refresh">{{ now()->format('h:i A') }}</h4>
                            <span class="text-muted fs-12" id="stat-refresh-note">Facultative enquiry sync time</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Cover Directory</h5>
                        <small class="text-muted">Select a cover to open endorsement details</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="newCoverBtn">
                        <i class="bx bx-plus me-1"></i> Add New Cover
                    </button>
                </div>
                <div class="card-body">
                    {{ html()->form('POST', route('endorsements_list'))->id('form_cover_datatable')->open() }}
                    <input type="text" id="customer_id" name="customer_id" hidden />
                    <input type="text" name="cover_no" id="cov_cover_no" hidden>
                    <div class="table-responsive">
                        <table id="coverlist" class="table table-bordered table-hover align-middle text-nowrap w-100"
                            style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Cover Number</th>
                                    <th>Endorsement No.</th>
                                    <th>Transaction</th>
                                    <th>Cover Type</th>
                                    <th>Class</th>
                                    <th>Cedant</th>
                                    <th>Cover From</th>
                                    <th>Expiry Date</th>
                                    <th>Verification</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    {{ csrf_field() }}
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal customer-model-wrapper effect-scale" id="newCoverModal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="new_cover_form" action="{{ route('cover.form') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Choose Cedant</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div class="d-flex flex-column ced-body">
                                    <input type="hidden" id="trans_type" name="trans_type" value="NEW">
                                    <input type="hidden" id="type_of_bus" name="type_of_bus">
                                    <p class="text-muted mb-2">Select a cedant to continue with cover registration.</p>
                                    <div class="alert py-2 mb-2" id="cedant-fetch-status" role="alert"></div>
                                    <select class="form-inputs select2" id="ccustomer_id" name="customer_id" required>
                                        <option value="">-- Select Cedant --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                            id="dismiss-cover-modal">Close</button>
                        <button type="button" id="next-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const typeOfBus = {!! json_encode($type_of_bus) !!};
            const businessTypeNames = {
                TPR: 'Treaty Proportional',
                TNP: 'Treaty Non-Proportional',
                FPR: 'Facultative Proportional',
                FNP: 'Facultative Non-Proportional'
            };
            const $cedantSelect = $('#ccustomer_id');
            const $nextBtn = $('#next-save-btn');
            const $cedantStatus = $('#cedant-fetch-status');
            const viewportHeight = window.innerHeight || 900;
            const reservedHeight = 520;
            const estimatedRowHeight = 44;
            const calculatedPageLength = Math.max(
                10,
                Math.min(100, Math.floor((viewportHeight - reservedHeight) / estimatedRowHeight))
            );
            const defaultPageLength = Number.isFinite(calculatedPageLength) ? calculatedPageLength : 25;
            const lengthOptions = Array.from(new Set([defaultPageLength, 25, 50, 100, 200])).sort((a, b) => a - b);
            const selectedBusTypes = Array.isArray(typeOfBus) ? typeOfBus : [typeOfBus].filter(Boolean);
            const isFacultative = selectedBusTypes.some(code => String(code || '').startsWith('F'));
            const segmentLabel = isFacultative ? 'Facultative' : 'Treaty';
            const classMix = selectedBusTypes.map(code => businessTypeNames[code] || code).join(', ') || '-';

            $('#stat-total-title').text(`Total ${segmentLabel} Covers`);
            $('#stat-total-note').text(`All ${segmentLabel.toLowerCase()} records`);
            $('#stat-filtered-title').text(`${segmentLabel} Matches`);
            $('#stat-bus-title').text(`${segmentLabel} Class Mix`);
            $('#stat-bus-note').text('Selected classes in this enquiry');
            $('#stat-refresh-title').text('Last Updated');
            $('#stat-refresh-note').text(`${segmentLabel} enquiry sync time`);

            $('#stat-bus-type').text(classMix);

            function showCedantStatus(message, typeClass) {
                $cedantStatus.removeClass('alert-info alert-success alert-warning alert-danger')
                    .addClass(typeClass)
                    .text(message)
                    .show();
            }

            function hideCedantStatus() {
                $cedantStatus.hide().text('');
            }

            function updateNextButtonState() {
                $nextBtn.prop('disabled', !$cedantSelect.val());
            }

            $('#newCoverModal').on('shown.bs.modal', function() {
                if (!$cedantSelect.hasClass('select2-hidden-accessible')) {
                    $cedantSelect.select2({
                        placeholder: '-- Select Cedant --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#newCoverModal')
                    });
                }
                updateNextButtonState();
            });

            const table = $('#coverlist').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: defaultPageLength,
                lengthMenu: [
                    lengthOptions,
                    lengthOptions
                ],
                order: [
                    [0, 'asc']
                ],
                ajax: {
                    url: '{{ route('treatyenquiry.data') }}',
                    data: function(d) {
                        d.type_of_bus = typeOfBus;
                    },
                    error: function() {
                        toastr.error('Failed to load covers data. Please refresh the page.');
                    }
                },
                columns: [{
                        data: 'cover_no',
                        defaultContent: "<b class='dashes'>—</b>",
                        className: 'fw-semibold text-primary'
                    },
                    {
                        data: 'endorsement_no',
                        defaultContent: "<b class='dashes'>—</b>"
                    },
                    {
                        data: 'transaction_type',
                        defaultContent: "<b class='dashes'>—</b>"
                    },
                    {
                        data: 'cover_type',
                        defaultContent: "<b class='dashes'>—</b>"
                    },
                    {
                        data: 'class_desc',
                        defaultContent: "<b class='dashes'>—</b>"
                    },
                    {
                        data: 'cedant_name',
                        defaultContent: "<b class='dashes'>—</b>"
                    },
                    {
                        data: 'cover_from',
                        defaultContent: "<b class='dashes'>—</b>"
                    },
                    {
                        data: 'cover_to',
                        defaultContent: "<b class='dashes'>—</b>"
                    },
                    {
                        data: 'status_verification',
                        orderable: false,
                        searchable: false,
                        defaultContent: "<b class='dashes'>—</b>"
                    },
                    {
                        data: 'status_badge',
                        orderable: false,
                        searchable: false,
                        defaultContent: "<b class='dashes'>—</b>"
                    },
                ],
                language: {
                    search: "Search covers:",
                    searchPlaceholder: "Search cover no, endorsement, cedant, type...",
                    emptyTable: "No covers available",
                    zeroRecords: "No matching covers found",
                    info: "Showing _START_ to _END_ of _TOTAL_ covers",
                    infoEmpty: "No covers available",
                    infoFiltered: "(filtered from _MAX_ total covers)"
                }
            });

            function updateStats() {
                const info = table.page.info();
                const searchTerm = table.search().trim();
                $('#stat-total-covers').text((info.recordsTotal || 0).toLocaleString());
                $('#stat-filtered-covers').text((info.recordsDisplay || 0).toLocaleString());
                $('#stat-last-refresh').text(new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                $('#stat-search-state').text(searchTerm ? `Search: "${searchTerm}"` : 'Current search');
            }

            table.on('init.dt draw.dt xhr.dt search.dt', updateStats);

            $('#coverlist').on('dblclick', 'tbody tr', function(e) {
                if ($(e.target).closest('a, button, .btn, input, select, textarea, label').length) {
                    return;
                }

                const rowData = table.row(this).data();
                const coverNo = rowData?.cover_no || '';

                if (coverNo) {
                    $("#cov_cover_no").val(coverNo);
                    $("#form_cover_datatable").submit();
                }
            });

            $('#newCoverBtn').click(function(e) {
                e.preventDefault();

                $('#type_of_bus').val(typeOfBus);
                hideCedantStatus();
                $cedantSelect.empty().append('<option value="">-- Select Cedant --</option>');
                $cedantSelect.prop('disabled', true);
                $nextBtn.prop('disabled', true).text('Loading Cedants...');
                $('#newCoverModal').modal('show');
                showCedantStatus('Loading cedants. Please wait...', 'alert-info');

                $.ajax({
                    url: "{{ route('cover.get-customers') }}",
                    method: 'GET',
                    data: {
                        cedant_only: 1
                    },
                    success: function(data) {
                        $cedantSelect.empty().append(
                            '<option value="">-- Select Cedant --</option>');

                        const customers = Array.isArray(data) ? data : [];
                        customers.sort((a, b) => (a.name || '').localeCompare(b.name || ''));

                        $.each(customers, function(key, customer) {
                            $cedantSelect.append('<option value="' + customer
                                .customer_id + '">' + customer
                                .name + '</option>');
                        });

                        $cedantSelect.prop('disabled', false).trigger('change.select2');
                        $nextBtn.text('Next');
                        updateNextButtonState();

                        if (customers.length === 0) {
                            showCedantStatus('No cedants available to select.',
                                'alert-warning');
                            return;
                        }

                        showCedantStatus(`Loaded ${customers.length} cedants.`,
                            'alert-success');
                    },
                    error: function() {
                        $cedantSelect.prop('disabled', true);
                        $nextBtn.prop('disabled', true).text('Next');
                        showCedantStatus('Failed to load cedants. Please try again.',
                            'alert-danger');
                        toastr.error('Failed to load cedants. Please try again.');
                    }
                });
            });

            $cedantSelect.on('change', function() {
                updateNextButtonState();
                if ($(this).val()) {
                    hideCedantStatus();
                }
            });

            $('#next-save-btn').click(function() {
                $("#new_cover_form").submit();
            });

            $("#new_cover_form").validate({
                errorClass: "errorClass",
                rules: {
                    customer_id: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $nextBtn.prop('disabled', true).text('Validating...');
                    form.submit();
                }
            });

            $('#newCoverModal').on('hidden.bs.modal', function() {
                $("#new_cover_form")[0].reset();
                $cedantSelect.val(null).trigger('change.select2');
                $cedantSelect.empty().append('<option value="">-- Select Cedant --</option>');
                $cedantSelect.prop('disabled', false);
                $nextBtn.prop('disabled', true).text('Next');
                hideCedantStatus();
                $("#ccustomer_id-error").hide();
            });
        });
    </script>
@endpush
