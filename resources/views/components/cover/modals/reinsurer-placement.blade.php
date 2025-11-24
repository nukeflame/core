<div class="modal fade reinsurer-wrapper-modal" id="reinsurer-modal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 80%">
        <div class="modal-content">
            <form method="POST" id="reinsurerForm" data-url="{{ route('cover.save_reinsurance_data') }}">
                @csrf
                <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}" />

                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Reinsurer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body row" data-cedant-comm-rate="{{ $cover->cedant_comm_rate }}">
                    @if (isset($coverTreaties) && $coverTreaties->count() > 1)
                        <div class="col-md-4 mb-2">
                            <label for="add-treaty-reinsurer" class="form-label">&nbsp;</label>
                            <button class="btn btn-primary btn-sm" type="button" id="add-treaty-reinsurer">
                                <i class="fa fa-plus"></i> Additional treaty-reinsurer section
                            </button>
                        </div>
                    @endif

                    <div id="treaty-div" class="reinsure-model-container">
                        <div class="treaty-div-section mb-2 mt-2 p-2" id="treaty-div-section-0" data-counter="0"
                            style="border: 1px solid #333;">

                            <div class="p-2 mb-2" style="border: 1px solid #333;">
                                <div class="row">
                                    @if (in_array($cover->type_of_bus, ['TPR', 'TNP']))
                                        <div class="col-md-3">
                                            <label for="reinsurer-treaty-0" class="form-label">Treaty</label>
                                            @if (isset($coverTreaties) && $coverTreaties->count() == 1)
                                                <input class="form-control color-blk"
                                                    value="{{ $coverTreaties[0]->treaty_dtl->treaty_name }}" readonly />
                                                <input type="hidden" name="treaty[0][treaty]"
                                                    class="form-control treaties"
                                                    value="{{ $coverTreaties[0]->treaty }}" readonly />
                                            @else
                                                <select name="treaty[0][treaty]" id="reinsurer-treaty-0"
                                                    class="form-select reinsurer-treaty treaties" data-counter="0">
                                                    <option value="">--Select Treaty--</option>
                                                    @foreach ($coverTreaties as $coverTreaty)
                                                        <option value="{{ $coverTreaty->treaty }}"
                                                            title="{{ $coverTreaty->treaty_name }}">
                                                            {{ $coverTreaty->treaty_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="col-md-3">
                                        <label for="share_offered-0" class="form-label">
                                            Offered share&nbsp;(%)
                                        </label>
                                        <input type="text" name="treaty[0][share_offered]" id="share_offered-0"
                                            value="{{ number_format($cover->share_offered, 2) }}"
                                            class="form-control color-blk share_offered treaties disabled" readonly />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="distributed_share-0" class="form-label">
                                            Distributed&nbsp;(%)
                                        </label>
                                        <input type="text" name="treaty[0][distributed_share]"
                                            id="distributed_share-0" class="form-control color-blk treaties disabled"
                                            readonly />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="rem_share-0" class="form-label">
                                            Undistributed&nbsp;(%)
                                        </label>
                                        <input type="text" name="treaty[0][rem_share]" id="rem_share-0"
                                            class="form-control color-blk treaties disabled" readonly />
                                    </div>
                                </div>

                                @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label for="reinsurer-total_sum_insured-0" class="form-label">
                                                Total Effective Sum Insured
                                            </label>
                                            <input type="text" name="total_sum_insured"
                                                id="reinsurer-total_sum_insured-0"
                                                value="{{ number_format($cover->effective_sum_insured, 2) }}"
                                                class="form-control color-blk disabled" readonly />
                                        </div>

                                        <div class="col-md-3">
                                            <label for="reinsurer-total_rein_premium-0" class="form-label">
                                                Total Cedant Premium
                                            </label>
                                            <input type="text" name="total_rein_premium"
                                                id="reinsurer-total_rein_premium-0"
                                                value="{{ number_format($cover->rein_premium, 2) }}"
                                                class="form-control color-blk disabled" readonly />
                                        </div>

                                        <div class="col-md-3">
                                            <label for="reinsurer-rein_comm_amt-0" class="form-label">
                                                Total Reinsurers Commission
                                            </label>
                                            <input type="text" name="rein_comm_amt" id="reinsurer-rein_comm_amt-0"
                                                value="{{ number_format($cover->rein_comm_amount, 2) }}"
                                                class="form-control color-blk disabled" readonly />
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="p-2" style="border: 1px solid #333;">
                                <div id="reinsurer-div">
                                    <div id="reinsurer-div-0-0" data-treaty-counter="0" data-counter="0"
                                        class="reinsurer-section">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="reinsurer-0-0" class="form-label">Reinsurer</label>
                                                <div class="reinsurer-card">
                                                    <select name="treaty[0][reinsurers][0][reinsurer]"
                                                        id="reinsurer-0-0"
                                                        class="form-inputs select2 reinsurer reinsurers"
                                                        data-treaty-counter="0" data-counter="0" required>
                                                        <option value="">--Select Reinsurer--</option>
                                                        @foreach ($reinsurers as $partner)
                                                            @php
                                                                $existsInCoverpart = $coverpart->contains(
                                                                    'partner_no',
                                                                    $partner->customer_id,
                                                                );
                                                                $existsInCoverRegister =
                                                                    $cover->customer_id == $partner->customer_id;
                                                            @endphp
                                                            @if (!$existsInCoverpart && !$existsInCoverRegister)
                                                                <option value="{{ $partner->customer_id }}"
                                                                    title="{{ $partner->name }}">
                                                                    {{ $partner->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <label for="written_share-0-0" class="form-label">
                                                    Written Lines(%)
                                                </label>
                                                <input type="number" name="treaty[0][reinsurers][0][written_share]"
                                                    id="written_share-0-0"
                                                    class="form-control color-blk reinsurer-written-share reinsurers"
                                                    data-treaty-counter="0" data-counter="0" required />
                                            </div>

                                            <div class="col-md-2">
                                                <label for="share-0-0" class="form-label">
                                                    Signed Lines(%)
                                                </label>
                                                <input type="number" name="treaty[0][reinsurers][0][share]"
                                                    id="share-0-0"
                                                    class="form-control color-blk reinsurer-share reinsurers"
                                                    data-treaty-counter="0" data-counter="0" required />
                                            </div>

                                            <div class="col-md-3">
                                                <label for="wht_rate-0-0" class="form-label">WHT. Rate(%)</label>
                                                <div class="reinsurer-card">
                                                    <select name="treaty[0][reinsurers][0][wht_rate]"
                                                        id="wht_rate-0-0" class="form-inputs select2" required>
                                                        <option value="">--Select WHT--</option>
                                                        @foreach ($whtRates as $whtRate)
                                                            <option value="{{ $whtRate->rate }}">
                                                                {{ $whtRate->description }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                                            <div class="row mt-2">
                                                <div class="col-md-3">
                                                    <label for="reinsurer-sum_insured-0" class="form-label">
                                                        Sum Insured
                                                    </label>
                                                    <input type="text" name="treaty[0][reinsurers][0][sum_insured]"
                                                        data-counter="0" id="reinsurer-sum_insured-0"
                                                        class="form-control color-blk reinsurers disabled" required
                                                        readonly />
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="reinsurer-premium-0" class="form-label">
                                                        Reinsurer Premium
                                                    </label>
                                                    <input type="text" name="treaty[0][reinsurers][0][premium]"
                                                        id="reinsurer-premium-0"
                                                        class="form-control color-blk reinsurers reinsurer-premium"
                                                        data-counter="0"
                                                        onkeyup="this.value=numberWithCommas(this.value)" />
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="reinsurer-comm_rate-0" class="form-label">
                                                        Reinsurer Commission Rate(%)
                                                    </label>
                                                    <input type="text" name="treaty[0][reinsurers][0][comm_rate]"
                                                        data-counter="0" id="reinsurer-comm_rate-0"
                                                        class="form-control color-blk reinsurers reinsurer-comm-rate"
                                                        onkeyup="this.value=numberWithCommas(this.value)" required />
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="reinsurer-comm_amt-0" class="form-label">
                                                        Reinsurer Commission Amount
                                                    </label>
                                                    <input type="text" name="treaty[0][reinsurers][0][comm_amt]"
                                                        data-counter="0" id="reinsurer-comm_amt-0"
                                                        class="form-control color-blk reinsurers reinsurer-comm-amt"
                                                        onkeyup="this.value=numberWithCommas(this.value)" required />
                                                </div>
                                            </div>
                                        @endif

                                        <div class="row mt-2">
                                            <div class="col-md-3">
                                                <label for="reins_pay_method" class="required">
                                                    Payment Method
                                                </label>
                                                <select class="form-inputs section" name="pay_method"
                                                    id="reins_pay_method" required>
                                                    <option selected value="">Choose Payment Method</option>
                                                    @foreach ($paymethods as $pay_method)
                                                        <option value="{{ $pay_method->pay_method_code }}"
                                                            pay_method_desc="{{ $pay_method->short_description }}">
                                                            {{ $pay_method->pay_method_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2" id="no_of_installments_section"
                                                style="display: none">
                                                <label class="required" id="no_of_installments_label">
                                                    No. of Installments
                                                </label>
                                                <input type="number" class="form-control color-blk"
                                                    id="no_of_installments" name="no_of_installments"
                                                    value="1" />
                                            </div>

                                            <div class="col-md-2" id="add_reinsurer_btn_section"
                                                style="display: none">
                                                <label style="height: 20px"></label><br>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    id="add_reinsurer_instalments">
                                                    Add Installment
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row mt-2" id="add_installments_box" style="display: none">
                                            <div class="col-md-12">
                                                <h6>Installment plans</h6>
                                                <div id="reinsurer_plan_section"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal"
                        id="dismiss-partner-btn">Close</button>
                    <button type="button" id="partner-save-btn"
                        class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">
                        <span>Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
