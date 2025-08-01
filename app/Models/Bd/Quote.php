<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;
     protected $table = 'quotes';
     protected $primaryKey = 'id';
     protected $fillable =[
        'quote_title'

     ];
   
}
