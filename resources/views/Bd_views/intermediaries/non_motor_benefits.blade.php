@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4">
            <div id="section_details">
                <h5 class="text-start my-2">Benefits</h5>
                <hr>
                <form id="section_details_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="policy_no" value="{{ $poldtl->policy_no }}" id="policy_no">
                    <input type="hidden" name="location" value="{{ $location }}" id="location">
                    <div class="" id="benefit_card">
                        <div class="card-body">
                            
                            @if($benefits->count() > 0)
                                <!-- <div class="row">
                                    <div class="col-md-2">
                                        <h6>Benefit</h6>
                                    </div>
                                    <div class="col-md-2">
                                        <h6>Basis</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <h6>Amount</h6>
                                    </div>
                                    <div class="col-md-2">
                                        <h6>Premium</h6>
                                    </div>
                                    <div class="col-md-1">
                                        <h6>Select</h6>
                                    </div>
                                </div> -->
                                <!-- <hr> -->
                                @foreach($benefits as $benefit)
                                        @if($exts->count() > 0)
                                            @if($exts->contains('ben_id', $benefit->ext_code))
                                                @foreach($exts as $ext)
                                                    @if($ext->ben_id == $benefit->ext_code)
                                                    @php
                                                        $benefit->rate = $ext->rate;
                                                        $benefit->ext_amount = $ext->sum;
                                                    @endphp
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif
                                    <div class="row">
                                        <div class="col-md-2 fw-bold">
                                            {{ $benefit->ext_description }}
                                        </div>
                                        <div class="col-md-2">
                                                <label for="rate">Rate</label>
                                            @if($benefit->ext_code_type == 'A')
                                                <input type="number" class="form-control" value="Fixed Amount" readonly>
                                            @elseif($benefit->ext_code_type == 'R')
                                                @if($benefit->rate_basis == 'S')
                                                <input type="number" class="form-control" value="Sum Insured" readonly>
                                                    <!-- Rate<i>(On sum insured)</i> -->
                                                @elseif($benefit->rate_basis == 'P')
                                                <input type="number" class="form-control" value="Basic premium" readonly>
                                                    <!-- Rate<i>(On basic premium)</i> -->
                                                @elseif($benefit->rate_basis == 'L')
                                                    <input type="number" name="rate" class="rate form-control" min_rate="{{$benefit->rate}}" id="rate_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$benefit->rate}}">
                                                @endif
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                            <label for="">Free Limit</label>
                                            <input type="number" name="free_limit" class="free_limit form-control" id="free_limit_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$benefit->free_limit}}" disabled>
                                        </div>
                                        <div class="col-md-2">
                                            <h6>
                                                <label for="">Amount</label>
                                                @if($benefit->ext_code_type == 'A')
                                                    {{ number_format($benefit->ext_amount,2) }}
                                                        <input type="number" name="fixed_amount" class="fixed_amount form-control" id="fixed_amount_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$benefit->ext_amount}}">
                                                @elseif($benefit->ext_code_type == 'R')
                                                    @if($benefit->rate_basis == 'S')
                                                        <input type="number" name="fixed_amount" class="fixed_amount form-control" id="fixed_amount_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$sum}}">
                                                        <!-- {{ number_format($benefit->rate*$sum/100, 2) }} -->
                                                    @elseif($benefit->rate_basis == 'P')
                                                        <input type="number" name="fixed_amount" class="fixed_amount form-control" id="fixed_amount_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$premium}}">
                                                        <!-- {{ number_format($benefit->rate*$premium/100, 2) }} -->
                                                    @elseif($benefit->rate_basis == 'L')
                                                        <input type="number" name="fixed_amount" class="fixed_amount form-control" id="fixed_amount_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$benefit->ext_amount}}">
                                                    @endif
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="col-md-2">
                                                <label for="">Premium</label>
                                                @if($benefit->ext_code_type == 'A')
                                                    {{ number_format($benefit->ext_amount,2) }}
                                                        <input type="number" name="premium" class="fixed_amount form-control" id="premium_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$benefit->ext_amount}}">
                                                @elseif($benefit->ext_code_type == 'R')
                                                    @if($benefit->rate_basis == 'S')
                                            <input type="number" name="premium" class="premium form-control" id="premium_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$sum*$benefit->rate/100}}" readonly>
                                                        <!-- {{ number_format($benefit->rate*$sum/100, 2) }} -->
                                                    @elseif($benefit->rate_basis == 'P')
                                            <input type="number" name="premium" class="premium form-control" id="premium_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$premium*$benefit->rate/100}}" readonly>
                                                        <!-- {{ number_format($benefit->rate*$premium/100, 2) }} -->
                                                    @elseif($benefit->rate_basis == 'L')
                                            <input type="number" name="premium" class="premium form-control" id="premium_{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" benefit_id="{{$benefit->ext_code}}" value="{{$benefit->ext_amount*$benefit->rate/100}}" readonly>
                                                    @endif
                                                @endif
                                        </div>
                                        <div class="col-md-1">
                                            @if($exts->count() > 0)
                                                @if($exts->contains('ben_id', $benefit->ext_code))
                                                <label for="">Add/Remove</label>
                                                    <input class="form-check-input remove_nm_benefit" type="checkbox" value="" benefit_id="{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}" checked>
                                                @else
                                                    <label for="">Add/Remove</label>
                                                    <input class="form-check-input add_nm_benefit" type="checkbox" value="" benefit_id="{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}">
                                                @endif
                                            @else
                                                <label for="">Add/Remove</label>
                                                <input class="form-check-input add_nm_benefit" type="checkbox" value="" benefit_id="{{$benefit->ext_code}}"   benefit_type="{{$benefit->ext_code_type}}">
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            @else
                                <p class="alert alert-info">
                                    There are no extensions for this class.
                                </p>
                            @endif
                        </div>  
                    </div>
                    
                </form>
            </div>
        </div> 
      
        <div class="card-footer">
            <div>
                <x-button.back class="col-md-2 float-end mx-3" id="sections_back">Back</x-button>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
