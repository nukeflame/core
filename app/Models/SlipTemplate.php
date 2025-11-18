<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlipTemplate extends Model
{
    use HasFactory;

    protected $table = 'slip_templates';

    public $timestamps = true;
    public $primaryKey = 'id';
    public $incrementing = false;
    protected $guarded = [];
}
