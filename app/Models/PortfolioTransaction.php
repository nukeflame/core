<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customeracc_det';

    public $timestamps = false;

    protected $guarded = [];

    public function scopePortfolio(Builder $query): Builder
    {
        return $query->where('entry_type_descr', 'portfolio');
    }
}
