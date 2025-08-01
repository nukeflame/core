<div class="total-mails border">
    <div class="p-3 d-flex align-items-center border-bottom">
        <div class="me-3">
            <input class="form-check-input" type="checkbox" id="checkAll" value="" aria-label="Select all emails">
        </div>
        <div class="flex-fill">
            <h6 class="fw-semibold mb-0">All Mails</h6>
        </div>
        <button class="btn btn-icon btn-light me-1 d-lg-none d-block total-mails-close" data-bs-toggle="tooltip"
            data-bs-placement="top" data-bs-title="Close">
            <i class="ri-close-line"></i>
        </button>
        <div class="dropdown" id="recentDropdown" style="display: none;">
            <button class="btn btn-icon btn-light btn-wave waves-light" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ti ti-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Recent</a></li>
                <li><a class="dropdown-item" href="#">Unread</a></li>
                <li><a class="dropdown-item" href="#">Mark All Read</a></li>
                <li><a class="dropdown-item" href="#">Spam</a></li>
                <li><a class="dropdown-item" href="#">Delete All</a></li>
            </ul>
        </div>
    </div>
    <div class="p-3 border-bottom">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0" placeholder="Search Email"
                aria-describedby="button-addon2">
            <button class="btn btn-light" type="button" id="button-addon2"><i
                    class="ri-search-line text-muted"></i></button>
        </div>
    </div>
    <div class="mail-messages" id="mail-messages">
        <ul class="list-unstyled mb-0 mail-messages-container">
            @if ($emails)
                @foreach ($emails->items() as $item)
                    <li
                        class="mail-page {{ request()->routeIs('mail.inbox.show') && (int) request()->route('messageId') === (int) $item->id ? 'active' : '' }}">
                        <div class="d-flex align-items-top">
                            <div class="me-3 mt-1">
                                <input class="form-check-input" type="checkbox" id="checkboxNoLabel{{ $item->id }}"
                                    value="{{ $item->id }}" aria-label="Select email {{ $item->id }}">
                            </div>
                            <div class="me-1 lh-1 btn_select_field" data-email-id={{ $item->id }}
                                data-ref-id={{ $item->uid }}>
                                <span class="avatar avatar-md offline me-2 avatar-rounded mail-msg-avatar">
                                    <img src="{{ $item->avatar ?? '/assets/images/faces/12.jpg' }}" alt="">
                                </span>
                            </div>
                            <div class="flex-fill btn_select_field" data-email-id={{ $item->id }}
                                data-ref-id={{ $item->uid }}>
                                <p class="mb-1 fs-12">
                                    {{ $item->from_name ?? 'Unknown Sender' }}
                                    <span class="float-end text-muted fw-normal fs-11">
                                        {{ $item->time ?? '' }}
                                    </span>
                                </p>
                                <p class="mail-msg mb-0">
                                    <span class="d-block mb-0 fw-semibold text-truncate">
                                        {{ $item->subject ?? 'No Subject' }}
                                    </span>
                                    <span class="fs-11 text-muted text-wrap text-truncate">
                                        {{ Str::limit($item->body_preview, 100, '...') ?? 'No preview available.' }}
                                        {{-- <button class="btn p-0 lh-1 mail-starred border-0 float-end pl-2"
                                            onclick="toggleStar(this)">
                                            <i class="{{ $item->starred ? 'ri-star-fill' : 'ri-star-line' }} fs-14"></i>
                                        </button> --}}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </li>
                @endforeach
            @endif

        </ul>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            $('#checkAll').on('change', function() {
                const isChecked = $(this).is(':checked');
                if (isChecked) {
                    $('#recentDropdown').show();
                } else {
                    $('#recentDropdown').hide();
                }
                $('.mail-messages-container input[type="checkbox"]').prop('checked', isChecked);
                if (isChecked) {
                    $(this).closest('.me-3').addClass('position-relative');
                    // console.log('CheckAll: Checked - Recent dropdown shown');
                } else {
                    // console.log('CheckAll: Unchecked - Recent dropdown hidden');
                }
            });

            $(document).on('click', function(event) {
                if (!$(event.target).closest('#checkAll, #recentDropdown').length) {
                    $('#recentDropdown').fadeOut(200);
                    $('#checkAll').prop('checked', false);
                }
            });

            $('#recentDropdown').on('click', function(event) {
                event.stopPropagation();
            });

            $('.mail-messages-container .mail-page .btn_select_field').on('click', function(e) {
                e.stopPropagation();
                const messageId = $(this).data('emailId');
                const refId = $(this).data('refId');
                const url = `/admin/mail/inbox/id/${encodeURIComponent(messageId)}?ref=${refId}`;
                window.location.href = url;
            });
        });
    </script>
@endpush
