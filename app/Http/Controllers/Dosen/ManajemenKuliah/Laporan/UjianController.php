<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah\Laporan;

use App\Exports\Dosen\Laporan\NilaiUjianExports;
use App\Http\Controllers\Controller;
use App\Models\KelolaPengguna\Mahasiswa;
use Illuminate\Http\Request;
use App\Models\ManajemenKuliah\Jadwal\Ujian as JadwalUjian;
use App\Models\ManajemenKuliah\Kelas;
use App\Models\ManajemenKuliah\Matkul;
use App\Models\ManajemenKuliah\Ujian\Ujian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UjianController extends Controller
{
    public function nilai_ujian()
    {
        $kelas_id = Auth::user()->dosen->kelas->pluck('id');
        $matkul_id = Auth::user()->dosen->matkuls->pluck('id');

        $jadwals = JadwalUjian::join('kelas', 'kelas.id', '=', 'jadwal_ujians.kelas_id')
            ->join('matkuls', 'matkuls.id', '=', 'jadwal_ujians.matkul_id')
            ->where('ujian_id', '!=', null)
            ->where('dosen_id', Auth::user()->dosen->id)
            ->whereIn('kelas_id', $kelas_id)
            ->whereIn('matkul_id', $matkul_id)
            ->select('kelas_id', 'matkul_id', 'dosen_id', 'kelas.kode', 'matkuls.kode as matkulKode', 'matkuls.nama')
            ->orderBy('kelas.kode', 'ASC')
            ->groupBy(['kelas_id', 'matkul_id', 'dosen_id', 'kode', 'matkulKode', 'nama'])
            ->get();

        return view('dashboard.dosen.laporan.nilai_ujian.index', [
            'jadwals' => $jadwals,
        ]);
    }

    public function fetchDataNilai(Request $request)
    {
        $key = explode('_', $request->key_id);
        $kelas_id = $key[0];
        $matkul_id = $key[1];
        $dosen_id = $key[2];

        $jadwals = JadwalUjian::with(['kelas', 'matkul', 'ujian'])
            ->where('kelas_id', $kelas_id)
            ->where('matkul_id', $matkul_id)
            ->where('dosen_id', $dosen_id)
            ->where('ujian_id', '!=', null)
            ->get();

        $output = view('dashboard.dosen.laporan.nilai_ujian._data-nilai', [
            'data' => $jadwals->first(),
            'kelas_id' => $kelas_id,
            'matkul_id' => $matkul_id,
        ])->render();

        return response()->json($output);
    }

    public function tableDataNilai(Request $request)
    {
        $key = explode('_', $request->key_id);
        $kelas_id = $key[0];
        $matkul_id = $key[1];
        $dosen_id = $key[2];

        $mahasiswas = Mahasiswa::with(['ujianMahasiswa' => function($q) {
            $q->with('ujian', 'ujian.jadwalUjian');
        }, 'user'])
            ->where('kelas_id', $kelas_id)
            ->orderBy('nama', 'ASC')
            ->get();

        $data = $mahasiswas->transform(function ($item) use ($kelas_id, $matkul_id, $dosen_id) {
            $item->kelas_id = $kelas_id;
            $item->matkul_id = $matkul_id;
            $item->dosen_id = $dosen_id;
            $item->foto = $item->user->foto;

            return $item;
        });

        if ($request->ajax()) {
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

            for ($i = 1; $i <= 8; $i++) { // Looping semester 1 - 8
                // Kolom semester 1 - 8
                $table->addColumn("s$i", function ($data) use ($i, $kelas_id, $matkul_id) {
                    // menampilkan nilai uts dan uas berdasarkan semester dengan kondisi jika nilai tidak kosong
                    $ujianMahasiswa = $data->ujianMahasiswa->where('ujian.semester',  $i)
                        ->where('ujian.jadwalUjian.kelas_id', $kelas_id)
                        ->where('ujian.jadwalUjian.matkul_id', $matkul_id)
                        ->where('nilai', '!=', null);

                    if ($ujianMahasiswa) { // Jika nilai tidak kosong
                        $uas = $ujianMahasiswa->where('ujian.tipe_ujian', 'uas')->first();
                        $uts = $ujianMahasiswa->where('ujian.tipe_ujian', 'uts')->first();

                        $nilai = "<div class='d-flex flex-column align-items-end'>";
                            if ($uts && $uas) {  // Jika nilai uts dan uas tidak kosong
                                $nilai .= "
                                    <div>$uts->nilai <span class='badge badge-success'>UTS</span></div>
                                    <div>$uas->nilai <span class='badge badge-info'>UAS</span></div>
                                ";
                            } else if ($uts) { // Jika nilai uts tidak kosong
                                $nilai .= "
                                    <div> $uts->nilai <span class='badge badge-success'>UTS</span></div>
                                ";
                            } else if ($uas) { // Jika nilai uas tidak kosong
                                $nilai .= "
                                    <div> $uas->nilai <span class='badge badge-info'>UAS</span></div>
                                ";
                            } else { // Jika nilai uts dan uas kosong
                                $nilai .= '-';
                            }
                        $nilai .= "</div>";

                        return $nilai;
                    } else {
                        return '-';
                    }
                });
            }

            $semester = array_fill(0, 8, ''); // Membuat array kosong dengan jumlah 8
            foreach ($semester as $key => $value) {
                $semester[$key] = "s" . ($key + 1); // Menambahkan prefix s pada array
            }

            return $table->rawColumns(array_merge(['mahasiswa'], $semester))
                ->make(true);
        }
    }

    public function exports($kelas_id, $matkul_id)
    {
        $matkul = Matkul::find(decrypt($matkul_id));

        $mahasiswa = Mahasiswa::with(['ujianMahasiswa.ujian', 'user'])
            ->where('kelas_id', decrypt($kelas_id))
            ->orderBy('nama', 'ASC')
            ->get();

        foreach ($mahasiswa as $i => $mhs) {
            $dataMhs[$i] = [
                'nama' => $mhs->nama,
                'nim' => $mhs->nim,
                'kelas' => $mhs->kelas->kode,
                'matkul' => $matkul->nama,
                'fakultas' => $mhs->fakultas->nama,
            ];

            for ($j = 1; $j <= 8; $j++) {
                $ujianMahasiswa = $mhs->ujianMahasiswa->where('ujian.semester',  $j)
                    ->where('ujian.jadwalUjian.kelas_id', decrypt($kelas_id))
                    ->where('ujian.jadwalUjian.matkul_id', decrypt($matkul_id))
                    ->where('nilai', '!=', null);

                if ($ujianMahasiswa) {
                    $uas = $ujianMahasiswa->where('ujian.tipe_ujian', 'uas')->first();
                    $uts = $ujianMahasiswa->where('ujian.tipe_ujian', 'uts')->first();

                    if ($uts && $uas) {
                        $dataMhs[$i]["s$j"] = "$uts->nilai (UTS) $uas->nilai (UAS)";
                    } else if ($uts) {
                        $dataMhs[$i]["s$j"] = "$uts->nilai (UTS)";
                    } else if ($uas) {
                        $dataMhs[$i]["s$j"] = "$uas->nilai (UAS)";
                    } else {
                        $dataMhs[$i]["s$j"] = '-';
                    }
                } else {
                    $dataMhs[$i]["s$j"] = '-';
                }
            }
        }

        ob_end_clean();

        $fileName = 'laporan_nilai_ujian_kelas_' . $dataMhs[0]['kelas'] .
            '_' . Str::slug($dataMhs[0]['matkul'], '_');

        return Excel::download(new NilaiUjianExports(collect($dataMhs)), $fileName . '.xlsx');
    }
}
