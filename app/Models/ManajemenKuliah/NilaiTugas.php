<?php

namespace App\Models\ManajemenKuliah;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class NilaiTugas extends Model
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'nilai_tugas';

    protected $fillable = [
        'nilai',
        'komentar',
        'tugas_id',
        'dosen_id',
        'mahasiswa_id',
    ];
}
