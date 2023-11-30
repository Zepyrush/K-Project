<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\KelolaPengguna\Mahasiswa;
use App\Models\ManajemenKuliah\Tugas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class TugasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $jadwal = JadwalKuliah::find(decrypt($id));

        $tugas = Tugas::where('jadwal_id', $jadwal->id)
            ->where('parent', 0)
            ->latest()
            ->get();

        if (request()->ajax()) {

            $data = $tugas->transform(function ($item) {
                $item->upload = Carbon::parse($item->created_at)->translatedFormat('d M Y - H:i') . " WIB";
                $item->deadline = Carbon::parse($item->pengumpulan)->translatedFormat('d M Y - H:i') . " WIB";
                return $item;
            });

            return datatables()->of($data)
                ->addColumn('action', function ($data) {

                    $tugasMahasiswa = Tugas::whereDoesntHave('nilaiTugas')
                        ->where('file_or_link', '!=', null)
                        ->where('parent', '!=', 0)
                        ->where('parent', $data->id)
                        ->where('pengumpulan', '>', date('Y-m-d H:i:s'))
                        ->latest()
                        ->get();

                    // INI UNTUK CEK APAKAH ADA MAHASISWA YANG BELUM MENGUMPULKAN TUGAS
                    $tugasMhs = [];
                    foreach ($tugasMahasiswa as $tugas) { // CEK APAKAH ADA TUGAS YANG BELUM DI NILAI
                        if ($tugas->nilaiTugas == null) { // JIKA NILAI TUGAS TIDAK ADA
                            $tugasMhs = $tugasMahasiswa->count(); // MAKA HITUNG JUMLAH TUGAS YANG BELUM DI NILAI
                        } else { // JIKA NILAI TUGAS ADA
                            if ($tugas->nilaiTugas->nilai == null) { // CEK APAKAH ADA TUGAS YANG BELUM DI NILAI
                                $tugasMhs = $tugasMahasiswa->count(); // MAKA HITUNG JUMLAH TUGAS YANG BELUM DI NILAI
                            } else { // JIKA TIDAK ADA
                                $tugasMhs = false; // MAKA TUGAS SUDAH DI NILAI SEMUA
                            }
                        }
                    }

                    if ($tugasMhs == false) { // JIKA TUGAS SUDAH DI NILAI SEMUA

                        $button = '<a href="' . route('manajemen.kuliah.tugas.dosen.show', encrypt($data->id)) . '"
                            class="btn btn-primary btn-sm mt-1 "
                            data-toggle="tooltip" title="Lihat Tugas">
                            <i class="fa fa-external-link-alt"></i>
                        </a>';
                    } else { // JIKA TUGAS BELUM DI NILAI SEMUA

                        ($tugasMhs == 0) ?
                            $count = '' :
                            $count = $tugasMhs;

                        $button = '<a href="' . route('manajemen.kuliah.tugas.dosen.show', encrypt($data->id)) . '"
                            class="btn btn-primary btn-sm mt-1  position-relative"
                            data-toggle="tooltip" title="Lihat Tugas">
                            <i class="fa fa-external-link-alt"></i>
                            <span class="badge notif badge-danger badge-pill float-right position-absolute"
                                style="top: -7px">
                                ' . $count . '
                            </span>
                        </a>';
                    }

                    $button .= '
                        <button type="button" name="edit" value="' . encrypt($data->id) . '"
                            class="edit_btn btn btn-warning btn-sm mt-1 " data-toggle="tooltip"
                            title="Edit Tugas">
                            <i class="fa fa-pen"></i>
                        </button>
                    ';

                    $button .= '
                        <button type="button" name="delete" value="' . encrypt($data->id) . '"
                            data-judul="' . $data->judul . '" class="del_btn btn btn-danger btn-sm mt-1 "
                            data-toggle="tooltip" title="Hapus Tugas">
                            <i class="fa fa-trash"></i>
                        </button>
                    ';

                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('dashboard.dosen.tugas', [
            'jadwal' => $jadwal,
            'jadwalDiBuka' => jam_sekarang() >= $jadwal->started_at && jam_sekarang() <= $jadwal->ended_at && $jadwal->hari == hari_ini(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $jadwal = JadwalKuliah::with('kelas', 'matkul')->find(decrypt($id));

        if ($jadwal) {
            if (request()->ajax()) {
                $pertemuan = $jadwal->absens()->where('parent', 0)
                    ->whereDate('created_at', now('Asia/Jakarta'))
                    ->latest()->select('pertemuan')->first();

                return response()->json([
                    'status' => 200,
                    'pertemuan' => $pertemuan,
                    'jadwal' => $jadwal,
                ]);
            } else {
                abort(404);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => "Materi tidak ditemukan!"
            ]);
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

        $jadwal = JadwalKuliah::find($jadwal_id);

        ($request->tipe == 'file') ?
            $validate = 'required|mimes:pdf,doc,docx,ppt,pptx|max:1024' :
            $validate = 'required|url';

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'file_or_link' => $validate,
            'tipe' => 'required',
            'deskripsi' => 'required|string|max:500',
            'pengumpulan' => 'required|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                // insert file pdf to folder assets/file/tugas
                if ($request->tipe == 'file') {
                    $file = $request->file('file_or_link');
                    $file_name = uniqid('TGS_') . '.' . $file->extension();
                    $file->move('assets/file/tugas', $file_name);
                }

                $tugas = Auth::user()->dosen->tugas()->create([ // insert tugas to dosen
                    'judul' => $request->judul,
                    'file_or_link' => $request->tipe == 'file' ? $file_name : $request->file_or_link,
                    'tipe' => $request->tipe,
                    'pengumpulan' => $request->pengumpulan,
                    'pertemuan' => $request->pertemuan,
                    'deskripsi' => $request->deskripsi,
                    'jadwal_id' => $jadwal_id,
                    'matkul_id' => decrypt($request->matkul_id),
                ]);

                $mahasiswas = Mahasiswa::where('kelas_id', decrypt($request->kelas_id))->get();

                foreach ($mahasiswas as $mahasiswa) { // insert tugas to mahasiswa
                    $mahasiswa->tugas()->create([ // insert tugas to mahasiswa
                        'judul' => $request->judul,
                        'file_or_link' => null,
                        'tipe' => $request->tipe,
                        'pengumpulan' => $request->pengumpulan,
                        'pertemuan' => $request->pertemuan,
                        'deskripsi' => $request->deskripsi,
                        'jadwal_id' => $jadwal_id,
                        'matkul_id' => decrypt($request->matkul_id),
                        'mahasiswa_id' => $mahasiswa->id,
                        'dosen_id' => null,
                        'parent' => $tugas->id,
                        'sudah_dinilai' => '0',
                    ]);
                }

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil membuat tugas untuk kelas {$jadwal->kelas->kode} !"
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 401,
                    'title' => 'Terjadi Kesalahan saat menyimpan data',
                    'message' => "Pesan: $th"
                ]);
            } finally {
                DB::commit();
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tugas = Tugas::findOrFail(decrypt($id));
        $tugas->upload = Carbon::parse($tugas->created_at)->translatedFormat('d F Y - H:i') . " WIB";
        $tugas->deadline = Carbon::parse($tugas->pengumpulan)->translatedFormat('d F Y - H:i') . " WIB";

        $tugasMahasiswa = Tugas::with(['mahasiswa' => function ($query) {
            $query->with('user');
        }])
            ->where('file_or_link', '!=', null)
            ->where('parent', '!=', 0)
            ->where('parent', $tugas->id)
            ->latest()
            ->get();


        if (request()->ajax()) {
            $data = $tugasMahasiswa->transform(function ($item) {
                $item->upload = Carbon::parse($item->created_at)->translatedFormat('d F Y - H:i') . " WIB";
                $item->updated = Carbon::parse($item->updated_at)->translatedFormat('d F Y - H:i') . " WIB";
                $item->nilai = $item->nilaiTugas->nilai ?? 'belum_dinilai';
                $item->komentar = $item->nilaiTugas->komentar ?? '-';
                $item->download_tugas = file_exists('assets/file/tugas/' . $item->file_or_link)
                    ? asset('assets/file/tugas/' . $item->file_or_link)
                    : null;

                return $item;
            });

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('mahasiswa', function ($data) {

                    if (file_exists('assets/image/users/' . $data->mahasiswa->user->foto)) {
                        $avatar = asset('assets/image/users/' . $data->mahasiswa->user->foto);
                    } else {
                        $avatar = asset('assets/image/avatar.png');
                    }

                    return '
                        <a href="javascript:void(0)" class="d-flex align-items-center" style="cursor: default">
                            <img src="' . $avatar . '" width="40" class="avatar rounded-circle me-3">
                            <div class="d-block ml-3">
                                <span class="fw-bold name-user">' . $data->mahasiswa->nama . '</span>
                                <div class="small text-secondary">' . $data->mahasiswa->nim . '</div>
                            </div>
                        </a>
                    ';
                })->addColumn('action', function ($data) {

                    ($data->nilai == 'belum_dinilai') ? $className = 'btn-secondary cursor_default' : $className = 'detail_btn btn-primary';
                    ($data->nilai == 'belum_dinilai') ? $title = 'Beri Nilai Terlebih Dahulu' : $title = 'Lebih Detail';

                    $button = '
                        <button type="button" value="' . encrypt($data->id) . '" class="' . $className . ' btn btn-sm"
                            data-toggle="tooltip" title="' . $title . '">
                            <i class="fa fa-eye"></i>
                        </button>
                    ';

                    $button .= '
                        <button type="button" value="' . encrypt($data->id) . '" class="nilai_btn btn btn-warning btn-sm"
                            data-toggle="tooltip" title="Beri Nilai">
                            <i class="fa fa-star text-white"></i>
                        </button>
                    ';

                    return $button;
                })->rawColumns(['action', 'mahasiswa'])
                ->make(true);
        }

        return view('dashboard.dosen._tugas.tugas-show', [
            'tugas' => $tugas,
            'tugasMahasiswa' => $tugasMahasiswa,
            'mahasiswa' => Mahasiswa::where('kelas_id', $tugas->kelas->id)->get(),
        ]);
    }

    public function tableMhsShow(Request $request)
    {
        $tugas = Tugas::findOrFail($request->tugas_id);

        if ($request->filter == 0) { // mahasiswa yang tidak mengumpulkan tugas
            $tugas_mhs = Tugas::with('mahasiswa.user')
                ->where('file_or_link', null)
                ->where('parent', '!=', 0)
                ->where('parent', $tugas->id)
                ->orderBy(Mahasiswa::select('nama')->whereColumn('id', 'mahasiswa_id'), 'asc')
                ->get();

            $mahasiswa = $tugas_mhs->transform(function ($item) {
                return $item->mahasiswa;
            });
        } else { // melihat semua mahasiswa
            $mahasiswa = Mahasiswa::with('user')
                ->where('kelas_id', $tugas->kelas->id)
                ->orderBy('nama', 'asc')
                ->get();
        }

        $data = []; // array untuk menampung data mahasiswa

        foreach ($mahasiswa as $mhs) {
            (file_exists('assets/image/users/' . $mhs->user->foto)) ?
                $avatar = asset('assets/image/users/' . $mhs->user->foto) :
                $avatar = asset('assets/image/avatar.png');

            $data[] = [
                'id' => $mhs->id,
                'nama' => $mhs->nama,
                'nim' => $mhs->nim,
                'foto' => $avatar,
            ];
        }

        return response()->json([
            'tugas' => $tugas,
            'mahasiswa' => $data,
        ], 200);
    }

    public function showNilai($id)
    {
        if (request()->ajax()) {
            $tugas = Tugas::with('mahasiswa')->find(decrypt($id));
            $tugas_parent = Tugas::where('id', $tugas->parent)->firstOrFail();

            return response()->json([
                'status' => 200,
                'tugas' => $tugas,
                'tugas_parent' => $tugas_parent,
            ]);
        } else {
            abort(404);
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
        if (request()->ajax()) {
            $tugas = Tugas::with('jadwal')->find(decrypt($id));

            if ($tugas) {
                return response()->json($tugas);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "Tugas tidak ditemukan!"
                ]);
            }
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
        $tugas = Tugas::find(decrypt($id));

        if ($request->tipe == 'link') {
            $validate = 'required|url';
        } else {
            if (!$request->hasFile('file_or_link')) {
                $validate = 'nullable';
            } else {
                $validate = 'required|mimes:pdf,doc,docx,ppt,pptx|max:1024';
            }
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'file_or_link' => $validate,
            'tipe' => 'required',
            'deskripsi' => 'required|string|max:500',
            'pengumpulan' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'validation' => true,
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                if ($request->tipe == 'file' && $request->hasFile('file_or_link')) {
                    if (File::exists('assets/file/tugas/' . $tugas->file_or_link)) {
                        File::delete('assets/file/tugas/' . $tugas->file_or_link);
                    }

                    $file = $request->file('file_or_link');
                    $file_name = uniqid('TGS_') . '.' . $file->extension();
                    $file->move('assets/file/tugas', $file_name);

                    $tugas->file_or_link = $file_name;
                }

                if ($request->tipe == 'link') {
                    if (File::exists('assets/file/tugas/' . $tugas->file_or_link)) {
                        File::delete('assets/file/tugas/' . $tugas->file_or_link);
                    }

                    $tugas->file_or_link = $request->file_or_link;
                }

                // insert file pdf to folder assets/file/tugas
                $this->updateTugasData($tugas, $request);

                // update tugas mahasiswa
                $tugasMhs = Tugas::where('parent', decrypt($id))->get();

                foreach ($tugasMhs as $item) {
                    $this->updateTugasData($item, $request);
                }

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil mengubah tugas untuk kelas " . $tugas->jadwal->kelas->kode . " !"
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'title' => 'Terjadi Kesalahan saat menyimpan data',
                    'message' => "Pesan: $th"
                ]);
            } finally {
                DB::commit();
            }
        }
    }

    /**
     * updateTugasData
     *
     * @param  mixed $tugas
     * @param  mixed $request
     * @return void
     */
    private function updateTugasData($tugas, $request)
    {
        $tugas->judul = $request->judul;
        $tugas->tipe = $request->tipe;
        $tugas->pengumpulan = $request->pengumpulan;
        $tugas->pertemuan = $request->pertemuan;
        $tugas->deskripsi = $request->deskripsi;

        $tugas->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tugas = Tugas::find(decrypt($id));

        if ($tugas) {
            DB::beginTransaction();
            try {
                if (File::exists('assets/file/tugas/' . $tugas->file_or_link)) {
                    File::delete('assets/file/tugas/' . $tugas->file_or_link);
                }

                $tugasMhs = $tugas->where('parent', decrypt($id))->get();
                foreach ($tugasMhs as $item) {
                    $item->delete();
                    $item->nilaiTugas()->delete();
                }

                $tugas->delete();

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil menghapus tugas !"
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'title' => 'Terjadi Kesalahan saat menghapus data',
                    'message' => "Pesan: $th"
                ]);
            } finally {
                DB::commit();
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => "Tugas tidak ditemukan!"
            ]);
        }
    }

    /**
     * Store Nilai Tugas
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeNilai(Request $request, $id)
    {
        $tugas = Tugas::find(decrypt($id));

        $validator = Validator::make($request->all(), [
            'komentar' => 'nullable|string|max:255',
            'nilai' => 'required|numeric|between:0,100',
        ], [
            'komentar.string' => 'Komentar harus berupa teks!',
            'komentar.max' => 'Komentar maksimal 255 karakter!',
            'nilai.required' => 'Nilai tidak boleh kosong!',
            'nilai.numeric' => 'Nilai harus berupa angka!',
            'nilai.between' => 'Nilai harus berupa angka 0-100!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'validation' => true,
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {

                $tugas->update([
                    'sudah_dinilai' => '1',
                ]);

                $store = Auth::user()->dosen->nilai_tugas()->updateOrCreate([
                    'tugas_id' => $tugas->id,
                ], [
                    'nilai' => $request->nilai,
                    'komentar' => $request->komentar ?? '-',
                ]);

                if ($store->wasRecentlyCreated) {
                    return response()->json([
                        'status' => 200,
                        'message' => "Berhasil memberikan nilai tugas."
                    ]);
                } else if ($store->wasChanged()) {
                    return response()->json([
                        'status' => 200,
                        'changed' => true,
                        'message' => "Berhasil mengubah nilai tugas."
                    ]);
                } else {
                    return response()->json([
                        'status' => 200,
                        'changed' => false,
                        'message' => "Tidak ada perubahan nilai tugas."
                    ]);
                }
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'title' => 'Terjadi Kesalahan saat menyimpan data',
                    'message' => "Pesan: $th"
                ]);
            } finally {
                DB::commit();
            }
        }
    }
}
