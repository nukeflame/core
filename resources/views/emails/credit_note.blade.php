<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Aptos', Arial, sans-serif;
        }

        .header {
            background: #f5f5f5;
            padding: 20px;
        }

        .content {
            padding: 20px;
        }

        .content p,
        .content h1,
        .content h2,
        .content h3,
        .content h4,
        .content h5,
        .content h6 {
            padding: 0px;
            margin: 0px;
        }
    </style>
</head>

<body>
    {{-- <div class="header">
        <h2>Facultative Credit Note</h2>
    </div> --}}
    <div class="content">
        @if ($content)
            {!! $content !!}
        @endif
    </div>
</body>

</html>
