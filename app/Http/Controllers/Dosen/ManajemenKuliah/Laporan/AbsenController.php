<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah\Laporan;

use App\Exports\Dosen\Laporan\AbsensiExports;
use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\ManajemenKuliah\Kelas;
use App\Models\KelolaPengguna\Mahasiswa;
use App\Models\ManajemenKuliah\Matkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AbsenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function absen()
    {
        $jadwals = JadwalKuliah::where('dosen_id', Auth::user()->dosen->id)
            ->whereHas("kelas.mahasiswa")
            ->orderBy(Kelas::select('kode')->whereColumn('id', 'kelas_id'), 'asc')
            ->get();

        return view('dashboard.dosen.laporan.absen.index', [
            'jadwals' => $jadwals
        ]);
    }

    public function fetchDataAbsen(Request $request)
    {
        if (request()->ajax()) {
            $key = explode('_', $request->key_id); // Kelas_id_matkul_id
            $kelas_id = $key[0]; // Kelas_id
            $matkul_id = $key[1]; // Matkul_id

            $jadwal = JadwalKuliah::where('kelas_id', $kelas_id)
                ->where('matkul_id', $matkul_id)
                ->first();

            $output = view('dashboard.dosen.laporan.absen._data-nilai', [
                'jadwal' => $jadwal,
                'kelas_id' => $kelas_id,
                'matkul_id' => $matkul_id,
            ])->render();

            return response()->json($output);
        } else {
            abort(404);
        }
    }

    public function tableDataAbsen(Request $request)
    {
        if (request()->ajax()) {
            $key = explode('_', $request->key_id);
            $kelas_id = $key[0];
            $matkul_id = $key[1];
            $kelasArray = []; // Array untuk menampung data kelas

            $jadwals = JadwalKuliah::with(['kelas' => function ($q) {
                $q->with('mahasiswa');
            }, 'matkul'])
                ->where('kelas_id', $kelas_id)
                ->where('matkul_id', $matkul_id)
                ->where('dosen_id', Auth::user()->dosen->id)
                ->get();


            foreach ($jadwals as $key => $kls) { // Looping untuk mengambil data kelas
                $kelasArray[$key] = $kls; // Menampung data kelas ke dalam array
            }

            /**
             * Jika ingin mengurutkan berdasarkan nilai tertinggi.
             * ->orderBy(NilaiTugas::select('nilai')->whereColumn('mahasiswa_id', 'mahasiswas.id'), 'desc')
             */
            $mahasiswas = Mahasiswa::with(['tugas', 'user', 'absens' => function ($q) {
                $q->with('jadwal');
            }])
                ->whereIn('kelas_id', collect($kelasArray)->pluck('kelas_id'))
                ->orderBy('nama', 'asc')
                ->get();

            $data = $mahasiswas->transform(function ($item) { // Transformasi data
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

            for ($i = 1; $i <= 16; $i++) { // Looping untuk menambahkan kolom pertemuan 1 - 16
                $table->addColumn("p$i", function ($data) use ($i, $matkul_id) {
                    $absn = '-';

                    if ($data->absens->isNotEmpty()) { // Jika data absen tidak kosong
                        foreach ($data->absens as $absen) { // Looping untuk mengambil data absen
                            // Jika pertemuan sama dengan data pertemuan dan matkul_id sama dengan data matkul_id
                            if ($absen->pertemuan == $i && $absen->jadwal->matkul_id == $matkul_id) {
                                $absn = $absen->status ?
                                    '<span class="text-success">✓</span>' :
                                    '<span class="text-danger">✗</span>';
                            }
                        }
                    }

                    return $absn;
                });
            }

            $table->addColumn("total_hadir", function ($data) use ($matkul_id) {
                $total_hadir = 0;

                if ($data->absens->isNotEmpty()) { // Jika data absen tidak kosong
                    foreach ($data->absens as $absen) { // Looping untuk mengambil data absen
                        // Jika status absen sama dengan 1 dan matkul_id sama dengan data matkul_id
                        if ($absen->status == 1 && $absen->jadwal->matkul_id == $matkul_id) {
                            $total_hadir++;
                        }
                    }
                }

                return $total_hadir;
            });

            $pertemuan = array_fill(0, 16, ''); // Array untuk menampung data pertemuan
            foreach ($pertemuan as $key => $value) { // Looping untuk mengubah data pertemuan
                $pertemuan[$key] = "p" . ($key + 1); // Menambahkan huruf p di depan data pertemuan
            }

            return $table->rawColumns(array_merge(['mahasiswa', 'total_hadir'], $pertemuan))
                ->make(true); // Membuat data menjadi json

        } else {
            abort(404);
        }
    }

    /**
     * Export to excel.
     *
     * @return \Illuminate\Http\Response
     * @param Kelas $kelas
     * @param Matkul $matkul
     */
    public function exports($kelas_id, $matkul_id)
    {
        $kelas = Kelas::find(decrypt($kelas_id));
        $matkul = Matkul::find(decrypt($matkul_id));
        $auth = Auth::user()->dosen;

        $mahasiswa = Mahasiswa::with(['absens' => function ($q) use ($auth) { // Mengambil data absen
            $q->whereIn('parent', $auth->absens->pluck('id'));
        }, 'kelas', 'fakultas'])
            ->where('kelas_id', $kelas->id)
            ->orderBy('nama', 'asc')
            ->get();

        foreach ($mahasiswa as $i => $mhs) { // Looping untuk mengubah data absen
            $dataMhs[$i] = [ // Menampung data mahasiswa ke dalam array
                'nama' => $mhs->nama,
                'nim' => $mhs->nim,
                'kelas' => $mhs->kelas->kode,
                'matkul' => $matkul->nama,
                'fakultas' => $mhs->fakultas->nama,
            ];

            $total_hadir = 0; // Menampung total hadir

            for ($j = 1; $j <= 16; $j++) // Looping untuk menambahkan kolom pertemuan
            {
                $dataMhs[$i]["p$j"] = '-'; // Menampung data absen ke dalam array

                if (!$mhs->absens->isEmpty()) { // Jika data absen tidak kosong
                    foreach ($mhs->absens as $absen) // Looping untuk menampilkan data absen
                    {
                        // Jika pertemuan sama dengan $j dan matkul_id sama dengan $matkul_id
                        if ($absen->pertemuan == $j && $absen->jadwal->matkul_id == $matkul->id)
                        {
                            $dataMhs[$i]["p$j"] = $absen->status ? 'v' : 'x'; // Menampung data absen ke dalam array
                            $total_hadir = $absen->status ? $total_hadir + 1 : $total_hadir; // Menampung total hadir
                        }
                    }
                }
            }

            $dataMhs[$i]['total_hadir'] = $total_hadir; // Menampung total hadir ke dalam array
        }

        ob_end_clean(); // menghapus semua isi buffer output

        $kelas = $dataMhs[0]['kelas']; // Mengambil data kelas
        $matkul = Str::slug($dataMhs[0]['matkul'], '_'); // Mengambil data matkul
        $fileName = "laporan_absensi_kelas_$kelas" . "_$matkul"; // Nama file excel

        // Export ke excel dengan nama file $fileName
        return Excel::download(new AbsensiExports(collect($dataMhs)), $fileName . '.xlsx');
    }
}
