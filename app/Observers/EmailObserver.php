<?php

namespace App\Observers;

use App\Models\Email;
use App\Jobs\SendEmailJob;

class EmailObserver
{
    public function created(Email $email)
    {
        if ($email->folder === 'outbox') {
            SendEmailJob::dispatch($email);
        }
    }

    public function updated(Email $email)
    {
        if ($email->isDirty('is_read')) {
            // SendEmailJob::dispatch($email);
        }
    }
}
