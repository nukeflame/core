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
   
    
    <h3 class="fw-light text-center text-lg-start">Debtors statement</h3>
    <hr>
    
    
    <div class="row float-right mr-3 mb-2">
            <div class="float-right"><br>
                        <a  href="{{ route('agent.downloadpdf')}}" class="btn btn-sm btn-outline-success"  role="button"><i class="fa fa-arrow-down" aria-hidden="true"></i>  Export Statement </a>
            </div>
        </div>

<table class="table  table-bordered">
  <thead class="bg-sum">
    <tr>
      <th scope="col"></th>
      <th scope="col">Current</th>
      <!-- <th scope="col">Total 30 Days</th> -->
      <th scope="col">Total 60 Days</th>
      <th scope="col">Total 90 Days</th>
      <th scope="col">Total 120 Days</th>
      <th scope="col">Over 120 Days</th>
      <th scope="col">Total</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Summary</th>
      <td>{{number_format($summary->current,2)}}</td>
      <!-- <td>{{$summary->total_30_days}}</td> -->
      <td>{{number_format($summary->total_60_days,2)}}</td>
      <td>{{number_format($summary->total_90_days,2)}}</td>
      <td>{{number_format($summary->total_120_days,2)}}</td>
      <td>{{number_format($summary->over_120_days,2)}}</td>
      <td>{{number_format($summary->total,2)}}</td>
    </tr>
   
  </tbody>
</table>
    <div class="card  mt-5 border border-secondary">
        <div class="table-responsive card-body">
            <table class="table table-striped table-bordered table-hover" id="debtors_statement">
                <thead >
                    <tr>
                        <td>Policy No</td>
                                          
                        <td>Current</td>                       
                                           
                        <td>Total 60 Days</td>                       
                        <td>Total 90 Days</td>                       
                        <td>Total 120 Days</td>   
                        <td>Over 120 Days</td> 
                        <td>Total</td>
                        <td>Action</td>                       
                                        
                                              
                    </tr>
                </thead>
            
                <tbody>
                    
                </tbody>
                
            </table>
        </div>
    </div>


@endsection
 @section('page_scripts')
     <script type="text/javascript">
			
        $(document).ready(function () {
           $('#debtors_statement').DataTable({
                processing: true,
                serverSide: true,
                ajax:'{!! route('agent.DebtorsStat')!!}',
                columns: [
                    {data:'policy_no',name:'policy_no'},
                    // {data:'branch',name:'branch'},
                    // {data:'agent',name:'agent'},
                   
                    {data:'current',name:'curr'},
             
                    {data:'total_60_days',name:'total_60_days',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'total_90_days',name:'total_90_days',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'total_120_days',name:'total_120_days',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'over_120_days',name:'total_150_days',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'total',name:'total',render: $.fn.dataTable.render.number( ',', '.', 2)},
                     {data:'namelink',name:'namelink'},
                    // {data:'total_240_days',name:'total_240_days'},
                    // {data:'total_270_days',name:'total_270_days'},
                    // {data:'total_300_days',name:'total_300_days'},
                    // {data:'total_330_days',name:'total_330_days'},
                    // {data:'total_365_days',name:'total_365_days'},
                    // {data:'over_365_days',name:'over_365_days'}
                    ]		
		    });
		});
	
    </script>
    <style>
    blockquote {
        border-left: 5px solid #000000;
    }
    .bf-sm {
    color: #cfd7e0;
    }
    </style>
  
    
@endsection


