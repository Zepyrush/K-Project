<div class="card card-primary card-outline mb-2">
    <div class="card-header position-relative" style="right:5px;">
        <h5 class="m-0 p-0 font-weight-bold">
            <i class="fas fa-bell text-primary mr-2"></i> Notifikasi Absensi
        </h5>
    </div>
</div>

@php
    $ada_absensi = false;
@endphp

<div class="container-notif">
    @foreach ($jadwalHariIni as $jadwal)
        @php
            $absen_has_created = $jadwal->absens
                ->where('created_at', '>=', Carbon\Carbon::today())
                ->count() > 0;

            if ($absen_has_created) { // Jika absensi sudah dibuat
                continue; // Lanjutkan ke jadwal selanjutnya
            }

            $ada_absensi = true;
        @endphp

        <div class="alert bg-white card p-2" role="alert">
            <div class="d-flex flex-row align-items-center justify-content-between justify-align-start">
                <i class="fas fa-bell ml-1 mr-3 text-primary" style="font-size: 18px;">
                </i>

                <p style="line-height: 1.3;">
                    Hei, {{ Auth::user()->name }}. Absensi <b>{{ $jadwal->matkul->nama }}</b>
                    dari Kelas <b>{{ $jadwal->kelas->kode }}</b> belum kamu mulai.
                    Silahkan <i class="text-bold">Mulai Absensi.</i>
                </p>

                @if (jam_sekarang() >= $jadwal->started_at && jam_sekarang() <= $jadwal->ended_at && $jadwal->hari == hari_ini())
                    <a href="{{ route('manajemen.kuliah.kelas.dosen.index', encrypt($jadwal->id)) }}"
                        class="btn btn-sm btn-primary show_btn ml-3" data-toggle="tooltip" title="Lihat Kelas"
                        data-placement="left">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                @else
                    <a href="javascript:void(0)" class="btn btn-sm btn-secondary cursor_default ml-3"
                        data-toggle="tooltip" title=" Kelas ini belum dimulai atau sudah selesai."
                        data-placement="left">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>


@if (!$ada_absensi)
    <div class="alert bg-white card p-2" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell-slash ml-1 mr-2 text-primary">
            </i>

            <p style="line-height: 1.3;">
                Tidak ada notifikasi absensi.
            </p>
        </div>
    </div>
@endif
