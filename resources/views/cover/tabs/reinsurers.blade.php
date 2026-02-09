<div class="card border-0 shadow-none">
    <div class="card-body py-3 px-2">
        <div class="table-responsive">

            <table id="reinsurers-table" class="table table-bordered table-hover w-100"
                data-url="{{ route('cover.reinsurers_datatable') }}"
                data-delete-url="{{ route('cover.delete_reinsurance_data') }}" style="width: 100%!important;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 3%">#</th>
                        <th style="width: 15%">Reinsurer</th>
                        <th style="width: 5%">Share %</th>
                        @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                            <th style="width: 10%" title="Sum Insured">Sum Insured</th>
                            <th style="width: 10%" title="Gross Premium">Gross Premium</th>
                            <th style="width: 5%" title="Commission Rate">Comm %</th>
                            <th style="width: 10%" title="Commission">Commission</th>
                            <th style="width: 5%" title="Brokerage Rate">Brok %</th>
                            <th style="width: 10%" title="Brokerage">Brokerage</th>
                            <th style="width: 10%" title="Withholding Tax Amount">WHT Amt</th>
                            <th style="width: 10%" title="Retrocession Amount">Retro Amt</th>
                            <th style="width: 13%" title="Net Amount">Net Amt</th>
                        @endif
                        <th style="width: 15%">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {

            function askBrokingCommission(endorsementNo, partnerNo = null) {
                let baseUrl = "{{ route('docs.reincreditnotes', ['endorsement_no' => '__ENDORSEMENT_NO__']) }}";
                baseUrl = baseUrl.replace('__ENDORSEMENT_NO__', endorsementNo);
                let url = baseUrl;
                if (partnerNo) {
                    url += `&partner_no=${partnerNo}`;
                }

                Swal.fire({
                    title: 'Include Broking Commission?',
                    icon: 'question',
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: 'Yes',
                    denyButtonText: 'No',
                    width: '450px',
                    customClass: {
                        actions: 'swal_actions_btn',
                        confirmButton: 'order-2 btn-confirm',
                        denyButton: 'order-3 btn-deny',
                    },
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            showConfirmButton: false,
                            confirmButtonText: 'OK',
                            title: 'Generating with Brokerage...',
                            text: '',
                            timer: 1500,
                            timerProgressBar: true
                        });
                        url += "&include_broking_commission=yes";
                        viewDocumentPdf(url, 'Credit Note')
                    } else if (result.isDenied) {
                        Swal.fire({
                            icon: 'success',
                            showConfirmButton: false,
                            confirmButtonText: 'OK',
                            title: 'Generating without Brokerage...',
                            text: '',
                            timer: 1500,
                            timerProgressBar: true
                        });
                        url += "&include_broking_commission=no";
                        viewDocumentPdf(url, 'Credit Note')
                    }
                })
            }

            async function viewDocumentPdf(url, title = 'Document') {
                const response = await fetch(url, {
                    method: 'GET'
                });

                if (response.ok) {
                    window.open(url, '_blank', 'noopener,noreferrer');
                } else {
                    toastr.error("This transaction is not yet debited", title)
                }
            }

            async function checkDebitExists(url, endorseNo, partnerNo = null) {
                const response = await fetch(url, {
                    method: 'GET',
                });
                if (response.ok) {
                    askBrokingCommission(endorseNo, partnerNo)
                } else {
                    toastr.error("This transaction is not yet debited", 'Credit Note')
                }
            }

            // Standardize all reinsurance document clicks
            $(document).on('click', '.rein_credit_note_btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                var endorseNo = $(this).data("endorsementno")
                var partnerNo = $(this).data("partnerno")

                checkDebitExists(url, endorseNo, partnerNo);
            });

            $(document).on('click', '.rein_cover_slip_btn', function(e) {
                e.preventDefault();
                var url = $(this).attr("href")
                viewDocumentPdf(url, 'Cover Slip');
            });

            $(document).on('click', '.rein_endorsement_slip_btn', function(e) {
                e.preventDefault();
                var url = $(this).attr("href")
                viewDocumentPdf(url, 'Endorsement Notice Slip');
            });

            // Legacy IDs (keeping for safety if they exist elsewhere on the page)
            $('#generateCreditNote').on('click', function(e) {
                e.preventDefault();
                const url = $("#generateDebitNote").attr('href');
                var endorseNo = $(this).data("endorsementno")
                checkDebitExists(url, endorseNo);
            });

            $('#generateCoverSlip').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr("href")
                viewDocumentPdf(url, 'Cover Slip');
            });

            $('#generateEndorsementSlip').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr("href")
                viewDocumentPdf(url, 'Endorsement Notice Slip');
            });

        });
    </script>
@endpush
