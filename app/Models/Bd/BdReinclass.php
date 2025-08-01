<?php

namespace App\Models\Bd;

use App\Models\ReinsClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BdReinclass extends Model
{
    use HasFactory;
    protected $table = 'bd_reinclasses';
    public $timestamps = true;
    public $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $fillable = [
        'cover_no',
        'endorsement_no',
        'reinclass',
    ];


    public function rein_class()
    {
        return $this->hasOne(ReinsClass::class, 'class_code', 'reinclass');
    }
}
