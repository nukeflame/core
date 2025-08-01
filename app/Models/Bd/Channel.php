<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $table = 'channels';

    protected $primaryKey ='channel';

    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;

}
