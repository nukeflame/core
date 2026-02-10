<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $table = 'tax_rates';
    public $timestamps = false;
    public $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];

    protected const CACHE_KEY = 'tax_rates_current';
    protected const CACHE_TTL = 3600;

    public static function getCurrentRate(string $taxCode, float $default = 0): float
    {
        $rates = static::getAllCurrentRates();

        return (float) ($rates[$taxCode] ?? $default);
    }

    public static function getAllCurrentRates(): array
    {
        return Cache::remember(
            static::CACHE_KEY,
            static::CACHE_TTL,
            fn() => static::pluck('tax_rate', 'tax_code')
                ->map(fn($rate) => (float) $rate)
                ->toArray()
        );
    }

    public static function clearCache(): void
    {
        Cache::forget(static::CACHE_KEY);
    }
}
