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
            /* z-index: 0; */
            border: none;
            position: relative
        }

        .primary-color {
            color: #E1251B;
        }

        hr {
            margin: 20px 0px;
        }

        .table th {
            font-size: 14px !important;
            font-weight: bold !important;
        }

        .table tbody td {
            font-size: 12px !important;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card px-0 pt-4 pb-0 mb-3">
                    <h5 class="text-center text-lg-start mb-0 mx-2">
                        Prospect Handover :
                        {{ $allVariables['prospProperties']->insured_name ?? 'Null' }}
                        <span class="primary-color" id="prospect_name"></span>
                    </h5>
                    <hr>
                    <div class="card-body">
                        <form id="msform">
                            @csrf
                            <input type="hidden" name="agent_onboard_client" value="Y">
                            <input type="hidden" name="prospect_id" value="{{ $allVariables['pipeid'] }}">
                            <fieldset>

                                {{-- beginning new code --}}

                                <div class="form-card" id="fac_section">
                                    <div class="individual">
                                        <b class="primary-color">Cedant Details</b>
                                        <hr>
                                        <div class="row mb-4">
                                            {{-- {{ dd($allVariables) }} --}}
                                            <x-OnboardingInputDiv id="prequalification_div">
                                                @php
                                                    $selectedBus = collect($allVariables['types_of_bus'])->firstWhere(
                                                        'bus_type_id',
                                                        $allVariables['prospProperties']->type_of_bus ?? 'N/A',
                                                    );
                                                @endphp

                                                <x-Input name="type_of_bus_display" id="type_of_bus_display" req=""
                                                    inputLabel="Type of Bus"
                                                    value="{{ firstUpper($selectedBus->bus_type_name ?? '') }}" />

                                                <input type="hidden" name="type_of_bus"
                                                    value="{{ $selectedBus->bus_type_id ?? '' }}">
                                            </x-OnboardingInputDiv>


                                            <x-OnboardingInputDiv>
                                                @foreach ($allVariables['customers'] as $customer)
                                                    @if ($customer->customer_id == $allVariables['prospProperties']->customer_id ?? 'N/A')
                                                        <x-Input name="customer_name_display"
                                                            value="{{ firstUpper($customer->name) ?? 'N/A' }}"
                                                            inputLabel="Cedant" req="" />
                                                        <input type="hidden" name="customer_id"
                                                            value="{{ $customer->customer_id ?? 'N/A' }}">
                                                    @endif
                                                @endforeach
                                            </x-OnboardingInputDiv>



                                            <x-OnboardingInputDiv id="countryDiv">
                                                <x-Input name="client_type" id="client_type" req=""
                                                    inputLabel="Lead Type"
                                                    value="{{ isset($allVariables['prospProperties']) ? $allVariables['prospProperties']->client_type : '' }}" />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="leadNameDiv">
                                                <x-Input name="quote_number" id="quote_number"
                                                    value="{{ $allVariables['quotes'][0]['quote_number'] ?? '' }}"
                                                    inputLabel="Ref No" req="" />

                                            </x-OnboardingInputDiv>


                                            <x-OnboardingInputDiv>
                                                <x-Input name="lead_year_display" id="lead_year_display" req=""
                                                    inputLabel="Year"
                                                    value="{{ optional(collect($allVariables['pipeYear'])->firstWhere('id', (int) ($allVariables['prospProperties']->pip_year ?? 0)))->year ?? 'N/A' }}" />

                                                <input type="hidden" name="lead_year"
                                                    value="{{ $allVariables['prospProperties']->pip_year ?? '' }}">
                                            </x-OnboardingInputDiv>


                                            <x-OnboardingInputDiv>
                                                <x-Input id="client_category_display" req=""
                                                    inputLabel="Insured Category"
                                                    value="{{ $allVariables['prospProperties']->client_category == 'N' ? 'New Prospect' : ($allVariables['prospProperties']->client_category == 'O' ? 'Organic Growth' : 'N/A') }}" />

                                                <input type="hidden" name="client_category"
                                                    value="{{ $allVariables['prospProperties']->client_category ?? 'N/A' }}">
                                            </x-OnboardingInputDiv>


                                            <x-OnboardingInputDiv id="countryDiv">
                                                <label for="country_display">Country</label>
                                                <x-Input id="country_display" req="" inputLabel="Country"
                                                    value="{{ optional(collect($countries)->firstWhere('country_iso', $allVariables['prospProperties']->country_code))->country_name ?? 'N/A' }}" />
                                                <input type="hidden" name="country_code"
                                                    value="{{ $allVariables['prospProperties']->country_code ?? 'N/A' }}">
                                            </x-OnboardingInputDiv>


                                            <x-OnboardingInputDiv>
                                                <label for="branch_display">Branch</label>
                                                <x-Input id="branch_display" req="" inputLabel="Branch"
                                                    value="{{ optional(collect($allVariables['branches'])->firstWhere('branch_code', $allVariables['prospProperties']->branchcode))->branch_name ?? 'N/A' }}" />

                                                <!-- Hidden input to store the branch code -->
                                                <input type="hidden" name="branchcode"
                                                    value="{{ $allVariables['prospProperties']->branchcode ?? 'N/A' }}">
                                            </x-OnboardingInputDiv>
                                            <x-OnboardingInputDiv id="lead_owner_div">
                                                <x-Input inputLabel="Prospect Lead"
                                                    value="{{ optional(collect($allVariables['users'])->firstWhere('id', optional($allVariables['prospProperties'])->lead_owner))->name ?? 'N/A' }}"
                                                    req="" />

                                                <input type="hidden" name="lead_owner"
                                                    value="{{ optional($allVariables['prospProperties'])->lead_owner ?? 'N/A' }}">
                                            </x-OnboardingInputDiv>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-sm-3 fac_instalment-section" id="fac_installments_box">
                                            <div class="col-md-12">
                                                <B class="primary-color">Insurance Details</B>

                                                <input type="hidden" value="0" class="form-control section"
                                                    id="installment_total_amount" />
                                                <div id="fac-installments-section"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="form-group" id="fac_section">
                                        <div class="row row-cols-12">
                                            <div class="col-sm-3 class_group_div fac_section_div">
                                                <label class="form-label required">Division</label>
                                                <div class="cover-card">
                                                    <x-Input inputLabel=""
                                                        value="{{ optional(collect($allVariables['reinsdivisions'])->firstWhere('division_code', optional($allVariables['prospProperties'])->divisions))->division_name ?? 'N/A' }}"
                                                        req="" />

                                                    <input type="hidden" name="division"
                                                        value="{{ optional($allVariables['prospProperties'])->divisions ?? 'N/A' }}">

                                                </div>

                                            </div>
                                            <!-- Class Groups -->
                                            <div class="col-sm-3 class_group_div fac_section_div">
                                                <label class="form-label required">Class Group</label>
                                                <div class="cover-card">
                                                    @php
                                                        $selectedClassGroup = collect(
                                                            $allVariables['classGroups'],
                                                        )->firstWhere(
                                                            'group_code',
                                                            $allVariables['prospProperties']->class_group,
                                                        );
                                                    @endphp

                                                    <x-Input name="class_group_display" id="class_group_display"
                                                        req="" inputLabel=" "
                                                        value="{{ $selectedClassGroup->group_name ?? 'N/A' }}" />

                                                    <input type="hidden" name="class_group"
                                                        value="{{ $selectedClassGroup->group_code ?? '' }}">
                                                </div>

                                            </div>

                                            <!-- Class -->
                                            <div class="col-sm-3 fac_section_div">
                                                <label class="form-label required">Class Name</label>
                                                <div class="cover-card">
                                                    @php
                                                        $selectedClass = collect($allVariables['class'])->firstWhere(
                                                            'class_code',
                                                            $allVariables['prospProperties']->classcode,
                                                        );
                                                    @endphp

                                                    <x-Input name="class_display" id="class_display" req=""
                                                        inputLabel=""
                                                        value="{{ $selectedClass->class_name ?? 'N/A' }}" />

                                                    <input type="hidden" name="classcode"
                                                        value="{{ $selectedClass->class_code ?? '' }}">
                                                </div>

                                            </div>

                                            <!-- Insured Name -->
                                            <div class="col-sm-3 fac_section_div">
                                                <label for="">Insured Name</label>
                                                <div class="cover-card">
                                                    <x-Input name="insured_name" id="insured_name" req=""
                                                        inputLabel=""
                                                        value="{{ $allVariables['prospProperties']->insured_name ?? '' }}"
                                                        oninput="this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());" />

                                                </div>
                                            </div>
                                            <x-OnboardingInputDiv id="industry_div">
                                                <x-Input name="industry" inputLabel="Industry"
                                                    value="{{ $allVariables['prospProperties']->industry ?? 'N/A' }}"
                                                    req="" />

                                            </x-OnboardingInputDiv>
                                            <div class="col-sm-3 fac_section_div">
                                                <label class="form-label required">Offered Date<span style="color: red;">*</span></label>
                                                <input type="date" class="form-control form-control fac_section"
                                                    id="offered_date" name="offered_date"
                                                    value="{{ $handover_approval ? $handover_approval->inception_date : '' }}"
                                                    required />

                                            </div>

                                            <div class="col-sm-3 fac_section_div">
                                                <label class="form-label required">Currency</label>
                                                <div class="cover-card">
                                                    <x-Input inputLabel=""
                                                        value="{{ optional(collect($allVariables['currencies'] ?? [])->firstWhere('currency_code', $allVariables['prospProperties']->currency_code ?? ''))->currency_name ?? 'N/A' }}"
                                                        req="" />
                                                    <input type="hidden" name="currency_code" id="currency_code"
                                                        value="{{ $allVariables['prospProperties']->currency_code ?? '' }}">


                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <label class="form-label required">E. Rate</label>
                                                <x-Input name="today_currency" id="today_currency" req=""
                                                    inputLabel=""
                                                    value="{{ number_format(old('today_currency', optional($allVariables['prospProperties'])->today_currency ?? 0), 2) }}"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    change="this.value=numberWithCommas(this.value)" />


                                            </div>
                                            <!-- Sum Insured Type -->
                                            <x-OnboardingInputDiv>
                                                <!-- Display Sum Insured Name -->
                                                <x-Input inputLabel="Sum Insured Type" req=""
                                                    value="{{ optional(collect($allVariables['types_of_sum_insured'] ?? []))->firstWhere('sum_insured_code', $allVariables['prospProperties']->sum_insured_type ?? '')->sum_insured_name ?? 'N/A' }}" />
                                                <input type="hidden" name="sum_insured_type" id="sum_insured_type"
                                                    value="{{ $allVariables['prospProperties']->sum_insured_type ?? '' }}">
                                            </x-OnboardingInputDiv>
                                            <div class="col-xl-3 fac_section_div mt-2">

                                                <x-Input name="total_sum_insured" id="total_sum_insured" req=""
                                                    inputLabel="100% Sum Insured"
                                                    value="{{ number_format(old('total_sum_insured', optional($allVariables['prospProperties'])->total_sum_insured ?? 0), 2) }}"
                                                    onkeyup="this.value=numberWithCommas(this.value)" />

                                            </div>
                                            <div class="col-sm-3">
                                                <label class="form-label">Excess Type<span style="color: red;">*</span></label>

                                                <Select class="form-control" name="excess_type" id="excess_type"
                                                    inputLabel="Excess Type" req="required"
                                                    :value="{{ $handover_approval ? $handover_approval->excess_type : '' }}">
                                                    <option value="">--Select excess type--</option>
                                                    <option value="R"
                                                        {{ old('excess_type', $handover_approval->excess_type ?? '') === 'R' ? 'selected' : '' }}>
                                                        Rate</option>
                                                    <option value="A"
                                                        {{ old('excess_type', $handover_approval->excess_type ?? '') === 'A' ? 'selected' : '' }}>
                                                        Amount</option>
                                                </Select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="excess*_labe" id="excess_label">Excess(%)</label><span style="color: red;">*</span>
                                                <input class="form-control amount" name="excess" id="excess" required
                                                    value="{{ $handover_approval ? $handover_approval->excess : '' }}" />
                                            </div>
                                            <x-OnboardingInputDiv style="display: flex; align-items: center; gap: 10px;">
                                                <label for="max_min">Max/Min<span style="color: red;">*</span></label>
                                                <input name="max_min" id="max_min" placeholder="" req="required"
                                                    class="form-control amount" style="width: 150px;"
                                                    value="{{ $handover_approval ? $handover_approval->{'max/min'} : '' }}" />

                                                <label>
                                                    <input type="radio" name="range" value="min" required
                                                        {{ $handover_approval && $handover_approval->range == 'min' ? 'checked' : '' }} />
                                                    Min
                                                </label>

                                                <label>
                                                    <input type="radio" name="range" value="max" required
                                                        {{ $handover_approval && $handover_approval->range == 'max' ? 'checked' : '' }} />
                                                    Max
                                                </label>

                                            </x-OnboardingInputDiv>
                                            <div class="col-sm-3 mt-2">
                                                <label for="apply_eml">Apply EML</label>
                                                <div class="cover-card">
                                                    {{-- value="{{ $allVariables['prospProperties']->reins_comm_type === 'R' ? 'Rate' : ($allVariables['prospProperties']->reins_comm_type === 'A' ? 'Amount' : 'N/A') }}" /> --}}
                                                    <input type="text" class="form-control"
                                                        value="{{ isset($allVariables['prospProperties']->apply_eml) ? ($allVariables['prospProperties']->apply_eml == 'Y' ? 'Yes' : ($allVariables['prospProperties']->apply_eml == 'N' ? 'No' : 'N/A')) : 'N/A' }}"
                                                        readonly />

                                                    <input type="hidden" name="apply_eml"
                                                        value="{{ isset($allVariables['prospProperties']->apply_eml) ? $allVariables['prospProperties']->apply_eml : '' }}" />

                                                </div>
                                            </div>
                                            @if ($allVariables['prospProperties']->apply_eml == 'Y')
                                                <div class="col-sm-3 eml-div mt-2">
                                                    <label class="form-label">EML Rate</label>
                                                    <div class="cover-card">
                                                        <input type="number" class="form-control fac_section"
                                                            id="eml_rate" name="eml_rate"
                                                            value="{{ old('eml_rate', $allVariables['prospProperties']->eml_rate ?? 'N/A') }}"
                                                            min="0" max="100" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 eml-div mt-2">
                                                    <label class="form-label">EML Amount</label>
                                                    <div class="cover-card">
                                                        <input type="text" class="form-control fac_section amount"
                                                            id="eml_amt" name="eml_amt"
                                                            value="{{ number_format(old('eml_amt', $allVariables['prospProperties']->eml_amt ?? 0), 2) }}"
                                                            required>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="col-sm-3 mt-2">
                                                <x-Input name="effective_sum_insured" id="effective_sum_insured"
                                                    req="" inputLabel="Effective Sum Insured"
                                                    value="{{ $allVariables['prospProperties']->effective_sum_insured ?? 'N/A' }}" />

                                            </div>
                                            <div class="col-sm-3 mt-2">
                                                <label class="form-label">Risk Details</label>
                                                <textarea class="form-control  section fac_section resize-none" id="risk_details" name="risk_details">{{ $allVariables['prospProperties']->risk_details ?? 'N/A' }}</textarea>
                                            </div>
                                          

                                        </div>
                                        <div class="row row-cols-12 mt-2">
                                              <div class="col-xl-3 fac_section_div mt-2">
                                                <label class="form-label">Cedant Premium</label>
                                                <input type="text" class="form-control  fac_section" id="cede_premium"
                                                    name="cede_premium"
                                                    value="{{ number_format($allVariables['prospProperties']->cede_premium, 2) }}"
                                                    onkeyup="this.value=numberWithCommas(this.value)">
                                            </div>
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Reinsurer Premium</label>
                                                <input type="text" class="form-control  fac_section" id="rein_premium"
                                                    name="rein_premium"
                                                    value={{ number_format($allVariables['prospProperties']->rein_premium, 2) }}
                                                    onkeyup="this.value=numberWithCommas(this.value)">
                                            </div>
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Written Share(%)</label>
                                                <input type="number" class="form-control fac_section"
                                                    id="fac_share_offered" name="fac_share_offered"
                                                    value="{{ $allVariables['prospProperties']->fac_share_offered ?? 'N/A' }}"
                                                    max="100" min="0">
                                            </div>
                                            <!-- Cedant Comm Rate -->
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Cedant Comm rate(%)</label>
                                                <input type="text" class="form-control fac_section"
                                                    aria-label="comm_rate" id="comm_rate" name="comm_rate"
                                                    value="{{ $allVariables['prospProperties']->comm_rate ?? 'N/A' }}"
                                                    max="100" min="0">
                                            </div>

                                            <!-- Cedant Comm Amount -->
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Cedant Comm Amount</label>
                                                <input type="text" class="form-control fac_section"
                                                    aria-label="comm_amt" id="comm_amt" name="comm_amt"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    value="{{ number_format($allVariables['prospProperties']->comm_amt, 2) }}">
                                            </div>

                                            <!-- Reinsurer Comm Type -->
                                            <div class="col-xl-3 fac_section_div reins_comm_type_div">
                                                <label class="form-label">Reinsurer Comm Type</label>
                                                <div class="cover-card">
                                                    <x-Input name="reins_comm_type" id="reins_comm_type" req=""
                                                        inputLabel=""
                                                        value="{{ $allVariables['prospProperties']->reins_comm_type === 'R' ? 'Rate' : ($allVariables['prospProperties']->reins_comm_type === 'A' ? 'Amount' : 'N/A') }}" />

                                                </div>
                                            </div>

                                            <!-- Reinsurer Comm Rate -->
                                            <div class="col-xl-3 fac_section_div reins_comm_rate_div">
                                                <label class="form-label">Reinsurer Comm rate(%)</label>
                                                <input type="text" class="form-control fac_section reins_comm_rate"
                                                    aria-label="reins_comm_rate" id="reins_comm_rate"
                                                    name="reins_comm_rate"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    value="{{ $allVariables['prospProperties']->reins_comm_rate ?? 'N/A' }}">
                                            </div>

                                            <!-- Reinsurer Comm Amount -->
                                            <div class="col-xl-3 fac_section_div reins_comm_amt_div">
                                                <label class="form-label">Reinsurer Comm Amount</label>
                                                <input type="text" class="form-control fac_section reins_comm_amt"
                                                    aria-label="reins_comm_amt" id="reins_comm_amt" name="reins_comm_amt"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    onchange="this.value=numberWithCommas(this.value)"
                                                    value="{{ number_format($allVariables['prospProperties']->reins_comm_amt, 2) }}">
                                            </div>

                                            <!-- Brokerage Commission Type -->
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Brokerage Commission Type</label>
                                                <div class="cover-card">

                                                    <x-Input name="brokerage_comm_type" id="brokerage_comm_type"
                                                        req="" inputLabel=""
                                                        value="{{ $allVariables['prospProperties']->brokerage_comm_type == 'R' ? 'Rate' : ($allVariables['prospProperties']->brokerage_comm_type == 'A' ? 'Quoted Amount' : 'N/A') }}" />

                                                </div>
                                            </div>

                                            <!-- Brokerage Commission Amount -->
                                            <div class="col-xl-3 fac_section_div brokerage_comm_amt_div">
                                                <label class="form-label">Brokerage Amount</label>
                                                <input type="text" class="form-control fac_section amount"
                                                    id="brokerage_comm_amt" name="brokerage_comm_amt"
                                                    value="{{ isset($allVariables['prospProperties']->brokerage_comm_amt) ? number_format($allVariables['prospProperties']->brokerage_comm_amt, 2) : '' }}" />
                                            </div>

                                            <!-- Brokerage Commission Rate -->
                                            <div class="col-xl-3 fac_section_div brokerage_comm_rate_div">
                                                <label class="form-label" id="brokerage_comm_rate_label">Brokerage
                                                    Rate</label>
                                                <input type="text" class="form-control fac_section amount"
                                                    id="brokerage_comm_rate" name="brokerage_comm_rate"
                                                    value="{{ $allVariables['prospProperties']->brokerage_comm_rate ?? '' }}">
                                            </div>
                                            <div class="col-xl-3 fac_section_div brokerage_comm_rate_amt_div">
                                                <label class="form-label" id="brokerage_comm_rate_amount_label">Brokerage
                                                    Rate Amount</label>
                                                <input type="text" class="form-control fac_section amount"
                                                    id="brokerage_comm_rate_amt" name="brokerage_comm_amt"
                                                    value="{{ $allVariables['prospProperties']->brokerage_comm_amt ?? '' }}"
                                                    readonly>
                                            </div>

                                            <!-- Hidden VAT Charged -->
                                            <input type="hidden" class="vat_charged fac_section" id="vat_charged"
                                                name="vat_charged" value="0">

                                        </div>
                                    </div>
                                    <!-- Treaty Group Section -->

                                </div>
                                <!-- Engagement Details -->
                                <B class="primary-color" style="margin-left:10px">Cover Period</B>
                                <hr>
                                <div class="row" style="margin-left:10px">
                                    <x-OnboardingInputDiv id="date_effective_div">
                                        <x-DateInput name="effective_date" id="effective_date"
                                            placeholder="Enter cover start date" inputLabel="Cover Start Date"
                                            req="required"
                                            value="{{ $allVariables['prospProperties']->effective_date ?? 'N/A' }}" />
                                    </x-OnboardingInputDiv>

                                    <x-OnboardingInputDiv id="date_closing_div">
                                        <x-DateInput name="closing_date" id="closing_date"
                                            placeholder="Enter bid closing date" inputLabel="Cover End Date"
                                            req=""
                                            value="{{ $allVariables['prospProperties']->closing_date ?? 'N/A' }}" />
                                    </x-OnboardingInputDiv>

                                    <x-OnboardingInputDiv id="handler">
                                        <x-SearchableSelect name="handler" id="handler" req="required"
                                            inputLabel="Account Handler">
                                            <option value="" disabled selected>Select handler</option>
                                            @foreach ($allVariables['users'] as $user)
                                                <option value="{{ $user->id }}"
                                                    @if (isset($handover_approval) && $handover_approval->handler == $user->id) selected @endif>
                                                    {{ firstUpper($user->name) }}</option>
                                            @endforeach
                                        </x-SearchableSelect>
                                    </x-OnboardingInputDiv>
                                    @php
                                        $selectedApprovers = isset($handover_approval)
                                            ? json_decode($handover_approval->approver, true)
                                            : [];
                                    @endphp
                                    <x-OnboardingInputDiv id="approver">
                                        <x-SearchableSelect name="approver[]" id="approver" req="required"
                                            inputLabel="Approver" multiple>
                                            <option value="">Select approver</option>
                                            @foreach ($allVariables['users'] as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ isset($handover_approval) && in_array($user->id, $selectedApprovers) ? 'selected' : '' }}>
                                                    {{ firstUpper($user->name) }}
                                            @endforeach
                                        </x-SearchableSelect>

                                    </x-OnboardingInputDiv>
                                    <x-OnboardingInputDiv>
                                        <label class="form-label">Remarks</label>
                                        <textarea class="form-control section fac_section resize-none" id="remarks" name="remarks">{{ $handover_approval ? $handover_approval->remarks : '' }}</textarea>
                                    </x-OnboardingInputDiv>



                                </div>
                                <div class="row my-md-3">
                                    <B class="primary-color">Reinsurers</B>
                                    <div class="m-0">
                                        <hr>
                                    </div>
                                    <div class="container">
                                        <h6 class="mb-3">Filter Reinsurers by Stage</h6>
                                        <input type="hidden" id="opportunity_id" value="{{ $allVariables['pipeid'] }}">
                                        <div class="row">
                                            <label for="stage" class="col-form-label col-md-2">Select
                                                Stage:</label>
                                            <div class="col-md-2">
                                                <select id="stage" class="form-control">
                                                    <option value="">-- Select Stage --</option>
                                                    <option value="2">Stage 2</option>
                                                    <option value="3">Stage 3</option>
                                                    <option value="4">Stage 4</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mt-4 col-md-8">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Reinsurer</th>
                                                        <th>Written Share</th>
                                                        <th>Signed Share</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="reinsurer-body">
                                                    <tr>
                                                        <td colspan="4" class="text-center">Select a stage to load
                                                            reinsurers.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                                <div class="row my-md-3">
                                    @if ($allVariables['decline_reinsurers']->isNotEmpty())
                                        @if ($allVariables['decline_reinsurers'])
                                            <B class="primary-color">Reinsurers Declined</B>
                                            <div class="m-0">
                                                <hr>
                                            </div>

                                            <div class="col-md-8" id="decline_reason">
                                                <table class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Reinsurer</th>
                                                            <th>Reason</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($allVariables['decline_reinsurers'] as $index => $item)
                                                            <tr>
                                                                <td>{{ $item['customer_name']['name'] }}</td>
                                                                <td>
                                                                    <button type="button" class="btn btn-link p-0"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#reasonModal{{ $index }}">
                                                                        View Reason
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>


                                            @foreach ($allVariables['decline_reinsurers'] as $index => $item)
                                                <div class="modal fade" id="reasonModal{{ $index }}"
                                                    tabindex="-1" aria-labelledby="reasonModalLabel{{ $index }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="reasonModalLabel{{ $index }}">
                                                                    Decline Reason</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ $item['reason'] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>



                                {{-- {{dd($allVariables['decline_reinsurers'])}} --}}

                                @if (isset($approval) && $approval == 1)
                                    <div class="row my-md-3">
                                        <B class="primary-color">Document Attachments</B>
                                        <div class="m-0">
                                            <hr>
                                        </div>
                                        @php
                                            // $baseAssetUrl = asset('uploads');
                                            $baseAssetUrl = Storage::disk('s3')->url('uploads');
                                        @endphp

                                        @foreach ($prosp_doc as $index => $doc)
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <label for="document_file">Document
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-4">
                                                        <input type="text" name="document_name[]"
                                                            class="form-control mt-2" value="{{ $doc->description }}" />
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group mt-2">
                                                            <input type="text" name="document_file[]"
                                                                id="document_file{{ $doc->id }}"
                                                                class="form-control " value="{{ $doc->file }}" />

                                                        </div>
                                                    </div>
                                                    <div class="col-1" style="margin-top: 12px">
                                                        <a href="{{ $baseAssetUrl . '/' . $doc->file }}" target="_blank"
                                                            class="btn btn-sm ">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                @else
                                    <div class="row my-md-3">
                                        <B class="primary-color">Document Attachments</B>
                                        <div class="m-0">
                                            <hr>
                                        </div>



                                        @foreach ($docs as $index => $doc)
                                            <div class="col-md-6"
                                                @if (!is_null($doc->division)) id=doc{{ $doc->division }} @endif>
                                                <div class="row">
                                                    <div class="col-2">
                                                        <label for="document_file">Document @if ($doc->mandatory === 'Y')
                                                                <font style="color:red;">*</font>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-4">
                                                        <input type="text" name="document_name[]"
                                                            class="form-control mt-2" value="{{ $doc->doc_type }}" />
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group mt-2">
                                                            <input type="file" name="document_file[]"
                                                                id="document_file{{ $doc->id }}"
                                                                {{-- {{logger($doc->mandatory)}} --}}
                                                                @if ($doc->mandatory === 'Y') required @endif
                                                                class="form-control " />
                                                            <button class="addDocfac btn btn-success"><i
                                                                    class="bx bx-plus"></i></button>
                                                        </div>
                                                    </div>

                                                    <div class="col-2" style="margin-top: 17px">
                                                        <i class="bx bx-show preview" id="preview{{ $doc->id }}">

                                                        </i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- <div class="row mt-1 {{ !is_null($doc->division) ? 'd-none  division_doc' : '' }}" @if (!is_null($doc->division)) id=doc{{ $doc->division }} @endif>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-4 pt-3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div> -->
                                        @endforeach

                                    </div>
                                    <div>
                                        @php
                                            $baseAssetUrl = Storage::disk('s3')->url('uploads');
                                        @endphp

                                        <div class="row my-md-3">
                                            <B class="primary-color">Documents Uploaded</B>
                                            <div class="m-0">
                                                <hr>
                                            </div>
                                            @foreach ($prosp_doc as $index => $doc)
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-2 mt-2">
                                                            <label for="document_file">Document
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <input type="text" class="form-control mt-2"
                                                                value="{{ $doc->description }}" />
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="input-group mt-2">
                                                                <input type="text"
                                                                    id="document_file{{ $doc->id }}"
                                                                    class="form-control " value="{{ $doc->file }}" />

                                                            </div>
                                                        </div>
                                                        <div class="col-1" style="margin-top: 12px">
                                                            <a href="{{ $baseAssetUrl . '/' . $doc->file }}"
                                                                target="_blank" class="btn btn-sm ">
                                                                <i class="bx bx-show"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                @endif

                                <div class="text-right mt-2">
                                    <button type="submit" id="submit" class="btn btn-success text-white">
                                        <span class="fa fa-save "></span> Handover
                                    </button>
                                </div>



                            </fieldset>





                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="v_docs" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="min-width:70%">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #cfd7e0 ">
                    <h5 class="modal-title">Document Preview</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="preview_iframe" width="100%" height="500px"
                        style="border: none; display: none;"></iframe>
                    <img id="preview_image" src="" style="max-width: 100%; display: none;" />
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="v_docs" role="dialog" aria-hidden="true">
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
    </div> --}}
