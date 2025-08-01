<div class="modal fade" id="new_coaleveldtl_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="newlevelcategory_form">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5">
                                <label for="">Level</label>
                                <select class="form-inputs chosen-select" name="level_id" id="level_id" @required(true)>
                                    <option value="">Select Category Level</option>
                                    @foreach($levels as $level)
                                        <option value="{{ trim($level->level_id) }}">{{ trim($level->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label for="">Parent</label>
                                <select class="form-inputs" name="parent_id" id="parent_id" @required(true)></select>
                            </div>
                            <div class="col-md-10">
                                <label for="">Description</label>
                                <input type="text" class="form-inputs" name="description" id="description" onkeyup="this.value=this.value.toUpperCase()" @required(true)>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="post_levelcategory" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>