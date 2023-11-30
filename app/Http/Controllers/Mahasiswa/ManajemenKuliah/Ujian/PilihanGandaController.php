<?php

namespace App\Http\Controllers\Mahasiswa\ManajemenKuliah\Ujian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManajemenKuliah\Jadwal\Ujian as JadwalUjian;
use App\Models\ManajemenKuliah\Ujian\SoalUjianPg;
use App\Models\ManajemenKuliah\Ujian\Ujian;
use App\Models\ManajemenKuliah\Ujian\UjianHasil;
use App\Models\ManajemenKuliah\Ujian\UjianMahasiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class PilihanGandaController extends Controller
{
    /**
     * Halaman ujian pilihan ganda
     *
     * @param  mixed $id
     * @return void
     */
    public function ujian($id)
    {
        $jadwal = JadwalUjian::aktif()
            ->with('kelas', 'matkul', 'ujian')
            ->find(decrypt($id));

        $ujian = $jadwal->ujian;

        if ($jadwal->ended_at != null) {
            $ujianMahasiswa = UjianMahasiswa::with('ujian')->where([
                'mahasiswa_id' => Auth::user()->mahasiswa->id,
                'status' => 0
            ])->where('ended_at', '>=', now())->first();
        } else {
            $ujianMahasiswa = UjianMahasiswa::with('ujian')->where([
                'mahasiswa_id' => Auth::user()->mahasiswa->id,
                'status' => 0
            ])->first();
        }

        if ($ujianMahasiswa == null) {
            return redirect()->route('manajemen.kuliah.ujian.mahasiswa.index')
                ->with('error', 'Ujian ini sudah kamu kerjakan.');
        }

        return view('dashboard.mahasiswa.ujian.pg', [
            'jadwal' => $jadwal,
            'ujian' => $ujian,
            'ujianMahasiswa' => $ujianMahasiswa,
        ]);
    }

    /**
     * Memulai ujian pilihan ganda
     *
     * @param  mixed $request
     * @return void
     */
    public function mulaiUjian(Request $request)
    {
        $jadwal = JadwalUjian::aktif()
            ->with('kelas', 'matkul', 'ujian')
            ->find(decrypt($request->jadwal_id));

        $current_time = strtotime('now');
        $exam_start_at = strtotime($jadwal->tanggal_ujian . ' ' . $jadwal->started_at);
        $max_time = strtotime('+120 minutes', $exam_start_at);

        if ($current_time >= $max_time) {
            return redirect()->back()
                ->with('error', 'Kamu terlambat! Silahkan menghubungi dosen pengawas.');
        }

        $ujian = $jadwal->ujian;

        $existingUjianMhs = UjianMahasiswa::where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
            ->where('started_at', "!=", null)
            ->first();

        if ($existingUjianMhs) { // jika sudah pernah mulai ujian
            return redirect()->route('manajemen.kuliah.ujian.mahasiswa.pg.ujian', encrypt($jadwal->id));
        } else {
            $updateUjianMhs = UjianMahasiswa::where('ujian_id', $ujian->id)
                ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
                ->first();

            $updateUjianMhs->started_at = Carbon::now();
            $updateUjianMhs->ended_at = Carbon::now()->addMinutes($ujian->durasi_ujian);
            $updateUjianMhs->user_agent = $request->userAgent();
            $updateUjianMhs->ip_address = $request->getClientIp();
            $updateUjianMhs->created_at = Carbon::now();
            $updateUjianMhs->updated_at = Carbon::now();
            $updateUjianMhs->update();

            ($ujian->random_soal == 1) ? // random soal jika random_soal = 1
                $soal = $ujian->soalUjianPg()->inRandomOrder()->get() :
                $soal = $ujian->soalUjianPg;

            foreach ($soal as $key => $value) {

                $existingUjianHasil = UjianHasil::where('ujian_mahasiswa_id', $updateUjianMhs->id)
                    ->where('soal_ujian_pg_id', $value->id)
                    ->first();

                if ($existingUjianHasil) {
                    continue;
                } else {
                    $ujianHasil = new UjianHasil();
                    $ujianHasil->ujian_mahasiswa_id = $updateUjianMhs->id;
                    $ujianHasil->soal_ujian_pg_id = $value->id;
                    $ujianHasil->status = 0;
                    $ujianHasil->save();
                }
            }

            return redirect()->route('manajemen.kuliah.ujian.mahasiswa.pg.ujian', encrypt($jadwal->id));
        }
    }

    /**
     * Fetch soal ujian pilihan ganda
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function fetchSoal(Request $request, $id)
    {
        $soal = UjianHasil::with(['soalUjianPg' => function ($query) {
            $query->select('id', 'pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'pilihan_e');
        }])->where('ujian_mahasiswa_id', decrypt($request->ujian_mahasiswa_id))
            ->paginate(1, ['*'], 'soal');

        return response()->json($soal);
    }

    /**
     * Fetch daftar soal ujian pilihan ganda
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function fetchDaftarSoal(Request $request, $id)
    {
        $daftarSoal = UjianHasil::where('ujian_mahasiswa_id', decrypt($request->ujian_mahasiswa_id))
            ->select("id", "ujian_mahasiswa_id", "soal_ujian_pg_id", "jawaban", "ragu")
            ->get();

        return response()->json($daftarSoal);
    }

    /**
     * Ragu-ragu soal ujian pilihan ganda
     *
     * @param  mixed $request
     * @return void
     */
    public function raguRagu(Request $request)
    {
        $ujianMahasiswa = UjianMahasiswa::with('ujian')->where([
            'mahasiswa_id' => Auth::user()->mahasiswa->id,
        ])->where('ended_at', '>=', now())->first();

        if ($ujianMahasiswa == null) {
            return response()->json([
                'status' => "jadwal_habis",
                'message' => 'Waktu ujian telah habis.'
            ]);
        } else {
            $soal = UjianHasil::find($request->id);
            $soal->ragu = $request->ragu;
            $soal->update();

            return response()->json("Ok..");
        }
    }

    /**
     * Simpan jawaban soal ujian pilihan ganda
     *
     * @param  mixed $request
     * @return void
     */
    public function simpanJawaban(Request $request)
    {
        $ujianMahasiswa = UjianMahasiswa::with('ujian')->where([
            'mahasiswa_id' => Auth::user()->mahasiswa->id,
        ])->where('ended_at', '>=', now())->first();

        if ($ujianMahasiswa == null) {
            return response()->json([
                'status' => "jadwal_habis",
                'message' => 'Waktu ujian telah habis.'
            ]);
        } else {
            $soal = UjianHasil::with('soalUjianPg')->find($request->id);

            $soal->jawaban = $request->jawaban;

            ($soal->soalUjianPg->jawaban_benar == $request->jawaban) ?
                $soal->status = 1 : // benar
                $soal->status = 0; // salah

            $soal->update();

            $this->hitungNilai($soal->ujian_mahasiswa_id); // hitung nilai

            return response()->json("Ok..");
        }
    }

    /**
     * Ujian selesai
     *
     * @param  mixed $request
     * @return void
     */
    public function selesaiUjian(Request $request)
    {
        $ujianMhs = UjianMahasiswa::with(['ujianHasil' => function ($q) {
            $q->with(['soalUjianPg' => function ($q) {
                $q->with('ujian');
            }]);
        }])->find(decrypt($request->ujian_mahasiswa_id));

        if ($ujianMhs->nilai != null) {
            $ujianMhs->status = 1;
            $ujianMhs->ended_at = Carbon::now();
            $ujianMhs->update();

            $this->hitungNilai($ujianMhs->id); // hitung nilai
            return response()->json($ujianMhs);
        } else {
            $ujianMhs->started_at = null; // ulangi ujian
            $ujianMhs->ended_at = null; // ulangi ujian
            $ujianMhs->update(); // update ujian_mahasiswa

            $text = "<span class='font-weight-bold'>Kamu Tidak Mengerjakan Ujian</span> <hr>
            Silahkan mengulangi ujian jika jadwal ujian masih tersedia. Jika jadwal ujian sudah habis,
            Silahkan menghubungi dosen pengampu matakuliah ini untuk mengulangi :ujian";

            return response()->json([
                'status' => "nilai_kosong",
                'message' => $text
            ]);
        }


    }

    /**
     * Hitung nilai ujian pilihan ganda
     *
     * @param  mixed $ujianMhsId
     * @return void
     */
    private function hitungNilai($ujianMhsId)
    {
        $ujianMhs = UjianMahasiswa::with('ujianHasil')->find($ujianMhsId);
        $jumlahSoal = $ujianMhs->ujianHasil->count();

        $benar = 0; // jumlah jawaban benar

        foreach ($ujianMhs->ujianHasil as $key => $value) { // hitung jumlah jawaban benar
            if ($value->status == 1) { // jika jawaban benar
                $benar++; // tambah jumlah jawaban benar
            }
        }

        $nilai = ($benar / $jumlahSoal) * 100; // hitung nilai
        $ujianMhs->nilai = $nilai; // update nilai
        $ujianMhs->update();

        return $nilai; // return nilai
    }
}