@endsection
@push('script')
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
        $(document).ready(function() {
            $approval = @json($approval);
            //disable inputs
            if ($approval == 1) {
                $(document).find('input, select, textarea').prop('disabled', true);
                $('#stage').prop('disabled', false);
                $('#decline_reason').prop('disabled', false);
                $(document).find('.addDocfac').hide();
                $('#submit').hide();
            } else {
                $('#fac_section').find('input, select, textarea').prop('disabled', false);
                $('#fac_section').find('.addDocfac').show();
                $('#submit').show();
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let prospect = "{{ $prospect }}"
            let ins_class = '';

            // if (prospect != null && prospect != '' && prospect != undefined) {
            //     $.ajax({
            //         type: "GET",
            //         data: {
            //             'prospect': prospect
            //         },
            //         url: "{{ route('get_prospect_details') }}",
            //         success: function(response) {
            //             console.log(response.insurance_class);
            //             let resp = response.details;
            //             localStorage.setItem('crldivisions', JSON.stringify(response.crdivisions))

            //             $('#prospect_name').text(resp.fullname);
            //             $('#postal_address').text(resp.postal_address);
            //             $('#postal_code').text(resp.postal_code);
            //             $('#prospect_id').val(resp.opportunity_id);
            //             $('#client_type').val(resp.client_type).trigger('change');
            //             $('#client_category').val(resp.client_category).trigger('change');
            //             $('#division').val(resp.division).trigger('change');
            //             $('#division').trigger('change');
            //             $('#insurance_class').val(resp.insurance_class).trigger('change');
            //             $('#engage_type').val(resp.engage_type).trigger('change')
            //             $('#country').val(resp.country_code).trigger('change')
            //             $('#full_name').val(resp.fullname);
            //             $('#corporate_name').val(resp.fullname);
            //             $('#phone_no0').val(resp.phone)
            //             $('#phone_1').val(resp.phone)
            //             $('#bd_lead').val(resp.lead_handler).trigger('change');
            //             $('#occupation_code').val(resp.industry).trigger('change');
            //             $('#email').val(resp.email);
            //             $('#contact_name').val(resp.contact_name);
            //             $('#town').val(resp.town);
            //             $('#postal_address').val(resp.postal_address);
            //             $('#postal_code').val(resp.postal_code);
            //             $('#telephone').val(resp.telephone);
            //             $('#contact_position').val(resp.contact_position);
            //             $('#address_3').val(resp.physical_address);
            //         }
            //     })
            // }




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
            $(document).on('click', '.preview', function() {
                const fileInput = $(this).closest('.row').find(
                    'input[type="file"]'); // Get the correct file input
                const file = fileInput[0].files[0];

                if (file) {
                    const fileURL = URL.createObjectURL(file); // Create object URL


                    if (file.type === 'application/pdf') {
                        $('#preview_iframe').attr('src', fileURL).show();
                        $('#preview_image').hide();
                    } else if (file.type.startsWith('image/')) {
                        $('#preview_image').attr('src', fileURL).show();
                        $('#preview_iframe').hide();
                    } else {
                        alert('Preview not available for this file type');
                        return;
                    }

                    $('#v_docs').modal('show');
                } else {
                    alert('Please select a file first');
                }
            });


            // add new file
            let fileCounter = 0;
            $('body').on('click', '.addDocfac', function(event) {
                event.preventDefault();

                fileCounter++;

                const newFileInput = `
                <div class="row mt-3 new-document-row">
                     <div class="row">
                        <div class="col-2">
                                <label for="document_file">Document
                            </label>
                        </div>
                    </div>
                     <div class="col-4">
                        <input type="text" name="document_name[]" id="document_name${fileCounter}" class="form-control mt-2"/>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                        <input type="file" name="document_file[]" id="document_file${fileCounter}" 
                            class="form-control" />
                        <button class="btn btn-danger remove-file" type="button">
                            <i class="bx bx-minus"></i> 
                        </button>
                        </div>
                    </div>
                    <div class="col-2" style="margin-top: 12px">
                        <i class="bx bx-show preview" id="preview${fileCounter}" style="cursor:pointer;"> </i>
                    </div>
                </div>`;


                $(this).closest('.row').after(newFileInput);
            });

            $('body').on('click', '.remove-file', function() {
                $(this).closest('.new-document-row').remove();
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


            $("#submit").click(function(e) {
                e.preventDefault()

                var form = $("#msform");

                if ($("input[name='document_file[]']").length === 0) {
                    console.error("File input missing!");
                    return false;
                }
                // form.validate({
                //     errorElement: 'span',
                //     errorClass: 'text-danger fst-italic small',
                //     highlight: function(element, errorClass) {},
                //     unhighlight: function(element, errorClass) {},
                //     ignore: ":hidden:not(.division_doc):not([required])", // Ensure file inputs are not ignored
                //     rules: {
                //         first_name: {
                //             required: true
                //         },


                //     },
                //     messages: {
                //         "document_file[]": {
                //             required: "Please upload a required file!"
                //         }
                //     }
                // });



                // if (form.valid() != true) {
                //     alert('Form not valid! Please check your inputs.');
                //     return false;
                // }
                if (form.valid()) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Are you sure your data is correct before saving ?',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            let myform = document.getElementById("msform");
                            let formData = new FormData(myform);
                            $('#submit').html(
                                    '<span class="fa fa-spinner fa-spin"></span> Submitting...')
                                .prop('disabled', true);

                            $.ajax({
                                type: 'post',
                                data: formData,
                                url: "{{ route('client.stage') }}",
                                processData: false,
                                contentType: false,
                                success: function(res) {

                                    if (res.status == 200) {
                                        Swal.fire({
                                            icon: 'success',
                                            text: 'Prospect successfully Submitted  To Handover'
                                        })
                                        setTimeout(function() {
                                            window.location.href =
                                                '/pipelines_view';
                                        }, 2000);
                                    } else {
                                        $('#submit').attr('disabled', false)
                                        Swal.fire({
                                            icon: 'error',
                                            text: res.message
                                        });
                                    }
                                    $('#submit').html(
                                        '<span class="fa fa-save"></span> Submit Details'
                                    ).prop('disabled', false);

                                }
                            });


                        }
                    })
                } else {
                    form.reportValidity();

                }
            })

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
            $('#stage').on('change', function() {
                let stage = $(this).val();
                let opportunity_id = $('#opportunity_id').val();

                if (stage) {
                    $.ajax({
                        url: '{{ route('reinsurers.filter') }}',
                        type: 'GET',
                        data: {
                            stage: stage,
                            opportunity_id: opportunity_id
                        },
                        success: function(response) {
                            let rows = '';

                            if (response.reinsurers.length > 0) {
                                response.reinsurers.forEach(function(item) {
                                    let capitalizedName = item.reinsurer_name.split(' ')
                                        .map(word => word.charAt(0).toUpperCase() + word
                                            .slice(1).toLowerCase())
                                        .join(' ');
                                    rows += `<tr>
                                <td>${capitalizedName}</td>
                                <td>${item.written_share ?  item.written_share: 'N/A'} </td>
                                <td>${item.signed_share ? item.signed_share : 'N/A'}</td>
                            </tr>`;
                                });
                            } else {
                                rows =
                                    '<tr><td colspan="4" class="text-center">No reinsurers found.</td></tr>';
                            }

                            $('#reinsurer-body').html(rows);
                        },
                        error: function() {
                            $('#reinsurer-body').html(
                                '<tr><td colspan="4" class="text-danger text-center">Failed to load data.</td></tr>'
                            );
                        }
                    });
                } else {
                    $('#reinsurer-body').html(
                        '<tr><td colspan="4" class="text-center">Please select a stage.</td></tr>');
                }
            });

            function updateExcessLabel() {
                const excessType = $('#excess_type').val();
                if (excessType === 'R') {
                    $('#excess_label').text('Excess(%)');
                } else if (excessType === 'A') {
                    $('#excess_label').text('Excess Amount');
                } else {
                    $('#excess_label').text('Excess');
                }
            }

            updateExcessLabel();

            $('#excess_type').on('change', updateExcessLabel);
            
             $('#effective_date').on('change', function() {
                var effectiveDate = $(this).val();

                if (effectiveDate) {
                    var date = new Date(effectiveDate);
                    date.setFullYear(date.getFullYear() + 1);
                    date.setDate(date.getDate() - 1);
                    var closingDate = date.toISOString().split('T')[0]; // YYYY-MM-DD format
                    $('#closing_date').val(closingDate);
                }
            });


        });
    </script>
@endpush
