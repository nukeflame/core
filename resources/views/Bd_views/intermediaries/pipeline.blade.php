{{-- @extends('layouts.admincast') --}}
@extends('layouts.intermediaries.base')
{{-- @extends('layouts.intermediaries.base') --}}
@section('header','MENU ITEMS')
@section('content')


<div class="m-2">

    <div class="table-responsive justify-content-between">
        <div class="d-flex justify-content-between">
            <div class="m-2">
               
                <a href="#" class="btn btn-outline-success" role="button" data-target="#addYearPipeline" data-toggle="modal">
                    <i class="fa-solid fa-plus"></i>
                    New pipeline
                </a>
            </div>
            <div class="m-2">
            <h6></h6>
            </div>
        </div>
    </div>
    <div class="card table-responsive">
        <div class="card-body">
            <table class="table table-striped table-hover" id="pipeline_table">
                <thead>
                    <tr>
                        <th>Year</th>
                        <!-- <th>Email</th>
                        <th>Source</th>
                        <th>Industry</th> -->
                        <th>Number of Prospects</th>
                        <th>Prospects Won</th>
                        <th>Prospects Lost</th>
                        <th>Pipeline Worth</th>
                        <th>Action</th>
                        
                    </tr>
                </thead>
    
            </table>
        </div>
    </div>
      
</div>

<!-- Modal -->
<div class="modal fade" id="addYearPipeline" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog modal-md" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="">Create Year Pipeline</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<form action="{{ route('pipelines.save') }}" method="POST">
				@csrf
				<div class="row ">        
					<div class="col-md-12">
						<label for="">Year</label>
						<input type="number" class="form-control" name="year" required>
					</div>
				</div>
				  
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                
                </div>
	        </form>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')

<script>
    $(document).ready(function () {
        $("#myInput").on("change", function () {
            var value = $(this).val().toLowerCase();

            $("#pipeline_table > tbody > tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
        $('#pipeline_table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pipelines.get')}}",
                type: "get"
                            
            },
            columns: [
                {data:'year',name:'year'},  
                {data:'opp_count',name:'opp_count'},
                {data:'opp_won',name:'opp_won'},
                {data:'opp_lost',name:'opp_lost'},
                {data:'pipeline_worth',name:'pipeline_worth'},
                {data:'action',name:'action'},
                ]		
        });

    })
</script>

@endsection
