<?php

namespace App\Exports\Dosen\Laporan;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\{FromView, WithStyles};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NilaiExport implements FromView, WithStyles
{
    protected $mahasiswas = [];

    public function __construct($mahasiswas)
    {
        $this->mahasiswas = $mahasiswas;
    }

    public function view(): View
    {
        return view('dashboard.dosen.laporan.nilai._table', [
            'mahasiswas' => $this->mahasiswas,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // all cells auto width size
        foreach(range('A',$sheet->getHighestColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A:Z')->getAlignment()->setWrapText(true);

        $sheet->getStyle('A1:Z1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:Z1')->getAlignment()->setVertical('center');

        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            '3:50' => ['alignment' => ['horizontal' => 'left']],
        ];
    }
}
