<?php

namespace Nukeflame\Core\Services;

use App\Models\Sequence;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SequenceService
{
    public const TRANS_TYPE_NEW = 'NEW';
    public const TRANS_TYPE_REN = 'REN';
    public const TRANS_TYPE_RFN = 'RFN';
    public const TRANS_TYPE_CNC = 'CNC';

    public const BUSINESS_TYPE_FAC = 'FAC';
    public const BUSINESS_TYPE_TREATY = 'TREATY';
    public const BUSINESS_TYPE_RETRO = 'RETRO';

    private const SEQ_COVER = 'COVER';
    private const SEQ_ENDORSEMENT = 'ENDORSEMENT';
    private const SEQ_DOCUMENT = 'DOCUMENT_GLOBAL';
    private const SEQ_DEBIT_NOTE = 'DN';
    private const SEQ_CREDIT_NOTE = 'CN';

    private const VALID_TRANS_TYPES = [
        self::TRANS_TYPE_NEW,
        self::TRANS_TYPE_REN,
        self::TRANS_TYPE_RFN,
        self::TRANS_TYPE_CNC,
    ];

    private const VALID_BUSINESS_TYPES = [
        self::BUSINESS_TYPE_FAC,
        self::BUSINESS_TYPE_TREATY,
        self::BUSINESS_TYPE_RETRO,
    ];

    private const FORMATS = [
        'cover' => 'C%06d%d',              // C0000012025
        'endorsement' => 'C%s%06d%d',      // CNEW0000012025
        'document' => 'EN%06d',            // EN000306
        'debit_note' => 'DN/%s/%d/%06d',   // DN/FAC/2025/000001
        'credit_note' => 'CN/%s/%d/%06d',  // CN/FAC/2025/000001
    ];

    /**
     * Generate a unique cover number
     * Format: C{SERIAL}{YEAR} e.g., C0000012025
     */
    public function generateCoverNumber(?int $year = null): object
    {
        $year = $year ?? now()->year;

        $result = $this->generateSequence(
            sequenceName: $this->buildSequenceName(self::SEQ_COVER, $year),
            year: $year,
            prefix: 'C',
            formatter: fn($value) => sprintf(self::FORMATS['cover'], $value, $year),
            context: ['type' => 'cover']
        );

        return (object) [
            'cover_no' => $result->formatted,
            'serial_no' => $result->serial_no,
        ];
    }

    /**
     * Generate a unique endorsement number
     * Format: C{TRANS_TYPE}{SERIAL}{YEAR} e.g., CNEW0000012025
     */
    public function generateEndorsementNumber(string $transType, ?int $year = null): object
    {
        $this->validateInSet($transType, self::VALID_TRANS_TYPES, 'transaction type');

        $year = $year ?? now()->year;

        $result = $this->generateSequence(
            sequenceName: $this->buildSequenceName(self::SEQ_ENDORSEMENT, $year),
            year: $year,
            prefix: 'C' . $transType,
            formatter: fn($value) => sprintf(self::FORMATS['endorsement'], $transType, $value, $year),
            context: ['type' => 'endorsement', 'trans_type' => $transType]
        );

        return (object) [
            'endorsement_no' => $result->formatted,
            'serial_no' => $result->serial_no,
        ];
    }

    /**
     * Generate document number for endorsement narrations
     * Format: REF{SERIAL} e.g., REF000306
     */
    public function generateDocumentNumber(): object
    {
        $result = $this->generateSequence(
            sequenceName: self::SEQ_DOCUMENT,
            year: null,
            prefix: 'REF',
            formatter: fn($value) => sprintf(self::FORMATS['document'], $value),
            context: ['type' => 'document']
        );

        return (object) [
            'doc_no' => $result->formatted,
            'serial_no' => $result->serial_no,
        ];
    }

    /**
     * Generate unique debit note number
     * Format: DN/{TYPE}/{YEAR}/{SEQUENCE} e.g., DN/TREATY/2025/000001
     */
    public function generateDebitNoteNumber(string $businessType, ?int $year = null): object
    {
        $this->validateInSet($businessType, self::VALID_BUSINESS_TYPES, 'business type');

        $year = $year ?? now()->year;
        $type = 'R';
        if ($businessType == self::BUSINESS_TYPE_TREATY) {
            $type = 'T';
        } elseif ($businessType == self::BUSINESS_TYPE_TREATY) {
            $type = 'F';
        }

        $result = $this->generateSequence(
            sequenceName: $this->buildSequenceName(self::SEQ_DEBIT_NOTE, $year, $businessType),
            year: $year,
            prefix: 'DN',
            formatter: fn($value) => sprintf(self::FORMATS['debit_note'], $type, $year, $value),
            context: ['type' => 'debit_note', 'business_type' => $businessType]
        );

        return (object) [
            'debit_no' => $result->formatted,
            'serial_no' => $result->serial_no,
        ];
    }

    /**
     * Generate unique credit note number
     * Format: CN/{TYPE}/{YEAR}/{SEQUENCE} e.g., CN/TREATY/2025/000001
     */
    public function generateCreditNoteNumber(string $businessType, ?int $year = null): object
    {
        $this->validateInSet($businessType, self::VALID_BUSINESS_TYPES, 'business type');

        $year = $year ?? now()->year;
        $type = 'R';
        if ($businessType == self::BUSINESS_TYPE_TREATY) {
            $type = 'T';
        } elseif ($businessType == self::BUSINESS_TYPE_TREATY) {
            $type = 'F';
        }

        $result = $this->generateSequence(
            sequenceName: $this->buildSequenceName(self::SEQ_CREDIT_NOTE, $year, $businessType),
            year: $year,
            prefix: 'CN',
            formatter: fn($value) => sprintf(self::FORMATS['credit_note'], $type, $year, $value),
            context: ['type' => 'credit_note', 'business_type' => $businessType]
        );

        return (object) [
            'credit_no' => $result->formatted,
            'serial_no' => $result->serial_no,
        ];
    }

    /**
     * Parse a cover number into its components
     * Input: C0000012025
     */
    public function parseCoverNumber(string $coverNo): array
    {
        return [
            'prefix' => substr($coverNo, 0, 1),
            'serial' => (int) substr($coverNo, 1, 6),
            'year' => (int) substr($coverNo, 7, 4),
        ];
    }

    /**
     * Parse an endorsement number into its components
     * Input: CNEW0000012025
     */
    public function parseEndorsementNumber(string $endorsementNo): array
    {
        return [
            'prefix' => substr($endorsementNo, 0, 1),
            'trans_type' => substr($endorsementNo, 1, 3),
            'serial' => (int) substr($endorsementNo, 4, 6),
            'year' => (int) substr($endorsementNo, 10, 4),
        ];
    }

    /**
     * Parse a debit/credit note number into its components
     * Input: DN/TREATY/2025/000001 or CN/TREATY/2025/000001
     */
    public function parseNoteNumber(string $noteNo): array
    {
        $parts = explode('/', $noteNo);

        return [
            'prefix' => $parts[0] ?? null,
            'type' => $parts[1] ?? null,
            'year' => (int) ($parts[2] ?? now()->year),
            'serial' => (int) ($parts[3] ?? 0),
        ];
    }

    /**
     * Initialize sequences for a new year
     */
    public function initializeNewYearSequences(?int $year = null): array
    {
        $newYear = $year ?? (now()->year + 1);

        return DB::transaction(function () use ($newYear) {
            $created = [];

            // Cover sequence
            $coverName = $this->buildSequenceName(self::SEQ_COVER, $newYear);
            if ($this->createSequenceIfNotExists($coverName, $newYear, 'C')) {
                $created[] = $coverName;
            }

            // Endorsement sequence
            $endorsementName = $this->buildSequenceName(self::SEQ_ENDORSEMENT, $newYear);
            if ($this->createSequenceIfNotExists($endorsementName, $newYear, 'C')) {
                $created[] = $endorsementName;
            }

            // Debit/Credit notes per business type
            foreach (self::VALID_BUSINESS_TYPES as $type) {
                $dnName = $this->buildSequenceName(self::SEQ_DEBIT_NOTE, $newYear, $type);
                if ($this->createSequenceIfNotExists($dnName, $newYear, 'DN')) {
                    $created[] = $dnName;
                }

                $cnName = $this->buildSequenceName(self::SEQ_CREDIT_NOTE, $newYear, $type);
                if ($this->createSequenceIfNotExists($cnName, $newYear, 'CN')) {
                    $created[] = $cnName;
                }
            }

            return $created;
        });
    }

    /**
     * Get current sequence status for monitoring/debugging
     */
    public function getSequenceStatus(?int $year = null): array
    {
        $year = $year ?? now()->year;

        $status = [
            'cover' => $this->getSequenceInfo(
                $this->buildSequenceName(self::SEQ_COVER, $year),
                $year
            ),
            'endorsement' => $this->getSequenceInfo(
                $this->buildSequenceName(self::SEQ_ENDORSEMENT, $year),
                $year
            ),
            'document' => $this->getSequenceInfo(self::SEQ_DOCUMENT, null),
        ];

        foreach (self::VALID_BUSINESS_TYPES as $type) {
            $status["debit_note_{$type}"] = $this->getSequenceInfo(
                $this->buildSequenceName(self::SEQ_DEBIT_NOTE, $year, $type),
                $year
            );
            $status["credit_note_{$type}"] = $this->getSequenceInfo(
                $this->buildSequenceName(self::SEQ_CREDIT_NOTE, $year, $type),
                $year
            );
        }

        return $status;
    }

    /**
     * Get valid transaction types
     */
    public static function getValidTransTypes(): array
    {
        return self::VALID_TRANS_TYPES;
    }

    /**
     * Get valid business types
     */
    public static function getValidBusinessTypes(): array
    {
        return self::VALID_BUSINESS_TYPES;
    }

    /**
     * Core sequence generation using the Sequence model
     */
    private function generateSequence(
        string $sequenceName,
        ?int $year,
        string $prefix,
        callable $formatter,
        array $context = []
    ): object {
        $result = Sequence::getNext(
            sequenceName: $sequenceName,
            year: $year,
            prefix: $prefix,
            context: $context
        );

        return (object) [
            'serial_no' => $result['value'],
            'formatted' => $formatter($result['value']),
        ];
    }

    /**
     * Build a sequence name (aligned with Sequence model's sequence_name field)
     */
    private function buildSequenceName(string $type, ?int $year = null, ?string $subType = null): string
    {
        $parts = [$type];

        if ($subType !== null) {
            $parts[] = $subType;
        }

        if ($year !== null) {
            $parts[] = $year;
        }

        return implode('_', $parts);
    }

    /**
     * Create a sequence if it doesn't exist
     */
    private function createSequenceIfNotExists(string $sequenceName, ?int $year, string $prefix): bool
    {
        $exists = Sequence::where('sequence_name', $sequenceName)
            ->where(function ($query) use ($year) {
                if ($year !== null) {
                    $query->where('year', $year);
                } else {
                    $query->whereNull('year');
                }
            })
            ->exists();

        if (!$exists) {
            Sequence::create([
                'sequence_name' => $sequenceName,
                'prefix' => $prefix,
                'current_value' => 0,
                'year' => $year,
                'notes' => "Initialized for year {$year}",
            ]);
            return true;
        }

        return false;
    }

    /**
     * Validate a value exists in a set
     */
    private function validateInSet(string $value, array $validSet, string $fieldName): void
    {
        if (!in_array($value, $validSet, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid %s: "%s". Valid options: %s',
                    $fieldName,
                    $value,
                    implode(', ', $validSet)
                )
            );
        }
    }

    /**
     * Get sequence info for status reporting
     */
    private function getSequenceInfo(string $sequenceName, ?int $year): array
    {
        $currentValue = Sequence::getCurrentValue($sequenceName, $year);

        $sequence = Sequence::where('sequence_name', $sequenceName)
            ->where(function ($query) use ($year) {
                if ($year !== null) {
                    $query->where('year', $year);
                } else {
                    $query->whereNull('year');
                }
            })
            ->first();

        return [
            'sequence_name' => $sequenceName,
            'current_value' => $currentValue,
            'year' => $year ?? 'global',
            'exists' => $sequence !== null,
            'prefix' => $sequence?->prefix,
            'last_updated' => $sequence?->updated_at?->toDateTimeString(),
            'updated_by' => $sequence?->updated_by,
        ];
    }
}
