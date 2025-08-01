<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyRenewalDocument extends Model
{
    use HasFactory;

    protected $table = 'policy_renewal_documents';

    protected $fillable = [
        'policy_renewal_id',
        'doc_name',
        'doc_path',
        'doc_size',
        'doc_type'
    ];

    public function policyRenewal()
    {
        return $this->belongsTo(PolicyRenewal::class);
    }
}
