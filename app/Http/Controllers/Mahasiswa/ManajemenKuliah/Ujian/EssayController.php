<?php

namespace App\Http\Controllers\Mahasiswa\ManajemenKuliah\Ujian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManajemenKuliah\Jadwal\Ujian as JadwalUjian;
use App\Models\ManajemenKuliah\Ujian\SoalUjianEssay;
use App\Models\ManajemenKuliah\Ujian\Ujian;
use App\Models\ManajemenKuliah\Ujian\UjianHasil;
use App\Models\ManajemenKuliah\Ujian\UjianMahasiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class EssayController extends Controller
{
    /**
     * Halaman ujian essay
     *
     * @param  mixed $id
     * @return void
     */
    public function ujian($id)
    {
        $jadwal = JadwalUjian::aktif()
            ->with('kelas', 'matkul', 'ujian')
            ->find(decrypt($id)); // decrypt($id) = JadwalUjian->id

        $ujian = $jadwal->ujian;

        if ($jadwal->ended_at != null) { // jika ended_at tidak null
            $ujianMahasiswa = UjianMahasiswa::with('ujian')->where([
                'mahasiswa_id' => Auth::user()->mahasiswa->id,
                'status' => 0
            ])->where('ended_at', '>=', now())->first();
        } else { // jika ended_at null
            $ujianMahasiswa = UjianMahasiswa::with('ujian')->where([
                'mahasiswa_id' => Auth::user()->mahasiswa->id,
                'status' => 0
            ])->first();
        }

        if ($ujianMahasiswa == null) { // jika sudah pernah mengerjakan ujian
            return redirect()->route('manajemen.kuliah.ujian.mahasiswa.index')
                ->with('error', 'Ujian ini sudah kamu kerjakan.');
        }

        return view('dashboard.mahasiswa.ujian.essay', [
            'jadwal' => $jadwal,
            'ujian' => $ujian,
            'ujianMahasiswa' => $ujianMahasiswa,
        ]);
    }

    /**
     * Mulai ujian
     *
     * @param  mixed $request
     * @return void
     */
    public function mulaiUjian(Request $request) // mulai ujian
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

        // cek apakah sudah pernah mulai ujian
        $existingUjianMhs = UjianMahasiswa::where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
            ->where('started_at', "!=", null)
            ->first();

        if ($existingUjianMhs) { // jika sudah pernah mulai ujian
            return redirect()
                ->route('manajemen.kuliah.ujian.mahasiswa.essay.ujian', encrypt($jadwal->id));
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

            ($ujian->random_soal == 1) ? // jika random soal = 1
                $soal = $ujian->soalUjianEssay()->inRandomOrder()->get() :
                $soal = $ujian->soalUjianEssay;

            foreach ($soal as $key => $value) { // insert soal ke ujian_hasil
                $existingUjianHasil = UjianHasil::where('ujian_mahasiswa_id', $updateUjianMhs->id)
                    ->where('soal_ujian_essay_id', $value->id)
                    ->first();

                if ($existingUjianHasil) { // jika sudah ada soal di ujian_hasil
                    continue; // skip
                } else { // jika belum ada soal di ujian_hasil
                    $ujianHasil = new UjianHasil();
                    $ujianHasil->ujian_mahasiswa_id = $updateUjianMhs->id;
                    $ujianHasil->soal_ujian_essay_id = $value->id;
                    $ujianHasil->status = 0;
                    $ujianHasil->save();
                }
            }

            // redirect ke halaman ujian yang functionnya di atas
            return redirect()->route('manajemen.kuliah.ujian.mahasiswa.essay.ujian', encrypt($jadwal->id));
        }
    }

    /**
     * MENAMPILKAN SOAL UJIAN ESSAY DENGAN PAGINATION AJAX
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function fetchSoal(Request $request, $id)
    {
        $soal = UjianHasil::with(['soalUjianEssay' => function ($query) {
            $query->select('id', 'pertanyaan');
        }])->where('ujian_mahasiswa_id', decrypt($request->ujian_mahasiswa_id))
            ->paginate(1, ['*'], 'soal');

        return response()->json($soal);
    }

    /**
     * MENAMPILKAN DAFTAR SOAL UJIAN ESSAY
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function fetchDaftarSoal(Request $request, $id)
    {
        $daftarSoal = UjianHasil::where('ujian_mahasiswa_id', decrypt($request->ujian_mahasiswa_id))
            ->select("id", "ujian_mahasiswa_id", "soal_ujian_essay_id", "jawaban", "ragu")
            ->get();

        return response()->json($daftarSoal);
    }

    /**
     * RADIO BUTTON RAGU-RAGU UNTUK SOAL UJIAN ESSAY
     *
     * @param  mixed $request
     * @return void
     */
    public function raguRagu(Request $request)
    {
        $ujianMahasiswa = UjianMahasiswa::with('ujian')->where([
            'mahasiswa_id' => Auth::user()->mahasiswa->id,
        ])->where('ended_at', '>=', now())->first();

        if ($ujianMahasiswa == null) { // jika waktu ujian sudah habis
            return response()->json([
                'status' => "jadwal_habis",
                'message' => 'Waktu ujian telah habis.'
            ]);
        } else { // jika lagi mengerjakan ujian
            $soal = UjianHasil::find($request->id);
            $soal->ragu = $request->ragu;
            $soal->update();

            return response()->json("Ok..");
        }
    }

    /**
     * SIMPAN JAWABAN UNTUK SOAL UJIAN ESSAY
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
            $soal = UjianHasil::with('soalUjianEssay')->find($request->id);
            $soal->jawaban = $request->jawaban;

            /**
             * kenapa status 2? ini untuk proses pengecekan nilai buat dosen
             * jika soalnya sudah dinilai sama dosen.
            */
            $soal->status = 2;

            $soal->update();

            return response()->json("Ok..");
        }
    }

    /**
     * SELESAIKAN UJIAN ESSAY
     *
     * @param  mixed $request
     * @return void
     */
    public function selesaiUjian(Request $request)
    {
        $ujianMhs = UjianMahasiswa::with(['ujianHasil' => function ($q) {
            $q->with(['soalUjianEssay' => function ($q) {
                $q->with('ujian');
            }]);
        }])->find(decrypt($request->ujian_mahasiswa_id));

        if ($ujianMhs->ujianHasil->first()->jawaban != null) {
            $ujianMhs->status = 1; // 1 = selesai
            $ujianMhs->ended_at = Carbon::now(); // update waktu selesai ujian
            $ujianMhs->update(); // update ujian_mahasiswa

            return response()->json($ujianMhs);
        } else {
            $ujianMhs->started_at = null; // update waktu mulai ujian
            $ujianMhs->ended_at = null; // update waktu selesai ujian
            $ujianMhs->update(); // update ujian_mahasiswa

            foreach ($ujianMhs->ujianHasil as $key => $ujnHasil) {
                $ujnHasil->status = 2; // 2 = belum dinilai
                $ujnHasil->update(); // update ujian_hasil
            }

            $text = "<span class='font-weight-bold'>Kamu Tidak Mengerjakan Ujian</span> <hr>
            Silahkan mengulangi ujian jika jadwal ujian masih tersedia. Jika jadwal ujian sudah habis,
            Silahkan menghubungi dosen pengampu matakuliah ini untuk mengulangi :ujian";

            return response()->json([
                'status' => "nilai_kosong",
                'message' => $text
            ]);
        }
    }
}
