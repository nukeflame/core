@props(['cover', 'endorsementNarration'])

<nav>
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
</nav>

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
