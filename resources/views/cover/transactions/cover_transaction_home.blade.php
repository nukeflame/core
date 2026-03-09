@extends('layouts.app')

@push('styles')
    <style>
        .stats-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-tpr {
            background-color: #6f42c1;
        }

        .badge-tnp {
            background-color: #9775d4;
        }

        .badge-fpr {
            background-color: #fd7e14;
        }

        .badge-fnp {
            background-color: #fda94e;
        }

        .badge-drn {
            background-color: #dc3545;
        }

        .badge-crn {
            background-color: #28a745;
        }

        .amount-positive {
            color: #28a745;
        }

        .amount-negative {
            color: #dc3545;
        }

        .filter-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }

        .filter-card .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.8rem;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            background-color: rgba(255, 255, 255, 0.95);
            border: none;
        }

        /* Additional styles for better table display */
        .text-truncate {
            max-width: 150px;
        }

        .badge-paid {
            background-color: #28a745;
        }

        .badge-unpaid {
            background-color: #dc3545;
        }

        .badge-partial {
            background-color: #ffc107;
            color: #212529;
        }

        .h-82 {
            height: 82% !important;
        }

        #customerAccountsTable tbody tr.account-row-clickable {
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        #customerAccountsTable tbody tr.account-row-clickable:hover {
            background-color: rgba(13, 110, 253, 0.06);
        }

        #customerAccountsTable .amount-cell {
            text-align: right;
            font-family: 'JetBrains Mono', 'Consolas', monospace;
            font-weight: 600;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    @php
        $backEndorsementNo = $cover->orig_endorsement_no ?: $cover->endorsement_no;
        $backToCoverUrl = route('cover.CoverHome', ['endorsement_no' => $backEndorsementNo]) . '#reinsurers-tab';
        $treatyBusinessType = match ($cover->type_of_bus ?? null) {
            'TPR' => 'Proportional',
            'TNP' => 'Non-Proportional',
            default => 'N/A',
        };
    @endphp
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Treaty Transactions</h1>
            <p class="text-muted mb-0 fs-12">View and manage transaction activity for this treaty cover.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Cover</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Treaty</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ firstUpper($customer->name) }}</li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Transactions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $cover->cover_no }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row row-cols-12 mx-0 mb-2">
        @if ($actionable)
            <x-cover.action-card :isCoverStatus="false" :cover="$cover" :endorsementNarration="$endorsementNarration" :isTransaction="$isTransaction" :transactionsUrl="route('cover.transactions.index', ['coverNo' => $cover->cover_no])"
                :backToCoverUrl="route('cover.CoverHome', [
                    'endorsement_no' => $cover->orig_endorsement_no ?: $cover->endorsement_no,
                ]) . '#reinsurers-tab'" />
        @endif
    </div>

    <div class="card mb-2 border">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div>
                    <span class="text-muted fs-12 d-block">Cover No</span>
                    <span class="fw-semibold">{{ $cover->cover_no ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-muted fs-12 d-block">Treaty Type</span>
                    <span class="fw-semibold">{{ $cover->treaty_type ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-muted fs-12 d-block">Treaty Classification</span>
                    <span
                        class="badge {{ $treatyBusinessType === 'Non-Proportional' ? 'bg-warning' : ($treatyBusinessType === 'Proportional' ? 'bg-primary' : 'bg-secondary') }}">
                        {{ $treatyBusinessType }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-0">
        <div class="col-xl-3 col-md-6 mb-0">
            <div class="card stats-card h-82">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bx bx-file fs-30"></i>
                        </div>
                        <div>
                            <div class="text-muted fs-14">Total Records</div>
                            <div class="fs-4 fw-bold">{{ number_format($stats['total_records'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-0">
            <div class="card stats-card h-82">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-danger bg-opacity-10 text-danger me-3">
                            <i class="bx bx-up-arrow fs-30"></i>
                        </div>
                        <div>
                            <div class="text-muted fs-14">Total Debits</div>
                            <div class="fs-4 fw-bold">{{ number_format($stats['total_debits'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-0">
            <div class="card stats-card h-82">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bx bx-down-arrow fs-30"></i>
                        </div>
                        <div>
                            <div class="text-muted fs-14">Total Credits</div>
                            <div class="fs-4 fw-bold">{{ number_format($stats['total_credits'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-0">
            <div class="card stats-card h-82">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bx bx-time fs-30"></i>
                        </div>
                        <div>
                            <div class="text-muted fs-14">Outstanding Balance</div>
                            <div class="fs-4 fw-bold">{{ number_format($stats['total_unallocated'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row-cols-12">
        <div class="card mb-2 custom-card border col">
            <div class="card-body pt-0">
                <nav>
                    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                        id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-debit-items-tab" data-bs-toggle="tab"
                            data-bs-target="#debit-items-tab" type="button" role="tab" aria-controls="debit-items-tab"
                            aria-selected="true">
                            <i class="bx bx-table me-1 align-middle"></i>Transactions
                            {{-- <span class="badge bg-primary ms-1" id="debitItemsCount">{{ $totalDebitItems }}</span> --}}
                        </button>

                        <button class="nav-link" id="nav-reinsurers-tab" data-bs-toggle="tab"
                            data-bs-target="#reinsurers-tab" type="button" role="tab" aria-controls="reinsurers-tab"
                            aria-selected="false" style="visibility: hidden;">
                            <i class="ri-building-2-line me-1"></i> Reinsurers
                        </button>

                        <button class="nav-link" id="nav-cedant-tab" data-bs-toggle="tab" data-bs-target="#cedant-tab"
                            type="button" role="tab" aria-controls="cedant-tab" aria-selected="false"
                            style="visibility: hidden;">
                            <i class="bx bx-briefcase me-1"></i> Cedant
                        </button>

                        <button class="nav-link" id="nav-approvals-tab" data-bs-toggle="tab" data-bs-target="#approvals-tab"
                            type="button" role="tab" aria-controls="approvals-tab" aria-selected="false"
                            style="visibility: hidden;">
                            <i class="bx bx-medal me-1 align-middle"></i>Approvals
                            <span class="badge bg-warning ms-1"></span>
                        </button>

                        <button class="nav-link" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#docs-tab"
                            type="button" role="tab" aria-controls="docs-tab" aria-selected="false"
                            style="visibility: hidden;">
                            <i class="ri-printer-line me-1 align-middle"></i>Print-outs
                        </button>
                    </div>
                </nav>

                <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                    <div class="tab-pane fade show active" id="debit-items-tab" role="tabpanel"
                        aria-labelledby="nav-debit-items-tab">
                        <div class="card border-0 shadow-none">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
                                    <table id="customerAccountsTable" class="table table-bordered table-hover w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="3%">#</th>
                                                <th width="10%">Reference</th>
                                                <th width="10%">Treaty</th>
                                                <th width="10%">Type</th>
                                                <th width="12%">Title</th>
                                                <th width="8%">Endorsement</th>
                                                <th width="10%">Posting Quarter</th>
                                                <th width="6%">Currency</th>
                                                <th width="10%">Exchange Rate</th>
                                                <th width="8%">Period</th>
                                                <th width="10%">Created At</th>
                                                <th width="8%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="8" class="text-end">Page Totals:</th>
                                                <th>0.00</th>
                                                <th colspan="3"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('cover.modals.quarterly-figures', [
        'itemCodes' => $itemCodes,
        'classGroups' => $classGroups,
        'businessClasses' => $businessClasses,
        'treatyClasses' => $treatyClasses,
        'taxRates' => $taxRates,
    ])
    @include('cover.modals.add-profit-commission')
    @include('cover.modals.add-portfolio')
    @include('cover.modals.adjust-comission')
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var transactionDataRaw = @json($accountsDataTable);
            var transactionData = Array.isArray(transactionDataRaw) ? transactionDataRaw : Object.values(
                transactionDataRaw || {});
            var hasData = transactionData.length > 0;
            var $tableEl = $('#customerAccountsTable');

            function updateDeleteButtonsState(apiInstance) {
                var dt = apiInstance || ($.fn.DataTable.isDataTable($tableEl) ? $tableEl.DataTable() : null);
                if (!dt) {
                    return;
                }

                var filteredRows = dt.rows({
                    search: 'applied'
                });
                var visibleDataRows = (filteredRows.data && typeof filteredRows.data === 'function') ?
                    filteredRows.data().length :
                    0;

                $tableEl.find('.js-delete-transaction').each(function() {
                    var $btn = $(this);
                    if (visibleDataRows <= 1) {
                        $btn.addClass('d-none').prop('disabled', true);
                    } else {
                        $btn.removeClass('d-none').prop('disabled', false);
                    }
                });
            }

            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            var table = $('#customerAccountsTable').DataTable({
                processing: true,
                data: transactionData,
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All']
                ],
                lengthChange: true,
                order: [
                    [10, 'desc']
                ],
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'reference',
                        render: function(data) {
                            return '<span class="fw-medium text-primary">' + escapeHtml(data) +
                                '</span>';
                        }
                    },
                    {
                        data: 'treaty_name',
                        render: function(data) {
                            return '<span class="fw-medium text-dark">' + escapeHtml(data) +
                                '</span>';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<span class="badge ' + escapeHtml(row.entry_type_badge_class) +
                                ' px-3">' + escapeHtml(row.entry_type_label) + '</span>';
                        }
                    },
                    {
                        data: 'cover_title',
                        render: function(data) {
                            return '<span class="text-dark capitalize">' + escapeHtml(data) +
                                '</span>';
                        }
                    },
                    {
                        data: 'endorsement_no',
                        render: function(data) {
                            return '<div class="fw-medium"><div class="text-dark">' + escapeHtml(
                                    data) +
                                '</div></div>';
                        }
                    },
                    {
                        data: 'quarter',
                        render: function(data) {
                            return '<span class="text-dark px-3">' + escapeHtml(data) + '</span>';
                        }
                    },
                    {
                        data: 'currency_code',
                        render: function(data) {
                            return '<span class="badge bg-secondary">' + escapeHtml(data) +
                                '</span>';
                        }
                    },
                    {
                        data: 'exchange_rate',
                        render: function(data, type) {
                            if (type === 'sort' || type === 'type') {
                                return Number(data || 0);
                            }
                            return Number(data || 0).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'period',
                        render: function(data) {
                            return '<span class="badge bg-info text-dark">' + escapeHtml(data) +
                                '</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(data, type, row) {
                            if (type === 'sort' || type === 'type') {
                                return row.created_at_sort || 0;
                            }
                            return escapeHtml(data);
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!row.view_url) {
                                return '';
                            }

                            var actions = '<div class="btn-group btn-group-sm" role="group">' +
                                '<a href="' + escapeHtml(row.view_url) +
                                '" class="btn btn-outline-primary" title="View ' + escapeHtml(row
                                    .entry_type_label) +
                                '"><i class="bx bx-show"></i></a>' +
                                '<a href="' + escapeHtml(row.view_url) +
                                '" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary" title="Open in New Tab">' +
                                '<i class="bx bx-link-external"></i></a>';

                            if (row.can_delete) {
                                actions +=
                                    '<button type="button" class="btn btn-outline-danger js-delete-transaction" title="Delete Transaction"' +
                                    ' data-delete-url="' + escapeHtml(row.delete_url) + '"' +
                                    ' data-endorsement-no="' + escapeHtml(row.endorsement_no) +
                                    '"' +
                                    ' data-entry-type="' + escapeHtml(row.entry_type) + '">' +
                                    '<i class="bx bx-trash"></i></button>';
                            }

                            actions += '</div>';
                            return actions;
                        }
                    }
                ],
                columnDefs: [{
                    targets: [0],
                    orderable: false,
                    searchable: false
                }],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    emptyTable: 'No transaction records available',
                    zeroRecords: 'No matching records found'
                },
                paging: hasData,
                searching: hasData,
                info: hasData,
                footerCallback: function() {
                    var api = this.api();
                    var pageTotal = api.column(8, {
                        page: 'current'
                    }).data().reduce(function(sum, value) {
                        return sum + (parseFloat(value) || 0);
                    }, 0);

                    var footerCell = api.column(8).footer();
                    if (footerCell) {
                        footerCell.innerHTML = pageTotal.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }

                    updateDeleteButtonsState(api);
                },
                createdRow: function(row, data) {
                    if (data.view_url) {
                        $(row).addClass('account-row-clickable').attr('data-view-url', data.view_url);
                    }
                }
            });

            updateDeleteButtonsState(table);

            $('#btnApplyFilter').on('click', function() {
                var params = $('#filterForm').serialize();
                var currentUrl = window.location.pathname;
                window.location.href = currentUrl + '?' + params;
            });

            $('#btnResetFilter').on('click', function() {
                $('#filterForm')[0].reset();
                window.location.href = window.location.pathname;
            });

            $('#customerAccountsTable tbody').on('click', 'tr[data-view-url]', function(e) {
                if ($(e.target).closest('a, button, input, select, textarea, .btn-group').length) {
                    return;
                }

                var viewUrl = $(this).data('view-url');
                if (viewUrl) {
                    window.location.href = viewUrl;
                }
            });

            $(document).on('click', '.js-copy-reference', function() {
                var reference = $(this).data('reference');
                if (!reference) return;

                var notify = function(message, type) {
                    type = type || 'info';
                    if (typeof toastr !== 'undefined') {
                        toastr[type](message);
                        return;
                    }
                    alert(message);
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(reference)
                        .then(function() {
                            notify('Reference copied: ' + reference, 'success');
                        })
                        .catch(function() {
                            notify('Unable to copy reference automatically', 'warning');
                        });
                    return;
                }

                notify('Clipboard API not available on this browser', 'warning');
            });

            $(document).on('click', '.js-delete-transaction', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $btn = $(this);
                const deleteUrl = $btn.data('delete-url');
                const endorsementNo = $btn.data('endorsement-no');
                const entryType = $btn.data('entry-type');

                const doDelete = function() {
                    $.ajax({
                            url: deleteUrl,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            data: {
                                endorsement_no: endorsementNo,
                                entry_type_descr: entryType,
                            },
                        })
                        .done(function(response) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message ||
                                    'Transaction deleted successfully');
                            }
                            setTimeout(() => window.location.reload(), 300);
                        })
                        .fail(function(xhr) {
                            const message = xhr.responseJSON?.message ||
                                'Failed to delete transaction';
                            if (typeof toastr !== 'undefined') {
                                toastr.error(message);
                            } else {
                                alert(message);
                            }
                        });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Delete transaction?',
                        text: 'This action will remove the selected transaction record and related generated endorsement.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Yes, delete it',
                        cancelButtonText: 'Cancel',
                    }).then(function(result) {
                        if (result.isConfirmed) doDelete();
                    });
                } else if (confirm('Delete this transaction?')) {
                    doDelete();
                }
            });
        });
    </script>
@endpush
