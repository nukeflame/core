@extends('layouts.intermediaries.base')
@section('content')

<div class="card">
        <input type="hidden" name="entity_id" value="{{$entity_id}}" id="entity_id">
        <input type="hidden" name="context_id" value="{{$context_id}}" id="context_id">
        <input type="hidden" name="process_id" value="{{$process_id}}" id="process_id">
        <input type="hidden" name="dept_id" value="{{$dept_id}}" id="dept_id">
    <div class="card-header">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Process Documents</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Previous Documents</button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="table-responsive mt-5">
                    <table id="process-interface-table" class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Document Code</th>
                                <th>Document Name</th>
                                <th>Description/Commentary</th>
                                <th>Required</th>
                                <th>Uploaded</th>
                                <th>Date Uploaded</th>
                                <th>Integrated To Imagenow</th>
                                <th>Last Modification Date</th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
        </div>
    </div>
</div>


       


<!-- Modal -->
<div class="modal fade" id="approve_file_note_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Approve/Decline File Note</h4>
			</div>
			<div class="modal-body">
				<form  method="post" action="#" role="form" class="form-horizontal">
					{{csrf_field()}}

                    <input type="hidden" name="claim_no" id="claim_no">

					<div class="row" style="margin-top: 10px;">

						<div class="col-sm-6">
							<label class="required">Select File Note</label>
							<select class="form-control chosen" id="file_note" name="file_note">
								<option value="" selected>Please select File Note</option>
							</select>
						</div>

						<div class="col-sm-4">
							<label class="required">Action</label>
                            <select class="form-control chosen" id="approve_action" name="approve_action">
								<option value="" selected>Select Action</option>
								<option value="Y" selected>Approve</option>
								<option value="N" selected>Decline</option>
							</select>
						</div>
					</div>
	

			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btn-fill">Ok</button>
				</form>
				<button type="button" class="btn btn-default btn-red" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>
    

@endsection

@section('page_scripts')

