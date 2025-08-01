<?php

namespace App\Exports\Reports\Coverage;

// use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoverByTypesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return DB::table('insurance_covers')
        //     ->whereBetween('date_offered', ['2025-01-01', '2025-01-31'])
        //     ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'TYPE CODE',
            'COVER TYPE',
            'COVER NO',
            'COVER TITLE',
            'GROUP',
            'CEDANT',
            'INSURED',
            'CURRENCY',
            'CLASS',
            'DATE OFFERED',
            'START DATE',
            'END DATE',
            'OUR SHARE (%)'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->type_code,
            $row->cover_type,
            $row->cover_no,
            $row->cover_title,
            $row->group,
            $row->cedant,
            $row->insured,
            $row->currency,
            $row->class,
            $row->date_offered,
            $row->start_date,
            $row->end_date,
            $row->our_share . '%'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
