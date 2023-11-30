<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\KelolaPengguna\{Dosen, Mahasiswa};
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'no_induk',
        'foto',
        'email',
        'password',
        'email_verified_at',
        'last_seen',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id', 'id');
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id', 'id');
    }

    public function getRole()
    {
        return $this->roles->pluck('name')->first();
    }

    public function dosenKelas()
    {
        return $this->hasMany(Dosen::class, 'dosen_id', 'id');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isDosen()
    {
        return $this->hasRole('dosen');
    }

    public function isMahasiswa()
    {
        return $this->hasRole('mahasiswa');
    }

    public function isOnline($id)
    {
        return Cache::has('is_online' . $id);
    }
}
