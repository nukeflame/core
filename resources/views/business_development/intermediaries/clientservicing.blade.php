{{-- @extends('layouts.admincast') --}}
@extends('layouts.intermediaries.base')
{{-- @extends('layouts.intermediaries.base') --}}
@section('header','MENU ITEMS')
@section('content')


<div class="mt-3">
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

<div class="m-2">



    <div class="card table-responsive">
        <div class="card-body">
            <div class="bs-example">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#client_listing" role="tab">
                        Clients Servicing
                        </a>
                    </li>
               
                </ul>
                <div class="tab-content p-3 text-muted">
                    <div class="tab-pane active" id="client_listing">
                        <table class="table table-striped table-hover" id="client-table">
                            <thead>
                                <tr>
                                    <th>Client Number</th>
                                    <th>Full Name</th>
                                    <th>Pin Number</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                
                        </table>
                    </div>
               
                </div>
            </div>
        </div>
    </div>
    

        
      
</div>
@endsection
@section('page_scripts')

<script>
    $(document).ready(function () {
        $('#client_view').on('click',function(){
            console.log("asegrf")
        })
        $("#myInput").on("change", function () {
            var value = $(this).val().toLowerCase();

            $("#client-table > tbody > tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
        $('#client-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('clientserv_get')}}",
                type: "get"
                            
            },
            columns: [
                {data:'global_customer_id',name:'global_customer_id'},
                {data:'full_name',name:'full_name'},
                // {data:'lob_customer_id',name:'lob_customer_id'},    
                {data:'pin_no',name:'pin_no'},
                {data:'email',name:'email'},
                {data:'action'}
                ]		
        });

    

    })

    $('#client-table').on('click', 'tbody td', function() {

        

        var clientno = $(this).closest('tr').find('td:eq(0)').text();
        var service_flag = "Y";
        var url = "{{ route('agent.view.client', ['client' => ':client', 'serviceflag' => ':serviceflag']) }}";
        url = url.replace(':client', clientno).replace(':serviceflag', service_flag);

        // Redirect to the constructed URL
        window.location = url;
        
       
    })
</script>

@endsection
