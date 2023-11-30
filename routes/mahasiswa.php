<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa\DashboardController;
use App\Http\Controllers\Mahasiswa\ManajemenKuliah\{
    JadwalController,
    KelasController,
    UjianController,
    Ujian\PilihanGandaController,
    Ujian\EssayController,
};


Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('mahasiswa.dashboard');

    Route::group(['prefix' => 'manajemen-kuliah', 'as' => 'manajemen.kuliah.'], function () {
        Route::group(['prefix' => 'jadwal', 'as' => 'jadwal.mahasiswa.'], function () {
            Route::get('/', [JadwalController::class, 'index'])->name('index');
        });

        Route::group(['prefix' => 'kelas', 'as' => 'kelas.mahasiswa.'], function () {
            Route::get('{id}', [KelasController::class, 'index'])->name('index');
            Route::post('presensi', [KelasController::class, 'presensi'])->name('presensi');
            Route::get('materi/{id}', [KelasController::class, 'materi'])->name('materi');
            Route::get('tugas/{id}', [KelasController::class, 'tugas'])->name('tugas');
            Route::get('tugas-selesai/{id}', [KelasController::class, 'tugasSelesai'])->name('tugas.selesai');
            Route::get('tugas/{jadwal}/{tugas}', [KelasController::class, 'lihatTugas'])->name('lihat.tugas');
            Route::post('store-tugas/{jadwal}/{tugas}', [KelasController::class, 'storeTugas'])->name('store.tugas');
            Route::get('detail-info-presensi/{id}', [KelasController::class, 'detailInfoPresensi'])->name('detailInfoPresensi');
        });

        Route::group(['prefix' => 'ujian', 'as' => 'ujian.mahasiswa.'], function () {
            Route::get('/', [UjianController::class, 'index'])->name('index');
            Route::get('informasi/{id}', [UjianController::class, 'informasiUjian'])->name('informasi');
            Route::get('riwayat-ujian', [UjianController::class, 'riwayatUjian'])->name('riwayatUjian');
            Route::get("hasil/{id}", [UjianController::class, 'hasilUjian'])->name('hasilUjian');
            Route::get('cetak-hasil/{id}', [UjianController::class, 'cetakHasilUjian'])->name('cetakHasilUjian');

            // route untuk ujian pilihan ganda
            Route::group(['prefix' => 'pg', 'as' => 'pg.'], function() {
                Route::get('{id}', [PilihanGandaController::class, 'ujian'])->name('ujian');
                Route::post('mulai', [PilihanGandaController::class, 'mulaiUjian'])->name('mulaiUjian');
                Route::get('fetch-soal/{jadwal_id}', [PilihanGandaController::class, 'fetchSoal'])->name('fetchSoal');
                Route::get('fetch-soal-list/{jadwal_id}', [PilihanGandaController::class, 'fetchDaftarSoal'])->name('fetchDaftarSoal');
                Route::post('simpan-jawaban', [PilihanGandaController::class, 'simpanJawaban'])->name('simpanJawaban');
                Route::post('ragu-ragu', [PilihanGandaController::class, 'raguRagu'])->name('raguRagu');
                Route::post('selesai', [PilihanGandaController::class, 'selesaiUjian'])->name('selesaiUjian');
            });

            // route untuk ujian essay
            Route::group(['prefix' => 'essay', 'as' => 'essay.'], function() {
                Route::get('{id}', [EssayController::class, 'ujian'])->name('ujian');
                Route::post('mulai', [EssayController::class, 'mulaiUjian'])->name('mulaiUjian');
                Route::get('fetch-soal/{jadwal_id}', [EssayController::class, 'fetchSoal'])->name('fetchSoal');
                Route::get('fetch-soal-list/{jadwal_id}', [EssayController::class, 'fetchDaftarSoal'])->name('fetchDaftarSoal');
                Route::post('simpan-jawaban', [EssayController::class, 'simpanJawaban'])->name('simpanJawaban');
                Route::post('ragu-ragu', [EssayController::class, 'raguRagu'])->name('raguRagu');
                Route::post('selesai', [EssayController::class, 'selesaiUjian'])->name('selesaiUjian');
            });
        });
    });
});
