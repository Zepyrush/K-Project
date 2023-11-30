<?php

namespace App\Http\Controllers\Admin\ManajemenKuliah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManajemenKuliah\Fakultas;
use App\Models\ManajemenKuliah\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FakultasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Fakultas::all();

        if (request()->ajax()) {
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" id="' . $data->id . '" class="edit_btn btn btn-warning btn-sm mr-1 mt-1" data-toggle="tooltip" title="Edit"><i class="fas fa-pen"></i></button>';
                    $button .= '<button type="button" name="delete" id="' . $data->id . '" data-name="' . $data->nama . '" class="del_btn btn btn-danger btn-sm mt-1" data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.admin.manajemen-kuliah.fakultas');
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
            'nama' => 'required|min:3|unique:fakultas,nama'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            DB::beginTransaction();
            try {

                $nama = $request->nama;

                Fakultas::create([
                    'nama' => $nama,
                    'kode' => $this->getKodeFromName($nama),
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
        $fakultas = Fakultas::find($id);

        if (request()->ajax()) {
            if ($fakultas) {
                return response()->json([
                    'status' => 200,
                    'data' => $fakultas
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
            'nama' => 'required|min:3|unique:fakultas,nama,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            DB::beginTransaction();
            try {

                $fakultas = Fakultas::find($id);

                if ($request->nama != $fakultas->nama) {
                    $nama = $request->nama;
                    $fakultas->nama = $nama;
                    $fakultas->kode = $this->getKodeFromName($nama);
                }

                if ($fakultas->isDirty()) {
                    $fakultas->update();

                    return response()->json([
                        'status' => 200,
                        'icon' => 'success',
                        'message' => "Berhasil memperbarui data",
                    ]);
                } else {
                    return response()->json([
                        'status' => 200,
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
        $fakultas = Fakultas::find($id);

        DB::beginTransaction();
        try {

            $uses = Prodi::join('prodi_fakultas', 'prodi_fakultas.prodi_id', '=', 'prodis.id')
                ->where('prodi_fakultas.fakultas_id', $id)
                ->get();

            if ($uses->count() > 0) {
                return response()->json([
                    'status' => 400,
                    'title' => "Oops!",
                    'message' => "Fakultas ini <strong>$fakultas->nama</strong> tidak dapat dihapus karena masih digunakan! <br>Ada <strong>" . $uses->count() . " Program Studi</strong> yang masih menggunakan fakultas ini.",
                ]);
            } else {
                $fakultas->delete();

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
    }


    /**
     * Get kode from name
     *
     * @param string $nama
     * @return string
     */
    public function getKodeFromName($nama)
    {
        $arr = explode(' ', $nama);
        $kode = '';
        foreach ($arr as $a) {
            $kode .= strtoupper(substr($a, 0, 1));
        }

        return $kode . '-' . rand(10, 99);
    }
}
