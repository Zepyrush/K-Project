<?php

namespace App\Http\Controllers\Admin\KelolaPengguna;

use App\Http\Controllers\Controller;
use App\Models\KelolaPengguna\Dosen;
use App\Models\KelolaPengguna\Mahasiswa;
use App\Models\RolePermission\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get user where id not equal to current user id
        $data = DB::table('users')
            ->where('users.id', '!=', Auth::id())
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')
            ->orderBy('roles.id', 'asc')
            ->get();

        if (request()->ajax()) {
            // return data to datatable with
            $data = $data->transform(function ($item) {
                $item->role_names = ucfirst($item->role_name);

                ($item->last_seen != null) ?
                    $item->last_seen = Carbon::parse($item->last_seen)->diffForHumans() :
                    $item->last_seen = '<span class="badge badge-secondary">Belum pernah login</span>';

                $item->status = Cache::has('user-is-online-' . $item->id) ?
                    '<span class="text-success">Online</span>' :
                    '<span class="text-muted">Offline</span>';

                return $item;
            })->all();

            if (request()->input('role') != null) {
                $data = collect($data)->where('role_names', request()->role)->all();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    // Jika bukan admin (default) maka tidak bisa mengedit dan menghapus dat
                    $button = '';

                    if ($data->id != '1') { // Jika bukan admin (default) maka bisa mengedit dan menghapus data

                        if ($data->role_name == 'admin') { // Jika role admin maka bisa mengedit
                            $button .= '
                                <button type="button" name="edit" id="' . $data->id . '"
                                    data-name="' . $data->name . '" data-toggle="tooltip" title="Edit"
                                    class="editBtn btn btn-warning btn-sm mt-1">
                                    <i class="fas fa-pen"></i>
                                </button>
                            ';
                        }

                        // hapus data
                        $button .= '
                            <button type="button" name="delete"
                                id="' . $data->id . '" data-noInduk="'.$data->no_induk.'"
                                data-name="' . $data->name . '" data-toggle="tooltip" title="Hapus"
                                class="delBtn btn btn-danger btn-sm mt-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        ';
                    } else { // Jika admin (default) maka tidak bisa mengedit dan menghapus data
                        $button .= '
                            <button type="button" name="info" data-toggle="tooltip"
                                title="Tidak bisa di setting akun ini, karena sebagai default."
                                class="btn btn-info btn-sm cursor_default">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        ';
                    }

                    return $button;
                })
                ->addColumn('user', function ($data) {
                    if (file_exists('assets/image/users/' . $data->foto)) {
                        $avatar = asset('assets/image/users/' . $data->foto);
                    } else {
                        $avatar = asset('assets/image/avatar.png');
                    }

                    return '
                        <a href="javascript:void(0)" class="d-flex align-items-center" style="cursor: default">
                            <img src="' . $avatar . '" width="40" class="avatar rounded-circle me-3">
                            <div class="d-block ml-3">
                                <span class="fw-bold name-user">' . $data->name . '</span>
                                <div class="small text-secondary" >' . $data->email . '</div>
                            </div>
                        </a>
                    ';
                })
                ->rawColumns(['action', 'user', 'status', 'last_seen'])
                ->make(true);
        }

        return view('dashboard.admin.manage-users.index', [
            'roles' => Role::where('name', '!=', 'dosen')
                ->where('name', '!=', 'mahasiswa')
                ->get(),
            'role_filters' => Role::all(),
        ]);
    }

    /**
     * Insert data to database
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_induk' => 'required|numeric|digits_between:8,8|unique:users,no_induk',
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required',
            'foto' => 'image|mimes:jpeg,png,jpg|max:1024',
        ], [
            'no_induk.digits_between' => 'Nomer induk harus 8 digit!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                if ($request->hasFile('foto')) {
                    $path = 'assets/image/users/';
                    $foto = $request->file('foto');
                    $fotoBaru = uniqid('ADMIN-') . '.' . $foto->extension();
                    // Resize
                    $resize = Image::make($foto->path());
                    $resize->fit(1000, 1000)->save($path . '/' . $fotoBaru);
                }

                $user = User::create([
                    'no_induk' => $request->no_induk,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'email_verified_at' => now(),
                    'foto' => $fotoBaru ?? 'avatar.png',
                ]);

                $user->assignRole($request->role);

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil menyimpan data",
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 401,
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
        $user = User::with('roles')->find($id);

        if (request()->ajax()) {
            if ($user) {
                return response()->json([
                    'status' => 200,
                    'data' => $user
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
        $user = User::find($id);

        if ($user) {
            $validator = Validator::make($request->all(), [
                'no_induk' => 'required|numeric|digits_between:8,8|unique:users,no_induk,' . $user->id,
                'name' => 'required|string|min:3',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'role' => 'required',
                'foto' => 'image|mimes:jpeg,png,jpg|max:1024',
            ], [
                'no_induk.digits_between' => 'Nomer induk harus 8 digit!',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()->toArray(),
                ]);
            } else {
                DB::beginTransaction();
                try {
                    if ($request->hasFile('foto')) {
                        $path = 'assets/image/users/';
                        $foto = $request->file('foto');
                        $fotoBaru = uniqid('ADMIN-') . '.' . $foto->extension();
                        // Resize
                        $resize = Image::make($foto->path());
                        $resize->fit(1000, 1000)->save($path . '/' . $fotoBaru);

                        if (File::exists($path . $user->foto)) {
                            File::delete($path . $user->foto);
                        }
                    }

                    $user->update([
                        'no_induk' => $request->no_induk,
                        'name' => $request->name,
                        'email' => $request->email,
                        'foto' => $fotoBaru ?? $user->foto,
                    ]);

                    $user->syncRoles($request->role);

                    return response()->json([
                        'status' => 200,
                        'message' => "Berhasil mengubah data",
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 401,
                        'title' => "Terjadi kesalahan! saat mengubah data",
                        'message' => "Pesan: $th"
                    ]);
                } finally {
                    DB::commit();
                }
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Data tidak ditemukan!'
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
        $user = User::with(['mahasiswa', 'dosen'])->where('id', $id)->first();
        if ($user) {
            DB::beginTransaction();
            try {
                $path = 'assets/image/users/';

                if (File::exists($path . $user->foto)) {
                    File::delete($path . $user->foto);
                }

                if ($user->isMahasiswa()) {
                    $user->mahasiswa->delete();
                    $user->mahasiswa->mahasiswa_kelas()->detach();
                } else if ($user->isDosen()) {
                    $user->dosen->delete();
                    $user->dosen->kelas()->detach();
                    $user->dosen->matkuls()->detach();
                }

                $user->delete();
                $user->roles()->detach();

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil menghapus data"
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => 400,
                    'title' => 'Terjadi Kesalahan saat menghapus data',
                    'message' => "message: $th"
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
