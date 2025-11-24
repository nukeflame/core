<nav>
    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card" id="nav-tab"
        role="tablist">

        @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
            <button class="nav-link active" id="nav-schedules-tab" data-bs-toggle="tab" data-bs-target="#schedules-tab"
                type="button" role="tab" aria-selected="true">
                <i class="bx bx-table me-1 align-middle"></i>Schedule Details
            </button>

            <button class="nav-link" id="nav-attachments-tab" data-bs-toggle="tab" data-bs-target="#attachments-tab"
                type="button" role="tab" aria-selected="false" tabindex="-1">
                <i class="bx bx-file me-1 align-middle"></i>File & Support Docs
            </button>

            <button class="nav-link" id="nav-clauses-tab" data-bs-toggle="tab" data-bs-target="#clauses-tab"
                type="button" role="tab" aria-selected="false" tabindex="-1">
                <i class="bx bx-medal me-1 align-middle"></i>Policy Clauses
            </button>
        @endif

        @if (in_array($cover->type_of_bus, ['TPR', 'TNP']))
            <button class="nav-link" id="nav-ins-classes-tab" data-bs-toggle="tab" data-bs-target="#ins-classes-tab"
                type="button" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-award-line me-1 align-middle"></i>Classes
            </button>
        @endif

        <button class="nav-link @if (in_array($cover->type_of_bus, ['TPR', 'TNP'])) active @endif" id="nav-reinsurers-tab"
            data-bs-toggle="tab" data-bs-target="#reinsurers-tab" type="button" role="tab"
            aria-selected="@if (in_array($cover->type_of_bus, ['TPR', 'TNP'])) true @else false @endif"
            @if (!in_array($cover->type_of_bus, ['TPR', 'TNP'])) tabindex="-1" @endif>
            <i class="ri-team-line me-1 align-middle"></i>Reinsurers
        </button>

        @if ($cover->no_of_installments > 1)
            <button class="nav-link" id="nav-installments-tab" data-bs-toggle="tab" data-bs-target="#installments-tab"
                type="button" role="tab" aria-selected="false" tabindex="-1">
                <i class="bi bi-archive me-1 align-middle"></i>Installment Details
            </button>
        @endif

        @if (count($endorsementNarration) > 0)
            <button class="nav-link" id="nav-endorse-narration-tab" data-bs-toggle="tab"
                data-bs-target="#endorse-narration-tab" type="button" role="tab" aria-selected="false"
                tabindex="-1">
                <i class="bx bx-file-blank me-1 align-middle"></i>Narration
            </button>
        @endif

        <button class="nav-link" id="nav-approvals-tab" data-bs-toggle="tab" data-bs-target="#approvals-tab"
            type="button" role="tab" aria-selected="false" tabindex="-1">
            <i class="bx bx-check me-1 align-middle"></i>Approvals
        </button>

        @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
            <button class="nav-link" id="nav-debits-tab" data-bs-toggle="tab" data-bs-target="#debits-tab"
                type="button" role="tab" aria-selected="false" tabindex="-1">
                <i class="bx bx-credit-card me-1 align-middle"></i>Cedant
            </button>
        @endif

        <button class="nav-link" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#docs-tab" type="button"
            role="tab" aria-selected="false" tabindex="-1">
            <i class="ri-printer-line me-1 align-middle"></i>Print-outs
        </button>
    </div>
</nav>

{{-- <nav>
    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card" id="nav-tab"
        role="tablist">
        @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
            <x-cover.tab-item icon="ri-table-line" target="schedules" label="Schedules" :active="true" />
            <x-cover.tab-item icon="ri-file-line" target="attachments" label="Documents" />
            <x-cover.tab-item icon="ri-file-text-line" target="clauses" label="Clauses" />
            <x-cover.tab-item icon="ri-team-line" target="reinsurers" label="Reinsurers" />

            @if ($cover->no_of_installments > 1)
                <x-cover.tab-item icon="ri-calendar-check-line" target="installments" label="Installments" />
            @endif
        @endif

        @if (in_array($cover->type_of_bus, ['TPR', 'TNP']))
            <x-cover.tab-item icon="ri-team-line" target="reinsurers" label="Reinsurers" :active="true" />
            <x-cover.tab-item icon="ri-award-line" target="ins-classes" label="Classes" />
        @endif

        @if (count($endorsementNarration) > 0)
            <x-cover.tab-item icon="ri-file-list-line" target="endorse-narration" label="Narration" />
        @endif

        <x-cover.tab-item icon="ri-check-double-line" target="approvals" label="Approvals" />

        @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
            <x-cover.tab-item icon="ri-money-dollar-circle-line" target="debits" label="Debits" />
        @endif

        <x-cover.tab-item icon="ri-printer-line" target="documents" label="Print-outs" />
    </div>
</nav> --}}

<style>
    .reinsurers-details-card {
        border-bottom: 2px solid #e9ecef !important;
        gap: 0.25rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .reinsurers-details-card::-webkit-scrollbar {
        height: 4px;
    }

    .reinsurers-details-card::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .reinsurers-details-card .nav-link {
        transition: all 0.2s ease;
        /* padding: 0px;
        margin-top: 0px; */
    }

    .reinsurers-details-card .nav-link:hover {
        color: #e1251b;
    }

    .reinsurers-details-card .nav-link span {
        font-size: 14px;
        font-weight: 500;
    }

    /* .reinsurers-details-card .nav-link i {
        font-size: 15px;
    } */
</style>
