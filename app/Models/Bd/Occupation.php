<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    protected $table = 'occupation';
    
    protected $primaryKey = 'occupation_code';

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';
    
    /**
     * client table relationship.
     */
    public function client()
    {
        return $this->hasOne(Client::class,'occupation_code','occupation_code');
    }
}
