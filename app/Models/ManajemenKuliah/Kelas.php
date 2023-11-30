<?php

namespace App\Models\ManajemenKuliah;

use App\Models\KelolaPengguna\{Mahasiswa, Dosen};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as Jadwal;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'kode',
    ];

    public function mhs()
    {
        return $this->hasMany(Mahasiswa::class, 'kelas_id');
    }

    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 'mahasiswa_kelas')->withTimestamps();
    }

    public function dosens()
    {
        return $this->belongsToMany(Dosen::class)->withTimestamps();
    }

    public function jadwal()
    {
        return $this->hasOne(Jadwal::class);
    }
}
