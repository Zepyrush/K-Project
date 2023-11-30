<?php

namespace App\Http\Controllers\Admin\ManajemenKuliah;

use App\Http\Controllers\Controller;
use App\Models\ManajemenKuliah\Fakultas;
use App\Models\ManajemenKuliah\Matkul;
use App\Models\ManajemenKuliah\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Prodi::with('fakultas')->get();

        if (request()->ajax()) {
            $data = $data->transform(function ($item) {
                $item->fakultas_nama = $item->fakultas->pluck('nama')->first();
                return $item;
            });

            if (request()->input('filter_fakultas') != null) {
                $data = collect($data)->where('fakultas_nama', request()->filter_fakultas)->all();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" id="' . $data->id . '" data-toggle="tooltip" title="Edit" class="edit_btn btn btn-warning mr-1 btn-sm mt-1"><i class="fas fa-pen"></i></button>';
                    $button .= '<button type="button" name="delete" id="' . $data->id . '" data-name="' . $data->nama . '" data-toggle="tooltip" title="Hapus" class="del_btn btn btn-danger btn-sm mt-1"><i class="fas fa-trash"></i></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.admin.manajemen-kuliah.prodi', [
            'fakultas' => Fakultas::all(),
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
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'fakultas' => 'required|exists:fakultas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {

                Prodi::create([
                    'nama' => $request->nama
                ])->fakultas()->attach($request->fakultas);

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil menyimpan data",
                ]);

            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'title' => "Terjadi kesalahan! saat menyimpan data",
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
        $data = Prodi::with('fakultas')->where('id', $id)->first();

        if (request()->ajax()) {
            if ($data) {
                return response()->json([
                    'status' => 200,
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => "Data tidak ditemukan"
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
            'nama' => 'required|string|max:255|unique:prodis,nama,' . $id,
            'fakultas' => 'required|exists:fakultas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'tipe' => 'validation',
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {

                $prodi = Prodi::find($id);
                $prodi->nama = $request->nama;
                $prodi->fakultas()->sync($request->fakultas);
                $prodi->save();

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil memperbarui data",
                ]);

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
        $prodi = Prodi::find($id);

        DB::beginTransaction();
        try {
            $prodi->delete();
            $prodi->fakultas()->detach();

            return response()->json([
                'status' => 200,
                'message' => "Berhasil menghapus data",
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
    }
}
