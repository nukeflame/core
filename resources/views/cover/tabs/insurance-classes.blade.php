<div class="card">
    {{-- <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bx bx-award me-2"></i>Insurance Classes
        </h6>
        @if ($actionable ?? false)
            <button class="btn btn-primary btn-sm" id="add-insurance-class" data-bs-toggle="modal"
                data-bs-target="#insurance-class-modal">
                <i class="bx bx-plus me-1"></i> Add Classes
            </button>
        @endif
    </div> --}}

    <div class="card-body py-3 px-2">
        <table id="insclass-table" class="table table-striped text-nowrap table-hover table-responsive"
            data-url="{{ route('cover.classes_datatable') }}" data-delete-url="" style="width: 100%">
            {{-- {{ route('cover.delete_insurance_class') }} --}}
            <thead>
                <tr>
                    <th scope="col" style="width: 5%">No.</th>
                    <th scope="col" style="width: 25%">Reinsurance Class</th>
                    <th scope="col" style="width: 15%">Class Code</th>
                    <th scope="col" style="width: 35%">Class Name</th>
                    <th scope="col" style="width: 20%">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
