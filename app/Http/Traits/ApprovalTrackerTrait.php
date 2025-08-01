<?php

namespace App\Http\Traits;

use App\Models\ApprovalsTracker;
use App\Models\Notification;
use App\Models\SystemProcessAction;
use App\Models\User;
use Illuminate\Support\Arr;

trait ApprovalTrackerTrait
{
    public function syncUsersOrModules(SystemProcessAction $approvalAction, Notification $approval)
    {
        switch ($approvalAction->nice_name) {
            case 'verify_cover':
                $received_by = User::permission('app.approval.manage')->pluck('id');
                $approval->users()->sync($received_by);
                break;
        }
        return $approval;
    }

    public function syncReadStatus(Notification $approvalNotice, ApprovalsTracker $approval)
    {
        $userIdArr = match ($approvalNotice->type) {
            'verify_cover' => User::permission('app.approval.manage')->get()->pluck('id'),
            "verify_claim_intimation_process" => User::permission('claims.notification.verify')->get()->pluck('id'),
        };

        $storeNoticeReadArray =  Arr::map(
            $userIdArr->toArray(),
            fn($uId) => [
                "user_id"         => $uId,
                "is_read"         => $uId === $approvalNotice->created_by
            ]
        );
        return $approvalNotice->read_status()->sync($storeNoticeReadArray);
    }
}
