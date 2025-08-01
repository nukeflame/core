@extends('layouts.intermediaries.base')
@section('content')

<div id="app">
<v-app>
  <faqs-agent/>
</v-app>

</div>
<script src="{{asset('js/app.js') }}"></script>
@endsection