<?php

namespace App\Models\ManajemenKuliah\Ujian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalUjianPg extends Model
{
    use HasFactory;

    protected $table = 'soal_ujian_pgs';

    protected $fillable = [
        'nomer_soal',
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'pilihan_e',
        'jawaban_benar',
        'ujian_id',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function isJawaban($jawaban)
    {
        return $this->jawaban_benar == $jawaban;
    }
}
