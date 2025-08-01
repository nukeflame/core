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


class ExpenseBudgetStatementExport implements FromArray, WithHeadings, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell
{
    public function array(): array
    {
        return [
            ['Staff Costs', 'Salaries and Wages', 45000000, false],
            ['Staff Costs', 'Training and Development', 3500000, false],
            ['Staff Costs', 'Medical Insurance', 5200000, false],
            ['Staff Costs', 'Total - Staff Costs', 53700000, true],
            ['Operational Expenses', 'Rent and Utilities', 12000000, false],
            ['Operational Expenses', 'Office Supplies', 2500000, false],
            ['Operational Expenses', 'IT Infrastructure', 8750000, false],
            ['Operational Expenses', 'Total - Operational Expenses', 23250000, true],
            ['Business Development', 'Marketing and Advertising', 10500000, false],
            ['Business Development', 'Client Acquisition', 7800000, false],
            ['Business Development', 'Total - Business Development', 18300000, true],
            ['Claims and Settlements', 'Claim Payments', 8000000, false],
            ['Claims and Settlements', 'Legal Fees', 3581290, false],
            ['Claims and Settlements', 'Total - Claims and Settlements', 11581290, true],
            ['Total', 'Total Budgeted Expenses', 106831290, true],
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
        return 'Expense Statement';
    }

    public function styles(Worksheet $sheet)
    {
        // Add title
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Budget Allocation - Expense Statement');
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
