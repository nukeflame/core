@extends('layouts.admincast')

@section('content')

<div class="btn-group btn-breadcrumb ">
    <a href="{{ route('admin.dashboard') }}"><i class="fa fa-home mx-2"></i></a>
    <a href="#" style="color: black"> Clients</a>
    <i class="fas fa-angle-double-right mx-2 white-text" aria-hidden="true"></i>
    <a href="#" style="color: black"> Client</a>
    <i class="fas fa-angle-double-right mx-2 white-text" aria-hidden="true"></i>
    <a href="#"> Edit Associations</a>


</div>
<br />
@if ($message = Session::get('success'))
<div class="alert alert-success mt-3">
    <p>{{ $message }}</p>
</div>
@endif

<div class="card mt-5">
    <div class="card-body">
        {{--  <div class="col-sm-6 mb-3">
			<button class="btn btn-default" id="addAssoBtn">+Add Association</button>
		</div>  --}}
        <form method="post" action="{{ route('updateclientasso',$global_customer_id->global_customer_id) }}">
            @csrf


			<div class="col-md-4">
				<div class="form-group">
					<span class="control-label col-md-12"> Associated System <font style="color:red;">*</font> </span>
					<select class="selectpicker form-control" multiple data-live-search="true" name="asso_id">

						@foreach ( $customerlinksystems as $customerlinksystem)
						<option value="{{$customerlinksystem ->asso_id}}"> {{ $customerlinksystem ->business}}</option>
						@endforeach

					</select>
				</div>

			</div>
            <br>


            <div class="row">
                <div class="col-md-6">
                    <br>
                    <button type="submit" class="btn btn-info  btn-sm">Update<i
                            class="fa fa-check-circle ml-1"></i></button>
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