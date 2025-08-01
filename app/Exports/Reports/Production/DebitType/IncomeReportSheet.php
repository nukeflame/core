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

class IncomeReportSheet implements FromArray, WithTitle, WithStyles, WithEvents, WithColumnWidths, WithCustomStartCell
{
    private $year;
    private $quarter;
    private $months;
    private $categories;
    private $reportData;
    private $getIncomeData;
    private $getGwpData;

    /**
     * Create a new sheet instance.
     *
     * @param callable $getIncomeData Function to retrieve income data
     * @param callable $getGwpData Function to retrieve GWP data
     * @param int $year
     * @param int $quarter
     * @return void
     */
    public function __construct($getIncomeData, $getGwpData = null, int $year, int $quarter)
    {
        $this->year = $year;
        $this->quarter = $quarter ?: 1;
        $this->getIncomeData = $getIncomeData;
        $this->getGwpData = $getGwpData ?? $getIncomeData;
        $this->months = $this->getMonthsForQuarter($this->quarter);
        $this->categories = $this->defineCategories();
        $this->reportData = is_callable($getIncomeData) ? $getIncomeData($year, $quarter) : [];
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
        $widths = [];
        $widths['A'] = 2;
        $widths['B'] = 36;

        if ($this->quarter == 5) {
            foreach (range('C', 'Z') as $column) {
                $widths[$column] = 20;
            }
        } else {
            foreach (range('C', 'H') as $column) {
                $widths[$column] = 20;
            }
        }

        return $widths;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        // Create the header row with months
        $headerRow = $this->quarter == 5 ? ['All Year'] : ['Quarter ' . $this->quarter];
        foreach ($this->months as $month) {
            $headerRow[] = $month;
            $headerRow[] = '';
        }

        // Create subheader row with Budgeted/Actual columns
        $subHeaderRow = [''];
        foreach ($this->months as $month) {
            $subHeaderRow[] = 'Budgeted';
            $subHeaderRow[] = 'Actual';
        }

        // Create the data array starting with headers
        $data = [
            [strtoupper("INCOME - {$this->year}")],
            [],
            $headerRow,
            $subHeaderRow,
        ];

        // Add categories and their data
        foreach ($this->categories as $categoryKey => $category) {
            if (isset($category['subcategories'])) {
                $row = [$category['name']];
                foreach ($this->months as $month) {
                    $row[] = $this->formatValue($this->getCategoryValue($categoryKey, $month, 'budgeted'));
                    $row[] = $this->formatValue($this->getCategoryValue($categoryKey, $month, 'actual'));
                }
                $data[] = $row;

                foreach ($category['subcategories'] as $subKey => $subName) {
                    $subRow = ['    ' . $subName];
                    foreach ($this->months as $month) {
                        $subRow[] = $this->formatValue($this->getSubcategoryValue($categoryKey, $subKey, $month, 'budgeted'));
                        $subRow[] = $this->formatValue($this->getSubcategoryValue($categoryKey, $subKey, $month, 'actual'));
                    }
                    $data[] = $subRow;
                }
            } else {
                $row = [$category['name']];
                foreach ($this->months as $month) {
                    $row[] = $this->formatValue($this->getCategoryValue($categoryKey, $month, 'budgeted'));
                    $row[] = $this->formatValue($this->getCategoryValue($categoryKey, $month, 'actual'));
                }
                $data[] = $row;
            }
        }

        // Add total row
        $totalRow = ['Total - Renewal Business'];
        foreach ($this->months as $month) {
            $totalRow[] = $this->formatValue($this->getCategoryValue('total', $month, 'budgeted', '#########'));
            $totalRow[] = $this->formatValue($this->getCategoryValue('total', $month, 'actual', '#########'));
        }
        $data[] = $totalRow;

        //---- GWP REPORT DATA
        $data[] = [[], []];

        // Add GWP header
        $data[] = [strtoupper("GWP - {$this->year}")];
        $data[] = [];

        $data[] = $headerRow;
        $data[] = $subHeaderRow;

        $gwpData = is_callable($this->getGwpData) ? call_user_func($this->getGwpData, $this->year, $this->quarter) : [];

        // Add categories and their data for GWP
        foreach ($this->categories as $categoryKey => $category) {
            if (isset($category['subcategories'])) {
                $row = [$category['name']];
                foreach ($this->months as $month) {
                    $row[] = $this->formatValue($this->getGwpCategoryValue($gwpData, $categoryKey, $month, 'budgeted'));
                    $row[] = $this->formatValue($this->getGwpCategoryValue($gwpData, $categoryKey, $month, 'actual'));
                }
                $data[] = $row;

                foreach ($category['subcategories'] as $subKey => $subName) {
                    $subRow = ['    ' . $subName];
                    foreach ($this->months as $month) {
                        $subRow[] = $this->formatValue($this->getGwpSubcategoryValue($gwpData, $categoryKey, $subKey, $month, 'budgeted'));
                        $subRow[] = $this->formatValue($this->getGwpSubcategoryValue($gwpData, $categoryKey, $subKey, $month, 'actual'));
                    }
                    $data[] = $subRow;
                }
            } else {
                $row = [$category['name']];
                foreach ($this->months as $month) {
                    $row[] = $this->formatValue($this->getGwpCategoryValue($gwpData, $categoryKey, $month, 'budgeted'));
                    $row[] = $this->formatValue($this->getGwpCategoryValue($gwpData, $categoryKey, $month, 'actual'));
                }
                $data[] = $row;
            }
        }

        $totalRow = ['Total - Renewal Business'];
        foreach ($this->months as $month) {
            $totalRow[] = $this->formatValue($this->getGwpCategoryValue($gwpData, 'total', $month, 'budgeted', '#########'));
            $totalRow[] = $this->formatValue($this->getGwpCategoryValue($gwpData, 'total', $month, 'actual', '#########'));
        }

        $data[] = $totalRow;
        return $data;
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        // Find the row where GWP section starts
        $gwpTitleRow = $this->findGwpTitleRow($sheet);

        $lastRow = $gwpTitleRow - 3 ??  $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $gwLastRow = $sheet->getHighestRow();

        // Apply global styles
        $sheet->getStyle('B2:' . $lastColumn . $gwLastRow)->getFont()->setName('Tahoma');
        $sheet->getStyle('B2:' . $lastColumn . $gwLastRow)->getFont()->setSize(9);
        $sheet->getStyle('B3:' . $lastColumn . $lastRow)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);

