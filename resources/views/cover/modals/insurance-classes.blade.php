<div class="modal fade" id="insurance-class-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('cover.save_insurance_class') }}" id="insuranceClassForm">
                @csrf

                <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}" />
                <input type="hidden" name="cover_no" value="{{ $cover->cover_no }}" />
                <input type="hidden" name="id" id="insurance_class_id" />

                <div class="modal-header">
                    <h5 class="modal-title text-white text-center" id="staticBackdropLabel">
                        <i class="bx bx-award me-2"></i>Insurance Classes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Treaty Information --}}
                    <div class="alert alert-info mb-3" role="alert">
                        <strong><i class="bx bx-info-circle me-2"></i>Treaty:</strong>
                        {{ $cover->cover_title ?? 'N/A' }}
                    </div>

                    <div class="row">
                        {{-- Reinsurance Class Selection --}}
                        <div class="col-md-12 mb-3">
                            <label for="reinclass" class="form-label required">
                                <i class="bx bx-category me-1"></i>Reinsurance Class
                            </label>
                            <select name="reinclass" id="reinclass" class="form-select form-inputs" required>
                                <option value="">--Select Reinsurance Class--</option>
                                @foreach ($coverReinclass as $reinclass)
                                    <option value="{{ $reinclass->reinclass }}"
                                        data-class-name="{{ $reinclass->rein_class->class_name ?? '' }}">
                                        {{ $reinclass->rein_class->class_name ?? 'Unknown Class' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Select the primary reinsurance class for this treaty
                            </small>
                        </div>

                        {{-- Insurance Classes Selection (Multiple) --}}
                        <div class="col-md-12 mb-3">
                            <label for="insurance_class" class="form-label required">
                                <i class="bx bx-list-ul me-1"></i>Insurance Classes
                            </label>
                            <select name="class[]" id="insurance_class" class="form-select form-inputs" multiple
                                required size="8">
                                <option value="" disabled>--Select Insurance Classes--</option>
                                @foreach ($insClasses as $ins_cls)
                                    <option value="{{ $ins_cls->class_code }}"
                                        data-class-name="{{ $ins_cls->class_name }}">
                                        {{ $ins_cls->class_code }} - {{ $ins_cls->class_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Hold <kbd>Ctrl</kbd> (Windows) or <kbd>Cmd</kbd> (Mac) to select multiple classes
                            </small>
                        </div>

                        {{-- Selected Classes Preview --}}
                        <div class="col-md-12 mb-3">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <i class="bx bx-check-circle me-1"></i>Selected Classes
                                    </h6>
                                    <div id="selected-classes-preview" class="d-flex flex-wrap gap-2">
                                        <span class="text-muted">No classes selected</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal"
                        id="close-insurance-class-modal">
                        <i class="bx bx-x me-1"></i>Close
                    </button>
                    <button type="button" id="ins-class-save-btn"
                        class="btn btn-primary btn-sm btn-wave waves-effect waves-light">
                        <i class="bx bx-save me-1"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
