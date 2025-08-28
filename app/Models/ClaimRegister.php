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

    protected $primaryKey = 'claim_serial_no';
    public $incrementing  = false;


    protected $guarded = [];

    protected $casts = [
        'date_of_loss' => 'date',
        'cover_from' => 'date',
        'cover_to' => 'date',
        'created_date' => 'datetime',
        'date_notified_insurer' => 'date',
        'date_notified_reinsurer' => 'date',
        'notification_sent_at' => 'datetime',
    ];
}
