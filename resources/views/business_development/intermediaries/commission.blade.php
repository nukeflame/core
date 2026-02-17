@extends('layouts.intermediaries.base')
@section('content')
    
    <div class="mt-3">
        @if ($message = Session::get('success'))
        {{--  <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>  --}}
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Commission Summary
                                
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm">
                            <div>
                                <small>Total Amount</small>
                            </div>
                        </div>
                        <div class="col-sm">
                                <div>
                                <h6 class="text-success font-weight-bold"> <span id="basic_premium">{{number_format(0,2)}}</span> </h6>
                                </div>
                        
                        </div>
                    
                    
                    </div>
                    <hr>
                    <div class="row">
                        
                        <div class="col-sm">
                            <div>
                                <small>Total Earned</small>
                            </div>
                            

                        </div>
                        <div class="col-sm">
                            
                            <div>
                                <h6 class="text-success font-weight-bold" > <span id="benefit_total">{{number_format(0,2)}}</span> </h6>
                            </div>
                            <!-- <div>
                            <h6 class="text-success font-weight-bold">Mlw. <span id="total_premium"></span></h6>
                            </div> -->

                        </div>
                        
                    </div>
                    <hr>
                    <div class="row">
                        
                        <div class="col-sm">
                            <div>
                                <small>Total Paid</small>
                            </div>
                            

                        </div>
                        <div class="col-sm">
                            
                            <div>
                                <h6 class="text-success font-weight-bold" ><span id="">{{number_format(0,2)}}</span> </h6>
                            </div>
                            

                        </div>
                        
                    </div>
                    <hr>
                    <div class="row">
                        
                        <div class="col-sm">
                            <div>
                                <small>Unpaid Commission</small>
                            </div>
                            

                        </div>
                        <div class="col-sm"
                            <div>
                            <h6 class="text-success font-weight-bold"><span id="total_premium">{{number_format(0,2)}}</span></h6>
                            </div>

                        </div>
                        
                    </div
                    
                    
                </div>
            </div>
        </div>
     

    </div>
    

    <div class="card  mt-5 border border-secondary">
        <div class="card-header">
            <h5 class="text-secondary">Commission Details</h5>
        </div>
        <div class="table-responsive card-body">
            
            <div>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="#allcomms" class="nav-link active text-bold" role="tab" data-toggle="tab">Total Commission</a>
                    </li>
                   
                    <li class="nav-item">
                        <a href="#earned_comm" class="nav-link text-bold" role="tab" data-toggle="tab">Earned Commission </a>
                    </li>
<!--                    
                    <li class="nav-item">
                        <a href="#paidcomm" class="nav-link text-bold" role="tab" data-toggle="tab">Paid Commission </a>
                    </li> -->
                </ul>
            </div>

            <div class="tab-content mt-4">
                <div class="tab-pane fade show active" id="allcomms">
                <div class="row">
                    <div class="pull-left"><br>

                                <a  href="{{ route('commission.downloadpdf')}}" class="btn btn-sm btn-outline-success"  role="button"><i class="fa fa-arrow-down" aria-hidden="true"></i>  Export Statement </a>
                    </div>
                </div><br>
                    <div class="row">
                    <table class="table table-striped table-borderless table-hover" id="commission_statement">
                        <thead>
                            <tr>
                                <th>Date Processed</th>
                                <th>Business</th>                     
                                <th>Policy Number</th>                       
                                <th>Endorsement Number</th>                       
                                <th>Gross Commission</th> 
                                <th>Vat on Commission Amount</th> 
                                <th>Nett Commission</th> 
                                <th>Commission Earned</th>                       
                                <th>Commission Paid</th>                                          
                            </tr>
                        </thead>
                    
                        <tbody>
                            
                        </tbody>
                        
                    </table>
                    </div>
                  
                </div>
                <div class="tab-pane fade show" id="earned_comm">
                    <!--  -->
                    <div class="row">
                        <div class="pull-left"><br>
                        <a  href="{{ route('commission.downloadpdf')}}" class="btn btn-sm btn-outline-success"  role="button"><i class="fa fa-arrow-down" aria-hidden="true"></i>  Export Statement </a>
                        </div>
                    </div><br>
                    <div>
                        <table class="table table-striped table-borderless table-hover" id="commission_statement_earned" width="100%">
                            <thead>
                                <tr>
                                    <td>Date Processed</td>
                                    <td>Business</td>                     
                                    <td>Policy Number</td>                       
                                    <td>Endorsement Number</td>                       
                                    <td>Gross Commission</td> 
                                    <td>Vat on Commission Amount</td> 
                                    <td>Nett Commission</td> 
                                    <td>Commission Earned</td>                       
                                    <td>Commission Paid</td>                                          
                                </tr>
                            </thead>
                        
                            <tbody>
                                
                            </tbody>
                            
                        </table>
                    </div>
                  
                </div>
               
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
   
    <script>
    
        $(document).ready(function () {
            $('#commission_statement').DataTable({
                processing: true,
                serverSide: true,
                ajax:'{!! route('commission.data')!!}',
                columns: [
                    {data:'date_effective',name:'date_effective'},
                    {data:'bus', name:'bus'},
                    {data:'policy_no',name:'policy_no'},
                   {data:'endt_renewal_no',name:'endt_renewal_no'},
                    {data:'comm_amt',name:'comm_amt',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'vat_on_comm_amount',name:'vat_on_comm_amount',render: $.fn.dataTable.render.number( ',', '.', 2),defaultContent:'0.00'},
                    {data:'nett_amt',name:'nett_amt',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'comm_earned',name:'comm_earned',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'comm_paid',name:'comm_paid',render: $.fn.dataTable.render.number( ',', '.', 2)}
                    ]		
		    });
            $('#commission_statement_earned').DataTable({
                processing: true,
                serverSide: true,
                ajax:'{!! route('commission.erned')!!}',
                columns: [
                    {data:'date_effective',name:'date_effective'},
                    {data:'bus', name:'bus'},
                    {data:'policy_no',name:'policy_no'},
                   {data:'endt_renewal_no',name:'endt_renewal_no'},
                    {data:'comm_amt',name:'comm_amt',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'vat_on_comm_amount',name:'vat_on_comm_amount',render: $.fn.dataTable.render.number( ',', '.', 2),defaultContent:'0.00'},
                    {data:'nett_amt',name:'nett_amt',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'comm_earned',name:'comm_earned',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'comm_paid',name:'comm_paid',render: $.fn.dataTable.render.number( ',', '.', 2)}
                    ]		
		    });
		});
	
    </script>
@endsection
