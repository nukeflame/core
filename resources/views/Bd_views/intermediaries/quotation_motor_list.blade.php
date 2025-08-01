@extends('layouts.intermediaries.base')
@section('content')

<!-- Page Content -->
<div class="container">

  <h1 class="fw-light text-center text-lg-start mt-4 mb-0">Motor Insurance products</h1>

  <hr class="mt-2 mb-5">

  <div class="row text-center text-lg-start">

    <div class="col-lg-3 col-md-4 col-12">
      <div class="card">
        <a href="{{route('agent.add_risk_quote',['class'=>70,'motorflag'=>'Y','clnt'=>$clientno])}}" class="d-block mb-4 h-100">
          <img class="img-fluid img-thumbnail" src="https://source.unsplash.com/aiwuLjLPFnU/400x300" alt="">
        </a>
        <div class="mx-2">
          <h5 class="text-lg-start mb-2">Motor private</h5>
          <p>This policy covers loss or damage to the vehicle and its accessories including legal liability to third parties </p>
          <a href="{{route('agent.add_risk_quote',['class'=>70,'motorflag'=>'Y','clnt'=>$clientno])}}">
            <button class="btn btn-sm btn-outline-primary mb-4">Get quote</button>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-12">
      <div class="card">
        <a href="{{route('agent.add_risk_quote',['class'=>70,'motorflag'=>'Y','clnt'=>$clientno])}}" class="d-block mb-4 h-100">
          <img class="img-fluid img-thumbnail" src="https://source.unsplash.com/0TmYp58QVNQ/400x300" alt="">
        </a>
        <div class="mx-2">
          <h5 class="text-lg-start mb-2">Motor commercial</h5>
          <p>This policy covers damage to the vehicle and passengers in respect of death or bodily injury arising from the use of the motor vehicle.</p>
          <a href="{{route('agent.add_risk_quote',['class'=>70,'motorflag'=>'Y','clnt'=>$clientno])}}">
            <button class="btn btn-sm btn-outline-primary mb-4">Get quote</button>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-12">
      <div class="card">
        <a href="#" class="d-block mb-4 h-100">
          <img class="img-fluid img-thumbnail" src="https://source.unsplash.com/A53o1drQS2k/400x300" alt="">
        </a>
        <div class="mx-2">
          <h5 class="text-lg-start mb-2">Motor trade</h5>
          <p>It provides cover for motor traders against risks during the testing and movement of the vehicle on its own wheel.</p>
          <button class="btn btn-sm btn-outline-primary mb-4">View More</button>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-12">
      <div class="card">
        <a href="#" class="d-block mb-4 h-100">
          <img class="img-fluid img-thumbnail" src="https://source.unsplash.com/pFq73TQlpvo/400x300" alt="">
        </a>
        <div class="mx-2">
          <h5 class="text-lg-start">Motor cycle</h5>
          <p>Motor cycles are a great, effective way to get around, but we all know they can be risky. </p>
          <button class="btn btn-sm btn-outline-primary mb-4">View More</button>
        </div>
      </div>
    </div>
  </div>

</div>
 

@endsection
@section('page_scripts')

@endsection