@props(['label' => '', 'for' => null])
<div class="">
    <label {{ $for ? "for=\"{$for}\"" : '' }} class="">
        {{ $label }}
    </label>
    <div class="">
        {{ $slot }}
    </div>
</div>
