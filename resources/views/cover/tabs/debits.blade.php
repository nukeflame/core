<div class="card">
    <div class="card-body py-3 px-2">
        <table id="debits-table" class="table table-striped text-nowrap table-hover table-responsive" style="width: 100%"
            data-url="{{ route('cover.debits_datatable') }}" data-delete-url="">
            <thead>
                <tr>
                    <th scope="col">ID.</th>
                    <th scope="col">Cedant</th>
                    <th scope="col">Debit No.</th>
                    <th scope="col">Installment</th>
                    <th scope="col">Share(%)</th>
                    <th scope="col">Sum insured</th>
                    <th scope="col">Premium</th>
                    {{-- <th scope="col">Commission</th> --}}
                    <th scope="col">Gross</th>
                    <th scope="col">Net Amount</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
