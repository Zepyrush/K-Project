<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Absen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\ManajemenKuliah\Jadwal\Ujian as JadwalUjian;
use App\Models\ManajemenKuliah\Ujian\Ujian;
use App\Models\ManajemenKuliah\Ujian\UjianMahasiswa;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->isDosen()) {
            // absen
            $absens = Absen::with('jadwal')->where([
                ['dosen_id', Auth::user()->dosen->id],
                ['parent', 0]
            ])->whereDate('created_at', Carbon::today())
                ->latest()
                ->get();

            // jadwal kuliah
            $jadwals = JadwalKuliah::with('matkul', 'kelas')
                ->where('dosen_id', Auth::user()->dosen->id)
                ->get();

            // jadwal ujian yang belum dibuat ujian
            $buatUjians = JadwalUjian::with('ujian')
                ->whereDoesntHave('ujian')
                ->where('dosen_id', Auth::user()->dosen->id)
                ->get();

            // ujian
            $ujians = JadwalUjian::join('ujians', 'ujians.id', '=', 'jadwal_ujians.ujian_id')
                ->join('ujian_mahasiswas', 'ujian_mahasiswas.ujian_id', '=', 'ujians.id')
                ->where('jadwal_ujians.dosen_id', Auth::user()->dosen->id)
                ->where('status', '1')
                ->where('nilai', null)
                ->get();

            // jadwal kuliah hari ini
            $jadwalHariIni = JadwalKuliah::with('absens')
                ->where('dosen_id', Auth::user()->dosen->id)
                ->where('hari', hari_ini())
                ->whereTime('started_at', '<=', date("H:i"))
                ->whereTime('ended_at', '>=', date("H:i"))
                ->get();

            return view('dashboard.dosen.dashboard', [
                'kuliah' => Auth::user()->dosen->jadwalKuliah->count(),
                'ujian' => Auth::user()->dosen->ujian->count(),
                'materi' => Auth::user()->dosen->materis->count(),
                'tugas' => Auth::user()->dosen->tugas->count(),
                'matkul' => Auth::user()->dosen->matkuls->count(),
                'absens' => $absens,
                'jadwals' => $jadwals,
                'ujians' => $ujians,
                'jadwalUjian' => $buatUjians,
                'jadwalHariIni' => $jadwalHariIni,
            ]);

        } else {
            abort(404);
        }
    }
}
