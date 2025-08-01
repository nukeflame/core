<?php

namespace App\Exports\Reports;

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

class ProductionByDebitTypeFSExport implements FromArray, WithTitle, WithStyles, WithEvents, WithColumnWidths, WithCustomStartCell
{
    private $year;
    private $quarter;
    private $sumQuarters;
    private $months;
    private $categories;
    private $reportData;

    /**
     * Create a new sheet instance.
     *
     * @param int $year
     * @param int $quarter
     * @return void
     */
    public function __construct($getIncomeData, int $year, int $quarter)
    {
        // logger($year);
        $this->year = $year;
        $this->quarter = 0;
        $this->sumQuarters = 1;
        $this->months = $this->getMonthsForQuarter(0);
        $this->categories = $this->defineCategories();
        // $this->reportData = $getIncomeData($year, $quarter);
        $this->reportData = [];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "INCOME - {$this->year}";
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'B2';
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 2,
            'B' => 36,
            'C' => 17,
            'D' => 17,
            'E' => 17,
            'F' => 17,
            'G' => 17,
            'H' => 17,
            'I' => 17,
            'J' => 17,
            'K' => 17,
            'L' => 17,
            'M' => 17,
            'N' => 17,
            'O' => 17,
            'P' => 17,
            'Q' => 17,
            'R' => 17,
            'S' => 17,
            'T' => 17,
            'U' => 17,
            'V' => 17,
            'W' => 17,
            'X' => 17,
            'Y' => 17,
            'Z' => 17,
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
            [strtoupper("INCOME - {$this->year}")], // Title
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
                    $row[] = $this->formatValue($this->reportData[$categoryKey][$month]['actual'] ?? '-');
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
                    $row[] = $this->formatValue($this->reportData[$categoryKey][$month]['budgeted'] ?? '-');
                    $row[] = $this->formatValue($this->reportData[$categoryKey][$month]['actual'] ?? '-');
                }
                $data[] = $row;
            }
        }

        // Add total row
        $totalRow = ['Total - Renewal Business'];
        foreach ($this->months as $month) {
            $totalRow[] = '#########';
            $totalRow[] = $this->formatValue($this->reportData['total'][$month]['actual'] ?? '#########');
        }
        $data[] = $totalRow;

        //---- GWP REPORT DATA

        // Create the data array starting with headers
        $data[] = [
            [], // Empty row
            [], // Empty row
        ];

        $data[] = [
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
        // $lastRow = $sheet->getHighestRow();
        $lastRow = 17;
        $lastColumn = $sheet->getHighestColumn();

        // Apply global styles
        $sheet->getStyle('B2:' . $lastColumn . $lastRow)->getFont()->setName('Tahoma');
        $sheet->getStyle('B2:' . $lastColumn . $lastRow)->getFont()->setSize(9);
        $sheet->getStyle('B3:' . $lastColumn . $lastRow)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);

        // Style title
        $sheet->mergeCells('B2');
        $sheet->getStyle('B2')->getFont()->setBold(true);
        $sheet->getStyle('B2')->getFont()->setSize(9);
        $sheet->getStyle('B2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CC0000');
        $sheet->getStyle('B2')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        // $sheet->getStyle('B2')->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle('B2')->getBorders()->getLeft()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle('B2')->getBorders()->getRight()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle('B2')->getBorders()->getOutline(false);

        // Style headers
        // $sheet->mergeCells('B3:B4');
        $sheet->getStyle('B3')->getFont()->setBold(true);
        $sheet->getStyle('B3:' . $lastColumn . '4')->getFont()->setBold(true);
        $sheet->getStyle('B3:' . $lastColumn . '4')->getBorders()->getInside()->setBorderStyle(Border::BORDER_DOTTED);

        // Style month headers by merging cells
        $columnIndex = 3; // Start from column C
        foreach ($this->months as $month) {
            $startColumn = Coordinate::stringFromColumnIndex($columnIndex);
            $endColumn = Coordinate::stringFromColumnIndex($columnIndex + 1);
            $sheet->mergeCells($startColumn . '3:' . $endColumn . '3');
            $columnIndex += 2;
        }

        // Style data rows
        $sheet->getStyle('B5:' . $lastColumn . $lastRow)->getBorders()->getInside()->setBorderStyle(Border::BORDER_DOTTED);

        // Style category rows
        $rowIndex = 5;
        foreach ($this->categories as $category) {
            if (isset($category['color'])) {
                $sheet->getStyle('B' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($category['color']);
            }
            $sheet->getStyle('B' . $rowIndex)->getFont()->setBold(true);
            $rowIndex++;

            // Skip rows for subcategories if they exist
            if (isset($category['subcategories'])) {
                $rowIndex += count($category['subcategories']);
            }
        }

        // Style total row
        $sheet->getStyle('B' . $lastRow . ':' . $lastColumn . $lastRow)->getFont()->setBold(true);
        $sheet->getStyle('B' . $lastRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDFFDD');

        return [];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Center align numeric cells
                $lastRow = $event->sheet->getHighestRow();
                $lastColumn = $event->sheet->getHighestColumn();
                $numericRange = 'C5:' . $lastColumn . $lastRow;
                $event->sheet->getStyle($numericRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Set print area
                $event->sheet->getPageSetup()->setPrintArea('B2:' . $lastColumn . $lastRow);
                $event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0);
            },
            []
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

        return $quarterMonths[$quarter] ?? ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
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
