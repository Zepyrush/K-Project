<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KelolaPengguna\Dosen;
use App\Models\ManajemenKuliah\Matkul;

class PivotDosenMatkulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $matkul = Matkul::all();

        foreach (Dosen::all() as $d) {
            $d->matkuls()->attach(
                $matkul->random(rand(1, $matkul->count()))->pluck('id')->take(3)->toArray()
            );
        }
    }
}
