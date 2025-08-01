@extends('printouts.base')

@section('title', 'Claim Notice Letter' . ' - ' . config('app.name')))

@section('content')
    <style>
        .courier-10 {
            font-size: 10pt;
            font-family: 'Open Sans', sans-serif;
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
            font-family: 'Open Sans', sans-serif;
        }

          .claim-page {
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
    @if ($reinsurers->count() > 0)
        @foreach ($reinsurers as $index => $reinsurer)
            <div class="claim-page{{ $index === 0 ? 'first-page' : '' }}" style="width:100%; margin-top: 0px; padding:0px; font-size: 10pt; font-family: 'Open Sans',sans-serif;">
                <div style="width:100%; margin-top: 0px; padding:0px; font-size: 10pt; font-family: 'Open Sans',sans-serif;" class="debit-reinsurer-page">
                    <table id="slip-header">
                        <tr>
                            <td style="width: 100%;">
                                <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                                    <tr>
                                        <td style="font-size: 10pt;" class="bold"> Date: {!! formatDate($claim->created_at) !!} </td>
                                    </tr>
                                    <br />
                                    <tr>
                                        <td style="font-size: 10pt; word-wrap: break-word; overflow-wrap: break-word; max-width: 200px;">
                                            {{ strtoupper($reinsurer->partner_name) }} </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10pt;">
                                            P.O BOX
                                            {{ preg_replace('/P(\.?O\.?\s?BOX)/i', '', $reinsurer->partner_postal_address) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10pt;"> {{ $reinsurer->partner_city }} </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10pt;"> {{ $reinsurer->partner_street }}, {{ $reinsurer->partner_city }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10pt;">
                                            {{ \App\Models\Country::where('country_iso', $reinsurer->partner_country_iso ?? '')->value('country_name') ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10pt;"> {{ $reinsurer->partner_telephone }} </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr></tr>
                    </table>
                    <br />
                    <p class="thin m-0 p-0" style="font-size: 10pt;">{{ $subject_title }},</p>
                    <br />
                    <div class="content">
                        <p style="font-weight: 500; text-decoration: underline; text-transform: uppercase;">{!! $ref_title !!}
                        </p>
                        <br />
                        <p>The above matter refers,</p>
                        <br />
                        <p>We have been notified of the above subject claim that occurred on  <b>{{ date('jS F Y', strtotime($cover->created_at)) }}</b>. as a result of {{ $claim->cause_of_loss}}</p>
                        <br />
                        <p>Kindly note we have registered the Claim No. <b>{{ $claim->cedant_claim_no }}</b> from the cover reference
                            <b>{{ $cover->cover_no }}</b></p>
                        <br />
                        <p>Kindly acknowledge receipt of:-</p>
                        <br />
                        <ul class="claim-list">
                            @foreach ($docs->where('date_received', '!=', null) as $doc)
                                <li style="font-size: 10pt;">{{ firstUpper($doc->document->doc_name) }}</li>
                            @endforeach
                        </ul>
                        <br />
                        <p>The claim loss amount is KES {{ $claim_amount }}</p>
                        <br />
                        <p>Your quick response will be much appreciated.</p>
                    </div>
                    <table class="signature-area" style="width: 100%;">
                         <tr> <td style="font-size: 10pt;" class="pt-4">Yours faithfully,</td></tr>
                         <tr> <td style="font-size: 10pt;" class="pt-4">For and on behalf of</td></tr>
                         <tr>
                            <td align="left" style="font-size: 10pt;">{{$company->company_name}}</td>
                            <td align="left">&nbsp;</td>
                            <td align="left"></td>
                        </tr>
                        <tr>
                            <td align="left"></td>
                            <td align="left">&nbsp;</td>
                            <td align="left"></td>
                        </tr>
                        <tr rowspan=5> </tr>
                        <tr>
                            <td align="left">____________________________</td>
                            <td align="left">&nbsp;</td>
                            <td align="left"></td>
                        </tr>
                        <tr>
                            <td align="left" style="font-size: 10.0pt; font-family: 'Open Sans', sans-serif;">Signature</td>
                            <td align="left">&nbsp;</td>
                            <td align="left" style="font-size: 10.0pt; font-family: 'Open Sans', sans-serif;">Date:{!! formatDate(date('Ymd')) !!} </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
@endsection
