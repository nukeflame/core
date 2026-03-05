<div class="card">
    <div class="card-body py-3 px-2">
        <table id="approvals-table" class="table table-striped text-nowrap table-hover table-responsive"
            data-url="{{ route('cover.approvals_datatable') }}"
            data-re-escalate-url="{{ route('admin.approvals.re-escalate') }}" data-delete-url="" style="width: 100%">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Approver</th>
                    <th scope="col">Comment</th>
                    <th scope="col">Approver Comment</th>
                    <th scope="col">Status</th>
                    <th scope="col">Approval Time</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
