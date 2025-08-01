<!-- next button component -->
@props([
    'id'=>"",
])
<button
      id={{$id}}
      {{ $attributes->merge(['type' => 'button' ,'class'=>'btn btn-outline-secondary']) }}>
     Back
</button>   