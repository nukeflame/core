<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class IncomeStatementExport implements FromArray, WithHeadings, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell
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

class ExpenseStatementExport implements FromArray, WithHeadings, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell
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

class SummaryStatementExport implements FromArray, WithHeadings, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell
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
        $sheet->setCellValue('A1', '3. Budget Summary');
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


class BudgetStatementExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new SummaryStatementExport(),
            new IncomeStatementExport(),
            new ExpenseStatementExport(),
        ];
    }
}
