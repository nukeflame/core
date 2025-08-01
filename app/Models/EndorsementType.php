<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndorsementType extends Model
{
    use HasFactory;

    protected $table = 'endorsement_types';
    public $timestamps = true;
    public $primaryKey = ['type_of_bus', 'endorse_type_slug'];
    public $incrementing = false;
    protected $guarded = [];
}
