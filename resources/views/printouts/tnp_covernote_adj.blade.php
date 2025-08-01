@extends('printouts.base')

@section('content')
    <hr>
    <p style="text-align:left; font-size: 9.0pt; font-family: 'Courier New'; "> TO </p>
    <p style="text-align:right; font-size: 10.0pt; font-family: 'Courier New'; ">
        @if ($debit->document == 'CRN')
            Credit Note{{ $debit->document }}{{ $debit->dr_no }}/{{ $debit->period_year }}
        @else
            Debit Note {{ $debit->document }}{{ $debit->dr_no }}/{{ $debit->period_year }}
        @endif
    </p>

    <p style="text-align:left; font-size: 9.0pt; font-family: 'Courier New'; ">{{ $cover->customer->name }} </p>
    <p style="text-align:right; font-size: 9.0pt; font-family: 'Courier New';"> Date: {!! formatDate($debit->created_at) !!} </p>

    <p style="font-size: 9.0pt; font-family: 'Courier New'; ">{{ $cover->customer->postal_address }} </p>
    <p style="text-align:right;font-size: 9.0pt; font-family: 'Courier New'; "> Currency: {!! $cover->currency_code !!} </p>

    <p style="font-size: 9.0pt; font-family: 'Courier New'; ">{{ $cover->customer->city }} </p>
    <hr>
    <h4 style="text-align: center font-size: 9.0pt; font-family: 'Courier New'; "> {{ $cover->cover_title }} </h4>
    <hr>
    <p>&nbsp;</p>
    <table style="width:100%;margin-top: 0px;padding:0px;" border="0">
        <tr>
            <td valign="top">
                <table style="width:60%;">
                    <tr>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">Cover Number</td>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">{{ $cover->cover_no }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">Cover Reference</td>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">{{ $cover->endorsement_no }}</td>
                    </tr>

                    <tr>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">Reinsured Name</td>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">{{ firstUpper($cover->customer->name) }}
                        </td>
                    </tr>

                    <tr>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">Class of Business</td>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">
                            {{ firstUpper($treaty_type->treaty_name) }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">Treaty Type</td>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">{{ firstUpper($cover->cover_title) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">Underwriting Year</td>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">
                            {{ date('Y', strtotime(formatDate($cover->cover_from))) }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">Period of Cover</td>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">
                            From:{{ formatDate($cover->cover_from) }} To:{{ formatDate($cover->cover_to) }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">Payment Terms</td>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">
                            {{ $ppw ? firstUpper($ppw->pay_term_desc) : ' ' }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">Our share </td>
                        <td style="font-size: 10.0pt; font-family: 'Courier New';">
                            {{ number_format($cover->share_offered, 2) }}%</td>
                    </tr>
                </table>
            </td>
            <td>
            </td>
        </tr>
    </table>
    <br />
    <table style="width:100%; margin:0px; border:3px; padding:3px;">
        <th align="left" colspan="2"> PARTICULARS</th>
        <th align="left"> AMOUNT</th>
        @foreach ($coverpremiums as $coverpremium)
            <tr>

                <td align="left"
                    style="font-size: 9.0pt; font-family: 'Courier New'; border: 1px solid #181212; padding: 8px;">
                    {{ firstUpper($coverpremium->premium_type_description) }} -
                    {{ firstUpper($coverpremium->premtype_name) }}
                    {{ $coverpremium->treaty_name ? ' - ' . firstUpper($coverpremium->treaty_name) : ' ' }}
                </td>
                <td align="left"
                    style="font-size: 9.0pt; font-family: 'Courier New'; border: 1px solid #181212; padding: 8px;">
                    {{ number_format($coverpremium->basic_amount, 2) }}@if ($coverpremium->apply_rate_flag == 'Y' && $coverpremium->rate != 0)
                        @ {{ number_format($coverpremium->rate, 2) }}%
                    @endif
                </td>
                <td align="left"
                    style="font-size: 9.0pt; font-family: 'Courier New'; border: 1px solid #181212; padding: 8px;">
                    {{ number_format($coverpremium->basic_amount, 2) }}
                </td>

            </tr>
        @endforeach
        <tr>
            <td align="left"
                style="font-size: 9.0pt; font-family: 'Courier New'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                TOTAL</td>
            <td align="right"
                style="font-size: 9.0pt; font-family: 'Courier New'; border: 1px solid #181212; padding: 8px; font-weight: bold;  ">
                <span
                    style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($basicTotalCR, 2) }}</span>
            </td>
            <td align="right"
                style="font-size: 9.0pt; font-family: 'Courier New'; border: 1px solid #181212; padding: 8px; font-weight: bold; ">
                <span
                    style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($basicTotalDR, 2) }}</span>
            </td>
        </tr>
    </table>
    <br />

    <table style="width:100%; border: 1px solid #181212; padding: 8px;">
        <tr>
            <td align="left" colspan="2"
                style="font-size: 9.0pt; font-family: 'Courier New'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                BALANCE DUE FROM YOU</td>

            <td align="right"
                style="font-size: 9.0pt; font-family: 'Courier New'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                {{ number_format($debit->net_amt, 2) }}</td>
        </tr>
    </table>

    <br />
    <br />
    <br />
    <table style="width: 100%;">
        <tr>
            <td align="left" style="font-size: 10.0pt; font-family: 'Courier New';">{{ $company->company_name }}</td>
            <td align="left">&nbsp;
            <td>
            <td align="left"></td>
        </tr>
        <tr>
            <td align="left">
                {{-- <img src="{{ asset('stamp.png')}}" alt="" style="width: 300px; height: auto;"> --}}
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
            <td align="left" style="font-size: 10.0pt; font-family: 'Courier New';">Signature</td>
            <td align="left">&nbsp;
            <td>
            <td align="left" style="font-size: 10.0pt; font-family: 'Courier New';">Date:{!! formatDate($debit->created_at) !!} </td>
        </tr>
    </table>
@endsection
