<?php

namespace App\Exports\Reports\Production\DebitType;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GwpReportSheet implements FromArray, WithTitle, WithStyles, WithEvents, WithColumnWidths, WithCustomStartCell
{
    private $year;
    private $quarter;
    private $months;
    private $categories;
    private $reportData;
    private $percentages;

    /**
     * Create a new sheet instance.
     *
     * @param ReportService $reportService
     * @param int $year
     * @param int $quarter
     * @return void
     */
    public function __construct($data, int $year, int $quarter)
    {
        // $this->data = $data;
        $this->year = $year;
        $this->quarter = $quarter;
        $this->months = $this->getMonthsForQuarter($quarter);
        $this->categories = $this->defineCategories();
        // $this->reportData = $this->data->getGwpData($year, $quarter);
        // $this->percentages = $this->data->getGwpPercentages($year, $quarter);
        $reportData  = [];
        $percentages  = [];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "GWP - {$this->year}";
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'A1';
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 15,
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        // Create the header row with months
        $headerRow = [''];
        foreach ($this->months as $month) {
            $headerRow[] = $month;
            $headerRow[] = '';
        }

        // Create subheader row with Budgeted/Actual columns
        $subHeaderRow = ['Quarter ' . $this->quarter];
        foreach ($this->months as $month) {
            $subHeaderRow[] = 'Budgeted';
            $subHeaderRow[] = 'Actual';
        }

        // Create the data array starting with headers
        $data = [
            [strtoupper("GWP - {$this->year}")], // Title
            [], // Empty row
            $headerRow,
            $subHeaderRow,
        ];

        // Add categories and their data
        foreach ($this->categories as $categoryKey => $category) {
            if (isset($category['subcategories'])) {
                // Add main category
                $row = [$category['name']];
                foreach ($this->months as $month) {
                    $row[] = $this->formatValue($this->reportData[$categoryKey][$month]['budgeted'] ?? '#########');
                    $row[] = $this->formatValue($this->reportData[$categoryKey][$month]['actual'] ?? '#########');
                }
                $data[] = $row;

                // Add subcategories
                foreach ($category['subcategories'] as $subKey => $subName) {
                    $subRow = ['    ' . $subName];
                    foreach ($this->months as $month) {
                        $subRow[] = $this->formatValue($this->reportData[$categoryKey]['subcategories'][$subKey][$month]['budgeted'] ?? '-');
                        $subRow[] = $this->formatValue($this->reportData[$categoryKey]['subcategories'][$subKey][$month]['actual'] ?? '-');
                    }
                    $data[] = $subRow;
                }
            } else {
                // Simple category without subcategories
                $row = [$category['name']];
                foreach ($this->months as $month) {
                    $row[] = $this->formatValue($this->reportData[$categoryKey][$month]['budgeted'] ?? '#########');
                    $row[] = $this->formatValue($this->reportData[$categoryKey][$month]['actual'] ?? '#########');
                }
                $data[] = $row;
            }
        }

        // Add total row
        $totalRow = ['Total - Renewal Business'];
        foreach ($this->months as $month) {
            $totalRow[] = '#########';
            $totalRow[] = '#########';
        }
        $data[] = $totalRow;

        // Add empty rows
        $data[] = [];
        $data[] = [];

        // Add percentages
        $percentageRow = [''];
        // foreach ($this->percentages as $month => $percentage) {
        //     if (in_array($month, $this->months)) {
        //         $percentageRow[] = $percentage . '%';
        //         $percentageRow[] = '';
        //     }
        // }
        $data[] = $percentageRow;

        // Create the data array starting with headers
        $data = [
            [strtoupper("GWP - {$this->year}")], // Title
            [], // Empty row
            $headerRow,
            $subHeaderRow,
        ];

        return $data;
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        // Find total rows for styling
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Apply global styles
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setName('Arial');
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setSize(10);

        // Style title
        $sheet->mergeCells('A1:' . $lastColumn . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CC0000');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Style headers
        $sheet->mergeCells('A3:A4');
        $sheet->getStyle('A3:' . $lastColumn . '4')->getFont()->setBold(true);
        $sheet->getStyle('A3:' . $lastColumn . '4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Style month headers by merging cells
        $columnIndex = 2; // Start from column B
        foreach ($this->months as $month) {
            $startColumn = Coordinate::stringFromColumnIndex($columnIndex);
            $endColumn = Coordinate::stringFromColumnIndex($columnIndex + 1);
            $sheet->mergeCells($startColumn . '3:' . $endColumn . '3');
            $columnIndex += 2;
        }

        // Find data rows end before percentages
        $dataEndRow = $sheet->getHighestRow() - 3;

        // Style data rows
        $sheet->getStyle('A5:' . $lastColumn . $dataEndRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Style category rows
        $rowIndex = 5;
        foreach ($this->categories as $category) {
            if (isset($category['color'])) {
                $sheet->getStyle('A' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($category['color']);
            }
            $sheet->getStyle('A' . $rowIndex)->getFont()->setBold(true);
            $rowIndex++;

            // Skip rows for subcategories if they exist
            if (isset($category['subcategories'])) {
                $rowIndex += count($category['subcategories']);
            }
        }

        // Style total row
        $sheet->getStyle('A' . $dataEndRow . ':' . $lastColumn . $dataEndRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $dataEndRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDFFDD');

        // Style percentages row
        $percentageRow = $lastRow;
        $startCol = Coordinate::stringFromColumnIndex(2); // Column B
        $sheet->getStyle($startCol . $percentageRow . ':' . $lastColumn . $percentageRow)->getNumberFormat()->setFormatCode('0.000%');

        return [];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Auto-size columns based on content
                foreach (range('A', $event->sheet->getHighestColumn()) as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Set row height
                for ($i = 1; $i <= $event->sheet->getHighestRow(); $i++) {
                    $event->sheet->getRowDimension($i)->setRowHeight(18);
                }

                // Center align numeric cells
                $lastRow = $event->sheet->getHighestRow() - 3; // Exclude percentage rows
                $lastColumn = $event->sheet->getHighestColumn();
                $numericRange = 'B5:' . $lastColumn . $lastRow;
                $event->sheet->getStyle($numericRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Right align percentage cells
                $percentageRow = $event->sheet->getHighestRow();
                $percentageRange = 'B' . $percentageRow . ':' . $lastColumn . $percentageRow;
                $event->sheet->getStyle($percentageRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Add input field decoration
                $inputCell = 'D' . ($percentageRow - 1);
                $event->sheet->getStyle($inputCell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $event->sheet->getStyle($inputCell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEFFEE');

                // Set print area
                $event->sheet->getPageSetup()->setPrintArea('A1:' . $lastColumn . $percentageRow);
                $event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }

    /**
     * Get the months for the specified quarter.
     *
     * @param int $quarter
     * @return array
     */
    private function getMonthsForQuarter(int $quarter): array
    {
        $quarterMonths = [
            1 => ['January', 'February', 'March'],
            2 => ['April', 'May', 'June'],
            3 => ['July', 'August', 'September'],
            4 => ['October', 'November', 'December'],
        ];

        return $quarterMonths[$quarter] ?? ['January', 'February', 'March', 'April', 'May', 'June'];
    }

    /**
     * Define report categories and their properties.
     *
     * @return array
     */
    private function defineCategories(): array
    {
        return [
            'facultative' => [
                'name' => 'Facultative (Offers & Quotations)',
            ],
            'special_lines' => [
                'name' => 'Special Lines',
                'subcategories' => [
                    'aviation' => 'Aviation',
                    'bbb' => 'BBB',
                    'clinical_trial' => 'Clinical Trial',
                    'medmal' => 'Medmal',
                    'cyber_liability' => 'Cyber Liability',
                ],
            ],
            'treaties' => [
                'name' => 'Treaties',
                'color' => 'CCFFCC',
            ],
            'international_market' => [
                'name' => 'International Market (Total)',
                'color' => 'CCFFCC',
                'subcategories' => [
                    'tanzania' => 'Tanzania',
                    'uganda' => 'Uganda',
                    'rwanda' => 'Rwanda',
                ],
            ],
        ];
    }

    /**
     * Format a value for display.
     *
     * @param mixed $value
     * @return string|float|int
     */
    private function formatValue($value)
    {
        return $value;
    }
}
