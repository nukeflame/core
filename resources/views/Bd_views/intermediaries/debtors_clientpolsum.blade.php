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
    
    <div class="row float-right mr-3">
        <div class="float-right"><br>
                <a   class="btn btn-outline-secondary"  onclick="goBack()"  role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back </a>
        </div>
    </div>


    <div class="card  mt-2">
        <div class="card-header">
            <h5>Policy Summary</h5>
        </div>
        <div class="table-responsive card-body">
            <table class="table table-striped table-borderless table-hover" id="debtors_statement_pol">
                <thead>
                    <tr>
                        <th>Policy Number</th>                                        
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
            var client_no = "{{$client_no}}";

           $('#debtors_statement_pol').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                            url: "{!! route('clientdebtlist') !!}",
                            data: function (d) {
                                // Additional data you want to send to the server can be defined here
                                // For example, you can add extra parameters like this:
                                d.client_no = client_no;
                            }
                    },
                columns: [
                    {data:'policy_no',name:'policy_no'}, 
                    {data:'number_of_trans',name:'number_of_trans'},
                    {data:'total_unallocated',name:'total_unallocated',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'action',name:'action'},

                  
                    ],"order": [[3, 'desc']]	
		    });
		});
        $('#debtors_statement_pol').on('click', 'tbody td', function() {

            var policy_no = $(this).closest('tr').find('td:eq(0)').text();
             window.location="{{route('endorsedebtlist', '')}}"+"/"+policy_no;

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


