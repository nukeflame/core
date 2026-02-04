@extends('printouts.base')

@section('content')
<hr>
<p style="text-align:left; font-size: 9.0pt; font-family: 'Aptos Mono'; "> TO </p> 
<p style="text-align:right; font-size: 10.0pt; font-family: 'Aptos Mono'; ">         
    @if($debit->document == 'CRN')
    Credit Note{{ $debit->document}}{{ $debit->dr_no}}/{{ $debit->period_year }}
@else
    Debit Note {{ $debit->document}}{{ $debit->dr_no}}/{{ $debit->period_year }}
@endif 
</p> 

<p style="text-align:left; font-size: 9.0pt; font-family: 'Aptos Mono'; ">{{ $cover->customer->name}} </p> 
<p style="text-align:right; font-size: 9.0pt; font-family: 'Aptos Mono';"> Date: {!! formatDate($debit->created_at) !!} </p>

<p style="font-size: 9.0pt; font-family: 'Aptos Mono'; ">{{ $cover->customer->postal_address}} </p>
<p style="text-align:right;font-size: 9.0pt; font-family: 'Aptos Mono'; "> Currency: {!! $cover->currency_code !!} </p>

<p style="font-size: 9.0pt; font-family: 'Aptos Mono'; ">{{ $cover->customer->city}} </p>
<hr>
<h4 style="text-align: center font-size: 9.0pt; font-family: 'Aptos Mono'; "> {{ $cover->cover_title}} </h4>
<hr>
<p>&nbsp;</p>
<table style="width:100%;margin-top: 0px;padding:0px;" border="0">
    <tr>
        <td valign="top">
            <table style="width:60%;">
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Cover Number</td>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{ $cover->cover_no}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Cover Reference</td>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{ $cover->endorsement_no}}</td>
                </tr>
                
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Reinsured Name</td>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{ firstUpper($cover->customer->name)}}</td>
                </tr>
             
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Class of Business</td>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{ firstUpper($treaty_type->treaty_name)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Treaty Type</td>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{ firstUpper($cover->cover_title)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Underwriting Year</td>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{ date('Y', strtotime(formatDate($cover->cover_from))) }}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Period of Cover</td>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">From:{{formatDate($cover->cover_from)}} To:{{formatDate($cover->cover_to)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Payment Terms</td>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';"> {{ $ppw ? firstUpper($ppw->pay_term_desc) : ' ' }}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">Our share </td>
                    <td style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{number_format($cover->share_offered,2) }}%</td>
                </tr>
            </table>
        </td>
        <td>
        </td>
    </tr>
</table>
<br/>
<table style="width:100%; margin:0px; border:3px; padding:3px;">
    <th align="left"> PARTICULARS</th>
    <th align="left"> TO CEDANT</th>
    <th align="left"> TO REINSURERS</th>
    @foreach($coverpremiums as $coverpremium)

    <tr>
            <td  align="left" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px;">
             {{ firstUpper($coverpremium->premtype_name) }}
            </td>
            <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px;">@if(in_array($coverpremium->dr_cr, ['CR'])) {{number_format($coverpremium->basic_amount,2) }} @else 0.00 @endif
            </td>
            <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px;">@if(in_array($coverpremium->dr_cr, ['DR'])) {{number_format($coverpremium->basic_amount,2) }} @else 0.00 @endif
            </td>

    </tr>
    @endforeach
    <tr>
        <td align="left" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;"> TOTAL</td>
        <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;  ">  <span style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($basicTotalCR, 2) }}</span>
        </td>
        <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold; ">  <span style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($basicTotalDR, 2) }}</span>
        </td>
    </tr>
</table>
<br/>
    <table style="width:100%; border: 1px solid #181212; padding: 8px;">
        <tr >
            <td  align="left"   colspan="2" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">PROFIT REALIZED</td>
            <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">{{ number_format($basicTotalDR - $basicTotalCR,2) }}</td>
        </tr>
        <tr >
            <td  align="left"   colspan="2" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">PROFIT COMMISSION RATE</td>
            <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">{{ number_format($cover->profit_comm_rate,2) }}%</td>
        </tr>
        <tr >
            <td  align="left"   colspan="2" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">PROFIT COMMISSION AMOUNT</td>
            <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">{{ number_format( ((($cover->profit_comm_rate) / 100) * ($basicTotalDR - $basicTotalCR) ),2) }}</td>
        </tr>
        <tr >
            <td  align="left"   colspan="2" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">YOUR SHARE</td>
            <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">{{ number_format($cover->share_offered,2) }}% </td>
        </tr>
        <tr >
            <td  align="left"   colspan="2" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">DUE TO YOU</td>
            <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;"><span style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;"> {{ number_format( (($cover->share_offered / 100) * (($cover->profit_comm_rate / 100) * ($basicTotalDR - $basicTotalCR) )),2) }} </span>
            </td>
        </tr>
        <tr>
            <td  align="left"   colspan="2" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">POSTED AMOUNT </td>
            <td align="right" style="font-size: 9.0pt; font-family: 'Aptos Mono'; border: 1px solid #181212; padding: 8px; font-weight: bold;">
                <span style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;"> 
                    {{ number_format( $debit->net_amt,2) }} 
                </span>
            </td>
        </tr>
    </table>

<br/>
<br/>
<br/>
<table style="width: 100%;">
    <tr>
        <td align="left" style="font-size: 10.0pt; font-family: 'Aptos Mono';">{{$company->company_name}}</td>
        <td  align="left">&nbsp;<td>
        <td  align="left"></td>
    </tr>
    <tr>
        <td align="left">
            {{-- <img src="{{ asset('stamp.png')}}" alt="" style="width: 300px; height: auto;"> --}}
        </td>
        <td  align="left">&nbsp;<td>
        <td  align="left"></td>
    </tr>
    <tr rowspan=5> </tr>
    <tr>
        <td align="left">________________________</td>
        <td  align="left">&nbsp;<td>
        <td  align="left"></td>
    </tr>
    <tr>
        <td align="left" style="font-size: 10.0pt; font-family: 'Aptos Mono';">Signature</td>
        <td  align="left">&nbsp;<td>
        <td align="left" style="font-size: 10.0pt; font-family: 'Aptos Mono';">Date:{!! formatDate($debit->created_at) !!} </td>
    </tr>
</table>

@endsection