<script>
$(document).ready(function(){
    
    let policy_no = $("#policy_no").val();
    let location = $("#location").val();
    let sum_insured = {{ $sum }};
    let wiba = "{!! $class->wiba_policy !!}"
    let pa = "{!! $class->pa !!}"
    let marine = "{!! $class->marine !!}"
    console.log(wiba);

    $('body').on('click','.add_nm_benefit', function(){
        let ben_id = $(this).attr('benefit_id');
        let ben_type = $(this).attr('benefit_type');
        let premium = 0
        let rate = 0
        let free_limit = 0
        let amount = 0
        if (ben_type == 'A') {
            premium = $("#premium_"+ben_id).val();
        } else {
            rate = $("#rate_"+ben_id).val();
            free_limit = $("#free_limit_"+ben_id).val();
            amount = $("#fixed_amount_"+ben_id).val();

            premium = (amount-free_limit)*rate/100
        }

        if (premium <= 0 && free_limit == 0) {
            $(this).prop('checked', false);
            toastr.warning('Cannot add extension, check data and try again', {timeOut: 5000});
            
        }else{
            $(this).removeClass("add_nm_benefit")
            $(this).addClass("remove_nm_benefit")
            $.ajax({
                type: 'GET',
                data:{'ben_id':ben_id,'location':location,'policy_no':policy_no,'rate':rate, 'sum_insured':amount, 'premium': premium},
                url: "{!! route('stage.nm_ben')!!}",
                success:function(data){
                

                }
            });
        }

    })

    $('.rate, .premium, .fixed_amount').on('change', function(){
        let ben_id = $(this).attr('benefit_id');
        let ben_type = $(this).attr('benefit_type');
        let premium = 0
        let rate = 0
        let free_limit = 0
        let amount = 0
        let min_rate = $("#rate_"+ben_id).attr('min_rate')
        if (ben_type != 'A') {
            rate = $("#rate_"+ben_id).val();
            if (rate< min_rate) {
                toastr.warning('Rate Cannot be less than the minimum', {timeOut: 5000});
                $("#rate_"+ben_id).val(min_rate);
            }
            free_limit = $("#free_limit_"+ben_id).val();
            amount = $("#fixed_amount_"+ben_id).val();

            premium = (amount-free_limit)*rate/100
            $("#premium_"+ben_id).val(premium);
        }
    })

    $('body').on('click','.remove_nm_benefit', function(){
        $(this).removeClass("remove_nm_benefit")
        $(this).addClass("add_nm_benefit")
        
        let ben_id = $(this).attr('benefit_id')
        $(".benefit_"+ben_id).remove()
        $.ajax({
            type: 'GET',
            data:{'ben_id':ben_id,'delete_flag':"Y",'policy_no':policy_no, 'location':location},
            url: "{!! route('stage.nm_ben')!!}",
            success:function(data){
                
            }
        });
    })

    $("#sections_back").on('click', function(){
        window.history.back();
      

    })
})
</script>
@endsection