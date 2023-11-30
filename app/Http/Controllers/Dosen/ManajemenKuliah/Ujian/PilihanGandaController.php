<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah\Ujian;

use App\Http\Controllers\Controller;
use App\Imports\Dosen\Ujian\SoalUjianPgImports;
use App\Models\KelolaPengguna\Mahasiswa;
use Illuminate\Http\Request;
use App\Models\ManajemenKuliah\Ujian\Ujian;
use App\Models\ManajemenKuliah\Jadwal\Ujian as JadwalUjian;
use App\Models\ManajemenKuliah\Ujian\SoalUjianEssay;
use App\Models\ManajemenKuliah\Ujian\SoalUjianPg;
use App\Models\ManajemenKuliah\Ujian\UjianHasil;
use App\Models\ManajemenKuliah\Ujian\UjianMahasiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PilihanGandaController extends Controller
{
    public function list(Request $request, $id)
    {
        $jadwal = JadwalUjian::with('ujian')
            ->where('dosen_id', Auth::user()->dosen->id)
            ->find(decrypt($id));

        if ($request->ajax()) {
            $soal = SoalUjianPg::where('ujian_id', $jadwal->ujian->id)
                ->paginate(1, ['*'], 'soal');

            $daftar_soal = SoalUjianPg::where('ujian_id', $jadwal->ujian->id)
                ->select('id', 'nomer_soal', 'jawaban_benar')
                ->paginate(5, ['*'], 'daftar_soal');

            return response()->json([
                'soal' => $soal,
                'daftar_soal' => $daftar_soal
            ]);
        } else {
            abort(404);
        }
    }

    public function detailNilai(Request $request, $id)
    {
        $ujianMhs = UjianMahasiswa::with(['mahasiswa', 'ujianHasil' => function ($q) {
            $q->with('soalUjianPg');
        }, 'ujian'])->find(decrypt($request->id));

        $start = Carbon::parse($ujianMhs->started_at); // waktu mulai
        $end = Carbon::parse($ujianMhs->ended_at); // waktu selesai
        $diff = $end->diffInSeconds($start); // waktu selesai dikurangi waktu mulai (dalam detik)
        $hours = floor($diff / 3600); // mengubah waktu ke jam
        $minutes = floor(($diff / 60) % 60); // mengubah waktu ke menit
        $seconds = $diff % 60; // mengubah waktu ke detik

        $data = [
            'namaMhs'       => $ujianMhs->mahasiswa->nama,
            'nimMhs'        => $ujianMhs->mahasiswa->nim,
            'userAgentMhs'  => $ujianMhs->user_agent,
            'ipAddressMhs'  => $ujianMhs->ip_address,
            'startedAtMhs'  => Carbon::parse($ujianMhs->started_at)->translatedFormat('l, d M Y - H:i') . " WIB",
            'endedAtMhs'    => Carbon::parse($ujianMhs->ended_at)->translatedFormat('l, d M Y - H:i') . " WIB",
            'durationMhs'   => $hours . ' Jam ' . $minutes . ' Menit ' . $seconds . ' Detik',
        ];

        return response()->json([
            'ujianMhs' => $ujianMhs,
            'data' => $data,
        ]);
    }

    public function create($id)
    {
        $jadwal = JadwalUjian::with(['ujian' => function ($q) {
            $q->with('soalUjianPg');
        }, 'matkul', 'kelas'])->where('dosen_id', Auth::user()->dosen->id)
            ->find(decrypt($id));

        if (!$jadwal) {
            return redirect()->route('manajemen.kuliah.jadwal.dosen.ujian.index');
        }

        if ($jadwal->started_at != null && $jadwal->ended_at != null) {
            // convert to duration
            $start = Carbon::parse($jadwal->started_at);
            $end = Carbon::parse($jadwal->ended_at);
            $duration = $start->diffInMinutes($end);
        } else {
            $duration = 0;
        }

        $jadwals = JadwalUjian::with('ujian')
            ->where("kelas_id", $jadwal->kelas_id)
            ->where("matkul_id", $jadwal->matkul_id)
            ->where("dosen_id", Auth::user()->dosen->id)
            ->get();

        if ($jadwals->isEmpty()) {
            $semester = 1;
            $tipe_ujian = ['uts', 'uas'];
        } else {

            $semester = 1;
            $isSemesterDone = false;

            while ($semester <= 9 && !$isSemesterDone) {
                if ($semester == 9) {
                    return redirect()->route('manajemen.kuliah.jadwal.dosen.ujian.index')
                        ->with('error', 'Maksimal 8 semester, hubungi admin untuk mengulang semester.');
                }

                $uts = $jadwals
                    ->where("kelas_id", $jadwal->kelas_id)
                    ->where("matkul_id", $jadwal->matkul_id)
                    ->where("dosen_id", Auth::user()->dosen->id)
                    ->where('ujian.semester', $semester)->where('ujian.tipe_ujian', 'uts')
                    ->first();

                $uas = $jadwals
                    ->where("kelas_id", $jadwal->kelas_id)
                    ->where("matkul_id", $jadwal->matkul_id)
                    ->where("dosen_id", Auth::user()->dosen->id)
                    ->where('ujian.semester', $semester)->where('ujian.tipe_ujian', 'uas')
                    ->first();

                if ($uts != null && $uas != null) {
                    $semester++;
                } else {
                    $isSemesterDone = true;

                    if ($uts != null) {
                        $tipe_ujian = ['uas'];
                    } else if ($uas != null) {
                        $tipe_ujian = ['uts'];
                    } else {
                        $tipe_ujian = ['uts', 'uas'];
                    }
                }
            }
        }

        return view('dashboard.dosen.ujian.soal.pg.create', [
            'jadwal' => $jadwal,
            'duration' => $duration,
            'semester' => $semester,
            'tipe_ujian' => $tipe_ujian,
        ]);
    }

    public function store(Request $request)
    {
        $jadwal = JadwalUjian::with(['ujian' => function ($q) {
            $q->with('soalUjianPg');
        }, 'kelas', 'matkul'])->where('dosen_id', Auth::user()->dosen->id)
            ->find(decrypt($request->jadwal_id));

        $mahasiswa = Mahasiswa::with('kelas')->whereHas('kelas', function ($q) use ($jadwal) {
            $q->where('kelas_id', $jadwal->kelas->id);
        })->get();

        DB::beginTransaction();
        try {
            $ujian = Ujian::create([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'durasi_ujian' => $request->durasi,
                'tipe_soal' => 'pilihan_ganda',
                'tipe_ujian' => $request->tipe_ujian,
                'random_soal' => $request->random_soal,
                'lihat_hasil' => $request->lihat_hasil,
                'jadwal_ujian_id' => $jadwal->id,
                'semester' => $request->semester ?? null,
            ]);

            JadwalUjian::where('id', $jadwal->id)->update([
                'ujian_id' => $ujian->id,
                'status_ujian' => 'aktif',
            ]);

            foreach ($mahasiswa as $key => $mhs) {
                UjianMahasiswa::create([
                    'ujian_id' => $ujian->id,
                    'mahasiswa_id' => $mhs->id,
                ]);
            }

            foreach ($request->soal as $key => $value) {
                SoalUjianPg::create([
                    'nomer_soal' => $key + 1,
                    'pertanyaan' => $value,
                    'pilihan_a' => htmlspecialchars($request->pilihan_a[$key]),
                    'pilihan_b' => htmlspecialchars($request->pilihan_b[$key]),
                    'pilihan_c' => htmlspecialchars($request->pilihan_c[$key]),
                    'pilihan_d' => htmlspecialchars($request->pilihan_d[$key]),
                    'pilihan_e' => htmlspecialchars($request->pilihan_e[$key]),
                    'jawaban_benar' => $request->jawaban_benar[$key],
                    'ujian_id' => $ujian->id,
                ]);
            }

            return Redirect::route('manajemen.kuliah.jadwal.dosen.ujian.show', encrypt($jadwal->id))
                ->with('success', "Berhasil menambahkan ujian!");
        } catch (\Throwable $th) {
            DB::rollBack();

            dd($th->getMessage());
        } finally {
            DB::commit();
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:100',
            'deskripsi' => 'required|string|max:1000',
            'durasi' => 'required|numeric',
            'random_soal' => 'required',
            'lihat_hasil' => 'required',
            'tipe_ujian' => 'required',
            'file' => 'required|mimes:xls,xlsx,csv|max:1024',
            'semester' => 'nullable',
        ], [
            'judul.required' => 'Judul ujian tidak boleh kosong.',
            'judul.string' => 'Judul ujian harus berupa string.',
            'judul.max' => 'Judul ujian maksimal 100 karakter.',
            'deskripsi.required' => 'Deskripsi ujian tidak boleh kosong.',
            'deskripsi.string' => 'Deskripsi ujian harus berupa string.',
            'deskripsi.max' => 'Deskripsi ujian maksimal 1000 karakter.',
            'durasi.required' => 'Durasi ujian tidak boleh kosong.',
            'durasi.numeric' => 'Durasi ujian harus berupa angka.',
            'random_soal.required' => 'Random soal tidak boleh kosong.',
            'lihat_hasil.required' => 'Lihat hasil tidak boleh kosong.',
            'tipe_ujian.required' => 'Tipe ujian tidak boleh kosong.',
            'file.required' => 'File tidak boleh kosong.',
            'file.mimes' => 'File harus berupa xls, xlsx, atau csv.',
            'file.max' => 'File maksimal 1 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'tipe' => 'validation',
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction(); // start transaction
            try {
                $jadwal = JadwalUjian::with(['ujian' => function ($q) {
                    $q->with('soalUjianPg');
                }, 'matkul', 'kelas'])->where('dosen_id', Auth::user()->dosen->id)
                    ->find(decrypt($request->jadwal_id));

                $mahasiswa = Mahasiswa::with('kelas')->whereHas('kelas', function ($q) use ($jadwal) {
                    $q->where('kelas_id', $jadwal->kelas->id);
                })->get();

                $ujian = Ujian::create([
                    'judul' => $request->judul,
                    'deskripsi' => $request->deskripsi,
                    'durasi_ujian' => $request->durasi,
                    'tipe_soal' => 'pilihan_ganda',
                    'tipe_ujian' => $request->tipe_ujian,
                    'random_soal' => $request->random_soal,
                    'lihat_hasil' => $request->lihat_hasil,
                    'jadwal_ujian_id' => $jadwal->id,
                    'semester' => $request->semester ?? null,
                ]);

                JadwalUjian::where('id', $jadwal->id)->update([
                    'ujian_id' => $ujian->id,
                    'status_ujian' => 'aktif',
                ]);

                foreach ($mahasiswa as $key => $mhs) {
                    UjianMahasiswa::create([
                        'ujian_id' => $ujian->id,
                        'mahasiswa_id' => $mhs->id,
                    ]);
                }

                $import = new SoalUjianPgImports($ujian->id);
                $import->import($request->file('file'));

                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil mengimport soal ujian!',
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'message' => $th->getMessage(),
                ]);
            } finally {
                DB::commit();
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $jadwal = JadwalUjian::with(['ujian' => function ($q) {
            $q->with('soalUjianPg');
        }, 'matkul', 'kelas'])->where('dosen_id', Auth::user()->dosen->id)->find(decrypt($id));

        if (!$jadwal) {
            return redirect()->route('manajemen.kuliah.jadwal.dosen.ujian.index');
        }

        $ujian = $jadwal->ujian;
        $soalPgs = $jadwal->ujian->soalUjianPg()->get();

        if ($jadwal->started_at != null && $jadwal->ended_at != null) {
            // convert to duration
            $start = Carbon::parse($jadwal->started_at); // start time
            $end = Carbon::parse($jadwal->ended_at); // end time
            $duration = $start->diffInMinutes($end); // duration in minutes
        } else {
            $duration = 0;
        }

        return view('dashboard.dosen.ujian.soal.pg.edit', [
            'jadwal' => $jadwal,
            'ujian' => $ujian,
            'duration' => $duration,
            'soalPgs' => $soalPgs,
        ]);
    }

    public function update(Request $request, $jadwalId)
    {
        DB::beginTransaction();
        try {
            $jadwal = JadwalUjian::with(['ujian' => function ($q) {
                $q->with('soalUjianPg');
            }, 'matkul', 'kelas'])->where('dosen_id', Auth::user()->dosen->id)->find(decrypt($jadwalId));

            $matkul_id = $jadwal->matkul_id;
            $kelas_id = $jadwal->kelas_id;
            $soal = $jadwal->ujian->soalUjianPg;
            $ujian = $jadwal->ujian;

            if ($jadwal->dosen_can_manage == '1') {
                $jadwal->update([
                    'status_ujian' => $request->status_ujian ?? $jadwal->status_ujian,
                    'started_at' => $request->started_at ?? $jadwal->started_at,
                    'ended_at' => $request->ended_at,
                    'tanggal_ujian' => $request->tanggal_ujian ?? $jadwal->tanggal_ujian,
                ]);
            }

            // Section tukeran tipe ujian (UTS <-> UAS) berdasarkan kelas, matkul, dan semester yang sama
            // ujian1 = ujian yang lagi diedit, ujian2 = ujian yang akan ditukar
            $ujian1 = $ujian->whereHas('jadwalUjian', function ($q) use ($matkul_id, $kelas_id) {
                    $q->where('matkul_id', $matkul_id)->where('kelas_id', $kelas_id);
                })->where('semester', $ujian->semester)->first();

            if ($ujian1 && $ujian1->tipe_ujian == $request->tipe_ujian) {
                $ujian->tipe_ujian = ($ujian1->tipe_ujian == 'uts') ? 'uas' : 'uts';
            }

            $ujian2 = Ujian::whereHas('jadwalUjian', function ($q) use ($matkul_id, $kelas_id) {
                    $q->where('matkul_id', $matkul_id)->where('kelas_id', $kelas_id);
                })
                ->where('id', '!=', $ujian->id)
                ->where('semester', $ujian->semester)
                ->where('tipe_ujian', $request->tipe_ujian)
                ->first();

            if ($ujian2 && $ujian2->tipe_ujian == $request->tipe_ujian) {
                $ujian2->update([
                    'tipe_ujian' => ($ujian2->tipe_ujian == 'uts') ? 'uas' : 'uts',
                ]);
            }
            // End section tukeran tipe ujian

            $ujian->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'durasi_ujian' => $request->durasi,
                'tipe_soal' => 'pilihan_ganda',
                'tipe_ujian' => $request->tipe_ujian,
                'random_soal' => $request->random_soal,
                'lihat_hasil' => $request->lihat_hasil,
                'semester' => $request->semester ?? null,
            ]);

            foreach ($request->pertanyaan as $key => $value) {
                if ($key >= count($soal) || count($soal) == 0) { // jika soal baru
                    SoalUjianPg::create([
                        'nomer_soal' => $key + 1, // nomer soal dimulai dari 1
                        'pertanyaan' => $value,
                        'pilihan_a' => htmlStrips($request->pilihan_a[$key]),
                        'pilihan_b' => htmlStrips($request->pilihan_b[$key]),
                        'pilihan_c' => htmlStrips($request->pilihan_c[$key]),
                        'pilihan_d' => htmlStrips($request->pilihan_d[$key]),
                        'pilihan_e' => htmlStrips($request->pilihan_e[$key]),
                        'jawaban_benar' => $request->jawaban_benar[$key],
                        'ujian_id' => $ujian->id,
                    ]);
                } else { // jika soal lama
                    $soal[$key]->update([
                        'nomer_soal' => $key + 1, // nomer soal dimulai dari 1
                        'pertanyaan' => $request->pertanyaan[$key],
                        'pilihan_a' => bersihkanHTML($request->pilihan_a[$key]),
                        'pilihan_b' => bersihkanHTML($request->pilihan_b[$key]),
                        'pilihan_c' => bersihkanHTML($request->pilihan_c[$key]),
                        'pilihan_d' => bersihkanHTML($request->pilihan_d[$key]),
                        'pilihan_e' => bersihkanHTML($request->pilihan_e[$key]),
                        'jawaban_benar' => $request->jawaban_benar[$key],
                        'ujian_id' => $ujian->id,
                    ]);
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Berhasil memperbarui ujian!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 400,
                'message' => $th->getMessage(),
            ]);
        } finally {
            DB::commit();
        }
    }

    public function getNomer(Request $request)
    {
        $soal = SoalUjianPg::where('ujian_id', $request->ujian_id)
            ->where('nomer_soal', $request->soal_id)->first();

        if ($soal == null) {
            return response()->json([
                'status' => 404,
            ]);
        }

        return response()->json($soal);
    }

    public function removeColumn(Request $request, $id)
    {
        $soal = SoalUjianPg::where('ujian_id', $request->ujian_id)
            ->where('nomer_soal', $request->soal_id)->first();

        if ($soal == null) {
            return response()->json([
                'status' => 400,
                'message' => 'Soal tidak ditemukan!',
            ]);
        } else {
            $soal->delete();
            UjianHasil::where('soal_ujian_pg_id', $soal->id)->delete();

            // mengambil semua soal dalam ujian yang sesuai
            $soals = SoalUjianPg::where('ujian_id', $request->ujian_id)
                ->orderBy('nomer_soal')
                ->get();

            // mengubah nilai nomer_soal pada setiap soal sesuai dengan indeksnya dalam array yang disortir
            foreach ($soals as $index => $soal) {
                $soal->nomer_soal = $index + 1;
                $soal->save();
            }

            return response()->json([
                'status' => 200,
                'message' => 'Berhasil menghapus soal ujian!',
            ]);
        }
    }

    public function fetch($jadwalId)
    {
        $jadwal = JadwalUjian::find(decrypt($jadwalId));

        $soals = SoalUjianPg::where('ujian_id', $jadwal->ujian->id)->get();

        $output = '';
        foreach ($soals as $key => $soal) {
            $no = $key + 1;

            // Selected jawaban
            $selected_a = $soal->jawaban_benar == 'a' ? 'selected' : '';
            $selected_b = $soal->jawaban_benar == 'b' ? 'selected' : '';
            $selected_c = $soal->jawaban_benar == 'c' ? 'selected' : '';
            $selected_d = $soal->jawaban_benar == 'd' ? 'selected' : '';
            $selected_e = $soal->jawaban_benar == 'e' ? 'selected' : '';
            $p_2 = $no === 1 ? '' : 'p-2';
            $ml_2 = $no === 1 ? '' : 'ml-2';

            $output .= '
                <div class="card soalUjian" id="soal_' . $no . '" data-id="' . $no . '">
                    <div class="card-header ' . $p_2 . '">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="font-weight-bold m-0 p-0 ' . $ml_2 . '">Soal No. ' . $no . '</h6>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <div class="form-group">
                            <textarea required name="pertanyaan[]" id="soal" class="form-control soal_ujian" data-id="' . $no . '"
                            rows="5" placeholder="Masukkan soal ujian.">' . $soal->pertanyaan . '</textarea>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-6 mb-2">
                                <label for="pilihan_a">Pilihan A</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">A</div>
                                    </div>
                                    <input value="' . $soal->pilihan_a . '" required type="text"
                                        name="pilihan_a[]" id="pilihan_a" class="form-control"
                                        placeholder="Isi jawaban untuk pilihan A.">
                                </div>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label for="pilihan_d">Pilihan D</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">D</div>
                                    </div>
                                    <input value="' . $soal->pilihan_d . '" required type="text"
                                        name="pilihan_d[]" id="pilihan_d" class="form-control"
                                        placeholder="Isi jawaban untuk pilihan D.">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-6 mb-2">
                                <label for="pilihan_b">Pilihan B</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">B</div>
                                    </div>
                                    <input value="' . $soal->pilihan_b . '" required type="text"
                                        name="pilihan_b[]" id="pilihan_b" class="form-control"
                                        placeholder="Isi jawaban untuk pilihan B.">
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="pilihan_e">Pilihan E</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">E</div>
                                    </div>
                                    <input value="' . $soal->pilihan_e . '" required type="text"
                                        name="pilihan_e[]" id="pilihan_e" class="form-control"
                                        placeholder="Isi jawaban untuk pilihan E.">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 mb-2">
                                <label for="pilihan_c">Pilihan C</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">C</div>
                                    </div>
                                    <input value="' . $soal->pilihan_c . '" required type="text"
                                        name="pilihan_c[]" id="pilihan_c" class="form-control"
                                        placeholder="Isi jawaban untuk pilihan C.">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="jawaban_benar">Pilihan Benar</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                    </div>
                                    <select required name="jawaban_benar[]" id="jawaban_benar"
                                        class="form-control jawaban_benar">
                                        <option value="" selected disabled>-- Silahkan Pilih --</option>
                                        <option value="a" ' . $selected_a . '>A</option>
                                        <option value="b" ' . $selected_b . '>B</option>
                                        <option value="c" ' . $selected_c . '>C</option>
                                        <option value="d" ' . $selected_d . '>D</option>
                                        <option value="e" ' . $selected_e . '>E</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }

        return response()->json($output);
    }
}
