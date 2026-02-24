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
                                    <input type="text" name="documentcode" value="{{ $document->document_code }}"
                                        id="documentcode" class="form-control" readonly>
                                </td>
                                <td>
                                    <input type="text" name="document_description"
                                        value="{{ $document->document_name_alias }}" class="form-control" readonly>
                                </td>
                            </tr>

                            <tr>
                                <td>Description/Comentary:</td>
                                <td colspan="2">
                                    <input type="text" name="commentary"
                                        value="{{ $document_dict->document_description }}" class="form-control">
                                </td>
                            </tr>

                            <tr id="document_status_div">
                                <td>Document status:</td>
                                <td colspan="2">
                                    <input type="text" name="document_status" value="" id="document_status"
                                        class="form-control" readonly>
                                </td>
                            </tr>

                            @if ($eft_no != null)
                                <tr id="eft_no">
                                    <td>EFT No</td>
                                    <td colspan="2">
                                        <input type="text" name="eft_no" value="{{ $eft_no }}" id="eft_no"
                                            class="form-control" readonly>
                                    </td>
                                </tr>
                            @endif

                        </table>
                    </div>

                    <!-- <input type="hidden" name="base64_string" value="{{ $base64_encoded_string }}" id="base64_string" reandonly> -->
                    @if ($document_dict->file_size > 1.2 * 1024 * 1024)
                        <div id="view-link" class="mt-3 p-4 text-center"
                            style="height: 300;width:100%;border: 1px solid #DDD;">
                            <a href="{{ route('view.largefile', $document_dict->document_id) }}" id="preview_btn"
                                role="button" class="btn btn-primary">File too Large Click here to Open</a>
                        </div>
                    @else
                        <object data="data:{{ $file_type }};base64,{{ $base64_encoded_string }}" type=""
                            height="600" width="100%" style="border: solid 1px #DDD;"></object>
                    @endif
                    <p class="mt-3">&nbsp;</p>



                </div>
                <div class="col-md-4" style="border: solid 1px #DDD; border-radius: 5px; padding-left: 5px;">
                    <h4 class="text-center">Comments</h4>
                    <a role="button" class="btn btn-sm btn-primary" id="add-comment"> new comment</a>
                    <hr>
                    <table id="comments-table" class="table table-borderless">
                        <thead>
                            <th>Date</th>
                            <th>User</th>
                            <th>Comment</th>
                            <th>Action</th>

                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>



    <!-- Upload Doc -->
    <div class="modal fade" tabindex="-1" role="dialog" id="upload-doc-modal" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
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

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-addcomment" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Comment</h4>
                </div>
                <div class="modal-body">
                    <form id="comment_form">
                        {{ csrf_field() }}
                        <input type="hidden" name="doc_id" value="{{ $document_dict->document_id }}" id="doc_id">
                        <input type="hidden" name="context_id" value="{{ $document_dict->context_id }}">
                        <input type="hidden" name="process_code" value="{{ $document_dict->process_code }}">
                        <input type="hidden" name="document_code" value="{{ $document_dict->document_code }}">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="required">Comment</label>
                                <textarea rows="10" columns="20" type="text" name="comment" id="comment" class="form-control"
                                    required> </textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="alert_user">Send To</label>
                                <select name="alert_user[]" id="alert_user" class="form-control multiple-select"
                                    multiple>
                                    <option value="">Select User</option>
                                    @foreach ($aimsusers as $aimsuser)
                                        <option value="{{ $aimsuser->user_id }}">{{ $aimsuser->name }} </option>
                                    @endforeach

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

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-editcomment" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Comment</h4>
                </div>
                <div class="modal-body">
                    <form id="edit_comment_form">
                        {{ csrf_field() }}
                        <input type="hidden" name="doc_id" value="{{ $document_dict->document_id }}" id="doc_id">
                        <input type="hidden" name="item_no" value="" id="item_no">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="required">Comment</label>
                                <textarea rows="10" columns="20" type="text" name="comment" id="edcomment" class="form-control"
                                    required> </textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="alert_user">Send To</label>
                                <select name="alert_user[]" id="edalert_user" class="form-control multiple-select"
                                    multiple>
                                    <option value="">Select User</option>
                                    @foreach ($aimsusers as $aimsuser)
                                        <option value="{{ $aimsuser->user_id }}">{{ $aimsuser->name }} </option>
                                    @endforeach

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


    <div class="modal fade" tabindex="-1" role="dialog" id="modal-approvedocument" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Approve/Reject</h4>
                </div>
                <div class="modal-body">
                    <form id="approve">
                        {{-- <form action="{{ route('approve.document') }}" method="POST"> --}}
                        {{ csrf_field() }}
                        <input type="hidden" name="document_id" value="{{ $document_dict->document_id }}"
                            id="document_id">
                        <div class="row">
                            <div class="col-md-9">
                                <label class="required">Want to:</label>
                                <select name="want_to" id="want_to" class="form-control chosen">
                                    <option value="">Select Option</option>
                                    <option value="Y">Approve</option>
                                    <option value="R">Reject</option>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success btn-sm">Submit</button>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#upload-btn').on('click', function(e) {
                e.preventDefault();
                $('#upload-doc-modal').modal('show');
            })
            var document_id = "{!! $document_dict->document_id !!}"
            var approved = "{!! $document_dict->approved !!}"
            var process_code = $('#pro_code').val();

            if (document_id != null && (process_code == 34 || process_code == 24)) {
                $('#document_status_div').show()
                $('#approve-btn').show()
                if (approved == 'Y') {
                    $('#document_status').val('Approved')
                } else if (approved == 'N') {
                    $('#document_status').val('Not Approved')
                } else if (approved == 'R') {
                    $('#document_status').val('Rejected')
                } else {
                    $('#document_status').val('Pending')
                }
            } else {
                $('#document_status_div').hide()
                $('#approve-btn').hide()
            }

            // check uploaded file
            $('#file_upload').on('change', function(e) {
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

            });

            $('#add-comment').on('click', function(e) {
                e.preventDefault();
                var documentCode = $('#doc_id').val().trim();

                if (documentCode.length != 0) {
                    $('#modal-addcomment').modal('show');
                } else {
                    toastr.warning('You must upload and save the document to add a comment on it')
                }

            });

            // edit comment
            $('#comments-table').on('click', 'tr #edit_process', function(e) {
                e.preventDefault();

                var comment_id = $(this).attr("data-comment_id")

                if (comment_id) {
                    $.ajax({
                        type: "POST",
                        url: "#",
                        data: {
                            "comment_id": comment_id
                        },
                        success: function(resp) {
                            var status = resp.status
                            if (status == 1) {
                                var data = resp.data

                                $('#edcomment').val(data.doc_comment)
                                $('#item_no').val(data.item_no)
                                $('#modal-editcomment').modal('show');

                            }
                            if (status == 0) {
                                toastr.error('<strong>Error :</strong> ' + resp.message);
                            }

                        },
                        error: function() {
                            toastr.error(
                                'Error while trying to submit your data.Kindly check and try again'
                            )

                        }
                    }).done(function() {

                    });

                } else {
                    toastr.error(
                        "No comment unique id was generated initially. This action can only be done on comments added now and hence forth."
                    )
                }

            });


            $('#comments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('edms.comments') }}",
                    'data': {
                        "documentcode": $('#doc_id').val().trim()
                    }
                },
                columns: [{
                        data: 'created_on',
                        name: 'created_on',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'username',
                        name: 'user',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'doc_comment',
                        name: 'comment',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: true
                    },

                ]
            });

            $('#comment_form').on('submit', function(e) {
                e.preventDefault();
                var comment_form_data = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "{!! route('document.comment') !!}",
                    data: comment_form_data,
                    success: function(resp) {
                        var status = resp.status
                        if (status == 1) {
                            toastr.success('<strong>Succes :</strong> ' + resp.message);
                        }
                        if (status == 0) {
                            toastr.error('<strong>Error :</strong> ' + resp.message);
                        }
                        $('#modal-addcomment').modal('hide');
                    },
                    error: function() {
                        toastr.error(
                            'Error while trying to submit your data.Kindly check and try again'
                        )

                    }
                }).done(function() {
                    // $('#save-btn').text('Save');
                    $('#modal-addcomment').modal('hide');
                    $('#comments-table').DataTable().ajax.reload();
                });
            })

            $('#edit_comment_form').on('submit', function(e) {
                e.preventDefault();
                var comment_form_data = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "#",
                    data: comment_form_data,
                    success: function(resp) {
                        var status = resp.status
                        if (status == 1) {
                            toastr.success('<strong>Succes :</strong> ' + resp.message);
                        }
                        if (status == 0) {
                            toastr.error('<strong>Error :</strong> ' + resp.message);
                        }
                        $('#modal-editcomment').modal('hide');
                    },
                    error: function() {
                        toastr.error(
                            'Error while trying to submit your data.Kindly check and try again'
                        )

                    }
                }).done(function() {
                    $('#modal-editcomment').modal('hide');
                    $('#comments-table').DataTable().ajax.reload();
                });
            })

            var redirect_route = null

            $('#save-btn').on('click', function(e) {
                e.preventDefault();
                var form_data = $('#form-data').serialize();
                $('#save-btn').addClass('disabled');
                $('#approve-btn').addClass('disabled');
                $('#reject-btn').addClass('disabled');
                $('#save-btn').text('Saving');

                var base64_string = $('#base64_string').val();

                if (base64_string.length <= 0) {
                    $('#save-btn').removeClass("disabled");
                    $('#approve-btn').removeClass('disabled');
                    $('#reject-btn').removeClass('disabled');
                    $('#save-btn').text('Save');
                    toastr.error('Could Not detect any File or Image Uploaded');
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{!! route('save_to_dictionary') !!}",
                        data: form_data,
                        success: function(resp) {
                            var status = resp.status
                            $('#save-btn').removeClass("disabled");
                            $('#approve-btn').removeClass('disabled');
                            $('#reject-btn').removeClass('disabled');
                            $('#save-btn').text('Save');

                            if (status == 1) {
                                toastr.success('<strong>Succes :</strong> ' + resp.message);

                                var redirect_route = resp.redirect_param

                                window.location.href = '/processInterface/' + redirect_route
                                    .entity_id + '/' + redirect_route.context_id + '/' +
                                    redirect_route.process_code + '/' + redirect_route.dept +
                                    '';


                            }
                            if (status == 2) {
                                toastr.success('<strong>Succes :</strong> ' + resp.message);
                            }
                        },
                        error: function() {
                            toastr.error(
                                'Error while trying to submit your data.Kindly check and try again'
                            )
                        }
                    }).done(function() {
                        $('#save-btn').removeClass("disabled");
                        $('#approve-btn').removeClass('disabled');
                        $('#reject-btn').removeClass('disabled');
                        $('#save-btn').text('Save');
                    });
                }


            });


            $(document).on('submit', '#upload-form', function(e) {

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
                    success: function(resp) {
                        if (resp.status == 1) {
                            toastr.success("Success in generating base64 compressed file")
                            $('#preview-object object').attr('data', 'data:' + resp.file_type +
                                ';base64,' + resp.base64_encoded_image);
                            $('#base64_string').val(resp.base64_encoded_image);


                        } else {
                            toastr.success("Failed to generate base64 compressed file")
                        }
                    },

                    error: function(err) {

                    }
                }).done(function() {
                    // window.location.reload();
                    $('#upload_file_btn').text('Save')
                    $('#upload_file_btn').removeClass('disabled')
                    $('#upload-doc-modal').modal('hide');
                });
            });

            $('#approve-btn').on('click', function(e) {

                e.preventDefault();
                $('#modal-approvedocument').modal('show')
            })


            $('#approve').on('submit', function(e) {

                e.preventDefault();

                var formdata = $(this).serialize();

                $.ajax({
                    url: "#",
                    type: "post",
                    data: formdata,
                    success: function(resp) {
                        var status = resp.status

                        if (status == 1) {
                            toastr.success(resp.message)

                        } else {
                            toastr.error('Failed to  process the request')
                        }
                    },

                    error: function(error) {
                        toastr.error("Failed to send your approval action")
                    }
                }).done(function() {
                    window.location.reload()
                })
            })

            $('#addcomment').on('click', function() {

                if ($('#base64_string').val().length) {
                    $('#upload-doc-modal').modal('show');
                } else {
                    toastr.info("You must upload a document to add comment")
                }

            });


            $('#edms_document').on('click', function() {
                var entity_id = $('#ent_code').val().trim();
                var context_id = $('#cont_code').val().trim();
                var process_code = $('#pro_code').val().trim();
                var dept = $('#dep_code').val().trim();

                window.location.href = '/processInterface/' + entity_id + '/' + context_id + '/' +
                    process_code + '/' + dept + '';
            })

        });
    </script>
@endpush
