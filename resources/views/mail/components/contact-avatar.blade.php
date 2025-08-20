<a href="#" class="mail-recepeint-person" data-contact-id="{{ $contact->id }}" data-bs-toggle="tooltip"
    data-bs-placement="left" data-bs-title="{{ $contact->name }}">
    <span class="avatar {{ $contact->is_online ? 'online' : 'offline' }} avatar-rounded">
        @if ($contact->avatar)
            <img src="{{ $contact->avatar }}" alt="{{ $contact->name }}">
        @else
            <div class="avatar-initial">{{ strtoupper(substr($contact->name, 0, 1)) }}</div>
        @endif
    </span>
</a>v
