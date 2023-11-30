<?php

namespace App\Models\ManajemenKuliah\Jadwal;

use App\Models\ManajemenKuliah\{Absen, Kelas, Matkul, Materi, NilaiTugas, Tugas};
use App\Models\KelolaPengguna\{Dosen, User};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Kuliah extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'jadwals';

    protected $fillable = [
        'hari',
        'started_at',
        'ended_at',
        'kelas_id',
        'matkul_id',
        'dosen_id',
    ];

    public $timestamps = false;

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function absens()
    {
        return $this->hasMany(Absen::class, 'jadwal_id');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'jadwal_id');
    }
}
