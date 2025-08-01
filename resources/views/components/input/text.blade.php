@props([
    'id',
    'type'=>'text'
])
<div class="">
    <input
        type="{{ $type }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
        id="{{ $id }}"
        />
</div>