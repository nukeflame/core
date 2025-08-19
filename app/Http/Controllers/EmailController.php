<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendClaimReinsurerRequest;
use App\Jobs\SendClaimReinNotificationJob;
use App\Jobs\SendOutlookEmailJob;
use App\Models\ClaimRegister;
use App\Models\Customer;
use App\Models\Email;
use App\Services\ContactNameMappingService;
use App\Services\EmailService;
use App\Services\OutlookService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EmailController extends Controller
{
    protected $emailService;
    private $outlookService;
    private $authUser;
    protected string $batchId;

    public function __construct(EmailService $emailService, OutlookService $outlookService)
    {
        $this->emailService = $emailService;
        $this->outlookService = $outlookService;
        $this->authUser = auth()->user();

        $this->batchId = Str::uuid()->toString();
    }

    public function index()
    {
        try {
            $emails = $this->emailService->getEmailsPaginated($this->authUser->email, 100, 'inbox');

            $countAll = $this->emailService->getAllCount();
            $countInbox = $this->emailService->getInboxCount();
            $countSpam = $this->emailService->getSpamCount();
            $countStarred = $this->emailService->getStarredCount();

            return view('admin.email.mail', compact(
                'emails',
                'countAll',
                'countInbox',
                'countSpam',
                'countStarred'
            ));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['Failed to load emails. Please try again.']);
        }
    }

    public function show($id)
    {
        try {
            if (empty($id)) {
                return redirect()->back()->withErrors(['Invalid email ID provided']);
            }

            $email = $this->emailService->getEmailByMessageId($id);
            $emails = $this->emailService->getEmailsPaginated($this->authUser->email, 100, 'inbox');

            $countAll = $this->emailService->getAllCount();
            $countInbox = $this->emailService->getInboxCount();
            $countSpam = $this->emailService->getSpamCount();
            $countStarred = $this->emailService->getStarredCount();

            if ($email && $email->count() > 0) {
                $email = $email->first();
            } else {
                $email = null;
            }

            return view('admin.email.show', compact(
                'emails',
                'email',
                'countAll',
                'countInbox',
                'countSpam',
                'countStarred'
            ));
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->withErrors(['Email not found']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['Email not found or invalid message ID']);
        }
    }

    public function getFolder($folder)
    {
        try {
            $validFolders = ['inbox', 'sent', 'spam', 'starred', 'trash'];
            if (!in_array($folder, $validFolders)) {
                return redirect()->back()->withErrors(['Invalid folder specified']);
            }

            $emails = $this->emailService->getEmailsPaginated($this->authUser->email, 100, $folder);

            $countAll = $this->emailService->getAllCount();
            $countInbox = $this->emailService->getInboxCount();
            $countSpam = $this->emailService->getSpamCount();
            $countStarred = $this->emailService->getStarredCount();

            return view('admin.email.mail', compact(
                'emails',
                'folder',
                'countAll',
                'countInbox',
                'countSpam',
                'countStarred'
            ));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['Failed to load folder emails']);
        }
    }

    public function sendClaimReinsurerEmail(SendClaimReinsurerRequest $request)
    {
        try {
            DB::beginTransaction();

            $claim = ClaimRegister::where('claim_no', $request->claim_no)->firstOrFail();
            $customer = $claim->customer ?? Customer::find($request->customer_id);
            $allEmails = array_merge(
                [$request->partner_email],
                $request->contacts ?? [],
                $request->cc_email ?? [],
                $request->bcc_email ?? []
            );
            // $toEmails = explode(',', $request->to_email) ?? [];

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
                        logger()->error('Failed to process attachment', [
                            'index' => $index,
                            'filename' => $file->getClientOriginalName(),
                            'error' => $e->getMessage()
                        ]);
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

                    // $emailRecord = Email::create([
                    //     'claim_id' => $claim->claim_serial_no,
                    //     'claim_no' => $claim->claim_no,
                    //     'sender_email' => auth()->user()->email,
                    //     'sender_name' => auth()->user()->name,
                    //     'recipient_email' => $recipient,
                    //     'recipient_name' => $recipientName,
                    //     'recipients' => json_encode([$recipient]),
                    //     'cc_emails' => json_encode($request->cc_email ?? []),
                    //     'bcc_emails' => json_encode($request->bcc_email ?? []),
                    //     'subject' => $request->subject,
                    //     'body' => $personalizedMessage,
                    //     'attachments' => json_encode($attachments),
                    //     'priority' => $request->priority ?? 'normal',
                    //     'category' => $request->category ?? 'claim',
                    //     'reference' => $request->reference ?? null,
                    //     'status' => 'queued',
                    //     'folder' => 'sent',
                    //     'reply_to_id' => $request->reply_to_id ?? null,
                    //     'created_at' => now(),
                    //     'updated_at' => now()
                    // ]);

                    // $emailRecords[] = $emailRecord;
                    $job = SendOutlookEmailJob::dispatch($emailData, $this->authUser->id, $jobId);

                    if ($request->input('schedule_at')) {
                        $scheduleAt = Carbon::parse($request->input('schedule_at'));
                        $job->delay($scheduleAt);
                    } elseif (!$request->boolean('send_immediately', true)) {
                        $job->delay(now()->addSeconds($index * 2));
                    }


                    // SendClaimReinNotificationJob::dispatch(
                    //     $recipient,
                    //     $request->cc_email ?? [],
                    //     $request->bcc_email ?? [],
                    //     $emailData,
                    //     $emailRecord->id,
                    //     $auth
                    // );

                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    logger()->error("Failed to dispatch email job for batch {$this->batchId}", [
                        'index' => $index,
                        'error' => $e->getMessage()
                    ]);
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
            logger()->error('Claim not found', [
                'claim_no' => $request->claim_no,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Claim not found: ' . $request->claim_no
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            logger()->error('Failed to send claim notification email', [
                'claim_no' => $request->claim_no ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send claim notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recipient names from customer contacts and other sources
     */
    private function getRecipientNames($customer, $request): array
    {
        $recipientNames = [];

        try {
            $emailToNameMap = [];

            if ($customer && $customer->contacts) {
                foreach ($customer->contacts as $contact) {
                    if (!empty($contact->email)) {
                        $emailToNameMap[$contact->email] = $this->formatContactName($contact);
                    }
                }
            }

            if ($customer && method_exists($customer, 'brokers') && $customer->brokers) {
                foreach ($customer->brokers as $broker) {
                    if (!empty($broker->email)) {
                        $emailToNameMap[$broker->email] = $this->formatContactName($broker);
                    }
                }
            }

            $allEmails = array_merge(
                [$request->partner_email],
                $request->contacts ?? [],
                $request->cc_email ?? [],
                $request->bcc_email ?? []
            );

            $allEmails = array_filter(array_unique($allEmails));

            if (Schema::hasTable('customer_contacts')) {
                $contacts = DB::table('customer_contacts')
                    ->whereIn('contact_email', $allEmails)
                    ->select('contact_email', 'contact_email', 'contact_name')
                    ->get();

                foreach ($contacts as $contact) {
                    if (!isset($emailToNameMap[$contact->contact_email])) {
                        $emailToNameMap[$contact->contact_email] = $this->formatGenericContactName($contact);
                    }
                }
            }

            if (Schema::hasTable('reinsurer_contacts')) {
                $reinsurerContacts = DB::table('reinsurer_contacts')
                    ->whereIn('email', $allEmails)
                    ->select('email', 'first_name', 'last_name', 'name', 'company_name')
                    ->get();

                foreach ($reinsurerContacts as $contact) {
                    if (!isset($emailToNameMap[$contact->email])) {
                        $emailToNameMap[$contact->email] = $this->formatGenericContactName($contact);
                    }
                }
            }

            foreach ($allEmails as $email) {
                if (!isset($emailToNameMap[$email])) {
                    $emailToNameMap[$email] = $this->extractNameFromEmail($email);
                }
            }

            foreach ($allEmails as $email) {
                $recipientNames[$email] = $emailToNameMap[$email] ?? 'Sir/Madam';
            }
        } catch (Exception $e) {
            logger()->error('Failed to get recipient names', [
                'error' => $e->getMessage(),
                'customer_id' => $customer->id ?? 'N/A'
            ]);

            $allEmails = array_merge(
                [$request->partner_email],
                $request->contacts ?? [],
                $request->cc_email ?? [],
                $request->bcc_email ?? []
            );

            foreach (array_unique($allEmails) as $email) {
                $recipientNames[$email] = 'Sir/Madam';
            }
        }

        return $recipientNames;
    }

    /**
     * Format contact name from customer contact object
     */
    private function formatContactName($contact): string
    {
        if (is_object($contact)) {
            $firstName = $contact->first_name ?? $contact->firstname ?? null;
            $lastName = $contact->last_name ?? $contact->lastname ?? null;
            $fullName = $contact->name ?? $contact->full_name ?? null;

            if ($firstName && $lastName) {
                return trim($firstName . ' ' . $lastName);
            } elseif ($firstName) {
                return $firstName;
            } elseif ($fullName) {
                return $fullName;
            }
        } elseif (is_array($contact)) {
            $firstName = $contact['first_name'] ?? $contact['firstname'] ?? null;
            $lastName = $contact['last_name'] ?? $contact['lastname'] ?? null;
            $fullName = $contact['name'] ?? $contact['full_name'] ?? null;

            if ($firstName && $lastName) {
                return trim($firstName . ' ' . $lastName);
            } elseif ($firstName) {
                return $firstName;
            } elseif ($fullName) {
                return $fullName;
            }
        }

        return 'Sir/Madam';
    }

    /**
     * Format contact name from generic contact record
     */
    private function formatGenericContactName($contact): string
    {
        $firstName = $contact->first_name ?? null;
        $lastName = $contact->last_name ?? null;
        $fullName = $contact->name ?? $contact->contact_name ?? null;

        if ($firstName && $lastName) {
            return trim($firstName . ' ' . $lastName);
        } elseif ($firstName) {
            return $firstName;
        } elseif ($fullName) {
            $nameParts = explode(' ', trim($fullName));
            return $nameParts[0];
        }

        return 'Sir/Madam';
    }

    /**
     * Extract name from email address as last resort
     */
    private function extractNameFromEmail($email): string
    {
        try {
            $localPart = explode('@', $email)[0];

            $name = str_replace(['.', '_', '-', '+'], ' ', $localPart);

            $nameParts = array_filter(explode(' ', $name));
            $formattedParts = array_map('ucfirst', $nameParts);

            if (!empty($formattedParts)) {
                return $formattedParts[0];
            }
        } catch (Exception $e) {
            logger()->debug('Failed to extract name from email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }

        return 'Sir/Madam';
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

    public function reply(Request $request, $id)
    {
        try {
            $originalEmail = Email::findOrFail($id);

            $validatedData = $request->validate([
                'body' => 'required|string|max:65535',
            ], [
                'body.required' => 'Reply message cannot be empty'
            ]);

            $email = Email::create([
                'sender_email' => auth()->user()->email,
                'sender_name' => auth()->user()->name,
                'recipient_email' => $originalEmail->sender_email,
                'recipient_name' => $originalEmail->sender_name,
                'subject' => 'Re: ' . $originalEmail->subject,
                'body' => $validatedData['body'],
                'folder' => 'sent',
                'sent_at' => now(),
                'parent_email_id' => $originalEmail->id // Track conversation thread
            ]);

            // TODO: Send actual reply email
            // Mail::to($originalEmail->sender_email)->send(new SendEmail($email));

            return redirect()->route('emails.show', $id)->with('success', 'Reply sent successfully!');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->withErrors(['Original email not found']);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Failed to send reply: ' . $e->getMessage())->withInput();
        }
    }

    public function star($id)
    {
        try {
            $email = Email::findOrFail($id);
            $previousStatus = $email->starred;
            $email->update(['starred' => !$email->starred]);
            return back();
        } catch (ModelNotFoundException $e) {
            return back()->withErrors(['Email not found']);
        } catch (Exception $e) {
            return back()->withErrors(['Failed to update email status']);
        }
    }

    public function markAsRead($id)
    {
        try {
            $email = Email::findOrFail($id);
            $wasUnread = !$email->is_read;
            $email->update(['is_read' => true]);
            return response()->json(['success' => true]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'Email not found'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to mark email as read'], 500);
        }
    }

    public function folder($folder)
    {
        try {
            $emails = Email::where('folder', $folder)
                ->orderBy('sent_at', 'desc')
                ->paginate(20);

            return view('emails.index', compact('emails', 'folder'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['Failed to load folder emails']);
        }
    }

    public function downloadAttachment($id, $attachmentIndex)
    {
        try {
            $email = Email::findOrFail($id);

            if (!is_numeric($attachmentIndex) || $attachmentIndex < 0) {
                abort(400, 'Invalid attachment index');
            }

            $attachment = $email->attachments[$attachmentIndex] ?? null;

            if (!$attachment) {
                abort(404, 'Attachment not found');
            }

            if (!Storage::exists($attachment['path'])) {
                abort(404, 'Attachment file not found');
            }

            return Storage::download($attachment['path'], $attachment['name']);
        } catch (ModelNotFoundException $e) {
            logger()->warning('EmailController@downloadAttachment: Email not found', ['id' => $id]);
            abort(404, 'Email not found');
        } catch (Exception $e) {
            logger()->error('EmailController@downloadAttachment: Download failed', [
                'email_id' => $id,
                'attachment_index' => $attachmentIndex,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Failed to download attachment');
        }
    }

    public function settings()
    {
        return view('admin.email.settings');
    }

    public function fetchEmails(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'folder' => 'sometimes|string|in:inbox,drafts,deleted,sent,spam,starred',
                'limit' => 'sometimes|integer|min:1|max:1000',
                'since' => 'sometimes|date',
                'forceFetch' => 'sometimes|boolean'
            ]);

            $fetchParams = array_merge([
                'folder' => 'inbox',
                'limit' => 200,
                'since' => '2020-01-01'
            ], $validatedData);

            $shouldFetch = false;
            $tableExists = false;
            $emailCount = 0;
            $emails = [];

            try {
                $tableExists = DB::getSchemaBuilder()->hasTable('fetched_emails');
                if ($tableExists) {
                    $fetchedEmails = DB::table('fetched_emails')
                        ->where('user_email', $this->authUser->email)
                        ->orderBy('date_received', 'desc');

                    $emailCount = $fetchedEmails->count();
                    $emails = $fetchedEmails->get();
                    $shouldFetch = $emailCount === 0 || ($validatedData['forceFetch'] ?? false);
                } else {
                    $shouldFetch = true;
                }
            } catch (Exception $dbException) {
                $shouldFetch = true;
            }

            if (!$shouldFetch) {
                return response()->json([
                    'success' => true,
                    'message' => 'Emails fetched successfully',
                    'existing_count' => $emailCount,
                    'fetched_count' => 0,
                    'action' => 'skipped',
                    'emails' => $emails
                ]);
            }

            $emails = $this->outlookService->fetchEmails($this->authUser, $fetchParams);
            $fetchedCount = is_array($emails) ? count($emails) : 0;
            // logger()->debug(['emails' => $emails]);
            return response()->json([
                'success' => true,
                'message' => 'Emails fetched successfully',
                'fetched_count' => $fetchedCount,
                'action' => 'fetched_and_stored',
                'emails' => $emails
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch emails: ' . $e->getMessage(),
                'error_details' => [
                    'type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    // /**
    //  * Get email status and tracking information
    //  */
    // public function getEmailStatus(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'claim_no' => 'required|string',
    //             'email_record_id' => 'nullable|integer'
    //         ]);

    //         $query = Email::where('claim_no', $request->claim_no);

    //         if ($request->email_record_id) {
    //             $query->where('id', $request->email_record_id);
    //         }

    //         $emails = $query->orderBy('created_at', 'desc')->get();

    //         $statusSummary = [
    //             'total' => $emails->count(),
    //             'sent' => $emails->where('status', 'sent')->count(),
    //             'queued' => $emails->where('status', 'queued')->count(),
    //             'failed' => $emails->where('status', 'failed')->count(),
    //             'permanently_failed' => $emails->where('status', 'permanently_failed')->count()
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'data' => [
    //                 'claim_no' => $request->claim_no,
    //                 'emails' => $emails->map(function ($email) {
    //                     return [
    //                         'id' => $email->id,
    //                         'recipient_email' => $email->recipient_email,
    //                         'subject' => $email->subject,
    //                         'status' => $email->status,
    //                         'sent_at' => $email->sent_at,
    //                         'failed_at' => $email->failed_at,
    //                         'error_message' => $email->error_message,
    //                         'outlook_message_id' => $email->outlook_message_id,
    //                         'conversation_id' => $email->conversation_id,
    //                         'priority' => $email->priority,
    //                         'has_attachments' => !empty(json_decode($email->attachments, true)),
    //                         'created_at' => $email->created_at
    //                     ];
    //                 }),
    //                 'summary' => $statusSummary
    //             ]
    //         ]);
    //     } catch (Exception $e) {
    //         logger()->error('Failed to get email status', [
    //             'claim_no' => $request->claim_no ?? 'N/A',
    //             'error' => $e->getMessage()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve email status: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * Get conversation thread for a claim
    //  */
    // public function getEmailConversation(Request $request, OutlookService $outlookService)
    // {
    //     try {
    //         $request->validate([
    //             'conversation_id' => 'required|string'
    //         ]);

    //         $result = $outlookService->getConversationMessages($request->conversation_id);

    //         if ($result['success']) {
    //             return response()->json([
    //                 'success' => true,
    //                 'data' => [
    //                     'conversation_id' => $request->conversation_id,
    //                     'messages' => $result['messages'],
    //                     'count' => $result['count']
    //                 ]
    //             ]);
    //         }

    //         throw new Exception($result['error'] ?? 'Failed to get conversation');
    //     } catch (Exception $e) {
    //         logger()->error('Failed to get email conversation', [
    //             'conversation_id' => $request->conversation_id ?? 'N/A',
    //             'error' => $e->getMessage()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve conversation: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * Send a reply to an existing email thread
    //  */
    // public function sendReplyEmail(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'original_message_id' => 'required|string',
    //             'claim_no' => 'required|string',
    //             'subject' => 'nullable|string|max:255',
    //             'message' => 'required|string|max:65535',
    //             'priority' => 'nullable|in:low,normal,high',
    //             'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif'
    //         ]);

    //         DB::beginTransaction();

    //         $claim = ClaimRegister::where('claim_no', $validatedData['claim_no'])->firstOrFail();

    //         // Process attachments
    //         $attachments = [];
    //         if ($request->hasFile('attachments')) {
    //             foreach ($request->file('attachments') as $index => $file) {
    //                 try {
    //                     $filename = time() . '_reply_' . $index . '_' . $file->getClientOriginalName();
    //                     $path = $file->storeAs('claim_attachments/' . $claim->claim_no . '/replies', $filename, 'public');

    //                     $attachments[] = [
    //                         'name' => $file->getClientOriginalName(),
    //                         'path' => storage_path('app/public/' . $path),
    //                         'size' => $file->getSize(),
    //                         'mime_type' => $file->getMimeType()
    //                     ];
    //                 } catch (Exception $e) {
    //                     logger()->error('Failed to process reply attachment', [
    //                         'index' => $index,
    //                         'filename' => $file->getClientOriginalName(),
    //                         'error' => $e->getMessage()
    //                     ]);
    //                     throw new Exception("Failed to process attachment: " . $file->getClientOriginalName());
    //                 }
    //             }
    //         }

    //         // Create email record for the reply
    //         $emailRecord = Email::create([
    //             'claim_id' => $claim->id,
    //             'claim_no' => $claim->claim_no,
    //             'sender_email' => auth()->user()->email,
    //             'sender_name' => auth()->user()->name,
    //             'recipient_email' => 'reply', // Will be determined from original message
    //             'subject' => $validatedData['subject'] ?? 'RE: Claim ' . $claim->claim_no,
    //             'body' => $validatedData['message'],
    //             'attachments' => json_encode($attachments),
    //             'priority' => $validatedData['priority'] ?? 'normal',
    //             'category' => 'claim_reply',
    //             'status' => 'queued',
    //             'folder' => 'sent',
    //             'reply_to_id' => $validatedData['original_message_id'],
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ]);

    //         // Prepare email data for the job
    //         $emailData = [
    //             'claim' => $claim,
    //             'subject' => $validatedData['subject'] ?? 'RE: Claim ' . $claim->claim_no,
    //             'message' => $this->formatMessageForHtml($validatedData['message']),
    //             'priority' => $validatedData['priority'] ?? 'normal',
    //             'category' => 'claim_reply',
    //             'attachments' => $attachments,
    //             'sender_name' => auth()->user()->name,
    //             'sender_email' => auth()->user()->email,
    //             'reply_to_message_id' => $validatedData['original_message_id']
    //         ];

    //         // Dispatch reply job
    //         SendClaimReplyJob::dispatch(
    //             $validatedData['original_message_id'],
    //             $emailData,
    //             $emailRecord->id
    //         )->onQueue('emails');

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Reply has been queued for sending',
    //             'data' => [
    //                 'claim_no' => $claim->claim_no,
    //                 'email_record_id' => $emailRecord->id,
    //                 'original_message_id' => $validatedData['original_message_id'],
    //                 'has_attachments' => !empty($attachments),
    //                 'attachments_count' => count($attachments)
    //             ]
    //         ]);
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         logger()->error('Failed to send reply email', [
    //             'claim_no' => $request->claim_no ?? 'N/A',
    //             'original_message_id' => $request->original_message_id ?? 'N/A',
    //             'error' => $e->getMessage()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to send reply: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * Retry failed email sending
    //  */
    // public function retryFailedEmail(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'email_record_id' => 'required|integer'
    //         ]);

    //         $emailRecord = Email::findOrFail($request->email_record_id);

    //         if (!in_array($emailRecord->status, ['failed', 'permanently_failed'])) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Email is not in a failed state'
    //             ], 400);
    //         }

    //         // Reset status to queued
    //         $emailRecord->update([
    //             'status' => 'queued',
    //             'error_message' => null,
    //             'failed_at' => null,
    //             'updated_at' => now()
    //         ]);

    //         // Get claim data
    //         $claim = ClaimRegister::find($emailRecord->claim_id);
    //         if (!$claim) {
    //             throw new Exception('Associated claim not found');
    //         }

    //         // Prepare email data
    //         $emailData = [
    //             'claim' => $claim,
    //             'subject' => $emailRecord->subject,
    //             'message' => $emailRecord->body,
    //             'priority' => $emailRecord->priority ?? 'normal',
    //             'category' => $emailRecord->category ?? 'claim',
    //             'attachments' => json_decode($emailRecord->attachments, true) ?? [],
    //             'sender_name' => $emailRecord->sender_name,
    //             'sender_email' => $emailRecord->sender_email,
    //             'reply_to_message_id' => $emailRecord->reply_to_id
    //         ];

    //         // Dispatch appropriate job based on whether it's a reply
    //         if ($emailRecord->reply_to_id) {
    //             SendClaimReplyJob::dispatch(
    //                 $emailRecord->reply_to_id,
    //                 $emailData,
    //                 $emailRecord->id
    //             )->onQueue('emails');
    //         } else {
    //             SendClaimReinNotificationJob::dispatch(
    //                 $emailRecord->recipient_email,
    //                 json_decode($emailRecord->cc_emails, true) ?? [],
    //                 json_decode($emailRecord->bcc_emails, true) ?? [],
    //                 $emailData,
    //                 $emailRecord->id
    //             )->onQueue('emails');
    //         }

    //         logger()->info('Failed email retry queued', [
    //             'email_record_id' => $emailRecord->id,
    //             'claim_no' => $claim->claim_no,
    //             'recipient' => $emailRecord->recipient_email
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Email has been queued for retry',
    //             'data' => [
    //                 'email_record_id' => $emailRecord->id,
    //                 'claim_no' => $claim->claim_no,
    //                 'status' => 'queued'
    //             ]
    //         ]);
    //     } catch (Exception $e) {
    //         logger()->error('Failed to retry email', [
    //             'email_record_id' => $request->email_record_id ?? 'N/A',
    //             'error' => $e->getMessage()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retry email: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * Get email statistics for a claim
    //  */
    // public function getEmailStatistics(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'claim_no' => 'nullable|string',
    //             'date_from' => 'nullable|date',
    //             'date_to' => 'nullable|date',
    //         ]);

    //         $query = Email::query();

    //         if ($request->claim_no) {
    //             $query->where('claim_no', $request->claim_no);
    //         }

    //         if ($request->date_from) {
    //             $query->where('created_at', '>=', $request->date_from);
    //         }

    //         if ($request->date_to) {
    //             $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
    //         }

    //         $emails = $query->get();

    //         $statistics = [
    //             'total_emails' => $emails->count(),
    //             'sent_successfully' => $emails->where('status', 'sent')->count(),
    //             'pending' => $emails->where('status', 'queued')->count(),
    //             'failed' => $emails->where('status', 'failed')->count(),
    //             'permanently_failed' => $emails->where('status', 'permanently_failed')->count(),
    //             'success_rate' => $emails->count() > 0 ?
    //                 round(($emails->where('status', 'sent')->count() / $emails->count()) * 100, 2) : 0,
    //             'by_priority' => [
    //                 'high' => $emails->where('priority', 'high')->count(),
    //                 'normal' => $emails->where('priority', 'normal')->count(),
    //                 'low' => $emails->where('priority', 'low')->count()
    //             ],
    //             'by_category' => $emails->groupBy('category')->map(function ($group) {
    //                 return $group->count();
    //             }),
    //             'recent_activity' => $emails->where('created_at', '>=', now()->subDays(7))
    //                 ->groupBy(function ($email) {
    //                     return $email->created_at->format('Y-m-d');
    //                 })
    //                 ->map(function ($group) {
    //                     return $group->count();
    //                 })
    //                 ->sortKeys()
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'data' => $statistics
    //         ]);
    //     } catch (Exception $e) {
    //         logger()->error('Failed to get email statistics', [
    //             'error' => $e->getMessage()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to get statistics: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}
