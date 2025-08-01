@extends('layouts.intermediaries.base')
@section('content')
<div class="card mt-2">
        <div class="card-header">
           <strong>Product Listing</strong>
        </div>
        <div class="card-body mb-0 pb-0">

          <div class="row text-center text-lg-start">

            <div class="col-lg-3 col-md-4 col-12">
              <div class="card">
                <a href="{{route('add_risk_quote_nonmotor',['class'=>40,'motorflag'=>'N'])}}" class="d-block mb-4 h-100">
                  <img class="img-fluid img-thumbnail" src="https://source.unsplash.com/BdTtvBRhOng/400x300" alt="">
                </a>
                <div class="mx-2">
                  <h5 class="text-lg-start mb-2">Fire industrial</h5>
                  <p>This policy covers loss or damage to the property insured caused by fire, earthquake, lightening. Buildings. 
                    It covers Furniture, office Equipment and machinery</p>
                  <a href="{{route('add_risk_quote_nonmotor',['class'=>40,'motorflag'=>'N'])}}">
                    <button class="btn btn-sm btn-outline-primary mb-4">Get quote</button>
                  </a>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-12">
              <div class="card">
                <a href="#" class="d-block mb-4 h-100">
                  <img class="img-fluid img-thumbnail" src="https://source.unsplash.com/4_TYsMnML60/400x300" alt="">
                </a>
                <div class="mx-2">
                  <h5 class="text-lg-start mb-2">Goods in transit</h5>
                  <p>This covers Loss of or damage to the property by an accident or theft following collision or overturning of the vehicle or conveyance</p>
                  <a href="#">
                    <button class="btn btn-sm btn-outline-primary mb-4">Get quote</button>
                  </a>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-12">
              <div class="card">
                <a href="#" class="d-block mb-4 h-100">
                  <img class="img-fluid img-thumbnail" src="https://source.unsplash.com/6Mxb_mZ_Q8E/400x300" alt="">
                </a>
                <div class="mx-2">
                  <h5 class="text-lg-start mb-2">Travel insurance</h5>
                  <p>A short-term policy covering medical expenses and financial losses that could arise while you are away from home on a trip, whether business or recreation.</p>
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
</div>
 

@endsection
@section('page_scripts')

@endsection