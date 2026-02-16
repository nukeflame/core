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

        }

        #reinsurer-details td,
        #cover-details td,
        #credit-details td,
        #breakdown-details td {
            text-align: left;
            font-size: 9.0pt;

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
                /* Adjust based on your header height */
                z-index: 1000;
                /* Ensure the header stays on top */
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
    <hr>
    <div class="reinsurer-page first-page" style="width:100%;margin-top: 100px;padding:0px; font-size: 10.0pt;">
        <table>
            <tr>
                <td>
                    <table style="width:100%;margin-top: 0px;padding:0px; font-size: 10.0pt;">
                        <tr>
                            <td> TO </td>
                        </tr>
                        <tr>
                            <td> {{ $cover->customer->name }} </td>
                        </tr>
                        <tr>
                            <td> {{ $cover->customer->postal_address }} </td>
                        </tr>
                        <tr>
                            <td> {{ $cover->customer->city }} </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="width:100%; margin-top: 0px;padding-left:300px; font-size: 10.0pt;">
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
        <table style="width:100%;margin-top: 0px;padding:0px;" border="0">
            <tr>
                <td valign="top">
                    <table style="width:100%;">
                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Cover Number</td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ $cover->cover_no }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Cover Reference</td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ $cover->endorsement_no }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Reinsured Name</td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ firstUpper($cover->customer->name) }}
                            </td>
                        </tr>

                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Class of Business</td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">
                                {{ firstUpper($treaty_type->treaty_name) }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Treaty Type</td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ firstUpper($cover->cover_title) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Underwriting Year</td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">
                                {{ date('Y', strtotime(formatDate($cover->cover_from))) }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Period of Cover</td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">
                                From:{{ formatDate($cover->cover_from) }} To:{{ formatDate($cover->cover_to) }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Payment Terms</td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">
                                {{ $ppw ? firstUpper($ppw->pay_term_desc) : ' ' }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Our share </td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">
                                {{ number_format($share_percent ?? $cover->share_offered, 2) }}%</td>
                        </tr>
                        <tr>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">Description </td>
                            <td style="font-size: 10.0pt; font-family: 'Calibri';">
                                {{ date('Y', strtotime(formatDate($cover->cover_from))) }}-{{ firstUpper($cover->cover_title) }}-{{ firstUpper($cover->customer->name) }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
            </tr>
        </table>
        <br />
        <table style="font-size: 10.0pt; font-family: 'Calibri'; width:100%; margin:0px; border:3px; padding:3px;">
            <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> PARTICULARS</th>
            <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> BASIC AMOUNT</th>
            <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> DEBIT AMOUNT</th>
            <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> CREDIT AMOUNT</th>
            @foreach ($coverpremiums as $coverpremium)
                @if ($coverpremium->final_amount > 0)
                    <tr>
                        <td align="left"
                            style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px;">
                            {{ firstUpper($coverpremium->premium_type_description) }} -
                            {{ firstUpper($coverpremium->premtype_name) }}
                            {{ $coverpremium->treaty_name ? ' - ' . firstUpper($coverpremium->treaty_name) : ' ' }}
                        </td>
                        <td align="left"
                            style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px;">
                            {{ number_format($coverpremium->basic_amount, 2) }}@if ($coverpremium->apply_rate_flag == 'Y')
                                @ {{ number_format($coverpremium->rate, 2) }}%
                            @endif
                        </td>
                        <td align="right"
                            style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px;">
                            @if (in_array($coverpremium->dr_cr, ['DR']))
                                {{ number_format($coverpremium->final_amount, 2) }}
                            @else
                                0.00
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
                    style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;  ">
                    <span
                        style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($finalTotalDR, 2) }}</span>
                </td>
                <td align="right"
                    style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold; ">
                    <span
                        style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($finalTotalCR, 2) }}</span>
                </td>
            </tr>

        </table>
        <br />
        <br />
        <br />

        <table style="width:100%; border: 1px solid #181212; padding: 8px; margin-bottom:0px;">
            <tr>
                <td align="left" colspan="2"
                    style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                    BALANCE DUE FROM YOU {{ number_format(abs($finalTotalDR - $finalTotalCR), 2) }} @
                    {{ number_format($share_percent ?? $cover->share_offered, 2) }}%</td>

                <td align="right"
                    style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                    {{ number_format($debit->net_amt, 2) }}</td>
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
@endsection
