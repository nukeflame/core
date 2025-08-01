<div class="card bg-light shadow mb-3">
    <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bx bx-filter me-2"></i>Filters :-
        </h6>
        <button class="btn btn-link btn-sm text-decoration-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#filtersCollapse" aria-expanded="true">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filtersCollapse">
        <div class="card-body">
            <form id="filtersForm">
                <div class="row">
                    <div class="col-md-2 col-lg-2 mb-3">
                        <label for="dateFrom" class="form-label fw-semibold">From Date</label>
                        <input type="date" class="form-inputs" id="dateFrom" name="date_from"
                            value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-2 col-lg-2 mb-3">
                        <label for="dateTo" class="form-label fw-semibold">To Date</label>
                        <input type="date" class="form-inputs" id="dateTo" name="date_to"
                            value="{{ date('Y-m-t') }}">
                    </div>
                    <div class="col-md-2 col-lg-2 mb-3">
                        <label for="bizType" class="form-label fw-semibold">Business Type</label>
                        <select class="form-inputs select2" id="bizType" name="biz_type">
                            <option value="">All Types</option>
                            <option value="RENEWAL">Renewal</option>
                            <option value="NEW">New</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2 mb-3">
                        <label for="reinsurer" class="form-label fw-semibold">Reinsurer</label>
                        <select class="form-inputs select2" id="reinsurer" name="reinsurer">
                            <option value="">All Reinsureds</option>
                            {{-- @foreach ($reinsureds as $reinsured)
                                <option value="{{ $reinsured }}">{{ $reinsured }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2 mb-3">
                        <label for="insurer" class="form-label fw-semibold">Insurer</label>
                        <select class="form-inputs select2" id="insurer" name="insurer">
                            <option value="">All Insurers</option>
                            {{-- @foreach ($reinsureds as $reinsured)
                                <option value="{{ $reinsured }}">{{ $reinsured }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2 mb-3">
                        <label for="cedant" class="form-label fw-semibold">Cedant</label>
                        <select class="form-inputs select2" id="cedant" name="cedant">
                            <option value="">All Cedants</option>
                            {{-- @foreach ($reinsureds as $reinsured)
                                <option value="{{ $reinsured }}">{{ $reinsured }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 col-lg-2 col-sm-12  mb-3">
                        <label for="currency" class="form-label fw-semibold">Currency</label>
                        <select class="form-inputs select2" id="currency" name="currency">
                            <option value="">All Currencies</option>
                            {{-- @foreach ($currencies as $curr)
                                <option value="{{ $curr }}">{{ $curr }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="col-md-5 col-lg-5 mb-3 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-search me-1"></i>Apply Filters
                            </button>
                            <button type="button" class="btn btn-success btn-action me-2" id="export-excel">
                                <i class="bx bx-export me-2"></i>Export to Excel
                            </button>
                            <button type="button" class="btn btn-info btn-action me-2" id="generate-report">
                                <i class="bx bx-file me-2"></i>Generate Report
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                                <i class="bx bx-undo me-1"></i>Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