        $this->styleSectionTitle($sheet, 'B2', 'CC0000');

        // Style headers
        $this->styleHeaders($sheet, 'B3', $lastColumn, '4');
        $this->mergeQuarterHeaders($sheet, 'B', '3', '4');

        // Style month headers by merging cells for INCOME section
        $this->mergeMonthHeaders($sheet, 3);

        // Style data rows
        $sheet->getStyle('B5:' . $lastColumn . $lastRow)->getBorders()->getInside()->setBorderStyle(Border::BORDER_DOTTED);
        $sheet->getStyle('B5:' . $lastColumn . $lastRow)->getBorders()->getInside()->setBorderStyle(Border::BORDER_DOTTED);


        if ($gwpTitleRow > 0) {
            $this->styleSectionTitle($sheet, 'B' . $gwpTitleRow, 'CC0000');
            $sheet->getStyle('B' . $gwpTitleRow + 1 . ':' .  $lastColumn . $gwLastRow)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);

            $headerRow = $gwpTitleRow + 1;
            $subheaderRow = $headerRow + 1;

            $this->styleHeaders($sheet, 'B' . $headerRow, $lastColumn, $subheaderRow);
            $this->mergeQuarterHeaders($sheet, 'B', $headerRow, $headerRow + 1);

            $this->mergeMonthHeaders($sheet, $headerRow);
        }

        // Style category rows for both sections
        $this->styleCategoryRows($sheet);

        return [];
    }

    private function styleSectionTitle(Worksheet $sheet, string $cell, string $color): void
    {
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
        $sheet->getStyle($cell)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle($cell)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle($cell)->getBorders()->getRight()->setBorderStyle(Border::BORDER_MEDIUM);
    }

    private function mergeQuarterHeaders(Worksheet $sheet, string $cell, string $startCell, string $lastCell): void
    {

        $sheet->mergeCells($cell . $startCell . ':' . $cell . $lastCell);
    }

    private function styleHeaders(Worksheet $sheet, string $startCell, string $lastColumn, string $subheaderRow): void
    {
        $sheet->getStyle($startCell)->getFont()->setBold(true);
        $sheet->getStyle($startCell . ':' . $lastColumn . $subheaderRow)->getFont()->setBold(true);
        $sheet->getStyle($startCell . ':' . $lastColumn . $subheaderRow)->getBorders()->getInside()->setBorderStyle(Border::BORDER_DOTTED);
    }

    private function mergeMonthHeaders(Worksheet $sheet, int $headerRowIndex): void
    {
        $columnIndex = 3; // Start from column C
        foreach ($this->months as $month) {
            $startColumn = Coordinate::stringFromColumnIndex($columnIndex);
            $endColumn = Coordinate::stringFromColumnIndex($columnIndex + 1);
            $sheet->mergeCells($startColumn . $headerRowIndex . ':' . $endColumn . $headerRowIndex);
            $columnIndex += 2;
        }
    }

    private function findGwpTitleRow(Worksheet $sheet): int
    {
        for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
            $value = $sheet->getCell('B' . $row)->getValue();
            if ($value == strtoupper("GWP - {$this->year}")) {
                return $row;
            }
        }
        return 0;
    }

    private function styleCategoryRows(Worksheet $sheet): void
    {
        $lastColumn = $sheet->getHighestColumn();
        $inIncomeSection = true;
        $categoryIndex = 0;
        $totalRows = [];


        // Iterate through all rows to find and style categories
        for ($row = 5; $row <= $sheet->getHighestRow(); $row++) {
            $value = $sheet->getCell('B' . $row)->getValue();

            // Skip empty rows
            if (empty($value)) {
                continue;
            }

            // Check if we've reached the GWP section
            if ($value == strtoupper("GWP - {$this->year}")) {
                $inIncomeSection = false;
                continue;
            }

            // Check if it's a total row
            if (strpos($value, 'Total - ') === 0) {
                $totalRows[] = $row;
                continue;
            }

            // Check if it's a category (not subcategory, which is indented)
            if (substr($value, 0, 4) !== '    ') {
                $categoryKeys = array_keys($this->categories);

                if (isset($categoryKeys[$categoryIndex])) {
                    $categoryKey = $categoryKeys[$categoryIndex];
                    $category = $this->categories[$categoryKey];

                    if (isset($category['color'])) {
                        $sheet->getStyle('B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($category['color']);

                        // for gwp set dynamically
                        $sheet->getStyle('B' . $row + 18)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($category['color']);
                    }

                    $sheet->getStyle('B' . $row)->getFont()->setBold(true);

                    // for gwp set dynamically
                    $sheet->getStyle('B' . $row + 18)->getFont()->setBold(true);

                    // Only increment the category index in the first section
                    if ($inIncomeSection) {
                        $categoryIndex++;
                    }
                }
            }
        }

        // Style total rows
        foreach ($totalRows as $row) {
            $sheet->getStyle('B' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDFFDD');
        }
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
                // $numericRange = 'C5:' . $lastColumn . $lastRow;

                //set dynamically
                $event->sheet->getStyle('C5:N17')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('C23:H35')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Set print area
                $event->sheet->getPageSetup()->setPrintArea('B2:' . $lastColumn . $lastRow);
                $event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0);
            }
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

        if ($quarter >= 1 && $quarter <= 4) {
            return $quarterMonths[$quarter];
        }

        return array_merge($quarterMonths[1], $quarterMonths[2], $quarterMonths[3], $quarterMonths[4]);
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

    private function formatValue($value)
    {
        return $value;
    }

    private function getCategoryValue($category, $month, $type, $default = '-')
    {
        return $this->reportData[$category][$month][$type] ?? $default;
    }

    private function getSubcategoryValue($category, $subcategory, $month, $type, $default = '-')
    {
        return $this->reportData[$category]['subcategories'][$subcategory][$month][$type] ?? $default;
    }

    private function getGwpCategoryValue($gwpData, $category, $month, $type, $default = '-')
    {
        return $gwpData[$category][$month][$type] ?? $default;
    }

    private function getGwpSubcategoryValue($gwpData, $category, $subcategory, $month, $type, $default = '-')
    {
        return $gwpData[$category]['subcategories'][$subcategory][$month][$type] ?? $default;
    }
}
