<?php

namespace Database\Seeders;

use App\Models\ManajemenKuliah\Matkul;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatkulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $matkuls = collect([
            'Dasar Pemrograman',
            'Matematika Diskrit',
            'Bahasa Inggris',
            'Struktur Database',
            'Logika dan Algoritma',
            'Pemrograman Web',
            'Pengantar Bisnis',
            'Ekonomi Pembangunan',
            'Manajemen Perbankan',
            'Fotografi',
            'Desain Media',
            'Manajemen Media',
        ]);

        $matkuls->each(function ($matkul) {
            $arr = explode(' ', $matkul);
            $kode = '';

            foreach ($arr as $a) {
                // upper case substr
                $kode .= strtoupper(substr($a, 0, 1));
            }

            Matkul::create([
                'kode' => $kode . '-' . rand(10, 99),
                'nama' => $matkul,
                'sks' => rand(1,4),
            ]);
        });
    }
}
