<?php

namespace App\Http\Controllers\Admin\RolePermission;

use App\Http\Controllers\Controller;
use App\Models\RolePermission\LabelPermission;
use App\Models\RolePermission\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Role::with('permissions')->get();

        if (request()->ajax()) {
            $data = $data->transform(function ($item) {

                if ($item->permissions->count() > 0) {
                    $item->label = $item->permissions->pluck('name')->implode(', ');
                } else {
                    if ($item->name == 'admin') {
                        $item->label = 'Semua';
                    } else {
                        $item->label = "Tidak ada permission yang di pilih";
                    }

                }

                return $item;
            });

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    if ($data->name == 'admin') {
                        $button = '<button type="button" data-toggle="tooltip" title="Tidak dapat di edit" class="disabledBtn btn btn-warning btn-sm mr-1 mt-1"><i class="fas fa-pen"></i></button>';
                        $button .= '<button type="button" data-toggle="tooltip" title="Tidak dapat di hapus" class="disabledBtn btn btn-danger btn-sm mt-1"><i class="fas fa-trash"></i></button>';
                    } else {
                        $button = '<button type="button" name="edit" value="' . $data->id . '" data-toggle="tooltip" title="Edit" class="edit_btn btn btn-warning btn-sm mr-1 mt-1"><i class="fas fa-pen"></i></button>';
                        $button .= '<button type="button" name="delete" value="' . $data->id . '" data-name="' . $data->name . '" data-toggle="tooltip" title="Hapus" class="del_btn btn btn-danger btn-sm mt-1"><i class="fas fa-trash"></i></button>';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.admin.role-permissions.role', [
            'roles' => Role::whereNotIn('name', ['admin'])->get(),
            'permissions' => LabelPermission::whereHas('permissions')->get(),
        ]);
    }

    public function fetchPermission($id)
    {
        if (request()->ajax()) {
            $role = Role::find($id);
            $permissions = LabelPermission::whereHas('permissions')->get();
            $checked = $role->permissions->pluck('id')->toArray();

            $output = '';

            $output .= '
                <div id="edit_container" class="row" style="margin-left: -9px;margin-bottom: 4px">
            ';
            foreach ($permissions as $permission) {

                $output .= '
                    <ul class="list-group mx-1">
                        <li class="list-group-item mt-1 bg-info text-white">
                        ' . $permission->name . '
                        </li>';

                foreach ($permission->permissions as $item) {
                    $checkBoxCheck = in_array($item->id, old('permissions', $checked)) ? 'checked' : null;

                    if (old('permissions',  $checked)) {
                        $checkBox = '
                                <input id="edit' . $item->id . '" name="permissions[]"
                                class="form-check-input checkPermissionEdit" type="checkbox"
                                value="' . $item->id . '" ' . $checkBoxCheck . '>
                            ';
                    } else {
                        $checkBox = '
                                <input id="edit' . $item->id . '" name="permissions[]"
                                class="form-check-input checkPermissionEdit" type="checkbox"
                                value="' . $item->id . '">
                            ';
                    }

                    $output .= '
                            <li class="list-group-item">
                                <div class="form-check show_edit">
                                    ' . $checkBox . '

                                    <label for="edit' . $item->id . '" class="form-check-label checks">
                                        ' . $item->name . '
                                    </label>
                                </div>
                            </li>
                        ';
                }
                $output .= '</ul>';
            }
            $output .= '</div>';

            echo $output;
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
            'name' => 'required|max:15|min:3|unique:roles,name',
            'permissions' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            try {
                $role = Role::create([
                    'name' => $request->name,
                ]);
                $role->givePermissionTo($request->permissions);

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
        $role = Role::find($id);

        if (request()->ajax()) {
            if ($role) {
                return response()->json([
                    'status' => 200,
                    'data' => $role
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
            'name' => 'required|min:3|unique:roles,name,' . $id,
            'permissions' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            try {
                $role = Role::find($id);

                $role->name = $request->input('name');
                $role->syncPermissions($request->permissions);
                $role->update();

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil memperbarui data",
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'title' => "Terjadi kesalahan! saat memperbarui data",
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
        $role = Role::find($id);

        if (User::role($role->name)->count()) {
            return response()->json([
                'status' => 400,
                'message' => "Role ini tidak dapat dihapus karena masih digunakan oleh user!"
            ]);
        }

        DB::beginTransaction();
        try {
            $role->revokePermissionTo($role->permissions->pluck('name')->toArray());
            $role->delete();

            return response()->json([
                'status' => 200,
                'message' => "Berhasil menghapus data",
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 400,
                'title' => "Terjadi kesalahan! saat menghapus data",
                'message' => "Pesan: $th"
            ]);
        } finally {
            DB::commit();
        }
    }
}
