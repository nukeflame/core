<?php

namespace App\Exports\Bd;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReinsurersDeclinedExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return [
            'Pipeline ID',
            'Reinsurer Name',
            'Declined Reason',
            'Declined Date',
        ];
    }

    public function map($row): array
    {
        $customer = DB::table('customers')
            ->where('customer_id', (int) ($row->customer_id ?? 0))
            ->first();
        $customerName = $customer ? $this->firstUpper($customer->name) : 'N/A';
        return [
            $row->opportunity_id ?? 'N/A',
            $this->firstUpper($customerName ?? ''),
            $this->firstUpper($row->reason ?? ''),
            $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->data) + 1; // +1 for header
        $styles = [
            // Header styles (row 1)
            1 => [
                'font' => [
                    'name' => 'Arial',
                    'size' => 13,
                    'bold' => true,
                    'color' => ['rgb' => '333333'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'E9ECEF'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'DEE2E6'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Body styles (rows 2 to end)
            "A2:D{$rowCount}" => [
                'font' => [
                    'name' => 'Arial',
                    'size' => 13,
                    'color' => ['rgb' => '333333'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'DEE2E6'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];

        // Apply striped rows
        foreach ($this->data as $index => $row) {
            $rowNum = $index + 2; // Start from row 2
            if ($index % 2 === 0) {
                $sheet->getStyle("A{$rowNum}:D{$rowNum}")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F8F9FA'],
                    ],
                ]);
            }
        }

        return $styles;
    }

    private function firstUpper($text)
    {
        return $text ? ucwords(strtolower($text)) : '';
    }

    private function formatNumber($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $num = floatval(str_replace(',', '', $value));
        return is_nan($num) ? $value : number_format($num, 2, '.', ',');
    }
}