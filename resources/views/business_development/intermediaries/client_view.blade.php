
@extends('layouts.intermediaries.base')
@section('header', 'EDIT USER')
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
<br />
@if ($message = Session::get('success'))

@endif
   @if($serviceflag <> "Y")
    <div class="row">
            <div class="pull-left col-md-6">
                        <a  href="{{ route('add_risk_quote_nonmotor', ['qstring' => Crypt::encrypt('client=' . $client->global_customer_id . '&source=client&motorflag=N&type=POL&categ=N&quote_no=' . null)]) }}" class="btn btn-outline-success " type="button">
                        New Policy
                         </a>

            </div>

            <div class="col-md-4"></div>
            <div class="pull-right col-md-2">



            </div>
    </div>
    @endif





<!--  -->
<div class="card mt-3 border border-secondary">
    <div class="card-header">
    <strong>Client Details</strong>
    <button id="client_view" style="margin-left:10px" class="btn btn-sm border border-primary text-primary">
        <i class="fa fa-eye"></i>
        View more
    </button>
    </div>
    <div class="card-body mb-0 pb-0">
        <div class="row">
            <table class="table  ">
                <tr>
                    <th>Client Name</th>
                    <td>{{$client->full_name}}</td>
                    <th>Client Email</th>
                    <td>{{$client->email}}</td>

                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td>{{$client->phone_1 }}</td>
                    <th>Client Number</th>
                    <td>{{$client->global_customer_id }}</td>
                    <!-- <th>Integration status</th> -->
                    <!-- @if(is_null($client->lob_customer_id ))
                        <td class="text-danger fw-bold">Not Integrated
                            @if(auth()->user()->hasRole('admin'))
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <button class="btn btn-sm btn-success" id="integrate_client">Integrate</button>
                            @endif
                        </td>
                    @else
                        <td class="text-success fw-bold">Integrated</td>
                    @endif -->
                </tr>

                <!-- <tr>
                    <th>Outstanding Balance</th>
                    <td>0.0</td>
                    <th>Status </th>
                    <td><p class="text-info">Active<p></td>
                </tr> -->


            </table>

            <br><br>

        </div>
    </div>
</div>
<!--  -->




