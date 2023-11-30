<?php

namespace App\Models\ManajemenKuliah;

use App\Models\KelolaPengguna\Dosen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Materi extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'materis';

    protected $fillable = [
        'judul',
        'tipe',
        'file_or_link',
        'pertemuan',
        'deskripsi',
        'dosen_id',
        'kelas_id',
        'matkul_id',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class);
    }
}
