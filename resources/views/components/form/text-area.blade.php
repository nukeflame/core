<label class="form-label pr-1 fw-bold">
    {{ $inputLabel }}@if ($req == 'required')
        <i style="color:red;">*</i>
    @endif
</label>
<textarea {{ $attributes->merge(['class' => 'form-inputs checkempty']) }} {{ $req }}>{{ $slot }}</textarea>
