<?php

namespace App\Services;

use App\Models\Sequence;
use Carbon\Carbon;

class SequenceService
{
    private int $year;
    private int $month;
    private int $quarter;

    public function __construct()
    {
        $now = Carbon::now();
        $this->year = $now->year;
        $this->month = $now->month;
        $this->quarter = $now->quarter;
    }

    /**
     * Generate a unique cover number
     * Format: C{SERIAL}{YEAR} e.g., C0000012025
     *
     * @return object {serial_no: int, cover_no: string}
     */
    public function generateCoverNumber(): object
    {
        $result = Sequence::getNext('cover_number', $this->year, 'C');

        return (object) [
            'serial_no' => $result['value'],
            'cover_no' => $result['formatted'],
        ];
    }

    /**
     * Generate a unique endorsement number
     * Format: C{TRANS_TYPE}{SERIAL}{YEAR} e.g., CNEW0000012025
     *
     * @param string $transType Transaction type (NEW, RFN, CNC)
     * @return object {serial_no: int, endorsement_no: string}
     */
    public function generateEndorsementNumber(string $transType): object
    {
        $result = Sequence::getNext('endorsement_number', $this->year);

        $endorsementNo = 'C' . $transType .
            str_pad($result['value'], 6, '0', STR_PAD_LEFT) .
            $this->year;

        return (object) [
            'serial_no' => $result['value'],
            'endorsement_no' => $endorsementNo,
        ];
    }

    /**
     * Generate document number for endorsement narrations
     * Format: EN{SERIAL} e.g., EN000306
     *
     * @return string
     */
    public function generateDocumentNumber(): string
    {
        $result = Sequence::getNext('document_number', null, 'EN');
        return $result['formatted'];
    }

    /**
     * Initialize sequences for a new year
     * Should be called at year-end or start of new year
     *
     * @param int|null $year If null, uses next year
     * @return void
     */
    public function initializeNewYearSequences(?int $year = null): void
    {
        $newYear = $year ?? ($this->year + 1);

        Sequence::resetForNewYear('cover_number', $newYear);
        Sequence::resetForNewYear('endorsement_number', $newYear);
    }

    /**
     * Get current sequence status for monitoring/debugging
     *
     * @return array
     */
    public function getSequenceStatus(): array
    {
        return [
            'cover_number' => [
                'current' => Sequence::getCurrentValue('cover_number', $this->year),
                'year' => $this->year,
            ],
            'endorsement_number' => [
                'current' => Sequence::getCurrentValue('endorsement_number', $this->year),
                'year' => $this->year,
            ],
            'document_number' => [
                'current' => Sequence::getCurrentValue('document_number'),
                'year' => 'global',
            ],
        ];
    }
}
