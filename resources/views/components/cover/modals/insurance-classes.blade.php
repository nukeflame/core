@props(['cover', 'coverReinclass', 'insClasses'])

<div class="modal effect-scale md-wrapper" id="insurance-class-modal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('cover.save_insurance_class') }}" id="insuranceClassForm">
                @csrf
                <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}" />
                <input type="hidden" name="cover_no" value="{{ $cover->cover_no }}" />
                <div class="modal-header">
                    <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Insurance classes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label for="treaty" class="form-label">Treaty Name</label>
                            <input type="text" class="form-control fw-bold" id="treaty" name="treaty"
                                value="{{ $cover->cover_title }}" readonly />
                        </div>
                        <div class="mb-3">
                            <label for="reinclass" class="form-label">Reinsurance Class</label>
                            <select name="reinclass" id="reinclass" class="form-select" required>
                                <option value="">--Select class--</option>
                                @foreach ($coverReinclass as $reinclass)
                                    <option value="{{ $reinclass->reinclass }}">
                                        {{ $reinclass->rein_class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="class" class="form-label">Class</label>
                            <select name="class[]" id="insurance_class" class="form-select" multiple required>
                                <option value="">--Select class--</option>
                                @foreach ($insClasses as $ins_cls)
                                    <option value="{{ $ins_cls->class_code }}">{{ $ins_cls->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="ins-class-save-btn">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
