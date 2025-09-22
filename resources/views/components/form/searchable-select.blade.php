<label class="form-label pr-1 fw-bold">
    {{ $inputLabel }}@if ($req == 'required')
        <i style="color:red;">*</i>
    @endif
</label>
<div class="cover-card">
    <select {{ $attributes->merge(['class' => 'select2 form-inputs checkempty']) }} {{ $req }}>
        {{ $slot }}
    </select>
</div>
