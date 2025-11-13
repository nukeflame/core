<?php

namespace App\Exports\Bd;

use App\Models\Bd\Leads\Pipeline;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PipelineReportExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters)
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return [
            'Pipeline Code',
            'Insured Name',
            'Insured Category',
            'Cedant',
            'Lead Type',
            'Lead Name',
            'Effective Closure Date',
            'Premium',
            'Start Date',
            'End Date',
            'Currency',
            'Sum Insured Type',
            'Total Sum Insured',
            'Total Gross Premium',
            'Cedant Commissions',
            'Reinsurance Commission',
            'Total Brokerage',
            'Reverted To Pipeline',
        ];
    }

    public function map($row): array
    {
        // Cedant
        $customer = DB::table('customers')
            ->where('customer_id', (int) ($row->customer_id ?? 0))
            ->first();
        $cedant = $customer ? $this->firstUpper($customer->name) : 'N/A';

        // Insured Category
        $category = $row->client_category === 'O' ? 'Organic Growth' : ($row->client_category === 'N' ? 'New Business' : 'Standard');

        // Sum Insured Type
        $sum_insured_type = DB::table('type_of_sum_insured')
            ->where('sum_insured_code', $row->sum_insured_type)
            ->first()->sum_insured_name ?? '';

        return [
            $row->opportunity_id ?? 'N/A',
            $this->firstUpper($row->insured_name ?? ''),
            $category,
            $cedant,
            $this->firstUpper($row->client_type ?? ''),
            $this->firstUpper($row->lead_name ?? ''),
            $this->formatDate($row->fac_date_offered ?? ''),
            $this->formatCurrency($row->cede_premium ?? null, $row->currency_code ?? 'USD'),
            $this->formatDate($row->effective_date ?? ''),
            $this->formatDate($row->closing_date ?? ''),
            $row->currency_code ?? 'USD',
            $this->firstUpper($sum_insured_type ?? ''),
            $this->formatCurrency($row->total_sum_insured ?? null, $row->currency_code ?? 'USD'),
            $this->formatCurrency($row->cede_premium ?? null, $row->currency_code ?? 'USD'),
            $this->formatPercentage($row->comm_rate ?? null),
            $this->formatPercentage($row->reins_comm_rate ?? null),
            $this->formatCurrency($row->brokerage_comm_amt ?? null, $row->currency_code ?? 'USD'),
            $this->firstUpper($row->reverted_to_pipeline ?? ''),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $filterCount = count($this->buildFilterLabels());
        $headerRow = 3 + $filterCount; // Dynamic header row based on filter count
        $rowCount = count($this->data) + $headerRow; // Total rows including headers

        // Auto-size columns for better readability
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $styles = [
            // Company name (row 1, left side)
            'A1:Q1' => [
                'font' => [
                    'name' => 'Calibri',
                    'size' => 20,
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '1B4F72'], // Deep blue
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            // Logo area (row 1, right side)
            'R1' => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            // Report title (row 2)
            2 => [
                'font' => [
                    'name' => 'Calibri',
                    'size' => 16,
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '2980B9'], // Professional blue
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            // Column headers (dynamic row)
            $headerRow => [
                'font' => [
                    'name' => 'Calibri',
                    'size' => 12,
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => ['rgb' => '2980B9'], // Professional blue
                    'endColor' => ['rgb' => '3498DB'], // Lighter blue
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '1B4F72'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            // Body styles (data rows)
            "A" . ($headerRow + 1) . ":R{$rowCount}" => [
                'font' => [
                    'name' => 'Calibri',
                    'size' => 11,
                    'color' => ['rgb' => '2C3E50'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'BDC3C7'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
        ];

        // Style filter rows (rows 3 to headerRow-1)
        for ($i = 3; $i < $headerRow; $i++) {
            $styles[$i] = [
                'font' => [
                    'name' => 'Calibri',
                    'size' => 11,
                    'bold' => false,
                    'color' => ['rgb' => '2C3E50'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'F8F9FA'], // Light gray
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'BDC3C7'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ];
        }

        // Apply striped rows and conditional highlights for data
        foreach ($this->data as $index => $row) {
            $rowNum = $index + $headerRow + 1; // Start from after header
            $style = [];

            // Striped rows (alternating colors)
            if ($index % 2 === 0) {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'F8F9FA'], // Very light gray
                ];
            } else {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FFFFFF'], // White
                ];
            }

            // Conditional highlights based on status
            $lead_status = DB::table('lead_status')
                ->where('id', $row->stage ?? 0)
                ->where('category_type', $row->category_type ?? '')
                ->first();
            $status_name = $lead_status ? $lead_status->status_name : '';

            if ($status_name === 'Lost') {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FADBD8'], // Soft red
                ];
                $style['font'] = [
                    'color' => ['rgb' => 'C0392B'],
                ];
            } elseif ($status_name === 'Pending') {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FEF9E7'], // Soft yellow
                ];
                $style['font'] = [
                    'color' => ['rgb' => 'D68910'],
                ];
            } elseif ($status_name === 'Won') {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'D5F4E6'], // Soft green
                ];
                $style['font'] = [
                    'color' => ['rgb' => '27AE60'],
                ];
            }

            if (!empty($style)) {
                $sheet->getStyle("A{$rowNum}:R{$rowNum}")->applyFromArray($style);
            }
        }

        // Special styling for currency and percentage columns
        $currencyColumns = ['H', 'M', 'N', 'Q']; // Premium, Total Sum Insured, Total Gross Premium, Total Brokerage
        $percentageColumns = ['O', 'P']; // Cedant Commissions, Reinsurance Commission

        foreach ($currencyColumns as $col) {
            $sheet->getStyle("{$col}" . ($headerRow + 1) . ":{$col}{$rowCount}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        foreach ($percentageColumns as $col) {
            $sheet->getStyle("{$col}" . ($headerRow + 1) . ":{$col}{$rowCount}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Set row heights for better appearance
        $sheet->getRowDimension(1)->setRowHeight(45); // Company header
        $sheet->getRowDimension(2)->setRowHeight(35); // Report title

        // Filter rows
        for ($i = 3; $i < $headerRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }

        $sheet->getRowDimension($headerRow)->setRowHeight(30); // Column headers

        // Data rows
        for ($i = $headerRow + 1; $i <= $rowCount; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(22);
        }

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\BeforeSheet::class => function ($event) {
                $sheet = $event->sheet->getDelegate();

                // Company header (left side)
                $companyHeader = 'ACENTRIA INTERNATIONAL';
                $sheet->setCellValue('A1', $companyHeader);
                $sheet->mergeCells('A1:Q1');

                // Logo placeholder (right side) - you'll need to add the actual logo
                $this->insertLogo($sheet);

                // Report title
                $reportTitle = 'PIPELINE REPORT';
                $sheet->setCellValue('A2', $reportTitle);
                $sheet->mergeCells('A2:R2');

                // Filter labels (left side, multiple rows)
                $filterLabels = $this->buildFilterLabels();
                $currentRow = 3;
                foreach ($filterLabels as $label) {
                    $sheet->setCellValue('A' . $currentRow, $label);
                    $sheet->mergeCells('A' . $currentRow . ':R' . $currentRow);
                    $currentRow++;
                }

                // Freeze panes for better navigation
                $sheet->freezePane('A' . ($currentRow + 1));
            },
        ];
    }

    private function insertLogo(Worksheet $sheet)
    {
        $logoPath = public_path('logo.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Acentria International Logo');
            $drawing->setPath($logoPath);
            $drawing->setHeight(40);
            $drawing->setCoordinates('R1');
            $drawing->setWorksheet($sheet);
        } else {
            // Fallback text if logo is missing
            $sheet->setCellValue('N1', 'Logo Placeholder');
            $sheet->getStyle('N1:R1')->applyFromArray([
                'font' => [
                    'color' => ['rgb' => '2C3E50'], // Dark gray for fallback
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);
        }
    }


    private function buildFilterLabels()
    {
        $filters = $this->filters;
        $labels = [];

        // Generation info
        $labels[] = 'Report Generated: ' . date('F j, Y \a\t g:i A');

        // Individual filter lines
        if (!empty($filters['from_year'])) {
            $pipeline = Pipeline::where('id', $filters['from_year'])->first();
            $labels[] = "From Pipeline Year: {$pipeline->year}";
        }

        if (!empty($filters['to_year'])) {
            $pipeline = Pipeline::where('id', $filters['to_year'])->first();
            $labels[] = "To Pipeline Year: {$pipeline->year}";
        }

        if (!empty($filters['start_date'])) {
            $labels[] = "Start Date: " . $this->formatDate($filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $labels[] = "End Date: " . $this->formatDate($filters['end_date']);
        }

        if (!empty($filters['closure_date'])) {
            $labels[] = "Closure Date: " . $this->formatDate($filters['closure_date']);
        }

        // If no filters, add a note
        if (count($labels) === 1) { // Only generation info
            $labels[] = 'No Filters Applied';
        }

        return $labels;
    }

    private function firstUpper($text)
    {
        return $text ? ucwords(strtolower(trim($text))) : '';
    }

    private function formatNumber($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $num = floatval(str_replace(',', '', $value));
        return is_nan($num) ? $value : number_format($num, 2, '.', ',');
    }

    private function formatCurrency($value, $currency = 'USD')
    {
        if ($value === null || $value === '') {
            return '';
        }

        $num = floatval(str_replace(',', '', $value));
        if (is_nan($num)) {
            return $value;
        }

        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'KES' => 'KSh',
            'UGX' => 'USh',
            'TZS' => 'TSh',
        ];

        $symbol = $currencySymbols[$currency] ?? $currency . ' ';
        return $symbol . number_format($num, 2, '.', ',');
    }

    private function formatPercentage($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $num = floatval(str_replace(',', '', $value));
        if (is_nan($num)) {
            return $value;
        }

        // If value is less than 1, assume it's already a decimal (0.15 = 15%)
        if ($num < 1 && $num > 0) {
            return number_format($num * 100, 2, '.', ',') . '%';
        }

        // Otherwise, assume it's already a percentage value
        return number_format($num, 2, '.', ',') . '%';
    }

    private function formatDate($date)
    {
        if (empty($date)) {
            return '';
        }

        try {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                return $date;
            }
            return date('M j, Y', $timestamp);
        } catch (\Exception $e) {
            return $date;
        }
    }
}
