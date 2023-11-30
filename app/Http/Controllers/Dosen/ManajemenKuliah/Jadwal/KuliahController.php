<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah\Jadwal;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\ManajemenKuliah\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // data jadwals dengan sortBy hari sekarang
        $jadwalHariIni = JadwalKuliah::with('matkul', 'kelas')
            ->where('dosen_id', Auth::user()->dosen->id)
            ->where('hari', hari_ini())
            ->orderBy(Kelas::select('kode')->whereColumn('id', 'kelas_id'), 'asc')
            ->paginate();

        $jadwals = JadwalKuliah::with('matkul', 'kelas')
            ->where('dosen_id', Auth::user()->dosen->id)
            ->where('hari', '!=', hari_ini())
            ->orderBy(Kelas::select('kode')->whereColumn('id', 'kelas_id'), 'asc')
            ->paginate(6);

        return view('dashboard.dosen.jadwal.kuliah', [
            'jadwalHariIni' => $jadwalHariIni,
            'jadwals' => $jadwals
        ]);
    }
}
