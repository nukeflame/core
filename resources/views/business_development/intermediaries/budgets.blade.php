@extends('layouts.intermediaries.base')
@section('content')
    <div class="row m-2">
        <div class="col-12">
            <div class="">
                <h3 class="fw-light text-center text-lg-start">Budgets</h3>
                <hr>
            </div>
            <div class="">
                <div class="col-4 mt-3">
                    <label for="year">Enter Year</label>
                    <form action="" method="post" id="yearform">
                        <input type="number" min='2000' max='{{$current_year}}' name="year" id="year" class="form-control" required>
                        <button type="submit" class="btn btn-primary mt-2" id="submityear">Submit</button>
                    </form>
                </div>
                <div class="col-12 mt-3">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#agentbranch" class="nav-link active text-bold" role="tab" data-toggle="tab">Per Branch Budgets</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active table-responsive" id="agentbranch">
                            <table class="table table-black  table-borderless table-striped" id="agentbudgets">
                                <thead >
                                    <tr>
                                        <th>Branch Name</th>
                                        <th>Previous Performance</th>
                                        <th>Original Target/Budget</th>
                                        @for($i=1; $i<=$budget_refocus; $i++)
                                            <th>Refocus {{$i}}</th>
                                        @endfor
                                        <th>Current Performance</th>
                                        <th>Variance(%)</th>
                                        <th>Per month</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pmagentbudgets" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:90%">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #cfd7e0 ">
                    <h5 class="modal-title" id="adminClaimModalLabel">Per month  budget </span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div>
                    <table class="table table-black  table-borderless table-striped" id="agentbudgetspm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Previous Performance</th>
                                <th>Original Target/Budget</th>
                                @for($i=1; $i<=$budget_refocus; $i++)
                                    <th>Refocus {{$i}}</th>
                                @endfor
                                <th>Current Performance</th>
                                <th>Variance(%)</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_scripts')
    <script type="text/javascript">
    $(document).ready(function(){
        $('#yearform').on('submit', function(e){
            e.preventDefault();
            // $('#submityear').attr('Disabled', true);
            let year = $('#year').val();

            getBudgetAgent(year);
        })
        $('body').on('click','#showpmbudgets', function(e){
            let budget_id = $(this).attr('budget_id');
            let agent_id = $(this).attr('agent_id');
            let branch_id = $(this).attr('branch_id');
            // console.log(budget_id);

            getBudgetAgentpm(budget_id,branch_id,agent_id);
        })

        function getBudgetAgent(year){
            $('#agentbudgets').DataTable({
                'destroy': true,
                'paging': true,
                'processing': true,
                'ajax':{
                    'url': '{{ route("intermediary.budgets") }}',
                    data:function(d){
                        d.year = year;
                    }
                },
                'columns':[
                    {data:'branchname', searchable:true},
                    {data:'prevperformance', searchable:true},
                    {data:'original_budget', searchable:true},
                    @for($i=1; $i<=$budget_refocus; $i++)
                        {data:'refocus{!!$i!!}', searchable:true},
                    @endfor
                    {data:'performance', searchable:true},
                    {data:'variance', searchable:true},
                    {data:'permonth', searchable:true},
                ]
            });
        }

        function getBudgetAgentpm(budget_id,branch_id,agent_id){
            $('#agentbudgetspm').DataTable({
                'destroy': true,
                'paging': true,
                'processing': true,
                'aaSorting': [],
                'ajax':{
                    'url': '{{ route("intermediary.budgetspm") }}',
                    data:function(d){
                        d.budget_id = budget_id;
                        d.branch_id = branch_id;
                        d.agent_id = agent_id;
                    }
                },
                'columns':[
                    {data:'month', searchable:true},
                    {data:'prevperformance', searchable:true},
                    {data:'original_budget', searchable:true},
                    @for($i=1; $i<=$budget_refocus; $i++)
                        {data:'refocus{!!$i!!}', searchable:true},
                    @endfor
                    {data:'performance', searchable:true},
                    {data:'variance', searchable:true},
                ]
            });
        }
        

        })
    </script>
@endsection