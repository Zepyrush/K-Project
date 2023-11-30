<?php

namespace Database\Factories;

use App\Models\KelolaPengguna\Dosen;
use App\Models\ManajemenKuliah\{Matkul, Kelas};
use App\Models\ManajemenKuliah\Jadwal\Kuliah;
use App\Models\RolePermission\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KelolaPengguna\Dosen>
 */
class DosenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Dosen::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
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
            'password' => bcrypt($noInduk)
        ]);

        $user->roles()->attach(Role::where('name', 'dosen')->first());

        return [
            'nama' => $nama,
            'nip' => $noInduk,
            'kode' => Str::random(6),
            'email' => $email,
            'user_id' => $user->id,
        ];
    }

    public function withRelation()
    {
        $kelas = Kelas::all();
        $matkul = Matkul::all();

        return $this->afterCreating(function (Dosen $dosen) use ($matkul, $kelas) {
            if ($matkul->count() > 0) {
                $dosen->matkuls()->attach(
                    $matkul->filter(function ($matkul) use ($dosen) {
                        return !$dosen->matkuls->contains('id', $matkul->id);
                    })
                    ->random(rand(1, $matkul->count()))
                    ->pluck('id')
                    ->toArray()
                );
            }

            if ($kelas->count() > 0) {
                $dosen->kelas()->attach(
                    $kelas->random(rand(1, $kelas->count())
                )->pluck('id')->toArray());
            }
        });
    }
}
