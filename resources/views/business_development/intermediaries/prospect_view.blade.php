@extends('layouts.app')
@section('content')
    <style>
        {
            margin: 0;
            padding: 0
        }

        html {
            height: 100%
        }

        p {
            color: grey
        }


        .card {
            z-index: 0;
            border: none;
            position: relative
        }

        .primary-color {
            color: #E1251B;
        }

        hr {
            margin: 20px 0px;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card px-0 pt-4 pb-0 mb-3">
                    <h5 class="text-center text-lg-start mb-0 mx-2">Prospect Handover: <span class="primary-color"
                            id="prospect_name"></span></h5>
                    <hr>
                    <div class="card-body">

                        <form id="msformVerify" method="POST" action="{{ route('prospectVerify') }}">
                            @csrf
                            <input type="hidden" name="agent_onboard_client" value="Y">
                            </ul>
                            <fieldset>
                                <div class="form-card">
                                    <div class="individual">
                                        <div class="row">

                                            <B class="primary-color">Client Details</B>
                                            <div class="m-0">
                                                <hr>
                                            </div>

                                            <input type="hidden" name="prospId" value="{{ $lead->id }}">
                                            <input type="hidden" name="prospect_id" id="prospect_id"
                                                value="{{ $lead->prospect_id }}">

                                            <x-OnboardingInputDiv class="col-md-3 col-sm-12 mt-2">
                                                <x-SearchableSelect name="client_type" value="{{ $lead->client_type }}"
                                                    id="client_type" req="required" inputLabel="Client Type">
                                                    <option value="">Select client type</option>
                                                    <option value="C"
                                                        {{ $lead->client_type == 'C' ? 'selected' : '' }}>Corporate</option>
                                                    <option value="I"
                                                        {{ $lead->client_type == 'I' ? 'selected' : '' }}>Retail</option>
                                                    <option value="N"
                                                        {{ $lead->client_type == 'N' ? 'selected' : '' }}>NGO</option>
                                                    <option value="G"
                                                        {{ $lead->client_type == 'G' ? 'selected' : '' }}>Government
                                                    </option>
                                                    <option value="S"
                                                        {{ $lead->client_type == 'S' ? 'selected' : '' }}>SME's</option>
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="client_category" id="client_category"
                                                    req="required" inputLabel="Client Category"
                                                    value="{{ $lead->client_category }}">
                                                    <option value="">Select prospect category</option>
                                                    <option value="N"
                                                        {{ $lead->client_category == 'N' ? 'selected' : '' }}>New prospect
                                                    </option>
                                                    <option value="O"
                                                        {{ $lead->client_category == 'O' ? 'selected' : '' }}>Organic
                                                        growth
                                                    </option>
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>


                                            <x-OnboardingInputDiv style="display:none" id="corporate">
                                                <x-Input name="corporate_name" placeholder="Enter company name"
                                                    id="corporate_name" value="{{ $lead->full_name }}"
                                                    inputLabel="Company Name" req="required"
                                                    onkeyup="this.value=this.value.toUpperCase();" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="salutation_code">
                                                <x-SearchableSelect name="salutation_code" id="salutation_code"
                                                    req="" inputLabel="Salutation"
                                                    value="{{ $lead->contact_salutation }}">
                                                    <option value="">Select salutation</option>
                                                    @foreach ($salutations as $salutation)
                                                        <option value="{{ $salutation->name }}"
                                                            {{ $salutation->name === $lead->contact_salutation ? 'selected' : '' }}>
                                                            {{ $salutation->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="full_name_div">
                                                <x-Input id="full_name" name="fname" value="{{ $lead->full_name }}"
                                                    req="required" inputLabel="Full Name" placeholder="Enter full name"
                                                    onkeyup='this.value=this.value.toUpperCase();' />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="idTypeDiv">
                                                <x-SelectInput name="id_type" id="id_type" req="required"
                                                    inputLabel="ID Type" value="{{ $lead->id_type }}">
                                                    <option value="" {{ empty($lead->id_type) ? 'selected' : '' }}>
                                                        Select ID Type</option>
                                                    <option value="N" {{ $lead->id_type == 'N' ? 'selected' : '' }}>
                                                        National ID</option>
                                                    <option value="P" {{ $lead->id_type == 'P' ? 'selected' : '' }}>
                                                        Passport ID</option>
                                                    <option value="F" {{ $lead->id_type == 'F' ? 'selected' : '' }}>
                                                        Foreigners ID</option>
                                                    <option value="M" {{ $lead->id_type == 'M' ? 'selected' : '' }}>
                                                        Military ID</option>
                                                </x-SelectInput>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="genderDiv">
                                                <x-SelectInput name="gender" id="gender_code"
                                                    class="form-control checkempty" req="required" inputLabel="Gender"
                                                    value="{{ $lead->gender_code }}">
                                                    <option value="">Select gender</option>
                                                    @foreach ($genders as $gender)
                                                        <option value="{{ $gender->gender_code }}"
                                                            {{ $lead->gender_code == $gender->gender_code ? 'selected' : '' }}>
                                                            {{ $gender->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SelectInput>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="occupationDiv">
                                                <x-SearchableSelect name="occupation_code" id="occupation_code"
                                                    req="required" inputLabel="Industry"
                                                    value="{{ $lead->occupation_code }}">
                                                    <option value="">Select occupation</option>
                                                    @foreach ($occupations as $occupation)
                                                        <option value="{{ $occupation->name }}"
                                                            {{ $lead->occupation_code == $occupation->name ? 'selected' : '' }}>
                                                            {{ $occupation->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="division_div">
                                                <x-SearchableSelect name="division" id="division" req="required"
                                                    inputLabel="Division" value="{{ $lead->division }}">
                                                    <option value="">Select division</option>
                                                    @foreach ($divisions as $division)
                                                        <option value="{{ $division->id }}"
                                                            {{ $lead->division == $division->id ? 'selected' : '' }}>
                                                            {{ $division->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="insurance_class" id="insurance_class"
                                                    req="required" inputLabel="Class of Insurance"
                                                    value="{{ $lead->class_of_insurance }}">
                                                    <option value="">Select class of insurance</option>
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="engage_type" id="engage_type" req="required"
                                                    inputLabel="Nature of engagement"
                                                    value="{{ $lead->nature_of_engagement }}">
                                                    <option value="">Select engagement type</option>
                                                    <option value="1"
                                                        {{ $lead->nature_of_engagement == 1 ? 'selected' : '' }}>Direct
                                                    </option>
                                                    <option value="2"
                                                        {{ $lead->nature_of_engagement == 2 ? 'selected' : '' }}>Broker
                                                    </option>
                                                </x-SearchableSelect>

                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>

                                                <x-SearchableSelect name="lead_handler" id="lead_handler" req="required"
                                                    inputLabel="CR Lead" value="{{ $lead->cr_handler }}">
                                                    <option value="">Select lead</option>
                                                    <!-- Options will be populated dynamically using JavaScript -->
                                                </x-SearchableSelect>


                                            </x-OnboardingInputDiv>


                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="bd_lead" id="bd_lead" req="required"
                                                    inputLabel="BD Lead" value="{{ $lead->bd_handler }}">
                                                    <option value="">Select lead</option>
                                                    @foreach ($bd_users as $user)
                                                        <option value="{{ $user->username }}"
                                                            {{ $lead->bd_handler == $user->username ? 'selected' : '' }}>
                                                            {{ strtoupper($user->firstname) }}
                                                            {{ strtoupper($user->lastname) }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>
                                        </div>

                                        <div class="row my-md-3">
                                            <B class="primary-color">Contact Details</B>
                                            <div class="m-0">
                                                <hr>
                                            </div>

                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="contact_salutation" id="contact_salutation"
                                                    req="" inputLabel="Contact Salutation"
                                                    value="{{ $lead->contact_salutation }}">
                                                    <option value="">Select salutation</option>
                                                    @foreach ($salutations as $salutation)
                                                        <option value="{{ $salutation->name }}"
                                                            {{ $lead->contact_salutation == $salutation->name ? 'selected' : '' }}>
                                                            {{ $salutation->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input name="contact_name" id="contact_name" placeholder="Enter name"
                                                    inputLabel="Contact Fullname" req="required"
                                                    value="{{ $lead->contact_fullname }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-EmailInput id="email" name="email" req="required"
                                                    inputLabel="Email Address" placeholder="Enter email"
                                                    value="{{ $lead->email }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="countryDiv">
                                                <x-SearchableSelect name="country_code" id="country" req="required"
                                                    inputLabel="Country" value="{{ $lead->country }}">
                                                    <option value="">Select country code</option>
                                                    @foreach ($countries as $country)
                                                        {{ $country->country_code }}
                                                        <option value="{{ $country->id }}"
                                                            {{ $lead->country == $country->id ? 'selected' : '' }}>
                                                            {{ $country->name }} +{{ $country->country_code }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input name="postal_address" id="postal_address"
                                                    placeholder="Enter postal address" inputLabel="Postal Address"
                                                    req="" value="{{ $lead->postal_address }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input name="postal_code" id="postal_code"
                                                    placeholder="Enter Postal Code" inputLabel="Postal Code"
                                                    req="" value="{{ $lead->postal_code }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input name="postal_code" id="postal_code"
                                                    placeholder="Enter Postal Code" inputLabel="Postal Code"
                                                    req="" value="{{ $lead->postal_code }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-NumberInput id="telephone" name="telephone" req="required"
                                                    inputLabel="Telephone" class="telephone"
                                                    placeholder="Enter telephone number"
                                                    value="{{ $lead->telephone }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-NumberInput id="phone_1" name="phone_1" req="required"
                                                    inputLabel="Primary Phone" class="phone_1"
                                                    placeholder="Enter phone number" value="{{ $lead->phone_1 }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input id="address_3" name="address_3" req="required"
                                                    inputLabel="Physical Address" placeholder="Enter physical address"
                                                    value="{{ $lead->address_3 }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input name="contact_position" id="contact_position"
                                                    placeholder="Enter position" inputLabel="Contact Position"
                                                    req="required" value="{{ $lead->contact_position }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="alternative_salutation"
                                                    id="alternative_salutation" req=""
                                                    inputLabel="Alternative Contact Salutation"
                                                    value="{{ $lead->alternative_salutation }}">
                                                    <option value="">Select salutation</option>
                                                    @foreach ($salutations as $salutation)
                                                        <option value="{{ $salutation->name }}"
                                                            {{ $lead->alternative_salutation == $salutation->name ? 'selected' : '' }}>
                                                            {{ $salutation->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input name="alternative_contact_name" id="alternative_contact_name"
                                                    placeholder="Enter name" inputLabel="Alternative Contact Fullname"
                                                    req="" value="{{ $lead->alternative_contact_name }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-EmailInput id="alternative_email" name="alternative_email"
                                                    req="" inputLabel="Alternative Contact Email"
                                                    placeholder="Enter email" value="{{ $lead->alternative_email }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-NumberInput id="alternative_phone_number"
                                                    name="alternative_phone_number" req=""
                                                    inputLabel="Alternative Phone No." class="phone"
                                                    placeholder="Enter phone number"
                                                    value="{{ $lead->alternative_phone_number }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input name="alternative_contact_position"
                                                    id="alternative_contact_position" placeholder="Enter position"
                                                    inputLabel="Alternative Contact Position" req=""
                                                    value="{{ $lead->alternative_contact_position }}" />
                                            </x-OnboardingInputDiv>
                                        </div>

                                        <div class="row my-md-3">
                                            <B class="primary-color">Quotation Details</B>
                                            <div class="m-0">
                                                <hr>
                                            </div>

                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="quote_currency" id="quote_currency"
                                                    req="required" inputLabel="Currency"
                                                    value="{{ $lead->quote_currency }}">
                                                    <option value="">Select Currency</option>
                                                    @foreach ($currencies as $currency)
                                                        <option value="{{ $currency->currency }}"
                                                            shortcode="{{ $currency->short_description }}"
                                                            {{ $lead->quote_currency == $currency->currency ? 'selected' : '' }}>
                                                            {{ $currency->description }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input name="final_premium" id="final_premium"
                                                    inputLabel="Final Premium" req="required"
                                                    value="{{ $lead->final_premium }}" oninput="formatNumber(this)" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input id="final_commission" name="final_commission" req="required"
                                                    inputLabel="Final Commission" value="{{ $lead->final_commission }}"
                                                    oninput="formatNumber(this)" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>

                                                <x-Input name="remarks" id="remarks" placeholder="Enter remarks"
                                                    rows="1" inputLabel="Remarks" req="required"
                                                    value="{{ $lead->remarks }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-DateInput id="incept_date" name="incept_date" req="required"
                                                    inputLabel="Inception Date" placeholder="Date of Inception"
                                                    value="{{ $lead->inception_date }}" />

                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-Input id="agent_name" name="agent_name" req="required"
                                                    inputLabel="Agent Name" value="{{ $lead->agent_name }}"
                                                    placeholder="Agent Name" />
                                            </x-OnboardingInputDiv>


                                            <x-OnboardingInputDiv>
                                                <x-Input id="ag_comm_rate" name="ag_comm_rate" req="required"
                                                    inputLabel="Agent Rate(%)" value="{{ $lead->agent_comm_rate }}"
                                                    placeholder="Agent Rate" oninput="formatNumber(this)" />
                                            </x-OnboardingInputDiv>

                                        </div>

                                        <div class="row my-md-3">
                                            <B class="primary-color">KYC Details</B>
                                            <div class="m-0">
                                                <hr>
                                            </div>


                                            <x-OnboardingInputDiv>
                                                <x-Input id="pin_no" name="pin_no" req="required"
                                                    inputLabel="Pin/Tin Number" value="{{ $lead->pin_no }}"
                                                    placeholder="Enter pin/tin number" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="idNoDiv">
                                                <label for="id_number"><span id="id_num_type"></span>
                                                    <font style="color:red;">*</font>
                                                </label>
                                                <input type="text" name="identity_no" class="form-control"
                                                    id="identity_no" placeholder="Enter identity number" required
                                                    value="{{ $lead->id_value }}">
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="incorporation_div">
                                                <x-Input id="incorporation_cert" name="incorporation_cert" req=""
                                                    inputLabel="Cetificate Of Incorporation Number"
                                                    placeholder="Enter cert. number"
                                                    value="{{ $lead->incorporation_cert }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="dob_div">
                                                <x-DateInput id="dob" name="dob" req="required"
                                                    inputLabel="Date of Registration"
                                                    value="{{ $lead->date_of_birth_registration }}"
                                                    placeholder="Date of Birth" />
                                            </x-OnboardingInputDiv>
                                        </div>

                                        <div class="row my-md-3">
                                            <B class="primary-color">Document Attached</B>
                                            <div class="m-0">
                                                <hr>
                                            </div>

                                            <div class="container py-2">
                                                <div class="row">
                                                    @foreach ($docs as $index => $doc)
                                                        <div class="col-md-6 mb-1">
                                                            <div class="card h-100 shadow-sm">
                                                                <div class="card-body d-flex flex-column">
                                                                    <div class="flex-grow-1">
                                                                        <h5 class="card-title text-dark mb-0">
                                                                            {{ $doc->description ?? 'No description available' }}
                                                                        </h5>
                                                                    </div>
                                                                    <div class="text-right mt-auto">
                                                                        <a href="{{ asset('uploads/' . $doc->file) }}"
                                                                            target="_blank" class="btn btn-danger">
                                                                            <i class="fas fa-file mr-2"></i>View Document
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <!-- <button class="btn btn-outline-success next action-button" name="next">Next</button> -->
                                <div class="text-right">
                                    <button type="submit" id="submit" class="btn btn-success text-white">
                                        <span class="fa fa-save "></span> Verify details
                                    </button>
                                </div>

                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="v_docs" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:70%">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #cfd7e0 ">
                    <h5 class="modal-title" id="adminClaimModalLabel">Document</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="Xcontainer">
                    <div class="modal-body">
                        <div class="embed-responsive embed-responsive-16by9">
                            <object id="doc_view" data="base64" height="900" width="100%"
                                style="border: solid 1px #DDD;"></object>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_scripts')
    <script>
        function formatNumber(input) {
            let value = input.value;
            value = value.replace(/[^0-9.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts[1];
            }

            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            input.value = parts.join('.');

            if (value !== '' && isNaN(value)) {
                input.setCustomValidity('Please enter a valid number');
            } else {
                input.setCustomValidity('');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            let finalPremiumInput = document.getElementById('final_premium');
            if (finalPremiumInput) {
                formatNumber(finalPremiumInput);
            }
            let final_commission = document.getElementById('final_commission');
            if (final_commission) {
                formatNumber(final_commission);
            }
            let ag_comm_rate = document.getElementById('ag_comm_rate');
            if (finalPremiumInput) {
                formatNumber(ag_comm_rate);
            }


        });

        $(document).ready(function() {


            let division = parseInt($('#division option:selected').val());
            let crldivisions = JSON.parse(localStorage.getItem('crldivisions'));
            $('#lead_handler').empty();


            $('#lead_handler').append('<option value="">Select lead</option>');

            crldivisions.forEach(function(crlead) {
                if (crlead.divisions_id === division) {

                    $('#lead_handler').append(
                        '<option value="' + crlead.users_id + '">' + crlead.firstname + ' ' + crlead
                        .lastname + '</option>'
                    );
                }
            });

            let selectedLead = '{{ $lead->cr_handler }}';

            if (selectedLead) {
                $('#lead_handler').val(selectedLead).trigger('change');
            }


            $.ajax({
                type: "GET",
                data: {
                    'division': division
                },
                url: "{{ route('get_division_classes') }}",
                success: function(resp) {
                    if (resp.status == 1) {

                        $('#insurance_class').empty();

                        $('#insurance_class').append($("<option />").val('').text(
                            'Select class'));
                        $.each(resp.classes, function() {
                            var selected = (this.id ==
                                    '{{ $lead->class_of_insurance }}') ?
                                'selected' : '';
                            $('#insurance_class').append($("<option />").val(this
                                .id).text(this
                                .class_name).attr('selected', selected));
                        });
                    }
                }
            })


            let prospect = "{{ $prospect }}"
            let ins_class = '';

            if (prospect != null && prospect != '' && prospect != undefined) {
                $.ajax({
                    type: "GET",
                    data: {
                        'prospect': prospect
                    },
                    url: "{{ route('get_prospect_details') }}",
                    success: function(response) {

                        let resp = response.details;
                        localStorage.setItem('crldivisions', JSON.stringify(response.crdivisions))
                        if (response.details.client_type != 'I') {

                            $('#idTypeDiv').hide()
                            $('#genderDiv').hide();
                            $('#idNoDiv').hide();

                            $("#id_type").attr('required', false)
                            $('#gender_code').attr('required', false)
                            $("#identity_no").attr('required', false)
                        }

                        $('#prospect_name').text(resp.fullname);
                        $('#postal_address').text(resp.postal_address);
                        $('#postal_code').text(resp.postal_code);
                        $('#prospect_id').val(resp.opportunity_id);
                        $('#full_name').val(resp.fullname);
                        $('#corporate_name').val(resp.fullname);
                        $('#phone_no0').val(resp.phone)
                        $('#phone_1').val(resp.phone)
                        $('#email').val(resp.email);
                        $('#contact_name').val(resp.contact_name);
                        $('#town').val(resp.town);
                        $('#postal_address').val(resp.postal_address);
                        $('#postal_code').val(resp.postal_code);
                        $('#contact_position').val(resp.contact_position);
                        $('#address_3').val(resp.physical_address);
                    }
                })
            }



            $('body').on('change', '.document_file', function() {

                let file = $(this)[0].files[0];
                let id = $(this).attr('id')
                let id_length = id.length
                let rowID = id.slice(13, id_length)


                let formData = new FormData();
                formData.append('doc', file);

                $.ajax({
                    type: "POST",
                    data: formData,
                    url: "{{ route('doc_preview') }}",
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#preview" + rowID).attr('data-file', resp);

                    }
                })
            })

            $('body').on('click', '.preview', function(e) {
                $('object').attr('data', '');
                var doc = $(this).attr('data-file');

                $('#doc_view').html('<iframe src="' + doc + '" width="100%" height="900"></iframe>');

                $('#v_docs').modal('show');



            });


            $('#incorporation_div').hide();

            // enable disabled corporate fields starts here
            $("select#client_type").change(function() {
                let ctype = $("#client_type").val();
                if (ctype === "I") {
                    $('#dob_div label').text('Date of Birth')
                    $('#corporate').hide();
                    $('#occupationDiv').show();
                    $('#salutation_code').show();
                    $('#full_name_div').show();
                    $('#customer_id').show();
                    $("#id_type").prop('disabled', false);
                    $("#pin_no").prop('disabled', false);
                    $("#gender_code").prop('disabled', false);
                    $('#occupation_code').prop('disabled', false);
                    $('#dobDiv').show();
                    $('#genderDiv').show();
                    $('#idTypeDiv').show();
                    $('#idNoDiv').show();
                    $('#incorporation_div').hide();

                } else {
                    $('#dob_div label').text('Date of Registration')
                    $('#corporate').show();
                    $('#salutation_code').hide();
                    $('#full_name_div').hide();
                    $('#dobDiv').hide();
                    $('#genderDiv').hide();
                    $('#idTypeDiv').hide();
                    $('#idNoDiv').hide();
                    $('#occupationDiv').show();
                    $('#incorporation_div').show();
                }
            });

            //set identity number label on load
            $('#id_num_type').text('National ID/Passport No.');

            //set identity number label on change of type
            $("select#id_type").change(function() {
                let iDType = $("#id_type").val();

                if (iDType === 'N') {
                    $('#id_num_type').text('National ID');
                } else if (iDType === 'M') {
                    $('#id_num_type').text('Millitary ID');
                } else if (iDType === 'P') {
                    $('#id_num_type').text('Passport Number');
                } else if (iDType === 'F') {
                    $('#id_num_type').text('Foreigners ID');
                } else {
                    $('#id_num_type').text('National ID');
                }

                $(this).trigger("chosen:updated");
            });
            var current_fs, next_fs, previous_fs; //fieldsets
            var opacity;
            var current = 1;
            var steps = $("fieldset").length;




            $('#division').on('change', function() {
                let division = parseInt($('#division option:selected').val())

                $('.division_doc').each(function() {
                    let id = $(this).attr('id');

                    if (id) {
                        let id_length = id.length;
                        let rowID = id.slice(3, id_length);


                        if (rowID == division) {
                            $('#doc' + rowID).removeClass('d-none')
                        } else {
                            $('#doc' + rowID).addClass('d-none')
                        }
                    }
                });


                $('#lead_handler').empty();

                $('#lead_handler').append('<option value="">Select lead</option>');

                let crldivisions = JSON.parse(localStorage.getItem('crldivisions'));
                crldivisions.forEach(function(crlead) {

                    if (crlead.divisions_id === division) {

                        $('#lead_handler').append(
                            '<option value="' + crlead.id + '">' + crlead.firstname +
                            ' ' + crlead.lastname + '</option>'
                        );
                    }

                });


                $('#doc' + division).show()
                $.ajax({
                    type: "GET",
                    data: {
                        'division': division
                    },
                    url: "{{ route('get_division_classes') }}",
                    success: function(resp) {

                        if (resp.status == 1) {
                            $('#insurance_class').empty()
                            $('#insurance_class').append($("<option />").val('').text(
                                'Select class'));
                            $.each(resp.classes, function() {
                                $('#insurance_class').append($("<option />").val(this
                                    .id).text(this.class_name));
                            });
                        }
                    }
                })


            })

            $("#msformVerify").submit(function(e) {
                e.preventDefault()
                var formData = new FormData(this);

                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure you have verified all the details of the prospect ?',
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'post',
                            data: formData,
                            url: $(this).attr('action'),
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                console.log(res)
                                if (res.status === 200) {
                                    Swal.fire({
                                            icon: 'success',
                                            title: 'Verification was successs! ',
                                            showDenyButton: false,
                                            showCancelButton: true,
                                            confirmButtonText: 'Yes'
                                        })
                                        .then((result) => {
                                            if (result.isConfirmed) {
                                                window.history.back();
                                            }
                                        })

                                } else if (res.status === 201) {
                                    let details = {
                                        'prospectId': res.prospectId,
                                        'mail': res.email
                                    }
                                    localStorage.setItem('details', JSON.stringify(
                                        details))

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Verification was successs, would you like to draft email for client ? ',
                                        showDenyButton: false,
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes'
                                    }).then((result) => {
                                        if (result.isConfirmed) {

                                            const details = localStorage
                                                .getItem(
                                                    'details');


                                            window.location.href =
                                                `${res.redirect}?details=${encodeURIComponent(details)}`;

                                            setTimeout(() => {
                                                localStorage.removeItem(
                                                    'details')
                                            }, 3000);
                                        }
                                    })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        text: 'Error, Verifying prospect'
                                    });
                                }
                            }
                        });
                    }

                })
            })

        });
    </script>
    <style>
        .document-item {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            /* height: 100%; */
        }
    </style>
@endsection
