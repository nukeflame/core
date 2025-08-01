<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendEmail extends Model
{
    use HasFactory;

    protected $table = 'sendemails';
    public $timestamps = true;
    public $primaryKey = ['email_id'];
    public $incrementing = false;
    protected $guarded = [];
}
