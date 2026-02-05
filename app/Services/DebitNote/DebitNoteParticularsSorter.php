<?php

namespace App\Services\DebitNote;

use Illuminate\Support\Facades\DB;

class DebitNoteParticularsSorter
{

    public function arrangeParticulars(array $lineItems): array
    {
        $groupedByClass = collect($lineItems)->groupBy('class_code');

        $itemOrder = [
            'IT01' => 1, // Gross Premium
            'IT02' => 2, // Claims
            'IT03' => 3, // Commission
            'IT05' => 4, // Premium Tax
        ];

        $arranged = $groupedByClass->map(function ($classItems) use ($itemOrder) {
            return $classItems->sortBy(function ($item) use ($itemOrder) {
                return $itemOrder[$item['item_code']] ?? 999;
            })->values();
        });

        $sortedParticulars = $arranged->sortBy(function ($items) {
            return $items->first()['line_no'];
        })->flatten(1)->values()->all();

        return $sortedParticulars;
    }

    public function arrangeParticularsByFirstOccurrence(array $lineItems): array
    {
        $classFirstOccurrence = collect($lineItems)
            ->groupBy('class_code')
            ->map(function ($items) {
                return $items->min('line_no');
            });

        $groupedByClass = collect($lineItems)->groupBy('class_code');

        $itemOrder = [
            'IT01' => 1, // Gross Premium
            'IT02' => 2, // Claims
            'IT03' => 3, // Commission
            'IT05' => 4, // Premium Tax
        ];

        $arranged = $groupedByClass->map(function ($classItems) use ($itemOrder) {
            return $classItems->sortBy(function ($item) use ($itemOrder) {
                return $itemOrder[$item['item_code']] ?? 999;
            })->values();
        });

        $sortedParticulars = $arranged->sortBy(function ($items, $classCode) use ($classFirstOccurrence) {
            return $classFirstOccurrence[$classCode] ?? 999;
        })->flatten(1)->values()->all();

        return $sortedParticulars;
    }

    public function arrangeAndRenumber(array $lineItems): array
    {
        $arranged = $this->arrangeParticulars($lineItems);

        return collect($arranged)->map(function ($item, $index) {
            $item['line_no'] = $index + 1;
            return $item;
        })->all();
    }

    public function groupByClassForDisplay(array $lineItems): array
    {
        $arranged = $this->arrangeParticulars($lineItems);

        return collect($arranged)->groupBy('class_code')->map(function ($items, $classCode) {
            $firstItem = $items->first();

            return [
                'class_code' => $classCode,
                'class_group_code' => $firstItem['class_group_code'] ?? null,
                'class_description' => $this->extractClassDescription($items->all()),
                'items' => $items->all(),
                'total_debit' => $items->where('ledger', 'DR')->sum('amount'),
                'total_credit' => $items->where('ledger', 'CR')->sum('amount'),
            ];
        })->values()->all();
    }

    private function extractClassDescription(array $items): string
    {
        $firstItem = $items[0] ?? null;

        if (!$firstItem) {
            return 'Unknown Class';
        }

        $class = DB::table('classnames')
            ->where('class_code', $firstItem['class_code'])
            ->first();

        if ($class) {
            return $class->description ?? $class->name ?? 'Class ' . $firstItem['class_code'];
        }

        return 'Class ' . $firstItem['class_code'];
    }

    public function calculateBalance(array $lineItems): float
    {
        $arranged = $this->arrangeParticulars($lineItems);

        $totalDebit = collect($arranged)->where('ledger', 'DR')->sum('amount');
        $totalCredit = collect($arranged)->where('ledger', 'CR')->sum('amount');

        return $totalDebit - $totalCredit;
    }
}
