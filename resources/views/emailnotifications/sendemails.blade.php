<html>

<head>
    <style>
        body {
            font-family: Verdana, Geneva, Tahoma, sans-serif;

        }
    </style>
</head>

<body styled="background: black; color: light">
    {{-- <b>{{$title}}</b> --}}
    @if ($content->recipient_name != null)
        <p>Dear {{ $content->recipient_name }},</p>
    @else
        <p>Greetings, </p>
    @endif
    <div style="align-content: left">
        <p style="background: #F8F8F8;border:1px solid #F5F5F5; border-radius:5px;">
            {!! $content->body !!}
            <br>
            <hr>
        <p class="well" style="font-size:0.75rem"> <b> Created by </b> <i>{{ $content->created_by }}</i> on <i>
                {{ $createdate }} </i> </p>
        </p>
    </div>

</body>

</html>
