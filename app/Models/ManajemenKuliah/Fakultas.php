<?php

namespace App\Models\ManajemenKuliah;

use App\Models\KelolaPengguna\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    use HasFactory;

    protected $table = 'fakultas';

    protected $fillable = [
        'nama',
        'kode',
    ];

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'fakultas_id');
    }

    public function prodi()
    {
        return $this->hasMany(Prodi::class);
    }
}
