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
                        <h5 class="text-start my-2">Travel details</h5>
                    </div>
                    <div>
                        <x-button.back class="btn-sm mx-3 float-end" id="loc_back"><i class="fa fa-up-left"></i></x-button>
                    </div>
                </div>
                <hr>

                <div id="travel_details">
                    <form id="travel_details_form" enctype="multipart/form-data">
                        <input type="hidden" name="quote_no" value="{{$quote->quote_no}}">
                        @csrf
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="travel_det" name="origin" id="origin" req="required" inputLabel="Origin">
                                    <option value="">Select source</option>
                                    @foreach($countries as $country)
                                        <option @if($travel_details->origin == $country->iso) selected @endif value="{{$country->iso}}">{{$country->iso}}-{{$country->nicename}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="travel_det" name="destination" id="destination" req="required" inputLabel="Destination">
                                    <option value="">Select destination</option>
                                    @foreach($countries as $country)
                                        <option @if($travel_details->destination == $country->iso) selected @endif  value="{{$country->iso}}">{{$country->iso}}-{{$country->nicename}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-Input class="travel_det" name="persons_number" value="{{$travel_details->no_of_persons}}" id="persons_number"  inputLabel="No.of Persons" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="travel_det" name="plan" id="plan" req="required" inputLabel="Tavel Plan" disabled>
                                    <option value="">Select plan</option>
                                    @foreach($plans as $plan)
                                        <option  @if($travel_details->plan == $plan->plan_code) selected @endif value="{{$plan->plan_code}}">{{$plan->plan_descr}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                            <div class="col-md-9">
                                <x-TextArea onkeyup="this.value=this.value.toUpperCase()" class="travel_det" name="travel_reason" id="travel_reason"  inputLabel="Travel Reason" req="required" >
                                {{$travel_details->reason}}
                                </x-TextArea>
                            </div>
                        </div>

                        <hr>
                        <div>
                            <x-button.submit class="col-md-2 float-end" id="save_travel">Save</x-button>
                        </div>
                    </form>
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
        
        var source = {!! json_encode($source)  !!}
        var quote = {!! json_encode($quote)  !!}
        $("#loc_back").on('click', function(){
            window.location="{{ route('travel_details')}}"+"?qstring={{Crypt::encrypt("quote_no=$quote->quote_no&source=$source")}}"           
       
        })


        $('#save_travel').on('click', function(e){
            e.preventDefault()
            let form = $("#travel_details_form")
            form.validate({
                errorElement: 'span',
                errorClass: 'text-danger fst-italic',
                highlight: function(element, errorClass) {
                },
                unhighlight: function(element, errorClass) {
                }
            });
            if (form.valid() === true){
                let data = $('#travel_details_form').serialize()
                $.ajax({
                    type: 'POST',
                    data:data,
                    url: "{!! route('edit_travel_details')!!}",
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

        
    });

</script>
@endsection