<?php

namespace Nukeflame\Core\Services;

use App\Models\Email;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Webklex\IMAP\Facades\Client;

class EmailService
{
    private $userEmail;

    public function __construct()
    {
        $this->userEmail = auth()->user()->email;
    }

    protected function processIncomingEmail($message)
    {
        $attachments = [];

        if ($message->hasAttachments()) {
            foreach ($message->getAttachments() as $attachment) {
                $filename = $attachment->getName();
                $path = 'email_attachments/' . uniqid() . '_' . $filename;
                Storage::put($path, $attachment->getContent());

                $attachments[] = [
                    'name' => $filename,
                    'path' => $path,
                    'size' => $attachment->getSize()
                ];
            }
        }

        Email::create([
            'sender_email' => $message->getFrom()[0]->mail,
            'sender_name' => $message->getFrom()[0]->personal,
            'recipient_email' => config('mail.username'),
            'subject' => $message->getSubject(),
            'body' => $message->getHTMLBody() ?: $message->getTextBody(),
            'attachments' => $attachments,
            'folder' => 'inbox',
            'sent_at' => $message->getDate(),
        ]);
    }

    public function sendEmail($data)
    {
        try {
            $email = Email::create([
                'sender_email' => auth()->user()->email,
                'sender_name' => auth()->user()->name,
                'recipient_email' => $data['recipient_email'],
                'recipient_name' => $data['recipient_name'] ?? '',
                'subject' => $data['subject'],
                'body' => $data['body'],
                'attachments' => $data['attachments'] ?? [],
                'folder' => 'sent',
                'sent_at' => now()
            ]);

            // Mail::to($data['recipient_email'])->send(new \App\Mail\SendEmail($email));

            return ['success' => true, 'email' => $email];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function processAttachments($files)
    {
        $attachments = [];

        foreach ($files as $file) {
            $path = $file->store('email_attachments');
            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ];
        }

        return $attachments;
    }

    /**
     * Get count of all emails
     */
    public function getAllCount(): int
    {
        return DB::table('fetched_emails')->where('user_email', $this->userEmail)->count();
    }

    /**
     * Get count of inbox emails
     */
    public function getInboxCount(): int
    {
        return DB::table('fetched_emails')->where('user_email', $this->userEmail)->where('folder', 'inbox')->count();
    }

    /**
     * Get count of spam emails
     */
    public function getSpamCount(): int
    {
        return DB::table('fetched_emails')->where('user_email', $this->userEmail)->where('folder', 'spam')->count();
    }

    /**
     * Get count of starred emails
     */
    public function getStarredCount(): int
    {
        return DB::table('fetched_emails')->where('user_email', $this->userEmail)->where('folder', 'starred')->count();
    }

    /**
     * Get all email counts at once
     */
    public function getAllCounts(): array
    {
        return [
            'all' => $this->getAllCount(),
            'inbox' => $this->getInboxCount(),
            'spam' => $this->getSpamCount(),
            'starred' => $this->getStarredCount(),
        ];
    }

    /**
     * Get count by folder
     */
    public function getCountByFolder(string $folder): int
    {
        return DB::table('fetched_emails')->where('user_email', $this->userEmail)->where('folder', $folder)->count();
    }

    /**
     * Get emails with pagination
     */
    public function getEmailsPaginated(string $userEmail, int $perPage = 100, string $folder = 'inbox')
    {
        $query = DB::table('fetched_emails')->where('user_email', $this->userEmail)->orderBy('date_received', 'desc');

        if ($userEmail) {
            $query->where('user_email', $userEmail);
        }

        if ($folder) {
            $query->where('folder', $folder);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get single email by message_id
     */
    public function getEmailByMessageId(string $messageId)
    {
        return DB::table('fetched_emails')->where('user_email', $this->userEmail)->where('id', $messageId)->get();
    }
}
