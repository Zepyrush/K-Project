<?php

namespace Database\Seeders;

use App\Models\KelolaPengguna\Dosen;
use App\Models\ManajemenKuliah\DosenKelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ManajemenKuliah\Jadwal\Ujian as JadwalUjian;
use Carbon\Carbon;

class JadwalUjianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dosen = DosenKelas::all();

        foreach ($dosen as $key => $val) {
            $today = Carbon::today();
            $randomDate = $today->addDays(rand(0, 15)); // 15 hari kedepan
            $date = $randomDate->format('Y-m-d');

            $hour = rand(00, 23);
            $minute = rand(00, 59);
            $started_at_hour = $hour + 1 > 23 ? 23 : $hour + 1; // 1 jam
            $started_at_minute = $minute;

            ($hour < 10) ? $hour = '0' . $hour : $hour;
            ($minute < 10) ? $minute = '0' . $minute : $minute;
            ($started_at_hour < 10) ? $started_at_hour = '0' . $started_at_hour :
                $started_at_hour;
            ($started_at_minute < 10) ? $started_at_minute = '0' . $started_at_minute :
                $started_at_minute;

            JadwalUjian::create([
                'tanggal_ujian' => $date,
                'status_ujian' => 'draft',
                'dosen_can_manage' => strval(rand(0, 1)),
                'started_at' => $hour . ':' . $minute,
                'ended_at' => $started_at_hour . ':' . $started_at_minute,
                'dosen_id' => $val->dosen_id,
                'kelas_id' => $val->kelas_id,
                'matkul_id' => $val->matkul_id,
            ]);
        }
    }
}
