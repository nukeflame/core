@extends('printouts.base')

@section('content')
    <style>
        .renewal-pager {
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

        .renewal-page,
        #insurance-table {
            font-family: "Open Sans", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        #insurance-table th {
            border: none;
            background: transparent;
        }

        #insurance-table tbody tr:first-child td {
            padding-top: 8px !important;
        }
    </style>
    @php
        $disableAutoHeader = true;
    @endphp
    @foreach ($reinsurers as $index => $reinsurer)
        <div class="renewal-pager{{ $index === 0 ? 'first-page' : '' }}">
            <header class="logo-header">
                <div class="row">
                    <div class="logo">
                        <img align="left" src="data:image/png;base64,<?php echo base64_encode(file_get_contents(base_path('public/logo.png'))); ?>" alt=""
                            style="width: 230px; height: auto;">
                    </div>
                    <div class="company-info">
                        <p>{{ $company->company_name }}</p>
                        <p>{{ $company->postal_address }}</p>
                        <p>Phone: {{ $company->mobilephone }}</p>
                        <p>Email: {{ $company->email }}</p>
                    </div>
                </div>
                <hr style="padding:0px; border-top: .5pt solid #ddd; margin: 10pt 1pt; margin-left: 5.5pt;" />
            </header>
            <div style="width:100%; margin-top: 110px; padding:0px; font-size: 9pt; font-family: 'Open Sans';"
                class="renewal-page">
                <table id="cover-header">
                    <tr>
                        <td colspan="2" class="text-center">
                            <b class="uppercase"> Renewal Notice</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                                <tr>
                                    <td> <strong>Date:</strong> {{ $created_at }}</td>
                                </tr>
                                <tr>
                                    <td> <strong>Currency:</strong> {!! $cover->currency_code !!}</td>
                                </tr>
                                <tr></tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <br />
                    <tr>
                        <td colspan="2" class="text-center">
                            <div class="hr-line-btm"></div>
                            <b> {{ strtoupper($cover->insured_name) }} - {{ strtoupper($class_name) }} -
                                {{ strtoupper($customer->name) }} </b>
                            <div class="hr-line-btm"></div>
                        </td>
                    </tr>
                </table>
                <table id="cover-details">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td valign="top">
                            The expiration date for your reinsurance coverage is
                            {{ $expiration_date }}. Please inform us if you
                            would
                            like to
                            proceed with renewing
                            coverage.<br /> The current terms of your coverage are as follows:
                        </td>
                    </tr>
                    <table id="cover-details">
                        <tr>
                            <td valign="top">
                                <table class="w-100">
                                    <tr>
                                        <td class="courier-10"><strong> Cover Number:</strong></td>
                                        <td class="courier-10">{{ $cover->cover_no }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4 courier-9"><strong>Reinsured Name:</strong></td>
                                        <td class="pt-4 courier-9">{{ firstUpper($reinsurer?->partner?->name) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4 courier-9"><strong>Insured's Name:</strong></td>
                                        <td class="pt-4 courier-9">{{ firstUpper($cover->insured_name) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4 courier-9"><strong>Business Class</strong></td>
                                        <td class="pt-4 courier-9">{{ firstUpper($class_name) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4 courier-9"><strong>Period of Cover</strong></td>
                                        <td class="pt-4 courier-9">From: {{ formatDate($cover->cover_from) }} To:
                                            {{ formatDate($cover->cover_to) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4 courier-9"><strong>Brief Description </strong></td>
                                        <td class="pt-4 courier-9 uppercase">
                                            {{ firstUpper($cover->insured_name) }}-{{ $cover->type_of_bus }}-{{ firstUpper($cover->customer->name) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4 courier-10"><strong>Sum Insured (100%) </strong></td>
                                        <td class="pt-4 courier-10">{{ number_format($cover->total_sum_insured, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4 courier-10"><strong>Your share S.I</strong> </td>
                                        <td class="pt-4 courier-10">{{ number_format($reinsurer->sum_insured, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4 courier-10"><strong>Premiums (100%) </strong></td>
                                        <td class="pt-4 courier-10">{{ number_format($cover->rein_premium, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4 courier-10"><strong>Your share</strong></td>
                                        <td class="pt-4 courier-10">{{ number_format($reinsurer->share, 2) }}%</td>
                                    </tr>
                                    <tr>
                                </table>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                    <table id="insurance-table"
                        style="width: 100%; border-collapse: collapse; margin: 0; font-family: inherit;">
                        <thead>
                            <tr>
                                <th
                                    style="border-bottom:1px solid #000; width: 40%; padding: 8px; font-weight: bold; text-align: left;">
                                    Reinsurer</th>
                                <th></th>
                                <th
                                    style="border-bottom:1px solid #000; width: 16.66%; padding: 8px; font-weight: bold; text-align: right;">
                                    Share</th>
                                <th></th>
                                <th
                                    style="border-bottom:1px solid #000; width: 16.66%; padding: 8px; font-weight: bold; text-align: right;">
                                    Sum Insured</th>
                                <th></th>
                                <th
                                    style="border-bottom:1px solid #000; width: 16.66%; padding: 8px; font-weight: bold; text-align: right;">
                                    Premium</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $reinsure_share = ($reinsurer->share / 100) * $cover->total_sum_insured;
                                $cover_share = $cover->share_offered;
                            @endphp
                            <tr>
                                <td style="padding: 3px 6px 3px 6px;">{{ firstUpper($reinsurer->partner->name) }}
                                </td>
                                <td></td>
                                <td style="padding: 3px 6px 3px 6px; text-align: right;">
                                    {{ number_format($reinsurer->share, 2) }}&#37;</td>
                                <td></td>
                                <td style="padding: 3px 6px 3px 6px; text-align: right;">
                                    {{ number_format($reinsurer->sum_insured, 2) }}</td>
                                <td></td>
                                <td style="padding: 3px 6px 3px 6px; text-align: right;">
                                    {{ number_format($reinsurer->premium, 2) }}</td>
                            </tr>
                            <tr></tr>
                            <tr>
                                <td style="padding: 6px; font-weight: bold;"></td>
                                <td></td>
                                <td
                                    style="border-bottom:1px solid #000; border-top:1px solid #000; padding: 6px; text-align: right; font-weight: bold;">
                                    {{ number_format($reinsurer->share, 2) }}&#37;
                                </td>
                                <td></td>
                                <td
                                    style="border-bottom:1px solid #000; border-top:1px solid #000; padding: 6px; text-align: right; font-weight: bold;">
                                    {{ number_format($reinsurer->sum_insured, 2) }}
                                </td>
                                <td></td>
                                <td
                                    style="border-bottom:1px solid #000; border-top:1px solid #000; padding: 6px; text-align: right; font-weight: bold;">
                                    {{ number_format($reinsurer->premium, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </div>
            <div class="footer-wrapper">
                <div class="footer">
                    <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                            class="page-number"></span></span>
                </div>
            </div>
            {{-- <div class="footer">
                <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved.</span>
            </div> --}}
    @endforeach
@endsection
