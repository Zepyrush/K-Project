<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah\Laporan;

use App\Exports\Dosen\Laporan\NilaiTugasExports;
use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\ManajemenKuliah\Kelas;
use App\Models\KelolaPengguna\Mahasiswa;
use App\Models\ManajemenKuliah\Matkul;
use App\Models\NilaiTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TugasController extends Controller
{
    public function nilaiTugas()
    {
        $jadwals = JadwalKuliah::where('dosen_id', Auth::user()->dosen->id)
            ->whereHas('tugas')
            ->orderBy(Kelas::select('kode')->whereColumn('id', 'kelas_id'), 'asc')
            ->get();

        return view('dashboard.dosen.laporan.nilai_tugas.index', [
            'jadwals' => $jadwals,
        ]);
    }

    public function fetchDataNilai(Request $request)
    {
        if (request()->ajax()) {
            $key = explode('_', $request->key_id);
            $kelas_id = $key[0];
            $matkul_id = $key[1];
            $dosen_id = $key[2];

            $jadwal = JadwalKuliah::where('kelas_id', $kelas_id)
                ->where('matkul_id', $matkul_id)
                ->first();

            $output = view('dashboard.dosen.laporan.nilai_tugas._data-nilai', [
                'jadwal' => $jadwal,
                'kelas_id' => $kelas_id,
                'matkul_id' => $matkul_id,
            ])->render();

            return response()->json($output);
        } else {
            abort(404);
        }
    }

    public function tableDataNilai(Request $request)
    {
        if (request()->ajax()) {
            $key = explode('_', $request->key_id);
            $kelas_id = $key[0];
            $matkul_id = $key[1];
            $kelasArray = [];

            $jadwals = JadwalKuliah::with([
                'kelas' => fn ($q) => $q->with('mahasiswa'),
                'matkul'
            ])
                ->where('kelas_id', $kelas_id)
                ->where('matkul_id', $matkul_id)
                ->where('dosen_id', Auth::user()->dosen->id)
                ->get();

            foreach ($jadwals as $key => $kls) {
                $kelasArray[$key] = $kls;
            }
            /**
             * Jika ingin mengurutkan berdasarkan nilai tertinggi.
             * ->orderBy(NilaiTugas::select('nilai')->whereColumn('mahasiswa_id', 'mahasiswas.id'), 'desc')
             */
            $mahasiswas = Mahasiswa::with('tugas', 'user')
                ->whereIn('kelas_id', collect($kelasArray)->pluck('kelas_id'))
                ->orderBy('nama', 'asc')
                ->get();

            $data = $mahasiswas->transform(function ($item) {
                $item->foto = $item->user->foto;
                return $item;
            });

            $table = datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('mahasiswa', function ($data) {
                    if (file_exists('assets/image/users/' . $data->foto)) {
                        $avatar = asset('assets/image/users/' . $data->foto);
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
                });

            for ($i = 1; $i <= 14; $i++) { // 14 adalah jumlah pertemuan
                // $i adalah pertemuan ke-
                $table->addColumn("p$i", function ($data) use ($i, $matkul_id) {

                    $nilai = '-'; // nilai default

                    foreach ($data->tugas as $tugas) { // looping tugas mahasiswa
                        if ($tugas->pertemuan == $i)  { // jika pertemuan sama dengan $i
                            if ($matkul_id == $tugas->matkul_id) { // jika matkul_id sama dengan $matkul_id
                                if ($tugas->nilaiTugas) { // jika nilai tugas ada
                                    $nilai = $tugas->nilaiTugas->nilai;
                                } else { // jika nilai tugas tidak ada
                                    $nilai = 0;
                                }
                            }
                        }
                    }

                    return $nilai;
                });
            }

            $pertemuan = array_fill(0, 14, '');
            foreach ($pertemuan as $key => $value) {
                $pertemuan[$key] = "p" . ($key + 1);
            }

            return $table->rawColumns(array_merge(['mahasiswa'], $pertemuan))
                ->make(true);
        } else {
            abort(404);
        }
    }

    public function exportNilaiTugas($kelas, $matkul)
    {
        $kelas = Kelas::find(decrypt($kelas));
        $matkul = Matkul::find(decrypt($matkul));

        $auth = Auth::user()->dosen;

        $mahasiswa = Mahasiswa::with(['tugas' => function ($q) use ($auth) {
            $q->whereIn('parent', $auth->tugas->pluck('id'))
                ->select('id', 'mahasiswa_id', 'pertemuan', 'matkul_id');
        }, 'tugas.matkul', 'tugas.nilaiTugas', 'kelas'])
            ->where('kelas_id', $kelas->id)
            ->orderBy('nama', 'asc')
            ->get();

        foreach ($mahasiswa as $i => $mhs) {

            $dataMhs[$i] = [
                'nama' => $mhs->nama,
                'nim' => $mhs->nim,
                'kelas' => $mhs->kelas->kode,
                'matkul' => $matkul->nama,
                'fakultas' => $mhs->fakultas->nama,
            ];

            for ($j = 1; $j <= 14; $j++) {
                $dataMhs[$i]["p$j"] = '-'; // nilai default

                foreach ($mhs->tugas as $tugas) { // looping tugas mahasiswa
                    if ($tugas->matkul_id == $matkul->id) { // jika matkul_id sama dengan $matkul_id
                        if ($tugas->pertemuan == $j) { // jika pertemuan sama dengan $i
                            if ($tugas->nilaiTugas) { // jika nilai tugas ada
                                $dataMhs[$i]["p$j"] = $tugas->nilaiTugas->nilai;
                            } else {
                                $dataMhs[$i]["p$j"] = '0'; // jika nilai tugas tidak ada
                            }
                        }
                    }
                }
            }
        }

        ob_end_clean();

        $fileName = 'laporan_nilai_tugas_kelas_' . $dataMhs[0]['kelas'] .
            '_' . Str::slug($dataMhs[0]['matkul'], '_');

        return Excel::download(new NilaiTugasExports(collect($dataMhs)), $fileName . '.xlsx');
    }
}
