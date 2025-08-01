@extends('layouts.app')
@section('content')
    {{-- <link type="text/css" href="{{ asset('assets/ckeditor5/sample/css/sample.css') }}" rel="stylesheet" media="screen" /> --}}
    <main>
        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Proportional Treaty Slip Template</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Printouts Setup</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Proportional Treaty Slip</li>
                    </ol>
                </nav>
            </div>
        </div> <!-- Page Header Close -->
        <form action="{{ route('docs-setup.save-slip') }}" method="post" id="wordingForm">
            @csrf
            <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
            <input type="hidden" name="wording" id="wording"
                @if ($wording) value = "{{ $wording->wording }}"
    @elseif($standardWording)
        value = "{{ $standardWording->wording }}" @endif>
        </form>
        <button type="button" class="btn btn-primary" id="save">Save Wording</button>
        <div class="centered">
            <div id="editor">

            </div>
        </div>
        <button type="button" class="btn btn-primary" id="bottom-save">Save Wording</button>

    </main>
@endsection
@push('script')
    <script src="{{ asset('assets/ckeditor5/ckeditor.js') }}"></script>

    <script>
        ClassicEditor
            .create(document.querySelector('#editor'), {
                // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
            })
            .then(editor => {
                window.editor = editor;
            })
            .catch(err => {
                console.error(err.stack);
            });

        $(document).ready(function() {

            // set default wording
            editor.setData($('#wording').val());


            $('#save, #bottom-save').click(function(e) {
                e.preventDefault();
                const editorData = editor.getData()
                $('#wording').val(editorData);

                $('#wordingForm').submit();
            });
        });
    </script>
@endpush
