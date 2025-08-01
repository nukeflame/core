<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeepDiveSession extends Model
{
    use HasFactory;
    protected $fillable = [
        'global_customer_id',
        'session_title',
        'session_description',
        'attachment',
        'policy_no'
    ];
}
