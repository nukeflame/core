@extends('printouts.base')

@section('content')
    <style>
        .courier-10 {
            font-size: 10.0pt;
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
    </style>

    <div class="claim-page"
        style="width:100%;margin-top: 0px; padding:0px; font-size: 10.0pt; font-family: 'Open Sans',sans-serif;">
        <div style="width:100%; margin-top: 0px; padding:0px; font-size: 10pt; font-family: 'Open Sans',sans-serif;"
            class="debit-reinsurer-page">
            <br />

            <table id="slip-header">
                <tr>
                    <td tyle="width: 100%;">
                        <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                            <tr>
                                <td style="font-size: 10.0pt;" class="bold"> Date: {!! formatDate($cover->created_at) !!} </td>
                            </tr>
                            <br />
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
                </tr>
                <tr></tr>
            </table>

            <br />
            <br />
            <p class="thin m-0 p-0">{{ $subject_title }},</p>
            <br />
            <br />

            <div class="content">
                <p><strong><u>Re: Damage to Houses on Dec 05,2024</u></strong></p>
                <p><strong><u>Insured: Bohemian Flowers Limited</u></strong></p>

                <br />
                <p>The above matter refers,</p>
                <br />

                <p>We acknowledge receipt of the above subject claim that occurred on
                    {{ date('d/m/Y', strtotime($cover->created_at)) }}
                    as a result of d.</p>
                <br />
                <p>Kindly note we have registered the Claim No. 293930 from the cover reference
                    C0290393.</p>
                <br />
                <p>Kindly acknowledge receipt of:</p>
                <br />
                <ul>
                    {{-- @foreach ($claim['documents'] as $document)
                        <li>{{ $document }}</li>
                    @endforeach --}}
                </ul>
                <br />
                <p>The claim loss amount is KES. 102,672.00.</p>
                <br />
                <p>We thank you for notifying us of claim occurrence.</p>
            </div>
            <br />
            <div class="signature-area">
                <p>Yours Faithfully,</p>
                <br />
                <p>For and on behalf of<br>
                    W</p>
                <br />
                <p>Date: {{ date('d/m/Y', strtotime(now())) }}</p>
                <br />
                <p>Signature</p>
                <br />
            </div>
        </div>
    </div>
@endsection
