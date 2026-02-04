@extends('printouts.base')

@section('content')
    <style>
        .debit-reinsurer-page,
        .row-header {
            font-family: 'Aptos', sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            display: block;
        }
    </style>
    @php
        $sortedSchedules = collect($schedules)->sort(function ($a, $b) {
            $aPosition =
                $a['schedule_header']['position'] !== '0'
                    ? (int) $a['schedule_header']['position']
                    : (int) $a['schedule_position'];
            $bPosition =
                $b['schedule_header']['position'] !== '0'
                    ? (int) $b['schedule_header']['position']
                    : (int) $b['schedule_position'];

            return $aPosition <=> $bPosition;
        });
    @endphp
    @if (!$has_partner)
        <div style="width:100%; margin-top: 0px; padding:0px; font-size: 10pt; font-family: 'Aptos';"
            class="debit-reinsurer-pdage">
            <table id="slip-header">
                <tr>
                    <td tyle="width: 40%;">
                        <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                            <tr>
                                <td class="courier-10 bold"> TO </td>
                            </tr>
                            <tr>
                                <td
                                    style="font-size: 10.0pt; word-wrap: break-word; overflow-wrap: break-word; max-width: 200px;">
                                    {{ strtoupper($cover->partner_name) }} </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt;">
                                    P.O BOX
                                    {{ preg_replace('/P(\.?O\.?\s?BOX)/i', '', $cover->partner_postal_address) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt;"> {{ $cover->partner_city }} </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt;"> {{ $cover->partner_street }}, {{ $cover->partner_city }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt;">
                                    {{ \App\Models\Country::where('country_iso', $cover->partner_country_iso ?? '')->value('country_name') ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt;"> {{ $cover->partner_telephone }} </td>
                            </tr>
                        </table>
                    </td>
                    <td tyle="width: 60%;">
                        <table style="width:100%; margin-top: 0px;padding-left:300px; font-size: 10.0pt;">
                            <tr>
                                <td style="font-size: 10.0pt;"> Date: {!! formatDate($cover->created_at) !!} </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt;"> Currency: {!! $cover->currency_code !!} </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt;"> </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr></tr>
                <tr>
                    <td colspan="2" class="text-center">
                        <div class="hr-line-btm uppercase"></div>
                        <b> {{ $cover->insured_name }} - {{ $cover->type_of_bus }} - {{ $cover->partner_name }}
                        </b>
                        <div class="hr-line-btm"></div>
                    </td>
                </tr>
            </table>
            <table style="width: 100%; margin-top: 15px; padding:0px;" id="slip-details">
                <tr>
                    <td valign="top" style="width: 100%;">
                        <table style="width:100%;">
                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Our Reference</td>
                                <td style="width: 70%;" class="courier-10 s-r">{{ $cover->cover_no }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Business Class</td>
                                <td style="width: 70%;" class="courier-10 s-r">{{ firstUpper($class_name) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Reinsured Name</td>
                                <td style="width: 70%;" class="courier-10 s-r">{{ firstUpper($cover->partner_name) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Original Insured</td>
                                <td style="width: 70%;" class="courier-10 s-r">{{ firstUpper($cover->insured_name) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Period of Cover</td>
                                <td style="width: 70%;" class="courier-10 s-r">From:
                                    {{ formatDate($cover->cover_from) }} To: {{ formatDate($cover->cover_to) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Payment Terms</td>
                                <td style="width: 70%;" class="courier-10 s-r">
                                    {{ $ppw ? firstUpper($ppw->pay_term_desc) : ' ' }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Sum Insured (100%) </td>
                                <td style="width: 70%;" class="courier-10 s-r">
                                    {{ number_format($cover->total_sum_insured, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Our share S.I
                                    ({{ number_format($cover->share_offered, 2) }}%)</td>
                                <td style="width: 70%;" class="courier-10 s-r">
                                    {{ number_format(($cover->share_offered / 100) * $cover->total_sum_insured, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Premiums (100%) </td>
                                <td style="width: 70%;" class="courier-10 s-r">
                                    {{ number_format($cover->cedant_premium, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Our share Premium
                                    ({{ number_format($cover->share_offered, 2) }}%)</td>
                                <td style="width: 70%;" class="courier-10 s-r">
                                    {{ number_format(($cover->share_offered / 100) * $cover->cedant_premium, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>

                            @if ((int) $cover->rein_comm_rate > 0)
                                <tr>
                                    <td style="width: 30%;"" class="courier-10 s-l bold">Reinsured Commission </td>
                                    <td style="width: 70%;" class="courier-10 s-r">
                                        {{ number_format($cover->rein_comm_rate, 2) }}&#37;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="height: 7px;"></td>
                                </tr>
                            @endif

                            <tr>
                                <td style="width: 30%;"" class="courier-10 s-l bold">Risk Details </td>
                                <td style="width: 70%;" class="courier-10 s-r brief-description">
                                    {!! $cover->risk_details !!}</td>
                            </tr>

                            <tr>
                                <td colspan="2" style="height: 7px;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            {{-- <div class="footer">
                <span>&copy; {{ date('Y') }} Zamara Group. All rights reserved. | Page No: <span
                        class="page-number"></span></span>
            </div> --}}
            <div style="width: 100%; margin-top: 5px; padding: 0; overflow: visible; page-break-before: auto; margin-bottom: 15px;"
                id="schedule-wrapper">
                @foreach ($sortedSchedules as $schedule)
                    <div style="width: 100%; margin-bottom: 7px;" class="clearfix">
                        <div style="width: 40%; padding-right: 10px; float: left; display: flex; align-items: center;"
                            class="courier-10 s-l bold">
                            {{ $schedule['title'] }}
                        </div>
                        <div style="width: 60%; float: right;" class="courier-10 s-r schedule-details">
                            @if (is_array($schedule['details']))
                                {{ implode(', ', $schedule['details']) }}
                            @else
                                {!! $schedule['details'] !!}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- <div
                style="width: 100%; margin-top: 5px; padding: 0; overflow: visible; page-break-before: auto; margin-bottom: 10px;">
                @foreach ($clauses as $clause)
                    <div>
                        <div style="width: 100%; margin-bottom: 7px;" id="clause-description" class="clearfix">
                            {!! $clause->clause_wording !!}
                        </div>
                    </div>
                @endforeach
            </div> --}}
            <div style="page-break-before: always; margin-top:0px;"></div>
            <table style="width: 100%; margin-top: 0mm; margin-bottom: 7mm;">
                <tr>
                    <td colspan="2" class="uppercase text-center"><strong><u>Reinsurers Participation</u></strong>
                    </td>
                </tr>
            </table>
            <table id="insurance-table"
                style="width: 100%; border-collapse: collapse; font-family: inherit; margin-bottom:8mm; margin-top:1mm;">
                <thead>
                    <tr>
                        <th
                            style="border-bottom:1px solid #000; width: 50%; padding: 8px; font-weight: bold; text-align: left;">
                            Reinsurer</th>
                        <th
                            style="border-bottom:1px solid #000; width: 25%; padding: 8px; font-weight: bold; text-align: right;">
                            Accepted Shares</th>
                        <th
                            style="border-bottom:1px solid #000; width: 25%; padding: 8px; font-weight: bold; text-align: right;">
                            Sum Insured</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($reinsurers)
                        @foreach ($reinsurers as $reinsurer)
                            @php
                                $reinsure_share = ($reinsurer->share / 100) * $cover->total_sum_insured;
                            @endphp
                            <tr>
                                <td style="padding: 3px 8px; text-align: left;">
                                    {{ firstUpper($reinsurer->partner->name) }}</td>
                                <td style="padding: 3px 8px; text-align: right;">
                                    {{ number_format($reinsurer->share, 2) }}&#37;</td>
                                <td style="padding: 3px 8px; text-align: right;">
                                    {{ number_format($reinsurer->sum_insured, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td style="padding: 6px 8px; font-weight: bold;"></td>
                        <td
                            style="border-bottom:1px solid #000; border-top:1px solid #000; padding: 6px 8px; text-align: right; font-weight: bold;">
                            {{ number_format($cover->share_offered, 2) }}&#37;
                        </td>
                        <td
                            style="border-bottom:1px solid #000; border-top:1px solid #000; padding: 6px 8px; text-align: right; font-weight: bold;">
                            {{ number_format($cover->total_sum_insured, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td class="uppercase">Please sign a copy of this reinsurance slip and return to
                        {{ strtoupper($company->company_name) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 10px;"></td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 10.5pt;">Sincerely</td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 10px;"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-uppercase">
                        <b> {{ strtoupper($company->company_name) }} </b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 130px;"></td>
                </tr>
                <tr>
                    <td> __________________________________</td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 15px;"></td>
                </tr>
                @if ($approver)
                    <tr>
                        <td colspan="2" class="uppercase"><strong>{{ $approver?->name }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="uppercase"><strong><u>{{ $position?->name }}</u></strong></td>
                    </tr>
                @else
                    <tr>
                        <td colspan="2" class="uppercase"><strong>------------------</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="uppercase"><strong><u>------------------</u></strong></td>
                    </tr>
                @endif
            </table>
            <div style="page-break-before: always; margin-top:0px;"></div>
            <table style="width: 100%; margin-top: 5mm;">
                <tr>
                    <td colspan="2" class="uppercase text-center"><strong><u>Reinsured Signing Page</u></strong>
                    </td>
                </tr>
            </table>
            <table style="width: 100%; margin-top: 12mm;">
                <tr>
                    <td style="font-weight: 600; font-size: 12pt;">Attaching to and forming part of this Reinsurance
                        Agreement for Professional Indemnity</td>
                </tr>
            </table>
            <table style="width: 100%; margin-top: 12mm;">
                <tr>
                    <td style="font-weight: 600; margin-top: 12mm;">{{ strtoupper($cover->partner_name) }}
                    </td>
                </tr>
            </table>
            <table style="width: 100%; margin-top: 12mm;">
                <tr>
                    <td style="font-size: 10.5pt">
                        Signed at ______________________ on this ___________________ day of ____________ 20 _____
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 10px margin-top: 12mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; font-size: 10.5pt">For and on behalf of the reinsured
                        {{ ucwords(strtolower($cover->partner_name)) }}
                    </td>
                </tr>
            </table>
        </div>
    @endif

    @if (!$is_cover_note)
        @if (count($reinsurers) > 0)
            @if (!$has_partner)
                <div style="page-break-before: always;"></div>
            @endif
            @foreach ($reinsurers as $index => $reinsurer)
                @php
                    $reinsurerCompany = $reinsurer->partner;
                @endphp
                <div class="reinsurer-page{{ $index === 0 ? 'first-page' : '' }}">
                    <div style="width:100%; margin-top: 0px; padding:0px; font-size: 10pt; font-family: 'Aptos';"
                        class="debit-reinsurer-page">
                        <table id="slip-header">
                            <tr>
                                <td style="width: 40%;">
                                    <table style="width: 100%;" class="w-100 courier-10 p-0 m-0 reinsurer-details">
                                        <tr>
                                            <td class="courier-10 bold"> TO </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="font-size: 10.0pt; word-wrap: break-word; overflow-wrap: break-word; max-width: 200px;">
                                                {{ strtoupper($reinsurerCompany->name) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10.0pt;">
                                                P.O BOX
                                                {{ preg_replace('/P(\.?O\.?\s?BOX)/i', '', $reinsurerCompany->postal_address) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10.0pt;"> {{ $reinsurerCompany->city }} </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10.0pt;"> {{ $reinsurerCompany->street }},
                                                {{ $reinsurerCompany->city }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10.0pt;">
                                                {{ \App\Models\Country::where('country_iso', $reinsurerCompany->country_iso)->value('country_name') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10.0pt;"> {{ $reinsurerCompany->telephone }} </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 60%;">
                                    <table style="width:100%; margin-top: 0px;padding-left:300px; font-size: 10.0pt;">
                                        <tr>
                                            <td style="font-size: 10.0pt;"> Date: {!! formatDate($reinsurerCompany->created_at) !!} </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10.0pt;"> Currency: {!! $cover->currency_code !!} </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10.0pt;"> </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr></tr>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <div class="hr-line-btm uppercase"></div>
                                    <b> {{ $cover->insured_name }} - {{ $cover->type_of_bus }} -
                                        {{ $cover->partner_name }}
                                    </b>
                                    <div class="hr-line-btm"></div>
                                </td>
                            </tr>
                        </table>
                        <table style="width: 100%; margin-top: 5px; padding:0px;" id="slip-details">
                            <tr>
                                <td valign="top">
                                    <table style="width:100%;">
                                        <tr rows="3">
                                            <td style="width: 30%;" class="courier-10 s-l bold">Our Reference</td>
                                            <td style="width: 70%;" class="courier-10 s-r">{{ $cover->cover_no }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr rowspan="2">
                                            <td style="width: 30%;" class="courier-10 s-l bold">Business Class</td>
                                            <td style="width: 70%;" class="courier-10 s-r">{{ firstUpper($class_name) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Reinsured Name</td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                @if ($has_partner)
                                                    {{ firstUpper($cover->customer->name) }}
                                                @else
                                                    {{ firstUpper($cover->partner_name) }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Original Insured</td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ firstUpper($cover->insured_name) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Period of Cover</td>
                                            <td style="width: 70%;" class="courier-10 s-r">From:
                                                {{ formatDate($cover->cover_from) }} To:
                                                {{ formatDate($cover->cover_to) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Payment Terms</td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ $ppw ? firstUpper($ppw->pay_term_desc) : ' ' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Sum Insured (100%) </td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ number_format($cover->total_sum_insured, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Our share S.I
                                                ({{ number_format($cover->share_offered, 2) }}%)
                                            </td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ number_format(($cover->share_offered / 100) * $cover->total_sum_insured, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Premiums (100%) </td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ number_format($cover->cedant_premium, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Our share Premium
                                                ({{ number_format($cover->share_offered, 2) }}%)</td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ number_format(($cover->share_offered / 100) * $cover->cedant_premium, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        @if ((int) $reinsurer->comm_rate > 0)
                                            <tr>
                                                <td style="width: 30%;" class="courier-10 s-l bold">Total Deductions</td>
                                                <td style="width: 70%;" class="courier-10 s-r">
                                                    {{ number_format($reinsurer->comm_rate, 2) }}&#37;
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Brief Description </td>
                                            <td class="courier-10 s-r brief-description">
                                                {!! $cover->risk_details !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Accepting Company </td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ firstUpper($reinsurer->name) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Accepting Share </td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ number_format($reinsurer->share, 2) }}&#37;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Accepting Sum Insured:
                                                (100%)
                                            </td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ number_format(abs($reinsurer->sum_insured), 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 30%;" class="courier-10 s-l bold">Accepting Premium (100%)
                                            </td>
                                            <td style="width: 70%;" class="courier-10 s-r">
                                                {{ number_format($reinsurer->premium, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="height: 7px;"></td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                </td>
                            </tr>
                        </table>
                        {{-- <div class="footer">
                            <span>&copy; {{ date('Y') }} Zamara Group. All rights reserved. | Page No: <span
                                    class="page-number"></span></span>
                        </div> --}}
                        <div style="width: 100%; margin-top: 5px; padding: 0; overflow: visible; page-break-before: auto; margin-bottom: 15px;"
                            id="schedule-wrapper">
                            @foreach ($sortedSchedules as $schedule)
                                <div style="width: 100%; margin-bottom: 7px;" class="clearfix">
                                    <div style="width: 40%; padding-right: 10px; float: left; display: flex; align-items: center;"
                                        class="courier-10 s-l bold">
                                        {{ $schedule['title'] }}
                                    </div>
                                    <div style="width: 60%; float: right;" class="courier-10 s-r schedule-details">
                                        @if (is_array($schedule['details']))
                                            {{ implode(', ', $schedule['details']) }}
                                        @else
                                            {!! $schedule['details'] !!}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div
                            style="width: 100%; margin-top: 5px; padding: 0; overflow: visible; page-break-before: auto; margin-bottom: 10px;">
                            @foreach ($clauses as $clause)
                                <div style="width: 100%; margin-bottom: 7px;" id="clause-description" class="clearfix">
                                    {!! $clause->clause_wording !!}
                                </div>
                            @endforeach
                        </div>
                        @if (!$loop->last)
                            <div style="page-break-before: always;margin-top:0px;"></div>
                        @endif
                        <table style="width: 100%; margin-top: 12mm;">
                            <tr>
                                <td class="uppercase">Please sign a copy of this reinsurance slip and return to
                                    {{ strtoupper($company->company_name) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 10px;"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 10.5pt;">Sincerely</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 10px;"></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-uppercase">
                                    <b> {{ strtoupper($company->company_name) }} </b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 130px;"></td>
                            </tr>
                            <tr>
                                <td> __________________________________</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 15px;"></td>
                            </tr>
                            @if ($approver)
                                <tr>
                                    <td colspan="2" class="uppercase"><strong>{{ $approver?->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="uppercase"><strong><u>{{ $position?->name }}</u></strong>
                                    </td>
                            </tr @else <tr>
                                <td colspan="2" class="uppercase"><strong>------------------</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="uppercase"><strong><u>------------------</u></strong></td>
                                </tr>
                            @endif
                        </table>
                        @if (!$loop->last)
                            <div style="page-break-before: always;margin-top:0px;"></div>
                        @endif
                        <table style="width: 100%;margin-top: 12mm;">
                            <tr>
                                <td colspan="2" class="uppercase text-center"><strong><u>Reinsured Signing
                                            Page</u></strong>
                                </td>
                            </tr>
                        </table>
                        <table style="width: 100%; margin-top: 12mm;">
                            <tr>
                                <td style="font-weight: 600; font-size: 12pt;">Attaching to and forming part of this
                                    Facultative
                                    Reinsurance Agreement for Professional Indemnity</td>
                            </tr>
                        </table>
                        <table style="width: 100%; margin-top: 12mm;">
                            <tr>
                                <td class="s-l bold">In the name of:</td>
                                <td class="s-r">
                                    {{ strtoupper($cover->partner_name) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 15px;"></td>
                            </tr>
                            <tr>
                                <td class="s-l bold">Reinsurer:</td>
                                <td class="s-r">
                                    {{ strtoupper($reinsurer->name) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 15px;"></td>
                            </tr>
                            <tr>
                                <td class="s-l bold">Participation:</td>
                                <td class="s-r">
                                    {{ number_format($reinsurer->share, 2) }}% -
                                    {{ number_format($reinsurer->sum_insured, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 15px;"></td>
                            </tr>
                            @if ((int) $reinsurer->comm_rate > 0)
                                <tr>
                                    <td class="s-l bold">Total Deductions:</td>
                                    <td class="s-r">
                                        {{ number_format($reinsurer->comm_rate, 2) }}%
                                    </td>
                                </tr>
                            @endif
                        </table>
                        <table style="width: 100%; margin-top: 12mm;">
                            <tr>
                                <td style="font-size: 10.5pt">
                                    Signed at ______________________ on this ___________________ day of ____________ 20
                                    _____
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 10px margin-top: 12mm;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600; font-size: 10.5pt">For and on behalf of the reinsured
                                    {{ ucwords(strtolower($cover->partner_name)) }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    @if (!$loop->last)
                        <div style="page-break-before: always;"></div>
                    @endif
                </div>
            @endforeach
        @endif
    @endif
@endsection
