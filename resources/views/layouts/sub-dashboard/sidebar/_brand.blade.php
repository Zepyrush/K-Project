@php
    if (Auth::user()->isDosen()) {
        $route = 'dosen.dashboard';
    } else if (Auth::user()->isMahasiswa()) {
        $route = 'mahasiswa.dashboard';
    } else {
        $route = 'admin.dashboard';
    }
@endphp

<a href="{{ route($route) }}" class="brand-link" style="text-align: left;">
    <img src="{{ asset('assets/image/logo.png') }}" alt="{{ config('app.name') }} Logo"
        class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text ml-1">{{ config('app.name') }}</span>
</a>
