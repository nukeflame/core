<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageComment extends Model
{
    use HasFactory;
    protected $table = 'stage_comments';
    protected $primaryKey ='id';
}
