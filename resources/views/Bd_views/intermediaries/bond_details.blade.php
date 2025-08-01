@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4">
            <div id="bond_details">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-start my-2">Bond details</h5>
                    </div>
                    <div>
                        <x-button.back class="mx-3 float-end" id="loc_back"><i class="fa fa-up-left"></i></x-button>
                    </div>
                </div>
                <hr>
                <form id="bond_details_form" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-2">
                        <input type="hidden" name="quote_no" value="{{$quote->quote_no}}">
                        <x-QuotationInputDiv>
                            <x-SearchableSelect class="bond_det" name="bond_type" id="bond_type" req="required" inputLabel="Bond Type">
                                <option value="">Select bond type</option>
                                @foreach($bond_types as $bond)
                                    <option value="{{$bond->bond_type}}">{{$bond->description}}</option>
                                @endforeach
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>

                        <div class="col-md-9">
                            <x-TextArea onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="bond_desc" id="bond_desc"  inputLabel="Bond Description" req="required">

                            </x-TextArea>
                        </div>
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer" id="employer"  inputLabel="Employer" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer_addr1" id="employer_addr1"  inputLabel="Employer Address 1" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer_addr2" id="employer_addr2"  inputLabel="Employer Address 2" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer_addr3" id="employer_addr3"  inputLabel="Employer Address 3" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal" id="principal"  inputLabel="Principal" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal_addr1" id="principal_addr1"  inputLabel="Principal Address 1" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal_addr2" id="principal_addr2"  inputLabel="Principal Address 2" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal_addr3" id="principal_addr3"  inputLabel="Principal Address 3" req="required"/>
                        </x-QuotationInputDiv>
                    </div>

                    <hr>
                    <div class="row">
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety" id="surety"  inputLabel="Surety" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety_addr2" id="surety_addr2"  inputLabel="Surety Address 2" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety_addr3" id="surety_addr3"  inputLabel="Surety  Address 3" req="required"/>
                        </x-QuotationInputDiv>
                    </div>

                    <hr>
                    <div class="row">
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="project_bond" id="project_bond"  inputLabel="Project" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-NumberInput class="bond_det" name="valid_days" id="valid_days"  inputLabel="Valid Days" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-DateInput class="bond_det" name="signing_date" id="signing_date"  inputLabel="Signing Date" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-NumberInput class="bond_det" name="bond_per" id="bond_per"  inputLabel="Percentage Contract Amount" req="required" />
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="bond_det" name="contract_amt" id="contract_amt"  inputLabel="Contract Amount" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="bond_det" name="bond_sum" id="bond_sum"  inputLabel="Sum Insured" req="required" />
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="bond_det" name="loc_equiv" id="loc_equiv"  inputLabel="Local Equivalent" req="required" />
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-SearchableSelect class="bond_det" name="bond_security" id="bond_security" req="required" inputLabel="Security Type">
                                <option value="">Select security type</option>
                                <option value="L" >Logbook</option>
                                <option value="T" >Title deed</option>
                                <option value="C" >Cash deposit</option>
                                <option value="O" >Other security type</option>
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>
                        
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="security_desc" id="security_desc"  inputLabel="Security Description" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="bond_det" name="security_val" id="security_val"  inputLabel="Security Value" req="required" onchange="this.value=numberWithCommas(this.value)" />
                        </x-QuotationInputDiv>
                        
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="signatority" id="signatority"  inputLabel="Signatority" req="required"/>
                        </x-QuotationInputDiv>
                    </div>
                    
                    <hr>
                    <div>
                        <x-button.submit class="col-md-2 float-end" id="save_bond">Save</x-button>
                    </div>
                </form>
                
                <div id="bonds" class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bond type</th>
                                <th>Contract Amount</th>
                                <th>% of Contract Amount</th>
                                <th>Sum Insured</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bonds as $bn)
                                <tr>
                                    <td>{{$bn->description}}</td>
                                    <td>{{ number_format($bn->contract_value,2) }}</td>
                                    <td>{{ number_format($bn->percent_of_contract_val,2) }}%</td>
                                    <td>{{ number_format($bn->sum_insured,2) }}</td>
                                    <td>
                                        
                                        <span type="span" class='text-primary editdetails' data-quote='{{$bn->quote_no}}' title="Edit" style="cursor:pointer;">
                                            <a href="{{route('show_bond_details',['qstring'=>Crypt::encrypt('quote_no='.$bn->quote_no.'&source='.$source)])}}" class="location_det" style="text-decoration:none">                       
                                                <i class='fa fa-edit'></i>Edit
                                            </a>
                                        </span>|
                                        <span class="text-primary" title="Add section">
                                            <a href="{{route('add_location_sections',['qstring'=>Crypt::encrypt('quote_no='.$bn->quote_no.'&source='.$source.'&location=1')])}}" class="location_det" style="text-decoration:none">
                                                    <i class='fa fa-circle-plus'></i>Sections
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
        <div class="card-footer">

        </div>
    </div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function(){
        let bond = {!! $bonds->count() !!}

        if (bond > 0) {
            $("#bond_details_form").hide()
        }else{
            $("#bonds").hide()
        }

        

        $('#save_bond').on('click', function(e){
            e.preventDefault()
            let form = $("#bond_details_form")
            form.validate({
                errorElement: 'span',
                errorClass: 'text-danger fst-italic',
                highlight: function(element, errorClass) {
                },
                unhighlight: function(element, errorClass) {
                }
            });
            if (form.valid() === true){
                let data = $('#bond_details_form').serialize()
                $.ajax({
                    type: 'POST',
                    data:data,
                    url: "{!! route('add_bond_details')!!}",
                    success:function(data){
                        if (data.status == 1) {
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

            source == 'client' ? window.location="{{ route('add_risk_quote_nonmotor')}}"+"?qstring={{Crypt::encrypt("client=$client&source=client&motorflag=N&quote_no=$quote->quote_no")}}" :
            window.location="{{ route('add_risk_quote_nonmotor')}}"+"?qstring={{Crypt::encrypt("lead=$client&source=lead&motorflag=N&quote_no=$quote->quote_no")}}";
        })

        

        $("#bond_per").on('change', function(){
            let percentage = $(this).val()
            let contract_amt = $('#contract_amt').val()
            let sum_insured = 0;

            if (contract_amt > 0) {
                sum_insured = percentage*contract_amt/100;
                $("#bond_sum").val(numberWithCommas(sum_insured))
                $("#loc_equiv").val(numberWithCommas(sum_insured))
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
            }
        })
    });

</script>
@endsection