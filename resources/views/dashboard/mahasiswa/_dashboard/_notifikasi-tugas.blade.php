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
            $tugas_count = $jadwal
                ->tugas()
                ->where('parent', '!=', 0)
                ->where('sudah_dinilai', '0')
                ->where('pengumpulan', '>', date('Y-m-d H:i:s'))
                ->where('file_or_link', null)
                ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
                ->count();

            $sudah_presensi = Auth::user()
                ->mahasiswa
                ->presensi($jadwal->id)
                ->first();

            if (!$sudah_presensi) {
                $tugas_count = 0;
            }

            $tugas = $jadwal->tugas->first();

            if ($tugas_count > 0) {
                $tugas_ada = true;
            }

            (strlen(Auth::user()->name) > 15) ?
                $name = substr(Auth::user()->name, 0, 15) . '..' :
                $name = Auth::user()->name;
        @endphp

        @if ($tugas_count > 0)
            <div class="alert bg-white card p-2" role="alert">
                <div class="d-flex flex-row align-items-center justify-content-between justify-align-start">
                    <i class="fas fa-bell ml-1 mr-3 text-primary" style="font-size: 18px;">
                    </i>

                    <p style="line-height: 1.3;">
                        Hei, {{ $name }}. Ada
                        <b>{{ $tugas_count }}</b> Tugas <b>{{ $jadwal->matkul->kode }}</b> yang belum
                        kamu kerjakan. Silahkan <i class="text-bold">Kerjakan Sebelum Deadline.</i>
                    </p>

                    <a href="{{ route('manajemen.kuliah.kelas.mahasiswa.tugas', encrypt($jadwal->id)) }}"
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
