@props([
    'icon' => '',
    'target' => '',
    'label' => '',
    'active' => false,
    'badge' => null,
    'badgeColor' => 'primary',
])

@php
    $activeClass = $active ? 'active' : '';
    $ariaSelected = $active ? 'true' : 'false';
    $tabId = $target . '-tab-trigger';
    $tabTarget = '#' . $target . '-tab';
@endphp

<li class="nav-item" role="presentation">
    <button class="nav-link {{ $activeClass }}" id="{{ $tabId }}" data-bs-toggle="tab"
        data-bs-target="{{ $tabTarget }}" type="button" role="tab" aria-controls="{{ $target }}-tab"
        aria-selected="{{ $ariaSelected }}">
        @if ($icon)
            <i class="{{ $icon }} me-2"></i>
        @endif
        <span>{{ $label }}</span>
        @if ($badge !== null)
            <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} ms-2">
                {{ $badge }}
            </span>
        @endif
    </button>
</li>
