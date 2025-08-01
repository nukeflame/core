<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderSubcategory extends Model
{
    use HasFactory;

    protected $table = 'tender_subcategories';

    public function tenderDocParam()
    {
        return $this->belongsTo(TenderDocParam::class, 'doc_id', 'doc_id');
    }
}
