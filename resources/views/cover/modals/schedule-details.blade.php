<div class="modal fade" id="addScheduleModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticScheduleDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="schedulesForm" data-post-url="{{ route('cover.add_schedule') }}"
                data-put-url="{{ route('cover.amend_schedule') }}">
                @csrf
                @method('POST')
                <input type="hidden" name="cover_no" value="{{ $cover->cover_no }}" />
                <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}" />
                <input type="hidden" name="id" id="id" />
                <input type="hidden" name="schedule_id" id="schedule_id" />

                <div class="modal-header">
                    <h5 class="modal-title dc-modal-title" id="staticScheduleDetailsLabel">
                        Schedule Details
                    </h5>
                    <button type="button" aria-label="Close" class="btn-close btn-close-white closeScheduleForm"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label class="form-label fs-14" for="sched-header">Schedule item</label>
                            <select name="header" id="sched-header" class="form-inputs select2" required>
                                <option value="">--Select Schedule items--</option>
                                @foreach ($schedHeaders as $hdr)
                                    <option value="{{ $hdr->id }}" data-name="{{ $hdr->name }}">
                                        {{ $hdr->name }}
                                    </option>
                                @endforeach
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

                    <hr>

                    <input type="hidden" name="title" id="title" class="form-control color-blk" required />

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fs-14" for="schedule_description">Details</label>
                                </div>
                            </div>
                            <div class="form-control section fac_section" id="schedule_description"
                                contenteditable="true"
                                style="border: 1px solid #363434; padding: 8px; min-height: 400px;
                                        resize: none; width:100%; overflow: auto; max-height: 500px;
                                        background-color: var(--input-bg-color);
                                        color: var(--input-text-color); border-radius: 0px;">
                            </div>
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
