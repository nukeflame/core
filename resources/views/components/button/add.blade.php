@props(['id'])
<button id={{ $id }} {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-primary']) }}>
    {{ $slot }}
</button>
