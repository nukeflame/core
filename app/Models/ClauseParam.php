<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClauseParam extends Model
{
    use HasFactory;

    protected $table = 'clauses_param';
    public $timestamps = true;
    public $primaryKey = 'clause_id';
    public $incrementing = false;
    protected $guarded = [];
}
