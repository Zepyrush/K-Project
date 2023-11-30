<?php

namespace App\Http\Controllers\Admin\ManajemenKuliah;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Kelas;
use App\Models\KelolaPengguna\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('kelas')
            ->leftJoin('mahasiswa_kelas', 'kelas.id', '=', 'mahasiswa_kelas.kelas_id')
            ->leftJoin('mahasiswas', 'mahasiswa_kelas.mahasiswa_id', '=', 'mahasiswas.id')
            ->leftJoin('dosen_kelas', 'kelas.id', '=', 'dosen_kelas.kelas_id')
            ->leftJoin('dosens', 'dosen_kelas.dosen_id', '=', 'dosens.id')
            ->select(
                'kelas.*',
                DB::raw('GROUP_CONCAT(mahasiswas.nama SEPARATOR ", ") as mahasiswa'),
                DB::raw('GROUP_CONCAT(dosens.nama SEPARATOR ", ") as dosen_mengajar')
            )
            ->groupBy('kelas.id', 'kelas.kode', 'kelas.created_at', 'kelas.updated_at')
            ->get();

        $kelas = Kelas::with('mahasiswa')->get();

        if (request()->ajax()) {
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('daftar_mahasiswa', function ($data) use ($kelas) {
                    $countMhs = 0; // inisialisasi jumlah mahasiswa
                    foreach ($kelas as $kls) { // cari kelas
                        if ($kls->id == $data->id) { // jika kelas ditemukan
                            $countMhs = count($kls->mahasiswa); // hitung jumlah mahasiswa
                        }
                    }

                    if ($data->mahasiswa) { // jika kelas memiliki mahasiswa
                        $mhsArr = explode(', ', $data->mahasiswa);
                        $uniqueArr = array_unique($mhsArr);

                        // jika jumlah mahasiswa lebih dari 10 dan harus unik
                        if (count($uniqueArr) >= 10) {
                            $mahasiswa = array_slice($uniqueArr, 0, 10);
                            return implode(', ', $mahasiswa) . ', <b>dan ' . ($countMhs - count($mahasiswa)) . ' mahasiswa lainnya..</b>'; // tampilkan 10 mahasiswa dan sisanya
                        } else { // jika jumlah mahasiswa kurang dari 10
                            return implode(', ', $uniqueArr);
                        }
                    } else {
                        return "<span class='badge badge-danger'>Belum memiliki mahasiswa</span>";
                    }
                })
                ->addColumn('dosen_mengajar', function ($data) {
                    $dosenArr = explode(', ', $data->dosen_mengajar);
                    $uniqueArr = array_unique($dosenArr);
                    $dosen = implode(', ', $uniqueArr);

                    return $data->dosen_mengajar ? $dosen : "<span class='badge badge-danger'>Belum memiliki dosen</span>";
                })
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" id="' . $data->id . '" class="edit_btn btn btn-warning btn-sm mr-1 mt-1" data-toggle="tooltip" title="Edit"><i class="fas fa-pen"></i></button>';
                    $button .= '<button type="button" id="' . $data->id . '" data-kode="' . $data->kode . '" class="del_btn btn btn-danger btn-sm mt-1" data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return $button;
                })
                ->rawColumns(['action', 'daftar_mahasiswa', 'dosen_mengajar'])
                ->make(true);
        }

        return view('dashboard.admin.manajemen-kuliah.kelas');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {
        if ($request->ajax()) {
            $kelas = [];
            if ($request->has('q')) {
                $search = $request->q;
                $kelas = Kelas::select('id', 'kode')->where('kode', 'LIKE', "%$search%")
                    ->limit(5)
                    ->get();
            } else {
                $kelas = Kelas::select('id', 'kode')->limit(5)->get();
            }
            return response()->json($kelas);
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
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|unique:kelas,kode'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            DB::beginTransaction();
            try {
                Kelas::create([
                    'kode' => $request->kode,
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil menyimpan data",
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
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
        $kelas = Kelas::find($id);

        if (request()->ajax()) {
            if ($kelas) {
                return response()->json([
                    'status' => 200,
                    'data' => $kelas
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Data tidak ditemukan!'
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
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|unique:kelas,kode,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            DB::beginTransaction();
            try {
                $kelas = Kelas::find($id);

                $kelas->kode = $request->kode;

                if ($kelas->isDirty()) {
                    $kelas->update();

                    return response()->json([
                        'status' => 200,
                        'message' => "Berhasil memperbarui data",
                    ]);
                } else {
                    return response()->json([
                        'status' => 201,
                        'message' => "Tidak ada perubahan data",
                    ]);
                }
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'title' => "Terjadi kesalahan! saat memperbarui data!",
                    'message' => "Pesan: $th"
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
        $kelas = Kelas::find($id);

        if ($kelas) {
            DB::beginTransaction();
            try {
                $usesMahasiswa = $kelas->mahasiswa->count();
                $usesDosen = $kelas->dosens->count();

                if ($usesMahasiswa > 0 || $usesDosen > 0) {

                    if ($usesMahasiswa > 0 && $usesDosen > 0) {
                        $msg = "Kelas ini sedang digunakan oleh $usesMahasiswa mahasiswa dan $usesDosen dosen";
                    } else if ($usesDosen > 0) {
                        $msg = "Kelas ini sedang digunakan oleh $usesDosen dosen";
                    } else if ($usesMahasiswa > 0) {
                        $msg = "Kelas ini sedang digunakan oleh $usesMahasiswa mahasiswa";
                    }

                    return response()->json([
                        'status' => 400,
                        'title' => "Gagal menghapus data!",
                        'message' => $msg
                    ]);
                } else {
                    $kelas->delete();

                    return response()->json([
                        'status' => 200,
                        'message' => "Berhasil menghapus data",
                    ]);
                }
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
                'message' => 'Data tidak ditemukan!'
            ]);
        }
    }
}
