<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else if (Auth::user()->isDosen()) {
                return redirect()->route('dosen.dashboard');
            } else if (Auth::user()->isMahasiswa()) {
                return redirect()->route('mahasiswa.dashboard');
            }
        }

        return view('dashboard.index');
    }
}
