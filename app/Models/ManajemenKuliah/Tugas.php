<?php

namespace App\Models\ManajemenKuliah;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KelolaPengguna\{Mahasiswa};
use App\Models\ManajemenKuliah\Jadwal\{Kuliah as Jadwal};
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Tugas extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [ 'jadwal_id', 'matkul_id', 'parent', 'judul', 'tipe', 'file_or_link', 'pertemuan', 'deskripsi', 'pengumpulan', 'sudah_dinilai', 'dosen_id', 'mahasiswa_id'];

    protected $with = ['nilaiTugas'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function parent()
    {
        return $this->belongsTo(Self::class, 'parent');
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class);
    }

    public function nilaiTugas()
    {
        return $this->hasOne(NilaiTugas::class, 'tugas_id', 'id');
    }

    public function kelas()
    {
        return $this->hasOneThrough(Kelas::class, Jadwal::class, 'id', 'id', 'jadwal_id', 'kelas_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}
