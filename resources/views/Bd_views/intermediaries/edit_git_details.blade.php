@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4">
            <div id="git_details">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-start my-2">Goods In Transit details</h5>
                    </div>
                    <div>
                        <x-button.back class="mx-3 float-end" id="loc_back"><i class="fa fa-up-left"></i></x-button>
                    </div>
                </div>
                <hr>
                    <form id="git_details_form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="quote_no" value="{{$quote->quote_no}}">
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="business" id="business"  inputLabel="Business" req="required" value="{{$gits->business}}" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="physical_location" id="physical_location" req="required" inputLabel="Physical Location" value="{{$gits->phy_location}}"/>
                            </x-QuotationInputDiv>
                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <input  type="checkbox" name="one_off_transit" id="one_off_transit" /> One off transit
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input @if($gits->road_transport == "Y") checked @endif type="checkbox" name="road" id="road_transport" /> Road Transportation
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input @if($gits->rail_transport == "Y") checked @endif type="checkbox" name="rail" id="rail_transport" /> Rail Transportation
                            </x-QuotationInputDiv>
                        </div>

                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="limit_of_liability" id="limit_of_liability"  inputLabel="Limit of Liability" req="required" value="{{ number_format($gits->liability_limit)}}"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="est_annual_carry" id="est_annual_carry"  inputLabel="Estimated Annua Carry" req="required" value="{{ number_format($gits->annual_carry)}}"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="git_dec" name="package_type" id="package_type" req="required" inputLabel="Package Type">
                                    <option value="">Select package type</option>
                                    <option @if($gits->package_type == "P") selected @endif value="P">Packed Goods</option>
                                    <option @if($gits->package_type == "U") selected @endif value="U">Unpacked Goods</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                            <div class="col-md-9">
                                <x-Input class="git_dec" name="goods_descr" id="goods_descr"  inputLabel="Description of Goods Carried" value="{{$gits->goods_descr}}" req="required"/>
                            </div>
                        </div>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="loading_location" id="loading_location" value="{{$gits->loading_location}}"  inputLabel="Loading location" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="unloading_location" id="unloading_location" value="{{$gits->unloading_location}}"  inputLabel="Unloading Location" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="git_dec" name="territory" id="territory" value="{{$gits->territory}}"  inputLabel="Territorial Limit" req="required"/>
                            </x-QuotationInputDiv>
                        </div>
                        
                        <hr>
                        <div class="row">
                            <h6>Freight vehicle details (optional)</h6>
                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="veh_reg_no" id="veh_reg_no" value="{{$gits->reg_no}}"  inputLabel="Vehicle Reg No" req=""/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-NumberInput class="git_dec" name="veh_value" id="veh_value" value="{{$gits->vehicle_value}}"  inputLabel="Value Of Vehicle" req=""/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-NumberInput class="git_dec" name="veh_max_load" id="veh_max_load" value="{{$gits->maximum_load}}"  inputLabel="Maximum Load for Vehicle. (kgs)" req=""/>
                            </x-QuotationInputDiv>
                        </div>

                        <hr>
                        <div>
                            <x-button.submit class="col-md-2 float-end" id="save_git">Save</x-button>
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
        $('#save_git').on('click', function(e){
            e.preventDefault()
            let form = $("#git_details_form")
            form.validate({
                errorElement: 'span',
                errorClass: 'text-danger fst-italic',
                highlight: function(element, errorClass) {
                },
                unhighlight: function(element, errorClass) {
                }
            });
            if (form.valid() === true){
                let data = $('#git_details_form').serialize()
                $.ajax({
                    type: 'POST',
                    data:data,
                    url: "{!! route('edit_git_details')!!}",
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

        $('#one_off_transit').click(function() {
            checked = $(this).prop('checked')
            aoc_limit = $('#limit_of_liability').val()

            if (checked == true) {
                $('#est_annual_carry').prop('readonly', true)
                $('#est_annual_carry').val(aoc_limit)
            } else {
                $('#est_annual_carry').prop('readonly', false)
            }
        })

        $('#limit_of_liability').change(function() {
            checked = $('#one_off_transit').prop('checked')
            limit = removeCommas($(this).val())
            $(this).val(numberWithCommas(limit))

            if (checked == true) {
                $('#est_annual_carry').val(numberWithCommas(limit))
            }
        })
    });

</script>
@endsection