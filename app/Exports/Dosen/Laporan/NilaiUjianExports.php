<?php

namespace App\Exports\Dosen\Laporan;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\{WithStyles, WithHeadings, FromCollection, WithColumnWidths};

class NilaiUjianExports implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    protected $nilaiUjian;

    public function __construct($nilaiUjian)
    {
        $this->nilaiUjian = $nilaiUjian;
    }

    /**
     * @return array
     *
     */
    public function headings(): array
    {
        return [
            'Nama',
            'NIM',
            'Kelas',
            'Mata Kuliah',
            'Fakultas',
            'S 1',
            'S 2',
            'S 3',
            'S 4',
            'S 5',
            'S 6',
            'S 7',
            'S 8',
        ];
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
            'D' => 30,
            'E' => 30,
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
    public function styles(Worksheet $sheet)
    {
        $rowCount = $this->nilaiUjian->count() + 1; // +1 for heading
        $sheet->getStyle("F2:M$rowCount")->getAlignment()->setWrapText(true); // S1 s.d. S8 wrap text

        return [
            1 => ['font' => ['bold' => true]],
            "A2:E$rowCount" => ['alignment' => [ // Nama s.d. Fakultas
                'horizontal' => 'left',
                'vertical' => 'center'
            ]],
            "F2:M$rowCount" => ['alignment' => [ // S1 s.d. S8
                'horizontal' => 'right',
                'vertical' => 'center'
            ]],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->nilaiUjian;
    }
}
