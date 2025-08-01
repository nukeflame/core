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

class SummaryBudgetStatementExport implements FromArray, WithHeadings, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell
{
    public function array(): array
    {
        $totalIncome = 221536698.00;
        $totalExpenses = 106831290.00;
        $grossProfit = 437947517.00;
        $costIncomeRatio = 32.79;
        $profitMargin = 67.21;

        return [
            ['Total Income', $totalIncome, false],
            ['Total Expenses', $totalExpenses, false],
            ['Gross Profit', $grossProfit, true],
            ['Cost-Income Ratio', $costIncomeRatio . '%', false],
            ['Profit Margin', $profitMargin . '%', true],
        ];
    }

    public function headings(): array
    {
        return [
            'Item',
            'Amount (KES)',
            'Is Total?',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function title(): string
    {
        return 'Budget Summary';
    }

    public function styles(Worksheet $sheet)
    {
        // Add title
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', 'Budget Allocation - Summary');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0F7EA'); // Light green background for section title

        // Style headers
        $sheet->getStyle('A2:C2')->getFont()->setBold(true);
        $sheet->getStyle('A2:C2')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD3D3D3');

        // Auto-size columns
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style the rows - highlight total rows and gross profit row
        $dataRows = $this->array();
        $row = 3; // Start from row 3 (after headers)

        foreach ($dataRows as $dataRow) {
            if ($dataRow[0] === 'Gross Profit') {
                $sheet->getStyle('A' . $row . ':C' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF5F5F5'); // Light gray for Gross Profit
            }

            if ($dataRow[2] === true) { // If it's a total row
                $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            }

            $row++;
        }

        // Add borders to all cells
        $sheet->getStyle('A2:C' . (count($dataRows) + 2))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Right-align all value cells
        $sheet->getStyle('B3:B' . (count($dataRows) + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return $sheet;
    }
}
