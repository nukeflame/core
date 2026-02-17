@extends('layouts.intermediaries.base')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6 col-sm-12">
                    <!-- small box -->
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex justify-content-between">
                                    <div class="media-body text-left mt-2">
                                        <h3 class="text-success"><b>{{$policy_count}}</b></h3>
                                        <span>All Policies</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-file-lines text-success fa-3x float-right"></i>
                                    </div>
                                </div>
                                <div class="progress my-2" style="height: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6  col-sm-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex justify-content-between">
                                    <div class="media-body text-left mt-2">
                                        <h3 class="text-danger"><b>{{$client_count}}</b></h3>
                                        <span>All Clients</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-arrow-trend-down text-danger fa-3x float-right"></i>
                                    </div>
                                </div>
                                <div class="progress my-2" style="height: 5px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6  col-sm-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex justify-content-between">
                                    <div class="media-body text-left mt-2">
                                        <h3 class="text-info"><b>{{$claim_count}}</b></h3>
                                        <span>Active Claims</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-paste text-info fa-3x float-right"></i>
                                    </div>
                                </div>
                                <div class="progress my-2" style="height: 5px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
				<div class="col-lg-3 col-6  col-sm-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex justify-content-between">
                                    <div class="media-body text-left mt-2">
                                        <h4 class="text-success"><b>{{$quotations_count}}</b></h4>
                                        <span>All Quotation</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fa fa-folder-open text-success fa-3x float-right"></i>
                                    </div>
                                </div>
                                <div class="progress my-2" style="height: 5px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mt-3">
                    <div class="card">
                        <div class="card-body m-2">
                            <h5>Quotations Pending Approval</h5>
                            <hr>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Quotation ID</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotations as $quote)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $quote->quote_no }}</td>
                                        <td>{{ $quote->quote_date }}</td>
                                        <td>{{ $quote->status }}</td>
                                    </tr>
                                        @if($loop->iteration >= 5)
                                            @break
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-6 mt-3">
                <div class="card">
                        <div class="card-body m-2">
                            <h5>Claims Notifications Pending Approval</h5>
                            <hr>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Claim Reference Number</th>
                                        <th>Policy Holder</th>
                                        <th>Date Reported</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($claims_pending as $claim)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $claim->claim_ref_no }}</td>
                                        <td>{{ $claim->policy_holder }}</td>
                                        <td>{{ $claim->date_reported }}</td>
                                        <td><a href="{{ route('intermedclaimsNotif.show',$claim->claim_ref_no) }}" class=" text-primary">View</a></td>
                                    </tr>
                                        @if($loop->iteration >= 5)
                                            @break
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                  
                </div>
            </div>
        </div>
    </section>
@endsection