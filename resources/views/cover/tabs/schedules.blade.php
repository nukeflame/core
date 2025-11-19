<div class="card">
    <div class="card-body py-3 px-2">
        <table id="schedules-table" class="table table-striped text-nowrap table-hover table-responsive"
            data-url="{{ route('cover.schedules_datatable') }}" data-delete-url="{{ route('cover.delete_schedule') }}"
            style="width: 100%">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Title</th>
                    <th scope="col">Details</th>
                    <th scope="col">Position</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
