@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4">
            <div >
                <div class="d-flex justify-content-between">
                    <div id="gpa_details" style="display:none">
                        <h5 class="text-start my-2">Group Personal Accident Details</h5>
                    </div>
                    <div id="pa_details"  style="display:none">
                        <h5 class="text-start my-2">Personal Accident Details</h5>
                    </div>
                    <div>
                        <x-button.back class="mx-3 float-end loc_back"><i class="fa fa-up-left"></i></x-button>
                    </div>
                </div>
                <hr>
                <form id="gpa_details_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="quote_no" value="{{$quote->quote_no}}">
                    <div class="row m-2">
                        
                        <x-QuotationInputDiv>
                            <x-SearchableSelect class="gpa_dec" name="rating_basis" id="rating_basis" req="required" inputLabel="Basis of rating">
                                <option value="" selected>Choose basis</option>
                                <option @if($gpas->multiple_earnings == "F") selected @endif value="F">Fixed Benefits</option>
                                <option @if($gpas->multiple_earnings == "M") selected @endif value="M">Multiple Earnings</option>
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>
                        
                        <x-QuotationInputDiv>
                            <x-Input class="gpa_dec" name="persons_number" id="persons_number" value="{{$gpas->number_of_person}}" inputLabel="No. of Persons" req="required"/>
                        </x-QuotationInputDiv>
                        
                        <x-QuotationInputDiv>
                            <x-SearchableSelect class="gpa_dec" name="salary_type" id="salary_type" req="required" inputLabel="Monthly or Annual Salary">
                                <option value="" selected>Choose type</option>
                                <option  @if($gpas->salary_type == "M") selected @endif value="M">Monthly</option>
                                <option  @if($gpas->salary_type == "A") selected @endif value="A">Annual</option>
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>
                        <x-QuotationInputDiv>
                            <x-Input class="gpa_dec" name="ma_earning" id="ma_earning"  value="{{$gpas->multiple_of_salary}}"  inputLabel="Multiples of Annual Earnings" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="gpa_dec" name="est_earnings" id="est_earnings"   value="{{$gpas->annual_earnings}}"   inputLabel="Estimated Annual Earnings" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-Input class="gpa_dec" name="death_limit" id="death_limit"   value="{{$gpas->death_limit}}"  inputLabel="Death Limit" req="required"/>
                        </x-QuotationInputDiv>
                        
                        <!-- <x-QuotationInputDiv style="display:none">
                            <x-SearchableSelect class="gpa_dec" name="prem_basis" id="prem_basis" req="required" inputLabel="Premium Computation Basis">
                                <option value="" selected>Based on</option>
                                <option selected value="SEC">Section</option>
                                <option value="SCH">Schedule Uploaded</option>
                            </x-SearchableSelect>
                        </x-QuotationInputDiv> -->
                    </div>
                    <hr>
                    <div>
                        <x-button.submit class="col-md-2 float-end" id="save_gpa">Save</x-button>
                    </div>
                </form>
            </div>

            <div  style="display:none">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-start my-2">Personal Accident Details</h5>
                    </div>
                    <div>
                        <x-button.back class="btn-sm mx-3 float-end loc_back" id=""><i class="fa fa-up-left"></i></x-button>
                    </div>
                </div>
                <hr>
                @if($class->pa == "Y" && !is_null($pas))
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add_benefit_button" data-bs-toggle="modal" data-bs-target="#benefit_modal">
                        <i class="fa fa-plus"></i> 
                        Add Accident Benefits
                    </button>
                    <a href="{{ route('location_benefit', ['qstring'=>Crypt::encrypt('quote_no='.$quote->quote_no.'&source='.$source.'&location=1')]) }}">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="add_benefit_button">
                            <i class="fa fa-plus"></i> 
                            Add Extensions
                        </button>
                    </a>
                @endif

                @if(!is_null($pas))
                    <form id="pa_details_edit" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="quote_no" value="{{$quote->quote_no}}">
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="pa_dec_edit" name="idemnity_limit" id="edit_idemnity_limit"  inputLabel="Limit of Idemnity" req="required" onkeyup="this.value=numberWithCommas(this.value)" value="{{number_format($pas->indemnity_limit)}}"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="pa_dec_edit" name="rate" id="edit_rate"  inputLabel="Rate" req="required" value="{{$pas->rate}}"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="pa_dec_edit" name="premium" id="edit_premium"  inputLabel="Premium" req="required" readonly value="{{number_format($pas->premium)}}"/>
                            </x-QuotationInputDiv>
                        </div>
                        <hr>
                        <div>
                            <x-button.submit class="col-md-2 float-end" id="edit_save_pa">Update</x-button>
                        </div>
                    </form>
                @else
                    <form id="pa_details_form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="quote_no" value="{{$quote->quote_no}}">
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="pa_dec" name="idemnity_limit" id="idemnity_limit"  inputLabel="Limit of Idemnity" req="required" onkeyup="this.value=numberWithCommas(this.value)"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="pa_dec" name="rate" id="rate"  inputLabel="Rate" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="pa_dec" name="premium" id="premium"  inputLabel="Premium" req="required" readonly/>
                            </x-QuotationInputDiv>
                        </div>
                        <hr>
                        <div>
                            <x-button.submit class="col-md-2 float-end" id="save_pa">Save</x-button>
                        </div>
                    </form>
                @endif
            </div>
        </div> 
    </div>


    <div class="modal fade" id="benefit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-lg" role="modal">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #cfd7e0 !important">
                    <h5 class="modal-title">Accident Benefits</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form enctype="multipart/form-data" id="pa_benefits_form">
                        @csrf
                        <div class="row">
                            
                            <input type="text" name="quote_no" value="{{ $quote->quote_no }}" hidden>
                        </div>
                        
                        <div id="benefit_selection">
                            <div id="benefitrow_0">
                                <div class="row mx-0 form-group">
                                    <div class="col-md-6">
                                        <label>Accident Benefit</label>
                                        <select name="benefit_name[]" id="benefitname_0" class="form-control benefitname" required>
                                            <option selected value="">Select Benefit</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="required">Benefit Amount</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="benefit_limit[]" onkeyup="this.value=numberWithCommas(this.value)" id="benefitlimit_0" required>
                                            <span class="btn btn-primary" id="new_benefit"><span class="fa fa-plus"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="plus_benefits"></div>
                        </div>
                        <br>

                

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="submit_benefits" class="btn btn-outline-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function(){
        let pa = "{{$class->pa}}"

        if (pa == "Y") {
            $("#pa_details").show()
            
        } else {
            $("#gpa_details").show()
        }


        $("#salary_type").trigger('change');
        $("select#rating_basis").trigger('change');
        $('#est_earnings').trigger('keyup');

        $(".pa_dec_edit").on('change', function(){
            let limit = $("#edit_idemnity_limit").val()
            let rate = $("#edit_rate").val()

            let premium = removeCommas(limit)*rate/100;

            $("#edit_premium").val(numberWithCommas(premium))
        })


        $('#save_gpa').on('click', function(e){
            e.preventDefault()
            let form = $("#gpa_details_form")
            form.validate({
                errorElement: 'span',
                errorClass: 'text-danger fst-italic',
                highlight: function(element, errorClass) {
                },
                unhighlight: function(element, errorClass) {
                }
            });
            if (form.valid() === true){
                let data = $('#gpa_details_form').serialize()
                $.ajax({
                    type: 'POST',
                    data:data,
                    url: "{!! route('edit_pa_details')!!}",
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

        $(".loc_back").on('click', function(){
            var source = @json($source);

            source == 'client' ? 
            window.location="{{ route('add_risk_quote_nonmotor')}}"+"?qstring={{Crypt::encrypt("client=$client&source=client&motorflag=N&quote_no=$quote->quote_no")}}" :
            window.location="{{ route('add_risk_quote_nonmotor')}}"+"?qstring={{Crypt::encrypt("lead=$client&source=lead&motorflag=N&quote_no=$quote->quote_no")}}";
        })

        



           
        let selected_sections_array = new Array();
        let all_sections = {!! $classsect !!}
        let counter = 0

        $.ajax({
            type:"get",
            url:"{{ route('pa_benefits') }}",
            data:{'cls': "{{$class->class_code}}"},
            type:"get",
            success: function(data){
                $.each(data , function(index, value) {
                    section_no = value.section_no
                    selected_sections_array.push(section_no)
                })

                console.log(data)

                if(selected_sections_array.length > 0){
                    $('#benefitname_0').empty()
                    $('#benefitname_0').append('<option selected value="">Select Benefit</option>')
                } else if (selected_sections_array.length == 0) {
                    $('#benefitname_0').empty()
                    $('#benefitname_0').append('<option selected value="">Select Benefit</option>')
                }

                $.each(all_sections , function(index, value) {
                    if ((!($.inArray(value.section_no, selected_sections_array) >= 0))) {
                        $('#benefitname_0').append($('<option>').text(value.section_description).attr('value', value.section_no));
                    }
                });
            }
        })
    });

    
    $('#salary_type').change(function() {
            multiple = $(this).val()
            $('#ma_earning').prop('readonly', true)

            if (multiple == 'A') {
                $('#ma_earning').val(1)
            } else if (multiple == 'M') {
                $('#ma_earning').val(12)
            }

            rating_basis = $('#rating_basis').val()
            benefit = $('#est_earnings').val()
            benefit = removeCommas(benefit)
            
            if (rating_basis == 'M') {
                multiple = $('#ma_earning').val()
                total_benefit = parseInt(multiple) * parseInt(benefit)

                $('#death_limit').val(numberWithCommas(total_benefit))
            }
        })


        ////////////basis of rating/////////
        $("select#rating_basis").change(function(){
            var basis=$("select#rating_basis option:selected").attr('value');

            switch(basis){
                case 'M':
                    $('#salary_type').prop('disabled',false);
                    $('#ma_earning').prop('disabled',false);
                    $('#ma_earning').prop('readonly', true);
                    $('#salary_type').prop('disabled',false);
                    $('#death_limit').val("");
                    $('#est_earnings').val();
                    $('#death_limit').prop('disabled',false);
                    $('#death_limit').prop('readonly',true);
                    $('#est_earnings').prop('disabled',false);
                break;

                case 'F':
                    $('#salary_type').val("");
                    $('#salary_type').prop('disabled',true);
                    $('#salary_type').prop('disabled',true);
                    $('#ma_earning').prop('disabled',true);
                    $('#ma_earning').val('');
                    $('#death_limit').prop('readonly',false);
                    $('#death_limit').prop('disabled',false);
                    $('#est_earnings').val("");
                    $('#est_earnings').prop('disabled',true);
                break;
            }

        });

        $('#est_earnings').keyup(function() {
            benefit = $(this).val()
            basis_rating = $('#rating_basis').val()

            benefit = removeCommas(benefit)
            basis_rating = removeCommas(basis_rating)
            
            if (basis_rating == 'M') {
                multiple = $('#ma_earning').val()
                total_benefit = parseInt(multiple) * parseInt(benefit)

                total_benefit = numberWithCommas(total_benefit)
                $('#death_limit').val(total_benefit)
            }
        })

</script>
@endsection