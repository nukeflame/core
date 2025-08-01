@extends('layouts.intermediaries.base')
@section('header', 'Claims')
<style type="text/css">
    .br span{
        border-radius: 50%;
        padding: 20px;
        text-align:center;
        border: 1px solid;
        color: #b3d9ff;
    }
    .q-link{
        text-decoration: none;
    }

    .q-link p{
        color: black;
    }
    .br span:hover{
        color: #3399ff;
    }
</style>
@section('content')

@if ($message = Session::get('success'))

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


    @component('components.card')    
        
        @slot('class')
           mx-3
        @endslot

        @slot('title')
        Quotation
        @endslot
             <div class="row">
                <div class="col-12 text-center">
                    <h3 class="fw-bold">Choose a product to get a quote.</h3>
                    <hr>
                    <br>
                </div>
                <div class="col-md-3 text-center">
                    <a href="{{route('agent.quot_motorlst')}}" class="q-link">
                        <div class="br">
                            <span class="fa fa-car fa-3x"></span><br>
                        </div>
                        <p>Motor insurance.</p>
                    </a>
				</div>
                <div class="col-md-3 text-center">
                    <a href="{{route('agent.quot_non_motorlst',['category'=>'N'])}}" class="q-link">
                        <div class="br">
                            <span class="fa fa-shop fa-3x"></span><br>
                        </div>
                        <p>Non-motor insurance.</p>
                    </a>
				</div>
                <div class="col-md-3 text-center">
                    <div class="br">
					    <span class="fa fa-universal-access fa-3x"></span><br>
                    </div>
					<p>Medical insurance.</p>
				</div>
                <div class="col-md-3 text-center">
                    <div class="br">
					    <span class="fa fa-people-line fa-3x"></span><br>
                    </div>
					<p>Life assurance.</p>
				</div>
                    
            </div>


    @endcomponent
    <br><br>

    

        <div class="container">
            <div class="table-responsive">
                
                <table class="table table-striped table-borderless table-hover" id="quotation_data_table">
                    <thead >
                        <tr>
                            <td>Quote number</td>
                            <!-- <td>Location</td>                        -->
                            <td>Class</td>   
                            <td>Quote Validity</td>
                            <td>Expected Amount</td>
                            <td>Quote Date</td>
                            <td>Status</td>            
                        </tr>
                    </thead>
                
                    <tbody>
                        
                    </tbody>
                    
                </table>
            </div> 
        </div>
    
  


    @endsection
    @section('page_scripts')
   
    <script>
      
        $(document).ready(function () {
            
            $("#myInput").on("change", function() {
            var value = $(this).val().toLowerCase();

            $("#data-table > tbody > tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
            });
            $("#quot_details").hide();
            $('#quotation_data_table').DataTable({
                processing: true,
                serverSide: true,
                ajax:'{!! route('Agent.qoute.get')!!}',
                columns: [
                    {data:'quote_no',name:'quote_no'},
                    // {data:'location',name:'location'},
                    {data:'class_rec',name:'class_rec'},
                    {data:'quote_validity',name:'quote_validity'},
                    {data:'total',name:'total',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'quote_date',name:'quote_date'},
                    {data:'status',name:'status'},
                    //  {data:'namelink',name:'namelink'},
                   
                    ]		
		    });
            $('#quotation_archived_table').DataTable({
                processing: true,
                serverSide: true,
                ajax:'{!! route('Agent.archive.get')!!}',
                columns: [
                    {data:'quote_no',name:'quote_no'},
                    {data:'location',name:'location'},
                    {data:'class_rec',name:'class_rec'},
                  
                    {data:'quote_validity',name:'quote_validity'},
                    {data:'total',name:'total',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'quote_date',name:'quote_date'},
                    {data:'status',name:'status'},
                     {data:'namelink',name:'namelink'},
                   
                    ]		
		    });
           

        });
        $('#quotation_data_table').on('click', 'tbody td', function() {

            var qtno = $(this).closest('tr').find('td:eq(0)').text();
            window.location="{{route('Agent.view_quote', '')}}"+"/"+qtno;

        })

        
   


        
      

   
 
    </script>
    <style>
   
    </style>
    @endsection
  