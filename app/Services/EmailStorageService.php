<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmailStorageService
{
    public function getStoredEmails(?string $folder = null, ?int $limit = null, ?string $search = null): Collection
    {
        try {
            if (!$this->tableExists()) {
                return collect();
            }

            $query = $this->buildEmailQuery($folder, $search)
                ->select([
                    'id',
                    'uid',
                    'message_id',
                    'subject',
                    'from_name',
                    'from_email',
                    'body_preview',
                    'date_received',
                    'is_read',
                    'is_flagged',
                    'has_attachments',
                    'folder',
                    'importance',
                ]);

            if ($limit) {
                $query->limit($limit);
            }

            $emails = collect($query->orderBy('date_received', 'desc')->get());

            // Transform the results to match expected structure
            // return $emails->map(function ($email) {
            //     return [
            //         'id' => $email->id,
            //         'uid' => $email->uid ?? $email->message_id,
            //         'message_id' => $email->message_id,
            //         'subject' => $email->subject,
            //         'from_name' => $email->from_name,
            //         'from_email' => $email->from_email,
            //         'body_preview' => $email->body_preview,
            //         'body_text' => $email->body_text ?? null,
            //         'body_html' => $email->body_html ?? null,
            //         'date_received' => $email->date_received,
            //         'date_sent' => $email->date_sent ?? null,
            //         'is_read' => (bool) $email->is_read,
            //         'has_attachments' => (bool) ($email->has_attachments ?? false),
            //         'folder' => $email->folder ?? 'inbox',
            //         'importance' => $email->importance ?? 'normal',
            //         'conversation_id' => $email->conversation_id ?? null,
            //         'to_recipients' => $this->parseJsonRecipients($email->to_recipients ?? null),
            //         'cc_recipients' => $this->parseJsonRecipients($email->cc_recipients ?? null),
            //         'bcc_recipients' => $this->parseJsonRecipients($email->bcc_recipients ?? null),
            //     ];
            // });
            // logger()->debug(json_encode($query['emails'][0]['body_preview'], JSON_PRETTY_PRINT));

            return $emails;
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getStoredEmail(string $id): ?array
    {
        try {
            if (!$this->tableExists()) {
                return null;
            }

            $email = DB::table('fetched_emails')
                ->where('id', $id)
                ->where('user_email', $this->getUserEmail())
                ->first();

            return $email ? (array) $email : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function storeEmails(array $emails, string $folder): bool
    {
        try {
            if (!$this->tableExists()) {
                return false;
            }

            $userEmail = $this->getUserEmail();
            $timestamp = now();

            $emailData = array_map(function ($email) use ($userEmail, $folder, $timestamp) {
                return [
                    'id' => $email['id'],
                    'user_email' => $userEmail,
                    'folder' => $folder,
                    'subject' => $email['subject'] ?? '',
                    'sender_name' => $email['sender']['name'] ?? '',
                    'sender_email' => $email['sender']['email'] ?? '',
                    'body_preview' => $email['bodyPreview'] ?? '',
                    'date_received' => $email['receivedDateTime'] ?? $timestamp,
                    'is_read' => $email['isRead'] ?? false,
                    'is_flagged' => $email['flag']['flagStatus'] === 'flagged' ?? false,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }, $emails);

            DB::table('fetched_emails')->upsert(
                $emailData,
                ['id', 'user_email'],
                ['subject', 'sender_name', 'sender_email', 'body_preview', 'date_received', 'is_read', 'is_flagged', 'updated_at']
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function buildEmailQuery(?string $folder = null, ?string $search = null)
    {
        $query = DB::table('fetched_emails')
            ->where('user_email', $this->getUserEmail());

        // Handle special folder filters
        if ($folder && $folder !== 'all') {
            switch ($folder) {
                case 'inbox':
                case 'sent':
                case 'drafts':
                case 'spam':
                case 'trash':
                case 'archive':
                    $query->where('folder', $folder);
                    break;
                case 'starred':
                    $query->where('is_flagged', true);
                    break;
                case 'important':
                    $query->where('importance', 'high');
                    break;
                default:
                    $query->where('folder', $folder);
                    break;
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('from_name', 'like', "%{$search}%")
                    ->orWhere('from_email', 'like', "%{$search}%")
                    ->orWhere('body_preview', 'like', "%{$search}%")
                    ->orWhere('body_text', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function parseJsonRecipients(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        try {
            $decoded = json_decode($json, true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function tableExists(): bool
    {
        return DB::getSchemaBuilder()->hasTable('fetched_emails');
    }

    private function getUserEmail(): string
    {
        return Auth::user()->email;
    }
}
