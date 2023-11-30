<?php

namespace App\Http\Controllers\Mahasiswa\ManajemenKuliah;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Absen;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\ManajemenKuliah\Materi;
use App\Models\ManajemenKuliah\Tugas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $jadwal_id = decrypt($id);
        $jadwal = JadwalKuliah::where('id', $jadwal_id)->first();

        if (hari_ini() == $jadwal->hari) { // JIKA HARI INI SAMA DENGAN HARI YANG DITENTUKAN
            $waktu_presensi = jam_sekarang() >= $jadwal->started_at && jam_sekarang() <= $jadwal->ended_at;
        } else {
            $waktu_presensi = false;
        }

        $presensi = Absen::where('jadwal_id', $jadwal_id)
            ->where('parent', 0)
            ->whereDate('created_at', now())
            ->first();

        $sudah_presensi = Auth::user()
            ->mahasiswa
            ->presensi($jadwal_id)
            ->first();

        // Ini untuk next dan prev kelas berdasarkan kelas yang sama
        $next = JadwalKuliah::where('id', '>', $jadwal_id)
            ->where('kelas_id', $jadwal->kelas->id)
            ->orderBy('id', 'asc')
            ->first();

        $prev = JadwalKuliah::where('id', '<', $jadwal_id)
            ->where('kelas_id', $jadwal->kelas->id)
            ->orderBy('id', 'desc')
            ->first();

        // UNTUK TUGAS NOTIFIKASI JIKA TUGAS BELUM DINILAI DAN BELUM DIKUMPULKAN
        $tugas_dinilai = $jadwal->tugas()
            ->where('parent', '!=', 0)
            ->where('sudah_dinilai', '0')
            ->where('pengumpulan', '>', date('Y-m-d H:i:s'))
            ->where('file_or_link', null)
            ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
            ->get();

        if ($tugas_dinilai->isNotEmpty() && $sudah_presensi) { // JIKA ADA TUGAS YANG BELUM DINILAI
            $tugas = $jadwal->tugas()
                ->where('parent', 0)
                ->where('pengumpulan', '>', date('Y-m-d H:i:s'))
                ->count();
        } else {
            $tugas = false;
        }

        $absens = Auth::user()->mahasiswa->absens()->where('jadwal_id', $jadwal_id)->get();

        if (request()->ajax()) {
            $data = $absens->transform(function ($absen) use ($presensi, $sudah_presensi, $waktu_presensi, $jadwal) {
                $status = $this->statusAbsen($absen, $presensi, $sudah_presensi, $waktu_presensi, $jadwal);

                $absen->tanggal = $absen->created_at->format('L, d F Y');
                $absen->matkul = $absen->jadwal->matkul->nama;
                $absen->status = $status;

                return $absen;
            });

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '
                        <button type="button" id="' . encrypt($data->id) . '" class="detail_presensi btn btn-primary btn-sm"
                            data-toggle="tooltip" title="Lebih Detail">
                            <i class="fa fa-external-link-alt"></i>
                        </button>
                    ';

                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.mahasiswa.kelas', [
            'jadwal' => $jadwal,
            'waktu_presensi' => $waktu_presensi,
            'presensi' => $presensi,
            'sudah_presensi' => $sudah_presensi,
            'next' => $next,
            'prev' => $prev,
            'tugas' => $tugas,
        ]);
    }

    /**
     * Status absensi mahasiswa
     *
     * @param  mixed $absen
     * @param  mixed $presensi
     * @param  mixed $sudah_presensi
     * @param  mixed $waktu_presensi
     * @param  mixed $jadwal
     * @return void
     */
    private function statusAbsen($absen, $presensi, $sudah_presensi, $waktu_presensi, $jadwal)
    {
        if ($waktu_presensi && !$sudah_presensi) { // JIKA WAKTU PRESENSI DAN BELUM PRESENSI
            if ($absen->pertemuan == $presensi->pertemuan) { // JIKA PERTEMUAN SAMA
                $status = 'belum_absen';
            } else { // JIKA PERTEMUAN TIDAK SAMA
                if ($absen->status == 1) { // jika status hadir
                    $status = 'hadir';
                } else {
                    $status = 'tidak_hadir';
                }
            }
        } else { // JIKA TIDAK WAKTU PRESENSI
            if ($absen->status == 1) { // jika status hadir
                $status = 'hadir';
            } else {
                $status = 'tidak_hadir';
            }
        }

        return $status;
    }

    public function detailInfoPresensi(Request $request, $absenId)
    {
        if (request()->ajax()) {
            $jadwal_id = decrypt($request->jadwal_id);
            $jadwal = JadwalKuliah::where('id', $jadwal_id)->first();

            $absen = Absen::where('id', decrypt($absenId))
                ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
                ->first();

            (hari_ini() == $jadwal->hari) ?
                $waktu_presensi = jam_sekarang() >= $jadwal->started_at && jam_sekarang() <= $jadwal->ended_at :
                $waktu_presensi = false;

            $presensi = Absen::where('jadwal_id', $jadwal_id)
                ->where('parent', 0)
                ->whereDate('created_at', now())
                ->first();

            $sudah_presensi = Auth::user()
                ->mahasiswa
                ->presensi($jadwal_id)
                ->first();

            $status = $this->statusAbsen($absen, $presensi, $sudah_presensi, $waktu_presensi, $jadwal);

            if ($status == "hadir") { // JIKA HADIR
                $status = "<span>Hadir <i class='fas fa-check-circle text-success ml-1'></i></span>";
            } else if ($status == "tidak_hadir") { // JIKA TIDAK HADIR
                $status = "<span>Tidak Hadir <i class='fas fa-times-circle text-danger ml-1'></i></span>";
            } else { // JIKA BELUM PRESENSI
                $status = "<span>Belum Absen <i class='fas fa-question-circle text-warning ml-1'></i></span>";
            }

            $data = [
                'matkul' => $jadwal->matkul->nama,
                'status' => $status,
                'tanggal' => Carbon::parse($absen->created_at)->translatedFormat('l, d F Y'),
                'pertemuan' => "Ke-" . $absen->pertemuan,
                'rangkuman' => $absen->rangkuman,
                'berita_acara' => $absen->berita_acara,
            ];

            return response()->json([
                'jadwal' => $jadwal,
                'absen' => $absen,
                'data' => $data,
            ]);
        } else {
            abort(404);
        }
    }

    public function presensi(Request $request)
    {
        $jadwal_id = decrypt($request->jadwal);
        $absen = Absen::where('jadwal_id', $jadwal_id)
            ->where('parent', 0)
            ->latest()
            ->first();

        $presensi = Auth::user()->mahasiswa->presensi($jadwal_id)->first();

        if (!$presensi) { // Jika belum pernah melakukan presensi
            Absen::updateOrCreate(
                [ // Jika belum pernah melakukan presensi
                    'mahasiswa_id' => Auth::user()->mahasiswa->id,
                    'parent' => $absen->id,
                ],
                [ // Jika sudah pernah melakukan presensi
                    'jadwal_id' => $jadwal_id,
                    'pertemuan' => $absen->pertemuan,
                    'parent' => $absen->id,
                    'status' => 1,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil melakukan absensi di pertemuan ke-' . $absen->pertemuan . '.',
        ]);
    }

    public function materi($id)
    {
        $jadwal_id = decrypt($id);

        $jadwal = JadwalKuliah::find($jadwal_id);

        $materis = Materi::where('matkul_id', $jadwal->matkul_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('dosen_id', $jadwal->dosen_id)
            ->latest()
            ->get();

        $materiFiles = Materi::where('tipe', 'pdf')
            ->where('matkul_id', $jadwal->matkul_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('dosen_id', $jadwal->dosen_id)
            ->latest()
            ->get();

        if (request()->ajax()) {

            $data = $materiFiles->transform(function ($item) {
                $item->materi_dosen = $item->dosen->nama;
                $item->materi_kelas = $item->kelas->nama;
                $item->materi_matkul = $item->matkul->nama;
                $item->upload = Carbon::parse($item->created_at)->translatedFormat('l, d F Y');

                return $item;
            });

            return datatables()->of($data)
                ->addColumn('action', function ($data) {

                    if (file_exists('assets/file/materi/' . $data->file_or_link)) {
                        $path = asset('assets/file/materi/' . $data->file_or_link);
                    } else {
                        $path = asset('assets/file/default.pdf');
                    }

                    $button = '<a download href=' . $path . ' class="download_btn btn btn-primary btn-sm mt-1 mr-1" data-toggle="tooltip" title="Download File Materi"><i class="fas fa-download"></i></a download>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        if (Auth::user()->mahasiswa->kelas->id != $jadwal->kelas_id) {
            abort(404);
        }

        return view('dashboard.mahasiswa.materi', [
            'jadwal' => $jadwal,
            'materis' => $materis,
        ]);
    }

    public function tugas($jadwal_id)
    {
        $jadwal_id = decrypt($jadwal_id);

        $jadwal = JadwalKuliah::find($jadwal_id);

        $tugas = Tugas::where('jadwal_id', $jadwal_id)
            ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
            ->orderBy("pertemuan", "desc")
            ->get();

        if (request()->ajax()) {
            $data = $tugas->transform(function ($item) {
                $item->upload = Carbon::parse($item->created_at)
                    ->translatedFormat('l, d F Y - H:i') . " WIB";

                $item->deadline = Carbon::parse($item->pengumpulan)
                    ->translatedFormat('l, d F Y - H:i') . " WIB";

                return $item;
            });

            return datatables()->of($data)
                ->addColumn('action', function ($data) use ($jadwal) {

                    // jika belum absen pada pertemuan yang sama
                    $sudah_presensi = Auth::user()
                        ->mahasiswa
                        ->presensi($jadwal->id)
                        ->first();

                    $parentTugas = Tugas::where('pertemuan', $data->pertemuan)
                        ->where('parent', 0)
                        ->where('jadwal_id', $jadwal->id)
                        ->first();

                    if ($parentTugas->tipe == 'file') {
                        (file_exists('assets/file/tugas/' . $parentTugas->file_or_link)) ?
                            $path = asset('assets/file/tugas/' . $parentTugas->file_or_link) :
                            $path = asset('assets/file/default.pdf');

                        $button = '
                            <a download href=' . $path . '
                                class="download_btn btn btn-primary btn-sm mt-1"
                                data-toggle="tooltip" title="Download Soal Tugas">
                                <i class="fas fa-download"></i>
                            </a>
                        ';
                    } else {
                        $button = '
                            <a href="' . $parentTugas->file_or_link . '" target="_blank"
                                class="btn btn-primary btn-sm mt-1"
                                data-toggle="tooltip" title="Lihat Soal Tugas">
                                <i class="fa fa-external-link-alt"></i>
                            </a>
                        ';
                    }

                    if ($parentTugas->pengumpulan < date('Y-m-d H:i:s')) { // Jika waktu pengumpulan sudah habis
                        if ($data->sudah_dinilai == 1) { // Jika tugas sudah dinilai
                            $button .= '
                                <button type="button" value="' . $parentTugas->id . '"
                                    class="btn-secondary cursor_default btn btn-sm mt-1"
                                    data-toggle="tooltip" title="Tugas Sudah Dinilai">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            ';
                        } else {
                            $button .= '
                                <button type="button" value="' . $parentTugas->id . '"
                                    class="btn-secondary cursor_default btn btn-sm mt-1"
                                    data-toggle="tooltip" title="Waktu Pengumpulan Sudah Habis">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            ';
                        }
                    } else { // Jika waktu pengumpulan masih ada
                        if ($data->sudah_dinilai == 1) { // Jika tugas sudah dinilai
                            $button .= '
                                <button type="button" value="' . $parentTugas->id . '"
                                    class="btn-secondary cursor_default btn btn-sm mt-1"
                                    data-toggle="tooltip" title="Tugas Sudah Dinilai">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            ';
                        } else {
                            if (!$sudah_presensi) { // Jika belum absen
                                $button .= '
                                    <button type="button" value="' . $parentTugas->id . '"
                                        class="btn-secondary cursor_default btn btn-sm mt-1"
                                        data-toggle="tooltip" title="Anda Belum Absen">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>
                                ';
                            } else {
                                $button .= '
                                    <button type="button" value="' . $parentTugas->id . '"
                                    class="send_btn btn-success btn btn-sm mt-1" data-toggle="tooltip"
                                    title="Kirim Tugas">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>
                                ';
                            }
                        }
                    }

                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('dashboard.mahasiswa.tugas', [
            'jadwal' => $jadwal,
        ]);
    }

    public function tugasSelesai($id)
    {
        $jadwal_id = decrypt($id);

        $tugasSelesai = Auth::user()->mahasiswa->tugas()
            ->where('file_or_link', '!=', null)
            ->where('jadwal_id', $jadwal_id)
            ->orderBy("pertemuan", "desc")
            ->get();

        if (request()->ajax()) {
            $data = $tugasSelesai->transform(function ($item) {
                $item->nilai = $item->nilaiTugas->nilai ?? '<span class="badge badge-danger">Belum dinilai</span>';
                $item->komentar = $item->nilaiTugas->komentar ?? '-';
                $item->link_tugas = $item->file_or_link ?? '-';

                ($item->nilaiTugas != null) ?
                    $item->create = Carbon::parse($item->nilaiTugas->created_at)->translatedFormat('l, d F Y - H:i') . " WIB" :
                    $item->create = '-';
                // $item->update = Carbon::parse($item->updated_at)->translatedFormat('l, d F Y - H:i');

                return $item;
            });

            return datatables()->of($data)
                ->addIndexColumn()
                ->rawColumns(['nilai'])
                ->make(true);
        }
    }

    public function lihatTugas($jadwal_id, $tugas_id)
    {
        if (request()->ajax()) {
            $jadwal = JadwalKuliah::find(decrypt($jadwal_id));

            $tugas = Tugas::where('id', $tugas_id)
                ->firstOrFail();

            $getParentTugas = Tugas::where('pertemuan', $tugas->pertemuan)
                ->where('parent', 0)
                ->where('jadwal_id', $jadwal->id)
                ->first();

            $tugasSelesai = Auth::user()->mahasiswa->tugas()
                ->with('nilaiTugas')
                ->where('jadwal_id', $jadwal->id)
                ->where('parent', '!=', 0)
                ->where('pertemuan', $tugas->pertemuan)
                ->latest()
                ->first();

            // get tugas mahasiswa if exist
            $tugas_mhs = Tugas::with('nilaiTugas')->where('parent', $getParentTugas->id)
                ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
                ->first();

            if ($tugas->pengumpulan < date('Y-m-d H:i:s')) { // Jika waktu pengumpulan sudah habis
                if ($tugasSelesai != null) { // Jika tugas sudah selesai
                    return response()->json([
                        'status' => 500,
                        'message' => '<span class="font-weight-bold text-uppercase">waktu pengumpulan tugas sudah habis</span> <hr> Kamu tidak bisa mengirim atau mengedit tugas ini lagi.',
                    ]);
                } else { // Jika tugas belum selesai
                    return response()->json([
                        'status' => 500,
                        'message' => '<span class="font-weight-bold text-uppercase">waktu pengumpulan tugas sudah habis</span> <hr> Kapan-kapan kirim tugasnya tepat waktu ya <b>' . Auth::user()->mahasiswa->nama . '</b> !',
                    ]);
                }
            } else { // Jika waktu pengumpulan masih ada
                if ($tugasSelesai != null) { // Jika tugas sudah selesai
                    if ($tugasSelesai->nilaiTugas == null) { // Jika tugas belum dinilai
                        return response()->json([
                            'status' => 200,
                            'tugas_mhs' => $tugas_mhs,
                        ]);
                    } else { // Jika tugas sudah dinilai
                        return response()->json([
                            'status' => 500,
                            'message' => '<span class="font-weight-bold text-uppercase">tugas kamu sudah dinilai oleh dosen</span> <hr> Kamu sudah tidak bisa mengirim atau mengedit tugas ini lagi.',
                        ]);
                    }
                } else { // Jika tugas belum selesai
                    return response()->json([
                        'status' => 200,
                        'tugas_mhs' => $tugas_mhs,
                    ]);
                }
            }
        } else {
            abort(404);
        }
    }

    public function storeTugas(Request $request, $jadwal_id, $tugas_id)
    {
        $jadwal = JadwalKuliah::find(decrypt($jadwal_id));
        $tugas = Tugas::find($tugas_id);

        if ($tugas) {
            DB::beginTransaction();
            try {
                if ($tugas->pengumpulan > date('Y-m-d H:i:s')) {
                    $validator = Validator::make(
                        request()->all(),
                        [
                            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
                        ],
                        [
                            'file.required' => 'File tugas tidak boleh kosong!',
                            'file.file' => 'File tugas harus berupa file!',
                            'file.mimes' => 'File tugas harus berupa file pdf, doc, docx, xls, xlsx, ppt, pptx!',
                            'file.max' => 'File tugas maksimal 2MB!',
                        ]
                    );

                    if ($validator->fails()) {
                        return response()->json([
                            'status' => 500,
                            'error' => 'validation',
                            'message' => $validator->errors()->toArray(),
                        ]);
                    } else {
                        $getParentTugas = Tugas::where('pertemuan', $tugas->pertemuan)
                            ->where('parent', 0)
                            ->where('jadwal_id', $jadwal->id)
                            ->first();

                        $tugasMhs = Tugas::where('parent', $getParentTugas->id)
                            ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
                            ->first();

                        ($tugasMhs != null) ? $tugasMhs->delete() : '';

                        // Tambahan Kode
                        if ($request->hasFile('file')) {
                            if (File::exists('assets/file/tugas/' . $tugasMhs->file_or_link)) {
                                File::delete('assets/file/tugas/' . $tugasMhs->file_or_link);
                            }

                            $file = $request->file('file');
                            $file_name = uniqid("MHS-") . '.' . $file->extension();
                            $file->move('assets/file/tugas', $file_name);
                        }

                        $sending = Auth::user()->mahasiswa->tugas()->updateOrCreate(
                            [ // Cek apakah tugas sudah pernah dikirim
                                'mahasiswa_id' => Auth::user()->mahasiswa->id,
                                'parent' => $getParentTugas->id,
                                'created_at' => now(),
                                'file_or_link' => $file_name
                            ],
                            [ // Jika belum pernah dikirim
                                'matkul_id' => $jadwal->matkul_id,
                                'jadwal_id' => $jadwal->id,
                                'judul' => $tugas->judul,
                                'parent' => $getParentTugas->id,
                                'tipe' => 'link',
                                'pertemuan' => $tugas->pertemuan,
                                'pengumpulan' => $tugas->pengumpulan,
                                'deskripsi' => $tugas->deskripsi,
                            ]
                        );

                        if ($sending->wasRecentlyCreated) {
                            return response()->json([
                                'status' => 200,
                                'message' => 'Tugas berhasil dikirim.',
                            ]);
                        } else if ($sending->wasChanged()) {
                            return response()->json([
                                'status' => 200,
                                'message' => 'Tugas berhasil diperbarui.',
                            ]);
                        } else {
                            return response()->json([
                                'status' => 200,
                                'changed' => false,
                                'message' => 'Tidak ada perubahan pada tugas!',
                            ]);
                        }
                    }
                } else { // Jika waktu pengumpulan sudah habis
                    return response()->json([
                        'status' => 500,
                        'error' => 'timeout',
                        'message' => 'Yah.. waktu pengumpulan tugas sudah habis :( <br> Kapan-kapan kirim tugasnya tepat waktu ya <b>' . Auth::user()->mahasiswa->nama . '</b> !',
                    ]);
                }
            } catch (\Throwable $th) {
                DB::rollback();

                return response()->json([
                    'status' => 500,
                    'error' => 'server',
                    'message' => 'Terjadi kesalahan pada server! <br> Pesan error: ' . $th->getMessage(),
                ]);
            } finally {
                DB::commit();
            }
        } else { // Jika tugas tidak ditemukan
            return response()->json([
                'status' => 500,
                'error' => 'notfound',
                'message' => 'Tugas tidak ditemukan!',
            ]);
        }
    }
}
