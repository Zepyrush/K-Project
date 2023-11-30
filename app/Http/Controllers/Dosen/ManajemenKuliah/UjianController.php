<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManajemenKuliah\Ujian\Ujian;
use App\Models\ManajemenKuliah\Jadwal\Ujian as JadwalUjian;
use App\Models\ManajemenKuliah\Kelas;
use App\Models\ManajemenKuliah\Matkul;
use App\Models\ManajemenKuliah\Ujian\UjianHasil;
use App\Models\ManajemenKuliah\Ujian\UjianMahasiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UjianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $jadwals = JadwalUjian::with(['ujian', 'kelas', 'matkul'])
            ->where('dosen_id', Auth::user()->dosen->id)
            ->orderBy(Kelas::select('kode')->whereColumn('id', 'kelas_id'), 'asc')
            ->orderBy(Matkul::select('nama')->whereColumn('id', 'matkul_id'), 'asc')
            ->orderBy(Ujian::select('semester')->whereColumn('id', 'ujian_id'), 'asc')
            ->orderBy(Ujian::select('tipe_ujian')->whereColumn('id', 'ujian_id'), 'desc')
            ->get();

        if ($request->ajax()) {
            $data = $jadwals->transform(function ($item) {

                $item->kelas_jadwal = $item->kelas->kode;
                $item->matkul_jadwal = $item->matkul->nama;
                $item->tanggal = Carbon::parse($item->tanggal_ujian)->isoFormat('dddd, D MMMM Y');

                if ($item->ujian != null) { // Jika ujian tidak kosong
                    $ujian = $item->ujian;
                    ($ujian->tipe_ujian == 'uts') ?
                        $tipe_ujian = "<span class='ml-1 badge badge-success position-relative' style='bottom: 1px;'>UTS</span>" :
                        $tipe_ujian = "<span class='ml-1 badge badge-info position-relative' style='bottom: 1px;'>UAS</span>";
                    $item->tipe_soal = $ujian->tipe_soal . $tipe_ujian;
                    $item->semester = $ujian->semester;

                    if ($ujian->tipe_soal == 'Pilihan Ganda') { // Jika tipe soal pilihan ganda
                        if ($ujian->soalUjianPg) {
                            $item->ujian_soal = $ujian->soalUjianPg->count() . ' Soal';
                        }
                    } else { // Jika tipe soal essay
                        if ($ujian->soalUjianEssay) {
                            $item->ujian_soal = $ujian->soalUjianEssay->count() . ' Soal';
                        }
                    }
                }
                return $item;
            });

            // Filtering data
            if ($request->filterKelas != null) {
                $data = collect($data)->where('kelas_jadwal', $request->filterKelas)->all();
            }

            if ($request->filterMatkul != null) {
                $data = collect($data)->where('matkul_jadwal', $request->filterMatkul)->all();
            }

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $ujianBlmDinilai = $data->ujian ?
                        $data->ujian->ujianMahasiswa()
                        ->where('status', '1')
                        ->where('nilai', null)
                        ->count() : 0;

                    if ($data->ujian != null) {
                        $tipe_soal = $data->ujian->tipe_soal == 'Essay' ? 'essay' : 'pg';

                        $button = '
                            <a href="' . route(
                            'manajemen.kuliah.jadwal.dosen.ujian.soal.' . $tipe_soal . '.edit',
                            encrypt($data->id)) . '" class="btn btn-warning btn-sm mt-1 "
                                data-toggle="tooltip" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                        ';

                        if ($ujianBlmDinilai > 0) { // Jika ada ujian yang belum dinilai
                            $button .= '
                                <a href="' . route('manajemen.kuliah.jadwal.dosen.ujian.show', encrypt($data->id)) . '"
                                    class="btn btn-primary btn-sm mt-1 mr-1 position-relative" data-toggle="tooltip" title="Lihat">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span class="badge badge-danger badge-pill float-right position-absolute notif"
                                        style="top: -7px">
                                        ' . $ujianBlmDinilai . '
                                    </span>
                                </a>
                            ';
                        } else {
                            $button .= '
                                <a href="' . route('manajemen.kuliah.jadwal.dosen.ujian.show', encrypt($data->id)) . '"
                                    class="btn btn-primary btn-sm mt-1 mr-1" data-toggle="tooltip" title="Lihat">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            ';
                        }

                        // if ($data->dosen_can_manage == 1) {
                        //     $button .= '<button type="button" name="delete" id="' . encrypt($data->id) . '" class="del_btn btn btn-danger btn-sm mt-1" data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></button>';
                        // }
                    } else {
                        $button = '<a id="' . encrypt($data->id) . '" class="btn btn-success btn-sm btnBuatUjian" data-toggle="tooltip" title="Buat Ujian / Soal"><i class="fas fa-plus-circle"></i></a>';
                    }

                    return $button;
                })
                ->rawColumns(['action', 'tipe_soal'])
                ->make(true);
        } // End request ajax

        return view('dashboard.dosen.jadwal.ujian', [
            'jadwals' => $jadwals,
            'data_kelas' => Auth::user()->dosen->kelas->unique('kode'),
            'data_matkul' => Auth::user()->dosen->matkuls,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jadwal = JadwalUjian::with(['ujian', 'matkul', 'kelas'])
            ->where('dosen_id', Auth::user()->dosen->id)
            ->find(decrypt($id));

        if (!$jadwal) {
            return redirect()->route('manajemen.kuliah.jadwal.dosen.ujian.index')
                ->with('error', 'Oops! Sepertinya jadwal ujian tidak ditemukan.');
        }

        $ujian = $jadwal->ujian;

        if (!$ujian) {
            return redirect()->route('manajemen.kuliah.jadwal.dosen.ujian.index')
                ->with('error', 'Oops! Sepertinya anda belum membuat ujian untuk jadwal ini.');
        }

        // ini untuk menghitung durasi ujian
        $jam = floor($ujian->durasi_ujian / 60);
        $menit = $ujian->durasi_ujian % 60;

        ($menit == 0) ?
            $durasi = $jam . ' Jam' :
            $durasi = $jam . ' Jam ' . $menit . ' Menit';

        $durasi = $durasi;

        $mahasiswa = UjianMahasiswa::with(['mahasiswa' => function ($q) {
            $q->with('kelas', 'user');
        }, 'ujianHasil', 'ujian'])
            ->where('ujian_id', $ujian->id)
            ->orderBy("created_at", "desc")
            ->get();

        if (request()->ajax()) {
            $data = $mahasiswa->transform(function ($item) use ($ujian) {

                if ($item->ujianHasil != null && $item->ujianHasil->count() > 0) {
                    if ($ujian->tipe_soal == 'Pilihan Ganda') { // Jika tipe soal adalah pilihan ganda
                        $item->salah = $item->ujianHasil->where('status', '0')->where("jawaban", "!=", null)->count() ?? 0;
                        $item->benar = $item->ujianHasil->where('status', '1')->count() ?? 0;
                        $item->tidak_jawab = $item->ujianHasil->where('jawaban', null)->count() ?? 0;
                        $item->nilai_ujian = $item->nilai ?? 0;
                    } else { // Jika tipe soal adalah essay
                        if ($item->nilai == null) { // Jika nilai belum dinilai
                            $item->salah = "<span class='badge badge-danger'>Belum dinilai</span>";
                            $item->benar = "<span class='badge badge-danger'>Belum dinilai</span>";
                            $item->nilai_ujian = "<span class='badge badge-danger'>Belum dinilai</span>";
                        } else { // Jika nilai sudah dinilai
                            $item->salah = $item->ujianHasil->where('status', '0')->where("jawaban", "!=", null)->count() ?? 0;
                            $item->benar = $item->ujianHasil->where('status', '1')->count() ?? 0;
                            $item->nilai_ujian = $item->nilai ?? 0;
                        }
                    }
                }

                $item->nama = $item->mahasiswa->nama;
                $item->nim = $item->mahasiswa->nim;
                $item->foto = $item->mahasiswa->user->foto;

                return $item;
            });

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('salah', function ($data) {
                    return $data->salah ?? '-';
                })
                ->addColumn('benar', function ($data) {
                    return $data->benar ?? '-';
                })
                ->addColumn('tidak_jawab', function ($data) {
                    return $data->tidak_jawab ?? '-';
                })
                ->addColumn('nilai_ujian', function ($data) {
                    return $data->nilai_ujian ?? '-';
                })
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
                })
                ->addColumn('action', function ($data) use ($jadwal) {
                    if ($data->user_agent == null && $data->status == 0) {
                        $button = "-";
                    } else {

                        // Jika ada soal yang belum dinilai
                        $data->ujianHasil->where('status', '2')->count() != 0 ?
                            $addClass = "btnNilaiDulu" : $addClass = "btnLihatHasil";

                        if ($data->ujian->tipe_soal == 'Essay') {
                            $button = '<a href="#" id="' . encrypt($data->id) . '" data-id="' . $data->id . '"
                                class="btn btn-warning btn-sm mt-1 mr-1 btnNilaiHasil text-white"
                                data-toggle="tooltip" title="Penilaian">
                                    <i class="fas fa-star"></i>
                                </a>';

                            $button .= '<a href="#" id="' . encrypt($data->id) . '" class="btn btn-primary btn-sm mt-1 mr-1 ' . $addClass . '"
                                data-toggle="tooltip" title="Lebih Detail">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>';
                        } else {
                            $button = '<a href="#" id="' . encrypt($data->id) . '" class="btn btn-primary btn-sm mt-1 mr-1 ' . $addClass . '"
                                data-toggle="tooltip" title="Lebih Detail">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>';
                        }
                    }

                    return $button;
                })
                ->rawColumns(['action', 'mahasiswa', 'tidak_jawab', 'nilai_ujian', 'salah', 'benar'])
                ->make(true);
        }

        return view('dashboard.dosen.ujian.index', [
            'jadwal' => $jadwal,
            'mahasiswa' => $mahasiswa,
            'ujian' => $ujian,
            'durasiToHour' => $durasi,
        ]);
    }

    public function getNilaiEssayMahasiswa(Request $request, $jadwalId)
    {
        if ($request->ajax()) {
            $jadwal = JadwalUjian::find(decrypt($jadwalId));

            $ujianMhs = UjianMahasiswa::where('id', $request->ujianMhsId)->firstOrFail();
            $mahasiswa = $ujianMhs->mahasiswa;

            $soal = UjianHasil::with('soalUjianEssay')
                ->where('ujian_mahasiswa_id', $ujianMhs->id)
                ->paginate(1, ['*'], 'essayMhs');

            $daftarSoal = UjianHasil::with('soalUjianEssay')
                ->where('ujian_mahasiswa_id', $ujianMhs->id)
                ->get();

            return response()->json([
                'soal' => $soal,
                'daftarSoal' => $daftarSoal,
                'mahasiswa' => $mahasiswa,
                'ujian' => $jadwal->ujian,
                'ujianMhs' => $ujianMhs,
            ], 200);
        } else {
            abort(404);
        }
    }

    public function simpanNilaiEssayMahasiswa(Request $request)
    {
        $ujianMhs = UjianMahasiswa::find($request->ujianMhsId);

        $ujianHsl = UjianHasil::find($request->ujianHasilId);
        $ujianHsl->dosen_id = Auth::user()->dosen->id;
        $ujianHsl->komentar_dosen = $request->komentar ?? $ujianHsl->komentar_dosen;
        $ujianHsl->skor = $request->skor ?? $ujianHsl->skor;

        if ($request->updateStatus != 2)
            $ujianHsl->status = $request->updateStatus;
        else
            $ujianHsl->status = $ujianHsl->status;

        $ujianHsl->update();

        $this->hitungNilai($ujianMhs->id);
        return response()->json("Ok..", 200);
    }

    private function hitungNilai($ujianMhsId)
    {
        $ujianMhs = UjianMahasiswa::with('ujianHasil')->find($ujianMhsId);
        $ujianHasil = $ujianMhs->ujianHasil;

        // Jumlah soal dalam ujian
        $jumlahSoal = $ujianHasil->count();

        $skorMax = 20;
        $skorTotal = 0;

        foreach ($ujianHasil as $hasil)
        {
            $skorJawaban = $hasil->skor;
            $skorJawaban = max(0, min($skorMax, $skorJawaban));
            $skorTotal += $skorJawaban;
        }

        /**
         * skorTotal = skor yang didapat mahasiswa dari penilaian si dosen
         * jumlahSoal = jumlah soal dalam ujian
         * skorMax = skor maksimal yang bisa didapat dari setiap soal disini adalah 20
         *
         * lalu kita hitung nilai akhirnya dengan rumus :
         * (Total skor / (Jumlah soal * Skor Maksimum per Soal)) * 100
         **/
        $nilai = ($skorTotal / ($jumlahSoal * $skorMax)) * 100;

        $ujianMhs->nilai = intval($nilai);
        $ujianMhs->update();

        return $nilai;
    }
}
