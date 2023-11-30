<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.profile');
    }

    public function updatePhoto(Request $request) // UPDATE FOTO PROFIL
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'image|mimes:jpg,png,jpeg,svg|max:1024',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' =>  400,
                'tipe' => 'validation',
                'message' => $validator->errors()->toArray(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                $user = User::with('mahasiswa')->with('dosen')->find(Auth::id());

                if ($request->base64image || $request->base64image != '0') {
                    $path = 'assets/image/users/';
                    // PECAHIN FILE INPUT DARI BASE64IMAGE
                    $img_parts = explode(";base64", $request->base64image); // PISAHIN DARI ;base64
                    $img_type_aux = explode("image/", $img_parts[0]); // PISAHIN DARI image/
                    $img_type = $img_type_aux[1]; // AMBIL EXTENSION FILE
                    $img_base64 = base64_decode($img_parts[1]); // AMBIL BASE64 IMAGE

                    if (Auth::user()->isAdmin()) { // JIKA ADMIN
                        $titleFileName = "ADMIN-";
                    } else if (Auth::user()->isDosen()) { // JIKA DOSEN
                        $titleFileName = "DOSEN-";
                    } else if (Auth::user()->isMahasiswa()) { // JIKA MAHASISWA
                        $titleFileName = "MAHASISWA-";
                    }

                    $filename = uniqid($titleFileName) . '.' . $img_type; // GENERATE FILENAME
                    $file = $path . $filename; // GENERATE PATH FILE
                    $upload = file_put_contents($file, $img_base64); // UPLOAD FILE
                }

                if (!$upload) { // JIKA UPLOAD GAGAL
                    return response()->json([
                        'status' =>  400,
                        'message' => "Terjadi kesalahan saat memperbarui data!",
                    ]);
                } else { // JIKA UPLOAD BERHASIL

                    $oldPicture = $user->getAttributes()['foto']; // AMBIL FOTO LAMA

                    if ($oldPicture != '') { // JIKA FOTO LAMA ADA
                        if (File::exists($path . $oldPicture)) { // JIKA FOTO LAMA ADA DI SERVER
                            File::delete($path . $oldPicture); // HAPUS FOTO LAMA
                        }
                    }

                    $updated = $user->update([
                        'foto' => $filename,
                    ]);

                    if ($updated) { // JIKA BERHASIL UPDATE
                        return response()->json([
                            'status' => 200,
                            'message' => "Foto profil berhasil diperbarui",
                        ]);
                    } else {
                        return response()->json([
                            'status' => 400,
                            'message' => "Terjadi kesalahan saat memperbarui data!",
                        ]);
                    }
                }
            } catch (\Illuminate\Database\QueryException $th) { // JIKA TERJADI ERROR QUERY
                DB::rollback();

                $msg = $th->getMessage();

                if (isset($th->errorInfo[2])) {
                    $msg = $th->errorInfo[2];
                }

                return response()->json([
                    'status' => 400,
                    'message' => "Terjadi kesalahan saat memperbarui data!\nPesan: $msg",
                ]);
            } finally {
                DB::commit();
            }
        }
    }

    public function updatePassword(Request $request) // UPDATE PASSWORD
    {
        $validator = Validator::make($request->all(), [ // VALIDASI DATA
            'oldpass' => [
                'required', 'string', 'min:7', 'max:16',
                function ($attr, $val, $fail) { // VALIDASI PASSWORD LAMA
                    if (!Hash::check($val, Auth::user()->password)) { // JIKA PASSWORD LAMA TIDAK SESUAI
                        $fail("Password sekarang tidak sesuai! Silahkan coba lagi.");
                    }
                }
            ],
            'newpass' => ['required', 'string', 'min:7', 'max:16', 'different:oldpass'],
            'confirmpass' => ['required', 'string', 'min:7', 'max:16', 'same:newpass'],
        ], [
            'oldpass.required' => 'Password sekarang tidak boleh kosong!',
            'oldpass.min' => 'Password sekarang minimal 7 karakter!',
            'oldpass.max' => 'Password sekarang maksimal 16 karakter!',
            'newpass.required' => 'Password baru tidak boleh kosong!',
            'newpass.min' => 'Password baru minimal 7 karakter!',
            'newpass.max' => 'Password baru maksimal 16 karakter!',
            'newpass.different' => 'Password baru tidak boleh sama dengan password sekarang!',
            'confirmpass.required' => 'Konfirmasi password tidak boleh kosong!',
            'confirmpass.min' => 'Konfirmasi password minimal 7 karakter!',
            'confirmpass.max' => 'Konfirmasi password maksimal 16 karakter!',
            'confirmpass.same' => 'Konfirmasi password tidak sesuai!',
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
                $user = User::find(Auth::id());

                if ($user) {
                    $user->password = Hash::make($request->newpass); // HASH PASSWORD BARU

                    if ($user->isDirty()) { // JIKA ADA PERUBAHAN
                        $user->update();

                        return response()->json([
                            'status' => 200,
                            'message' => "Password telah diperbarui",
                        ]);
                    } else {
                        return response()->json([
                            'status' => 200,
                            'tipe' => 'warning',
                            'message' => "Tidak ada perubahan",
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 200,
                        'tipe' => 'warning',
                        'message' => "Data anda tidak ditemukan",
                    ]);
                }
            } catch (\Illuminate\Database\QueryException $th) {
                DB::rollBack();

                $msg = $th->getMessage();
                if (isset($th->errorInfo[2])) {
                    $msg = $th->errorInfo[2];
                }

                return response()->json([
                    'status' => 400,
                    'message' => "Terjadi kesalahan saat memperbarui data!\nPesan: $msg",
                ]);
            } finally {
                DB::commit();
            }
        }
    }

    public function deletePhoto(Request $request) // HAPUS FOTO PROFIL
    {
        $user = User::with('mahasiswa')->with('dosen')->find(Auth::id());

        if ($user) {
            $path = 'assets/image/users/';

            $oldPicture = $user->getAttributes()['foto'];

            if ($oldPicture != '') {
                if (File::exists($path . $oldPicture)) {
                    File::delete($path . $oldPicture);
                }
            }

            $updated = $user->update([
                'foto' => 'avatar.png',
            ]);

            if ($updated) {
                return response()->json([
                    'status' => 200,
                    'message' => "Foto profil berhasil dihapus",
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => "Terjadi kesalahan saat menghapus foto profil!",
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => "Data tidak ditemukan!",
            ]);
        }
    }
}
