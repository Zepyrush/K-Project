<?php

namespace App\Http\Controllers\Mahasiswa\ManajemenKuliah;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\ManajemenKuliah\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jadwalHariIni = JadwalKuliah::with('matkul', 'kelas')
            ->where('kelas_id', Auth::user()->mahasiswa->kelas->id)
            ->where('hari', hari_ini())
            ->orderBy(Kelas::select('kode')->whereColumn('id', 'kelas_id'), 'asc')
            ->paginate();

        $jadwals = JadwalKuliah::with('matkul','kelas','dosen','tugas')
            ->where('kelas_id', Auth::user()->mahasiswa->kelas->id)
            ->where('hari', '!=', hari_ini())
            ->paginate(6);

        return view('dashboard.mahasiswa.jadwal', [
            'jadwals' => $jadwals,
            'jadwalHariIni' => $jadwalHariIni
        ]);
    }
}
