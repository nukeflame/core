<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimRegister extends Model
{
    use HasFactory, SoftDeletes;

    protected $table     = 'claim_register';
    public $timestamps   = false;
    public $primaryKey   = ['claim_no'];
    public $incrementing = false;
    protected $guarded = [];

    protected $fillable = [
        'notification_status',
        'notification_sent_at',
        'notification_sent_by'
    ];
}
