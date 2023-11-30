<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\KelolaPengguna\Dosen;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolePermissionSeeder::class,
            KelasSeeder::class,
            MatkulSeeder::class,
            UserSeeder::class,
            DosenSeeder::class,
            ProdiFakultasSeeder::class,
            MahasiswaSeeder::class,
            PivotMahasiswaKelasSeeder::class,
            PivotDosenMatkulSeeder::class,
            PivotDosenKelasSeeder::class,
            JadwalKuliahSeeder::class,
            JadwalUjianSeeder::class,
        ]);

    }
}
