<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OutlookService;
use App\Services\MailService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MailController extends Controller
{
    public function __construct(
        private OutlookService $outlookService,
        private MailService $mailService
    ) {}

    public function index(Request $request): View
    {
        $requestData = $this->validateIndexRequest($request);

        try {
            $data = $this->mailService->getMailData(
                $requestData['folder'],
                $requestData['search'],
                $requestData['limit']
            );

            return view('mail.index', array_merge($data, [
                'folder' => $requestData['folder'],
                'user' => Auth::user()
            ]));
        } catch (\Exception $e) {
            logger()->error('Mail index failed', ['error' => $e->getMessage(), 'user' => Auth::id()]);

            return view('mail.index', $this->getEmptyMailData($requestData['folder']));
        }
    }

    public function folder(string $folder): View|JsonResponse
    {
        try {
            $emails = $this->mailService->getEmails($folder);

            if (request()->wantsJson()) {
                return response()->json(['emails' => $emails, 'folder' => $folder]);
            }

            return view('mail.partials.email-list', compact('emails', 'folder'));
        } catch (\Exception $e) {
            logger()->error('Folder load failed', ['folder' => $folder, 'error' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to load folder'], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $email = $this->mailService->getEmail($id);

            if (!$email) {
                return response()->json(['error' => 'Email not found'], 404);
            }

            // Mark as read when viewing
            $this->mailService->markAsRead($email['uid']);

            return response()->json($email);
        } catch (\Exception $e) {
            logger()->error('Email show failed', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to load email'], 500);
        }
    }

    public function showInbox($id)
    {
        try {
            if (empty($id)) {
                return redirect()->back()->withErrors(['Invalid email ID provided']);
            }
            $email = $this->mailService->getEmailByMessageId($id, auth()->user()->email);

            $request = new Request();
            $requestData = $this->validateIndexRequest($request);

            $data = $this->mailService->getMailData(
                $requestData['folder'],
                $requestData['search'],
                $requestData['limit']
            );

            return view('mail.index', array_merge($data, [
                'folder' => $requestData['folder'],
                'email' => $email,
                'user' => Auth::user()
            ]));
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->withErrors(['Email not found']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['Email not found or invalid message ID']);
        }
    }

    public function send(Request $request): JsonResponse
    {
        $validated = $this->validateSendRequest($request);

        try {
            $result = $this->mailService->sendEmail($validated);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Email sent successfully' : 'Failed to send email'
            ]);
        } catch (\Exception $e) {
            logger()->error('Email send failed', ['error' => $e->getMessage(), 'user' => Auth::id()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the email'
            ], 500);
        }
    }

    public function reply(Request $request, string $id): JsonResponse
    {
        $validated = $this->validateReplyRequest($request);

        try {
            $result = $this->mailService->replyToEmail($id, $validated);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Reply sent successfully' : 'Failed to send reply'
            ]);
        } catch (\Exception $e) {
            logger()->error('Email reply failed', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the reply'
            ], 500);
        }
    }

    public function star(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate(['starred' => 'required|boolean']);

        return $this->handleEmailAction(
            fn() => $this->mailService->toggleStar($id, $validated['starred']),
            'star',
            $id
        );
    }

    public function delete(string $id): JsonResponse
    {
        return $this->handleEmailAction(
            fn() => $this->mailService->deleteEmail($id),
            'delete',
            $id
        );
    }

    public function archive(string $id): JsonResponse
    {
        return $this->handleEmailAction(
            fn() => $this->mailService->archiveEmail($id),
            'archive',
            $id
        );
    }

    public function spam(string $id): JsonResponse
    {
        return $this->handleEmailAction(
            fn() => $this->mailService->markAsSpam($id),
            'spam',
            $id
        );
    }

    public function markRead(string $id): JsonResponse
    {
        return $this->handleEmailAction(
            fn() => $this->mailService->markAsRead($id),
            'mark_read',
            $id
        );
    }

    public function markUnread(string $id): JsonResponse
    {
        return $this->handleEmailAction(
            fn() => $this->mailService->markAsUnread($id),
            'mark_unread',
            $id
        );
    }

    public function checkNew(): JsonResponse
    {
        try {
            $newEmailCount = $this->mailService->getNewEmailCount();
            return response()->json(['newEmails' => $newEmailCount]);
        } catch (\Exception $e) {
            return response()->json(['newEmails' => 0]);
        }
    }

    public function downloadAttachment(string $emailId, string $attachmentId)
    {
        try {
            return $this->mailService->downloadAttachment($emailId, $attachmentId);
        } catch (\Exception $e) {
            logger()->error('Attachment download failed', [
                'email_id' => $emailId,
                'attachment_id' => $attachmentId,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Failed to download attachment');
        }
    }

    public function downloadAllAttachments(string $emailId)
    {
        try {
            return $this->mailService->downloadAllAttachments($emailId);
        } catch (\Exception $e) {
            logger()->error('All attachments download failed', [
                'email_id' => $emailId,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Failed to create zip file');
        }
    }

    private function validateIndexRequest(Request $request): array
    {
        return [
            'folder' => $request->get('folder', 'inbox'),
            'search' => $request->get('search'),
            'limit' => (int) $request->get('limit', 50)
        ];
    }

    private function validateSendRequest(Request $request): array
    {
        return $request->validate([
            'from' => 'nullable|string|max:255',
            'to' => 'required|array|min:1',
            'to.*' => 'email',
            'cc' => 'nullable|array',
            'cc.*' => 'email',
            'bcc' => 'nullable|array',
            'bcc.*' => 'email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'priority' => 'nullable|in:low,normal,high',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|max:10240',
            'reply_to_id' => 'nullable|string',
        ]);
    }

    private function validateReplyRequest(Request $request): array
    {
        return $request->validate([
            'body' => 'required|string',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|max:10240'
        ]);
    }

    private function handleEmailAction(callable $action, string $actionName, string $emailId): JsonResponse
    {
        try {
            $result = $action();
            return response()->json(['success' => $result]);
        } catch (\Exception $e) {
            logger()->error("Email {$actionName} failed", [
                'id' => $emailId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false], 500);
        }
    }

    private function getEmptyMailData(string $folder): array
    {
        return [
            'emails' => collect(),
            'folders' => collect(),
            'contacts' => collect(),
            'folder' => $folder,
            'user' => Auth::user(),
            'error' => 'Failed to load emails. Please check your connection.'
        ];
    }

    /**
     * Get contacts for email composer - maintains your original structure
     */
    public function getContacts(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'email_contacts_' . (auth()->id() ?? 'guest');

            $contacts = $this->loadContacts();

            // Cache::remember($cacheKey, 300, function () {
            //     return $this->loadContacts();
            // });

            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = strtolower($request->search);
                $contacts = array_filter($contacts, function ($contact) use ($searchTerm) {
                    return strpos(strtolower($contact['name']), $searchTerm) !== false ||
                        strpos(strtolower($contact['email']), $searchTerm) !== false;
                });
                $contacts = array_values($contacts);
            }

            return response()->json([
                'success' => true,
                'data' => $contacts,
                'message' => 'Contacts loaded successfully'
            ]);
        } catch (\Exception $e) {
            logger()->error('Error loading contacts: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'data' => $this->getFallbackContacts(),
                'message' => 'Contacts loaded (fallback mode)'
            ]);
        }
    }

    /**
     * Load contacts from your existing database structure
     */
    private function loadContacts(): array
    {
        $contacts = [];

        try {
            $users = User::select('id', 'name', 'email')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->orderBy('name')
                ->get();

            foreach ($users as $user) {
                $contacts[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => 'user'
                ];
            }

            if (Schema::hasTable('contacts')) {
                $contactRecords = DB::table('contacts')
                    ->select('id', 'name', 'email')
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->orderBy('name')
                    ->get();

                foreach ($contactRecords as $contact) {
                    $contacts[] = [
                        'id' => 'contact_' . $contact->id,
                        'name' => $contact->name,
                        'email' => $contact->email,
                        'type' => 'contact'
                    ];
                }
            }

            if (Schema::hasTable('customer_contacts')) {
                $customerContacts = DB::table('customer_contacts')
                    ->select('id', 'contact_name', 'contact_email')
                    ->whereNotNull('contact_email')
                    ->where('contact_email', '!=', '')
                    ->orderBy('contact_name')
                    ->get();

                foreach ($customerContacts as $contact) {
                    $contacts[] = [
                        'id' => 'customer_contact_' . $contact->id,
                        'name' => $contact->contact_name,
                        'email' => $contact->contact_email,
                        'type' => 'customer_contacts'
                    ];
                }
            }

            $uniqueContacts = [];
            $seenEmails = [];

            foreach ($contacts as $contact) {
                if (!in_array(strtolower($contact['email']), $seenEmails)) {
                    $seenEmails[] = strtolower($contact['email']);
                    $uniqueContacts[] = $contact;
                }
            }

            return $uniqueContacts;
        } catch (\Exception $e) {
            logger()->error('Error in loadContacts: ' . $e->getMessage());
            return $this->getFallbackContacts();
        }
    }

    /**
     * Fallback contacts - maintains your original data structure
     */
    private function  getFallbackContacts(): array
    {
        return [
            [
                'id' => 4,
                'name' => 'Support Team',
                'email' => 'pknuek@gmail.com',
                'type' => 'fallback'
            ]
        ];
    }

    /**
     * Clear contacts cache
     */
    public function clearContactsCache(): JsonResponse
    {
        try {
            $cacheKey = 'email_contacts_' . (auth()->id() ?? 'guest');
            Cache::forget($cacheKey);

            return response()->json([
                'success' => true,
                'message' => 'Contacts cache cleared'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    public function getInlineImages(Request $request, string $messageId)
    {
        $email = DB::table('fetched_emails')
            ->where('uid', (string) $messageId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$email) {
            abort(404, 'Email not found');
        }

        $filename = $request->get('filename', 'missing_attachment.png');

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $slugged = Str::slug($basename);
        $underscored = str_replace('-', '_', $slugged);

        $processedFilename = $underscored . ($extension ? ".{$extension}" : '');
        $filep = "emails/" . auth()->user()->email . "/{$processedFilename}";
        $filepath = storage_path('app/public/' . $filep);

        if (!$filepath) {
            logger()->error("File not found");
            abort(404, 'File not found');
        }

        try {
            // Get MIME type using native PHP function
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filepath);
            finfo_close($finfo);

            $contentType = $mimeType ?: 'application/octet-stream';

            return response()->file($filepath, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . basename($filepath) . '"'
            ]);
        } catch (\Exception $e) {
            logger()->error("Error retrieving file: " . $e->getMessage());
            abort(500, 'Error retrieving file');
        }
    }
}
