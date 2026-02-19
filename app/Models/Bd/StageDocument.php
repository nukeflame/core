<?php

 namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageDocument extends Model
{
    use HasFactory;
    protected $table='stage_documents';
     public $primaryKey = 'id';
     protected $fillable =[
        'stage',
        'doc_type',
        'mandatory',
        'category_type',
        'type_of_bus',
        'path',
        's3_path',
     ];

   
}
