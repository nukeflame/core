<?php

namespace App\Observers;

use App\Models\Email;
use App\Jobs\SendEmailJob;

class EmailObserver
{
    public function created(Email $email)
    {
        // Log email creation
        logger()->nfo('Email created', ['email_id' => $email->id, 'subject' => $email->subject]);

        // Queue email for sending if it's in drafts and ready to send
        if ($email->folder === 'outbox') {
            SendEmailJob::dispatch($email);
        }
    }

    public function updated(Email $email)
    {
        // Log important updates
        if ($email->isDirty('is_read')) {
            logger()->nfo('Email read status changed', ['email_id' => $email->id, 'is_read' => $email->is_read]);
        }
    }
}
