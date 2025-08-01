<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinderCover extends Model
{
    use HasFactory;
    // protected $connection = 'mysql';
    protected $table = 'binder_register';
    public $timestamps = false;
    public $primaryKey = ['binder_cov_no'];
    public $incrementing = false;
    protected $guarded = [];
}
