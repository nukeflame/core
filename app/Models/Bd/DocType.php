<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocType extends Model
{
    use HasFactory;
    protected $table = 'doc_types';
    public $primaryKey = 'id';
    protected $guarded = [];

}
