<?php

namespace App\Models\ManajemenKuliah\Ujian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KelolaPengguna\Mahasiswa;

class UjianMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'ujian_mahasiswas';

    public $timestamps = false;

    protected $fillable = [
        'ujian_id',
        'mahasiswa_id',
        'started_at',
        'ended_at',
        'nilai',
        'user_agent',
        'status',
        'ip_address',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function ujianHasil()
    {
        return $this->hasMany(UjianHasil::class, 'ujian_mahasiswa_id');
    }
}
