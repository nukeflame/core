<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendemailTemplate extends Model
{
    use HasFactory;

    protected $table = 'sendemail_templates';
    public $timestamps = true;
    public $primaryKey = ['template_id'];
    public $incrementing = false;
    protected $guarded = [];
}
