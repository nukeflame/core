@extends('layouts.intermediaries.base')
@section('header', 'EDIT USER')
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

    <div class="card">
        <div class="card-body">
            <div class="row">  
                <div class="col-sm-12">  
                    @if($data->complete_flag == "N")
                        @if($data->status != "PA")
                        @if(trim($class->motor_flag) == 'Y')
                            @if($source == 'client')
                            <a class="btn btn-sm btn-outline-success"  role="button"  href="{{route('agent.add_risk_quote',['qstring'=>Crypt::encrypt('client='.$client_no.'&source=client&motorflag=Y&quote_no='.$data->quote_no.'')])}}"><i class="fa fa-edit" aria-hidden="true"></i>  Change Quote  </a>  
                            @else
                            <a class="btn btn-sm btn-outline-success"  role="button"  href="{{route('agent.add_risk_quote',['qstring'=>Crypt::encrypt('lead='.$client_no.'&source=lead&motorflag=Y&quote_no='.$data->quote_no.'')])}}"><i class="fa fa-edit" aria-hidden="true"></i>  Change Quote  </a>  
                            @endif

                        @else
                            @if($source == 'client')
                            <a class="btn btn-sm btn-outline-success"  role="button"  href="{{route('add_risk_quote_nonmotor',['qstring'=>Crypt::encrypt('client='.$client_no.'&source=client&motorflag=N&quote_no='.$data->quote_no.'')])}}"><i class="fa fa-edit" aria-hidden="true"></i>  Change Quote  </a>  
                            @else
                            <a class="btn btn-sm btn-outline-success"  role="button"  href="{{route('add_risk_quote_nonmotor',['qstring'=>Crypt::encrypt('lead='.$client_no.'&source=lead&motorflag=N&quote_no='.$data->quote_no.'')])}}"><i class="fa fa-edit" aria-hidden="true"></i>  Change Quote  </a>  
                            @endif

                        @endif
                           
                            <a class="btn btn-sm btn-outline-success"  role="button" id="commit" onclick="commit()"><i class="fa fa-check fa-lg" aria-hidden="true"></i>  Mark as Complete  </a> 
                        @endif
                    @else
                        @if($data->status != 'ACT' && $data->status != 'EXP')
                            <a @if($data->status =="EXP") onclick="cantedit()" @else  href="{{ route('dwnload.quotepdf',[$data->quote_no,$data->version])}}" @endif class="btn btn-sm btn-outline-success"  role="button" target="_blank"><i class="fa fa-arrow-down" aria-hidden="true"></i>  Download </a>
                            <a @if($data->status =="EXP") onclick="cantedit()" @else  href="{{ route('mail.quote',[$data->quote_no,$data->version])}}" @endif class="btn btn-sm btn-outline-success"  role="button"><i class="fa fa-envelope" aria-hidden="true"></i> Send to Mail</a>
                            @if($data->status != 'PPA')
                            <a @if($data->status =="EXP") onclick="cantedit()" @else  href="{{route('quotedocs',['qstring'=>Crypt::encrypt('quote_no='.$data->quote_no.'&source='.$source.'')])}}" @endif class="btn btn-sm btn-outline-success"  id="make_payment" role="button"><i class="fa fa-book"></i> Upload Documents</a>
                            <a @if($data->status =="EXP") onclick="cantedit()" @else  href="{{route('quotePayment',['qstring'=>Crypt::encrypt('quote_no='.$data->quote_no.'&source='.$source.'')])}}" @endif class="btn btn-sm btn-outline-success"  id="make_payment" role="button"><i class="fa fa-dollar"></i> Make payment</a>
                            @endif
                            <a @if($data->status =="EXP") onclick="cantedit()" @else  href="{{ route('genetate.pol',[$data->quote_no,$data->version])}}" @endif class="btn btn-sm btn-outline-success"  role="button"><i class="fa fa-spinner mr-4" aria-hidden="true"></i>Generate Policy</a>

                        @endif
                    @endif

                </div>
                    <!--  -->
                    <div class=" mt-3">
                        <div class="mb-0 pb-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless table-striped">
                                        <tr>
                                            <th>Quotation Number</th>
                                            <td>{{$data->quote_no}}</td>
                                            
                                        </tr>
                                    
                                        <tr>
                                        <th>Quote date</th>
                                            <td>
                                                <?php 
                                                $date = new DateTime($data->quote_date);
                                                $date=date_format($date,'Y-m-d');
                                                    
                                                $date=date("F jS, Y", strtotime($date));
                                
                                                echo $date;

                                            
                                                ?>
                                            </td>
                                        </tr>
                                    
                                        <tr>
                                            <th>Expiry date</th>
                                            <td>    
                                                <?php 
                                                $date = new DateTime($data->quote_date);
                                                $date->modify("+". (int)$data->quote_validity."day");
                                                $date=date_format($date,'Y-m-d');
                                                    
                                                $date=date("F jS, Y", strtotime($date));
                                
                                                echo $date;

                                            
                                                ?>
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <th>Version</th>
                                            <td>    
                                            {{$data->version}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status </th>
                                            <td><b style="color:#AB162B">
                                                @foreach($status as $state)
                                                    @if($state->status_code == $data->status)
                                                        {{$state->description}}
                                                        @break
                                                    @endif
                                                @endforeach
                                            </b></td>
                                        </tr>
                                        
                                        @if(trim($data->status) == 'ACT')
                                        <tr>
                                            <th>PolicyNumber </th>
                                            <th><i>{{$data->policy_no}}</i></th>
                                        </tr>
                                        @endif
                                    
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless table-striped">
                                        <tr>
                                            <th>Currency</th>
                                            <td> {{$curr->short_description}}</td>
                                        </tr>
                                    
                                        <tr>
                                            <th>Basic Premium</th>
                                            <td>{{number_format($basic_prem,2)}}</td>
                                        </tr>
                                    
                                        <tr>
                                            <th>Benefits Premium</th>
                                            <td>{{number_format($sections_prem,2)}}</td>
                                        </tr>
                                        <tr>
                                            <th>Discount</th>
                                            <td>{{number_format($data->discount*-1,2)}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Premium</th>
                                            <td>{{number_format($basic_prem+$sections_prem-$data->discount,2)}}</td>
                                        </tr>
                                    
                                    </table>
                                </div>
                                <br><br>  
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <nav>
                <div class="nav nav-tabs mt-3" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">History</button>
                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Risk Details</button>
                    <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Benefits</button>
                    <!-- <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-payment" type="button" role="tab" aria-controls="nav-payment" aria-selected="false">Payment Details</button> -->


                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade table-responsive" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                    <table class="table table-striped table-hover" width="100%" id="benefits_data_table">
                        <thead >
                            <tr>
                                <td>Code</td>
                                <td>Description</td>                       
                                <td>Amount</td>                 
                            </tr>
                        </thead>

                        <tbody>
                            
                        </tbody>

                    </table>
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <!--  -->
                    <div class="mt-2"> 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        @if(trim($class->motor_flag) == 'Y')
                                        <h5>Vehicles Covered</h5>
                                        <hr>
                                            @foreach($vehicles as $veh)
                                            
                                                <p>
                                                    <i class="fa fa-car"></i><span class="mx-1">{{ $loop->iteration }}. {{ $veh->reg_no }}: &nbsp; &nbsp; &nbsp;</span>{{ $veh->make }} <span class="fw-bold">&nbsp; &nbsp;</span> {{ $veh->model }}<span class="fw-bold">&nbsp; &nbsp;</span> <small class="fw-bold">Sum Insured:</small> {{ number_format($veh->sum_insured,2) }}
                                                </p>
                                                <hr>
                                            @endforeach
                                            {{$vehicles->links()}}
                                        @else
                                        <h5>Sections Covered</h5>
                                        <hr>
                                            @foreach($sections as $section)
                                                <p>
                                                    <span class="mx-1">{{ $loop->iteration }}.<small class="fw-bold">Location:</small>  {{ $section->location }}: &nbsp; &nbsp; &nbsp;<small class="fw-bold">Section:</small>  {{ $section->section_description }}: &nbsp; &nbsp; &nbsp;<small class="fw-bold">Sum Insured:</small> {{ number_format($section->sum_insured) }}&nbsp; &nbsp;</span> <small class="fw-bold">Premium:</small> {{ number_format($section->premium) }}
                                    
                                                </p>
                                                <hr>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!--  -->

            </div>
            
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                                <div class="panel-body table-responsive">
                                    <table class="table table-striped table-hover" style="border-collapse:collapse;">

                                        <thead>
                                            <tr><th>&nbsp;</th>
                                                <th>Quote Number </th>
                                                <th>Number Of Versions</th>
                                                <th>Class</th>
                                                <th>Expiry Date</th>
                                                <th>Total Sum/Liability</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr data-toggle="collapse" data-target="#demo1" class="accordion-toggle">
                                                <td><button class="btn btn-default btn-xs"><span><i class="fa fa-eye" aria-hidden="true"></i></span></button></td>
                                                <td>{{$data->quote_no}}</td>
                                                <td>{{$data->version}}</td>
                                                <td>{{$class->class_description}}</td>
                                                <td>{{$expiry_date}}</td>
                                                <td>{{number_format($data->sum_insured,2)}}</td>

                                            </tr>
                                            <tr>
                                                <td colspan="12" class="hiddenRow">
                                                    <div class="accordian-body collapse" id="demo1"> 
                                                        <table class="table table-striped table-bordered table-hover" width="100%" id="qot_data_table">
                                                            <thead  >
                                                                <tr>
                                                                    <td>Quote number</td>
                                                                    <td>Version</td>                       
                                                                    <td>Class</td>   
                                                                    <td>Expiry Date</td>
                                                                    <td>Quote Date</td>
                                                                    <!-- <td>Status</td>
                                                                    <td>Action</td>                     -->
                                                                </tr>
                                                            </thead>
                                
                                                            <tbody>
                                                                
                                                            </tbody>
                                    
                                                        </table>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>   
                        </div> 
                    </div>           
                    <!--  -->            
            </div>
            <div class="tab-pane fade" id="nav-payment" role="tabpanel" aria-labelledby="nav-payment-tab">
                    @component('components.table')   
                        @slot('id')
                        payments_table     
                        @endslot 

                        @slot('class')
                            
                        @endslot

                        @slot('header')
                        <tr>
                            <th>Payment Date</th>
                            <th>Order number</th>

                            <th>Payment Method</th>                       
                            <th>Confirmation Code</th>
                            <th>Paid Amount</th>
                            <th>Status</th>
                                                
                        </tr>
                        @endslot

                    @endcomponent
            </div>
        </div>
    </div>


 
@endsection
@section('page_scripts')

<!-- diable future date in date input fild -->
<script src="{{ asset('admincast/js/disable_date.js') }}"></script>


<script>


  
    $(document).ready(function () {
             
        $('#discounts-table').DataTable({         
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('Agent.qouterisk.get')}}",
                type: "get",
                data: function (d) {
                    d.quote_no = "{{$data->quote_no}}";
                },
            },
            columns: [
                {data:'make',name:'make'},
                {data:'reg_no',name:'reg_no'},        
                // {data:'rate',name:'rate'},
                {data:'sum_insured',name:'sum_insured',render: $.fn.dataTable.render.number( ',', '.', 2)},
                {data:'annual_prem',name:'annual_prem',render: $.fn.dataTable.render.number( ',', '.', 2)},
                {data:'quote_date',name:'quote_date'},
                {data:'namelink',name:'namelink'}
            ]		
        });

                $('#qot_data_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                                    url: "{{route('view.versions')}}",
                                    type: "get",
                                    data: function (d) {
                                        d.quote_no = "{{$data->quote_no}}";
                                    },
                                },
                        columns: [
                            {data:'quote_no',name:'quote_no'},
                            {data:'version',name:'version'},
                            {data:'class_rec',name:'class_rec'},
                            {data:'valid',name:'valid'},
                            // {data:'total',name:'total',render: $.fn.dataTable.render.number( ',', '.', 2)},
                            {data:'quote_date',name:'quote_date'},
                            // {data:'status',name:'status'},
                            // {data:'action',name:'action'},
                        
                            ]		
                });
                $('#benefits_data_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{route('view.benefits')}}",
                            type: "get",
                            data: function (d) {
                                d.quote_no = "{{$data->quote_no}}";
                            },
                        },
                        columns: [
                            {data:'section',name:'section'},
                            {data:'description',name:'description'},
                            {data:'premium',name:'premium',render: $.fn.dataTable.render.number( ',', '.', 2)},
                         
                            ]		
                });
                $('#payments_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{route('agent.quote.pay')}}",
                        type: "get",
                        data: function (d) {
                            d.quote = "{{$data->quote_no}}";
                        },
                    },
                    columns: [
                        {data:'created_at',name:'created_at'},
                        {data:'order_no',name:'order_no'},
                        {data:'payment_method',name:'payment_method'},
                        {data:'confirmation_code',name:'confirmation_code'},
                        {data:'paid_amount',name:'paid_amount',render: $.fn.dataTable.render.number( ',', '.', 2)},
                        {data:'message',name:'message'}
                        
                    ]		
                });
                $('#qot_data_table').on('click', 'tbody td', function() {

                    var qtno = $(this).closest('tr').find('td:eq(0)').text();
                    var version = $(this).closest('tr').find('td:eq(1)').text();
                    var flag="Y"
                    window.location="{{route('agent.quotepdf', '')}}"+"/"+qtno+"/"+version;

                })
                $('#data-table').DataTable({
                        
                        processing: true,
                        serverSide: true,
                        ajax: {
                                        url: "{{ route('Agent.section.get')}}",
                                        type: "get",
                                        data: function (d) {
                                            d.quote_no = "{{$data->quote_no}}";
                                        },
                                },
                        columns: [
                            {data:'description',name:'description'},
                            // {data:'rate',name:'rate'},        
                            {data:'amount',name:'amount',render: $.fn.dataTable.render.number( ',', '.', 2)}
                            ]		
                });
           
        
               


    });
    function cantedit(e){
        
        swal.fire({
            icon: "warning",
            title: "Warning",
            html:"<h6 class='text-success'>The current quotation status doesnt allow editing</h6>"
        })
    }
    function commit(e){
        Swal.fire({
        // title: "Save Quote",
        html: "Are You Sure You Want to Complete this Quotation?",
        icon: "warning",
        confirmButtonText: "Yes",
        showCancelButton: true,
        cancelButtonColor: '#d33'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'GET',
                    data:{'quoteno':"{{$data->quote_no}}"},
                    url: "{!! route('agent.markcomplete')!!}",
                    success: function(response) {
                        if(response ==1){
                            Swal.fire({
                                text: "Quotation Completed",
                                icon: "success"
                            }).then(function(result) {
                                window.location.reload();
                            });
                        }else{
                            Swal.fire({
                                text: "Failed. Please check quotation details and try again",
                                icon: "error"
                            });
                        }
                       
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                        title: "Error",
                        text: textStatus,
                        icon: "error"
                        });
                    }
                });
            }
        })
    }
    $('#reject').on('click',function(){
        var quote ='{{$data->quote_no}}'
        $(this).html('Rejecting...');
        $.ajax({
            data:{'quote':quote,'reject_flag':"Y"},
            type: 'GET',
            url: "{!! route('admin.approve')!!}",
            success:function(data){
                if(+data ==1 ){
                   
               
                    swal.fire({
                        icon: "success",
                        title: "Success",
                        html:"<h6 class='text-success'>Quotation Rejected</h6>"
                    })
                    window.location.reload(); 
                    
                }else{
                    swal.fire({
                        icon: "error",
                        title: "Error",
                        html:"<h6 class='text-danger'>Process failed</h6>"
                    })
                    $(this).html('Reject');    

                }
              
                      
               
               
            }
              });
    })   
 



</script>
<style>
    .bs-example {
        margin: 20px;
    }
    .form-control {
    width:150px;
}
pre{
    display: block;
    /* font-family: monospace; */
    font-family: Arial, Helvetica, sans-serif;
    padding: 9.5px;
    margin: 0 0 10px;
    font-size: 15px;
    font-weight: bold;
    line-height: 1.42857143;
    color: #3e72c7;
    word-break: break-all;
    word-wrap: break-word;
    background-color: #ffffff;
    border: 1px solid #ccc;
    border-radius: 4px;
}

</style>

@endsection


