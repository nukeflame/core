<!-- submit button component -->
@props([
    "id",
])
<button
      id={{$id}}
      {{ $attributes->merge(["type" => "button" ,"class"=>"btn btn-success"]) }}>
      {{ $slot }}
</button>