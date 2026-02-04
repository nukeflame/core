@extends('printouts.base')

@section('content')
    <style>
        .debit-reinsurer-page {
            font-family: 'Aptos', sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }
    </style>
    <div style="width:100%; margin-top: 0px; padding:0px; font-size: 9pt; font-family: 'Aptos';"
        class="debit-reinsurer-page">
        <table id="cover-header">
            <tr>
                <td>
                    <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                        <tr>
                            <td> <strong>TO </strong></td>
                        </tr>
                        <tr>
                            <td> {{ $cover->customer->name }} </td>
                        </tr>
                        <tr>
                            <td> {{ $cover->customer->postal_address }} </td>
                        </tr>
                        <tr>
                            <td> {{ $cover->customer->street }} </td>
                        </tr>
                        <tr>
                            <td>{{ $cover->customer->city }},
                                {{ \App\Models\Country::where('country_iso', $cover->customer->country_iso)->value('country_name') }}
                            </td>
                        </tr>
                        <tr>
                            <td> {{ $cover->customer->telephone }} </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="w-100 courier-10" style="margin-top: 0px;padding-left:300px;">
                        <tr>
                            <td class="">
                                @if ($debit)
                                    <div class="info-box uppercase">
                                        <strong>Debit Note:</strong>
                                    </div>
                                    <div class="info-box text-left">
                                        {{ $debit->document }}/{{ $debit->dr_no }}/{{ $debit->period_year }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-box uppercase">
                                    <strong>Date:</strong>
                                </div>
                                <div class="info-box text-left">
                                    {!! formatDate($debit->created_at) !!}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-box uppercase">
                                    <strong>Currency:</strong>
                                </div>
                                <div class="info-box text-left">
                                    {!! $cover->currency_code !!}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">
                    <div class="hr-line-btm"></div>
                    <b> {{ $cover->cover_title }} </b>
                    <div class="hr-line-btm"></div>
                </td>
            </tr>
        </table>
        <table id="cover-details">
            <tr>
                <td valign="top">
                    <table class="w-100">
                        <tr>
                            <td class="courier-10"><strong> Cover Number</strong></td>
                            <td class="courier-10">{{ $cover->cover_no }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Cover Reference</strong></td>
                            <td class="pt-4 courier-9">{{ $cover->endorsement_no }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Business Class</strong></td>
                            <td class="pt-4 courier-9">{{ firstUpper($class_name) }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Reinsured Name</strong></td>
                            <td class="pt-4 courier-9">{{ firstUpper($cover->customer->name) }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Insured Name</strong></td>
                            <td class="pt-4 courier-9">{{ firstUpper($cover->insured_name) }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Period of Cover</strong></td>
                            <td class="pt-4 courier-9">From: {{ formatDate($cover->cover_from) }} To:
                                {{ formatDate($cover->cover_to) }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Payment Terms</strong></td>
                            <td class="pt-4 courier-9">
                                {{ str_contains($cover->endorsement_no, 'EXT') ? $cover->premium_payment_days . '3 Days Premium Payment Warranty' : firstUpper($ppw?->pay_term_desc) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong> Brief Description </strong></td>
                            <td class="pt-4 courier-9 uppercase">
                                {{ firstUpper($cover->insured_name) }}-{{ $cover->type_of_bus }}-{{ firstUpper($cover->customer->name) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Sum Insured (100%)</strong> </td>
                            <td class="pt-4 courier-9">{{ number_format($cover->total_sum_insured, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong> Our share
                                    S.I({{ number_format($cover->share_offered, 2) }}%)
                                </strong>
                            </td>
                            <td class="pt-4 courier-9">
                                {{ number_format(($cover->share_offered / 100) * $cover->total_sum_insured, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"> <strong> Premiums (100%) </strong></td>
                            <td class="pt-4 courier-9">{{ number_format($cover->cedant_premium, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong> Our share Premium
                                    ({{ number_format($cover->share_offered, 2) }}%)</strong>
                            </td>
                            <td class="pt-4 courier-9">
                                {{ number_format(($cover->share_offered / 100) * $cover->cedant_premium, 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>
        </table>
        <table id="particular-details" style="margin-bottom: 10px; font-size: 8pt;">
            <thead style="border: 1px solid black; padding:0px; margin:0px;">
                <tr>
                    <th class="no-border text-left p-9-l"> PARTICULARS</th>
                    <th class="no-border text-left p-3"></th>
                    <th class="no-border p-3"> DEBIT</th>
                    <th class="no-border p-9-r"> CREDIT</th>
                </tr>
            </thead>
            @foreach ($coverpremiums as $coverpremium)
                @if ($coverpremium->final_amount > 0)
                    <tr>
                        <td class="calibri-10 no-border text-left bottom-border">
                            {{ firstUpper($coverpremium->premium_type_description) }}
                        </td>
                        <td class="calibri-10 text-left bottom-border">
                            {{ number_format($coverpremium->basic_amount, 2) }}
                            @if ($coverpremium->apply_rate_flag == 'Y')
                                @ {{ number_format($coverpremium->rate, 2) }}%
                            @endif
                        </td>
                        <td class="calibri-10 text-right bottom-border">
                            @if (in_array($coverpremium->dr_cr, ['DR']))
                                {{ number_format($coverpremium->final_amount, 2) }}
                            @else
                                0.00
                            @endif
                        </td>
                        <td class="calibri-10 text-right bottom-border">
                            @if (in_array($coverpremium->dr_cr, ['CR']))
                                {{ number_format($coverpremium->final_amount, 2) }}
                            @else
                                0.00
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr style="border-bottom: 1px solid #181212;">
                <td class="calibri-10 no-border text-left p-8 bold"> TOTAL</td>
                <td class="calibri-10 no-border text-left p-8 bold">&nbsp;</td>
                <td class="calibri-10 no-border text-right p-8 bold">
                    <span
                        style=" border-bottom: 0px solid #181212; padding: 0px;">{{ number_format($finalTotalDR, 2) }}</span>
                </td>
                <td class="calibri-10 no-border text-right p-8 bold">
                    <span
                        style=" border-bottom: 0px solid #181212; padding: 0px;">{{ number_format($finalTotalCR, 2) }}</span>
                </td>
            </tr>
        </table>
        <table style="width:100%; border: 1px solid #000; margin-bottom: 10px; font-size:8pt; padding:0pt;">
            <thead style="border: 1px solid black; padding:0px; margin:0px;">
                <th class="no-border text-left p-3 p-9-l"><strong>BALANCE DUE FROM YOU</strong></th>
                <th class="no-border p-3">&nbsp;</th>
                <th class="no-border p-3">&nbsp;</th>
                <th class="no-border text-right p-3 p-9-r">{{ number_format($debit->net_amt, 2) }}</th>
            </thead>
        </table>
        <table
            style="width:100%; border-collapse: collapse; font-size: 8pt; font-family: 'Aptos'; margin-bottom: 10px;">
            <thead>
                <tr style="border: 1px solid #181212;">
                    <th colspan="1" class="no-border text-left p-3 p-9-l"> PREMIUM DUE DATE</th>
                    <th class="no-border p-3">&nbsp;</th>
                    <th class="no-border text-right p-3 p-9-r"> AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($installmentAmts as $installment)
                    <tr class="bottom-border">
                        <td colspan="1" class="text-left p-3 p-9-l">{{ formatDate($installment->installment_date) }}
                        </td>
                        <td class="p-3">&nbsp;</td>
                        <td class="text-right p-3 p-9-r bold">
                            {{ number_format($installment->installment_amt, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br />
        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td align="left" style="font-size: 10.0pt; font-family: 'Aptos';">
                    {{ $company->company_name }}</td>
                <td align="left">&nbsp;</td>
                <td align="left"></td>
            </tr>
            <tr>
                <td align="left">
                    {{-- @stampImageOrEmpty('app/private/stamp.png') --}}
                </td>
                <td align="left">&nbsp;
                <td>
                <td align="left"></td>
            </tr>
            <tr rowspan=5></tr>
        </table>
        <table style="width: 100%;">
            <tr>
                <td align="left">________________________</td>
                <td align="left">&nbsp;
                <td>
                <td align="left"></td>
            </tr>
            <tr>
                <td class="text-left courier-10">Signature</td>
                <td class="text-left">&nbsp;
                <td>
                <td class="text-right courier-10">Date: {!! formatDate($debit->created_at) !!} </td>
            </tr>
        </table>
    </div>
@endsection
