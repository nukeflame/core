{{-- resources/views/components/cover/tabs-navigation.blade.php --}}
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
            <button class="nav-link" id="nav-debit-items-tab" data-bs-toggle="tab" data-bs-target="#debit-items-tab"
                type="button" role="tab" aria-selected="false" tabindex="-1">
                <i class="bx bx-receipt me-1 align-middle"></i>Debit Items </button>
        @endif

        <button class="nav-link @if (in_array($cover->type_of_bus, ['TPR', 'TNP'])) active @endif" id="nav-reinsurers-tab"
            data-bs-toggle="tab" data-bs-target="#reinsurers-tab" type="button" role="tab"
            aria-selected="@if (in_array($cover->type_of_bus, ['TPR', 'TNP'])) true @else false @endif"
            @if (!in_array($cover->type_of_bus, ['TPR', 'TNP'])) tabindex="-1" @endif>
            <i class="bx bx-palette me-1 align-middle"></i>Reinsurers
        </button>

        @if (in_array($cover->type_of_bus, ['TPR', 'TNP']))
            <button class="nav-link" id="nav-ins-classes-tab" data-bs-toggle="tab" data-bs-target="#ins-classes-tab"
                type="button" role="tab" aria-selected="false" tabindex="-1">
                <i class="bx bx-award me-1 align-middle"></i>Insurance Classes
            </button>
        @endif

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
            <i class="bx bx-file-blank me-1 align-middle"></i>Print-outs
        </button>
    </div>
</nav>
