<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ApprovalsTracker extends Model
{
    use HasFactory;

    protected $table = 'approvals_tracker';
    public $timestamps = true;
    public $primaryKey = 'id';
    public $incrementing = false;
    protected $guarded = [];

    /**
     * Get the source associated with the ApprovalsTracker
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function source(): HasOne
    {
        return $this->hasOne(ApprovalSourceLink::class, 'approval_id', 'id');
    }

    /**
     * Get the source associated with the ApprovalsTracker
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function notification(): HasOne
    {
        return $this->hasOne(Notification::class, 'approval_tracker_id', 'id');
    }
}
