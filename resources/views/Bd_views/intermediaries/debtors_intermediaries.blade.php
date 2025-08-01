@extends('layouts.intermediaries.base')
@section('content')

   
    <div class="">
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
    
    
    <div class="row float-right mr-3 mb-2">
        <div class="float-right"><br>
                    <a  href="{{ route('agent.downloadpdf')}}" target="_blank" class="btn btn-success"  role="button"><i class="fa fa-arrow-down" aria-hidden="true"></i>  Export Statement </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Debtors Statement</h5>
        </div>
        <div class="card-body">
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
                    <td>{{number_format($summary->sum_30_days,2)}}</td>
                    <td>{{number_format($summary->sum_31_to_60_days,2)}}</td>
                    <td>{{number_format($summary->sum_61_to_90_days,2)}}</td>
                    <td>{{number_format($summary->sum_91_to_120_days,2)}}</td>
                    <td>{{number_format($summary->sum_over_120_days,2)}}</td>
                    <td>{{number_format($summary->total_amt,2)}}</td>
                    </tr>
                
                </tbody>
            </table>
        </div>
    </div>


    <div class="card  mt-2">
        <div class="table-responsive card-body">
            <table class="table table-striped table-borderless table-hover" id="debtors_statement">
                <thead class="">
                    <tr>
                        <th>Insured/Client</th>      
                        <th>Outstanding Transactions</th>                       
                        <th>Total Outstanding Amount</th>
                        <th>Action</th>                       
                                        
                                              
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
                ajax:'{!! route('debtclientsummary')!!}',
                columns: [
                    {data:'Insured',name:'Insured'},     
                    //{data:'lob_customer_id',name:'lob_customer_id'},
                    {data:'number_of_trans',name:'number_of_trans'},
                    {data:'total_unallocated',name:'total_unallocated',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'action',name:'action'},

                  
                    ],"order": [[3, 'desc']]	
		    });
		});
        $('#debtors_statement').on('click', 'tbody td', function() {

            var client_no = $(this).closest('tr').find('td:eq(1)').text();
            window.location="{{route('clientdebtlist', '')}}"+"/"+client_no;

        })
	
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


