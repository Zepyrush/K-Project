<?php

namespace Database\Seeders;

use App\Models\ManajemenKuliah\Kelas;
use App\Models\KelolaPengguna\Mahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PivotMahasiswaKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mahasiswa = Mahasiswa::all();

        foreach ($mahasiswa as $mhs) {
            $mhs->mahasiswa_kelas()->attach($mhs->kelas_id);
        }
    }
}
