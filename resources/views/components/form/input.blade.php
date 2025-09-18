<label class="form-label pr-1 fw-bold">
    {{ $inputLabel }}@if ($req == 'required')
        <font style="color:red;">*</font>
    @endif
</label>
<input {{ $attributes->merge(['class' => 'form-inputs checkempty']) }} type="text" {{ $req }} />
