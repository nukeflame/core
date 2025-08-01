<?php

namespace App\Exports\Reports\Production\DebitType;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class NotesSheet implements FromArray, WithTitle, WithEvents
{
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Notes';
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return [
            ['Notes'],
            [],
            ['1', 'xxxxxx'],
            ['2', 'xxxxxx'],
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Style header
                $event->sheet->getStyle('A1')->getFont()->setBold(true);
                $event->sheet->getStyle('A1')->getFont()->setSize(12);

                // Style note numbers
                $event->sheet->getStyle('A3:A4')->getFont()->setBold(true);

                // Auto-size columns
                $event->sheet->getColumnDimension('A')->setWidth(5);
                $event->sheet->getColumnDimension('B')->setWidth(50);

                // Set row height
                for ($i = 1; $i <= 4; $i++) {
                    $event->sheet->getRowDimension($i)->setRowHeight(18);
                }
            },
        ];
    }
}
