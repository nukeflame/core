<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Services\EmailService;
use App\Services\OutlookService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EmailController extends Controller
{
    protected $emailService;
    private $outlookService;
    private $userEmail;

    public function __construct(EmailService $emailService, OutlookService $outlookService)
    {
        $this->emailService = $emailService;
        $this->outlookService = $outlookService;
        $this->userEmail = auth()->user()->email;
    }

    public function index()
    {
        try {
            $emails = $this->emailService->getEmailsPaginated($this->userEmail, 100, 'inbox');

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
            $emails = $this->emailService->getEmailsPaginated($this->userEmail, 100, 'inbox');

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

            $emails = $this->emailService->getEmailsPaginated($this->userEmail, 100, $folder);

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

    public function compose()
    {
        return view('emails.compose');
    }

    public function send(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'recipient_email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'body' => 'required|string|max:65535',
                'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif'
            ], [
                'recipient_email.required' => 'Recipient email is required',
                'recipient_email.email' => 'Please provide a valid email address',
                'subject.required' => 'Email subject is required',
                'body.required' => 'Email body cannot be empty',
                'attachments.*.max' => 'Each attachment must be smaller than 10MB'
            ]);

            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    try {
                        $path = $file->store('email_attachments');
                        $attachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'mime_type' => $file->getMimeType()
                        ];

                        logger()->debug('Attachment processed', [
                            'index' => $index,
                            'name' => $file->getClientOriginalName(),
                            'size' => $file->getSize()
                        ]);
                    } catch (Exception $e) {
                        logger()->error('Failed to process attachment', [
                            'index' => $index,
                            'filename' => $file->getClientOriginalName(),
                            'error' => $e->getMessage()
                        ]);
                        throw new Exception("Failed to process attachment: " . $file->getClientOriginalName());
                    }
                }
            }

            // Create email record
            $email = Email::create([
                'sender_email' => auth()->user()->email,
                'sender_name' => auth()->user()->name,
                'recipient_email' => $validatedData['recipient_email'],
                'recipient_name' => $request->input('recipient_name', ''),
                'subject' => $validatedData['subject'],
                'body' => $validatedData['body'],
                'attachments' => $attachments,
                'folder' => 'sent',
                'sent_at' => now()
            ]);

            // TODO: Implement actual email sending
            // Mail::to($request->recipient_email)->send(new SendEmail($email));

            return redirect()->route('emails.index')->with('success', 'Email sent successfully!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage())->withInput();
        }
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

            // Check if file exists
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
                'folder' => 'sometimes|string|in:inbox,sent,spam,starred',
                'limit' => 'sometimes|integer|min:1|max:1000',
                'since' => 'sometimes|date',
                'force_fetch' => 'sometimes|boolean'
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
                    $fetchedEmails = DB::table('fetched_emails')->where('user_email', $this->userEmail);
                    $emailCount = $fetchedEmails->count();
                    $emails = $fetchedEmails->get();
                    $shouldFetch = $emailCount === 0 || ($validatedData['force_fetch'] ?? false);
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

            $emails = $this->outlookService->fetchEmails($fetchParams);
            $fetchedCount = is_array($emails) ? count($emails) : 0;

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
}
