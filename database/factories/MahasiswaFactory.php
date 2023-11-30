<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\ManajemenKuliah\{Fakultas,Kelas};
use App\Models\KelolaPengguna\Mahasiswa;
use App\Models\RolePermission\Role;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KelolaPengguna\Mahasiswa>
 *
 * Jalankan di php artisan tinker, dengan cara:
 * Mahasiswa::factory()->count(10)->create();
 */
class MahasiswaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Mahasiswa::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $noInduk = $this->faker->unique()->numerify('########');
        $nama = $this->faker->name;
        $email = $noInduk . '@mail.com';
        $foto = 'avatar.png';

        $user = User::factory()->create([
            'name' => $nama,
            'no_induk' => $noInduk,
            'email' => $email,
            'foto' => $foto,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'password' => bcrypt($noInduk),
        ]);

        $user->roles()->attach(Role::where('name', 'mahasiswa')->first());

        return [
            'nama' => $nama,
            'nim' => $noInduk,
            'email' => $email,
            'user_id' => $user->id,
            'fakultas_id' => Fakultas::all()->random()->id,
            'kelas_id' => Kelas::all()->random()->id,
        ];
    }
}
