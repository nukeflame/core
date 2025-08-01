<div>
    <div class="table-responsive">
        <table id="covers-ending-table" class="table table-striped table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>GROUP</th>
                    <th>RENEW STATUS</th>
                    <th>COVER NO</th>
                    <th>TYPE</th>
                    <th>CLASS</th>
                    <th>CEDANT CODE</th>
                    <th>CEDANT NAME</th>
                    <th>CEDANT BROKER CODE</th>
                    <th>CEDANT BROKER NAME</th>
                    <th>REGION</th>
                    <th>INSURED</th>
                    <th>END DATE</th>
                    <th>PREMIUM (100%)</th>
                    <th>OUR SHARE</th>
                    <th>PREMIUM (KES)</th>
                    <th>PREMIUM TAX</th>
                    <th>REIN TAX</th>
                    <th>CEDANT WHT TAX</th>
                    <th>CEDANT CLAIM LOC</th>
                    <th>DEDUCTIONS (KES)</th>
                    <th>CEDANT NET (KES)</th>
                    <th>REINS PREMIUM (KES)</th>
                    <th>REVENUE (KES)</th>
                    <th>CEDANT PAID (KES)</th>
                    <th>CEDANT BALANCE (KES)</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            const cvTable = $('#covers-ending-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('reports.covers_ending.data') }}',
                columns: [{
                        data: 'group',
                        name: 'group'
                    },
                    {
                        data: 'renew_status',
                        name: 'renew_status'
                    },
                    {
                        data: 'cover_no',
                        name: 'cover_no'
                    },
                    {
                        data: 'cover_type',
                        name: 'cover_type'
                    },
                    {
                        data: 'class',
                        name: 'class'
                    },
                    {
                        data: 'cedant_code',
                        name: 'cedant_code'
                    },
                    {
                        data: 'cedant_name',
                        name: 'cedant_name'
                    },
                    {
                        data: 'cedant_broker_code',
                        name: 'cedant_broker_code'
                    },
                    {
                        data: 'cedant_broker_name',
                        name: 'cedant_broker_name'
                    },
                    {
                        data: 'region',
                        name: 'region'
                    },
                    {
                        data: 'insured',
                        name: 'insured'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        render: function(data) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: 'premium_100',
                        name: 'premium_100'
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
                        data: 'premium_kes',
                        name: 'premium_kes'
                    },
                    {
                        data: 'premium_tax',
                        name: 'premium_tax'
                    },
                    {
                        data: 'rein_tax',
                        name: 'rein_tax'
                    },
                    {
                        data: 'cedant_wht_tax',
                        name: 'cedant_wht_tax'
                    },
                    {
                        data: 'cedant_claim_loc',
                        name: 'cedant_claim_loc'
                    },
                    {
                        data: 'deductions_kes',
                        name: 'deductions_kes'
                    },
                    {
                        data: 'cedant_net_kes',
                        name: 'cedant_net_kes'
                    },
                    {
                        data: 'reins_premium_kes',
                        name: 'reins_premium_kes'
                    },
                    {
                        data: 'revenue_kes',
                        name: 'revenue_kes'
                    },
                    {
                        data: 'cedant_paid_kes',
                        name: 'cedant_paid_kes'
                    },
                    {
                        data: 'cedant_balance_kes',
                        name: 'cedant_balance_kes'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
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
