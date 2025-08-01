@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4">
            <div id="wiba_details">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-start my-2">WIBA Details</h5>
                    </div>
                    <div>
                        <a href="{{ route('location_benefit', ['location'=>1, 'quote_no'=>$quote->quote_no]) }}"  id="add_benefit_button">
                            <button type="button" class="btn btn-outline-secondary">
                                <i class="fa fa-plus"></i> 
                                Add Benefits
                            </button>
                        </a>
                        <x-button.back class="mx-3 float-end" id="loc_back"><i class="fa fa-up-left"></i></x-button>
                    </div>
                </div>
                <hr>

                <form id="wiba_details_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="quote_no" value="{{$quote->quote_no}}">
                    <div id="occupation_div">
                        <div id="detail">
                            <div class="row my-2">
                                <x-QuotationInputDiv>
                                    <x-Input class="wiba_dec"   name="occupation[]" onkeyup="this.value = this.value.toUpperCase()" id="occupation_0"  inputLabel="Occupation of Employees" req="required"/>
                                </x-QuotationInputDiv>
                                
                                <x-QuotationInputDiv>
                                    <x-Input class="wiba_dec"  name="persons[]" id="persons_0" onkeyup="this.value=numberWithCommas(this.value)"  inputLabel="No. of Employees" req="required"/>
                                </x-QuotationInputDiv>

                                <x-QuotationInputDiv>
                                    <x-Input class="wiba_dec" class="rate" name="rate[]" id="rate_0"  inputLabel="Rate" req="required"/>
                                </x-QuotationInputDiv>

                                <x-QuotationInputDiv>
                                    <label class="required">Est. Annual Earnings</label>
                                    <div class="input-group">
                                        <input  name="earnings[]" id="earnings_0" required class="wiba_dec form-control earnings" onkeyup="this.value=numberWithCommas(this.value)" />
                                        <span class="btn btn-primary" id="add_employee">&plus;</span>
                                    </div>
                                </x-QuotationInputDiv>
                            </div>
                        </div>
                    </div>
                    <hr>
                    
  
                    <div class="row ml-0 mr-0 form-group">
                        <x-QuotationInputDiv>
                            <label class="required">Limit per Person</label>
                            <input type="text" name="limit_person" class="form-control" onkeyup="this.value=numberWithCommas(this.value)" value="" id="limit_person" required>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv> 
                            <label class="required">Limit per Occurence/Event</label>
                            <input type="text" class="form-control" name="limit_occurence" id="limit_occurence" onkeyup="this.value=numberWithCommas(this.value)" value="" required>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <label class="required">Limit per One Period</label>
                            <input type="text" class="form-control rate" name="limit_year" onkeyup="this.value=numberWithCommas(this.value)" value="" id="limit_year" required>
                        </x-QuotationInputDiv>
                    </div>
                    <div>
                        <x-button.submit class="col-md-2 float-end" id="save_wiba">Save</x-button>
                    </div>
                </form>
                
                <div id="wibas" class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Occupation</th>
                                <th>Number of employees</th>
                                <th>Annual Earnings</th>
                                <th>Premium</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                             @foreach($wibas as $pa)
                                <tr>
                                    <td>{{ $pa->occupation }}</td>
                                    <td>{{ $pa->number_of_employees }}</td>
                                    <td>{{ number_format($pa->annual_earnings, 2) }}</td>
                                    <td>{{ number_format($pa->premium, 2) }}</td>
                                    <td>
                                        
                                        <span type="span" class='text-secondary editdetails' data-quote='{{$pa->quote_no}}' title="Edit" style="cursor:pointer;">
                                            <i class='fa fa-edit'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </span>
                                        <span class="text-primary" title="Add section">
                                            <a href="{{route('add_location_sections',['location'=>1, 'quote_no'=>$pa->quote_no])}}" class="location_det">
                                                    <i class='fa fa-circle-plus'></i>
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
        let wiba = {!! $wibas->count() !!}

        if (wiba > 0) {
            $("#wiba_details_form").hide()
        }else{
            $("#wibas").hide()
            $("#add_benefit_button").hide()
        }


        $('#save_wiba').on('click', function(e){
            e.preventDefault()
            let form = $("#wiba_details_form")
            form.validate({
                errorElement: 'span',
                errorClass: 'text-danger fst-italic',
                highlight: function(element, errorClass) {
                },
                unhighlight: function(element, errorClass) {
                }
            });
            if (form.valid() === true){
                let data = $('#wiba_details_form').serialize()
                $.ajax({
                    type: 'POST',
                    data:data,
                    url: "{!! route('add_wiba_details')!!}",
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
            ;
        })

        
        var counter = 0;
        $('#add_employee').on('click',function(){
            var occupation = $('#occupation_'+counter).val()
            var persons = $('#persons_'+counter).val()
            var rate = $('#rate_'+counter).val()
            var earnings = $('#earnings_' + counter).val()

            if (occupation == '' || persons == '' || rate == '' || earnings == '') {
                toastr.warning('Kindly fill the fields before moving on to the next row', {timeOut: 5500});
            } else {
                counter += 1;

                $('#detail').append(
                    '<div class="row ml-0 mr-0 form-group">' + 
                        '<div class="col-sm-3">' + 
                            '<label class="required">Occupation of Employees</label>' + 
                            '<input type="text" name="occupation[]" class="form-control" onkeyup="this.value = this.value.toUpperCase()" id="occupation_'+ counter +'" required>' + 
                        '</div>' + 

                        '<div class="col-sm-3">' + 
                            '<label class="required">No. of Employees</label>' + 
                            '<input type="text" class="form-control" name="persons[]" id="persons_'+ counter +'" onkeyup="this.value=numberWithCommas(this.value)" required>' + 
                        '</div>' + 

                        '<div class="col-sm-3">' + 
                            '<label class="required">Rate</label>' + 
                            '<input type="text" class="form-control rate" name="rate[]" id="rate_'+ counter +'" required>' + 
                        '</div>' + 

                        '<div class="col-sm-3">' + 
                            '<label class="required">Est. Annual Earnings</label>' + 
                            '<div class="input-group">' + 
                                '<input type="text" class="form-control earnings" name="earnings[]" id="earnings_'+ counter +'" onkeyup="this.value=numberWithCommas(this.value) " required>' +
                                '<span class="btn btn-danger" id="remove_section">&minus;</span>'+
                            '</div>' + 
                        '</div>' + 
                    '</div>'
                );
            }
        });

        
    $(document).delegate('#remove_section', 'click', function(){
        counter--
        $(this).parent().parent().parent().remove();
    });


    });

</script>
@endsection