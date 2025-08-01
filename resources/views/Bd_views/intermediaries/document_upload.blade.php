@extends('layouts.intermediaries.base')
@section('content')
   
 
<div class="row">
    <form id="form-data">
            {{ csrf_field() }}
        <div class="row">
            <input type="hidden" name="process_code" value="{{ $process_code }}" id="pro_code">
            <input type="hidden" name="entity_id" value="{{ $entity_id }}" id="ent_code">
            <input type="hidden" name="context_id" value="{{ $context_id }}" id="cont_code">
            <input type="hidden" name="dept" value="{{ $dept }}" id="dep_code">
            <div class="col-md-8">
                <div style="border: solid 1px #DDD; border-radius: 5px; padding-left: 5px;">
                    <table class="table">
                        <tr>
                            <td>Document:</td>
                            <td>
                                <input type="text" name="documentcode" value="{{ $document->document_code }}" id="documentcode" class="form-control" readonly>
                            </td>
                            <td>
                            <input type="text" name="document_description" value="{{ $document->document_name_alias }}" class="form-control" readonly>
                            </td>
                        </tr>

                        <tr>
                            <td>Description/Comentary:</td>
                            <td colspan="2">
                                <input type="text" name="commentary" @if(!empty($document_dict)) value="{{ $document_dict->document_description }}" @endif  class="form-control">
                            </td>
                        </tr>

                        <tr id="document_status_row">
                            <td>Document status:</td>
                            <td colspan="2">
                                <input type="text" name="document_status" value="" id="document_status"  class="form-control" readonly>
                            </td>
                        </tr>

                        <tr id="eft_no_div">
                            <td>EFT No</td>
                            <td colspan="2">
                                <input type="text" name="eft_no" value="" id="eft_no"  class="form-control">
                            </td>
                        </tr>

                        <tr id="fnote_no_div">
                            <td>File Note No</td>
                            <td colspan="2">
                                <input type="text" name="fnote_no" value="" id="fnote_no"  class="form-control" required>
                            </td>
                        </tr>

                    </table> 
                </div>

                <div class="pull-right mt-3 mb-3">
                    <input type="file" name="file_to_upload" id="imgupload" style="display:none"/> 
                    <button  id="upload-btn" class="btn btn-info btn-sm">Upload</button>
                    <a role="button" href="#" id="download-btn" class="btn btn-success btn-sm">Download</a>
                </div>

                <input type="hidden" name="base64_string" value="{{ $base64_encoded_image??null }}" id="base64_string" reandonly>
                <input type="hidden" name="file_type" value="{{ $file_type??null }}" id="file_type" reandonly>

                <div id="preview-object">
                    <object  data="data:{{$file_type??null}};base64,{{ $base64_encoded_image??null }}"  height="600" width="100%" style="border: solid 1px #DDD;"></object>
                    <!-- <iframe  src="data:{{$file_type??null}};base64,{{ $base64_encoded_image??null }}"  height="600" width="100%" style="border: solid 1px #DDD;"></iframe> -->
                </div>

                <div id="view-link" class="mt-3 p-4 text-center" style="height: 300;width:100%;border: 1px solid #DDD;">
                        <p class="text-danger"><strong>this file is too large to be previewed. Save It first and click view to confirm its validity</strong></p>
                </div>
                
                
                <div class="mt-3 mb-4 pull-right">
                    <button id="save-btn" class="btn btn-primary btn-sm">Save</button>
                    <button id="approve-btn" class="btn btn-info btn-sm">request approval</button>
                </div>
                
            </div>
            <div class="col-md-4" style="border: solid 1px #DDD; border-radius: 5px; padding-left: 5px;">
                <h4 class="text-center">Comments</h4>
                
                <table id="comments-table" class="table table-borderless">
                    <thead>
                        <th>Date</th>
                        <th>User</th>
                        <th>Comment &nbsp;&nbsp;<a style="cursor: pointer;" id="add-comment"><i class="fa fa-plus-square-o"></i> Add</a></th>
                        <!-- <th><a style="cursor: pointer;" data-toggle="modal" data-target="#modal-addcomment"><i class="fa fa-plus-square-o"></i> Add</a></th> -->
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
 
    
    <!-- Upload Doc -->
    <div class="modal fade" tabindex="-1" role="dialog" id="upload-doc-modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Upload Document</h4>
              </div>
            <div class="modal-body">
                <!-- <form action="{{ route('upload.document') }}" method="post" enctype="multipart/form-data"> -->
                <form id="upload-form" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <input type="hidden" name="doc_code" value="{{ $document->document_code }}">
                    <input type="hidden" name="pro_code" value="{{ $process_code }}">
                    <input type="hidden" name="ent_id" value="{{ $entity_id }}">
                    <input type="hidden" name="cxt_id" value="{{ $context_id }}">
                    <input type="hidden" name="dept_id" value="{{ $dept }}">
                    <div class="row">
                        <div class="col-md-9">
                            <label class="required">Upload File (Pdf)</label>
                            <input type="file" name="file_upload" id="file_upload" class="form-control" required>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success btn-sm" id="upload_file_btn">Upload</button>
                </form>
            </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-addcomment" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Comment</h4>
              </div>
            <div class="modal-body">
                <form id="comment_form" >
                {{ csrf_field() }}
                    <input type="hidden" name="doc_id" value="" id="doc_id">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="required">Comment</label>
                            <textarea colums="30" rows="10" type="text" name="comment" id="comment" class="form-control" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="alert_user">Send To</label>
                            <select name="alert_user" id="alert_user" class="form-control multiple-select" multiselect>
                                <option value="">Select User</option>
                            </select>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success btn-sm">Save</button>
                </form>
            </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>



    <div class="modal fade" tabindex="-1" role="dialog" id="approvemodal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Request Approval</h4>
              </div>
            <div class="modal-body">
                <form id="approval_request_form">
                {{ csrf_field() }}
                    <input type="hidden" name="document_id" value="" id="document_id">
                    <div class="row">
                        <div class="col-md-9">
                            <label class="required">Send to Approver</label>
                            <select name="approver" id="approver" class="form-control chosen">
                                <option value="">Select Approver</option>
                            </select>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success btn-sm">Send</button>
                </form>
            </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection
