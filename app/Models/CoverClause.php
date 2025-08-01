<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoverClause extends Model
{
    use HasFactory;

    protected $table = 'cover_clauses';
    public $timestamps = true;
    public $primaryKey = ['cover_no', 'endorsement_no', 'clause_id'];
    public $incrementing = false;
    protected $guarded = [];
}
