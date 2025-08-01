<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Report</title>
    <!-- Include PDFObject library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.4/pdfobject.min.js"></script>
</head>
<body>
    <div id="pdf-container"></div>

    <script>
        // Embed the PDF content into the page
        var pdfContent = {!! json_encode($pdfContent) !!};
        PDFObject.embed(pdfContent, "#pdf-container");
    </script>
</body>
</html>