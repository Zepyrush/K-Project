<?php

namespace App\Http\Controllers\Admin\ManajemenKuliah\Jadwal;

use App\Http\Controllers\Controller;
use App\Models\KelolaPengguna\Dosen;
use App\Models\ManajemenKuliah\DosenKelas;
use Illuminate\Http\Request;
use App\Models\ManajemenKuliah\Jadwal\Kuliah;
use App\Models\ManajemenKuliah\Kelas;
use App\Models\ManajemenKuliah\Materi;
use App\Models\ManajemenKuliah\Matkul;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class KuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Kuliah::with('kelas', 'matkul', 'dosen')
            ->orderBy(Kelas::select('kode')->whereColumn('id', 'kelas_id'), 'asc')
            ->orderBy(Matkul::select('nama')->whereColumn('id', 'matkul_id'), 'asc')
            ->get();

        if ($request->ajax()) {
            $data = $data->transform(function ($item) {
                $item->kelas_jadwal = $item->kelas->kode;
                $item->matkul_jadwal = $item->matkul->nama;
                $item->dosen_jadwal = $item->dosen->nama;

                return $item;
            });

            if ($request->filterKelas != null) {
                $data = collect($data)->where('kelas_jadwal', $request->filterKelas)->all();
            }

            if ($request->filterMatkul != null) {
                $data = collect($data)->where('matkul_jadwal', $request->filterMatkul)->all();
            }

            if ($request->filterDosen != null) {
                $data = collect($data)->where('dosen_jadwal', $request->filterDosen)->all();
            }

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" id="' . $data->id . '" class="edit_btn btn btn-warning btn-sm mr-1 mt-1" data-toggle="tooltip" title="Edit"><i class="fas fa-pen"></i></button>';
                    $button .= '<button type="button" name="delete" id="' . $data->id . '" class="del_btn btn btn-danger btn-sm mt-1" data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.admin.manajemen-kuliah.jadwal.kuliah', [
            'data_kelas' => Kelas::all(),
            'data_matkul' => Matkul::all(),
            'data_dosen' => Dosen::all(),
            'data_hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu', 'Minggu'],
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
        $checkIfExist = Kuliah::where('kelas_id', $request->kelas)
            ->where('matkul_id', $request->matkul)
            ->where('dosen_id', $request->dosen)
            ->first();

        if ($checkIfExist) {
            return response()->json([
                'status' => 401,
                'message' => '<span class="font-weight-bold">Matakuliah di kelas ini sudah ada!</span> <hr> Silahkan pilih matakuliah yang lain.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'kelas' => 'required',
            'matkul' => 'required',
            'dosen' => 'required',
            'hari' => 'required',
            'started' => 'required',
            'ended' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->toArray(),
                'status' => 400,
            ]);
        } else {
            DB::beginTransaction();
            try {
                Kuliah::create([
                    'kelas_id' => $request->kelas,
                    'matkul_id' => $request->matkul,
                    'dosen_id' => $request->dosen,
                    'hari' => $request->hari,
                    'started_at' => $request->started,
                    'ended_at' => $request->ended,
                ]);

                DB::table('dosen_kelas')->insert([
                    'dosen_id' => $request->dosen,
                    'kelas_id' => $request->kelas,
                    'matkul_id' => $request->matkul,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $kelas = Kelas::find($request->kelas);

                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil menyimpan data',
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Kuliah::with(['kelas:id,kode', 'matkul:id,nama', 'dosen', 'absens' => function ($q) {
            $q->where("parent", '0');
        }])->find($id);

        if (request()->ajax()) {
            if ($data) {
                return response()->json([
                    'status' => 200,
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => 'Data tidak ditemukan',
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
        $data = Kuliah::with('kelas', 'matkul', 'dosen')->find($id);

        if ($data) {
            $checkIfExist = Kuliah::where('id', '!=', $id)
                ->where('kelas_id', $request->kelas)
                ->where('matkul_id', $request->matkul)
                ->where('dosen_id', $request->dosen)
                ->first();

            if ($checkIfExist) {
                return response()->json([
                    'status' => 401,
                    'message' => '<span class="font-weight-bold">Matakuliah di kelas ini sudah ada!</span> <hr> Silahkan pilih matakuliah yang lain.',
                ]);
            }

            $validator = Validator::make($request->all(), [
                'kelas' => 'required',
                'matkul' => 'required',
                'dosen' => 'required',
                'hari' => 'required',
                'started' => 'required',
                'ended' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()->toArray(),
                    'status' => 400,
                ]);
            } else {
                DB::beginTransaction();
                try {
                    DB::table('dosen_kelas')
                        ->where('dosen_id', $data->dosen_id)
                        ->where('kelas_id', $data->kelas_id)
                        ->where('matkul_id', $data->matkul_id)
                        ->update([
                            'matkul_id' => $request->matkul ?? $data->matkul_id,
                            'kelas_id' => $request->kelas ?? $data->kelas_id,
                        ]);

                    $data->update([
                        'kelas_id' => $request->kelas,
                        'matkul_id' => $request->matkul,
                        'dosen_id' => $request->dosen,
                        'hari' => $request->hari,
                        'started_at' => $request->started,
                        'ended_at' => $request->ended,
                    ]);

                    return response()->json([
                        'status' => 200,
                        'message' => 'Berhasil memperbarui data',
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
                'status' => 401,
                'message' => 'Data tidak ditemukan',
            ]);
        }
    }

    public function reset(Request $request)
    {
        // Reset semua yang berhubungan dengan jadwal kuliah ini
        $kuliah = Kuliah::with('absens', 'tugas')->find($request->id);

        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                foreach ($kuliah->absens as $absen) {
                    $materi = Materi::where('pertemuan', $absen->pertemuan)
                        ->where('kelas_id', $kuliah->kelas_id)
                        ->where('matkul_id', $kuliah->matkul_id)
                        ->first();

                    if ($materi && $materi->tipe == 'pdf') {
                        if (File::exists('assets/file/materi/' . $materi->file_or_link)) {
                            File::delete('assets/file/materi/' . $materi->file_or_link);
                        }
                    }

                    if ($materi) {
                        $materi->delete();
                    }

                    $absen->delete();
                }

                foreach ($kuliah->tugas as $tugas) {
                    $tugas->delete();
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil mereset data',
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'title' => "Terjadi kesalahan! saat mereset data!",
                    'message' => "Pesan: $th"
                ]);
            } finally {
                DB::commit();
            }
        } else {
            abort(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $jadwal = Kuliah::with('kelas', 'matkul', 'dosen')->find($id);

        if ($jadwal) {
            DB::beginTransaction();
            try {
                // check jika dosen tidak mengampu matakuliah lain
                $checkDosen = DosenKelas::where('kelas_id', $jadwal->kelas_id)
                    ->where('dosen_id', $jadwal->dosen_id)
                    ->where('matkul_id', null)
                    ->get();

                if ($checkDosen->isNotEmpty()) { // jika tidak ada matakuliah yang diampu
                    DosenKelas::where('kelas_id', $jadwal->kelas_id)
                        ->where('dosen_id', $jadwal->dosen_id)
                        ->where('matkul_id', $jadwal->matkul_id)
                        ->delete();
                } else { // jika ada matakuliah yang diampu
                   DosenKelas::where('kelas_id', $jadwal->kelas_id)
                        ->where('dosen_id', $jadwal->dosen_id)
                        ->where('matkul_id', $jadwal->matkul_id)
                        ->update([
                            'matkul_id' => null,
                        ]);
                }

                $jadwal->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil menghapus data',
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
                'message' => 'Data tidak ditemukan',
            ]);
        }
    }
}
