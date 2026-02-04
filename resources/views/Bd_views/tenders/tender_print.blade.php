<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <title>Tender Print out</title>
        <style>
            body {
                font-family: 'Aptos', Arial, sans-serif;
                margin: 0;
                padding: 0;
                top: 0;
            }

            .centered-content {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }

            .page {
                width: 95%;
                height: 100vh;
                /* Ensures each page fills the viewport */
                page-break-inside: avoid;
                page-break-after: always;
                padding: 10px;
            }
        </style>

    </head>

    <body>

        <div class="centered-content" style="font-weight: bold; height:95%;">
            <div>
                <img src="{{ public_path('images/header.png') }}" alt="header"
                    style="width: 100%; display: block; margin: 0; padding: 0;" />
            </div>
            <div style="height 50%; margin-top:160px;">

                <p style="padding-left: 5%">{{ $tender->tender_no }}</p>
                <p style="padding-left: 5%">{{ $tender->client_name }}</p>
                <p style="padding-left: 5%">{{ $tender->tender_nature }}</p>
                <p style="padding-left: 5%">{{ $tender->tender_category }}</p>
                <p style="padding-left: 5%">{{ $tender->tender_description }}</p>
                <p style="padding-left: 5%">{{ formatDate($tender->closing_date) }}</p>
            </div>
            <div style="position: absolute; bottom: 0;height 15%; margin-bottom:0">
                <img src="{{ public_path('images/footer.png') }}" alt="footer"
                    style="width: 100%; display: block; margin: 0; padding: 0; bottom:0" />
            </div>
        </div>
        <div class="page">

            <div style="margin-bottom: 50px;">
                <img src="{{ public_path('img/company/accentria.png') }}"
                    style="width: 25%; height: auto; float:right" />
            </div>
            <p></p>

            @php
                $pageNumber = 1; // Initialize page number
            @endphp

            <!-- Table of Contents -->
            <div class="toc-container">
                @foreach ($tenderTocSecs as $tenderTocSec)
                    <!-- Section Title with Page Number aligned to the far right -->
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <!-- Title Section (on the left) -->
                        <p style="flex-grow: 1; margin: 0;">
                            <b>{{ $tenderTocSec->toc_description }}</b>
                        </p>
                        <!-- Page Number (on the right) -->
                        <p style="margin: 0; text-align: right; flex-shrink: 0;">
                            <b>{{ $pageNumber }}</b>
                        </p>
                    </div>

                    <!-- Loop through Subcategories within the section -->
                    @foreach ($tendersubs as $sub)
                        @if ($tenderTocSec->toc_no == $sub->toc_no)
                            <!-- Subcategory Title with Page Number aligned to the far right -->
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-left: 20px; margin-bottom: 10px;">
                                <!-- Subsection Title (slightly indented) -->
                                <p style="flex-grow: 1; margin: 0;">
                                    {{ $sub->subcat_desc }}
                                </p>
                                <!-- Page Number (on the right) -->
                                <p style="margin: 0; text-align: right; width: 620px; flex-shrink: 0; font-size: 14px;">
                                    <strong>{{ $pageNumber }}</strong>
                                </p>
                            </div>
                            @php
                                // Increment page number after each subcategory
                                $pageNumber++;
                            @endphp
                        @endif
                    @endforeach
                @endforeach
            </div>


        </div>
        @foreach ($tenderTocSecs as $tenderTocSec)
            <div style="margin-bottom: 50px;">
                <img src="{{ public_path('img/company/accentria.png') }}"
                    style="width: 25%; height: auto; float:right" />
            </div>
            <div class="centered-content">
                <p style="margin: 0; text-align: right; width: 400px;  height:100%;">
                    <b>{{ $tenderTocSec->toc_description }}</b>
                </p>
            </div>

            @foreach ($tendersubs as $tenderTocItem)
                @if ($tenderTocSec->toc_no == $tenderTocItem->toc_no)
                    <p style="margin-left: 10px; height:100%;">{{ $tenderTocItem->subcat_desc }}</p>
                @else
                    @continue
                @endif
                @foreach ($tenderDocs as $tenderDoc)
                    @if ($tenderDoc->doc_id == $tenderTocItem->doc_id)
                        @if (str_contains($tenderDoc->mimetype, 'pdf'))
                            <div class="page">
                                <img src="{{ public_path('img/company/accentria.png') }}"
                                    style="width: 25%; height: auto; float:right" />



                                <object src="data:application/pdf;base64,{{ $tenderDoc->base64 }}" width="100%"
                                    height="500px"></object>

                            </div>
                        @elseif(str_contains($tenderDoc->mimetype, 'image'))
                            <div class="page">
                                <div style="margin-bottom: 50px;">
                                    <img src="{{ public_path('img/company/accentria.png') }}"
                                        style="width: 25%; height: auto; float:right" />
                                </div>

                                <img src="{{ asset('uploads/2048436297_Acknowledgement letter.pdf') }}" alt="Image"
                                    width="100%" height="500px" style="margin-top:30px" />

                            </div>
                        @else
                        @endif
                    @endif
                @endforeach
            @endforeach
        @endforeach
    </body>

</html>
