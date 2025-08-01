<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoverGledgerLink extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='cover_gledger_link';
	public $timestamps=false;
	public $primaryKey=['type_of_bus','transaction_type','entry_type_descr'];
	public $incrementing=false;
    protected $guarded = [];
}
