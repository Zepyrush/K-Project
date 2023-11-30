<?php

namespace Database\Seeders;

use App\Models\ManajemenKuliah\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create random kelas like 10AB2020
        for ($i=0; $i < 10; $i++) {
            Kelas::create([
                'kode' => chr(rand(65, 90)) . chr(rand(65, 90)) . '-' . rand(1000, 9999),
            ]);
        }
    }
}
