<?php

namespace App\Console\Commands;

use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SendEmail;
use App\Mail\SendEmail_Mail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use App\Listeners\UpdateEmailStatusOnFail;
use App\Listeners\UpdateEmailStatusOnSend;

class SendingEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all the emails stored in the sendemail table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $datas = SendEmail::whereRaw("trim(status) = 'SEND'")->get();
            $this->info(1);

            if (!empty($datas)) {
                foreach ($datas as $data) {
                    if (!empty($data->email_to)) {
                        $email = trim($data->email_to);
                        $subject = trim($data->email_subject);
                        $title = trim($data->email_subject);
                        $emailId = $data->email_id;
                        $createdate = Carbon::parse($data->created_at)->diffForhumans();
                        $user = $data->created_by;
                        $user = User::whereRaw("trim(user_name) = '" . trim($user) . "'")->first();
                        $recipient = $data->salutation;
                        $data['recipient_name'] = trim($recipient);
                        $data['body'] = trim($data->email_body);

                        Mail::to($email)->send(new SendEmail_Mail($data, $title, $createdate, $subject, $user));

                        // Debugging: Output the email ID to verify it's correct
                        $this->info("Email ID being processed: $emailId");

                    }
                }
            }
            $this->info("All mails have been processed");
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}
