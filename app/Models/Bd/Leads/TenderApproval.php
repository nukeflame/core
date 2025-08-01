<?php

namespace App\Models\Bd\Leads;

use App\Models\Bd\Tender;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderApproval extends Model
{
    public $primaryKey ='id';
    protected $fillable = [
        'tender_id',
        'tender_no',
        'stage_id',
        'email_dated',
        'commence_year',
        'main_email',
        'cc_emails',
        'pdf_filename',
        'approver_id',
        'submitter_id',
        'status',
        'remarks',
        'file'
    ];

    protected $casts = [
        'main_email' => 'array',
        'cc_emails' => 'array',
        'approver_id' => 'array'
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class, 'tender_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }
}
