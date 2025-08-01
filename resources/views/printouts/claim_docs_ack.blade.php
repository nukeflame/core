@extends('printouts.base')

@section('content')
<style>
    #breakdown-details  {
        width: 100%;
    }
    #breakdown-details tr {
        border-bottom: 1px solid #000;
    }
    #breakdown-details td {
        border-bottom: 1px solid #000;
        padding: 8px 10px;
    }

    #cover-details,#reinsurer-details,#credit-details,table {
        /* border: 1px solid black; */
        border-collapse: collapse;
    }

    #cover-details td {
        font-size: 10.0pt; 
        padding: 4px;
        font-family: 'Courier New';
    }
    #reinsurer-details td,#cover-details td,#credit-details td,#breakdown-details td{
        text-align:left; 
        font-size: 9.0pt; 
        font-family: 'Courier New';
    }
    /* #credit-details td{
        border: 1px solid black;
        padding-left: 15px;
    } */
    .reinsurer-page {
    page-break-before: always;
}
.first-page {
    page-break-before: auto;
}
.pt-4{ padding-top: 6px;}
.text-center{ text-align: center;}
.hr-line{ border: 0.8px solid #3D3D3D;padding: 0;margin: 2px;}
.calibri-10{font-size: 10.0pt;font-family: 'Calibri';}
.courier-9{font-size: 9.0pt; font-family: 'Courier New';}
.courier-10{font-size: 10.0pt; font-family: 'Courier New';}
.w-100{width: 100%;}
.no-border{border: none;}
.text-right{ text-align: right;}
.text-left{text-align:left;}
.p-8{padding: 8px;}
.bottom-border{border-bottom: 0.2px solid #181212;}
.bold{font-weight: bold;}
.m-0{margin:0;}
.p-0{padding:0;}
.p-6{padding:6px;}
.info-box{
    display: inline-block;padding: 2px; margin-left: 2px; 
    min-width:80px; font-size: 8.0pt;
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
            height: 120px; /* Adjust based on your header height */
            z-index: 1000; /* Ensure the header stays on top */
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
@foreach($reinsurers as $index => $reinsurer)
<div class="reinsurer-page  {{ $index === 0 ? 'first-page' : '' }}" style="width:100%;margin-top: 100px;padding:0px; font-size: 10.0pt; font-family: 'Courier New';">

<table class="w-100 courier-10 p-0">
    <tr>
        <td>
            <table class="w-100 courier-10 p-0 m-0">
                <tr>
                    <td> {{ formatDate($claim->created_at)}}</td>
                </tr>
                <tr>
                    <td> {{ $reinsurer->partner_name}} </td>
                </tr>
                <tr>
                    <td> {{ $reinsurer->partner_postal_address}}  </td>
                </tr>
                <tr>
                    <td> {{ $reinsurer->partner_street}}, {{ $reinsurer->partner_city}} </td>
                </tr>
                <tr>
                    <td> {{ \App\Models\Country::where('country_iso', $reinsurer->partner_country_iso)->value('country_name') }} </td>
                </tr>
                <tr>
                    <td> {{ $reinsurer->partner_telephone}}  </td>
                </tr>
            </table>
        </td>
    </tr>
    
</table>
<br>
<table class="w-100" >
    <tr>
        <td valign="top">
            <table class="w-100">
                <tr>
                    <td class="pt-4 courier-10" >Dear Sir/Madam,</td>
                </tr>
                <br>
                <tr>
                    <td class="pt-4 courier-10"><strong>RE:{{$claim->loss_narration}} ON {{formatDate($claim->date_of_loss)}}</strong></td>
                </tr>
                
                <tr>
                    <td class="pt-4 courier-10"><strong> INSURED:{{$claim->insured_name}} </strong></td>
                </tr>
                <br>
                <tr>
                    <td class="pt-4 courier-10" > Above matter refers.</td>
                </tr>
                <tr>
                    <td class="pt-4 courier-10"> Kindly note we have registered the above claim under claim no. {{$claim->claim_no }} of which note to quote in your
                        future correspondence.</td>
                </tr>
                @if($docs->where('date_received', '!=', null)->count() > 0)
                <tr> 
                    <td class="pt-4 courier-10"> We acknowledge receipt of </td>
                </tr>
                @foreach($docs->where('date_received', '!=', null) as $doc)
                    <tr>
                        <td class="pt-4 courier-10"> - {{ firstUpper($doc->document->doc_name) }} </td>
                    </tr>
                @endforeach
            @endif
            
            @if($docs->where('date_received', '==', null)->count() > 0)
                <tr> 
                    <td class="pt-4 courier-10"> Pending documents are :- </td> 
                </tr>
                @foreach($docs->where('date_received', '==', null) as $doc)
                    <tr>
                        <td class="pt-4 courier-10"> - {{ firstUpper($doc->document->doc_name) }} </td>
                    </tr>
                @endforeach
            @endif
            
            <br>
            <tr> <td class="pt-4 courier-10">On Without prejudice please let us have reasons for late notification. </td></tr>
            <br>
            <tr> <td class="pt-4 courier-10">Your quick response will be appreciated. </td></tr>
            <br></br>

            </table>
        </td>
        <td>
        </td>
    </tr>
</table>

<table style="width: 100%;">
    <tr> <td class="pt-4 courier-10">Yours faithfully,</td></tr>
    <tr> <td class="pt-4 courier-10">For and on behalf of</td></tr>
    <tr>
        <td align="left" style="font-size: 10.0pt; font-family: 'Calibri';">{{$company->company_name}}</td>
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
        <td align="left">____________________________</td>
        <td  align="left">&nbsp;<td>
        <td  align="left"></td>
    </tr>
    <tr>
        <td align="left" style="font-size: 10.0pt; font-family: 'Calibri';">Signature</td>
        <td  align="left">&nbsp;<td>
        <td align="left" style="font-size: 10.0pt; font-family: 'Calibri';">Date:{!! formatDate(date('Ymd')) !!} </td>
    </tr>
</table>
@endforeach
@endsection
