<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'gender';
    
    protected $primaryKey = 'gender_code';

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';


    /**
     * client table relationship.
     */
    public function client()
    {
        return $this->hasOne(Client::class,'gender_code','gender_code');
    }
    
}
