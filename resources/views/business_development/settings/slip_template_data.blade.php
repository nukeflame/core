@extends('layouts.app')

@section('content')
    <div>

        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Schedule Slip Template</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Schedule template Setup</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Schedule Slip</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Page Header Close -->

        <div class="row">
            <div class="col-xl-6">
                <button type="button" class="btn btn-sm btn-dark btn-wave" id="newSlipClause">Add new schedule
                    template</button>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Schedule Template list</div>
                    </div>
                    <div class="card-body">
                        {{-- {{ html()->form('POST', '/cover/endorsements_list')->id('form_cover_datatable')->open() }} --}}
                        {{-- <input type="text" id="customer_id" name="customer_id" hidden />
                        <input type="text" name="cover_no" id="cov_cover_no" hidden> --}}
                        <table id="coversliplist" class="table text-nowrap table-hover table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Schedule Title</th>
                                    <th>Class Group</th>
                                    <th>Class Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        {{-- {{ csrf_field() }} --}}
                        {{-- {{ html()->form()->close() }} --}}
                    </div>
                </div>
            </div>
        </div>

        <!--Choose clause -->
        {{-- <div class="modal effect-scale" id="newSlipModal" data-bs-backdrop="static" data-bs-keyboard="false"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editSlipform" action="{{ route('docs-setup.bd_schedule_template_form') }}">
                        @csrf
                        <input type="hidden" name="class_group" id="class_group">
                        <input type="hidden" name="classcode" id="classcode">
                        <input type="hidden" name="clause" id="clause">
                    </form>
                    <form id="newSlipform" action="{{ route('docs-setup.bd_schedule_template_form') }}">
                        @csrf
                        <div class="modal-body">
                            <h6 class="form-label md-title font-weight-bold mb-2">Create Bd Schedules Template</h6>
                            <div class="row">
                                <div class="col">
                                    <div class="d-flex flex-column ced-body">
                                        <label for="title" class="form-label md-title">Class Group</label>
                                        <div class="cover-card">
                                            <select class="form-slect select2" id="select_classgroup" name="class_group"
                                                required>
                                                @switch($trans_type)
                                                    @case('NEW')
                                                        <option selected value="">-- Select --</option>
                                                        @foreach ($classGroups as $classGroup)
                                                            <option value="{{ $classGroup->group_code }}">
                                                                {{ $classGroup->group_name }}
                                                            </option>
                                                        @endforeach
                                                    @break

                                                    @case('EXT')
                                                    @case('CNC')

                                                    @case('REN')
                                                    @case('RFN')

                                                    @case('NIL')
                                                    @case('INS')

                                                    @case('EDIT')
                                                        @foreach ($classGroups as $classGroup)
                                                            <option value="{{ $classGroup->group_code }}">
                                                                {{ $classGroup->group_name }}
                                                            </option>
                                                        @endforeach
                                                    @break
                                                @endswitch
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label required">Class Name</label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2 fac_section" name="classcode"
                                            id="sel_classcode" required>
                                            <option value="">-- Select --</option>


                                        </select>
                                        <div class="text-danger">{{ $errors->first('classcode') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                                id="dismissSlipBtn">Close</button>
                            <button type="button" id="saveSlipBtn"
                                class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}
    </div>
@endsection
