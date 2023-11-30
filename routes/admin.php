<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;

use App\Http\Controllers\Admin\KelolaPengguna\{
    UserController,
    DosenController,
    MahasiswaController
};

use App\Http\Controllers\Admin\RolePermission\{
    RoleController,
    PermissionController,
    LabelPermissionController
};

use App\Http\Controllers\Admin\ManajemenKuliah\{
    KelasController,
    MatkulController,
    FakultasController,
    ProdiController,
    Jadwal\KuliahController,
    Jadwal\UjianController
};

Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::group(['prefix' => 'kelola-pengguna', 'as' => 'manage.users.'], function () {
        // User
        Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('store', [UserController::class, 'store'])->name('store');
            Route::put('{id}', [UserController::class, 'update'])->name('update');
            Route::get('{id}', [UserController::class, 'show'])->name('show');
            Route::delete('{id}', [UserController::class, 'destroy'])->name('delete');
        });

        // Dosen
        Route::group(['prefix' => 'dosen', 'as' => 'dosen.'], function () {
            Route::get('/', [DosenController::class, 'index'])->name('index');
            Route::post('store', [DosenController::class, 'store'])->name('store');
            Route::put('{id}', [DosenController::class, 'update'])->name('update');
            Route::get('{id}', [DosenController::class, 'show'])->name('show');
            Route::delete('{id}', [DosenController::class, 'destroy'])->name('delete');
        });

        // Mahasiswa
        Route::group(['prefix' => 'mahasiswa', 'as' => 'mahasiswa.'], function () {
            Route::get('/', [MahasiswaController::class, 'index'])->name('index');
            Route::post('import', [MahasiswaController::class, 'import'])->name('import');
            Route::post('store', [MahasiswaController::class, 'store'])->name('store');
            Route::put('{id}', [MahasiswaController::class, 'update'])->name('update');
            Route::get('{id}', [MahasiswaController::class, 'show'])->name('show');
            Route::delete('{id}', [MahasiswaController::class, 'destroy'])->name('delete');
        });
    });

    // Role & Permission
    Route::group(['prefix' => 'role-permission', 'as' => 'role.permission.'], function () {
        // Role
        Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('store', [RoleController::class, 'store'])->name('store');
            Route::put('{id}', [RoleController::class, 'update'])->name('update');
            Route::get('{id}', [RoleController::class, 'show'])->name('show');
            Route::delete('{id}', [RoleController::class, 'destroy'])->name('delete');
            Route::get('fetch-permission/{id}', [RoleController::class, 'fetchPermission'])->name('fetch.permission');
        });

        // Permission
        Route::group(['prefix' => 'permission', 'as' => 'permission.'], function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::post('store', [PermissionController::class, 'store'])->name('store');
            Route::put('{id}', [PermissionController::class, 'update'])->name('update');
            Route::get('{id}', [PermissionController::class, 'show'])->name('show');
            Route::delete('{id}', [PermissionController::class, 'destroy'])->name('delete');
        });

        // Label Permission
        Route::group(['prefix' => 'label-permission', 'as' => 'label.permission.'], function () {
            Route::get('/', [LabelPermissionController::class, 'index'])->name('index');
            Route::post('store', [LabelPermissionController::class, 'store'])->name('store');
            Route::put('{id}', [LabelPermissionController::class, 'update'])->name('update');
            Route::get('{id}', [LabelPermissionController::class, 'show'])->name('show');
            Route::delete('{id}', [LabelPermissionController::class, 'destroy'])->name('delete');
        });
    });

    Route::group(['prefix' => 'manajemen-kuliah', 'as' => 'manajemen.kuliah.'], function () {
        // Mata Kuliah
        Route::group(['prefix' => 'mata-kuliah', 'as' => 'matkul.'], function () {
            Route::get('/', [MatkulController::class, 'index'])->name('index');
            Route::post('store', [MatkulController::class, 'store'])->name('store');
            Route::put('{id}', [MatkulController::class, 'update'])->name('update');
            Route::get('/fetch', [MatkulController::class, 'fetch'])->name('fetch');
            Route::get('{id}', [MatkulController::class, 'show'])->name('show');
            Route::delete('{id}', [MatkulController::class, 'destroy'])->name('delete');
        });

        // Fakultas
        Route::group(['prefix' => 'fakultas', 'as' => 'fakultas.'], function () {
            Route::get('/', [FakultasController::class, 'index'])->name('index');
            Route::post('store', [FakultasController::class, 'store'])->name('store');
            Route::put('{id}', [FakultasController::class, 'update'])->name('update');
            Route::get('{id}', [FakultasController::class, 'show'])->name('show');
            Route::delete('{id}', [FakultasController::class, 'destroy'])->name('delete');
        });

        // Prodi
        Route::group(['prefix' => 'program-studi', 'as' => 'prodi.'], function () {
            Route::get('/', [ProdiController::class, 'index'])->name('index');
            Route::post('store', [ProdiController::class, 'store'])->name('store');
            Route::put('{id}', [ProdiController::class, 'update'])->name('update');
            Route::get('{id}', [ProdiController::class, 'show'])->name('show');
            Route::delete('{id}', [ProdiController::class, 'destroy'])->name('delete');
        });

        // Kelas
        Route::group(['prefix' => 'kelas', 'as' => 'kelas.'], function () {
            Route::get('/', [KelasController::class, 'index'])->name('index');
            Route::post('store', [KelasController::class, 'store'])->name('store');
            Route::put('{id}', [KelasController::class, 'update'])->name('update');
            Route::get('/fetch', [KelasController::class, 'fetch'])->name('fetch');
            Route::get('show/{id}', [KelasController::class, 'show'])->name('show');
            Route::get('{kode}', [KelasController::class, 'showKelas'])->name('showKelas');
            Route::delete('{id}', [KelasController::class, 'destroy'])->name('delete');
            Route::get('fetch-mahasiswa/{kode}', [KelasController::class, 'fetchMahasiswa'])->name('fetchMahasiswa');
        });

        // Jadwal Kuliah
        Route::group(['prefix' => 'jadwal', 'as' => 'jadwal.admin.'], function () {

            Route::group(['prefix' => 'kuliah', 'as' => 'kuliah.'], function () {
                Route::get('/', [KuliahController::class, 'index'])->name('index');
                Route::post('store', [KuliahController::class, 'store'])->name('store');
                Route::put('{id}', [KuliahController::class, 'update'])->name('update');
                Route::get('/fetch', [KuliahController::class, 'fetch'])->name('fetch');
                Route::get('{id}', [KuliahController::class, 'show'])->name('show');
                Route::delete('{id}', [KuliahController::class, 'destroy'])->name('delete');
                Route::delete('reset/{id}', [KuliahController::class, 'reset'])->name('reset');
            });

            Route::group(['prefix' => 'ujian', 'as' => 'ujian.'], function () {
                Route::get('/', [UjianController::class, 'index'])->name('index');
                Route::post('store', [UjianController::class, 'store'])->name('store');
                Route::delete('reset', [UjianController::class, 'reset'])->name('reset');
                Route::get('{id}', [UjianController::class, 'show'])->name('show');
                Route::put('{id}', [UjianController::class, 'update'])->name('update');
                Route::delete('{id}', [UjianController::class, 'destroy'])->name('delete');
            });

            // Route ini untuk menampilkan dropdown create data kelas dan matkul berdasarkan dosen
            Route::get('dropdown/{id}', function ($id) {
                $kelas = DB::table('dosen_kelas')
                    ->where('dosen_id', $id)
                    ->join('kelas', 'dosen_kelas.kelas_id', '=', 'kelas.id')
                    ->select('kelas.id', 'kelas.kode')
                    ->distinct() // agar tidak ada data yang sama
                    ->get('kelas_id');

                $matkul = DB::table('dosen_matkul')
                    ->where('dosen_id', $id)
                    ->join('matkuls', 'dosen_matkul.matkul_id', '=', 'matkuls.id')
                    ->select('matkuls.id', 'matkuls.nama')
                    ->get('matkul_id');

                return response()->json([
                    'kelas' => $kelas,
                    'matkul' => $matkul
                ]);
            })->name('dropdown');

            // Route ini untuk menampilkan dropdown edit data kelas dan matkul berdasarkan dosen
            Route::get('dropdown-edit/{id}', function ($id) {
                // hanya menerima request ajax
                if (request()->ajax()) {
                    // Query untuk menampilkan data kelas dan matkul berdasarkan dosen
                    $kelas = DB::table('dosen_kelas')
                        ->where('dosen_id', $id)
                        ->join('kelas', 'dosen_kelas.kelas_id', '=', 'kelas.id')
                        ->select('kelas.id', 'kelas.kode')
                        ->distinct() // agar tidak ada data yang sama
                        ->get('kelas_id');

                    $matkul = DB::table('dosen_matkul')
                        ->where('dosen_id', $id)
                        ->join('matkuls', 'dosen_matkul.matkul_id', '=', 'matkuls.id')
                        ->select('matkuls.id', 'matkuls.nama')
                        ->get('matkul_id');

                    // Inisialisasi variabel output
                    $output = '';

                    // Kelas
                    $output .= '
                    <div class="form-group mb-3">
                        <label for="kelas">Kelas</label>
                        <select name="kelas" id="edit_kelas" class="form-control">';
                    foreach ($kelas as $kls) {
                        $output .= '<option value="' . $kls->id . '">' . $kls->kode . '</option>';
                    }
                    $output .= '</select>
                        <span class="invalid-feedback d-block error-text edit_kelas_error"></span>
                    </div>';

                    // Matkul
                    $output .= '
                    <div class="form-group mb-3">
                        <label for="matkul">Mata Kuliah</label>
                        <select name="matkul" id="edit_matkul" class="form-control">';
                    foreach ($matkul as $mtk) {
                        $output .= '<option value="' . $mtk->id . '">' . $mtk->nama . '</option>';
                    }
                    $output .= '</select>
                        <span class="invalid-feedback d-block error-text edit_matkul_error"></span>
                    </div>';

                    // mengembalikan output dalam bentuk json
                    return response()->json($output);
                } else {
                    // jika bukan request ajax, maka akan mengembalikan error 404
                    abort(404);
                }
            })->name('dropdownEdit');
        });
    });
});
