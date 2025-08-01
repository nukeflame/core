<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BdPremtype extends Model
{
    use HasFactory;
    protected $table = 'bd_premtypes';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public $incrementing = true;
    protected $fillable = [
        'cover_no',
        'endorsement_no',
        'reinclass',
        'premtype_code',
        'premtype_name',
        'comm_rate',
        'treaty',
    ];
    


}
