<div class="card card-primary card-outline">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold p-0 m-0">
                <i class="fas fa-chart-line text-primary mr-2"></i>
                Laporan Nilai Ujian Kelas {{ $data->kelas->kode }} - {{ $data->matkul->nama }}
            </h5>
        </div>
    </div>
</div>

<div class="card card-primary card-outline mb-2 sticky">
    <div class="card-header p-2">
        <div class="d-flex align-items-center">
            <a href="{{ route('manajemen.kuliah.laporan.dosen.exports.nilaiUjian', [encrypt($kelas_id), encrypt($matkul_id)]) }}"
                class="btn btn-success btn-sm mr-1 float-right">
                <i class="fas fa-file-excel mr-1"></i>
                Ekspor Excel
            </a>
            <button id="cetakTable" class="btn btn-primary btn-sm">
                <i class="fas fa-print mr-1"></i> Cetak
            </button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table id="tableLaporanUjian" class="table table-hover laporan">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mahasiswa</th>
                    @for ($i = 1; $i <= 8; $i++)
                        <th>{{ 'S' . $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
