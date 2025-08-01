<label>{{$inputLabel }}@if($req == "required")<font style="color:red;">*</font>@endif</label>
<input {{ $attributes->merge(['class'=>'form-control checkempty',]) }} type="email" {{$req}}/>