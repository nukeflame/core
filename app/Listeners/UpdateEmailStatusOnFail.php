<?php

namespace App\Listeners;

use App\Models\SendEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Mail\Events\MessageFailed;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateEmailStatusOnFail implements ShouldQueue
{

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event)
    {
        $emailId = $event->data['content']->email_id;
        $errorMessage = $event->errorMessage; // Get the error message

        // Update the status of the email to "FAILED" and set the error message
        SendEmail::where('email_id', $emailId)->update([
            'status' => 'FAILED',
            'email_error' => $errorMessage
        ]);
    }
}
