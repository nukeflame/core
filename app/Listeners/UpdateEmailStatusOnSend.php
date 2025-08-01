<?php

namespace App\Listeners;

use App\Models\SendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateEmailStatusOnSend implements ShouldQueue
{

    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        if (isset($event->data['content'])) {
            if (isset($event->data['content']->email_id)) {
                $emailId = $event->data['content']->email_id;

                SendEmail::where('email_id', $emailId)->update(['status' => 'SENT']);
            }
        }
    }
}
