<?php

namespace App\Models\Bd;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $incrementing = false;

}
