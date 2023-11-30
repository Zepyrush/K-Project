<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KelolaPengguna\{Dosen, Mahasiswa};
use App\Models\ManajemenKuliah\{
    Fakultas,
    Prodi,
    Matkul,
    Kelas,
    Jadwal\Kuliah,
    Jadwal\Ujian,
};
use App\Models\User;
use App\Models\RolePermission\{
    Role,
    Permission,
    LabelPermission
};

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin()) {

            $last_login = User::where('id', '!=', Auth::id())
                ->orderBy('last_seen', 'desc')
                ->take(25)
                ->get();

            return view('dashboard.admin.index', [
                'dosen' => Dosen::count(),
                'mahasiswa' => Mahasiswa::count(),
                'fakultas' => Fakultas::count(),
                'prodi' => Prodi::count(),
                'matkul' => Matkul::count(),
                'kelas' => Kelas::count(),
                'kuliah' => Kuliah::count(),
                'ujian' => Ujian::count(),
                'users' => User::where('id', '!=', Auth::id())->get(),
                'roles' => Role::count(),
                'permissions' => Permission::count(),
                'label_permissions' => LabelPermission::count(),
                'last_login' => $last_login,
            ]);

        } else {
            abort(404);
        }
    }
}
