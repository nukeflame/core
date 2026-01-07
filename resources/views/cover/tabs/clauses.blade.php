<div class="card">
    <div class="card-body py-3 px-2">
        <table id="clauses-table" class="table table-striped text-nowrap table-hover table-responsive"
            data-url="{{ route('cover.clauses_datatable') }}" data-delete-url="" style="width: 100%">
            <thead>
                <tr>
                    <th scope="col">Clause ID</th>
                    <th scope="col">Clause Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
