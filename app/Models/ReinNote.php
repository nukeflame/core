<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReinNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rein_notes';
    protected $guarded = [];
}
