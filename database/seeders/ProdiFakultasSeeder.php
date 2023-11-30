<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdiFakultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Fakultas
        $fakultas = collect([
            'Teknologi dan Informasi',
            'Ekonomi dan Bisnis',
            'Komunikasi dan Bahasa',
        ]);

        $fakultas->each(function ($fakultas) {
            $arr = explode(' ', $fakultas);
            $kode = '';

            foreach ($arr as $a) {
                // upper case substr
                $kode .= strtoupper(substr($a, 0, 1));
            }

            DB::table('fakultas')->insert([
                'kode' => $kode . '-' . rand(10, 99),
                'nama' => $fakultas,
            ]);
        });

        // Prodi
        $prodi = collect([
            'Rekayasa Perangkat Lunak',
            'Akutansi',
            'Ilmu Komunikasi'
        ]);

        $prodi->each(function ($prodi) {
            DB::table('prodis')->insert([
                'nama' => $prodi,
            ]);
        });

        // Prodi Fakultas relation
        DB::table('prodi_fakultas')->insert([
            [
                'prodi_id' => 1,
                'fakultas_id' => 1,
            ],
            [
                'prodi_id' => 2,
                'fakultas_id' => 2,
            ],
            [
                'prodi_id' => 3,
                'fakultas_id' => 3,
            ],
        ]);
    }
}
