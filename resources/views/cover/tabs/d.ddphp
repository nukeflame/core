@props(['cover', 'schedHeaders'])

<div class="modal fade" id="schedulesModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="schedulesForm">
                @csrf
                <input type="hidden" name="_method" value="POST" />
                <input type="hidden" name="cover_no" value="{{ $cover->cover_no }}" />
                <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}" />
                <input type="hidden" name="id" id="schedule_id" />
                <input type="hidden" name="title" id="schedule_title" />

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-table-line me-2"></i>Schedule Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label required">Schedule Item</label>
                            <select name="header" id="sched-header" class="form-select" required>
                                <option value="">--Select Schedule Item--</option>
                                @foreach ($schedHeaders as $header)
                                    <option value="{{ $header->id }}" data-name="{{ $header->name }}">
                                        {{ $header->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Position</label>
                            <input type="number" name="schedule_position" id="schedule_position" class="form-control"
                                min="1" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label required">Details</label>
                            <div id="schedule_description" class="form-control" contenteditable="true"
                                style="min-height: 400px; max-height: 500px; overflow: auto;">
                            </div>
                            <textarea id="hidden_schedule_description" name="details" class="d-none" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="schedule-save-btn">
                        <i class="ri-save-line me-1"></i> Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #schedule_description {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.75rem;
    }

    #schedule_description:focus {
        outline: none;
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .required::after {
        content: " *";
        color: #dc3545;
    }
</style>
