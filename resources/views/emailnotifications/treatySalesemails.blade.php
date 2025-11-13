<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        .docs-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }

        .docs-section h3 {
            color: #333;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .docs-section ul {
            list-style-type: disc;
            padding-left: 20px;
            margin: 0;
        }

        .docs-section li {
            font-size: 16px;
            color: #555;
            margin-bottom: 8px;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="container">
        <p style="color: #555;">Dear {{ $salutation }},</p>
        {{-- @if (!empty($cedant_doc_present))
            <p style="font-size: 16px; line-height: 1.6; color: #555; margin-top: 10px; margin-bottom: 10px;">
                Please find attached the documents you requested. Kindly review them and let us know if any additional
                information is required.
            </p>
            <div style="height: 10px;"></div>
        @endif --}}


        @if (!empty($docs_we_require))
            <p style="font-size: 16px; line-height: 1.6; color: #555; margin-top: 10px; margin-bottom: 10px;">
                {{-- To continue processing the treaty, we kindly request you to provide the documents listed below. Please
                submit them at your earliest convenience, and feel free to contact us if you have any questions. --}}
                Please Find attached letter.
            </p>

            {{-- <div class="docs-section">
                <h3>Documents We Require</h3>
                <ul>
                    @foreach ($docs_we_require as $doc)
                        <li>{{ $doc }}</li>
                    @endforeach
                </ul>
            </div> --}}
        @endif

        {{-- @if (!empty($received_docs))
          <p style="font-size: 16px; line-height: 1.6; color: #555; margin-top: 10px; margin-bottom: 10px;">
               These are the documents we have received from you.
            </p>
            <div class="docs-section">
                <h3>Documents We Received</h3>
                <ul>
                    @foreach ($received_docs as $doc)
                        <li>{{ $doc }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        <div class="footer">
            <p>Best regards,</p>
            <p><i>Signature</i></p>
            <p>Acentria International</p>
        </div>
    </div>
</body>

</html>
