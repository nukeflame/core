<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ARCustomerGroup extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql';
    protected $table = 'ar_customer_groups';
    public $timestamps = false;
    public $primaryKey = 'group_id';
    public $incrementing = false;
    protected $guarded = [];
}
