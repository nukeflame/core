@extends('layouts.intermediaries.base')
@section('content') 
<div class="rowd">
<x-button.back class="mx-3 ml-auto mb-3" id="loc_back"><i class="fa fa-up-left"></i></x-button>
</div>
<div class="row">
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Premium Summary       
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                        <div>
                            <small>Currency</small>
                        </div>
                    </div>

                    <div class="col-sm">
                        <div>
                            <h6 class="text-success font-weight-bold"><span id="selcurr">{{$curren->currency}}</span> </h6>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm">
                        <div>
                            <small>Basic Premium</small>
                        </div>
                    </div>

                    <div class="col-sm">
                        <div>
                            <h6 class="text-success font-weight-bold text-align-right"><span id="basic_premium">{{ number_format($premiums->basicprem, 2, '.', ',') }}</span> </h6>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm">
                        <div>
                            <small>Benefit Amount</small>
                        </div>
                    </div>

                    <div class="col-sm">
                        <div>
                            <h6 class="text-success font-weight-bold" ><span id="benefit_total">{{ number_format($premiums->benefitprem, 2, '.', ',') }}</span> </h6>
                        </div>
                    </div>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-sm">
                        <div>
                            <small>Total Premium</small>
                        </div>
                    </div>
                    <div class="col-sm">
                        <div>
                            <h6 class="text-success font-weight-bold"><span id="total_premium">{{ number_format($premiums->totalprem, 2, '.', ',') }}</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <div class="col-md-6">
        <div class="card" id="benefit_card">
            <div class="card-header">
                Optional Benefits
            </div>
            <div class="card-body">
                <table class="table" id="opt_benefit">
                    <thead>
                        <tr>
                        <th scope="col">Benefit</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Select</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>  
        </div>

    </div>
</div>
<div class="card mt-4" id="">
    <!-- <div class="card-header">
  
    </div> -->
    <div class="card-body">
        <div class="container mt-5">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Benefits Summary</a>
            </li>
            <!-- <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Discounts</a>
            </li> -->
        
        </ul>

        <!-- Tab panes -->
        <div class="tab-content mt-2" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                <table class="table table- striped  table-hover" id="benefits_table" width="100%">
                    <thead class="">
                        <tr>
                            <td>Registration number</td>
                            <td>Description</td>   
                            <td>Premium</td>
                            
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <h4>Discounts</h4>
            <p>This is the content of the Profile tab.</p>
            </div> -->
        
        </div>
        </div>
    </div>
</div>


    


@endsection
@section('page_scripts')
<script type="text/javascript">
    $(document).ready(function() {
             // Code to be executed when the DOM is ready
             var cls ="{{$modtls->class}}"
             var policy_no ="{{$modtls->policy_no}}"
             var reg_no ="{{$modtls->reg_no}}"


             getsection(cls,policy_no,reg_no);
             $('#benefits_table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax:{
                        'url' : '{{ route("getbenefits") }}',
                        'data' : function(d){
                                var policy_no= "{{$modtls->policy_no}}"
                                d.policy_no=policy_no
                            },
                    },
                    
                    columns: [
                        {data:'reg_no',name:'reg_no'},
                        {data:'description',name:'description'},
                        {data:'premium',name:'premium',render: $.fn.dataTable.render.number( ',', '.', 2)},
                        
                        ]		
                    });

   });

    function getsection(cls,policy_no,reg_no){
            $.ajax({
                type: 'GET',
                data:{'cls':cls,'policy_no':policy_no,'reg_no':reg_no},
                url: "{!! route('agent.fetchsections')!!}",
                success:function(data){
                    console.log(data.sections)
                    console.log(data.modtl)
                     $("#opt_benefit tbody").empty()
                    // $("#covtype").append($("<option />").val('').text('Choose cover type'));
                    var length = data.sections.length;
                    if (data.sections && data.sections.length > 0) {
                    $.each(data.sections, function() {
                        console.log(this.grp_code)
                        var basis = this.rate_basis;
                        var rate_amount = this.rate_amount;
                        var bsp = data.modtl.annual_prem;
                        var sum_insured = data.modtl.sum_insured;
                        var benamt = 0;
                        if(basis == 'S'){
                            benamt=rate_amount*sum_insured/100;

                        }else if(basis == 'P'){
                            benamt=rate_amount*bsp/100;


                        }
                        var checked=""
                        if ($.inArray(this.item_code, data.selected_bens) !== -1) {
                           checked='checked';
                        }

                        if(benamt > 0){
                            $("#opt_benefit tbody").append(
                            '<tr class="section">'
                                + '<td>'
                                    +'<div class="benefit_amt">'
                                        +'<div class="d-block font-weight-bold benefit_desc">'
                                            + this.description
                                        +'</div>'
                                    +'</div>'
                                +'</td>'
                                +'<td align="right">'
                                    +'<div class="benefit_amt">'
                                        +'<div class="d-block">'
                                            +`<span class="benefit_amount"  basis=${this.rate_basis}>${benamt}</span>`
                                        +'</div>'
                                    +'</div>'
                                +'</td>'
                                +'<td align="right">'
                                    +'<div class="form-check">'
                                        +`<input class="form-check-input add_benefit" type="checkbox" value="" benefit_id=${this.item_code} benefit_desc=${this.description} benefit_rate=${this.rate_amount} benefit_amount="" ${checked}>`
                                    +'</div>'
                                +'</td>'
                            +'</tr>'
                            )

                        }
                       
                    });
                }else{
                    $("#opt_benefit thead").empty()
                    $("#opt_benefit tbody").append('<tr><td colspan="3">Benefits Not Applicable</td></tr>');
                }
                }
            });
    }
     // add benefit on the quote level
     $('body').on('click','.add_benefit', function(){
        var deleteflag ='Y';
        if($(this).is(":checked")){
            deleteflag='N';
        }
          var policy_no ="{{$modtls->policy_no}}"
          var reg_no ="{{$modtls->reg_no}}"

            let ben_id = $(this).attr('benefit_id');
            let ben_desc = $(this).attr('benefit_desc');
            let benrate = $(this).attr('benefit_rate');
            let ben_amount =  $(this).parents('tr.section').find('.benefit_amount').text()
            ben_amount = removeCommas(ben_amount)
            $.ajax({
                    type: 'GET',
                    data:{'ben_id':ben_id,'policy_no':policy_no,'ben_amount':ben_amount,'rate':benrate,'section':ben_id,'deleteflag':deleteflag,'reg_no':reg_no},
                    url: "{!! route('add.benefit')!!}",
                    success:function(data){
                        if(data.status == 1){
                            $('#total_premium').text(data.totalprem)
                            $('#basic_premium').text(data.basicprem)
                            $('#benefit_total').text(data.benefitprem)


                        }else{

                        }
                        var table = $('#benefits_table').DataTable()
                       table.ajax.reload();
                    
                    }
                   
        });


    })
    $("#loc_back").on('click', function(){
        window.history.back();
     })
</script>
<style>
   .rowd {
        display: flex;
        justify-content: flex-end;
    }
</style>
@endsection

