<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimStatusParam extends Model
{
    use HasFactory;

    protected $table='claim_status_param';
	public $timestamps=true;
	public $primaryKey='id';
	public $incrementing=false;
    protected $guarded = [];
}
