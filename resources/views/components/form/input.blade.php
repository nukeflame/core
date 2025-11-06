@if ($inputLabel)
    <label class="form-label pr-1 fw-bold">
        {{ $inputLabel }}
        @if ($req === 'required')
            <i style="color:red;">*</i>
        @endif
    </label>
@endif

<input {{ $attributes->merge(['class' => 'form-inputs checkempty']) }} type="text"
    {{ $req === 'required' ? 'required' : '' }} />
