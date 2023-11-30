<?php

namespace App\Models\ManajemenKuliah\Ujian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianHasil extends Model
{
    use HasFactory;

    protected $table = 'ujian_mahasiswa_hasils';

    protected $fillable = [
        'ujian_mahasiswa_id',
        'soal_ujian_pg_id',
        'soal_ujian_essay_id',
        'jawaban',
        'ragu',
        'status',
        'dosen_id',
        'komentar_dosen',
        'skor'
    ];

    public function ujianMahasiswa()
    {
        return $this->belongsTo(UjianMahasiswa::class, 'ujian_mahasiswa_id');
    }

    public function soalUjianPg()
    {
        return $this->belongsTo(SoalUjianPg::class, 'soal_ujian_pg_id');
    }

    public function soalUjianEssay()
    {
        return $this->belongsTo(SoalUjianEssay::class, 'soal_ujian_essay_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}
