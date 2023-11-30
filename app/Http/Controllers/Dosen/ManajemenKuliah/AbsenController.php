<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Absen;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as Jadwal;
use App\Models\KelolaPengguna\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AbsenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $absensi = Absen::with('jadwal')->where([
            ['dosen_id', Auth::user()->dosen->id],
            ['parent', 0]
        ])->whereDate('created_at', Carbon::today())
            ->latest()
            ->get();

        if (request()->ajax()) {
            $data = $absensi->transform(function ($item) {
                $item->absen_kelas = $item->jadwal->kelas->kode;
                $item->absen_matkul = $item->jadwal->matkul->nama;

                return $item;
            });

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $jadwal = $data->jadwal;

                    if (jam_sekarang() >= $jadwal->started_at && jam_sekarang() <= $jadwal->ended_at && $jadwal->hari == hari_ini()) {
                        $button = '
                            <a href="' . route('manajemen.kuliah.kelas.dosen.index', encrypt($data->jadwal_id)) . '"
                                class="btn btn-primary btn-sm mt-1 lihat_btn" data-toggle="tooltip"
                                title="Lihat Kelas">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        ';
                    } else {
                        $button = '
                            <a href="javascript:void(0)"
                                class="btn btn-sm btn-secondary cursor_default mt-1" data-toggle="tooltip"
                                title="Kelas ini sudah selesai.">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        ';
                    }

                    $button .= '
                        <button type="button" value="' . encrypt($data->id) . '"
                            class="edit_btn btn btn-warning btn-sm mt-1" data-toggle="tooltip"
                            title="Edit Data Absensi">
                            <i class="fas fa-pen"></i>
                        </button>
                    ';

                    $button .= '
                        <button type="button" value="' . encrypt($data->id) . '"
                            class="del_btn btn btn-danger btn-sm mt-1" data-toggle="tooltip"
                            title="Hapus Data Absensi">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';

                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.dosen.absen', compact('absensi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        if (request()->ajax()) {
            $jadwal = Jadwal::with('kelas', 'matkul')->find(decrypt($id));

            $absen = Absen::where('dosen_id', Auth::user()->dosen->id)
                ->where('jadwal_id', $jadwal->id)
                ->latest()
                ->first();

            return response()->json([
                'jadwal' => $jadwal,
                'absen' => $absen
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $jadwal_id = decrypt($request->jadwal);

        $validator = Validator::make($request->all(), [
            'pertemuan' => 'required|numeric|unique:absens,pertemuan,NULL,id,jadwal_id,' . $jadwal_id,
            'berita_acara' => 'nullable|string|max:1000',
            'rangkuman' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            DB::beginTransaction();
            try {
                $absen = Auth::user()->dosen->absens()->create([
                    'jadwal_id' => $jadwal_id,
                    'pertemuan' => $request->pertemuan,
                    'berita_acara' => $request->berita_acara ?? '-',
                    'rangkuman' => $request->rangkuman ?? '-',
                ]);

                $mahasiswas = Mahasiswa::where('kelas_id', $request->kelas)->get();

                foreach ($mahasiswas as $mahasiswa) {
                    $mahasiswa->absens()->create([
                        'parent' => $absen->id,
                        'jadwal_id' => $jadwal_id,
                        'pertemuan' => $request->pertemuan,
                        'berita_acara' => $request->berita_acara ?? '-',
                        'rangkuman' => $request->rangkuman ?? '-',
                        'mahasiswa_id' => $mahasiswa->id,
                    ]);
                }

                return response()->json([
                    'status' => 200,
                    'title' => "Berhasil!",
                    'message' => "Berhasil membuat absensi hari ini!"
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 401,
                    'title' => "Terjadi kesalahan! saat menyimpan data!",
                    'message' => "Pesan: $th"
                ]);
            } finally {
                DB::commit();
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $absen = Absen::with('jadwal')->find(decrypt($id));
        $absen->absen_kelas = $absen->jadwal->kelas->kode;
        $absen->absen_matkul = $absen->jadwal->matkul->nama;

        if (request()->ajax()) {
            return response()->json($absen);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $absen = Absen::find(decrypt($id));

        if ($absen) {
            $validator = Validator::make($request->all(), [
                'pertemuan' => 'required|numeric|unique:absens,pertemuan,' . $absen->id . ',id,jadwal_id,' . $absen->jadwal_id . ',dosen_id,' . Auth::user()->dosen->id . ',created_at,' . $absen->created_at,
                'berita_acara' => 'nullable|string|max:1000',
                'rangkuman' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()->toArray()
                ]);
            } else {
                DB::beginTransaction();
                try {
                    $childAbsens = Absen::where('parent', $absen->id)->get();

                    foreach ($childAbsens as $childAbsen) {
                        $childAbsen->pertemuan = $request->pertemuan;
                        $childAbsen->berita_acara = $request->berita_acara;
                        $childAbsen->rangkuman = $request->rangkuman;
                        $childAbsen->update();
                    }

                    $absen->pertemuan = $request->pertemuan;
                    $absen->berita_acara = $request->berita_acara;
                    $absen->rangkuman = $request->rangkuman;
                    $absen->update();

                    return response()->json([
                        'status' => 200,
                        'title' => "Berhasil!",
                        'message' => "Berhasil mengubah absensi!"
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 401,
                        'title' => "Terjadi kesalahan! saat memperbarui data!",
                        'message' => "Pesan: $th"
                    ]);
                } finally {
                    DB::commit();
                }
            }
        } else {
            return response()->json([
                'status' => 400,
                'title' => "Terjadi kesalahan! saat menyimpan data!",
                'message' => "Data absensi tidak ditemukan!"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $absen = Absen::find(decrypt($id));

        if ($absen) {
            DB::beginTransaction();
            try {
                $absen->delete();

                Absen::whereNotNull('mahasiswa_id')->where('parent', $absen->id)->delete();

                return response()->json([
                    'status' => 200,
                    'title' => "Berhasil!",
                    'message' => "Berhasil menghapus absensi!"
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'title' => "Terjadi kesalahan! saat menghapus data!",
                    'message' => "Pesan: $th"
                ]);
            } finally {
                DB::commit();
            }
        } else {
            return response()->json([
                'status' => 400,
                'title' => "Terjadi kesalahan! saat menghapus data!",
                'message' => "Pesan: Data absensi tidak ditemukan!"
            ]);
        }
    }
}
