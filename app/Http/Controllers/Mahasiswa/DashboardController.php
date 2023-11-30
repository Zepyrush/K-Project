<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\ManajemenKuliah\Jadwal\Ujian as JadwalUjian;
use App\Models\ManajemenKuliah\Ujian\Ujian;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->isMahasiswa()) {

            $jadwals = JadwalKuliah::with('matkul', 'kelas', 'dosen', 'tugas')
                ->where('kelas_id', Auth::user()->mahasiswa->kelas->id)
                ->get();

            $jadwalHariIni = JadwalKuliah::with('matkul', 'kelas')
                ->where('kelas_id', Auth::user()->mahasiswa->kelas->id)
                ->where('hari', hari_ini())
                ->whereTime('started_at', '<=', date("H:i"))
                ->whereTime('ended_at', '>=', date("H:i"))
                ->get();

            $riwayatUjian = JadwalUjian::aktif()
                ->with('ujian', 'kelas', 'matkul')
                ->where('kelas_id', Auth::user()->mahasiswa->kelas->id)
                ->whereHas('ujian', function ($q) {
                    $q->whereHas('ujianMahasiswa', function ($q) {
                        $q->where('status', 1)->where('mahasiswa_id', Auth::user()->mahasiswa->id);
                    });
                })
                ->latest()
                ->get();

            $jadwalUjian = JadwalUjian::aktif()
                ->with(['ujian' => function ($q) {
                    $q->with(['ujianMahasiswa' => function ($q) {
                        $q->where('mahasiswa_id', Auth::user()->mahasiswa->id)->where('status', 0);
                    }]);
                }, 'kelas', 'matkul'])
                ->where('kelas_id', Auth::user()->mahasiswa->kelas->id)
                ->whereHas('ujian', function ($q) {
                    $q->whereHas('ujianMahasiswa', function ($q) {
                        $q->where('status', 0)->where('mahasiswa_id', Auth::user()->mahasiswa->id);
                    });
                })
                ->orderBy(Ujian::select('id')->whereColumn('ujian_id', 'ujians.id')
                    ->orderBy("semester", "desc")->limit(1), "asc")
                ->get();

            return view('dashboard.mahasiswa.dashboard', [
                'jadwals' => $jadwals,
                'jadwalHariIni' => $jadwalHariIni,
                'jadwalUjian' => $jadwalUjian,
                'riwayatUjian' => $riwayatUjian,
            ]);
        } else {
            abort(404);
        }
    }
}
