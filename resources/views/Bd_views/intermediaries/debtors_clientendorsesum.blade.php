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
   
    
    <h3 class="fw-light text-center text-lg-start">Endorsement Listings</h3>
    <hr>
    
    
    <div class="row float-right mr-3 mb-2">
            <div class="float-right"><br>
                        <a   class="btn btn-sm btn-default"  onclick="goBack()"  role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back </a>
            </div>
        </div>


    <div class="card  mt-5 border border-secondary">
        <div class="table-responsive card-body">
            <table class="table table-striped table-bordered table-hover" id="debtors_statement_endt">
                <thead >
                    <tr>
                        <td>Policy Number</td>                                        
                        <td>Endorsement Number</td>                                        
                        <td>Outstanding Transactions</td>                       
                        <td>Effective Date</td>                       
                        <td>Total Outstanding Amount</td>
                        <td>Age(Days)</td>

                                     
                                        
                                              
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
            var policy_no = "{{$policy_no}}";

           $('#debtors_statement_endt').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                            url: "{!! route('endorsedebtlist') !!}",
                            data: function (d) {
                                d.policy_no = policy_no;
                            }
                    },
                columns: [
                    {data:'policy_no',name:'policy_no'}, 
                    {data:'endt_renewal_no',name:'endt_renewal_no'}, 
                    {data:'document_type',name:'document_type'},
                    {data:'date_effective',name:'date_effective'},
                    {data:'unallocated',name:'unallocated',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'range',name:'range'},


                  
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


