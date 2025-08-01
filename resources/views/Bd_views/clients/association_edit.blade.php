@extends('layouts.admincast')

@section('content')

<div class="btn-group btn-breadcrumb ">
	<a href="{{ route('admin.dashboard') }}"><i class="fa fa-home mx-2"></i></a>
	<a href="#"> Clients</a>
	<i class="fas fa-angle-double-right mx-2 white-text" aria-hidden="true"></i>
	<a href="{{ route('view.client',$client -> global_customer_id) }}"> Client</a>
	<i class="fas fa-angle-double-right mx-2 white-text" aria-hidden="true"></i>
	<a href="#"> Edit Banks</a>


</div>
<br />
@if ($message = Session::get('success'))
<div class="alert alert-success mt-3">
	<p>{{ $message }}</p>
</div>
@endif

<div class="card mt-5">
	<div class="card-body">
		<div class="col-sm-6 mb-3">
			<button class="btn btn-default" id="addAssoBtn">+Add Association</button>
		</div>
        <form method="post" action="{{ route('updateasso',$association->asso_id) }}">
            @csrf
            
            
                <div class="row ">
                <div class="col-md-6">
                    Associate id<br />
                    <input type="text" class="form-control" name="asso_id" value="{{$association->asso_id}}" disabled>
                </div>
                
                <div class="col-md-6">
                    System<br />
                    <input type="text" class="form-control" name="desc" value="{{$association->desc}}" required>
                </div>
                
                <div class="col-md-6 mt-3">
                    Business<br>
                    <input type="text" name="business" class="form-control" value="{{$association->business}}" required>
                </div>
            </div>
            <br>
                
            
            <div class="row">
                <div class="col-md-6">
                    <br>
                    <button type="submit" class="btn btn-success  btn-sm">Update<i class="fa fa-check-circle ml-1"></i></button>
                </div>
            </div>
        </form>	
	</div>
</div>
@endsection
@section('page_scripts')

<script>
	$(document).ready(function () {
		$(function () {
			$('#deleteAssoBtn').click(function () {
				let recId = $('#recordID').val();

				$.ajax({

					type: 'post',

					url: 'clients/association/delete',

					data: {
						'recId': recordID,
						_token: '{{ csrf_token() }}'
					},

					success: function (data) {

						console.log(data);

						// $('#divItems').load(location.href + ' #divItems');

					},
				});
			});

			$('#asso-div').delegate('#deleteBtn', 'click', function () {
				$(this).parent().parent().remove();
			});

			$('#bank-div').delegate(
				'#bank_name, #branch,  #account_name, #account_no, #status, #default_bank', 'keyup',
				function () {
					let tr = $(this).parent().parent();
					let bank = tr.find('#bank_name').val() - 0;
					let branch = tr.find('#branch').val() - 0;
					let accName = tr.find('#account_name').val() - 0;
					let accNo = tr.find('#account_no').val() - 0;
					let status = tr.find('#status').val() - 0;
					let d = tr.find('#default_bank').val() - 0;
				});
		});
	});
</script>

@endsection