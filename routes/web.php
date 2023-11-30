<?php

use App\Http\Controllers\Auth\SessionExpiredController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['middleware' => 'prevent.back.history'], function () {

    Auth::routes();

    Route::group(['middleware' => ['auth', 'web']], function () {
        Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        Route::group(['prefix' => 'akun', 'as' => 'profile.'], function () {
            Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('index');
            Route::post('/update-photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('update.photo');
            Route::put('/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('update.password');
            Route::put('/delete-photo', [App\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('delete.photo');
        });
    });
});

