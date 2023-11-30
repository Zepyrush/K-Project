<?php

namespace App\Models\ManajemenKuliah\Ujian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalUjianEssay extends Model
{
    use HasFactory;

    protected $table = 'soal_ujian_essays';

    protected $fillable = [
        'nomer_soal',
        'pertanyaan',
        'ujian_id',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }
}
