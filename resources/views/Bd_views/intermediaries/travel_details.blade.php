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
                        @if(!is_null($travel_details) )
                            @if($travellers < $travel_details->no_of_persons)
                                <a href="{{ route('travel_schedule', ['qstring'=>Crypt::encrypt('quote_no='.$quote->quote_no.'&source='.$source.'&location=1')]) }}"  id="add_travel_schedule">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-plus"></i> 
                                        Add Schedule
                                    </button>
                                </a>
                            @endif
                        @endif
                        <x-button.back class="btn-sm mx-3 float-end" id="loc_back"><i class="fa fa-up-left"></i></x-button>
                    </div>
                </div>
                <hr>

                @if(!is_null($travel_details))
                    <div id="travels">
                        <div class="bs-example">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a href="#details" class="nav-link active" data-toggle="tab">Travel Details</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#sections" class="nav-link" data-toggle="tab">Sections</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#schedule" class="nav-link" data-toggle="tab">Travel Schedule</a>
                                </li>
                            

                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active table-responsive my-2" id="details">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Origin</th>
                                                <th>Destination</th>
                                                <th>Number of persons</th>
                                                <th>Plan</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    @foreach($countries as $country)
                                                        @if($travel_details->origin == $country->iso)
                                                            {{$country->name}}
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach($countries as $country)
                                                        @if($travel_details->destination == $country->iso)
                                                            {{$country->name}}
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>{{ $travel_details->no_of_persons }}</td>
                                                <td>
                                                    @foreach($plans as $plan)
                                                        @if($travel_details->plan == $plan->plan_code)
                                                            {{$plan->plan_descr}}
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <span type="span" class='text-primary editdetails' title="Edit" style="cursor:pointer;">
                                                        <a href="{{route('show_travel_details',['qstring'=>Crypt::encrypt('quote_no='.$travel_details->quote_no.'&source='.$source)])}}" class="location_det" style="text-decoration:none">                       
                                                            <i class='fa fa-edit'></i>Edit
                                                        </a>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- tab 1 ends here -->

                                <div class="tab-pane fade" id="sections">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="sectiontable">
                                            <thead>
                                                <tr>
                                                    <th>Benefit</th>
                                                    <th>Limit</th>
                                                    <th>Excess</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sections as $sect)
                                                    <tr>
                                                        <td>{{$sect->name}}</td>    
                                                        <td>{{$sect->limit}}</td>
                                                        <td>{{$sect->excess}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- tab2 ends here -->

                                <div class="tab-pane fade" id="schedule">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="scheduletable">
                                            <thead>
                                                <tr>
                                                    <th>Full Name</th>
                                                    <th>Departure Date</th>
                                                    <th>Duration</th>
                                                    <th>Plan</th>
                                                    <th>Premium</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- tab3 ends here -->
                            </div>
                        </div>
                    </div>
                @else
                    <div id="travel_details">
                        <form id="travel_details_form" enctype="multipart/form-data">
                            <input type="hidden" name="quote_no" value="{{$quote->quote_no}}">
                            @csrf
                            <div class="row m-2">
                                <x-QuotationInputDiv>
                                    <x-SearchableSelect class="travel_det" name="origin" id="origin" req="required" inputLabel="Origin">
                                        <option value="">Select source</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->iso}}">{{$country->iso}}-{{$country->nicename}}</option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </x-QuotationInputDiv>
                                
                                <x-QuotationInputDiv>
                                    <x-SearchableSelect class="travel_det" name="destination" id="destination" req="required" inputLabel="Destination">
                                        <option value="">Select destination</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->iso}}">{{$country->iso}}-{{$country->nicename}}</option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </x-QuotationInputDiv>
                                
                                <x-QuotationInputDiv>
                                    <x-Input class="travel_det" name="persons_number" id="persons_number"  inputLabel="No.of Persons" req="required"/>
                                </x-QuotationInputDiv>
                                
                                <x-QuotationInputDiv>
                                    <x-SearchableSelect class="travel_det" name="plan" id="plan" req="required" inputLabel="Tavel Plan">
                                        <option value="">Select plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{$plan->plan_code}}">{{$plan->plan_descr}}</option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </x-QuotationInputDiv>

                                <div class="col-md-9">
                                    <x-TextArea onkeyup="this.value=this.value.toUpperCase()" class="travel_det" name="travel_reason" id="travel_reason"  inputLabel="Travel Reason" req="required" />
                                </div>
                            </div>

                            <hr>
                            <div>
                                <x-button.submit class="col-md-2 float-end" id="save_travel">Save</x-button>
                            </div>
                        </form>
                    </div>
                @endif
                
            </div>
        </div> 
        <div class="card-footer">

        </div>
    </div>
@endsection

@section('page_scripts')
@if(!is_null($travel_details))
    <script>
        let td = {!! $travel_details->no_of_persons !!}
    </script>
@else
    <script>
        let td = 0
    </script>
@endif
<script>
    $(document).ready(function(){
        console.log(td, "Liuri");
        let quote = "{{ $quote->quote_no }}"
        let source = @json($source);

        if (td > 0) {
            console.log("iorygfrvueykjnkm");
            $("#travel_details").hide()
        }else{
            $("#travels").hide()
            $("#add_travel_schedule").hide()
        }

        if (td > 0) {
            $('#scheduletable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                    'url' : '{{ route("get_schedule_travel") }}',
                    'data' : function(d){
                        d.quote_no=quote
                    },
                },
                
                columns: [
                    {data:'full_name',name:'full_name'},
                    {data:'departure_date',name:'departure_date'},
                    {data:'duration',name:'duration'},
                    {data:'plan',name:'plan'},
                    {data:'premium',name:'premium'},
                    {data:'action',name:'action'},
                ]		
            });

            
            $('#sectiontable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                    'url' : '{{ route("get_section_travel") }}',
                    'data' : function(d){
                        d.quote_no=quote
                    },
                },
                
                columns: [
                    {data:'name',name:'name'},
                    {data:'limit',name:'limit'},
                    {data:'excess',name:'excess'}
                ]		
            })
        }
        

        $("#loc_back").on('click', function(){
            if(source == 'client'){
                window.location="{{ route('add_risk_quote_nonmotor')}}"+"?qstring={{Crypt::encrypt("client=$client&source=client&motorflag=N&quote_no=$quote->quote_no")}}";

            } else {
                window.location="{{ route('add_risk_quote_nonmotor')}}"+"?qstring={{Crypt::encrypt("lead=$client&source=lead&motorflag=N&quote_no=$quote->quote_no")}}";

            }
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
                    url: "{!! route('add_travel_details')!!}",
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