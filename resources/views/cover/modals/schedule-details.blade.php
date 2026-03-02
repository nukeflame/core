<div class="modal fade" id="addScheduleModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticScheduleDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="schedulesForm" data-post-url="{{ route('cover.add_schedule') }}"
                data-put-url="{{ route('cover.amend_schedule') }}" data-fetch-url="{{ route('cover.get_schedule') }}"
                data-schedule-items-url="{{ route('cover.available_schedule_items') }}">
                @csrf
                @method('POST')
                <input type="hidden" name="cover_no" value="{{ $cover->cover_no }}" />
                <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}" />
                <input type="hidden" name="id" id="id" />
                <input type="hidden" name="schedule_id" id="schedule_id" />

                <div class="modal-header">
                    <h6 class="modal-title dc-modal-title" id="staticScheduleDetailsLabel">
                        Schedule Details
                    </h6>
                    <button type="button" aria-label="Close" class="btn-close btn-close-white closeScheduleForm"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label class="form-label fs-14" for="sched-header">Schedule item</label>
                            <select name="header" id="sched-header" class="form-inputs select2" required>
                                <option value="">--Select Schedule items--</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label fs-14" for="schedule_position">Position</label>
                            <input type="number" name="schedule_position" id="schedule_position"
                                class="form-control color-blk" />
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label class="form-label fs-14" for="schedule_item_description">Description</label>
                            <input type="text" name="description" id="schedule_item_description"
                                class="form-control color-blk" maxlength="255" />
                        </div>
                    </div>

                    <hr>

                    <input type="hidden" name="title" id="title" class="form-control color-blk" required />

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label fs-14 mb-2" for="schedule_details_preview">Details</label>
                            <textarea id="schedule_details_preview" class="form-control resize-none" rows="8"
                                placeholder="Select a schedule item to load details. Click to open breakdown editor." readonly></textarea>
                            <small class="text-muted d-block mt-1">Click the textarea to open breakdown editor.</small>
                            <textarea id="hidden_schedule_description" name="details" class="form-control resize-none d-none" rows="10"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm closeScheduleForm"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="schedule-save-btn"
                        class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="scheduleBreakdownModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="scheduleBreakdownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: linear-gradient(90deg, #f31829 0%, #3a3a3a 100%);">
                <h6 class="modal-title" id="scheduleBreakdownModalLabel">
                    <i class="bx bx-edit-alt me-1"></i> Breakdown Editor
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card border mb-3">
                    <div class="card-body py-2">
                        <h6 class="mb-3 text-danger fw-semibold">
                            <i class="bx bx-list-check me-1"></i> Editor Actions
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                id="loadExistingScheduleContentBtn">Load Existing Content</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                id="clearScheduleBreakdownBtn">Clear Content</button>
                        </div>
                    </div>
                </div>

                <div class="border rounded">
                    <div id="schedule_breakdown_toolbar">
                        <select class="ql-font"></select>
                        <select class="ql-size"></select>
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                        <button class="ql-strike"></button>
                        <button class="ql-color"></button>
                        <button class="ql-background"></button>
                        <button class="ql-script" value="super"></button>
                        <button class="ql-script" value="sub"></button>
                        <button class="ql-list" value="ordered"></button>
                        <button class="ql-list" value="bullet"></button>
                        <button class="ql-indent" value="-1"></button>
                        <button class="ql-indent" value="+1"></button>
                        <button class="ql-align"></button>
                        <button class="ql-link"></button>
                        <button class="ql-clean"></button>
                    </div>
                    <div id="schedule_breakdown_editor" style="min-height: 420px;"></div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-info btn-sm" id="previewScheduleBreakdownBtn">
                    <i class="bx bx-show-alt me-1"></i> Preview
                </button>
                <div>
                    <button type="button" class="btn btn-link btn-sm text-dark"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dark btn-sm" id="saveScheduleBreakdownBtn">
                        <i class="bx bx-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
