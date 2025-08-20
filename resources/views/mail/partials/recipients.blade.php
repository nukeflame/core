<div class="mail-recepients border">
    <div class="p-3 border-bottom">
        <button class="btn btn-light btn-icon rounded-pill" data-bs-toggle="tooltip" data-bs-placement="left"
            data-bs-title="Add Recipient" id="addRecipientBtn">
            <i class="ri-add-line"></i>
        </button>
    </div>
    <div class="p-3 d-flex flex-column align-items-center total-mail-recepients" id="mail-recepients">
        @if (isset($contacts) && $contacts->count() > 0)
            @foreach ($contacts->take(10) as $contact)
                @include('mail.components.contact-avatar', ['contact' => $contact])
            @endforeach
        @else
            <div class="text-center text-muted">
                <i class="ri-user-line fs-24 mb-2"></i>
                <p class="fs-12 mb-0">No contacts</p>
            </div>
        @endif
    </div>
</div>
