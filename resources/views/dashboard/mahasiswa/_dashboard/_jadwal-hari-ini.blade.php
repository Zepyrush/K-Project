<div class="card card-primary card-outline mb-2">
    <div class="card-header position-relative p-2" style="right:3px;">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="m-0 p-0 font-weight-bold ml-2">
                <i class="fas fa-bell text-primary mr-2"></i> Notifikasi Jadwal Hari Ini
            </h5>

            <a href="{{ route('manajemen.kuliah.jadwal.mahasiswa.index') }}" class="btn btn-sm
                btn-primary" data-toggle="tooltip" title="Lihat Jadwal Kuliah" data-placement="left">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </div>
    </div>
</div>

@php
    $ada_jadwal = false;
@endphp

<div class="container-notif">
    @foreach ($jadwalHariIni as $jadwal)
        @php
            (strlen(Auth::user()->name) > 15) ?
                $name = substr(Auth::user()->name, 0, 15) . '..' :
                $name = Auth::user()->name;

            $absen_dibuat = App\Models\ManajemenKuliah\Absen::where('jadwal_id', $jadwal->id)
                ->first();

            $sudah_absen = Auth::user()
                ->mahasiswa->presensi($jadwal->id)
                ->first();

            if ($absen_dibuat) {
                if ($sudah_absen) {
                    continue; // skip to next iteration
                } else {
                    $ada_jadwal = true;
                }
            } else {
                continue;
            }

        @endphp

        @if ($ada_jadwal)
            <div class="alert bg-white card p-2" role="alert">
                <div class="d-flex flex-row align-items-center justify-content-between justify-align-start">
                    <i class="fas fa-bell ml-1 mr-3 text-primary" style="font-size: 18px;">
                    </i>

                    <p style="line-height: 1.3;">
                        Hei, {{ $name }}. Kamu mempunyai Jadwal Kuliah
                        <b>{{ $jadwal->matkul->nama }}</b> pada jam
                        <b>{{ $jadwal->started_at }} s.d. {{ $jadwal->ended_at }} WIB</b>.

                        @if ($sudah_absen)
                            <span class="badge badge-success position-relative" style="bottom: 2px;">
                                Kamu sudah absen
                            </span>
                        @else
                            <span class="badge badge-danger position-relative" style="bottom: 2px;">
                                Kamu belum absen
                            </span>
                        @endif
                    </p>

                    <a href="{{ route('manajemen.kuliah.kelas.mahasiswa.index', encrypt($jadwal->id)) }}"
                        class="btn btn-primary btn-sm ml-3 show_btn" data-toggle="tooltip"
                        title="Pergi ke Kelas {{ $jadwal->matkul->nama }}" data-placement="left">

                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
        @endif

    @endforeach
</div>


@if (!$ada_jadwal)
    <div class="alert bg-white card p-2" role="alert">
        <div class="d-flex flex-row align-items-center">
            <i class="fas fa-bell-slash ml-1 mr-2 text-primary">
            </i>

            <p style="line-height: 1.3;">
                Tidak ada notifikasi jadwal kuliah hari ini.
            </p>
        </div>
    </div>
@endif
