<div class="card border-0 shadow-none">
    <div class="card-body py-3 px-2">
        <div class="table-responsive">
            <table id="schedules-table" class="table table-bordered table-hover w-100"
                data-url="{{ route('cover.schedules_datatable') }}"
                data-delete-url="{{ route('cover.delete_schedule') }}" style="width: 100%!important;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 20%">Title</th>
                        <th style="width: 10%">Position</th>
                        <th style="width: 50%">Description</th>
                        <th style="width: 15%">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
