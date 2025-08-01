@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">
            @if ($trans_type == 'NEW')
                {{ 'New Cover' }}
            @elseif($trans_type == 'EXT')
                {{ 'Extra Endorsement' }}
            @elseif($trans_type == 'CNC')
                {{ 'Policy Cancellation' }}
            @elseif($trans_type == 'RFN')
                {{ 'Refund Endorsement' }}
            @elseif($trans_type == 'NIL')
                {{ 'NIL Endorsement' }}
            @elseif($trans_type == 'REN')
                {{ 'Policy Renewal' }}
            @elseif($trans_type == 'EDIT')
                {{ 'Edit Details' }}
            @elseif($trans_type == 'INS')
                {{ 'Instalment' }}
            @endif
        </h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('customer.info') }}">
                            {{ $customer->name }}
                        </a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        @if ($trans_type == 'NEW')
                            {{ 'New Cover' }}
                        @elseif($trans_type == 'EXT')
                            {{ 'Extra Endorsement' }}
                        @elseif($trans_type == 'CNC')
                            {{ 'Policy Cancellation' }}
                        @elseif($trans_type == 'RFN')
                            {{ 'Refund Endorsement' }}
                        @elseif($trans_type == 'NIL')
                            {{ 'NIL Endorsement' }}
                        @elseif($trans_type == 'REN')
                            {{ 'Policy Renewal' }}
                        @elseif($trans_type == 'EDIT')
                            {{ 'Edit Details' }}
                        @elseif($trans_type == 'INS')
                            {{ 'Instalment' }}
                        @endif
                    </li>
                    @if ($trans_type != 'NEW')
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $old_endt_trans->endorsement_no }}
                        </li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="cover-wrapper form-group">
        <form id="register_cover"
            action="{{ $trans_type == 'EDIT' ? route('cover.editCoverRegister') : route('cover.register') }}"
            method="POST">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="row row-cols-12">
                    <input type="text" name="customer_id" id="customer_id" value="{{ $customer->customer_id }}" hidden>
                    <input type="hidden" name="trans_type" id="trans_type" value="{{ $trans_type }}">
                    @if ($trans_type != 'NEW')
                        <input type="text" name="cover_no" id="cover_no" value="{{ $old_endt_trans->cover_no }}"
                            hidden>
                        <input type="text" name="endorsement_no" id="endorsement_no"
                            value="{{ $old_endt_trans->endorsement_no }}" hidden>
                    @endif

                    <!--type of business-->
                    <div class="col-sm-3">
                        <label class="form-label required">Business Type</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2" name="type_of_bus" id="type_of_bus"
                                @required(true) @if ($trans_type == 'REN' || $trans_type == 'EDIT' || $trans_type == 'NEW') @else @readonly(true) @endif>
                                @if ($trans_type == 'NEW')
                                    <option selected value="">Choose Business Type</option>
                                    @foreach ($types_of_bus as $type_of_bus)
                                        <option value="{{ $type_of_bus->bus_type_id }}">{{ $type_of_bus->bus_type_name }}
                                        </option>
                                    @endforeach
                                @elseif(
                                    $trans_type == 'REN' ||
                                        $trans_type == 'EDIT' ||
                                        $trans_type == 'EXT' ||
                                        $trans_type == 'CNC' ||
                                        $trans_type == 'RFN' ||
                                        $trans_type == 'NIL' ||
                                        $trans_type == 'INS')
                                    @foreach ($types_of_bus as $type_of_bus)
                                        @if ($type_of_bus->bus_type_id == $old_endt_trans->type_of_bus)
                                            <option value="{{ $type_of_bus->bus_type_id }}" selected>
                                                {{ $type_of_bus->bus_type_name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    {{-- cover type --}}
                    <div class="col-sm-3">
                        <label class="form-label required">Cover Type</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2" name="covertype" id="covertype">
                                @switch($trans_type)
                                    @case('NEW')
                                        <option selected value="">Choose Cover Type</option>
                                        @foreach ($covertypes as $covertype)
                                            @if ($covertype->status == 'A')
                                                <option value="{{ $covertype->type_id }}"
                                                    covertype_desc="{{ $covertype->short_description }}">
                                                    {{ $covertype->type_name }}</option>
                                            @endif
                                        @endforeach
                                    @break

                                    @case('CNC')
                                    @case('EXT')

                                    @case('RFN')
                                    @case('NIL')

                                    @case('INS')
                                        @foreach ($covertypes as $covertype)
                                            @if ($covertype->type_id == $old_endt_trans->cover_type && $covertype->status == 'A')
                                                <option value="{{ $covertype->type_id }}"
                                                    covertype_desc="{{ $covertype->short_description }}" selected>
                                                    {{ $covertype->type_name }}</option>
                                            @endif
                                        @endforeach
                                    @break

                                    @case('REN')
                                    @case('EDIT')
                                        @foreach ($covertypes as $covertype)
                                            @if ($covertype->type_id == $old_endt_trans->cover_type && $covertype->status == 'A')
                                                <option value="{{ $covertype->type_id }}"
                                                    covertype_desc="{{ $covertype->short_description }}" selected>
                                                    {{ $covertype->type_name }}</option>
                                            @else
                                                @if ($covertype->status == 'A')
                                                    <option value="{{ $covertype->type_id }}"
                                                        covertype_desc="{{ $covertype->short_description }}">
                                                        {{ $covertype->type_name }}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @break
                                @endswitch
                            </select>
                        </div>
                    </div>

                    {{-- binder policy --}}
                    <div class="col-sm-2" id="bindercoversec">
                        <label class="form-label required" id="binderlabel">Binder Policy</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2" name="bindercoverno" id="bindercoverno">
                                @if (
                                    $trans_type == 'CNC' ||
                                        $trans_type == 'RFN' ||
                                        $trans_type == 'EXT' ||
                                        $trans_type == 'REN' ||
                                        $trans_type == 'EDIT' ||
                                        $trans_type == 'INS')
                                    @if ($old_endt_trans->covertype == 'B' || $old_endt_trans->covertype == 'B')
                                        <option value="{{ $old_endt_trans->binder_cov_no }}">
                                            {{ $old_endt_trans->binder_cov_no }}</option>
                                    @endif
                                @endif
                            </select>
                        </div>
                    </div>

                    <!--branch-->
                    <div class="col-sm-2">
                        <label class="form-label required">Branch</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2" name="branchcode" id="branchcode" required>
                                @switch($trans_type)
                                    @case('NEW')
                                        <option selected value="">Choose Branch</option>

                                        @foreach ($branches as $branch)
                                            @if ($branch->status == 'A')
                                                <option value="{{ $branch->branch_code }}">{{ $branch->branch_name }}</option>
                                            @endif
                                        @endforeach
                                    @break

                                    @case('CNC')
                                    @case('EXT')

                                    @case('RFN')
                                    @case('NIL')

                                    @case('INS')
                                        @foreach ($branches as $branch)
                                            @if ($branch->branch_code == $old_endt_trans->branch_code and $branch->status == 'A')
                                                <option value="{{ $branch->branch_code }}" selected>{{ $branch->branch_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @break

                                    @case('REN')
                                    @case('EDIT')
                                        @foreach ($branches as $branch)
                                            @if ($branch->branch_code == $old_endt_trans->branch_code and $branch->status == 'A')
                                                <option value="{{ $branch->branch_code }}" selected>{{ $branch->branch_name }}
                                                </option>
                                            @else
                                                @if ($branch->status == 'A')
                                                    <option value="{{ $branch->branch_code }}">{{ $branch->branch_name }}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @break
                                @endswitch
                            </select>
                        </div>
                    </div>

                    {{-- Ceding broker  --}}
                    <div class="col-sm-2">
                        <label class="form-label required"> Ceding Broker Flag</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2" name="broker_flag" id="broker_flag" required>
                                @switch($trans_type)
                                    @case('NEW')
                                        <option value="">Select Option</option>
                                        <option value="N"> No </option>
                                        <option value="Y"> Yes </option>
                                    @break

                                    @case('CNC')
                                    @case('EXT')

                                    @case('RFN')
                                    @case('NIL')

                                    @case('INS')
                                        <option value="{{ $old_endt_trans->broker_flag }}" selected>
                                            {{ $old_endt_trans->broker_flag }}</option>
                                    @break

                                    @case('REN')
                                    @case('EDIT')
                                        <option value="" selected>--Select option--</option>
                                        <option value="N" @if ($old_endt_trans->broker_flag == 'N') selected @endif> No </option>
                                        <option value="Y" @if ($old_endt_trans->broker_flag == 'Y') selected @endif> Yes </option>
                                    @break
                                @endswitch
                            </select>
                        </div>
                    </div>

                    {{-- prospect ref id --}}
                    <div class="col-sm-2">
                        <label class="form-label required">Prospect Ref ID</label>
                        <input type="text" name="prospect_id" id="prospect_id" class="form-control section color-blk" />
                    </div>

                    <!--agency-->
                    <div class="col-sm-3 brokercode_div">
                        <label class="form-label required">Ceding Broker</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2" name="brokercode" id="brokercode" required>

                                @switch($trans_type)
                                    @case('NEW')
                                        <option selected value="">Choose Ceding Broker</option>
                                        @foreach ($brokers as $broker)
                                            <option value="{{ $broker->broker_code }}">{{ $broker->broker_name }}</option>
                                        @endforeach
                                    @break

                                    @case('CNC')
                                    @case('EXT')

                                    @case('RFN')
                                    @case('NIL')

                                    @case('INS')
                                        @foreach ($brokers as $broker)
                                            @if ($broker->broker_code == $old_endt_trans->broker_code)
                                                <option value="{{ $broker->broker_code }}" selected>{{ $broker->broker_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @case('REN')
                                    @case('EDIT')
                                        @foreach ($brokers as $broker)
                                            @if ($broker->broker_code == $old_endt_trans->broker_code)
                                                <option value="{{ $broker->broker_code }}" selected>{{ $broker->broker_name }}
                                                </option>
                                            @else
                                                <option value="{{ $broker->broker_code }}">{{ $broker->broker_name }}</option>
                                            @endif
                                        @endforeach
                                    @break
                                @endswitch
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row row-cols-12">
                    <!--reinsurance division-->
                    <div class="col-sm-3">
                        <label class="form-label required">Division</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2" name="division" id="division" required>
                                @switch($trans_type)
                                    @case('NEW')
                                        <option selected value="">Choose Division</option>
                                        @foreach ($reinsdivisions as $trtDivision)
                                            <option value="{{ $trtDivision->division_code }}">{{ $trtDivision->division_name }}
                                            </option>
                                        @endforeach
                                    @break

                                    @case('EDIT')
                                        <option selected value="">Choose Division</option>
                                        @foreach ($reinsdivisions as $trtDivision)
                                            <option value="{{ $trtDivision->division_code }}"
                                                @if ($trtDivision->division_code == $old_endt_trans->division_code) selected @endif>
                                                {{ $trtDivision->division_name }}</option>
                                        @endforeach
                                    @break

                                    @case('CNC')
                                    @case('EXT')

                                    @case('REN')
                                    @case('RFN')

                                    @case('NIL')
                                        @foreach ($reinsdivisions as $trtDivision)
                                            @if ($trtDivision->division_code == $old_endt_trans->division_code)
                                                <option value="{{ $trtDivision->division_code }}" selected>
                                                    {{ $trtDivision->division_name }}</option>
                                            @endif
                                        @endforeach
                                    @break
                                @endswitch
                            </select>
                        </div>
                    </div>

                    <!--pay method-->
                    <div class="col-sm-3">
                        <label class="form-label required">Payment Method</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2" name="pay_method" id="pay_method" required>
                                @if ($trans_type == 'NEW')
                                    <option selected value="" pay_method_desc="">Choose Payment Method</option>
                                    @foreach ($paymethods as $pay_method)
                                        <option value="{{ $pay_method->pay_method_code }}"
                                            pay_method_desc="{{ $pay_method->short_description }}">
                                            {{ $pay_method->pay_method_name }}</option>
                                    @endforeach
                                @elseif(
                                    $trans_type == 'EXT' ||
                                        $trans_type == 'CNC' ||
                                        $trans_type == 'RFN' ||
                                        $trans_type == 'NIL' ||
                                        $trans_type == 'INS')
                                    @foreach ($paymethods as $pay_method)
                                        @if ($pay_method->pay_method_code == $old_endt_trans->pay_method_code)
                                            <option value="{{ $pay_method->pay_method_code }}"
                                                pay_method_desc="{{ $pay_method->short_description }}" selected>
                                                {{ $pay_method->pay_method_name }}</option>
                                        @endif
                                    @endforeach
                                @elseif($trans_type == 'REN' || $trans_type == 'EDIT')
                                    @foreach ($paymethods as $pay_method)
                                        @if ($pay_method->pay_method_code == $old_endt_trans->pay_method_code)
                                            <option value="{{ $pay_method->pay_method_code }}"
                                                pay_method_desc="{{ $pay_method->short_description }}" selected>
                                                {{ $pay_method->pay_method_name }}</option>
                                        @else
                                            <option value="{{ $pay_method->pay_method_code }}"
                                                pay_method_desc="{{ $pay_method->short_description }}">
                                                {{ $pay_method->pay_method_name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <div class="text-danger">{{ $errors->first('pay_method') }}</div>
                        </div>
                    </div>

                    {{-- no of installments --}}
                    @if ($trans_type == 'NEW')
                        <div class="col-sm-1" id="no_of_installments_sec">
                            <label class="form-label required" id="no_of_installments_label">Installments</label>
                            @if ($trans_type == 'NEW')
                                <input type="number" class="form-control section color-blk" id="no_of_installments"
                                    name="no_of_installments" min="1" max="100" maxlength="100"
                                    value="" required>
                            @elseif(
                                $trans_type == 'EXT' ||
                                    $trans_type == 'CNC' ||
                                    $trans_type == 'RFN' ||
                                    $trans_type == 'NIL' ||
                                    $trans_type == 'INS')
                                <input type="number" class="form-control section color-blk" id="no_of_installments"
                                    name="no_of_installments" min="1" max="100" maxlength="100"
                                    value="{{ $old_endt_trans->no_of_installments }}" @readonly(true) required>
                            @elseif($trans_type == 'REN' || $trans_type == 'EDIT')
                                <input type="number" class="form-control section color-blk" id="no_of_installments"
                                    name="no_of_installments" min="1" max="100" maxlength="100"
                                    value="{{ $old_endt_trans->no_of_installments }}" required>
                            @endif
                        </div>
                    @else
                        <div class="col-sm-1" id="edit_no_of_installments_sec">
                            <label class="form-label required" id="no_of_installments_label">Installments</label>
                            @if ($trans_type == 'NEW')
                                <input type="number" class="form-inputs section color-blk" id="no_of_installments"
                                    name="no_of_installments" min="1" max="100" maxlength="100"
                                    value="" required>
                            @elseif(
                                $trans_type == 'EXT' ||
                                    $trans_type == 'CNC' ||
                                    $trans_type == 'RFN' ||
                                    $trans_type == 'NIL' ||
                                    $trans_type == 'INS')
                                <input type="number" class="form-control section color-blk" id="no_of_installments"
                                    name="no_of_installments" min="1" max="100" maxlength="100"
                                    value="{{ $old_endt_trans->no_of_installments }}" @readonly(true) required>
                            @elseif($trans_type == 'REN' || $trans_type == 'EDIT')
                                <input type="number" class="form-control section color-blk" id="no_of_installments"
                                    name="no_of_installments" min="1" max="100" maxlength="100"
                                    value="{{ $old_endt_trans->no_of_installments }}" required>
                            @endif
                        </div>
                    @endif
                    @if ($trans_type == 'NEW')
                        <div class="col-md-2" id="add_fac_inst_btn_section">
                            <label class="form-label" id="add_fac_instalments_label" style="height: 20px"></label></br>
                            <button type="button" class="btn btn-primary btn-sm" id="add_fac_instalments"> Add
                                Installment </button>
                        </div>
                    @else
                        <div class="col-md-2" id="edit_fac_inst_btn_section">
                            <label id="add_fac_instalments_label" style="height: 20px"></label></br>
                            <button type="button" class="btn btn-primary btn-sm" id="add_fac_instalments"> Add
                                Installment </button>
                        </div>
                    @endif

                    {{-- currency --}}
                    <div class="col-sm-2">
                        <label class="form-label required">Currency</label>
                        <div class="cover-card">
                            <select class="form-inputs select2" name="currency_code" id="currency_code" required>
                                @if ($trans_type == 'NEW')
                                    <option selected value="">Choose Currency</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->currency_code }}">{{ $currency->currency_name }}
                                        </option>
                                    @endforeach
                                @elseif(
                                    $trans_type == 'EXT' ||
                                        $trans_type == 'CNC' ||
                                        $trans_type == 'RFN' ||
                                        $trans_type == 'NIL' ||
                                        $trans_type == 'INS')
                                    @foreach ($currencies as $currency)
                                        @if ($currency->currency_code == $old_endt_trans->currency_code)
                                            <option value="{{ $currency->currency_code }}" selected>
                                                {{ $currency->currency_name }}</option>
                                        @endif
                                    @endforeach
                                @elseif($trans_type == 'REN' || $trans_type == 'EDIT')
                                    @foreach ($currencies as $currency)
                                        {{-- @if ($currency->currency_code == $old_endt_trans->currency_code) --}}
                                        <option value="{{ $currency->currency_code }}"
                                            @if ($currency->currency_code == $old_endt_trans->currency_code) selected @endif>
                                            {{ $currency->currency_name }}</option>
                                        {{-- @else
                        <option value="{{$currency->currency_code}}">{{$currency->currency_name}}</option>
                        @endif --}}
                                    @endforeach
                                @endif;
                            </select>
                            <div class="text-danger">{{ $errors->first('currency_code') }}</div>
                        </div>
                    </div>

                    <div class="col-sm-1">
                        <label class="form-label required">E. Rate</label>
                        @if ($trans_type == 'NEW')
                            <input type="text" name="today_currency" id="today_currency"
                                class="form-control section color-blk" onkeyup="this.value=numberWithCommas(this.value)"
                                change="this.value=numberWithCommas(this.value)" @readonly(true) />
                        @elseif(
                            $trans_type == 'EXT' ||
                                $trans_type == 'CNC' ||
                                $trans_type == 'RFN' ||
                                $trans_type == 'NIL' ||
                                $trans_type == 'INS')
                            <input type="text" name="today_currency" id="today_currency"
                                class="form-control section color-blk" value="{{ $old_endt_trans->currency_rate }}"
                                @readonly(true)>
                        @elseif($trans_type == 'REN' || $trans_type == 'EDIT')
                            <input type="text" name="today_currency" id="today_currency"
                                class="form-control section color-blk" value="{{ $old_endt_trans->currency_rate }}"
                                onkeyup="this.value=numberWithCommas(this.value)"
                                change="this.value=numberWithCommas(this.value)" @readonly(true) />
                        @endif
                    </div>

                    <!--pay method-->
                    <div class="col-sm-3">
                        <label class="form-label required">Premium Payment Terms</label>
                        <div class="cover-card">
                            <select class="form-inputs select2" name="premium_payment_term" id="premium_payment_term"
                                required>
                                @if ($trans_type == 'NEW')
                                    <option selected value="">Choose Payment Term</option>
                                    @foreach ($premium_pay_terms as $premium_pay_term)
                                        <option value="{{ $premium_pay_term->pay_term_code }}"
                                            pay_term_desc="{{ $premium_pay_term->pay_term_desc }}">
                                            {{ $premium_pay_term->pay_term_desc }}</option>
                                    @endforeach
                                @elseif(
                                    $trans_type == 'EXT' ||
                                        $trans_type == 'CNC' ||
                                        $trans_type == 'RFN' ||
                                        $trans_type == 'NIL' ||
                                        $trans_type == 'INS')
                                    @foreach ($premium_pay_terms as $premium_pay_term)
                                        @if ($premium_pay_term->pay_term_code == $old_endt_trans->premium_payment_code)
                                            <option value="{{ $premium_pay_term->pay_term_code }}"
                                                pay_term_desc="{{ $premium_pay_term->pay_term_desc }}" selected>
                                                {{ $premium_pay_term->pay_term_desc }}</option>
                                        @endif
                                    @endforeach
                                @elseif($trans_type == 'REN' || $trans_type == 'EDIT')
                                    <option selected value="">Choose Payment Term</option>
                                    @foreach ($premium_pay_terms as $premium_pay_term)
                                        <option @if ($premium_pay_term->pay_term_code == $old_endt_trans->premium_payment_code) selected @endif
                                            value="{{ $premium_pay_term->pay_term_code }}">
                                            {{ $premium_pay_term->pay_term_desc }}</option>
                                    @endforeach
                                @endif


                            </select>
                            <div class="text-danger">{{ $errors->first('pay_method') }}</div>
                        </div>
                    </div>
                </div>

                <div>
                    @if ($trans_type == 'NEW')
                        <div class="row row-cols-12 fac_instalment-section" id="fac_installments_box">
                            <div class="col-md-12">
                                <h6 class="mt-2">Installment plans</h6>
                                <input type="hidden" value="0" class="form-control section"
                                    id="installment_total_amount" />
                                <div id="fac-installments-section"></div>
                            </div>
                        </div>
                    @else
                        <div class="row row-cols-12 fac_instalment-section" id="fac_installments_box">
                            <div class="col-md-12">
                                <h6 class="mt-2">Installment plans</h6>
                                <input type="hidden" value="0" id="installment_total_amount" />
                                <div id="fac-installments-section">
                                    @if (count($coverInstallments) > 0)
                                        @foreach ($coverInstallments as $index => $installment)
                                            @php
                                                $idx = $index + 1;
                                            @endphp
                                            <div class="row fac-instalament-row" data-count="{{ $idx }}">
                                                <div class="col-md-3">
                                                    <label class="">Installment</label>
                                                    <input type="hidden" name="installment_no[]"
                                                        value="{{ $idx }}" readonly
                                                        class="form-inputs section" />
                                                    <input type="hidden" name="installment_id[]"
                                                        value="{{ $installment->id }}" readonly
                                                        class="form-inputs section" />
                                                    <input type="text" value="installment No. {{ $idx }}"
                                                        id="instl_no_{{ $idx }}" readonly
                                                        class="form-inputs section" required />
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="instl_date_{{ $idx }}">Installment Due
                                                        Date</label>
                                                    <input type="date" name="installment_date[]"
                                                        value="{{ $installment->installment_date }}"
                                                        id="instl_date_{{ $idx }}" class="form-inputs section"
                                                        required />
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="instl_amnt_{{ $idx }}">Total Installment
                                                        Amount</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" name="installment_amt[]"
                                                            id="instl_amnt_{{ $idx }}"
                                                            value="{{ $installment->installment_amt }}"
                                                            class="form-inputs form-input-group amount section amount"
                                                            {{-- onkeyup="this.value=numberWithCommas($installment->installment_amt)" --}} {{-- change="this.value=numberWithCommas(this.value)" - --}} required />
                                                        <button class="btn btn-danger btn-sm remove-fac-instalment"
                                                            type="button" id="remove-fac-instalment"><i
                                                                class="bx bx-minus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group" id="fac_section">
                <div class="row row-cols-12">
                    <!--class groups-->
                    <div class="col-sm-3 class_group_div fac_section_div">
                        <label class="form-label required">Class Group</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2 fac_section" name="class_group" id="class_group"
                                required>
                                @switch($trans_type)
                                    @case('NEW')
                                        <option selected value="">Choose Class Group</option>
                                        @foreach ($classGroups as $classGroup)
                                            <option value="{{ $classGroup->group_code }}">{{ $classGroup->group_name }}
                                            </option>
                                        @endforeach
                                    @break

                                    @case('EXT')
                                    @case('CNC')

                                    @case('REN')
                                    @case('RFN')

                                    @case('NIL')
                                    @case('INS')

                                    @case('EDIT')
                                        @foreach ($classGroups as $classGroup)
                                            <option value="{{ $classGroup->group_code }}"
                                                @if ($classGroup->group_code == $old_endt_trans->class_group_code) selected @endif>
                                                {{ $classGroup->group_name }}
                                            </option>
                                        @endforeach
                                    @break
                                @endswitch
                            </select>
                        </div>
                    </div>

                    <!--class-->
                    <div class="col-sm-3 fac_section_div">
                        <label class="form-label required">Class Name</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2 fac_section" name="classcode" id="classcode"
                                required>
                                <option value="">-- Select Class Name--</option>
                                {{-- @if ($trans_type == 'NEW')
                                    <option value="">-- Select Class Name--</option>
                                @elseif(
                                    $trans_type == 'EXT' ||
                                        $trans_type == 'CNC' ||
                                        $trans_type == 'REN' ||
                                        $trans_type == 'RFN' ||
                                        $trans_type == 'NIL' ||
                                        $trans_type == 'INS' ||
                                        $trans_type == 'EDIT')
                                    @foreach ($class as $classc)
                                        {{-- @if ($classc->class_code == $old_endt_trans->class_code) --}
                                <option value="{{ $classc->class_code }}"
                                    @if ($classc->class_code == $old_endt_trans->class_code) selected @endif>
                                    {{ $classc->class_name }}
                                </option>
                                {{-- @endif --}
                                    @endforeach
                                @endif --}}
                            </select>
                            <div class="text-danger">{{ $errors->first('classcode') }}</div>
                        </div>
                    </div>

                    {{-- insured name --}}
                    @if (isset($prospectId) && $prospectId)
                        <div class="col-sm-2 fac_section_div">
                            <label class="form-label required">Insured Name</label>
                            <div class="cover-card">
                                <select class="form-inputs section select2 fac_section" name="insured_name"
                                    id="insured_name" required>
                                    @if ($trans_type == 'NEW')
                                        <option selected value="">Choose Insured</option>
                                        @foreach ($insured as $insured_names)
                                            <option value="{{ $insured_names->name }}">{{ $insured_names->name }}
                                            </option>
                                        @endforeach
                                    @elseif(
                                        $trans_type == 'REN' ||
                                            $trans_type == 'EDIT' ||
                                            $trans_type == 'EXT' ||
                                            $trans_type == 'CNC' ||
                                            $trans_type == 'RFN' ||
                                            $trans_type == 'NIL' ||
                                            $trans_type == 'INS')
                                        @foreach ($insured as $insured_names)
                                            <option value="{{ $insured_names->name }}"
                                                @if ($old_endt_trans->insured_name == '{{ $insured_names->name }}') selected @endif>
                                                {{ $insured_names->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1 fac_section_div">
                            <label class="form-label">&nbsp;</label>
                            <div class="cover-card">
                                <button type="button" class="btn btn-dark btn-sm" id="addInsuredData"><i
                                        class="bx bx-plus-circle "></i> Add </button>
                            </div>
                        </div>
                    @else
                        <div class="col-sm-3 fac_section_div">
                            <label class="form-label required">Insured Name</label>
                            <div class="cover-card">
                                <select class="form-inputs section select2 fac_section" name="insured_name"
                                    id="insured_name" required>
                                    @if ($trans_type == 'NEW')
                                        <option selected value="">Choose Insured</option>
                                        @foreach ($insured as $insured_names)
                                            <option value="{{ $insured_names->name }}">{{ $insured_names->name }}
                                            </option>
                                        @endforeach
                                    @elseif(
                                        $trans_type == 'REN' ||
                                            $trans_type == 'EDIT' ||
                                            $trans_type == 'EXT' ||
                                            $trans_type == 'CNC' ||
                                            $trans_type == 'RFN' ||
                                            $trans_type == 'NIL' ||
                                            $trans_type == 'INS')
                                        @foreach ($insured as $insured_names)
                                            <option value="{{ $insured_names->name }}"
                                                @if ($old_endt_trans->insured_name == '{{ $insured_names->name }}') selected @endif>
                                                {{ $insured_names->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    @endif


                    <!--date offered-->
                    <div class="col-sm-3 fac_section_div">
                        <label class="form-label required">Date Offered</label>
                        @if ($trans_type == 'NEW')
                            <input type="date" class="form-inputs fac_section" aria-label="fac_date_offered"
                                id="fac_date_offered" name="fac_date_offered" required>
                        @else
                            <input type="date" class="form-inputs fac_section"
                                value="{{ $old_endt_trans->date_offered }}" aria-label="fac_date_offered"
                                id="fac_date_offered" name="fac_date_offered" required>
                        @endif
                    </div>
                </div>

                <div class="row row-cols-12">
                    <div class="col-sm-3 fac_section_div">
                        <label class="form-label required">Sum Insured Type</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2 fac_section" name="sum_insured_type"
                                id="sum_insured_type" required>
                                @if ($trans_type == 'NEW')
                                    <option selected value="">Choose Sum Insured Type</option>
                                    @foreach ($types_of_sum_insured as $type_of_sum_insured)
                                        <option value="{{ $type_of_sum_insured->sum_insured_code }}">
                                            {{ $type_of_sum_insured->sum_insured_name }}</option>
                                    @endforeach
                                @elseif(
                                    $trans_type == 'EXT' ||
                                        $trans_type == 'CNC' ||
                                        $trans_type == 'RFN' ||
                                        $trans_type == 'NIL' ||
                                        $trans_type == 'INS')
                                    @foreach ($types_of_sum_insured as $type_of_sum_insured)
                                        @if ($type_of_sum_insured->sum_insured_code == $old_endt_trans->type_of_sum_insured)
                                            <option value="{{ $type_of_sum_insured->sum_insured_code }}" selected>
                                                {{ $type_of_sum_insured->sum_insured_name }}</option>
                                        @endif
                                    @endforeach
                                @elseif($trans_type == 'REN' || $trans_type == 'EDIT')
                                    @foreach ($types_of_sum_insured as $type_of_sum_insured)
                                        @if ($type_of_sum_insured->sum_insured_code == $old_endt_trans->type_of_sum_insured)
                                            <option value="{{ $type_of_sum_insured->sum_insured_code }}" selected>
                                                {{ $type_of_sum_insured->sum_insured_name }}</option>
                                        @else
                                            <option value="{{ $type_of_sum_insured->sum_insured_code }}">
                                                {{ $type_of_sum_insured->sum_insured_name }}</option>
                                        @endif
                                    @endforeach
                                @endif;
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 fac_section_div">
                        <label class="form-label">100% SUM INSURED <span id="sum_insured_label"></span></label>
                        @if ($trans_type == 'NEW')
                            <input type="text" class="form-inputs fac_section" aria-label="total_sum_insured"
                                id="total_sum_insured" name="total_sum_insured"
                                onkeyup="this.value=numberWithCommas(this.value)" required>
                        @else
                            <input type="text" class="form-inputs fac_section" aria-label="total_sum_insured"
                                id="total_sum_insured" name="total_sum_insured"
                                value="{{ number_format($old_endt_trans->total_sum_insured, 2) }}"
                                onkeyup="this.value=numberWithCommas(this.value)" required>
                        @endif
                    </div>
                    <div class="col-sm-3">
                        <label for="apply_eml">Apply EML</label>
                        <div class="cover-card">
                            <select name="apply_eml" class="form-inputs section select2" id="apply_eml" required>
                                <option value="">--select option-</option>
                                @if ($trans_type == 'NEW')
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                @else
                                    <option value="Y" @if ($old_endt_trans->apply_eml == 'Y') selected @endif>Yes
                                    </option>
                                    <option value="N" @if ($old_endt_trans->apply_eml == 'N') selected @endif>No</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3 eml-div">
                        <label class="form-label"> EML Rate</label>
                        <div class="cover-card">
                            @if ($trans_type == 'NEW')
                                <input type="number" class="form-inputs fac_section" aria-label="eml_rate"
                                    id="eml_rate" name="eml_rate" value="100" min="0" max="100"
                                    required>
                            @else
                                <input type="number" class="form-inputs fac_section" aria-label="eml_rate"
                                    id="eml_rate" name="eml_rate" value="{{ $old_endt_trans->eml_rate }}"
                                    min="0" max="100" required>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-3 eml-div">
                        <label class="form-label">EML Amount</label>
                        <div class="cover-card">
                            @if ($trans_type == 'NEW')
                                <input type="text" class="form-inputs fac_section amount" aria-label="eml_amt"
                                    id="eml_amt" name="eml_amt" required>
                            @else
                                <input type="text" class="form-inputs fac_section amount" aria-label="eml_amt"
                                    id="eml_amt" name="eml_amt"
                                    value="{{ number_format($old_endt_trans->eml_amount, 2) }}" @required(true)>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Effective Sum Insured</label>
                        @if ($trans_type == 'NEW')
                            <input type="text" class="form-inputs fac_section amount"
                                aria-label="effective_sum_insured" id="effective_sum_insured"
                                name="effective_sum_insured" required readonly>
                        @else
                            <input type="text" class="form-inputs fac_section amount"
                                aria-label="effective_sum_insured" id="effective_sum_insured"
                                name="effective_sum_insured" value="{{ number_format($old_endt_trans->eml_amount, 2) }}"
                                required readonly>
                        @endif
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Risk Details</label>
                        @if ($trans_type == 'NEW')
                            <div class="form-inputs section fac_section" id="risk_details_content" contenteditable="true"
                                style="border: 1px solid #363434; padding: 8px; min-height: 100px; resize: none; width:100%; overflow: auto; max-height: 500px; background-color: var(--input-bg-color); color: var(--input-text-color);">
                            </div>
                            <input type="hidden" name="risk_details" id="hidden_risk_details" />
                        @else
                            <div class="form-inputs section fac_section" id="risk_details_content" contenteditable="true"
                                style="border: 1px solid #363434; padding: 8px; min-height: 100px; resize: none; overflow: auto; max-height: 500px; background-color: var(--input-bg-color); color: var(--input-text-color);">
                                {!! $old_endt_trans->risk_details !!}
                            </div>
                            <input type="hidden" name="risk_details" id="hidden_risk_details"
                                value={{ $old_endt_trans->risk_details }} />
                        @endif
                    </div>
                    <div class="col-xl-3 fac_section_div">
                        <label class="form-label">Cedant Premium</label>
                        @if ($trans_type == 'NEW')
                            <input type="text" class="form-inputs fac_section" aria-label="cede_premium"
                                id="cede_premium" name="cede_premium" onkeyup="this.value=numberWithCommas(this.value)"
                                required>
                        @else
                            <input type="text" class="form-inputs fac_section" aria-label="cede_premium"
                                id="cede_premium" name="cede_premium"
                                value="{{ number_format($old_endt_trans->cedant_premium, 2) }}"
                                onkeyup="this.value=numberWithCommas(this.value)" required>
                        @endif
                    </div>
                    <div class="col-xl-3 fac_section_div">
                        <label class="form-label">Reinsurer Premium</label>
                        @if ($trans_type == 'NEW')
                            <input type="text" class="form-inputs fac_section" aria-label="rein_premium"
                                id="rein_premium" name="rein_premium" onkeyup="this.value=numberWithCommas(this.value)"
                                required>
                        @else
                            <input type="text" class="form-inputs fac_section" aria-label="rein_premium"
                                id="rein_premium" name="rein_premium"
                                value="{{ number_format($old_endt_trans->rein_premium, 2) }}"
                                onkeyup="this.value=numberWithCommas(this.value)" required>
                        @endif
                    </div>
                    <div class="col-xl-3 fac_section_div">
                        <label class="form-label">Share Offered(%)</label>
                        <input type="number" class="form-inputs fac_section" aria-label="fac_share_offered"
                            id="fac_share_offered" name="fac_share_offered" max="100" min="0"
                            value="{{ $trans_type != 'NEW' ? $old_endt_trans->share_offered : '' }}" required>
                    </div>
                </div>

                <div class="row row-cols-12">
                    <div class="col-xl-3 fac_section_div">
                        <label class="form-label">Cedant Comm rate(%)</label>
                        <input type="text" class="form-inputs fac_section" aria-label="comm_rate" id="comm_rate"
                            name="comm_rate" value="{{ $trans_type != 'NEW' ? $old_endt_trans->cedant_comm_rate : '' }}"
                            required>
                    </div>
                    <div class="col-xl-3 fac_section_div">
                        <label class="form-label">Cedant Comm Amount</label>
                        <input type="text" class="form-inputs fac_section" aria-label="comm_amt" id="comm_amt"
                            name="comm_amt"
                            value="{{ $trans_type != 'NEW' ? number_format($old_endt_trans->cedant_comm_amount, 2) : '' }}"
                            onkeyup="this.value=numberWithCommas(this.value)" required>
                    </div>
                    <div class="col-xl-3 fac_section_div reins_comm_type_div">
                        <label class="form-label">Reinsurer Comm Type</label>
                        <div class="cover-card">
                            <select class="form-inputs section select2 fac_section reins_comm_type" name="reins_comm_type"
                                id="reins_comm_type" required>
                                <option value="">Choose Reinsurer Comm Type</option>
                                @if ($trans_type == 'NEW')
                                    <option value="R">Rate</option>
                                    <option value="A">Amount</option>
                                @else
                                    <option value="R" @if ($old_endt_trans->rein_comm_type == 'R') selected @endif>Rate
                                    </option>
                                    <option value="A" @if ($old_endt_trans->rein_comm_type == 'N') selected @endif>Amount
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3  fac_section_div reins_comm_rate_div">
                        <label class="form-label">Reinsurer Comm rate(%)</label>
                        <input type="text" class="form-inputs fac_section reins_comm_rate"
                            aria-label="reins_comm_rate" id="reins_comm_rate" name="reins_comm_rate"
                            value="{{ $trans_type == 'NEW' ? '' : number_format($old_endt_trans->rein_comm_rate, 2) }}"
                            onkeyup="this.value=numberWithCommas(this.value)" required disabled>
                    </div>
                    <div class="col-xl-3 fac_section_div reins_comm_amt_div">
                        <label class="form-label">Reinsurer Comm Amount</label>
                        <input type="text" class="form-inputs fac_section reins_comm_amt" aria-label="reins_comm_amt"
                            id="reins_comm_amt" name="reins_comm_amt"
                            value="{{ $trans_type == 'NEW' ? '' : number_format($old_endt_trans->rein_comm_amount, 2) }}"
                            onkeyup="this.value=numberWithCommas(this.value)"
                            onchange="this.value=numberWithCommas(this.value)" required>

                    </div>
                    <div class="col-xl-3 fac_section_div">
                        <label class="form-label">Brokerage Commission Type</label>
                        <div class="cover-card">
                            <select name="brokerage_comm_type" id="brokerage_comm_type"
                                class="form-inputs section select2" {{ $trans_type != 'NEW' ? 'required' : '' }}>
                                <option value="" @selected($trans_type != 'NEW' && $old_endt_trans->brokerage_comm_type == '')>
                                    --Select basis--</option>
                                <option value="R" @selected($trans_type != 'NEW' && $old_endt_trans->brokerage_comm_type == 'R')>
                                    Rate (<small><i>reinsurer rate - cedant rate</i></small>)</option>
                                <option value="A" @selected($trans_type != 'NEW' && $old_endt_trans->brokerage_comm_type == 'A')>
                                    Quoted Amount</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 fac_section_div brokerage_comm_amt_div">
                        <label class="form-label">Brokerage Amount</label>
                        <input type="text" class="form-inputs fac_section amount" id="brokerage_comm_amt"
                            name="brokerage_comm_amt"
                            value="{{ $trans_type == 'NEW' ? '0' : $old_endt_trans->brokerage_comm_amt }}">
                    </div>
                    <div class="col-xl-3 fac_section_div brokerage_comm_rate_div">
                        <label class="form-label" id="brokerage_comm_rate_label">Brokerage Rate</label>
                        <input type="text" class="form-inputs fac_section amount" id="brokerage_comm_rate"
                            name="brokerage_comm_rate"
                            value="{{ $trans_type == 'NEW' ? '' : number_format($old_endt_trans->brokerage_comm_rate, 2) }}">
                    </div>
                    <div class="col-xl-3 fac_section_div brokerage_comm_rate_amnt_div">
                        <label class="form-label" id="brokerage_comm_rate_amnt_label">Brokerage Amount</label>
                        <input type="text" class="form-inputs fac_section amount" id="brokerage_comm_rate_amnt"
                            name="brokerage_comm_rate_amnt" value="" readonly>
                    </div>
                    <input type="hidden" class="vat_charged fac_section" id="vat_charged" name="vat_charged"
                        value="0">
                </div>
            </div>

            <div class="form-group treaty_grp" id="treaty_grp">
                <div id="trt_common">
                    <div class="row row-cols-12">
                        <!--treaty type-->
                        <div class="col-sm-3 treatytype_div trt_common_div">
                            <label class="form-label required">Treaty Type</label>
                            <select class="form-inputs section treatytype trt_common" name="treatytype" id="treatytype"
                                required>
                                @foreach ($treatytypes as $treatytype)
                                    <option value="{{ $treatytype->treaty_code }}"
                                        @if ($trans_type != 'NEW' && $treatytype->treaty_code == $old_endt_trans->treaty_code) selected @endif>
                                        {{ $treatytype->treaty_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!--date offered-->
                        <div class="col-sm-3 date_offered_div trt_common_div">
                            <label class="form-label required">Date Offered</label>
                            @if ($trans_type == 'NEW')
                                <input type="date" class="form-inputs date_offered trt_common"
                                    aria-label="date_offered" id="date_offered" name="date_offered" required>
                            @else
                                <input type="date" class="form-inputs date_offered trt_common"
                                    value="{{ $old_endt_trans->date_offered }}" aria-label="date_offered"
                                    id="date_offered" name="date_offered" required>
                            @endif
                        </div>

                        <!--share offered-->
                        <div class="col-sm-2 share_offered_div trt_common_div">
                            <label class="required ">Share Offered(%)</label>
                            <input type="text" class="form-inputs share_offered trt_common"
                                @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->share_offered, 2) }} @endif"
                                aria-label="share_offered" id="share_offered" name="share_offered" required>
                        </div>

                        <!--Premium Tax  (%)-->
                        <div class="col-sm-2 prem_tax_rate_div trt_common_div">
                            <label class="required ">Premium Tax Rate (%)</label>
                            <input type="number" class="form-inputs prem_tax_rate trt_common"
                                @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->prem_tax_rate, 2) }} @endif"
                                aria-label="prem_tax_rate" id="prem_tax_rate" name="prem_tax_rate" required>
                        </div>

                        <!--RI Tax (%)-->
                        <div class="col-sm-2 ri_tax_rate_div prem_tax_rate_div trt_common_div">
                            <label class="required ">Reinsurance Tax Rate (%)</label>
                            <input type="number" class="form-inputs ri_tax_rate trt_common"
                                @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->ri_tax_rate, 2) }} @endif"
                                aria-label="ri_tax_rate" id="ri_tax_rate" name="ri_tax_rate" min="0"
                                max="100" required>
                        </div>

                        <!--Brokerage Comm (%)-->
                        <div class="col-sm-2 brokerage_comm_rate_div trt_common_div">
                            <label class="required ">Brokerage Commission Rate (%)</label>
                            <input type="number" class="form-inputs brokerage_comm_rate trt_common"
                                @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->brokerage_comm_rate, 2) }}" @endif
                                aria-label="brokerage_comm_rate" id="brokerage_comm_rate" name="brokerage_comm_rate"
                                min="0" max="100" required>
                        </div>

                    </div>
                    <div class="row">
                        <!--Capture shared reinsurer-->
                        <div class="col-sm-2 reinsurer_per_treaty_div tpr_section_div">
                            <label class="required ">Reinsurers are captured per treaty ?</label>
                            <select class="form-inputs reinsurer_per_treaty tpr_section" name="reinsurer_per_treaty"
                                id="reinsurer_per_treaty" required>
                                <option value=""> Select Option </option>
                                @if (in_array($trans_type, ['NEW', 'REN']))
                                    <option value="N"selected> No </option>
                                    <option value="Y"> Yes </option>
                                @else
                                    <option value="N" @if ($old_endt_trans->reinsurer_per_treaty == 'N') selected @endif> No
                                    </option>
                                    <option value="Y" @if ($old_endt_trans->reinsurer_per_treaty == 'Y') selected @endif> Yes
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>

                </div>
                <div class="form-group mt-3">
                    <div id="tpr_section">
                        <div class="row">

                            <!--premium rate-->
                            <div class="col-sm-3 port_prem_rate_div tpr_section_div">
                                <label class="required ">Portfolio Premium Rate(%)</label>
                                <input type="number" class="form-inputs port_prem_rate tpr_section"
                                    aria-label="port_prem_rate" id="port_prem_rate" name="port_prem_rate"
                                    data-counter="0" max="100" min="0" required
                                    @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->port_prem_rate, 2) }}" @endif>
                            </div>

                            <!--loss rate-->
                            <div class="col-sm-3 port_loss_rate_div tpr_section_div">
                                <label class="required ">Portfolio Loss Rate(%)</label>
                                <input type="number" class="form-inputs port_loss_rate tpr_section"
                                    aria-label="port_loss_rate" id="port_loss_rate" name="port_loss_rate"
                                    data-counter="0" max="100" min="0" required
                                    @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->port_loss_rate, 2) }}" @endif>
                            </div>

                            <!--profit comm rate-->
                            <div class="col-sm-3 profit_comm_rate_div tpr_section_div">
                                <label class="form-label required">Profit Comm Rate(%)</label>
                                <input type="number" class="form-inputs profit_comm_rate tpr_section"
                                    aria-label="profit_comm_rate" id="profit_comm_rate" name="profit_comm_rate"
                                    data-counter="0" max="100" min="0" required
                                    @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->profit_comm_rate, 2) }}" @endif>
                            </div>

                            <!--mgnt expense rate-->
                            <div class="col-sm-3 mgnt_exp_rate_div tpr_section_div">
                                <label class="required ">Mgnt Expense Rate(%)</label>
                                <input type="number" class="form-inputs mgnt_exp_rate tpr_section"
                                    aria-label="mgnt_exp_rate" id="mgnt_exp_rate" name="mgnt_exp_rate" data-counter="0"
                                    max="100" min="0" required
                                    @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->mgnt_exp_rate, 2) }}" @endif>
                            </div>

                        </div>

                        <div class="row">
                            <!--Deficit c/f (yrs)-->
                            <div class="col-sm-3 deficit_yrs_div tpr_section_div">
                                <label class="required_div deficit_yrs">Deficit C/F (yrs)</label>
                                <input type="number" class="form-inputs  tpr_section" aria-label=""
                                    class="deficit_yrs" id="deficit_yrs" name="deficit_yrs" data-counter="0"
                                    min="0" max="10" required
                                    @if ($trans_type != 'NEW') value="{{ $old_endt_trans->deficit_yrs }}" @endif>
                            </div>



                        </div>
                        <div class="row mb-2 tpr_section_div">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary tpr_section" id="add_rein_class"> Add
                                    Class </button>
                            </div>
                        </div>
                        @if ($trans_type != 'EDIT')
                            <div class="row reinclass-section " id="reinclass-section-0" data-counter="0">
                                <h6 class="section-title tpr_section_div">Section A</h6>

                                <div class="row mb-2">
                                    <!--reinsurance main class-->
                                    <div class="col-sm-3 treaty_reinclass tpr_section_div">
                                        <label class="form-label required">Reinsurance Class</label>
                                        <select class="form-inputs select2 treaty_reinclass tpr_section"
                                            name="treaty_reinclass[]" id="treaty_reinclass-0" data-counter="0" required>
                                            @if ($trans_type == 'NEW')
                                                <option value="">Choose Reinsurance Class</option>
                                                @foreach ($reinsclasses as $reinsclass)
                                                    <option value="{{ $reinsclass->class_code }}">
                                                        {{ $reinsclass->class_name }}</option>
                                                @endforeach
                                            @elseif(
                                                $trans_type == 'EXT' ||
                                                    $trans_type == 'CNC' ||
                                                    $trans_type == 'REN' ||
                                                    $trans_type == 'RFN' ||
                                                    $trans_type == 'NIL' ||
                                                    $trans_type == 'INS' ||
                                                    $trans_type == 'EDIT')
                                                @foreach ($reinsclasses as $reinsclass)
                                                    @if ($reinsclass->class_code == $old_endt_trans->reinsclass_code)
                                                        <option value="{{ $reinsclass->class_code }}" selected>
                                                            {{ $reinsclass->class_name }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="row quota_header_div tpr_section_div" style="display: none">
                                    <h6> Quota Share </h6>
                                </div>
                                <div class="row">

                                    <!--quota limit-->
                                    <div class="col-sm-2 quota_share_total_limit_div tpr_section_div"
                                        id="quota_share_total_limit_div">

                                        <label class="form-label required">100% Quota Share Limit</label>
                                        <input type="text" class="form-inputs quota_share_total_limit tpr_section"
                                            aria-label="quota_share_total_limit" id="quota_share_total_limit-0"
                                            data-counter="0" name="quota_share_total_limit[]"
                                            onkeyup="this.value=numberWithCommas(this.value)" required>
                                    </div>

                                    <!--Retention (%)-->
                                    <div class="col-sm-1 retention_per_div tpr_section_div" id="retention_per_div">
                                        <label class="form-label required">Retention(%)</label>
                                        <input type="number" class="form-inputs retention_per tpr_section"
                                            aria-label="retention_per" id="retention_per-0" data-counter="0"
                                            name="retention_per[]" min="0" max="100" required>
                                    </div>

                                    <!--Retention Amount-->
                                    <div class="col-sm-3 quota_retention_amt_div tpr_section_div"
                                        id="quota_retention_amt_div" style="display: none">
                                        <label class="form-label required">Retention Amount</label>
                                        <input type="text" class="form-inputs quota_retention_amt tpr_section"
                                            aria-label="quota_retention_amt" id="quota_retention_amt-0"
                                            name="quota_retention_amt[]" data-counter="0"
                                            onkeyup="this.value=numberWithCommas(this.value)" required disabled>
                                    </div>

                                    <!--treaty share(%)-->
                                    <div class="col-sm-2 treaty_reice_div tpr_section_div" id="treaty_reice_div">
                                        <label class="form-label required">Treaty (%)</label>
                                        <input type="number" class="form-inputs treaty_reice tpr_section"
                                            aria-label="treaty_reice" id="treaty_reice-0" name="treaty_reice[]"
                                            data-counter="0" min="0" max="100" required>
                                    </div>

                                    <!--treaty limit-->
                                    <div class="col-sm-3 quota_treaty_limit_div tpr_section_div"
                                        id="quota_treaty_limit_div" style="display: none">
                                        <label class="form-label required">Treaty Limit</label>
                                        <input type="text" class="form-inputs quota_treaty_limit tpr_section"
                                            aria-label="quota_treaty_limit" class="quota_treaty_limit"
                                            id="quota_treaty_limit-0" name="quota_treaty_limit[]" data-counter="0"
                                            onkeyup="this.value=numberWithCommas(this.value)" required disabled>
                                    </div>

                                </div>

                                <div class="row surp_header_div tpr_section_div" style="display: none" data-counter="0">
                                    <h6> Surplus </h6>
                                </div>

                                <div class="row">

                                    <!--Retention Amount-->
                                    <div class="col-sm-3 surp_retention_amt_div tpr_section_div"
                                        id="surp_retention_amt_div " style="display: none">
                                        {{-- <h6> Surplus </h6> --}}
                                        <label class="form-label required">Retention Amount</label>
                                        <input type="text" class="form-inputs surp_retention_amt tpr_section"
                                            aria-label="surp_retention_amt" id="surp_retention_amt-0"
                                            name="surp_retention_amt[]" data-counter="0"
                                            onkeyup="this.value=numberWithCommas(this.value)" required disabled>
                                    </div>

                                    <!--no of lines-->
                                    <div class="col-sm-3 no_of_lines_div tpr_section_div" id="no_of_lines_div">
                                        <label class="form-label required">No of Lines</label>
                                        <input type="number" class="form-inputs no_of_lines tpr_section"
                                            aria-label="no_of_lines" id="no_of_lines-0" data-counter="0"
                                            name="no_of_lines[]" required>
                                    </div>

                                    <!--treaty limit-->
                                    <div class="col-sm-3 surp_treaty_limit_div tpr_section_div" id="surp_treaty_limit_div"
                                        style="display: none">
                                        <label class="form-label required">Treaty Limit</label>
                                        <input type="text" class="form-inputs surp_treaty_limit tpr_section"
                                            aria-label="surp_treaty_limit" class="surp_treaty_limit"
                                            id="surp_treaty_limit-0" name="surp_treaty_limit[]" data-counter="0"
                                            onkeyup="this.value=numberWithCommas(this.value)" required disabled>
                                    </div>

                                </div>
                                <div class="row">
                                    <!--Estimated Income-->
                                    <div class="col-sm-3 estimated_income_div tpr_section_div">
                                        <label class="required ">Estimated Income</label>
                                        <input type="text" class="form-inputs estimated_income tpr_section"
                                            aria-label="estimated_income" class="" id="estimated_income-0"
                                            name="estimated_income[]" data-counter="0"
                                            onkeyup="this.value=numberWithCommas(this.value)" required>
                                    </div>

                                    <!--Cash Loss Limit-->
                                    <div class="col-sm-3 cashloss_limit_div tpr_section_div">
                                        <label class="required ">Cash Loss Limit</label>
                                        <input type="text" class="form-inputs cashloss_limit tpr_section"
                                            aria-label="cashloss_limit" id="cashloss_limit-0" name="cashloss_limit[]"
                                            data-counter="0" onkeyup="this.value=numberWithCommas(this.value)" required>
                                    </div>
                                </div>

                                <div class="mt-2 comm-section tpr_section_div" id="comm-section-0">
                                    <h7> Commission Section </h7>
                                    <div class="row comm-sections tpr_section_div" id="comm-section-0-0"
                                        data-class-counter="0" data-counter="0">

                                        <!--treaty-->

                                        <div class="col-sm-3 prem_type_treaty_div tpr_section_div">
                                            <label class="required ">Treaty</label>
                                            <select class="form-inputs select2 prem_type_treaty tpr_section"
                                                name="prem_type_treaty[]" data-reinclass="" data-class-counter="0"
                                                data-counter="0" id="prem_type_treaty-0-0" required>
                                            </select>
                                        </div>
                                        <!--reinsurance premium types-->
                                        <div class="col-sm-3 prem_type_code_div tpr_section_div">
                                            <label class="required ">Premium Type</label>
                                            <input type="hidden" class="form-inputs prem_type_reinclass tpr_section"
                                                id="prem_type_reinclass-0-0" name="prem_type_reinclass[]"
                                                data-counter="0">

                                            <select class="form-inputs select2 prem_type_code tpr_section"
                                                name="prem_type_code[]" data-counter="0" id="prem_type_code-0-0"
                                                data-reinclass="" data-class-counter="0" data-treaty="" required>
                                            </select>
                                        </div>

                                        <div class="col-sm-3 prem_type_comm_rate_div tpr_section_div">
                                            <label class="required ">Commision(%)</label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control prem_type_comm_rate tpr_section"
                                                    name="prem_type_comm_rate[]" data-counter="0"
                                                    id="prem_type_comm_rate-0-0" required>
                                                <button class="btn btn-primary add-comm-section" type="button"
                                                    id="add-comm-section-0-0" data-counter="0">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            @php
                                $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
                            @endphp
                            @foreach ($coverreinpropClasses as $index => $coverreinpropCls)
                                <div class="row reinclass-section " id="reinclass-section-{{ $loop->index }}"
                                    data-counter="{{ $loop->index }}">
                                    <h6 class="section-title tpr_section_div">Section {{ $sections[$index] }}</h6>

                                    <div class="row mb-2">

                                        <!--reinsurance main class-->
                                        <div class="col-sm-3 treaty_reinclass tpr_section_div">
                                            <label class="form-label required">Reinsurance Class</label>
                                            <select class="form-inputs select2 treaty_reinclass tpr_section"
                                                name="treaty_reinclass[]" id="treaty_reinclass-{{ $loop->index }}"
                                                data-counter="{{ $loop->index }}" required>
                                                <option value="">Choose Reinsurance Class</option>
                                                @foreach ($reinsclasses as $reinsclass)
                                                    <option value="{{ $reinsclass->class_code }}"
                                                        @if ($reinsclass->class_code == $coverreinpropCls->reinclass) selected @endif>
                                                        {{ $reinsclass->class_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @php
                                        $quotaCoverreinprop = $coverreinpropCls
                                            ->where('reinclass', $coverreinpropCls->reinclass)
                                            ->where('item_description', 'QUOTA')
                                            ->first();

                                        $surpCoverreinprop = $coverreinpropCls
                                            ->where('reinclass', $coverreinpropCls->reinclass)
                                            ->where('item_description', 'SURPLUS')
                                            ->first();
                                        $classPremTypes = $premtypes
                                            ->where('reinclass', $coverreinpropCls->reinclass)
                                            ->all();
                                        $reinClassPremTypes = $reinPremTypes
                                            ->where('reinclass', $coverreinpropCls->reinclass)
                                            ->all();
                                    @endphp
                                    <div class="row quota_header_div tpr_section_div" style="display: none">
                                        <h6> Quota Share </h6>
                                    </div>
                                    <div class="row">
                                        <!--quota limit-->
                                        <div class="col-sm-2 quota_share_total_limit_div tpr_section_div"
                                            id="quota_share_total_limit_div"
                                            @if ($quotaCoverreinprop->item_description == 'SURP') hidden @endif>

                                            <label class="form-label required">100% Quota Share Limit</label>
                                            <input type="text" class="form-inputs quota_share_total_limit tpr_section"
                                                aria-label="quota_share_total_limit"
                                                id="quota_share_total_limit-{{ $loop->index }}"
                                                data-counter="{{ $loop->index }}" name="quota_share_total_limit[]"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                value="{{ number_format($quotaCoverreinprop->treaty_limit, 2) }}"
                                                required>
                                        </div>

                                        <!--Retention (%)-->
                                        <div class="col-sm-1 retention_per_div tpr_section_div" id="retention_per_div">
                                            <label class="form-label required">Retention(%)</label>
                                            <input type="number" class="form-inputs retention_per tpr_section"
                                                aria-label="retention_per" id="retention_per-{{ $loop->index }}"
                                                data-counter="{{ $loop->index }}" name="retention_per[]"
                                                min="0" max="100"
                                                value="{{ number_format($quotaCoverreinprop->retention_rate, 2) }}"
                                                required>
                                        </div>

                                        <!--Retention Amount-->
                                        <div class="col-sm-3 quota_retention_amt_div tpr_section_div"
                                            id="quota_retention_amt_div"
                                            @if ($quotaCoverreinprop->item_description != 'QUOTA') hidden @endif>
                                            <label class="form-label required">Retention Amount</label>
                                            <input type="text" class="form-inputs quota_retention_amt tpr_section"
                                                aria-label="quota_retention_amt"
                                                id="quota_retention_amt-{{ $loop->index }}"
                                                name="quota_retention_amt[]" data-counter="{{ $loop->index }}"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                value="{{ number_format($quotaCoverreinprop->retention_amount, 2) }}"
                                                required disabled>
                                        </div>

                                        <!--treaty share(%)-->
                                        <div class="col-sm-2 treaty_reice_div tpr_section_div" id="treaty_reice_div">
                                            <label class="form-label required">Treaty (%)</label>
                                            <input type="number" class="form-inputs treaty_reice tpr_section"
                                                aria-label="treaty_reice" id="treaty_reice-{{ $loop->index }}"
                                                name="treaty_reice[]" data-counter="{{ $loop->index }}" min="0"
                                                max="100"
                                                value="{{ number_format($quotaCoverreinprop->treaty_rate, 2) }}"
                                                required>
                                        </div>

                                        <!--treaty limit-->
                                        <div class="col-sm-3 quota_treaty_limit_div tpr_section_div"
                                            id="quota_treaty_limit_div" @if ($quotaCoverreinprop->item_description == 'SURP') hidden @endif>
                                            <label class="form-label required">Treaty Limit</label>
                                            <input type="text" class="form-inputs quota_treaty_limit tpr_section"
                                                aria-label="quota_treaty_limit" class="quota_treaty_limit"
                                                id="quota_treaty_limit-{{ $loop->index }}" name="quota_treaty_limit[]"
                                                data-counter="{{ $loop->index }}"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                value="{{ number_format($quotaCoverreinprop->treaty_amount, 2) }}"required
                                                disabled>
                                        </div>

                                    </div>

                                    <div class="row surp_header_div tpr_section_div"
                                        @if ($surpCoverreinprop->item_description != 'SURPLUS') hidden @endif
                                        data-counter="{{ $loop->index }}">
                                        <h6> Surplus </h6>
                                    </div>

                                    <div class="row">

                                        <!--Retention Amount-->
                                        <div class="col-sm-3 surp_retention_amt_div tpr_section_div"
                                            id="surp_retention_amt_div "
                                            @if ($surpCoverreinprop->item_description != 'SURPLUS') hidden @endif>
                                            {{-- <h6> Surplus </h6> --}}
                                            <label class="form-label required">Retention Amount</label>
                                            <input type="text" class="form-inputs surp_retention_amt tpr_section"
                                                aria-label="surp_retention_amt"
                                                id="surp_retention_amt-{{ $loop->index }}" name="surp_retention_amt[]"
                                                data-counter="{{ $loop->index }}"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                value="{{ number_format($surpCoverreinprop->retention_amount, 2) }}"required
                                                disabled>
                                        </div>

                                        <!--no of lines-->
                                        <div class="col-sm-3 no_of_lines_div tpr_section_div" id="no_of_lines_div"
                                            @if ($surpCoverreinprop->item_description != 'SURPLUS') hidden @endif>
                                            <label class="form-label required">No of Lines</label>
                                            <input type="number" class="form-inputs no_of_lines tpr_section"
                                                aria-label="no_of_lines" id="no_of_lines-{{ $loop->index }}"
                                                data-counter="{{ $loop->index }}" name="no_of_lines[]"
                                                value="{{ number_format($surpCoverreinprop->no_of_lines, 2) }}"required>
                                        </div>

                                        <!--treaty limit-->
                                        <div class="col-sm-3 surp_treaty_limit_div tpr_section_div"
                                            id="surp_treaty_limit_div" @if ($surpCoverreinprop->item_description != 'SURPLUS') hidden @endif>
                                            <label class="form-label required">Treaty Limit</label>
                                            <input type="text" class="form-inputs surp_treaty_limit tpr_section"
                                                aria-label="surp_treaty_limit" class="surp_treaty_limit"
                                                id="surp_treaty_limit-{{ $loop->index }}" name="surp_treaty_limit[]"
                                                data-counter="{{ $loop->index }}"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                value="{{ number_format($surpCoverreinprop->treaty_amount, 2) }}"required
                                                disabled>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <!--Estimated Income-->
                                        <div class="col-sm-3 estimated_income_div tpr_section_div">
                                            <label class="required ">Estimated Income</label>
                                            <input type="text" class="form-inputs estimated_income tpr_section"
                                                aria-label="estimated_income" class=""
                                                id="estimated_income-{{ $loop->index }}" name="estimated_income[]"
                                                data-counter="{{ $loop->index }}"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                value="{{ number_format($surpCoverreinprop->estimated_income, 2) }}"
                                                required>
                                        </div>

                                        <!--Cash Loss Limit-->
                                        <div class="col-sm-3 cashloss_limit_div tpr_section_div">
                                            <label class="required ">Cash Loss Limit</label>
                                            <input type="text" class="form-inputs cashloss_limit tpr_section"
                                                aria-label="cashloss_limit" id="cashloss_limit-{{ $loop->index }}"
                                                name="cashloss_limit[]" data-counter="{{ $loop->index }}"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                value="{{ number_format($surpCoverreinprop->cashloss_limit, 2) }}"
                                                required>
                                        </div>
                                    </div>

                                    <div class="mt-2 comm-section tpr_section_div"
                                        id="comm-section-{{ $loop->index }}">
                                        <h7> Commission Section </h7>
                                        @foreach ($classPremTypes as $premType)
                                            <div class="row comm-sections tpr_section_div"
                                                id="comm-section-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                data-class-counter="{{ $loop->parent->index }}"
                                                data-counter="{{ $loop->index }}">

                                                <!--treaty-->
                                                <div class="col-sm-3 prem_type_treaty_div tpr_section_div">
                                                    <label class="required ">Treaty</label>
                                                    <select class="form-inputs select2 prem_type_treaty tpr_section"
                                                        name="prem_type_treaty[]" data-reinclass=""
                                                        data-class-counter="{{ $loop->parent->index }}"
                                                        data-counter="{{ $loop->index }}"
                                                        id="prem_type_treaty-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                        required>
                                                        @foreach ($treatytypes as $treatytype)
                                                            @if ($treatytype->treaty_code == $premType->treaty)
                                                                <option value="{{ $treatytype->treaty_code }}" selected>
                                                                    {{ $treatytype->treaty_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!--reinsurance premium types-->
                                                <div class="col-sm-3 prem_type_code_div tpr_section_div">
                                                    <label class="required ">Premium Type</label>
                                                    <input type="hidden"
                                                        class="form-inputs prem_type_reinclass tpr_section"
                                                        id="prem_type_reinclass-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                        name="prem_type_reinclass[]"
                                                        data-counter="{{ $loop->index }}">

                                                    <select class="form-inputs select2 prem_type_code tpr_section"
                                                        name="prem_type_code[]" data-counter="{{ $loop->index }}"
                                                        id="prem_type_code-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                        data-reinclass=""
                                                        data-class-counter="{{ $loop->parent->index }}" data-treaty=""
                                                        required>
                                                        @foreach ($reinClassPremTypes as $reinPremType)
                                                            <option value="{{ $reinPremType->premtype_code }}"
                                                                @if ($premType->premtype_code == $reinPremType->premtype_code) selected @endif>
                                                                {{ $reinPremType->premtype_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-sm-3 prem_type_comm_rate_div tpr_section_div">
                                                    <label class="required ">Commision(%)</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text"
                                                            class="form-control prem_type_comm_rate tpr_section"
                                                            name="prem_type_comm_rate[]"
                                                            data-counter="{{ $loop->index }}"
                                                            id="prem_type_comm_rate-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                            value="{{ number_format($premType->comm_rate, 2) }}">
                                                        @if ($loop->first)
                                                            <button class="btn btn-primary add-comm-section"
                                                                type="button"
                                                                id="add-comm-section-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                data-counter="{{ $loop->parent->index }}">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-danger remove-comm-section"
                                                                type="button"
                                                                id="remove-comm-section-{{ $loop->parent->index }}-{{ $loop->index }}"><i
                                                                    class="fa fa-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div id="tnp_section">
                    <div class="row">
                        <!--reinsurance main class-->
                        <div class="col-sm-3 reinclass_code tnp_section_div">
                            <label class="form-label required">Reinsurance Class</label>
                            <select class="form-inputs select2 reinclass_code tnp_section" name="reinclass_code[]"
                                id="tnp_reinclass_code" multiple required>
                                @if ($trans_type == 'NEW')
                                    {{-- <option value="">Choose Reinsurance Class</option> --}}
                                    @foreach ($reinsclasses as $reinsclass)
                                        <option value="{{ $reinsclass->class_code }}">
                                            {{ $reinsclass->class_name }}</option>
                                    @endforeach
                                @elseif(
                                    $trans_type == 'EXT' ||
                                        $trans_type == 'CNC' ||
                                        $trans_type == 'REN' ||
                                        $trans_type == 'RFN' ||
                                        $trans_type == 'NIL' ||
                                        $trans_type == 'INS' ||
                                        $trans_type == 'EDIT')
                                    @foreach ($reinsclasses as $reinsclass)
                                        @if ($reinsclass->class_code == $old_endt_trans->reinsclass_code)
                                            <option value="{{ $reinsclass->class_code }}" selected>
                                                {{ $reinsclass->class_name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <!--Burning Cost (B) / Flat Rate (F)-->
                        <div class="col-sm-3 method tnp_section_div">
                            <label class="required method ">Burning Cost (B) / Flat Rate (F)</label>
                            <select name="method" id="method" class="form-inputs method tnp_section">
                                <option value="">-- Select Method --</option>
                                <option value="B" @if (!empty($old_endt_trans) && $old_endt_trans->method == 'B') selected @endif>Burning
                                    Cost (B)</option>
                                <option value="F" @if (!empty($old_endt_trans) && $old_endt_trans->method == 'F') selected @endif>Flat
                                    Rate (F)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group tnp_section_div" id="layer-section">
                    <h4> Layers Section </h4>
                    <button class="btn btn-primary" type="button" id="add-layer-section">
                        <i class="fa fa-plus"></i>Add Layer
                    </button>
                    @if ($trans_type != 'EDIT')
                        <h6> Layer: 1 </h6>
                        <div class="layer-sections" id="layer-section-0" data-counter="0">
                            <div class="row">
                                <div class="col-sm-2 limit_per_reinclass_div">
                                    <label class="form-label required">Capture Limits per Class?</label>
                                    <select class="form-inputs limit_per_reinclass" name="limit_per_reinclass"
                                        id="limit_per_reinclass-0-0" required>
                                        <option value="">Select Option</option>
                                        <option value="N" selected>No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-1">
                                    <label class="form-label required">Reinclass</label>
                                    <input type="hidden" class="form-control layer_no" id="layer_no-0-0"
                                        name="layer_no[]" value="1" readonly>
                                    <input type="hidden" class="form-control nonprop_reinclass"
                                        id="nonprop_reinclass-0-0" name="nonprop_reinclass[]" value="ALL" readonly>
                                    <input type="text" class="form-control nonprop_reinclass_desc"
                                        id="nonprop_reinclass_desc-0-0" name="nonprop_reinclass_desc[]" value="ALL"
                                        readonly>
                                </div>

                                <!-- Indemnity -->
                                <div class="col-sm-2 indemnity_treaty_limit_div">
                                    <label class="form-label required">Limit</label>
                                    <input type="text" class="form-inputs indemnity_treaty_limit"
                                        id="indemnity_treaty_limit-0-0" name="indemnity_treaty_limit[]"
                                        onkeyup="this.value=numberWithCommas(this.value)" required>
                                </div>

                                <!-- Deductible Amount -->
                                <div class="col-sm-2 underlying_limit_div">
                                    <label class="form-label required">Deductible Amount</label>
                                    <input type="text" class="form-inputs underlying_limit"
                                        id="underlying_limit-0-0" name="underlying_limit[]"
                                        onkeyup="this.value=numberWithCommas(this.value)" required>
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach ($coverReinLayers as $reinLayer)
                            @php
                                $perClass = $coverReinLayers->where('reinclass', '<>', 'ALL')->count() > 0 ? 'Y' : 'N';
                            @endphp
                            <h6> Layer: {{ $loop->iteration }} </h6>
                            <div class="layer-sections" id="layer-section-{{ $loop->index }}"
                                data-counter="{{ $loop->index }}">
                                <div class="row">
                                    <div class="col-sm-2 limit_per_reinclass_div">
                                        <label class="form-label required">Capture Limits per Class?</label>
                                        <select class="form-inputs limit_per_reinclass" name="limit_per_reinclass"
                                            id="limit_per_reinclass-{{ $loop->index }}-0" required>
                                            <option value="">Select Option</option>
                                            <option value="N" @if ($perClass == 'N') selected @endif>No
                                            </option>
                                            <option value="Y" @if ($perClass == 'Y') selected @endif>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-1">
                                        <label class="form-label required">Reinclass</label>
                                        <input type="hidden" class="form-control layer_no"
                                            id="layer_no-{{ $loop->index }}-0" name="layer_no[]" value="1"
                                            readonly>
                                        <input type="hidden" class="form-control nonprop_reinclass"
                                            id="nonprop_reinclass-{{ $loop->index }}-0" name="nonprop_reinclass[]"
                                            value="{{ $reinLayer->reinclass }}" readonly>
                                        <input type="text" class="form-control nonprop_reinclass_desc"
                                            id="nonprop_reinclass_desc-{{ $loop->index }}-0"
                                            name="nonprop_reinclass_desc[]" value="{{ $reinLayer->reinclass }}"
                                            readonly>
                                    </div>

                                    <!-- Indemnity -->
                                    <div class="col-sm-2 indemnity_treaty_limit_div">
                                        <label class="form-label required">Limit</label>
                                        <input type="text" class="form-inputs indemnity_treaty_limit"
                                            id="indemnity_treaty_limit-{{ $loop->index }}-0"
                                            value="{{ number_format($reinLayer->indemnity_limit, 2) }}"
                                            name="indemnity_treaty_limit[]"
                                            onkeyup="this.value=numberWithCommas(this.value)" required>
                                    </div>

                                    <!--Underlying Limit-->
                                    <div class="col-sm-2 underlying_limit_div tnp_section_div">
                                        <label class="form-label required">Deductible Amount</label>
                                        <input type="text" class="form-inputs underlying_limit tnp_section"
                                            aria-label="underlying_limit" id="underlying_limit-{{ $loop->index }}-0"
                                            value="{{ number_format($reinLayer->underlying_limit, 2) }}"
                                            name="underlying_limit[]" onkeyup="this.value=numberWithCommas(this.value)"
                                            required>
                                    </div>

                                    <!--EGNPI (Estimated Premium)-->
                                    <div class="col-sm-2 egnpi_div tnp_section_div">
                                        <label class="form-label required">EGNPI</label>
                                        <input type="text" class="form-inputs egnpi tnp_section" aria-label="egnpi"
                                            id="egnpi-{{ $loop->index }}-0"
                                            value="{{ number_format($reinLayer->egnpi, 2) }}" name="egnpi[]"
                                            onkeyup="this.value=numberWithCommas(this.value)" required>
                                    </div>

                                    <!--For Burning Cost (B) --- Minimum Rate: (%)-->
                                    <div class="col-sm-3 burning_rate_div tnp_section_div">
                                        <label class="form-label required">Burning Cost-Minimum Rate(%)</label>
                                        <input type="text" name="min_bc_rate[]"
                                            id="min_bc_rate-{{ $loop->index }}-0"
                                            class="form-inputs burning_rate tnp_section"
                                            value="{{ number_format($reinLayer->min_bc_rate, 2) }}">
                                    </div>

                                    <!--Maximum Rate: (%)-->
                                    <div class="col-sm-2 burning_rate_div tnp_section_div">
                                        <label class="form-label required">Maximum Rate: (%)</label>
                                        <input type="text" name="max_bc_rate[]"
                                            id="max_bc_rate-{{ $loop->index }}-0"
                                            class="form-inputs burning_rate tnp_section"
                                            value="{{ number_format($reinLayer->max_bc_rate, 2) }}">
                                    </div>

                                    <!--For Flat Rate: (%)-->
                                    <div class="col-sm-2 flat_rate_div tnp_section_div ">
                                        <label class="form-label required">For Flat Rate: (%)</label>
                                        <input type="text" name="flat_rate[]"
                                            id="flat_rate-{{ $loop->index }}-0"
                                            class="form-inputs flat_rate tnp_section"
                                            value="{{ number_format($reinLayer->flat_rate, 2) }}"
                                            @if ($old_endt_trans->method == 'B') required readonly @endif>
                                    </div>

                                    <!--Adjustable Annually Rate-->
                                    <div class="col-sm-3 burning_rate_div tnp_section_div">
                                        <label class="form-label required">Upper Adjust. Annually Rate</label>
                                        <input type="text" name="upper_adj[]"
                                            id="upper_adj-{{ $loop->index }}-0"
                                            class="form-inputs burning_rate tnp_section"
                                            value="{{ number_format($reinLayer->upper_adj, 2) }}" required>
                                    </div>

                                    <!--Adjustable Annually Rate-->
                                    <div class="col-sm-3 burning_rate_div tnp_section_div">
                                        <label class="form-label required">Lower Adjust. Annually Rate</label>
                                        <input type="text" name="lower_adj[]"
                                            id="lower_adj-{{ $loop->index }}-0"
                                            class="form-inputs burning_rate tnp_section"
                                            value="{{ number_format($reinLayer->lower_adj, 2) }}" required>
                                    </div>

                                    <!--Minimum Premium Deposit-->
                                    <div class="col-sm-3 min_deposit_div tnp_section_div">
                                        <label class="form-label required">Minimum Deposit Premium </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="min_deposit[]"
                                                id="min_deposit-{{ $loop->index }}-0"
                                                class="form-control min_deposit tnp_section"
                                                value="{{ number_format($reinLayer->min_deposit, 2) }}"
                                                onkeyup="this.value=numberWithCommas(this.value)" required>
                                        </div>
                                    </div>

                                    {{-- Reinstatement Type Arrangement --}}
                                    <div class="col-sm-3 reinstatement_type_div tnp_section_div">
                                        <label class="form-label required"> Reinstatement Type </label>
                                        <div class="input-group mb-3">
                                            <select name="reinstatement_type[]"
                                                id="reinstatement_type-{{ $loop->index }}-0"
                                                class="form-inputs select2" required>
                                                <option value="">Please Select</option>
                                                <option value="NOR"
                                                    @if ($reinLayer->reinstatement_type == 'NOR') selected @endif>Number
                                                    of
                                                    Reinstatement</option>
                                                <option value="AAL"
                                                    @if ($reinLayer->reinstatement_type == 'AAL') selected @endif>Annual
                                                    Aggregate Limit</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Reinstatement Type Value --}}
                                    <div class="col-sm-3 reinstatement_value_div tnp_section_div">
                                        <label class="form-label required"> Reinstatement Value </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="reinstatement_value[]"
                                                id="reinstatement_value-{{ $loop->index }}-0"
                                                class="form-control reinstatement_value tnp_section"
                                                value="{{ $reinLayer->reinstatement_value }}"
                                                onkeyup="this.value=numberWithCommas(this.value)" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="row row-cols-12">
                    <div class="col-md-2">
                        <label class="form-label" for="coverfrom">Cover Start Date</label>
                        @switch($trans_type)
                            @case('NEW')
                                <input type="date" class="form-inputs" aria-label="covstartdate" id="coverfrom"
                                    name="coverfrom" required>
                            @break

                            @case('REN')
                                <input type="date" class="form-inputs" aria-label="covstartdate" id="coverfrom"
                                    name="coverfrom" value="{{ $renewal_date }}" required>
                            @break

                            @case('EDIT')
                                <input type="date" class="form-inputs" aria-label="covstartdate" id="coverfrom"
                                    name="coverfrom" value="{{ $old_endt_trans->cover_from->format('Y-m-d') }}" required>
                            @break

                            @default
                                <input type="date" class="form-inputs" aria-label="covstartdate" id="coverfrom"
                                    name="coverfrom" value="{{ $old_endt_trans->cover_from->format('Y-m-d') }}"
                                    @readonly(true) required>
                        @endswitch
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cover End Date</label>
                        @switch($trans_type)
                            @case('NEW')
                            @case('REN')
                                <input type="date" class="form-inputs" aria-label="covenddate" id="coverto"
                                    name="coverto" required>
                            @break

                            @case('EDIT')
                                <input type="date" class="form-inputs" aria-label="covenddate" id="coverto"
                                    name="coverto" value="{{ $old_endt_trans->cover_to->format('Y-m-d') }}" required>
                            @break

                            @default
                                <input type="date" class="form-inputs" aria-label="covenddate" id="coverto"
                                    name="coverto" value="{{ $old_endt_trans->cover_to->format('Y-m-d') }}" @readonly(true)
                                    required>
                        @endswitch

                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="leadSource">Underwriter</label>
                        <select class="form-inputs select2" id="leadSource">
                            <option value="">-- Select Staff --</option>
                            @if ($staff)
                                @foreach ($staff as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-2">
                    @switch($trans_type)
                        @case('NEW')
                            <button type="button" id="save_cover" class="btn btn-success btn-raised-shadow btn-wave"><span
                                    class="me-2">
                                    Save</span> <i class="bi bi-save"></i></button>
                        @break

                        @case('EDIT')
                            <button type="submit" id="save_cover" class="btn btn-dark btn-raised-shadow btn-wave">Save <i
                                    class="bx bi-save"></i></button>
                        @break

                        @case('EXT')
                        @case('CNC')

                        @case('RFN')
                        @case('INS')
                            <button type="submit" id="ext_cover" class="btn btn-dark btn-raised-shadow btn-wave">Save <i
                                    class="bx bi-save"></i></button>
                        @break

                        @case('REN')
                            <button type="submit" id="save_cover"
                                class="btn btn-dark btn-raised-shadow btn-wave">Renew</button>
                        @break
                    @endswitch
                </div>
            </div>
        </form>

        <div id="page-loader">
            <div class="loader-wrapper">
                <div class="loader"></div>
                <p class="loader-text"></p>
            </div>
        </div>

        <div class="modal effect-scale md-wrapper" id="addInsuredDataModal" tabindex="-1"
            aria-labelledby="addInsuredModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addInsuredModalLabel"><i class="bx bx-plus"></i> Add Insured</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('customer.store') }}" method="POST" id="partnerForm">
                            @csrf
                            <div class="card border border-dark custom-card mb-3">
                                <div class="card-header">
                                    <div class="card-title">Partner Details</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="partnerName" class="form-label">Partner Name</label>
                                            <input type="text" class="form-inputs" id="partnerName"
                                                name="partnerName" {{-- value="{{ old('partnerName') }}" --}}>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="customerType" class="form-label">Type of Customer</label>
                                            <div class="card-md">
                                                <select
                                                    class="form-inputs select2 @error('customerType') is-invalid @enderror"
                                                    id="customerType" name="customerType">
                                                    <option value="" selected disabled>-- Select --</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="financialRating" class="form-label">Financial Rating</label>
                                            <input type="text"
                                                class="form-inputs @error('financialRating') is-invalid @enderror"
                                                id="financialRating" name="financialRating" {{-- value="{{ old('financialRating') }}" --}}>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-inputs" id="email"
                                                {{-- name="email" value="{{ old('email') }}" --}}>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="street" class="form-label">Street</label>
                                            <input type="text" class="form-inputs" id="street" name="street"
                                                {{-- value="{{ old('street') }}" --}}>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="agencyRating" class="form-label">Agency Rating</label>
                                            <input type="text"
                                                class="form-inputs @error('agencyRating') is-invalid @enderror"
                                                id="agencyRating" name="agencyRating" {{-- value="{{ old('agencyRating') }}" --}}>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="taxNo" class="form-label">Tax No</label>
                                            <input type="text" class="form-inputs" id="taxNo" name="taxNo"
                                                {{-- value="{{ old('taxNo') }}" --}}>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="incorporationNo" class="form-label">Incorporation No</label>
                                            <input type="text" class="form-inputs" id="incorporationNo"
                                                name="incorporationNo" placeholder="Registration No"
                                                {{-- value="{{ old('incorporationNo') }}" --}} />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" class="form-inputs" id="website" name="website"
                                                {{-- value="{{ old('website') }}" --}} />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-inputs" id="city" name="city"
                                                {{-- value="{{ old('city') }}" --}} />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="identityType" class="form-label">Identity Type</label>
                                            <div class="card-md">
                                                <select class="form-inputs select2" id="identityType"
                                                    name="identityType">
                                                    <option value="" selected disabled>-- Select ID Type --</option>
                                                    <option value="passport"
                                                        {{ old('identityType') == 'passport' ? 'selected' : '' }}>
                                                        Passport</option>
                                                    <option value="nationalId"
                                                        {{ old('identityType') == 'nationalId' ? 'selected' : '' }}>
                                                        National ID</option>
                                                    <option value="drivingLicense"
                                                        {{ old('identityType') == 'drivingLicense' ? 'selected' : '' }}>
                                                        Driving License
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="telephone" class="form-label">Telephone</label>
                                            <input type="tel" class="form-inputs" id="telephone"
                                                name="telephone" {{-- value="{{ old('telephone') }}" --}} />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="postalCode" class="form-label">Postal Code</label>
                                            <input type="text" class="form-inputs" id="postalCode"
                                                name="postalCode" v {{-- alue="{{ old('postalCode') }}" --}}>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="identityNo" class="form-label">Identity No.</label>
                                            <input type="text" class="form-inputs" id="identityNo"
                                                name="identityNo" placeholder="ID No" {{-- value="{{ old('identityNo') }}" --}} />
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="country" class="form-label">Country</label>
                                            <div class="card-md">
                                                <select class="form-inputs select2" id="country" name="country">
                                                    <option value="" selected disabled>-- Select Country --</option>
                                                    {{-- @foreach ($countries as $country)
                                                        <option value="{{ $country->country_iso }}"
                                                            {{ old('country') == $country->country_iso ? 'selected' : '' }}>
                                                            {{ $country->country_iso }} - {{ $country->country_name }}
                                                        </option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light btn-wave waves-effect waves-light"
                            id="cancelRenewalEmail" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-success label-btn label-end" id="confirmReinEmail">
                            Save
                            <i class="bi  bi-save label-btn-icon ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const trans_type = $('#trans_type').val();
            var selected_reinclass = [];
            const resetableTransTypes = ['NEW']
            var installmentTotalAmount = 0;

            let handoverProspectId = "{{ $prospectId ?? null }}";

            $("#page-loader").fadeOut()

            $('#tnp_section').hide();
            $('#tpr_section').hide();
            $('#fac_section').hide();
            $('#trt_common').hide();
            $('#treaty_grp').hide();
            $('#eml_rate').hide();
            $('#eml_amt').hide();
            $('.eml-div').hide();
            $('.brokerage_comm_amt_div').hide();
            $('.brokerage_comm_rate_div').hide();
            $('.brokerage_comm_rate_amnt_div').hide();

            const coverFromInput = document.getElementById('coverfrom');
            const coverToInput = document.getElementById('coverto');
            coverFromInput.addEventListener('change', checkDateValidity);
            coverToInput.addEventListener('change', checkDateValidity);

            function checkDateValidity() {
                const coverFromDate = new Date(coverFromInput.value);
                const coverToDate = new Date(coverToInput.value);

                if (coverFromDate >= coverToDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date Range',
                        text: 'Cover from date must be earlier than cover to date',
                    })
                    coverFromInput.value = null;
                    coverToInput.value = null;
                }
            }

            $('#risk_details_content').on('paste', function(e) {
                const clipboardData = (e.originalEvent || e).clipboardData;
                const pastedText = clipboardData.getData('text/html');

                if (pastedText) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(pastedText, 'text/html');
                    const table = $(doc).find('table');
                    const currentText = $(this).val();

                    if (table.length) {
                        $("hidden_risk_details").val(table);
                    } else {
                        $("hidden_risk_details").val(currentText + pastedText);
                    }
                }
            });

            $("form#register_cover").validate({
                ignore: ":hidden",
                rules: {
                    covertype: {
                        required: true
                    },
                    branchcode: {
                        required: true
                    },
                },
                messages: {
                    covertype: {
                        required: "Cover Type is required"
                    },
                    branchcode: {
                        required: "Branch is required"
                    },
                },
                errorPlacement: function(error, element) {
                    error.addClass("text-danger");
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('error').removeClass('valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error').addClass('valid');
                },
            })

            $("form #save_cover").on('click', function(e) {
                e.preventDefault()
                if ($("form#register_cover").valid()) {
                    if ($("select#pay_method option:selected").attr('pay_method_desc') === 'I') {
                        if ($("#fac-installments-section").is(":empty")) {
                            toastr.error("Please click `Add Installment`");
                            return false;
                        }
                        $('#fac-installments-section').find('.fac-instalament-row').each(function(index) {
                            const idx = index + 1
                            const noInput = $(`input#instl_no_${idx}`);
                            const dateInput = $(`input#instl_date_${idx}`);
                            const amountInput = $(`input#instl_amnt_${idx}`);

                            if (!dateInput.val()) {
                                dateInput.attr('required', 'required');
                                return false;
                            } else {
                                dateInput.removeAttr('required');
                            }

                            if (!noInput.val()) {
                                noInput.attr('required', 'required');
                                return false;
                            } else {
                                noInput.removeAttr('required');
                            }

                            if (!amountInput.val()) {
                                amountInput.attr('required', 'required');
                                return false;
                            } else {
                                amountInput.removeAttr('required');
                            }
                        });

                        if (trans_type == 'EDIT') {
                            var instalAmount = computateInstalment()
                            const totalInstallments = parseInt($('#no_of_installments').val().replace(/,/g,
                                    '')) ||
                                0;
                            const totalFacAmount = parseFloat(instalAmount) || 0;
                            const totalFacInstAmt = (totalFacAmount / totalInstallments).toFixed(2);
                            installmentTotalAmount = totalFacAmount
                        }
                        var totalInstallment = 0;
                        $("#fac-installments-section input[name='installment_amt[]']").each(function() {
                            const value = parseFloat($(this).val().replace(/,/g, ''));
                            if (!isNaN(value)) {
                                totalInstallment += value;
                            }
                        });

                        if (!areDecimalsEqual(installmentTotalAmount, totalInstallment)) {
                            toastr.error("The total installment amount does not match the FAC amount.");
                            return false;
                        }
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to submit the form?",
                        icon: false,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, submit',
                        cancelButtonText: 'No, cancel',
                        customClass: {
                            confirmButton: 'custom-confirm',
                            cancelButton: 'swal2-cancel'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#hidden_risk_details').val($('#risk_details_content').html());
                            $("form#register_cover").submit();
                        } else {
                            return false;
                        }
                    });
                } else {
                    toastr.error("Please correct the errors before submitting.");
                }
            })

            $("select#type_of_bus").change(function() {
                var bustype = $("select#type_of_bus option:selected").attr('value');

                if (bustype == 'FPR' || bustype == 'FNP') {
                    $('#fac_section').show();

                    $('#tpr_section').hide();
                    $('#tnp_section').hide();
                    $('#trt_common').hide();
                    $('#treaty_grp').hide();

                    processSections('.fac_section', '.fac_section_div', 'enable');
                    processSections('.reins_comm_rate', '.reins_comm_rate_div', 'disable');
                    processSections('.tpr_section', '.tpr_section_div', 'disable');
                    processSections('.tnp_section', '.tnp_section_div', 'disable');
                    processSections('.trt_common', '.trt_common_div', 'disable');
                    processSections('.treaty_grp', '.treaty_grp_div', 'disable');
                } else if (bustype == 'TPR') {
                    $('#treaty_grp').show();
                    $('#trt_common').show();
                    $('#tpr_section').show();
                    $('#fac_section').hide();
                    $('#tnp_section').hide();

                    processSections('.trt_common', '.trt_common_div', 'enable');
                    processSections('.treaty_grp', '.treaty_grp_div', 'enable');
                    processSections('.tpr_section', '.tpr_section_div', 'enable');
                    processSections('.reinsurer_per_treaty', '.reinsurer_per_treaty_div', 'disable');
                    processSections('.fac_section', '.fac_section_div', 'disable');
                    processSections('.tnp_section', '.tnp_section_div', 'disable');



                } else if (bustype == 'TNP') {
                    $('#treaty_grp').show();
                    $('#trt_common').show();
                    $('#tnp_section').show();
                    $('#tpr_section').hide();
                    $('#fac_section').hide();

                    processSections('.trt_common', '.trt_common_div', 'enable');
                    processSections('.treaty_grp', '.treaty_grp_div', 'enable');
                    processSections('.tnp_section', '.tnp_section_div', 'enable');
                    processSections('.tpr_section', '.tpr_section_div', 'disable');
                    processSections('.fac_section', '.fac_section_div', 'disable');


                }

                let selectedTreatyType = ''
                @if (!empty($old_endt_trans))
                    selectedTreatyType = '{!! $old_endt_trans->treaty_type !!}'
                @endif

                $.ajax({
                    url: "{{ route('cover.get_treatyperbustype') }}",
                    data: {
                        "type_of_bus": bustype
                    },
                    type: "get",
                    success: function(resp) {
                        $(`#treatytype`).empty();

                        $(`#treatytype`).append($('<option>').text(
                                '-- Select Treaty Type--')
                            .attr('value', ''));
                        $.each(resp, function(i, value) {
                            $(`#treatytype`).append($('<option>').text(value
                                    .treaty_code + " - " + value.treaty_name)
                                .attr('value', value.treaty_code)
                            );
                        });
                        $(`#treatytype option[value='${selectedTreatyType}']`).prop(
                            'selected',
                            true)
                        $(`#treatytype`).trigger('change.select2');
                    },
                    error: function(resp) {
                        console.error;
                    }
                })
            });

            $('select#type_of_bus').trigger('change');

            $("select#class_group").change(function() {
                var class_group = $("select#class_group option:selected").attr('value');
                $('#classcode').empty();
                if ($(this).val() != '') {
                    $('#class').prop('disabled', false)
                    $.ajax({
                        url: "{{ route('get_class') }}",
                        data: {
                            "class_group": class_group
                        },
                        type: "get",
                        success: function(resp) {
                            $('#classcode').empty();
                            var classes = resp ? JSON.parse(resp) : [];
                            $('#classcode').append($('<option>').text(
                                    '-- Select Class Name--')
                                .attr('value', ''));
                            $.each(classes, function(i, value) {
                                $('#classcode').append($('<option>').text(value
                                        .class_code + " - " + value.class_name)
                                    .attr('value', value.class_code)
                                );
                            });

                            $('.section').trigger("chosen:updated");
                        },
                        error: function(resp) {
                            console.error(resp);
                        }
                    })
                }
            });

            $('select#covertype').trigger('change');

            if ($("select#covertype option:selected").attr('covertype_desc') != 'B') {
                $('#bindercoversec').hide();
            }

            /*** On change of cover Type ***/
            $("select#covertype").change(function() {
                // var binder = $("#covertype").val();
                var binder = $("select#covertype option:selected").attr('covertype_desc')
                $('#bindercoverno').empty();

                if (binder == 'B') {
                    $('#bindercoversec').show();

                    $.ajax({
                        url: "{{ route('get_binder_covers') }}",
                        //data:{"branch":branch},
                        type: "get",
                        success: function(resp) {

                            /*remove the choose branch option*/
                            $('#bindercoverno').empty();
                            var binders = $.parseJSON(resp);
                            $('#bindercoverno').append($('<option>').text(
                                    'Select Binder Cover')
                                .attr('value', ''));

                            $.each(binders, function(i, value) {
                                $('#bindercoverno').append($('<option>').text(value
                                    .binder_cov_no + "  -  " + value
                                    .agency_name
                                ).attr('value', value.binder_cov_no));
                            });

                            $('.section').trigger("chosen:updated");
                        },
                        error: function(resp) {
                            console.error;
                        }
                    })
                } else if (binder == 'N') {
                    $('#bindercoverno').empty();
                    $('#bindercoversec').hide();
                    $('.section').trigger("chosen:updated");

                    $('#bindercoverno').prop('required', false);
                }
            });

            /*** On change Pay Method ***/
            $("select#pay_method").change(function() {
                var pm = $("select#pay_method option:selected").attr('pay_method_desc')
                $('#no_of_installments').empty();
                if (trans_type === 'NEW') {
                    if (pm === 'I') {
                        $('#no_of_installments_sec').show();
                        $('#fac_installments_box').hide();
                        $('#no_of_installments').prop('required', true);
                        $('#no_of_installments').empty();
                        $('#no_of_installments').val();
                        $('#add_fac_inst_btn_section').show();
                    } else {
                        $('#no_of_installments').val(1);
                        $('#no_of_installments_sec').hide();
                        $('#fac_installments_box').hide();
                        $('#add_fac_inst_btn_section').hide();
                        $('#no_of_installments').prop('required', false);
                    }
                } else {
                    if (pm === 'I') {
                        $('#fac_installments_box').show();
                        $('#edit_no_of_installments_sec').show();
                        $('#edit_fac_inst_btn_section').show();
                        $('#instalment_sec_div').show();
                    } else {
                        $('#edit_no_of_installments_sec').hide();
                        $('#edit_fac_inst_btn_section').hide();
                        $('#instalment_sec_div').hide();
                        $('#fac_installments_box').hide();
                    }
                }
            });

            $('select#pay_method').trigger('change');

            /*** On change Broker Flag ***/
            $("select#broker_flag").change(function() {
                var broker_flag = $("select#broker_flag option:selected").attr('value');
                // $('#brokercode').empty();
                if (broker_flag == 'Y') {
                    $('.brokercode_div').show();
                    $('#brokercode').prop('required', true);
                    $('#brokercode').prop('disabled', false);
                } else {
                    $('#brokercode').val('');
                    $('.brokercode_div').hide();
                    $('#brokercode').prop('required', false);
                    $('#brokercode').prop('disabled', true);
                }
            });

            $("select#broker_flag").trigger('change')

            $('#add_fac_instalments').on('click', function() {
                var noOfInstallments = $("#no_of_installments").val();
                var businessType = $("#type_of_bus").val();
                var cedantPremium = $("#cede_premium").val();
                var facShareOffered = $("#fac_share_offered").val();
                var commRate = $("#comm_rate").val();

                if (Boolean(noOfInstallments === '')) {
                    toastr.error(`Please add Installments`, 'Incomplete data')
                    return false
                } else if (Boolean(businessType === '')) {
                    toastr.error(`Please Select Business Type`, 'Incomplete data')
                    return false
                } else if (Boolean(cedantPremium === '')) {
                    toastr.error(`Please add Cedant Premium`, 'Incomplete data')
                    return false
                } else if (Boolean(facShareOffered === '')) {
                    toastr.error(`Please add Share Offered`, 'Incomplete data')
                    return false
                } else if (Boolean(commRate === '')) {
                    toastr.error(`Please add Commission Rate`, 'Incomplete data')
                    return false
                }

                var instalAmount = computateInstalment()
                // computation for cedant installment amount
                $("#installment_total_amount").val(instalAmount);

                $('#fac_installments_box').show();
                // $('#no_of_installments').trigger('change');
                $('#fac-installments-section').empty()
                const totalInstallments = parseInt($('#no_of_installments').val().replace(/,/g, '')) ||
                    0;
                var no_of_installments = parseInt($('#no_of_installments').val().replace(/,/g, '')) ||
                    0;
                if (no_of_installments > 0) {
                    const totalAmount = instalAmount;
                    const totalFacAmount = parseFloat(totalAmount) || 0;
                    const totalFacInstAmt = (totalFacAmount / totalInstallments).toFixed(2);

                    installmentTotalAmount = totalFacAmount

                    if (totalInstallments <= 100) {
                        for (let i = 1; i <= totalInstallments; i++) {
                            $('#fac-installments-section').append(`
                                <div class="row fac-instalament-row" data-count="${i}">
                                    <div class="col-md-3">
                                        <label class="form-label">Installment</label>
                                        <input type="hidden" name="installment_no[]" value="${i}" readonly class="form-inputs"/>
                                        <input type="hidden" name="installment_id[]" value=""/>
                                        <input type="text" value="installment No. ${i}" id="instl_no_${i}" readonly class="form-inputs" required/>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="instl_date_${i}">Installment Due Date</label>
                                        <input type="date" name="installment_date[]" id="instl_date_${i}"  class="form-inputs" required/>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="instl_amnt_${i}">Total Installment Amount</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="installment_amt[]" id="instl_amnt_${i}" value="${numberWithCommas(totalFacInstAmt)}" class="form-inputs form-input-group amount"  onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required/>
                                            <button class="btn btn-danger btn-sm remove-fac-instalment" type="button" id="remove-fac-instalment"><i class="bx bx-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            `);
                        }
                    }
                }
            });

            $('#fac-installments-section').on('click', '.remove-fac-instalment', function() {
                // const removedIndex = $(this).closest('.fac-instalament-row').data('count');
                const currentInstallments = $('#no_of_installments').val();
                const remaingInstalment = currentInstallments >= 1 ? parseInt(currentInstallments - 1) :
                    0;
                if (remaingInstalment > 0) {
                    $('#no_of_installments').val(remaingInstalment);
                } else {
                    $('#no_of_installments').val('');
                    $('#fac_installments_box').hide();
                }
                $('#no_of_installments').trigger('change');
                $(this).closest('.fac-instalament-row').remove();
            });

            $('select#currency_code').trigger('change');
            /*currency logic*/
            $("select#currency_code").change(function() {
                var selected_currency = $("select#currency_code option:selected").attr('value');
                var selected_descr = $("select#currency_code option:selected").text();

                //ajax to check for date
                $.ajax({
                    url: "{{ route('get_todays_rate') }}",
                    data: {
                        'currency_code': selected_currency
                    },
                    type: "get",
                    success: function(resp) {
                        var status = $.parseJSON(resp);
                        // alert(status.valid);

                        if (status.valid == 2) {
                            // alert('test');
                            $('#today_currency').val(1);
                            $('#today_currency').prop('readonly', true)
                        } else if (status.valid == 1) {
                            // alert('test');
                            //populate rate field
                            $('#today_currency').val(status.rate);
                            $('#today_currency').prop('readonly', true)
                        } else {
                            $('#today_currency').prop('readonly', true)
                            $('#today_currency').val('');
                            alert('Currency rate for the day not yet set');
                            // $.ajax({
                            //     url:" {{ route('yesterdayRate') }}",
                            //     data: {'currency_code':selected_currency},
                            //     type: "GET",
                            //     success: function(resp) {
                            //         // alert(resp);
                            //         if (resp ==  0) {
                            //             $('#today_currency').prop('readonly', true)
                            //             $('#today_currency').val('');
                            //             $.notify({
                            //                 title: "<strong>Today's currency rate not Set </strong><br>",
                            //                 message: "Using yesterday's currency rate. Adjust currency rate to fit today's rate",
                            //             },{
                            //                 type: 'warning'
                            //             });
                            //         } else {
                            //             var rate = resp.currency_rate
                            //             $('#today_currency').val(rate);
                            //             $('#today_currency').prop('readonly', true)
                            //         }
                            //     }
                            // })
                        }
                    },
                    error: function(resp) {
                        //alert("Error");
                        console.error;
                    }
                })
            });

            $("select#sum_insured_type").change(function() {
                var label_txt = $("select#sum_insured_type option:selected").text();
                $('#sum_insured_label').text("(" + label_txt + ")");
            });

            $("#comm_rate").keyup(function() {
                var ratex = $(this).val() || 0;
                var cede = parseFloat(removeCommas($('#cede_premium').val())) || 0;
                var commAmount = (ratex / 100) * cede;
                $('#comm_amt').val(numberWithCommas(commAmount.toFixed(2)));
                calculateBrokerageCommRate()
            });

            $("#reins_comm_rate").keyup(function() {
                var ratex = $(this).val() || 0;
                var cede = parseFloat(removeCommas($('#rein_premium').val())) || 0;
                var commAmount = (ratex / 100) * cede;
                $('#reins_comm_amt').val(numberWithCommas(commAmount.toFixed(2)));
                calculateBrokerageCommRate()
            });

            $("#reins_comm_type").change(function() {
                var comm_type = $(this).val();
                // console.log('comm_type:' + comm_type);
                if (comm_type == 'R') {
                    processSections('.reins_comm_rate', '.reins_comm_rate_div', 'enable');
                    $('#reins_comm_amt').prop('readonly', true)
                } else {
                    processSections('.reins_comm_rate', '.reins_comm_rate_div', 'disable');
                    $('#reins_comm_amt').prop('readonly', false)
                }
                resetableTransTypes.includes(trans_type) ? $('#reins_comm_amt').val('') : null;

            });

            $("#reins_comm_type").trigger('change');

            $("#cede_premium").keyup(function() {
                $("#comm_rate").trigger('keyup');
                $("#rein_premium").val($(this).val());
            });

            $("#rein_premium").keyup(function() {
                $("#reins_comm_rate").trigger('keyup');
            });

            $(document).on('change', ".treaty_reinclass", function() {
                const treatyType = $(`#treatytype`).val();
                let counter = $(this).data('counter')
                const reinclass = $(`#treaty_reinclass-${counter}`).val()

                if (treatyType == null || treatyType == '' || treatyType == ' ') {

                    $(`#treaty_reinclass-${counter} option:selected`).removeAttr('selected');
                    $(`#treaty_reinclass-${counter}`).val(null)
                    toastr.error('Please Select Treaty Type First', 'Incomplete data')
                    //
                    return false
                }

                const premTypeCodeSelect = $(`#prem_type_code-${counter}-0`);
                premTypeCodeSelect.attr('data-reinclass', reinclass);

                $(`#prem_type_reinclass-${counter}-0`).val(reinclass);
                $(`#treaty_grp #prem_type_treaty-${counter}-0`).trigger('change')


            });

            $(document).on('change', ".prem_type_code", function() {
                let prem_type_code = $(this).val();
                let classcounter = $(this).data('class-counter')
                let premtypecounter = $(this).data('counter')
                let treaty = $(`#prem_type_treaty-${classcounter}-${premtypecounter}`).val();
                let reinclass = $(`#treaty_reinclass-${classcounter}`).val()

                $(`#prem_type_reinclass-${classcounter}-${premtypecounter}`).val(reinclass);
                // console.log('log',$(`#prem_type_reinclass-${classcounter}-${premtypecounter}`).val());
                const premTypeCodeSelect = $(`#prem_type_code-${classcounter}-${premtypecounter}`);
                premTypeCodeSelect.attr('data-reinclass', reinclass);
                premTypeCodeSelect.attr('data-treaty', treaty);

            });

            if (trans_type == 'EDIT') {
                $('.prem_type_code').each(function() {
                    $(this).trigger('change')
                });
            }

            $(document).on('change', ".prem_type_treaty", function() {
                let treaty = $(this).val();
                let classcounter = $(this).data('class-counter')
                let premtypecounter = $(this).data('counter')
                let reinclass = $(`#treaty_reinclass-${classcounter}`).val()
                // console.log('treaty:' + treaty + ' reinclass:' + reinclass + ' classcounter:' +
                //     classcounter + ' premcounter:' + premtypecounter);

                fetchPremTypes(treaty, premtypecounter, classcounter)
            });

            function fetchPremTypes(treaty, premCounter, classCounter) {
                let selectedPremTypes = []
                const classElem = $(`#treaty_reinclass-${classCounter}`)
                const reinClass = classElem.val()
                // $('.prem_type_code[data-reinclass="' + reinClass + '"]').each(function () {
                $('.prem_type_code[data-reinclass="' + reinClass + '"][data-treaty="' + treaty + '"]').each(
                    function() {
                        const selectedVal = $(this).find('option:selected').val()
                        if (selectedVal != null && selectedVal != '') {
                            selectedPremTypes.push(selectedVal)
                        }
                    })

                if (classElem.val() != '') {
                    $(`#prem_type_code-${classCounter}-${premCounter}`).prop('disabled', false)

                    $.ajax({
                        url: "{{ route('cover.get_reinprem_type') }}",
                        data: {
                            "reinclass": reinClass,
                            'selectedCodes': selectedPremTypes
                        },
                        type: "get",
                        success: function(resp) {

                            $(`#prem_type_reinclass-${classCounter}`).val(reinClass);
                            /*remove the choose branch option*/
                            $(`#prem_type_code-${classCounter}-${premCounter}`).empty();

                            $(`#prem_type_code-${classCounter}-${premCounter}`).append($(
                                    '<option>')
                                .text('-- Select Premium Type--').attr('value', ''));
                            $.each(resp, function(i, value) {
                                $(`#prem_type_code-${classCounter}-${premCounter}`)
                                    .append($(
                                            '<option>').text(value.premtype_code +
                                            " - " + value
                                            .premtype_name)
                                        .attr('value', value.premtype_code)
                                        .attr('data-reinclass', reinClass)
                                        .attr('data-treaty', treaty)
                                    );


                            });
                            $(`#prem_type_code-${classCounter}-${premCounter}`).trigger(
                                'change.select2');
                        },
                        error: function(resp) {
                            console.error;
                        }
                    })
                }
            }

            $(document).on('click', '.add-comm-section', function() {
                const addSectCounter = $(this).data('counter')

                const lastCommSection = $(`#comm-section-${addSectCounter}`).find(
                    '.comm-sections:last');

                const prevCounter = lastCommSection.data('counter')
                const classCounter = lastCommSection.data('class-counter')
                const reinClassVal = $(`#treaty_reinclass-${classCounter}`).val()
                const premTypeVal = $(`#prem_type_code-${classCounter}-${prevCounter}`).val()
                const premTypeComm = $(`#prem_type_comm_rate-${classCounter}-${prevCounter}`).val()
                if (reinClassVal == null || reinClassVal == '' || reinClassVal == ' ') {
                    toastr.error('Please Select Reinsurance Class', 'Incomplete data')
                    return false
                } else if (premTypeVal == null || premTypeVal == '' || premTypeVal == ' ') {
                    toastr.error('Please Select Premium Type', 'Incomplete data')
                    return false
                } else if (premTypeComm == null || premTypeComm == '' || premTypeComm == ' ') {
                    toastr.error('Input Commission Rate', 'Incomplete data')
                    return false
                }

                // Increment the counter
                let counter = prevCounter + 1;

                appendCommSection(counter, classCounter)
                // fetchPremTypes(counter,classCounter)

                // $(document).find(`#prem_type_code-${classCounter}-${counter}`).select2();

            });

            $(document).on('click', '.remove-comm-section', function() {
                $(this).closest('.comm-sections').remove();
            });

            function appendCommSection(premCounter, classCounter) {
                const reinClassVal = $(`#treaty_reinclass-${classCounter}`).val()
                const treatytype = $('#treatytype').val();

                var btn_class = ''
                var btn_id = ''
                var fa_class = ''
                if (premCounter == 0) {
                    btn_class = 'btn-primary add-comm-section'
                    btn_id = 'add-comm-section'
                    fa_class = 'bx-plus'
                } else {
                    btn_class = 'btn-danger remove-comm-section'
                    btn_id = 'remove-comm-section'
                    fa_class = 'bx-minus'
                }

                $(document).find(`#comm-section-${classCounter}`).append(`
                            <div class="row comm-sections" id="comm-section-${classCounter}-${premCounter}" data-class-counter="${classCounter}" data-counter="${premCounter}">
                                <!-- prem_type_treaty -->
                                <div class="col-sm-3 prem_type_treaty_div">
                                    <label class="form-label required">Treaty</label>
                                    <select class="form-inputs select2 prem_type_treaty" name="prem_type_treaty[]" id="prem_type_treaty-${classCounter}-${premCounter}" data-class-counter="${classCounter}" data-counter="${premCounter}" required>
                                        <option value=""> Select Treaty </option>
                                        <option value="SURP"> SURPLUS </option>
                                        <option value="QUOT"> QUOTA </option>
                                    </select>
                                </div>
                                <!-- reinsurance premium types -->
                                <div class="col-sm-3">
                                    <label class="form-label required">Premium Type</label>
                                    <input type="hidden" class="form-inputs prem_type_reinclass" id="prem_type_reinclass-${classCounter}-${premCounter}" name="prem_type_reinclass[]" data-counter="${premCounter}" value="${reinClassVal}">

                                    <select class="form-inputs select2 prem_type_code" name="prem_type_code[]" id="prem_type_code-${classCounter}-${premCounter}" data-reinclass="${reinClassVal}" data-treaty="" data-class-counter="${classCounter}" data-counter="${premCounter}" required>
                                        <option value="">--Select Premium Type--</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label required">Commision(%)</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-inputs" name="prem_type_comm_rate[]" id="prem_type_comm_rate-${classCounter}-${premCounter}" data-counter="${premCounter}" required>
                                        <button class="btn ${btn_class}" type="button" id="${btn_id}"><i class="bx ${fa_class}"></i></button>
                                    </div>
                                </div>
                            </div>
                        `);

                $(`#prem_type_treaty-${classCounter}-${premCounter}`).empty();

                // console.log('SPQT' + treatytype);
                if (treatytype == 'SPQT') {
                    $(`.prem_type_treaty_div`).show();
                    $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text(
                        'SURPLUS AND QUOTA').attr('value', 'SPQT')).change();
                } else if (treatytype == 'QUOT') {
                    $(`.prem_type_treaty_div`).show();
                    $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text('QUOTA')
                        .attr(
                            'value', 'QUOT')).change();
                } else if (treatytype == 'SURP') {
                    $(`.prem_type_treaty_div`).show();
                    $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text(
                        'SURPLUS').attr(
                        'value', 'SURP')).change();
                }

                $(`#treaty_grp #prem_type_treaty-${classCounter}-${premCounter}`).trigger('change');
            }

            $("#coverfrom").change(function() {
                let start_date = moment($(this).val());
                let end_date = start_date.add(1, 'years').subtract(1, 'days');
                $("#coverto").val(end_date.format('YYYY-MM-DD'));
            });

            $("#method").change(function() {
                const MethodVal = $(`#method`).val();

                $(".burning_rate").prop('disabled', true).val('');
                $(".flat_rate").prop('disabled', true).val('');
                $(".burning_rate_div").hide();
                $(".flat_rate_div").hide();

                if (MethodVal == 'B') {
                    $(".burning_rate_div").show();
                    $(".burning_rate").prop('disabled', false);
                } else {
                    $(".flat_rate_div").show();
                    $(".flat_rate").prop('disabled', false);
                }

            });

            $("#treatytype").change(function() {
                let treatytype = $(this).val();
                $(`#prem_type_treaty-0-0`).empty();

                if (treatytype == 'SURP') {

                    $('.reinsurer_per_treaty_div').hide();
                    $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                    $('.prem_type_treaty_div').show();
                    $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS').attr('value',
                            'SURP'))
                        .change();

                    $('.no_of_lines_div').show();
                    $('.no_of_lines').prop('disabled', false).val(null);

                    $('.surp_retention_amt_div').show();
                    $('.surp_retention_amt').prop('disabled', false).val(null);

                    $('.surp_treaty_limit_div').show();
                    $('.surp_treaty_limit').prop('disabled', false).val(null);

                    $('.surp_header_div').show();

                    $('.quota_share_total_limit_div').hide();
                    $('.quota_share_total_limit').prop('disabled', true).val(null);

                    $('.retention_per_div').hide();
                    $('.retention_per').prop('disabled', true).val(null);

                    $('.treaty_reice_div').hide();
                    $('.treaty_reice').prop('disabled', true).val(null);

                    $('.quota_retention_amt_div').hide();
                    $('.quota_retention_amt').prop('disabled', true).val(null);

                    $('.quota_treaty_limit_div').hide();
                    $('.quota_treaty_limit').prop('disabled', true).val(null);

                    $('.quota_header_div').hide();

                } else if (treatytype == 'QUOT') {

                    $('.reinsurer_per_treaty_div').hide();
                    $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                    $('.prem_type_treaty_div').show();
                    $(`#prem_type_treaty-0-0`).append($('<option>').text('QUOTA').attr('value', 'QUOT'))
                        .change();

                    $('.quota_share_total_limit_div').show();
                    $('.quota_share_total_limit').prop('disabled', false).val(null);

                    $('.retention_per_div').show();
                    $('.retention_per').prop('disabled', false).val(null);

                    $('.treaty_reice_div').show();
                    $('.treaty_reice').prop('disabled', false).val(null);

                    $('.quota_retention_amt_div').show();
                    $('.quota_retention_amt').prop('disabled', false).val(null);

                    $('.quota_treaty_limit_div').show();
                    $('.quota_treaty_limit').prop('disabled', false).val(null);

                    $('.quota_header_div').show();
                    //
                    $('.no_of_lines_div').hide();
                    $('.no_of_lines').prop('disabled', true).val(null);

                    $('.surp_retention_amt_div').hide();
                    $('.surp_retention_amt').prop('disabled', true).val(null);

                    $('.surp_treaty_limit_div').hide();
                    $('.surp_treaty_limit').prop('disabled', true).val(null);

                    $('.surp_header_div').hide();
                } else if (treatytype == 'SPQT') {

                    $('.reinsurer_per_treaty_div').show();
                    $('.reinsurer_per_treaty').prop('disabled', false).val(null);

                    $('.prem_type_treaty_div').show();
                    $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS AND QUOTA').attr(
                        'value',
                        'SPQT')).change();

                    $('.quota_share_total_limit_div').show();
                    $('.quota_share_total_limit').prop('disabled', false).val(null);

                    $('.retention_per_div').show();
                    $('.retention_per').prop('disabled', false).val(null);

                    $('.quota_retention_amt_div').show();
                    $('.quota_retention_amt').prop('disabled', false).val(null);

                    $('.quota_treaty_limit_div').show();
                    $('.quota_treaty_limit').prop('disabled', false).val(null);

                    $('.treaty_reice_div').show();
                    $('.treaty_reice').prop('disabled', false).val(null);

                    $('.no_of_lines_div').show();
                    $('.no_of_lines').prop('disabled', false).val(null);

                    $('.surp_retention_amt_div').show();
                    $('.surp_retention_amt').prop('disabled', false).val(null);

                    $('.surp_treaty_limit_div').show();
                    $('.surp_treaty_limit').prop('disabled', false).val(null);

                    $('.surp_header_div').show();
                    $('.quota_header_div').show();
                } else {

                    $('.reinsurer_per_treaty_div').hide();
                    $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                    $('.prem_type_treaty_div').show();
                    $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS').attr('value',
                            'SURP'))
                        .change();

                    $('.no_of_lines_div').hide();
                    $('.no_of_lines').prop('disabled', true).val(null);

                    $('.surp_retention_amt_div').hide();
                    $('.surp_retention_amt').prop('disabled', true).val(null);

                    $('.surp_treaty_limit_div').hide();
                    $('.surp_treaty_limit').prop('disabled', true).val(null);

                    $('.surp_header_div').hide();

                    $('.quota_share_total_limit_div').hide();
                    $('.quota_share_total_limit').prop('disabled', true).val(null);

                    $('.retention_per_div').hide();
                    $('.retention_per').prop('disabled', true).val(null);

                    $('.treaty_reice_div').hide();
                    $('.treaty_reice').prop('disabled', true).val(null);

                    $('.quota_retention_amt_div').hide();
                    $('.quota_retention_amt').prop('disabled', true).val(null);

                    $('.quota_treaty_limit_div').hide();
                    $('.quota_treaty_limit').prop('disabled', true).val(null);

                    $('.quota_header_div').hide();

                }

            });

            $(document).on('keyup', ".no_of_lines", function() {
                let reinclass_counter = $(`.treaty_reinclass`).data('counter')
                var lines = $(this).val() || 0;
                var counter = $(this).data('counter');
                var ret = parseFloat(removeCommas($(`#surp_retention_amt-${counter}`).val())) || 0;
                var trt_limit = lines * ret;
                $(`#surp_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
            });

            $(document).on('keyup', ".retention_per", function() {
                var ret_per = $(this).val() || 0;
                var counter = $(this).data('counter');
                var quota_limit_total = parseFloat(removeCommas($(`#quota_share_total_limit-${counter}`)
                    .val())) || 0;
                var trt_per = 100 - ret_per;
                var ret_amt = (ret_per / 100) * quota_limit_total;
                var trt_limit = (trt_per / 100) * quota_limit_total;

                $(`#treaty_reice-${counter}`).val(trt_per);
                $(`#quota_retention_amt-${counter}`).val(numberWithCommas(ret_amt));
                $(`#quota_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
            });

            $(document).on('keyup', ".quota_share_total_limit", function() {
                var ret_per = $(`#treaty_reice-${counter}`).val() || 0;
                var counter = $(this).data('counter');
                var quota_limit_total = parseFloat(removeCommas($(`#quota_share_total_limit-${counter}`)
                    .val())) || 0;
                var trt_per = 100 - ret_per;
                var ret_amt = (ret_per / 100) * quota_limit_total;
                var trt_limit = (trt_per / 100) * quota_limit_total;

                $(`#treaty_reice-${counter}`).val(trt_per);
                $(`#quota_retention_amt-${counter}`).val(numberWithCommas(ret_amt));
                $(`#quota_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
            });

            // Adding new layer
            $('#layer-section').on('click', '#add-layer-section', function() {
                const lastLayerSection = $('#layer-section .layer-sections:last');
                const MethodVal = $('#method').val();
                const prevCounter = lastLayerSection.data('counter');
                const IndemnityTreatyLimit = $(`#indemnity_treaty_limit-${prevCounter}-0`).val();
                const UnderlyingLimit = $(`#underlying_limit-${prevCounter}-0`).val();
                const EgnpiVal = $(`#egnpi-${prevCounter}-0`).val();
                const MinBcRate = $(`#min_bc_rate-${prevCounter}-0`).val();
                const MaxBcRate = $(`#max_bc_rate-${prevCounter}-0`).val();
                const FlatRate = $(`#flat_rate-${prevCounter}-0`).val();
                const UpperAdj = $(`#upper_adj-${prevCounter}-0`).val();
                const LowerAdj = $(`#lower_adj-${prevCounter}-0`).val();
                const MinDeposit = $(`#min_deposit-${prevCounter}-0`).val();
                const limit_per_reinclass = $(`#limit_per_reinclass-${prevCounter}-0`).val();

                // Validation
                if (!IndemnityTreatyLimit.trim()) {
                    toastr.error('Please Capture Treaty Limit', 'Incomplete data');
                    return false;
                } else if (!UnderlyingLimit.trim()) {
                    toastr.error('Please Capture Deductive', 'Incomplete data');
                    return false;
                } else if (!EgnpiVal.trim()) {
                    toastr.error('Please Capture EGNPI', 'Incomplete data');
                    return false;
                } else if (!MinBcRate.trim() && MethodVal === 'B') {
                    toastr.error('Input Minimum Burning Cost Rate', 'Incomplete data');
                    return false;
                } else if (!MaxBcRate.trim() && MethodVal === 'B') {
                    toastr.error('Input Maximum Burning Cost Rate', 'Incomplete data');
                    return false;
                } else if (!FlatRate.trim() && MethodVal === 'F') {
                    toastr.error('Input Flat Rate', 'Incomplete data');
                    return false;
                } else if (!UpperAdj.trim() && MethodVal === 'B') {
                    toastr.error('Please Capture Upper Adjustment Rate', 'Incomplete data');
                    return false;
                } else if (!LowerAdj.trim() && MethodVal === 'B') {
                    toastr.error('Please Capture Lower Adjustment Rate', 'Incomplete data');
                    return false;
                } else if (!MinDeposit.trim()) {
                    toastr.error('Please Confirm Minimum Deposit Premium(MDP) Amount',
                        'Incomplete data');
                    return false;
                }

                if (limit_per_reinclass === 'Y') {
                    const IndemnityTreatyLimit = $(`#indemnity_treaty_limit-${prevCounter}-1`).val();
                    const UnderlyingLimit = $(`#underlying_limit-${prevCounter}-1`).val();
                    const EgnpiVal = $(`#egnpi-${prevCounter}-1`).val();
                    const MinBcRate = $(`#min_bc_rate-${prevCounter}-1`).val();
                    const MaxBcRate = $(`#max_bc_rate-${prevCounter}-1`).val();
                    const FlatRate = $(`#flat_rate-${prevCounter}-1`).val();
                    const UpperAdj = $(`#upper_adj-${prevCounter}-1`).val();
                    const LowerAdj = $(`#lower_adj-${prevCounter}-1`).val();
                    const MinDeposit = $(`#min_deposit-${prevCounter}-1`).val();

                    if (!IndemnityTreatyLimit.trim()) {
                        toastr.error('Please Capture Treaty Limit', 'Incomplete data');
                        return false;
                    } else if (!UnderlyingLimit.trim()) {
                        toastr.error('Please Capture Deductive', 'Incomplete data');
                        return false;
                    } else if (!EgnpiVal.trim()) {
                        toastr.error('Please Capture EGNPI', 'Incomplete data');
                        return false;
                    } else if (!MinBcRate.trim() && MethodVal === 'B') {
                        toastr.error('Input Minimum Burning Cost Rate', 'Incomplete data');
                        return false;
                    } else if (!MaxBcRate.trim() && MethodVal === 'B') {
                        toastr.error('Input Maximum Burning Cost Rate', 'Incomplete data');
                        return false;
                    } else if (!FlatRate.trim() && MethodVal === 'F') {
                        toastr.error('Input Flat Rate', 'Incomplete data');
                        return false;
                    } else if (!UpperAdj.trim() && MethodVal === 'B') {
                        toastr.error('Please Capture Upper Adjustment Rate', 'Incomplete data');
                        return false;
                    } else if (!LowerAdj.trim() && MethodVal === 'B') {
                        toastr.error('Please Capture Lower Adjustment Rate', 'Incomplete data');
                        return false;
                    } else if (!MinDeposit.trim()) {
                        toastr.error('Please Confirm Minimum Deposit Premium(MDP) Amount',
                            'Incomplete data');
                        return false;
                    }
                }

                // Increment the counter
                let counter = prevCounter + 1;
                $('#layer-section').append(`
                    <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                        <h6> Layer: ${counter+1} </h6>
                        <div class="row">
                            <!--Flag to show if layers are per class-->
                            <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                <label class="form-label required">Capture Limits per Class ?</label>
                                <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" id="limit_per_reinclass-${counter}-0" value="N" required>
                                    <option value=""> Select Option </option>
                                    <option value="N" selected> No </option>
                                    <option value="Y"> Yes </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1 nonprop_reinclass">
                            <label class="form-label required">Reinclass</label>
                            <input type="hidden" class="form-control layer_no" aria-label="layer_no" data-counter="${counter}" id="layer_no-${counter}-0" name="layer_no[]" value="${counter + 1}" readonly>
                            <input type="hidden" class="form-control nonprop_reinclass" aria-label="nonprop_reinclass" data-counter="${counter}" id="nonprop_reinclass-${counter}-0" name="nonprop_reinclass[]" value="ALL" readonly>
                            <input type="text" class="form-control nonprop_reinclass_desc" aria-label="nonprop_reinclass_desc" data-counter="${counter}" id="nonprop_reinclass_desc-${counter}-0" name="nonprop_reinclass_desc[]" value="ALL" readonly>
                        </div>
                        <!--Indemnity-->
                        <div class="col-sm-2">
                            <label class="form-label required">Limit</label>
                            <input type="text" class="form-inputs" aria-label="indemnity_limit" id="indemnity_treaty_limit-${counter}-0" name="indemnity_treaty_limit[]" onkeyup="this.value=numberWithCommas(this.value)">
                        </div>

                        <!--Underlying Limit-->
                        <div class="col-sm-2">
                            <label class="form-label required">Deductible Amount</label>
                            <input type="text" class="form-inputs" aria-label="underlying_limit" id="underlying_limit-${counter}-0" name="underlying_limit[]" onkeyup="this.value=numberWithCommas(this.value)" >
                        </div>

                        <!--EGNPI (Estimated Premium)-->
                        <div class="col-sm-2">
                            <label class="form-label required">EGNPI</label>
                            <input type="text" class="form-inputs" aria-label="egnpi" id="egnpi-${counter}-0" name="egnpi[]" onkeyup="this.value=numberWithCommas(this.value)" >
                        </div>

                        <!--For Burning Cost (B) --- Minimum Rate: (%)-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Burning Cost-Minimum Rate(%)</label>
                            <input type="text" name="min_bc_rate[]" id="min_bc_rate-${counter}-0" class="form-inputs burning_rate" value="{{ old('min_bc_rate') }}">
                        </div>

                        <!--Maximum Rate: (%)-->
                        <div class="col-sm-2 burning_rate_div">
                            <label class="form-label required">Maximum Rate: (%)</label>
                            <input type="text" name="max_bc_rate[]" id="max_bc_rate-${counter}-0" class="form-inputs burning_rate" value="{{ old('max_bc_rate') }}">
                        </div>

                        <!--For Flat Rate: (%)-->
                        <div class="col-sm-2 flat_rate_div">
                            <label class="form-label required">For Flat Rate: (%)</label>
                            <input type="text" name="flat_rate[]" id="flat_rate-${counter}-0" class="form-inputs flat_rate" value="{{ old('applied_rate') }}">
                        </div>

                        <!--Adjustable Annually Rate-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Upper Adjust. Annually Rate</label>
                            <input type="text" name="upper_adj[]" id="upper_adj-${counter}-0" class="form-inputs burning_rate" value="{{ old('upper_adj') }}">
                        </div>

                        <!--Adjustable Annually Rate-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Lower Adjust. Annually Rate</label>
                            <input type="text" name="lower_adj[]" id="lower_adj-${counter}-0" class="form-inputs burning_rate" value="{{ old('lower_adj') }}">
                        </div>

                        <!--Minimum Deposit Premium -->
                        <div class="col-sm-3">
                            <label class="form-label required">Minimum Deposit Premium </label>
                            <div class="input-group mb-3">
                                <input type="text" name="min_deposit[]" id="min_deposit-${counter}-0" class="form-control" value="{{ old('min_deposit') }}" onkeyup="this.value=numberWithCommas(this.value)">
                                <button class="btn btn-danger remove-layer-section" type="button" id="remove-layer-section"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>

                        {{-- Reinstatement Type Arrangement --}}
                        <div class="col-sm-3 reinstatement_type_div tnp_section_div">
                            <label class="form-label required"> Reinstatement Type </label>
                            <div class="input-group mb-3">
                                <select name="reinstatement_type[]" id="reinstatement_type-${counter}-0" class="form-inputs select2">
                                    <option value="NOR">Number of Reinstatement</option>
                                    <option value="AAL">Annual Aggregate Limit</option>
                                </select>
                            </div>
                        </div>
                        {{-- Reinstatement Type Value --}}
                        <div class="col-sm-3 reinstatement_value_div tnp_section_div">
                            <label class="form-label required"> Reinstatement Value </label>
                            <div class="input-group mb-3">
                                <input type="text" name="reinstatement_value[]" id="reinstatement_value-${counter}-0" class="form-control reinstatement_value tnp_section" value="" onkeyup="this.value=numberWithCommas(this.value)" required>
                            </div>
                        </div>
                    </div>
                `);

                $(".burning_rate_div").hide();
                $(".flat_rate_div").hide();

                if (MethodVal === 'B') {
                    $(".burning_rate_div").show();
                    $(".burning_rate").prop('disabled', false);
                    $(".flat_rate").prop('disabled', true).val('');
                } else {
                    $(".flat_rate_div").show();
                    $(".flat_rate").prop('disabled', false);
                    $(".burning_rate").prop('disabled', true).val('');
                }
            });

            $('#layer-section').on('click', '.remove-layer-section', function() {
                $(this).closest('.layer-sections').remove();
            });

            $('#add_rein_class').on('click', function() {
                var $lastSection = $('.reinclass-section').last();

                const prevCounter = parseInt($lastSection.attr('data-counter'))
                const reinClassVal = $(`#treaty_reinclass-${prevCounter}`).val()
                const prevSectionLabel = String.fromCharCode(65 + prevCounter)
                if (reinClassVal == null || reinClassVal == '' || reinClassVal == ' ') {
                    toastr.error(`Please Select Reinsurance Class in Section ${prevSectionLabel}`,
                        'Incomplete data')
                    return false
                }

                var $newSection = $lastSection.clone(); // Clone the last section

                // Remove select2-related elements
                $newSection.find('.select2-container').remove();

                // Increment data-counter attributes for the new section and its children
                var counter = parseInt($lastSection.attr('data-counter')) + 1;
                $newSection.attr('id', 'reinclass-section-' + counter);
                $newSection.attr('data-counter', counter);
                $newSection.find('[id]').each(function() {
                    var id = $(this).attr('id');
                    $(this).attr('id', id.replace(/-\d$/, '-' + counter));
                    $(this).attr('data-counter', counter);
                });

                let selectedReinClasses = []
                $('.treaty_reinclass').each(function() {
                    const selectedVal = $(this).find('option:selected').val()

                    if (selectedVal != '') {
                        selectedReinClasses.push(selectedVal)
                    }
                });

                $newSection.find('.treaty_reinclass').attr('data-counter', counter)
                $newSection.find('.comm-section').attr('id', `comm-section-${counter}`)

                $newSection.find('.treaty_reinclass option').each(function() {
                    const val = $(this).val()
                    if (selectedReinClasses.indexOf(val) !== -1) {
                        $(this).remove();
                    }
                })

                // remove comm section and add afresh
                $newSection.find('.comm-sections').remove()

                // Update the section label (e.g., A, B, C, etc.)
                const currentSectionLabel = String.fromCharCode(65 + counter); // A: 65
                $newSection.find('.section-title').text('Section ' + currentSectionLabel);

                // Reset input values in the new section
                $newSection.find('input[type="text"], input[type="number"]').val('');

                // Clear selected options in select elements
                $newSection.find('select').val('').select2();

                // Insert the new section after the last section
                $lastSection.after($newSection);

                appendCommSection(0, counter)
            });

            function processSections(sectionClass, sectionDivClass, action) {
                if (action == 'enable') {
                    $(sectionClass + ', ' + sectionDivClass).each(function() {
                        if ($(this).hasClass(sectionDivClass.substr(1))) {
                            $(this).show();
                        } else {
                            $(this).prop('disabled', false);
                            resetableTransTypes.includes(trans_type) ? $(this).val(null) : null;

                        }
                    });
                } else {
                    $(sectionClass + ', ' + sectionDivClass).each(function() {
                        if ($(this).hasClass(sectionDivClass.substr(1))) {
                            $(this).hide();
                        } else {
                            $(this).prop('disabled', true);
                            resetableTransTypes.includes(trans_type) ? $(this).val(null) : null;
                        }
                    });
                }

            }

            // Adding new item in a layer
            $('#layer-section').on('change', '.limit_per_reinclass', function() {
                var lastLayerSection = $('#layer-section .layer-sections:last');
                var counter = lastLayerSection.data('counter');
                var itemcounter = 0;
                var MethodVal = $('#method').val();
                var limit_per_reinclass = $(`#limit_per_reinclass-${counter}-${itemcounter}`).val();
                // Remove existing layer sections
                $('[id^="layer-section-' + counter + '"]').remove();

                // Add new layers based on the selected limit_per_reinclass value
                if (limit_per_reinclass === 'Y') {
                    // Get the select element
                    var selectElement = document.getElementById("tnp_reinclass_code");

                    $('#layer-section').append(`
                        <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                            ${ counter !== 0 ? `<h6> Layer: ${counter + 1} </h6>` : '' }
                            <div class="row">
                                <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                    <label class="form-label required">Capture Limits per Class?</label>
                                    <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" data-counter="${counter}" id="limit_per_reinclass-${counter}-${itemcounter}" required>
                                        <option value="">Select Option</option>
                                        <option value="N">No</option>
                                        <option value="Y" selected>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `);

                    // Loop through each option in the selectElement
                    for (var i = 0; i < selectElement.options.length; i++) {
                        var option = selectElement.options[i];
                        if (option.selected) {
                            var optionValue = option.value;
                            var optionText = option.text;

                            if (optionValue != null && optionValue != '') {
                                $('#layer-section').append(`
                                    <div class="row layer-sections" id="layer-section-${counter}-${itemcounter}" data-counter="${counter}">
                                        <div class="col-sm-1 nonprop_reinclass">
                                            <label class="form-label required">Reinclass</label>
                                            <input type="hidden" class="form-control layer_no" data-counter="${counter}" id="layer_no-${counter}-${itemcounter}" name="layer_no[]" value="${counter + 1}" readonly>
                                            <input type="hidden" class="form-control nonprop_reinclass" data-counter="${counter}" id="nonprop_reinclass-${counter}-${itemcounter}" name="nonprop_reinclass[]" value="${optionValue}" readonly>
                                            <input type="text" class="form-control nonprop_reinclass_desc" data-counter="${counter}" id="nonprop_reinclass_desc-${counter}-${itemcounter}" name="nonprop_reinclass_desc[]" value="${optionText}" readonly>
                                        </div>
                                        <!-- Other inputs go here -->
                                    </div>
                                `);

                                $(".burning_rate_div").hide();
                                $(".flat_rate_div").hide();

                                if (MethodVal === 'B') {
                                    $(".burning_rate_div").show();
                                    $(".burning_rate").prop('disabled', false);
                                    $(".flat_rate").prop('disabled', true).val('');
                                } else {
                                    $(".flat_rate_div").show();
                                    $(".flat_rate").prop('disabled', false);
                                    $(".burning_rate").prop('disabled', true).val('');
                                }

                                itemcounter++;
                            }
                        }
                    }
                } else {
                    $('#layer-section').append(`
                        <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                            ${ counter !== 0 ? `<h6> Layer: ${counter + 1} </h6>` : '' }
                            <div class="row">
                                <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                    <label class="form-label required">Capture Limits per Class?</label>
                                    <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" id="limit_per_reinclass-${counter}-0" value="N" required>
                                        <option value="">Select Option</option>
                                        <option value="N" selected>No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Other inputs go here -->
                        </div>
                    `);

                    $(".burning_rate_div").hide();
                    $(".flat_rate_div").hide();

                    if (MethodVal === 'B') {
                        $(".burning_rate_div").show();
                        $(".burning_rate").prop('disabled', false);
                        $(".flat_rate").prop('disabled', true).val('');
                    } else {
                        $(".flat_rate_div").show();
                        $(".flat_rate").prop('disabled', false);
                        $(".burning_rate").prop('disabled', true).val('');
                    }
                }
            });

            $('#layer-section').on('click', '.remove-layer-section', function() {
                $(this).closest('.layer-sections').remove();
            });

            $('#apply_eml').change(function(e) {
                e.preventDefault();
                $(this).valid();
                const applyEML = $(this).val()

                $('#eml_rate').hide();
                $('#eml_amt').hide();
                $('.eml-div').hide();
                if (applyEML == 'Y') {
                    $('#eml_rate').show();
                    $('#eml_amt').show();
                    $('.eml-div').show();
                }
            });

            $('#eml_rate').keyup(function(e) {
                const emlRate = $(this).val()
                const totalSumInsured = parseFloat(removeCommas($('#total_sum_insured').val()))
                const emlAmt = totalSumInsured * (emlRate / 100)

                $('#eml_amt').val(numberWithCommas(emlAmt));
                $('#effective_sum_insured').val(numberWithCommas(emlAmt));
            });

            $('#total_sum_insured').keyup(function(e) {
                const totalSumInsured = removeCommas($(this).val())
                let effectiveSumInsured = totalSumInsured

                const emlRate = $('#eml_rate').val()
                const applyEml = $('#apply_eml').val()

                if ((emlRate != null && emlRate != '' && applyEml == 'Y') && (totalSumInsured != null &&
                        totalSumInsured != '')) {
                    const emlAmt = effectiveSumInsured = parseFloat(totalSumInsured) * (parseFloat(
                        emlRate) / 100)
                    $('#eml_amt').val(numberWithCommas(emlAmt));
                }

                $('#effective_sum_insured').val(numberWithCommas(effectiveSumInsured));
            });
            $('#total_sum_insured').trigger('keyup')

            $('#brokerage_comm_type').change(function(e) {
                const brokerageCommType = $(this).val()
                $('.brokerage_comm_amt_div').hide();
                $('#brokerage_comm_amt').hide();
                $('#brokerage_comm_rate').hide();
                $('#brokerage_comm_rate_amnt').hide();
                $('#brokerage_comm_rate_label').hide();
                $('#brokerage_comm_rate_amnt_label').hide();
                $('.brokerage_comm_rate_div').hide();
                $('.brokerage_comm_rate_amnt_div').hide();

                @if ($trans_type == 'EDIT')
                    var brokerage_comm_rate =
                        '{{ number_format($old_endt_trans->brokerage_comm_rate, 4) }}';
                    var brokerage_comm_amt =
                        '{{ number_format($old_endt_trans->brokerage_comm_amt, 2) }}';
                    $('#brokerage_comm_rate').val(brokerage_comm_rate);
                    $('#brokerage_comm_amt').val(brokerage_comm_amt);
                @else
                    $('#brokerage_comm_rate').val(null);
                    $('#brokerage_comm_amt').val(null);
                @endif
                if (brokerageCommType == 'R') {
                    $('.brokerage_comm_rate_div').show();
                    $('.brokerage_comm_rate_amnt_div').show();
                    $('#brokerage_comm_rate').show();
                    $('#brokerage_comm_rate_amnt').show();
                    $('#brokerage_comm_rate_label').show();
                    $('#brokerage_comm_rate_amnt_label').show();
                    calculateBrokerageCommRate()
                } else {
                    $('.brokerage_comm_amt_div').show();
                    $('#brokerage_comm_amt').show().prop('disabled', false);
                }
            });
            $('#brokerage_comm_type').trigger('change')

            function calculateBrokerageCommRate() {
                let cedantCommRate = removeCommas($('#comm_rate').val())
                let reinCommRate = removeCommas($('#reins_comm_rate').val())
                let commAmt = parseFloat(removeCommas($('#comm_amt').val())) || 0;
                let reinCommAmnt = parseFloat(removeCommas($('#reins_comm_amt').val())) || 0;
                let brokerageCommRate = 0;
                if (cedantCommRate !== '' && cedantCommRate !== null && reinCommRate !== '' && reinCommRate !==
                    null) {
                    brokerageCommRate = Math.max(0, parseFloat(reinCommRate) - parseFloat(cedantCommRate));
                }
                let brokerageCommRateAmnt = (brokerageCommRate / 100) * reinCommAmnt
                $('#brokerage_comm_rate').val(numberWithCommas(brokerageCommRate.toFixed(2)));
                $('#brokerage_comm_rate_amnt').val(numberWithCommas(brokerageCommRateAmnt.toFixed(2)));
            }

            if (resetableTransTypes.includes(trans_type)) {
                processSections('.trt_common', '.trt_common_div', 'disable');
                processSections('.treaty_grp', '.treaty_grp_div', 'disable');
                processSections('.tnp_section', '.tnp_section_div', 'disable');
                processSections('.tpr_section', '.tpr_section_div', 'disable');
                processSections('.fac_section', '.fac_section_div', 'disable');
                processSections('.brokercode', '.brokercode_div', 'disable');

                $('.quota_share_total_limit_div').hide();
                $('.quota_share_total_limit').prop('disabled', true).val(null);

                $('.retention_per_div').hide();
                $('.retention_per').prop('disabled', true).val(null);

                $('.quota_retention_amt_div').hide();
                $('.quota_retention_amt').prop('disabled', true).val(null);

                $('.quota_treaty_limit_div').hide();
                $('.quota_treaty_limit').prop('disabled', true).val(null);

                $('.treaty_reice_div').hide();
                $('.treaty_reice').prop('disabled', true).val(null);

                $('.no_of_lines_div').hide();
                $('.no_of_lines').prop('disabled', true).val(null);

                $('.surp_retention_amt_div').hide();
                $('.surp_retention_amt').prop('disabled', true).val(null);

                $('.surp_treaty_limit_div').hide();
                $('.surp_treaty_limit').prop('disabled', true).val(null);

                $('.surp_header_div').hide();
                $('.quota_header_div').hide();
            } else {
                $('#brokerage_comm_type').trigger('change')
                $('#apply_eml').trigger('change')
                $('#reins_comm_type').trigger('change')
            }

            function computateInstalment() {
                var shareOffered = parseFloat($('#fac_share_offered').val().replace(/,/g, '')) || 0;
                var rate = parseFloat($('#comm_rate').val().replace(/,/g, '')) || 0;
                var cedantPremium = parseInt($('#cede_premium').val().replace(/,/g, '')) || 0;
                var totalDr = parseFloat((shareOffered / 100) * cedantPremium).toFixed(2);
                var totalCr = parseFloat((rate / 100) * totalDr);
                return (totalDr - totalCr).toFixed(2);
            }

            function toDecimal(number) {
                return parseFloat(Number(number).toFixed(2));
            }

            function areDecimalsEqual(num1, num2, tolerance = 0.1) {}

            function updateInstallmentTotalAmount() {
                let total = 0;
                $('input[name="installment_amt[]"]').each(function() {
                    const amount = $(this).val().replace(/,/g, '');
                    total += parseFloat(amount) || 0;
                });
                $('#installment_total_amount').val(total);
            }

            function populateReinsClass(index, classData) {
                $(`#treaty_reinclass-${index}`).val(classData.reinclass).trigger('change');

                $(`#quota_share_total_limit-${index}`).val(classData.quota_share_total_limit);
                $(`#retention_per-${index}`).val(classData.retention_per);
                $(`#treaty_reice-${index}`).val(classData.treaty_reice);

                if (classData.quota_retention_amt) {
                    $(`#quota_retention_amt-${index}`).val(classData.quota_retention_amt);
                }
                if (classData.quota_treaty_limit) {
                    $(`#quota_treaty_limit-${index}`).val(classData.quota_treaty_limit);
                }

                $(`#no_of_lines-${index}`).val(classData.no_of_lines);
                if (classData.surp_retention_amt) {
                    $(`#surp_retention_amt-${index}`).val(classData.surp_retention_amt);
                }
                if (classData.surp_treaty_limit) {
                    $(`#surp_treaty_limit-${index}`).val(classData.surp_treaty_limit);
                }

                $(`#estimated_income-${index}`).val(classData.estimated_income);
                $(`#cashloss_limit-${index}`).val(classData.cashloss_limit);

                if (classData.commission_sections && classData.commission_sections.length > 0) {
                    $(`.comm-sections[data-class-counter="${index}"]:not(:first)`).remove();

                    populateCommissionSection(index, 0, classData.commission_sections[0]);
                    for (let i = 1; i < classData.commission_sections.length; i++) {
                        $(`#add-comm-section-${index}-0`).click();
                        populateCommissionSection(index, i, classData.commission_sections[i]);
                    }
                }
            }

            function populateCommissionSection(classIndex, sectionIndex, commData) {
                $(`#prem_type_treaty-${classIndex}-${sectionIndex}`).val(commData.treaty).trigger('change');

                setTimeout(() => {
                    $(`#prem_type_code-${classIndex}-${sectionIndex}`).val(commData.premtype_code).trigger(
                        'change');
                    $(`#prem_type_comm_rate-${classIndex}-${sectionIndex}`).val(commData.comm_rate);
                }, 300);
            }

            function populateLayer(index, layerData) {
                $(`#limit_per_reinclass-${index}-0`).val(layerData.limit_per_reinclass).trigger('change');

                $(`#layer_no-${index}-0`).val(layerData.layer_no);
                $(`#nonprop_reinclass-${index}-0`).val(layerData.reinclass);
                $(`#nonprop_reinclass_desc-${index}-0`).val(layerData.reinclass_desc);
                $(`#indemnity_treaty_limit-${index}-0`).val(layerData.indemnity_limit);
                $(`#underlying_limit-${index}-0`).val(layerData.underlying_limit);
                $(`#egnpi-${index}-0`).val(layerData.egnpi);

                $(`#min_bc_rate-${index}-0`).val(layerData.min_bc_rate);
                $(`#max_bc_rate-${index}-0`).val(layerData.max_bc_rate);
                $(`#flat_rate-${index}-0`).val(layerData.flat_rate);
                $(`#upper_adj-${index}-0`).val(layerData.upper_adj);
                $(`#lower_adj-${index}-0`).val(layerData.lower_adj);
                $(`#min_deposit-${index}-0`).val(layerData.min_deposit);

                $(`#reinstatement_type-${index}-0`).val(layerData.reinstatement_type).trigger('change');
                $(`#reinstatement_value-${index}-0`).val(layerData.reinstatement_value);
            }


            if (handoverProspectId) {
                $('#prospect_id').val(handoverProspectId);
                triggerHandoverProspect(handoverProspectId)
            }

            $('#prospect_id').on('change', function() {
                const prospectId = $(this).val();
                triggerHandoverProspect(prospectId)
            });

            function triggerHandoverProspect(prospectId) {
                if (prospectId.length >= 3) {
                    $('#page-loader .loader-text').text('Fetching data');
                    $('#page-loader').fadeIn(200);

                    $.ajax({
                        url: '/cover/prospect-data/' + encodeURIComponent(prospectId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {

                            if (response.status) {
                                const d = response.data;

                                $('#type_of_bus').val(d.type_of_bus).trigger('change');
                                $('#covertype').val(d.covertype).trigger('change');
                                $('#branchcode').val(d.branchcode).trigger(
                                    'change');

                                if (d.broker_flag) {
                                    $('#broker_flag').val(d.broker_flag).trigger(
                                        'change');
                                } else {
                                    $('#broker_flag').val('N').trigger('change');
                                }

                                if (d.broker_flag === 'Y' && d
                                    .broker_code) {
                                    $('#brokercode').val(d.broker_code).trigger(
                                        'change');
                                }
                                if (d.binder_cov_no) {
                                    $('#bindercoverno').val(d.binder_cov_no)
                                        .trigger('change');
                                }

                                if (d.division) {
                                    $('#division').val(d.division).trigger(
                                        'change');
                                } else {
                                    $('#division').val('GR').trigger('change');
                                }

                                if (d.pay_method) {
                                    $('#pay_method').val(d.pay_method).trigger(
                                        'change');
                                } else {

                                    // $('#pay_method').val('GR').trigger('change');
                                }

                                $('#no_of_installments').val(d.no_of_installments);
                                $('#currency_code').val(d.currency_code).trigger('change');
                                $('#today_currency').val(d.today_currency);
                                $('#premium_payment_term').val(d.premium_payment_term).trigger(
                                    'change');

                                if (d.no_of_installments && d.no_of_installments.length > 1) {
                                    $('#fac-installments-section').empty();
                                    d.no_of_installments.forEach((installment, index) => {
                                        const idx = index + 1;
                                        const installmentRow = `
                                                        <div class="row fac-instalament-row" data-count="${idx}">
                                                            <div class="col-md-3">
                                                                <label class="">Installment</label>
                                                                <input type="hidden" name="installment_no[]" value="${idx}" readonly class="form-inputs section" />
                                                                <input type="hidden" name="installment_id[]" value="${installment.id || ''}" readonly class="form-inputs section" />
                                                                <input type="text" value="Installment No. ${idx}" id="instl_no_${idx}" readonly class="form-inputs section" required />
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="instl_date_${idx}">Installment Due Date</label>
                                                                <input type="date" name="installment_date[]" value="${installment.installment_date}" id="instl_date_${idx}" class="form-inputs section" required />
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="instl_amnt_${idx}">Total Installment Amount</label>
                                                                <div class="input-group mb-3">
                                                                    <input type="text" name="installment_amt[]" id="instl_amnt_${idx}" value="${installment.installment_amt}" class="form-inputs form-input-group amount section amount" required />
                                                                    <button class="btn btn-danger btn-sm remove-fac-instalment" type="button" id="remove-fac-instalment"><i class="bx bx-minus"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `;
                                        $('#fac-installments-section').append(
                                            installmentRow);
                                    });
                                    $('#fac_installments_box').show();

                                    updateInstallmentTotalAmount();
                                }

                                $('#class_group').val(d.class_group).trigger('change');
                                // $('#classcode').val(d.classcode).trigger(
                                //     'change');

                                if (d.class_group) {
                                    // $('#classcode').empty();
                                    // $('#classcode').val(d.classcode).trigger('change')
                                    // $('#classcode').text(d.classcode + " - " +
                                    //     'FIRE INDUSTRIAL').trigger('change')
                                    console.log(d.class_group)
                                      $('#classcode').val("101").trigger('change');

                                    // console.log(d.classcode)
                                }

                                // $('#insured_name').val(d.insured_name).trigger('change');
                                $('#fac_date_offered').val(d.fac_date_offered);

                                $('#sum_insured_type').val(d.sum_insured_type).trigger(
                                    'change');
                                $('#total_sum_insured').val(d
                                    .total_sum_insured);
                                $('#apply_eml').val(d.apply_eml).trigger('change');
                                $('#eml_rate').val(d.eml_rate);
                                $('#eml_amt').val(d.eml_amount);
                                $('#effective_sum_insured').val(d.effective_sum_insured);

                                $('#risk_details_content').html(d.risk_details);
                                $('#hidden_risk_details').val(d.risk_details);

                                $('#cede_premium').val(d.cede_premium);
                                $('#rein_premium').val(d.rein_premium);
                                $('#fac_share_offered').val(d.fac_share_offered);

                                $('#comm_rate').val(d.comm_rate).trigger('change')
                                $('#comm_amt').val(d.comm_amt);
                                $('#reins_comm_type').val(d.reins_comm_type).trigger(
                                    'change');
                                $('#reins_comm_rate').val(d.reins_comm_rate);
                                $('#reins_comm_amt').val(d
                                    .reins_comm_amt);

                                $('#brokerage_comm_type').val(d.brokerage_comm_type).trigger(
                                    'change');
                                $('#brokerage_comm_amt').val(d
                                    .brokerage_comm_amt);
                                $('#brokerage_comm_rate').val(d.brokerage_comm_rate);
                                if (d.brokerage_comm_rate_amnt) {
                                    $('#brokerage_comm_rate_amnt').val(d
                                        .brokerage_comm_rate_amnt);
                                }
                                $('#vat_charged').val(d.vat_charged || '0');

                                if (d.apply_eml === 'Y') {
                                    $('.eml-div').show();
                                } else {
                                    $('.eml-div').hide();
                                }

                                if (d.rein_comm_type === 'R') {
                                    $('.reins_comm_rate_div').show();
                                    $('.reins_comm_rate').prop('disabled', false);
                                } else if (d.rein_comm_type === 'A') {
                                    $('.reins_comm_rate_div').hide();
                                    $('.reins_comm_rate').prop('disabled', true);
                                }

                                if (d.brokerage_comm_type === 'R') {
                                    $('.brokerage_comm_rate_div, .brokerage_comm_rate_amnt_div')
                                        .show();
                                    $('.brokerage_comm_amt_div').hide();
                                } else if (d.brokerage_comm_type === 'A') {
                                    $('.brokerage_comm_rate_div, .brokerage_comm_rate_amnt_div')
                                        .hide();
                                    $('.brokerage_comm_amt_div').show();
                                }

                                $('#treatytype').val(d.treaty_code).trigger('change');
                                $('#date_offered').val(d.date_offered);
                                $('#share_offered').val(d.share_offered);
                                $('#prem_tax_rate').val(d.prem_tax_rate);
                                $('#ri_tax_rate').val(d.ri_tax_rate);
                                $('#brokerage_comm_rate').val(d.brokerage_comm_rate);
                                $('#reinsurer_per_treaty').val(d.reinsurer_per_treaty).trigger(
                                    'change');

                                $('#port_prem_rate').val(d.port_prem_rate);
                                $('#port_loss_rate').val(d.port_loss_rate);
                                $('#profit_comm_rate').val(d.profit_comm_rate);
                                $('#mgnt_exp_rate').val(d.mgnt_exp_rate);
                                $('#deficit_yrs').val(d.deficit_yrs);

                                $('#coverfrom').val(d.coverfrom);
                                $('#coverto').val(d.coverto);

                                if (d.reinsurance_classes && d.reinsurance_classes.length > 0) {
                                    $('.reinclass-section:not(:first)').remove();
                                    populateReinsClass(0, d.reinsurance_classes[0]);
                                    for (let i = 1; i < d.reinsurance_classes.length; i++) {
                                        $('#add_rein_class')
                                            .click();
                                        populateReinsClass(i, d.reinsurance_classes[i]);
                                    }
                                }

                                if (d.nonprop_treaty) {
                                    $('#tnp_reinclass_code').val(d.nonprop_treaty
                                        .reinclass_codes).trigger('change');
                                    $('#method').val(d.nonprop_treaty.method).trigger('change');

                                    if (d.nonprop_treaty.layers && d.nonprop_treaty.layers
                                        .length > 0) {
                                        $('.layer-sections:not(:first)').remove();

                                        populateLayer(0, d.nonprop_treaty.layers[0]);
                                        for (let i = 1; i < d.nonprop_treaty.layers
                                            .length; i++) {
                                            $('#add-layer-section')
                                                .click();
                                            populateLayer(i, d.nonprop_treaty.layers[i]);
                                        }
                                    }
                                }
                            } else {
                                toastr.error('No data found for this Prospect ID');
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Error fetching prospect data:', error);
                        },
                        complete: function() {
                            $('#page-loader').fadeOut(200);
                            $('#page-loader .loader-text').text('Fetching data');
                        }
                    });
                }
            }

            $('#addInsuredData').on('click', function() {
                $("#addInsuredDataModal").modal('show');
            });
        });
    </script>
@endpush
