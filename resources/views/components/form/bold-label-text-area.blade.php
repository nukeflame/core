<label><strong>{{$inputLabel }}</strong>@if($req == "required")<font style="color:red;">*</font>@endif</label>
<textarea {{ $attributes->merge(['class'=>'form-control checkempty']) }} {{$req}}>
    {{ old($attributes->get('name'), $value ?? $slot) }}
</textarea>