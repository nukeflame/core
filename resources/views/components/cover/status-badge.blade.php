@props(['status'])

@php
    $statusConfig = [
        'P' => [
            'label' => 'Pending',
            'class' => 'bg-warning-subtle text-warning',
            'icon' => 'ri-time-line',
        ],
        'A' => [
            'label' => 'Approved',
            'class' => 'bg-success-subtle text-success',
            'icon' => 'ri-check-circle-line',
        ],
        'R' => [
            'label' => 'Rejected',
            'class' => 'bg-danger-subtle text-danger',
            'icon' => 'ri-close-circle-line',
        ],
        null => [
            'label' => 'Draft',
            'class' => 'bg-secondary-subtle text-secondary',
            'icon' => 'ri-draft-line',
        ],
    ];

    $config = $statusConfig[$status] ?? $statusConfig[null];
@endphp

<span {{ $attributes->merge(['class' => "badge {$config['class']} px-3 py-2"]) }}>
    <i class="{{ $config['icon'] }} me-1" style="vertical-align: -2px;"></i>
    {{ $config['label'] }}
</span>

<style>
    .badge {
        font-weight: 500;
        font-size: 0.8125rem;
        letter-spacing: 0.02em;
    }
</style>
