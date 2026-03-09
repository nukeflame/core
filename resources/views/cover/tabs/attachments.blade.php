<div class="card">
    <div class="card-body py-3 px-2">
        <table id="attachments-table" class="table table-striped text-nowrap table-hover table-responsive"
            data-url="{{ route('cover.attachments_datatable') }}"
            data-delete-url="{{ route('cover.delete_attachment') }}" style="width: 100%">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">File</th>
                    <th scope="col">Type</th>
                    <th scope="col">Updated By</th>
                    <th scope="col">Updated At</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
