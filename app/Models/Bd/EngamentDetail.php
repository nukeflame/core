<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Model;

class EngamentDetail extends Model
{
    protected $table = 'leads_source';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'description',
        'status',
        'sort_order',
    ];
}
