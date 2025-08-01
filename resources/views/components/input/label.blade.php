@props(['label', 'for'])
<div class="">
    <label for="{{ $for }}"
           class="">
           {{ $label }}
    </label>
    <div class="">
        {{ $slot }}
    </div>
</div>