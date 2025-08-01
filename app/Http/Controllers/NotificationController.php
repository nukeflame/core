<?php

namespace App\Http\Controllers;

use App\Models\ApprovalsTracker;
use App\Models\SendEmail;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    public $email_subject;
    public $email_from;
    public $email_to;
    public $email_body;
    public $email_cc;
    public $email_salutation;

    public function fetchNotifications()
    {
        try {
            $notifications = Notification::where('created_by', auth()->id())
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();

            $notification_counter = Notification::where('created_by', auth()->id())
                ->where('read', false)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();

            $count = $notification_counter->count();

            return response()->json([
                'count' => $count,
                'notifications' => $notifications,
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if ($notification && $notification->created_by === auth()->id()) {
            $notification->update(['read' => true]);
            return response()->json(['success' => true, 'notificationId' => $notification->id]);
        }

        return response()->json(['success' => false], 403);
    }

    public function markAllAsRead()
    {
        $notifications = Notification::where('created_by', auth()->id())->get();
        $notificationIds = [];
        if (count($notifications) > 0) {
            foreach ($notifications as $notification) {
                $notification->update(['read' => true]);
                $notificationIds[] = $notification->id;
            }
            return response()->json(['success' => true, 'notificationIds' => $notificationIds]);
        }
        return response()->json(['success' => false], 403);
    }

    public function show($id)
    {
        $approval = ApprovalsTracker::find($id);
        if (User::where('user_name', $approval->created_by)->exists()) {
            return response()->json(['success' => true, 'data' => $approval], 200);
        }
        return response()->json(['success' => false, 'data' => []], 403);
    }


    public function store_email()
    {
        SendEmail::create([
            'email_id' => SendEmail::max('email_id') + 1,
            'template_name' => 'none',
            'email_subject' => $this->email_subject,
            'email_body' => $this->email_body,
            'email_from' => $this->email_from,
            'email_to' => $this->email_to,
            'salutation' => $this->email_salutation,
            'email_cc' => $this->email_cc,
            'status' => 'SEND',
            'created_by' => 'system',
            'updated_by' => 'system',

        ]);
    }
}
