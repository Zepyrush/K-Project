<?php

namespace App\Http\Controllers\Admin\RolePermission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RolePermission\LabelPermission as Labels;
use App\Models\RolePermission\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LabelPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Labels::all();

        if (request()->ajax()) {
            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" id="' . $data->id . '" data-toggle="tooltip" title="Edit" class="editBtn btn btn-warning btn-sm mr-1 mt-1"><i class="fas fa-pen"></i></button>';
                    $button .= '<button type="button" name="delete" id="' . $data->id . '" data-name="' . $data->name . '" data-toggle="tooltip" title="Hapus" class="delBtn btn btn-danger btn-sm mt-1"><i class="fas fa-trash"></i></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('dashboard.admin.role-permissions.group-permission', [
            'labels' => Labels::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:label_permissions,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            DB::beginTransaction();
            try {
                Labels::create([
                    'name' => $request->name,
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
        $label = Labels::find($id);

        if (request()->ajax()) {
            if ($label) {
                return response()->json([
                    'status' => 200,
                    'data' => $label
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Label Permission tidak ditemukan!'
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
            'name' => 'required|min:3|unique:label_permissions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            DB::beginTransaction();
            try {
                $label = Labels::find($id);

                $label->name = $request->input('name');

                if ($label->isDirty()) {
                    $label->update();

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
        $label = Labels::find($id);

        DB::beginTransaction();
        try {

            $uses = Permission::join('group_label_permissions', 'group_label_permissions.permission_id', '=', 'permissions.id')
                ->where('group_label_permissions.label_permission_id', $label->id)
                ->get();

            if ($uses->count() > 0) {
                return response()->json([
                    'status' => 400,
                    'title' => "Oops!",
                    'message' => "Grup <strong>$label->name</strong> tidak dapat dihapus karena masih digunakan! <br>Ada <strong>" . $uses->count() . " permission</strong> yang masih menggunakan grup ini.",
                ]);
            } else {
                $label->delete();

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
}
