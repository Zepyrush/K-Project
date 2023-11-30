<div class="card card-primary card-outline mb-2">
    <div class="card-header position-relative" style="right:5px;">
        <h5 class="m-0 p-0 font-weight-bold">
            <i class="fas fa-bell text-primary mr-2"></i> Notifikasi Tugas
        </h5>
    </div>
</div>

@php
    $tugas_ada = false;
@endphp

<div class="container-notif">
    @foreach ($jadwals as $jadwal)
        @php
            $tugas = $jadwal
                ->tugas()
                ->whereDoesntHave('nilaiTugas')
                ->where('file_or_link', '!=', null)
                ->where('created_at', '>=', date('Y-m-d'))
                ->where('parent', '!=', 0)
                ->count();

            if ($tugas > 0) {
                // Jika ada tugas
                $tugas_ada = true;
            }
        @endphp

        @if ($tugas > 0)
            <div class="alert bg-white card p-2" role="alert">
                <div class="d-flex flex-row align-items-center justify-content-between justify-align-start">
                    <i class="fas fa-bell ml-1 mr-3 text-primary" style="font-size: 18px;">
                    </i>

                    <p style="line-height: 1.3;">
                        Hei, {{ Auth::user()->name }}. Ada
                        <b>{{ $tugas }}</b> Tugas <b>{{ $jadwal->matkul->kode }}</b>
                        dari mahasiswa Kelas <b>{{ $jadwal->kelas->kode }}</b> yang belum
                        kamu nilai. Silahkan <i class="text-bold">Beri Nilai.</i>
                    </p>

                    <a href="{{ route('manajemen.kuliah.tugas.dosen.index', encrypt($jadwal->id)) }}"
                        class="btn btn-primary btn-sm ml-3 show_btn" data-toggle="tooltip"
                        title="Pergi ke Tugas {{ $jadwal->matkul->nama }}" data-placement="left">

                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
        @endif
    @endforeach
</div>

@if (!$tugas_ada)
    <div class="alert bg-white card p-2" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell-slash ml-1 mr-2 text-primary">
            </i>

            <p style="line-height: 1.3;">
                Tidak ada notifikasi tugas.
            </p>
        </div>
    </div>
@endif
