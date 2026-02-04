@extends('printouts.base')

@section('content')
    <style>
        .endorsement-page {
            font-family: 'Aptos', sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            page-break-before: auto;
        }

        #endorsement-table th {
            border: none;
            background: transparent;
        }
    </style>
    <div class="endorsement-page">
        <div style="width:100%; margin-top: 0px; padding:0px; font-size: 9pt;">
            <table style="width: 100%; font-family: inherit;">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <table style="width: 100%">
                            <tr>
                                <td class="uppercase"><strong>Attention:</strong></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ $cover->partner_name }} </td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td> {{ $cover->partner_street }}, {{ $cover->partner_city }} </td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td> {{ $cover->partner_telephone }} </td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td> {{ \App\Models\Country::where('country_iso', $cover->partner_country_iso)->value('country_name') }}
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 40%; vertical-align: top; float-right">
                        <table style="width: 100%; float:right; text-align:right;">
                            <tr>
                                <td class="uppercase"><strong>ENDORSEMENT NOTICE FOR # :</strong>
                                    {{ $endorsement_document_no }}</td>
                            </tr>
                            <br />
                            <tr>
                                <td> <strong>DATE</strong> : {!! formatDate($cover->created_at) !!} </td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td> <strong>ISSUED BY</strong> : {{ $issuedBy }} </td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table style="margin-top: 1rem; width: 100%; font-family: inherit;">
                <tr>
                    <td colspan="2" class="text-center">
                        <div class="hr-line-btm"></div>
                        <b> {{ strtoupper($cover->insured_name) }} - {!! $cover->cover_from->format('Y') !!} {{ $cover->type_of_bus }}
                            ENDORSEMENT -
                            {{ strtoupper($cover->partner_name) }} </b>
                        <div class="hr-line-btm"></div>
                    </td>
                </tr>
            </table>
            <table style="width: 100%; font-family: inherit; margin-top: 1rem;">
                <tr>
                    <td><strong>COVER REFERENCE:</strong></td>
                    <td>{{ $cover->cover_no }}</td>
                </tr>
                <br />
                <tr>
                    <td><strong>ENDORSEMENT:</strong></td>
                    <td>{{ $endorsement_document_no }}</td>
                </tr>
                <br />
                <tr>
                    <td><strong>CEDANT:</strong></td>
                    <td>{{ $cover->partner_name }}</td>
                </tr>
                <br />
                <tr>
                    <td><strong>INSURANCE GROUP:</strong></td>
                    <td style="text-transform: uppercase;">{{ $class_group?->group_name }}</td>

                </tr>
                <br />
                <tr>
                    <td><strong>INSURANCE CLASS:</strong></td>
                    <td>{{ $class?->class_name }}</td>
                </tr>
            </table>
            <table style="width: 100%; font-family: inherit; margin-top: 1rem;">
                <tr>
                    <td style="text-transform: uppercase;"><strong>Endorsement Details:</strong></td>
                </tr>
                <div class="hr-line-btm"></div>
            </table>
            <div
                style="word-wrap: break-word; white-space: normal; max-width: 100%; margin-top: 10px; font-family: inherit;">
                {{ $narration?->narration }}
            </div>
            <table style="width: 100%; font-family: inherit; margin-top: 2rem;">
                <tr>
                    <td style=""><strong>All other terms, clauses and conditions shall remain
                            unaltered.</strong></td>
                </tr>
            </table>

        </div>
    </div>
@endsection
