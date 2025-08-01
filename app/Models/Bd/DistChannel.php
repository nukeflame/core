<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DistChannel extends Model
{
    protected $table = 'dist_channel';

    protected $primaryKey =false;

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

}
