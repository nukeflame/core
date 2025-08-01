<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageTransition extends Model
{
    use HasFactory;
    protected $fillable = ['opportunity_id', 'stage', 'started_at', 'ended_at', 'duration'];
}
