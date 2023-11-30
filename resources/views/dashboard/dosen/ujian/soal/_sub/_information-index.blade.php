<div class="row">
    <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Mata Kuliah</b>
            </div>
            <div class="col-lg-8 col-8">
                : {{ $jadwal->matkul->nama }}
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Kelas</b>
            </div>
            <div class="col-lg-8 col-8">
                : {{ $jadwal->kelas->kode }}
            </div>
        </div>
    </div>
</div>
<div class="row">
    {{-- <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Semester</b>
            </div>
            <div class="col-lg-8 col-8">
                : {{ $ujian->semester }}
            </div>
        </div>
    </div> --}}
    <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Tipe Ujian</b>
            </div>
            <div class="col-lg-8 col-8">
                : {{ $ujian->tipe_soal . " (" . strtoupper($ujian->tipe_ujian) . ")" }}
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Waktu Mulai</b>
            </div>
            <div class="col-lg-8 col-8">
                : {{ $jadwal->started_at . ' WIB' ?? '-' }}
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Waktu Selesai</b>
            </div>
            <div class="col-lg-8 col-8">
                :
               @if ($jadwal->ended_at == null)
                    -
               @else
                    {{ $jadwal->ended_at . ' WIB' }}
               @endif
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Durasi Ujian</b>
            </div>
            <div class="col-lg-8 col-8">
                : {{ $ujian->durasi_ujian }} Menit ({{ $durasiToHour }})
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Tanggal Ujian</b>
            </div>
            <div class="col-lg-8 col-8">
                :
                {{ Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('dddd, D MMMM Y') }}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Random Soal</b>
            </div>
            <div class="col-lg-8 col-8">
                : {{ $ujian->random_soal == 1 ? 'Ya' : 'Tidak' }}
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-2">
        <div class="row">
            <div class="col-lg-4 col-4">
                <b>Lihat Hasil</b>
            </div>
            <div class="col-lg-8 col-8">
                : {{ $ujian->lihat_hasil == 1 ? 'Ya' : 'Tidak' }}
            </div>
        </div>
    </div>
</div>
