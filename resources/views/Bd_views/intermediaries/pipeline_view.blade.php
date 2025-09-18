@extends('layouts.app')

@section('styles')
    <style>

    </style>
@endsection

@section('content')
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Sales Management
            </h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/">Business Development</a></li>
                        <li class="breadcrumb-item"><a href="/">Sales Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Facultative</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">Pipeline Details</div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <form id="pip_year_form" action="{{ route('pipeline.view') }}" method="get">
                        <input type="hidden" id="opp_id" name="opp_id">
                        <div class="row">
                            <div class="col-md-3">
                                <x-SearchableSelect id="pip_year_select" req="" inputLabel="Pipeline Year"
                                    name="pipeline">
                                    @foreach ($pipelines as $pip_year)
                                        <option @if ($pip_year->id == $pip) selected @endif
                                            value="{{ $pip_year->id }}">
                                            {{ $pip_year->year }}
                                        </option>
                                    @endforeach
                                </x-SearchableSelect>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row" style="height:300px;">
                    <div class="col-md-8">
                        <div class="ct-chart-ranking ct-golden-section ct-series-a"></div>
                    </div>
                    <div class="col-md-4"></div>
                </div>

                <div class="row">
                    <hr>
                    <div class="d-flex justify-content-center flex-wrap">
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #d70206; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Proposal</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #f05b4f; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Negotiation</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #f4c63d; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Lead</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #d17905; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Won</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #453d3f; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Lost</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <span class="dot rounded-circle me-2"
                                style="background-color: #59922b; width: 12px; height: 12px;"></span>
                            <span class="fw-normal small">Final Stage</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
