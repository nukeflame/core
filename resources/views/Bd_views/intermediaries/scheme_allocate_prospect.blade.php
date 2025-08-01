@extends('layouts.intermediaries.base')
@section('content')
    <div class="mt-2">
        <a href="#" style="color: black"><i class="fa fa-id-badge mx-2"></i></a>
        <a href="#" class=" text-primary">CR Scheme Allocation</a>
        <hr>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-body">
                    <form action="{{ route('schemeAllocations.CRSchemeAllocationSave') }}" method="POST">
                        <div class="row">
                            @csrf

                            <input type="text" value="{{ $prospectId }}" name="prospectId" hidden>

                            <div class="form-group">
                                <label for="staffname">CR Account Handler</label>
                                <select name="cr_handler" id="cr_handler" class="form-control" required>
                                    <option value="">Select CR Account Handler</option>
                                    @foreach ($crHandlers as $crhandler)
                                        <option value="{{ $crhandler->id }}">{{ $crhandler->firstname }} -
                                            {{ $crhandler->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="cr_co_handler">CR CO Account Handler</label>
                                <select name="cr_co_handler" id="cr_co_handler" class="form-control" required>
                                    <option value="">Select CR CO Account Handler</option>
                                    @foreach ($crCoHandlers as $crcohandler)
                                        <option value="{{ $crcohandler->id }}">{{ $crcohandler->firstname }} -
                                            {{ $crcohandler->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="underwritter">Underwritter</label>
                                <select name="underwritter" id="underwritter" class="form-control" required>
                                    <option value="">Select underwritter</option>
                                    @foreach ($underWritters as $underwrite)
                                        <option value="{{ $underwrite->id }}">{{ $underwrite->firstname }} -
                                            {{ $underwrite->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="bd_lead">BD Lead</label>
                                <select name="bd_lead" id="bd_lead" class="form-control" required>
                                    <option value="">Select BD Lead</option>
                                    @foreach ($bd_users as $bdUser)
                                        <option value="{{ $bdUser->id }}">{{ $bdUser->firstname }} -
                                            {{ $bdUser->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
