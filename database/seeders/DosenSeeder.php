<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dosens')->insert([
            [
                "nama" => "Dosen",
                "nip" => "11111111",
                "kode" => "DSN",
                "email" => "dosen@email.com",
                "user_id" => 2,
            ]
        ]);
    }
}
