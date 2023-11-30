<?php

namespace App\Http\Controllers\Admin\KelolaPengguna;

use App\Http\Controllers\Controller;
use App\Imports\Admin\MahasiswaImport;
use App\Models\ManajemenKuliah\Fakultas;
use App\Models\ManajemenKuliah\Kelas;
use App\Models\KelolaPengguna\Mahasiswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('mahasiswas')
            ->leftJoin('fakultas', 'mahasiswas.fakultas_id', '=', 'fakultas.id')
            ->leftJoin('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id')
            ->leftJoin('users', 'mahasiswas.user_id', '=', 'users.id')
            ->selectRaw('mahasiswas.*, fakultas.nama as fakultas_nama, kelas.kode as kelas_kode, foto')
            ->orderBy('mahasiswas.kelas_id', 'asc')
            ->get();

        if (request()->ajax()) {
            $data = $data->transform(function ($item) {
                $item->fakultas_nama = $item->fakultas_nama;
                $item->kelas_kode = $item->kelas_kode;
                $item->register_at = Carbon::parse($item->created_at)->translatedFormat('d F Y');
                return $item;
            });

            if (request()->input('f_kelas') != null) {
                $data = collect($data)->where('kelas_kode', request()->f_kelas)->all();
            }

            if (request()->input('f_fakultas') != null) {
                $data = collect($data)->where('fakultas_nama', request()->f_fakultas)->all();
            }

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" value="' . $data->id . '" data-toggle="tooltip" title="Edit" class="edit_btn btn btn-warning btn-sm mr-1 mt-1"><i class="fas fa-pen"></i></button>';
                    $button .= '<button type="button" name="delete" value="' . $data->id . '" data-name="' . $data->nama . '" data-toggle="tooltip" title="Hapus" class="del_btn btn btn-danger btn-sm mt-1"><i class="fas fa-trash"></i></button>';
                    return $button;
                })
                ->addColumn('mahasiswa', function ($data) {
                    if (file_exists('assets/image/users/' . $data->foto)) {
                        $avatar = asset('assets/image/users/' . $data->foto);
                    } else {
                        $avatar = asset('assets/image/avatar.png');
                    }

                    return '
                        <a href="javascript:void(0)" class="d-flex align-items-center" style="cursor: default">
                            <img src="' . $avatar . '" width="40" class="avatar rounded-circle me-3">
                            <div class="d-block ml-3" >
                                <span class="fw-bold name-user">' . $data->nama . '</span>
                                <div class="small text-secondary" >' . $data->nim . '</div>
                            </div>
                        </a>
                    ';
                })
                ->rawColumns(['action', 'mahasiswa'])
                ->make(true);
        }

        return view('dashboard.admin.manage-users.mahasiswa', [
            'fakultas' => Fakultas::get(),
            'kelas' => Kelas::get(),
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
            'nim' => 'required|unique:mahasiswas,nim|numeric|digits_between:8,8',
            'nama' => 'required|alpha_space|string|min:3',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8|max:16|confirmed',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'fakultas' => 'required',
            'kelas' => 'required',
        ], [
            'nim.required' => 'Nim tidak boleh kosong',
            'nim.numeric' => 'Nim harus berupa angka',
            'nim.digits_between' => 'Nim harus berjumlah 8 digit',
            'nama.required' => 'Nama tidak boleh kosong',
            'nama.alpha_space' => 'Nama hanya boleh berisi huruf dan spasi',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal berjumlah 8 karakter',
            'password.max' => 'Password maksimal berjumlah 16 karakter',
            'password.confirmed' => 'Password tidak sama',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.mimes' => 'Foto harus berupa gambar dengan format jpg, jpeg, atau png',
            'foto.max' => 'Foto maksimal berukuran 1 MB',
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
                    $fotoBaru = uniqid('MHS-') . '.' . $foto->extension();
                    // Resize
                    $resize = Image::make($foto->path());
                    $resize->fit(1000, 1000)->save($path . '/' . $fotoBaru);
                }

                User::create([
                    'name' => $request->nama,
                    'no_induk' => $request->nim,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'foto' => $fotoBaru ?? 'avatar.png',
                ])->assignRole('mahasiswa');

                $mahasiswa = Mahasiswa::create([
                    'nim' => $request->nim,
                    'nama' => $request->nama,
                    'email' => $request->email,
                    'fakultas_id' => $request->fakultas,
                    'kelas_id' => $request->kelas,
                    'user_id' => User::where('no_induk', $request->nim)->first()->id,
                ]);

                $mahasiswa->mahasiswa_kelas()->attach($request->kelas);

                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil menyimpan data'
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Mahasiswa::with('user', 'fakultas', 'kelas')->find($id);

        if (request()->ajax()) {
            if ($data) {
                return response()->json([
                    'status' => '200',
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'status' => '404',
                    'message' => 'Data Mahasiswa Tidak Ditemukan'
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
        $mahasiswa = Mahasiswa::with('user')->find($id);

        if ($mahasiswa) {
            $validator = Validator::make($request->all(), [
                'nim' => 'required|numeric|digits_between:8,8|unique:mahasiswas,nim,' . $mahasiswa->id,
                'nama' => 'required|alpha_space|string|min:3',
                'email' => 'required|email|unique:users,email,' . $mahasiswa->user->id,
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
                'fakultas' => 'required',
                'kelas' => 'required',
            ], [
                'nim.required' => 'Nim tidak boleh kosong',
                'nim.numeric' => 'Nim harus berupa angka',
                'nim.digits_between' => 'Nim harus berjumlah 8 digit',
                'nama.required' => 'Nama tidak boleh kosong',
                'nama.alpha_space' => 'Nama hanya boleh berisi huruf dan spasi',
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Email tidak valid',
                'foto.image' => 'Foto harus berupa gambar',
                'foto.mimes' => 'Foto harus berupa gambar dengan format jpg, jpeg, atau png',
                'foto.max' => 'Foto maksimal berukuran 1 MB',
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
                        if (File::exists($path . $mahasiswa->user->foto)) {
                            File::delete($path . $mahasiswa->user->foto);
                        }
                        $foto = $request->file('foto');
                        $fotoBaru = uniqid('MHS-') . '.' . $foto->extension();
                        // Resize
                        $resize = Image::make($foto->path());
                        $resize->fit(1000, 1000)->save($path . '/' . $fotoBaru);

                        $mahasiswa->user->foto = $fotoBaru;
                    }

                    // untuk update data di table users
                    $mahasiswa->user->name = $request->nama;
                    $mahasiswa->user->no_induk = $request->nim;
                    $mahasiswa->user->email = $request->email;
                    $mahasiswa->user->update();

                    // untuk update data di table mahasiswas
                    $mahasiswa->nim = $request->nim;
                    $mahasiswa->nama = $request->nama;
                    $mahasiswa->email = $request->email;
                    $mahasiswa->fakultas_id = $request->fakultas;
                    $mahasiswa->kelas_id = $request->kelas;
                    $mahasiswa->update();

                    $mahasiswa->mahasiswa_kelas()->sync($request->kelas);

                    return response()->json([
                        'status' => 200,
                        'message' => 'Berhasil memperbarui data'
                    ]);
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
                'title' => '',
                'message' => 'Data Mahasiswa Tidak Ditemukan'
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
        $mahasiswa = Mahasiswa::with('user')->find($id);

        if ($mahasiswa) {
            DB::beginTransaction();
            try {
                $path = 'assets/image/users/';
                if (File::exists($path . $mahasiswa->user->foto)) {
                    File::delete($path . $mahasiswa->user->foto);
                }

                $mahasiswa->user->delete();
                $mahasiswa->user->roles()->detach();
                $mahasiswa->delete();
                $mahasiswa->mahasiswa_kelas()->detach();

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil menghapus data"
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
                'title' => '',
                'message' => 'Data Mahasiswa Tidak Ditemukan'
            ]);
        }
    }

    /**
     * Import data from excel.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_excel.required' => 'File excel tidak boleh kosong',
            'file_excel.mimes' => 'File excel harus berupa file dengan format xlsx, xls, atau csv',
            'file_excel.max' => 'File excel maksimal berukuran 5 MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'val' => true,
                'errors' => $validator->errors()->toArray(),
            ]);
        }

        DB::beginTransaction();
        try {
            $import = new MahasiswaImport();
            $import->excel($request->file('file_excel'));

            return response()->json([
                'status' => 200,
                'message' => 'Berhasil mengimport data mahasiswa'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 400,
                'message' => "{$th->getMessage()}"
            ]);
        } finally {
            DB::commit();
        }
    }
}
