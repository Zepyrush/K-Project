@php
    $materi = \App\Models\ManajemenKuliah\Materi::where('matkul_id', $jadwal->matkul->id)
        ->where('kelas_id', Auth::user()->mahasiswa->kelas->id)
        ->where('tipe', '!=', 'slide')
        ->count();

    $tugas_dinilai = $jadwal
        ->tugas()
        ->where('parent', '!=', 0)
        ->where('sudah_dinilai', '0')
        ->where('pengumpulan', '>', date('Y-m-d H:i:s'))
        ->where('file_or_link', null)
        ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
        ->get();

    $sudah_presensi = Auth::user()
            ->mahasiswa
            ->presensi($jadwal->id)
            ->first();

    if ($tugas_dinilai->isNotEmpty() && $sudah_presensi) {
        $tugas = $jadwal
            ->tugas()
            ->where('parent', 0)
            ->where('pengumpulan', '>', date('Y-m-d H:i:s'))
            ->count();
    } else {
        $tugas = false;
    }
@endphp

<div class="col-lg-4">
    <div class="card">
        <div class="card-header text-center {{ $jadwal->hari == hari_ini() ? 'bg-success' : 'bg-secondary' }}">
            <h4 class="font-weight-bold">{{ $jadwal->matkul->nama }}</h4>
            <p class="m-0 p-0">
                {{ $jadwal->hari }} - {{ $jadwal->started_at . ' s.d. ' . $jadwal->ended_at . ' WIB' }}
            </p>
        </div>
        <div class="card-body">
            {{-- list bootstrap --}}
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-lg-6 col-6">
                            <p class="m-0 p-0 font-weight-bold">Kelas</p>
                        </div>
                        <div class="col-lg-6 col-6 text-muted font-weight-bold">
                            <p class="m-0 p-0">{{ $jadwal->kelas->kode }}</p>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-lg-6 col-6">
                            <p class="m-0 p-0 font-weight-bold">Kode Matkul</p>
                        </div>
                        <div class="col-lg-6 col-6 text-muted font-weight-bold">
                            <p class="m-0 p-0">{{ $jadwal->matkul->kode }}</p>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-lg-6 col-6">
                            <p class="m-0 p-0 font-weight-bold">SKS</p>
                        </div>
                        <div class="col-lg-6 col-6 text-muted font-weight-bold">
                            <p class="m-0 p-0">{{ $jadwal->matkul->sks }}</p>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-lg-6 col-6">
                            <p class="m-0 p-0 font-weight-bold">Dosen</p>
                        </div>
                        <div class="col-lg-6 col-6 text-muted font-weight-bold">
                            <p class="m-0 p-0">{{ $jadwal->dosen->nama }}</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>


        <div class="card-footer">
            <a href="{{ route('manajemen.kuliah.kelas.mahasiswa.index', encrypt($jadwal->id)) }}"
                class="btn btn-success">
                Masuk
            </a>
            <div class="float-right">
                <a href="{{ route('manajemen.kuliah.kelas.mahasiswa.materi', encrypt($jadwal->id)) }}"
                    class="btn btn-info toMateri mr-1">
                    Materi

                    <span class="badge badge-light">
                        {{ $materi }}
                    </span>
                </a>

                <a href="{{ route('manajemen.kuliah.kelas.mahasiswa.tugas', encrypt($jadwal->id)) }}"
                    class="btn btn-primary toTugas position-relative">
                    Tugas

                    @if ($tugas != false)
                        <span class="badge badge-danger">
                            {{ $tugas }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>
