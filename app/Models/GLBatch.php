<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GLBatch extends Model
{
    protected $table = 'glbatch';

    public $timestamps = true;
    public $primaryKey = ['batch_no'];
    public $incrementing = false;
    protected $guarded = [];
}
