<div class="modal fade effect-scale md-wrapper" id="facDebitModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticDebitLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="facDebitForm" data-post-url="{{ route('cover.generate-debit') }}">
                @csrf
                <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
                <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                <input type="hidden" name="posting_date" value="{{ now()->format('d/m/Y H:i:s') }}" />
                <input type="hidden" name=" posting_year" value="{{ now()->format('Y') }}" />
                <input type="hidden" name="posting_quarter" value="Q1" />
                <input type="hidden" name="type_of_bus" value="{{ $cover->type_of_bus }}" />
                <input type="hidden" name="brokerage_rate" value="{{ $cover->brokerage_comm_rate }}" />

                <div class="modal-header">
                    <h6 class="modal-title" id="staticDebitLabel">Create A Debit Note
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Cover</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $coverReg->cover_no }}"
                                readonly required />
                        </div>
                        <div class="col-md-6">
                            <label for="">Endorsement</label>
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $coverReg->endorsement_no }}" readonly required />
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="">Installment</label>
                            <input type="text" name="installment" id="installment"
                                class="form-control form-control-sm" value="{{ $nextInstallment }}" readonly required />
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="">Total Sum Insured</label>
                            <input type="text" name="amount" id="amount"
                                class="form-control form-control-sm amount"
                                value="{{ number_format($cover->total_sum_insured, 2) }}" readonly required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="debit-save-btn"
                        class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
