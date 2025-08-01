<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverSlipWording extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'coverslipwording';
    public $timestamps = true;
    public $primaryKey = ['id'];
    public $incrementing = false;
    protected $guarded = [];
}
