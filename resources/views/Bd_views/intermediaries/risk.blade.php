@extends('layouts.intermediaries.base')
@section('content') 
<div id="premium_info" class="card-body p-4 step" style="display: none">
            <div class="text-center">
             <h5 class="card-title font-weight-bold pb-2">Quoute View</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h5 class="card-title font-weight-bold pb-2">Premium overview</h5>
                                <hr>
                                <div class="card p-2 mb-2">
                                    <div>
                                        <small>Basic premium</small>
                                    </div>
                                    <div>
                                        <h4 class="text-success font-weight-bold">Mlw. <span id="basic_premium"></span> </h4>
                                    </div>
                                    <div>
                                        <small>Total premium</small>
                                    </div>
                                    <div>
                                        <h4 class="text-success font-weight-bold">Mlw. <span id="total_premium"></span></h4>
                                    </div>
                                </div>
                                <h5>Added benefits</h5>
                                <div class="card" id="ext" style="display:none">
                                    <div class="card-body" id="extensions">
                                        <div class="alert alert-info">No added benefits</div>
                                    </div>
                                </div>

                            </div>
                            <form id="premiumdetails">
                                <input type="text" name="total_prem" id="total_prem_field" hidden>
                                <!-- <input type="text" name="total_prem" id="total_prem_field" hidden> -->
                                <input class="form-control" type="text" id="basic_prem" name="basic_prem" hidden>
                            </form>
                            <div class="card-footer">
                                <button class="btn btn-outline-primary"><span class="fa fa-file"></span> Download quote</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <h5 class="card-title font-weight-bold pt-2">Benefits</h5>
                            <hr>
                            
                            @foreach($sections as $section)
                                <div class="border border-bottom align-items-center section">
                                    <div class="d-flex justify-content-between py-3">
                                        <div class="px-3">
                                            <span class="fa fa-paperclip fa-2x"></span>
                                        </div>
                                        <div class="benefit_amt">
                                            <div class="d-block font-weight-bold benefit_desc">
                                                {{$section->description}}
                                            </div>
                                            <div class="d-block">
                                                <span class="benefit_amount"  benefit_rate="{{$section->rate}}"></span>
                                            </div>
                                        </div>
                                        <div class="px-3">
                                            <button class="btn btn-sm btn-outline-secondary add_benefit" benefit_id="{{$section->code}}" benefit_desc="{{$section->description}}" benefit_amount="">
                                                <span class="fa fa-plus"></span>
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <p>
                        <button class="btn btn-outline-default" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><span class="fa fa-chevron-down"></span> View Risk Details</button>
                    </p>
                    <div class="collapse" id="collapseExample">
                    <div class="card card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-borderless table-hover" id="risks_data_table" width="100%">
                                <thead >
                                    <tr>
                                        <td>Registration number</td>
                                        <td>Make</td>   
                                        <td>Model</td>
                                        <td>Body Type</td>
                                        <td>Value</td>
                                        <td>Action</td>            
                                    </tr>
                                </thead>
                            
                                <tbody>
                                    
                                </tbody>
                                
                            </table>
                        </div> 
                    </div>
                    </div>
                </div>
            </div>
        </div>

@endsection
@section('page_scripts')
@endsection