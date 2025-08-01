<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';
    public $timestamps = false;
    public $primaryKey = 'class_code';
    public $incrementing = false;
    protected $guarded = [];
}
