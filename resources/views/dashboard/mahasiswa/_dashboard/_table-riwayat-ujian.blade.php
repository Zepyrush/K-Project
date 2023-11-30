@if ($riwayatUjian->count() > 0)
    <div class="card card-primary card-outline mb-2">
        <div class="card-header p-2">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="m-0 p-0 font-weight-bold ml-2">
                    <i class="fas fa-history text-primary mr-1"></i> Riwayat Ujian
                </h5>
                <a href="{{ route('manajemen.kuliah.ujian.mahasiswa.riwayatUjian') }}"
                    class="btn btn-sm btn-primary" data-toggle="tooltip" title="Lihat Riwayat Ujian">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <div style="overflow: auto;max-height: 330px;">
                <table class="table table-hover">
                    <thead style="position: sticky; top:0;">
                        <tr style="background: #fff; box-shadow: 1px 7px 11px -11px #000">
                            <th style="width: 50%">Judul Ujian</th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($riwayatUjian as $jadwal)
                            @php
                                $ujian = $jadwal->ujian;
                                $tanggal = \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y');
                            @endphp

                            <tr>
                                <td>{{ $ujian->judul }}</td>
                                <td>{{ $tanggal }}</td>
                                <td>
                                    @if ($ujian->lihat_hasil == 1)
                                        <button id="{{ encrypt($jadwal->id) }}" type="button"
                                            class="btn btn-primary btn-sm btn_lihat" data-toggle="tooltip"
                                            title="Lihat Hasil">
                                            <i class="fas fa-external-link-alt"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success btn-sm cursor_default"
                                            data-toggle="tooltip" title="Sudah Mengerjakan">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@else
    <div class="card card-primary card-outline mb-2">
        <div class="card-header p-2">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="m-0 p-0 font-weight-bold ml-2">
                    <i class="fas fa-history text-primary mr-1"></i> Riwayat Ujian
                </h5>
                <a href="{{ route('manajemen.kuliah.ujian.mahasiswa.riwayatUjian') }}"
                    class="btn btn-sm btn-primary" data-toggle="tooltip" title="Lihat Riwayat Ujian">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="alert alert-primary">
        <i class="fas fa-bell mr-2"></i>
        Tidak ada riwayat ujian.
    </div>
@endif


@include('dashboard.mahasiswa.ujian._modal._modal-hasil-ujian')
