<?php

namespace Database\Seeders;

use App\Models\KelolaPengguna\Mahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Mahasiswa::create([
            "nama" => "Mahasiswa",
            "nim" => "22222222",
            "email" => "mahasiswa@email.com",
            "user_id" => 3,
            "fakultas_id" => 1,
            "kelas_id" => 1
        ]);
    }
}
