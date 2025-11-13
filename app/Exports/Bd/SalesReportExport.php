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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesReportExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters)
    {
        $this->data = $data;
        $this->filters = $filters;
        Log::info('SalesReportExport filters received: ' . json_encode($filters));
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return [
            'Id',
            'Cedant',
            'Insured Name',
            'Division',
            'Business Class',
            'Currency',
            'Sum Insured(100%)',
            'Premium(100%)',
            'Revenue',
            'Start Date',
            'End Date',
            'Turn Around Time',
            'Sales Entry',
            'Stage Entry',
            'Stage Duration',
            'Reverted To Pipeline',
            'Status',
            'Category',
        ];
    }

    public function map($row): array
    {
        // Customer Name
        $customer = DB::table('customers')
            ->where('customer_id', (int) ($row->customer_id ?? 0))
            ->first();
        $customer_name = $customer ? $this->firstUpper($customer->name) : 'N/A';

        // Division Name
        $division = DB::table('reins_division')
            ->where('division_code', $row->divisions ?? '')
            ->first();
        $division_name = $division ? $this->firstUpper($division->division_name) : 'N/A';

        // Business Class
        $business_class = DB::table('classes')
            ->where('class_code', $row->classcode ?? '')
            ->first();
        $business_class_name = $business_class ? $this->firstUpper($business_class->class_name) : 'N/A';

        // Stage (Status)
        $stage_name = '';
        if ($row->category_type == 1 || $row->category_type == 2) {
            $lead_status = DB::table('lead_status')
                ->where('id', $row->stage ?? 0)
                ->where('category_type', $row->category_type ?? '')
                ->first();
            $stage_name = $lead_status ? $lead_status->status_name : '';
        }

        // Turnaround Time
        $turnaround_time = 'N/A';
        if (!empty($row->sales_entry_date) && !empty($row->won_at)) {
            $diff = Carbon::parse($row->sales_entry_date)->diff(Carbon::parse($row->won_at));
            $turnaround_time = sprintf('%02d hrs %02d mins %02d secs', $diff->h, $diff->i, $diff->s);
        }

        // Stage Duration
        $current_stage_duration = 'N/A';
        if (!empty($row->stage_updated_at)) {
            $duration = Carbon::now()->diffInSeconds(Carbon::parse($row->stage_updated_at));
            $current_stage_duration = gmdate('H:i:s', $duration);
        }

        // Category
        $category = $row->category_type == 1 ? 'Quotation' : ($row->category_type == 2 ? 'Facultative Offer' : '');

        return [
            $row->opportunity_id ?? 'N/A',
            $customer_name,
            $this->firstUpper($row->insured_name ?? ''),
            $division_name,
            $business_class_name,
            $row->currency_code ?? 'USD',
            $this->formatNumber($row->effective_sum_insured ?? null),
            $this->formatNumber($row->cede_premium ?? null),
            $this->formatNumber($row->brokerage_comm_amt ?? null),
            $this->formatDate($row->effective_date ?? ''),
            $this->formatDate($row->closing_date ?? ''),
            $turnaround_time,
            $this->formatDate($row->sales_entry_date ?? ''),
            $this->formatDate($row->stage_updated_at ?? ''),
            $current_stage_duration,
            $this->firstUpper($row->reverted_to_pipeline ?? 'N/A'),
            $stage_name,
            $category,
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
            'A1:M1' => [
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
            // Logo area (row 1, cell R1 only) - minimal styling, no color
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

        // Style filter rows (rows 3 to headerRow-1) - left-aligned
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

        // Apply striped rows and conditional highlights
        foreach ($this->data as $index => $row) {
            $rowNum = $index + $headerRow + 1;
            $style = [];

            // Alternating row colors
            if ($index % 2 === 0) {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'F8F9FA'],
                ];
            } else {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FFFFFF'],
                ];
            }

            // Status-based highlighting
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

        // Numeric and date column alignment
        $numericColumns = ['A', 'G', 'H', 'I'];
        foreach ($numericColumns as $col) {
            $sheet->getStyle("{$col}" . ($headerRow + 1) . ":{$col}{$rowCount}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $dateColumns = ['J', 'K', 'M', 'N'];
        foreach ($dateColumns as $col) {
            $sheet->getStyle("{$col}" . ($headerRow + 1) . ":{$col}{$rowCount}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(45); // Company header
        $sheet->getRowDimension(2)->setRowHeight(35); // Report title
        for ($i = 3; $i < $headerRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20); // Filter rows
        }
        $sheet->getRowDimension($headerRow)->setRowHeight(30); // Column headers
        for ($i = $headerRow + 1; $i <= $rowCount; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(22); // Data rows
        }

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\BeforeSheet::class => function ($event) {
                $sheet = $event->sheet->getDelegate();

                // Company header
                $sheet->setCellValue('A1', 'ACENTRIA INTERNATIONAL');
                $sheet->mergeCells('A1:M1');

                // Logo
                $this->insertLogo($sheet);

                // Report title
                $sheet->setCellValue('A2', 'SALES MANAGEMENT REPORT');
                $sheet->mergeCells('A2:R2');

                // Filter labels
                $filterLabels = $this->buildFilterLabels();
                Log::info('Filter labels generated: ' . json_encode($filterLabels));
                $currentRow = 3;
                foreach ($filterLabels as $label) {
                    $sheet->setCellValue('A' . $currentRow, $label);
                    $sheet->mergeCells('A' . $currentRow . ':R' . $currentRow);
                    $currentRow++;
                }

                // Freeze panes
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
            $sheet->setCellValue('R1', 'Logo');
            $sheet->getStyle('R1')->applyFromArray([
                'font' => [
                    'name' => 'Calibri',
                    'size' => 16,
                    'bold' => true,
                    'color' => ['rgb' => '2C3E50'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }
    }

    private function buildFilterLabels()
    {
        $filters = $this->filters;
        $labels = [];

        // Generation info (always included)
        $labels[] = 'Report Generated: ' . Carbon::now()->format('F j, Y \a\t g:i A');

        // Year filter
        if (!empty($filters['year'])) {
            $pipeline = Pipeline::where('id', (int) ($filters['year'] ?? 0))->first();
            if (!$pipeline) {
                Log::warning("Year filter ID {$filters['year']} not found in Pipeline");
            }
            $labels[] = "Year: " . ($pipeline->year ?? 'Unknown');
        }

        // Cedant filter
        if (!empty($filters['cedant'])) {
            $customer = DB::table('customers')
                ->where('customer_id', (int) ($filters['cedant'] ?? 0))
                ->first();
            if (!$customer) {
                Log::warning("Cedant ID {$filters['cedant']} not found in customers table");
            }
            $customer_name = $customer ? $this->firstUpper($customer->name) : 'Unknown';
            $labels[] = "Cedant: {$customer_name}";
        }

        // Class Group filter
        if (!empty($filters['class_group'])) {
            $class_group = DB::table('class_groups')
                ->where('id', $filters['class_group'] ?? 0)
                ->first();
            if (!$class_group) {
                Log::warning("Class Group ID {$filters['class_group']} not found in class_groups table");
            }
            $group_name = $class_group ? $this->firstUpper($class_group->group_name) : 'Unknown';
            $labels[] = "Class Group: {$group_name}";
        }

        // Business Class filter
        if (!empty($filters['classcode'])) {
            $class = DB::table('classes')
                ->where('class_code', $filters['classcode'] ?? '')
                ->first();
            if (!$class) {
                Log::warning("Class code {$filters['classcode']} not found in classes table");
            }
            $class_name = $class ? $this->firstUpper($class->class_name) : 'Unknown';
            $labels[] = "Business Class: {$class_name}";
        }

        // Category filter
        if (!empty($filters['category_type']) || !empty($filters['lead_status_category'])) {
            $category = !empty($filters['lead_status_category']) ? $filters['lead_status_category'] : ($filters['category_type'] ?? null);
            $category_name = $category == 1 ? 'Quotation' : ($category == 2 ? 'Facultative Offer' : 'Unknown');
            $labels[] = "Category: {$category_name}";
        }

        // Status filter
        if (!empty($filters['lead_status'])) {
            $lead_status = DB::table('lead_status')
                ->where('id', $filters['lead_status'] ?? 0)
                ->where('category_type', $filters['lead_status_category'] ?? $filters['category_type'] ?? null)
                ->first();
            if (!$lead_status) {
                // Log::warning("Lead status ID {$filters['lead_status']} not found for category_type {$filters['lead_status_category'] ?? $filters['category_type'] ?? 'null'}");
            }
            $status_name = $lead_status ? $this->firstUpper($lead_status->status_name) : 'Unknown';
            $labels[] = "Status: {$status_name}";
        }

        // Start Date filter
        if (!empty($filters['start_date'])) {
            $start_date = $this->formatDate($filters['start_date']);
            if ($start_date === $filters['start_date']) {
                Log::warning("Invalid start_date format: {$filters['start_date']}");
            }
            $labels[] = "Start Date: {$start_date}";
        }

        // Month filter
        if (!empty($filters['month'])) {
            $month_names = [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December'
            ];
            $month_name = $month_names[(int) $filters['month']] ?? 'Unknown';
            if ($month_name === 'Unknown') {
                Log::warning("Invalid month filter: {$filters['month']}");
            }
            $labels[] = "Month: {$month_name}";
        }

        // Log the final labels for debugging
        Log::info('Filter labels generated: ' . json_encode($labels));

        return empty($labels) ? ['No Filters Applied'] : $labels;
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

    private function stripHtml($html)
    {
        return strip_tags($html);
    }
}
