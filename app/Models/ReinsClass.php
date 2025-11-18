<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReinsClass extends Model
{
    use HasFactory;

    protected $table = 'reinsclasses';

    public $timestamps = false;
    public $primaryKey = 'class_code';
    public $incrementing = false;

    protected $keyType = 'string';
    protected $guarded = [];
}
