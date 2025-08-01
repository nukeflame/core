<label>{{$inputLabel }}@if($req == "required")<font style="color:red;">*</font>@endif</label>
<textarea {{ $attributes->merge(['class'=>'form-control checkempty']) }} {{$req}}>{{ $slot }}</textarea>