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
			<button class="btn btn-default" id="addBankBtn">+Add Bank</button>
		</div>
		<form action="{{ route('clients.updateclientbanks', $client->global_customer_id) }}" method="post">
			@method('patch')
			@csrf
			<table class="table">
				<thead>
					<tr>
						<td>Bank</td>
						<td>Branch</td>
						<td>Account Name</td>
						<td>Account Number</td>
						<td>Status</td>
						<td>Default</td>
						<td>Remove</td>
					</tr>
				</thead>

				<tbody>
					@foreach ($client -> banks as $bank)
					<tr id="recordID">
						<td>
							<input type="text" name="bank_name" id="bank_name" value="{{$bank->bank_name }}" class="form-control" required />
							
						</td>
						<td>
							<input type="text" name="branch" id="branch" value="{{$bank->branch }}" class="form-control" required />
							
						</td>
						<td>
							<input type="text" name="account_name" id="account_name" value="{{ $bank->account_name }}" class="form-control" required />
						</td>
						<td>
							<input type="text" name="account_no" id="account_no" value="{{ $bank->account_no }}" class="form-control" required />
							
						</td>

						<td>
							<select name="status[]" id="status" class="form-control" required>
								{{-- <option value="{{ $bank -> status_code }}">{{ $bank -> status }}</option> --}}
								@foreach ($statuses as $status)
								<option value="{{ $status -> status_description }}"{{ $status -> status_code == $client -> status_code ? "selected" :'' }}>
									{{ $status -> status_description }}
								</option>
								@endforeach
							</select>
						</td>
						<td>
							<div class="radio-inline">
								<input type="radio" name="addmore[0][default_bank]" id="default_bank" value="1" checked><br>
							<div>
							{{-- <select name="default_bank[]" id="default_bank" class="form-control" required>
								<option value="{{ $bank -> default_bank }}">{{ $bank -> default_bank }}</option>
								<option value=1>Yes</option>
								<option value=0>NO</option>
							</select> --}}
						</td>
						<td>
							<button id="deleteBankBtn" class="btn btn-default btn-small"><span
									class="fa fa-trash"></span></button>
						</td>
					</tr>
					@endforeach

					<tr>
						<td></td>
						<td><button class="btn btn-success">Submit</button></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
@endsection
@section('page_scripts')

<script>
	$(document).ready(function () {
		$(function () {
			$('#deleteBankBtn').click(function () {
				let recId = $('#recordID').val();

				$.ajax({

					type: 'post',

					url: 'clients/banks/delete',

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

			$('#bank-div').delegate('#deleteBtn', 'click', function () {
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