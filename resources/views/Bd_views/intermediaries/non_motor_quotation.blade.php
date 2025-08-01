@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>POLICY DETAILS</h4>
        </div>
        <div class="card-body p-4 step">
            @if($source == 'client')
            <h5 class="text-start">Client details</h5>
            @else
            <h5 class="text-start">Lead details</h5>
            @endif
            <hr>
            <div class="row">
                @if($source == 'client')
                <x-QuotationInputDiv>
                    <x-Input name="fname" id="" inputLabel="Full Name" req=""  value="{{$clientdtls->full_name}}" readonly/>
                </x-QuotationInputDiv>

                <x-QuotationInputDiv>
                    <x-Input name="email" id=""  inputLabel="Email" req=""  value="{{$clientdtls->email}}" readonly/>
                </x-QuotationInputDiv>

                <x-QuotationInputDiv>
                    <x-Input name="phone" id=""  inputLabel="Phone Number" req=""  value="{{$clientdtls->phone_1}}" readonly/>
                </x-QuotationInputDiv>
                @else
                <x-QuotationInputDiv>
                    <x-Input name="fname" id="" inputLabel="Full Name" req=""  value="{{$lead->full_name}}" readonly/>
                </x-QuotationInputDiv>

                <x-QuotationInputDiv>
                    <x-Input name="email" id=""  inputLabel="Email" req=""  value="{{$lead->email}}" readonly/>
                </x-QuotationInputDiv>

                <x-QuotationInputDiv>
                    <x-Input name="phone" id=""  inputLabel="Phone Number" req=""  value="{{$lead->phone_number}}" readonly/>
                </x-QuotationInputDiv>
                @endif

            </div>
            <div id="location_details">
                <h6 class="text-start mt-3">Cover details</h6>
                <hr>
                <form id="loc_details" class="needs-validation" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Main Company Selection -->
                        <x-QuotationInputDiv id="singlebiz">
                            <x-SelectInput name="brk_company" id="brk_company" req="required" inputLabel="Insurance Company">
                                <option selected disabled>Select a Company</option>
                                @foreach($compdata as $compdat)
                                    @if($policydtl != "")
                                        @if($policydtl->company_code == $compdat->company_id)
                                            <option value="{{ $compdat->company_id }}" selected>{{ $compdat->company_name }}</option>
                                        @endif
                                    @else
                                        <option value="{{ $compdat->company_id }}">{{ $compdat->company_name }}</option>
                                    @endif
                                @endforeach
                            </x-SelectInput>
                        </x-QuotationInputDiv>

                        <!-- Co-Insurance Checkbox -->
                        <x-QuotationInputDiv class="d-inline-flex align-items-center">
                            <input type="checkbox" id="co_insurance" name="co_insurance" class="me-2">
                            <label for="co_insurance" class="mb-0">Co-insurance</label>
                        </x-QuotationInputDiv>

                        <!-- Dynamic Co-Insurance Section -->
                        <x-QuotationInputDiv id="co_insurance_section" style="display: none;">
                            <div class="form-group">
                                <label>Insurance Companies</label>
                                <div id="company_container">
                                    <!-- Placeholder for dynamically added companies and their shares -->
                                </div>
                                <button type="button" id="add_company_btn" class="btn btn-success mt-2">+ Add Company</button>
                            </div>
                        </x-QuotationInputDiv>
                    </div>

                    <div class="row">
                    
                     
                        <input type="text" name="batch_no" id="batch_no" hidden>
                        <input type="text" name="quote_no" id="quote_no" hidden>
                        <input type="text" name="prev_pol" id="prev_pol" @if($policydtl != "") value="{{$policydtl->endorsement_no}}" @endif hidden>  

                        <input type="text" name="type" id="type" value="{{$type}}" hidden>  

                        <input type="text" name="user_role" id="user_role" value="{{auth()->user()->user_role}}" hidden>
                        
                        @if(auth()->user()->hasRole('admin')) 
                            <x-QuotationInputDiv>
                                <x-SearchableSelect name="branch" id="branch" req="required" inputLabel="Branch" >
                                    <option value="">Select branch</option>
                                    @foreach ($branches as $branch)
                                        @if($policydtl != "")
                                            @if($policydtl->branch == $branch->branch_code)
                                            <option value="{{ $branch->branch_code }}" selected>{{ $branch->description }}</option> 
                                            @endif
                                        @else
                                            <option value="{{ $branch->branch_code }}" >{{ $branch->description }}</option> 
                                        @endif
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                           
                        @endif

                        <x-QuotationInputDiv>
                            <x-SearchableSelect name="class_categ" id="class_categ" req="required" inputLabel="Class Category" >
                                <option value=""  selected disabled>Select Class Category</option>
                                @foreach ($class_categs as $class_categ)
                                @if($policydtl != "")
                                    @if($policydtl->class_categ == $class_categ->categ_code)
                                    <option value="{{ $class_categ->categ_code }}" selected>
                                            {{ $class_categ->description }}
                                        </option> 
                                        @else
                                        <option value="{{ $class_categ->categ_code }}" >
                                                {{ $class_categ->description }}
                                        </option> 
                                        @endif
                                @else
                                 <option value="{{ $class_categ->categ_code }}" >
                                        {{ $class_categ->description }}
                                 </option> 
                                @endif
                                       
                                @endforeach
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-SearchableSelect name="classbs" id="classbs" req="required" inputLabel="Class" >
                                
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>

                        
                        <input type='hidden'  name="agent" id="agent" value=''/>
                        <input type='hidden'  id="source" value="{{ $source }}" name="source" />
                        @if($source == 'client')
                        <input type='hidden'  id="client_no" value="{{ $clientdtls->global_customer_id }}" name="client_no" />
                        @else
                        <input type='hidden'  id="lead_no" value="{{ $lead->code }}" name="lead_no" />
                        @endif
                        <x-QuotationInputDiv>
                            <x-SelectInput name="ast_marker" id="plan" req="required" inputLabel="Plan">
                                    @if($policydtl != "")
                                        @if(trim($policydtl->ast_marker) == "A")
                                            <option value="A" selected>Annual</option>
                                            <option value="S">Short term</option>
                                        @endif 
                                        @if(trim($policydtl->ast_marker) == "S")
                                            <option value="A" >Annual</option>
                                            <option value="S" selected>Short term</option>
                                        @endif             
                                    @else
                                    <option value="">Select plan</option>
                                        <option value="A" >Annual</option>
                                        <option value="S" >Short term</option>
                                    @endif
                            </x-SelectInput>
                        </x-QuotationInputDiv>
                        
                        
                        <x-QuotationInputDiv>
                        @if($policydtl != "") 
                            <x-DateInput name="eff_date" id="eff_date"  placeholder="Enter Transaction Date"  inputLabel="Transaction Date" req="required" value="{{$policydtl->effective_date}}"/>
                        @else
                            <x-DateInput name="eff_date" id="eff_date"  placeholder="Enter Transaction Date"  inputLabel="Transaction Date" req="required"/>
                        @endif
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        @if($policydtl != "")
                            @if($type =="REN")
                            <x-DateInput name="period_from" id="period_from"  placeholder="Enter Period From"  inputLabel="Period From" req="required" value="{{$policydtl->renewal_date}}"/>
                            @else
                            <x-DateInput name="period_from" id="period_from"  placeholder="Enter Period From"  inputLabel="Period From" req="required" value="{{$policydtl->period_from}}"/>
                            @endif
                       @else
                       <x-DateInput name="period_from" id="period_from"  placeholder="Enter Period From"  inputLabel="Period From" req="required" value=""/>

                       @endif
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        @if($policydtl != "" && $type != "REN")
                       <x-DateInput name="period_to" id="period_to"  placeholder="Enter Period To"  inputLabel="Period To" req="required" value="{{$policydtl->period_to}}"/>
                       @else
                       <x-DateInput name="period_to" id="period_to"  placeholder="Enter Period To"  inputLabel="Period To" req="required" value=""/>

                       @endif
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        @if($policydtl != ""  && $type != "REN")
                        <x-DateInput name="renewal_date" id="renewal_date"  placeholder="Enter Renewal Date"  inputLabel="Renewal Date" req="required" value="{{$policydtl->renewal_date}}" disabled/>
                        @else
                         <x-DateInput name="renewal_date" id="renewal_date"  placeholder="Enter Renewal Date"  inputLabel="Renewal Date" req="required" value="" disabled/>
                        @endif
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        @if($policydtl != ""  && $type != "REN") 
                           <x-NumberInput name="cover_days" id="cover_days"  placeholder="Cover days"  inputLabel="Cover Days" req="required" value="{{$policydtl->days_covered}}" disabled/>
                        @else
                        <x-NumberInput name="cover_days" id="cover_days"  placeholder="Cover days"  inputLabel="Cover Days" req="required" disabled />

                        @endif
                    </x-QuotationInputDiv>
                    <!-- <x-QuotationInputDiv>
                        
                       <x-DateInput name="cover_from" id="cover_from"  placeholder="Enter Cover From"  inputLabel="Cover From" req="required" value=""/>

                       
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        <x-DateInput name="cover_to" id="cover_to"  placeholder="Enter Cover To"  inputLabel="Cover To" req="required" value=""/>

                    </x-QuotationInputDiv> -->

                    <x-QuotationInputDiv>
                        <x-SelectInput  class="select2" name="currency" id="currency" req="required" inputLabel="Currency">
                            <option disabled>Select Currency...</option>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->currency_code }}" @if($currency->base_currency=="Y") selected @endif shortcode="{{$currency->short_description }}">
                                    {{ $currency->description }}</option> 
                            @endforeach
                        </x-SelectInput>
                    </x-QuotationInputDiv>
                  

                    <!-- <div class="col-md-6 mt-1">
                        <label>Levies Charged</label><br>
                        <div class="form-check form-check-inline mt-2">
                            <input class="form-check-input" type="checkbox" name="traininglevy" value="Y">
                            <label class="form-check-label" for="traininglevy">Training levy</label>
                            </div>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="phcf" value="Y">
                            <label class="form-check-label" for="phcf">PHCFund</label>
                            </div>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="stampduty" value="Y">
                            <label class="form-check-label" for="stampduty">Stamp Duty</label>
                        </div>
                        
                            
                            <input type="text" name="vat" id="vat"  value="2" hidden>

                        </div>
                  
                    </div> -->
                     <!-- <div class="mt-2">
                     <label>Charged Levies</label><br>
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                        <label class="form-check-label" for="inlineCheckbox1">Training levy</label>
                        </div>
                        <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                        <label class="form-check-label" for="inlineCheckbox2">PHCFund</label>
                        </div>
                        <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                        <label class="form-check-label" for="inlineCheckbox3">Stamp Duty</label>
                      </div>
                     </div> -->

                  
                    <div class="mt-4 mb-3">
                        <x-button.submit class="save_loc col-md-2 float-end text-white" id="save_loc">Save</x-button>
                        <x-button.back class="float-end mx-2 col-md-2 " style="display: none" id="back_to_loc"><i class="fa-solid fa-left"></i>Back</x-button>
                    </div>
                    

                    <div style="display:none" class="row display_location">
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary" id="add_location_button"><i class="fa fa-plus"></i> Add Location</button>
                            <button type="button" class="btn btn-success" id="finish"><i class="fa fa-check"></i> Finish</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped  table-hover" width="100%" id="location_table">
                                <thead class="">
                                    <tr>
                                        <th>Sections</th>
                                        <th>Location</th>
                                        <th>Location Name</th>   
                                        <th>Plot No.</th>  
                                        <th>Total Sum Insured</th>  
                                        <th>Premium</th>     
                                        <th>Action</th>     
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $location)
                                        <tr class="loc_row">
                                            <td>
                                                <span class="accordion-toggle text-success"  data-bs-toggle="collapse" data-bs-target="#location_{{$location->location}}" loc="{{$location->location}}" 
                                                    title="View sections" style="cursor:pointer;">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-circle-plus fa-2x"></i> 
                                                </span>
                                            </td>
                                            <td>{{$location->location}}</td>
                                            <td>{{$location->name}}</td>   
                                            <td>{{$location->plot_no}}</td> 
                                            <td>{{ number_format($location->sum_insured, 2)}}</td>   
                                            <td>{{ number_format($location->total_premium, 2)}}</td>     
                                            <td>
                                                <span type="span" class='text-danger deletedetails' data-id='{{$location->location}}' data-quote='{{$location->quote_no}}' title="Delete" style="cursor:pointer;">
                                                    <i class='fa fa-circle-xmark'></i>Remove
                                                </span>|
                                                <span class="text-primary" title="Add section">
                                                    <a href="{{route('add_location_sections',['qstring'=>Crypt::encrypt('quote_no='.$location->quote_no.'&source='.$source.'&location='.$location->location.'')])}}" class="location_det" style="text-decoration:none">
                                                            <i class='fa fa-edit'></i>Add/Edit Sections
                                                    </a>
                                                </span>
                                            </td>     
                                        </tr>
                                        <tr>
                                            <td colspan="12" class="hiddenRow">
                                                <div class="accordian-body collapse" id="location_{{$location->location}}">
                                                    <table class="table table-striped  table-hover" id="sections_data_table_{{$location->location}}" width="100%">
                                                        <thead class="">
                                                            <tr>
                                                                <th>Section Name</th>
                                                                <th>Sum Insured</th>   
                                                                <th>Rate</th>  
                                                                <th>Premium</th>     
                                                                <th>Action</th>     
                                                            </tr>
                                                        </thead>
                                                    </table> 
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
        let totalShare = 0;
          // Toggle visibility of the co-insurance section
        $('#co_insurance').on('change', function() {
            if ($(this).is(':checked')) {
                $('#co_insurance_section').show();
                $('#singlebiz').hide();

            } else {
                $('#co_insurance_section').hide();
                $('#singlebiz').show();
                $('#company_container').empty();
                totalShare = 0; // Reset total share
            }
        });
        // Function to add a new company input row
        $('#add_company_btn').on('click', function() {
            // Check if totalShare is less than 100 before adding more
            if (totalShare < 100) {
                let companyRow = `
                    <div class="company-row form-group">
                        <label for="company">Company:</label>
                        <select name="brk_company[]" class="form-control company-select">
                            <option value="" disabled selected>Select a Company</option>
                            @foreach($compdata as $compdat)
                                <option value="{{$compdat->company_id}}">{{$compdat->company_name}}</option>
                            @endforeach
                        </select>
                        
                        <label for="risk_share">Share (%):</label>
                        <input type="number" name="risk_share[]" class="form-control risk-share-input" max="100" min="1" placeholder="Enter share">
                        
                        <button type="button" class="remove-company btn btn-danger">-</button>
                    </div>`;
                
                $('#company_container').append(companyRow);
            } else {
                alert('Total share cannot exceed 100%.');
            }
        });
        // Function to remove a company row
        $('#company_container').on('click', '.remove-company', function() {
            // Deduct the share value of the removed row from totalShare
            let removedShare = $(this).closest('.company-row').find('.risk-share-input').val();
            totalShare -= parseFloat(removedShare) || 0;
            $(this).closest('.company-row').remove();
        });
         // Function to remove a company row
    $('#company_container').on('click', '.remove-company', function() {
        // Deduct the share value of the removed row from totalShare
        let removedShare = $(this).closest('.company-row').find('.risk-share-input').val();
        totalShare -= parseFloat(removedShare) || 0;
        $(this).closest('.company-row').remove();
    });

    // Function to validate total share does not exceed 100%
    $('#company_container').on('input', '.risk-share-input', function() {
        let shareValue = parseFloat($(this).val());
        
        if (isNaN(shareValue) || shareValue < 0 || shareValue > 100) {
            alert('Please enter a valid share percentage between 1 and 100.');
            $(this).val('');
            return;
        }

        // Calculate the current total of all share inputs
        totalShare = 0;
        $('.risk-share-input').each(function() {
            let value = parseFloat($(this).val());
            totalShare += (value || 0);
        });

        // Check if total share exceeds 100%
        if (totalShare > 100) {
            alert('Total share cannot exceed 100%.');
            $(this).val('');
        }
    });
        var today = new Date().toISOString().split('T')[0];
        $('#eff_date').val(today);
            
            // Make the input field read-only
        $('#eff_date').prop('readonly', true);
        $('#period_from').on('change', function() {
                var periodFrom = new Date($(this).val());
                
                if (!isNaN(periodFrom)) {
                    // var periodTo = new Date(periodFrom);
                    // periodTo.setFullYear(periodTo.getFullYear() + 1);

                    // // Ensure the date format is yyyy-mm-dd
                    // var day = String(periodTo.getDate()).padStart(2, '0');
                    //      day =day-1
                    // var renday = String(periodTo.getDate()).padStart(2, '0');
                 


                    // var month = String(periodTo.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                    // var year = periodTo.getFullYear();

                    // var formattedDate = year + '-' + month + '-' + day;
                    // alert(formattedDate)
                    var periodTo = new Date(periodFrom);
                    periodTo.setFullYear(periodTo.getFullYear() + 1);

                    // Subtract one day
                    periodTo.setDate(periodTo.getDate() -1);

                    // Ensure the date format is yyyy-mm-dd
                    var day = String(periodTo.getDate()).padStart(2, '0');
                    var month = String(periodTo.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                    var year = periodTo.getFullYear();

                    var formattedDate = year + '-' + month + '-' + day;
                  
                    
                    // $('#period_to').val(formattedDate);
                    var timeDiff = periodTo.getTime() - periodFrom.getTime();
                    var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)); // Convert milliseconds to days
                    $('#cover_days').val(daysDiff+1);
                    // Set renewal date to one day after periodTo
                    var renewalDate = new Date(periodTo);
                    renewalDate.setDate(renewalDate.getDate() + 1);

                    var renewalDay = String(renewalDate.getDate()).padStart(2, '0');
                    var renewalMonth = String(renewalDate.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                    var renewalYear = renewalDate.getFullYear();

                    var formattedRenewalDate = renewalYear + '-' + renewalMonth + '-' + renewalDay;
                    var plan = $('#plan').val();
                    if(plan == 'A'){
                        $('#period_to').val(formattedDate);
                        $('#cover_to').val(formattedDate);
                        $('#cover_from').val($(this).val());
                        $('#renewal_date').val(formattedRenewalDate);

                    }

                

                }
        });
        $('#period_to').on('change', function() {
            var periodTo = new Date($(this).val());
            var periodFrom = new Date($("#period_from").val());
                
                if (!isNaN(periodFrom)) { 
                    var timeDiff = periodTo.getTime() - periodFrom.getTime();
                    var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)); // Convert milliseconds to days
                    $('#cover_days').val(daysDiff);
                    var renewalDate = new Date(periodTo);
                    renewalDate.setDate(renewalDate.getDate() + 1);

                    var renewalDay = String(renewalDate.getDate()).padStart(2, '0');
                    var renewalMonth = String(renewalDate.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                    var renewalYear = renewalDate.getFullYear();

                    var formattedRenewalDate = renewalYear + '-' + renewalMonth + '-' + renewalDay;
                    $('#renewal_date').val(formattedRenewalDate);
                }else{
                    $("#period_to").val("")
                    toastr.warning('Select Transaction Period From!', {timeOut: 5000});
                    
                }


        })
        $('#period_from').trigger("change");
        let quote_no = "{{$quote_no}}"
        let branch = ""
        let location = 0;
        let bond = ""
        let git = ""
        let marine = ""
        let gpa = ""
        let pa = ""
        let wiba = ""
        let travel = ""
        let class_categ= ""
        let cls = ""
        @if(isset($policydtl))
          class_categ ="{{$policydtl->class_categ}}"
          cls ="{{$policydtl->class}}"

        @endif

        var source = @json($source); // Pass the source value from your server-side code
        var quoteNo = @json($quote_no);

        <?php 
        $qstring = Crypt::encrypt('source='.$source.'&quote_no='.$quote_no)
        ?>
        

        $("#classbs").on('change', function(){
                $('#classbs').on('change', function() {
                    var cls =$(this).val(); 
                    var company  = $('#brk_company').val();

                    
                    $("#cls").val(cls)
                    $.ajax({
                        type: 'GET',
                        data:{'cls':cls,'company':company},
                        url: "{!! route('agent.checkcommission')!!}",
                        success:function(data){
                            console.log(data)
                            if (data.status == 1) {
                                if (data.exists !=true) {
                                    toastr.warning('Commission Not Set, Please Set Commission To Proceed!', {timeOut: 5000});
                                    $('#save_loc').prop('disabled', true);

                                    
                                    
                                }else{
                                    $('#save_loc').prop('disabled', false);

                                }
                                
                            }else{
                                toastr.error('Error Occurred!', {timeOut: 5000});
                                $('#save_loc').prop('disabled', true);




                            }
                            
                        }
                    });

                });
        });

        $('#plan').on('change', function() {
            let plan =$(this).val(); 
            if (plan == 'A') {
                $('#cover_days').attr('readonly', 'readonly');
                $('#cover_days').val(365);
            } else {
                $('#cover_days').removeAttr('readonly');
                $('#cover_days').val(0);
            }
        });

       

        //class category on change
        $('#class_categ').on('change', function(){
            let class_categ =$(this).val(); 
            $.ajax({
                    type: 'GET',
                    data:{'class_categ':class_categ,'motor_flag':'N'},
                    url: "{!! route('policy.getclasses')!!}",
                    success:function(data){

                        if(data.status==1){
                          var data =data.data

                          populateSelect(data);

                        }

                    

                    }
                })
         


        });

        $('#save_loc').on('click', function(){
            let data = $('#loc_details').serialize()
            
            $('#loc_details').validate({
                    errorElement: 'span',
                    errorClass: 'text-danger fst-italic',
                    highlight: function(element, errorClass) {
                    },
                    unhighlight: function(element, errorClass) {
                    },
                    rules: {
                        first_name: {
                            minlength: 6,
                        }
                    }
                });
                
            if ($('#loc_details').valid() == true) {
                $.ajax({
                    type: 'GET',
                    data:data,
                    url: "{!! route('agent.stage_single_process')!!}",
                    success:function(data){

                        window.location="{{route('policy.addrisk', '')}}"+"/"+data.policy_no;

                    }
                })
                
            }
            
            
        })

        $('#location_table').on('click', '.deletedetails', function(e) {
            e.preventDefault()
                var location = $(this).closest('tr').find('td:eq(1)').text();
                var quote_no =  $('#quote_no').val();
            Swal.fire({
                title: "Warning!",
                html: "Are You Sure You Want to delete this Location?",
                icon: "warning",
                confirmButtonText: "Yes",
                showCancelButton: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        data:{'location':location,'quote_no':quote_no},
                        url: "{!! route('agent.delete.nmrisk')!!}",
                        success: function(response) {
                            if (response.status == 1) {
                                toastr.success('Deleted Successfully', {
                                        timeOut: 5000
                                    });
                            }
                            window.location.reload()
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                            title: "Error",
                            text: textStatus,
                            icon: "error"
                            });
                        }
                    });
                }
            })


        });

        $('#add_location_button').on('click', function(){
            let project_det = $("option:selected", '#classbs').attr('engineering');
            let bypass_location = $("option:selected", '#classbs').attr('bypass_loc');

            if (project_det == 'Y') {
                $('#project_details').css('display', 'block')
                $('.proj_det').addClass('checkempty')
            } else {
                $('#project_details').css('display', 'none')
                $('.proj_det').removeClass('checkempty')
            }

            if (bypass_location == 'N') {
                $('#location_header').css('display', 'block')
                $('.display_location').css('display', 'block')
                $('.loc_det').addClass('checkempty')
            } else {
                $('#location_header').css('display', 'none')
                $('.display_location').css('display', 'none')
                $('.loc_det').removeClass('checkempty')
            }

            $('#location_div').css('display', 'block')
            $('.loc_det').addClass('checkempty')
            $('.display_location').css('display', 'none')
            $('#save_loc').show()
            $('#back_to_loc').show()

        });

        $("body").on('click', '.accordion-toggle', function(){
            let location = $(this).attr('loc')
            console.log(location);
            
            $('#sections_data_table_'+location).DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                    'url' : '{{ route("get_loc_sections") }}',
                    'data' : function(d){
                        d.quote_no=quote_no
                        d.location=location
                    },
                },
                
                columns: [
                    {data:'section_description',name:'description'},
                    {data:'sum_insured',name:'sum_insured'},
                    {data:'rate',name:'rate'},
                    {data:'premium',name:'premium'},
                    {data:'action',name:'action'},
                ]		
            })

            $('#sections_data_table_'+location).DataTable().destroy();
        })

        


        $("#back_to_loc").on('click', function(){
            $('#location_div').css('display', 'none')
            $('#project_details').css('display', 'none')
            $('.display_location').css('display', 'block')
            $('#save_loc').hide()
            $(this).hide()
        })

        $("#finish").on('click', function(){
            let i = 0
            $("#location_table .loc_row").each(function() {
                let val = $(this).find('td:eq(5)').text();
                let travel = $("option:selected", "#classbs").attr('travel');
                val = parseFloat(val.replaceAll(',', ''))

                if(val <= 0){
                    toastr.error('All locations must have at least one section', {
                            timeOut: 5000
                        });
                    i = i +1
                    return flase
                }
            });
            

            if (i == 0) {
                window.location="/brokage/Agent/quot/view/"+quote_no+"/"+source;
            }
        })

        $('#branch').on('change', function(){
            let branch = $(this).val()
            console.log(branch);

            $.ajax({
                type: "GET",
                data: {'branch': branch},
                url: "{{ route('get_branch_agents')}}",
                success:function(resp){
                    if (resp.status == 1) {
                        $("#agent").empty()
                        $("#agent").append($("<option />").val('').text('Select Agent'));
                        $.each(resp.agents, function() {
                            $("#agent").append($("<option />").val(this.lob_intermediary_id).text(this.full_name));
                        });

                        var agent =null
                        if (quote_no !== null && quote_no !== undefined && quote_no !== "") {
                           
                          agent="{{$quote?$quote->agent:null}}"
                          
                       }
                       
                        $.each(resp.agents, function() {
                            if(agent ==this.lob_intermediary_id){
                                $("#agent").append($("<option />").val(this.lob_intermediary_id).text(this.full_name).prop('selected', true));
                            }else{
                                $("#agent").append($("<option />").val(this.lob_intermediary_id).text(this.full_name));

                            }
                        });
                    }
                }
            })

        })

        function redirectToLocationDetails(bond,git,marine,gpa, pa, wiba,travel, qstring){
            let quote = $('#quote_no').val()
            let source = $('#source').val()
            if (bond == "Y") {
                window.location = "{{route('bond_details')}}"+"?qstring="+qstring
            }
            
            else if (git == "Y") {
                window.location = "{{route('git_details')}}"+"?qstring="+qstring
            }
            
            else if (marine == "Y") {
                window.location = "{{route('marine_details')}}"+"?qstring="+qstring
            }
            
            else if (gpa == "Y" || pa == "Y") {
                window.location = "{{route('pa_details')}}"+"?qstring="+qstring
            }
            
            else if (wiba == "Y") {
                window.location = "{{route('wiba_details')}}"+"?qstring="+qstring
            }
            else if (travel == "Y") {
                window.location = "{{route('travel_details')}}"+"?qstring="+qstring
            }else{
                window.location = "{{ route('add_location_sections') }}"+"?qstring="+qstring

            }
        }
        function populateSelect(data) {
            // Clear the existing options first
            $('#classbs').empty();
            
            // Add the default option
            $('#classbs').append($('<option>', {
                value: '',
                text: 'Select class',
                disabled: true,
                selected: true
            }));
            
            // Iterate over the data array and add options
            $.each(data, function(index, item) {
                // Construct the option string with interpolated values
                if(item.class_code == cls){
                    var optionString = `<option selected value="${item.class_code}" bypass_loc="${item.bypass_loc}" earthquake="${item.earthquake}" bond="${item.bond}" engineering="${item.engineering}" travel="${item.travel}" marine="${item.marine}" git="${item.git}" pa="${item.pa}" wiba="${item.wiba}" gpa="${item.gpa}">${item.class_description} </option>`;

                }else{
                    var optionString = `<option value="${item.class_code}" bypass_loc="${item.bypass_loc}" earthquake="${item.earthquake}" bond="${item.bond}" engineering="${item.engineering}" travel="${item.travel}" marine="${item.marine}" git="${item.git}" pa="${item.pa}" wiba="${item.wiba}" gpa="${item.gpa}">${item.class_description}</option>`;

                }
                
                // Append the option string to the select element
                $('#classbs').append(optionString);
            });
        }
        $('#classbs').on('change', function(){
            let classbs = $(this).val()
            let bypass_location = $("option:selected", this).attr('bypass_loc');
            earthquake = $("option:selected", this).attr('earthquake');
            bond = $("option:selected", this).attr('bond');
            marine = $("option:selected", this).attr('marine');
            git = $("option:selected", this).attr('git');
            gpa = $("option:selected", this).attr('gpa');
            pa = $("option:selected", this).attr('pa');
            wiba = $("option:selected", this).attr('wiba');
            travel = $("option:selected", this).attr('travel');

            let project_det = $("option:selected", this).attr('engineering');

            if (bypass_location != 'Y') {
                $('#benefit_per_location').css('display', 'block')
                $('#bypasslocation').val('N')
                $('#location_header').css('display', 'block')
                $('#location_div').css('display', 'block')
                $('.loc_det').addClass('checkempty')
                $('#save_loc').show()
                $('#add_location_button').show()
                $('.next').hide()
            } else {
                $('#benefit_per_location').css('display', 'none')
                $('#bypasslocation').val('Y')
                $('#location_header').css('display', 'none')
                $('.display_location').css('display', 'none')
                $('#location_div').css('display', 'none')
                $('#project_details').css('display', 'none')
                $('.loc_det').removeClass('checkempty')
                $('.proj_det').removeClass('checkempty')
                $('#save_loc').show()
                $('.next').hide()
                $('#add_location_button').hide()
                $('#sec_location').trigger("change");
            }

            if (earthquake == 'Y') {
                $('#earthquake').attr('disabled', false)
            } else {
                $('#earthquake').attr('disabled', true)
                $('#earthquake').val('N')
            }

            if (project_det == 'Y') {
                $('#engineering_project').val('Y')
                $('#project_details').css('display', 'block')
            } else {
                $('#engineering_project').val('N')
                $('#project_details').css('display', 'none')
            }
        })
        $('#class_categ').val(class_categ).trigger('change');
      
    });
</script>
@endsection