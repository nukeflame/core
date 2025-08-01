<label>{{$inputLabel }}@if($req == "required")<font style="color:red;">*</font>@endif</label>
<select {{ $attributes->merge(['class'=>'select2 form-inputs checkempty']) }}  {{$req}}>
    {{ $slot }}
</select>