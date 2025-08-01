@extends('printouts.base')

@section('content')
    @foreach ($scheduleHeaders as $hdr)
        <h3>{{ $hdr->name }}</h3>
        <div>
            @foreach ($hdr->schedules as $sched)
                {!! $sched->details !!}
            @endforeach
        </div>
    @endforeach
    {!! $wording !!}
@endsection
