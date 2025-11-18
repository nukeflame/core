@props(['cover', 'endorsementNarration'])

<ul class="nav nav-tabs-custom mb-3" role="tablist">
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
</ul>

<style>
    .nav-tabs-custom {
        border-bottom: 2px solid #e9ecef;
        gap: 0.25rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .nav-tabs-custom::-webkit-scrollbar {
        height: 4px;
    }

    .nav-tabs-custom::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #64748b;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .nav-tabs-custom .nav-link:hover {
        color: #0d6efd;
        background: #f8f9fa;
    }

    .nav-tabs-custom .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: transparent;
    }

    .nav-tabs-custom .nav-link i {
        font-size: 1.1rem;
        vertical-align: middle;
    }
</style>
