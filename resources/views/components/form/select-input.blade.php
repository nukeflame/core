<label>{{$inputLabel }}@if($req == "required")<font style="color:red;">*</font>@endif</label>
<select {{ $attributes->merge(['class'=>'form-control checkempty']) }}  {{$req}}>
    {{ $slot }}
</select>