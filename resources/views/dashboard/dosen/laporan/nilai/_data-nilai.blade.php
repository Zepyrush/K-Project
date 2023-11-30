@php
    $nama_mtkl = Auth::user()->dosen->matkuls->find($jadwal->matkul_id)->nama ?? $jadwal->matkul->nama;
    $name = "Laporan Nilai Tugas & Ujian Kelas {$jadwal->kelas->kode} - $nama_mtkl"
@endphp

<div class="card card-primary card-outline">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold p-0 m-0 title-laporan" data-title="{{ $name }}">
                <i class="fas fa-chart-line text-primary mr-2"></i>
                {{ $name }}
            </h5>
        </div>
    </div>
</div>

<div class="card card-primary card-outline sticky mb-2">
    <div class="card-header p-2">
        <div class="d-flex align-items-center">
            <a href="{{ route('manajemen.kuliah.laporan.dosen.exports.nilai', [encrypt($kelas_id), encrypt($matkul_id)]) }}"
                class="btn btn-success btn-sm">
                <i class="fas fa-file-excel mr-1"></i>
                Ekspor Excel
            </a>
            <button id="cetakTable" class="btn btn-primary btn-sm ml-1">
                <i class="fas fa-print mr-1"></i> Cetak
            </button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table id="tableLaporanNilai" class="table table-hover laporan">
            <thead>
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Mahasiswa</th>
                    <th colspan="14" class="text-center bg-primary">TUGAS</th>
                    <th rowspan="2" class="text-center bg-primary">Rata Rata</th>
                    <th rowspan="2" class="text-center">UTS</th>
                    <th rowspan="2" class="text-center">UAS</th>
                    <th rowspan="2" class="text-center">Total</th>
                </tr>

                <tr>
                    @for ($i = 1; $i <= 14; $i++)
                        <th>
                            P{{ $i }}
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
