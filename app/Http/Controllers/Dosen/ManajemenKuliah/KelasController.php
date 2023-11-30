<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Absen;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\KelolaPengguna\Mahasiswa;
use App\Models\ManajemenKuliah\Materi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $jadwal = JadwalKuliah::with('kelas', 'matkul', 'kelas')
            ->where('dosen_id', Auth::user()->dosen->id)
            ->where('id', decrypt($id))
            ->first();

        $jamMasuk = jam_sekarang() >= $jadwal->started_at && jam_sekarang() <= $jadwal->ended_at && $jadwal->hari == hari_ini();

        if ($jamMasuk) { // jika jam masuk

            $absen = Absen::where('dosen_id', Auth::user()->dosen->id)
                ->where('jadwal_id', $jadwal->id)
                ->whereDate('created_at', now())
                ->first();

            // notif jika ada tugas yang sudah dikumpulkan oleh mahasiswa
            $tugas_belum_dinilai = $jadwal->tugas()
                ->whereDoesntHave('nilaiTugas')
                ->where('file_or_link', '!=', null)
                ->where('created_at', '>=', Carbon::today())
                ->where('parent', '!=', 0)
                ->count();

            ($tugas_belum_dinilai > 0) ? $count = $tugas_belum_dinilai : $count = 0; // jika ada tugas yang belum dinilai

            $mahasiswa = Mahasiswa::with(['presensiHariIni' => function ($query) use ($jadwal) {
                $query->where('jadwal_id', $jadwal->id);
            }, 'user'])->where('kelas_id', $jadwal->kelas->id)
                ->orderBy('nama', 'asc')
                ->get();

            $mahasiswaHadir = $mahasiswa->where('presensiHariIni', '!=', null)->count();
            $mahasiswaTidakHadir = $mahasiswa->where('presensiHariIni', null)->count();

            if (request()->ajax()) {
                return datatables()->of($mahasiswa)
                    ->addColumn('mahasiswa', function ($data) {
                        if (file_exists('assets/image/users/' . $data->user->foto)) {
                            $avatar = asset('assets/image/users/' . $data->user->foto);
                        } else {
                            $avatar = asset('assets/image/avatar.png');
                        }

                        return '
                            <a href="javascript:void(0)" class="d-flex align-items-center" style="cursor: default">
                                <img src="' . $avatar . '" width="40" class="avatar rounded-circle me-3">
                                <div class="d-block ml-3">
                                    <span class="fw-bold name-user">' . $data->nama . '</span>
                                    <div class="small text-secondary">' . $data->nim . '</div>
                                </div>
                            </a>
                        ';
                    })
                    ->addColumn('status', function ($data) {
                        $presensi = $data->presensiHariIni ? 'checked' : '';
                        $absensi = !$data->presensiHariIni ? 'checked' : '';

                        $output = '';
                        $output .= '<input type="hidden" value="' . $data->id . '" name="mahasiswa[]">';
                        $output .= '
                            <div class="form-check">
                                <div class="d-flex">
                                    <input id="hadir' . $data->id . '" class="form-check cursor_p"
                                        type="radio" value="1"
                                        name="status[]' . $data->id . '" ' . $presensi . '>
                                    <label class="form-check-label ml-1 cursor_p"
                                        for="hadir' . $data->id . '">
                                        Hadir
                                    </label>
                                </div>

                                <div class="d-flex">
                                    <input id="tHadir' . $data->id . '" class="form-check cursor_p"
                                        type="radio" value="0" name="status[]' . $data->id . '" ' . $absensi . '>
                                    <label class="form-check-label ml-1 cursor_p"
                                        for="tHadir' . $data->id . '">
                                        Tidak Hadir
                                    </label>
                                </div>
                            </div>
                        ';

                        return $output;
                    })
                    ->rawColumns(['mahasiswa', 'status'])
                    ->addIndexColumn()
                    ->make(true);
            }

            return view('dashboard.dosen.kelas', [
                'jadwal' => $jadwal,
                'mahasiswa' => $mahasiswa,
                'mahasiswaHadir' => $mahasiswaHadir,
                'mahasiswaTidakHadir' => $mahasiswaTidakHadir,
                'absen' => $absen,
                'absen_has_created' => $jadwal->absens->where('created_at', '>=', Carbon::today())->count() > 0,
                'tugas_has_created' => $jadwal->tugas->where('created_at', '>=', Carbon::today())->count() > 0,
                'tugas_belum_dinilai' => $count,
            ]);
        } else {

            $msg = 'Silahkan masuk pada waktu yang telah ditentukan!';

            return redirect()->back()->with('error', $msg);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeKehadiran(Request $request)
    {
        $absen = collect(Absen::where('dosen_id', Auth::user()->dosen->id)
            ->where('jadwal_id', $request->jadwal)
            ->whereDate('created_at', date('Y-m-d'))
            ->first());

        if (request()->ajax()) {

            if ($absen->isNotEmpty()) {
                DB::beginTransaction();
                try {
                    for ($i = 0; $i < count($request->mahasiswa); $i++) { // loop data mahasiswa
                        Absen::updateOrCreate(
                            [ // jika data sudah ada maka update
                                'mahasiswa_id' => $request->mahasiswa[$i],
                                'parent' => $absen['id'],
                            ],
                            [ // jika data belum ada maka buat baru
                                'parent' => $request->parent,
                                'status' => $request->status[$i],
                                'jadwal_id' => $request->jadwal,
                                'pertemuan' => $request->pertemuan,
                            ]
                        );
                    }

                    return response()->json([
                        'status' => 200,
                        'message' => 'Berhasil menyimpan data kehadiran!',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollback();

                    return response()->json([
                        'status' => 500,
                        'message' => 'Gagal menyimpan data kehadiran! <hr> Pesan: ' . $th->getMessage(),
                    ]);
                } finally {
                    DB::commit();
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Anda belum membuat absensi untuk pertemuan ini. Silahkan buat absensi terlebih dahulu!'
                ]);
            }
        }
    }

    public function infoKehadiranMhs($jadwalId)
    {
        $jadwal = JadwalKuliah::with('kelas', 'matkul')
            ->where('id', decrypt($jadwalId))
            ->where('dosen_id', Auth::user()->dosen->id)
            ->firstOrFail();

        $mahasiswa = Mahasiswa::with(['presensiHariIni' => function ($query) use ($jadwal) {
            $query->where('jadwal_id', $jadwal->id);
        }])->where('kelas_id', $jadwal->kelas->id)
            ->orderBy('nama', 'asc')
            ->get();

        $mahasiswaHadir = $mahasiswa->where('presensiHariIni', '!=', null)->count();
        $mahasiswaTidakHadir = $mahasiswa->where('presensiHariIni', null)->count();

        if (request()->ajax()) {
            $output = view('dashboard.dosen._kelas._info-kehadiran-mhs', [
                'mahasiswa' => $mahasiswa->count(),
                'mahasiswaHadir' => $mahasiswaHadir,
                'mahasiswaTidakHadir' => $mahasiswaTidakHadir,
            ])->render();

            return response()->json($output, 200);
        } else {
            abort(404);
        }
    }
}