@section('page_scripts')


    <script>

        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var document_id = "{{!! $document_dict->document_id??null !!}}"
            var process_code = $('#pro_code').val();

            // file note no
            if (process_code != 70) {
                $('#fnote_no_div').hide()
                $('#fnote_no').removeAttr('required')
            }

            $('#upload-btn').on('click',function(e){
                e.preventDefault();
                $('#upload-doc-modal').modal('show');
            })

            $('#view-link').hide()

            // check uploaded file
            $('#file_upload').on('change',function(e) {
                var ext = this.value.match(/\.([^\.]+)$/)[1];
                switch (ext) {
                    case 'pdf':
                    case 'png':
                    case 'jpeg':
                    case 'jpg':
                        break;
                    default:
                        alert('File Type Not allowed');
                        this.value = '';
                }

                var file_size = e.target.files[0].size

                if (file_size > 1.2 * 1024 * 1024) {

                    $('#view-link').show()
                    $('#preview-object').hide()
                }else{
                    $('#view-link').hide()
                    $('#preview-object').show()
                }

                $('#file_size').val(file_size)

                const max_size = 40 * 1024 * 1024 // 40mbs
                if(file_size > max_size){
                    alert("File too big!. Max allowed is 40mb");
                    this.value = "";
                }

            });

            // preview document
            $('#preview_file').on('click',function(){
                var file_type = $('#file_type').val()
                // var base64_string = $('#base64_string').val()
                var base64_string = ""
                var file_size = $('#file_size')
                var form_data = $('#preview_param_form').serialize()

                $.ajax({
                    type: "post",
                    url: "{{ route('preview_file') }}",
                    data: form_data,
                    success: function(){

                    },
                    error: function(){

                    }
                })
            });

            if(document_id != null && (process_code != 34 || process_code != 24) ){
                $('#document_status_row').hide()
            }else{
                $('#document_status_row').show()
            }

            if(process_code == 34 || process_code == 24){
                $('#approve-btn').show()
            }else{
                $('#approve-btn').hide()
            }

            $('#add-comment').on('click',function(e){
                e.preventDefault();
                var documentCode = $('#doc_id').val().trim();
           
                if (documentCode.length != 0) {
                    $('#modal-addcomment').modal('show');
                }else{
                    toastr.warning('You must upload and save the document to add a comment on it')
                }
               
            });

            $('#approve-btn').addClass('disabled')

            $('#approve-btn').on('click',function(e){
                e.preventDefault()
                var document_id = $('#document_id').val()
                if(document_id.length == 0){
                    toastr.error('Document must be saved before sending an approval request')
                }else{

                    $('#approvemodal').modal('show');
                }
                
            });

            $('#approval_request_form').on('submit',function(e){

                e.preventDefault();

                var formdata = $(this).serialize();

                $.ajax({
                    url: "#",
                    type: "post",
                    data: formdata,
                    success: function(resp){
                        var status = resp.status 

                        if (status == 1) {
                            toastr.success('Successfully sent approval request')
                        }else{
                            toastr.error('Failed to send approval request')
                        }
                    },

                    error: function(error) {
                        toastr.error("Failed to send your request")
                    }
                }).done(function(){
                    $('#approvemodal').modal('toggle');
                })
            })


            $('#comment_form').on('submit',function(e){
                e.preventDefault();
                var comment_form_data = $(this).serialize();

                $.ajax({
                        type: "POST",
                        url: "{!! route('document.comment') !!}",
                        data: comment_form_data,
                        success: function(resp){
                            var status = resp.status 
                            if (status == 1) {
                                toastr.success('<strong>Succes :</strong> '+resp.message);
                            }if(status == 0){
                                toastr.error('<strong>Error :</strong> '+resp.message);
                            }
                            $('#modal-addcomment').modal('hide');
                        },
                        error: function(){
                            toastr.error('Error while trying to submit your data.Kindly check and try again')

                        }
                    }).done(function() {
                        $('#modal-addcomment').modal('hide');
                        $('#comments-table').DataTable().ajax.reload();
                    }); 
            })

            var redirect_route = null

            $('#save-btn').on('click',function(e){
                e.preventDefault();
                var form_data = $('#form-data').serialize();
                $('#save-btn').addClass('disabled');
                $('#approve-btn').addClass('disabled');
                $('#reject-btn').addClass('disabled');
                $('#save-btn').text('Saving');
               
                var base64_string = $('#base64_string').val();

                if(base64_string.length <= 0) {
                    $('#save-btn').removeClass("disabled");
                    $('#approve-btn').removeClass('disabled');
                    $('#reject-btn').removeClass('disabled');
                    $('#save-btn').text('Save');
                    toastr.error('Could Not detect any File or Image Uploaded');
                }else {
                    $.ajax({
                        type: "POST",
                        url: "{!! route('save_to_dictionary') !!}",
                        data: form_data,
                        success: function(resp){
                            var status = resp.status 
                        
                            if (status == 1) {
                                toastr.success('<strong>Succes :</strong> '+resp.message);
                                
                                redirect_route = resp.redirect_param

                                $('#doc_id').val(resp.documentID)
                                $('#document_id').val(resp.documentID)
                                fetchComments(resp.documentID)
                                $('#approve-btn').removeClass('disabled')

                               // window.location.href = '/processInterface/'+ redirect_route.entity_id +'/'+ redirect_route.context_id +'/'+ redirect_route.process_code +'/'+ redirect_route.dept +'';

                            }if(status == 2){
                                $('#save-btn').removeClass("disabled");
                                toastr.error('<strong>Succes :</strong> '+resp.message);
                            }
                        },
                        error: function(){
                            $('#save-btn').removeClass("disabled");
                            toastr.error('Error while trying to submit your data.Kindly check and try again')
                        }
                    }).done(function() {
                            $('#save-btn').text('Save');
                    }); 
                }


            });


            $.ajax({
                url: "{{ route('get_approvers') }}",
                type: "post",
                success: function(resp){
                    var status = resp.status
                    var user_groups = resp.user_groups
                    var users = resp.users

                    if (status == 1) {
                        $.each(users, function(index,user){
                            $.each(user_groups, function(key,group){
                                if (group.group_id == user.aims_group) {
                                    $('#approver').append('<option value="'+user.user_id+'">'+user.name+'</option>')
                                }
                            })
                        })
                    }else{
                        toastr.warning('While loading approvers')
                    }
                }
            })


            $(document).on('submit','#upload-form',function(e){

                e.preventDefault();
                
                $('#upload_file_btn').text('Loading...')
                $('#upload_file_btn').addClass('disabled')

                $.ajax({
                    type: "post",
                    url: "{!! route('upload.document') !!}",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    enctype: "multipart/form-data",
                    data: new FormData(this),
                    success: function(resp){
                        if (resp.status == 1) {
                            toastr.success("Success in generating base64 compressed file")
                            $('#preview-object object').attr('data','data:'+resp.file_type+';base64,'+resp.base64_encoded_image);
                            $('#base64_string').val(resp.base64_encoded_image);
                            $('#file_type').val(resp.file_type);

                            $('#prbase64_string').val(resp.base64_encoded_image);
                            $('#prfile_type').val(resp.file_type);
                            
                        }else{
                            toastr.error("Failed to generate base64 compressed file")
                        }
                    },

                    error: function(err){

                    }
                }).done(function(){
                    // window.location.reload();
                    $('#upload_file_btn').text('Save')
                    $('#upload_file_btn').removeClass('disabled')
                    $('#upload-doc-modal').modal('hide');
                });
            });

            $('#addcomment').on('click',function(){

                if ($('#base64_string').val().length) {
                    $('#upload-doc-modal').modal('show');
                }else{
                    toastr.info("You must upload a document to add comment")
                }
              
            });

            $('#edms_document').on('click',function(){
                var entity_id = $('#ent_code').val().trim();
                var context_id = $('#cont_code').val().trim();
                var process_code = $('#pro_code').val().trim() ;
                var dept = $('#dep_code').val().trim();

                window.location.href = '/processInterface/'+ entity_id +'/'+ context_id +'/'+ process_code +'/'+ dept +'';
            })

            $('#comments-table').DataTable({})

        });

        function fetchComments(comment_code){
            $('#comments-table').DataTable({
                "destroy": true,
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('edms.comments') }}",
                    'method': "GET",
                    'data':{
                        "documentcode": comment_code
                    } 
                },
                columns: [
                    {data: 'created_on', name: 'created_on',orderable: false, searchable: true},
                    {data: 'username', name: 'user',orderable: false, searchable: true},
                    {data: 'doc_comment', name: 'comment',orderable: false, searchable: true},
                    
                ]
            });
        }

    </script>
   
@endsection