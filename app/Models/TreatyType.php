<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatyType extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='treaty_types';
	public $timestamps=false;
	public $primaryKey='treaty_code';
	public $incrementing=false;
    protected $guarded = [];

    public function cover()
    {
        return $this->hasMany(CoverRegister::class, 'treaty_type', 'treaty_code');
    }
}
