<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'class_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'class_code',
        'class_name',
        'class_group_code',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_code', 'group_code');
    }

    public function classGroup(): BelongsTo
    {
        return $this->category();
    }

    public function group(): BelongsTo
    {
        return $this->category();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('category', fn($q) => $q->where('status', 'A'));
    }

    public function scopeInGroup(Builder $query, string $groupCode): Builder
    {
        return $query->where('class_group_code', $groupCode);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('class_name');
    }

    public function getGroupNameAttribute(): ?string
    {
        return $this->category?->group_name;
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->class_code} - {$this->class_name}";
    }

    public static function getAllCached(): array
    {
        return Cache::remember('classes:all', 3600, function () {
            return static::active()
                ->with('category:group_code,group_name')
                ->ordered()
                ->get()
                ->toArray();
        });
    }

    public static function getForSelect(): array
    {
        return Cache::remember('classes:select', 3600, function () {
            return static::active()
                ->ordered()
                ->pluck('class_name', 'class_code')
                ->toArray();
        });
    }

    public static function getGroupedByCategory(): array
    {
        return Cache::remember('classes:grouped', 3600, function () {
            return static::active()
                ->with('category:group_code,group_name')
                ->ordered()
                ->get()
                ->groupBy(fn($class) => $class->category?->group_name ?? 'Uncategorized')
                ->map(fn($items) => $items->mapWithKeys(
                    fn($item) => [$item->class_code => $item->class_name]
                )->toArray())
                ->sortKeys()
                ->toArray();
        });
    }

    public static function getByGroup(string $groupCode): array
    {
        return static::inGroup($groupCode)
            ->ordered()
            ->pluck('class_name', 'class_code')
            ->toArray();
    }

    public static function getValidCodes(): array
    {
        return Cache::remember('classes:valid', 3600, function () {
            return static::active()->pluck('class_code')->toArray();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('classes:all');
        Cache::forget('classes:select');
        Cache::forget('classes:grouped');
        Cache::forget('classes:valid');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(fn() => static::clearCache());
        static::deleted(fn() => static::clearCache());
    }
}
