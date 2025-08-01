<div>
    <div class="table-responsive">
        <table id="covers-table" class="table table-striped table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>TYPE CODE</th>
                    <th>COVER TYPE</th>
                    <th>COVER NO</th>
                    <th>COVER TITLE</th>
                    <th>GROUP</th>
                    <th>CEDANT</th>
                    <th>INSURED</th>
                    <th>CURRENCY</th>
                    <th>CLASS</th>
                    <th>DATE OFFERED</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>OUR SHARE (%)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            const cvTable = $('#covers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('reports.covers_by_type.data') }}',
                columns: [{
                        data: 'type_code',
                        name: 'type_code'
                    },
                    {
                        data: 'cover_type',
                        name: 'cover_type'
                    },
                    {
                        data: 'cover_no',
                        name: 'cover_no'
                    },
                    {
                        data: 'cover_title',
                        name: 'cover_title'
                    },
                    {
                        data: 'group',
                        name: 'group'
                    },
                    {
                        data: 'cedant',
                        name: 'cedant'
                    },
                    {
                        data: 'insured',
                        name: 'insured'
                    },
                    {
                        data: 'currency',
                        name: 'currency',
                        render: function(data) {
                            return `<span class="badge bg-secondary">${data}</span>`;
                        }
                    },
                    {
                        data: 'class',
                        name: 'class'
                    },
                    {
                        data: 'date_offered',
                        name: 'date_offered',
                        render: function(data) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        render: function(data) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        render: function(data) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: 'our_share',
                        name: 'our_share',
                        render: function(data) {
                            let badgeClass = 'bg-info';
                            if (data > 80) {
                                badgeClass = 'bg-danger';
                            } else if (data > 50) {
                                badgeClass = 'bg-warning';
                            } else if (data < 20) {
                                badgeClass = 'bg-success';
                            }
                            return `<span class="badge ${badgeClass}">${data}%</span>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        sortable: false,
                    },
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
            });

            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB');
            }
        });
    </script>
@endpush
