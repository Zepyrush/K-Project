@php
    $kelas = $jadwal->kelas;
    $matkul = $jadwal->matkul;

    $materi = \App\Models\ManajemenKuliah\Materi::where('kelas_id', $kelas->id)
        ->where('matkul_id', $matkul->id)
        ->where('dosen_id', Auth::user()->dosen->id)
        ->where('tipe', '!=', 'slide')
        ->count();

    $tugas = \App\Models\ManajemenKuliah\Tugas::where('jadwal_id', $jadwal->id)
        ->where('matkul_id', $matkul->id)
        ->where('dosen_id', Auth::user()->dosen->id)
        ->where('parent', 0)
        ->count();
@endphp

<div class="col-lg-4">
    <div class="card">

        <div
            class="card-header text-center {{ $jadwal->hari == hari_ini() ? 'bg-success' : 'bg-secondary' }}">
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
            @if (jam_sekarang() >= $jadwal->started_at && jam_sekarang() <= $jadwal->ended_at && $jadwal->hari == hari_ini())
                <a href="{{ route('manajemen.kuliah.kelas.dosen.index', encrypt($jadwal->id)) }}"
                    class="btn btn-success">
                    Masuk
                </a>
            @else
                <a href="javasript:void(0)" class="masuk btn btn-secondary">
                    Masuk
                </a>
            @endif

            <div class="float-right">
                <a href="{{ route('manajemen.kuliah.materi.dosen.index', encrypt($jadwal->id)) }}"
                    class="btn btn-info toMateri mr-1">
                    Materi
                    <span class="badge badge-light">
                        {{ $materi }}
                    </span>
                </a>
                <a href="{{ route('manajemen.kuliah.tugas.dosen.index', encrypt($jadwal->id)) }}"
                    class="btn btn-primary toTugas position-relative">
                    Tugas
                    <span class="badge badge-light">
                        {{ $tugas }}
                    </span>

                    @php
                        $tugas_belum_dinilai = $jadwal
                            ->tugas()
                            ->whereDoesntHave('nilaiTugas')
                            ->where('file_or_link', '!=', null)
                            ->where('created_at', '>=', date('Y-m-d'))
                            ->where('parent', '!=', 0)
                            ->count();
                    @endphp

                    @if ($tugas_belum_dinilai != 0)
                        <span class="position-absolute badge rounded-pill notif badge-danger"
                            style="top:-5px; padding: 0 5px 0 5px;" data-toggle="tooltip"
                            title="Ada tugas yang belum dinilai">
                            &nbsp;
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>
