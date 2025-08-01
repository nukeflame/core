<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndorsementNarration extends Model
{
    // use HasFactory;

    protected $table = 'endorsement_narration';
    public $timestamps = true;
    public $primaryKey = ['endorsement_no', 'endorse_type_slug'];
    public $incrementing = false;
    protected $guarded = [];
}
