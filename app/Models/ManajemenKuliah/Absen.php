<?php

namespace App\Models\ManajemenKuliah;

use App\Models\KelolaPengguna\{Mahasiswa, Dosen};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ManajemenKuliah\Jadwal\{Kuliah as Jadwal};
use Spatie\MediaLibrary\InteractsWithMedia;

class Absen extends Model
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'absens';

    protected $fillable = [
        'mahasiswa_id',
        'jadwal_id',
        'dosen_id',
        'parent',
        'status',
        'rangkuman',
        'berita_acara',
        'pertemuan'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function getTanggalAttribute($value)
    {
        return Carbon::parse($this->attributes['created_at'])->translatedFormat('l, d F Y');
    }


}
