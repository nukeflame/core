
@extends('layouts.intermediaries.base')
@section('content')
<style>
    .align_right{
        text-align: right;
        padding: 20px 0px;
    }
</style>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div>
                <h3 class="">Upload supporting documents</h3>
                <hr>

                <div class="col-md-8 mx-3" style="border: 1px solid red">
                    <ul>
                        <li>
                            <i>Please upload all the documents</i>
                        </li>
                        <li>
                            <i>Documents once uploaded cannot be changed when payments are made </i>
                        </li>
                    </ul>
                </div>
                <br>
                <div class="mx-3">
                    <form id="quote_docs" enctype="multipart/form-data" >
                        @csrf
                        <input name="quote_no" type="text" value="{{ $quote_no }}" hidden> 
                        <div class="col-md-8">
                            @foreach ($docs as $doc)
                                @if(count($uploaded_docs) > 0)
                                    <div>
                                        <label for="">{{$doc->document_description}}</label>
                                        @if(!is_null($doc->uploaded))
                                            <label class="text-success fw-bold" style="font-style:italic">(Document already uploaded, select again to reupload)</label>
                                            <input class="form-control" type="file" name="file[]" />
                                        @else
                                            @if($doc->mandatory_flag == 'Y') <span class="text-danger fw-bold">*</span> @endif
                                            <input class="form-control" type="file" name="file[]" @if($doc->mandatory_flag == 'Y') required @endif />
                                        @endif
                                        <input  name="doc_type[]" type="text" value="{{ $doc->document_code }}" hidden />
                                        <input  name="process_id[]" type="text" value="{{ $doc->process_code }}" hidden />
                                    </div>
                                @else
                                    <div>
                                        <label for="">{{$doc->document_description}}</label>
                                        @if($doc->mandatory_flag == 'Y') <span class="text-danger fw-bold">*</span> @endif
                                        <input class="form-control" type="file" name="file[]" @if($doc->mandatory_flag == 'Y') required @endif />
                                        <input  name="doc_type[]" type="text" value="{{ $doc->document_code }}" hidden />
                                        <input  name="process_id[]" type="text" value="{{ $doc->process_code }}" hidden />
                                    </div>
                                @endif
                            @endforeach
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div>
            <x-button.submit class="col-md-2 float-end" id="save_docs">Save</x-button>
        </div>
        </form>
    </div>
</div>

  


           

@endsection

@section('page_scripts')

<script type="text/javascript">
    $(document).ready(function () {
        $('input:file').on('change',function(e) {
            var maxFileSize = 2 * 1024 * 1024; // 5 MB
            var fileSize = $(this)[0].files[0].size;

            if (fileSize > maxFileSize) {
                    swal.fire({
                        icon: 'warning',
                        text: 'File size must not exceed 2MB.'
                    })
                this.value = ''
            }else{
                var ext = this.value.match(/\.([^\.]+)$/)[1];
                switch (ext) {
                    case 'pdf':
                    case 'png':
                    case 'jpeg':
                    case 'jpg':
                        break;
                    default:
                        swal.fire({
                            icon: 'warning',
                            text: 'File type not allowed, kindly upload .pdf, .png or .jpg files only.'
                        })
                    this.value = ''
                }
            }
        })
            
        $('#save_docs').on('click', function(e){
            e.preventDefault()
            let form = $("#quote_docs")
            form.validate({
                errorElement: 'span',
                errorClass: 'text-danger fst-italic',
                highlight: function(element, errorClass) {
                },
                unhighlight: function(element, errorClass) {
                }
            });
            if (form.valid() === true){
                let data = new FormData(document.getElementById('quote_docs'))
                $.ajax({
                    type: 'POST',
                    data:data,
                    url: "{!! route('add_quote_docs')!!}",
                    contentType: false,
                    processData: false,
                    success:function(data){
                        if (data.status == 1) {
                            toastr.success('Documents saved.', {
                                timeOut: 5000
                            });
                            window.location.href = "/brokage/Agent/quot/view/{{$quote_no}}/{{$source}}"
                        }else{
                            swal.fire({
                                icon: "error",
                                title: "Failed",
                                html:"<h6>Check details and try again</h6>"
                            });
                        }

                    }
                })
            }
        })
    });
        
</script>

@endsection