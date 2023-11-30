<div class="card card-primary card-outline mb-2">
    <div class="card-header position-relative" style="right:5px;">
        <h5 class="m-0 p-0 font-weight-bold">
            <i class="fas fa-bell text-primary mr-2"></i>

            Notifikasi Ujian
        </h5>
    </div>
</div>

@php
    $ujian_ada = false;

    if ($ujians->count() > 0) {
        $ujian_ada = true;
    }

    if ($ujians->count() >= 1) {
        $text = $ujians->count() == 1 ? "satu" : "beberapa";
    }
@endphp

<div class="container-notif">
    @if ($ujians->count() > 0)
        <div class="alert bg-white card p-2" role="alert">
            <div class="d-flex flex-row align-items-center justify-content-between justify-align-start">
                <i class="fas fa-bell ml-1 mr-3 text-primary" style="font-size: 18px;">
                </i>

                <p style="line-height: 1.3;">
                    Hei, {{ Auth::user()->name }}. Ada
                    <b>{{ $text }}</b> ujian mahasiswa yang belum
                    kamu nilai. Silahkan <i class="text-bold">Beri Nilai.</i>
                </p>

                <a href="{{ route('manajemen.kuliah.jadwal.dosen.ujian.index') }}"
                    class="btn btn-primary btn-sm ml-3 show_btn" data-toggle="tooltip"
                    title="Pergi ke halaman ujian" data-placement="left">

                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>
    @endif
</div>


<div class="container-notif">
    @foreach ($jadwalUjian as $jadwal)
        @php
            $ujian_ada = true;
        @endphp

        <div class="alert bg-white card p-2" role="alert">
            <div class="d-flex flex-row align-items-center justify-content-between justify-align-start">
                <i class="fas fa-bell ml-1 mr-3 text-primary" style="font-size: 18px;">
                </i>

                <p style="line-height: 1.3;">
                    Hei, {{ Auth::user()->name }}. Ujian <b>{{ $jadwal->matkul->nama }}</b>
                    dari Kelas <b>{{ $jadwal->kelas->kode }}</b> belum dibuat.
                    Silahkan klik tombol tambah untuk <i class="text-bold">Membuat Ujian.</i>
                </p>

                <a href="javascript:void(0)" id="{{ encrypt($jadwal->id) }}"
                    class="btn btn-success btn-sm ml-3 btnBuatUjian" data-toggle="tooltip" title="Buat Ujian"
                    data-placement="left">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
        </div>
    @endforeach
</div>

@if (!$ujian_ada)
    <div class="alert bg-white card p-2" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell-slash ml-1 mr-2 text-primary">
            </i>

            <p style="line-height: 1.3;">
                Tidak ada notifikasi ujian.
            </p>
        </div>
    </div>
@endif

@include('dashboard.dosen.jadwal._modal._modal-create')

@push('js')
    <script>
        $('.pilgan').on('click', function() {
            localStorage.setItem(`${noIndukUser}_fromDashboard`, 'true');
        });

        $('.essay').on('click', function() {
            localStorage.setItem(`${noIndukUser}_fromDashboard`, 'true');
        });
    </script>
@endpush
