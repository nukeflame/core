@extends('layouts.intermediaries.base')
@section('header', 'Claims')
@section('content')
@if ($message = Session::get('success'))

@endif
<div class="mt-5">
    
</div>
<a  href="{{ route('dwnload.quotepdf',[$quotation_details->quote_no,$quotation_details->version])}}" class="btn btn-sm btn-outline-success"  role="button"><i class="fa fa-arrow-down" aria-hidden="true"></i>  Download Quote  </a>
 <a  href="{{ route('mail.quote',[$quotation_details->quote_no,$quotation_details->version])}}" class="btn btn-sm btn-outline-success"  role="button"><i class="fa fa-envelope" aria-hidden="true"></i>  Send to Mail </a>
                                            <br><br>
@include('quotation')

@endsection
