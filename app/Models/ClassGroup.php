<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassGroup extends Model
{
    use HasFactory;

    protected $table = 'class_groups';
    public $timestamps = false;
    public $primaryKey = 'group_code';
    public $incrementing = false;
    protected $guarded = [];
}
