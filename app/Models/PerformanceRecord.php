<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'account_period',
        'record_date',

        'new_fac_gwp',
        'new_special_gwp',
        'new_treaty_gwp',
        'new_market_gwp',
        'new_fac_income',
        'new_special_income',
        'new_treaty_income',
        'new_market_income',

        'renewal_fac_gwp',
        'renewal_special_gwp',
        'renewal_treaty_gwp',
        'renewal_market_gwp',
        'renewal_fac_income',
        'renewal_special_income',
        'renewal_treaty_income',
        'renewal_market_income',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'record_date' => 'date',
        'new_fac_gwp' => 'float',
        'new_special_gwp' => 'float',
        'new_treaty_gwp' => 'float',
        'new_market_gwp' => 'float',
        'new_fac_income' => 'float',
        'new_special_income' => 'float',
        'new_treaty_income' => 'float',
        'new_market_income' => 'float',
        'renewal_fac_gwp' => 'float',
        'renewal_special_gwp' => 'float',
        'renewal_treaty_gwp' => 'float',
        'renewal_market_gwp' => 'float',
        'renewal_fac_income' => 'float',
        'renewal_special_income' => 'float',
        'renewal_treaty_income' => 'float',
        'renewal_market_income' => 'float',
    ];

    /**
     * Get the user that owns the performance record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the total new business GWP
     *
     * @return float
     */
    public function getNewBusinessGwpAttribute(): float
    {
        return $this->new_fac_gwp +
            $this->new_special_gwp +
            $this->new_treaty_gwp +
            $this->new_market_gwp;
    }

    /**
     * Get the total new business income
     *
     * @return float
     */
    public function getNewBusinessIncomeAttribute(): float
    {
        return $this->new_fac_income +
            $this->new_special_income +
            $this->new_treaty_income +
            $this->new_market_income;
    }

    /**
     * Get the total renewal business GWP
     *
     * @return float
     */
    public function getRenewalBusinessGwpAttribute(): float
    {
        return $this->renewal_fac_gwp +
            $this->renewal_special_gwp +
            $this->renewal_treaty_gwp +
            $this->renewal_market_gwp;
    }

    /**
     * Get the total renewal business income
     *
     * @return float
     */
    public function getRenewalBusinessIncomeAttribute(): float
    {
        return $this->renewal_fac_income +
            $this->renewal_special_income +
            $this->renewal_treaty_income +
            $this->renewal_market_income;
    }

    /**
     * Get the total GWP (new + renewal)
     *
     * @return float
     */
    public function getTotalGwpAttribute(): float
    {
        return $this->new_business_gwp + $this->renewal_business_gwp;
    }

    /**
     * Get the total income (new + renewal)
     *
     * @return float
     */
    public function getTotalIncomeAttribute(): float
    {
        return $this->new_business_income + $this->renewal_business_income;
    }

    public function accountHandler()
    {
        return $this->belongsTo(User::class)->withTimestamps();
    }
}
