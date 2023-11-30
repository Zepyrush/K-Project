<?php

namespace App\Http\Controllers\Admin\RolePermission;

use App\Http\Controllers\Controller;
use App\Models\RolePermission\Permission;
use App\Models\RolePermission\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\RolePermission\LabelPermission as Label;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Permission::with('labelPermissions')
            ->whereHas('labelPermissions', function ($q) {
                $q->orderBy('label_permissions.created_at', 'desc');
            })->get();

        if (request()->ajax()) {
            $data = $data->transform(function ($item) {
                $item->label = $item->labelPermissions->pluck('name')->first();
                return $item;
            });

            if (request()->input('label') == '') {
                $data = collect($data)->all();
            } else {
                $data = collect($data)->where('label', request()->label)->all();
            }

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" value="' . $data->id . '" data-toggle="tooltip" title="Edit" class="edit_btn btn btn-warning btn-sm mr-1 mt-1"><i class="fas fa-pen"></i></button>';
                    $button .= '<button type="button" name="delete" value="' . $data->id . '" data-name="' . $data->name . '" data-toggle="tooltip" title="Hapus" class="del_btn btn btn-danger btn-sm mt-1"><i class="fas fa-trash"></i></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.admin.role-permissions.permission', [
            'labelcruds' => Label::with('permissions')->get(),
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
            'name' => 'required|min:3|unique:permissions,name',
            'label' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            DB::beginTransaction();
            try {
                $permission = Permission::create([
                    'name' => $request->name,
                ]);

                $permission->labelPermissions()->attach($request->label);

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
        $permission = Permission::with('labelPermissions')->find($id);

        if (request()->ajax()) {
            if ($permission) {
                return response()->json([
                    'status' => 200,
                    'data' => $permission
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Data permission tidak ditemukan!'
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
            'name' => 'required|min:3|unique:permissions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            DB::beginTransaction();
            try {
                $permission = Permission::find($id);

                $permission->name = $request->input('name');
                $permission->labelPermissions()->sync($request->input('label'));
                $permission->update();

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil memperbarui data",
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 201,
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
        $permission = Permission::find($id);

        $used = Role::join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->where('permission_id', $permission->id)->get();

        if ($used->count() > 0) {
            return response()->json([
                'status' => 400,
                'message' => "Data permission ini tidak dapat dihapus, karena sedang digunakan!"
            ]);
        }

        DB::beginTransaction();
        try {
            $permission->delete();
            $permission->labelPermissions()->detach();

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
