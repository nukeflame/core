<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTypes extends Model
{
    use HasFactory;

    protected $table = 'customer_types';
    public $timestamps = false;
    // public $primaryKey=['type_name'];
    public $primaryKey = 'type_id';
    public $incrementing = false;
    protected $guarded = [];
}
