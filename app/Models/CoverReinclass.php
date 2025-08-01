<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoverReinclass extends Model
{
    use HasFactory, ModelCompositeKey;
    // protected $connection = 'mysql'; 
    protected $table='cover_reinclass';
	public $timestamps=true;
	public $primaryKey=['cover_no','endorsement_no','class_code'];
	public $incrementing=false;
    protected $guarded = [];


    public function rein_class()
    {
        return $this->hasOne(ReinsClass::class, 'class_code', 'reinclass');
    }
}
