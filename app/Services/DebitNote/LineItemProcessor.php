<?php

namespace App\Services\DebitNote;

use App\Models\DebitNote;
use App\Models\DebitNoteItem;

/**
 * Handles creation and management of debit note line items
 */
class LineItemProcessor
{
    private const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];

    /**
     * Create line items for a debit note
     */
    public function createLineItems(DebitNote $debitNote, array $items): void
    {
        $lineNo = 1;
        $insertData = [];

        foreach ($items as $item) {
            $itemData = $this->prepareLineItem($debitNote, $item, $lineNo);

            if ($itemData) {
                $insertData[] = $itemData;
                $lineNo++;
            }
        }

        if (! empty($insertData)) {
            DebitNoteItem::insert($insertData);
        }
    }

    /**
     * Replace all line items for a debit note
     */
    public function replaceLineItems(DebitNote $debitNote, array $items): void
    {
        // Delete existing items
        $debitNote->items()->delete();

        // Create new items
        $this->createLineItems($debitNote, $items);
    }

    /**
     * Update specific line items
     */
    public function updateLineItems(DebitNote $debitNote, array $items): void
    {
        foreach ($items as $item) {
            if (isset($item['id'])) {
                // Update existing item
                DebitNoteItem::where('id', $item['id'])
                    ->where('debit_note_id', $debitNote->id)
                    ->update($this->prepareUpdateData($item));
            } else {
                // Create new item
                $lineNo = $debitNote->items()->max('line_no') + 1;
                $itemData = $this->prepareLineItem($debitNote, $item, $lineNo);

                if ($itemData) {
                    DebitNoteItem::create($itemData);
                }
            }
        }
    }

    /**
     * Prepare line item data for insertion
     */
    protected function prepareLineItem(DebitNote $debitNote, array $item, int $lineNo): ?array
    {
        $amount = (float) ($item['amount'] ?? 0);

        // Skip empty items
        if ($amount <= 0 && empty($item['description']) && empty($item['item_code'])) {
            return null;
        }

        $itemCode = $item['item_code'] ?? $item['description'] ?? null;
        $ledger = $item['ledger'] ?? $this->determineLedger($itemCode);
        $itemNo = $this->generateItemNumber($lineNo);

        return [
            'debit_note_id' => $debitNote->id,
            'line_no' => $lineNo,
            'item_code' => $itemCode,
            'item_no' => $itemNo,
            'status' => DebitNote::STATUS_POSTED,
            'description' => $item['description'] ?? '',
            'class_group_code' => $item['class_group'] ?? null,
            'class_code' => $item['class_name'] ?? null,
            'line_rate' => $item['line_rate'] ?? 0,
            'ledger' => $ledger,
            'amount' => $amount,
            'commission' => $item['commission'] ?? 0,
            'premium_tax' => $item['premium_tax'] ?? 0,
            'net_amount' => $item['net_amount'] ?? $amount,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Prepare data for update
     */
    protected function prepareUpdateData(array $item): array
    {
        $data = [];

        $updatableFields = [
            'description',
            'class_group_code' => 'class_group',
            'class_code' => 'class_name',
            'line_rate',
            'amount',
            'commission',
            'premium_tax',
            'net_amount',
        ];

        foreach ($updatableFields as $dbField => $inputField) {
            $field = is_numeric($dbField) ? $inputField : $dbField;
            $input = is_numeric($dbField) ? $inputField : $inputField;

            if (isset($item[$input])) {
                $data[$field] = $item[$input];
            }
        }

        $data['updated_at'] = now();

        return $data;
    }

    /**
     * Determine ledger type (DR or CR)
     */
    protected function determineLedger(?string $itemCode): string
    {
        if (empty($itemCode)) {
            return 'CR';
        }

        return in_array($itemCode, self::DEBIT_CODES) ? 'DR' : 'CR';
    }

    /**
     * Generate unique item number
     */
    protected function generateItemNumber(int $lineNo): string
    {
        return 'ITM-'.date('Y').'-'.str_pad($lineNo, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validate line items
     */
    public function validateItems(array $items): array
    {
        $errors = [];

        foreach ($items as $index => $item) {
            $itemErrors = [];

            // Validate amount
            if (! isset($item['amount']) || ! is_numeric($item['amount'])) {
                $itemErrors[] = 'Amount is required and must be numeric';
            }

            // Validate description or item code
            if (empty($item['description']) && empty($item['item_code'])) {
                $itemErrors[] = 'Description or item code is required';
            }

            // Validate line rate if provided
            if (isset($item['line_rate']) && (! is_numeric($item['line_rate']) || $item['line_rate'] < 0 || $item['line_rate'] > 100)) {
                $itemErrors[] = 'Line rate must be between 0 and 100';
            }

            if (! empty($itemErrors)) {
                $errors["item_{$index}"] = $itemErrors;
            }
        }

        return $errors;
    }
}
