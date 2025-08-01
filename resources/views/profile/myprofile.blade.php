@extends('layouts.app')

@section('content')
    <!-- Main content -->
    <section class="content">

        <nav class="breadcrumb pt-3">
            <a class="breadcrumb-item" href>Profile </a><span> ➤ My profile</span>
        </nav>

        <div class="row">
            <div class="col-md-12">

                <div class="card border-0">
                    <div class="card-header mb-0">
                        <div class="row">
                            <div class="col">
                                <h5> My Profile</h5>
                            </div>
                            <div class="col text-end">
                                <button type="button" class="btn btn-primary"><a href="{{ route('user.changepwd') }}"
                                        style="color:white"><i class="fa fa-edit"></i> Change password </a></button>
                                <button type="button" class="btn btn-primary btn-sm custom-btn mb-0" data-toggle="modal"
                                    data-target="#edit_sign"> My Signature </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>Full Names</td>
                                        <td><input type="text" name="" class="form-control"
                                                value="{{ $user->name }}" readonly /></td>
                                    </tr>
                                    <tr>
                                        <td>Department</td>
                                        <td><input type="text" name="" class="form-control"
                                                value="{{ $user->department ? $user->department->department_name : 'No Department' }}"
                                                readonly /></td>
                                    </tr>
                                    <tr>
                                        <td>Role</td>
                                        <td><input type="text" name="" class="form-control"
                                                value="{{ $user->role ? $user->role->name : 'No Role' }}" readonly /></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><input type="text" name="" class="form-control"
                                                value="{{ $user->email }}" readonly /></td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>
    <!-- /.content -->

    <!--****************modal for editting details****************-->
    <div class="modal fade" id="edit_details" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Change Password</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('profile.chgpwd') }}" method="post">
                        {{ csrf_field() }}
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

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!--****************modal for signature****************-->
    <div class="modal fade" id="edit_sign" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">My Signature</h4>
                </div>
                <div class="modal-body">
                    <form action="" method="post">{{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                <label>Current Signature</label>
                                <input type="text" name="current_sign" id="current_sign" class="form-control"
                                    required />
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6 ">
                                <br>
                                <label>New Signature</label>
                                <input type="text" name="new_sign" id="new_sign" class="form-control" required />
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                <br>
                                <label>Confirm Signature</label>
                                <input type="text" name="confirm_sign" id="confirm_sign" class="form-control"
                                    required />
                            </div>
                            <div class="col-md-3"></div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@push('script')
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush
