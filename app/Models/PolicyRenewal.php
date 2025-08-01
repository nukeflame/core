<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolicyRenewal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'policy_renewals';

    protected $fillable = [
        'client_name',
        'client_email',
        'policy_number',
        'renewal_date',
        'last_notice_sent',
        'notice_status',
        'doc_name'
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'last_notice_sent' => 'date'
    ];

    protected static function booted()
    {
        static::deleting(function ($policyRenewal) {
            $policyRenewal->documents()->each(function ($document) {
                if (file_exists(storage_path('app/public/renewals/' . $document->doc_name))) {
                    unlink(storage_path('app/public/renewals/' . $document->doc_name));
                }
                $document->delete();
            });
        });
    }

    public function documents()
    {
        return $this->hasMany(PolicyRenewalDocument::class);
    }
}
