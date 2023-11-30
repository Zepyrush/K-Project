<?php

namespace App\Models\ManajemenKuliah;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    use HasFactory;

    protected $table = 'matkuls';

    protected $fillable = [
        'kode',
        'nama',
        'sks',
    ];

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function dosens()
    {
        return $this->belongsToMany(Dosen::class, 'dosen_matkul');
    }

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }
}
