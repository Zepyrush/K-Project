<?php

namespace App\Http\Controllers\Dosen\ManajemenKuliah;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Absen;
use App\Models\ManajemenKuliah\Jadwal\Kuliah as JadwalKuliah;
use App\Models\ManajemenKuliah\Materi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MateriController extends Controller
{
    public function index($id)
    {
        $jadwal = JadwalKuliah::with('kelas', 'matkul')
            ->where('id', decrypt($id))
            ->where('dosen_id', Auth::user()->dosen->id)
            ->firstOrFail();

        $materiVideos = Materi::with('kelas', 'matkul')
            ->where('tipe', 'youtube')
            ->where('matkul_id', $jadwal->matkul_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('dosen_id', $jadwal->dosen_id)
            ->latest()
            ->get();

        $materiSlides = Materi::with('kelas', 'matkul')
            ->where('tipe', 'slide')
            ->where('matkul_id', $jadwal->matkul_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('dosen_id', $jadwal->dosen_id)
            ->latest()
            ->get();

        $materiFiles = Materi::with('kelas', 'matkul')
            ->where('tipe', 'pdf')
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
                $item->diupload_pada = Carbon::parse($item->created_at)
                    ->translatedFormat('d M Y ~ H:i') . " WIB";

                return $item;
            });

            return datatables()->of($data)
                ->addColumn('action', function ($data) {

                    if (file_exists('assets/file/materi/' . $data->file_or_link)) {
                        $path = asset('assets/file/materi/' . $data->file_or_link);
                    } else {
                        $path = asset('assets/file/default.pdf');
                    }

                    $button = '
                        <a download href=' . $path . ' class="download_btn btn btn-primary btn-sm
                            mt-1 " data-toggle="tooltip" title="Download Data Materi">
                            <i class="fas fa-download"></i>
                        </a>
                    ';

                    $button .= '
                        <button type="button" value="' . encrypt($data->id) . '"
                            class="edit_btn btn btn-warning btn-sm mt-1 " data-toggle="tooltip"
                            title="Edit Data Materi">
                            <i class="fas fa-pen"></i>
                        </button>
                    ';

                    $button .= '
                        <button type="button" value="' . encrypt($data->id) . '"
                            data-judul="' . $data->judul . '" class="del_btn btn btn-danger btn-sm mt-1 "
                            data-toggle="tooltip" title="Hapus Data Materi">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';

                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('dashboard.dosen.materi', [
            'jadwal' => $jadwal,
            'materiFiles' => $materiFiles,
            'materiVideos' => $materiVideos,
            'materiSlides' => $materiSlides,
            'jadwalDiBuka' => jam_sekarang() >= $jadwal->started_at && jam_sekarang() <= $jadwal->ended_at && $jadwal->hari == hari_ini(),
        ]);
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

        ($request->tipe == 'pdf') ?
            $validate = 'required|mimes:pdf|max:1024' :
            $validate = 'required';

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'file_or_link' => $validate,
            'tipe' => 'required',
            'pertemuan' => 'required|numeric|unique:materis,pertemuan,NULL,id,matkul_id,' . decrypt($request->matkul_id),
            'deskripsi' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                // insert file pdf to folder assets/file/materi
                if ($request->tipe == 'pdf') {
                    $file = $request->file('file_or_link');
                    $matkul = Str::slug($request->judul, '_');
                    $file_name = $request->kelas . '_' . $request->matkul_kode . '_' . 'P' . $request->pertemuan . '_' . rand(100, 999) . '.' . $file->extension();
                    $file->move('assets/file/materi', $file_name);
                }

                Auth::user()->dosen->materis()->create([
                    'judul' => $request->judul,
                    'file_or_link' => $request->tipe == 'pdf' ? $file_name : $request->file_or_link,
                    'tipe' => $request->tipe,
                    'pertemuan' => $request->pertemuan,
                    'deskripsi' => $request->deskripsi,
                    'jadwal_id' => $jadwal_id,
                    'kelas_id' => decrypt($request->kelas_id),
                    'matkul_id' => decrypt($request->matkul_id),
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil menambahkan materi!'
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 401,
                    'title' => 'Terjadi Kesalahan saat menyimpan data',
                    'message' => "message: $th"
                ]);
            } finally {
                DB::commit();
            }
        }
    }

    public function create($jadwal_id)
    {
        $jadwal_id = decrypt($jadwal_id);
        $jadwal = JadwalKuliah::find($jadwal_id);

        if (request()->ajax()) {

            $absen = collect(Absen::where('dosen_id', Auth::user()->dosen->id)
                ->where('jadwal_id', $jadwal_id)
                ->whereDate('created_at', date('Y-m-d'))
                ->first());

            if ($absen->isNotEmpty()) { // jika sudah absen
                $materi = Materi::where('kelas_id', $jadwal->kelas->id)
                    ->where('matkul_id', $jadwal->matkul->id)
                    ->where('dosen_id', $jadwal->dosen->id)
                    ->where('tipe', '!=', 'slide')
                    ->whereDate('created_at', now('Asia/Jakarta'))
                    ->first();

                if ($materi != null) { // jika sudah membuat materi
                    if ($materi->count() > 0) { // jika sudah membuat materi
                        return response()->json([
                            'status' => 500,
                            'message' => 'Oops, materi sudah dibuat untuk pertemuan hari ini! Silahkan edit materi saja yang sudah dibuat.'
                        ]);
                    } else { // jika belum membuat materi
                        $pertemuan = $jadwal->absens()->where('parent', 0)
                            ->whereDate('created_at', now('Asia/Jakarta'))
                            ->latest()->select('pertemuan')->first();

                        return response()->json([
                            'status' => 200,
                            'pertemuan' => $pertemuan,
                            'jadwal' => $jadwal,
                        ]);
                    }
                } else { // jika belum membuat materi
                    $pertemuan = $jadwal->absens()->where('parent', 0)
                        ->whereDate('created_at', now('Asia/Jakarta'))
                        ->latest()->select('pertemuan')->first();

                    return response()->json([
                        'status' => 200,
                        'pertemuan' => $pertemuan,
                        'jadwal' => $jadwal,
                    ]);
                }
            } else { // jika belum absen
                return response()->json([
                    'status' => 500,
                    'message' => 'Anda belum membuat absensi untuk pertemuan hari ini.',
                    'error' => 'absensi',
                ]);
            }
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
        $materi = Materi::find(decrypt($id));

        if (request()->ajax()) {
            if ($materi) {
                return response()->json($materi);
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "Materi tidak ditemukan!"
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
        $materi = Materi::findOrFail(decrypt($id));

        if ($request->tipe == 'youtube') { // jika tipe materi adalah youtube
            $validate = 'required';
        } else { // jika tipe materi adalah pdf
            if (!$request->hasFile('file_or_link')) { // jika tidak ada file yang diupload
                $validate = 'nullable';
            } else { // jika ada file yang diupload
                $validate = 'required|mimes:pdf|max:1024';
            }
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'file_or_link' => $validate,
            'tipe' => 'required',
            'pertemuan' => 'required|numeric|unique:materis,pertemuan,' . decrypt($id) . ',id,matkul_id,' . $materi->matkul_id,
            'deskripsi' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                if ($request->tipe == 'pdf' && $request->hasFile('file_or_link')) {
                    if (File::exists('assets/file/materi/' . $materi->file_or_link)) {
                        File::delete('assets/file/materi/' . $materi->file_or_link);
                    }

                    $file = $request->file_or_link;

                    $file_name = $materi->kelas->kode . '_' .
                        $materi->matkul->kode . '_' . 'P' . $request->pertemuan . '_' .
                        rand(100, 999) . '.' . $file->extension();

                    $file->move('assets/file/materi', $file_name);

                    $materi->file_or_link = $file_name;
                }

                if ($request->tipe == 'youtube') {
                    if (File::exists('assets/file/materi/' . $materi->file_or_link)) {
                        File::delete('assets/file/materi/' . $materi->file_or_link);
                    }

                    $materi->file_or_link = $request->file_or_link;
                }

                $materi->judul = $request->judul;
                $materi->tipe = $request->tipe;
                $materi->pertemuan = $request->pertemuan;
                $materi->deskripsi = $request->deskripsi;

                if ($materi->isDirty()) {
                    $materi->update();

                    return response()->json([
                        'status' => 200,
                        'message' => 'Berhasil mengubah materi!'
                    ]);
                } else {
                    return response()->json([
                        'status' => 200,
                        'nothing' => 1,
                        'message' => 'Tidak ada perubahan data!'
                    ]);
                }
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 401,
                    'title' => 'Terjadi Kesalahan saat memperbarui data',
                    'message' => "message: $th"
                ]);
            } finally {
                DB::commit();
            }
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
        $materi = Materi::find(decrypt($id));

        if ($materi) {
            DB::beginTransaction();
            try {
                if ($materi->tipe == 'pdf') {
                    if (File::exists('assets/file/materi/' . $materi->file_or_link)) {
                        File::delete('assets/file/materi/' . $materi->file_or_link);
                    }
                } else if ($materi->tipe == 'slide') {
                    if (File::exists('assets/file/slide/' . $materi->file_or_link)) {
                        File::delete('assets/file/slide/' . $materi->file_or_link);
                    }
                }

                $materi->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil menghapus materi!'
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 401,
                    'title' => 'Terjadi Kesalahan saat menghapus data',
                    'message' => "message: $th"
                ]);
            } finally {
                DB::commit();
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Materi tidak ditemukan!"

            ]);
        }
    }

    /**
     * Store a slide in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function storeSlide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_or_link' => 'required|mimes:zip|max:10240',
            'judul' => 'required|string|max:50',
        ], [
            'file_or_link.required' => 'File zip harus diisi',
            'file_or_link.mimes' => 'File harus berformat zip',
            'file_or_link.max' => 'File zip maksimal 10 MB',
            'judul.required' => 'Judul slide harus diisi',
            'judul.string' => 'Judul slide harus berupa string',
            'judul.max' => 'Judul slide maksimal 50 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                $file = $request->file_or_link;
                $file_name = uniqid('SLD_') . '.' . $file->extension();
                $file->move('assets/file/slide', $file_name);

                Auth::user()->dosen->materis()->create([
                    'judul' => $request->judul,
                    'file_or_link' => $file_name,
                    'tipe' => 'slide',
                    'pertemuan' => '-',
                    'deskripsi' => '-',
                    'jadwal_id' => decrypt($request->jadwal),
                    'kelas_id' => decrypt($request->kelas_id),
                    'matkul_id' => decrypt($request->matkul_id),
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil menambahkan slide!'
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 401,
                    'title' => 'Terjadi Kesalahan saat menyimpan data',
                    'message' => "message: $th"
                ]);
            } finally {
                DB::commit();
            }
        }
    }

    public function updateSlide(Request $request, $id)
    {
        $slide = Materi::findOrFail(decrypt($id));

        if (!$request->hasFile('file_or_link')) {
            $validate = 'nullable';
        } else {
            $validate = 'required|mimes:zip|max:10240';
        }

        if ($slide) {

            $validator = Validator::make($request->all(), [
                'file_or_link' => $validate,
                'judul' => 'required|string|max:50',
            ], [
                'file_or_link.required' => 'File zip harus diisi',
                'file_or_link.mimes' => 'File harus berformat zip',
                'file_or_link.max' => 'File zip maksimal 10 MB',
                'judul.required' => 'Judul slide harus diisi',
                'judul.string' => 'Judul slide harus berupa string',
                'judul.max' => 'Judul slide maksimal 50 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()->toArray(),
                ]);
            } else {
                DB::beginTransaction();
                try {

                    if ($request->hasFile('file_or_link')) {

                        if (File::exists('assets/file/slide/' . $slide->file_or_link)) {
                            File::delete('assets/file/slide/' . $slide->file_or_link);
                        }

                        $file = $request->file_or_link;
                        $file_name = uniqid('SLD_') . '.' . $file->extension();
                        $file->move('assets/file/slide', $file_name);

                        $slide->file_or_link = $file_name;
                    }

                    $slide->judul = $request->judul;

                    if ($slide->isDirty()) {
                        $slide->update();

                        return response()->json([
                            'status' => 200
                        ]);
                    } else {
                        return response()->json([
                            'status' => 200,
                            'nothing' => 1
                        ]);
                    }
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 401,
                        'title' => 'Terjadi Kesalahan saat memperbarui data',
                        'message' => "message: $th"
                    ]);
                } finally {
                    DB::commit();
                }
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Oops! Slide tidak ditemukan!"
            ]);
        }
    }
}
