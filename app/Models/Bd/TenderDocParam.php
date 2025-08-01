<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderDocParam extends Model
{
    use HasFactory;

    protected $table = 'tender_doc_param';
    protected $primaryKey = 'doc_id'; // Changed from array to string
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->doc_id)) {
                // Get the maximum doc_id and increment by 1
                $maxId = static::max('doc_id');
                $model->doc_id = ($maxId ?? 0) + 1;
            }
        });
    }
}
