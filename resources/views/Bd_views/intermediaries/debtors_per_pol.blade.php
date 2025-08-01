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
    

    <h3 class="fw-light text-center text-lg-start">Policy Transactions</h3>
    <hr>
        <div class="row float-right mr-3 mb-2">
            <div class="float-right">
                    <a href="{{ route('downloadpdf')}}" class="btn btn-success float-right" role="button">Export Pdf</a>
            </div>
        </div>
    <div class="card  mt-2">
        <div class="table-responsive card-body">
            <table class="table table-striped table-bordered table-hover" id="debtors_statement">
                <thead class="">
                    <tr>
                       <th>Transaction Date</th> 
                        <th>Date effective</th>
                        <th>Policy No</th>
                        <!-- <th>Branch</th> 
                        <th>Agent</th>                        -->
                        <th>Endorsement_no</th>                       
                        <th>Reference</th>                       
                        <th>Document Type</th>                       
                        <th>Entry Type</th>                       
                        <th>Unallocated</th>                       
                        <th>Allocated</th>   
                        <th>Nett</th> 
                     
                
                                              
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
                ajax:"{!! route('agent.debtor_pol_datatable',['policy_no'=>$policy]) !!}",
                columns: [
                    {data:'transaction_date',name:'transaction_date'},
                    {data:'date_effective',name:'date_effective'},
                    {data:'policy_no',name:'policy_no'},
                    {data:'endt_renewal_no',name:'endt_renewal_no'},
                    // {data:'branch',name:'branch'},
                    // {data:'agent',name:'agent'},
                    {data:'reference',name:'reference'},
                    {data:'doc_type',name:'doc_type'},
                    {data:'entry_type_descr',name:'entry_type_descr'},
                    {data:'unallocated',name:'unallocated',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'allocated',name:'allocated',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'nett',name:'nett',render: $.fn.dataTable.render.number( ',', '.', 2)}
                   
                   
                    ]		
		    });
		});
	
    </script>
        <style>
    blockquote {
        border-left: 5px solid #000000;
    }
    p {
    color: black;
    }
    </style>
  
    
@endsection


