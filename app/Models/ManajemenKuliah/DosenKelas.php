<?php

namespace App\Models\ManajemenKuliah;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenKelas extends Model
{
    use HasFactory;

    protected $table = 'dosen_kelas';

    protected $fillable = [
        'dosen_id',
        'kelas_id',
        'matkul_id',
    ];
}
