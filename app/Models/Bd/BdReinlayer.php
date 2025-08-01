<?php

namespace App\Models\Bd;

use App\Models\ReinsClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BdReinlayer extends Model
{
    use HasFactory;
    protected $table = 'bd_reinlayers';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public function reinclassInfo()
    {
        return $this->belongsTo(ReinsClass::class, 'reinclass', 'class_code');
    }

}
