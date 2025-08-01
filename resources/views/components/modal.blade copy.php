<div class="modal fade {{ $class ?? '' }}" id="{{ $id }}"data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered {{ $size ?? '' }}" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ Str::slug($title) }}-title">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $body }}
            </div>
            <div class="modal-footer">
                {{ $footer }}
            </div>
        </div>
    </div>
</div>