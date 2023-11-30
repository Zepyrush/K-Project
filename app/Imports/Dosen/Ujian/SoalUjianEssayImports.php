<?php

namespace App\Imports\Dosen\Ujian;

use App\Models\ManajemenKuliah\Ujian\SoalUjianEssay;
use App\Models\ManajemenKuliah\Ujian\Ujian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class SoalUjianEssayImports implements WithHeadingRow, ToModel
{
    protected $ujian_id;

    public function __construct($ujian_id)
    {
        $this->ujian_id = $ujian_id;
    }

    public function import($file)
    {
        Excel::import(new SoalUjianEssayImports($this->ujian_id), $file);
    }

    public function model(array $row)
    {
        $ujian = Ujian::find($this->ujian_id);
        static $nomer_soal = 1;

        if ($nomer_soal > 50) {
            return null;
        } else {
            return new SoalUjianEssay([
                'nomer_soal' => $nomer_soal++,
                'pertanyaan' => html_entity_decode($row['pertanyaan']),
                'ujian_id' => $ujian->id,
            ]);
        }
    }
}
