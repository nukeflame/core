<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;


class ClassGroup extends Model
{
    use HasFactory;

    protected $table = 'class_groups';
    protected $primaryKey = 'group_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'group_code',
        'group_name',
        'status',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class, 'class_group_code', 'group_code');
    }

    public function activeClasses(): HasMany
    {
        return $this->classes()->where('status', 'A');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('group_name');
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'A';
    }

    public static function getAllCached(): array
    {
        return Cache::remember('class_groups:all', 3600, function () {
            return static::active()
                ->ordered()
                ->get()
                ->toArray();
        });
    }

    public static function getForSelect(): array
    {
        return Cache::remember('class_groups:select', 3600, function () {
            return static::active()
                ->orderBy('sort_order')
                ->get(['group_code', 'group_name'])
                ->toArray();
        });
    }

    public static function getForModal(): array
    {
        return Cache::remember('class_groups:modal', 3600, function () {
            return static::active()
                ->ordered()
                ->get(['group_code', 'group_name'])
                ->map(fn($item) => [
                    'class_group_code' => $item->group_code,
                    'class_group_name' => $item->group_name,
                ])
                ->toArray();
        });
    }

    public static function getWithClasses(): array
    {
        return Cache::remember('class_groups:with_classes', 3600, function () {
            return static::active()
                ->with(['classes' => fn($q) => $q->orderBy('class_name')])
                ->ordered()
                ->get()
                ->mapWithKeys(fn($group) => [
                    $group->group_name => $group->classes
                        ->mapWithKeys(fn($c) => [$c->class_code => $c->class_name])
                        ->toArray()
                ])
                ->toArray();
        });
    }

    public static function getValidCodes(): array
    {
        return Cache::remember('class_groups:valid', 3600, function () {
            return static::active()->pluck('group_code')->toArray();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('class_groups:all');
        Cache::forget('class_groups:active');
        Cache::forget('class_groups:select');
        Cache::forget('class_groups:modal');
        Cache::forget('class_groups:with_classes');
        Cache::forget('class_groups:valid');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(fn() => static::clearCache());
        static::deleted(fn() => static::clearCache());
    }
}
