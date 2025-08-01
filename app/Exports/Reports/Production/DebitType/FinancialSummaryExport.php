<?php

namespace App\Exports\Reports\Production\DebitType;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Excel;

class FinancialSummaryExport implements WithMultipleSheets, Responsable
{
    use Exportable;

    private $data;
    private $fileName;
    private $writerType;
    private $headers;
    private $year;
    private $quarter;
    private $getIncomeData;

    /**
     * Create a new export instance.
     *
     * @return void
     */
    public function __construct(array $data, string $viewPath, int $year, int $quarter)
    {
        $this->getIncomeData = $data;
        $this->year = $year;
        $this->quarter = $quarter;
        $this->writerType = Excel::XLSX;
        $this->headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $getIncomeData = [];
        $getGwpData = null;

        return [
            new IncomeReportSheet($getIncomeData, $getGwpData, $this->year, $this->quarter),
        ];
    }
}
