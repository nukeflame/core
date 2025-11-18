@props(['cover', 'customer', 'actionable'])

<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <h1 class="page-title fw-semibold fs-18 mb-0">Cover Details</h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="/" class="text-decoration-none">
                        <i class="ri-home-4-line me-1"></i>Covers
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('customer.dtl', ['customer_id' => $customer->customer_id]) }}"
                        class="text-decoration-none">
                        {{ Str::title($customer->name) }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $cover->cover_no }}
                </li>
            </ol>
        </nav>
    </div>

</div>

{{-- <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb"
    style="z-index: 1020;">
    <div class="container-fluid py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="/" class="text-decoration-none">
                                <i class="ri-home-4-line me-1"></i>Covers
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('customer.dtl', ['customer_id' => $customer->customer_id]) }}"
                                class="text-decoration-none">
                                {{ Str::title($customer->name) }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $cover->cover_no }}
                        </li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h4 class="mb-1 fw-semibold d-flex align-items-center gap-2">
                            {{ $cover->cover_no }}
                            <span class="badge bg-primary-subtle text-primary fs-6">
                                {{ $cover->transaction_type }}
                            </span>
                        </h4>
                        <div class="text-muted small d-flex flex-wrap gap-3">
                            <span>
                                <i class="ri-calendar-line me-1"></i>
                                {{ formatDate($cover->cover_from) }} - {{ formatDate($cover->cover_to) }}
                            </span>
                            <span>
                                <i class="ri-building-line me-1"></i>
                                {{ $customer->name }}
                            </span>
                            <span>
                                <i class="ri-money-dollar-circle-line me-1"></i>
                                {{ $cover->currency_code ?? 'USD' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 text-end">
                <x-cover.status-badge :status="$cover->verified" />

                @if ($cover->verified === 'P')
                    <div class="mt-2">
                        <span class="badge bg-warning-subtle text-warning">
                            <i class="ri-time-line me-1"></i>Awaiting Verification
                        </span>
                    </div>
                @elseif($cover->verified === 'A')
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="ri-check-line me-1"></i>
                            Verified on {{ formatDate($cover->updated_at) }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> --}}
