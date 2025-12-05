<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReinclassPremtype extends Model
{
    use HasFactory;

    protected $table = 'reinclass_premtypes';
    public $timestamps = false;
    public $primaryKey = 'premtype_code';

    public $incrementing = false;

    protected $guarded = [];

    function classGroup()
    {
        return $this->belongsTo(ReinsClass::class, 'reinclass', 'class_code');
    }
}
