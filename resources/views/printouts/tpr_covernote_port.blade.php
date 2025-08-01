@extends('printouts.base')

@section('content')
<hr>
<p style="text-align:left; font-size: 10.0pt; font-family: 'Calibri'; "> TO </p> 
<p style="text-align:right; font-size: 10.0pt; font-family: 'Calibri'; ">         
    @if($debit->document == 'CRN')
    Credit Note{{ $debit->document}}{{ $debit->dr_no}}/{{ $debit->period_year }}
@else
    Debit Note {{ $debit->document}}{{ $debit->dr_no}}/{{ $debit->period_year }}
@endif 
</p> 

<p style="text-align:left; font-size: 10.0pt; font-family: 'Calibri'; ">{{ $cover->customer->name}} </p> 
<p style="text-align:right; font-size: 10.0pt; font-family: 'Calibri';"> Date: {!! formatDate($debit->created_at) !!} </p>

<p style="font-size: 10.0pt; font-family: 'Calibri'; ">{{ $cover->customer->postal_address}} </p>
<p style="text-align:right;font-size: 10.0pt; font-family: 'Calibri'; "> Currency: {!! $cover->currency_code !!} </p>

<p style="font-size: 10.0pt; font-family: 'Calibri'; ">{{ $cover->customer->city}} </p>
<hr>
<h4 style="text-align: center font-size: 10.0pt; font-family: 'Calibri'; "> {{ $cover->cover_title}} </h4>
<hr>
<p>&nbsp;</p>
<table style="width:100%;margin-top: 0px;padding:0px;" border="0">
    <tr>
        <td valign="top">
            <table style="width:60%;">
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">Cover Number</td>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ $cover->cover_no}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">Cover Reference</td>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ $cover->endorsement_no}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">Reinsured Name</td>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ firstUpper($cover->customer->name)}}</td>
                </tr>
             
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">Class of Business</td>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ firstUpper($treaty_type->treaty_name)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">Treaty Type</td>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ firstUpper($cover->cover_title)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">Underwriting Year</td>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ date('Y', strtotime(formatDate($cover->cover_from))) }}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">Period of Cover</td>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">From:{{formatDate($cover->cover_from)}} To:{{formatDate($cover->cover_to)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">Payment Terms</td>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">{{ $ppw ? firstUpper($ppw->pay_term_desc) : ' ' }}</td>
                </tr>
                <tr>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">Our share </td>
                    <td style="font-size: 10.0pt; font-family: 'Calibri';">{{number_format($cover->share_offered,2) }}%</td>
                </tr>
            </table>
        </td>
        <td>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 10.0pt; font-family: 'Calibri'; width:100%; margin:0px; border:3px; padding:3px;">
    <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; " > PARTICULARS</th>
    <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> BASIC AMOUNT</th>
    <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> DEBIT AMOUNT</th>
    <th align="left" style="font-size: 10.0pt; font-family: 'Calibri'; "> CREDIT AMOUNT</th>
    @foreach($coverpremiums as $coverpremium)
    @if($coverpremium->final_amount > 0)
    <tr>
            <td  align="left" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px;">
                {{ firstUpper($coverpremium->premium_type_description) }} 
            </td>
            <td align="left" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px;">{{number_format($coverpremium->basic_amount,2) }}@if($coverpremium->apply_rate_flag=='Y') @ {{number_format($coverpremium->rate,2)}}% @endif
            </td>
            <td align="right" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px;">@if(in_array($coverpremium->dr_cr, ['DR'])) {{number_format($coverpremium->final_amount,2) }} @else 0.00 @endif
            </td>
            <td align="right" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px;">@if(in_array($coverpremium->dr_cr, ['CR'])) {{number_format($coverpremium->final_amount,2) }} @else 0.00 @endif
            </td>

    </tr>
    @endif
    @endforeach
    <tr>
        <td align="left" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;"> TOTAL</td>
        <td align="left" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;"> </td>
        <td align="right" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;  ">  <span style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($finalTotalDR, 2) }}</span>
        </td>
        <td align="right" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold; ">  <span style="border-top: 2px double #181212; border-bottom: 2px double #181212; padding: 3px;">{{ number_format($finalTotalCR, 2) }}</span>
        </td>
    </tr>
</table>
<br/>
  
    <table style="width:100%; border: 1px solid #181212; padding: 8px;">
        <tr >
            <td  align="left"   colspan="2" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">BALANCE DUE FROM YOU</td>
            
            <td align="right" style="font-size: 10.0pt; font-family: 'Calibri'; border: 1px solid #181212; padding: 8px; font-weight: bold;">{{ number_format($debit->net_amt,2) }}</td>
        </tr>
        </table>

<br/>
<br/>
<br/>
<table style="width: 100%;">
    <tr>
        <td align="left" style="font-size: 10.0pt; font-family: 'Calibri';">{{$company->company_name}}</td>
        <td  align="left">&nbsp;<td>
        <td  align="left"></td>
    </tr>
    <tr>
        <td align="left">
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
        <td align="left" style="font-size: 10.0pt; font-family: 'Calibri';">Signature</td>
        <td  align="left">&nbsp;<td>
        <td align="left" style="font-size: 10.0pt; font-family: 'Calibri';">Date:{!! formatDate($debit->created_at) !!} </td>
    </tr>
</table>

@endsection
