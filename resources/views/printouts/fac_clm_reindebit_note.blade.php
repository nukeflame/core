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
    @php
        $disableAutoFooter = true;
    @endphp
    @foreach ($reinsurers as $index => $reinsurer)
        @foreach ($rein_notes as $idx => $rein_note)
            @if ($index === $idx)
                @php
                    $credit_partner = $credits->where('partner_no', $reinsurer->partner_no);
                @endphp
                <div class="reinsurer-page{{ $index === 0 ? 'first-page' : '' }}">
                    <div style="width:100%; margin-top: 0px; padding:0px; font-size: 9pt; font-family: 'Open Sans';">
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
                                                    <strong>Debit Note: </strong>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="info-box">
                                                    DRN/{{ $debit->dr_no }}/{{ $debit->period_year }}
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
                                            <td class="pt-4 courier-9"><strong>Period of Cover</strong></td>
                                            <td class="pt-4 courier-9">From: {{ formatDate($cover->cover_from) }} To:
                                                {{ formatDate($cover->cover_to) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-4 courier-9"><strong>Our Claim Reference</strong></td>
                                            <td class="pt-4 courier-9">{{ $claim->claim_no }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-4 courier-9"><strong>Cedant Claim Reference</strong></td>
                                            <td class="pt-4 courier-9">{{ $claimNotification->cedant_claim_no }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-4 courier-9"><strong>Insured Name</strong></td>
                                            <td class="pt-4 courier-9">{{ firstUpper($cover->insured_name) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-4 courier-9"><strong>Date Reported</strong> </td>
                                            <td class="pt-4 courier-9">{{ formatDate($claim->date_notified_reinsurer) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-4 courier-9"><strong>Date of Loss</strong></td>
                                            <td class="pt-4 courier-9">{{ formatDate($claim->date_of_loss) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-4 courier-9"> <strong>Risk Description</strong></td>
                                            <td class="pt-4 courier-9">{{ $claim->loss_narration }}</td>
                                        </tr>
                                    </table>
                                </td>
                                <td></td>
                            </tr>
                        </table>
                        <table id="particular-details" style="margin-bottom: 10px; font-size: 8pt;">
                            <thead style="border: 1px solid black; padding:0px; margin:0px;">
                                <tr>
                                    <th class="no-border text-left p-9-l">PARTICULARS</th>
                                    <th class="no-border text-left p-3"></th>
                                    <th class="no-border p-3">DEBIT</th>
                                    <th class="no-border p-9-r">CREDIT</th>
                                </tr>
                            </thead>
                            <tr>
                                <td class="calibri-10 bottom-border text-left p-8">
                                    Claim Payable Amount
                                </td>
                                <td class="calibri-10 text-left p-8 bottom-border">
                                    {{ number_format($totalClaimAmount, 2) }} @
                                    {{ number_format($rein_note->share, 2) }}%
                                </td>
                                <td class="calibri-10 text-right p-8 bottom-border">
                                    {{-- @if (in_array($debit->document, ['DRN']))
                                        {{ number_format($debit->net_amt, 2) }}
                                    @else
                                        -
                                    @endif --}}
                                    @php
                                        $debitAmount = 0;
                                        $debitAmount = ($rein_note->share / 100) * $totalClaimAmount;
                                    @endphp
                                    {{ number_format($debitAmount, 2) }}
                                </td>
                                <td class="calibri-10 text-right p-8 bottom-border">
                                    {{-- @if (in_array($debit->document, ['CRN']))
                                        {{ number_format($debit->net_amt, 2) }}
                                    @else
                                        -
                                    @endif --}}
                                    -
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%; border: 1px solid #000; margin-bottom: 10px; font-size:8pt; padding:0pt;">
                            <thead style="border: 1px solid black; padding:0px; margin:0px;">
                                <th class="no-border text-left p-3 p-9-l"><strong>BALANCE DUE FROM YOU</strong></th>
                                <th class="no-border p-3">&nbsp;</th>
                                <th class="no-border p-3">&nbsp;</th>
                                <th class="no-border text-right p-3 p-9-r">{{ number_format($debitAmount, 2) }}</th>
                            </thead>
                        </table>
                        <table class="calibri-10 w-100" style="margin: 0px; border-collapse: collapse; font-size: 9pt;">
                            <thead>
                                <tr>
                                    <td colspan="2" class="text-center uppercase">
                                        <div class="hr-line-btm"></div>
                                        <span class="courier-8 bold"> Claims Amount Breakdown</span>
                                        <div class="hr-line-btm" style="margin-bottom: 7pt;"></div>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                        <table id="particular-details" style="margin-bottom: 10px; font-size: 8pt;">
                            <thead style="border: 1px solid black; padding:0px; margin:0px;">
                                <tr>
                                    <th class="no-border text-left p-9-l uppercase"> Claim Particulars</th>
                                    <th class="no-border text-left p-3"></th>
                                    <th class="no-border p-3"> DEBIT</th>
                                    <th class="no-border p-9-r"> CREDIT</th>
                                </tr>
                            </thead>
                            @foreach ($claimperils as $claimperil)
                                @if ($claimperil->basic_amount > 0)
                                    <tr>
                                        <td class="calibri-10 bottom-border text-left p-8">
                                            {{ firstUpper($claimperil->peril_name) }}
                                        </td>
                                        <td class="calibri-10 text-left p-8 bottom-border"></td>
                                        <td class="calibri-10  p-8 bottom-border">
                                            @if (in_array($claimperil->dr_cr, ['DR']))
                                                {{ number_format($claimperil->basic_amount, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="calibri-10 text-right p-8 pr-0 bottom-border">
                                            @if (in_array($claimperil->dr_cr, ['CR']))
                                                {{ number_format($claimperil->basic_amount, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr style="border-bottom: 1px solid #181212;">
                                <td class="calibri-10 no-border text-left p-8 bold uppercase"> Total</td>
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
                            <tr style="border-bottom: 1px solid #181212;">
                                <td class="calibri-10 no-border text-left p-8 bold uppercase"> Net Claim Amount</td>
                                <td class="calibri-10 no-border p-8 bold">&nbsp;</td>
                                <td class="calibri-10 no-border p-8 bold">&nbsp;</td>
                                <td class="calibri-10 no-border text-right p-8 bold">
                                    <span
                                        style=" border-bottom: 0px solid #181212; padding: 0px;">{{ number_format($totalClaimAmount, 2) }}</span>
                                </td>
                            </tr>
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
                                    @stampImageOrEmpty('app/private/stamp.png')
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
                    <div class="footer-wrapper">
                        <div class="footer">
                            <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                                    class="page-number"></span></span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach
@endsection
