<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\SoftDeletes;


class CoverAttachment extends Model
{
    use HasFactory, ModelCompositeKey, SoftDeletes;

    protected $table = 'cover_attachment';
    public $timestamps = true;
    public $primaryKey = ['id'];
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'file_base64' => 'string'
    ];
}
