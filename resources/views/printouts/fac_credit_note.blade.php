    @extends('printouts.base')

    @section('content')
        <style>
            #breakdown-details {
                width: 100%;
            }

            #breakdown-details tr {
                border-bottom: 1px solid #000;
            }

            #breakdown-details td {
                border-bottom: 1px solid #000;
                padding: 3pt;
                padding-left: 9pt;
                padding-right: 9pt;
            }

            .reinsurer-page {
                page-break-before: always;
                break-inside: avoid;
                page-break-inside: avoid;
                font-family: "Open Sans", sans-serif;
                font-optical-sizing: auto;
                font-weight: 400;
                font-style: normal;
            }

            .first-page {
                page-break-before: auto;
            }
        </style>
    @endsection
    @foreach ($reinsurers as $index => $reinsurer)
        @php
            $credit_partner = $credits->where('partner_no', $reinsurer->partner_no);
        @endphp
        <div class="reinsurer-page{{ $index === 0 ? 'first-page' : '' }}">
            <div
                style="width:100%;
                    margin-top: 0px; padding:0px; font-size: 9pt; font-family: 'Open Sans';">
                <table id="cover-header">
                    <tr>
                        <td>
                            <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                                <tr>
                                    <td><strong>TO</strong></td>
                                </tr>
                                <tr>
                                    <td>{{ $reinsurer->partner_name }} </td>
                                </tr>
                                <tr>
                                    <td>{{ $reinsurer->partner_postal_address }}</td>
                                </tr>
                                <tr>
                                    <td> {{ $reinsurer->partner_street }}, {{ $reinsurer->partner_city }} </td>
                                </tr>
                                <tr>
                                    <td> {{ \App\Models\Country::where('country_iso', $reinsurer->partner_country_iso)->value('country_name') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td> {{ $reinsurer->partner_telephone }} </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table id="" class="w-100 courier-10" style="margin-top: 0px;padding-left:300px;">
                                <tr>
                                    <td>
                                        <div class="info-box uppercase">
                                            <strong>Credit Note: </strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="info-box">
                                            CRN/{{ $reinsurer->tran_no }}/{{ $reinsurer->period_year }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="info-box uppercase">
                                            <strong>Date:</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="info-box text-left">
                                            {!! formatDate($reinsurer->created_at) !!}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="info-box uppercase">
                                            <strong>Currency: </strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="info-box text-left">
                                            {!! $cover->currency_code !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center">
                            <div class="hr-line-btm"></div>
                            <b> FACULTATIVE PROPORTIONAL ACCOUNT </b>
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
                                    <td class="pt-4 courier-10"><strong>Cover Reference</strong></td>
                                    <td class="pt-4 courier-10">{{ $cover->endorsement_no }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong>Business Class</strong></td>
                                    <td class="pt-4 courier-10">{{ firstUpper($class_name) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong>Reinsured Name</strong></td>
                                    <td class="pt-4 courier-10">{{ firstUpper($cover->customer->name) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong>Insured Name</strong></td>
                                    <td class="pt-4 courier-10">{{ firstUpper($cover->insured_name) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong>Period of Cover</strong></td>
                                    <td class="pt-4 courier-10">From: {{ formatDate($cover->cover_from) }} To:
                                        {{ formatDate($cover->cover_to) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong>Payment Terms</strong></td>
                                    <td class="pt-4 courier-10">{{ $ppw ? firstUpper($ppw->pay_term_desc) : ' ' }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong> Brief Description </strong></td>
                                    <td class="pt-4 courier-10 uppercase">
                                        {{ firstUpper($cover->insured_name) }}-{{ $cover->type_of_bus }}-{{ firstUpper($cover->customer->name) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong>Sum Insured (100%) </strong></td>
                                    <td class="pt-4 courier-10">{{ number_format($cover->total_sum_insured, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong>Your share S.I</strong> </td>
                                    <td class="pt-4 courier-10">{{ number_format($reinsurer->sum_insured, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong>Premiums (100%) </strong></td>
                                    <td class="pt-4 courier-10">{{ number_format($cover->rein_premium, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-10"><strong>Your share</strong></td>
                                    <td class="pt-4 courier-10">{{ number_format($reinsurer->share, 2) }}%</td>
                                </tr>
                                <tr>
                            </table>
                        </td>
                        <td></td>
                    </tr>
                </table>
                <table id="breakdown-details" style="margin-bottom: 10px; font-size: 8pt;">
                    <thead style="border: 1px solid black; padding:0px; margin:0px;">
                        <tr>
                            <th class="no-border text-left p-9-l"> PARTICULARS</th>
                            <th class="no-border text-left p-3"></th>
                            <th class="no-border p-3"> DEBIT</th>
                            <th class="no-border p-9-r"> CREDIT</th>
                        </tr>
                    </thead>

                    @foreach ($credit_partner as $credit)
                        @if ((int) $credit->gross > 0 && $credit->entry_type_descr != 'BRC')
                            <tr>
                                <td class="calibri-10 no-border text-left bottom-border">
                                    {{ firstUpper($credit->item_title) }}
                                </td>
                                <td class="calibri-10 text-left bottom-border">
                                    @if ($credit->total_gross == 0)
                                        {{ number_format($cover->cedant_premium, 2) }}
                                    @else
                                        {{ number_format($credit->total_gross, 2) }}
                                        @endif @if ($credit->rate != 0)
                                            @ {{ number_format($credit->rate, 2) }}%
                                        @endif
                                </td>
                                <td class="calibri-10 text-right bottom-border">
                                    @if (in_array($credit->dr_cr, ['DR']))
                                        {{ number_format($credit->gross, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="calibri-10 text-right bottom-border">
                                    @if (in_array($credit->dr_cr, ['CR']))
                                        {{ number_format($credit->gross, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr style="border-bottom: 1px solid #181212;">
                        <td class="calibri-10 no-border text-left p-8 bold"><strong> TOTAL</strong></td>
                        <td class="calibri-10 no-border text-left p-8 bold"><strong> </strong></td>
                        <td class="calibri-10 no-border text-right p-8 bold">
                            <span
                                style="border-bottom: 0px solid #181212; padding: 0px;">{{ number_format($credit_partner->where('dr_cr', 'DR')->where('entry_type_descr', '!=', 'BRC')->sum('gross'), 2) }}</span>
                        </td>
                        <td class="calibri-10 no-border text-right p-8 bold">
                            <span
                                style="border-bottom: 0px solid #181212; padding: 0px;">{{ number_format($credit_partner->where('dr_cr', 'CR')->where('entry_type_descr', '!=', 'BRC')->sum('gross'), 2) }}</span>
                        </td>
                    </tr>
                </table>
                <table style="width:100%; border: 1px solid #000; margin-bottom: 10px; font-size:8pt; padding:0pt;">
                    <thead style="border: 1px solid black; padding:0px; margin:0px;">
                        <tr>
                            <th class="no-border text-left p-3 p-9-l"><strong>BALANCE DUE TO YOU</strong></th>
                            <th class="no-border p-3">&nbsp;</th>
                            <th class="no-border p-3">&nbsp;</th>
                            <th class="no-border text-right p-3 p-9-r">
                                @php
                                    $crSum = $credit_partner->where('dr_cr', 'CR')->sum('gross');
                                    // $frfSum = $credit_partner->where('entry_type_descr', 'FRF')->sum('gross');
                                    $drSum = $credit_partner
                                        ->where('dr_cr', 'DR')
                                        ->where('entry_type_descr', '!=', 'BRC')
                                        // ->where('entry_type_descr', '!=', 'FRF')
                                        ->sum('gross');
                                @endphp
                                {{-- @if ((int) $frfSum > 0)
                                        <strong>{{ number_format(abs($crSum - $drSum + $frfSum), 2) }}</strong>
                                    @else --}}
                                <strong>{{ number_format(abs($crSum - $drSum), 2) }}</strong>
                                {{-- @endif --}}
                            </th>
                        </tr>
                    </thead>
                </table>
                <table style="width:100%; border: 1px solid #000; margin-bottom: 10px;">
                    @foreach ($credit_partner as $credit)
                        @if ((int) $credit->gross > 0 && $credit->entry_type_descr === 'BRC')
                            <tr>
                                <td class="no-border text-left p-3 p-9-l">
                                    {{ firstUpper($credit->item_title) }}
                                </td>
                                <td class="no-border text-right p-3 bold">
                                    @if ($credit_partner->where('entry_type_descr', 'COM')->where('rate', '0'))
                                    @else
                                        @if ($credit->total_gross == 0)
                                            {{ number_format($cover->cedant_premium, 2) }}
                                        @else
                                            {{ number_format($credit->total_gross, 2) }}
                                        @endif
                                        @if ($credit->rate != 0)
                                            @ {{ number_format($credit->rate, 2) }}%
                                        @endif
                                    @endif
                                </td>
                                <td class="no-border text-right p-3">&nbsp;</td>
                                <td class="no-border text-right p-3 p-9-r bold">
                                    @if (in_array($credit->dr_cr, ['DR', 'CR']))
                                        {{ number_format($credit->gross, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
                <table
                    style="width:100%; border-collapse: collapse; font-size: 8pt; font-family: 'Open Sans'; margin-bottom: 10px;">
                    <thead>
                        <tr style="border: 1px solid #181212;">
                            <th colspan="1" class="no-border text-left p-3 p-9-l"> PREMIUM DUE DATE</th>
                            <th class="no-border p-3">&nbsp;</th>
                            <th class="no-border text-right p-3 p-9-r"> AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($installmentAmts as $installment)
                            @if ($reinsurer->partner_no == $installment->partner_no)
                                <tr class="bottom-border">
                                    <td colspan="1" class="text-left p-3 p-9-l">
                                        {{ formatDate($installment->installment_date) }}</td>
                                    <td class="p-3">&nbsp;</td>
                                    {{ formatDate($installment->installment_date) }}</td>
                                    <td class="text-right p-3 p-9-r bold">
                                        {{ number_format(abs($installment->installment_amt), 2) }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <br />
                <table style="width: 100%; margin-bottom: 10px;">
                    <tr>
                        <td align="left" style="font-size: 10.0pt; font-family: 'Open Sans';">
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
        </div>
    @endforeach
