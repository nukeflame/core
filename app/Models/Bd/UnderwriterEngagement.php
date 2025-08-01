<?php

namespace App\Models\Bd;

use App\Models\Admin\Client\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnderwriterEngagement extends Model
{
    use HasFactory;
    protected $fillable = ['global_customer_id', 'engagement_type', 'attachment','policy_no'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}