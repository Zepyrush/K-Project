<div class="card card-primary card-outline mb-2">
    <div class="card-header position-relative p-2" style="right:3px;">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="m-0 p-0 font-weight-bold ml-2">
                <i class="fas fa-bell text-primary mr-2"></i> Notifikasi Ujian
            </h5>

            <a href="{{ route('manajemen.kuliah.ujian.mahasiswa.index') }}" class="btn btn-sm
                btn-primary" data-toggle="tooltip" title="Lihat Jadwal Ujian" data-placement="left">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </div>
    </div>
</div>

@php
    $ada_ujian = false;
@endphp

<div class="container-notif">
    @foreach ($jadwalUjian as $jadwal)
        @php
            $tanggal = date('d-m-Y') == $jadwal->tanggal_ujian;
            $mulai = date('H:i') >= $jadwal->started_at;
            $selesai = date('H:i') <= $jadwal->ended_at;

            if ($tanggal) { // jika tanggal ujian hari ini
                if (!empty($jadwal->ended_at)) { // jika ada jam selesainya
                    if ($mulai && $selesai) { // waktu dimulai dan selesai
                        $ada_ujian = true; // notifikasi ujian
                    } else {
                        continue; // tidak ada notifikasi
                    }
                } else { // jika tidak ada jam selesainya
                    if ($mulai) { // jika sudah dimulai

                        // jika sudah lewat 15 menit dari jam mulai maka tidak bisa kerjakan
                        $current_time = strtotime('now');
                        $exam_start_at = strtotime($jadwal->tanggal_ujian . ' ' . $jadwal->started_at);
                        $max_time = strtotime('+120 minutes', $exam_start_at);

                        if ($current_time >= $max_time) {
                            continue;
                        }

                        $ada_ujian = true; // notifikasi ujian
                    } else {
                        continue;
                    }
                }
            }

            $ujian = $jadwal->ujian;

            (strlen(Auth::user()->name) > 15) ?
                $name = substr(Auth::user()->name, 0, 15) . '..' :
                $name = Auth::user()->name;
        @endphp

        @if ($ada_ujian)
            <div class="alert bg-white card p-2" role="alert">
                <div class="d-flex flex-row align-items-center justify-content-between justify-align-start">
                    <i class="fas fa-bell ml-1 mr-3 text-primary"
                        style="font-size: 18px;">
                    </i>

                    <p style="line-height: 1.3;">
                        Hei, {{ $name }}. Kamu ada <b>Ujian <span>{{ $ujian->tipe_soal }} ({{ strtoupper($ujian->tipe_ujian) }})</span>
                        {{ $jadwal->matkul->nama }} (SMS-{{ $ujian->semester }})</b>.
                        Silahkan klik tombol pulpen Untuk Mengerjakan <i class="text-bold">sebelum lewat 15 menit dari jam mulai.</i>
                    </p>

                    <button type="button" id="{{ encrypt($jadwal->id) }}" class="btn btn-primary btn-sm ml-3 btnMulai" data-toggle="tooltip"
                        title="Kerjakan" data-placement="left">
                        <i class="fas fa-pen"></i>
                    </button>
                </div>
            </div>
        @endif
    @endforeach
</div>


@if (!$ada_ujian)
    <div class="container-notif">
        <div class="alert bg-white card p-2" role="alert">
            <div class="d-flex flex-row align-items-center ">
                <i class="fas fa-bell-slash ml-1 mr-2 text-primary">
                </i>

                <p style="line-height: 1.3;">
                    Tidak ada notifikasi ujian.
                </p>
            </div>
        </div>
    </div>
@endif

@include("dashboard.mahasiswa.ujian._modal._modal-mulai-ujian")