<script type="text/javascript">
        $(document).ready(function(){
            var imagenow_process_code =  $('#process_id').val().trim();
            var imagenow_dept_class = '{!! $dept_id !!}'
            var context_id = $('#context_id').val().trim();
            var entity_id = $('#entity_id').val().trim();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
           
            uploadDocs()
         


            $('[href="#uploaded"]').closest('li').hide();
            
            // if process code is for requisition
            if (imagenow_process_code ==  34 && (imagenow_dept_class == 'CLM' || imagenow_dept_class == 'FAC' || imagenow_dept_class == 'UW' || imagenow_dept_class == 'CB')) {
                $('[href="#uploaded"]').closest('li').show();
               
            }else if(imagenow_process_code == 19 || imagenow_process_code == 39 || imagenow_process_code == 36 || imagenow_process_code == 92 || imagenow_process_code ==71 || imagenow_process_code == 11){
                $('[href="#uploaded"]').closest('li').show();
            }
            else{
                $('[href="#uploaded"]').closest('li').hide();
            }
            
           


            $('a[data-toggle="tab"]').on('shown.bs.tab',function(e){
                var currentTab = $(e.target).text();
                switch(currentTab){
                    case 'Previous Documents':
                        viewDocs()
                        break;

                    case 'Process Documents':
                        uploadDocs();
                        break;
                    
                    default:
                }
            });

            function uploadDocs(){
                $('#process-interface-table').DataTable({
                    "destroy": true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "{{ route('proceeinterface.list') }}",
                        'method': "GET",
                        "data": {
                            "process_code": imagenow_process_code,
                            "dept": imagenow_dept_class,
                            "entity_id": entity_id,
                            "context_id": context_id
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                        { data: 'document_code', name: 'document_code', orderable: true, searchable: true },
                        { data: 'document_name', name: 'document_name', orderable: true, searchable: true },
                        { data: 'commentary', name: 'commentary', orderable: true, searchable: true },
                        { data: 'mandatory_flag', name: 'mandatory_flag', orderable: true, searchable: true },
                        { data: 'uploaded', name: 'uploaded', orderable: true, searchable: true },
                        { data: 'date_uploaded', name: 'date_uploaded', orderable: true, searchable: true },
                        { data: 'sent', name: 'sent', orderable: true, searchable: true },
                        { data: 'last_modified', name: 'last_modified', orderable: true, searchable: true },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });

            };

            function viewDocs(){
                $('#uploaded-interface-table').DataTable({
                    "destroy": true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "#",
                        'method': "GET",
                        "data": {
                            "process_code": imagenow_process_code,
                            "dept": imagenow_dept_class,
                            "entity_id": entity_id,
                            "context_id": context_id
                        }
                    },
                    columns: [
                        { data: 'DT_Row_Index', name: 'DT_Row_Index' },
                        { data: 'document_code', name: 'document_code', orderable: true, searchable: true },
                        { data: 'document_name', name: 'document_name', orderable: true, searchable: true },
                        { data: 'mandatory_flag', name: 'mandatory_flag', orderable: true, searchable: true },
                        { data: 'uploaded', name: 'uploaded', orderable: true, searchable: true },
                        { data: 'date_uploaded', name: 'date_uploaded', orderable: true, searchable: true },
                        { data: 'sent', name: 'sent', orderable: true, searchable: true },
                        { data: 'last_modified', name: 'last_modified', orderable: true, searchable: true },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }
            
            
            

            $('#process-interface-table tbody').on('click', 'tr #upload', function() {
                alert(1)
                var document_code = $(this).attr('data-document_code');
                var imagenow_process_code = $(this).attr('data-process_code');
                var imagenow_dept_class = $(this).attr('data-dept');
                var entity_id = $(this).attr('data-entity_id');
                var context_id = $(this).attr('data-context_id');
                window.location.href = 'DocumentUpload/'+document_code+'/'+ entity_id +'/'+ context_id +'/'+ imagenow_process_code +'/'+ imagenow_dept_class +'';
            });

            $('#process-interface-table tbody').on('click', 'tr #view', function() {
                var document_id =  $(this).attr('data-document_id');
                var document_code = $(this).attr('data-document_code');
                var imagenow_process_code = $(this).attr('data-process_code');
                var imagenow_dept_class = $(this).attr('data-dept');
                var entity_id = $(this).attr('data-entity_id');
                var context_id = $(this).attr('data-context_id');

                // $.ajax({
                //     url: "#",
                //     type: "get",
                //     data: {"document_id":document_id},
                //     success: function(resp){
                //         var status = resp.status 
                //         var document = resp.document

                //         if (status == 1) {
                //             const dataUrl = `data:${document.file_type};base64,${document.base64_string}`;
                //             var base64String = document.base64_string
                //             var file_type = document.file_type
                //             debugBase64(dataUrl)

                //         }else{

                //             toastr.error('Failed to get data for given document');
                //         }
                //     },

                //     error: function(error) {
                //         toastr.error("Failed to send your request")
                //     }
                // }).done(function(){
                //     // $('#approvemodal').modal('toggle');
                // })


                window.location.href = 'ViewDocument/'+document_code+'/'+ entity_id +'/'+ context_id +'/'+ imagenow_process_code +'/'+ imagenow_dept_class +'/'+document_id+'';
            });


            function debugBase64(base64URL){
                var win = window.open();
                win.document.write('<iframe src="' + base64URL  + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');
            }


            $('#process-interface-table tbody').on('click', 'tr #delete', function() {
                var document_id =  $(this).attr('data-document_id');
                $(this).text('sending')
                $(this).addClass('disabled')

                $.ajax({
                    type: "post",
                    url: "#",
                    data: {"documentid":document_id},
                    success: function(resp){
                        if (resp.status == 200) {
                            toastr.success(resp.message)
                        }else if( resp.status == 202){
                            toastr.warning(resp.message)
                        }
                        else{
                            toastr.error(resp.message)
                        }
                    },
                    error: function(err){
                        toastr.error('Error while sending you request')
                    }
                }).done(function(){
                    $(this).text('remove')
                    $(this).removeClass('disabled')
                    window.location.reload();
                });
               
            });

            $('#uploaded-interface-table tbody').on('click', 'tr #view', function() {
                var document_id =  $(this).attr('data-document_id');
                var document_code = $(this).attr('data-document_code');
                var imagenow_process_code = $(this).attr('data-process_code');
                var imagenow_dept_class = $(this).attr('data-dept');
                var entity_id = $(this).attr('data-entity_id');
                var context_id = $(this).attr('data-context_id');

                window.location.href = '/ViewDocument/'+document_code+'/'+ entity_id +'/'+ context_id +'/'+ imagenow_process_code +'/'+ imagenow_dept_class +'/'+document_id+'';
            });
            
            $('#process-interface-table tbody').on('click', 'tr #approve_file_note', function() {
             
                var claim_no = $(this).attr('data-claim_no');
                $('#claim_no').val(claim_no)

                // file_note
                $.ajax({
                    url:"#",
                    dataType: "json",
                    data: { 'claim_no':claim_no},
                    success: function(resp) {

                        if (resp.status == 1) {
                            var filenotes = resp.fnotes

                            $("#file_note").empty();
                            $("#file_note").append($("<option>").attr('value','').text('Select File Note'));		
                            $.each(filenotes, function(i, item) {
                                $("#file_note").append($("<option>").attr('value', item.order_number).text(item.order_number+" - "+item.your_ref));
                            });

                        }else{
                            toastr.error("Failed to Load File note Listing")
                        }

                    },
                    error: function(resp){
                        console.log(resp)
                    }
                });
                // $('#claim_no').val(claim_no)
                // $('#order_num').val(order_num)

                $('#approve_file_note_modal').modal('show');
                
            });

            // $('#edms_back_btn').on('click',function(e){
            //     e.preventDefault();
            //     var process_code = $('#process_id').val().trim()
            //     var context_id = $('#context_id').val().trim();
               
            //     $.ajax({
            //         type: "post",
            //         url: "#",
            //         data: { "process_code": process_code, "context_id" : context_id},
            //         success : function(response) {
            //             window.location.replace(response);
            //         },
            //         error: function(err){
            //             toastr.error("Error trying to redirect back. Kindly reload your page.")
            //         }
            //     });
            // });

        });
</script>
@endsection
