@extends('printouts.base')

@section('title', 'Claim Acknowledgment' . ' - ' . config('app.name')))

@section('content')
    <style>
        .courier-10 {
            font-size: 10pt;
            font-family: 'Aptos', sans-serif;
        }

        .letterhead {
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-style: italic;
        }

        .content {
            margin-bottom: 30pt;
            font-size: 11pt;

            p {
                margin: 0px;
                padding: 0px;
            }
        }

        .signature-area {
            margin-top: 40px;
        }

        .claim-list {
            margin: 0px;
            margin-bottom: 12px;
        }

        p {
            font-size: 10pt;
            font-family: 'Aptos', sans-serif;
        }
    </style>

    <div class="claim-page"
        style="width:100%;margin-top: 0px; padding:0px; font-size: 10pt; font-family: 'Aptos', sans-serif;">
        <div style="width:100%; margin-top: 0px; padding:0px; font-size: 10pt; font-family: 'Aptos', sans-serif;"
            class="debit-reinsurer-page">
            {{-- <br /> --}}
            <table id="slip-header">
                <tr>
                    <td tyle="width: 100%;">
                        <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                            <tr>
                                <td style="font-size: 10pt;" class="bold"> Date: {!! formatDate($cover->created_at) !!} </td>
                            </tr>
                            <br />
                            <tr>
                                <td
                                    style="font-size: 10pt; word-wrap: break-word; overflow-wrap: break-word; max-width: 200px;">
                                    {{ strtoupper($cover->partner_name) }} </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10pt;">
                                    P.O BOX
                                    {{ preg_replace('/P(\.?O\.?\s?BOX)/i', '', $cover->partner_postal_address) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10pt;"> {{ $cover->partner_city }} </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10pt;"> {{ $cover->partner_street }}, {{ $cover->partner_city }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10pt;">
                                    {{ \App\Models\Country::where('country_iso', $cover->partner_country_iso ?? '')->value('country_name') ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 10pt;"> {{ $cover->partner_telephone }} </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr></tr>
            </table>
            <br />
            <p class="thin m-0 p-0" style="font-size: 10pt;">{!! $subject_title !!},</p>
            <br />
            <div class="content">
                <p style="font-weight: 500; text-decoration: underline; text-transform: uppercase;">{!! $ref_title !!}
                </p>
                <br />
                <p>The above-captioned matter refers</p>
                <br />
                <p>We acknowledge receipt of your claim notification in respect of the incident that occurred on
                    <b>{{ date('jS F Y', strtotime($cover->created_at)) }}</b>. The claim has been registered under Claim
                    Number <b>{{ $cover->cover_no }}</b>, in accordance with policy reference
                    <b>{{ $cover->endorsement_no }}</b>.
                </p>
                <br />
                @if ($documented)
                    <p>We confirm receipt of the following documentation:-</p>
                    <br />
                    <ul class="claim-list">
                        @foreach ($docs->where('document_type', 'received_doc') as $doc)
                            <li style="font-size: 10pt;">{{ firstUpper($doc->document->doc_name) }}</li>
                        @endforeach
                    </ul>
                    <br />
                    <p>Total Claim Amount is <b>KES {{ $claim_amount }}</b></p>
                    <br />
                    <p>Please be advised that we have informed the reinsurers accordingly and will update you on the
                        progress of the claim settlement in due course.</p>
                    <br />
                    <p>Should you require any additional assistance, please do not hesitate to contact us.</p>
                @else
                    <p>We confirm receipt of the following documentation:-</p>
                    <br />
                    <ul class="claim-list">
                        @foreach ($docs->where('document_type', 'received_doc') as $doc)
                            <li style="font-size: 10pt;">{{ firstUpper($doc->document->doc_name) }}</li>
                        @endforeach
                    </ul>
                    <br />
                    <p>To enable us to proceed with the assessment and processing of this claim, we kindly request that you
                        furnish us with the below outstanding documentations at your earliest convenience.</p>
                    <br />
                    <ul class="claim-list">
                        @foreach ($docs->where('document_type', 'missing_doc') as $doc)
                            <li style="font-size: 10pt;">{{ firstUpper($doc->document->doc_name) }}</li>
                        @endforeach
                    </ul>
                    <br />
                    <p>Looking forward to your feedback on the pending documentations.</p>
                    </p>
                    <br />
                @endif
            </div>
            <table class="signature-area" style="width: 100%;">
                <tr>
                    <td style="font-size: 10pt;">Yours faithfully,</td>
                </tr>
                <tr>
                    <td style="font-size: 10pt;">For and on behalf of</td>
                </tr>
                <tr>
                    <td align="left" style="font-size: 10pt;">{{ $company->company_name }}</td>
                    <td align="left">&nbsp;
                    <td>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left"></td>
                    <td align="left">&nbsp;
                    <td>
                    <td align="left"></td>
                </tr>
                <tr rowspan=5> </tr>
                <tr>
                    <td align="left">____________________________</td>
                    <td align="left">&nbsp;
                    <td>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left" style="font-size: 10.0pt; font-family: 'Aptos', sans-serif;">Signature</td>
                    <td align="left">&nbsp;
                    <td>
                    <td align="left" style="font-size: 10.0pt; font-family: 'Aptos', sans-serif;">
                        Date:{!! formatDate(date('Ymd')) !!} </td>
                </tr>
            </table>
        </div>
    </div>
@endsection
