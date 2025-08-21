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

            $query = $this->buildEmailQuery($folder, $search);

            if ($limit) {
                $query->limit($limit);
            }

            return collect($query->orderBy('date_received', 'desc')->get());
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

        if ($folder) {
            $query->where('folder', $folder);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhere('sender_email', 'like', "%{$search}%")
                    ->orWhere('body_preview', 'like', "%{$search}%");
            });
        }

        return $query;
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
