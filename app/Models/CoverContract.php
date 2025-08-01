<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoverContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'cedant_id',
        'cover_type',
        'region_id',
        'line_of_business',
        'premium',
        'inception_date',
        'status',
        'placement_date',
        'completion_date'
    ];

    protected $casts = [
        'inception_date' => 'date',
        'placement_date' => 'date',
        'completion_date' => 'date',
        'premium' => 'decimal:2',
    ];

    public function cedant()
    {
        return $this->belongsTo(Customer::class);
    }

    // public function coverType()
    // {
    //     return $this->belongsTo(CoverType::class);
    // }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // public function lineOfBusiness()
    // {
    //     return $this->belongsTo(LineOfBusiness::class);
    // }

    public function getStatusColorClass()
    {
        return [
            'placed' => 'success',
            'pending' => 'warning',
            'declined' => 'danger',
        ][$this->status] ?? 'secondary';
    }

    public function getFormattedPremiumAttribute()
    {
        return 'KES ' . number_format($this->premium, 2);
    }

    public function scopeFilterByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('inception_date', [$startDate, $endDate]);
    }

    public function scopeFilterByRegion($query, $regionId)
    {
        if ($regionId !== 'all') {
            return $query->where('region_id', $regionId);
        }
        return $query;
    }

    public function scopeFilterByLineOfBusiness($query, $lobId)
    {
        // if ($lobId !== 'all') {
        //     return $query->where('line_of_business_id', $lobId);
        // }
        return $query;
    }
}
