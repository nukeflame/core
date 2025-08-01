<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductionByDebitTypeBPExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithCustomStartCell, WithEvents
{

    /**
     * The report title.
     *
     * @var string
     */
    private string $reportTitle = 'REPORT: PRODUCTION BY DEBIT TYPE';
    protected $data;

    /**
     * Create a new export instance.
     * @return void
     */
    public function __construct($data, $viewPath)
    {
        $this->data = $data;
    }


    /**
     * Retrieve the data for the export.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        // logger(json_encode($data, JSON_PRETTY_PRINT));

        // In a real application, this should come from a repository or service
        return $this->getReportData();
    }

    private function getReportData(): Collection
    {
        return $this->data;
    }

    /**
     * Define the column headings.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'TYPE CODE',
            'TYPE NAME',
            'CED PREMIUM KES(GWP)',
            'COMMISION KES(CEDANT)',
            'PRM TAX KES',
            'REIN TAX KES',
            'W/TAX KES',
            'VAT RES',
            'CLAIM KES',
            'REVENUE KES(INCOME)',
        ];
    }

    /**
     * Set the title of the worksheet.
     *
     * @return string
     */
    public function title(): string
    {
        return 'Production By Debit Type';
    }

    /**
     * Set the starting cell for the data.
     *
     * @return string
     */
    public function startCell(): string
    {
        return 'A4';
    }

    /**
     * Apply styles to the worksheet.
     *
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A4:' . $this->getLastColumnLetter() . '4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'C0C0FF',
                ],
            ],
        ]);
    }

    /**
     * Register events for additional processing.
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->addReportTitle($event);
                $this->autoSizeColumns($event);
                $this->addBorders($event);
            },
        ];
    }

    /**
     * Add the report title to the sheet.
     *
     * @param AfterSheet $event
     * @return void
     */
    private function addReportTitle(AfterSheet $event): void
    {
        $lastColumn = $this->getLastColumnLetter();

        $event->sheet->setCellValue('A1', $this->reportTitle);
        $event->sheet->mergeCells("A1:{$lastColumn}1");

        $event->sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
    }

    /**
     * Auto-size all columns to fit content.
     *
     * @param AfterSheet $event
     * @return void
     */
    private function autoSizeColumns(AfterSheet $event): void
    {
        $lastColumn = $this->getLastColumnLetter();

        foreach (range('A', $lastColumn) as $column) {
            $event->sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    /**
     * Add borders to the data.
     *
     * @param AfterSheet $event
     * @return void
     */
    private function addBorders(AfterSheet $event): void
    {
        $lastColumn = $this->getLastColumnLetter();
        $lastRow = 4 + $this->collection()->count();

        $event->sheet->getStyle("A4:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
    }

    /**
     * Get the letter of the last column based on headings.
     *
     * @return string
     */
    private function getLastColumnLetter(): string
    {
        return Coordinate::stringFromColumnIndex(count($this->headings()));
    }
}