<div class="card mt-3">
    <div class="bs-example">
        <ul class="nav nav-tabs">
        @if($serviceflag <> "Y")
            <li class="nav-item">
                <a href="#policies" class="nav-link active" data-toggle="tab">Policies</a>
            </li>

            <li class="nav-item">
                <a href="#claims" class="nav-link" data-toggle="tab">Claims</a>
            </li>

            <li class="nav-item">
                <a href="#client_statement" class="nav-link" data-toggle="tab">Client Statement</a>
            </li>
        @else
           <li class="nav-item">
                <a href="#implementation" class="nav-link active" data-toggle="tab">Implementation</a>
            </li>

            <li class="nav-item">
                <a href="#value-addition-items" class="nav-link" data-toggle="tab">Value Addition</a>
            </li>

        @endif


        </ul>
        <div class="tab-content">
          @if($serviceflag <> "Y")
            <div class="tab-pane fade show active" id="policies">
                <div class="card ">
                <div class="table-responsive">
                    @component('components.table')
                        @slot('id')
                        policy-data-table
                        @endslot

                        @slot('class')

                        @endslot

                        @slot('header')
                        <tr>
                            <th>Policy Number</th>
                            <th>Class</th>
                            <th>Period From</th>
                            <th>Period To</th>
                            <th>Sum Insured</th>
                            <th>Renewal Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        @endslot

                    @endcomponent
                </div>
                </div>
            </div>
            <!-- tab 1 ends here -->
            <div class="tab-pane fade" id="claims">
                <div class="card ">


                    <div class="card-body table-responsive">
                        <table class="table table-striped table-hover" id="">
                            <thead >
                                <tr>
                                    <td>No</td>
                                    <td>Policy No</td>
                                    <td>Claim No</td>
                                    <td>Date of Loss</td>
                                    <td>Date Reported</td>
                                    <td>Current Estimates</td>
                                    <td>Class</td>

                                    <td>Status</td>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($claims as $claim)
                                <tr class="clickable" onclick="window.location='{{ route('viewclaims',$claim->claim_ref_no) }}'">
                                    <td>{{$loop->iteration }}</td>
                                    <td>{{$claim->policy_no}}</td>
                                    <td>{{$claim->claim_no}}</td>
                                    <td>{{$claim->date_of_loss}}</td>
                                    <td>{{$claim->date_reported}}</td>
                                    <td>{{$claim->current_estimate}}</td>
                                    <td>{{$claim->classList->class_description}}</td>
                                    <td>
                                        @if ($claim->status == 'OS')
                                            Outstanding
                                        @else
                                            {{$claim->status}}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!-- tab2 ends here -->
            <div class="tab-pane" id="client_statement" role="tabpanel">
                <!--  -->
                    <div class="card  mt-2">
                        <div class="row">
                            <div class="col-sm-6 ">
                                <div class="row">
                                    <div class="col-md-6 offset-md-3 d-flex justify-content-between align-items-center">
                                        <h5 class="text-center">Transaction Listing</h5>
                                        <a href="{{route('debtindex',['client_no' => $clientdata->lob_customer_id])}}" class="btn btn-link">View All</a>
                                    </div>
                                </div>
                                <ul class="list-group list-group-flush" style="margin-top:2.4rem;">
                                    @foreach ($transactions as $transaction)
                                        <li class="list-group-item inbox-item pb-0" id="endo_li">
                                            <div class="inbox-header align-items-center">
                                                <div class="ml-2" style="margin-left:10px">
                                                    <p class="txt-bold mb-0">
                                                            @php
                                                                $classDescription = \App\Models\Admin\Client\ClassList::where('class_code', $transaction->class)->value('class_description');
                                                                @endphp
                                                            {{ $classDescription }}</p>

                                                    </p>
                                                    <div class="row">
                                                    <div class="col-sm-4">
                                                        <p class="text-muted font-small mb-0">Transaction Date<br> {{ \Carbon\Carbon::createFromFormat('Y-m-d',  $transaction->date_effective)->format('Y F jS') }}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <p class="text-muted font-small mb-0">Amount Payable<br> {{ number_format($transaction->nett, 2, '.', ',') }}</p>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <p class="text-muted font-small mb-0">
                                                            @php
                                                                if ($transaction->doc_type == "DRN") {
                                                                    $w_foreign_withheld = $transaction->witheld_amount /$transaction->currency_rate;
                                                                    $w_foreign_unallocated= $transaction->foreign_unallocated - $w_foreign_withheld;

                                                                }else{
                                                                    $w_foreign_unallocated=$transaction->foreign_unallocated;
                                                                }
                                                            @endphp
                                                            Outstanding Balance<br>{{ number_format($w_foreign_unallocated, 2, '.', ',') }}
                                                        </p>
                                                    </div>

                                                    </div>

                                                </div>

                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-sm-6 vertical-divider">
                                <div class="row">
                                    <div class="col-md-6 offset-md-3 d-flex justify-content-between align-items-center">
                                        <h5 class="text-center">Debtors Statement</h5>
                                        <a href="{{route('debtindx',['client_no' => $clientdata->lob_customer_id])}}"class="btn btn-link">View All</a>
                                    </div>
                                </div>
                                <ul class="list-group list-group-flush" style="margin-top:2.4rem;">
                                    @foreach ($debtors as $debtor)
                                        <li class="list-group-item inbox-item pb-0" id="endo_li">
                                            <div class="inbox-header align-items-center">
                                                <div class="ml-2" style="margin-left:10px">
                                                    <p class="txt-bold mb-0">
                                                            @php
                                                                $classDescription = \App\Models\Admin\Client\ClassList::where('class_code', $debtor->class)->value('class_description');
                                                                @endphp
                                                            {{ $classDescription }}</p>

                                                    </p>
                                                    <div class="row">
                                                    <div class="col-sm-4">
                                                        <p class="text-muted font-small mb-0">Transaction Date<br> {{ \Carbon\Carbon::createFromFormat('Y-m-d',  $debtor->date_effective)->format('Y F jS') }}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <p class="text-muted font-small mb-0">Amount Payable<br> {{ number_format($debtor->nett, 2, '.', ',') }}</p>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <p class="text-muted font-small mb-0">
                                                            @php
                                                                if ($debtor->doc_type == "DRN") {
                                                                    $w_foreign_withheld = $debtor->witheld_amount /$debtor->currency_rate;
                                                                    $w_foreign_unallocated= $debtor->foreign_unallocated - $w_foreign_withheld;

                                                                }else{
                                                                    $w_foreign_unallocated=$debtor->foreign_unallocated;
                                                                }
                                                            @endphp
                                                            Outstanding Balance<br>{{ number_format($w_foreign_unallocated, 2, '.', ',') }}
                                                        </p>
                                                    </div>

                                                    </div>

                                                </div>

                                            </div>
                                        </li>

                                    @endforeach
                                </ul>

                            </div>

                        </div>
                    </div>
                    <!--  -->
            </div>
            @else
                <div class="tab-pane fade show active" id="implementation">
                    <div class="card ">
                    <div class="table-responsive">
                         <table id="implementation_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Objective</th>
                                        <th>Prompt Date</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                         </table>
                    </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="value-addition-items">
                    <div class="card ">
                        <div class="card-body table-responsive">
                            <table id="vadd_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Cost</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            @endif


            <!-- tab3 ends here -->
        </div>
    </div>
</div>


</div>






@endsection
@section('page_scripts')
<script src="{{ asset('admincast/js/datatable.js') }}"></script>
<style>
    .bs-example {
        margin: 20px;
    }
