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
                                @foreach($bond_types as $bon)
                                    <option @if($bond->bond_type == $bon->bond_type) selected @endif value="{{$bond->bond_type}}">{{$bond->description}}</option>
                                @endforeach
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>

                        <div class="col-md-9">
                            <x-TextArea onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="bond_desc" id="bond_desc"  inputLabel="Bond Description" req="required">
                            {{$bond->bond_description}}
                            </x-TextArea>
                        </div>
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer" id="employer" value="{{$bond->employer}}" inputLabel="Employer" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer_addr1" id="employer_addr1" value="{{$bond->employer_address1}}"  inputLabel="Employer Address 1" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer_addr2" id="employer_addr2" value="{{$bond->employer_address2}}"  inputLabel="Employer Address 2" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer_addr3" id="employer_addr3" value="{{$bond->employer_address3}}"  inputLabel="Employer Address 3" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal" id="principal" value="{{$bond->principal1}}"  inputLabel="Principal" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal_addr1" id="principal_addr1" value="{{$bond->principal1_address1}}"  inputLabel="Principal Address 1" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal_addr2" id="principal_addr2" value="{{$bond->principal1_address2}}" inputLabel="Principal Address 2" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal_addr3" id="principal_addr3" value="{{$bond->principal1_address3}}" inputLabel="Principal Address 3" req="required"/>
                        </x-QuotationInputDiv>
                    </div>

                    <hr>
                    <div class="row">
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety" id="surety" value="{{$bond->surety}}" inputLabel="Surety" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety_addr2" id="surety_addr2" value="{{$bond->surety_address2}}" inputLabel="Surety Address 2" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety_addr3" id="surety_addr3" value="{{$bond->surety_address3}}" inputLabel="Surety  Address 3" req="required"/>
                        </x-QuotationInputDiv>
                    </div>

                    <hr>
                    <div class="row">
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" value="{{$bond->project_name}}" name="project_bond" id="project_bond"  inputLabel="Project" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-NumberInput class="bond_det" name="valid_days" id="valid_days"  value="{{$bond->valid_days}}" inputLabel="Valid Days" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-DateInput class="bond_det" name="signing_date" id="signing_date"  value="{{$bond->signing_date}}" inputLabel="Signing Date" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-NumberInput class="bond_det" name="bond_per" id="bond_per" value="{{$bond->percent_of_contract_val}}" inputLabel="Percentage Contract Amount" req="required" />
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="bond_det" name="contract_amt" id="contract_amt"  value="{{number_format($bond->contract_value)}}" inputLabel="Contract Amount" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="bond_det" name="bond_sum" id="bond_sum"  value="{{number_format($bond->sum_insured)}}" inputLabel="Sum Insured" req="required" />
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="bond_det" name="loc_equiv" id="loc_equiv"  value="{{number_format($bond->local_sum_insured)}}" inputLabel="Local Equivalent" req="required" />
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-SearchableSelect class="bond_det" name="bond_security" id="bond_security" req="required" inputLabel="Security Type">
                                <option value="">Select security type</option>
                                <option  @if($bond->security_type == "L") selected @endif value="L" >Logbook</option>
                                <option  @if($bond->security_type == "T") selected @endif value="T" >Title deed</option>
                                <option  @if($bond->security_type == "C") selected @endif value="C" >Cash deposit</option>
                                <option  @if($bond->security_type == "O") selected @endif value="O" >Other security type</option>
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>
                        
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"  value="{{$bond->security_descr}}" name="security_desc" id="security_desc"  inputLabel="Security Description" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="bond_det" name="security_val" id="security_val"  value="{{number_format($bond->security_val)}}" inputLabel="Security Value" req="required" onchange="this.value=numberWithCommas(removeCommas(this.value))"/>
                        </x-QuotationInputDiv>
                        
                        <x-QuotationInputDiv>
                            <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"  value="{{$bond->signatority_1}}" name="signatority" id="signatority"  inputLabel="Signatority" req="required"/>
                        </x-QuotationInputDiv>
                    </div>
                    
                    <hr>
                    <div>
                        <x-button.submit class="col-md-2 float-end" id="save_bond">Save</x-button>
                    </div>
                </form>
                
            </div>
        </div> 
        <div class="card-footer">

        </div>
    </div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function(){
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
                    url: "{!! route('edit_bond_details')!!}",
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
                sum_insured = percentage*removeCommas(contract_amt)/100;
                $("#bond_sum").val(numberWithCommas(sum_insured))
                $("#loc_equiv").val(numberWithCommas(sum_insured))
            }
        })
    

        $("#contract_amt").on('change', function(){
            let contract_amt = $(this).val()
            let percentage = $('#bond_per').val()
            let sum_insured = 0;

            if (percentage > 0) {
                sum_insured = percentage*removeCommas(contract_amt)/100;
                $("#bond_sum").val(numberWithCommas(sum_insured))
                $("#loc_equiv").val(numberWithCommas(sum_insured))
            }
        })
    });

</script>
@endsection