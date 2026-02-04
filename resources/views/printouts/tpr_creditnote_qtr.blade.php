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
            padding: 8px 10px;
        }

        #cover-details,
        #reinsurer-details,
        #credit-details,
        table {
            /* border: 1px solid black; */
            border-collapse: collapse;
        }

        #cover-details td {
            font-size: 10.0pt;
            padding: 4px;
            font-family: 'Aptos Mono';
        }

        #reinsurer-details td,
        #cover-details td,
        #credit-details td,
        #breakdown-details td {
            text-align: left;
            font-size: 9.0pt;
            font-family: 'Aptos Mono';
        }

        #credit-details td {
            border: 1px solid black;
            padding-left: 15px;
        }

        .reinsurer-page {
            page-break-before: always;
        }

        .first-page {
            page-break-before: auto;
        }

        @media print {
            header {
                position: fixed;
                top: 0;
                right: 0;
                width: 100%;
                background: #fff;
                border-bottom: 1px solid #ddd;
                padding: 10px;
                text-align: right;
                height: 120px;
                z-index: 1000;
            }

            body {
                margin-top: 120px;
            }

            .reinsurer-page {
                page-break-before: always;
                break-inside: avoid;
            }
        }
    </style>

    @foreach ($reinsurers as $index => $reinsurer)
        <div class="reinsurer-page  {{ $index === 0 ? 'first-page' : '' }}"
            style="width:100%;margin-top: 100px;padding:0px; font-size: 10.0pt; font-family: 'Aptos Mono';">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <table class="reinsurer-details" border="0">
                            <tr>
                                <td>TO</td>
                            </tr>
                            <tr>
                                <td>{{ $reinsurer->partner->name }} </td>
                            </tr>
                            <tr>
                                <td>P.O BOX {{ $cover->customer->postal_address }}</td>
                            </tr>
                            <tr>
                                <td>{{ $cover->customer->city }}</td>
                            </tr>
                            <tr>
                                <td>{{ $cover->customer->telephone }}</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table
                            style="width:100%; margin-top: 0px;padding-left:300px; font-size: 10.0pt; font-family: 'Aptos Mono';">
                            <tr>
                                <td>
                                    @if ($debit->document == 'CRN')
                                        Credit Note:{{ $debit->document }}{{ $debit->dr_no }}/{{ $debit->period_year }}
                                    @else
                                        Debit Note: {{ $debit->document }}{{ $debit->dr_no }}/{{ $debit->period_year }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td> Date: {!! formatDate($debit->created_at) !!} </td>
                            </tr>
                            <tr>
                                <td> Currency: {!! $cover->currency_code !!} </td>
                            </tr>
                            <tr>
                                <td> </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <hr>
                        <b> {{ $cover->cover_title }} </b>
                        <hr>
                    </td>

                </tr>

            </table>
            <table style="width:100%;margin-top: 0px;padding:0px;">
                <tr>
                    <td valign="top">
                        <table style="width:100%;">
                            <tr>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Cover Number</td>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{ $cover->cover_no }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Cover Reference</td>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{ $cover->endorsement_no }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Sub class of Risk</td>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{ firstUpper($class_name) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Reinsured Name</td>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">
                                    {{ firstUpper($cover->customer->name) }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Insured Name</td>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">
                                    {{ firstUpper($cover->insured_name) }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Period of Cover</td>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">
                                    From:{{ formatDate($cover->cover_from) }} To:{{ formatDate($cover->cover_to) }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Payment Terms</td>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">
                                    {{ $ppw ? firstUpper($ppw->pay_term_desc) : ' ' }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Brief Description </td>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">
                                    {{ firstUpper($cover->insured_name) }}-{{ $cover->type_of_bus }}-{{ firstUpper($cover->customer->name) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Description </td>
                                <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">
                                    {{ date('Y', strtotime(formatDate($cover->cover_from))) }}-{{ firstUpper($cover->cover_title) }}-{{ firstUpper($cover->customer->name) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
            <table style="font-size: 10.0pt; font-family: 'Calibri'; width:100%; margin:0px; border:3px; padding:3px;">
                <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> PARTICULARS</th>
                <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> BASIC AMOUNT</th>
                <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> DEBIT AMOUNT</th>
                <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> CREDIT AMOUNT</th>
                @foreach ($credits->where('partner_no', $reinsurer->partner_no) as $credit)
                    @if ($credit->net_amt > 0)
                        <tr>
                            <td align="left"
                                style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px;">
                                {{ $credit->item_title ? $credit->item_title : ' ' }}
                            </td>
                            <td align="right"
                                style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px;">
                                @if (in_array($credit->dr_cr, ['CR']))
                                    {{ number_format($credit->net_amt, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td align="left"
                        style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                        TOTAL</td>
                    <td align="left"
                        style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                    </td>
                    <td align="right"
                        style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                        <span
                            style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($credits->where('partner_no', $reinsurer->partner_no)->where('dr_cr', 'DR')->sum('gross'), 2) }}</span>
                    </td>
                    <td align="right"
                        style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                        <span
                            style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($credits->where('partner_no', $reinsurer->partner_no)->where('dr_cr', 'CR')->sum('gross'), 2) }}</span>
                    </td>
                </tr>
            </table>
            <br />

            <table style="width:100%; border: 1px solid #181212; padding: 8px;">
                <tr>
                    <td align="left" colspan="2"
                        style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                        BALANCE DUE FROM YOU</td>

                    <td align="right"
                        style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                        {{ number_format(abs($credits->where('partner_no', $reinsurer->partner_no)->where('dr_cr', 'CR')->sum('gross') - $credits->where('partner_no', $reinsurer->partner_no)->where('dr_cr', 'DR')->sum('gross')), 2) }}
                    </td>
                </tr>
            </table>

            <br />
            <br />
            <br />
            <table style="width: 100%;">
                <tr>
                    <td align="left" style="font-size: 10.0pt; font-family: 'Calibri';">{{ $company->company_name }}</td>
                    <td align="left">&nbsp;
                    <td>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left">
                    </td>
                    <td align="left">&nbsp;
                    <td>
                    <td align="left"></td>
                </tr>
                <tr rowspan=5> </tr>
                <tr>
                    <td align="left">________________________</td>
                    <td align="left">&nbsp;
                    <td>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left" style="font-size: 10.0pt; font-family: 'Calibri';">Signature</td>
                    <td align="left">&nbsp;
                    <td>
                    <td align="left" style="font-size: 10.0pt; font-family: 'Calibri';">Date:{!! formatDate($debit->created_at) !!} </td>
                </tr>
            </table>
        </div>
    @endforeach
@endsection
