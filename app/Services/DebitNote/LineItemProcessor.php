<?php

namespace App\Services\DebitNote;

use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;

class LineItemProcessor
{
    protected $sorter;

    public function __construct(DebitNoteParticularsSorter $sorter)
    {
        $this->sorter = $sorter;
    }

    private const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];

    public function createDebitNoteLineItems(DebitNote $debitNote, array $items): void
    {
        $lineNo = 1;
        $insertData = [];

        foreach ($items['cedant']['items'] ?? [] as $item) {
            $itemData = $this->prepareDebitNoteLineItem($debitNote, $item, $lineNo);

            if ($itemData) {
                $insertData[] = $itemData;
                $lineNo++;
            }
        }

        if (!empty($insertData)) {
            $arrangedParticulars = $this->sorter->arrangeParticulars($insertData);
            DebitNoteItem::insert($arrangedParticulars);
        }
    }

    public function replaceDebitNoteLineItems(DebitNote $debitNote, array $items): void
    {
        $debitNote->items()->delete();
        $this->createDebitNoteLineItems($debitNote, $items);
    }

    public function updateDebitNoteLineItems(DebitNote $debitNote, array $items): void
    {
        foreach ($items as $item) {
            if (isset($item['id'])) {
                DebitNoteItem::where('id', $item['id'])
                    ->where('debit_note_id', $debitNote->id)
                    ->update($this->prepareUpdateData($item));
            } else {
                $lineNo = $debitNote->items()->max('line_no') + 1;
                $itemData = $this->prepareDebitNoteLineItem($debitNote, $item, $lineNo);

                if ($itemData) {
                    DebitNoteItem::create($itemData);
                }
            }
        }
    }

    protected function prepareDebitNoteLineItem(DebitNote $debitNote, array $item, int $lineNo): ?array
    {
        $amount = (float) ($item['amount'] ?? 0);

        if ($amount <= 0 && empty($item['description']) && empty($item['item_code'])) {
            return null;
        }

        $itemCode = $item['item_code'] ?? $item['description'] ?? null;
        $itemNo = $this->generateDebitNoteItemNumber($lineNo);

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
            'ledger' => $item['ledger'] ?? $this->determineLedger($itemCode),
            'amount' => $amount,
            'commission' => $item['commission'] ?? 0,
            'premium_tax' => $item['premium_tax'] ?? 0,
            'net_amount' => $amount,
            'original_amount' => $item['original_amount'] ?? 0,
            'original_line_rate' => $item['original_line_rate'] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function generateDebitNoteItemNumber(int $lineNo): string
    {
        return 'DN-ITM-' . date('Y') . '-' . str_pad($lineNo, 4, '0', STR_PAD_LEFT);
    }


    public function createCreditNoteLineItems(CreditNote $creditNote, array $items): void
    {
        $lineNo = 1;
        $insertData = [];

        foreach ($items as $item) {
            $itemData = $this->prepareCreditNoteLineItem($creditNote, $item, $lineNo);

            if ($itemData) {
                $insertData[] = $itemData;
                $lineNo++;
            }
        }

        if (!empty($insertData)) {
            $arrangedParticulars = $this->sorter->arrangeParticulars($insertData);
            CreditNoteItem::insert($arrangedParticulars);
        }
    }

    public function replaceCreditNoteLineItems(CreditNote $creditNote, array $items): void
    {
        $creditNote->items()->delete();

        $lineNo = 1;
        $insertData = [];

        foreach ($items as $item) {
            $itemData = $this->prepareCreditNoteLineItem($creditNote, $item, $lineNo);

            if ($itemData) {
                $insertData[] = $itemData;
                $lineNo++;
            }
        }

        if (!empty($insertData)) {
            CreditNoteItem::insert($insertData);
        }
    }

    protected function prepareCreditNoteLineItem(CreditNote $creditNote, array $item, int $lineNo): ?array
    {
        $amount = (float) ($item['amount'] ?? 0);

        if ($amount <= 0 && empty($item['description']) && empty($item['item_code'])) {
            return null;
        }

        $itemCode = $item['item_code'] ?? null;
        $ledger = $item['ledger'] ?? $this->determineLedger($itemCode);
        $itemNo = $this->generateCreditNoteItemNumber($lineNo);
        $netAmount = $item['net_amount'] ?? $amount;

        return [
            'credit_note_id' => $creditNote->id,
            'line_no' => $lineNo,
            'item_code' => $itemCode,
            'item_no' => $itemNo,
            'status' => CreditNote::STATUS_DRAFT,
            'description' => $item['description'] ?? $itemCode ?? '',
            'class_group_code' => $item['class_group_code'] ?? $item['class_group'] ?? null,
            'class_code' => $item['class_code'] ?? $item['class_name'] ?? null,
            'line_rate' => $item['line_rate'] ?? 0,
            'ledger' => $ledger,
            'amount' => $amount,
            'commission' => $item['commission'] ?? 0,
            'brokerage' => $item['brokerage'] ?? 0,
            'premium_tax' => $item['premium_tax'] ?? 0,
            'reinsurance_tax' => $item['reinsurance_tax'] ?? 0,
            'withholding_tax' => $item['withholding_tax'] ?? 0,
            'original_amount' => $item['original_amount'] ?? 0,
            'original_line_rate' => $item['original_line_rate'] ?? 0,
            'net_amount' => $netAmount,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function generateCreditNoteItemNumber(int $lineNo): string
    {
        return 'CN-ITM-' . date('Y') . '-' . str_pad($lineNo, 4, '0', STR_PAD_LEFT);
    }

    protected function prepareUpdateData(array $item): array
    {
        $data = [];

        $updatableFields = [
            'description' => 'description',
            'class_group_code' => 'class_group',
            'class_code' => 'class_name',
            'line_rate' => 'line_rate',
            'amount' => 'amount',
            'commission' => 'commission',
            'brokerage' => 'brokerage',
            'premium_tax' => 'premium_tax',
            'net_amount' => 'net_amount',
            'original_line_rate' => 'original_line_rate',
        ];

        foreach ($updatableFields as $dbField => $inputField) {
            if (isset($item[$inputField])) {
                $data[$dbField] = $item[$inputField];
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

    public function validateItems(array $items): array
    {
        $errors = [];

        foreach ($items as $index => $item) {
            $itemErrors = [];

            if (!isset($item['amount']) || !is_numeric($item['amount'])) {
                $itemErrors[] = 'Amount is required and must be numeric';
            }

            if (empty($item['description']) && empty($item['item_code'])) {
                $itemErrors[] = 'Description or item code is required';
            }

            if (isset($item['line_rate']) && (!is_numeric($item['line_rate']) || $item['line_rate'] < 0 || $item['line_rate'] > 100)) {
                $itemErrors[] = 'Line rate must be between 0 and 100';
            }

            if (!empty($itemErrors)) {
                $errors["item_{$index}"] = $itemErrors;
            }
        }

        return $errors;
    }
}
