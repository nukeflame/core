<?php

namespace App\Exports\Budgets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class IncomeBudgetStatementExport implements FromArray, WithHeadings, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell
{
    public function array(): array
    {
        return [
            ['New Business', 'Facultative (Offers & Quotations)', 83782130, false],
            ['New Business', 'Special Lines', 20455994, false],
            ['New Business', 'Treaties', 3359483, false],
            ['New Business', 'International Markets (Total)', 10073220, false],
            ['New Business', 'Total - New Business', 117670827, true],
            ['Renewal Business', 'Facultative', 66340572, false],
            ['Renewal Business', 'Special Lines', 14273565, false],
            ['Renewal Business', 'Treaties', 5119383, false],
            ['Renewal Business', 'Market Expansion (Total)', 5132352, false],
            ['Renewal Business', 'Total - Renewal Business', 90865872, true],
            ['Other Income', 'Interest from Investment', 13000000, false],
            ['Total', 'Total Budgeted Income', 221536698, true],
        ];
    }

    public function headings(): array
    {
        return [
            'Category',
            'Subcategory',
            'Amount (KES)',
            'Is Total?',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function title(): string
    {
        return 'Income Statement';
    }

    public function styles(Worksheet $sheet)
    {
        // Add title
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Budget Allocation - Income Statement');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style headers
        $sheet->getStyle('A2:D2')->getFont()->setBold(true);
        $sheet->getStyle('A2:D2')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD3D3D3');

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style total rows
        $dataRows = $this->array();
        $row = 3; // Start from row 3 (after headers)

        foreach ($dataRows as $dataRow) {
            if ($dataRow[3] === true) { // If it's a total row
                $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEBEBEB');
            }
            $row++;
        }

        // Add borders
        $sheet->getStyle('A2:D' . (count($dataRows) + 2))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return $sheet;
    }
}
