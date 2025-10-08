<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Stage extends Enum
{
    const LEAD        = 'lead';
    const PROPOSAL    = 'proposal';
    const NEGOTIATION = 'negotiation';
    const WON         = 'won';
    const LOST        = 'lost';
    const FINAL_STAGE = 'final_stage';

    private static array $stages = [
        self::LEAD        => '1',
        self::PROPOSAL    => '2',
        self::NEGOTIATION => '3',
        self::WON         => '4',
        self::LOST        => '5',
        self::FINAL_STAGE => '6',
    ];

    public function getStage(): string
    {
        return self::$stages[$this->value];
    }

    public function getKeyValue()
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->getStage(),
            'key' => $this->getKeyV(),
        ];
    }

    public static function fromKeyValue(string $key)
    {
        if (self::hasValue($key)) {
            return new static($key);
        }

        return null;
    }

    public static function getAllStages(): array
    {
        return array_map(function ($value) {
            $stage = new static($value);
            return $stage->toArray();
        }, self::getValues());
    }

    public static function getStageByKey(string $key): ?string
    {
        return self::$stages[$key] ?? null;
    }

    public static function fromStageValue(string $stageValue): ?string
    {
        $key = array_search($stageValue, self::$stages, true);

        if ($key !== false) {
            return $key;
        }

        return null;
    }
}
