<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendClaimReinsurerRequest;
use App\Jobs\SendOutlookEmailJob;
use App\Jobs\SyncUserEmails;
use App\Models\ClaimRegister;
use App\Models\Customer;
use App\Models\EmailSyncState;
use App\Models\User;
use App\Services\ContactNameMappingService;
use App\Services\OutlookService;
use App\Services\MailService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MailController extends Controller
{
    protected $mailService;
    private $outlookService;
    private $authUser;
    protected string $batchId;

    public function __construct(MailService $mailService, OutlookService $outlookService)
    {
        $this->mailService = $mailService;
        $this->outlookService = $outlookService;
        $this->authUser = auth()->user();

        $this->batchId = Str::uuid()->toString();
    }

    public function index()
    {
        try {
            return view('mail.index', [
                'isOutlookConnected' => $this->hasValidOutlookConnection(auth()->user()),
            ]);
        } catch (\Exception $e) {
            return view('mail.index', [
                'isOutlookConnected' => false,
            ]);
        }
    }

    public function fetchEmails(Request $request)
    {
        try {
            $validated = $request->validate([
                'force_refresh' => 'sometimes|boolean',
                'folder' => 'sometimes|string|nullable',
                'limit' => 'sometimes|integer|min:1|max:100'
            ]);

            $forceRefresh = $validated['force_refresh'] ?? false;
            $folder = $validated['folder'] ?? 'inbox';
            $limit = $validated['limit'] ?? 50;

            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            if (!$this->hasValidOutlookConnection($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlook account is not connected or token is expired.',
                    'requires_connect' => true,
                ], 400);
            }

            if ($forceRefresh) {
                $userId = $request->user()->id;
                $syncType = $request->input('type', 'delta');

                $syncState = EmailSyncState::where('user_id', $userId)->first();

                if ($syncState && ($syncState->is_locked || ($syncState->is_syncing ?? false))) {
                    return response()->json([
                        'message' => 'Sync already in progress',
                        'status' => 'locked'
                    ], 409);
                }

                SyncUserEmails::dispatch($userId, $syncType);
            }


            $emails = $this->mailService->getEmails($folder, $limit);

            return response()->json([
                'success' => true,
                'data' => $emails,
                'count' => count($emails),
                'folder' => $folder
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch emails: ' . $e->getMessage()
            ], 500);
        }
    }

    public function folder(string $folder): View|JsonResponse
    {
        try {
            $emails = $this->mailService->getEmails($folder);
            $isOutlookConnected = $this->hasValidOutlookConnection(request()->user());

            if (request()->wantsJson()) {
                return response()->json(['emails' => $emails, 'folder' => $folder]);
            }

            return view('mail.partials.email-list', compact('emails', 'folder', 'isOutlookConnected'));
        } catch (\Exception $e) {
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

            $this->mailService->markAsRead($email['uid']);

            return response()->json($email);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load email'], 500);
        }
    }

    public function messageDetail(string $id): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $user = auth()->user();
            if (!$this->hasValidOutlookConnection($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlook account is not connected or token is expired.',
                    'requires_connect' => true,
                ], 400);
            }

            $this->outlookService->setAuthenticatedUser($user);
            $message = $this->outlookService->getMessageDetails($id);

            if (empty($message) || empty($message['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found',
                ], 404);
            }

            if (!($message['isRead'] ?? false)) {
                $this->outlookService->markMessage($user, $id, true);
            }

            $trail = [];
            $conversationId = $message['conversationId'] ?? null;
            if (!empty($conversationId)) {
                $conversation = $this->outlookService->getConversationMessages($conversationId);
                $trail = collect($conversation['messages'] ?? [])
                    ->map(function (array $item): array {
                        return [
                            'id' => $item['id'] ?? null,
                            'subject' => $item['subject'] ?? '[No Subject]',
                            'from_name' => $item['from']['emailAddress']['name'] ?? 'Unknown Sender',
                            'from_email' => $item['from']['emailAddress']['address'] ?? null,
                            'to' => $this->formatGraphRecipients($item['toRecipients'] ?? []),
                            'received_at' => $item['receivedDateTime'] ?? null,
                            'sent_at' => $item['sentDateTime'] ?? null,
                            'preview' => $item['bodyPreview'] ?? '',
                            'has_attachments' => (bool) ($item['hasAttachments'] ?? false),
                        ];
                    })
                    ->values()
                    ->toArray();
            }

            if (empty($trail) || !collect($trail)->contains(fn($item) => ($item['id'] ?? null) === ($message['id'] ?? null))) {
                $trail[] = [
                    'id' => $message['id'] ?? null,
                    'subject' => $message['subject'] ?? '[No Subject]',
                    'from_name' => $message['from']['emailAddress']['name'] ?? 'Unknown Sender',
                    'from_email' => $message['from']['emailAddress']['address'] ?? null,
                    'to' => $this->formatGraphRecipients($message['toRecipients'] ?? []),
                    'received_at' => $message['receivedDateTime'] ?? null,
                    'sent_at' => $message['sentDateTime'] ?? null,
                    'preview' => $message['bodyPreview'] ?? '',
                    'has_attachments' => (bool) ($message['hasAttachments'] ?? false),
                ];
            }

            $trail = collect($trail)
                ->sortBy(fn($item) => $item['received_at'] ?? $item['sent_at'] ?? '')
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message['id'],
                    'conversation_id' => $conversationId,
                    'subject' => $message['subject'] ?? '[No Subject]',
                    'from_name' => $message['from']['emailAddress']['name'] ?? 'Unknown Sender',
                    'from_email' => $message['from']['emailAddress']['address'] ?? null,
                    'to' => $this->formatGraphRecipients($message['toRecipients'] ?? []),
                    'cc' => $this->formatGraphRecipients($message['ccRecipients'] ?? []),
                    'received_at' => $message['receivedDateTime'] ?? null,
                    'sent_at' => $message['sentDateTime'] ?? null,
                    'is_read' => (bool) ($message['isRead'] ?? false),
                    'importance' => $message['importance'] ?? 'normal',
                    'has_attachments' => (bool) ($message['hasAttachments'] ?? false),
                    'body_html' => $message['body']['content'] ?? '',
                    'body_preview' => $message['bodyPreview'] ?? '',
                    'trail' => $trail,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load email details: ' . $e->getMessage(),
            ], 500);
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
                'user' => Auth::user(),
                'isOutlookConnected' => $this->hasValidOutlookConnection(auth()->user()),
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
            // Check for unread emails in database
            $unreadCount = DB::table('fetched_emails')
                ->where('user_email', auth()->user()->email)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'unreadEmails' => $unreadCount,
                'newEmails' => 0 // This will be updated via WebSocket
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'unreadEmails' => 0,
                'newEmails' => 0
            ]);
        }
    }

    public function currentMonthEmails(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'folder' => 'sometimes|string|in:inbox,sent,drafts,spam,important,trash,archive,starred,all',
                'limit' => 'sometimes|integer|min:1|max:100',
                'page' => 'sometimes|integer|min:1',
                'force_refresh' => 'sometimes|boolean',
            ]);

            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $user = $request->user();
            if (!$this->hasValidOutlookConnection($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlook account is not connected or token is expired.',
                    'requires_connect' => true,
                ], 400);
            }

            $folder = $validated['folder'] ?? 'inbox';
            $limit = $validated['limit'] ?? 50;
            $page = $validated['page'] ?? 1;
            $forceRefresh = (bool) ($validated['force_refresh'] ?? false);
            $skip = ($page - 1) * $limit;

            $endUtc = now('UTC');
            $startOfPeriodUtc = (clone $endUtc)->subMonths(3);
            $filter = sprintf(
                'receivedDateTime ge %s and receivedDateTime lt %s',
                $startOfPeriodUtc->toISOString(),
                $endUtc->toISOString()
            );

            $cacheKey = sprintf(
                'mail:last-3-months:%d:%s:%d:%d',
                $user->id,
                $folder,
                $limit,
                $page
            );

            $fetch = function () use ($user, $folder, $limit, $skip, $filter) {
                $this->outlookService->setAuthenticatedUser($user);

                $messages = $this->outlookService->getMessages([
                    'folder' => $this->mapFolderForGraph($folder),
                    'limit' => $limit,
                    'skip' => $skip,
                    'filter' => $filter,
                    'select' => 'id,subject,from,receivedDateTime,isRead,hasAttachments,bodyPreview,importance,internetMessageId',
                    'orderBy' => 'receivedDateTime DESC',
                ]);

                return array_map(function (array $message): array {
                    return [
                        'id' => $message['id'] ?? null,
                        'message_id' => $message['internetMessageId'] ?? null,
                        'conversation_id' => $message['conversationId'] ?? null,
                        'subject' => $message['subject'] ?? '[No Subject]',
                        'sender_name' => $message['from']['emailAddress']['name'] ?? 'Unknown Sender',
                        'sender_email' => $message['from']['emailAddress']['address'] ?? null,
                        'received_at' => $message['receivedDateTime'] ?? null,
                        'is_read' => (bool) ($message['isRead'] ?? false),
                        'has_attachments' => (bool) ($message['hasAttachments'] ?? false),
                        'importance' => $message['importance'] ?? 'normal',
                        'preview' => $message['bodyPreview'] ?? '',
                    ];
                }, $messages);
            };

            if ($forceRefresh) {
                Cache::forget($cacheKey);
                $emails = $fetch();
                Cache::put($cacheKey, $emails, now()->addSeconds(60));
            } else {
                $emails = Cache::remember($cacheKey, now()->addSeconds(60), $fetch);
            }

            return response()->json([
                'success' => true,
                'data' => $emails,
                'meta' => [
                    'folder' => $folder,
                    'period_start_utc' => $startOfPeriodUtc->toISOString(),
                    'period_end_utc' => $endUtc->toISOString(),
                    'page' => $page,
                    'limit' => $limit,
                    'has_more' => count($emails) === $limit,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch last 3 months emails: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Trigger email sync manually
     */
    public function triggerSync(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();

            // Check Outlook connection
            if (!$this->hasValidOutlookConnection($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlook account is not connected or token is expired.',
                    'requires_connect' => true,
                ], 400);
            }

            // Check if sync is already in progress
            $syncState = EmailSyncState::where('user_id', $userId)->first();

            if ($syncState && ($syncState->is_locked || $syncState->is_syncing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sync already in progress',
                    'status' => 'locked'
                ], 409);
            }

            // Dispatch sync job
            SyncUserEmails::dispatch($userId);

            return response()->json([
                'success' => true,
                'message' => 'Email sync initiated successfully',
                'status' => 'processing'
            ], 202);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to trigger sync: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadAttachment(string $emailId, string $attachmentId)
    {
        try {
            return $this->mailService->downloadAttachment($emailId, $attachmentId);
        } catch (\Exception $e) {
            abort(500, 'Failed to download attachment');
        }
    }

    public function downloadAllAttachments(string $emailId)
    {
        try {
            return $this->mailService->downloadAllAttachments($emailId);
        } catch (\Exception $e) {
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

    private function mapFolderForGraph(string $folder): string
    {
        return match ($folder) {
            'sent' => 'sentitems',
            'trash' => 'deleteditems',
            'all', 'important', 'starred' => 'inbox',
            default => $folder,
        };
    }

    private function formatGraphRecipients(array $recipients): array
    {
        return array_values(array_filter(array_map(function ($recipient) {
            $address = $recipient['emailAddress']['address'] ?? null;
            if (empty($address)) {
                return null;
            }

            return [
                'email' => $address,
                'name' => $recipient['emailAddress']['name'] ?? null,
            ];
        }, $recipients)));
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
            'isOutlookConnected' => $this->hasValidOutlookConnection(Auth::user()),
            'error' => 'Failed to load emails. Please check your connection.'
        ];
    }

    private function hasValidOutlookConnection(?User $user): bool
    {
        if (!$user || empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $tokenRecord = DB::table('oauth_tokens')
            ->where('provider', 'outlook')
            ->where(function ($query) use ($user) {
                $query->where('email', $user->email)
                    ->orWhere('user_id', $user->id);
            })
            ->orderByDesc('updated_at')
            ->first();

        if (!$tokenRecord) {
            return false;
        }

        try {
            $this->outlookService->setAuthenticatedUser($user);
            $validToken = $this->outlookService->getValidToken();

            if (!$validToken) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
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
            abort(500, 'Error retrieving file');
        }
    }

    public function sendClaimReinsurerEmail(SendClaimReinsurerRequest $request)
    {
        DB::beginTransaction();
        try {
            $claim = ClaimRegister::where('claim_no', $request->claim_no)->firstOrFail();
            $customer = $claim->customer ?? Customer::find($request->customer_id);
            $allEmails = array_merge(
                [$request->partner_email],
                $request->contacts ?? [],
                $request->cc_email ?? [],
                $request->bcc_email ?? []
            );

            $recipientNames = ContactNameMappingService::getRecipientNames($customer, $allEmails);

            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    try {
                        $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('claim_attachments/' . $claim->claim_no, $filename, 'public');

                        $attachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => storage_path('app/public/' . $path),
                            'size' => $file->getSize(),
                            'mime_type' => $file->getMimeType()
                        ];
                    } catch (Exception $e) {
                        throw new Exception("Failed to process attachment: " . $file->getClientOriginalName() . ". Error: " . $e->getMessage());
                    }
                }
            }

            $recipients = [];
            if (!empty($request->reply_to_id)) {
                $recipients[] = $request->partner_email;
            } else {
                $recipients[] = $request->partner_email;

                if (!empty($request->contacts)) {
                    $recipients = array_merge($recipients, $request->contacts);
                }
            }

            $recipients = array_unique($recipients);

            // $emailRecords = [];
            $successCount = 0;
            $failedCount = 0;

            foreach ($recipients as $index => $recipient) {
                try {
                    $jobId = $this->batchId . '-' . ($index + 1);
                    $recipientEmail = explode(',', $recipient) ?? [];

                    $recipientName = $recipientNames[$recipient] ?? 'Sir/Madam';
                    $personalizedMessage = $this->formatMessageForHtml($request->message, $recipientName);
                    $rawMessage = $this->formatRawMessageForHtml($request->message);

                    $emailData = [
                        'claim' => $claim,
                        'subject' => $request->subject,
                        'message' => $personalizedMessage,
                        'priority' => $request->priority ?? 'normal',
                        'category' => $request->category ?? 'claim',
                        'reference' => $request->reference ?? null,
                        'attachments' => $attachments,
                        'senderName' => $this->authUser->name,
                        'senderEmail' => $this->authUser->email,
                        'recipientName' => $recipientName,
                        'replyToId' => $request->reply_to_id,
                        'replyMessage' => $rawMessage ?? '',
                        'to' => $recipientEmail,
                        'cc' => $request->cc_email ?? [],
                        'bcc' => $request->bcc_email ?? [],
                    ];

                    // $emailRecords[] = $emailRecord;
                    $job = SendOutlookEmailJob::dispatch($emailData, $this->authUser->id, $jobId);

                    if ($request->input('schedule_at')) {
                        $scheduleAt = Carbon::parse($request->input('schedule_at'));
                        $job->delay($scheduleAt);
                    } elseif (!$request->boolean('send_immediately', true)) {
                        $job->delay(now()->addSeconds($index * 2));
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                }
            }

            // $claim->notification_status = 'notification_sent';
            // $claim->notification_sent_at = now();
            // $claim->notification_sent_by = auth()->id();
            // $claim->update();

            DB::commit();

            $emailType = !empty($request->reply_to_id) ? 'reply' : 'notification';
            return response()->json([
                'success' => true,
                'message' => "Claim {$emailType} emails have been queued for sending to " . count($recipients) . ' recipient(s)',
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Claim not found: ' . $request->claim_no
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to send claim notification: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatMessageForHtml($message, $recipientName = 'Sir/Madam')
    {
        $personalizedMessage = str_replace('{recipient_name}', $recipientName, $message);

        $html = nl2br(htmlspecialchars($personalizedMessage, ENT_QUOTES, 'UTF-8'));

        $html = str_replace("\n\n", "</p><p>", $html);
        $html = "<p>" . $html . "</p>";

        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        $html = preg_replace('/<p>(\d+\..*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ol>$1</ol>', $html);

        $html = preg_replace('/<p>[-•*]\s*(.*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>(?:(?!<ol>).)*<\/li>)/s', '<ul>$1</ul>', $html);

        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        if (!preg_match('/^<p>\s*(Dear|Hello|Hi|Greetings)/i', $html)) {
            $greeting = ContactNameMappingService::getAppropriateGreeting($recipientName);
            $html = "<p>" . $greeting . "</p>" . $html;
        }

        if (!preg_match('/(Best regards|Sincerely|Kind regards|Yours faithfully)/i', $html)) {
            $html .= "<p>Best regards,<br>" . auth()->user()->name . "</p>";
        }

        return $html;
    }

    private function formatRawMessageForHtml($message)
    {
        $html = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

        $html = str_replace("\n\n", "</p><p>", $html);

        $html = "<p>" . $html . "</p>";

        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        $html = preg_replace('/<p>(\d+\..*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ol>$1</ol>', $html);

        $html = preg_replace('/<p>[-•*]\s*(.*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>(?:(?!<ol>).)*<\/li>)/s', '<ul>$1</ul>', $html);

        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        return $html;
    }
}
