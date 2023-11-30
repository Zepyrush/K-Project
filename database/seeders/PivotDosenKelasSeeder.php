<?php

namespace Database\Seeders;

use App\Models\KelolaPengguna\Dosen;
use App\Models\ManajemenKuliah\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PivotDosenKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kelas = Kelas::all();
        $dosen = Dosen::all();

        foreach ($dosen as $dsn) {
            $dsn->kelas()->attach(
                $kelas->random(rand(1, $kelas->count()))->pluck('id')->take(3)->toArray()
            );

            foreach ($kelas as $kls) {
                $dsn->kelas()->updateExistingPivot(
                    ['kelas_id' => $kls->id],
                    ['matkul_id' => $dsn->matkuls->random()->id],
                );
            }
        }
    }
}
