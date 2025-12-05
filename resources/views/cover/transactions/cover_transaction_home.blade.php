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
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Treaty Transaction Details</h1>
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
            <x-cover.action-card :isCoverStatus="false" :cover="$cover" :endorsementNarration="$endorsementNarration" :isTransaction="$isTransaction" />
        @endif
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

    <div class="card mb-0">
        <div class="card-body">
            <div class="table-responsive">
                <table id="customerAccountsTable" class="table table-striped table-hover" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Treaty</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Endorsement</th>
                            {{-- <th>Insured</th> --}}
                            <th>Status</th>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>Amount Paid</th>
                            <th>Outstanding</th>
                            <th>Period</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            @php
                                $foreignNettAmount = $account->foreign_nett_amount ?? 0;
                                $unallocatedAmount = $account->unallocated_amount ?? 0;

                                $amountPaid = $foreignNettAmount - $unallocatedAmount;
                                $outstandingAmount = $unallocatedAmount;

                                $amountPaid = max(0, $amountPaid);

                                $isFullyPaid = $account->status === 'paid';
                                $isPartiallyPaid = $account->status === 'partial';

                                if ($isFullyPaid) {
                                    $statusClass = 'badge-paid';
                                    $statusText = 'Paid';
                                } elseif ($isPartiallyPaid) {
                                    $statusClass = 'badge-partial';
                                    $statusText = 'Partial';
                                } else {
                                    $statusClass = 'badge-unpaid';
                                    $statusText = 'Not Paid';
                                }

                                $entryType = $account->entry_type_descr ?? '';
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-medium text-primary">
                                        {{ $account->reference ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">
                                        {{ $account->treaty_name ?? ($account->treaty_type ?? 'SURPLUS') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark px-3">
                                        {{ Str::title($entryType) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark capitalize">
                                        {{ Str::title($cover->cover_title) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-medium">
                                        <div class="text-dark">{{ $account->endorsement_no ?? '-' }}</div>
                                    </div>
                                </td>
                                {{-- <td>
                                    <div class="text-truncate" title="{{ $account->insured ?? '' }}">
                                        {{ Str::limit($account->insured ?? '-', 30) }}
                                    </div>
                                </td> --}}
                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $account->currency_code ?? 'KES' }}</span>
                                </td>
                                <td>
                                    {{ number_format($foreignNettAmount, 2) }}
                                </td>
                                <td>
                                    <span class="text-success fw-medium">
                                        {{ number_format($amountPaid, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $outstandingAmount > 0 ? 'text-danger' : 'text-success' }} fw-medium">
                                        {{ number_format($outstandingAmount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ str_pad($account->account_month ?? 1, 2, '0', STR_PAD_LEFT) }}/{{ $account->account_year ?? date('Y') }}
                                    </span>
                                </td>
                                <td>
                                    {{ $account->created_at ? \Carbon\Carbon::parse($account->created_at)->format('jS M Y, H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @switch($entryType)
                                            @case('quarterly-figures')
                                            @case('portfolio')

                                            @case('adjust-commission')
                                                <a href="{{ route('cover.transactions.quarterly-figures', [
                                                    'coverNo' => $cover->cover_no,
                                                    'refNo' => $account->reference,
                                                    'endorsementNo' => $account->endorsement_no,
                                                ]) }}"
                                                    class="btn btn-outline-primary"
                                                    title="View {{ Str::title(str_replace('-', ' ', $entryType)) }}">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            @break

                                            @case('profit-commission')
                                                <a href="{{ route('cover.transactions.profit-commission', [
                                                    'coverNo' => $cover->cover_no,
                                                    'refNo' => $account->reference,
                                                    'endorsementNo' => $account->endorsement_no,
                                                ]) }}"
                                                    class="btn btn-outline-primary" title="View Profit Commission">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            @break

                                            @default
                                        @endswitch
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-inbox fs-1 d-block mb-3"></i>
                                            <p class="mb-0">No transaction records found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($accounts->isNotEmpty())
                            <tfoot class="table-light">
                                @php
                                    $totalForeignNett = $accounts->sum('foreign_nett_amount') ?? 0;
                                    $totalUnallocated = $accounts->sum('unallocated_amount') ?? 0;
                                    $totalAmountPaid = $totalForeignNett - $totalUnallocated;
                                @endphp
                                <tr>
                                    <th colspan="7" class="text-end">Page Totals:</th>
                                    <th>{{ number_format($totalForeignNett, 2) }}</th>
                                    <th class="text-success">{{ number_format($totalAmountPaid, 2) }}</th>
                                    <th class="{{ $totalUnallocated > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($totalUnallocated, 2) }}
                                    </th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Modals -->
        @include('cover.modals.quarterly-figures')
        @include('cover.modals.add-profit-commission')
        @include('cover.modals.add-portfolio')
        @include('cover.modals.adjust-comission')
    @endsection

    @push('script')
        <script>
            $(document).ready(function() {
                var $tbody = $('#customerAccountsTable tbody');
                var hasData = $tbody.find('tr').length > 0 &&
                    $tbody.find('tr td[colspan="13"]').length === 0;

                var table = $('#customerAccountsTable').DataTable({
                    processing: true,
                    pageLength: 25,
                    order: [
                        [11, 'desc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [12]
                    }],
                    language: {
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                        emptyTable: 'No transaction records available',
                        zeroRecords: 'No matching records found'
                    },
                    paging: hasData,
                    searching: hasData,
                    info: hasData,
                    footerCallback: function(row, data, start, end, display) {}
                });

                $('#btnApplyFilter').on('click', function() {
                    var params = $('#filterForm').serialize();
                    var currentUrl = window.location.pathname;
                    window.location.href = currentUrl + '?' + params;
                });

                $('#btnResetFilter').on('click', function() {
                    $('#filterForm')[0].reset();
                    window.location.href = window.location.pathname;
                });
            });
        </script>
    @endpush