</style>
<script>
    $(document).ready(function () {
        //close bankshowmodal
        $("#addBankBtn").click(function () {
            $("#showbModal").modal("hide")
        })
        $('#clnt_quotation_data_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                            url: "{{ route('view.allquotes')}}",
                            type: "get",
                            data: function (d) {
                                d.client_no = "{{$client->global_customer_id}}";
                            },
                        },
                columns: [
                    {data:'quote_no',name:'quote_no'},
                    {data:'company',name:'company'},
                    {data:'class_rec',name:'class_rec'},
                    {data:'quote_date',name:'quote_date'},
                    {data:'total',name:'total',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'valid',name:'valid'},
                    // {data:'status',name:'status'},
                    {data:'action',name:'action'},

                    ]
		});
        $('#implementation_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('getimplementationdata')}}",
                type: "get",
                data: {
                    client_no:"{{$clientdata->global_customer_id}}",
                }
            },
            columns: [
                { data: 'task', name: 'task' },
                { data: 'objective', name: 'objective' },
                { data: 'prompt_date', name: 'prompt_date' },
                { data: 'cstatus', name: 'cstatus' },
                { data: 'action', name: 'action' }
            ]
        });
        $('#vadd_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('getvalueadddata')}}",
                type: "get",
                data: {
                    client_no:"{{$clientdata->global_customer_id}}",
                }
            },
            columns: [
                { data: 'task', name: 'task' },
                { data: 'cost', name: 'cost' },
                { data: 'due_date', name: 'due_date' },
                { data: 'cstatus', name: 'cstatus' },
                { data: 'action', name: 'action' }
            ]
        });
        $('#policy-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pol.index')}}",
                type: "get",
                data: {
                    client_no:"{{$client->global_customer_id}}",
                    service_flag:"{{$serviceflag}}"
                }
            },
            columns: [
                { data: 'policy_no', name: 'policy_no' },
                { data: 'class_rec', name: 'class_rec' },
                { data: 'period_from', name: 'period_from' },
                { data: 'period_to', name: 'period_to' },
                { data: 'total_sum_insured', name: 'total_sum_insured' },
                { data: 'period_to', name: 'period_to' },
                { data: 'pol_status', name: 'pol_status' },
                { data: 'action', name: 'action' }
            ]
        });


        $('#clnt_quotation_data_table').on('click', 'tbody td', function() {
            var qtno = $(this).closest('tr').find('td:eq(0)').text();
            // window.location="/brokerage/intermediary/Agent/quot/view/"+qtno+"/client"
            window.location="{{route('Agent.view_quote', '')}}"+"/"+qtno+"?source=client";

        })
        $('#implementation_table').on('click', 'tbody td', function() {
            var serviceflag ="{{$serviceflag}}";
            var polno = $(this).closest('tr').find('td:eq(0)').text();
              if(serviceflag !="Y"){
                window.location="{{route('view.policies', '')}}"+"/"+polno;

              }else{
                // var encryptedUrl = "{{ route('clientservicing', ['qstring' => Crypt::encrypt('client=' . $client->global_customer_id . '&source=client&motorflag=N&type=POL&categ=N&policy_no=:polno')]) }}";
                //    alert(encryptedUrl)
                // var url = encryptedUrl.replace(':polno', polno);
                // window.location=url;
                $.ajax({
                    url: "{{ route('generate.encrypted.url') }}",
                    method: 'GET',
                    data: {
                        polno: polno,
                        client: "{{$client->global_customer_id}}"
                    },
                    success: function(response) {
                        // Redirect to the dynamically encrypted URL
                        window.location.href = response.url;
                    }
                });

              }

      })


        //edit banks
        $(".editbank").click(function () {
            $("#bankModal").modal("show")
            $("#showbModal").modal("hide")
            var id = $(this).data("id")
            var statuses = '{!! $statuses !!}'
            var status_obj = JSON.parse(statuses);

            $.ajax({
                url: "{{route('editclientbanks') }}",
                type: "get",
                data: {
                    'id': id
                },
                success: function (res) {
                    var data = res.bank
                    //   console.log(data)
                    $("#bank_name").val(data.bank_name)
                    $("#branch").val(data.branch)
                    $("#account_name").val(data.account_name)
                    $("#account_no").val(data.account_no)
                    $("#id").val(data.id)
                    $.each(status_obj, function (key, value) {
                        if (value.status_description == data.status) {
                            $('#status').append('<option value="' + value
                                .status_description + '">' + value
                                .status_description + '</option>')
                        }

                    })

                },
                error: function (err) {
                    console.log(err)

                }




            })
        })

        //edit banks
        $('#view_banks').on('click',function(){

      $('#editbank_modal').modal({backdrop: 'static'});
      $('#editbank_modal').modal('show');

   });
        //end
        //edit associations


        $(".editasso").click(function () {
            $("#assoModal").modal("show")
            var id = $(this).data("id")
            //   alert(id)

        })
    $('#client_view').click(function(){
       window.location = "{{ route('client.view')}}"+"?client={{$client->global_customer_id}}"
    })

    $("#integrate_client").click(function () {
        var global_id = '{!! $client->global_customer_id !!}'

        $.ajax({
            url: "{{route('integrate_client') }}",
            type: "get",
            data: {
                'client_id': global_id
            },
            success: function (res) {
                if (res.status == 1) {
                    toastr.success("Client integrated Successful", {timeOut: 5000})
                    location.reload()
                } else {
                    toastr.error(res.message, {timeOut: 5000})
                }
            },
            error: function (err) {
                    toastr.error("Failed")

            }




        })
    })
    })

</script>
@endsection