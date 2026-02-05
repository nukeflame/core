<?php

namespace App\Services\DebitNote;

use App\Models\DebitNote;
use App\Models\DebitNoteItem;

class LineItemProcessor
{
    protected $sorter;

    public function __construct(DebitNoteParticularsSorter $sorter)
    {
        $this->sorter = $sorter;
    }

    private const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];

    public function createLineItems(DebitNote $debitNote, $items): void
    {
        $lineNo = 1;
        $insertData = [];

        foreach ($items['cedant']['items'] ?? [] as $item) {
            $itemData = $this->prepareLineItem($debitNote, $item, $lineNo);

            if ($itemData) {
                $insertData[] = $itemData;
                $lineNo++;
            }
        }

        if (! empty($insertData)) {
            $arrangedParticulars = $this->sorter->arrangeParticulars($insertData);

            DebitNoteItem::insert($arrangedParticulars);
        }
    }

    public function replaceLineItems(DebitNote $debitNote, array $items): void
    {
        $debitNote->items()->delete();

        $this->createLineItems($debitNote, $items);
    }

    public function updateLineItems(DebitNote $debitNote, array $items): void
    {
        foreach ($items as $item) {
            if (isset($item['id'])) {
                DebitNoteItem::where('id', $item['id'])
                    ->where('debit_note_id', $debitNote->id)
                    ->update($this->prepareUpdateData($item));
            } else {
                $lineNo = $debitNote->items()->max('line_no') + 1;
                $itemData = $this->prepareLineItem($debitNote, $item, $lineNo);

                if ($itemData) {
                    DebitNoteItem::create($itemData);
                }
            }
        }
    }

    protected function prepareLineItem(DebitNote $debitNote, array $item, int $lineNo): ?array
    {
        $amount = (float) ($item['amount'] ?? 0);

        if ($amount <= 0 && empty($item['description']) && empty($item['item_code'])) {
            return null;
        }

        $itemCode = $item['item_code'] ?? $item['description'] ?? null;
        $itemNo = $this->generateItemNumber($lineNo);

        return [
            'debit_note_id' => $debitNote->id,
            'line_no' => $lineNo,
            'item_code' => $itemCode,
            'item_no' => $itemNo,
            'status' => DebitNote::STATUS_POSTED,
            'description' => $item['description'] ?? '',
            'class_group_code' => $item['class_group_code'] ?? null,
            'class_code' => $item['class_code'] ?? null,
            'line_rate' => $item['line_rate'] ?? 0,
            'ledger' => $item['ledger'],
            'amount' => $amount,
            'commission' => $item['commission'] ?? 0,
            'premium_tax' => $item['premium_tax'] ?? 0,
            'net_amount' =>  $amount,
            'original_amount' => $item['original_amount'] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

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

    protected function determineLedger(?string $itemCode): string
    {
        if (empty($itemCode)) {
            return 'CR';
        }

        return in_array($itemCode, self::DEBIT_CODES) ? 'DR' : 'CR';
    }

    protected function generateItemNumber(int $lineNo): string
    {
        return 'ITM-' . date('Y') . '-' . str_pad($lineNo, 4, '0', STR_PAD_LEFT);
    }

    public function validateItems(array $items): array
    {
        $errors = [];

        foreach ($items as $index => $item) {
            $itemErrors = [];

            if (! isset($item['amount']) || ! is_numeric($item['amount'])) {
                $itemErrors[] = 'Amount is required and must be numeric';
            }

            if (empty($item['description']) && empty($item['item_code'])) {
                $itemErrors[] = 'Description or item code is required';
            }

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
