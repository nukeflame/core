<?php

namespace App\Http\Controllers;

use App\Services\OutlookService;
use App\Services\MailService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

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
            'attachments.*' => 'file|max:10240'
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
}
