<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayMethod extends Model
{
    use HasFactory;

    protected $table = 'pay_method';
    public $timestamps = false;
    public $primaryKey = 'pay_method_code';
    public $incrementing = false;
    protected $guarded = [];
}
