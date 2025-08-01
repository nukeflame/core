@extends('layouts.app')

@section('content')

<!-- Main content -->
<section class="content">

  <nav class="breadcrumb pt-3">
    <a class="breadcrumb-item" href>Profile </a><span> ➤ Change Password</span>
  </nav>

  <div class="row">
    <div class="col-md-12">

      <div class="card border-0">
        <div class="card-header mb-0">
          <div class="row">
            <div class="col">
              <h5> Change Password</h5>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
              
                <form action="{{ route('profile.chgpwd') }}" method="post">
                  {{csrf_field()}}
                  <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                      <label>Email</label>
                      <input type="hidden" name="user_email" id="user_email" class="form-inputs" value="{{$user->email}}"readonly />
                      <input type="hidden" name="user_name" id="user_name" class="form-inputs" value="{{$user->user_name}}"readonly />
                      <input type="text" name="dd" id="dd" class="form-inputs" value="{{$user->email}}" readonly />
                    </div>
                  </div>
                    <div class="row">
                      <div class="col-md-3"></div>
                    <div class="col-md-6">
                      <label>Current Password</label>
                      <input type="password" name="current_pass" id="current_pass" class="form-inputs" required />
                    </div>
                    <div class="col-md-3"></div>
                  </div>
                  <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6 ">
                      <br>
                      <label>New Password</label>
                      <input type="password" name="new_pass" id="new_pass" class="form-inputs" required />
                    </div>
                    <div class="col-md-3"></div>
                  </div>
                  <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                      <br>
                      <label>Confirm Password</label>
                      <input type="password" name="confirm_pass" id="confirm_pass" class="form-inputs" required />
                    </div>
                    <div class="col-md-3"></div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                      <br>
                      <button type="button" class="btn btn-danger">Cancel</button>
                      <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </div>
                    <div class="col-md-3"></div>
                  </div>
                </form>
              </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</section>
<!-- /.content -->

@endsection

@push('script')
<script>
  $(document).ready(function() {

  });
</script>
@endpush