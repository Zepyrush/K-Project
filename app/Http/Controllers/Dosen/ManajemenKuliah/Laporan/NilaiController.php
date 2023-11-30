<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah\Laporan;

use App\Exports\Dosen\Laporan\NilaiExport;
use App\Http\Controllers\Controller;
use App\Models\KelolaPengguna\Mahasiswa;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\ManajemenKuliah\Jadwal\Ujian as JadwalUjian;
use App\Models\ManajemenKuliah\Kelas;
use App\Models\ManajemenKuliah\Matkul;
use App\Models\ManajemenKuliah\Ujian\UjianMahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class NilaiController extends Controller
{
    public function nilai()
    {
        $jadwals = JadwalKuliah::where('dosen_id', Auth::user()->dosen->id)
            ->whereHas('kelas.mahasiswa')
            ->orderBy(Kelas::select('kode')->whereColumn('id', 'kelas_id'), 'asc')
            ->get();

        return view('dashboard.dosen.laporan.nilai.index', [
            'jadwals' => $jadwals,
        ]);
    }

    public function fetchDataNilai(Request $request)
    {
        if (request()->ajax())
        {
            $key = explode('_', $request->key_id);
            $kelas_id = $key[0];
            $matkul_id = $key[1];
            $dosen_id = $key[2];

            $jadwal = JadwalKuliah::where('kelas_id', $kelas_id)
                ->where('matkul_id', $matkul_id)
                ->first();

            $output = view('dashboard.dosen.laporan.nilai._data-nilai', [
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
        if ($request->ajax())
        {
            $key = explode('_', $request->key_id);
            $kelas_id = $key[0];
            $matkul_id = $key[1];
            $dosen_id = $key[2];

            $mahasiswas = Mahasiswa::with([
                'ujianMahasiswa' => fn ($q) => $q->with('ujian.jadwalUjian'),
                'user'
            ])
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

            $table->addColumn('rata_rata', function ($data) use ($matkul_id) {
                $nilai = 0;
                $jumlah = 0;

                foreach ($data->tugas as $tugas) {
                    if ($matkul_id == $tugas->matkul_id) {
                        if ($tugas->nilaiTugas) {
                            $nilai += $tugas->nilaiTugas->nilai;
                            $jumlah++;
                        }
                    }
                }

                return $jumlah > 0 ? $nilai / $jumlah : 0;
            });

            $table->addColumn('nilai_uts', function ($data) use ($kelas_id, $matkul_id) {
                $ujianMahasiswa = $data->ujianMahasiswa->where('nilai', '!=', null);

                if ($ujianMahasiswa) {
                    $uts = $ujianMahasiswa->where('ujian.tipe_ujian', 'uts')->first();
                    $nilai = $uts ? intval($uts->nilai) : 0;
                }

                return $nilai ?? 0;
            });

            $table->addColumn('nilai_uas', function ($data) use ($matkul_id) {
                $ujianMahasiswa = $data->ujianMahasiswa->where('nilai', '!=', null);

                if ($ujianMahasiswa) {
                    $uas = $ujianMahasiswa->where('ujian.tipe_ujian', 'uas')->first();
                    $nilai = $uas ? intval($uas->nilai) : 0;
                }

                return $nilai ?? 0;
            });

            $table->addColumn('total', function ($data) use ($matkul_id) {
                $totalNilaiTugas = 0;
                $jumlahTugas = 0;
                $ujianMahasiswa = $data->ujianMahasiswa->where('nilai', '!=', null);

                foreach ($data->tugas as $tugas) {
                    if ($matkul_id == $tugas->matkul_id) {
                        if ($tugas->nilaiTugas) {
                            $totalNilaiTugas += $tugas->nilaiTugas->nilai;
                            $jumlahTugas++;
                        }
                    }
                }

                if ($ujianMahasiswa) {
                    $uts = $ujianMahasiswa->where('ujian.tipe_ujian', 'uts')->first();
                    $uas = $ujianMahasiswa->where('ujian.tipe_ujian', 'uas')->first();

                    $nilaiUts = $uts ? intval($uts->nilai) : 0;
                    $nilaiUas = $uas ? intval($uas->nilai) : 0;
                }

                if ($totalNilaiTugas !== 0 || $jumlahTugas !== 0 || $nilaiUts !== 0 || $nilaiUas !== 0) {
                    if ($totalNilaiTugas == 0 || $jumlahTugas == 0) {
                        $nilai = ($nilaiUts == 0) ? ($nilaiUas * 0.4) : ($nilaiUts * 0.3);
                    } else {
                        $nilai = ($totalNilaiTugas / $jumlahTugas) * 0.3 + $nilaiUts * 0.3 + $nilaiUas * 0.4;
                    }
                } else {
                    $nilai = 0;
                }

                return round($nilai, 1) ?? 0;
            });

            $pertemuan = array_fill(0, 14, '');

            foreach ($pertemuan as $key => $value) {
                $pertemuan[$key] = "p" . ($key + 1);
            }

            return $table->rawColumns(array_merge(
                ['mahasiswa'],
                $pertemuan
            ))->make(true);
        }
    }

    public function exports($kelas_id, $matkul_id)
    {
        $kelas = Kelas::find(decrypt($kelas_id));
        $matkul = Matkul::find(decrypt($matkul_id));
        $auth = Auth::user()->dosen;

        $mahasiswas = Mahasiswa::with(['tugas' => function ($q) use ($auth) {
            $q->whereIn('parent', $auth->tugas->pluck('id'))
                ->select('id', 'mahasiswa_id', 'pertemuan', 'matkul_id');
        }, 'tugas.matkul', 'tugas.nilaiTugas', 'kelas'])
            ->where('kelas_id', $kelas->id)
            ->orderBy('nama', 'asc')
            ->get();

        foreach ($mahasiswas as $i => $mhs)
        {
            $totalNilaiTugas = 0;
            $jumlahTugas = 0;

            $data[$i] = [
                'nama' => $mhs->nama,
                'nim' => $mhs->nim,
                'kelas' => $mhs->kelas->kode,
                'matkul' => $matkul->nama,
                'fakultas' => $mhs->fakultas->nama,
                'rata_rata' => 0,
                'nilai_uts' => 0,
                'nilai_uas' => 0,
                'total' => 0,
            ];

            for ($j = 1; $j <= 14; $j++)
            {
                $data[$i]["p$j"] = '-';

                foreach ($mhs->tugas as $tugas) {
                    if ($tugas->matkul_id == $matkul->id) {
                        if ($tugas->pertemuan == $j)  {
                            if ($tugas->nilaiTugas) {
                                $data[$i]["p$j"] = $tugas->nilaiTugas->nilai;

                                $totalNilaiTugas += $tugas->nilaiTugas->nilai;
                                $jumlahTugas++;
                            } else {
                                $data[$i]["p$j"] = 0;
                            }
                        }
                    }
                }
            }

            $ujianMahasiswa = $mhs->ujianMahasiswa->where('nilai', '!=', null);

            if ($ujianMahasiswa) {
                $uts = $ujianMahasiswa->where('ujian.tipe_ujian', 'uts')->first();
                $uas = $ujianMahasiswa->where('ujian.tipe_ujian', 'uas')->first();

                $data[$i]['nilai_uts'] = $uts ? intval($uts->nilai) : 0;
                $data[$i]['nilai_uas'] = $uas ? intval($uas->nilai) : 0;
            }

            $data[$i]['rata_rata'] = $jumlahTugas > 0 ? $totalNilaiTugas / $jumlahTugas : 0;
            $data[$i]['total'] = floatval(($data[$i]['rata_rata'] * 0.3) + ($data[$i]['nilai_uts'] * 0.3) + ($data[$i]['nilai_uas'] * 0.4));
        }

        ob_end_clean();

        $fileName = "laporan_nilai_{$kelas->kode}_" . Str::slug($matkul->nama, '_');
        return Excel::download(new NilaiExport(collect($data)), $fileName . '.xlsx');
    }
}
