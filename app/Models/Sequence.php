<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Sequence extends Model
{
    protected $fillable = [
        'sequence_name',
        'prefix',
        'current_value',
        'year',
        'created_by',
        'updated_by',
        'notes',
    ];

    protected $casts = [
        'current_value' => 'integer',
        'year' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::user()->user_name ?? Auth::user()->name;
                $model->updated_by = Auth::user()->user_name ?? Auth::user()->name;
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::user()->user_name ?? Auth::user()->name;
            }
        });
    }

    /**
     * Get the next value for a sequence with pessimistic locking
     *
     * @param string $sequenceName
     * @param int|null $year If null, sequence is not year-based
     * @param string|null $prefix Optional prefix for the generated number
     * @param array $context Optional context for history logging
     * @return array ['value' => int, 'formatted' => string]
     */
    public static function getNext(
        string $sequenceName,
        ?int $year = null,
        ?string $prefix = null,
        array $context = []
    ): array {
        return DB::transaction(function () use ($sequenceName, $year, $prefix, $context) {
            $sequence = self::where('sequence_name', $sequenceName)
                ->where(function ($query) use ($year) {
                    if ($year !== null) {
                        $query->where('year', $year);
                    } else {
                        $query->whereNull('year');
                    }
                })
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = self::create([
                    'sequence_name' => $sequenceName,
                    'prefix' => $prefix,
                    'current_value' => 0,
                    'year' => $year,
                ]);
            }

            $oldValue = $sequence->current_value;

            $nextValue = $sequence->current_value + 1;
            $sequence->current_value = $nextValue;

            if (Auth::check()) {
                $sequence->updated_by = Auth::user()->user_name ?? Auth::user()->name;
            }

            $sequence->save();

            $formatted = ($sequence->prefix ?? $prefix ?? '') .
                str_pad($nextValue, 6, '0', STR_PAD_LEFT);

            if ($year !== null) {
                $formatted .= $year;
            }

            self::logHistory(
                $sequence,
                $oldValue,
                $nextValue,
                $formatted,
                'increment',
                $context
            );

            return [
                'value' => $nextValue,
                'formatted' => $formatted,
            ];
        });
    }

    /**
     * Reset sequence for a new year
     *
     * @param string $sequenceName
     * @param int $newYear
     * @param string|null $notes Optional notes about the reset
     * @return void
     */
    public static function resetForNewYear(string $sequenceName, int $newYear, ?string $notes = null): void
    {
        DB::transaction(function () use ($sequenceName, $newYear, $notes) {
            // Check if sequence already exists for new year
            $exists = self::where('sequence_name', $sequenceName)
                ->where('year', $newYear)
                ->exists();

            if (!$exists) {
                // Get the prefix from current year's sequence
                $currentSequence = self::where('sequence_name', $sequenceName)
                    ->whereNotNull('year')
                    ->orderBy('year', 'desc')
                    ->first();

                $newSequence = self::create([
                    'sequence_name' => $sequenceName,
                    'prefix' => $currentSequence?->prefix,
                    'current_value' => 0,
                    'year' => $newYear,
                    'notes' => $notes ?? "Reset for year {$newYear}",
                ]);

                self::logHistory(
                    $newSequence,
                    null,
                    0,
                    null,
                    'reset',
                    ['reason' => 'new_year_initialization']
                );
            }
        });
    }

    /**
     * Get current value without incrementing
     *
     * @param string $sequenceName
     * @param int|null $year
     * @return int
     */
    public static function getCurrentValue(string $sequenceName, ?int $year = null): int
    {
        $sequence = self::where('sequence_name', $sequenceName)
            ->where(function ($query) use ($year) {
                if ($year !== null) {
                    $query->where('year', $year);
                } else {
                    $query->whereNull('year');
                }
            })
            ->first();

        return $sequence?->current_value ?? 0;
    }

    /**
     * Manually set sequence value (use with caution!)
     *
     * @param string $sequenceName
     * @param int $value
     * @param int|null $year
     * @param string $reason Reason for manual override
     * @return void
     */
    public static function setCurrentValue(
        string $sequenceName,
        int $value,
        ?int $year = null,
        string $reason = 'Manual override'
    ): void {
        DB::transaction(function () use ($sequenceName, $value, $year, $reason) {
            $sequence = self::where('sequence_name', $sequenceName)
                ->where(function ($query) use ($year) {
                    if ($year !== null) {
                        $query->where('year', $year);
                    } else {
                        $query->whereNull('year');
                    }
                })
                ->lockForUpdate()
                ->firstOrFail();

            $oldValue = $sequence->current_value;
            $sequence->current_value = $value;
            $sequence->notes = $reason;

            if (Auth::check()) {
                $sequence->updated_by = Auth::user()->user_name ?? Auth::user()->name;
            }

            $sequence->save();

            self::logHistory(
                $sequence,
                $oldValue,
                $value,
                null,
                'manual_set',
                ['reason' => $reason]
            );
        });
    }

    /**
     * Log sequence change to history table
     *
     * @param Sequence $sequence
     * @param int|null $oldValue
     * @param int $newValue
     * @param string|null $formatted
     * @param string $action
     * @param array $context
     * @return void
     */
    private static function logHistory(
        Sequence $sequence,
        ?int $oldValue,
        int $newValue,
        ?string $formatted,
        string $action,
        array $context = []
    ): void {
        try {
            // DB::table('sequence_history')->insert([
            //     'sequence_id' => $sequence->id,
            //     'sequence_name' => $sequence->sequence_name,
            //     'old_value' => $oldValue,
            //     'new_value' => $newValue,
            //     'formatted_number' => $formatted,
            //     'year' => $sequence->year,
            //     'action' => $action,
            //     'triggered_by' => Auth::check()
            //         ? (Auth::user()->user_name ?? Auth::user()->name)
            //         : 'system',
            //     'ip_address' => request()?->ip(),
            //     'user_agent' => request()?->userAgent(),
            //     'context' => json_encode($context),
            //     'created_at' => now(),
            // ]);
        } catch (\Exception $e) {
            logger()->error('Failed to log sequence history', [
                'sequence' => $sequence->sequence_name,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get history for this sequence
     */
    public function history()
    {
        return DB::table('sequence_history')
            ->where('sequence_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent history across all sequences
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public static function getRecentHistory(int $limit = 100)
    {
        return DB::table('sequence_history')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get history for a specific sequence name
     *
     * @param string $sequenceName
     * @param int|null $year
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public static function getHistoryByName(string $sequenceName, ?int $year = null, int $limit = 100)
    {
        return DB::table('sequence_history')
            ->where('sequence_name', $sequenceName)
            ->when($year, fn($q) => $q->where('year', $year))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
