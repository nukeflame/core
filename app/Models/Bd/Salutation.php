<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Model;

class Salutation extends Model
{
    protected $table = 'salutation';
    
    protected $primaryKey = 'salutation_code';

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

  /**
     * client table relationship.
     */
    public function client()
    {
        return $this->hasOne(Client::class,'salutation_code','salutation_code');
    }
}
