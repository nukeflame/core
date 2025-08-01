<!-- modal for adding new coa level-->
<div id="newcoalevel_modal" class="modal fade" role="dialog">
    <form id="newcoalevel_form">
      {{csrf_field()}}
   <div class="modal-dialog">
      <div class="modal-content">
  
        <div class="modal-header">
          {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
          <h4 class="modal-title" id="paramlabel">New COA Level</h4>
        </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
            <label class="required">Level ID</label>
            <input type="text" class="form-inputs" name="level_id" id="level_id">
          </div> 
  
          <div class="col-md-8">
            <label class="required">Name</label>
            <input type="text" class="form-inputs" name="level_name" id="level_name" onkeyup="this.value=this.value.toUpperCase()">
          </div>
        </div>
      </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-fill" id="post_coalevel">Save</button>
        </div>
      </div>
    </div>
  </form>
  </div>
  