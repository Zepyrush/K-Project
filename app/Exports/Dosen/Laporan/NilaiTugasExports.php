<?php

namespace App\Exports\Dosen\Laporan;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\{WithStyles, WithHeadings, FromCollection, WithColumnWidths};

class NilaiTugasExports implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    protected $nilaiTugas;

    public function __construct($nilaiTugas)
    {
        $this->nilaiTugas = $nilaiTugas;
    }

    /**
     * @return array
     *
     */
    public function headings() : array
    {
        return [
            'Nama',
            'NIM',
            'Kelas',
            'Mata Kuliah',
            'Fakultas',
            'P 1',
            'P 2',
            'P 3',
            'P 4',
            'P 5',
            'P 6',
            'P 7',
            'P 8',
            'P 9',
            'P 10',
            'P 11',
            'P 12',
            'P 13',
            'P 14',
            'P 15',
            'P 16',
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
            'F' => 5,
            'G' => 5,
            'H' => 5,
            'I' => 5,
            'J' => 5,
            'K' => 5,
            'L' => 5,
            'M' => 5,
            'N' => 5,
            'O' => 5,
            'P' => 5,
            'Q' => 5,
            'R' => 5,
            'S' => 5,
            'T' => 5,
            'U' => 5,
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            '2:50' => ['alignment' => ['horizontal' => 'left']],
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->nilaiTugas;
    }
}
