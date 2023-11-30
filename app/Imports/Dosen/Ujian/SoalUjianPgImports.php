<?php

namespace App\Imports\Dosen\Ujian;

use App\Models\ManajemenKuliah\Ujian\SoalUjianPg;
use App\Models\ManajemenKuliah\Ujian\Ujian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class SoalUjianPgImports implements WithHeadingRow, ToModel
{
    protected $ujian_id;

    public function __construct($ujian_id)
    {
        $this->ujian_id = $ujian_id;
    }

    public function import($file)
    {
        Excel::import(new SoalUjianPgImports($this->ujian_id), $file);
    }

    public function model(array $row)
    {
        $ujian = Ujian::find($this->ujian_id);
        static $nomer_soal = 1;

        if ($nomer_soal > 50) {
            return null;
        } else {
            return new SoalUjianPg([
                'nomer_soal' => $nomer_soal++,
                'pertanyaan' => html_entity_decode($row['pertanyaan']),
                'pilihan_a' => htmlStrips($row['pilihan_a']),
                'pilihan_b' => htmlStrips($row['pilihan_b']),
                'pilihan_c' => htmlStrips($row['pilihan_c']),
                'pilihan_d' => htmlStrips($row['pilihan_d']),
                'pilihan_e' => htmlStrips($row['pilihan_e']),
                'jawaban_benar' => trim($row['jawaban_benar']),
                'ujian_id' => $ujian->id,
            ]);
        }
    }
}
