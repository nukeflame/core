<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalSourceLink extends Model
{
    use HasFactory;

    protected $table = 'approval_source_link';
    public $timestamps = true;
    public $primaryKey = 'id';
    public $incrementing = false;
    protected $guarded = [];
}
