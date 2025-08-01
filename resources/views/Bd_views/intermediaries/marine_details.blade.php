@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4">
            <div>
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-start my-2">Marine Declaration</h5>
                    </div>
                    <div>
                        <x-button.back class="mx-3 float-end btn-sm" id="loc_back"><i class="fa fa-up-left"></i></x-button>
                        <a href="{{ route('location_benefit', ['qstring'=>Crypt::encrypt('quote_no='.$quote->quote_no.'&source='.$source.'&location=1')]) }}" id="add_benefit_button">
                            <button type="button" class="btn btn-sm btn-outline-secondary" >
                                <i class="fa fa-plus"></i> 
                                Add Extensions
                            </button>
                        </a>
                    </div>
                </div>
                <hr>
                
                <div id="marine_details">
                    <form id="marine_details_form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="quote_no" value="{{$quote->quote_no}}">
                        @if($class->open_cover == "Y")
                            <div id="open-cover">
                                <div class="row m-2" style="margin-top: 20px;">
                                    <h6>Open Cover Certificate Details</h6>
                                    <x-QuotationInputDiv>
                                        <x-DateInput class="open_cover_dec" name="mac_period_from" id="mac_period_from"  inputLabel="Certificate From:" req="required"/>
                                    </x-QuotationInputDiv>

                                    <x-QuotationInputDiv>
                                        <x-DateInput class="open_cover_dec" name="mac_period_to" id="mac_period_to"  inputLabel="Certificate To:" req="required"/>
                                    </x-QuotationInputDiv>

                                    <x-QuotationInputDiv>
                                        <x-NumberInput class="open_cover_dec" name="mac_cover_days" id="mac_cover_days"  inputLabel="Days Covered:" req="required"/>
                                    </x-QuotationInputDiv>
                                    
                                    <x-QuotationInputDiv>
                                        <x-SearchableSelect class="open_cover_dec" name="financier" id="financier" req="" inputLabel="Financier: (if applicable)">
                                            <option value="">Choose financier</option>
                                        </x-SearchableSelect>
                                    </x-QuotationInputDiv>

                                    <x-QuotationInputDiv>
                                        <x-DateInput class="open_cover_dec" name="ipf_repayment_date" id="ipf_repayment_date"  inputLabel="IPF Repayment Date: (if applicable) " req=""/>
                                    </x-QuotationInputDiv>

                                    <x-QuotationInputDiv>
                                        <x-NumberInput class="open_cover_dec" name="annual_transit_limit" id="annual_transit_limit"  inputLabel="Annual Transit Limit: (if applicable)" req=""/>
                                    </x-QuotationInputDiv>

                                    <x-QuotationInputDiv>
                                        <x-NumberInput class="open_cover_dec" name="limit_per_transit" id="limit_per_transit"  inputLabel="Limit per Transit: (if applicable)" req=""/>
                                    </x-QuotationInputDiv>
                                        
                                    <x-QuotationInputDiv>
                                        <x-SearchableSelect class="open_cover_dec" name="limit_source" id="limit_source" req="" inputLabel="Limit Source">
                                            <option value="">Select source</option>
                                            @foreach($ports as $port)
                                                <option value="{{$port->iso}}">{{$port->iso}}-{{$port->nicename}}</option>
                                            @endforeach
                                        </x-SearchableSelect>
                                    </x-QuotationInputDiv>
                                </div>
                            </div>
                        @endif

                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-NumberInput class="marine_dec" name="item_no" id="item_no"  inputLabel="Item Number" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="product_group" id="product_group" req="required" inputLabel="Product Group">
                                    <option value="">Select product group</option>
                                    @foreach($margroups as $grp)
                                        <option value="{{$grp->group_code}}">{{$grp->description}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="marine_dec" name="cargo_type" id="cargo_type"  inputLabel="Cargo Type" req="required"/>
                            </x-QuotationInputDiv>

                            <div class="col-md-9">
                                <x-TextArea onkeyup="this.value=this.value.toUpperCase()" class="marine_dec" name="marine_desc" id="marine_desc"  inputLabel="Description" req="required">

                                </x-TextArea>
                            </div>
                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <h6>Mode of cover</h6>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="radio" value="icca" name="icc" class="icc"/> I.C.C (A)
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="radio" value="iccb" name="icc" class="icc"/> I.C.C (B)
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="radio" value="iccc" name="icc" class="icc"/> I.C.C (C)
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="checkbox" name="war" id="war" value="Y" /> WAR
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="checkbox" name="srrc" id="srcc" value="Y" /> S.R.R.C
                            </x-QuotationInputDiv>
                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <h6>Mode of conveyance</h6>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <input type="radio" name="conveyance" id="dc_mode_air" value="mode_air" /> AIR
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="radio" name="conveyance" id="dc_mode_sea" value="mode_sea" /> SEA
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                Rate
                                <input type="text" name="air_sea_rate" id="air_sea_rate" value="" required/> 
                            </x-QuotationInputDiv>

                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="marine_dec" name="survey_agent" id="survey_agent"  inputLabel="Survey Agent" req="required"/>
                            </x-QuotationInputDiv>


                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="package_type" id="package_type" req="required" inputLabel="Packaging Type">
                                    <option value="">Select packaging type</option>
                                    @foreach($packtypes as $type)
                                        <option value="{{$type->pack_type}}">{{$type->description}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="source" id="source" req="required" inputLabel="Source">
                                    <option value="">Select source</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->iso}}">{{$port->iso}}-{{$port->nicename}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="loading_at" id="loading_at" req="required" inputLabel="Loading At">
                                    <option value="">Select loading at</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="destination" id="destination" req="required" inputLabel="Destination">
                                    <option value="">Select destination</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->iso}}">{{$port->iso}}-{{$port->nicename}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="port_of_discharge" id="port_of_discharge" req="required" inputLabel="Port of Discharge">
                                    <option value="">Select port of discharge</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="marine_dec" name="vessel_name" id="vessel_name"  inputLabel="Vessel" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="transhipment" id="transhipment" req="required" inputLabel="Transhipment">
                                    <option value="">Select transhipment</option>
                                    <option value="N">NO</option>
                                    <option value="Y">YES</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="transhipment_country" id="transhipment_country" req="" inputLabel=" Transhipment Country">
                                    <option value="">Select destination</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->iso}}">{{$port->iso}}-{{$port->nicename}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="transhipment_port" id="transhipment_port" req="" inputLabel="Transhipment Destination">
                                    <option value="">Select port</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="bill_of_landing" id="bill_of_landing"  inputLabel="Bill of Landing No" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-DateInput class="marine_dec" name="bill_of_landing_date" id="bill_of_landing_date"  inputLabel="Issue Date (Bill of Landing)" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-DateInput class="marine_dec" name="invoice_date" id="invoice_date"  inputLabel="Invoice Date" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-DateInput class="marine_dec" name="date_signed" id="date_signed"  inputLabel="Date Signed" req="required"/>
                            </x-QuotationInputDiv>
                        </div>

                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="insurance_value" id="insurance_value"  inputLabel="Value of Insurance" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="clearing_charges" id="clearing_charges"  inputLabel="Clearing Charges & Internal Freight" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="customs_duty" id="customs_duty"  inputLabel="Customs Duty" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="vat" id="vat"  inputLabel="Value Added Tax(VAT)" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="marine_dec" min="0" max="100" name="profit" id="profit"  inputLabel="Profit(% of C&F)" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="profit_amount" id="profit_amount"  inputLabel="Profit Amount" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="total_sum_insured" id="total_sum_insured"  inputLabel="Total Sum Insured" req="required"/>
                            </x-QuotationInputDiv>
                        </div>
                    </form>

                    <hr>
                    <div>
                        <x-button.submit class="col-md-2 float-end" id="save_marine">Save</x-button>
                    </div>
                </div>
                
                <div id="madtls" class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Goods Description</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Premium</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($madtls as $madtl)
                                <tr>
                                    <td>{{ $madtl->cargo_type }}</td>
                                    <td>
                                        @foreach($ports as $country)
                                            @if(trim($madtl->transit_from) == $country->iso)
                                                {{$country->name}}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        
                                        @foreach($ports as $country)
                                            @if(trim($madtl->transit_to) == $country->iso)
                                                {{$country->name}}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{ number_format($madtl->total_amount, 2) }}</td>
                                    <td>
                                        <span type="span" class='text-primary editdetails' title="Edit" style="cursor:pointer;">
                                            <a href="{{route('show_marine_details',['source'=>$source, 'quote_no'=>$madtl->quote_no])}}" class="location_det" style="text-decoration:none">                       
                                                <i class='fa fa-edit'></i>Edit
                                            </a>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div> 
    </div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function(){
        let marine = {!! $madtls->count() !!}

        if (marine > 0) {
            $("#marine_details").hide()
            $('#add_benefit_button').show()
        }else{
            $("#madtls").hide()
            $('#add_benefit_button').hide()
        }
        /***** fetch loading ports ******/
        $("#source").on('change', function(){
            var source = $('#source').val();
        
            $.ajax({
                url:"{{route('fetch_ports')}}",
                data:{'source':source},
                type:"get",
                success:function(resp){
                    $('#loading_at').empty();
                
                    if (resp.status == 1) {
                        $('#loading_at').empty()
                        $('#loading_at').append($("<option />").val('').text('Select loading at'));
                        $.each(resp.ports, function() {
                            $('#loading_at').append($('<option>').text(this.port_name).attr('value', this.port_code));
                        });
                    }     
                },
                error:function(resp){
                    //alert('error');
                    console.error;
                }
            });
        });
        
        /***** fetch destination ports ******/
        $("#destination").on('change', function(){
            var source = $(this).val();
        
            $.ajax({
                url:"{{route('fetch_ports')}}",
                data:{'source':source},
                type:"get",
                success:function(resp){
                    $('#port_of_discharge').empty();
                
                    if (resp.status == 1) {
                        $('#port_of_discharge').empty()
                        $('#port_of_discharge').append($("<option />").val('').text('Select port of discharge'));
                        $.each(resp.ports, function() {
                            $('#port_of_discharge').append($('<option>').text(this.port_name).attr('value', this.port_code));
                        });
                    }     
                },
                error:function(resp){
                    //alert('error');
                    console.error;
                }
            });
        });

        /***** fetch transhipment ports ******/
        $("#transhipment_country").on('change', function(){
            var source = $(this).val();
        
            $.ajax({
                url:"{{route('fetch_ports')}}",
                data:{'source':source},
                type:"get",
                success:function(resp){
                
                    if (resp.status == 1) {
                        $('#transhipment_port').empty()
                        $('#transhipment_port').append($("<option />").val('').text('Select transhipment destination port'));
                        $.each(resp.ports, function() {
                            $('#transhipment_port').append($('<option>').text(this.port_name).attr('value', this.port_code));
                        });
                    }     
                },
                error:function(resp){
                    //alert('error');
                    console.error;
                }
            });
        });


        $('.icc').on('click', function() {
            icc_type = $(this).val()

            if (icc_type == 'icca') {
                $('#war').prop('checked', true)
                $('#srcc').prop('checked', true)

                $('#war').prop('disabled', true)
                $('#srcc').prop('disabled', true)
            } else {
                $('#war').prop('checked', false)
                $('#srcc').prop('checked', false)

                $('#war').prop('disabled', false)
                $('#srcc').prop('disabled', false)
            }
        })
        

        $("#contract_amt").on('change', function(){
            let contract_amt = $(this).val()
            let percentage = $('#bond_per').val()
            let sum_insured = 0;

            if (percentage > 0) {
                sum_insured = percentage*contract_amt/100;
                $("#bond_sum").val(numberWithCommas(sum_insured))
                $("#loc_equiv").val(numberWithCommas(sum_insured))
                $("#sum_insured_0").val(numberWithCommas(sum_insured))
                $("#sum_insured_0").attr('readonly', true)
            }
        })

        $("#profit").on("change", function(){
            let profit = $(this).val()
            let val_insurance = $('#insurance_value').val()

            let profit_amt = parseFloat(val_insurance) * profit/100
            $("#profit_amount").val(profit_amt)
            $("#profit_amount").trigger('change')
        })

        $("#transhipment").on("change", function(){
            let transhipment = $(this).val()
            
            if (transhipment == "Y") {
                $("#transhipment_country").attr('req',"required")
                $("#transhipment_port").attr('req',"required")
            }else{
                $("#transhipment_country").attr('req',"")
                $("#transhipment_port").attr('req',"")
            }


        })


        $('#insurance_value').keyup(function(){
            compute_total_sum();
        })

        $('#clearing_charges').keyup(function(){
            compute_total_sum();
        })

        $('#customs_duty').keyup(function(){
            compute_total_sum();
        })

        $('#vat').keyup(function(){
            compute_total_sum();
        })
        
        $('#profit_amount').change(function(){
            compute_total_sum();
        })

        function compute_total_sum(){
            val_insurance = $('#insurance_value').val()
            clearing = $('#clearing_charges').val()
            duty = $('#customs_duty').val()
            vat = $('#vat').val()
            profit_amt = $('#profit_amount').val()

            val_insurance = parseFloat(val_insurance) || 0;
            clearing = parseFloat(clearing) || 0;
            duty = parseFloat(duty) || 0;
            vat = parseFloat(vat) || 0;
            profit_amt=parseFloat(profit_amt) || 0;

            tot_amt = val_insurance + clearing + duty + vat + profit_amt;

            tot_amt = numberWithCommas(tot_amt)
            $('#total_sum_insured').val(tot_amt)
        }


        $('#save_marine').on('click', function(e){
            e.preventDefault()
            let form = $("#marine_details_form")
            form.validate({
                errorElement: 'span',
                errorClass: 'text-danger fst-italic',
                highlight: function(element, errorClass) {
                },
                unhighlight: function(element, errorClass) {
                }
            });
            if (form.valid() === true){
                let data = $('#marine_details_form').serialize()
                $.ajax({
                    type: 'POST',
                    data:data,
                    url: "{!! route('add_marine_details')!!}",
                    success:function(data){
                        if (data.status == 1) {
                            toastr.success('Details saved.', {
                                timeOut: 5000
                            });
                            window.location.reload()
                        }else{
                            swal.fire({
                                icon: "error",
                                title: "Failed",
                                html:"<h6>Check details and try again</h6>"
                            });
                        }

                    }
                })
            }
        })

        $("#loc_back").on('click', function(){
            var source = @json($source);

            source == 'client' ? 
            window.location="{{ route('add_risk_quote_nonmotor')}}"+"?qstring={{Crypt::encrypt("client=$client&source=client&motorflag=N&quote_no=$quote->quote_no")}}" :
            window.location="{{ route('add_risk_quote_nonmotor')}}"+"?qstring={{Crypt::encrypt("lead=$client&source=lead&motorflag=N&quote_no=$quote->quote_no")}}";
        })
    });

</script>
@endsection