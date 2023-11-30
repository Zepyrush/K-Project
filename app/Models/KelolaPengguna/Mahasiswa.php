<?php

namespace App\Models\KelolaPengguna;

use App\Models\User;
use App\Models\ManajemenKuliah\{Kelas, Fakultas, Absen, Tugas};
use App\Models\ManajemenKuliah\Ujian\{ UjianMahasiswa};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Mahasiswa extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'nama',
        'nim',
        'email',
        'user_id',
        'fakultas_id',
        'kelas_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mahasiswa_kelas()
    {
        return $this->belongsToMany(Kelas::class, 'mahasiswa_kelas')->withTimestamps();
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->translatedFormat('l, d F Y');
    }

    public function absens()
    {
        return $this->hasMany(Absen::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function ujianMahasiswa()
    {
        return $this->hasMany(UjianMahasiswa::class);
    }

    public function presensiHariIni()
    {
        return $this->hasOne(Absen::class)
            ->whereNotNull('mahasiswa_id')
            ->where('parent', '!=', 0)
            ->where('status', 1)
            ->whereDate('created_at', date('Y-m-d'));
    }

    public function presensi($jadwal_id)
    {
        return $this->absens()->whereNotNull('mahasiswa_id')
            ->where('parent', '!=', 0)
            ->where('status', 1)
            ->where('jadwal_id', $jadwal_id)
            ->whereDate('created_at', date('Y-m-d'));
    }

    public function mengerjakanUjian($ujian_id)
    {
        return $this->ujianMahasiswa()
            ->where('ujian_id', $ujian_id)
            ->where('status', 1);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new \Database\Factories\MahasiswaFactory();
    }
}
